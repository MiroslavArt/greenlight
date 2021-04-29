<?php

class ItrCompaniesList extends CBitrixComponent
{
    public function executeComponent()
    {
        $arCompany = \Itrack\Custom\CUserEx::getUserCompany();
        $url = "";
        switch($arCompany["PROPERTIES"]["TYPE"]["VALUE_XML_ID"]) {
            case "client":
                $url = "/clients/";
                break;

            case "insurer":
                $url = "/insurance-companies/";
                break;

            case "adjuster":
                $url = "/adjusters/";
                break;

            case "broker":
                $url = "/brokers/";
                break;

        }

        $id = $arCompany["ID"];
        $url .= "$id/";
        LocalRedirect($url);
    }
}
