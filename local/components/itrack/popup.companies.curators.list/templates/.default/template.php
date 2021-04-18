<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?php
$arParties = [];
foreach ($arResult["PARTIES"] as $party) {
	$arParties[] = GetMessage($party);
}

$last = array_pop($arParties);

$strParties = !count($arParties)
	? $last
	: implode(", ", $arParties) . " и $last"
;
?>

<div class="popup small rivals-list" style="display: inline-block; padding: 60px">
	<h3 class="block_title">Все <?=$strParties?> по <?=GetMessage($arResult["TARGET_TYPE"]);?></h3>
	<?foreach ($arResult["ITEMS"] as $arItem):?>
		<div class="client_block" style="align-items: center;">
			<img src="<?=$arItem["LOGO"]?>" alt="<?=$arItem["NAME"]?>" width="40" height="40">
			<span><?=$arItem["NAME"]?></span>
		</div><!-- END client_block -->
	<?endforeach?>
</div>
