<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define("EXTRANET_NO_REDIRECT", true);
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Itrack\Custom\Participation\CParticipation;
use Itrack\Custom\Participation\CLost;

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

foreach ($_FILES as $file) {
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
}

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

$companies = array_merge($companies, $insarray);
$companies = array_merge($companies, $adjarray);

Loader::includeModule('iblock');
$add = new \CIBlockElement();

$data = [
    'IBLOCK_ID' => 3,
    'ACTIVE' => 'Y',
    'NAME' => 'Убыток №:'.$_POST['docnum'],
    'CODE' => $_POST['docnum'],
    'DATE_ACTIVE_FROM' => $_POST['docdate'],
    'PROPERTY_VALUES' => [
        'DESCRIPTION'=> $_POST['description'],
        'STATUS' => $_POST['status'],
        'CONTRACT' => $_POST['contract'],
        'LOSS_NUMBER' => $_POST['docnum'],
        'REQUEST_DOCS' => $_POST['reqdoc'],
        'REQUEST_DATE' => $_POST['reqdate'],
        'REQUEST_USER' => $_POST['user'],
        'REQUEST_TERM' => $_POST['req_term'],
        'VALUABLE_DOCS' => $fidids,
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
$id = $add->Add($data);

if(intval($id) > 0) {
    /*foreach($kurators as $kurator) {
        $rsUser = \CUser::GetByID($kurator);
        $arUser = $rsUser->Fetch();
        $companyid = $arUser['UF_COMPANY'];
        if(in_array($kurator,$kurleaders )) {
            $leader = 7;
        } else {
            $leader = 0;
        }
        $data = [
            'IBLOCK_ID' => 4,
            'ACTIVE' => 'Y',
            'NAME' => $_POST['docnum'],
            'PROPERTY_VALUES' => [
                'LOST'=> $id,
                'COMPANY' => $companyid,
                'CURATOR' => $kurator,
                'LEADER' => $leader
            ]
        ];
        $id2 = $add->Add($data);
    }*/
    $participation = new CParticipation(new CLost($id));
    $participation->createFromArrays(
        $companies,			// Компании
        $companiesleaders, 				// Компании-лидеры
        $kurators, 	// Кураторы
        $kurleaders	// Кураторы-лидеры
    );
    __CrmPropductRowListEndResponse(array('sucsess'=>'Y'));
} else {
    __CrmPropductRowListEndResponse(array('error'=>strip_tags($add->LAST_ERROR)));
}