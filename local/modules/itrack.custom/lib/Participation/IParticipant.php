<?php

namespace Itrack\Custom\Participation;

interface IParticipant
{
	public function __construct(int $participantId);

	public static function initByTargetAndCompany(int $targetId, int $companyId): ?IParticipant;
}
