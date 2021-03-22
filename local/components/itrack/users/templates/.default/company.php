<?php
global $USER;
$userId = $arResult["VARIABLES"]["USER_ID"];

$listUrl = $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["list"];

$profileUrl = $arResult["FOLDER"] . str_replace(
		"#USER_ID#",
		$arResult["VARIABLES"]["USER_ID"],
		$arResult["URL_TEMPLATES"]["profile"]
	);
?>

<?$APPLICATION->IncludeComponent(
	"itrack:company.profile",
	"",
	[
		"USER_ID" => $userId == "my" ? $USER->GetID() : $userId,
		"LIST_URL" => $listUrl,
		"PROFILE_URL" => $profileUrl
	]
);?>

