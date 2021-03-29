<?php

namespace Itrack\Custom\Participation;

use Itrack\Custom\InfoBlocks\BaseInfoBlockClass;

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
			$curators = false;
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
}
