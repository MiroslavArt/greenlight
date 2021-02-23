<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (
	isset($arResult["User"])
	&& is_array($arResult["User"])
	&& isset($arResult["User"]["PersonalPhotoImgThumbnail"])
	&& empty($arResult["User"]["PersonalPhotoImgThumbnail"]["Image"])
)
{
	$arResult["User"]["PersonalPhotoImgThumbnail"]["Image"] = '<img src="'.$this->GetFolder().'/images/nopic_30x30.gif" width="'.$arParams["THUMBNAIL_LIST_SIZE"].'" height="'.$arParams["THUMBNAIL_LIST_SIZE"].'">';
}


if ($arCompany = \Itrack\Custom\CUserEx::getUserCompany() ) {
    $arResult['USER_COMPANY'] = $arCompany;
    if (!empty($arCompany['PROPERTIES']['PROPERTY_LOGO']['VALUE']) && intval($arCompany['PROPERTIES']['PROPERTY_LOGO']['VALUE']) > 0 ) {
        $arResult["User"]["PersonalPhotoImgThumbnail"]["Image"] = '<img src="'. CFile::GetFileArray($arCompany['PROPERTIES']['PROPERTY_LOGO']['VALUE'])['SRC'] .'" width="'.$arParams["THUMBNAIL_LIST_SIZE"].'" height="'.$arParams["THUMBNAIL_LIST_SIZE"].'">';
    }
}

if($arGroups = \Itrack\Custom\CUserEx::getUserGroups()) {
    $currentGroup = array_pop($arGroups);
}

if (!empty($currentGroup['GROUP_DESCRIPTION']) && strlen($currentGroup['GROUP_DESCRIPTION']) > 0) {
    $arDescription = explode('|', $currentGroup['GROUP_DESCRIPTION']);
    if (count($arDescription) > 1) {
        $arResult['USER']['ROLE'] = $arDescription[0];
        $arResult['USER']['ROLE_STATUS'] = $arDescription[1];
    }
}