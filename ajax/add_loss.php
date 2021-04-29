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

// в начале проверяем номер на дубликат
$arFilter['CODE'] = $_POST['docnum'];
$arFilter["PROPERTY_CLIENT_LEADER.ID"] = $_POST['clientid'];
$losses = Lost::getElementsByConditions($arFilter, [], []);
if($losses) {
    $loss= current($losses);
    $numerror = false;
    if($_POST['lostid']) {
        if($_POST['lostid']!=$loss['ID']) {
            $numerror = true;
        }
    } else {
        $numerror = true;
    }
    if($numerror) {
        __CrmPropductRowListEndResponse(array('error'=>"Убыток по данному клиенту с таким номером уже существует"));
    }
}

// потом все остальное
$companies = [$_POST['clientid'], $_POST['brokerid']];
$companiesleaders = [$_POST['clientid'], $_POST['brokerid'], $_POST['insleader'], $_POST['adjleader']];

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

$companies = array_merge($companies, $insarray);
$companies = array_merge($companies, $adjarray);

if($_POST['lostid']) {
    $ID = $_POST['lostid'];
    $data = [
        'NAME' => $_POST['docnum'],
        'CODE' => $_POST['docnum'],
        'DATE_ACTIVE_FROM' => $_POST['docdate']
    ];

    $PROP = [
        'NEED_ACCEPT' => $needaccept,
        'NEED_NOTIFY' => $neednotify,
        'INSURANCE_COMPANY' => $insarray,
        'INSURANCE_COMPANY_LEADER' => $_POST['insleader'],
        'ADJUSTER' => $adjarray,
        'ADJUSTER_LEADER' => $_POST['adjleader']
    ];

    $res = Lost::updateElement($ID, $data, $PROP);
    if($res != '1') {
        $ID = 0;
        $res = preg_replace('/символьным кодом/', 'номером', $res);
        __CrmPropductRowListEndResponse(array('error'=>strip_tags($res)));
    }
} else {
    $data = [
        'ACTIVE' => 'Y',
        'NAME' => $_POST['docnum'],
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
}

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

    if($_POST['lostid']) {
        foreach ($kurators as $kurator) {
            (new CUserAccess($kurator))->dropAcceptanceForLost($id);
            (new CUserAccess($kurator))->dropNotificationForLost($id);
        }
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
    if($kurnotify && !$_POST['lostid']) {
        CNotification::send( 'new_loss', $kurnotify, 'nocomment', $ID);
    }

    __CrmPropductRowListEndResponse(array('sucsess'=>'Y'));
} else {
    __CrmPropductRowListEndResponse(array('error'=>strip_tags($ID)));
}