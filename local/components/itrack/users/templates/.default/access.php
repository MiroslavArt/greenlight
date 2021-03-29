<?php
global $USER;
$userId = $arResult["VARIABLES"]["USER_ID"];

$profileUrl = $arResult["FOLDER"] . str_replace(
		"#USER_ID#",
		$arResult["VARIABLES"]["USER_ID"],
		$arResult["URL_TEMPLATES"]["profile"]
	);
?>

<?$APPLICATION->IncludeComponent(
	"itrack:user.access",
	"",
	[
		"USER_ID" => $userId == "my" ? $USER->GetID() : $userId,
		"PROFILE_URL" => $profileUrl
	]
);?>
