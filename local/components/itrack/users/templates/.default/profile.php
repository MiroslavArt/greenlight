<?php
global $USER;
$userId = $arResult["VARIABLES"]["USER_ID"];

$listUrl = $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["list"];

$companyUrl = $arResult["FOLDER"] . str_replace(
		"#USER_ID#",
		$arResult["VARIABLES"]["USER_ID"],
		$arResult["URL_TEMPLATES"]["company"]
	);

$accessUrl = $arResult["FOLDER"] . str_replace(
		"#USER_ID#",
		$arResult["VARIABLES"]["USER_ID"],
		$arResult["URL_TEMPLATES"]["access"]
	);
?>


<?$APPLICATION->IncludeComponent(
	"itrack:user.profile",
	"",
	[
		"USER_ID" => $userId == "my" ? $USER->GetID() : $userId,
		"LIST_URL" => $listUrl,
		"COMPANY_URL" => $companyUrl,
		"ACCESS_URL" => $accessUrl,
	]
);?>

