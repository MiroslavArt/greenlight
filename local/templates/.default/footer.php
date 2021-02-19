<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>


<!-- Yandex.Metrika counter -->
<? $APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "file",
        "EDIT_TEMPLATE" => "",
        "PATH" => DEFAULT_TEMPLATE_PATH . "/include/yandex.php"
    )
);
?>
<!-- /Yandex.Metrika counter -->
</body>
</html>