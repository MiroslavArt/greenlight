<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");
?>



<!--items-->
<div id="users-list">
	<? if (is_array($arResult['ITEMS']) && count($arResult['ITEMS'])): ?>
		<ul class="data_table">
			<li class="row table_head">
				<div class="table_block align_left item3"><p>Логин</p></div>
				<div class="table_block align_left item4"><p>ФИО</p></div>
				<div class="table_block align_left"><p>Права</p></div>
				<div class="table_block align_left item4"><p>Компания</p></div>
				<div class="table_block align_left item3"><p>Дата последнего входа</p></div>
				<div class="table_block align_left item7"></div>
			</li>

			<?foreach ($arResult["ITEMS"] as $arItem):?>
				<li class="row">
					<div class="table_block align_left item3" data-name="Логин"><p><?=$arItem["LOGIN"]?></p></div>
					<div class="table_block align_left item4" data-name="ФИО"><p><?=$arItem["FIO"]?></p></div>
					<?$super = $arItem["IS_SUPER"] ? "not_registered" : "registered"?>
					<div class="table_block align_left" data-name="Права"><p class="<?=$super?>"></p></div>
					<div class="table_block clients_column item4 align_left" data-name="Компания">
						<?if ($arItem["COMPANY_LOGO"]):?>
							<img src="<?=$arItem["COMPANY_LOGO"]?>" width="40" height="40" alt="img1" />
						<?endif?>
						<span><?=$arItem["COMPANY_NAME"]?></span>
					</div><!-- END table_block -->
					<div class="table_block align_left item3" data-name="Дата последнего входа"><?=$arItem["LAST_LOGIN"]?></div>
					<div class="table_block align_left links_column item7" data-name="Ссылки">
						<a href="<?=$arItem['URL_PROFILE']?>" class="link ico_settings">Настройки</a>
						<a href="<?=$arItem['URL_ACCESS']?>" class="link ico_rights">Права доступа</a>
					</div><!-- END table_block -->
				</li>
			<?endforeach?>
		</ul><!-- END data_table -->
	<?else:?>
		<div class="ui-alert ui-alert-danger ui-alert-icon-danger has-errors">
			<span class="ui-alert-message">Данные не найдены</span>
		</div>
	<?endif?>
</div>
<!--items-->
