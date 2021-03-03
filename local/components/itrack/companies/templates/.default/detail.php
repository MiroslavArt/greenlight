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
    'itrack:company.detail',
    '',
    array(
        "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
        "LIST_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["list"],
        "CLIENT_ID" => $arResult['VARIABLES']["ELEMENT_ID"]
    ),
    null
);
?>