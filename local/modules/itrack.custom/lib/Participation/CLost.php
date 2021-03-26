<?php

namespace Itrack\Custom\Participation;

class CLost extends ATarget
{
	protected static $ibBlockCode = "lost";

	public static function getParticipantClass(): string
	{
		return CLostParticipant::class;
	}
}
