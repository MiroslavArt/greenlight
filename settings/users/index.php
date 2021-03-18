<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?>

<?$APPLICATION->IncludeComponent("itrack:users", "", [ "SEF_FOLDER" => "/settings/users/" ], false);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
