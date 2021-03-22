<?php

use \Bitrix\Main\UserTable;
use \Bitrix\Main\GroupTable;
use \Bitrix\Main\Context;
use Itrack\Custom\CUserEx;
use \Itrack\Custom\CUserRole;
use \Itrack\Custom\InfoBlocks\Company;

class ItrCompanyProfile extends CBitrixComponent
{
	/** @var CUserRole $editorRole */
	private $editorRole;

	private $arFields = [
		"NAME" => "super_user"
	];

	private $arProperties = [
		"TYPE" => "super_broker",
		"LOGO" => "super_user",
		"FULL_NAME" => "super_user",
		"LEGAL_ADDRESS" => "super_user",
		"ACTUAL_ADDRESS" => "super_user",
	];


    public function executeComponent()
    {
		$userId = $this->arParams["USER_ID"];
		$companyId = CUserEx::getUserCompanyId($userId);

		$this->editorRole = new CUserRole();
		$editorId = $GLOBALS["USER"]->GetID();
		$editorCompanyId = CUserEx::getUserCompanyId($editorId);

		$userHasAccess =
			$this->editorRole->isSuperBroker() ||
			$this->editorRole->isSuperUser() && $companyId === $editorCompanyId;

		if (!$userHasAccess) LocalRedirect("/");


    	$request = Context::getCurrent()->getRequest();

		$formSent = $request->get("FORM_SENT") === "Y" && check_bitrix_sessid();

		if ($formSent) {
			$updateResult = $this->updateCompany($companyId, $request);
		}


		$this->arResult = Company::getElementByID($companyId);

		$this->arResult["COMPANY_TYPES"] = $this->getCompanyTypes($this->arResult["PROPERTIES"]["TYPE"]["ID"]);

		$this->arResult["USER"] = \CUser::GetByID($userId)->GetNext();
		$this->arResult["SUCCESS"] = $updateResult["SUCCESS"];
		$this->arResult["ERROR"] = $updateResult["ERROR"];
		$this->arResult["IS_SUPER"] = (new CUserRole($userId))->isSuperUser();
		$this->arResult["EDITOR_IS_SUPER_USER"] = $this->editorRole->isSuperUser();
		$this->arResult["EDITOR_IS_SUPER_BROKER"] = $this->editorRole->isSuperBroker();

		$this->includeComponentTemplate();
    }

    private function getCompanyTypes($propId)
	{
		$res = \CIBlockPropertyEnum::GetList(
			["SORT"=>"ASC", "VALUE"=>"ASC"],
			["PROPERTY_ID" => $propId]
		);

		$values = [];

		while ($row = $res->GetNext()) {
			$values[] = $row;
		}

		return $values;
	}

    private function updateCompany($companyId, $request) {
		$arValues = [];

		foreach ($this->arFields as $field => $role) {
			$skipField =
				$role === "super_broker" && !$this->editorRole->isSuperBroker() ||
				$role === "super_user"   && !$this->editorRole->isSuperUser();

			if ($skipField) continue;


			$value = $request->get($field);

			if (is_null($value)) continue;


			$arValues[$field] = $value;
		}

		$iblock = new CIblockElement();

		if (!$iblock->Update($companyId, $arValues)) {
			return [
				"SUCCESS"=> false,
				"ERROR" => $iblock->LAST_ERROR
			];
		}


		$arValues = [];

		foreach ($this->arProperties as $prop => $role) {
			$skipField =
				$role === "super_broker" && !$this->editorRole->isSuperBroker() ||
				$role === "super_user"   && !$this->editorRole->isSuperUser();

			if ($skipField) continue;


			if ($prop === "LOGO") {
				$value = $request->getFile("LOGO");

				if (!is_array($value) || $value["error"] != 0) continue;

				$mimeType = mime_content_type($value["tmp_name"]);
				if (strpos($mimeType, "image/") !== 0) continue;
			}
			else {
				$value = $request->get($prop);

				if (is_null($value)) continue;
			}


			$arValues[$prop] = $value;
		}

		CIBlockElement::SetPropertyValuesEx($companyId, false, $arValues);

		return [ "SUCCESS" => true ];
	}
}
