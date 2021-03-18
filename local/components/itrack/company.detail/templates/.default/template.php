<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset;
\Bitrix\Main\UI\Extension::load("ui.alerts");
//Asset::getInstance()->addJs(SITE_TEMPLATE_PATH ."/js/jquery.js");
Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH ."/css/jquery-ui.css");
Asset::getInstance()->addJs(DEFAULT_TEMPLATE_PATH ."/js/jquery-ui.js");

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
                <a href="#add_contract" class="btn" data-fancybox>Добавить договор</a>
            </div><!-- END title_right_block -->
        </div><!-- END title_container -->
        <div class="popup" id="add_contract">
            <h3 class="block_title">Добавление договора</h3>
            <form class="form_popup">
                <div class="form_row">
                    <div class="input_container column_25">
                        <input type="text" class="text_input" placeholder="Номер договора" />
                    </div><!-- END input_container -->
                    <div class="input_container column_25">
                        <input type="text" class="text_input ico_date js_datapicker" placeholder="Дата договора" />
                    </div><!-- END input_container -->
                    <div class="input_container column_50">
                        <select data-placeholder="Введите часть имени..." class="select js_select" name='responsibleIds[]'>
                        <!-- <select class="select js_select"> -->
                        <!-- <select class="select js_select"> -->
                            <? foreach ($arResult['INSTYPES'] as $instype) { ?>
                                <option value="<?= $instype['ID']?>"><?= $instype['UF_NAME']?></option>
                            <? } ?>
                        </select><!-- END select -->
                    </div><!-- END input_container -->
                </div><!-- END form_row -->
                <div class="form_row">
                    <label class="big_label">Прикрепить полезные документы</label>
                    <div class="input_container column_25">
                        <div class="logo_upload_container without_img">
                            <div class="logo_upload">
                                <input type="file" />
                                <span class="upload"><span>Договор страхования</span></span>
                            </div><!-- END logo_upload -->
                        </div><!-- END logo_upload_container -->
                    </div><!-- END input_container -->
                    <div class="input_container column_25">
                        <div class="logo_upload_container without_img">
                            <div class="logo_upload">
                                <input type="file" />
                                <span class="upload"><span>Памятка</span></span>
                            </div><!-- END logo_upload -->
                        </div><!-- END logo_upload_container -->
                    </div><!-- END input_container -->
                    <div class="input_container column_25">
                        <div class="logo_upload_container without_img">
                            <div class="logo_upload">
                                <input type="file" />
                                <span class="upload"><span>Прочие документы</span></span>
                            </div><!-- END logo_upload -->
                        </div><!-- END logo_upload_container -->
                    </div><!-- END input_container -->
                    <div class="input_container column_25">
                        <p class="upload_desc">Прикрепите прочие документы в разделе «Полезные документы»</p>
                    </div><!-- END input_container -->
                </div><!-- END form_row -->
                <h3 class="subtitle">Кураторы</h3>
                <h4 class="big_label">Клиент</h4>
                <div class="gray_block">
                    <div class="input_container">
                        <label class="big_label"><?=$arResult['COMPANY']['NAME']?></label>
                    </div><!-- END input_container -->
                    <a href="#" class="link ico_add"><span>Добавить куратора</span></a>
                </div><!-- END gray_block -->
                <div class="company_card_container">
                    <div class="company_card">
                        <span class="delete"></span>
                        <ul class="company_card_list">
                            <li class="js_open_dropdown with_dropdown">
                                <span>ФИО</span>
                                <p>Петровольский С.А.</p>
                                <div class="company_card_dropdown">
                                    <a href="#">Петровольский С.А.</a>
                                    <a href="#">Петровольский С.А.</a>
                                    <a href="#">Петровольский С.А.</a>
                                </div><!-- END company_card_dropdown -->
                            </li>
                            <li>
                                <span>Должность</span>
                                <p class="gray">Укажите должность</p>
                            </li>
                            <li>
                                <span>email</span>
                                <p>petrovod87@mail.ru</p>
                            </li>
                            <li>
                                <span>Моб.телефон</span>
                                <p>+7 952 118 3688</p>
                            </li>
                            <li>
                                <span>Раб. телефон</span>
                                <p>+7 495 118 3688</p>
                            </li>
                            <li>
                                <label class="leader js_checkbox active"><input type="checkbox" checked />Назначен лидером</label>
                            </li>
                        </ul><!-- END company_card_list -->
                    </div><!-- END company_card -->
                </div><!-- END company_card_container -->
                <h4 class="big_label">Страховой брокер</h4>
                <div class="gray_block">
                    <div class="input_container">
                        <label class="big_label"><?=$arResult['BROKER']?></label>
                    </div><!-- END input_container -->
                    <a href="#" class="link ico_add"><span>Добавить куратора</span></a>
                </div><!-- END gray_block -->
                <div class="company_card_container">
                    <div class="company_card">
                        <span class="delete"></span>
                        <ul class="company_card_list">
                            <li>
                                <span>ФИО</span>
                                <p>Петровольский С.А.</p>
                            </li>
                            <li>
                                <span>Должность</span>
                                <p class="gray">Укажите должность</p>
                            </li>
                            <li>
                                <span>email</span>
                                <p>petrovod87@mail.ru</p>
                            </li>
                            <li>
                                <span>Моб.телефон</span>
                                <p>+7 952 118 3688</p>
                            </li>
                            <li>
                                <span>Раб. телефон</span>
                                <p>+7 495 118 3688</p>
                            </li>
                            <li>
                                <label class="leader js_checkbox active"><input type="checkbox" checked />Назначен лидером</label>
                            </li>
                        </ul><!-- END company_card_list -->
                    </div><!-- END company_card -->
                </div><!-- END company_card_container -->
                <h4 class="big_label">Страховая компания</h4>
                <div class="form_row">
                    <!--<select data-placeholder="Введите часть имени..." class="chosen-select" name='responsibleIds[]'>
                        <option value="Bubnova">Bubnova</option>
                        <option value="Gorodetsky">Gorodetsky</option>
                        <option value="Gorsky">Gorsky</option>
                    </select> -->
                    <div class="input_container without_small">
                        <input id="search_ins" type="text" class="text_input" placeholder="Название страховой компании" />
                        <input id="sel_ins" type="hidden" />
                    </div><!-- END input_container -->
                    <label class="flag js_checkbox"><input type="checkbox"></label>
                    <a href="#" class="link ico_add"><span>Добавить страховую компанию</span></a>
                </div><!-- END form_row -->
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
                <input type="submit" class="btn" value="Добавить договор" />
            </form><!-- END form_edit_profile -->
        </div><!-- END popup -->
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
