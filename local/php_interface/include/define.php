<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
define('DEFAULT_TEMPLATE_PATH', '/local/templates/.default');
$vuejsDebug = (bool)preg_match('/softmonster.ru/', $_SERVER['SERVER_NAME']);
define('PATH_TO_404', '/404.php');
define('VUEJS_DEBUG', $vuejsDebug);
define('MODULE_ID_ITRACK', 'itrack.custom');
define('CL_GROUP', '9');
define('CL_SU_GROUP', '10');