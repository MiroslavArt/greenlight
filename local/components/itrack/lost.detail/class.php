<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Contract;
use Itrack\Custom\InfoBlocks\Lost;

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
        $this->getContract();
        //get company
        $arResult['COMPANY'] = $this->getCompany($this->companyId);
        //get insurance company
        if(!empty($arResult['CONTRACT']['PROPERTIES']['INSURANCE_COMPANY_LEADER']['VALUE'])) {
            $arResult['INSURANCE_COMPANY'] = $this->getCompany($arResult['CONTRACT']['PROPERTIES']['INSURANCE_COMPANY_LEADER']['VALUE']);
        }

        if(isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $arResult['IS_AJAX'] = 'Y';
        }

        $arResult['CONTRACT_PAGE_URL'] = $this->arParams['LIST_URL'] . $this->arParams['CLIENT_ID'] . '/contract/' . $this->arParams['CONTRACT_ID'] . '/';
        $APPLICATION->SetTitle(GetMessage('LOST_CARD') . ' - ' . $arResult['LOST']['NAME']);

        $this->includeComponentTemplate();
    }

    private function getLost() {
        $arLost = Lost::getElementByID($this->lostId);
        if(!empty($arLost)) {
            $this->arLost = $arLost;
            unset($arLost);
            return true;
        } else {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }
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

}