<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

\Bitrix\Main\UI\Extension::load("ui.alerts");

?>

<h2 class="block_title">Запроc</h2>
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
        <span class="delete"></span>
    </li>
<?php } ?>
<form class="upload_doc_container">
    <div class="input_container">
        <input type="text" class="text_input" placeholder="Описание / название документа" />
    </div><!-- END input_container -->
    <div class="logo_upload">
        <input type="file">
        <span class="upload">
            <span>Файл</span>
            <span class="upload_btn_text">Загрузите <br />пожалуйста файл</span>
        </span><!-- END upload -->
    </div><!-- END logo_upload -->
    <button type="submit" class="btn">Добавить <br />документ</button>
</form><!-- END upload_doc_container -->