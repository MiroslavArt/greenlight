<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define("EXTRANET_NO_REDIRECT", true);
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use \Itrack\Custom\Highloadblock\HLBWrap;

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
    "name" =>  $_FILES['loss_file']['name'],
    "size" => $_FILES['loss_file']['size'],
    "tmp_name" => $_FILES['loss_file']['tmp_name'],
    "type" => $_FILES['loss_file']['type'],
    "old_file" => "",
    "del" => "Y",
    "MODULE_ID" => '');
$fid = CFile::SaveFile($arr_file, "lossdocs");

$data = array(
    "UF_LOST_ID" => $_POST['lost_id'],
    "UF_NAME"=>$_POST['doc_name'],
    "UF_FILE_INT"=> $fid,
    "UF_COMMENT"=>$_POST['comment'],
    "UF_DATE_CREATED" => ConvertDateTime($_POST['doc_date'], "DD.MM.YYYY")." 23:59:59",
    "UF_USER_ID" => $USER->GetID(),
    "UF_DOC_TYPE"=> 2
);

$objDocument = new HLBWrap('uploaded_docs');

$id = $objDocument->add($data);

if(intval($id->getId())>0) {
    __CrmPropductRowListEndResponse(array('sucsess' => 'Y'));
} else {
    __CrmPropductRowListEndResponse(array('error'=>strip_tags($id)));
}


