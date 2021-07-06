<?php

namespace Itrack\Custom\Participation;

use Itrack\Custom\InfoBlocks\BaseInfoBlockClass;
use Itrack\Custom\InfoBlocks\Company;

abstract class AParticipant extends BaseInfoBlockClass implements IParticipant
{
	/**
	 * @var int
	 */
	protected $participantId;

	protected static $ibBlockType = "main_data";

	public function __construct(int $participantId) {
		$this->participantId = $participantId;
	}

	public static function initByTargetAndCompany(int $targetId, int $companyId): ?IParticipant {
		$res = self::getElementsByConditions([
			"PROPERTY_TARGET_ID" => $targetId,
			"PROPERTY_PARTICIPANT_ID" => $companyId
		]);

		if (!count($res)) {
			return null;
		}

		return new static($res[0]["ID"]);
	}

	public function bindCurator(int $userId, bool $isLeader = false) {
		$participation = $this->fetchParticipation();

		$propValues = [];

		$curators = $participation["PROPERTIES"]["CURATORS"]["VALUE"];
		$curators[] = $userId;
		$propValues["CURATORS"] = array_unique($curators);

		if ($isLeader) {
			$propValues["CURATOR_LEADER"] = $userId;
		}

		\CIBlockElement::SetPropertyValuesEx($participation["ID"], false, $propValues);
	}

	public function unbindCurator(int $userId) {
		$participation = $this->fetchParticipation();

		$curators = $participation["PROPERTIES"]["CURATORS"]["VALUE"];
		$curators = array_unique(array_diff($curators, [$userId]));

		if (count($curators) <= 0) {
			throw new \DomainException("There must be at least one curator from company.");
		}

		$leader = $participation["PROPERTIES"]["CURATOR_LEADER"]["VALUE"];

		if ($leader == $userId) {
			$leader = false;
		}

		$propValues = [
			"CURATORS" => $curators,
			"CURATOR_LEADER" => $leader
		];

		\CIBlockElement::SetPropertyValuesEx($participation["ID"], false, $propValues);
	}

	private function fetchParticipation(): ?array {
		$res = static::getElementsByConditions(
			["ID" => $this->participantId],
			[],
			["ID", "IBLOCK_ID", "PROPERTY_CURATORS", "PROPERTY_CURATOR_LEADER"]
		);

		return $res ? $res[0] : null;
	}

	public static function getLeaders(array $arIds) {
		$arParties = Company::getPropertyList("TYPE");

		$companyPartyCodesById = [];
		foreach ($arParties as $arParty) {
			$id = $arParty["ID"];
			$companyPartyCodesById[$id] = $arParty["XML_ID"];
		}


		$arLeaders = self::getElementsByConditions([
			"PROPERTY_TARGET_ID" => $arIds,
			"!PROPERTY_IS_LEADER" => false,
		],
			[],
			[
				"PROPERTY_TARGET_ID",
				"PROPERTY_PARTICIPANT_ID.NAME",
				"PROPERTY_PARTICIPANT_ID.PROPERTY_LOGO",
				"PROPERTY_PARTICIPANT_ID.PROPERTY_TYPE"
			]);


		$result = [];
		foreach ($arLeaders as $arLeader) {
			$partyId = $arLeader["PROPERTY_PARTICIPANT_ID_PROPERTY_TYPE_ENUM_ID"];
			$targetId = $arLeader["PROPERTY_TARGET_ID_VALUE"];
			$party = $companyPartyCodesById[$partyId];

			$result[$targetId][$party] = [
				"NAME" => $arLeader["PROPERTY_PARTICIPANT_ID_NAME"],
				"LOGO" => $arLeader["PROPERTY_PARTICIPANT_ID_PROPERTY_LOGO_VALUE"],
			];
		}

		return $result;
	}
}
