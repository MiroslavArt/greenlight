<?php
namespace Itrack\Custom\Controller;

use Bitrix\Main;
use Bitrix\Crm;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

class Signal extends Controller
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        $this->checkModules();

    }

    /**
     * @throws Main\LoaderException
     */
    protected function checkModules()
    {
        if (!Loader::includeModule('iblock')) {
            throw new Main\LoaderException('not install module iblock');
        }

        if (!Loader::includeModule('itrack.custom')) {
            throw new Main\LoaderException('not install module itrack.custom');
        }

    }


    public function getSignalAction($location)
    {
        //$signalarr = [1,2,3];
        return $location;
    }

    public function getCompaniesAction($type)
    {
        $result = [];

        $arSelect = Array("ID", "NAME");
        $arFilter = Array("IBLOCK_ID"=>1, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_TYPE"=>$type);
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        while($ob = $res->fetch())
        {
            $item = [];
            $item['value'] = $ob['ID'];
            $item['label'] = $ob['NAME'];

            array_push($result, $item);
        }

        return $result;
    }
}

