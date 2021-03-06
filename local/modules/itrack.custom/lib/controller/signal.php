<?php
namespace Itrack\Custom\Controller;

use Bitrix\Main;
use Bitrix\Crm;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Request;
use Itrack\Custom\CUserRole;
use Itrack\Custom\Highloadblock\HLBWrap;
use Itrack\Custom\Participation\ATarget;
use Itrack\Custom\Participation\CParticipation;
use Itrack\Custom\Participation\CContract;
use Itrack\Custom\Participation\CLost;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\Participation\CContractParticipant;
use Itrack\Custom\Participation\CLostParticipant;
use Itrack\Custom\InfoBlocks\Contract;
use Itrack\Custom\InfoBlocks\Lost;
use Itrack\Custom\InfoBlocks\LostDocuments;
use Itrack\Custom\UserAccess\CUserAccess;
use Itrack\Custom\CNotification;

class Signal extends Controller
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        $this->checkModules();

    }

    /**
     * @throws Main\LoaderException
     */
    protected function checkModules()
    {
        if (!Loader::includeModule('iblock')) {
            throw new Main\LoaderException('not install module iblock');
        }

        if (!Loader::includeModule('itrack.custom')) {
            throw new Main\LoaderException('not install module itrack.custom');
        }

    }


    public function getSignalAction($location)
    {
        //$signalarr = [1,2,3];
        return $location;
    }

    public function getCompaniesAction($type)
    {
        $result = [];

        $arSelect = Array("ID", "NAME");
        $arFilter = Array("IBLOCK_ID"=>1, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_TYPE"=>$type);
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        while($ob = $res->fetch())
        {
            $item = [];
            $item['value'] = $ob['ID'];
            $item['label'] = $ob['NAME'];

            array_push($result, $item);
        }

        return $result;
    }

    public function getUsersAction($company)
    {
        $result = [];

        $filter = Array
        (
            "ACTIVE" => "Y",
            "UF_COMPANY" => $company
        );

        $params = Array
        (
            'SELECT' =>  array("UF_*")
        );


        $rsUser = \CUser::GetList(($by="ID"), ($order="desc"), $filter, $params);
        // ?????????????? ???????????? ????????????????????
        $users = array();

        while ($ob = $rsUser->Fetch()) {
            $item = [];
            $item['value'] = $ob['ID'];
            $item['label'] = $ob['NAME'].' '.$ob['LAST_NAME'];
            $item['email'] = $ob['EMAIL'];
            $item['position'] = $ob['WORK_POSITION'];
            $item['wphone'] = $ob['WORK_PHONE'];
            $item['mphone'] = $ob['PERSONAL_MOBILE'];
            $item['companyid'] = $ob['UF_COMPANY'];
            $item['isleader'] = false;

            array_push($result, $item);
        }
        return $result;
    }

    public function getContkuratorsAction($contract)
    {
        $participation = new CParticipation(new CContract($contract));

        $partips = $participation->getParticipants();

        $result = [];

        foreach($partips as $partip) {
            $leader = $partip['PROPERTIES']['CURATOR_LEADER']['VALUE'];
            $curators = $partip['PROPERTIES']['CURATORS']['VALUE'];
            $companyid = $partip['PROPERTIES']['PARTICIPANT_ID']['VALUE'];
            foreach($curators as $user) {
                $rsUser = \CUser::GetByID($user);
                $arUser = $rsUser->Fetch();
                $item = [];
                $item['value'] = $arUser['ID'];
                $item['label'] = $arUser['NAME'].' '.$arUser['LAST_NAME'];
                $item['email'] = $arUser['EMAIL'];
                $item['position'] = $arUser['WORK_POSITION'];
                $item['wphone'] = $arUser['WORK_PHONE'];
                $item['mphone'] = $arUser['PERSONAL_MOBILE'];
                $item['companyid'] = $companyid;
                if($user == $leader) {
                    $item['isleader'] = true;
                } else {
                    $item['isleader'] = false;
                }

                $elements = Company::getElementsByConditions(['ID'=>$companyid], [], []);

                $type = $elements[0]['PROPERTIES']['TYPE']['VALUE_ENUM_ID'];
                $item['typeid'] = $type;
                if ($type == 1) {
                    $item['type'] = 'broker';
                } elseif ($type == 2) {
                    $item['type'] = 'insuer';
                } elseif ($type == 4) {
                    $item['type'] = 'client';
                }
                array_push($result, $item);

            }
        }

        return $result;
    }

    public function getParticipantstargetsAction($type, $participant)
    {
        $result = [];
        if($type == 'contract') {
            $items = CContractParticipant::getElementsByConditions(["PROPERTY_PARTICIPANT_ID" => $participant]);
            foreach($items as $item) {
                $resval = [];
                $tid = $item['PROPERTIES']['TARGET_ID']['VALUE'];
                $resval['ID'] = $tid;
                $cnt = current(Contract::getElementsByConditions(["ID" => $tid]));
                $resval['NAME'] = $cnt['NAME'];
                array_push($result, $resval);
            }
        } elseif($type == 'lost') {
            $items = CLostParticipant::getElementsByConditions(["PROPERTY_PARTICIPANT_ID" => $participant]);
            foreach($items as $item) {
                $resval = [];
                $tid = $item['PROPERTIES']['TARGET_ID']['VALUE'];
                $resval['ID'] = $tid;
                $cnt = current(Lost::getElementsByConditions(["ID" => $tid]));
                $resval['NAME'] = $cnt['NAME'];
                array_push($result, $resval);
            }
        }
        return $result;
    }



    public function addUserAction($userdata)
    {
        $groups = array(REG_GROUP);

        $contracts = array_unique($userdata['contract']);
        $losses = array_unique($userdata['loss']);

        $user = new \CUser;
        $arFields = Array(
            "NAME" => $userdata['lastname'].' '.$userdata['name'].' '.$userdata['secondname'],
            //"LAST_NAME" => $userdata['lastname'],
            //"SECOND_NAME" => $userdata['secondname'],
            "EMAIL" => $userdata['email'],
            "WORK_POSITION" => $userdata['position'],
            "PERSONAL_PHONE" => $userdata['persphone'],
            "WORK_PHONE" => $userdata['workphone'],
            "WORK_FAX" => $userdata['addphone'],
            "LOGIN" => $userdata['email'],
            "ACTIVE" => "Y",
            "GROUP_ID" => $groups,
            "PASSWORD" => $userdata['pwd'],
            "CONFIRM_PASSWORD" => $userdata['pwd'],
            "UF_COMPANY" => $userdata['company']
        );

        $ID = $user->Add($arFields);
        if(intval($ID) > 0) {
            // ?????????????????? ???????? ????????????????????????
            $party = Company::getPartyByCompany($userdata['company']);
            if ($userdata['superuser']=='true') {
                $superuser = true;
            } else {
                $superuser = false;
            }
            $userrole = new CUserRole($ID);
            $userrole->setUserRole($party, $superuser);

            // ?????????????????? ???????????????? ????????????????
            foreach ($contracts as $item) {
                if($item!="N/A") {
                    $participantClass = CContract::getParticipantClass();
                    $participant = $participantClass::initByTargetAndCompany($item, $userdata['company']);
                    $participant->bindCurator($ID);
                }
            }
            // ?????????????????? ???????????????? ????????????
            foreach ($losses as $item) {
                if($item!="N/A") {
                    $participantClass = CLost::getParticipantClass();
                    $participant = $participantClass::initByTargetAndCompany($item, $userdata['company']);
                    $participant->bindCurator($ID);
                }
            }

            $result = 'added';

            \CEvent::Send(
                "USER_INFO",
                SITE_ID,
                [
                    "EMAIL" => $userdata['email'],
                    "NAME" => $userdata['name'],
                    "LAST_NAME" => $userdata['lastname'],
                    "LOGIN" => $userdata['email'],
                    "PWD" => $userdata['pwd'],
                    "USER_ID" => $ID
                ]
            );
        } else {
            $result = strip_tags($user->LAST_ERROR);
        }

        return $result;
    }

    public function addLostdocAction($formdata)
    {
        $result = 'error';

        $reset = false;

        $data = [];
        $data['DATE_ACTIVE_FROM'] = date('d.m.Y');

        global $USER;

        $uid = $USER->GetID();

        if($formdata) {
            foreach ($formdata as $item) {
                if($item['name']=='lostid') {
                    $data['PROPERTY_VALUES']['LOST'] = $item['value'];
                } else if($item['name']=='docname') {
                    $data['NAME'] = $item['value'];
                } else if($item['name']=='docterm') {
                    $data['PROPERTY_VALUES']['REQUEST_DEADLINE'] = $item['value'];
                } else if($item['name']=='author') {
                    $data['PROPERTY_VALUES']['REQUEST_AUTHOR'] = $item['value'];
                } else if($item['name']=='origin') {
                    if($item['value']=='????') {
                        $data['PROPERTY_VALUES']['GET_ORIGINAL'] = '6';
                    }
                } else if($item['name']=='status') {
                    if($item['value']=='yellow') {
                        $reset = true;
                    }
                }
            }
            $dateupdate = date("d.m.Y H:i:s");
            $data['PROPERTY_VALUES']['STATUS'] = '1';
            $data['PROPERTY_VALUES']['STATUS_DATE'] = $dateupdate;


            $ID = LostDocuments::createElement($data, []);
            if(intval($ID) > 0) {
                $objHistory = new HLBWrap('e_history_lost_document_status');
                $histdata = [
                    'UF_CODE_ID' => 1,
                    'UF_DATE' => date("d.m.Y. H:i:s"),
                    'UF_LOST_ID' => $data['PROPERTY_VALUES']['LOST'],
                    'UF_LOST_DOC_ID' => intval($ID),
                    'UF_USER_ID' => $uid
                ];

                $id = $objHistory->add($histdata);
                if($reset) {
                    $PROP[16] = 'red';
                    Lost::updateElement($data['PROPERTY_VALUES']['LOST'], [], $PROP);

                    $objHistory = new HLBWrap('e_history_lost_status');
                    $histdata = [
                        'UF_CODE_ID' => '1',
                        'UF_DATE' => date("d.m.Y. H:i:s"),
                        'UF_LOST_ID' => $data['PROPERTY_VALUES']['LOST'],
                        'UF_USER_ID' => $USER->GetID()
                    ];
                    $objHistory->add($histdata);
                }
                $result = 'added';
                $participation = new CParticipation(new CLost($data['PROPERTY_VALUES']['LOST']));
                $partips = $participation->getParticipants();
                $clients = [];
                foreach ($partips as $partip) {
                    $curators = $partip['PROPERTIES']['CURATORS']['VALUE'];
                    foreach ($curators as $curator) {
                        $isclient = (new CUserRole($curator))->isClient();
                        if($isclient) {
                            $clients[] = $curator;
                        }
                    }
                }
                if($clients) {
                    CNotification::send('doc_added', $clients, $data['PROPERTY_VALUES']['LOST'], $data['PROPERTY_VALUES']['LOST'], $ID);
                }


            } else {
                $result = $ID;
            }
        }

        return $result;
    }

    public function updateLostfilecommentAction($fileid, $newcomment)
    {
        $objDocument = new HLBWrap('uploaded_docs');

        $data = ['UF_COMMENT'=>$newcomment];

        $id = $objDocument->update($fileid, $data);

        //if($id->isSuccess()) {
        return 'success';
        //} else {
        //    return $id->getErrorMessages();
        //}
    }

    public function updateLossdescAction($formdata)
    {
        $PROP = [];

        if($formdata) {
            foreach ($formdata as $item) {
                if ($item['name'] == 'lossdescript') {
                    $PROP['DESCRIPTION'] = $item['value'];
                } elseif($item['name'] == 'lostid') {
                    $lostid = $item['value'];
                }
            }
        }
        Lost::updateElement($lostid, [], $PROP);
        return 'updated';
        //} else {
        //    return $id->getErrorMessages();
        //}
    }

    public function delLostfileAction($fileid)
    {
        $objDocument = new HLBWrap('uploaded_docs');

        $id = $objDocument->delete($fileid);

        if($id->isSuccess()) {
            return 'success';
        } else {
            return $id->getErrorMessages();
        }
    }
    // workflow
    public function acceptLostdocAction($lostid, $lostdocid, $status, $user, $orig)
    {
        $dateupdate = date("d.m.Y. H:i:s");
        $usernotify = [];
        $PROP = [];

        if ($status == 1) {
            $superuserclient = 0;
            $needacceptsupuclient = false;
            $brokerscur = [];

            $arGroups = \CUser::GetUserGroup($user);
            //if (!in_array(CL_SU_GROUP, $arGroups)) {
            $participation = new CParticipation(new CLost($lostid));
            $partips = $participation->getParticipants();
            foreach ($partips as $partip) {
                $curators = $partip['PROPERTIES']['CURATORS']['VALUE'];
                foreach ($curators as $curator) {
                        $arGroups2 = \CUser::GetUserGroup($curator);
                        if (in_array(CL_SU_GROUP, $arGroups2)) {
                            if (!in_array(CL_SU_GROUP, $arGroups)) {
                                $superuserclient = $curator;
                            }
                            //break;
                        } elseif (in_array(SB_GROUP, $arGroups2)) {
                            array_push($brokerscur, $curator);
                    }
                }
            }
            //}
            if ($superuserclient) {
                $needacceptsupuclient = (new CUserAccess($superuserclient))->hasAcceptanceForLost($lostid);
            }
            if ($needacceptsupuclient) {
                $newstatus = '2';
                $usernotify = [$superuserclient];
                $nottempl = 'suc_accept';
            } else {
                $newstatus = '4';
                $usernotify = $brokerscur;
                $nottempl = 'sb_accept';

            }
        } elseif ($status==3) {
            $brokerscur = [];
            $participation = new CParticipation(new CLost($lostid));
            $partips = $participation->getParticipants();
            foreach ($partips as $partip) {
                $curators = $partip['PROPERTIES']['CURATORS']['VALUE'];
                foreach ($curators as $curator) {
                    $arGroups2 = \CUser::GetUserGroup($curator);
                    if (in_array(SB_GROUP, $arGroups2)) {
                        array_push($brokerscur, $curator);
                    }
                }
            }
            $newstatus = '4';
            $usernotify = $brokerscur;
            $nottempl = 'sb_accept';
        } elseif ($status==6) {
            $superuserbroker = 0;
            $needacceptsupubroker = false;
            $insadjcurators = [];
            $participation = new CParticipation(new CLost($lostid));
            $partips = $participation->getParticipants();
            foreach ($partips as $partip) {
                $curators = $partip['PROPERTIES']['CURATORS']['VALUE'];
                foreach ($curators as $curator) {
                    $arGroups2 = \CUser::GetUserGroup($curator);
                    if (in_array(SB_SU_GROUP, $arGroups2)) {
                        $superuserbroker = $curator;
                        //break;
                    } elseif(in_array(AJ_GROUP, $arGroups2) || in_array(INS_GROUP, $arGroups2)) {
                        array_push($insadjcurators, $curator);
                    }
                }
            }
            if($superuserbroker) {
                $needacceptsupubroker = (new CUserAccess($superuserbroker))->hasAcceptanceForLost($lostid);
            }
            if($needacceptsupubroker) {
                $newstatus = '7';
                $usernotify = [$superuserbroker];
                $nottempl = 'sb_accept';
            } else {
                $newstatus = '10';
                $usernotify = $insadjcurators;
                if($orig) {
                    $nottempl = 'doc_read_orig';
                } else {
                    $nottempl = 'doc_read';
                }
            }
        } elseif ($status==9) {
            $arGroups = \CUser::GetUserGroup($user);
            if(in_array(SB_SU_GROUP, $arGroups)) {
                $newstatus = '10';
            }
            $insadjcurators = [];
            $participation = new CParticipation(new CLost($lostid));
            $partips = $participation->getParticipants();
            foreach ($partips as $partip) {
                $curators = $partip['PROPERTIES']['CURATORS']['VALUE'];
                foreach ($curators as $curator) {
                    $arGroups2 = \CUser::GetUserGroup($curator);
                    if (in_array(AJ_GROUP, $arGroups2) || in_array(INS_GROUP, $arGroups2)) {
                        array_push($insadjcurators, $curator);
                    }
                }
            }
            $usernotify = $insadjcurators;
            if($orig=='true') {
                $nottempl = 'doc_read_orig';
            } else {
                $nottempl = 'doc_read';
            }
        }

        if($usernotify && $nottempl) {
            CNotification::send($nottempl, $usernotify, 'nocomment', $lostid, $lostdocid);
            if($nottempl=='doc_read_orig') {
                $curclient = [];
                $participation = new CParticipation(new CLost($lostid));
                $partips = $participation->getParticipants();
                foreach ($partips as $partip) {
                    $curators = $partip['PROPERTIES']['CURATORS']['VALUE'];
                    foreach ($curators as $curator) {
                        $arGroups2 = \CUser::GetUserGroup($curator);
                        if (in_array(CL_GROUP, $arGroups2)) {
                            $curclient[] = $curator;
                        }
                    }
                }
                CNotification::send('doc_read_orig_cl', $curclient, 'nocomment', $lostid, $lostdocid);
            }

        }

        if($newstatus) {
            $PROP[27] = $newstatus;
            $PROP[61] = $dateupdate;
            LostDocuments::updateElement($lostdocid, [], $PROP);
            $objHistory = new HLBWrap('e_history_lost_document_status');
            $histdata = [
                'UF_CODE_ID' => $newstatus,
                'UF_DATE' => $dateupdate,
                'UF_LOST_ID' => $lostid,
                'UF_LOST_DOC_ID' => $lostdocid,
                'UF_USER_ID' => $user
            ];

            $id = $objHistory->add($histdata);

            if($newstatus=='10' && $orig=='false') {

                $this->updateLossStatus($lostid, $orig);
            }

            if(intval($id->getId())>0) {
                return "updated";
            } else {
                return "error";
            }
        } else {
            return "error";
        }
    }
    // workflow
    public function declineLostdocAction($lostid, $lostdocid, $status, $user, $comment)
    {
        $dateupdate = date("d.m.Y. H:i:s");
        $newstatus = '1';

        if($status==3) {
            $curclient = [];
            $participation = new CParticipation(new CLost($lostid));
            $partips = $participation->getParticipants();
            foreach ($partips as $partip) {
                $curators = $partip['PROPERTIES']['CURATORS']['VALUE'];
                foreach ($curators as $curator) {
                    $arGroups2 = \CUser::GetUserGroup($curator);
                    if (in_array(CL_GROUP, $arGroups2)) {
                        if(!in_array(CL_SU_GROUP, $arGroups2)) {
                            $curclient[] = $curator;
                        }
                    }
                }
            }
            $nottempl = 'suc_decline';
            $priornewstatus = 5;
        } else if($status==6 || $status==9) {
            $curclient = [];
            $participation = new CParticipation(new CLost($lostid));
            $partips = $participation->getParticipants();
            foreach ($partips as $partip) {
                $curators = $partip['PROPERTIES']['CURATORS']['VALUE'];
                foreach ($curators as $curator) {
                    $arGroups2 = \CUser::GetUserGroup($curator);
                    if (in_array(CL_GROUP, $arGroups2)) {
                        array_push($curclient, $curator);
                    }
                }
            }
            $nottempl = 'sb_decline';
            if($status==6) {
                $priornewstatus = 8;
            } else {
                $priornewstatus = 11;
            }
        }

        if($curclient) {
            $usernotify = $curclient;
        }

        if($usernotify && $nottempl) {
            CNotification::send($nottempl, $usernotify, $comment, $lostid, $lostdocid);
        }

        if($newstatus) {
            $PROP[27] = $newstatus;
            $PROP[61] = $dateupdate;
            LostDocuments::updateElement($lostdocid, [], $PROP);

            if($priornewstatus) {
                $objHistory = new HLBWrap('e_history_lost_document_status');
                $histdata = [
                    'UF_CODE_ID' => $priornewstatus,
                    'UF_DATE' => $dateupdate,
                    'UF_LOST_ID' => $lostid,
                    'UF_LOST_DOC_ID' => $lostdocid,
                    'UF_USER_ID' => $user,
                    'UF_COMMENT' => $comment
                ];

                $id = $objHistory->add($histdata);
            }

            $objHistory = new HLBWrap('e_history_lost_document_status');
            $histdata = [
                'UF_CODE_ID' => $newstatus,
                'UF_DATE' => $dateupdate,
                'UF_LOST_ID' => $lostid,
                'UF_LOST_DOC_ID' => $lostdocid,
                'UF_USER_ID' => $user,
                'UF_COMMENT' => ''
            ];

            $id = $objHistory->add($histdata);

            if(intval($id->getId())>0) {
                return "updated";
            } else {
                return "error";
            }
        } else {
            return "error";
        }
    }
    // workflow
    private function updateLossStatus($lostid, $orig) {

        if($orig=='false') {
            $termstatus = 10;
        } else {
            $termstatus = 13;
        }

        $needupdate = true;

        global $USER;

        $arRequests = LostDocuments::getElementsByConditions(['PROPERTY_LOST' => $lostid]);
        foreach ($arRequests as $reqitem) {
            if(intval($reqitem['PROPERTY_27']<$termstatus)) {
                $needupdate = false;
                break;
            }
        }

        if($needupdate) {
            $PROP[16] = 'yellow';
            Lost::updateElement($lostid, [], $PROP);
            $objHistory = new HLBWrap('e_history_lost_status');
            $histdata = [
                'UF_CODE_ID' => '2',
                'UF_DATE' => date("d.m.Y. H:i:s"),
                'UF_LOST_ID' => $lostid,
                'UF_USER_ID' => $USER->GetID()
            ];
            $objHistory->add($histdata);
        }
    }

    // workflow
    public function getOrigAction($lostid, $lostdocid, $status, $user, $origdate)
    {
        $dateupdate = date("d.m.Y. H:i:s");
        $newstatus = '14';
        if($newstatus) {
            $PROP[27] = $newstatus;
            $PROP[61] = $dateupdate;
            $PROP[69] = $origdate;
            LostDocuments::updateElement($lostdocid, [], $PROP);
            $objHistory = new HLBWrap('e_history_lost_document_status');
            $histdata = [
                'UF_CODE_ID' => $newstatus,
                'UF_DATE' => $dateupdate,
                'UF_LOST_ID' => $lostid,
                'UF_LOST_DOC_ID' => $lostdocid,
                'UF_USER_ID' => $user
            ];

            $id = $objHistory->add($histdata);

            $this->updateLossStatus($lostid, 'true');

            if(intval($id->getId())>0) {
                return "updated";
            } else {
                return "error";
            }
        } else {
            return "error";
        }
    }

    // workflow
    public function closeLossAction($formdata)
    {
        $result = 'error';

        $data = [];

        global $USER;

        $uid = $USER->GetID();

        if($formdata) {
            foreach ($formdata as $item) {
                if($item['name']=='decision') {
                    if($item['value']=='Y') {
                        $PROP[76] = 33;
                    } else {
                        $PROP[76] = 34;
                    }
                } else if($item['name']=='sum') {
                    $PROP[75] = $item['value'];
                } else if($item['name']=='lostid') {
                    $lostid = $item['value'];
                }
            }
            $data['PROPERTY_VALUES']['STATUS'] = '1';

            $PROP[16] = 'green';
            Lost::updateElement($lostid, [], $PROP);

            $objHistory = new HLBWrap('e_history_lost_status');
            $histdata = [
                        'UF_CODE_ID' => '3',
                        'UF_DATE' => date("d.m.Y. H:i:s"),
                        'UF_LOST_ID' => $lostid,
                        'UF_USER_ID' => $USER->GetID()
            ];
            $objHistory->add($histdata);

            $result = 'added';
        }

        return $result;
    }
}

