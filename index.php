<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Обмен электронными документами по убыткам");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Главная страница");
?>

<div class="index_container flexbox">
    <div class="index_bg">
        <h1 class="index_title">Обмен электронными <br />документами по <br />убыткам</h1>
    </div><!-- END index_bg -->
    <div class="index_content">
        <div class="tiles_container">
            <div class="tile">
                <a href="/html/edit_profile_user.html"><img src="images/img.png" width="120" height="120" alt="img" /></a>
                <div class="tile_content">
                    <span class="who">Брокер</span>
                    <h2 class="name">Сергей Григорьевич Петровольский</h2>
                    <span class="company">N-mark Industries Corporation TM</span>
                    <span class="access">Права доступа «Пользователь»</span>
                </div><!-- END tile_content -->
                <a href="/html/authorization.html" class="logout"></a>
            </div><!-- END tile -->
            <a href="/html/clients.html" class="tile_link ico_1">Клиенты</a>
            <a href="/html/company.html" class="tile_link ico_2">Страховые <br />компании</a>
            <a href="/html/company.html" class="tile_link ico_3">Аджастеры</a>
            <a href="/html/settings.html" class="tile_link ico_4">Настройки</a>
            <a href="/html/loss_card.html" class="btn">Все убытки</a>
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



