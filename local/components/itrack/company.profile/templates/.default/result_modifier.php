<?php

$arLogo = &$arResult["PROPERTIES"]["LOGO"];

$arImg = CFile::ResizeImageGet(
	$arLogo["VALUE"],
	['width' => 80, 'height' => 80],
	BX_RESIZE_IMAGE_EXACT
);

$arLogo["SRC"] = $arImg['src'];
