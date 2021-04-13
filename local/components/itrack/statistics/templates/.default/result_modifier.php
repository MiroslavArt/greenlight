<?php

foreach ($arResult['ITEMS'] as &$arItem) {
	if (empty($arItem["LOGO"])) {
		continue;
	}

	$arImg = CFile::ResizeImageGet(
		$arItem["LOGO"],
		['width' => 80, 'height' => 80],
		BX_RESIZE_IMAGE_EXACT
	);

	$arItem["LOGO"] =  $arImg['src'];
}


