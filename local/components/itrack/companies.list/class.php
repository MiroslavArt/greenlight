<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Lost;

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

            if($this->arParams['TYPE_ID'] == 2) {
                $arFilter2 = [
                    "ACTIVE" => 'Y',
                    "PROPERTY_INSURANCE_COMPANY.ID" => [$element['ID']],
                ];
            } else if($this->arParams['TYPE_ID'] == 3) {
                $arFilter2 = [
                    "ACTIVE" => 'Y',
                    "PROPERTY_ADJUSTER.ID" => [$element['ID']],
                ];
            } else if($this->arParams['TYPE_ID'] == 4) {
                $arFilter2 = [
                    "ACTIVE" => 'Y',
                    "PROPERTY_CLIENT.ID" => [$element['ID']],
                ];
            }
            $elements2 = Lost::getElementsByConditions($arFilter2, [], []);

            //\Bitrix\Main\Diag\Debug::writeToFile($fid, "fid", "__miros.log");
            $arItem = [
                'ID' => $element['ID'],
                'NAME' => $element['NAME'],
                'LOGO' => $element['PROPERTIES']['LOGO']['VALUE'],
                'DETAIL_PAGE_URL' => $element['DETAIL_PAGE_URL'],
                'ALL_LOST' => count($elements2)
            ];
            $arResult['ITEMS'][$element['ID']] = $arItem;
        }
    }

}