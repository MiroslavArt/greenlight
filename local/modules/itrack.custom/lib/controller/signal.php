<?php
namespace Itrack\Custom\Controller;

use Bitrix\Main;
use Bitrix\Crm;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Request;

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

    public function getUsersAction($company)
    {
        $result = [];

        $filter = Array
        (
            "ACTIVE" => "Y",
            "UF_COMPANY" => $company
        );

        $params = Array
        (
            'SELECT' =>  array("UF_*")
        );


        $rsUser = \CUser::GetList(($by="ID"), ($order="desc"), $filter, $params);
        // заносим прочие показатели
        $users = array();

        while ($ob = $rsUser->Fetch()) {
            $item = [];
            $item['value'] = $ob['ID'];
            $item['label'] = $ob['NAME'].' '.$ob['LAST_NAME'];
            $item['email'] = $ob['EMAIL'];
            $item['position'] = $ob['WORK_POSITION'];
            $item['wphone'] = $ob['WORK_PHONE'];
            $item['mphone'] = $ob['PERSONAL_MOBILE'];
            $item['isleader'] = false;

            array_push($result, $item);
        }
        return $result;
    }

    public function getContkuratorsAction($contract)
    {
        Loader::includeModule('iblock');
        $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_28", "PROPERTY_29");
        $arFilter = Array("IBLOCK_ID"=>2, "ID"=>26, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect)->fetch();

        $result = [
            //'broker' => [],
            //'insuer' => [],
            //'client' => []
        ];

        $leaders = $res['PROPERTY_29_VALUE'];

        foreach($res['PROPERTY_28_VALUE'] as $user) {
            $rsUser = \CUser::GetByID($user);
            $arUser = $rsUser->Fetch();
            $companyid = $arUser['UF_COMPANY'];

            $item = [];
            $item['value'] = $arUser['ID'];
            $item['label'] = $arUser['NAME'].' '.$arUser['LAST_NAME'];
            $item['email'] = $arUser['EMAIL'];
            $item['position'] = $arUser['WORK_POSITION'];
            $item['wphone'] = $arUser['WORK_PHONE'];
            $item['mphone'] = $arUser['PERSONAL_MOBILE'];
            $item['companyid'] = $companyid;
            if(in_array($user, $leaders)) {
                $item['isleader'] = true;
            } else {
                $item['isleader'] = false;
            }

            $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_TYPE");
            $arFilter = Array("IBLOCK_ID" => 1, "ID" => $companyid, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
            $res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 50), $arSelect);
            $company = $res->fetch();

            if ($company['PROPERTY_TYPE_ENUM_ID'] == 1) {
                $item['type'] = 'broker';
                //array_push($result['broker'], $item);
            } elseif ($company['PROPERTY_TYPE_ENUM_ID'] == 2) {
                $item['type'] = 'insuer';
                //array_push($result['insuer'], $item);
            } elseif ($company['PROPERTY_TYPE_ENUM_ID'] == 4) {
                $item['type'] = 'client';
                //array_push($result['client'], $item);
            }
            array_push($result, $item);
        }

        return $result;
    }


    public function addUserAction($userdata)
    {
        Loader::includeModule('iblock');
        $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_TYPE");
        $arFilter = Array("IBLOCK_ID" => 1, "ID" => $userdata['company'], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 50), $arSelect);
        $company = $res->fetch();
        $groups = array(5);
        if ($company['PROPERTY_TYPE_ENUM_ID'] == 1) {
            $type = 'СБ';
            array_push($groups, 7);
            if ($userdata['superuser']) {
                array_push($groups, 8);
            }
        } elseif ($company['PROPERTY_TYPE_ENUM_ID'] == 2) {
            $type = 'СК';
            array_push($groups, 11);
            if ($userdata['superuser']) {
                array_push($groups, 12);
            }
        } elseif ($company['PROPERTY_TYPE_ENUM_ID'] == 4) {
            $type = 'Клиент';
            array_push($groups, 9);
            if ($userdata['superuser']) {
                array_push($groups, 10);
            }
        } elseif ($company['PROPERTY_TYPE_ENUM_ID'] == 3) {
            $type = 'Аджастер';
            array_push($groups, 13);
            if ($userdata['superuser']) {
                array_push($groups, 14);
            }
        }

        $user = new \CUser;
        $arFields = Array(
            "NAME" => $userdata['name'],
            "LAST_NAME" => $userdata['lastname'],
            "SECOND_NAME" => $userdata['secondname'],
            "EMAIL" => $userdata['email'],
            "WORK_POSITION" => $userdata['position'],
            "PERSONAL_PHONE" => $userdata['persphone'],
            "WORK_PHONE" => $userdata['workphone'],
            "LOGIN" => $userdata['email'],
            "ACTIVE" => "Y",
            "GROUP_ID" => $groups,
            "PASSWORD" => $userdata['pwd'],
            "CONFIRM_PASSWORD" => $userdata['pwd'],
            "UF_COMPANY" => $userdata['company']
        );

        $ID = $user->Add($arFields);
        if(intval($ID) > 0) {
            // обновляем карточку договора
            $el = new \CIBlockElement;
            if($userdata['contract']) {
                $arSelect = Array("ID", "NAME", "PROPERTY_*");
                $arFilter = Array("IBLOCK_ID" => 2, "ID" => $userdata['contract'], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
                $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect)->fetch();
                $PROP = array();
                $PROP[4] = $res['PROPERTY_4'];
                $PROP[5] = $res['PROPERTY_5'];
                $PROP[6] = $res['PROPERTY_6'];
                $PROP[7] = $res['PROPERTY_7'];
                $PROP[8] = $res['PROPERTY_8'];
                $PROP[9] = $res['PROPERTY_9'];
                $PROP[9] = $res['PROPERTY_9'];
                $PROP[10] = $res['PROPERTY_10'];
                $PROP[11] = $res['PROPERTY_11'];
                $PROP[12] = $res['PROPERTY_12'];
                $PROP[13] = $res['PROPERTY_13'];
                $kontkurators = array();
                if ($res['PROPERTY_28']) {
                    $kontkurators = $res['PROPERTY_28'];
                }
                array_push($kontkurators, $ID);
                $PROP[28] = $kontkurators;
                $PROP[29] = $res['PROPERTY_29'];
                $arLoadContractArray = Array(
                    "PROPERTY_VALUES"=> $PROP
                );
                $res1 = $el->Update($res['ID'], $arLoadContractArray);
            }
            // добавляем участника урегулирования убытка
            if($userdata['loss']) {
                $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
                $arFilter = Array("IBLOCK_ID"=>3, "ID"=>$userdata['loss'], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
                $res2 = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect)->fetch();
                $name = $res2['NAME'].'-'.$type;
                $data = [
                    'IBLOCK_ID' => 4,
                    'ACTIVE' => 'Y',
                    'NAME' => $name,
                    'PROPERTY_VALUES' => [
                        'LOST'=> $userdata['loss'],
                        'COMPANY'=> $userdata['company'],
                        'CURATOR'=> $ID
                    ]
                ];
                $res3 = $el->Add($data);
            }
            $result = 'added';
        } else {
            $result = strip_tags($user->LAST_ERROR);
        }

        return $result;
    }
}

