<?php

namespace Itrack\Custom\Participation;

interface ITarget
{
	public function getId();

	public function checkAddArray(array $arAdd);

	public static function getParticipantClass(): string;
}
