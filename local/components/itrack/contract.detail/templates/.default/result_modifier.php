<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
use Itrack\Custom\CUserRole;


if(!empty($arResult['INSURANCE_COMPANY']['PROPERTIES']['LOGO']['VALUE']) && intval($arResult['INSURANCE_COMPANY']['PROPERTIES']['LOGO']['VALUE']) > 0 ) {
    $arResult['INSURANCE_COMPANY']['LOGO_SRC'] = CFile::ResizeImageGet($arResult['INSURANCE_COMPANY']['PROPERTIES']['LOGO']['VALUE'], ['width' => 80, 'height' => 80])['src'];
}

if(!empty($arResult['COMPANY']['PROPERTIES']['LOGO']['VALUE']) && intval($arResult['COMPANY']['PROPERTIES']['LOGO']['VALUE']) > 0 ) {
    $arResult['COMPANY']['LOGO_SRC'] = CFile::ResizeImageGet($arResult['COMPANY']['PROPERTIES']['LOGO']['VALUE'], ['width' => 80, 'height' => 80])['src'];
}

$users = [];

$filter = Array
(
    "ACTIVE" => "Y"
);

$rsUser = \CUser::GetList(($by="ID"), ($order="desc"), $filter);
// заносим прочие показатели
while ($arResUser = $rsUser->Fetch()) {
    if($arResUser['NAME'] || $arResUser['LAST_NAME'])
        array_push($users, $arResUser);
}

$arResult['USERS'] = $users;

$isinsuer = (new CUserRole($USER->GetID()))->isInsurer();
$isadjuster = (new CUserRole($USER->GetID()))->isAdjuster();

if($isinsuer || $isadjuster) {
    $arResult['BAN_CNT_EDIT'] = true;
}