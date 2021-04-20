<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


if(!empty($arResult)) {
    foreach ($arResult as $arLink) {
        $arResult['LINKS'][] = $arLink;
    }
}

$arUser = CUser::GetByID($GLOBALS["USER"]->GetId())->GetNext();

if (
	isset($arResult["User"])
	&& is_array($arResult["User"])
	&& isset($arResult["User"]["PersonalPhotoImgThumbnail"])
	&& empty($arResult["User"]["PersonalPhotoImgThumbnail"]["Image"])
)
{
	$arResult["User"]["PersonalPhotoImgThumbnail"]["Image"] = '<img src="'.$this->GetFolder().'/images/nopic_30x30.gif" width="'.$arParams["THUMBNAIL_LIST_SIZE"].'" height="'.$arParams["THUMBNAIL_LIST_SIZE"].'">';
}

if(!empty($arUser['PERSONAL_PHOTO'])) {
    $arUser['PERSONAL_PHOTO']['SRC'] = CFile::GetFileArray($arUser['PERSONAL_PHOTO'])['SRC'];
}

if ($arCompany = \Itrack\Custom\CUserEx::getUserCompany() ) {
    $arResult['USER_COMPANY'] = $arCompany;

    if (!empty($arCompany['PROPERTIES']['LOGO']['VALUE']) && intval($arCompany['PROPERTIES']['LOGO']['VALUE']) > 0) {
        $arResult['COMPANY_LOGO']['SRC'] = CFile::GetFileArray($arCompany['PROPERTIES']['LOGO']['VALUE'])['SRC'];
    } else {
        $arResult['COMPANY_LOGO']['SRC'] = $this->GetFolder().'/images/nopic_30x30.gif';
    }
}

if(empty($arUser['PERSONAL_PHOTO']['SRC']) ) {
    $arUser['PERSONAL_PHOTO']['SRC'] = $this->GetFolder().'/images/nopic_user_100_noborder.gif';
}

$arResult['USER'] = $arUser;
$arResult['USER']["DETAIL_URL"] = '/settings/users/my/profile/';
$arResult['USER_COMPANY']["DETAIL_URL"] = '/settings/users/my/company/';
$arResult['USER']['NAME_FORMATTED'] = CUser::formatName('#NAME# #SECOND_NAME# #LAST_NAME#', $arUser, false, false);