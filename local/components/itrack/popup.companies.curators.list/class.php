<?php

use \Bitrix\Main\Application;
use \Bitrix\Main\Context;
use Itrack\Custom\CUserEx;
use \Itrack\Custom\CUserRole;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\Participation\AParticipant;
use Itrack\Custom\Participation\CContractParticipant;
use Itrack\Custom\Participation\CLostParticipant;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity;
use \Bitrix\Main\Entity\Query;
use \Bitrix\Main\Entity\ReferenceField;
use \Bitrix\Main\ORM\Query\Join;

class ItrCompaniesCuratorsListPopup extends CBitrixComponent
{
	private $userId;
	private $companyId;

	/** @var CUserRole $editorRole */
	private $userRole;

    public function executeComponent()
    {
		$this->userId = $GLOBALS["USER"]->GetID();
		$this->companyId = CUserEx::getUserCompanyId($this->userId);
		$this->userRole = new CUserRole($this->userId);


    	$request = Context::getCurrent()->getRequest();

		$targetId = $request->get("target-id");
		$targetType = $request->get("target-type");
		$parties = explode(",", $request->get("parties"));

		$this->arResult["TARGET_TYPE"] = $targetType;
		$this->arResult["PARTIES"] = $parties;

		$arParticipants = $this->getParticipants($targetId, $targetType);

		$result = [];
		if ($this->userHasAccess($arParticipants)) {
			$companyPartyCodesById = $this->getCompanyTypes();

			foreach ($arParticipants as $arParticipant) {
				$participantId = $arParticipant["PROPERTY_PARTICIPANT_ID_VALUE"];
				$partyId = $arParticipant["PROPERTY_PARTICIPANT_ID_PROPERTY_TYPE_ENUM_ID"];
				$party = $companyPartyCodesById[$partyId];

				if (!in_array($party, $parties)) {
					continue;
				}

				$result[$participantId] = [
					"NAME" => $arParticipant["PROPERTY_PARTICIPANT_ID_NAME"],
					"LOGO" => $arParticipant["PROPERTY_PARTICIPANT_ID_PROPERTY_LOGO_VALUE"]
				];
			}
		}


		$this->arResult["ITEMS"] = $result;

		$this->includeComponentTemplate();
    }

	private function getCompanyTypes() {
		$arParties = Company::getPropertyList("TYPE");

		$companyPartyCodesById = [];
		foreach ($arParties as $arParty) {
			$id = $arParty["ID"];
			$companyPartyCodesById[$id] = $arParty["XML_ID"];
		}

		return $companyPartyCodesById;
	}

    private function userHasAccess(array $arParticipants) {
    	if ($this->userRole->isSuperBroker()) {
			return true;
		}

		foreach ($arParticipants as $arParticipant) {
			$participantId = $arParticipant["PROPERTY_PARTICIPANT_ID_VALUE"];

			$curator = $arParticipant["PROPERTY_CURATORS_VALUE"];
			$curatorLeader = $arParticipant["PROPERTY_CURATOR_LEADER_VALUE"];

			$participationConfirmed = $this->userRole->isSuperUser()
				? $this->companyId == $participantId
				: $this->userId == $curator || $this->userId == $curatorLeader
			;

			if ($participationConfirmed) {
				return true;
			}
		}

		return false;
	}


	private function getParticipants(int $lostId, string $targetType) {
    	switch ($targetType) {
			case "contract":
				$participant = CContractParticipant::class;
				break;
			case "lost":
				$participant = CLostParticipant::class;
				break;
			default:
				throw new Exception("Invalid participant type");
		}

		/** @var AParticipant $participant */
		return $participant::getElementsByConditions([
			"PROPERTY_TARGET_ID" => $lostId,
		],
		[],
		[
			"ID",
			"PROPERTY_PARTICIPANT_ID",
			"PROPERTY_PARTICIPANT_ID.NAME",
			"PROPERTY_PARTICIPANT_ID.PROPERTY_TYPE",
			"PROPERTY_PARTICIPANT_ID.PROPERTY_LOGO",
			"PROPERTY_CURATORS",
			"PROPERTY_CURATOR_LEADER",
		]);
	}
}
