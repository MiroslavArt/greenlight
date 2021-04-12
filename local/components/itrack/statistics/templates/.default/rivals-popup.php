<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<div class="popup small rivals-list" style="display: inline-block; padding: 60px">
	<h3 class="block_title">Все СК и аджастеры по убытку</h3>
	<?foreach ($arResult["ITEMS"] as $arItem):?>
		<div class="client_block">
			<img src="<?=$arItem["LOGO"]?>" alt="<?=$arItem["NAME"]?>" width="40" height="40">
			<span><?=$arItem["NAME"]?></span>
		</div><!-- END client_block -->
	<?endforeach?>
</div>
