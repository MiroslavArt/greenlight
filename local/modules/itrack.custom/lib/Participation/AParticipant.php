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
}
