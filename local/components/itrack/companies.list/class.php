<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\Company;

class ItrCompaniesList extends CBitrixComponent
{

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function executeComponent()
    {
        $arFilter = [];
        if(isset($this->request['q_name'])) {
            $arFilter = [
                "NAME" => "%" . trim($this->request['q_name']) . "%"
            ];
            $this->arResult['ACTION'] = 'search';
        }

        if(!empty($this->arParams['TYPE_ID'])) {
            $arFilter['PROPERTY_TYPE'] = $this->arParams['TYPE_ID'];
        }

        if(isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $this->arResult['IS_AJAX'] = 'Y';
        }

        $this->getClients($arFilter);
        $this->includeComponentTemplate();
    }

    private function getClients(array $arFilter = [])
    {
        $arResult =& $this->arResult;
        //$elements = Company::getFilteredList($arFilter);

        $elements = Company::getElementsByConditions($arFilter, [], [],  $this->arParams["DETAIL_URL"]);
        //Utils::varDump($this->arParams["DETAIL_URL"], "", $this->arParams["IBLOCK_URL"]);

        foreach ($elements as $element) {
            /*$arItem = [
                'ID' => $element->get('ID'),
                'NAME' => $element->get('NAME'),
                'LOGO' => $element->getPropertyLogo()->getFile()->getId(),
            ];

            $arResult['ITEMS'][$element->get('ID')] = $arItem;*/

            $arItem = [
                'ID' => $element['ID'],
                'NAME' => $element['NAME'],
                'LOGO' => $element['PROPERTIES']['LOGO']['VALUE'],
                'DETAIL_PAGE_URL' => $element['DETAIL_PAGE_URL'],
            ];
            $arResult['ITEMS'][$element['ID']] = $arItem;
        }
    }

}