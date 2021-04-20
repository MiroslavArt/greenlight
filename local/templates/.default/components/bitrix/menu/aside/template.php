<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<?php if (!empty($arResult)): ?>
    <div class="header">
        <a href="<?=$arResult['USER']["DETAIL_URL"]?>" class="user_link"><img src="<?=$arResult['USER']['PERSONAL_PHOTO']['SRC']?>" width="40" height="40" alt="img" /></a>
        <span class="open_menu_mob js_open_menu"></span>
    </div><!-- END header -->
    <div class="main_menu">
        <span class="open_menu js_open_menu"></span>
        <div class="user_container">
            <a href="<?=$arResult['USER_COMPANY']["DETAIL_URL"]?>"><img src="<?=$arResult['COMPANY_LOGO']['SRC']?>" width="60" height="60" alt="img" /></a>
            <div class="user_content">
                <a href="<?=$arResult['USER']["DETAIL_URL"]?>" class="username"><?=$arResult['USER']['NAME_FORMATTED']?></a>
                <span class="company"><?=(!empty($arResult['USER_COMPANY']['NAME']) ? $arResult['USER_COMPANY']['NAME'] : '')?></span>
            </div><!-- END user_content -->
        </div><!-- END user_container -->
        <div class="menu_btn_container">
            <a href="/">
                <svg width="24" height="26" viewBox="0 0 24 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.33333 25V13H15.6667V25M1 9.4L12 1L23 9.4V22.6C23 23.2365 22.7425 23.847 22.284 24.2971C21.8256 24.7471 21.2039 25 20.5556 25H3.44444C2.79614 25 2.17438 24.7471 1.71596 24.2971C1.25754 23.847 1 23.2365 1 22.6V9.4Z" stroke="#C2C7CF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                <span style="display: none;">На главную</span>
            </a>
            <?php if (!empty($arResult['LINKS'])) : ?>
                <?php foreach ($arResult['LINKS'] as $arItem) : ?>
                    <a href="<?= $arItem["LINK"] ?>" <?php if ($arItem["SELECTED"]) { ?>class="active"<? } ?>>
                        <?=(!empty($arItem['PARAMS']['icon']) ? $arItem['PARAMS']['icon'] : '')?>
                        <span><?= $arItem["TEXT"] ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            <a href="<?=$APPLICATION->GetCurPageParam("logout=yes&".bitrix_sessid_get(), array("login", "logout", "register", "forgot_password", "change_password"));?>" class="menu_logout">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 19H3C2.46957 19 1.96086 18.7893 1.58579 18.4142C1.21071 18.0391 1 17.5304 1 17V3C1 2.46957 1.21071 1.96086 1.58579 1.58579C1.96086 1.21071 2.46957 1 3 1H7M14 15L19 10M19 10L14 5M19 10H7" stroke="#C2C7CF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Выйти из аккаунта</span>
            </a>
        </div><!-- END menu_btn_container -->
    </div><!-- END main_menu -->
<?php endif; ?>