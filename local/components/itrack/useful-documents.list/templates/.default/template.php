<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

\Bitrix\Main\UI\Extension::load("ui.alerts");

?>

<div class="wrapper">
    <?php if(!empty($arParams['CONTRACT_ID'])) : ?>
        <a href="<?=$arParams['PATH_TO']['contract']?>" class="back">Договор страхования</a>
    <?php else: ?>
        <a href="<?=$arParams['PATH_TO']['detail']?>" class="back">Карточка <?=$arResult['COMPANY']['NAME']?></a>
    <?php endif;?>
    <div class="title_container">
        <div class="title_block">
            <h2 class="block_title"><?=Loc::getMessage('USEFUL_DOCUMENTS')?></h2>
        </div><!-- END title_block -->
    </div><!-- END title_container -->
    <?php if(!empty($arResult['DOCUMENTS'])) :?>
    <div id="documents-list">
        <ul class="data_table">
            <li class="row table_head">
                <div class="table_block align_left"></div>
                <div class="table_block align_left item8"><p>Название документа</p></div>
                <div class="table_block align_left item6"><p>Вид документа</p></div>
                <div class="table_block align_left item6"><p>Дата загрузки</p></div>
            </li>
            <?php foreach ($arResult['DOCUMENTS'] as $arDocument) : ?>
            <li class="row">
                <div class="table_block align_left" data-name="Выбрать"><label class="checkbox js_checkbox"><input
                                type="checkbox"></label></div>
                <div class="table_block align_left item8" data-name="Название документа">
                    <p><?=(!empty($arDocument['PROPERTIES']['FILE']['VALUE']) ? '<a href = "' . \CFile::GetPath($arDocument['PROPERTIES']['FILE']['VALUE']) . '" download>' . $arDocument['NAME'] . '</a>' : $arDocument['NAME'])?></p></div>
                <div class="table_block align_left item6" data-name="Вид документа"><p><?=$arDocument['PROPERTIES']['DOC_TYPE']['VALUE']?></p></div>
                <div class="table_block align_left item6" data-name="Дата загрузки"><p><?=$arDocument['PROPERTIES']['DATE_LOADED']['VALUE']?></p></div>
            </li>
            <?php endforeach; ?>
        </ul><!-- END data_table -->
    </div>
    <?php else: ?>
    <div id="documents-list">
        <div class="ui-alert ui-alert-danger ui-alert-icon-danger has-errors">
            <span class="ui-alert-message">Данные не найдены</span>
        </div>
    </div>
    <?php endif; ?>
    <div class="btn_container">
        <a href="<?/*=$arParams['PATH_TO']['useful-documents-add']*/?>#useful_doc" data-fancybox class="btn ico_plus">Добавить</a>
        <a href="#" class="btn ico_minus">Открепить</a>
    </div><!-- END btn_container -->
</div>

<div class="popup useful_doc" id="useful_doc">
    <h2 class="block_title">Добавить полезный документ</h2>
    <form class="form_popup js-submit-file js-needs-validation" action="<?=$arParams['PATH_TO']['useful-documents-add']?>" data-url="<?=$arParams['PATH_TO']['useful-documents-add']?>" method="post" enctype="multipart/form-data">
        <?php echo bitrix_sessid_post();?>
        <div class="form_row">
            <div class="input_container column_50">
                <input name="doc_name" type="text" class="text_input" placeholder="Описание / название документа" required>
            </div><!-- END input_container -->
            <div class="input_container column_50">
                <select name="doc_type" class="js_select" required>
                    <option>Вид документа</option>
                    <?php foreach ($arResult['DOCUMENT_TYPES'] as $arType) :?>
                    <option value="<?=$arType['ID']?>"><?=$arType['VALUE']?></option>
                    <?php endforeach;?>
                </select>
            </div><!-- END input_container -->
        </div><!-- END form_row -->
        <div class="form_row">
            <div class="input_container with_text column_50">
                <div class="logo_upload">
                    <input name="doc_file" type="file" required>
                    <span class="upload">
						<span>Файл</span>
						<span class="upload_btn_text">Загрузите <br>пожалуйста файл</span>
					</span><!-- END upload -->
                </div><!-- END logo_upload -->
            </div><!-- END input_container -->
        </div><!-- END form_row -->
        <input type="submit" class="btn" value="Добавить документ">
    </form><!-- END form_popup -->
</div><!-- END popup  -->