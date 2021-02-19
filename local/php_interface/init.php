<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Context;

$docRoot = Context::getCurrent()->getServer()->getDocumentRoot();

//Подключаем глобальные константы проекта
if (file_exists($docRoot . '/local/php_interface/include/define.php')) {
    require_once $docRoot . '/local/php_interface/include/define.php';
}

//Подключаем функции доступные глобально
if (file_exists($docRoot . '/local/php_interface/include/functions.php')) {
    require_once $docRoot . '/local/php_interface/include/functions.php';
}

//Подключаем обработчик событий
if (file_exists($docRoot . '/local/php_interface/include/handlers.php')) {
    require_once $docRoot . '/local/php_interface/include/handlers.php';
}

//Подключаем агенты
if (file_exists($docRoot . '/local/php_interface/include/agents.php')) {
    require_once $docRoot . '/local/php_interface/include/agents.php';
}