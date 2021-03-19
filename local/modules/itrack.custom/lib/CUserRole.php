<?php


namespace Itrack\Custom;


class CUserRole
{
	const BROKER = 'broker';
	const CLIENT = 'client';
	const INSURER = 'insurer';
	const ADJUSTER = 'adjuster';

	const SUPER = '_superuser';

	private $userGroups;

	public function __construct()
	{
		$userGroups = CUserEx::getUserGroups();

		foreach ($userGroups as $group) {
			$this->userGroups[] = $group["GROUP_CODE"];
		}
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
		foreach ([self::BROKER, self::CLIENT, self::INSURER, self::ADJUSTER] as $role) {
			if ($this->isUserParty($role)) {
				return $role;
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
