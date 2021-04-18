<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define("EXTRANET_NO_REDIRECT", true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

global $USER, $APPLICATION;

$APPLICATION->IncludeComponent("itrack:popup.companies.curators.list", "", []);
