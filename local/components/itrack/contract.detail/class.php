<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Contract;
use Itrack\Custom\InfoBlocks\Lost;

class ItrContract extends CBitrixComponent
{
    private $companyId;
    private $contractId;
    private $errors;

    public function onPrepareComponentParams($arParams)
    {
        $this->companyId = $arParams['CLIENT_ID'];
        $this->contractId = $arParams['CONTRACT_ID'];

        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $arResult =& $this->arResult;

        $this->getContract();

        //get company
        $arResult['COMPANY'] = $this->getCompany($this->companyId);
        //get insurance company
        $insarray = [];
        foreach($arResult['CONTRACT']['PROPERTIES']['INSURANCE_COMPANY']['VALUE'] as $insco) {
            $inscodata = $this->getCompany($insco);
            array_push($insarray, $inscodata);
        }
        $arResult['INSURANCE_COMPANIES'] = $insarray;
        $arResult['INSURANCE_COMPANY'] = $this->getCompany($arResult['CONTRACT']['PROPERTIES']['INSURANCE_COMPANY_LEADER']['VALUE']);
        $arResult['BROKER'] = $this->getCompany(current($arResult['CONTRACT']['PROPERTIES']['INSURANCE_BROKER']['VALUE']));

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
                'DATE' => (new \DateTime($element['DATE_CREATE']))->format('d.m.Y'),
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
}