<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {die();} ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . '/local/templates/.default/header.php'); ?>
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
        <div class="footer">
            <a href="#" class="logo"></a>
            <p class="copy">Copyright &copyl; 2020 Willis Towers Watson. All rights reserved.</p>
        </div><!-- END footer -->
    </div><!-- END index_content -->
</div><!-- END index_container -->
