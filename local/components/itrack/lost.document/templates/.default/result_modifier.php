<?
use Itrack\Custom\Participation\CLost;
use Itrack\Custom\Participation\CParticipation;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
// workflow: проверка, что пользователь - клиент, суперпользователь клиента
global $USER;
$uid = $USER->getID();
$arGroups = CUser::GetUserGroup($uid);
$lostid = $arResult['DOCUMENT']['PROPERTY_23'];
$participation = new CParticipation(new CLost($lostid));
$partips = $participation->getParticipants();
$iskurator = false;

$arResult['CURUSER'] = $uid;
$arResult['ISCLIENT'] = false;
$arResult['ISCLIENTSUPUSER'] = false;
$arResult['SHOWACCEPT'] = false;
$arResult['SHOWDECLINE'] = false;

foreach($partips as $partip) {
    $curators = $partip['PROPERTIES']['CURATORS']['VALUE'];
    if(in_array($uid, $curators)) {
        $iskurator = true;
        break;
    }
}

if($iskurator==true) {
    if(in_array(CL_GROUP, $arGroups)) {
        $arResult['ISCLIENT'] = true;
    }
    if(in_array(CL_SU_GROUP, $arGroups)) {
        $arResult['ISCLIENTSUPUSER'] = true;
    }
}

// открываем кнопку акцепта, если документ загрузил куратор клиента
if($arResult['ISCLIENT'] && $arResult['DOCUMENT']['PROPERTIES']['STATUS']['VALUE']['ID']==1) {
    $arResult['SHOWACCEPT'] = true;
}

\Bitrix\Main\Diag\Debug::writeToFile($arResult, "result", "__miros.log");
