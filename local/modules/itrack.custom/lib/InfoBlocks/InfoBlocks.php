<?php

namespace Itrack\Custom\InfoBlocks;

interface InfoBlocks
{
    /**
     * Get Element by ID
     * @param int $elementID
     * @return array
     */
    public static function getElementByID(int $elementID):array;

    /**
     * Get element by CODE
     * @param string $elementCode
     * @return array
     */
    public static function getElementByCode(string $elementCode):array;

    /**
     * Get section by ID
     * @param int $sectionID
     * @return array
     */
    public static function getSectionByID(int $sectionID):array;

    /**
     * Get section by ID
     * @param string $sectionCode
     * @return array
     */
    public static function getSectionByCode(string $sectionCode):array;

    /**
     * Get all sections
     * @return array
     */
    public static function getSections():array;

    /**
     * Get section by conditions
     * @param array $filter
     * @param array $sort
     * @param array $select
     * @return array
     */
    public static function getSectionsByConditions(array $filter = [], array $sort = [], array $select = []):array;

    /**
     * Get all elements
     * @return array
     */
    public static function getElements():array;

    /**
     * Get all elements by section ID
     * @param int $sectionID
     * @return array
     */
    public static function getElementsBySectionID(int $sectionID):array;

    /**
     * Get all elements by section CODE
     * @param string $sectionCode
     * @return array
     */
    public static function getElementsBySectionCode(string $sectionCode):array;

    /**
     * Get elements by conditions
     * @param array $filter
     * @param array $sort
     * @param array $select
     * @return array
     */
    public static function getElementsByConditions(array $filter = [], array $sort = [], array $select = []):array;

    /**
     * Create new element
     * @param array $fields
     * @param array $properties
     * @return int|null
     */
    public static function createElement(array $fields, array $properties):?string;

    /**
     * Update product
     * @param int $id
     * @param array $params
     * @param array $properties
     */
    public static function updateElement(int $id, array $params = [], array $properties = []);

    /**
     * Create new section
     * @param array $fields
     * @return int|null
     */
    public static function createSection(array $fields):?int;

    /**
     * Get property by code
     * @param string $property
     * @return array
     */
    public static function getProperty(string $property):array;

    /**
     * Get IB_BLOCK_ID
     * @return int
     */
    public static function getIbBlockID():int;
}