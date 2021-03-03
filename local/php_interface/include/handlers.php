<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();


//Событие для обработки статуса 404
//$eventManager->addEventHandler("main", "OnEpilog", "Redirect404");
function Redirect404()
{
    if (
        !defined('ADMIN_SECTION') &&
        defined("ERROR_404") &&
        defined("PATH_TO_404") &&
        file_exists($_SERVER["DOCUMENT_ROOT"] . PATH_TO_404)
    ) {

        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        CHTTP::SetStatus("404 Not Found");

        include($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/header.php");
        include($_SERVER["DOCUMENT_ROOT"] . PATH_TO_404);
        include($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/footer.php");
    }
}


if (Loader::includeModule('itrack.custom')) {


}

