<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Contract;
use Itrack\Custom\InfoBlocks\Lost;
use \Itrack\Custom\InfoBlocks\UsefulDocuments;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\GroupTable;
use \Bitrix\Main\Context;
use Itrack\Custom\CUserEx;
use \Itrack\Custom\CUserRole;

class ItrLostAllAjusters extends CBitrixComponent
{
    private $errors;
    private $arLost;
    private $lostId;

    public function onPrepareComponentParams($arParams)
    {
        global $USER;
        $this->lostId = $arParams['LOST_DOC'];
        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $arResult =& $this->arResult;

        if ($this->getLost()) {
            $arResult['LOST'] = $this->arLost;
        }

        // adjusters
        $adjarray = [];
        foreach($arResult['LOST']['PROPERTIES']['ADJUSTER']['VALUE'] as $adjco) {
            $adjcodata = $this->getCompany($adjco);
            array_push($adjarray, $adjcodata);
        }
        $arResult['ADJUSTER_COMPANIES'] = $adjarray;

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

    private function getCompany(int $companyId)
    {
        $arCompany = Company::getElementByID($companyId);
        if (!empty($arCompany)) {
            return $arCompany;
        } else {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }
    }

}