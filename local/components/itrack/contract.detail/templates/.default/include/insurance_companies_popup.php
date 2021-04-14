<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();  ?>

<?php if(!empty($arResult['INSURANCE_COMPANIES'])) :?>
<div class="popup small" id="all_sk">
    <h3 class="block_title">Все СК по договору</h3>
    <?php foreach ($arResult['INSURANCE_COMPANIES'] as $arItem) : ?>
    <div class="client_block">
        <?php if(!empty($arItem['PROPERTIES']['LOGO']['VALUE']) && intval($arItem['PROPERTIES']['LOGO']['VALUE']) > 0 ) :?>
        <img src="<?=CFile::ResizeImageGet($arResult['INSURANCE_COMPANY']['PROPERTIES']['LOGO']['VALUE'], ['width' => 40, 'height' => 40])['src']?>" alt="<?=$arItem['NAME']?>" width="40" height="40">
        <?php endif; ?>
        <span><?=$arItem['NAME']?></span>
    </div>
    <?php endforeach;?>
</div>
<?php endif;?>