<?php
namespace Itrack\Custom\UserAccess;

use Itrack\Custom\UserAccess\Tables\UserContractAcceptanceTable;
use Itrack\Custom\UserAccess\Tables\UserContractNotificationTable;
use Itrack\Custom\UserAccess\Tables\UserLostAcceptanceTable;
use Itrack\Custom\UserAccess\Tables\UserLostNotificationTable;

class CUserAccess
{
	private $userId;

	public function __construct(int $userId)
	{
		$this->userId = $userId;
	}


	public function getNotificationForAllContracts(): array {
		return UserContractNotificationTable::getAllTargets($this->userId);
	}

	public function getNotificationForAllLosts(): array {
		return UserLostNotificationTable::getAllTargets($this->userId);
	}

	public function getAcceptanceForAllContracts(): array {
		return UserContractAcceptanceTable::getAllTargets($this->userId);
	}

	public function getAcceptanceForAllLosts(): array {
		return UserLostAcceptanceTable::getAllTargets($this->userId);
	}


	public function hasNotificationForContract(int $targetId): bool {
		return UserContractNotificationTable::has($this->userId, $targetId);
	}

	public function hasNotificationForLost(int $targetId): bool {
		return UserLostNotificationTable::has($this->userId, $targetId);
	}

	public function hasAcceptanceForContract(int $targetId): bool {
		return UserContractAcceptanceTable::has($this->userId, $targetId);
	}

	public function hasAcceptanceForLost(int $targetId): bool {
		return UserLostAcceptanceTable::has($this->userId, $targetId);
	}


	public function setNotificationForContract(int $targetId) {
		return UserContractNotificationTable::set($this->userId, $targetId);
	}

	public function setNotificationForLost(int $targetId) {
		return UserLostNotificationTable::set($this->userId, $targetId);
	}

	public function setAcceptanceForContract(int $targetId) {
		return UserContractAcceptanceTable::set($this->userId, $targetId);
	}

	public function setAcceptanceForLost(int $targetId) {
		return UserLostAcceptanceTable::set($this->userId, $targetId);
	}


	public function dropNotificationForContract(int $targetId) {
		return UserContractNotificationTable::drop($this->userId, $targetId);
	}

	public function dropNotificationForLost(int $targetId) {
		return UserLostNotificationTable::drop($this->userId, $targetId);
	}

	public function dropAcceptanceForContract(int $targetId) {
		return UserContractAcceptanceTable::drop($this->userId, $targetId);
	}

	public function dropAcceptanceForLost(int $targetId) {
		return UserContractAcceptanceTable::drop($this->userId, $targetId);
	}
}
