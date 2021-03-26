<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");
//\Itrack\Custom\Helpers\Utils::varDump($arResult['HISTORY']);
?>


<div class="wrapper">
    <a href="<?=$arParams['PATH_TO']['lost-document']?>" class="back">К документу</a>
    <div class="title_container">
        <div class="title_block table_big">
            <span class="type_page">Статусы документа</span>
            <h2 class="block_title"><?=$arResult['DOCUMENT']['NAME']?></h2>
            <div class="card_status_container">
                <span class="card_status <?=$arResult['DOCUMENT']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR']?>">
                    <?=$arResult['DOCUMENT']['PROPERTIES']['STATUS']['VALUE']['UF_NAME']?>
                </span>
                <span class="type_page">от <?=$arResult['DOCUMENT']['PROPERTIES']['STATUS_DATE']['VALUE']?></span>
            </div><!-- END card_status_container -->
        </div><!-- END title_block -->
        <?php if($arResult['DOCUMENT']['PROPERTIES']['GET_ORIGINAL']['VALUE'] == 'Y') : ?>
        <div class="title_right_block">
            <div class="docs_container desc_left">
                <ul class="list_dosc">
                    <li>
                        <p>Оригинал предоставлен</p>
                    </li>
                </ul><!-- END list_docs -->
                <div class="docs_container_bottom">
                    <span class="desc_title">от 01.07.2017</span>
                </div><!-- END docs_container_bottom -->
            </div><!-- END docs_container -->
        </div><!-- END title_right_block -->
        <?php endif;?>
    </div><!-- END title_container -->
    <div class="desc_container">
        <ul class="doc_info">
            <li>
                <span>Срок предоставления</span>
                <p><?=$arResult['DOCUMENT']['PROPERTIES']['REQUEST_DEADLINE']['VALUE']?></p>
            </li>
            <?php if(!empty($arResult['REQUEST_USER_FIO'])) : ?>
            <li>
                <span>Автор</span>
                <p><?=$arResult['REQUEST_USER_FIO']?></p>
            </li>
            <?php endif; ?>
            <li>
                <span>Ссылка на запрос</span>
                <p><a href="#" class="link"><span>Запрос</span></a></p>
            </li>
        </ul><!-- END doc_info -->
    </div><!-- END desc_container -->
    <?php if(!empty($arResult['HISTORY'])) :?>
    <ul class="data_table">
        <li class="row table_head">
            <div class="table_block align_left item4"><p>Дата присвоения статуса</p></div>
            <div class="table_block align_left item4"><p>Статус развернуто</p></div>
            <div class="table_block align_left item4"><p>Кем установлен статус</p></div>
            <div class="table_block align_left"></div>
            <div class="table_block align_left item8"><p>Комментарий</p></div>
        </li>
        <?php foreach ($arResult['HISTORY'] as $arStatus) :?>
            <li class="row">
                <div class="table_block align_left align_top item4" data-name="Дата присвоения статуса"><?=$arStatus['UF_DATE']->format('d.m.Y')?></div>
                <div class="table_block align_left align_top item4" data-name="Статус развернуто"><?=$arStatus['STATUS']['UF_NAME']?></div>
                <div class="table_block align_left align_top item4" data-name="Кем установлен статус"><?=$arStatus['USER_FIO']?></div>
                <div class="table_block align_left align_top mob_hihe"><a href="#" class="remarks"></a></div>
                <div class="table_block align_left align_top item8" data-name="Комментарий"><a href="#" class="remarks mob"></a><p><?=$arStatus['UF_COMMENT']?></p></div>
            </li>
        <?php endforeach; ?>
    </ul><!-- END data_table -->
    <?php endif;?>
</div><!-- END wrapper -->
