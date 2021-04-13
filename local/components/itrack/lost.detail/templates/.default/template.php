<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");
//\Itrack\Custom\Helpers\Utils::varDump($arResult['CURATORS']);
?>
<div class="wrapper">
    <?php if($arParams['CURATORS_MODE']) : ?>
        <a href="<?= $arParams['PATH_TO']['lost'] ?>" class="back"><?=GetMessage("LOST_CARD")?></a>
    <?php else :?>
        <a href="<?= $arResult['CONTRACT_PAGE_URL'] ?>" class="back">Договор страхования</a>
    <?php endif;?>
    <div class="title_container">
        <div class="title_block">
            <span class="type_page"><?= GetMessage('ALL_LOST_CURATORS') ?></span>
            <h2 class="block_title"><?= $arResult['LOST']['NAME'] ?></h2>
            <div class="card_status_container">
                <span class="card_status <?= $arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR'] ?>">Статус: <?= (!empty($arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_NAME']) ? $arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_NAME'] : ' не установлен') ?></span>
                <span class="type_page">от <?= (new \DateTime($arResult['TIMESTAMP_X']))->format('d.m.Y') ?></span>

            </div><!-- END card_status_container -->

        </div><!-- END title_block -->
        <div class="title_right_block">
            <? if($arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR']=='yellow') { ?>
                 <a href="#change_status" data-fancybox class="ico_settings link">Закрыть убыток</a>
            <? } ?>
            <? if($arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR']=='red' || $arResult['LOST']['PROPERTIES']['STATUS']['VALUE']['UF_COLOR']=='yellow') { ?>
                <a href="#add_doc2" data-fancybox class="btn">Добавить документ</a>
            <? } ?>
        </div><!-- END title_right_block -->
    </div><!-- END title_container -->
    <div class="popup all_statuses" id="change_status">
		<form class="form_popup2">
			<div class="form_row">
				<div class="input_container column_50 decision_container">
				    <input type="hidden" name="lostid" value="<?=$arResult['LOST']['ID']?>"/>
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
        <form class="form_popup">
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
                    <input type="text" class="text_input ico_date js_datapicker" name="docterm" placeholder="Срок предоставления" />
                </div><!-- END input_container -->
                <div class="input_container column_50">
                    <span>От куратора</span>
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
    <?php if($arParams['CURATORS_MODE']) : ?>
        <?php include 'includes/curators.php'; ?>
    <?php else :?>
        <?php include 'includes/default.php'; ?>
    <?php endif;?>
</div><!-- END wrapper -->

