<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");
//\Itrack\Custom\Helpers\Utils::varDump($arResult['REQUESTS']);
?>


<div class="wrapper">
    <a href="<?= $arResult['CONTRACT_PAGE_URL'] ?>" class="back">Договор страхования</a>
    <div class="title_container">
        <div class="title_block">
            <span class="type_page"><?= GetMessage('LOST_CARD') ?></span>
            <h2 class="block_title"><?= $arResult['LOST']['NAME'] ?></h2>
            <div class="card_status_container">
                <span class="card_status <?= $arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR'] ?>">Статус: <?= (!empty($arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_NAME']) ? $arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_NAME'] : ' не установлен') ?></span>
                <span class="type_page">от <?= (new \DateTime($arResult['TIMESTAMP_X']))->format('d.m.Y') ?></span>
            </div><!-- END card_status_container -->
        </div><!-- END title_block -->
        <div class="title_right_block">
            <a href="#" class="btn">Добавить документ</a>
        </div><!-- END title_right_block -->
    </div><!-- END title_container -->
    <div class="desc_container">
        <div class="desc">
            <span class="desc_title">Описание страхового случая</span>
            <?php if (!empty($arResult['LOST']['PREVIEW_TEXT'])) : ?>
                <?= $arResult['LOST']['~PREVIEW_TEXT'] ?>
            <?php endif; ?>
        </div><!-- END desc -->
        <div class="table_container">
            <table class="table_curators">
                <tr>
                    <th width="160">Участник</th>
                    <th width="160">ФИО</th>
                    <th width="160">Должность</th>
                    <th width="160">Телефон (моб)</th>
                </tr>
                <?php if (!empty($arResult['CURATORS'])) : ?>
                    <?php foreach ($arResult['CURATORS'] as $arCurator) : ?>
                        <tr>
                            <td><?= $arCurator['COMPANY_TYPE'] ?> <br/><?= $arCurator['COMPANY_NAME'] ?></td>
                            <td><?= CUser::formatName('#NAME# #SECOND_NAME# #LAST_NAME#', $arCurator, false, false); ?></td>
                            <td><?= $arCurator['POSITION'] ?></td>
                            <td><?= $arCurator['PHONE'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table><!-- END table_curators -->
            <a href="/html/all_curators.html" class="all_curators"><span>Все кураторы по убытку</span></a>
        </div><!-- END table_container -->
        <div class="docs_container">
            <span class="desc_title">Документы по убытку</span>
            <?php if(!empty($arResult['DOCS_STATUSES'])) : ?>
            <ul class="list_dosc">
                <?php foreach ($arResult['DOCS_STATUSES'] as $key=>$arStatus) : ?>
                <li class="<?=$arResult['STATUSES'][$key]['UF_COLOR']?>">
                    <p><?=$arResult['STATUSES'][$key]['UF_NAME']?></p>
                    <span><?=count($arStatus['DOCS'])?> док.</span>
                </li>
                <?php endforeach;?>
            </ul><!-- END list_docs -->
            <?php endif; ?>
            <div class="docs_container_bottom">
                <?php if ($arResult['CONTRACT']['PROPERTIES']['ORIGIN_REQUIRED']['VALUE_XML_ID'] == 'Y') : ?>
                    <span class="originals"><span>Предоставление оригиналов</span></span>
                <?php endif; ?>
                <?php if (!empty($arResult['REQUESTS'])) : ?>
                    <?php $intDocsQty = count($arResult['REQUESTS']); ?>
                    <span class="desc_title">всего <?= $intDocsQty; ?> <?= num2word($intDocsQty, array('документ', 'документа', 'документов')); ?></span>
                <?php endif; ?>
            </div><!-- END docs_container_bottom -->
        </div><!-- END docs_container -->
    </div><!-- END desc_container -->
    <?php if (!empty($arResult['REQUESTS'])) : ?>
        <ul class="data_table">
            <li class="row table_head">
                <div class="table_block align_left"><p>Статус</p></div>
                <div class="table_block align_left item2"><p>Детали</p></div>
                <div class="table_block align_left item3"><p>Запрошенные документы</p></div>
                <div class="table_block align_left item2"><p>Дата запроса</p></div>
                <div class="table_block align_left item3"><p>Автор запроса</p></div>
                <div class="table_block align_left item2"><p>Ссылка <br/>на запрос</p></div>
                <div class="table_block align_left item2"><p>Информация <br/>предоставлена</p></div>
                <div class="table_block align_left item2"><p>Документы</p></div>
                <div class="table_block align_left item2"><p>Информация <br/>предоставлена <br/>в печатном виде</p>
                </div>
                <div class="table_block align_left item3"><p>Комментарий</p></div>
            </li>
            <?php foreach ($arResult['REQUESTS'] as $arItem) : ?>
                <li class="row">
                    <div class="table_block align_left align_top" data-name="Статус"><span class="status <?= $arResult['STATUSES'][$arItem['PROPERTIES']['STATUS']['VALUE']]['UF_COLOR'] ?>"></span></div>
                    <div class="table_block align_left align_top item2" data-name="Детали"><?= $arItem['STATUS_NAME'] ?></div>
                    <div class="table_block align_left align_top item3" data-name="Запрошенные документы"><?= $arItem['NAME'] ?></div>
                    <div class="table_block align_left align_top item2" data-name="Дата запроса"><?= (new \DateTime($arItem['DATE_CREATE']))->format('d.m.Y') ?></div>
                    <div class="table_block align_left align_top item3" data-name="Автор запроса"><?= $arItem['USER_FIO'] ?></div>
                    <div class="table_block align_left align_top item2" data-name="Ссылка на запрос"><a href="#" class="link">Запрос</a></div>
                    <div class="table_block align_left align_top item2" data-name="Информация предоставлена"></div>
                    <div class="table_block align_left align_top item2" data-name="Документы"><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="link">Документ</a></div>
                    <div class="table_block align_left align_top item2" data-name="Информация предоставлена в печатном виде">25.08.2020</div>
                    <div class="table_block align_left align_top item3" data-name="Комментарий"><a href="#" class="link ico_remarks">Замечаний нет</a></div>
                </li>
            <?php endforeach; ?>
        </ul><!-- END data_table -->
    <?php endif; ?>
</div><!-- END wrapper -->