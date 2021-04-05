<?php

namespace Itrack\Custom\InfoBlocks;

\Bitrix\Main\Loader::includeModule('iblock');

use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\BaseInfoBlockClass as BaseClass;

class NotificationTemplate extends BaseClass
{
    protected static $ibBlockCode   =   'notification_templates';
    protected static $ibBlockType   =   'main_data';

}
