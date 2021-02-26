<?php

namespace Itrack\Custom\InfoBlocks;

use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\BaseInfoBlockClass as BaseClass;

class Company extends BaseClass
{
    protected static $selectElement =   ['ID', 'IBLOCK_ID', 'ACTIVE', 'SORT', 'IBLOCK_SECTION', 'CODE', 'NAME', 'PREVIEW_TEXT', 'PROPERTY_*'];
    protected static $ibBlockCode   =   'company';
    protected static $ibBlockType   =   'main_data';

    public static function getFilteredList(array $arFilter = []) {

        $arFilter['=ACTIVE'] = 'Y';

        $elements = \Bitrix\Iblock\Elements\ElementCompanyTable::getList([
            'select' => ['ID', 'NAME', 'PROPERTY_LOGO.FILE'],
            'filter' => $arFilter,
            'cache' => ['ttl' => 3600],
        ])->fetchCollection();

        return $elements;
    }

}