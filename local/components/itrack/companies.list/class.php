<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Itrack\Custom\CUserEx;
use Itrack\Custom\CUserRole;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\Participation\CLost;
use Itrack\Custom\Participation\CLostParticipant;
use Itrack\Custom\Participation\CContractParticipant;
use Itrack\Custom\Participation\CParticipation;
use Itrack\Custom\Participation\CContract;

/*

						client list			insurer list		adjuster list

broker regular			personal			personal			personal
broker super			all					all					all

client regular			personal*			deny access			deny access
client super			company	*			deny access			deny access

insurer regular			personal			personal*			deny access
insurer super			company				company	*			deny access

adjuster regular		personal			deny access			personal*
adjuster super			company				deny access			company *

all		 - user can see all companies
personal - user can see all the companies he works with
company  - user can see all the companies that his company works with
* 		 - user can only see his company

*/

class ItrCompaniesList extends CBitrixComponent
{
	private $userId;
	private $companyId;
	private $companyParty;

	/** @var CUserRole $userRole */
	private $userRole;

    public function executeComponent()
    {
    	$this->userId = $GLOBALS["USER"]->GetID();
    	$this->companyId = CUserEx::getUserCompanyId($this->userId);
    	$this->userRole = new CUserRole($this->userId);
    	$this->companyParty = $this->arParams["PARTY"];

    	$pageIsClientList = $this->companyParty === CUserRole::getClientGroupCode();
		$userPartyIsEqualToCompanyParty = $this->userRole->equalTo($this->companyParty);

    	$userHasAccess = $userPartyIsEqualToCompanyParty || $this->userRole->isBroker() || $pageIsClientList;

		if (!$userHasAccess) LocalRedirect("/");

		$this->arResult["USER_IS_BROKER"] = $this->userRole->isSuperBroker();

        if(isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $this->arResult['IS_AJAX'] = 'Y';
        }


        $this->fetchCompanies();
        $this->includeComponentTemplate();
    }

    private function fetchCompanies()
    {
        $permittedLostIds = $this->getLostIds();
		$counts = $this->getLostCountsByCompany($permittedLostIds);
		$availableCompanyIds = array_keys($counts);
		$searchQuery = trim($this->request->get("search"));

		if(!$this->userRole->isSuperBroker() && $this->userRole->isBroker()) {
            $ids = CParticipation::getTargetIdsByUser($this->userId, CContract::class);
            $extraavailableCompanyIds = $this->getContractsCountsByCompany($ids);
            $availableCompanyIds = array_merge($availableCompanyIds, $extraavailableCompanyIds);
            $availableCompanyIds = array_unique($availableCompanyIds);
        }


		$arCompanies = Company::getElementsByConditions(
			[
				"ID" => $this->getPermittedCompanyIds($availableCompanyIds),
				"PROPERTY_TYPE" => $this->getPermittedCompanyTypeId(),
				"NAME" => $searchQuery ? "%$searchQuery%" : "",
			],
			["NAME"=>'ASC'],
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
				"DETAIL_PAGE_URL" => $arCompany["DETAIL_PAGE_URL"],
                "CNT" => $counts[$companyId]
			];

            foreach ($counts[$companyId] as $statusCode => $cnt) {
                $countsTotal[$statusCode] += $cnt;
            }
		}

		$this->arResult["ITEMS"] = $companies;
		$this->arResult["CNT_TOTAL"] = $countsTotal;
    }

    private function getContractsCountsByCompany($permittedContractsIds) {
        $arParticipants = CContractParticipant::getElementsByConditions([
            "PROPERTY_TARGET_ID" => $permittedContractsIds
        ], [], [
            "PROPERTY_TARGET_ID.PROPERTY_STATUS",
            "PROPERTY_PARTICIPANT_ID",
        ]);

        $companies = [];
        foreach ($arParticipants as $arParticipant) {
            $companies[] = $arParticipant["PROPERTY_PARTICIPANT_ID_VALUE"];
        }
        $companies = array_unique($companies);
        return $companies;
    }

    private function getLostCountsByCompany($permittedLostIds) {
		$arParticipants = CLostParticipant::getElementsByConditions([
			"PROPERTY_TARGET_ID" => $permittedLostIds
		], [], [
			"PROPERTY_TARGET_ID.PROPERTY_STATUS",
			"PROPERTY_PARTICIPANT_ID",
		]);


		$counts = [];
		foreach ($arParticipants as $arParticipant) {
			$companyId = $arParticipant["PROPERTY_PARTICIPANT_ID_VALUE"];
			$statusCode = $arParticipant["PROPERTY_TARGET_ID_PROPERTY_STATUS_VALUE"];

			$counts[$companyId][$statusCode]++;
			$counts[$companyId]["SUM"]++;
		}

		return $counts;
	}

    private function getLostIds() {
		if ($this->userRole->isSuperBroker()) {
			return [];
		}

		$lostIds = $this->userRole->isSuperUser()
            ? CParticipation::getTargetIdsByCompany($this->companyId, CLost::class)
            : CParticipation::getTargetIdsByUser($this->userId, CLost::class)
        ;

        return $lostIds ?: false;
	}

	private function getPermittedCompanyIds($rivals) {
		if ($this->userRole->isSuperBroker()) {
			return [];
		}

		if (count($rivals) && $this->userRole->getUserParty() == $this->companyParty) {
			return [ $this->companyId ];
		}

		return $rivals ?: false;
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
