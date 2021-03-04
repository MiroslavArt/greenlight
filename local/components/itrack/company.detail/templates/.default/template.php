<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");

?>

<?php if ($arResult['IS_AJAX'] == 'Y') {
    $APPLICATION->RestartBuffer();
    ob_start();
}
?>
    <div id="contracts-wrapper" class="wrapper">
        <a href="<?=$arParams['LIST_URL']?>" class="back">Вернуться назад</a>
        <div class="title_container">
            <div class="title_block">
                <span class="type_page">Карточка клиента</span>
                <h2 class="block_title"><?=$arResult['COMPANY']['NAME']?></h2>
            </div><!-- END title_block -->
            <div class="title_right_block">
                <form class="search_form js-submit js-needs-validation" method="get"
                      data-url="<?= $APPLICATION->GetCurPage() ?>">
                    <?= bitrix_sessid_post() ?>
                    <input type="hidden" name="is_ajax" value="y">
                    <input type="text" name="q_name"
                           value="<?= isset($_REQUEST['q_name']) ? $_REQUEST['q_name'] : '' ?>" class="search_text"
                           placeholder="Поиск по списку договоров"/>
                    <input type="submit" class="search" value=""/>
                </form><!-- END search_form -->
                <a href="/html/stats.html" class="btn">Добавить договор</a>
            </div><!-- END title_right_block -->
        </div><!-- END title_container -->
        <?php if (!empty($arResult['CONTRACTS'])) : ?>
            <div id="contracts-list">
                <ul class="data_table">
                    <li class="row table_head">
                        <div class="table_block align_left item3"><p>№ договора</p></div>
                        <div class="table_block align_left item2"><p>Дата договора</p></div>
                        <div class="table_block align_left item3"><p>Вид страхования</p></div>
                        <div class="table_block stat_column"><p>Убытки, <br />шт</p></div>
                        <div class="table_block stat_column"><p>Закрыто</p></div>
                        <div class="table_block stat_column item2"><p>Документы <br />предоставлены</p></div>
                        <div class="table_block stat_column"><p>Открыто</p></div>
                        <div class="table_block item3"><p>СК (Лидер)</p></div>
                        <div class="table_block item6 links_column"><p>Ссылки</p></div>
                    </li>
                    <?php foreach ($arResult['CONTRACTS'] as $arItem) : ?>
                        <li class="row">
                            <div class="table_block align_left item3" data-name="№ договора"><p><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></p></div>
                            <div class="table_block align_left item2" data-name="Дата договора"><p><?=$arItem['DATE']?></p></div>
                            <div class="table_block align_left item3" data-name="Вид страхования"><p><?=$arItem['TYPE']?></p></div>
                            <div class="table_block stat_column" data-name="Убытки, шт">3</div>
                            <div class="table_block stat_column green">1</div>
                            <div class="table_block stat_column yellow item2">1</div>
                            <div class="table_block stat_column red">1</div>
                            <div class="table_block item3"><p class="ico_check"><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['INSURANCE_COMPANY_LEADER_NAME']?></a></p></div>
                            <div class="table_block item6 links_column">
                                <a href="#" class="link ico_doc"><span>Все СК по договору</span></a>
                                <a href="#" class="link ico_doc"><span>Все убытки</span></a>
                            </div><!-- END links_column -->
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
            <div id="contracts-list">
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
    $arResult['htmlContainer'] = '#contracts-list';
    $arResult['success'] = true;
    if (!empty($arResult['ERROR'])) {
        unset($arResult['success']);
        $arResult['errorMessage'] = true;
    }
    echo json_encode($arResult);
    die();
}
