<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

\Bitrix\Main\UI\Extension::load("ui.alerts");

?>

    <h3 class="block_title">Все Аджастеры по убытку</h3>
<?php foreach ($arResult['ADJUSTER_COMPANIES'] as $arItem) : ?>
    <div class="client_block">
        <?php if(!empty($arItem['PROPERTIES']['LOGO']['VALUE']) && intval($arItem['PROPERTIES']['LOGO']['VALUE']) > 0 ) :?>
            <img src="<?=CFile::ResizeImageGet($arItem['PROPERTIES']['LOGO']['VALUE'], ['width' => 40, 'height' => 40])['src']?>" alt="<?=$arItem['NAME']?>" width="40" height="40">
        <?php endif; ?>
        <span><?=$arItem['NAME']?></span>
    </div>
<?php endforeach;?>