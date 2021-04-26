<?php

namespace Itrack\Custom\InfoBlocks;

use Itrack\Custom\Helpers\Utils;
use CIBlockElement;
use CIBlockSection;
use CIBlockProperty;
use Bitrix\Main\SystemException;

class BaseInfoBlockClass extends AbstractInfoBlock
{
    protected static $selectElement = ['ID', 'IBLOCK_ID', 'ACTIVE', 'SORT', 'IBLOCK_SECTION', 'CODE', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'DETAIL_TEXT', 'DETAIL_PICTURE', 'PROPERTY_*'];
    protected static $selectSection = ['ID', 'IBLOCK_ID', 'ACTIVE', 'SORT', 'IBLOCK_SECTION_ID', 'CODE', 'NAME', 'SECTION_PAGE_URL', 'PICTURE', 'DESCRIPTION', 'UF_*'];
    protected static $sort          = ['SORT' => 'ASC'];

    /**
     * Get Element by ID
     * @param int $elementID
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getElementByID(int $elementID):array
    {
        if (empty($elementID) && !is_int($elementID)) {
            die('ID is not specified.');
        }

        $elements   =   [];
        $arOrder    =   static::$sort;
        $arSelect   =   static::$selectElement;
        $arFilter   =   ['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType), 'ACTIVE' => 'Y', 'ID' => $elementID];

        $elementsList = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

        $elementsList->SetUrlTemplates(static::$detailElementUrlTemplate, static::$detailSectionUrlTemplate);

        if ($el = $elementsList->GetNextElement()) {
            $elements               =   $el->GetFields();
            $elements['PROPERTIES'] =   $el->GetProperties();
        }

        return $elements;
    }

    /**
     * Get element by CODE
     * @param string $elementCode
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getElementByCode(string $elementCode):array
    {
        if (empty($elementCode) && !is_string($elementCode)) {
            die('CODE is not specified.');
        }

        $elements   =   [];
        $arOrder    =   static::$sort;
        $arSelect   =   static::$selectElement;
        $arFilter   =   ['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType), 'ACTIVE' => 'Y', 'CODE' => $elementCode];

        $elementsList = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

        $elementsList->SetUrlTemplates(static::$detailElementUrlTemplate, static::$detailSectionUrlTemplate);

        if ($el = $elementsList->GetNextElement()) {
            $elements               =   $el->GetFields();
            $elements['PROPERTIES'] =   $el->GetProperties();
        }

        return $elements;
    }

    /**
     * Get section by ID
     * @param int $sectionID
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getSectionByID(int $sectionID):array
    {
        if (empty($sectionID) && !is_int($sectionID)) {
            die('ID is not specified.');
        }

        $section    =   [];
        $arOrder    =   static::$sort;
        $arFilter   =   ['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType), 'ACTIVE' => 'Y', 'ID' => $sectionID];
        $arSelect   =   static::$selectSection;
        $rsSections =   CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);

        if ($res = $rsSections->Fetch()) {
            $section = $res;
        }

        return $section;
    }

    /**
     * Get section by ID
     * @param string $sectionCode
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getSectionByCode(string $sectionCode):array
    {
        if (empty($sectionCode) && !is_string($sectionCode)) {
            die('CODE is not specified.');
        }

        $section    =   [];
        $arOrder    =   static::$sort;
        $arFilter   =   ['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType), 'ACTIVE' => 'Y', 'CODE' => $sectionCode];
        $arSelect   =   static::$selectSection;
        $rsSections =   CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);

        if ($res = $rsSections->Fetch()) {
            $section = $res;
        }

        return $section;
    }

    /**
     * Get section by conditions
     * @param array $filter
     * @param array $sort
     * @param array $select
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getSectionsByConditions(array $filter = [], array $sort = [], array $select = []):array
    {
        $sections   =   [];
        $arOrder    =   static::$sort;

        if (!empty($sort)) {
            $arOrder = array_merge(static::$sort, $sort);
        }

        $arFilter = ['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType)];

        if (!empty($filter)) {
            $arFilter = array_merge(['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType)], $filter);
        }

        $arSelect = static::$selectSection;

        if (!empty($select)) {
            $arSelect = array_merge($arSelect, $select);
        }

        $rsSections = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);

        while ($res = $rsSections->Fetch()) {
            $sections[] = $res;
        }

        return $sections;
    }

    /**
     * Get all sections
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getSections():array
    {
        $arOrder    =   static::$sort;
        $arFilter   =   ['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType), 'ACTIVE' => 'Y'];
        $arSelect   =   static::$selectSection;
        $rsSections =   CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);
        $sections   =   [];

        while ($results = $rsSections->GetNext()) {
            $sections[] = $results;
        }

        return $sections;
    }

    /**
     * Get all elements
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getElements():array
    {
        $key        =   0;
        $elements   =   [];
        $arOrder    =   static::$sort;
        $arSelect   =   static::$selectElement;
        $arFilter   =   ['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType), 'ACTIVE' => 'Y'];

        $elementsList = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

        $elementsList->SetUrlTemplates(static::$detailElementUrlTemplate, static::$detailSectionUrlTemplate);

        while ($el = $elementsList->GetNextElement()) {
            $elements[$key]                 =   $el->GetFields();
            $elements[$key]['PROPERTIES']   =   $el->GetProperties();
            $key++;
        }

        return $elements;
    }

    /**
     * Get all elements by section ID
     * @param int $sectionID
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getElementsBySectionID(int $sectionID):array
    {
        if (empty($sectionID) && !is_int($sectionID)) {
            die('ID is not specified.');
        }

        $key        =   0;
        $elements   =   [];
        $arOrder    =   static::$sort;
        $arSelect   =   static::$selectElement;
        $arFilter   =   ['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType), 'ACTIVE' => 'Y', 'IBLOCK_SECTION_ID' => $sectionID];

        $elementsList = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

        $elementsList->SetUrlTemplates(static::$detailElementUrlTemplate, static::$detailSectionUrlTemplate);

        while ($el = $elementsList->GetNextElement()) {
            $elements[$key]                 =   $el->GetFields();
            $elements[$key]['PROPERTIES']   =   $el->GetProperties();
            $key++;
        }

        return $elements;
    }

    /**
     * Get all elements by section CODE
     * @param string $sectionCode
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getElementsBySectionCode(string $sectionCode):array
    {
        if (empty($sectionCode) && !is_string($sectionCode)) {
            die('CODE is not specified.');
        }

        $section = self::getSectionByCode($sectionCode);

        if (empty($section)) {
            die('CODE is not specified.');
        }

        $key        =   0;
        $elements   =   [];
        $arOrder    =   static::$sort;
        $arSelect   =   static::$selectElement;
        $arFilter   =   ['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType), 'ACTIVE' => 'Y', 'IBLOCK_SECTION_ID' => $section['ID']];

        $elementsList = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

        $elementsList->SetUrlTemplates(static::$detailElementUrlTemplate, static::$detailSectionUrlTemplate);

        while ($el = $elementsList->GetNextElement()) {
            $elements[$key]                 =   $el->GetFields();
            $elements[$key]['PROPERTIES']   =   $el->GetProperties();
            $key++;
        }

        return $elements;
    }

    /**
     * Get elements by conditions
     * @param array $filter
     * @param array $sort
     * @param array $selectElement
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getElementsByConditions(array $filter = [], array $sort = [], array $selectElement = []):array
    {
        $key        =   0;
        $elements   =   [];

        if (empty($selectElement)) {
            $arSelect = static::$selectElement;
        } else {
            $arSelect = $selectElement;
        }

        if (empty($sort)) {
            $arOrder = static::$sort;
        } else {
            $arOrder = $sort;
        }

        $arFilter = array_merge(['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType)], $filter);

        $elementsList = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

        $elementsList->SetUrlTemplates(static::$detailElementUrlTemplate, static::$detailSectionUrlTemplate);

        while ($el = $elementsList->GetNextElement()) {
            $elements[$key]                 =   $el->GetFields();
            $elements[$key]['PROPERTIES']   =   $el->GetProperties();
            $key++;
        }

        return $elements;
    }

	/**
	 * Get elements by conditions with grouping
	 * @param array $filter
	 * @param array $sort
	 * @param array $groupBy
	 * @return array
	 * @throws \Bitrix\Main\LoaderException
	 */
	public static function getElementsGrouped(array $filter = [], array $sort = [], array $groupBy = []):array
	{
		$key        =   0;
		$elements   =   [];

		if (empty($sort)) {
			$arOrder = static::$sort;
		} else {
			$arOrder = $sort;
		}

		$arFilter = array_merge(['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType)], $filter);

		$elementsList = CIBlockElement::GetList($arOrder, $arFilter, $groupBy);

		$elementsList->SetUrlTemplates(static::$detailElementUrlTemplate, static::$detailSectionUrlTemplate);

		while ($el = $elementsList->GetNext()) {
			$elements[$key] = $el;
			$key++;
		}

		return $elements;
	}

    /**
     * Create new element
     * @param array $fields
     * @param array $properties
     * @return int|null
     * @throws \Bitrix\Main\LoaderException
     */
    public static function createElement(array $fields, array $properties):?string
    //public static function createElement(array $fields, array $properties)
    {
        $el = new CIBlockElement();

        $fields = array_merge([
            'IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType),
        ], $fields);

        $resultID = $el->Add($fields);

        if ($resultID) {
            foreach ($properties as $key => $property) {
                CIBlockElement::SetPropertyValuesEx($resultID, false, [$key => $property]);
            }

            return (int)$resultID;
        }

        AddMessage2Log([$el->LAST_ERROR, $fields], 'ERROR.CREATE_ELEMENT');
        return strip_tags($el->LAST_ERROR);
        //return null;
    }

    /**
     * Update product
     * @param int $id
     * @param array $params
     * @param array $properties
     */
    public static function updateElement(int $id, array $params = [], array $properties = [])
    {
        if (empty($id) || $id === null) {
            exit('ID not set!');
        }

        $el = new CIBlockElement();

        if (!empty($params)) {
            $res = $el->Update($id, $params);
        }

        if (!empty($properties)) {
            foreach ($properties as $key => $property) {
                CIBlockElement::SetPropertyValuesEx($id, false, [$key => $property]);
            }
        }
        if($el->LAST_ERROR) {
            return strip_tags($el->LAST_ERROR);
        } else {
            return $res;
        }
    }

	/**
	 * Delete element
	 * @param int $id
	 */
	public static function deleteElement(int $id)
	{
		if (empty($id) || $id === null) {
			exit('ID not set!');
		}

		CIBlockElement::Delete($id);
	}

    /**
     * Create new section
     * @param array $fields
     * @return int|null
     * @throws \Bitrix\Main\LoaderException
     */
    public static function createSection(array $fields):?int
    {
        $section = new CIBlockSection();

        $fields = array_merge([
            'IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType),
        ], $fields);

        $resultID = $section->Add($fields);

        if ($resultID) {
            return (int)$resultID;
        }

        AddMessage2Log([$section->LAST_ERROR, $fields], 'ERROR.CREATE_SECTION');

        return null;
    }

    /**
     * Get property by code
     * @param string $property
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getProperty(string $property):array
    {
        $dbEnumList = CIBlockProperty::GetPropertyEnum($property, [], ['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType)]);

        return $dbEnumList->GetNext();
    }

    public static function getPropertyList(string $property):array
    {
        $arPropertyList = [];

        $dbEnumList = CIBlockProperty::GetPropertyEnum($property, ["VALUE"=>"ASC"], ['IBLOCK_ID' => Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType)]);

        while($arProperty = $dbEnumList->GetNext())
        {
            $arPropertyList[] = $arProperty;
        }

        return $arPropertyList;
    }

    /**
     * Get IB_BLOCK_ID
     * @return int
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getIbBlockID():int
    {
        return Utils::getIDIblockByCode(static::$ibBlockCode, static::$ibBlockType);
    }
}
