<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arDefaultUrlTemplates404 = [
	'list'    => 'index.php',
	'add'    => 'add/',
	'profile' => '#USER_ID#/profile/',
	'company' => '#USER_ID#/company/',
	'access' => '#USER_ID#/access/',
];


$arComponentVariables = ['USER_ID'];
$sefFolder = $arParams['SEF_FOLDER'];
$arVariables = [];


$componentPage = CComponentEngine::ParseComponentPath(
	$sefFolder,
	$arDefaultUrlTemplates404,
	$arVariables
);


if (strlen($componentPage) <= 0) {
	$componentPage = 'list';
}

$arResult = [
	'FOLDER'        => $sefFolder,
	'VARIABLES'     => $arVariables,
	'URL_TEMPLATES' => $arDefaultUrlTemplates404,
];

$this->IncludeComponentTemplate($componentPage);

?>
