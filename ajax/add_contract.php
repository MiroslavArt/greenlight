<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define("EXTRANET_NO_REDIRECT", true);
use Bitrix\Main\Context;
use Bitrix\Main\Loader;

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

$request = Context::getCurrent()->getRequest();
\Bitrix\Main\Diag\Debug::writeToFile($_POST, "post", "__miros.log");
\Bitrix\Main\Diag\Debug::writeToFile($_FILES, "files", "__miros.log");

$fidids = [];

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
if($_POST['inscompanies']) {
    $insarray = explode(",", $_POST['inscompanies']);
}

if($_POST['kurators']) {
    $kurators = explode(",", $_POST['kurators']);
}

if($_POST['kurleaders']) {
    $kurleaders = explode(",", $_POST['kurleaders']);
}

Loader::includeModule('iblock');
$add = new \CIBlockElement();

$data = [
    'IBLOCK_ID' => 2,
    'ACTIVE' => 'Y',
    'NAME' => 'Договор №'.$_POST['docnum'],
    'PROPERTY_VALUES' => [
        'DATE'=> $_POST['docdate'],
        'TYPE'=> $_POST['instype'],
        'CLIENT'=> array($_POST['clientid']),
        'CLIENT_LEADER'=> $_POST['clientid'],
        'INSURANCE_BROKER'=> array($_POST['brokerid']),
        'INSURANCE_BROKER_LEADER'=> $_POST['brokerid'],
        'INSURANCE_COMPANY' => $insarray,
        'INSURANCE_COMPANY_LEADER' => $_POST['insleader'],
        'KURATORS' => $kurators,
        'KURATORS_LEADERS' => $kurleaders,
        'ORIGIN_REQUIRED' => $_POST['original'],
        'DOCS' => $fidids
    ]
];
$id = $add->Add($data);

if($id) {
    __CrmPropductRowListEndResponse(array('sucsess'=>'Y'));
} else {
    __CrmPropductRowListEndResponse(array('error'=>'Y'));
}







