<?php

namespace Itrack\Custom\Participation;

class CContract extends ATarget
{
	protected static $ibBlockCode = "company";

	public function checkAddArray(array $arAdd)
	{
		parent::checkAddArray($arAdd);

		// todo check contract-specific requirements, for example, absence of adjusters
	}

	public static function getParticipantClass(): string
	{
		return CContractParticipant::class;
	}
}
