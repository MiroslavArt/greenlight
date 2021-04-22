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
    private $arDocument;
    private $lostId;
    private $companyId;
    private $contractId;
    private $documentId;
    private $errors;

    public function onPrepareComponentParams($arParams)
    {
        $this->companyId = $arParams['CLIENT_ID'];
        $this->contractId = $arParams['CONTRACT_ID'];
        $this->lostId = $arParams['LOST_ID'];
        $this->documentId = $arParams['LOST_DOCUMENT_ID'];

        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $arResult =& $this->arResult;

        if ($this->getDocument()) {
            $arResult['DOCUMENT'] = $this->arDocument;
        }

        if(!empty($arResult['DOCUMENT']['PROPERTIES']['REQUEST_AUTHOR']['VALUE'])) {
            $rsUser = CUser::GetByID($arResult['DOCUMENT']['PROPERTIES']['REQUEST_AUTHOR']['VALUE']);
            $arUser = $rsUser->Fetch();
            if($arUser) {
                $arResult['REQUEST_USER'] = $arUser;
                $arResult['REQUEST_USER_FIO'] = \CUser::formatName('#NAME#&nbsp #SECOND_NAME#&nbsp #LAST_NAME#&nbsp', $arUser, false, false);
            }
        }
        //Document Files
        $arResult['HISTORY'] = $this->getHistory();

        if (isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $arResult['IS_AJAX'] = 'Y';
        }

        $APPLICATION->SetTitle(GetMessage('LOST_DOCUMENT_HISTORY') . ' - ' . $arResult['DOCUMENT']['NAME']);

        $this->includeComponentTemplate();
    }

    private function getDocument()
    {
        $arDocument = LostDocuments::getElementByID($this->documentId);
        if (empty($arDocument)) {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
        }

        //Статус
        $tableName = $arDocument['PROPERTIES']['STATUS']['USER_TYPE_SETTINGS']['TABLE_NAME'];
        $arDocumentStatus = [];
        if ($tableName) {
            $objDocumentStatus = new \Itrack\Custom\Highloadblock\HLBWrap($tableName);
            $rsStatus = $objDocumentStatus->getList([
                "filter" => array('UF_XML_ID' => $arDocument['PROPERTIES']['STATUS']['VALUE']),
                "select" => array("*"),
                "order" => array("ID" => "ASC")
            ]);

            while ($arStatus = $rsStatus->fetch()) {
                $arDocument['PROPERTIES']['STATUS']['VALUE'] = $arStatus;
            }
        }

        $this->arDocument = $arDocument;
        unset($arDocument);

        return true;
    }


    private function getHistory()
    {
        //ToDo переделать выборку файлов
        $arHistory = [];
        $objHistory = new \Itrack\Custom\Highloadblock\HLBWrap('e_history_lost_document_status');
        $rsHistory = $objHistory->getList([
            "filter" => array('UF_LOST_ID' => $this->lostId, 'UF_LOST_DOC_ID' => $this->documentId),
            "select" => array("*"),
            "order" => array("UF_DATE" => "DESC")
        ]);

        while ($arStatus = $rsHistory->fetch()) {
            $arHistory[] = $arStatus;
            $arUserIDs[] = $arStatus['UF_USER_ID'];
        }

        //Статусы
        $objStatus = new \Itrack\Custom\Highloadblock\HLBWrap('e_lost_doc_status');
        $rsStatus = $objStatus->getList([
            "select" => array("*"),
            "order" => array("ID" => "ASC")
        ]);

        while ($arStatus = $rsStatus->fetch()) {
            $arStatuses[$arStatus['ID']] = $arStatus;
        }

        if (!empty($arUserIDs)) {
            $filter = ["ID" => implode("|", $arUserIDs)];
            $rsUsers = \CUser::GetList(($by = "NAME"), ($order = "desc"), $filter);
            while ($arUser = $rsUsers->Fetch()) {
                $arUsers[$arUser['ID']] = \CUser::formatName('#NAME#&nbsp #SECOND_NAME#&nbsp #LAST_NAME#&nbsp', $arUser, false, false);
            }
        }

        foreach ($arHistory as &$arItem) {
            $arItem['USER_FIO'] = $arUsers[$arItem['UF_USER_ID']];
            $arItem['STATUS'] = $arStatuses[$arItem['UF_CODE_ID']];
        }

        return $arHistory;
    }

}