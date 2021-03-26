<?php

namespace Itrack\Custom\InfoBlocks;

\Bitrix\Main\Loader::includeModule('iblock');

use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\BaseInfoBlockClass as BaseClass;

class Company extends BaseClass
{
    protected static $selectElement =   ['ID', 'IBLOCK_ID', 'ACTIVE', 'SORT', 'IBLOCK_SECTION', 'CODE', 'NAME', 'PREVIEW_TEXT', 'DETAIL_PAGE_URL', 'PROPERTY_*'];
    protected static $ibBlockCode   =   'company';
    protected static $ibBlockType   =   'main_data';

    public static function getFilteredList(array $arFilter = []) {

        $arFilter['=ACTIVE'] = 'Y';

        $elements = \Bitrix\Iblock\Elements\ElementCompanyTable::getList([
            'select' => ['ID', 'NAME', 'LOGO.FILE', 'TYPE.ITEM'],
            'filter' => $arFilter,
            'cache' => ['ttl' => 3600],
        ])->fetchCollection();

        return $elements;
    }

    public static function getElementsByConditions(array $filter = [], array $sort = [], array $selectElement = [], string $detailUrlTemplate = ''):array
    {
        $key        =   0;
        $elements   =   [];

        if (empty($selectElement)) {
            $arSelect = static::$selectElement;
        } else {
            $arSelect = $selectElement;
        }

        if (empty($sort)) {
            $arOrder = static::$sort;
        } else {
            $arOrder = $sort;
        }

        $arFilter = array_merge(['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType)], $filter);

        $elementsList = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

        $elementsList->SetUrlTemplates($detailUrlTemplate);

        while ($el = $elementsList->GetNextElement()) {
            $elements[$key]                 =   $el->GetFields();
            $elements[$key]['PROPERTIES']   =   $el->GetProperties();
            $key++;
        }

        return $elements;
    }

    public static function getPartyByCompany($companyId) {
    	$arCompany = self::getElementsByConditions(["ID" => $companyId])[0];

    	return $arCompany['PROPERTIES']['TYPE']['VALUE_XML_ID'];
	}

	public static function getParties(array $companyIds) {
		$arCompanies = self::getElementsByConditions(["ID" => $companyIds]);

		$arParties = [];

		foreach ($arCompanies as $arCompany) {
			$companyId = $arCompany['ID'];
			$party = $arCompany['PROPERTIES']['TYPE']['VALUE_XML_ID'];
			$arParties[$companyId] = $party;
		}

		return $arParties;
	}
}
