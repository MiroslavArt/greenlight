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

$curbroker = [];
$curins = [];
$curadj = [];
$curclint = [];
$curallorder = [];

foreach ($arResult['CURATORS'] as $item) {
    $arResult['LOST']['PROPERTIES']['CURATORS'][$item['COMPANY_ID']][$item['ID']] = $item;
    if($item['COMPANY_TYPE']=='Страховая Компания') {
        $curins[] = $item;
    } elseif($item['COMPANY_TYPE']=='Аджастер') {
        $curadj[] = $item;
    } elseif($item['COMPANY_TYPE']=='Клиент') {
        $curclint[] = $item;
    } elseif($item['COMPANY_TYPE']=='Страховой Брокер') {
        $curbroker[] = $item;
    }
}

foreach($curclint as $item) {
    $curallorder[] = $item;
}

foreach($curbroker as $item) {
    $curallorder[] = $item;
}

foreach($curins as $item) {
    $curallorder[] = $item;
}

foreach($curadj as $item) {
    $curallorder[] = $item;
}

$arResult['CURATORS'] = $curallorder;

$isclient = (new CUserRole($USER->GetID()))->isClient();

$arResult['BAN_DOC'] = $isclient;

$isinsuer = (new CUserRole($USER->GetID()))->isInsurer();
$isadjuster = (new CUserRole($USER->GetID()))->isAdjuster();

if($isinsuer || $isadjuster) {
    $arResult['BAN_LOSS_EDIT'] = true;
}