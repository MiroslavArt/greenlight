<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

foreach ($arResult['ITEMS'] as $index => $arItem) {
	foreach ($arItem["LEADERS"] as $party => $arLeader) {
		if (empty($arLeader["LOGO"])) {
			continue;
		}

		$arImg = CFile::ResizeImageGet(
			$arLeader["LOGO"],
			['width' => 80, 'height' => 80],
			BX_RESIZE_PROPORTIONAL_ALT
		);

		$arResult['ITEMS'][$index]['LEADERS'][$party]['LOGO'] =  $arImg['src'];
	}
}
