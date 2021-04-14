<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\CUserEx;
use Itrack\Custom\CUserRole;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Lost;
use Itrack\Custom\Participation\CLost;
use Itrack\Custom\Participation\CLostParticipant;
use Itrack\Custom\Participation\CParticipation;

class ItrCompaniesList extends CBitrixComponent
{
	private $pageIsClientList;
	private $userId;
	private $companyId;

	/** @var CUserRole $userRole */
	private $userRole;

    public function executeComponent()
    {
    	$this->userId = $GLOBALS["USER"]->GetID();
    	$this->companyId = CUserEx::getUserCompanyId($this->userId);
    	$this->userRole = new CUserRole($this->userId);

    	$userIsNotUsualClient = $this->userRole->isSuperUser() || !$this->userRole->isClient();

    	$this->pageIsClientList = $this->arParams["PARTY"] === CUserRole::getClientGroupCode();

    	$userHasAccess = $userIsNotUsualClient && ($this->pageIsClientList || $this->userRole->isBroker());

		if (!$userHasAccess) LocalRedirect("/");


        if(isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $this->arResult['IS_AJAX'] = 'Y';
        }


        $this->fetchCompanies();
        $this->includeComponentTemplate();
    }

    private function fetchCompanies()
    {
		$counts = $this->getLostCountsByCompany();
		$searchQuery = trim($this->request->get("q_name"));

		$arCompanies = Company::getElementsByConditions(
			[
				"ID" => $this->userRole->isSuperBroker() ? [] : array_keys($counts),
				"PROPERTY_TYPE" => $this->getPermittedCompanyTypeId(),
				"NAME" => $searchQuery ? "%$searchQuery%" : "",
			],
			[],
			[
				"ID",
				"NAME",
				"DETAIL_PAGE_URL",
				"PROPERTY_LOGO",
			],
			$this->arParams["DETAIL_URL"]
		);

		$companies = [];
		$countsTotal = [];
		foreach ($arCompanies as $arCompany) {
			$companyId = $arCompany["ID"];

			$companies[$companyId] = [
				"ID" => $arCompany["ID"],
				"NAME" => $arCompany["NAME"],
				"LOGO" => $arCompany["PROPERTY_LOGO_VALUE"],
				"DETAIL_PAGE_URL" => $arCompany["DETAIL_PAGE_URL"]
			];

			$sumByCompany = 0;
			$countsOfCompany = $counts[$companyId];

			foreach ($countsOfCompany as $statusCode => $count) {
				$companies[$companyId]["CNT"][$statusCode] = $count;
				$sumByCompany += $count;

				$countsTotal[$statusCode] += $count;
			}

			$companies[$companyId]["CNT"]["SUM"] = $sumByCompany;
			$countsTotal["SUM"] += $sumByCompany;
		}

		$this->arResult["ITEMS"] = $companies;
		$this->arResult["CNT_TOTAL"] = $countsTotal;
    }

    private function getLostCountsByCompany() {
		$arParticipants = CLostParticipant::getElementsByConditions([
			"PROPERTY_TARGET_ID" => $this->getLostIds()
		], [], [
			"PROPERTY_TARGET_ID.PROPERTY_STATUS",
			"PROPERTY_PARTICIPANT_ID",
		]);


		$counts = [];
		foreach ($arParticipants as $arParticipant) {
			$companyId = $arParticipant["PROPERTY_PARTICIPANT_ID_VALUE"];
			$statusCode = $arParticipant["PROPERTY_TARGET_ID_PROPERTY_STATUS_VALUE"];

			$counts[$companyId][$statusCode]++;
		}

		return $counts;
	}

    private function getLostIds() {
		if ($this->userRole->isSuperBroker()) {
			return [];
		}

		$arTargets = $this->userRole->isSuperUser()
			? CParticipation::getTargetsByCompany($this->companyId, CLost::class)["TARGETS"]
			: CParticipation::getTargetsByUser($this->userId, CLost::class)["TARGETS"]
		;

		return array_map(
			function($v) { return $v["ID"]; },
			$arTargets
		);
	}

	private function getPermittedCompanyTypeId() {
		$arParties = Company::getPropertyList("TYPE");

		foreach ($arParties as $arParty) {
			$code = $arParty["XML_ID"];
			$id = $arParty["ID"];

			if ($code == $this->arParams["PARTY"]) {
				return $id;
			}
		}

		throw new Exception("Invalid parameter value");
	}
}
