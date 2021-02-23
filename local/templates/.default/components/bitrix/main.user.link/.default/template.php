<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

if($arResult["FatalError"] <> '')
{
	?><span class='errortext'><?=$arResult["FatalError"]?></span><br /><br /><?
}
else
{
    ?>
    <a href="<?= $arResult["User"]["DETAIL_URL"] ?>"><?= $arResult["User"]["PersonalPhotoImgThumbnail"]["Image"] ?></a>
    <div class="tile_content">
        <span class="who"><?=$arResult['USER']['ROLE']?></span>
        <h2 class="name"><?= $arResult["User"]["NAME_FORMATTED"] ?></h2>
        <span class="company"><?=(!empty($arResult['USER_COMPANY']['NAME']) ? $arResult['USER_COMPANY']['NAME'] : '')?></span>
        <span class="access"><?=(!empty($arResult['USER']['ROLE_STATUS']) ? 'Права доступа «' . $arResult['USER']['ROLE_STATUS'] . '»' : '' )?></span>
    </div><!-- END tile_content -->

    <a href="<?=$APPLICATION->GetCurPageParam("logout=yes&".bitrix_sessid_get(), array("login", "logout", "register", "forgot_password", "change_password"));?>" class="logout"></a>

    <?php
}
?>