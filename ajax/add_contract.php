<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define("EXTRANET_NO_REDIRECT", true);
use Bitrix\Main\Context;

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
\Bitrix\Main\Diag\Debug::writeToFile("catch", "catch", "__miros.log");
\Bitrix\Main\Diag\Debug::writeToFile($_POST, "post", "__miros.log");
\Bitrix\Main\Diag\Debug::writeToFile($_FILES, "files", "__miros.log");

__CrmPropductRowListEndResponse(array('sucsess'=>'Y'));





