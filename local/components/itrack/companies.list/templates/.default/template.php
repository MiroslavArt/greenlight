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
                    <input type="text" name="search" class="search_text" placeholder="<?=GetMessage("search_" . $arParams["PARTY"])?>"/>
                    <input type="submit" class="search" value=""/>
                </form><!-- END search_form -->
                <!-- <a href="/html/stats.html" class="btn">Добавить компанию</a> -->
				<?if ($arResult["USER_IS_BROKER"]):?>
                	<a href="#add_company" data-fancybox class="btn"><?=GetMessage("add_" . $arParams["PARTY"])?></a>
				<?endif?>
            </div><!-- END title_right_block -->
        </div><!-- END title_container -->
        <div class="popup" id="add_company">
            <h3 class="block_title">Добавить <?=$arResult['addtxt']?></h3>
            <form class="form_popup">
                <input type="hidden" id="company_type" value="<?=$arParams['TYPE_ID']?>"/>
                <div class="form_row">
                    <div class="input_container column_50">
                        <input type="text" class="text_input" id="company_name" placeholder="Название компании" />
                    </div><!-- END input_container -->
                    <div class="input_container column_50">
                        <input type="text" class="text_input" id="company_full_name" placeholder="Полное наименование (юридическое наименование)" />
                    </div><!-- END input_container -->
                </div><!-- END form_row -->
                <div class="form_row">
                    <div class="input_container column_50">
                        <input type="text" class="text_input" id="company_adress" placeholder="Юридический адрес" />
                    </div><!-- END input_container -->
                    <div class="input_container column_50">
                        <input type="text" class="text_input" id="company_legal_adress" placeholder="Почтовый адрес" />
                    </div><!-- END input_container -->
                </div><!-- END form_row -->
                <div class="form_row">
                    <div class="input_container column_25">
                        <input type="text" class="text_input" id="company_inn" placeholder="ИНН" />
                    </div><!-- END input_container -->
                    <div class="input_container column_25">
                        <input type="text" class="text_input" id="company_kpp" placeholder="КПП" />
                    </div><!-- END input_container -->
                </div><!-- END form_row -->
                <div class="form_row">
                    <div class="logo_upload_container column_3 margin">
                        <img id="company_logo_view" src="#" width="40" height="40" alt="Лого" />
                        <div class="logo_upload">
                            <input id="company_logo_input" type="file" />
                            <span class="upload"><span>Загрузить лого</span></span>
                        </div><!-- END logo_upload -->
                    </div><!-- END logo_upload_container -->
                </div><!-- END form_row -->
                <p class="link" id="mistake"></p>
                <input type="submit" class="btn" value="Добавить клиента" />
            </form><!-- END form_edit_profile -->
        </div><!-- END popup -->
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
                                <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="link ico_doc"><span>Список договоров страхования</span></a>
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
