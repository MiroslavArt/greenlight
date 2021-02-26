<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Обмен электронными документами по убыткам");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Главная страница");
?>

<div class="index_container flexbox">
    <div class="index_bg">
        <h1 class="index_title">Обмен электронными <br />документами <br />по убыткам</h1>
    </div><!-- END index_bg -->
    <div class="index_content">
        <div class="tiles_container type_2">
            <div class="tile">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:main.user.link",
                    "",
                    Array(
                        "CACHE_TIME" => "7200",
                        "CACHE_TYPE" => "A",
                        "DATE_TIME_FORMAT" => "d.m.Y H:i:s",
                        "ID" => $USER->GetID(),
                        "NAME_TEMPLATE" => "#NOBR##NAME# #SECOND_NAME# #LAST_NAME##/NOBR#",
                        "PATH_TO_SONET_USER_PROFILE" => "",
                        "PROFILE_URL" => "/users/profile/",
                        "SHOW_FIELDS" => Array("PERSONAL_BIRTHDAY","PERSONAL_ICQ","PERSONAL_PHOTO","PERSONAL_CITY","WORK_COMPANY","WORK_POSITION"),
                        "SHOW_LOGIN" => "Y",
                        "SHOW_YEAR" => "Y",
                        "THUMBNAIL_DETAIL_SIZE" => "120",
                        "THUMBNAIL_LIST_SIZE" => "120",
                        "USER_PROPERTY" => Array(),
                        "USE_THUMBNAIL_LIST" => "Y",
                        "INLINE" => "N"
                    )
                );?>
            </div><!-- END tile -->
            <a href="/clients/" class="tile_link ico_1">Клиенты</a>
            <a href="/html/company.html" class="tile_link ico_2">Страховые <br />компании</a>
            <a href="/html/company.html" class="tile_link ico_3">Аджастеры</a>
            <a href="/html/settings.html" class="tile_link ico_4">Настройки</a>
            <a href="/html/loss_card.html" class="tile_link ico_5">Все убытки</a>
        </div><!-- END tiles_container -->
        <? $APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            Array(
                "AREA_FILE_SHOW" => "file",
                "EDIT_TEMPLATE" => "",
                "PATH" => DEFAULT_TEMPLATE_PATH . "/include/footer.php"
            )
        );
        ?>
    </div><!-- END index_content -->
</div><!-- END index_container -->

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>