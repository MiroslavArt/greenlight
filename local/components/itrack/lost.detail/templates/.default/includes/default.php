<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="desc_container">
    <div class="desc">
        <span class="desc_title">Описание страхового случая</span>
        <?php if (!empty($arResult['LOST']['PROPERTIES']['DESCRIPTION'])) : ?>
            <p data-fancybox data-src="#edit_comm" class="pointer"><?= mb_substr($arResult['LOST']['PROPERTIES']['DESCRIPTION']['VALUE'], 0, 280) ?><?
                if(mb_strlen($arResult['LOST']['PROPERTIES']['DESCRIPTION']['VALUE'])>280) {
                    echo "...";
                }
                ?></p>
            <div class="popup add_comment" id="edit_comm">
                <h6 class="small_title">Комментарий</h6>
                <form class="form3">
                    <div class="form_row">
                        <div class="input_container column_100">
                            <input type="hidden" name="lostid" value="<?=$arResult['LOST']['ID']?>"/>
                            <textarea class="textarea" name="lossdescript"><?= $arResult['LOST']['PROPERTIES']['DESCRIPTION']['VALUE'] ?></textarea>
                        </div><!-- END input_container -->
                    </div><!-- END form_row -->
                    <p class="link" id="mistake1"></p>
                    <input type="submit" class="btn" value="Обновить">
                </form><!-- END form_popup -->
            </div><!-- END popup -->
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
                <?php foreach ($arResult['CURATORS'] as $arCurator) :
                    if($arCurator['IS_LEADER']=='Y') :
                    ?>
                    <tr>
                        <td><?= $arCurator['COMPANY_TYPE'] ?> <br/><?= $arCurator['COMPANY_NAME'] ?></td>
                        <td><?= CUser::formatName('#NAME# #SECOND_NAME# #LAST_NAME#', $arCurator, false, false); ?></td>
                        <td><?= $arCurator['POSITION'] ?></td>
                        <td><?= $arCurator['PHONE'] ?></td>
                    </tr>
                <?php
                    endif;
                    endforeach; ?>
            <?php endif; ?>
        </table><!-- END table_curators -->
        <a href="<?= $arParams['PATH_TO']['lost-curators'] ?>" class="all_curators"><span>Все кураторы по убытку</span></a>
    </div><!-- END table_container -->
    <div class="docs_container">
        <span class="desc_title">Документы по убытку</span>
        <?php if(!empty($arResult['DOCS_STATUSES'])) : ?>
            <ul class="list_dosc">
                <?php foreach ($arResult['DOCS_STATUSES'] as $key=>$arStatus) : ?>
                    <li class="<?=$arResult['STATUSES'][$key][$arResult['COLOR_FIELD']]?>">
                        <p><?=$arResult['STATUSES'][$key]['UF_NAME']?></p>
                        <span><?=count($arStatus['DOCS'])?> док.</span>
                    </li>
                <?php endforeach;?>
            </ul><!-- END list_docs -->
        <?php endif; ?>
        <div class="docs_container_bottom">
            <?php if ($arResult['CONTRACT']['PROPERTIES']['ORIGIN_REQUIRED']['VALUE_XML_ID'] == 'Y') : ?>
                <span class="originals"><span>Предоставление оригиналов</span></span>
            <?php else : ?>
                <span class="originals no_files"><span>Без предоставления оригиналов</span></span>
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
            <div class="table_block align_left item2"><p>Предоставить до</p></div>
            <div class="table_block align_left item3"><p>Автор запроса</p></div>
            <div class="table_block align_left item2"><p>Ссылка <br/>на запрос</p></div>
            <div class="table_block align_left item2"><p>Информация <br/>предоставлена</p></div>
            <div class="table_block align_left item2"><p>Документы</p></div>
            <div class="table_block align_left item2"><p>Информация <br/>предоставлена <br/>в печатном виде</p>
            </div>
            <div class="table_block align_left item3"><p>Комментарий</p></div>
        </li>
        <?php foreach ($arResult['REQUESTS'] as $arItem) :
            ?>
            <li class="row">
                <div class="table_block align_left align_top" data-name="Статус"><span class="status <?= $arResult['STATUSES'][$arItem['PROPERTIES']['STATUS']['VALUE']][$arResult['COLOR_FIELD']] ?>"></span></div>
                <div class="table_block align_left align_top item2" data-name="Детали"><?= $arItem['STATUS_NAME'] ?></div>
                <div class="table_block align_left align_top item3" data-name="Запрошенные документы"><?= $arItem['NAME'] ?></div>
                <div class="table_block align_left align_top item2" data-name="Срок предоставления"><?= (new \DateTime($arItem['PROPERTIES']['REQUEST_DEADLINE']['VALUE']))->format('d.m.Y') ?></div>
                <div class="table_block align_left align_top item3" data-name="Автор запроса"><?= $arItem['USER_FIO'] ?></div>
                <div class="table_block align_left align_top item2" data-name="Ссылка на запрос"><a href="#add_doc<?= $arItem['ID'] ?>" data-fancybox class="link reqdoclink">Запрос</a></div>
                <div class="popup add_doc" id="add_doc<?= $arItem['ID'] ?>">
                    <?php
                    $APPLICATION->IncludeComponent(
                        'itrack:lost.request.docs',
                        '',
                        array(
                            "LOST_DOC" => $arItem['ID'],
                            "TYPE" => 1
                        ),
                        null
                    );
                    ?>
                </div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена"><?=$arItem['INFO_PROVIDED']?></div>
                <div class="table_block align_left align_top item2" data-name="Документы"><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="link">Документ</a></div>
                <div class="table_block align_left align_top item2" data-name="Информация предоставлена в печатном виде">
                    <?
                    if($arItem['PROPERTIES']['STATUS']['VALUE']==14) {
                        if($arItem['PROPERTIES']['STATUS_DATE']['VALUE']) {
                            echo date("d.m.Y", strtotime($arItem['PROPERTIES']['STATUS_DATE']['VALUE']));
                        }
                    } ?></div>
                <div class="table_block align_left align_top item3" data-name="Комментарий">
                    <? if($arItem['REJECTIONS']) { ?>
                    <a class="link" href="#read_comment<?=$arItem['ID']?>" data-fancybox>Есть замечания</a></div>
                <div class="popup add_comment" id="read_comment<?=$arItem['ID']?>">
                    <h6 class="small_title">Комментарий</h6>
                    <div class="text">
                        <? foreach ($arItem['REJECTIONS'] as $reject) { ?>
                            <p><?=$reject?></p>
                        <? } ?>
                    </div><!-- END text -->
                    <span data-fancybox-close class="link close_modal">Закрыть описание</span>
                </div><!-- END popup -->
                <? } else { ?>
                    <!--<a href="#" class="link ico_remarks">Замечаний нет</a>-->
                    <span class="link">Замечаний нет</span>
                    </div>
                <? } ?>
            </li>
        <?php endforeach; ?>
    </ul><!-- END data_table -->
<?php endif; ?>