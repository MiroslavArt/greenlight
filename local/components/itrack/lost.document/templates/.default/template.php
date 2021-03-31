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
            <h2 class="block_title"><?=$arResult['DOCUMENT']['NAME']?></h2>
            <div class="card_status_container">
                <span class="card_status <?=$arResult['DOCUMENT']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR']?>">
                    <?=$arResult['DOCUMENT']['PROPERTIES']['STATUS']['VALUE']['UF_NAME']?>
                </span>
                <span class="type_page">от <?=$arResult['DOCUMENT']['PROPERTIES']['STATUS_DATE']['VALUE']?></span>
            </div><!-- END card_status_container -->
        </div><!-- END title_block -->
        <?php if($arResult['DOCUMENT']['PROPERTIES']['GET_ORIGINAL']['VALUE'] == 'Y') : ?>
        <div class="title_right_block">
            <div class="docs_container desc_left">
                <ul class="list_dosc">
                    <li>
                        <p>Оригинал предоставлен</p>
                    </li>
                </ul><!-- END list_docs -->
                <div class="docs_container_bottom">
                    <span class="desc_title">от 01.07.2017</span>
                </div><!-- END docs_container_bottom -->
            </div><!-- END docs_container -->
        </div><!-- END title_right_block -->
        <?php endif;?>
    </div><!-- END title_container -->
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
                <p><a href="#" class="link"><span>Запрос</span></a></p>
            </li>
        </ul><!-- END doc_info -->
        <a href="#add_contract" class="btn" data-fancybox>Акцептовать</a>
        <a href="#all_sk" class="btn" data-fancybox>Отклонить</a>
        <a href="<?=$arParams['PATH_TO']['lost-document-history']?>" class="btn">Все статусы <br />документа</a>
        <a href="#add_doc2" class="btn" data-fancybox>Добавить</a>
    </div><!-- END desc_container -->
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
