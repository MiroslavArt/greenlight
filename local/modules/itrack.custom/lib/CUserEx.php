<?php

namespace Itrack\Custom;

use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc;

use Itrack\Custom\Helpers\Utils;
use Itrack\Custom\InfoBlocks\Company;


class CUserEx
{
    const BROKER = 'broker';
    const BROKER_SUPERUSER = 'broker_superuser';

    /**
     * Группы, в которых участвует пользователь
     * @return array
     */
    public static function getUserGroups() {

        $arResult = [];

        $result = \Bitrix\Main\UserGroupTable::getList(array(
            'filter' => array('USER_ID'=>$GLOBALS["USER"]->GetID(),'GROUP.ACTIVE'=>'Y'),
            'select' => array('GROUP_ID','GROUP_CODE'=>'GROUP.STRING_ID', 'GROUP_DESCRIPTION'=>'GROUP.DESCRIPTION'),
            'order' => array('GROUP.C_SORT'=>'ASC'),
        ));

        while ($arGroup = $result->fetch()) {
            $arResult[$arGroup['GROUP_ID']] = $arGroup;
        }
        return $arResult;
    }

    /**
     * Компания пользователя
     * @return array|bool
     */
    public static function getUserCompany() {
        $arUser = \CUser::GetByID($GLOBALS["USER"]->GetId())->GetNext();

        if(empty($arUser['UF_COMPANY']) || intval($arUser['UF_COMPANY']) <= 0) {
            return false;
        }

        return Company::getElementByID($arUser['UF_COMPANY']);

    }
}