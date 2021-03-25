<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");
//\Itrack\Custom\Helpers\Utils::varDump($arResult['DOCUMENTS']);
?>


<div class="wrapper">
    <a href="<?=$arParams['PATH_TO']['lost']?>" class="back">К убытку</a>
    <div class="title_container">
        <div class="title_block table_big">
            <span class="type_page">Карточка документа</span>
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
        <a href="#add_contract" class="btn" data-fancybox>Акцептовать</a>
        <a href="#all_sk" class="btn" data-fancybox>Отклонить</a>
        <a href="#add_loss" class="btn" data-fancybox>Все статусы <br />документа</a>
        <a href="#add_user" class="btn" data-fancybox>Добавить</a>
    </div><!-- END desc_container -->
    <?php if(!empty($arResult['DOCUMENTS'])) :?>
    <ul class="data_table">
        <li class="row table_head">
            <div class="table_block align_left"></div>
            <div class="table_block align_left item8"><p>Название файла</p></div>
            <div class="table_block align_left item3"><p>Срок предоставления</p></div>
            <div class="table_block align_left item3"><p>Дата предоставления</p></div>
            <div class="table_block align_left"></div>
            <div class="table_block align_left item6"><p>Комментарий</p></div>
        </li>
        <?php foreach ($arResult['DOCUMENTS'] as $arDocument) :?>
            <li class="row">
                <div class="table_block align_left align_top" data-name="Открепить"><a href="#" class="delete"></a></div>
                <div class="table_block align_left align_top item8" data-name="Название файла"><?=(!empty($arDocument['FILE']['SRC'])) ? '<a href="' . $arDocument['FILE']['SRC'] . '" download>' . $arDocument['UF_NAME'] . '</a>' : $arDocument['UF_NAME']?></div>
                <div class="table_block align_left align_top item3" data-name="Срок предоставления">
                    <?=$arResult['DOCUMENT']['PROPERTIES']['REQUEST_DEADLINE']['VALUE']?>
                </div>
                <div class="table_block align_left align_top item3" data-name="Дата предоставления"><?=$arDocument['UF_DATE_CREATED']->format('d.m.Y')?></div>
                <div class="table_block align_left align_top mob_hihe"><a href="#" class="remarks"></a></div>
                <div class="table_block align_left align_top item6" data-name="Комментарий"><a href="#" class="remarks mob"></a><p><?=$arDocument['UF_COMMENT']?></p></div>
            </li>
        <?php endforeach; ?>
    </ul><!-- END data_table -->
    <?php endif;?>
</div><!-- END wrapper -->
