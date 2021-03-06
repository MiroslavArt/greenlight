<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div id="contracts-wrapper" class="wrapper">
    <a href="<?=$arParams['LIST_URL']?>" class="back">Вернуться к списку клиентов</a>
    <div class="title_container">
        <div class="title_block">
            <span class="type_page">Список договоров</span>
            <h2 class="block_title"><?=$arResult['COMPANY']['NAME']?></h2>
        </div><!-- END title_block -->
        <div class="title_right_block">
            <form class="search_form js-submit js-needs-validation" method="get"
                  data-url="<?= $APPLICATION->GetCurPage() ?>">
                <?= bitrix_sessid_post() ?>
                <input type="hidden" name="is_ajax" value="y">
                <input type="text" name="search" class="search_text" placeholder="Поиск по списку договоров"/>
                <input type="submit" class="search" value=""/>
            </form><!-- END search_form -->
            <? if($arResult['CAN_ADD_CONTRACT']==1) { ?>
                <a href="#add_contract" class="btn" data-fancybox>Добавить договор страхования</a>
            <? } ?>
        </div><!-- END title_right_block -->
    </div><!-- END title_container -->
    <div class="popup" id="add_contract">
        <h3 class="block_title">Добавление договора</h3>
        <form class="form_popup">
            <div class="form_row">
                <div class="input_container column_25">
                    <input type="text" id="docnum" class="text_input" name="docnum" placeholder="Номер договора" />
                </div><!-- END input_container -->
                <div class="input_container column_25">
                    <input type="text" id="docdate" class="text_input ico_date js_datapicker" name="docdata" placeholder="Дата договора" />
                </div><!-- END input_container -->
                <div class="input_container column_50">
                    <select data-placeholder="Введите часть имени..." id="instype" class="select js_select" name='responsibleIds[]'>
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
                            <input name='file1' class="cont_file1" type="file" />
                            <span class="upload"><span>Договор страхования</span></span>
                        </div><!-- END logo_upload -->
                    </div><!-- END logo_upload_container -->
                </div><!-- END input_container -->
                <div class="input_container column_25">
                    <div class="logo_upload_container without_img">
                        <div class="logo_upload">
                            <input name='file2' class="cont_file2" type="file" />
                            <span class="upload"><span>Памятка</span></span>
                        </div><!-- END logo_upload -->
                    </div><!-- END logo_upload_container -->
                </div><!-- END input_container -->
                <div class="input_container column_25">
                    <div class="logo_upload_container without_img">
                        <div class="logo_upload">
                            <input name='file3' class="cont_file3"  type="file" />
                            <span class="upload"><span>Прочие документы</span></span>
                        </div><!-- END logo_upload -->
                    </div><!-- END logo_upload_container -->
                </div><!-- END input_container -->
                <div class="input_container column_25">
                    <p class="upload_desc">Прикрепите прочие документы в разделе «Полезные документы»</p>
                </div><!-- END input_container -->
            </div><!-- END form_row -->
            <div class="form_row attached_container">
                <!--<label class="big_label">Документы</label>-->
                <div class="input_container column_25">
                    <ul class="docs_list" id="contract_files">
                    </ul><!-- END docs_list -->
                </div>
                <div class="input_container column_25">
                    <ul class="docs_list" id="pamyatka_files">
                    </ul><!-- END docs_list -->
                </div>
                <div class="input_container column_25">
                    <ul class="docs_list" id="other_files">
                    </ul><!-- END docs_list -->
                </div>
            </div><!-- END form_row -->
            <h3 class="subtitle">Кураторы</h3>
            <h4 class="big_label">Клиент</h4>
            <a href="#" class="link ico_add js_add"><span>Добавить клиента</span></a>
            <div class="form_row ins_comp hidden">
                <div class="input_container column_100">
                    <input id="search_cl" type="text" class="text_input inserted_co_label" placeholder="Выберите клиента по вводу букв из названия" />
                </div><!-- END input_container -->
            </div> <!-- END form_row -->
            <div class="gray_blocks" id="ins_client">
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
                </div><!-- END input_container -->
            </div>
            <div id="brok_kur_card" class="company_card_container">
            </div>
            <h4 class="big_label">Страховая компания</h4>
            <a href="#" class="link ico_add js_add"><span>Добавить страховую компанию</span></a>
            <div class="form_row ins_comp hidden">
                <div class="input_container column_100">
                    <input id="search_ins" type="text" class="text_input inserted_co_label" placeholder="Выберите страховую компанию по вводу букв из названия" />
                </div><!-- END input_container -->
            </div> <!-- END form_row -->
            <div class="gray_blocks" id="ins_insuers">
            </div>
            <div class="gray_block originals_required">
                <div class="switch_container">
                    <label id="provideoriginal" class="switch js_checkbox active"><input type="checkbox"></label>
                    <span>Предоставлять оригиналы</span>
                </div><!-- END switch_container -->
                <p>Переключите если договор  подразумевает «предоставление оригиналов»</p>
            </div><!-- END originals_required -->
            <div class="form_row">
                <div class="switches_container">
                    <label class="big_label">Необходимость акцепта</label>
                    <div class="switch_container">
                        <label id="clientaccept" class="switch js_checkbox active"><input type="checkbox"></label>
                        <span>Клиент</span>
                    </div>
                    <div class="switch_container">
                        <label id="brokeraccept" class="switch js_checkbox active"><input type="checkbox"></label>
                        <span>Страховой Брокер</span>
                    </div>
                    <div class="switch_container">
                        <label id="insaccept" class="switch js_checkbox active"><input type="checkbox"></label>
                        <span>Страховая Компания</span>
                    </div>
                    <div class="switch_container">
                        <label id="adjaccept" class="switch js_checkbox active"><input type="checkbox"></label>
                        <span>Аджастер</span>
                    </div>
                </div>
                <div class="switches_container">
                    <label class="big_label">Уведомления</label>
                    <div class="switch_container">
                        <label id="clientnot" class="switch js_checkbox active"><input type="checkbox"></label>
                        <span>Клиент</span>
                    </div>
                    <div class="switch_container">
                        <label id="brokernot" class="switch js_checkbox active"><input type="checkbox"></label>
                        <span>Страховой Брокер</span>
                    </div>
                    <div class="switch_container">
                        <label id="insnot" class="switch js_checkbox active"><input type="checkbox"></label>
                        <span>Страховая Компания</span>
                    </div>
                    <div class="switch_container">
                        <label id="adjnot" class="switch js_checkbox active"><input type="checkbox"></label>
                        <span>Аджастер</span>
                    </div>
                </div>
            </div><!-- END form_row -->
            <!-- <div class="btn senddoc">Добавить договор</div> -->
            <p class="link" id="mistake"></p>
            <input type="submit" class="btn" value="Добавить договор" />
        </form><!-- END form_edit_profile -->
    </div><!-- END popup -->
    <span class="type_page">Список договоров страхования</span>
    <br/>
    <?php if (!empty($arResult['ITEMS'])): ?>
        <div id="contracts-list">
            <ul class="data_table">
                <li class="row table_head">
                    <div class="table_block align_left item2"><p>№ договора</p></div>
                    <div class="table_block align_left item2"><p>Дата договора</p></div>
                    <div class="table_block align_left item2"><p>Вид страхования</p></div>
                    <div class="table_block stat_column"><p>Убытки, <br />шт</p></div>
                    <div class="table_block stat_column item2"><p>Закрыто</p></div>
                    <div class="table_block stat_column item2"><p>Документы <br />предоставлены</p></div>
                    <div class="table_block stat_column item2"><p>Открыто</p></div>
                    <div class="table_block item3"><p>СК (Лидер)</p></div>
                    <div class="table_block item4 links_column"><p>Ссылки</p></div>
                </li>
                <?php foreach ($arResult['ITEMS'] as $arItem): ?>
                    <li class="row">
                        <div class="table_block align_left item2" data-name="№ договора"><p><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></p></div>
                        <div class="table_block align_left item2" data-name="Дата договора"><p><?=$arItem['DATE']?></p></div>
                        <div class="table_block align_left item2" data-name="Вид страхования"><p><?=$arItem['TYPE']?></p></div>
                        <div class="table_block stat_column" data-name="Убытки, шт"><?=$arItem["CNT"]["SUM"] ?: 0?></div>
                        <div class="table_block stat_column green item2" data-name="Закрыто"><?=$arItem["CNT"]["green"] ?: 0?></div>
                        <div class="table_block stat_column yellow item2" data-name="Документы предоставлены"><?=$arItem["CNT"]["yellow"] ?: 0?></div>
                        <div class="table_block stat_column red item2" data-name="Открыто"><?=$arItem["CNT"]["red"] ?: 0?></div>
                        <div class="table_block clients_column item3 align_left" data-name="СК (Лидер)">
                            <img src="<?=$arItem["LEADERS"]["insurer"]["LOGO"]?>" width="40" height="40" alt="img1" />
                            <span>
								<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
									<?=$arItem["LEADERS"]["insurer"]["NAME"]?>
								</a>
							</span>
						</div>
                        <div class="table_block item6 links_column" data-name="Ссылки">
                            <a data-fancybox data-type="ajax" href="/ajax/companies_popup.php?target-id=<?=$arItem["ID"]?>&target-type=contract&parties=insurer" class="link ico_doc"><span>Все СК по договору</span></a>
                            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="link ico_doc"><span>Все убытки</span></a>
                        </div><!-- END links_column -->
                    </li>
                <?php endforeach; ?>
            </ul><!-- END data_table -->
            <ul class="data_table no_bg">
				<li class="row">
					<div class="table_block head_column item6" data-name="Клиент"><p>Итого</p></div>
					<div class="table_block stat_column" data-name="Убытки, шт"><?=$arResult["CNT_TOTAL"]["SUM"] ?: 0?></div>
					<div class="table_block stat_column item2"><?=$arResult["CNT_TOTAL"]["green"] ?: 0?></div>
					<div class="table_block stat_column item2"><?=$arResult["CNT_TOTAL"]["yellow"] ?: 0?></div>
					<div class="table_block stat_column item2"><?=$arResult["CNT_TOTAL"]["red"] ?: 0?></div>
					<div class="table_block item9 links_column"></div>
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
