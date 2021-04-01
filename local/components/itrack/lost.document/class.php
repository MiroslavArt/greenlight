<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\Highloadblock\HLBWrap;
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
use Itrack\Custom\Participation\CLost;
use Itrack\Custom\Participation\CParticipation;


class ItrLostDocument extends CBitrixComponent
{
    private $arDocument;
    private $lostId;
    private $companyId;
    private $contractId;
    private $documentId;
    private $errors;
    private $statuschanged;
    private $uid;
    private $isclient;
    private $showaccept;
    private $showdecline;

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

        if($this->processforWf($arResult['DOCUMENT'])) {
            $arResult['CURUSER'] = $this->uid;
            $arResult['ISCLIENT'] = $this->isclient;
            $arResult['SHOWACCEPT'] = $this->showaccept;
            $arResult['SHOWDECLINE'] = $this->showdecline;
        }

        if($this->statuschanged) {
            if ($this->getDocument()) {
                $arResult['DOCUMENT'] = $this->arDocument;
            }
        }

        if(!empty($arResult['DOCUMENT']['PROPERTIES']['REQUEST_AUTHOR']['VALUE'])) {
            $rsUser = CUser::GetByID($arResult['DOCUMENT']['PROPERTIES']['REQUEST_AUTHOR']['VALUE']);
            $arUser = $rsUser->Fetch();
            if($arUser) {
                $arResult['REQUEST_USER'] = $arUser;
                $arResult['REQUEST_USER_FIO'] = \CUser::formatName('#LAST_NAME#&nbsp;#NAME_SHORT#&nbsp;#SECOND_NAME_SHORT# ', $arUser, false, false);
            }
        }
        //Document Files
        $arResult['DOCUMENTS'] = $this->getDocuments();

        if (isset($this->request['is_ajax']) && $this->request['is_ajax'] == 'y') {
            $arResult['IS_AJAX'] = 'Y';
        }

        $APPLICATION->SetTitle(GetMessage('LOST_DOCUMENT') . ' - ' . $arResult['DOCUMENT']['NAME']);

        $this->includeComponentTemplate();
    }

    private function processforWf($document)
    {
        global $USER;
        $usid = $USER->getID();
        $arGroups = \CUser::GetUserGroup($usid);
        $dateupdate = date("d.m.Y. H:i:s");
        $PROP = [];

        $status = $document['PROPERTIES']['STATUS']['VALUE']['ID'];
        $lostdocid = $document['ID'];
        $lostid = $document['PROPERTY_23'];

        $iskurator = false;
        $issupusrcl = false;
        $this->uid = $usid;
        $this->statuschanged = false;
        $this->isclient = false;
        $this->showaccept = false;
        $this->showdecline = false;

        $participation = new CParticipation(new CLost($lostid));
        $partips = $participation->getParticipants();

        foreach($partips as $partip) {
            $curators = $partip['PROPERTIES']['CURATORS']['VALUE'];
            if(in_array($usid, $curators)) {
                $iskurator = true;
                break;
            }
        }

        if($iskurator==true) {
            if(in_array(CL_GROUP, $arGroups)) {
                $this->isclient = true;
            }
            if(in_array(CL_SU_GROUP, $arGroups)) {
                $issupusrcl = true;
            }
        }

        if($status==1 && $this->isclient) {
            $this->showaccept = true;
        }

        if($status==2 && $issupusrcl) {
            $newstatus = '3';
            $this->statuschanged = true;
            $this->showaccept = true;
            $this->showdecline = true;
        }

        if($status==3 && $issupusrcl) {
            $this->showaccept = true;
            $this->showdecline = true;
        }

        if($this->statuschanged) {
            $PROP[27] = $newstatus;
            $PROP[61] = $dateupdate;
            LostDocuments::updateElement($lostdocid, [], $PROP);
            $objHistory = new HLBWrap('e_history_lost_document_status');
            $histdata = [
                'UF_CODE_ID' => $newstatus,
                'UF_DATE' => $dateupdate,
                'UF_LOST_ID' => $lostid,
                'UF_LOST_DOC_ID' => $lostdocid,
                'UF_USER_ID' =>$usid
            ];
            $id = $objHistory->add($histdata);
        }

        return true;
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


    private function getDocuments()
    {
        //ToDo переделать выборку файлов
        $arDocuments = [];
        $objDocuments = new \Itrack\Custom\Highloadblock\HLBWrap('uploaded_docs');
        $rsDocuments = $objDocuments->getList([
            "filter" => array('UF_LOST_ID' => $this->documentId, 'UF_DOC_TYPE' => 2),
            "select" => array("*"),
            "order" => array("UF_DATE_CREATED" => "DESC")
        ]);

        while ($arDocument = $rsDocuments->fetch()) {
            if(intval($arDocument['UF_FILE']) > 0) {
                $arDocument['FILE'] =  \CFile::GetFileArray($arDocument['UF_FILE']);
            }
            $arDocuments[] = $arDocument;
        }

        return $arDocuments;
    }

}