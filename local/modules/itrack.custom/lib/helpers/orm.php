<?

namespace Itrack\Custom\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\ModuleManager;

class Orm
{

    /**
     * Проверка поддержки D7
     *
     * @return bool
     */
    public static function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion("main"), "14.00.00");
    }

    /**
     * Создаём таблицу на названию класса
     * @param $className
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function createTable($className)
    {
        $instance = Base::getInstance($className);
        /* @var $class \Bitrix\Main\Entity\DataManager */
        $class = new $className;
        if (!Application::getConnection($class::getConnectionName())->isTableExists($instance->getDBTableName()))
            $instance->createDbTable();
    }

    /**
     * Удаляет таблицу по названию класса
     * @param $className
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\SystemException
     */
    public static function dropTable($className)
    {
        $instance = Base::getInstance($className);
        /* @var $class \Bitrix\Main\Entity\DataManager */
        $class = new $className;
        $sql = "DROP TABLE IF EXISTS " . ($instance->getDBTableName());
        Application::getConnection($class::getConnectionName())->queryExecute($sql);
    }

    /**
     * Очищает таблицу по названию класса
     * @param $className
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\SystemException
     */
    public static function clearTable($className)
    {
        $instance = Base::getInstance($className);
        /* @var $class \Bitrix\Main\Entity\DataManager */
        $class = new $className;
        $sql = "TRUNCATE TABLE " . ($instance->getDBTableName());
        Application::getConnection($class::getConnectionName())->queryExecute($sql);
    }
}