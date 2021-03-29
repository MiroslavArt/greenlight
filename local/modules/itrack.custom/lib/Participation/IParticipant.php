<?php

namespace Itrack\Custom\Participation;

interface IParticipant
{
	public function __construct(int $participantId);

	public static function initByTargetAndCompany(int $targetId, int $companyId): ?IParticipant;

	public function bindCurator(int $userId, bool $isLeader = false);
	public function unbindCurator(int $userId);
}
