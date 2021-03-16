<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");
\Itrack\Custom\Helpers\Utils::varDump($arResult['LOST']);
?>

<?php if ($arResult['IS_AJAX'] == 'Y') {
    $APPLICATION->RestartBuffer();
    ob_start();
}
?>
    <div class="wrapper">
        <a href="<?=$arResult['CONTRACT_PAGE_URL']?>" class="back">Договор страхования</a>
        <div class="title_container">
            <div class="title_block">
                <span class="type_page"><?=GetMessage('LOST_CARD')?></span>
                <h2 class="block_title"><?=$arResult['LOST']['NAME']?></h2>
                <div class="card_status_container">
                    <span class="card_status red">Открытый статус</span>
                    <span class="type_page">от 01.07.2017</span>
                </div><!-- END card_status_container -->
            </div><!-- END title_block -->
            <div class="title_right_block">
                <a href="#" class="btn">Добавить убыток</a>
            </div><!-- END title_right_block -->
        </div><!-- END title_container -->
        <div class="desc_container">
            <div class="desc">
                <span class="desc_title">Описание страхового случая</span>
                <p>В результате залива подтверждена отделка офиса, который расположен в полуподвальном помещении. Вздулась паркетная доска, отошел плинтус, повреждены обои, дверь в ванную не закрывается.</p>
            </div><!-- END desc -->
            <div class="table_container">
                <table class="table_curators">
                    <tr>
                        <th width="160">Участник</th>
                        <th width="160">ФИО</th>
                        <th width="160">Должность</th>
                        <th width="160">Телефон (моб)</th>
                    </tr>
                    <tr>
                        <td>Клиент <br />MВМ Entertainment</td>
                        <td>Сергеев С.М.</td>
                        <td>Заместитель директора</td>
                        <td>7 962 692 2288</td>
                    </tr>
                    <tr>
                        <td>СБ Лидер <br />Ингосстрах</td>
                        <td>Федоренков Е.Г.</td>
                        <td>Ведущий специалист <br />казначейства</td>
                        <td>7 920 256 8833</td>
                    </tr>
                    <tr>
                        <td>СК Лидер <br />Ингосстрах</td>
                        <td>Михалеченко А.Л.</td>
                        <td>Ведущий специалист</td>
                        <td>7 956 873 1111</td>
                    </tr>
                    <tr>
                        <td>Аджастер Лидер <br />Ингосстрах</td>
                        <td>Федоренков Е.Г.</td>
                        <td>Ведущий специалист <br />казначейства</td>
                        <td>7 920 256 8833</td>
                    </tr>
                </table><!-- END table_curators -->
                <a href="/html/all_curators.html" class="all_curators"><span>Все кураторы по убытку</span></a>
            </div><!-- END table_container -->
            <div class="docs_container">
                <span class="desc_title">Документы по убытку</span>
                <ul class="list_dosc">
                    <li class="red">
                        <p>Запрошенных</p>
                        <span>1 док.</span>
                    </li>
                    <li class="yellow">
                        <p>Переданные в эл./в.</p>
                        <span>2 док.</span>
                    </li>
                    <li>
                        <p>Переданные в печ./в.</p>
                        <span>2 док.</span>
                    </li>
                </ul><!-- END list_docs -->
                <div class="docs_container_bottom">
                    <span class="originals"><span>Предоставление оригиналов</span></span>
                    <span class="desc_title">всего 5 документов</span>
                </div><!-- END docs_container_bottom -->
            </div><!-- END docs_container -->
        </div><!-- END desc_container -->
        <ul class="data_table">
            <li class="row table_head">
                <div class="table_block align_left"><p>Статус</p></div>
                <div class="table_block align_left item2"><p>Детали</p></div>
                <div class="table_block align_left item3"><p>Запрошенные документы</p></div>
                <div class="table_block align_left item2"><p>Дата запроса</p></div>
                <div class="table_block align_left item3"><p>Автор запроса</p></div>
                <div class="table_block align_left item2"><p>Ссылка <br />на запрос</p></div>
                <div class="table_block align_left item2"><p>Информация <br />предоставлена</p></div>
                <div class="table_block align_left item2"><p>Документы</p></div>
                <div class="table_block align_left item2"><p>Информация <br />предоставлена <br />в печатном виде</p></div>
                <div class="table_block align_left item3"><p>Комментарий</p></div>
            </li>
            <li class="row">
                <div class="table_block align_left align_top" data-name="Статус"><span class="status"></span></div>
                <div class="table_block align_left align_top item2" data-name="Детали">Передан в печатном виде</div>
                <div class="table_block align_left align_top item3" data-name="Запрошенные документы">Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</div>
                <div class="table_block align_left align_top item2" data-name="Дата запроса">25.08.2020</div>
                <div class="table_block align_left align_top item3" data-name="Автор запроса">Матрешенкович В.С.</div>
                <div class="table_block align_left align_top item2" data-name="Ссылка на запрос"><a href="#" class="link">Запрос</a></div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена">25.08.2020</div>
                <div class="table_block align_left align_top item2" data-name="Документы"><a href="/html/doc_card.html" class="link">Запрос</a></div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена в печатном виде">25.08.2020</div>
                <div class="table_block align_left align_top item3" data-name="Комментарий"><a href="#" class="link ico_remarks">Замечаний нет</a></div>
            </li>
            <li class="row">
                <div class="table_block align_left align_top" data-name="Статус"><span class="status"></span></div>
                <div class="table_block align_left align_top item2" data-name="Детали">Передан в печатном виде</div>
                <div class="table_block align_left align_top item3" data-name="Запрошенные документы">Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</div>
                <div class="table_block align_left align_top item2" data-name="Дата запроса">25.08.2020</div>
                <div class="table_block align_left align_top item3" data-name="Автор запроса">Матрешенкович В.С.</div>
                <div class="table_block align_left align_top item2" data-name="Ссылка на запрос"><a href="#" class="link">Запрос</a></div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена">25.08.2020</div>
                <div class="table_block align_left align_top item2" data-name="Документы"><a href="/html/doc_card.html" class="link">Запрос</a></div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена в печатном виде">25.08.2020</div>
                <div class="table_block align_left align_top item3" data-name="Комментарий"><a href="#" class="link ico_remarks">Замечаний нет</a></div>
            </li>
            <li class="row">
                <div class="table_block align_left align_top" data-name="Статус"><span class="status yellow"></span></div>
                <div class="table_block align_left align_top item2" data-name="Детали">Передан в печатном виде</div>
                <div class="table_block align_left align_top item3" data-name="Запрошенные документы">Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</div>
                <div class="table_block align_left align_top item2" data-name="Дата запроса">25.08.2020</div>
                <div class="table_block align_left align_top item3" data-name="Автор запроса">Матрешенкович В.С.</div>
                <div class="table_block align_left align_top item2" data-name="Ссылка на запрос"><a href="#" class="link">Запрос</a></div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена">25.08.2020</div>
                <div class="table_block align_left align_top item2" data-name="Документы"><a href="/html/doc_card.html" class="link">Запрос</a></div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена в печатном виде">25.08.2020</div>
                <div class="table_block align_left align_top item3" data-name="Комментарий"><a href="#" class="link ico_remarks">Замечаний нет</a></div>
            </li>
            <li class="row">
                <div class="table_block align_left align_top" data-name="Статус"><span class="status yellow"></span></div>
                <div class="table_block align_left align_top item2" data-name="Детали">Передан в печатном виде</div>
                <div class="table_block align_left align_top item3" data-name="Запрошенные документы">Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</div>
                <div class="table_block align_left align_top item2" data-name="Дата запроса">25.08.2020</div>
                <div class="table_block align_left align_top item3" data-name="Автор запроса">Матрешенкович В.С.</div>
                <div class="table_block align_left align_top item2" data-name="Ссылка на запрос"><a href="#" class="link">Запрос</a></div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена">25.08.2020</div>
                <div class="table_block align_left align_top item2" data-name="Документы"><a href="/html/doc_card.html" class="link">Запрос</a></div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена в печатном виде">25.08.2020</div>
                <div class="table_block align_left align_top item3" data-name="Комментарий"><a href="#" class="link ico_remarks">Замечаний нет</a></div>
            </li>
            <li class="row">
                <div class="table_block align_left align_top" data-name="Статус"><span class="status red"></span></div>
                <div class="table_block align_left align_top item2" data-name="Детали">Передан в печатном виде</div>
                <div class="table_block align_left align_top item3" data-name="Запрошенные документы">Акт расследования с описанием произошедшего события, его причин и установлением виновной стороны</div>
                <div class="table_block align_left align_top item2" data-name="Дата запроса">25.08.2020</div>
                <div class="table_block align_left align_top item3" data-name="Автор запроса">Матрешенкович В.С.</div>
                <div class="table_block align_left align_top item2" data-name="Ссылка на запрос"><a href="#" class="link">Запрос</a></div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена">25.08.2020</div>
                <div class="table_block align_left align_top item2" data-name="Документы"><a href="/html/doc_card.html" class="link">Запрос</a></div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена в печатном виде">25.08.2020</div>
                <div class="table_block align_left align_top item3" data-name="Комментарий"><a href="#" class="link ico_remarks">Замечаний нет</a></div>
            </li>
        </ul><!-- END data_table -->
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
