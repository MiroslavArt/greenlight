<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

\Bitrix\Main\UI\Extension::load("ui.alerts");

?>

<h2 class="block_title">Запроc</h2>
<div class="doc_conti">
    <ul class="doc_list">
        <?php foreach ($arResult['DOCUMENTS'] as $arItemdoc) { ?>
            <li>
                <div class="doc_format">
                    <p class="doc_list_title">Формат документа</p>
                    <span class="name_format"><?= $arItemdoc['FILE']['CONTENT_TYPE'] ?></span>
                </div><!-- END doc_format -->
                <div class="doc_desc">
                    <p class="doc_list_title">Краткое описание</p>
                    <?=(!empty($arItemdoc['FILE']['SRC'])) ? '<a href="' . $arItemdoc['FILE']['SRC'] . '" download>' . $arItemdoc['UF_COMMENT'] . '</a>' : $arItemdoc['UF_COMMENT']?>
                </div><!-- END doc_format -->
                <div class="doc_format">
                    <p class="doc_list_title">Дата запроса</p>
                    <span class="name_format"><?= date("d.m.Y", strtotime($arItemdoc['UF_DATE_CREATED'])) ?></span>
                </div><!-- END doc_format -->
                <div class="doc_format">
                    <p class="doc_list_title">Автор запроса</p>
                    <span class="name_format"><?= $arItemdoc['USER_FIO'] ?></span>
                </div><!-- END doc_format -->
                <? if(!$arResult['BAN_DOC']) { ?>
                    <span class="delete js_deletereqdoc" data-id="<?=$arItemdoc['ID']?>"></span>
                <? } ?>
            </li>
        <?php } ?>
    </ul>
    <? if(!$arResult['BAN_DOC']) { ?>
        <form class="upload_doc_container" data-id="<?=$arParams['LOST_DOC']?>">
            <div class="input_container">
                <input type="text" class="text_input ico_date js_datapicker" placeholder="Дата запроса" />
            </div><!-- END input_container -->
            <div class="input_container">
                <input type="text" class="text_input" placeholder="Описание запроса" />
            </div><!-- END input_container -->
            <div class="logo_upload">
                <input type="file" class="req_file">
                <span class="upload">
                <span>Файл</span>
                <span class="upload_btn_text">Загрузите <br />пожалуйста файл</span>
            </span><!-- END upload -->
            </div><!-- END logo_upload -->
            <p class="link mistakereq"></p>
            <button type="submit" class="btn">Добавить <br />документ</button>
        </form><!-- END upload_doc_container -->
    <? } ?>
</div>
