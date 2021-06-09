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

function reArrayFiles(&$file_post)
{
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);
    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }
    return $file_ary;
}

// в начале проверяем номер на дубликат
$arFilter['NAME'] = $_POST['docnum'];
$arFilter["PROPERTY_CLIENT_LEADER.ID"] = $_POST['clientid'];
$contracts = Contract::getElementsByConditions($arFilter, [], []);
if($contracts) {
    $contract = current($contracts);
    $numerror = false;
    if($_POST['contractnum']) {
        if($_POST['contractnum']!=$contract['ID']) {
            $numerror = true;
        }
    } else {
        $numerror = true;
    }

    if($numerror) {
        __CrmPropductRowListEndResponse(array('error'=>"Договор по данному клиенту с таким номером уже существует"));
    }
}
// потом все остальное
$fidids = [
    'contracts' => [],
    'pamyatka' => [],
    'other' => []
];

$companies = [$_POST['clientid'], $_POST['brokerid']];
$companiesleaders = [$_POST['clientid'], $_POST['brokerid'], $_POST['insleader']];

foreach ($_FILES as $key => $filearr) {
    //$fidids[$key] = [];
    $arDocuments = reArrayFiles($filearr);
    foreach ($arDocuments as $file) {
        if (!empty($file['tmp_name'])) {
            $arr_file=Array(
                "name" =>  $file['name'],
                "size" => $file['size'],
                "tmp_name" => $file['tmp_name'],
                "type" => $file['type'],
                "old_file" => "",
                "del" => "Y",
                "MODULE_ID" => "iblock");
            $fid = CFile::SaveFile($arr_file, "contractdocs");
            $fiddoc = \CFile::MakeFileArray($fid);
            array_push($fidids[$key], $fiddoc);
        }
    }
}

if($_POST['curdocids']) {
    $curdocsarray = explode(",", $_POST['curdocids']);
    foreach ($curdocsarray as $curdoc) {
        $curdocfile = \CFile::MakeFileArray($curdoc);
        array_push($fidids['contracts'], $curdocfile);
    }
}

if($_POST['curdocids_pamyatka']) {
    $curdocsarray = explode(",", $_POST['curdocids_pamyatka']);
    foreach ($curdocsarray as $curdoc) {
        $curdocfile = \CFile::MakeFileArray($curdoc);
        array_push($fidids['pamyatka'], $curdocfile);
    }
}

if($_POST['curdocids_other']) {
    $curdocsarray = explode(",", $_POST['curdocids_other']);
    foreach ($curdocsarray as $curdoc) {
        $curdocfile = \CFile::MakeFileArray($curdoc);
        array_push($fidids['other'], $curdocfile);
    }
}

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
        'DOCS' => $fidids['contracts'],
        'DOCS_PAMYATKA' => $fidids['pamyatka'],
        'DOCS_OTHERS' => $fidids['other']
    ]
];

if($_POST['contractnum']) {
    $ID = $_POST['contractnum'];
    $updatedataprop = $data['PROPERTY_VALUES'];
    unset($data['PROPERTY_VALUES']);
    Contract::updateElement($ID, $data, $updatedataprop);
} else {
    $ID = Contract::createElement($data, []);
}

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

    if($_POST['contractnum']) {
        foreach ($kurators as $kurator) {
            (new CUserAccess($kurator))->dropAcceptanceForContract($id);
            (new CUserAccess($kurator))->dropNotificationForContract($id);
        }
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







