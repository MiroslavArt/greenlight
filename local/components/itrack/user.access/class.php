<?php

use \Bitrix\Main\Context;
use Itrack\Custom\CUserEx;
use \Itrack\Custom\CUserRole;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\Participation\IParticipant;
use Itrack\Custom\Participation\AParticipant;
use Itrack\Custom\Participation\CParticipation;
use Itrack\Custom\UserAccess\CUserAccess;
use Itrack\Custom\Participation\CLost;
use Itrack\Custom\Participation\CContract;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity;

class ItrUserAccess extends CBitrixComponent
{
	/** @var CUserRole $editorRole */
	private $editorRole;

	private $userId;
	private $companyId;




    public function executeComponent()
    {
		$this->userId = $this->arParams["USER_ID"];
		$this->companyId = CUserEx::getUserCompanyId($this->userId);

		$this->editorRole = new CUserRole();
		$editorId = $GLOBALS["USER"]->GetID();
		$editorCompany = CUserEx::getUserCompanyId($editorId);

		$userHasAccess =
			$editorId === $this->userId ||
			$this->editorRole->isSuperUser() && $editorCompany === $this->companyId && $this->companyId ||
			$this->editorRole->isSuperBroker()
		;

		if (!$userHasAccess) LocalRedirect("/");

    	$request = Context::getCurrent()->getRequest();

		$isAjax = $request->isAjaxRequest() && check_bitrix_sessid() && $this->editorRole->isSuperUser();

		if ($isAjax) {
			$GLOBALS["APPLICATION"]->RestartBuffer();
			header('Content-Type: application/json; charset='.LANG_CHARSET);

			try {
				$answer = $this->handleAjaxRequest($request);
				echo json_encode($answer ?: ["success" => "y"]);
			} catch (Exception $e) {
				echo json_encode(["error" => $e->getMessage()]);
			} finally {
				die();
			}
		}



		$this->arResult["USER"] = \CUser::GetByID($this->userId)->GetNext();
		$this->arResult["COMPANY"] = Company::getElementByID($this->companyId);


//		$this->arResult["LOST"] = Lost::getElementsByConditions();
//		$this->arResult["CONTRACT"] = Contract::getElementsByConditions();


		$filterLosts = $request->get("lost_curator") === "y";
		$filterContracts = $request->get("contract_curator") === "y";

		$this->arResult["FILTER_LOSTS"] = $filterLosts ? "Y" : "N";
		$this->arResult["FILTER_CONTRACTS"] = $filterContracts ? "Y" : "N";

		$this->arResult["LOST_FILTER_URL"] = $filterLosts
			? $GLOBALS["APPLICATION"]->GetCurPageParam("lost_curator=n", ["lost_curator"])
			: $GLOBALS["APPLICATION"]->GetCurPageParam("lost_curator=y", ["lost_curator"])
			;

		$this->arResult["CONTRACT_FILTER_URL"] = $filterContracts
			? $GLOBALS["APPLICATION"]->GetCurPageParam("contract_curator=n", ["contract_curator"])
			: $GLOBALS["APPLICATION"]->GetCurPageParam("contract_curator=y", ["contract_curator"])
		;

		$this->arResult["LOST"] = $filterLosts
			? CParticipation::getTargetsByUser($this->userId, CLost::class)
			: CParticipation::getTargetsByCompany($this->companyId, CLost::class);

		$this->arResult["CONTRACT"] = $filterContracts
			? CParticipation::getTargetsByUser($this->userId, CContract::class)
			: CParticipation::getTargetsByCompany($this->companyId, CContract::class);

		$this->arResult["CONTRACT_STATISTICS"] = $this->getContractStatistics();

		$this->arResult["STATUS_LIST"] = $this->getStatusListOfLost();

		$userAccess = new CUserAccess($this->userId);

		$this->arResult["SWITCH"] = [
			"LOST" => [
				"ACCEPTANCE" => $userAccess->getAcceptanceForAllLosts(),
				"NOTIFICATION" => $userAccess->getNotificationForAllLosts(),
			],
			"CONTRACT" => [
				"ACCEPTANCE" => $userAccess->getAcceptanceForAllContracts(),
				"NOTIFICATION" => $userAccess->getNotificationForAllContracts(),
			],
		];



		$this->arResult["IS_SUPER"] = (new CUserRole($this->userId))->isSuperUser();
		$this->arResult["EDITOR_IS_SUPER_USER"] = $this->editorRole->isSuperUser();
		$this->arResult["EDITOR_IS_SUPER_BROKER"] = $this->editorRole->isSuperBroker();

		$this->includeComponentTemplate();
    }

    private function getStatusListOfLost() {
		Loader::IncludeModule("highloadblock");

		$hlblock = HighloadBlockTable::getList(["filter" => ["TABLE_NAME" => "e_lost_status"]])->fetch();
		$entity = HighloadBlockTable::compileEntity($hlblock);
		$entityDataClass = $entity->getDataClass();

		$res = $entityDataClass::getList();

		$statusList = [];
		while ($row = $res->fetch()) {
			$code = $row["UF_XML_ID"];
			$statusList[$code] = $row;
		}

		return $statusList;
	}

	private function getContractStatistics() {
    	$contractIds = [];
		foreach ($this->arResult["CONTRACT"]["TARGETS"] as $contract) {
			$contractIds[] = $contract["ID"];
    	}

		$res = CIBlockElement::GetList([], ["PROPERTY_CONTRACT" => $contractIds], ["PROPERTY_CONTRACT", "PROPERTY_STATUS"]);

		$statistics = [];

		while ($row = $res->GetNext()) {
			$contractId = $row["PROPERTY_CONTRACT_VALUE"];
			$statusCode = $row["PROPERTY_STATUS_VALUE"];

			$statistics[$contractId][$statusCode] = $row["CNT"];
		}

		return $statistics;
	}

    private function handleAjaxRequest($request) {
    	$action = $request->get("action");				// switch, binding
		$switchType = $request->get("switchType");		// notification, acceptance
    	$enable = $request->get("enable") === "y";
    	$targetId = (int)$request->get("targetId");		// contractId or lostId
    	$targetType = $request->get("targetType");		// contract, lost

    	switch ($action) {
    		case "switch";
    			$this->manageSwitch($enable, $switchType, $targetId, $targetType);
    			break;
    		case "binding";
				$this->manageBinding($enable, $targetId, $targetType);
				break;

		}
	}

	private function manageSwitch(bool $enable, string $switchType, int $targetId, string $targetType) {
		$userAccess = new CUserAccess($this->userId);

		$methodName = $enable ? "set" : "drop";
		$methodName .= mb_convert_case($switchType, MB_CASE_TITLE);
		$methodName .=  "For";
		$methodName .= mb_convert_case($targetType, MB_CASE_TITLE);

		if (!is_callable([$userAccess, $methodName])) {
			throw new Exception("Invalid request parameters");
		}

		$userAccess->$methodName($targetId);
	}

	private function manageBinding(bool $enable, int $targetId, string $targetType) {
		switch($targetType) {
			case "lost":
				$participantClass = CLost::getParticipantClass();
				break;

			case "contract":
				$participantClass = CContract::getParticipantClass();
				break;

			default:
				throw new Exception("Invalid request parameters");

		}

		/** @var IParticipant $participantClass */
		/** @var IParticipant $participant */
		$participant = $participantClass::initByTargetAndCompany($targetId, $this->companyId);

		if ($enable) {
			$participant->bindCurator($this->userId);
		} else {
			$participant->unbindCurator($this->userId);
		}

	}
}
