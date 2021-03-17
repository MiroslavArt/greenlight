<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
use Bitrix\Main\Loader;

Loader::includeModule("highloadblock");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$hlbl = 3; // Указываем ID нашего highloadblock блока к которому будет делать запросы.
$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();

$rsData = $entity_data_class::getList(array(
    "select" => array("*"),
    "order" => array("ID" => "ASC"),
    "filter" => array()  // Задаем параметры фильтра выборки
));

$arResult['INSTYPES'] = $rsData->fetchAll();


Loader::includeModule('iblock');
$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_TYPE");
$arFilter = Array("IBLOCK_ID"=>1, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_TYPE"=>1);
$res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
$broker = $res->fetch();
$arResult['BROKER'] = $broker['NAME'];


