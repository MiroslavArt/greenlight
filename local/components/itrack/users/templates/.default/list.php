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
			<!-- <a href="#" class="btn">Добавить пользователя</a> -->
            <a href="#add_user" class="btn" data-fancybox>Добавить пользователя</a>
		</div><!-- END title_right_block -->
	</div><!-- END title_container -->
    <div class="popup" id="add_user">
        <h3 class="block_title">Добавление пользователя</h3>
        <form class="form_popup js_user_add">
            <div class="form_row">
                <div class="input_container column_25">
                    <input type="text" class="text_input" id="last_name" placeholder="Фамилия" />
                </div><!-- END input_container -->
                <div class="input_container column_25">
                    <input type="text" class="text_input" id="name" placeholder="Имя" />
                </div><!-- END input_container -->
                <div class="input_container column_25">
                    <input type="text" class="text_input" id="second_name" placeholder="Отчество" />
                </div><!-- END input_container -->
                <div class="input_container column_25">
                    <input type="email" class="text_input" id="email" placeholder="Email" data-id="<?=$arResult['PWD']?>" />
                </div><!-- END input_container -->
            </div><!-- END form_row -->
            <div class="form_row">
                <div class="input_container">
                    <select id="company" class="select js_select">
                        <? foreach ($arResult['COMPANIES'] as $company) { ?>
                            <option value="<?= $company['ID']?>"><?= $company['NAME']?></option>
                        <? } ?>
                    </select><!-- END select -->
                    <!-- <input type="text" class="text_input" placeholder="Название компании" /> -->
                </div><!-- END input_container -->
                <div class="input_container">
                    <input type="text" class="text_input" id="position" placeholder="Должность" />
                </div><!-- END input_container -->
            </div><!-- END form_row -->
            <div class="form_row">
                <div class="input_container column_25">
                    <input type="tel" class="text_input" id="persphone" placeholder="Контактный телефон" />
                </div><!-- END input_container -->
                <div class="input_container column_25">
                    <input type="tel" class="text_input" id="workphone" placeholder="Рабочий телефон" />
                </div><!-- END input_container -->
                <div class="input_container small">
                    <input type="text" class="text_input" id="code" placeholder="Доб. код" />
                </div><!-- END input_container -->
                <div class="input_container column_25 superuser">
                    <label class="switch big js_checkbox"><input type="checkbox" /></label>
                    <label class="label">Суперпользователь</label>
                </div><!-- END input_container -->
            </div><!-- END form_row -->
            <div class="form_row">
                <div class="input_container">
                    <label class="big_label">Прикрепить к договору страхования</label>
                    <select class="select js_select" id="contract">
                        <? foreach ($arResult['CONTRACTS'] as $contract) { ?>
                            <option value="<?= $contract['ID']?>"><?= $contract['NAME']?></option>
                        <? } ?>
                    </select><!-- END select -->
                </div><!-- END input_container -->
                <div class="input_container">
                    <label class="big_label">Прикрепить к убытку</label>
                    <select class="select js_select" id="loss">
                        <? foreach ($arResult['LOSSES'] as $contract) { ?>
                            <option value="<?= $contract['ID']?>"><?= $contract['NAME']?></option>
                        <? } ?>
                    </select><!-- END select -->
                </div><!-- END input_container -->
            </div><!-- END form_row -->
            <p class="link" id="mistake"></p>
            <input type="submit" class="btn" value="Добавить пользователя" />
        </form><!-- END form_edit_profile -->
    </div><!-- END popup -->

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
