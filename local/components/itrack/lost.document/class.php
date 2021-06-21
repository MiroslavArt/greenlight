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
    private $shpworiginalpanel;
    private $originalstatuset;
    private $originalgot;
    private $showadd;
    private $declinestatus;

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
            $arResult['SHOWORIGINAL'] = $this->shpworiginalpanel;
            $arResult['ORIGINALSTATUSSET'] = $this->originalstatuset;
            $arResult['ORIGINALGOT'] =  $this->originalgot;
            $arResult['SHOWADD'] =  $this->showadd;
            $arResult['DECLINESTATUS'] =  $this->declinestatus;
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
                $arResult['REQUEST_USER_FIO'] = \CUser::formatName('#NAME#&nbsp #SECOND_NAME#&nbsp #LAST_NAME#&nbsp', $arUser, false, false);
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
        $isbroker = false;
        $issupbroker = false;
        $isins = false;
        $isadj = false;
        $this->uid = $usid;
        $this->statuschanged = false;
        $this->isclient = false;
        $this->showaccept = false;
        $this->showdecline = false;
        $this->shpworiginalpanel = false;
        $this->originalstatuset = false;
        $this->originalgot = false;
        $this->showadd = false;
        $this->declinestatus = '';

        if($document['PROPERTIES']['GET_ORIGINAL']['VALUE'] == 'Да') {
            $this->shpworiginalpanel = true;
            if($status==12) {
                if($isbroker || $this->isclient) {
                    $this->originalstatuset = true;
                }
            } elseif ($status==14) {
                $this->originalgot = true;
            }
        }

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
            if(in_array(SB_GROUP, $arGroups)) {
                $isbroker = true;
            }
            if(in_array(SB_SU_GROUP, $arGroups)) {
                $issupbroker  = true;
            }
            if(in_array(INS_GROUP, $arGroups)) {
                $isins = true;
            }
            if(in_array(AJ_GROUP, $arGroups)) {
                $isadj = true;
            }
        }

        if($status==1) {
            $objHistory = new \Itrack\Custom\Highloadblock\HLBWrap('e_history_lost_document_status');
            $rsHistory = $objHistory->getList([
                "filter" => array('UF_LOST_ID' => $this->lostId, 'UF_LOST_DOC_ID' => $this->documentId),
                "select" => array("*"),
                "order" => array("ID" => "DESC")
            ]);
            $countstatus = 1;
            while ($arStatus = $rsHistory->fetch()) {
                if($countstatus == 2) {
                    $objStatus = new \Itrack\Custom\Highloadblock\HLBWrap('e_lost_doc_status');
                    $rsStatus = $objStatus->getList([
                        "filter" => array("ID"=>$arStatus['UF_CODE_ID'])
                    ])->fetch();
                    $this->declinestatus = $rsStatus['UF_NAME'];
                    break;
                }
                $countstatus++;
            }

            if($this->isclient) {
                $this->showaccept = true;
                $this->showadd = true;
            }
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

        if($status==4) {
            if($isbroker) {
                $this->statuschanged = true;
                $this->showaccept = true;
                $this->showdecline = true;
                if(!$issupbroker) {
                    $newstatus = '6';
                } else {
                    $newstatus = '9';
                }
            }
        }

        if($status==6) {
            if($isbroker) {
                $this->showaccept = true;
                $this->showdecline = true;
            }
        }

        if($status==7 || $status==9) {
            if($issupbroker) {
                $this->statuschanged = true;
                $this->showaccept = true;
                $this->showdecline = true;
                $newstatus = '9';
            }
        }

        if($status==10) {
            if($isadj || $isins) {
                $this->statuschanged = true;
                //$this->showaccept = true;
                //$this->showdecline = true;
                $newstatus = '12';
            }
            if($isbroker || $this->isclient) {
                if($this->shpworiginalpanel) {
                    $this->originalstatuset = true;
                }
            }
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