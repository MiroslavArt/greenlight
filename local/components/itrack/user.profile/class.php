<?php

use \Bitrix\Main\UserTable;
use \Bitrix\Main\GroupTable;
use \Bitrix\Main\Context;
use Itrack\Custom\CUserEx;
use \Itrack\Custom\CUserRole;
use \Itrack\Custom\InfoBlocks\Company;

class ItrUserProfile extends CBitrixComponent
{
	/** @var CUserRole $editorRole */
	private $editorRole;

	private $arFields = [
		"LOGIN" => "super_user",
		"EMAIL" => "user",
		"NAME" => "super_user",
		"PERSONAL_MOBILE" => "user",
		"WORK_POSITION" => "super_user",
		"WORK_PHONE" => "user",
        "PERSONAL_PHONE" => "user",
		"WORK_FAX" => "user",
		"UF_COMPANY" => "super_broker",
		"PASSWORD" => "user",
		"CONFIRM_PASSWORD" => "user"
	];


    public function executeComponent()
    {
		$userId = $this->arParams["USER_ID"];
		$companyId = CUserEx::getUserCompanyId($userId);

		$this->editorRole = new CUserRole();
		$editorId = $GLOBALS["USER"]->GetID();
		$editorCompany = CUserEx::getUserCompanyId($editorId);

		$userHasAccess =
			$editorId === $userId ||
			$this->editorRole->isSuperUser() && $editorCompany === $companyId && $companyId ||
			$this->editorRole->isSuperBroker()
		;

		if (!$userHasAccess) LocalRedirect("/");


    	$request = Context::getCurrent()->getRequest();

		$formSent = $request->get("FORM_SENT") === "Y" && check_bitrix_sessid();

		if ($formSent) {
			$this->updateUserSuperpower($userId, $companyId, $request);
			$this->updateUserFields($userId, $request);
		}


		$arUser = \CUser::GetByID($userId)->GetNext();
		foreach($this->arFields as $code => $role) {
			if ($code === "PASSWORD") continue;
			$this->arResult[$code] = $arUser[$code];
		}

		$companyList = Company::getElements();
		$this->arResult["COMPANY_LIST"] = $companyList;

		foreach ($companyList as $arCompany) {
			if ($arCompany["ID"] === $this->arResult["UF_COMPANY"]) {
				$this->arResult["COMPANY"] = $arCompany;
				break;
			}
		}

		$this->arResult["IS_SUPER"] = (new CUserRole($userId))->isSuperUser();
		$this->arResult["EDITOR_IS_SUPER_USER"] = $this->editorRole->isSuperUser();
		$this->arResult["EDITOR_IS_SUPER_BROKER"] = $this->editorRole->isSuperBroker();

		$this->includeComponentTemplate();
    }

    private function updateUserFields($userId, $request) {


        $arValues = [];

		foreach ($this->arFields as $fieldCode => $role) {

			$skipField =
				$role === "super_broker" && !$this->editorRole->isSuperBroker() ||
				$role === "super_user"   && !$this->editorRole->isSuperUser();

			if ($skipField) continue;


			$value = $request->get($fieldCode);

			if (strpos($fieldCode, "PASSWORD") !== false && empty($value)) continue;
			if (is_null($value)) continue;

			$arValues[$fieldCode] = $value;
		}

		if (empty($arValues)) return;

		$user = new CUser();

		if ($user->Update($userId, $arValues)) {
			$this->arResult["SUCCESS"] = true;
		}
		else {
			$this->arResult["SUCCESS"] = false;
			$this->arResult["ERROR"] = $user->LAST_ERROR;
		}
	}

	private function updateUserSuperpower($userId, $companyId, $request) {
		if (!$this->editorRole->isSuperBroker()) return;
		if ($userId === $GLOBALS["USER"]->GetID) return;


    	$editedUserRole = new CUserRole($userId);
		$superWas = $editedUserRole->isSuperUser();
		$superSwitchedTo = !!$request->get("IS_SUPER");

		if ($superSwitchedTo === $superWas) return;

		$party = Company::getPartyByCompany($companyId);

		$editedUserRole->setUserRole($party, $superSwitchedTo);
	}
}
