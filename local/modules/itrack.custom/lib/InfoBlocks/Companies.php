<?php

namespace Itrack\Custom\InfoBlocks;

use Itrack\Custom\InfoBlocks\BaseInfoBlockClass as BaseClass;

class Companies extends BaseClass
{
    protected static $selectElement =   ['ID', 'IBLOCK_ID', 'ACTIVE', 'SORT', 'IBLOCK_SECTION', 'CODE', 'NAME', 'PREVIEW_TEXT', 'PROPERTY_*'];
    protected static $ibBlockCode   =   'companies';
    protected static $ibBlockType   =   'main_data';
}