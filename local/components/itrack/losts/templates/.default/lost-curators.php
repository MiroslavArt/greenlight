<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);

$APPLICATION->IncludeComponent(
    'itrack:lost.detail',
    '',
    array(
        "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
        "LIST_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["list"],
        "LOST_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["lost"],
        "LOST_ID" => $arResult['VARIABLES']["ELEMENT_ID"],
        "PATH_TO" => $arResult['PATH_TO'],
        'CURATORS_MODE' => 'Y'
    ),
    null
);
?>