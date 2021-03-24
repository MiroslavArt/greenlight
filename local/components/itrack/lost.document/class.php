<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Contract;
use Itrack\Custom\InfoBlocks\Lost;
use \Bitrix\Iblock\Elements\ElementCompanyTable;
use \Bitrix\Main\Entity\Query;
use \Bitrix\Main\Entity\ReferenceField;
use \Bitrix\Main\ORM\Query\Join;
use \Bitrix\Main\UserGroupTable;
use \Itrack\Custom\UserFieldValueTable;
use \Itrack\Custom\CUserRole;
use Itrack\Custom\InfoBlocks\LostDocuments;


class ItrLostDocument extends CBitrixComponent
{
    private $arLost;
    private $lostId;
    private $companyId;
    private $contractId;
    private $errors;

    public function onPrepareComponentParams($arParams)
    {
        $this->companyId = $arParams['CLIENT_ID'];
        $this->contractId = $arParams['CONTRACT_ID'];
        $this->lostId = $arParams['LOST_ID'];

        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $arResult =& $this->arResult;

        if ($this->getLost()) {
            $arResult['LOST'] = $this->arLost;
        }

        //Contract
        $this->getContract();
        //get company
        $arResult['COMPANY'] = $this->getCompany($this->companyId);
        //get insurance company
        if (!empty($arResult['CONTRACT']['PROPERTIES']['INSURANCE_COMPANY_LEADER']['VALUE'])) {
            $arResult['INSURANCE_COMPANY'] = $this->getCompany($arResult['CONTRACT']['PROPERTIES']['INSURANCE_COMPANY_LEADER']['VALUE']);
        }
        //curators
        $arCurators = $this->getCurators();
        if (!empty($arCurators)) {
            $arResult['CURATORS'] = $arCurators;
            unset($arCurators);
        }
        //Lost Requests
        $arResult['REQUESTS'] = $this->getRequests();

        if (isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $arResult['IS_AJAX'] = 'Y';
        }

        $arResult['CONTRACT_PAGE_URL'] = $this->arParams['LIST_URL'] . $this->arParams['CLIENT_ID'] . '/contract/' . $this->arParams['CONTRACT_ID'] . '/';
        $APPLICATION->SetTitle(GetMessage('LOST_CARD') . ' - ' . $arResult['LOST']['NAME']);

        $this->includeComponentTemplate();
    }

    private function getLost()
    {
        $arLost = Lost::getElementByID($this->lostId);
        if (empty($arLost)) {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }

        //Статус
        $tableName = $arLost['PROPERTIES']['STATUS']['USER_TYPE_SETTINGS']['TABLE_NAME'];
        $arLostStatus = [];
        if ($tableName) {
            $objLostStatus = new \Itrack\Custom\Highloadblock\HLBWrap($tableName);
            $rsStatus = $objLostStatus->getList([
                "filter" => array('UF_XML_ID' => $arLost['PROPERTIES']['STATUS']['VALUE']),
                "select" => array("*"),
                "order" => array("ID" => "ASC")
            ]);

            while ($arStatus = $rsStatus->fetch()) {
                $arLost['PROPERTIES']['STATUS']['VALUE'] = $arStatus;
            }
        }

        $this->arLost = $arLost;
        unset($arLost);

        return true;
    }

    private function getCurators()
    {
        $arCurators = \Itrack\Custom\InfoBlocks\LostUsers::getElementsByConditions(['PROPERTY_LOST' => [$this->arLost['ID']]]);
        if (empty($arCurators)) {
            return false;
        }

        $arCuratorsIds = [];
        foreach ($arCurators as $arCurator) {
            $arCuratorsIds[] = $arCurator['PROPERTIES']['CURATOR']['VALUE'];
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

            $isSuper = in_array($group, CUserRole::getSuperGroups()) || $items[$userId]['IS_SUPER'];

            $groups = $items[$userId]['GROUPS'] ?: [];
            $groups[] = $group;

            unset($row['GROUP_CODE']);
            $items[$userId] = $row;
            $items[$userId]['GROUPS'] = $groups;
            $items[$userId]['IS_SUPER'] = $isSuper;
        }

        return $items;
    }

    private function getRequests()
    {
        $arRequests = [];

        $arRequests = LostDocuments::getElementsByConditions(['PROPERTY_LOST' => [$this->arLost['ID']]]);

        foreach ($arRequests as $arRequest) {
            $arUserIDs[] = $arRequest['PROPERTIES']['REQUEST_AUTHOR']['VALUE'];
        }

        if (!empty($arUserIDs)) {
            $filter = ["ID" => implode("|", $arUserIDs)];
            $rsUsers = \CUser::GetList(($by = "NAME"), ($order = "desc"), $filter);
            while ($arUser = $rsUsers->Fetch()) {
                $arUsers[$arUser['ID']] = \CUser::formatName('#NAME# #SECOND_NAME# #LAST_NAME#', $arUser, false, false);
            }
        }

        //Статус
        $tableName = $arRequests[0]['PROPERTIES']['STATUS']['USER_TYPE_SETTINGS']['TABLE_NAME'];
        $arStatus = [];
        if ($tableName) {
            $objStatus = new \Itrack\Custom\Highloadblock\HLBWrap($tableName);
            $rsStatus = $objStatus->getList([
                "select" => array("*"),
                "order" => array("ID" => "ASC")
            ]);

            while ($arStatus = $rsStatus->fetch()) {
                $arStatuses[$arStatus['UF_XML_ID']] = $arStatus;
            }
        }

        foreach ($arRequests as $key => &$arRequest) {
            $arRequest['USER_FIO'] = $arUsers[$arRequest['PROPERTIES']['REQUEST_AUTHOR']['VALUE']];
            $arRequest['STATUS_NAME'] = $arStatuses[$arRequest['PROPERTIES']['STATUS']['VALUE']]['UF_NAME'];
            //Documents Statuses
            $this->arResult['DOCS_STATUSES'][$arStatuses[$arRequest['PROPERTIES']['STATUS']['VALUE']]['ID']]['DOCS'][] = $arRequest["ID"];
        }
        if(!empty($arStatuses)) {
            $this->arResult['STATUSES'] = $arStatuses;
            unset($arStatuses);
        }
        return $arRequests;
    }

    private function getCompany(int $companyId)
    {
        $arCompany = Company::getElementByID($companyId);
        if (!empty($arCompany)) {
            return $arCompany;
        } else {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }
    }

    private function getContract()
    {
        $arContract = Contract::getElementByID($this->contractId);
        if (!empty($arContract)) {
            $this->arResult['CONTRACT'] = $arContract;
        } else {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }
    }

}