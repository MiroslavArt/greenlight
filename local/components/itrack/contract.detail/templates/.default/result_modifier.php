<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var CBitrixComponentTemplate $this */


if(!empty($arResult['INSURANCE_COMPANY']['PROPERTIES']['LOGO']['VALUE']) && intval($arResult['INSURANCE_COMPANY']['PROPERTIES']['LOGO']['VALUE']) > 0 ) {
    $arResult['INSURANCE_COMPANY']['LOGO_SRC'] = CFile::ResizeImageGet($arResult['INSURANCE_COMPANY']['PROPERTIES']['LOGO']['VALUE'], ['width' => 80, 'height' => 80])['src'];
}
