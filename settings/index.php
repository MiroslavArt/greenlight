<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?>


	<div class="authorization_container settings_page wrapper">
		<div class="index_content">
			<div class="tiles_container">
				<? $userRoles = new \Itrack\Custom\CUserRole(); ?>

				<?if ($userRoles->isSuperUser()):?>
					<a href="/settings/users" class="tile_link ico_6">Пользователи</a>
				<?endif?>
				<?if ($userRoles->isSuperBroker()):?>
					<a href="/settings/statuses" class="tile_link ico_7">Статусы</a>
				<?endif?>
				<?if ($userRoles->isSuperBroker() || $userRoles->isSuperClient()):?>
					<a href="/settings/notifications" class="tile_link ico_8">Уведомления</a>
				<?endif?>
				<a href="/settings/profile" class="tile_link ico_9">Редактирование профиля</a>
			</div><!-- END tiles_container -->
		</div><!-- END index_content -->
	</div><!-- END authorization_container -->

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
