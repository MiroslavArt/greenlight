<?php
namespace Itrack\Custom\UserAccess\Tables;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;



abstract class AbstractUserAccessTable extends DataManager
{
	private const TARGET_FIELD = "TARGET_ID";
	private const USER_FIELD = "USER_ID";

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new IntegerField(self::TARGET_FIELD, [ "primary" => true ]),
			new IntegerField(self::USER_FIELD, [ "primary" => true ]),
		];
	}


	public static function set(int $userId, int $targetId)
	{
		try {
			self::add([
				self::USER_FIELD => $userId,
				self::TARGET_FIELD => $targetId,
			]);
		}
		catch (\Exception $e) {
			// Nothing to do. This row already exists
		}
	}

	public static function drop(int $userId, int $targetId)
	{
		self::delete([
			self::USER_FIELD => $userId,
			self::TARGET_FIELD => $targetId,
		]);
	}

	public static function isRowExists(int $userId, int $targetId): bool
	{
		$result = self::getList([
			"select" => ["*"],
			"filter" => [
				self::USER_FIELD => $userId,
				self::TARGET_FIELD => $targetId,
			]
		]);

		return !!$result->fetch();
	}

	public static function getAllTargets(int $userId): array
	{
		$result = self::getList([
			"select" => [ self::TARGET_FIELD ],
			"filter" => [ self::USER_FIELD => $userId ]
		]);


		$targets = [];
		while ($row = $result->fetch()) {
			$targets[] = $row[self::TARGET_FIELD];
		}

		return $targets;
	}
}
