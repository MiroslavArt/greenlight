<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define("EXTRANET_NO_REDIRECT", true);
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Itrack\Custom\Participation\CParticipation;
use Itrack\Custom\Participation\CContract;
use Itrack\Custom\InfoBlocks\Contract;
use Itrack\Custom\UserAccess\CUserAccess;

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
$companiesleaders = [$_POST['clientid'], $_POST['brokerid'], $_POST['insleader']];

foreach ($_FILES as $file) {
    $arr_file=Array(
        "name" =>  $file['name'],
        "size" => $file['size'],
        "tmp_name" => $file['tmp_name'],
        "type" => $file['type'],
        "old_file" => "",
        "del" => "Y",
        "MODULE_ID" => "iblock");
    $fid = CFile::SaveFile($arr_file, "contractdocs");
    array_push($fidids, $fid);
}

//\Bitrix\Main\Diag\Debug::writeToFile($_POST, "post", "__miros.log");
if($_POST['inscompanies']) {
    $insarray = explode(",", $_POST['inscompanies']);
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
if($_POST['needaccept']) {
    $needaccept = explode(",", $_POST['needaccept']);
}
if($_POST['neednotify']) {
    $neednotify = explode(",", $_POST['neednotify']);
}
$companies = array_merge($companies, $insarray);

$data = [
    'NAME' => $_POST['docnum'],
    'PROPERTY_VALUES' => [
        'DATE'=> $_POST['docdate'],
        'TYPE'=> $_POST['instype'],
        'CLIENT'=> array($_POST['clientid']),
        'CLIENT_LEADER'=> $_POST['clientid'],
        'INSURANCE_BROKER'=> array($_POST['brokerid']),
        'INSURANCE_BROKER_LEADER'=> $_POST['brokerid'],
        'INSURANCE_COMPANY' => $insarray,
        'INSURANCE_COMPANY_LEADER' => $_POST['insleader'],
        'NEED_ACCEPT' => $needaccept,
        'NEED_NOTIFY' => $neednotify,
        'ORIGIN_REQUIRED' => $_POST['original'],
        'DOCS' => $fidids
    ]
];

$ID = Contract::createElement($data, []);

if(intval($ID) > 0) {
    $id = intval($ID);
    try {
        $participation = new CParticipation(new CContract($id));
        $participation->createFromArrays(
            $companies,			// Компании
            $companiesleaders, 				// Компании-лидеры
            $kurators, 	// Кураторы
            $kurleaders			// Кураторы-лидеры
        );
    } catch (Exception $e) {
        __CrmPropductRowListEndResponse(array('error'=>$e->getMessage()));
    }
    $kuracceptance = [];
    $kurnotify = [];

    if(in_array(17, $needaccept)) {
        foreach ($kuratorscl as $kurator) {
            $kuracceptance[] = $kurator;
        }
    }
    if(in_array(18, $needaccept)) {
        foreach ($kuratorsbr as $kurator) {
            $kuracceptance[] = $kurator;
        }
    }
    if(in_array(19, $needaccept)) {
        foreach ($kuratorsins as $kurator) {
            $kuracceptance[] = $kurator;
        }
    }
    if(in_array(21, $neednotify)) {
        foreach ($kuratorscl as $kurator) {
            $kurnotify[] = $kurator;
        }
    }
    if(in_array(22, $neednotify)) {
        foreach ($kuratorsbr  as $kurator) {
            $kurnotify[] = $kurator;
        }
    }
    if(in_array(23, $neednotify)) {
        foreach ($kuratorsins as $kurator) {
            $kurnotify[] = $kurator;
        }
    }

    foreach ($kuracceptance as $kurator) {
        (new CUserAccess($kurator))->setAcceptanceForContract($id);
    }
    foreach ($kurnotify as $kurator) {
        (new CUserAccess($kurator))->setNotificationForContract($id);
    }
    __CrmPropductRowListEndResponse(array('sucsess'=>'Y'));
} else {
    __CrmPropductRowListEndResponse(array('error'=>strip_tags($ID)));
}







