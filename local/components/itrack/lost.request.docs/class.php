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

class ItrLostRequestDocs extends CBitrixComponent
{
    private $errors;

    public function onPrepareComponentParams($arParams)
    {
        global $USER;
        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $arResult =& $this->arResult;

        if($this->arParams['LOST_DOC']) {
            $this->getDocumentsList();
        }

        $this->includeComponentTemplate();
    }



    private function getDocumentsList(array $arFilter = [])
    {
        $arResult =& $this->arResult;

        $arDocuments = [];
        $objDocuments = new \Itrack\Custom\Highloadblock\HLBWrap('uploaded_docs');
        $rsDocuments = $objDocuments->getList([
            "filter" => array('UF_LOST_ID' => $this->arParams['LOST_DOC'], 'UF_DOC_TYPE' => 1),
            "select" => array("*"),
            "order" => array("UF_DATE_CREATED" => "DESC")
        ]);

        while ($arDocument = $rsDocuments->fetch()) {
            if(intval($arDocument['UF_FILE']) > 0) {
                $arDocument['FILE'] =  \CFile::GetFileArray($arDocument['UF_FILE']);
            }
            $rsUser = \CUser::GetByID($arDocument['UF_USER_ID']);
            $arUser = $rsUser->Fetch();
            $arDocument['USER_FIO'] = $arUser['NAME'].' '.$arUser['LAST_NAME'];

            $arDocuments[] = $arDocument;
        }
        $arResult['DOCUMENTS'] = $arDocuments;

        unset($arDocuments);
    }

}