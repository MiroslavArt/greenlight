<?php


namespace Itrack\Custom;

use \Bitrix\Main\GroupTable;
use \Bitrix\Main\UserGroupTable;


class CUserRole
{
	const BROKER = 'broker';
	const CLIENT = 'client';
	const INSURER = 'insurer';
	const ADJUSTER = 'adjuster';

	const SUPER = '_superuser';

	private $userGroups;
	private $userId;

	public function __construct(?int $userId = null)
	{
		if (empty($userId)) {
			$userId = $GLOBALS["USER"]->GetID();
		}

		$this->userId = $userId;

		$this->userGroups = self::getUserRoleGroups($userId);
	}

	public static function getUserRoleGroups($userId) {
		$result = UserGroupTable::query()
			->setSelect(["GROUP_ID", "GROUP_CODE" => "GROUP.STRING_ID"])
			->where("USER_ID", $userId)
			->whereIn("GROUP.STRING_ID", self::getAllRoles())
			->exec();

		$groups = [];

		while ($row = $result->fetch()) {
			$id = $row["GROUP_ID"];
			$code = $row["GROUP_CODE"];
			$groups[$id] = $code;
		}

		return $groups;
	}

	public function setUserRole(string $party, bool $isSuperUser = false) {
		if (!in_array($party, self::getParties())) {
			throw new \Exception("Unknown party");
		}

		$role = $isSuperUser ? $party . self::SUPER : $party;

		$roleGroups = self::getRoleGroups();

		$this->dropUserRoles();

        UserGroupTable::add([
            "USER_ID" => $this->userId,
            "GROUP_ID" => $roleGroups[$party],
        ]);

        if($role != $party) {
            UserGroupTable::add([
                "USER_ID" => $this->userId,
                "GROUP_ID" => $roleGroups[$role],
            ]);
        }
	}

	private function dropUserRoles() {
		if (!is_array($this->userGroups) || empty($this->userGroups)) return;

		foreach ($this->userGroups as $id => $code) {
			UserGroupTable::delete([
				"USER_ID" => $this->userId,
				"GROUP_ID" => $id,
			]);
		}
	}

	public static function getRoleGroups() {
		$result = GroupTable::query()
			->setSelect(["ID", "STRING_ID"])
			->whereIn("STRING_ID", self::getAllRoles())
			->setCacheTtl(86400)
			->exec();

		$groups = [];

		while ($row = $result->fetch()) {
			$id = $row["ID"];
			$code = $row["STRING_ID"];

			$groups[$code] = $id;
		}

		return $groups;
	}

	private function isUserRole($code)
	{
		return in_array($code, $this->userGroups);
	}

	private function isUserParty(string $userType) {
		return
			$this->isUserRole($userType) ||
			$this->isUserRole($userType . self::SUPER);
	}

	public function getUserParty(): ?string
	{
		foreach (self::getParties() as $party) {
			if ($this->isUserParty($party)) {
				return $party;
			}
		}
	}

	public function getUserRole(): ?string {
		$parties = self::getParties();

		foreach ($parties as $role) {
			if ($this->isUserRole($role . self::SUPER)) {
				return $role . self::SUPER;
			}
		}

		foreach ($parties as $role) {
			if ($this->isUserRole($role)) {
				return $role;
			}
		}
	}

	public static function getParties(): array {
		return [
			self::BROKER,
			self::CLIENT,
			self::INSURER,
			self::ADJUSTER
		];
	}

	public static function getSuperGroups(): array {
		return [
			self::BROKER . self::SUPER,
			self::CLIENT . self::SUPER,
			self::INSURER . self::SUPER,
			self::ADJUSTER . self::SUPER
		];
	}

	public static function getAllRoles(): array {
		return [
			self::BROKER,
			self::CLIENT,
			self::INSURER,
			self::ADJUSTER,
			self::BROKER . self::SUPER,
			self::CLIENT . self::SUPER,
			self::INSURER . self::SUPER,
			self::ADJUSTER . self::SUPER
		];
	}

	public function equalTo(string $role): bool { return $this->isUserParty($role); }
	public function isBroker(): bool { return $this->isUserParty(self::BROKER); }
	public function isClient(): bool { return $this->isUserParty(self::CLIENT); }
	public function isInsurer(): bool { return $this->isUserParty(self::INSURER); }
	public function isAdjuster(): bool { return $this->isUserParty(self::ADJUSTER); }

	public function isSuperBroker(): bool { return $this->isUserRole(self::BROKER . self::SUPER); }
	public function isSuperClient(): bool { return $this->isUserRole(self::CLIENT . self::SUPER); }
	public function isSuperInsurer(): bool { return $this->isUserRole(self::INSURER . self::SUPER); }
	public function isSuperAdjuster(): bool { return $this->isUserRole(self::ADJUSTER . self::SUPER); }

	public function isSuperUser(): bool {
		return
			$this->isSuperBroker() ||
			$this->isSuperClient() ||
			$this->isSuperInsurer() ||
			$this->isSuperAdjuster()
			;
	}


	public static function getBrokerGroupCode():string { return self::BROKER; }
	public static function getClientGroupCode():string { return self::CLIENT; }
	public static function getInsurerGroupCode():string { return self::INSURER; }
	public static function getAdjusterGroupCode():string { return self::ADJUSTER; }

	public static function getSuperBrokerGroupCode():string { return self::BROKER . self::SUPER; }
	public static function getSuperClientGroupCode():string { return self::CLIENT . self::SUPER; }
	public static function getSuperInsurerGroupCode():string { return self::INSURER . self::SUPER; }
	public static function getSuperAdjusterGroupCode():string { return self::ADJUSTER . self::SUPER; }
}
