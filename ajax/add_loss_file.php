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

if($_POST['type']) {
    $type = $_POST['type'];
} else {
    $type = '2';
}

$curuser = $USER->GetID();
$rsUser = \CUser::GetByID($curuser);
$arUser = $rsUser->Fetch();
$curusername = $arUser['NAME'].' '.$arUser['LAST_NAME'];


$arr_file=Array(
    "name" =>  $_FILES['loss_file']['name'],
    "size" => $_FILES['loss_file']['size'],
    "tmp_name" => $_FILES['loss_file']['tmp_name'],
    "type" => $_FILES['loss_file']['type'],
    "old_file" => "",
    "del" => "Y",
    "MODULE_ID" => '');
$fid = CFile::SaveFile($arr_file, "lossdocs");

$file = \CFile::MakeFileArray($fid);

if($type=='1' || $type=='2') {
    $data = array(
        "UF_LOST_ID" => $_POST['lost_id'],
        "UF_NAME"=>$_POST['doc_name'],
        "UF_FILE"=> $file,
        "UF_COMMENT"=>$_POST['comment'],
        "UF_DATE_CREATED" => ConvertDateTime($_POST['doc_date'], "DD.MM.YYYY")." 23:59:59",
        "UF_USER_ID" => $curuser,
        "UF_DOC_TYPE"=> $type
    );
} else {
    $data = array(
        "UF_MAINLOST_ID" => $_POST['lost_id'],
        "UF_NAME"=>$_POST['doc_name'],
        "UF_FILE"=> $file,
        "UF_COMMENT"=>$_POST['comment'],
        "UF_DATE_CREATED" => ConvertDateTime($_POST['doc_date'], "DD.MM.YYYY")." 23:59:59",
        "UF_USER_ID" => $curuser,
        "UF_DOC_TYPE"=> $type
    );
}

$objDocument = new HLBWrap('uploaded_docs');

$id = $objDocument->add($data);

if(intval($id->getId())>0) {
    $file['docid'] = intval($id->getId());
    $file['uname'] = $curusername;
    __CrmPropductRowListEndResponse(array('success' => $file));
} else {
    __CrmPropductRowListEndResponse(array('error'=>strip_tags($id)));
}


