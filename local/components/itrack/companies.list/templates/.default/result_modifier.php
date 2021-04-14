<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

if(!empty($arResult['ITEMS'])) {
    foreach ($arResult['ITEMS'] as $key=>$arItem) {
        if(!empty($arItem['LOGO']) && intval($arItem['LOGO']) > 0 ) {
            $arResult['ITEMS'][$key]['LOGO_SRC'] = CFile::ResizeImageGet($arItem['LOGO'], ['width' => 80, 'height' => 80])['src'];
        }
    }
}
