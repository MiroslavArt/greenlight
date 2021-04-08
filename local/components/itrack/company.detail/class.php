<?php

use \Bitrix\Main\Loader;
use \Bitrix\Highloadblock as HL;
use \Bitrix\Main\Entity;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Contract;
use Itrack\Custom\InfoBlocks\Lost;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\SystemException;
use \Itrack\Custom\Highloadblock\HLBWrap;


class ItrCompany extends CBitrixComponent
{
    private $companyId;

    public function onPrepareComponentParams($arParams)
    {
        $this->companyId = $arParams['CLIENT_ID'];
        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $arResult =& $this->arResult;

        //get company
        $this->getCompany();

        if(!empty($arResult['COMPANY']['PROPERTIES']['TYPE']['VALUE_XML_ID'])) {
            $arResult['COMPANY_TYPE'] = $arResult['COMPANY']['PROPERTIES']['TYPE']['VALUE_XML_ID'];
        } else {
            $arResult['ERRORS'][] = Loc::getMessage('COMPANY_TYPE_IS_EMPTY');
        }

        $arFilter = [
            "ACTIVE" => 'Y',
            "PROPERTY_CLIENT.ID" => [$this->companyId],
            ];

        if(isset($this->request['q_name'])) {
            $arFilter = [
                array("LOGIC" => "OR",
                    array("NAME" => "%" . trim($this->request['q_name']) . "%"),
                    array("PROPERTY_DATE" => "%" . trim($this->request['q_name']) . "%"),
                    array("PROPERTY_TYPE" => "%" . trim($this->request['q_name']) . "%")
                )
            ];
            $this->arResult['ACTION'] = 'search';
        }

        if(isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $this->arResult['IS_AJAX'] = 'Y';
        }

        $this->getContractsList($arFilter);

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

    private function getContractsList(array $arFilter = [])
    {
        $arResult =& $this->arResult;
        $elements = Contract::getElementsByConditions($arFilter, [], []);

        foreach ($elements as $element) {


            $arFilter2 = [
                "ACTIVE" => 'Y',
                "PROPERTY_CONTRACT" => $element['ID'],
            ];

            $elements2 = Lost::getElementsByConditions($arFilter2, [], []);

            $all = 0;
            $red = 0;
            $yellow = 0;
            $green = 0;

            foreach ($elements2 as $elitem) {
                $all++;
                if($elitem['PROPERTIES']['STATUS']['VALUE']=='red') {
                    $red++;
                } elseif ($elitem['PROPERTIES']['STATUS']['VALUE']=='yellow') {
                    $yellow++;
                } elseif ($elitem['PROPERTIES']['STATUS']['VALUE']=='green') {
                    $green++;
                }
            }

            $arItem = [
                'ID' => $element['ID'],
                'NAME' => $element['NAME'],
                'DATE' => $element['PROPERTIES']['DATE']['VALUE'],
                'TYPE' => $element['PROPERTIES']['TYPE']['VALUE'],
                'INSURANCE_COMPANY_LEADER' => $element['PROPERTIES']['INSURANCE_COMPANY_LEADER']['VALUE'],
                'INSURANCE_COMPANY_LEADER_NAME' => $element['PROPERTY_INSURANCE_COMPANY_LEADER_NAME'],
                'DETAIL_PAGE_URL' => $this->arParams['LIST_URL'] . $this->arParams['CLIENT_ID'] . '/contract/' . $element['ID'] . '/',
                'ALL_LOST' => $all,
                'R_LOST' => $red,
                'Y_LOST' => $yellow,
                'G_LOST' => $green
            ];

            $arResult['CONTRACTS'][$element['ID']] = $arItem;
        }
        unset($elements);
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