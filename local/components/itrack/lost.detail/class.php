<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\Highloadblock\HLBWrap;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Contract;
use Itrack\Custom\InfoBlocks\Lost;
use \Bitrix\Iblock\Elements\ElementCompanyTable;
use \Bitrix\Main\Entity\Query;
use \Bitrix\Main\Entity\ReferenceField;
use \Bitrix\Main\ORM\Query\Join;
use \Bitrix\Main\UserGroupTable;
use Itrack\Custom\Participation\CLost;
use Itrack\Custom\Participation\CParticipation;
use \Itrack\Custom\UserFieldValueTable;
use \Itrack\Custom\CUserRole;
use Itrack\Custom\InfoBlocks\LostDocuments;


class ItrLost extends CBitrixComponent
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
        if(!empty($this->arParams['CONTRACT_ID'])) {
            $this->getContract();
        }

        //get company
        if(!empty($this->arParams['CLIENT_ID'])) {
            $arResult['COMPANY'] = $this->getCompany($this->companyId);
            //get insurance company
            if (!empty($arResult['CONTRACT']['PROPERTIES']['INSURANCE_COMPANY_LEADER']['VALUE'])) {
                $arResult['INSURANCE_COMPANY'] = $this->getCompany($arResult['CONTRACT']['PROPERTIES']['INSURANCE_COMPANY_LEADER']['VALUE']);
            }
        }
        //curators
        $arCurators = $this->getCurators();
        if (!empty($arCurators)) {
            $arResult['CURATORS'] = $arCurators;
            unset($arCurators);
        }
        //Lost Requests
        $arResult['REQUESTS'] = $this->getRequests();

        // ins companies
        $insarray = [];
        foreach($arResult['LOST']['PROPERTIES']['INSURANCE_COMPANY']['VALUE'] as $insco) {
            $inscodata = $this->getCompany($insco);
            array_push($insarray, $inscodata);
        }
        $arResult['INSURANCE_COMPANIES'] = $insarray;
        $arResult['INSURANCE_COMPANY'] = $this->getCompany($arResult['LOST']['PROPERTIES']['INSURANCE_COMPANY_LEADER']['VALUE']);

        // adjusters
        $adjarray = [];
        foreach($arResult['LOST']['PROPERTIES']['ADJUSTER']['VALUE'] as $adjco) {
            $adjcodata = $this->getCompany($adjco);
            array_push($adjarray, $adjcodata);
        }
        $arResult['ADJUSTER_COMPANIES'] = $adjarray;
        $arResult['ADJUSTER_COMPANY'] = $this->getCompany($arResult['LOST']['PROPERTIES']['ADJUSTER_LEADER']['VALUE']);


        $arResult['BROKER'] = $this->getCompany(current($arResult['LOST']['PROPERTIES']['INSURANCE_BROKER']['VALUE']));



        if (isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $arResult['IS_AJAX'] = 'Y';
        }

        $arResult['CONTRACT_PAGE_URL'] = $this->arParams['LIST_URL'] . $this->arParams['CLIENT_ID'] . '/contract/' . $this->arParams['CONTRACT_ID'] . '/';

        if($this->arParams['CURATORS_MODE'] == 'Y') {
            $APPLICATION->SetTitle(GetMessage('ALL_LOST_CURATORS') . ' - ' . $arResult['LOST']['NAME']);
        } else {
            $APPLICATION->SetTitle(GetMessage('LOST_CARD') . ' - ' . $arResult['LOST']['NAME']);
        }


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
        //$arCurators = \Itrack\Custom\InfoBlocks\LostUsers::getElementsByConditions(['PROPERTY_LOST' => [$this->arLost['ID']]]);
        $participation = new CParticipation(new CLost($this->arLost['ID']));

        $arCurators  = $participation->getParticipants();
        if (empty($arCurators)) {
            return false;
        }

        $this->arResult['CURATORS_LEADERS'] = [];
        $arCuratorsIds = [];

        foreach ($arCurators as $arCurator) {
            //$arCuratorsIds[] = $arCurator['PROPERTIES']['CURATORS']['VALUE'];
            $arCuratorsIds = array_merge($arCuratorsIds, $arCurator['PROPERTIES']['CURATORS']['VALUE']);
            if(!empty($arCurator['PROPERTIES']['CURATOR_LEADER']['VALUE']) && intval($arCurator['PROPERTIES']['CURATOR_LEADER']['VALUE']) > 0) {
                $this->arResult['CURATORS_LEADERS'][$arCurator['PROPERTIES']['CURATOR_LEADER']['VALUE']] = $arCurator['PROPERTIES']['CURATOR_LEADER']['VALUE'];
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
            if(in_array($userId, $this->arResult['CURATORS_LEADERS'])) {
                $items[$userId]['IS_LEADER'] = 'Y';
            }
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
            $arRequest['DETAIL_PAGE_URL'] = $this->arParams['PATH_TO']['lost'] . 'lost-document-' . $arRequest['ID'] . '/';
            //Documents Statuses
            $this->arResult['DOCS_STATUSES'][$arStatuses[$arRequest['PROPERTIES']['STATUS']['VALUE']]['ID']]['DOCS'][] = $arRequest["ID"];

            $objDocuments = new HLBWrap('uploaded_docs');
            $rsDocument = $objDocuments->getList([
                "filter" => array('UF_LOST_ID' => $arRequest['ID'], 'UF_DOC_TYPE' => 2),
                "select" => array("*"),
                "order" => array("UF_DATE_CREATED" => "ASC")
            ])->fetch();
            if($rsDocument) {
                $arRequest['INFO_PROVIDED'] = date("d.m.Y", strtotime($rsDocument['UF_DATE_CREATED']));
            }

            $objHistory = new HLBWrap('e_history_lost_document_status');
            $rsHistory = $objHistory->getList([
                "filter" => array('UF_LOST_DOC_ID' => $arRequest['ID']),
                "select" => array("*"),
                "order" => array("ID" => "DESC")
            ]);
            while ($arHistory = $rsHistory->fetch()) {
                if($arHistory['UF_COMMENT']) {
                    $arRequest['REJECTIONS'][] = 'Замечание от:'.date("d.m.Y", strtotime($arHistory['UF_DATE'])).
                            '.Текст: '.$arHistory['UF_COMMENT'];
                }
            }
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