<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("404 Not Found");

/*$APPLICATION->IncludeComponent("bitrix:main.map", ".default", Array(
	"LEVEL"	=>	"3",
	"COL_NUM"	=>	"2",
	"SHOW_DESCRIPTION"	=>	"Y",
	"SET_TITLE"	=>	"Y",
	"CACHE_TIME"	=>	"3600"
	)
);*/
?>
    <div class="authorization_container settings_page">
        <div class="not_found_container">
            <div class="not_found_column">
                <h1 class="not_found_title">404 ошибка</h1>
                <h2 class="not_found_smalltitle">страница не найдена</h2>
            </div><!-- END not_found_column -->
            <div class="not_found_column">
                <p>Страница, на которые Вы хотели перейти, не найдена. <br />Возможно, введён некорректный адрес или страница была удалена</p>
                <p><a href="index.php" class="link">Вернуться на главную страницу Портала</a></p>
                <p><a href="#" class="link">Написать письмо в службу поддержки</a></p>
            </div><!-- END not_found_column -->
        </div><!-- END not_found_container -->
    </div><!-- END authorization_container -->
    <!--<div class="footer">
        <a href="/html/" class="logo"></a>
        <p class="copy">Copyright &copyl; 2020 Willis Towers Watson. All rights reserved.</p>
    </div>--><!-- END footer -->
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>