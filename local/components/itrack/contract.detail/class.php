<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\CUserRole;
use Itrack\Custom\CUserEx;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\Highloadblock\HLBWrap;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Contract;
use Itrack\Custom\InfoBlocks\Lost;
use Itrack\Custom\Participation\CLost;
use Itrack\Custom\Participation\CContract;
use Itrack\Custom\Participation\CParticipation;
use Itrack\Custom\UserFieldValueTable;
use \Bitrix\Main\UserGroupTable;
use \Bitrix\Main\Entity\ReferenceField;
use \Bitrix\Main\ORM\Query\Join;
use \Bitrix\Iblock\Elements\ElementCompanyTable;

class ItrContract extends CBitrixComponent
{
    private $userId;
    private $userCompanyId;
    private $companyId;
    private $contractId;
    private $errors;

    /** @var CUserRole $userRole */
    private $userRole;

    public function onPrepareComponentParams($arParams)
    {
        if(!empty($arParams['CLIENT_ID'])) {
            $this->companyId = $arParams['CLIENT_ID'];
        }

        $this->contractId = $arParams['CONTRACT_ID'];


        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $this->userId = $GLOBALS["USER"]->GetID();
        $this->userCompanyId = CUserEx::getUserCompanyId($this->userId);
        $this->userRole = new CUserRole($this->userId);

        $arResult =& $this->arResult;

        $this->getContract();

        //get company
        if(!empty($this->companyId)) {
            $arResult['COMPANY'] = $this->getCompany($this->companyId);
            if($arResult["COMPANY"]["PROPERTIES"]["TYPE"]["VALUE_XML_ID"] == CUserRole::getClientGroupCode()) {
                $arResult['KLIENT'] = $arResult['COMPANY'];
            } else {
                $arResult['KLIENT'] = $this->getCompany(current($arResult['CONTRACT']['PROPERTIES']['CLIENT']['VALUE']));
            }
        }

        //get insurance company
        $insarray = [];
        foreach($arResult['CONTRACT']['PROPERTIES']['INSURANCE_COMPANY']['VALUE'] as $insco) {
            $inscodata = $this->getCompany($insco);
            array_push($insarray, $inscodata);
        }
        $arResult['INSURANCE_COMPANIES'] = $insarray;
        if(!empty($arResult['CONTRACT']['PROPERTIES']['INSURANCE_COMPANY_LEADER']['VALUE'])) {
            $arResult['INSURANCE_COMPANY'] = $this->getCompany($arResult['CONTRACT']['PROPERTIES']['INSURANCE_COMPANY_LEADER']['VALUE']);
        }
        if(!empty(current($arResult['CONTRACT']['PROPERTIES']['INSURANCE_BROKER']['VALUE']))) {
            $arResult['BROKER'] = $this->getCompany(current($arResult['CONTRACT']['PROPERTIES']['INSURANCE_BROKER']['VALUE']));
        }

        if(isset($this->request['q_name'])) {
            $arFilter = [
                array("LOGIC" => "OR",
                    array("NAME" => "%" . trim($this->request['q_name']) . "%"),
                    array("DATE_CREATE" => "%" . trim($this->request['q_name']) . "%"),
                    array("PROPERTY_DESCRIPTION" => "%" . trim($this->request['q_name']) . "%")
                )
            ];
            $arResult['ACTION'] = 'search';
        }

        if(isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $arResult['IS_AJAX'] = 'Y';
        }

        $arFilter["ACTIVE"] = 'Y';
        $arFilter["PROPERTY_CONTRACT.ID"] = [$this->contractId];

        $this->getInstypes();
        $this->getLostList($arFilter);
        $arCurators = $this->getCurators();
        if (!empty($arCurators)) {
            $arResult['CONTRACT']['PROPERTIES']['CURATORS'] = $arCurators;
            unset($arCurators);
        }

        $APPLICATION->SetTitle($arResult['CONTRACT']['NAME']);

        $this->includeComponentTemplate();
    }

    private function getCompany(int $companyId) {
        $arCompany = Company::getElementByID($companyId);
        if(!empty($arCompany)) {
            return $arCompany;
        } else {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }
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

    private function getContract() {

        $permittedLosts = $this->getPermittedLosts();



        $arPermittedContractIds = $this->getPermittedContractIds($permittedLosts);

        if(empty($arPermittedContractIds)) {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }

        $arContract = Contract::getElementByID($this->contractId);


        if(!empty($arContract)) {
            if($arContract['PROPERTIES']['DOCS']['VALUE']) {
                foreach ($arContract['PROPERTIES']['DOCS']['VALUE'] as $item) {
                    $arContract['PROPERTIES']['DOCS']['VALUE_DETAIL'][$item] =  \CFile::GetFileArray($item);
                }
            }
            if ($arContract['PROPERTIES']['DOCS_PAMYATKA']['VALUE']) {
                foreach ($arContract['PROPERTIES']['DOCS_PAMYATKA']['VALUE'] as $item) {
                    $arContract['PROPERTIES']['DOCS_PAMYATKA']['VALUE_DETAIL'][$item] =  \CFile::GetFileArray($item);
                }
            }
            if ($arContract['PROPERTIES']['DOCS_OTHERS']['VALUE']) {
                foreach ($arContract['PROPERTIES']['DOCS_OTHERS']['VALUE'] as $item) {
                    $arContract['PROPERTIES']['DOCS_OTHERS']['VALUE_DETAIL'][$item] =  \CFile::GetFileArray($item);
                }
            }
            $this->arResult['CONTRACT'] = $arContract;
        } else {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }
    }

    private function getLostList(array $arFilter = [])
    {
        $arLostIds = $this->getPermittedLosts();
        $arFilter['ID'] = $arLostIds ?: false;
        $arResult =& $this->arResult;
        $elements = Lost::getElementsByConditions($arFilter, [], []);

        if(empty($elements)) {
            return false;
        }

        $tableName = $elements[0]['PROPERTIES']['STATUS']['USER_TYPE_SETTINGS']['TABLE_NAME'];
        $arLostStatus = [];
        if($tableName) {
            $objLostStatus = new \Itrack\Custom\Highloadblock\HLBWrap($tableName);
            $rsStatus = $objLostStatus->getList([
                "select" => array("*"),
                "order" => array("ID" => "ASC")
            ]);

            while($arStatus = $rsStatus->fetch()) {
                $arLostStatus[$arStatus['UF_XML_ID']] = $arStatus;
            }
        }

        foreach ($elements as $element) {
            $arCompaniesIds[] = $element['PROPERTIES']['CLIENT']['VALUE'];
            $arItem = [
                'ID' => $element['ID'],
                'NAME' => $element['NAME'],
                'DATE' => (new \DateTime($element['DATE_ACTIVE_FROM']))->format('d.m.Y'),
                'STATUS' => $arLostStatus[$element['PROPERTIES']['STATUS']['VALUE']],
                'DESCRIPTION' => $element['PROPERTIES']['DESCRIPTION']['VALUE'],
                'DETAIL_PAGE_URL' => $this->arParams['LIST_URL'] . $this->arParams['CLIENT_ID'] . '/contract/' . $this->arParams['CONTRACT_ID'] . '/lost-' . $element['ID'] . '/',
            ];
            $arResult['LOSTS'][$element['ID']] = $arItem;
        }

        if(!empty($arCompaniesIds)) {
            $arResult['LOST_COMPANIES_IDS'] = array_unique($arCompaniesIds);
            $arResult['LOST_COMPANIES'] = $this->getCompanies($arResult['LOST_COMPANIES_IDS']);
        }

        unset($elements);
    }


    private function getCurators()
    {
        //$arCurators = \Itrack\Custom\InfoBlocks\LostUsers::getElementsByConditions(['PROPERTY_LOST' => [$this->arLost['ID']]]);
        $participation = new CParticipation(new CContract($this->contractId));

        $arCurators  = $participation->getParticipants();
        if (empty($arCurators)) {
            return false;
        }

        $this->arResult['CONTRACT']['PROPERTIES']['CURATORS_LEADERS'] = [];
        $arCuratorsIds = [];

        foreach ($arCurators as $arCurator) {
            //$arCuratorsIds[] = $arCurator['PROPERTIES']['CURATORS']['VALUE'];
            $arCuratorsIds = array_merge($arCuratorsIds, $arCurator['PROPERTIES']['CURATORS']['VALUE'] ?: []);
            if(!empty($arCurator['PROPERTIES']['CURATOR_LEADER']['VALUE']) && intval($arCurator['PROPERTIES']['CURATOR_LEADER']['VALUE']) > 0) {
                $this->arResult['CONTRACT']['PROPERTIES']['CURATORS_LEADERS'][$arCurator['PROPERTIES']['CURATOR_LEADER']['VALUE']] = $arCurator['PROPERTIES']['CURATOR_LEADER']['VALUE'];
            }
        }

        $query = UserGroupTable::query()
            ->setSelect([
                'ID' => 'USER.ID',
                'LOGIN' => 'USER.LOGIN',
                'GROUP_CODE' => 'GROUP.STRING_ID',
                'NAME' => 'USER.NAME',
                'LAST_NAME' => 'USER.LAST_NAME',
                'SECOND_NAME' => 'USER.SECOND_NAME',
                'LAST_LOGIN' => 'USER.LAST_LOGIN',
                'POSITION' => 'USER.WORK_POSITION',
                'PHONE' => 'USER.WORK_PHONE',
                'EMAIL' => 'USER.EMAIL',
                'MPHONE' => 'USER.PERSONAL_PHONE',
                'COMPANY_ID' => 'IB_COMPANY.ID',
                'COMPANY_NAME' => 'IB_COMPANY.NAME',
                'COMPANY_TYPE' => 'IB_COMPANY.TYPE.ITEM.VALUE'
            ])
            ->registerRuntimeField(new ReferenceField(
                'UF_VAL',
                UserFieldValueTable::class,
                Join::on('this.USER_ID', 'ref.VALUE_ID')
            ))
            ->registerRuntimeField((new ReferenceField(
                'IB_COMPANY',
                ElementCompanyTable::class,
                Join::on('this.UF_VAL.UF_COMPANY', 'ref.ID')
            ))->configureJoinType(Join::TYPE_INNER))
            ->whereIn('ID', $arCuratorsIds);


        $result = $query->exec();
        $items = [];

        while ($row = $result->fetch()) {

            $userId = $row['ID'];
            $group = $row['GROUP_CODE'];

            //$isSuper = in_array($group, CUserRole::getSuperGroups()) || $items[$userId]['IS_SUPER'];

            $groups = $items[$userId]['GROUPS'] ?: [];
            $groups[] = $group;

            unset($row['GROUP_CODE']);
            $items[$row['COMPANY_ID']][$userId] = $row;
            //$items[$userId]['GROUPS'] = $groups;
            //$items[$userId]['IS_SUPER'] = $isSuper;
            if(in_array($userId, $this->arResult['CONTRACT']['PROPERTIES']['CURATORS_LEADERS'])) {
                $items[$row['COMPANY_ID']][$userId]['IS_LEADER'] = 'Y';
            }
        }
        return $items;
    }

    private function getCompanies(array $ids) {
        $result = [];
        $notFoundInCache = [];
        $ttl = 86400;
        $initDir = "itrack";


        $cache = \Bitrix\Main\Data\Cache::createInstance();


        foreach ($ids as $id) {
            $cacheId = "COMPANY_LOGO_$id";

            if ($cache->initCache($ttl, $cacheId, $initDir)) {
                $result[$id] = $cache->getVars();
            }
            else {
                $notFoundInCache[] = $id;
            }
        }

        if (count($notFoundInCache) && Loader::includeModule("iblock")) {
            $companies = Company::getElementsByConditions([ "ID" => $notFoundInCache ]);
            foreach ($companies as $company) {
                $logoFileId = $company['PROPERTIES']['LOGO']['VALUE'];
                $companyId = $company['ID'];
                $cacheId = "COMPANY_LOGO_$companyId";

                $cache->initCache($ttl, $cacheId, $initDir);
                $cache->startDataCache();
                $cache->endDataCache($logoFileId);

                $result[$companyId] = $logoFileId;
            }
        }

        return $result;
    }




    private function getPermittedContractIds(array $permittedLosts): array {
        $contractsOfCompany = CParticipation::getTargetIdsByCompany($this->companyId, CContract::class);

        $contractsOfPermittedLosts = array_map(
            function($v) { return $v["PROPERTY_CONTRACT_VALUE"]; },
            CLost::getElementsByConditions(["ID" => $permittedLosts ?: false], [], ["PROPERTY_CONTRACT"])
        );

        if ($this->userRole->isSuperBroker()) {
            return array_merge($contractsOfCompany, $contractsOfPermittedLosts);
        }

        $contractsOfUser = $this->userRole->isSuperUser()
            ? CParticipation::getTargetIdsByCompany($this->userCompanyId, CContract::class)
            : CParticipation::getTargetIdsByUser($this->userId, CContract::class)
        ;

        $permittedContracts = array_intersect(
            $contractsOfCompany,
            $contractsOfUser
        );

        return array_merge($permittedContracts, $contractsOfPermittedLosts);
    }
}
