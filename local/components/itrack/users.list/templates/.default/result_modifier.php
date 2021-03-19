<?php

foreach ($arResult['ITEMS'] as &$arItem) {
	$arImg = CFile::ResizeImageGet(
		$arItem['COMPANY_LOGO'],
		['width' => 80, 'height' => 80]
	);

	$arItem['COMPANY_LOGO'] = $arImg['src'];

	if ($arItem['LAST_LOGIN']) {
		$arItem['LAST_LOGIN'] = $arItem['LAST_LOGIN']->format("d.m.Y");
	}

	$arItem['URL_PROFILE'] = $arParams['FOLDER'] . str_replace('#USER_ID#', $arItem['ID'], $arParams['URL_TEMPLATES']['profile']);
	$arItem['URL_ACCESS'] = $arParams['FOLDER'] . str_replace('#USER_ID#', $arItem['ID'], $arParams['URL_TEMPLATES']['access']);
}
