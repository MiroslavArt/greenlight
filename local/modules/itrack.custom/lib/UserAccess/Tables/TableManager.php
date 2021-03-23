<?php
namespace Itrack\Custom\UserAccess\Tables;

use \Bitrix\Main\Application;
use \Bitrix\Main\Entity\Base;

class TableManager
{
	private static $tables = [
		UserContractAcceptanceTable::class,
		UserContractNotificationTable::class,
		UserLostAcceptanceTable::class,
		UserLostNotificationTable::class,
	];

	public static function createTables()
	{
		self::manageExistenceOfTables(true);
	}

	public static function dropTables()
	{
		self::manageExistenceOfTables(false);
	}

	private static function manageExistenceOfTables(bool $create = true)
	{
		$connection = Application::getConnection();

		foreach (self::$tables as $tableClass) {
			$tableInstance = Base::getInstance($tableClass);
			$tableName = $tableInstance->getDBTableName();
			$isTableExists = $connection->isTableExists($tableName);

			if ($create && !$isTableExists) {
				$tableInstance->createDBTable();
			} elseif(!$create && $isTableExists) {
				$connection->dropTable($tableName);
			}
		}
	}
}
