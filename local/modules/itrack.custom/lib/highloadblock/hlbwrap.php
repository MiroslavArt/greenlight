<?

namespace Itrack\Custom\Highloadblock;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

/**
 * Class HLBWrap - класс-обертка для работы с Highload блоками.
 * Скрывает необходимость подготовки ORM-сущности методами compileEntity(), getDataClass().
 *
 * @package Intervolga\Custom\HighloadBlock
 */
class HLBWrap
{
    private $tableName = "";

    /**
     * @param string $tableName - имя таблицы БД, связанной с Highload блоком.
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Получение имени таблицы БД, связанной с Highload блоком.
     *
     * @return string - имя таблицы БД.
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Получение id highload блока по текущему имени таблицы.
     *
     * @return int - id highload блока или 0 в случае ошибки.
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getHlbId()
    {
        if (Loader::includeModule('highloadblock')) {
            $arHlBlock = $this->getHlbInfo();
            return (int)$arHlBlock['ID'];
        }
        return 0;
    }

    /**
     * Получения списка элементов Highload блока.
     *
     * @param array $parameters - массив параметров.
     * @return mixed - результат запроса.
     * @return \Bitrix\Main\ORM\Query\Result
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getList(array $parameters)
    {
        $class = static::getClass();
        return $class::getList($parameters);
    }

    /**
     * @param array $filter
     * @return int
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getCount($filter = array())
    {
        $class = static::getClass();
        return $class::getCount($filter);
    }

    /**
     * Добавление элемента Highload блока.
     *
     * @param array $data - данные элемента.
     * @return \Bitrix\Main\Entity\AddResult - результат операции.
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function add(array $data)
    {
        $class = static::getClass();
        return $class::add($data);
    }

    /**
     * Обновление элемента Highload блока.
     *
     * @param $id - идентификатор элемента Highload блока.
     * @param array $data - новые данные элемента Highload блока.
     * @return \Bitrix\Main\Entity\UpdateResult - результат операции.
     * @return \Bitrix\Main\ORM\Data\UpdateResult
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function update($id, array $data)
    {
        $class = static::getClass();
        return $class::update($id, $data);
    }

    /**
     * Удаление элемента Highload блока.
     *
     * @param $id - идентификатор элемента Highload блока.
     * @return \Bitrix\Main\ORM\Data\DeleteResult
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function delete($id)
    {
        $class = static::getClass();
        return $class::delete($id);
    }

    /**
     * Получение ORM-сущности для работы с Highload блоком.
     * @return \Bitrix\Main\ORM\Data\DataManager|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getClass()
    {
        if (Loader::includeModule('highloadblock')) {
            $arHLBlock = $this->getHlbInfo();
            $entity = HLBT::compileEntity($arHLBlock);
            return $entity->getDataClass();
        }
        return null;
    }

    /**
     * Получение информации о highload блоке по текущему имени таблицы.
     * @return array|false|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getHlbInfo()
    {
        if (Loader::includeModule('highloadblock')) {
            return HLBT::getList([
                'filter' => [
                    '=TABLE_NAME' => $this->tableName
                ]
            ])->fetch();
        }
        return null;
    }
}