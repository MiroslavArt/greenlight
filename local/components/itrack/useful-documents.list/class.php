<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Contract;
use \Itrack\Custom\InfoBlocks\UsefulDocuments;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\GroupTable;
use \Bitrix\Main\Context;
use Itrack\Custom\CUserEx;
use \Itrack\Custom\CUserRole;

class ItrUsefulDocumentsList extends CBitrixComponent
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
        $this->companyId = $arParams['ELEMENT_ID'];
        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $arResult =& $this->arResult;

        $arFilter = [
            "ACTIVE" => 'Y',
            "PROPERTY_COMPANY_ID" => $this->companyId
            ];

        if(isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $arResult['IS_AJAX'] = 'Y';
        }

        if(isset($this->request['action'])) {
            $this->arParams['ACTION'] = $this->request['action'];
        }

        if($this->arParams['ACTION'] == 'add') {
            if($this->addDocument()) {
                $arResult['success'] = true;
                $arResult['reload'] = true;
                $APPLICATION->RestartBuffer();
                echo  json_encode($arResult);
                die();
            }
        }

        //удаляем привязки
        if($this->arParams['ACTION'] == 'unlink' && !empty($this->request['doc_id'])) {
            if($this->unlinkDocuments($this->request['doc_id'])) {
                $arResult['success'] = true;
                $arResult['reload'] = true;
                $APPLICATION->RestartBuffer();
                echo  json_encode($arResult);
                die();
            }
        }

        $this->getDocumentsList($arFilter);

        $arResult['DOCUMENT_TYPES'] = $this->getDocumentTypes();

        //get company
        $arResult['COMPANY'] = $this->getCompany($this->companyId);

        $APPLICATION->SetTitle($arResult['COMPANY']['NAME'] . ' - ' . GetMessage('USEFUL_DOCUMENTS'));

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

    private function getDocumentsList(array $arFilter = [])
    {
        $arResult =& $this->arResult;

        $elements = UsefulDocuments::getElementsByConditions($arFilter, [], []);

        if(empty($elements)) {
            return false;
        }

        $arResult['DOCUMENTS'] = $elements;

        unset($elements);
    }

    private function getDocumentTypes() {
        return UsefulDocuments::getPropertyList('DOC_TYPE');
    }

    private function addDocument() {

        $arResult =& $this->arResult;


        if ( 0 < $_FILES['doc_file']['error'] ) {
            $this->addError($_FILES['doc_file']['error']);
        }
        else {
            $file = [
                'name' => $_FILES["doc_file"]['name'],
                'size' => $_FILES["doc_file"]['size'],
                'tmp_name' => $_FILES["doc_file"]['tmp_name'],
                'type' => $_FILES["doc_file"]['type']
            ];
        }

        $date = date("d.m.Y H:i:s");

        $arProperties['FILE'] = $file;
        $arProperties['DOC_TYPE'] = $_REQUEST['doc_type'];
        $arProperties['DATE_TO_LOAD'] = $date;
        $arProperties['DATE_LOADED'] = $date;

        if(!empty($this->arParams['CONTRACT_ID'])) {
            $arProperties['CONTRACT_ID'] = $this->arParams['CONTRACT_ID'];
        }

        if(!empty($this->arParams['ELEMENT_ID'])) {
            $arProperties['COMPANY_ID'] = $this->arParams['ELEMENT_ID'];
        }

        $arFields = Array(
            "DATE_CREATE"  => date("d.m.Y H:i:s"),
            "CREATED_BY"    => $GLOBALS['USER']->GetID(),
            "PROPERTY_VALUES"=> $arProperties,
            "NAME"           => strip_tags($_REQUEST['doc_name']),
            "ACTIVE"         => "Y",
        );

        $result = UsefulDocuments::createElement($arFields, []);

        if(intval($result) > 0) {
            return true;
        } else {
            $arResult['ERRORS'][] = $result;
            return false;
        }

    }

    /**
     * Удаляем привязку к данной компании или договору
     * @param array $arIds массив идентификаторов документов
     * @return bool
     */
    private function unlinkDocuments($arIds) {
        $elements = UsefulDocuments::getElementsByConditions(['ID' => $arIds], [], []);

        if(empty($elements)) {
            return false;
        }

        foreach ($elements as $element) {
            if (!empty($this->companyId)) {
                $propertyCode = "COMPANY_ID";
            }
            if (!empty($this->contractId)) {
                $propertyCode = "CONTRACT_ID";
            }

            \CIBlockElement::SetPropertyValuesEx($element['ID'], false, array($propertyCode => ''));
        }

        return true;
    }


}