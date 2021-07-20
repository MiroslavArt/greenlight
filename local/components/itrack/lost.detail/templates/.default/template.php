<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");
//\Itrack\Custom\Helpers\Utils::varDump($arParams);
?>
<div class="wrapper">
    <?php if($arParams['CURATORS_MODE']) : ?>
        <a href="<?= $arParams['PATH_TO']['lost'] ?>" class="back"><?=GetMessage("LOST_CARD")?></a>
    <?php elseif ( ( empty($arParams['CLIENT_ID']) || empty($arParams['CONTRACT_ID'])) && empty($arParams['CURATORS_MODE']) ) :?>
        <a href="<?= $arParams['LIST_URL'] ?>" class="back">К списку убытков</a>
    <?php else :?>
        <a href="<?= $arResult['CONTRACT_PAGE_URL'] ?>" class="back">Договор страхования</a>
    <?php endif;?>
    <div class="title_container">
        <div class="title_block">
            <span class="type_page"><?= GetMessage('ALL_LOST_CURATORS') ?></span>
            <h2 class="block_title"><?= $arResult['LOST']['NAME'] ?></h2>
            <div class="card_status_container">
                <span class="card_status <?= $arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR'] ?>">Статус убытка: <?= (!empty($arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_NAME']) ? $arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_NAME'] : ' не установлен') ?></span>
                <span class="type_page">от <?= $arResult['LOST']['DATE_ACTIVE_FROM']// (new  \DateTime($arResult['TIMESTAMP_X']))->format('d.m.Y') ?></span>

            </div><!-- END card_status_container -->

        </div><!-- END title_block -->
        <div class="title_right_block">
            <? if($arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR']=='yellow' && $arResult["CAN_CLOSE_LOSS"]) { ?>
                 <a href="#change_status" data-fancybox class="ico_remarks link">Закрыть убыток</a>
            <? } ?>
            <? if(!$arResult['BAN_DOC']) {
                if($arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR']=='red' || $arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR']=='yellow') { ?>
                    <a href="#add_doc2" data-fancybox class="btn">Добавить документ</a>
                <?
                }
            } ?>
            <? if($arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR']=='red') { ?>
                <a href="#edit_loss" data-fancybox class="btn">Редактировать <br>убыток</a>
            <? } ?>
        </div><!-- END title_right_block -->
    </div><!-- END title_container -->
    <div class="popup all_statuses" id="change_status">
		<form class="form_popup form1">
			<div class="form_row">
				<div class="input_container column_50 decision_container">
				    <input type="hidden" name="lostid" id="lostid" value="<?=$arResult['LOST']['ID']?>"/>
					<span class="decision_title"><em class="decision">Признано страховым случаем</em></span>
					<div class="small_select">
						<select class="select js_select"  name="decision">
							<option value="Y">Да</option>
							<option value="N">Нет</option>
						</select><!-- END select -->
					</div><!-- END small_select -->
				</div><!-- END input_container -->
				<div class="input_container column_50">
					<input type="text" class="text_input" placeholder="С суммой компенсации" name="sum" />
				</div><!-- END input_container -->
			</div><!-- END form_row -->
			<div class="form_row">
			    <p class="link" id="mistake2"></p>
				<!--<div class="input_container column_50">
					<a href="#" class="btn">Отменить</a>
				</div>---><!-- END input_container -->
				<div class="input_container column_50">
					<input type="submit" class="btn" value="Закрыть убыток">
				</div><!-- END input_container -->
			</div><!-- END form_row -->
		</form><!-- END form_popup -->
	</div><!-- END popup -->
    <div class="popup add_doc2" id="add_doc2">
        <h3 class="block_title">Добавление документа</h3>
        <form class="form_popup form2">
            <div class="form_row">
                <div class="input_container column_100">
                    <input type="hidden" name="origin" value="<?=$arResult['CONTRACT']['PROPERTIES']['ORIGIN_REQUIRED']['VALUE']?>"/>
                    <input type="hidden" name="lostid" value="<?=$arResult['LOST']['ID']?>"/>
                    <input type="hidden" name="status" value="<?=$arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR']?>"/>
                    <input type="text" class="text_input" name="docname" placeholder="Название документа" />
                </div><!-- END input_container -->
            </div><!-- END form_row -->
            <!-- <div class="form_row">
                <div class="input_container column_50">
                    <span>Дата запроса</span>
                    <input type="text" class="text_input ico_date js_datapicker" name="docdate" placeholder="Дата запроса" />
                </div>
            </div>-->
            <div class="form_row margin">
                <div class="input_container column_50">
                    <span>Срок предоставления</span>
                    <input type="text" class="text_input ico_date js_datapicker" id="term_date" name="docterm" placeholder="Срок предоставления" />
                </div><!-- END input_container -->
                <div class="input_container column_50">
                    <span>Автор запроса</span>
                    <select class="select js_select" id="author" name="author">
                        <? foreach ($arResult['CURATORS'] as $user) { ?>
                            <option value="<?= $user['ID']?>"><?= $user['NAME'].' '.$user['LAST_NAME']?></option>
                        <? } ?>
                    </select><!-- END select -->
                </div><!-- END input_container -->
            </div><!-- END form_row -->
            <p class="link" id="mistake"></p>
            <input type="submit" class="btn" value="Добавить документ">
        </form><!-- END form_popup -->
    </div><!-- END popup -->
    <div class="popup" id="edit_loss">
        <h3 class="block_title">Редактирование убытка</h3>
        <form class="form_popup form4">
            <div class="form_row">
                <div class="input_container column_25">
                    <span>Номер убытка</span>
                    <input type="text" class="text_input" id="doc_num" value="<?=$arResult['LOST']['NAME']?>" />
                </div><!-- END input_container -->
                <div class="input_container column_25">
                    <span>Дата убытка</span>
                    <input type="text" class="text_input ico_date js_datapicker" id="doc_date" placeholder="Дата убытка" value="<?=$arResult['LOST']['DATE_ACTIVE_FROM']?>"/>
                </div><!-- END input_container -->
                <!-- <a href="#" class="link ico_add"><span>Добавить договор страхования</span></a> -->
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
                <? foreach ($arResult['LOST']['PROPERTIES']['CURATORS'][$arResult['COMPANY']['ID']] as $item) { ?>
                    <div class="company_card">
                        <!-- <span class="delete"></span> -->
                        <ul class="company_card_list">
                            <li>
                                <span>ФИО</span>
                                <p><?= $item['NAME'] ?></p>
                            </li>
                            <li>
                                <span>Должность</span>
                                <p><?= $item['POSITION'] ?></p>
                            </li>
                            <li>
                                <span>email</span>
                                <p><?= $item['EMAIL'] ?></p>
                            </li>
                            <li>
                                <span>Моб.телефон</span>
                                <p><?= $item['MPHONE'] ?></p>
                            </li>
                            <li>
                                <span>Раб. телефон</span>
                                <p><?= $item['PHONE'] ?></p>
                            </li>
                            <li>
                                <input type="hidden" class="inserted_kur_co_id" value="<?= $item['ID'] ?>" />
                                <? if($item['IS_LEADER']=='Y') { ?>
                                    <label class="leader client js_checkbox active"><input type="checkbox" checked />Назначен лидером</label>
                                <? } else { ?>
                                    <label class="leader client js_checkbox"><input type="checkbox" checked />Назначен лидером</label>
                                <? } ?>
                            </li>
                        </ul><!-- END company_card_list -->
                    </div><!-- END company_card -->
                <? } ?>
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
                <? foreach ($arResult['LOST']['PROPERTIES']['CURATORS'][$arResult['BROKER']['ID']] as $item) { ?>
                    <div class="company_card">
                        <!-- <span class="delete"></span> -->
                        <ul class="company_card_list">
                            <li>
                                <span>ФИО</span>
                                <p><?= $item['NAME'] ?></p>
                            </li>
                            <li>
                                <span>Должность</span>
                                <p><?= $item['POSITION'] ?></p>
                            </li>
                            <li>
                                <span>email</span>
                                <p><?= $item['EMAIL'] ?></p>
                            </li>
                            <li>
                                <span>Моб.телефон</span>
                                <p><?= $item['MPHONE'] ?></p>
                            </li>
                            <li>
                                <span>Раб. телефон</span>
                                <p><?= $item['PHONE'] ?></p>
                            </li>
                            <li>
                                <input type="hidden" class="inserted_kur_co_id" value="<?= $item['ID'] ?>" />
                                <? if($item['IS_LEADER']=='Y') { ?>
                                    <label class="leader broker js_checkbox active"><input type="checkbox" checked />Назначен лидером</label>
                                <? } else { ?>
                                    <label class="leader broker js_checkbox"><input type="checkbox" checked />Назначен лидером</label>
                                <? } ?>
                            </li>
                        </ul><!-- END company_card_list -->
                    </div><!-- END company_card -->
                <? } ?>
            </div>
            <h4 class="big_label">Страховая компания</h4>
            <div class="gray_blocks" id="ins_insuers">
                <? foreach ($arResult['INSURANCE_COMPANIES'] as $insco) { ?>
                    <div class="ins_insuer">
                        <div class="gray_block delete_left">
                            <!-- <span class="delete js_delete1"></span> -->
                            <div class="input_container with_flag">
                                <label class="big_label"><?=$insco['NAME']?></label>
                                <input class="inserted_co_id" type="hidden" data-type="ins" value="<?= $insco['ID'] ?>"/>
                                <label class="flag js_checkbox <? if($insco['ID']==$arResult['INSURANCE_COMPANY']['ID']) { ?>active<? } ?>"><input type="checkbox"></label>
                            </div><!-- END input_container -->
                        </div><!-- END gray_block -->
                    </div>
                    <div class="company_card_container ins_kurators">
                        <? foreach ($arResult['LOST']['PROPERTIES']['CURATORS'][$insco['ID']] as $item) { ?>
                            <div class="company_card">
                                <!-- <span class="delete"></span> -->
                                <ul class="company_card_list">
                                    <li>
                                        <span>ФИО</span>
                                        <p><?= $item['NAME'] ?></p>
                                    </li>
                                    <li>
                                        <span>Должность</span>
                                        <p><?= $item['POSITION'] ?></p>
                                    </li>
                                    <li>
                                        <span>email</span>
                                        <p><?= $item['EMAIL'] ?></p>
                                    </li>
                                    <li>
                                        <span>Моб.телефон</span>
                                        <p><?= $item['MPHONE'] ?></p>
                                    </li>
                                    <li>
                                        <span>Раб. телефон</span>
                                        <p><?= $item['PHONE'] ?></p>
                                    </li>
                                    <li>
                                        <input type="hidden" class="inserted_kur_co_id" value="<?= $item['ID'] ?>" />
                                        <? if($item['IS_LEADER']=='Y') { ?>
                                            <label class="leader insco js_checkbox active"><input type="checkbox" checked />Назначен лидером</label>
                                        <? } else { ?>
                                            <label class="leader insco js_checkbox"><input type="checkbox" checked />Назначен лидером</label>
                                        <? } ?>
                                    </li>
                                </ul><!-- END company_card_list -->
                            </div><!-- END company_card -->
                        <? } ?>
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
                <? foreach ($arResult['ADJUSTER_COMPANIES'] as $adjco) { ?>
                    <div class="ins_adjuster" data-id="<?= $adjco['ID'] ?>">
                        <div class="gray_block delete_left">
                            <!-- <span class="delete js_delete1"></span> -->
                            <div class="input_container with_flag">
                                <label class="big_label"><?=$adjco['NAME']?></label>
                                <input class="inserted_co_id" type="hidden" data-type="aj"  value="<?=$adjco['ID'] ?>"/>
                                <label class="flag js_checkbox <? if($adjco['ID']==$arResult['ADJUSTER_COMPANY']['ID']) { ?>active<? } ?>"><input type="checkbox"></label>
                            </div><!-- END input_container -->
                        </div><!-- END gray_block -->
                    </div>
                    <div class="company_card_container">
                        <? foreach ($arResult['LOST']['PROPERTIES']['CURATORS'][$adjco['ID']] as $item) { ?>
                            <div class="company_card">
                                <!-- <span class="delete"></span> -->
                                <ul class="company_card_list">
                                    <li>
                                        <span>ФИО</span>
                                        <p><?= $item['NAME'] ?></p>
                                    </li>
                                    <li>
                                        <span>Должность</span>
                                        <p><?= $item['POSITION'] ?></p>
                                    </li>
                                    <li>
                                        <span>email</span>
                                        <p><?= $item['EMAIL'] ?></p>
                                    </li>
                                    <li>
                                        <span>Моб.телефон</span>
                                        <p><?= $item['MPHONE'] ?></p>
                                    </li>
                                    <li>
                                        <span>Раб. телефон</span>
                                        <p><?= $item['PHONE'] ?></p>
                                    </li>
                                    <li>
                                        <input type="hidden" class="inserted_kur_co_id" value="<?= $item['ID'] ?>" />
                                        <? if($item['IS_LEADER']=='Y') { ?>
                                            <label class="leader adjuster js_checkbox active"><input type="checkbox" checked />Назначен лидером</label>
                                        <? } else { ?>
                                            <label class="leader adjuster js_checkbox"><input type="checkbox" checked />Назначен лидером</label>
                                        <? } ?>
                                    </li>
                                </ul><!-- END company_card_list -->
                            </div><!-- END company_card -->
                        <? } ?>
                    </div>
                <? } ?>
            </div>
            <div class="form_row">
                <div class="switches_container">
                    <label class="big_label">Необходимость акцепта</label>
                    <div class="switch_container">
                        <? if(in_array('CL', $arResult['LOST']['PROPERTIES']['NEED_ACCEPT']['VALUE_XML_ID'])) { ?>
                            <label class="switch js_checkbox active" id="clientaccept"><input type="checkbox"></label>
                        <? } else {?>
                            <label class="switch js_checkbox" id="clientaccept"><input type="checkbox"></label>
                        <? } ?>
                        <span>Клиент</span>
                    </div>
                    <div class="switch_container">
                        <? if(in_array('BR', $arResult['LOST']['PROPERTIES']['NEED_ACCEPT']['VALUE_XML_ID'])) { ?>
                            <label class="switch js_checkbox active" id="brokeraccept"><input type="checkbox"></label>
                        <? } else {?>
                            <label class="switch js_checkbox" id="brokeraccept"><input type="checkbox"></label>
                        <? } ?>
                        <span>Страховой Брокер</span>
                    </div>
                    <div class="switch_container">
                        <? if(in_array('INS', $arResult['LOST']['PROPERTIES']['NEED_ACCEPT']['VALUE_XML_ID'])) { ?>
                            <label class="switch js_checkbox active" id="insaccept"><input type="checkbox"></label>
                        <? } else {?>
                            <label class="switch js_checkbox" id="insaccept"><input type="checkbox"></label>
                        <? } ?>
                        <span>Страховая Компания</span>
                    </div>
                    <div class="switch_container">
                        <? if(in_array('AJ', $arResult['LOST']['PROPERTIES']['NEED_ACCEPT']['VALUE_XML_ID'])) { ?>
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
                        <? if(in_array('CL', $arResult['LOST']['PROPERTIES']['NEED_NOTIFY']['VALUE_XML_ID'])) { ?>
                            <label class="switch js_checkbox active" id="clientnot"><input type="checkbox"></label>
                        <? } else {?>
                            <label class="switch js_checkbox" id="clientnot"><input type="checkbox"></label>
                        <? } ?>
                        <span>Клиент</span>
                    </div>
                    <div class="switch_container">
                        <? if(in_array('BR', $arResult['LOST']['PROPERTIES']['NEED_NOTIFY']['VALUE_XML_ID'])) { ?>
                            <label class="switch js_checkbox active" id="brokernot"><input type="checkbox"></label>
                        <? } else {?>
                            <label class="switch js_checkbox" id="brokernot"><input type="checkbox"></label>
                        <? } ?>
                        <span>Страховой Брокер</span>
                    </div>
                    <div class="switch_container">
                        <? if(in_array('INS', $arResult['LOST']['PROPERTIES']['NEED_NOTIFY']['VALUE_XML_ID'])) { ?>
                            <label class="switch js_checkbox active" id="insnot"><input type="checkbox"></label>
                        <? } else {?>
                            <label class="switch js_checkbox" id="insnot"><input type="checkbox"></label>
                        <? } ?>
                        <span>Страховая Компания</span>
                    </div>
                    <div class="switch_container">
                        <? if(in_array('AJ', $arResult['LOST']['PROPERTIES']['NEED_NOTIFY']['VALUE_XML_ID'])) { ?>
                            <label class="switch js_checkbox active" id="adjnot"><input type="checkbox"></label>
                        <? } else {?>
                            <label class="switch js_checkbox" id="adjnot"><input type="checkbox"></label>
                        <? } ?>
                        <span>Аджастер</span>
                    </div>
                </div>
            </div>
            <p class="link" id="mistaketext3"></p>
            <div><ul class="link" id="mistake3">
            </ul></div>
            <input type="submit" class="btn" value="Отредактировать" />
        </form><!-- END form_edit_profile -->
    </div><!-- END popup -->


    <?php if($arParams['CURATORS_MODE']) : ?>
        <?php include 'includes/curators.php'; ?>
    <?php else :?>
        <?php include 'includes/default.php'; ?>
    <?php endif;?>
</div><!-- END wrapper -->

