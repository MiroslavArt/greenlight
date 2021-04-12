<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$searchQuery = $request->get("search");
$isAjax = $request->isAjaxRequest() && isset($searchQuery);

if ($isAjax) {
	$buffer = ob_get_clean();
	$GLOBALS['APPLICATION']->RestartBuffer();

	list(, $html) = explode('<!--items-->', $buffer);

	// Без этих дивов не работает вставка контента на страницу
	// $(json.htmlContainer).find(json.html)
	// .find() не находит контейнер, если этот контейнер корневой
	$arResult['html'] = "<div>" . $html . "</div>";
	$arResult['htmlContainer'] = '#lost-list';
	$arResult['success'] = true;
	if (!empty($arResult['ERROR'])) {
		unset($arResult['success']);
		$arResult['errorMessage'] = true;
	}

	echo json_encode($arResult);

	die();
}


