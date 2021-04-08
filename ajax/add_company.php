<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define("EXTRANET_NO_REDIRECT", true);
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Itrack\Custom\InfoBlocks\Company;

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

$arr_file=Array(
    "name" =>  $_FILES['logo']['name'],
    "size" => $_FILES['logo']['size'],
    "tmp_name" => $_FILES['logo']['tmp_name'],
    "type" => $_FILES['logo']['type'],
    "old_file" => "",
    "del" => "Y",
    "MODULE_ID" => "iblock");
$fid = CFile::SaveFile($arr_file, "companylogo");

//\Bitrix\Main\Diag\Debug::writeToFile($fid, "fid", "__miros.log");

$data = [
    //'IBLOCK_ID' => 1,
    'ACTIVE' => 'Y',
    'NAME' => $_POST['name'],
    'PROPERTY_VALUES' => [
        'TYPE'=> $_POST['type'],
        'LOGO'=> $fid,
        'FULL_NAME' => $_POST['full_name'],
        'LEGAL_ADDRESS' => $_POST['legal_adress'],
        'ACTUAL_ADDRESS' => $_POST['adress'],
        'INN' => $_POST['inn'],
        'KPP' => $_POST['kpp']
    ]
];

$ID = Company::createElement($data, []);

if(intval($ID) > 0) {
    __CrmPropductRowListEndResponse(array('sucsess' => 'Y'));
} else {
    __CrmPropductRowListEndResponse(array('error'=>strip_tags($add->LAST_ERROR)));
}