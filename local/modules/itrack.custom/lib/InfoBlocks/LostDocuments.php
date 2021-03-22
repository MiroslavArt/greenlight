<?php

namespace Itrack\Custom\InfoBlocks;

\Bitrix\Main\Loader::includeModule('iblock');

use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\BaseInfoBlockClass as BaseClass;

class LostDocuments extends BaseClass
{
    protected static $selectElement =   ['ID', 'IBLOCK_ID', 'ACTIVE', 'SORT', 'IBLOCK_SECTION', 'CODE', 'DATE_CREATE', 'TIMESTAMP_X', 'NAME', 'PREVIEW_TEXT', 'DETAIL_PAGE_URL', 'PROPERTY_*'];
    protected static $ibBlockCode   =   'lost_doc';
    protected static $ibBlockType   =   'main_data';

}