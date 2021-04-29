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
    private $companyParty;

    /** @var CUserRole $userRole */
    private $userRole;

    public function executeComponent()
    {
        $this->userId = $GLOBALS["USER"]->GetID();
        $this->userCompanyId = CUserEx::getUserCompanyId($this->userId);
        $this->userRole = new CUserRole($this->userId);

        // todo change CLIENT_ID to COMPANY_ID
        $this->companyId = $this->arParams['CLIENT_ID'] ?: $this->userCompanyId;

        $this->getCompany();

        $userPartyIsEqualToCompanyParty = $this->userRole->equalTo($this->companyParty);
        $companyIsClient = $this->companyParty === CUserRole::CLIENT;
        $userHasAccess = $userPartyIsEqualToCompanyParty || $this->userRole->isBroker() || $companyIsClient;

        if (!$userHasAccess) LocalRedirect("/");


        $this->arResult["CAN_ADD_CONTRACT"] = $this->userRole->isSuperBroker();

        global $APPLICATION;

        $arResult =& $this->arResult;


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
            $this->companyParty = $arCompany["PROPERTIES"]["TYPE"]["VALUE_XML_ID"];
        } else {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }

    }

    private function companyIsClient() {
    	return $this->arResult["COMPANY"]["PROPERTIES"]["TYPE"]["VALUE_XML_ID"] == CUserRole::getClientGroupCode();
	}

    private function getContractList()
    {
		$permittedLosts = $this->getPermittedLosts();
		$arCounts = $this->getCountsOfLost($permittedLosts);

		$arPermittedContractIds = $this->getPermittedContractIds($permittedLosts);
		$arLeaders = CContractParticipant::getLeaders($arPermittedContractIds);

		$searchQuery = trim($this->request->get("search"));

		$contractFilter = [
			"ACTIVE" => "Y",
			"ID" => $arPermittedContractIds ?: false,
		];

		$filterLikeContractList = in_array($this->companyParty, [CUserRole::CLIENT, CUserRole::BROKER]);

		// filter for client/broker detail
		if ($filterLikeContractList) {
			$contractFilter[] = [
				"LOGIC" => "OR",
				"NAME" => $searchQuery ? "%$searchQuery%" : "",
				"PROPERTY_TYPE" => $searchQuery ? "%$searchQuery%" : "",
				"PROPERTY_DATE" => $this->prepareSearchQueryForDate($searchQuery),
			];
		}

		$arContracts = Contract::getElementsByConditions($contractFilter);

		$result = [];
        $countsTotal = [];
		$listUrl = $this->arParams['LIST_URL'];
		foreach ($arContracts as $arContract) {
			$id = $arContract["ID"];

			// filter for adjuster/insurer detail
			if ($searchQuery && !$filterLikeContractList) {
				$arClient = $arLeaders[$id][CUserRole::getClientGroupCode()];

				if (empty($arClient) || mb_stripos($arClient["NAME"], $searchQuery) === false) {
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

			foreach ($arCounts[$id] as $statusCode => $cnt) {
                $countsTotal[$statusCode] += $cnt;
            }
		}

		$this->arResult["ITEMS"] = $result;
		$this->arResult["CNT_TOTAL"] = $countsTotal;
    }

    private function getCountsOfLost(array $arLostIds = []): array {
		$arCountsOfLost = Lost::getElementsByConditions([
			"ACTIVE" => "Y",
			"ID" => $arLostIds ?: false
		], [], [
			"PROPERTY_CONTRACT",
			"PROPERTY_STATUS",
		]);


		$result = [];
		foreach ($arCountsOfLost as $arCount) {
			$contractId = $arCount["PROPERTY_CONTRACT_VALUE"];
			$statusCode = $arCount["PROPERTY_STATUS_VALUE"];

			$result[$contractId][$statusCode]++;
			$result[$contractId]["SUM"]++;
		}

		return $result;
	}

	private function getPermittedLosts(): array {
        $lostsOfCompany = CParticipation::getTargetIdsByCompany($this->companyId, CLost::class);

        if ($this->userRole->isSuperBroker()) {
            return $lostsOfCompany;
        }

        $lostsOfUser = $this->userRole->isSuperUser()
            ? CParticipation::getTargetIdsByCompany($this->userCompanyId, CLost::class)
            : CParticipation::getTargetIdsByUser($this->userId, CLost::class)
        ;

        return array_intersect(
            $lostsOfCompany,
            $lostsOfUser
        );
    }

	private function getPermittedContractIds(array $permittedLosts): array {
        $contractsOfCompany = CParticipation::getTargetIdsByCompany($this->companyId, CContract::class);

        if ($this->userRole->isSuperBroker()) {
            return $contractsOfCompany;
        }

        $contractsOfUser = $this->userRole->isSuperUser()
            ? CParticipation::getTargetIdsByCompany($this->userCompanyId, CContract::class)
            : CParticipation::getTargetIdsByUser($this->userId, CContract::class)
        ;

        $permittedContracts = array_intersect(
            $contractsOfCompany,
            $contractsOfUser
        );


        $contractsOfPermittedLosts = array_map(
            function($v) { return $v["PROPERTY_CONTRACT_VALUE"]; },
            CLost::getElementsByConditions(["ID" => $permittedLosts ?: false], [], ["PROPERTY_CONTRACT"])
        );

        return array_merge($permittedContracts, $contractsOfPermittedLosts);
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
