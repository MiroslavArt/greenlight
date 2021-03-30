<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Contract;
use Itrack\Custom\InfoBlocks\Lost;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\GroupTable;
use \Bitrix\Main\Context;
use Itrack\Custom\CUserEx;
use \Itrack\Custom\CUserRole;

class ItrLostList extends CBitrixComponent
{
    private $userId;
    private $userRole;
    private $companyId;

    private $errors;

    public function onPrepareComponentParams($arParams)
    {
        global $USER;
        $this->userId = $USER->GetID();
        $this->userRole = new CUserRole();
        $this->companyId = CUserEx::getUserCompanyId($this->userId);
        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $arResult =& $this->arResult;

        //get company
        $arResult['COMPANY'] = $this->getCompany($this->companyId);

        $arFilter = [
            "ACTIVE" => 'Y',
            ];

        switch ($this->userRole->getUserParty()) {
            case $this->userRole::BROKER:
                $arFilter['=PROPERTY_INSURANCE_BROKER.ID'] = $this->companyId;
                break;
            case $this->userRole::CLIENT:
                $arFilter['=PROPERTY_CLIENT.ID'] = $this->companyId;
                break;
            case ($this->userRole::INSURER):
                $arFilter['=PROPERTY_INSURANCE_COMPANY.ID'] = $this->companyId;
                break;
            case ($this->userRole::ADJUSTER):
                $arFilter['=PROPERTY_ADJUSTER.ID'] = $this->companyId;
                break;
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

        $this->getLostList($arFilter);

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

    private function getContract() {
        $arContract = Contract::getElementByID($this->contractId);
        if(!empty($arContract)) {
            $this->arResult['CONTRACT'] = $arContract;
        } else {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }
    }

    private function getLostList(array $arFilter = [])
    {
        $arResult =& $this->arResult;
        $selectElement =   ['ID', 'IBLOCK_ID', 'ACTIVE', 'SORT', 'IBLOCK_SECTION', 'CODE', 'DATE_CREATE', 'TIMESTAMP_X', 'NAME', 'PREVIEW_TEXT', 'DETAIL_PAGE_URL', 'PROPERTY_*'];
        $elements = Lost::getElementsByConditions($arFilter, [], $selectElement);

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
            $arItem = [
                'ID' => $element['ID'],
                'NAME' => $element['NAME'],
                'DATE' => (new \DateTime($element['DATE_CREATE']))->format('d.m.Y'),
                'STATUS' => $arLostStatus[$element['PROPERTIES']['STATUS']['VALUE']],
                'DESCRIPTION' => $element['PROPERTIES']['DESCRIPTION']['VALUE'],
                'DETAIL_PAGE_URL' => $this->arParams['LIST_URL'] . $element['ID'] . '/',
            ];
            $arResult['LOSTS'][$element['ID']] = $arItem;
        }

        unset($elements);
    }

}