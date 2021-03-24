<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");
//\Itrack\Custom\Helpers\Utils::varDump($arResult['REQUESTS']);
?>


<div class="wrapper">
    <a href="<?=$arParams['PATH_TO']['lost']?>" class="back">К убытку</a>
    <div class="title_container">
        <div class="title_block table_big">
            <span class="type_page">Карточка документа</span>
            <h2 class="block_title">Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</h2>
            <div class="card_status_container">
                <span class="card_status red">Открытый статус</span>
                <span class="type_page">от 01.07.2017</span>
            </div><!-- END card_status_container -->
        </div><!-- END title_block -->
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
    </div><!-- END title_container -->
    <div class="desc_container">
        <ul class="doc_info">
            <li>
                <span>Срок предоставления</span>
                <p>13.08.2020</p>
            </li>
            <li>
                <span>Автор</span>
                <p>Григорьев М.А.</p>
            </li>
            <li>
                <span>Ссылка на запрос</span>
                <p><a href="#" class="link"><span>Запрос</span></a></p>
            </li>
        </ul><!-- END doc_info -->
        <a href="#add_contract" class="btn" data-fancybox>Акцептовать</a>
        <a href="#all_sk" class="btn" data-fancybox>Отклонить</a>
        <a href="#add_loss" class="btn" data-fancybox>Все статусы <br />документа</a>
        <a href="#add_user" class="btn" data-fancybox>Добавить</a>
    </div><!-- END desc_container -->
    <ul class="data_table">
        <li class="row table_head">
            <div class="table_block align_left"></div>
            <div class="table_block align_left item8"><p>Название файла</p></div>
            <div class="table_block align_left item3"><p>Срок предоставления</p></div>
            <div class="table_block align_left item3"><p>Дата предоставления</p></div>
            <div class="table_block align_left"></div>
            <div class="table_block align_left item6"><p>Комментарий</p></div>
        </li>
        <li class="row">
            <div class="table_block align_left align_top" data-name="Открепить"><a href="#" class="delete"></a></div>
            <div class="table_block align_left align_top item8" data-name="Название файла">Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</div>
            <div class="table_block align_left align_top item3" data-name="Срок предоставления">25.08.2020</div>
            <div class="table_block align_left align_top item3" data-name="Дата предоставления">25.08.2020</div>
            <div class="table_block align_left align_top mob_hihe"><a href="#" class="remarks"></a></div>
            <div class="table_block align_left align_top item6" data-name="Комментарий"><a href="#" class="remarks mob"></a><p>Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</p></div>
        </li>
        <li class="row">
            <div class="table_block align_left align_top" data-name="Открепить"><a href="#" class="delete"></a></div>
            <div class="table_block align_left align_top item8" data-name="Название файла">Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</div>
            <div class="table_block align_left align_top item3" data-name="Срок предоставления">25.08.2020</div>
            <div class="table_block align_left align_top item3" data-name="Дата предоставления">25.08.2020</div>
            <div class="table_block align_left align_top mob_hihe"><a href="#" class="remarks"></a></div>
            <div class="table_block align_left align_top item6" data-name="Комментарий"><a href="#" class="remarks mob"></a><p>Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</p></div>
        </li>
        <li class="row">
            <div class="table_block align_left align_top" data-name="Открепить"><a href="#" class="delete"></a></div>
            <div class="table_block align_left align_top item8" data-name="Название файла">Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</div>
            <div class="table_block align_left align_top item3" data-name="Срок предоставления">25.08.2020</div>
            <div class="table_block align_left align_top item3" data-name="Дата предоставления">25.08.2020</div>
            <div class="table_block align_left align_top mob_hihe"><a href="#" class="remarks"></a></div>
            <div class="table_block align_left align_top item6" data-name="Комментарий"><a href="#" class="remarks mob"></a><p>Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</p></div>
        </li>
        <li class="row">
            <div class="table_block align_left align_top" data-name="Открепить"><a href="#" class="delete"></a></div>
            <div class="table_block align_left align_top item8" data-name="Название файла">Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</div>
            <div class="table_block align_left align_top item3" data-name="Срок предоставления">25.08.2020</div>
            <div class="table_block align_left align_top item3" data-name="Дата предоставления">25.08.2020</div>
            <div class="table_block align_left align_top mob_hihe"><a href="#" class="remarks"></a></div>
            <div class="table_block align_left align_top item6" data-name="Комментарий"><a href="#" class="remarks mob"></a><p>Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</p></div>
        </li>
        <li class="row">
            <div class="table_block align_left align_top" data-name="Открепить"><a href="#" class="delete"></a></div>
            <div class="table_block align_left align_top item8" data-name="Название файла">Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</div>
            <div class="table_block align_left align_top item3" data-name="Срок предоставления">25.08.2020</div>
            <div class="table_block align_left align_top item3" data-name="Дата предоставления">25.08.2020</div>
            <div class="table_block align_left align_top mob_hihe"><a href="#" class="remarks"></a></div>
            <div class="table_block align_left align_top item6" data-name="Комментарий"><a href="#" class="remarks mob"></a><p>Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</p></div>
        </li>
    </ul><!-- END data_table -->
</div><!-- END wrapper -->
