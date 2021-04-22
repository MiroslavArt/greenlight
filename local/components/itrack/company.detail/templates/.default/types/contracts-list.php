<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div id="contracts-wrapper" class="wrapper">
    <a href="<?=$arParams['LIST_URL']?>" class="back">На главную</a>
    <div class="title_container">
        <div class="title_block">
            <h2 class="block_title">Список договоров</h2>
        </div><!-- END title_block -->
        <div class="title_right_block">
            <form class="search_form js-submit js-needs-validation" method="get"
                  data-url="<?= $APPLICATION->GetCurPage() ?>">
                <?= bitrix_sessid_post() ?>
                <input type="hidden" name="is_ajax" value="y">
                <input type="text" name="search" class="search_text" placeholder="Поиск по списку договоров"/>
                <input type="submit" class="search" value=""/>
            </form><!-- END search_form -->
        </div><!-- END title_right_block -->
    </div><!-- END title_container -->
    <?php if (!empty($arResult['ITEMS'])): ?>
        <div id="contracts-list">
            <ul class="data_table">
                <li class="row table_head">
                    <div class="table_block align_left item3"><p>№ договора</p></div>
                    <div class="table_block align_left item2"><p>Дата договора</p></div>
                    <div class="table_block align_left item3"><p>Вид страхования</p></div>
                    <div class="table_block stat_column"><p>Убытки, <br />шт</p></div>
                    <div class="table_block stat_column"><p>Закрыто</p></div>
                    <div class="table_block stat_column item2"><p>Документы <br />предоставлены</p></div>
                    <div class="table_block stat_column"><p>Открыто</p></div>
                    <div class="table_block item3"><p>СК (Лидер)</p></div>
                    <div class="table_block item6 links_column"><p>Ссылки</p></div>
                </li>
                <?php foreach ($arResult['ITEMS'] as $arItem): ?>
                    <li class="row">
                        <div class="table_block align_left item3" data-name="№ договора"><p><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></p></div>
                        <div class="table_block align_left item2" data-name="Дата договора"><p><?=$arItem['DATE']?></p></div>
                        <div class="table_block align_left item3" data-name="Вид страхования"><p><?=$arItem['TYPE']?></p></div>
                        <div class="table_block stat_column" data-name="Убытки, шт"><?=$arItem["CNT"]["SUM"] ?: 0?></div>
                        <div class="table_block stat_column green" data-name="Закрыто"><?=$arItem["CNT"]["green"] ?: 0?></div>
                        <div class="table_block stat_column yellow item2" data-name="Документы предоставлены"><?=$arItem["CNT"]["yellow"] ?: 0?></div>
                        <div class="table_block stat_column red" data-name="Открыто"><?=$arItem["CNT"]["red"] ?: 0?></div>
                        <div class="table_block clients_column item3 align_left" data-name="СК (Лидер)">
                            <img src="<?=$arItem["LEADERS"]["insurer"]["LOGO"]?>" width="40" height="40" alt="img1" />
                            <span>
								<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
									<?=$arItem["LEADERS"]["insurer"]["NAME"]?>
								</a>
							</span>
						</div>
                        <div class="table_block item6 links_column" data-name="Ссылки">
                            <a data-fancybox data-type="ajax" href="/ajax/companies_popup.php?target-id=<?=$arItem["ID"]?>&target-type=contract&parties=insurer" class="link ico_doc"><span>Все СК по договору</span></a>
                            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="link ico_doc"><span>Все убытки</span></a>
                        </div><!-- END links_column -->
                    </li>
                <?php endforeach; ?>
            </ul><!-- END data_table -->
            <ul class="data_table no_bg">
				<li class="row">
					<div class="table_block head_column item8" data-name="Клиент"><p>Итого</p></div>
					<div class="table_block stat_column" data-name="Убытки, шт"><?=$arResult["CNT_TOTAL"]["SUM"] ?: 0?></div>
					<div class="table_block stat_column"><?=$arResult["CNT_TOTAL"]["green"] ?: 0?></div>
					<div class="table_block stat_column item2"><?=$arResult["CNT_TOTAL"]["yellow"] ?: 0?></div>
					<div class="table_block stat_column"><?=$arResult["CNT_TOTAL"]["red"] ?: 0?></div>
					<div class="table_block item9 links_column"></div>
				</li>
            </ul><!-- END data_table -->
        </div>
    <?php else: ?>
        <div id="contracts-list">
            <div class="ui-alert ui-alert-danger ui-alert-icon-danger has-errors">
                <span class="ui-alert-message">Данные не найдены</span>
            </div>
        </div>
    <?php endif; ?>
</div><!-- END wrapper -->
