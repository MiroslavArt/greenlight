<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");

?>

<?php if ($arResult['IS_AJAX'] == 'Y') {
    $APPLICATION->RestartBuffer();
    ob_start();
}
?>
    <div id="clients-wrapper" class="wrapper">
        <a href="/" class="back">Вернуться на домашнюю страницу</a>
        <div class="title_container">
            <div class="title_block">
                <h2 class="block_title"><? (empty($arResult['IS_AJAX']) ? $APPLICATION->ShowTitle(false) : '') ?></h2>
            </div><!-- END title_block -->
            <div class="title_right_block">
                <form class="search_form js-submit js-needs-validation" method="get"
                      data-url="<?= $APPLICATION->GetCurPage() ?>">
                    <?= bitrix_sessid_post() ?>
                    <input type="hidden" name="is_ajax" value="y">
                    <input type="text" name="q_name"
                           value="<?= isset($_REQUEST['q_name']) ? $_REQUEST['q_name'] : '' ?>" class="search_text"
                           placeholder="Поиск по списку клиентов"/>
                    <input type="submit" class="search" value=""/>
                </form><!-- END search_form -->
                <a href="/html/stats.html" class="btn">Добавить клиента</a>
            </div><!-- END title_right_block -->
        </div><!-- END title_container -->
        <?php if (!empty($arResult['ITEMS'])) : ?>
            <div id="clients-list">
                <ul class="data_table">
                    <li class="row table_head">
                        <div class="table_block clients_column item6"><p>Клиент</p></div>
                        <div class="table_block stat_column item2"><p>Убытки, <br/>шт</p></div>
                        <div class="table_block stat_column item2"><p>Закрыто</p></div>
                        <div class="table_block stat_column item2"><p>Документы <br/>предоставлены</p></div>
                        <div class="table_block stat_column item2"><p>Открыто</p></div>
                        <div class="table_block links_column item8"><p>Ссылки</p></div>
                    </li>
                    <?php foreach ($arResult['ITEMS'] as $arItem) : ?>
                        <li class="row">
                            <div class="table_block clients_column item6 no_header" data-name="Клиент">
                                <?php if (!empty($arItem['LOGO_SRC'])) : ?>
                                    <img src="<?= $arItem['LOGO_SRC'] ?>" width="40" height="40"
                                         alt="<?= $arItem['NAME'] ?>"/>
                                <?php endif; ?>
                                <span><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?= $arItem['NAME'] ?></a></span>
                            </div><!-- END table_block -->
                            <div class="table_block stat_column item2" data-name="Убытки, шт">3</div>
                            <div class="table_block stat_column green item2" data-name="Закрыто">1</div>
                            <div class="table_block stat_column yellow item2" data-name="Документы предоставлены">1
                            </div>
                            <div class="table_block stat_column red item2" data-name="Открыто">1</div>
                            <div class="table_block links_column item8" data-name="Ссылки">
                                <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="link ico_doc"><span>Список договоров страхования</span></a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul><!-- END data_table -->
                <ul class="data_table no_bg">
                    <li class="row">
                        <div class="table_block head_column item6" data-name="Клиент"><p>Итого</p></div>
                        <div class="table_block stat_column item2" data-name="Убытки, шт">18</div>
                        <div class="table_block stat_column item2" data-name="Закрыто">6</div>
                        <div class="table_block stat_column item2" data-name="Документы предоставлены">6</div>
                        <div class="table_block stat_column item2" data-name="Открыто">6</div>
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

<?php
if ($arResult['IS_AJAX'] == 'Y') {
    $html = ob_get_contents();
    ob_end_clean();
    $arResult['html'] = $html;
    $arResult['htmlContainer'] = '#clients-list';
    $arResult['success'] = true;
    if (!empty($arResult['ERROR'])) {
        unset($arResult['success']);
        $arResult['errorMessage'] = true;
    }
    echo json_encode($arResult);
    die();
}
