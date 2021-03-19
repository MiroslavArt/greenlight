<?php
global $APPLICATION;

use Bitrix\Main\Context;
use Itrack\Custom\CUserRole;

$currUserRole = new CUserRole();

if (!$currUserRole->isSuperUser()) {
	LocalRedirect('/');
}

$request = Context::getCurrent()->getRequest();
$currTab = $request->get('tab') ?: $currUserRole->getUserParty();
?>

<div class="wrapper">
	<a href="/" class="back">Вернуться к главному разделу</a>
	<div class="title_container">
		<div class="title_block">
			<h2 class="block_title">Пользователи</h2>
		</div><!-- END title_block -->
		<div class="title_right_block">
			<form class="search_form js-submit js-needs-validation" method="get" data-url="<?= $APPLICATION->GetCurPageParam() ?>">
				<input type="text" class="search_text" name="search" value="<?=$request->get('search')?>" placeholder="Поиск по списку клиентов" />
				<input type="submit" class="search" value="" />
			</form><!-- END search_form -->
			<a href="#" class="btn">Добавить пользователя</a>
		</div><!-- END title_right_block -->
	</div><!-- END title_container -->

	<?
	$tabs = [
		[
			'NAME' => 'Клиенты',
			'SHOW' => $currUserRole->isSuperBroker() || $currUserRole->isSuperClient(),
			'CODE' => CUserRole::getClientGroupCode(),
		],
		[
			'NAME' => 'Страховой брокер',
			'SHOW' => $currUserRole->isSuperBroker(),
			'CODE' => CUserRole::getBrokerGroupCode()
		],
		[
			'NAME' => 'Страховые компании',
			'SHOW' => $currUserRole->isSuperBroker() || $currUserRole->isSuperInsurer(),
			'CODE' => CUserRole::getInsurerGroupCode()
		],
		[
			'NAME' => 'Аджастеры',
			'SHOW' => $currUserRole->isSuperBroker() || $currUserRole->isSuperAdjuster(),
			'CODE' => CUserRole::getAdjusterGroupCode()
		],
	];
	?>
	<ul class="tabs margin">
		<?foreach ($tabs as $tab):?>
			<? if (!$tab['SHOW']) continue; ?>
			<?$active = $currTab === $tab['CODE'] ? 'active' : ''?>
			<li class="<?=$active?>">
				<a href="?tab=<?=$tab['CODE']?>"><?=$tab['NAME']?></a>
			</li>
		<?endforeach?>
	</ul><!-- END tabs -->

	<?
	$arUser = \CUser::GetByID($GLOBALS["USER"]->GetId())->GetNext()
	?>

	<?$APPLICATION->IncludeComponent(
		"itrack:users.list",
		"",
		[
			'TAB' => $currTab,
			'COMPANY' => $currUserRole->isSuperBroker() ? false : $arUser['UF_COMPANY'],
			'FOLDER' => $arResult['FOLDER'],
			'URL_TEMPLATES' => $arResult['URL_TEMPLATES']
		],
		false
	);?>
</div><!-- END wrapper -->
