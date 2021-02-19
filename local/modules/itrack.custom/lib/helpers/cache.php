<?

namespace Itrack\Custom\Helpers;

use \Bitrix\Main\Application;

class Cache
{

    /**
     * Обёрка для кеширования
     *
     * @param        $ttl
     * @param        $cacheId
     * @param        $callback
     * @param array $arCallbackParams
     * @param string $initDir
     *
     * @return mixed
     *
     *
     * <h4>Пример</h4>
     * <pre bgcolor="#323232" style="padding:5px;">
     *  public static function getIblockItems($iblockId){
     *      return self::returnFileCache(3600, "iblock_".$iblockId, function () use($iblockId){
     *            Loader::includeModule("iblock");
     *            $dbItems = \CIBlockElement::GetList(['SORT'=>'ASC'],['IBLOCK_ID'=>$iblockId,'ACTIVE'=>'Y'],false,false,['ID','NAME']);
     *            while($arItem = $dbItems->Fetch()) {
     *                $items[$arItem['ID']] = $arItem['NAME'];
     *            }
     *
     *        return $items;
     *        });
     *  }
     *  </pre>
     */
    public static function returnFileCache($ttl, $cacheId, $callback, $arCallbackParams = array(), $initDir = "itrack")
    {
        $cache = \Bitrix\Main\Data\Cache::createInstance();
        if ($cache->initCache($ttl, $cacheId, $initDir)) {
            $result = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $result = $callback($arCallbackParams);
            $cache->endDataCache($result);
        }

        return $result;
    }


    public static function getCache($ttl, $cacheId)
    {
        $result = false;
        $cacheManager = Application::getInstance()->getManagedCache();

        if ($cacheManager->read($ttl, $cacheId)) {
            $result = $cacheManager->get($cacheId);
        }

        return $result;
    }

    public static function setCache($cacheId, $value)
    {
        $cacheManager = Application::getInstance()->getManagedCache();
        $cacheManager->set($cacheId, $value);
    }

    public static function cleanCache($cacheId)
    {
        $cacheManager = Application::getInstance()->getManagedCache();
        $cacheManager->clean($cacheId);
    }
}