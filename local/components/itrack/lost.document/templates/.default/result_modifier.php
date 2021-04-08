<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

if($arResult['SHOWORIGINAL']) {
    $arResult['COLOR_FIELD'] = 'UF_COLOR_ORIG';
} else {
    $arResult['COLOR_FIELD'] = 'UF_COLOR';
}