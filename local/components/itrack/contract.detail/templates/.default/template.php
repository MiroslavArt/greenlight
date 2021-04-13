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
                <p class="contract_number" data-id="<?=$arResult['CONTRACT']['ID']?>">№ договора<span><?=$arResult['CONTRACT']['NAME']?></span></p>
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
                        <input type="text" class="text_input" id="doc_num" placeholder="Номер убытка" />
                    </div><!-- END input_container -->
                    <div class="input_container column_25">
                        <input type="text" class="text_input ico_date js_datapicker" id="doc_date" placeholder="Дата убытка" />
                    </div><!-- END input_container -->
                    <!-- <a href="#" class="link ico_add"><span>Добавить договор страхования</span></a> -->
                </div><!-- END form_row -->
                <div class="form_row">
                    <div class="input_container column_100">
                        <textarea class="textarea" id="loss_descr" placeholder="Описание убытка"></textarea>
                    </div><!-- END input_container -->
                    <div class="input_container column_50">
                        <a href="#" class="btn white" id="kurtransfer">Перенести кураторов из договора страхования</a>
                    </div><!-- END input_container -->
                </div><!-- END form_row -->
                <h3 class="subtitle">Кураторы</h3>
                <h4 class="big_label">Клиент</h4>
                <div class="gray_block">
                    <div class="input_container">
                        <label class="big_label"><?=$arResult['COMPANY']['NAME']?></label>
                    </div><!-- END input_container -->
                    <!-- <a href="#" class="link ico_add"><span>Добавить куратора</span></a> -->
                </div><!-- END gray_block -->
                <a href="#" class="link ico_add js_add"><span>Добавить куратора</span></a>
                <div class="form_row client_comp hidden">
                    <div class="input_container without_small">
                        <input id="kur_client_search_ins" data-id="<?=$arResult['COMPANY']['ID']?>" type="text" class="text_input inserted_co_label" placeholder="Выберите куратора от клиента по вводу букв из ФИО" />
                    </div>
                </div><!-- END input_container -->
                <div id="ins_kur_card" class="company_card_container">
                </div>
                <h4 class="big_label">Страховой брокер</h4>
                <div class="gray_block">
                    <div class="input_container">
                        <label class="big_label"><?=$arResult['BROKER']['NAME']?></label>
                    </div><!-- END input_container -->
                    <!-- <a href="#" class="link ico_add"><span>Добавить куратора</span></a> -->
                </div><!-- END gray_block -->
                <a href="#" class="link ico_add js_add"><span>Добавить куратора</span></a>
                <div class="form_row brok_comp hidden">
                    <div class="input_container without_small">
                        <input id="kur_broker_search_ins" data-id="<?=$arResult['BROKER']['ID']?>" type="text" class="text_input inserted_co_label" placeholder="Выберите куратора от страхового брокера по вводу букв из ФИО" />
                    </div>
                </div>
                <div id="brok_kur_card" class="company_card_container">
                </div>
                <h4 class="big_label">Страховая компания</h4>
                <div class="gray_blocks" id="ins_insuers">
                    <? foreach ($arResult['INSURANCE_COMPANIES'] as $insco) { ?>
                        <div class="ins_insuer">
                            <div class="gray_block delete_left">
                                <!-- <span class="delete js_delete1"></span> -->
                                <div class="input_container with_flag">
                                    <label class="big_label"><?=$insco['NAME']?></label>
                                    <input class="inserted_co_id" type="hidden" value="<?=$insco['ID'] ?>"/>
                                    <label class="flag js_checkbox <? if($insco['ID']==$arResult['INSURANCE_COMPANY']['ID']) { ?>active<? } ?>"><input type="checkbox"></label>
                                </div><!-- END input_container -->
                            </div><!-- END gray_block -->
                        </div>
                    <? } ?>
                </div>
                <h4 class="big_label">Аджастер</h4>
                <a href="#" class="link ico_add js_add"><span>Добавить аджастера</span></a>
                <div class="form_row ins_comp hidden">
                    <div class="input_container without_small">
                        <input type="text" class="text_input inserted_co_label" id="search_adj" placeholder="Выберите аджастера по вводу букв из названия" />
                    </div><!-- END input_container -->
                </div> <!-- END form_row -->
                <div class="gray_blocks" id="ins_adjusters">
                </div>
                <div class="form_row">
                    <div class="switches_container">
                        <label class="big_label">Необходимость акцепта</label>
                        <div class="switch_container">
                            <? if($arResult['CONTRACT']['PROPERTY_71'][17]) { ?>
                                <label class="switch js_checkbox active" id="clientaccept"><input type="checkbox"></label>
                            <? } else {?>
                                <label class="switch js_checkbox" id="clientaccept"><input type="checkbox"></label>
                            <? } ?>
                            <span>Клиент</span>
                        </div>
                        <div class="switch_container">
                            <? if($arResult['CONTRACT']['PROPERTY_71'][18]) { ?>
                                <label class="switch js_checkbox active" id="brokeraccept"><input type="checkbox"></label>
                            <? } else {?>
                                <label class="switch js_checkbox" id="brokeraccept"><input type="checkbox"></label>
                            <? } ?>
                            <span>Страховой Брокер</span>
                        </div>
                        <div class="switch_container">
                            <? if($arResult['CONTRACT']['PROPERTY_71'][19]) { ?>
                                <label class="switch js_checkbox active" id="insaccept"><input type="checkbox"></label>
                            <? } else {?>
                                <label class="switch js_checkbox" id="insaccept"><input type="checkbox"></label>
                            <? } ?>
                            <span>Страховая Компания</span>
                        </div>
                        <div class="switch_container">
                            <? if($arResult['CONTRACT']['PROPERTY_71'][20]) { ?>
                                <label class="switch js_checkbox active" id="adjaccept"><input type="checkbox"></label>
                            <? } else {?>
                                <label class="switch js_checkbox" id="adjaccept"><input type="checkbox"></label>
                            <? } ?>
                            <span>Аджастер</span>
                        </div>
                    </div>
                    <div class="switches_container">
                        <label class="big_label">Уведомления</label>
                        <div class="switch_container">
                            <? if($arResult['CONTRACT']['PROPERTY_72'][21]) { ?>
                                <label class="switch js_checkbox active" id="clientnot"><input type="checkbox"></label>
                            <? } else {?>
                                <label class="switch js_checkbox" id="clientnot"><input type="checkbox"></label>
                            <? } ?>
                            <span>Клиент</span>
                        </div>
                        <div class="switch_container">
                            <? if($arResult['CONTRACT']['PROPERTY_72'][22]) { ?>
                                <label class="switch js_checkbox active" id="brokernot"><input type="checkbox"></label>
                            <? } else {?>
                                <label class="switch js_checkbox" id="brokernot"><input type="checkbox"></label>
                            <? } ?>
                            <span>Страховой Брокер</span>
                        </div>
                        <div class="switch_container">
                            <? if($arResult['CONTRACT']['PROPERTY_72'][23]) { ?>
                                <label class="switch js_checkbox active" id="insnot"><input type="checkbox"></label>
                            <? } else {?>
                                <label class="switch js_checkbox" id="insnot"><input type="checkbox"></label>
                            <? } ?>
                            <span>Страховая Компания</span>
                        </div>
                        <div class="switch_container">
                            <? if($arResult['CONTRACT']['PROPERTY_72'][24]) { ?>
                                <label class="switch js_checkbox active" id="adjnot"><input type="checkbox"></label>
                            <? } else {?>
                                <label class="switch js_checkbox" id="adjnot"><input type="checkbox"></label>
                            <? } ?>
                            <span>Аджастер</span>
                        </div>
                    </div>
                </div>
                <!--<div class="form_row">
                    <div class="input_container column_100">
                        <input type="text" class="text_input" id="req_doc" placeholder="Запрашиваемые документы" />
                    </div>--><!-- END input_container -->
                <!--</div>--><!-- END form_row -->
                <!--<div class="form_row">
                    <div class="input_container column_25">
                        <input type="text" class="text_input ico_date js_datapicker" id="req_date" placeholder="Дата запроса" />
                    </div>--><!-- END input_container -->
                    <!--<div class="input_container">
                        <select class="select js_select" id="users"> --?
                            <? //foreach ($arResult['USERS'] as $user) { ?>
                                <option value=""></option>
                            <? //} ?>
                        </select>-->
                        <!-- <input type="text" class="text_input" placeholder="Название компании" /> -->
                    <!--</div>--><!-- END input_container -->
                    <!-- <div class="input_container column_50">
                        <input type="text" class="text_input" placeholder="Автор запроса" />
                    </div>--><!-- END input_container -->
                    <!--<div class="input_container column_25">
                        <input type="text" class="text_input ico_date js_datapicker" id="req_term" placeholder="Срок предоставления" />
                    </div>--><!-- END input_container -->
                <!--</div>--><!-- END form_row -->
                <!--<div class="form_row">-->
                    <!--<label class="big_label">Прикрепить полезные документы</label> -->
                    <!--<div class="input_container column_3">
                        <div class="logo_upload_container without_img">
                            <div class="logo_upload">
                                <input class="loss_file" type="file" />
                                <span class="upload"><span>Прикрепить документы к запросу</span></span>
                            </div>--><!-- END logo_upload -->
                        <!--</div>--><!-- END logo_upload_container -->
                    <!--</div>--><!-- END input_container -->
                <!--</div>--><!-- END form_row -->
                <!--<div class="form_row attached_container">
                    <label class="big_label">Документы</label>
                    <ul class="docs_list">
                    </ul>--><!-- END docs_list -->
                <!--</div>--><!-- END form_row -->
                <p class="link" id="mistake"></p>
                <input type="submit" class="btn" value="Добавить убыток" />
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
                            <div class="table_block align_left item2" data-name="Статус"><span class="status <?=$arItem['STATUS']['UF_COLOR']?>"></span></div>
                            <div class="table_block align_left item3" data-name="Статус развернуто"><p><?=$arItem['STATUS']['UF_NAME']?></p></div>
                            <div class="table_block align_left item3" data-name="Уникальный номер"><p><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></p></div>
                            <div class="table_block align_left item2" data-name="Дата"><?=$arItem['DATE']?></div>
                            <div class="table_block align_left item2" data-name="Описание"><?=$arItem['DESCRIPTION']?></div>
                            <div class="table_block links_column item9" data-name="Ссылки">
                                <a href="#" class="link"><span>Список <br>Аджастеров</span></a>
                                <a href="/html/docs.html" class="link"><span>Список <br>документов</span></a>
                                <a href="#corresp<?= $arItem['ID'] ?>" data-fancybox class="link"><span>Корреспон<wbr>денция</span></a>
                                <a href="#report<?= $arItem['ID'] ?>" data-fancybox class="link"><span>Отчеты</span></a>
                            </div><!-- END links_column -->
                        </li>
                        <div class="popup add_doc correspondence" id="corresp<?= $arItem['ID'] ?>">
                            <?php
                            $APPLICATION->IncludeComponent(
                                'itrack:lost.request.docs',
                                'repcorr',
                                array(
                                    "LOST_DOC" => $arItem['ID'],
                                    "TYPE" => 3
                                ),
                                null
                            );
                            ?>
                        </div>
                        <div class="popup add_doc correspondence" id="report<?= $arItem['ID'] ?>">
                            <?php
                            $APPLICATION->IncludeComponent(
                                'itrack:lost.request.docs',
                                'repcorr',
                                array(
                                    "LOST_DOC" => $arItem['ID'],
                                    "TYPE" => 4
                                ),
                                null
                            );
                            ?>
                        </div>
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