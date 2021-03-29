<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="wrapper" data-sessid="<?=bitrix_sessid()?>">
	<a href="<?=$arParams["PROFILE_URL"]?>" class="back">Вернуться к редактированию пользователя</a>
	<div class="title_container">
		<div class="title_block">
			<h2 class="block_title">Права доступа</h2>
		</div><!-- END title_block -->
		<div class="title_right_block">
			<div class="profile_desc">
				<h3><?=$arResult["USER"]["NAME"]?></h3>
				<p><?=$arResult["USER"]["WORK_POSITION"]?><br /><?=$arResult["COMPANY"]["NAME"]?></p>
			</div><!-- END profile_desc -->
		</div><!-- END title_right_block -->
	</div><!-- END title_container -->
	<div class="title_container">
		<div class="title_block">
			<h2 class="subtitle">Список убытков</h2>
		</div><!-- END title_block -->
		<div class="title_right_block">
			<?$active = $arResult["FILTER_LOSTS"] == "Y" ? "active" : ""?>
			<label class="checkbox_type2 js_checkbox <?=$active?>" data-filter-url="<?=$arResult["LOST_FILTER_URL"]?>">
				<input type="checkbox" /><span>Отображать только те, где я являюсь куратором</span>
			</label>
		</div><!-- END title_right_block -->
	</div><!-- END title_container -->
	<ul class="data_table">
		<li class="row table_head">
			<div class="table_block align_left item2"><p>Статус</p></div>
			<div class="table_block align_left item2"><p>Акцепт</p></div>
			<div class="table_block align_left item2"><p>Уведомления</p></div>
			<div class="table_block align_left item2"><p>Прикрепить</p></div>
			<div class="table_block align_left item3"><p>Статус развернуто</p></div>
			<div class="table_block align_left item3"><p>Уникальный номер</p></div>
			<div class="table_block align_left item2"><p>Дата</p></div>
			<div class="table_block align_left item6"><p>Описание</p></div>
		</li>
		<?foreach ($arResult["LOST"]["TARGETS"] as $arLost):?>
			<li class="row" data-target-type="lost" data-target-id="<?=$arLost["ID"]?>">
				<div class="table_block align_left item2" data-name="Статус"><span class="status <?=$arLost["PROPERTIES"]["STATUS"]["VALUE"]?>"></span></div>

				<?$isActive = in_array($arLost["ID"], $arResult["SWITCH"]["LOST"]["ACCEPTANCE"])?>
				<div class="table_block align_left item2" data-name="Акцепт">
					<?$active = $isActive ? "active" : ""?>
					<?$checked = $isActive ? "checked" : ""?>
					<label class="switch <?=$active?> js_checkbox">
						<input class="js-access-switch" data-switch-type="acceptance" type="checkbox" <?=$checked?> />
					</label>
				</div>

				<?$isActive = in_array($arLost["ID"], $arResult["SWITCH"]["LOST"]["NOTIFICATION"])?>
				<div class="table_block align_left item2" data-name="Уведомления">
					<?$active = $isActive ? "active" : ""?>
					<?$checked = $isActive ? "checked" : ""?>
					<label class="switch <?=$active?> js_checkbox">
						<input class="js-access-switch" data-switch-type="notification" type="checkbox" <?=$checked?> />
					</label>
				</div>

				<?
				$participation = $arResult["LOST"]["PARTICIPANTS"][$arLost["ID"]];
				$isCurator = in_array($arResult["USER"]["ID"], $participation["PROPERTIES"]["CURATORS"]["VALUE"]);
				$isLeader = $arResult["USER"]["ID"] === $participation["PROPERTIES"]["CURATOR_LEADER"]["VALUE"];
				$isActive = $isCurator || $isLeader;
				?>
				<div class="table_block align_left item2" data-name="Прикрепить">
					<a href="#" class="js-unbind-curator btn ico_minus ico_minus_short <?=$isActive ? "" : "hidden"?>" data-enable="n"></a>
					<a href="#" class="js-bind-curator btn ico_plus ico_plus_short <?=$isActive ? "hidden" : ""?>" data-enable="y"></a>
				</div>

				<div class="table_block align_left item3" data-name="Статус развернуто">
					<?$statusCode = $arLost["PROPERTIES"]["STATUS"]["VALUE"]?>
					<p><?=$arResult["STATUS_LIST"][$statusCode]["UF_NAME"]?></p>
				</div>
				<div class="table_block align_left item3" data-name="Уникальный номер"><?=$arLost["CODE"]?></div>
				<div class="table_block align_left item2" data-name="Дата"><?=$arLost["PROPERTIES"]["REQUEST_DATE"]["VALUE"]?></div>
				<div class="table_block align_left item6" data-name="Описание"><?=$arLost["PREVIEW_TEXT"]?></div>
			</li>
		<?endforeach;?>
	</ul><!-- END data_table -->
	<div class="title_container">
		<div class="title_block">
			<h2 class="subtitle">Список договоров</h2>
		</div><!-- END title_block -->
		<div class="title_right_block">
			<?$active = $arResult["FILTER_CONTRACTS"] == "Y" ? "active" : ""?>
			<label class="checkbox_type2 js_checkbox <?=$active?>" data-filter-url="<?=$arResult["CONTRACT_FILTER_URL"]?>">
				<input type="checkbox" /><span>Отображать только те, где я являюсь куратором</span>
			</label>
		</div><!-- END title_right_block -->
	</div><!-- END title_container -->
	<ul class="data_table">
		<li class="row table_head">
			<div class="table_block align_left item2"><p>№ договора</p></div>
			<div class="table_block align_left item2"><p>Акцепт</p></div>
			<div class="table_block align_left item2"><p>Уведомления</p></div>
			<div class="table_block align_left item2"><p>Прикрепить</p></div>
			<div class="table_block align_left item2"><p>Дата <br />договора</p></div>
			<div class="table_block align_left item3"><p>Вид страхования</p></div>
			<div class="table_block stat_column item2"><p>Убытки <br />всего</p></div>
			<div class="table_block stat_column item2"><p>Закрытые</p></div>
			<div class="table_block stat_column item2"><p>Документы <br />предоставлены</p></div>
			<div class="table_block stat_column item2"><p>Открытые</p></div>
		</li>
		<?foreach ($arResult["CONTRACT"]["TARGETS"] as $arContract):?>
			<li class="row"  data-target-type="contract" data-target-id="<?=$arContract["ID"]?>">
				<div class="table_block align_left item2" data-name="№ договора"><p><?=$arContract["NAME"]?></p></div>

				<?$isActive = in_array($arContract["ID"], $arResult["SWITCH"]["CONTRACT"]["ACCEPTANCE"])?>
				<div class="table_block align_left item2" data-name="Акцепт">
					<?$active = $isActive ? "active" : ""?>
					<?$checked = $isActive ? "checked" : ""?>
					<label class="switch <?=$active?> js_checkbox">
						<input class="js-access-switch" data-switch-type="acceptance" type="checkbox" <?=$checked?> />
					</label>
				</div>

				<?$isActive = in_array($arContract["ID"], $arResult["SWITCH"]["CONTRACT"]["NOTIFICATION"])?>
				<div class="table_block align_left item2" data-name="Уведомления">
					<?$active = $isActive ? "active" : ""?>
					<?$checked = $isActive ? "checked" : ""?>
					<label class="switch <?=$active?> js_checkbox">
						<input class="js-access-switch" data-switch-type="notification" type="checkbox" <?=$checked?> />
					</label>
				</div>


				<?
				$participation = $arResult["CONTRACT"]["PARTICIPANTS"][$arContract["ID"]];
				$isCurator = in_array($arResult["USER"]["ID"], $participation["PROPERTIES"]["CURATORS"]["VALUE"]);
				$isLeader = $arResult["USER"]["ID"] === $participation["PROPERTIES"]["CURATOR_LEADER"]["VALUE"];
				$isActive = $isCurator || $isLeader;
				?>
				<div class="table_block align_left item2" data-name="Прикрепить">
					<a href="#" class="js-unbind-curator btn ico_minus ico_minus_short <?=$isActive ? "" : "hidden"?>" data-enable="n"></a>
					<a href="#" class="js-bind-curator btn ico_plus ico_plus_short <?=$isActive ? "hidden" : ""?>" data-enable="y"></a>
				</div>


				<div class="table_block align_left item2" data-name="Дата договора"><p><?=$arContract["PROPERTIES"]["DATE"]["VALUE"]?></p></div>
				<div class="table_block align_left item3" data-name="Вид страхования"><p><?=$arContract["PROPERTIES"]["TYPE"]["VALUE"]?></p></div>

				<? $statistics = $arResult["CONTRACT_STATISTICS"][$arContract["ID"]]; ?>
				<div class="table_block stat_column item2" data-name="Убытки всего"><?=$statistics["green"] + $statistics["yellow"] + $statistics["red"]?></div>
				<div class="table_block stat_column green item2" data-name="Закрытые"><?=$statistics["green"] ?: 0?></div>
				<div class="table_block stat_column yellow item2" data-name="Документы предоставлены"><?=$statistics["yellow"] ?: 0?></div>
				<div class="table_block stat_column red item2" data-name="Открытые"><?=$statistics["red"] ?: 0?></div>
			</li>
		<?endforeach;?>
	</ul><!-- END data_table -->
</div>

