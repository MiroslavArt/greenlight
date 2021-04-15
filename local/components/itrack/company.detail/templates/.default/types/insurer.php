<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="wrapper">
    <a href="<?=$arParams['LIST_URL']?>" class="back">Вернуться к главному разделу</a>
    <div class="title_container">
        <div class="title_block">
            <span class="type_page">Карточка страховой компании</span>
            <h2 class="block_title"><?= $arResult['COMPANY']['NAME'] ?></h2>
        </div><!-- END title_block -->
        <div class="title_right_block">
            <form class="search_form">
                <input type="text" class="search_text" placeholder="Поиск по списку клиентов"/>
                <input type="submit" class="search" value=""/>
            </form><!-- END search_form -->
            <a href="<?=$arParams['PATH_TO']['useful-documents']?>" class="btn tablet_hide">Полезные документы</a>
            <a href="#" class="btn">Добавить клиента</a>
        </div><!-- END title_right_block -->
    </div><!-- END title_container -->
    <?php if (!empty($arResult['ITEMS'])):?>
        <div id="clients-list">
            <ul class="data_table">
                <li class="row table_head">
                    <div class="table_block clients_column item6"><p>Название компании</p></div>
                    <div class="table_block stat_column item2"><p>Убытки, <br/>шт</p></div>
                    <div class="table_block stat_column item2"><p>Закрыто</p></div>
                    <div class="table_block stat_column item2"><p>Документы <br/>предоставлены</p></div>
                    <div class="table_block stat_column item2"><p>Открыто</p></div>
                    <div class="table_block links_column item8"><p>Ссылки</p></div>
                </li>
                <?php foreach ($arResult['ITEMS'] as $arItem):?>
                    <li class="row">
                        <div class="table_block clients_column item6 no_header" data-name="Клиент">
                            <?php if (!empty($arItem['LOGO_SRC'])) : ?>
                                <img src="<?= $arItem['LOGO_SRC'] ?>" width="40" height="40"
                                     alt="<?= $arItem['NAME'] ?>"/>
                            <?php endif; ?>
                            <span><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?= $arItem['NAME'] ?></a></span>
                        </div><!-- END table_block -->
                        <div class="table_block stat_column item2" data-name="Убытки, шт"><?= $arItem["CNT"]["SUM"] ?: 0 ?></div>
                        <div class="table_block stat_column green item2" data-name="Закрыто"><?=$arItem["CNT"]["green"] ?: 0?></div>
                        <div class="table_block stat_column yellow item2" data-name="Документы предоставлены"><?=$arItem["CNT"]["yellow"] ?: 0?>
                        </div>
                        <div class="table_block stat_column red item2" data-name="Открыто"><?=$arItem["CNT"]["red"] ?: 0 ?></div>
                        <div class="table_block links_column item8" data-name="Ссылки">
                            <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="link ico_doc"><span>Все убытки</span></a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul><!-- END data_table -->
            <ul class="data_table no_bg">
                <li class="row">
                    <div class="table_block head_column item6" data-name="Клиент"><p>Итого</p></div>
                    <div class="table_block stat_column item2" data-name="Убытки, шт"><?= $arResult["CNT_TOTAL"]["SUM"] ?: 0?></div>
                    <div class="table_block stat_column item2" data-name="Закрыто"><?= $arResult["CNT_TOTAL"]["green"] ?: 0 ?></div>
                    <div class="table_block stat_column item2" data-name="Документы предоставлены"><?= $arResult["CNT_TOTAL"]["yellow"] ?: 0 ?></div>
                    <div class="table_block stat_column item2" data-name="Открыто"><?= $arResult["CNT_TOTAL"]["red"] ?: 0 ?></div>
                    <div class="table_block links_column"></div>
                </li>
            </ul><!-- END data_table -->
        </div>
    <?php else: ?>
        <div id="clients-list">
            <div class="ui-alert ui-alert-danger ui-alert-icon-danger has-errors">
                <span class="ui-alert-message">Данные не найдены</span>
            </div>
        </div>
    <?php endif; ?>
</div><!-- END wrapper -->