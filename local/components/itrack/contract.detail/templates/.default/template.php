<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");

?>

<?php if ($arResult['IS_AJAX'] == 'Y') {
    $APPLICATION->RestartBuffer();
    ob_start();
}
?>
    <div id="contract-wrapper" class="wrapper">
        <a href="<?=$arParams['LIST_URL']?><?=$arParams['CLIENT_ID']?>/" class="back"><?=$arResult['COMPANY']['NAME']?></a>
        <div class="cart_container">
            <div class="cart_block">
                <span class="type_page">Карточка договор страхования клиента</span>
                <div class="client">
                    <img src="<?=$arResult['INSURANCE_COMPANY']['LOGO_SRC']?>" alt="<?=$arResult['INSURANCE_COMPANY']['NAME']?>" width="40" height="40">
                    <h1 class="block_title"><?=$arResult['INSURANCE_COMPANY']['NAME']?></h1>
                </div><!-- END client -->
                <p class="contract_number">№ договора<span><?=$arResult['CONTRACT']['NAME']?></span></p>
            </div><!-- END cart_block -->
        </div>
        <div class="title_container">
            <div class="title_block">
                <h2 class="block_title">Список убытков</h2>
            </div><!-- END title_block -->
            <div class="title_right_block">
                <form class="search_form js-submit js-needs-validation" method="get"
                      data-url="<?= $APPLICATION->GetCurPage() ?>">
                    <?= bitrix_sessid_post() ?>
                    <input type="hidden" name="is_ajax" value="y">
                    <input type="text" name="q_name"
                           value="<?= isset($_REQUEST['q_name']) ? $_REQUEST['q_name'] : '' ?>" class="search_text"
                           placeholder="Поиск по списку убытков"/>
                    <input type="submit" class="search" value=""/>
                </form><!-- END search_form -->
                <!-- <a href="/html/stats.html" class="btn">Добавить убыток</a> -->
                <a href="#add_loss" class="btn" data-fancybox>Добавить убыток</a>
            </div><!-- END title_right_block -->
        </div><!-- END title_container -->
        <div class="popup" id="add_loss">
            <h3 class="block_title">Добавление убытка</h3>
            <form class="form_popup">
                <div class="form_row">
                    <div class="input_container column_25">
                        <input type="text" class="text_input" placeholder="Номер убытка" />
                    </div><!-- END input_container -->
                    <div class="input_container column_25">
                        <input type="text" class="text_input ico_date js_datapicker" placeholder="Дата убытка" />
                    </div><!-- END input_container -->
                    <!-- <a href="#" class="link ico_add"><span>Добавить договор страхования</span></a> -->
                </div><!-- END form_row -->
                <div class="form_row">
                    <div class="input_container column_100">
                        <textarea class="textarea" placeholder="Описание убытка"></textarea>
                    </div><!-- END input_container -->
                    <div class="input_container column_50">
                        <a href="#" class="btn white">Перенести кураторов из договора страхования</a>
                    </div><!-- END input_container -->
                </div><!-- END form_row -->
                <h3 class="subtitle">Кураторы</h3>
                <h4 class="big_label">Клиент</h4>
                <div class="gray_block">
                    <div class="input_container">
                        <label class="big_label">Компания</label>
                        <select class="select js_select">
                            <option>МВМ Entertainment industries</option>
                            <option>1Untropetion Ltd Companies International</option>
                            <option>2Untropetion Ltd Companies International</option>
                            <option>3Untropetion Ltd Companies International</option>
                            <option>4Untropetion Ltd Companies International</option>
                        </select><!-- END select -->
                    </div><!-- END input_container -->
                </div><!-- END gray_block -->
                <h4 class="big_label">Страховой брокер</h4>
                <div class="gray_block">
                    <div class="input_container">
                        <label class="big_label">Компания</label>
                        <select class="select js_select">
                            <option>МВМ Entertainment industries</option>
                            <option>1Untropetion Ltd Companies International</option>
                            <option>2Untropetion Ltd Companies International</option>
                            <option>3Untropetion Ltd Companies International</option>
                            <option>4Untropetion Ltd Companies International</option>
                        </select><!-- END select -->
                    </div><!-- END input_container -->
                </div><!-- END gray_block -->
                <h4 class="big_label">Страховая компания</h4>
                <div class="gray_block">
                    <div class="input_container">
                        <label class="big_label">Компания</label>
                        <select class="select js_select">
                            <option>МВМ Entertainment industries</option>
                            <option>1Untropetion Ltd Companies International</option>
                            <option>2Untropetion Ltd Companies International</option>
                            <option>3Untropetion Ltd Companies International</option>
                            <option>4Untropetion Ltd Companies International</option>
                        </select><!-- END select -->
                    </div><!-- END input_container -->
                </div><!-- END gray_block -->
                <div class="gray_block">
                    <div class="input_container">
                        <label class="big_label">Компания</label>
                        <select class="select js_select">
                            <option>МВМ Entertainment industries</option>
                            <option>1Untropetion Ltd Companies International</option>
                            <option>2Untropetion Ltd Companies International</option>
                            <option>3Untropetion Ltd Companies International</option>
                            <option>4Untropetion Ltd Companies International</option>
                        </select><!-- END select -->
                    </div><!-- END input_container -->
                </div><!-- END gray_block -->
                <div class="gray_block">
                    <div class="input_container">
                        <label class="big_label">Аджастер</label>
                        <select class="select js_select">
                            <option>МВМ Entertainment industries</option>
                            <option>1Untropetion Ltd Companies International</option>
                            <option>2Untropetion Ltd Companies International</option>
                            <option>3Untropetion Ltd Companies International</option>
                            <option>4Untropetion Ltd Companies International</option>
                        </select><!-- END select -->
                    </div><!-- END input_container -->
                </div><!-- END gray_block -->
                <div class="form_row">
                    <div class="switches_container">
                        <label class="big_label">Необходимость акцепта</label>
                        <div class="switch_container">
                            <label class="switch js_checkbox"><input type="checkbox"></label>
                            <span>Клиент</span>
                        </div><!-- END switch_container -->
                        <div class="switch_container">
                            <label class="switch js_checkbox"><input type="checkbox"></label>
                            <span>Страховой Брокер</span>
                        </div><!-- END switch_container -->
                        <div class="switch_container">
                            <label class="switch js_checkbox"><input type="checkbox"></label>
                            <span>Страховая Компания</span>
                        </div><!-- END switch_container -->
                        <div class="switch_container">
                            <label class="switch js_checkbox"><input type="checkbox"></label>
                            <span>Аджастер</span>
                        </div><!-- END switch_container -->
                    </div><!-- END switches_container -->
                    <div class="switches_container">
                        <label class="big_label">Уведомления</label>
                        <div class="switch_container">
                            <label class="switch js_checkbox"><input type="checkbox"></label>
                            <span>Клиент</span>
                        </div><!-- END switch_container -->
                        <div class="switch_container">
                            <label class="switch js_checkbox"><input type="checkbox"></label>
                            <span>Страховой Брокер</span>
                        </div><!-- END switch_container -->
                        <div class="switch_container">
                            <label class="switch js_checkbox"><input type="checkbox"></label>
                            <span>Страховая Компания</span>
                        </div><!-- END switch_container -->
                        <div class="switch_container">
                            <label class="switch js_checkbox"><input type="checkbox"></label>
                            <span>Аджастер</span>
                        </div><!-- END switch_container -->
                    </div><!-- END switches_container -->
                </div><!-- END form_row -->
                <div class="form_row">
                    <div class="input_container column_100">
                        <input type="text" class="text_input" placeholder="Запрашиваемые документы" />
                    </div><!-- END input_container -->
                </div><!-- END form_row -->
                <div class="form_row">
                    <div class="input_container column_25">
                        <input type="text" class="text_input ico_date js_datapicker" placeholder="Дата запроса" />
                    </div><!-- END input_container -->
                    <div class="input_container column_50">
                        <input type="text" class="text_input" placeholder="Автор запроса" />
                    </div><!-- END input_container -->
                    <div class="input_container column_25">
                        <input type="text" class="text_input ico_date js_datapicker" placeholder="Срок предоставления" />
                    </div><!-- END input_container -->
                </div><!-- END form_row -->
                <div class="form_row">
                    <label class="big_label">Прикрепить полезные документы</label>
                    <div class="input_container column_3">
                        <div class="logo_upload_container without_img">
                            <div class="logo_upload">
                                <input type="file" />
                                <span class="upload"><span>Прикрепить запрос</span></span>
                            </div><!-- END logo_upload -->
                        </div><!-- END logo_upload_container -->
                    </div><!-- END input_container -->
                </div><!-- END form_row -->
                <input type="submit" class="btn" value="Добавить договор" />
            </form><!-- END form_edit_profile -->
        </div><!-- END popup -->
        <?php if (!empty($arResult['LOSTS'])) : ?>
            <div id="losts-list">
                <ul class="data_table">
                    <li class="row table_head">
                        <div class="table_block align_left item2"><p>Статус</p></div>
                        <div class="table_block align_left item3"><p>Статус развернуто</p></div>
                        <div class="table_block align_left item3"><p>Уникальный <br>номер</p></div>
                        <div class="table_block align_left item2"><p>Дата</p></div>
                        <div class="table_block align_left item2"><p>Описание</p></div>
                        <div class="table_block links_column align_left item9"><p>Ссылки</p></div>
                    </li>
                    <?php foreach ($arResult['LOSTS'] as $arItem) : ?>
                        <li class="row">
                            <div class="table_block align_left item2" data-name="Статус"><span class="status"></span></div>
                            <div class="table_block align_left item3" data-name="Статус развернуто"><p><?=$arItem['STATUS']['UF_NAME']?></p></div>
                            <div class="table_block align_left item3" data-name="Уникальный номер"><p><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></p></div>
                            <div class="table_block align_left item2" data-name="Дата"><?=$arItem['DATE']?></div>
                            <div class="table_block align_left item2" data-name="Описание"><?=$arItem['DESCRIPTION']?></div>
                            <div class="table_block links_column item9" data-name="Ссылки">
                                <a href="#" class="link"><span>Список <br>Аджастеров</span></a>
                                <a href="/html/docs.html" class="link"><span>Список <br>документов</span></a>
                                <a href="#" class="link"><span>Корреспон<wbr>денция</span></a>
                                <a href="#" class="link"><span>Отчеты</span></a>
                            </div><!-- END links_column -->
                        </li>
                    <?php endforeach; ?>
                </ul><!-- END data_table -->
            </div>
        <?php else: ?>
            <div id="losts-list">
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
    $arResult['htmlContainer'] = '#losts-list';
    $arResult['success'] = true;
    if (!empty($arResult['ERROR'])) {
        unset($arResult['success']);
        $arResult['errorMessage'] = true;
    }
    echo json_encode($arResult);
    die();
}