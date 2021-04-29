<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");
//\Itrack\Custom\Helpers\Utils::varDump($arParams);
?>


<div class="wrapper">
    <a href="<?=$arParams['PATH_TO']['lost']?>" class="back">К убытку</a>
    <div class="title_container">
        <div class="title_block table_big">
            <span class="type_page">Карточка документа</span>
            <h2 class="block_title"
                data-status="<?=$arResult['DOCUMENT']['PROPERTIES']['STATUS']['VALUE']['ID']?>"
                data-user="<?=$arResult['CURUSER']?>"
                data-lost="<?=$arResult['DOCUMENT']['PROPERTY_23']?>"
            >
                <?=$arResult['DOCUMENT']['NAME']?>
            </h2>
            <div class="card_status_container">
                <span class="card_status <?=$arResult['DOCUMENT']['PROPERTIES']['STATUS']['VALUE'][$arResult['COLOR_FIELD']]?>">
                    <?=$arResult['DOCUMENT']['PROPERTIES']['STATUS']['VALUE']['UF_NAME']?>
                </span>
                <span class="type_page">от <?=$arResult['DOCUMENT']['PROPERTIES']['STATUS_DATE']['VALUE']?>
                <?php  if($arResult['DECLINESTATUS']) { ?>(ранее <?=$arResult['DECLINESTATUS']?>)<? } ?></span>
            </div><!-- END card_status_container -->
        </div><!-- END title_block -->
        <?php if($arResult['SHOWORIGINAL']) : ?>
        <div class="title_right_block">
            <div class="docs_container desc_left">
                <?php if($arResult['ORIGINALSTATUSSET']) { ?>
                    <a href="#change_status" data-fancybox class="ico_settings link"></a>
                <? } ?>
                <!--<ul class="list_dosc">
                    <li> -->
                        <?php if($arResult['ORIGINALGOT']) { ?>
                            <p>Оригинал предоставлен</p>
                        <? } else { ?>
                            <p>Оригинал не предоставлен!</p>
                        <? } ?>
                   <!-- </li>
                </ul>--><!-- END list_docs -->
                <div class="docs_container_bottom">
                    <?php if($arResult['ORIGINALGOT']) { ?>
                        <span class="desc_title">от <?=$arResult['DOCUMENT']['PROPERTY_69']?></span>
                    <? } ?>
                </div><!-- END docs_container_bottom -->
            </div><!-- END docs_container -->
        </div><!-- END title_right_block -->
        <?php endif;?>
    </div><!-- END title_container -->
    <div class="popup change_status" id="change_status">
        <h2 class="block_title">Зафиксировать предоставление оригинала</h2>
        <form class="status_form form_popup4">
            <div class="fieldset">
                <!-- <div class="input_container area_b"> -->
                <input type="text" class="text_input ico_date js_datapicker" id="origdate" placeholder="Дата предоставления" />
                <!-- </div> -->
            </div>
            <p class="link" id="mistakeorig"></p>
            <input type="submit" class="btn" value="Сохранить" />
        </form><!-- END status_form -->
    </div><!-- END popup -->
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
                <p><a href="#add_doc" data-fancybox class="link"><span>Запрос</span></a></p>
            </li>
        </ul><!-- END doc_info -->
        <? if($arResult['SHOWADD']) { ?><a href="#add_doc2" class="btn" data-fancybox>Добавить</a> <? } ?>
        <?
            if($arResult['SHOWACCEPT']) {
        ?>
            <a href="#" class="btn" id="accept" data-orig="<?=$arResult['SHOWORIGINAL']?>">Акцептовать</a>
        <? } ?>
        <?
            if($arResult['SHOWDECLINE']) {
        ?>
            <a href="#decline_comment" class="btn" data-fancybox>Отклонить</a>
        <? } ?>
        <a href="<?=$arParams['PATH_TO']['lost-document-history']?>" class="btn">Все статусы <br />документа</a>

    </div><!-- END desc_container -->
    <div class="popup add_doc" id="add_doc">
        <?php
        $APPLICATION->IncludeComponent(
            'itrack:lost.request.docs',
            '',
            array(
                "LOST_DOC" => $arResult['DOCUMENT']['ID'],
                "TYPE" => 1
            ),
            null
        );
        ?>
    </div>
    <div class="popup add_comment" id="decline_comment">
        <h3 class="block_title">Отклонить с коммментарием</h3>
        <form class="form_popup3">
            <div class="form_row">
                <div class="input_container column_100">
                    <textarea class="textarea" placeholder="Комментарий при отклонении"></textarea>
                </div><!-- END input_container -->
            </div><!-- END form_row -->
            <input type="submit" class="btn" value="Отклонить">
        </form><!-- END form_popup -->
    </div><!-- END popup -->
    <div class="popup add_doc2" id="add_doc2">
        <h3 class="block_title">Добавление файла к документу убытка</h3>
        <form class="form_popup">
            <div class="form_row upload_btn_container">
                <div class="input_container">
                    <input type="hidden" id="lost_id" value="<?=$arResult['DOCUMENT']['ID'] ?>"/>
                    <input type="text" class="text_input" id="doc_name" placeholder="Название документа" />
                </div><!-- END input_container -->
                <div class="input_container with_text">
                    <div class="logo_upload">
                        <input type="file" id="loss_file">
                        <span class="upload">
						<span>Файл</span>
						<span class="upload_btn_text">Загрузите <br>пожалуйста файл</span>
					</span><!-- END upload -->
                    </div><!-- END logo_upload -->
                </div><!-- END input_container -->
            </div><!-- END form_row -->
            <div class="form_row">
                <div class="input_container column_50">
                    <input type="text" class="text_input ico_date js_datapicker" id="doc_date" placeholder="Дата предоставления" />
                </div><!-- END input_container -->
            </div><!-- END form_row -->
            <div class="form_row">
                <div class="input_container column_100">
                    <textarea class="textarea" id="comment" placeholder="Комментарий"></textarea>
                </div><!-- END input_container -->
            </div><!-- END form_row -->
            <p class="link" id="mistake"></p>
            <input type="submit" class="btn" value="Добавить документ">
        </form><!-- END form_popup -->
    </div><!-- END popup -->
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
                <div class="table_block align_left align_top" data-name="Открепить" data-id="<?=$arDocument['ID']?>"><a href="#" class="delete js_deletefile"></a></div>
                <div class="table_block align_left align_top item8" data-name="Название файла"><?=(!empty($arDocument['FILE']['SRC'])) ? '<a href="' . $arDocument['FILE']['SRC'] . '" download>' . $arDocument['UF_NAME'] . '</a>' : $arDocument['UF_NAME']?></div>
                <div class="table_block align_left align_top item3" data-name="Срок предоставления">
                    <?=$arResult['DOCUMENT']['PROPERTIES']['REQUEST_DEADLINE']['VALUE']?>
                </div>
                <div class="table_block align_left align_top item3" data-name="Дата предоставления"><?=$arDocument['UF_DATE_CREATED']->format('d.m.Y')?></div>
                <div class="table_block align_left align_top mob_hihe"><a href="#edit_comment<?=$arDocument['ID']?>" data-fancybox class="remarks"></a></div>
                <div class="popup add_comment" id="edit_comment<?=$arDocument['ID']?>">
                    <h3 class="block_title">Исправить комментарий</h3>
                    <form class="form_popup2" data-id="<?=$arDocument['ID']?>">
                        <div class="form_row">
                            <div class="input_container column_100">
                                <textarea class="textarea" placeholder="Комментарий"><?=$arDocument['UF_COMMENT']?></textarea>
                            </div><!-- END input_container -->
                        </div><!-- END form_row -->
                        <input type="submit" class="btn" value="Исправить">
                    </form><!-- END form_popup -->
                </div><!-- END popup -->
                <div class="table_block align_left align_top item6" data-name="Комментарий"><a href="#" class="remarks mob"></a><p><?=$arDocument['UF_COMMENT']?></p></div>
            </li>
        <?php endforeach; ?>
    </ul><!-- END data_table -->
    <?php endif;?>
</div><!-- END wrapper -->
