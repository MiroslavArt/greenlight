<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Все убытки");
?>

<?php
$APPLICATION->IncludeComponent(
    "itrack:losts",
    "",
    Array(
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "ALLOW_DELETE" => "Y",
        "ALLOW_EDIT" => "Y",
        "CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
        "CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
        "CUSTOM_TITLE_DETAIL_PICTURE" => "",
        "CUSTOM_TITLE_DETAIL_TEXT" => "",
        "CUSTOM_TITLE_IBLOCK_SECTION" => "",
        "CUSTOM_TITLE_NAME" => "",
        "CUSTOM_TITLE_PREVIEW_PICTURE" => "",
        "CUSTOM_TITLE_PREVIEW_TEXT" => "",
        "CUSTOM_TITLE_TAGS" => "",
        "DEFAULT_INPUT_SIZE" => "30",
        "DETAIL_TEXT_USE_HTML_EDITOR" => "N",
        "ELEMENT_ASSOC" => "CREATED_BY",
        "GROUPS" => array(),
        "IBLOCK_ID" => "3",
        "IBLOCK_TYPE" => "main_data",
        "LEVEL_LAST" => "Y",
        "MAX_FILE_SIZE" => "0",
        "MAX_LEVELS" => "100000",
        "MAX_USER_ENTRIES" => "100000",
        "NAV_ON_PAGE" => "10",
        "PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
        "PROPERTY_CODES" => array(),
        "PROPERTY_CODES_REQUIRED" => array(),
        "RESIZE_IMAGES" => "N",
        "SEF_FOLDER" => "/losts/",
        "SEF_MODE" => "Y",
        "SEF_URL_TEMPLATES" => Array("detail"=>"#ELEMENT_ID#/","list"=>""),
        "STATUS" => "ANY",
        "STATUS_NEW" => "N",
        "TYPE_ID" => "",
        "USER_MESSAGE_ADD" => "",
        "USER_MESSAGE_EDIT" => "",
        "USE_CAPTCHA" => "N"
    )
);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>