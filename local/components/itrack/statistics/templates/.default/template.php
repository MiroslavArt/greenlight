<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
\Bitrix\Main\UI\Extension::load("ui.alerts");
?>



<a href="/" class="back">Вернуться к главному разделу</a>
<div class="title_container">
	<div class="title_block">
		<h2 class="block_title">Статистика</h2>
	</div><!-- END title_block -->
	<div class="title_right_block">
		<form class="search_form js-submit js-needs-validation" method="get">
			<input type="text" class="search_text" name="search" placeholder="Поиск Убытка" />
			<input type="submit" class="search" value="" />
		</form><!-- END search_form -->
	</div><!-- END title_right_block -->
</div><!-- END title_container -->
<!--items-->
<div id="lost-list">
	<? if (is_array($arResult['ITEMS']) && count($arResult['ITEMS'])): ?>
		<ul class="data_table">
			<?php
				$arFields = [
					"STATUS" => [
						"NAME" => "Статус",
						"CLASS" => "item2"
					],
					"CLIENT" => [
						"NAME" => "Клиент",
						"CLASS" => "item3"
					],
					"CONTRACT_TYPE" => [
						"NAME" => "Вид договора",
						"CLASS" => "item3"
					],
					"DATE_OPENED" => [
						"NAME" => "Дата регистрации <br>в системе",
						"CLASS" => "item3"
					],
					"DATE_CLOSED" => [
						"NAME" => "Дата <br>завершения",
						"CLASS" => "item2"
					],
					"DATE_DIFF" => [
						"NAME" => "Длительности <br>(раб.дни)",
						"CLASS" => "item2"
					],
					"INSURER" => [
						"NAME" => "Страховая комания",
						"CLASS" => "item4 clients_column "
					],
					"COMPENSATION" => [
						"NAME"	 => "Сумма компенсации <br>(руб.)",
						"CLASS" => "item3"
					]
				];
			?>

			<li class="row table_head">
				<?foreach ($arFields as $fieldCode => $arField):?>
					<?php $order = $arResult["SORT"]["ORDER"] === "ASC" ? "order-is-asc" : "order-is-desc"; ?>
					<?php $sort = $arResult["SORT"]["FIELD"] === $fieldCode ? "sorted-by-curr-field" : ""; ?>
					<div class="table_block align_left <?=$arField["CLASS"]?>">
						<a class="<?=$sort?> <?=$order?>" href="<?=$arResult["SORT"]["URLS"][$fieldCode]?>">
							<?=$arField["NAME"]?>
						</a>
					</div>
				<?endforeach?>
			</li>
			<?foreach ($arResult["ITEMS"] as $arItem):?>
			<li class="row">
				<?foreach ($arFields as $fieldCode => $arField):?>
					<div class="table_block align_left <?=$arField["CLASS"]?>" data-name="<?=strip_tags($arField["NAME"])?>">
						<?if ($fieldCode === "STATUS"):?>
							<span class="status <?=$arItem["STATUS"]?>" title="<?=$arItem["RESULT"]?>"></span>
						<?elseif ($fieldCode === "INSURER"):?>
							<img src="<?=$arItem["LOGO"]?>" width="40" height="40" alt="<?=$arItem[$fieldCode]?>">
							<span class="insurer-name" data-fancybox data-type="ajax" data-src="/ajax/companies_popup.php?target-id=<?=$arItem["ID"]?>&target-type=lost&parties=insurer,adjuster"><?=$arItem[$fieldCode]?></span>
						<?else:?>
							<?switch($fieldCode) {
								case "CLIENT": $hint = $arItem["CONTRACT_CODE"]; break;
								case "DATE_CLOSED": $hint = $arItem["RESULT"]; break;
								default: $hint = ""; break;
							}?>
							<p title="<?=$hint?>"><?=$arItem[$fieldCode]?></p>
						<?endif?>
					</div>
				<?endforeach?>
			</li>
			<?endforeach?>
		</ul>
	<?else:?>
		<div class="ui-alert ui-alert-danger ui-alert-icon-danger has-errors">
			<span class="ui-alert-message">Данные не найдены</span>
		</div>
	<?endif?>
</div>
<!--items-->


