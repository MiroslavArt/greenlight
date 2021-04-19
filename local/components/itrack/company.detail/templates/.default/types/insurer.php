<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="wrapper">
    <a href="<?=$arParams['LIST_URL']?>" class="back">Вернуться к главному разделу</a>
    <div class="title_container">
        <div class="title_block">
            <span class="type_page">Карточка страховой компании</span>
            <h2 class="block_title"><?= $arResult['COMPANY']['NAME'] ?></h2>
        </div><!-- END title_block -->
        <div class="title_right_block">
            <form class="search_form">
                <input type="text" class="search_text" placeholder="Поиск по списку клиентов"/>
                <input type="submit" class="search" value=""/>
            </form><!-- END search_form -->
            <a href="<?=$arParams['PATH_TO']['useful-documents']?>" class="btn tablet_hide">Полезные документы</a>
            <a href="#" class="btn">Добавить клиента</a>
        </div><!-- END title_right_block -->
    </div><!-- END title_container -->
	<div id="contracts-list">
	<?php if (!empty($arResult['ITEMS'])):?>
		<ul class="data_table">
			<li class="row table_head">
				<div class="table_block clients_column item4"><p>Клиент</p></div>
				<div class="table_block stat_column item2"><p>Убытки, <br />шт</p></div>
				<div class="table_block stat_column item2"><p>Закрыто</p></div>
				<div class="table_block stat_column item2"><p>Документы <br />предоставлены</p></div>
				<div class="table_block stat_column item2"><p>Открыто</p></div>
				<div class="table_block clients_column item3 align_left"><p>СК (Лидер)</p></div>
				<div class="table_block links_column item6"><p>Ссылки</p></div>
			</li>
			<?php foreach ($arResult['ITEMS'] as $arItem):?>
				<li class="row">
					<div class="table_block clients_column item4" data-name="Клиент">
						<?php if (!empty($arItem['LEADERS']['client']['LOGO'])) : ?>
							<img src="<?= $arItem['LEADERS']['client']['LOGO'] ?>" width="40" height="40"
									alt="<?= $arItem['LEADERS']['client']['NAME'] ?>"/>
						<?php endif; ?>
						<span><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?= $arItem['LEADERS']['client']['NAME'] ?></a></span>
					</div><!-- END table_block -->
					<div class="table_block stat_column item2" data-name="Убытки, шт"><?= $arItem["CNT"]["SUM"] ?: 0 ?></div>
					<div class="table_block stat_column green item2" data-name="Закрыто"><?= $arItem["CNT"]["green"] ?: 0 ?></div>
					<div class="table_block stat_column yellow item2" data-name="Документы предоставлены"><?= $arItem["CNT"]["yellow"] ?: 0 ?></div>
					<div class="table_block stat_column red item2" data-name="Открыто"><?= $arItem["CNT"]["red"] ?: 0 ?></div>
					<div class="table_block clients_column item3 align_left" data-name="СК (Лидер)">
						<?php if (!empty($arItem['LEADERS']['insurer']['LOGO'])) : ?>
							<img src="<?= $arItem['LEADERS']['insurer']['LOGO'] ?>" width="40" height="40"
									alt="<?= $arItem['LEADERS']['insurer']['NAME'] ?>"/>
						<?php endif; ?>
						<span><?= $arItem['LEADERS']['insurer']['NAME'] ?></span>
					</div><!-- END table_block -->
					<div class="table_block links_column item6" data-name="Ссылки">
						<a data-fancybox data-type="ajax" href="/ajax/companies_popup.php?target-id=<?=$arItem["ID"]?>&target-type=contract&parties=insurer" class="link ico_doc"><span>Все СК по договору</span></a>
						<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="link ico_doc"><span>Все убытки</span></a>
					</div><!-- END table_block -->
				</li>
			<?php endforeach; ?>
		</ul><!-- END data_table -->
		<ul class="data_table no_bg">
			<li class="row">
				<div class="table_block head_column item4" data-name="Клиент"><p>Итого</p></div>
				<div class="table_block stat_column item2" data-name="Убытки, шт"><?= $arResult["CNT_TOTAL"]["SUM"] ?: 0?></div>
				<div class="table_block stat_column item2" data-name="Закрыто"><?= $arResult["CNT_TOTAL"]["green"] ?: 0?></div>
				<div class="table_block stat_column item2" data-name="Документы предоставлены"><?= $arResult["CNT_TOTAL"]["yellow"] ?: 0?></div>
				<div class="table_block stat_column item2" data-name="Открыто"><?= $arResult["CNT_TOTAL"]["green"] ?: 0?></div>
				<div class="table_block links_column"></div>
			</li>
		</ul><!-- END data_table -->
	<?php else: ?>
		<div class="ui-alert ui-alert-danger ui-alert-icon-danger has-errors">
			<span class="ui-alert-message">Данные не найдены</span>
		</div>
	<?php endif; ?>
</div>
</div><!-- END wrapper -->
