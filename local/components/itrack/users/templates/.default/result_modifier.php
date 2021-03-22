<?php
\Bitrix\Main\Loader::includeModule('iblock');
$companies = [];
$inscontracts = [];
$losses = [];
$arSelect = Array("ID", "NAME");
$arFilter = Array("IBLOCK_ID"=>1, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
$res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->fetch())
{
    array_push($companies, $ob);
}

$arSelect = Array("ID", "NAME");
$arFilter = Array("IBLOCK_ID"=>2, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
$res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->fetch())
{
    array_push($inscontracts, $ob);
}

$arSelect = Array("ID", "NAME");
$arFilter = Array("IBLOCK_ID"=>5, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
$res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->fetch())
{
    array_push($losses, $ob);
}

$set_password = randString(7);
$arResult['COMPANIES'] = $companies;
$arResult['CONTRACTS'] = $inscontracts;
$arResult['LOSSES'] = $losses;
$arResult['PWD'] = $set_password;