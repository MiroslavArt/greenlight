<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity;
use Itrack\Custom\CUserEx;
use Itrack\Custom\CUserRole;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Contract;
use Itrack\Custom\InfoBlocks\Lost;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\SystemException;
use \Itrack\Custom\Highloadblock\HLBWrap;
use Itrack\Custom\Participation\CContract;
use Itrack\Custom\Participation\CContractParticipant;
use Itrack\Custom\Participation\CLost;
use Itrack\Custom\Participation\CParticipation;


class ItrCompany extends CBitrixComponent
{
    private $userId;
    private $userCompanyId;

    private $companyId;

    /** @var CUserRole $userRole */
    private $userRole;

    public function executeComponent()
    {

        $this->userId = $GLOBALS["USER"]->GetID();
        $this->userCompanyId = CUserEx::getUserCompanyId($this->userId);

		// todo change CLIENT_ID to COMPANY_ID
        if(!empty($this->arParams['CLIENT_ID'])) {
            $this->companyId = $this->arParams['CLIENT_ID'];
        } else {
            $this->companyId = $this->userCompanyId;
        }

        global $APPLICATION;

        $arResult =& $this->arResult;

        $this->getCompany();

        if(!empty($arResult['COMPANY']['PROPERTIES']['TYPE']['VALUE_XML_ID'])) {
            $arResult['COMPANY_TYPE'] = $arResult['COMPANY']['PROPERTIES']['TYPE']['VALUE_XML_ID'];
        } else {
            $arResult['ERRORS'][] = Loc::getMessage('COMPANY_TYPE_IS_EMPTY');
        }

        // is it necessary?
        if(isset($this->request['search'])) {
            $this->arResult['ACTION'] = 'search';
        }

        if(isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $this->arResult['IS_AJAX'] = 'Y';
        }

        $this->getContractList();

        $this->getInstypes();
        $this->getBroker();

        $APPLICATION->SetTitle($this->arResult['COMPANY']['NAME']);

        $this->includeComponentTemplate();
    }

    private function getCompany() {
        $arResult =& $this->arResult;

        $arCompany = Company::getElementByID($this->companyId);
        if(!empty($arCompany)) {
            $arResult['COMPANY'] = $arCompany;
        } else {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }

    }

    private function companyIsClient() {
    	return $this->arResult["COMPANY"]["PROPERTIES"]["TYPE"]["VALUE_XML_ID"] == CUserRole::getClientGroupCode();
	}

    private function getContractList()
    {

		$this->userRole = new CUserRole($this->userId);

		$arPermittedContractIds = $this->getPermittedContractIds();
		$arCounts = $this->getCountsOfLost($arPermittedContractIds);
		$arLeaders = CContractParticipant::getLeaders($arPermittedContractIds);

		$searchQuery = trim($this->request->get("search"));

		$contractFilter = [
			"ACTIVE" => "Y",
			"ID" => $arPermittedContractIds,
		];

		// filter for client detail
		if ($this->companyIsClient()) {
			$contractFilter[] = [
				"LOGIC" => "OR",
				"NAME" => $searchQuery ? "%$searchQuery%" : "",
				"PROPERTY_TYPE" => $searchQuery ? "%$searchQuery%" : "",
				"PROPERTY_DATE" => $this->prepareSearchQueryForDate($searchQuery),
			];
		}

		$arContracts = Contract::getElementsByConditions($contractFilter);

		$result = [];

		$listUrl = $this->arParams['LIST_URL'];
		foreach ($arContracts as $arContract) {
			$id = $arContract["ID"];

			// filter for adjuster/insurer detail
			if ($searchQuery && !$this->companyIsClient()) {
				$arClient = $arLeaders[$id][CUserRole::getClientGroupCode()];

				if (empty($arClient) || mb_stripos($arClient["NAME"], $searchQuery) === false) {
					// correcting total count values due to such a stupid filtering method
					foreach ($arCounts["TOTAL"] as $k => &$v) {
						$v -=$arCounts[$id][$k];
					}

					continue;
				}
			}


			$detailPageUrl = "$listUrl{$this->companyId}/contract/$id/";

            if(!empty($this->arParams['PAGE_TYPE']) && $this->arParams['PAGE_TYPE'] == 'contracts-list') {
                $detailPageUrl = "$listUrl$id/";
            }

			$result[] = [
				"ID" => $arContract["ID"],
				"NAME" => $arContract["NAME"],
				"DATE" => $arContract["PROPERTIES"]["DATE"]["VALUE"],
				"TYPE" => $arContract["PROPERTIES"]["TYPE"]["VALUE"],
				"DETAIL_PAGE_URL" => $detailPageUrl,
				"CNT" => $arCounts[$id],
				"LEADERS" => $arLeaders[$id],
			];
		}

		$this->arResult["ITEMS"] = $result;
		$this->arResult["CNT_TOTAL"] = $arCounts["TOTAL"];
    }

    private function getCountsOfLost(array $arContractIds) {
		$arCountsOfLost = Lost::getElementsGrouped([
			"ACTIVE" => "Y",
			"PROPERTY_CONTRACT" => $arContractIds
		], [], [
			"PROPERTY_CONTRACT",
			"PROPERTY_STATUS",
		]);


		$result = [];
		foreach ($arCountsOfLost as $arCount) {
			$contractId = $arCount["PROPERTY_CONTRACT_VALUE"];
			$statusCode = $arCount["PROPERTY_STATUS_VALUE"];
			$cnt = (int)$arCount["CNT"];

			$result[$contractId][$statusCode] = $cnt;
			$result[$contractId]["SUM"] += $cnt;

			$result["TOTAL"][$statusCode] += $cnt;
			$result["TOTAL"]["SUM"] += $cnt;
		}

		return $result;
	}


	private function getPermittedContractIds() {
		$allTargetsOfCompany = $this->getTargetsOfCompany($this->companyId);
		$arContractIdsOfCompany = $this->getContractIdsByAllTargets($allTargetsOfCompany);


		if ($this->userRole->isSuperBroker()) return $arContractIdsOfCompany;


		$allTargetsOfUser = $this->userRole->isSuperUser()
			? $this->getTargetsOfCompany($this->userCompanyId)
			: $this->getTargetsOfUser($this->userId)
		;

		$arContractIdsOfUser = $this->getContractIdsByAllTargets($allTargetsOfUser);

		return array_intersect(
			$arContractIdsOfCompany,
			$arContractIdsOfUser
		);
	}

	private function getContractIdsByAllTargets(array $allTargets) {
		list($arContractIds, $arLostIds) = $allTargets;

		$arContractIds = array_merge(
			$arContractIds,
			array_map(
				function($v) { return $v["PROPERTY_CONTRACT_VALUE"]; },
				CLost::getElementsByConditions(["ID" => $arLostIds], [], ["PROPERTY_CONTRACT"])
			)
		);

		// Для фильтрации договоров только по привязке к убыткам
		//return array_map(
		//	function($v) { return $v["PROPERTY_CONTRACT_VALUE"]; },
		//	CLost::getElementsByConditions(["ID" => $arLostIds], [], ["PROPERTY_CONTRACT"])
		//);

		return array_unique($arContractIds);
	}

	private function getTargetsOfUser(int $userId):array {
    	return [
			CParticipation::getTargetIdsByUser($userId, CContract::class),
			CParticipation::getTargetIdsByUser($userId, CLost::class)
		];
	}

	private function getTargetsOfCompany(int $companyId):array {
    	return [
			CParticipation::getTargetIdsByCompany($companyId, CContract::class),
			CParticipation::getTargetIdsByCompany($companyId, CLost::class)
		];
	}

    private function prepareSearchQueryForDate($searchQuery) {
    	if (!$searchQuery) return "";

    	$parts = explode(".", $searchQuery);
		$parts = array_reverse($parts);

		foreach ($parts as $part) {
			if (!is_numeric($part)) return "";
		}

		$searchQuery = implode("-", $parts);

		return "%$searchQuery%";
	}

    private function getInstypes() {
        $arResult =& $this->arResult;

        $objDocument = new HLBWrap('e_ins_types');

        $rsData = $objDocument->getList(array(
            "select" => array("*"),
            "order" => array("ID" => "ASC"),
            "filter" => array()  // Задаем параметры фильтра выборки
        ));

        $arResult['INSTYPES'] = $rsData->fetchAll();
    }

    private function getBroker() {
        $arResult =& $this->arResult;

        $arCompany = Company::getElementByID(INS_BROKER_ID);
        if(!empty($arCompany)) {
            $arResult['BROKER'] = $arCompany;
        } else {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }
    }
}
