<?php

namespace Itrack\Custom\Controllers;

use Bitrix\Main;
use Bitrix\Main\Engine;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Request;
use Bitrix\Sale\Delivery;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

use Itrack\Custom\Helpers;

class Test extends Engine\Controller
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

    /*public function configureActions()
    {
        return [
            'location' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf()
                ],
                'postfilters' => []
            ],
        ];
    }


    public function locationAction($location = 0)
    {
        $result = [];

        $result['city'] = $location;

        return $result ?? [];
    }*/

    public function getSignalAction($location)
    {
        //$signalarr = [1,2,3];
        return $location;
    }

}
