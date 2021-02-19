<?php

namespace Itrack\Custom\Highloadblock;


class HlbForWs
{
    protected static $highloadBlocks = [];

    /**
     * @return string
     */
    public static function getTableName()
    {
        // Override me!
        return '';
    }

    /**
     * @return \Itrack\Custom\HighloadBlock\HLBWrap
     */
    final public static function getHlb()
    {
        if (!static::$highloadBlocks[get_called_class()]) {
            static::$highloadBlocks[get_called_class()] = new HLBWrap(static::getTableName());
        }

        return static::$highloadBlocks[get_called_class()];
    }


    /**
     * @param array $filter
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getOneRecord(array $filter)
    {
        $hlb = static::getHlb();
        return $hlb->getList(array(
            'filter' => $filter,
            'limit' => 1,
            'order' => array(
                'ID' => 'DESC',
            ),
        ))->fetch();
    }

    /**
     * @param array $filter
     * @param array|string[] $order
     * * @param array|string[] $select
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getList(array $filter, array $order = ['ID' => 'DESC'], array $select = ["*"])
    {
        $hlb = static::getHlb();
        return $hlb->getList(array(
            'filter' => $filter,
            'select' => $select,
            'order' => $order,
        ))->fetchAll();
    }

    /**
     * @param array $data
     * @return \Bitrix\Main\Entity\AddResult
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function add(array $data)
    {
        $hlb = static::getHlb();
        return $hlb->add($data);
    }

    /**
     * @param $id
     * @param array $data
     * @return \Bitrix\Main\Entity\UpdateResult
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function update($id, array $data)
    {
        $hlb = static::getHlb();

        return $hlb->update($id, $data);
    }


}