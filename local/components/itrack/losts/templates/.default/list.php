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
    'itrack:lost.list',
    '',
    array(
        "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
        "LIST_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["list"],
        "PATH_TO" => $arResult['PATH_TO']
    ),
    null
);
?>