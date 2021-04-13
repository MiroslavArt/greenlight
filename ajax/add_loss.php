<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define("EXTRANET_NO_REDIRECT", true);
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Itrack\Custom\CNotification;
use Itrack\Custom\Participation\CParticipation;
use Itrack\Custom\InfoBlocks\Lost;
use Itrack\Custom\Participation\CLost;
use Itrack\Custom\UserAccess\CUserAccess;
use Itrack\Custom\Highloadblock\HLBWrap;

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');


global $USER, $APPLICATION;

if(!function_exists('__CrmPropductRowListEndResponse'))
{
    function __CrmPropductRowListEndResponse($result)
    {
        $GLOBALS['APPLICATION']->RestartBuffer();
        header('Content-Type: application/json; charset='.LANG_CHARSET);


        if(!empty($result))
        {
            echo json_encode($result);
        }
        require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
        die();
    }
}

$fidids = [];
$companies = [$_POST['clientid'], $_POST['brokerid']];
$companiesleaders = [$_POST['clientid'], $_POST['brokerid'], $_POST['insleader'], $_POST['adjleader']];

/*foreach ($_FILES as $file) {
    $arr_file=Array(
        "name" =>  $file['name'],
        "size" => $file['size'],
        "tmp_name" => $file['tmp_name'],
        "type" => $file['type'],
        "old_file" => "",
        "del" => "Y",
        "MODULE_ID" => "iblock");
    $fid = CFile::SaveFile($arr_file, "lossdocs");
    array_push($fidids, $fid);
}*/

if($_POST['inscompanies']) {
    $insarray = explode(",", $_POST['inscompanies']);
}

if($_POST['adjusters']) {
    $adjarray = explode(",", $_POST['adjusters']);
}

if($_POST['kurators']) {
    $kurators = explode(",", $_POST['kurators']);
}

if($_POST['kurleaders']) {
    $kurleaders = explode(",", $_POST['kurleaders']);
}

if($_POST['kuratorscl']) {
    $kuratorscl = explode(",", $_POST['kuratorscl']);
}
if($_POST['kuratorsins']) {
    $kuratorsins = explode(",", $_POST['kuratorsins']);
}
if($_POST['kuratorsbr']) {
    $kuratorsbr = explode(",", $_POST['kuratorsbr']);
}
if($_POST['kuratorsadj']) {
    $kuratorsadj = explode(",", $_POST['kuratorsadj']);
}
if($_POST['needaccept']) {
    $needaccept = explode(",", $_POST['needaccept']);
}
if($_POST['neednotify']) {
    $neednotify = explode(",", $_POST['neednotify']);
}

$companies = array_merge($companies, $adjarray);

$data = [
    'ACTIVE' => 'Y',
    'NAME' => 'Убыток №:'.$_POST['docnum'],
    'CODE' => $_POST['docnum'],
    'DATE_ACTIVE_FROM' => $_POST['docdate'],
    'PROPERTY_VALUES' => [
        'DESCRIPTION'=> $_POST['description'],
        'STATUS' => $_POST['status'],
        'CONTRACT' => $_POST['contract'],
        'NEED_ACCEPT' => $needaccept,
        'NEED_NOTIFY' => $neednotify,
        'CLIENT'=> array($_POST['clientid']),
        'CLIENT_LEADER'=> $_POST['clientid'],
        'INSURANCE_BROKER'=> array($_POST['brokerid']),
        'INSURANCE_BROKER_LEADER'=> $_POST['brokerid'],
        'INSURANCE_COMPANY' => $insarray,
        'INSURANCE_COMPANY_LEADER' => $_POST['insleader'],
        'ADJUSTER' => $adjarray,
        'ADJUSTER_LEADER' => $_POST['adjleader']
    ]
];

$ID = Lost::createElement($data, []);

if(intval($ID) > 0) {
    $id = intval($ID);

    $objHistory = new HLBWrap('e_history_lost_status');
    $histdata = [
        'UF_CODE_ID' => '1',
        'UF_DATE' => date("d.m.Y. H:i:s"),
        'UF_LOST_ID' => $id,
        'UF_USER_ID' => $USER->GetID()
    ];
    $objHistory->add($histdata);

    /*foreach ($fidids as $fid) {
        $file = \CFile::MakeFileArray($fid);
        $data = array(
            "UF_MAINLOST_ID" => $id,
            "UF_NAME"=>$_POST['reqdoc'],
            "UF_FILE"=> $file,
            "UF_COMMENT"=>$_POST['reqdoc'],
            "UF_DATE_CREATED" => ConvertDateTime($_POST['reqdate'], "DD.MM.YYYY")." 23:59:59",
            "UF_DATE_TERM" => ConvertDateTime($_POST['req_term'], "DD.MM.YYYY")." 23:59:59",
            "UF_USER_ID" => $_POST['user'],
            "UF_DOC_TYPE"=> '1'
        );
        $objDocument = new HLBWrap('uploaded_docs');
        $objDocument->add($data);
    }*/

    try {
        $participation = new CParticipation(new CLost($id));
        $participation->createFromArrays(
            $companies,			// Компании
            $companiesleaders, 				// Компании-лидеры
            $kurators, 	// Кураторы
            $kurleaders	// Кураторы-лидеры
        );
    } catch (Exception $e) {
        __CrmPropductRowListEndResponse(array('error'=>$e->getMessage()));
    }
    $kuracceptance = [];
    $kurnotify = [];

    if(in_array(25, $needaccept)) {
        foreach ($kuratorscl as $kurator) {
            $kuracceptance[] = $kurator;
        }
    }
    if(in_array(26, $needaccept)) {
        foreach ($kuratorsbr as $kurator) {
            $kuracceptance[] = $kurator;
        }
    }
    if(in_array(27, $needaccept)) {
        foreach ($kuratorsins as $kurator) {
            $kuracceptance[] = $kurator;
        }
    }
    if(in_array(28, $needaccept)) {
        foreach ($kuratorsadj as $kurator) {
            $kuracceptance[] = $kurator;
        }
    }
    if(in_array(29, $neednotify)) {
        foreach ($kuratorscl as $kurator) {
            $kurnotify[] = $kurator;
        }
    }
    if(in_array(30, $neednotify)) {
        foreach ($kuratorsbr  as $kurator) {
            $kurnotify[] = $kurator;
        }
    }
    if(in_array(31, $neednotify)) {
        foreach ($kuratorsins as $kurator) {
            $kurnotify[] = $kurator;
        }
    }
    if(in_array(32, $neednotify)) {
        foreach ($kuratorsadj as $kurator) {
            $kurnotify[] = $kurator;
        }
    }

    foreach ($kuracceptance as $kurator) {
        (new CUserAccess($kurator))->setAcceptanceForLost($id);
    }
    foreach ($kurnotify as $kurator) {
        (new CUserAccess($kurator))->setNotificationForLost($id);
    }
    if($kurnotify) {
        CNotification::send( 'new_loss', $kurnotify, 'nocomment', $ID);
    }

    __CrmPropductRowListEndResponse(array('sucsess'=>'Y'));
} else {
    __CrmPropductRowListEndResponse(array('error'=>strip_tags($ID)));
}