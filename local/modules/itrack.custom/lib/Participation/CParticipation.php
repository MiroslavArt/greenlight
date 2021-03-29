<?php

namespace Itrack\Custom\Participation;

class CParticipation
{
	/** @var ITarget */
	private $target;

	public function __construct(ITarget $target)
	{
		$this->target = $target;
	}

	public function getParticipants() {
		/** @var AParticipant $participant */
		$participant = $this->target::getParticipantClass();

		// todo format data as you need
		return $participant::getElementsByConditions(["PROPERTY_TARGET_ID" => $this->target->getId()]);
	}

	public function createFromArrays(array $companies, array $companiesLeaders, array $curators, array $curatorsLeaders) {
		$curators = array_unique(array_merge($curators, $curatorsLeaders));
		$companies = array_unique(array_merge($companies, $companiesLeaders));

		$curatorsCompanies = \Itrack\Custom\CUserEx::getUsersCompanies($curators);
		$partiesOfCompanies = \Itrack\Custom\InfoBlocks\Company::getParties($companies);


		$curatorsByCompanies = [];
		$leadersByCompanies = [];
		foreach ($curatorsCompanies as $userId => $companyId) {
			$curatorsByCompanies[$companyId][] = $userId;

			$isLeader = in_array($userId, $curatorsLeaders);

			if ($isLeader) {
				$leadersByCompanies[$companyId][] = $userId;
			}
		}

		$companiesByParties = [];
		foreach ($partiesOfCompanies as $companyId => $party) {
			$companiesByParties[$party][$companyId] = [
				"IS_LEADER" => in_array($companyId, $companiesLeaders),
				"CURATORS" => $curatorsByCompanies[$companyId],
				"LEADERS" => $leadersByCompanies[$companyId]
			];
		}

		$this->target->checkAddArray($companiesByParties);


		/** @var AParticipant $participant */
		$participant = $this->target::getParticipantClass();

		$participantIsLeaderPropValueId = $participant::getProperty("IS_LEADER")["ID"];

		foreach ($companiesByParties as $party => $arCompanies) {
			foreach ($arCompanies as $copmanyId => $arCompany) {
				$fields = [
					"NAME" => "Участие $copmanyId в {$this->target->getId()}"
				];

				$props = [
					"TARGET_ID" => $this->target->getId(),
					"PARTICIPANT_ID" => $copmanyId,
					"IS_LEADER" => $arCompany["IS_LEADER"] ? $participantIsLeaderPropValueId : false,
					"CURATORS" => $arCompany["CURATORS"],
					"CURATOR_LEADER" => $arCompany["LEADERS"][0]
				];

				$participant::createElement($fields, $props);
			}
		}
	}


	public static function getTargetsByUser(int $userId, string $targetClass): array {
		return self::getTargetsByConditions(
			[
				[
					"LOGIC" => "OR",
					"PROPERTY_CURATORS" => $userId,
					"PROPERTY_CURATOR_LEADER" => $userId
				]
			],
			$targetClass
		);
	}

	public static function getTargetsByCompany(int $companyId, string $targetClass): array {
		return self::getTargetsByConditions(
			[ "PROPERTY_PARTICIPANT_ID" => $companyId ],
			$targetClass
		);
	}

	private static function getTargetsByConditions(array $filter, string $targetClass): array {
		self::validateClass($targetClass);

		/** @var ITarget $targetClass */
		$participantClass = $targetClass::getParticipantClass();

		/** @var AParticipant $participantClass */
		$arParticipants = $participantClass::getElementsByConditions(
			$filter,
			[],
			[ "ID", "IBLOCK_ID", "PROPERTY_TARGET_ID", "PROPERTY_CURATORS", "PROPERTY_CURATOR_LEADER" ]
		);


		$participants = [];
		foreach ($arParticipants as $arParticipant) {
			$targetId = $arParticipant["PROPERTY_TARGET_ID_VALUE"];
			$participants[$targetId] = $arParticipant;
		}

		$targetIds = array_keys($participants);

		$filter = count($targetIds)
			? ["ID" => $targetIds]
			: ["ID" => false]
		;


		/** @var ATarget $targetClass */
		return [
			"PARTICIPANTS" => $participants,
			"TARGETS" => $targetClass::getElementsByConditions($filter)
		];
	}


	private static function validateClass(string $targetClass) {
		$classIsChild = in_array(ITarget::class, class_implements($targetClass));

		if (!$classIsChild) {
			throw new \Exception("Parameter \$class must be the child of " . ITarget::class);
		}
	}
}
