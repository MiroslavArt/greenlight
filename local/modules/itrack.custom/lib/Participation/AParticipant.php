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
}
