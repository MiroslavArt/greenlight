<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="desc_container">
    <div class="desc big">
        <span class="desc_title">Описание страхового случая</span>
        <?php if (!empty($arResult['LOST']['PROPERTIES']['DESCRIPTION'])) : ?>
            <?= $arResult['LOST']['PROPERTIES']['DESCRIPTION']['VALUE'] ?>
        <?php endif; ?>
    </div><!-- END desc -->
    <div class="docs_container">
        <span class="desc_title">Документы по убытку</span>
        <?php if(!empty($arResult['DOCS_STATUSES'])) : ?>
            <ul class="list_dosc">
                <?php foreach ($arResult['DOCS_STATUSES'] as $key=>$arStatus) : ?>
                    <li class="<?=$arResult['STATUSES'][$key][$arResult['COLOR_FIELD']]?>">
                        <p><?=$arResult['STATUSES'][$key]['UF_NAME']?></p>
                        <span><?=count($arStatus['DOCS'])?> док.</span>
                    </li>
                <?php endforeach;?>
            </ul><!-- END list_docs -->
        <?php endif; ?>
        <div class="docs_container_bottom">
            <?php if ($arResult['CONTRACT']['PROPERTIES']['ORIGIN_REQUIRED']['VALUE_XML_ID'] == 'Y') : ?>
                <span class="originals"><span>Предоставление оригиналов</span></span>
            <?php else : ?>
                <span class="originals no_files"><span>Без предоставления оригиналов</span></span>
            <?php endif; ?>
            <?php if (!empty($arResult['REQUESTS'])) : ?>
                <?php $intDocsQty = count($arResult['REQUESTS']); ?>
                <span class="desc_title">всего <?= $intDocsQty; ?> <?= num2word($intDocsQty, array('документ', 'документа', 'документов')); ?></span>
            <?php endif; ?>
        </div><!-- END docs_container_bottom -->
    </div><!-- END docs_container -->
</div><!-- END desc_container -->
<span class="type_page">Все кураторы по убытку</span><br/>
<?php if (!empty($arResult['CURATORS'])) : ?>
<ul class="data_table">
    <li class="row table_head">
        <div class="table_block align_left item2"><p>Роль</p></div>
        <div class="table_block align_left item3"><p>Компания</p></div>
        <div class="table_block align_left item3"><p>Роль куратора</p></div>
        <div class="table_block align_left item4"><p>ФИО</p></div>
        <div class="table_block align_left item4"><p>Должность</p></div>
        <div class="table_block align_left item3"><p>Эл. почта</p></div>
        <div class="table_block align_left item3"><p>Телефон</p></div>
    </li>
    <?php foreach ($arResult['CURATORS'] as $arCurator) : ?>
        <li class="row">
            <div class="table_block align_left align_top item2" data-name="Роль"><?= $arCurator['COMPANY_TYPE'] ?></div>
            <div class="table_block align_left align_top item3" data-name="Компания"><?= $arCurator['COMPANY_NAME'] ?></div>
            <div class="table_block align_left align_top item3" data-name="Роль куратора">Куратор<?=(!empty($arCurator['IS_LEADER']) && $arCurator['IS_LEADER'] == 'Y') ? ' - Лидер' : '';?></div>
            <div class="table_block align_left align_top item4" data-name="ФИО"><?= \CUser::formatName('#NAME# #SECOND_NAME# #LAST_NAME#', $arCurator, false, false); ?></div>
            <div class="table_block align_left align_top item4" data-name="Должность"><?= $arCurator['POSITION'] ?></div>
            <div class="table_block align_left align_top item3" data-name="Эл. почта"><?= $arCurator['EMAIL'] ?></div>
            <div class="table_block align_left align_top item3" data-name="Телефон"><?= $arCurator['PHONE'] ?></div>
        </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>