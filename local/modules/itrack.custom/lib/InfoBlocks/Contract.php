<?php

namespace Itrack\Custom\InfoBlocks;

\Bitrix\Main\Loader::includeModule('iblock');

use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\BaseInfoBlockClass as BaseClass;

class Contract extends BaseClass
{
    protected static $selectElement =   ['ID', 'IBLOCK_ID', 'ACTIVE', 'SORT', 'IBLOCK_SECTION', 'CODE', 'NAME', 'PREVIEW_TEXT', 'DETAIL_PAGE_URL', 'PROPERTY_*', 'PROPERTY_INSURANCE_COMPANY_LEADER.NAME'];
    protected static $ibBlockCode   =   'contract';
    protected static $ibBlockType   =   'main_data';

}