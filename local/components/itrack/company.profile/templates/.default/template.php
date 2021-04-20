<?php
\Bitrix\Main\UI\Extension::load("ui.alerts");

if (!function_exists("printInputCompany")) {
	function printInputCompany($arResult, $title, $name, $isProp = true) {
		?>
		<div class="input_container">
			<label class="label"><?=$title?></label>
			<?$value = $isProp ? $arResult["PROPERTIES"][$name]["VALUE"] : $arResult[$name]?>
			<input type="text" class="text_input" name="<?=$name?>" value="<?=$value?>" />
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
				<h3><?=$arResult["USER"]["NAME"]?></h3>
				<p><?=$arResult["USER"]["WORK_POSITION"]?><br /><?=$arResult["NAME"]?></p>
			</div><!-- END profile_desc -->
		</div><!-- END title_right_block -->
	</div><!-- END title_container -->
	<ul class="tabs">
		<li><a href="<?=$arParams["PROFILE_URL"]?>">Пользователь</a></li>
		<li class="active"><a href="#">Компания</a></li>
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

	<form class="form_edit_profile" method="post" enctype="multipart/form-data">
		<input type="hidden" name="FORM_SENT" value="Y">
		<?=bitrix_sessid_post();?>

		<div class="form_row">
			<?printInputCompany($arResult, "Название компании", "NAME", false)?>
			<?printInputCompany($arResult, "Полное наименование (Юридическое наименование)", "FULL_NAME")?>
		</div><!-- END form_row -->
		<div class="form_row">
			<?printInputCompany($arResult, "Юридический адрес", "LEGAL_ADDRESS")?>
			<?printInputCompany($arResult, "Почтовый адрес", "ACTUAL_ADDRESS")?>
		</div><!-- END form_row -->
        <div class="form_row">
            <?printInputCompany($arResult, "ИНН", "INN")?>
            <?printInputCompany($arResult, "КПП", "KPP")?>
        </div><!-- END form_row -->
		<div class="form_row">

			<div class="input_container column_3">
				<label class="label">Роль компании</label>
				<?if ($arResult["EDITOR_IS_SUPER_BROKER"] && $arResult["CHANGE_COMPANY_TYPE"]):?>
					<select name="TYPE" class="select js_select">
						<?foreach ($arResult["COMPANY_TYPES"] as $arType):?>
							<?$selected = $arResult["PROPERTIES"]["TYPE"]["VALUE_ENUM_ID"] === $arType["ID"] ? "selected" : ""?>
							<option <?=$selected?> value="<?=$arType["ID"]?>"><?=$arType["VALUE"]?></option>
						<?endforeach;?>
					</select><!-- END select -->
				<?else:?>
					<input class="text_input" type="text" disabled value="<?=$arResult["PROPERTIES"]["TYPE"]["VALUE"]?>">
				<?endif?>
			</div><!-- END input_container -->

			<div class="logo_upload_container column_3">
				<img class="logo-preview" src="<?=$arResult["PROPERTIES"]["LOGO"]["SRC"]?>" width="40" height="40" alt="img1" style="object-fit: cover;" />
				<div class="logo_upload">
					<input type="file" name="LOGO" />
					<span class="upload"><span>Загрузить лого</span></span>
				</div><!-- END logo_upload -->
			</div><!-- END logo_upload_container -->

		</div><!-- END form_row -->
		<div class="form_row">
			<div class="input_container column_3">
				<a href="<?=$arParams["LIST_URL"]?>" class="btn white">Все пользователи компании</a>
			</div><!-- END input_container -->
		</div><!-- END form_row -->
		<input type="submit" class="btn" value="Сохранить изменения" />
	</form><!-- END form_edit_profile -->
</div><!-- END wrapper -->

