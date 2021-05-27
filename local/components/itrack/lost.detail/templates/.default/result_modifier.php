<?

use Itrack\Custom\CUserRole;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
global $USER;
// выбор поля для отображения цвета статуса
if($arResult['CONTRACT']['PROPERTIES']['ORIGIN_REQUIRED']['VALUE']) {
    $arResult['COLOR_FIELD'] = 'UF_COLOR_ORIG';
} else {
    $arResult['COLOR_FIELD'] = 'UF_COLOR';
}

foreach ($arResult['CURATORS'] as $item) {
    $arResult['LOST']['PROPERTIES']['CURATORS'][$item['COMPANY_ID']][$item['ID']] = $item;
}

$isclient = (new CUserRole($USER->GetID()))->isClient();

$arResult['BAN_DOC'] = $isclient;