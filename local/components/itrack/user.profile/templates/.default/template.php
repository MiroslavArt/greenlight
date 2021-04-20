<?php
\Bitrix\Main\UI\Extension::load("ui.alerts");

if (!function_exists("printInputProfile")) {
	function printInputProfile($arResult, $title, $name, $forSuperUser = false, $type = "text", $class = "column_3") {
		?>
		<div class="input_container <?=$class?>">
			<label class="label"><?=$title?></label>
			<?if ($forSuperUser && !$arResult["EDITOR_IS_SUPER_USER"]):?>
				<input type="<?=$type?>" class="text_input" value="<?=$arResult[$name]?>" disabled/>
			<?else:?>
				<input type="<?=$type?>" class="text_input" value="<?=$arResult[$name]?>" name="<?=$name?>" />
			<?endif;?>
		</div><!-- END input_container -->
		<?
	}
}
?>

<div class="wrapper">
	<a href="<?=$arParams["LIST_URL"]?>" class="back">Вернуться к разделу Пользователи</a>
	<div class="title_container">
		<div class="title_block table_big">
			<h2 class="block_title">Редактирование профиля</h2>
		</div><!-- END title_block -->
		<div class="title_right_block">
			<div class="profile_desc">
				<h3><?=$arResult["NAME"]?></h3>
				<p><?=$arResult["WORK_POSITION"]?><br /><?=$arResult["COMPANY"]["NAME"]?></p>
			</div><!-- END profile_desc -->
		</div><!-- END title_right_block -->
	</div><!-- END title_container -->
	<ul class="tabs">
		<li class="active"><a href="#">Пользователь</a></li>
		<?if ($arResult["EDITOR_IS_SUPER_USER"]):?>
			<li><a href="<?=$arParams["COMPANY_URL"]?>">Компания</a></li>
		<?endif?>
	</ul><!-- END tabs -->

	<?if ($arResult["SUCCESS"]):?>
		<div class="ui-alert ui-alert-success ui-alert-icon-success">
			<span class="ui-alert-message">Изменения сохранены</span>
		</div>
	<?elseif($arResult["ERROR"]):?>
		<div class="ui-alert ui-alert-danger ui-alert-icon-danger has-errors">
			<span class="ui-alert-message"><?=$arResult["ERROR"]?></span>
		</div>
	<?endif?>

	<form class="form_edit_profile" method="POST">
		<input type="hidden" name="FORM_SENT" value="Y">
		<?=bitrix_sessid_post();?>
		<div class="form_row">
			<div class="input_container column_2_3">
				<label class="label">Компания</label>
				<?if ($arResult["EDITOR_IS_SUPER_BROKER"] && $arResult["CHANGE_COMPANY"]):?>
					<select name="UF_COMPANY" class="select js_select">
						<?foreach ($arResult["COMPANY_LIST"] as $arCompany):?>
							<?$selected = $arResult["UF_COMPANY"] === $arCompany["ID"] ? "selected" : ""?>
							<option <?=$selected?> value="<?=$arCompany["ID"]?>"><?=$arCompany["NAME"]?></option>
						<?endforeach;?>
					</select><!-- END select -->
				<?else:?>
					<input class="text_input" type="text" disabled value="<?=$arResult["COMPANY"]["NAME"]?>">
				<?endif?>
			</div><!-- END input_container -->
			<? printInputProfile($arResult, "Должность", "WORK_POSITION", true) ?>
		</div><!-- END form_row -->
		<div class="form_row">

			<? printInputProfile($arResult, "ФИО", "NAME", true) ?>
			<? printInputProfile($arResult, "Логин", "LOGIN", true) ?>

			<div class="input_container column_3 superuser">
				<div class="flexbox">
					<label class="label">Суперпользователь</label>
						<?$checked = $arResult["IS_SUPER"] ? "checked" : ""?>
						<?$disabled = $arResult["EDITOR_IS_SUPER_BROKER"] ? "" : "disabled"?>
						<input <?=$checked?> <?=$disabled?> class="profile-checkbox" id="is_super_checkbox" name="IS_SUPER" type="checkbox" />
						<label for="is_super_checkbox">
					</label>
				</div><!-- END flexbox -->
				<a href="<?=$arParams["ACCESS_URL"]?>" class="btn white">Права доступа</a>
			</div><!-- END input_container -->
		</div><!-- END form_row -->
		<div class="form_row">
			<? printInputProfile($arResult, "Email", "EMAIL") ?>
			<? printInputProfile($arResult, "Телефон (мобильный)", "PERSONAL_PHONE", false) ?>
			<? printInputProfile($arResult, "Телефон (рабочий)", "WORK_PHONE", false, "tel", "tel") ?>
			<? printInputProfile($arResult, "Доб. номер", "WORK_FAX", false, "text", "small") ?>
		</div><!-- END form_row -->
		<div class="form_row">
			<div class="input_container change_password">
				<a href="#" onclick="$.fancybox.open($('#change_pass')); return false;" class="btn white">Смена пароля</a>
			</div><!-- END input_container -->
		</div><!-- END form_row -->
		<input type="submit" class="btn" value="Сохранить изменения" />
	</form><!-- END form_edit_profile -->
</div><!-- END wrapper -->


<div style="display: none;">
	<div class="popup small" id="change_pass">
		<h3 class="block_title">Смена пароля</h3>
		<form class="form_popup " method="POST">
			<input type="hidden" name="FORM_SENT" value="Y">
			<?=bitrix_sessid_post();?>

			<div class="form_row">
				<? printInputProfile($arResult, "Введите пароль", "PASSWORD", false, "password", "column_100") ?>
			</div><!-- END form_row -->
			<div class="form_row">
				<? printInputProfile($arResult, "Повторите пароль", "CONFIRM_PASSWORD", false, "password", "column_100") ?>
			</div><!-- END form_row -->
			<input type="submit" class="btn" value="Сохранить изменения" style="margin: 30px auto 0;" />
		</form>
	</div>
</div>
