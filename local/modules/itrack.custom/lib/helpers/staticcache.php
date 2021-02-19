<?

namespace Itrack\Custom\Helpers;

/*
 * Статическое хранилище для данных.
 * Используется если необходимо в нескольких местах получать одинаковые данные в рамках одного хита
 */

class StaticCache
{
    private static $cache = array();

    /**
     * @param        $key
     * @param        $value
     * @param string $module
     */
    public static function set($key, $value, $module = "base")
    {
        if (!isset(self::$cache[$module]))
            self::$cache[$module] = array();

        self::$cache[$module][$key] = $value;
    }

    /**
     * @param        $key
     * @param string $module
     */
    public static function get($key, $module = "base")
    {
        self::$cache[$module][$key];
    }

    /**
     * @param array $arr
     * @param string $module
     *
     * @return array
     */
    public static function getMultiply($arr = array(), $module = "base")
    {
        $result = array();
        foreach ($arr as $key => $val) {
            $result[$key] = self::$cache[$module][$key];
        }

        return $result;
    }

    /**
     * @param array $arr
     * @param string $module
     */
    public static function setMultiply($arr = array(), $module = "base")
    {
        if (!isset(self::$cache[$module]))
            self::$cache[$module] = array();

        self::$cache[$module] = array_merge(self::$cache[$module], $arr);
    }

    /**
     * @param string $module
     */
    public static function clear($module = "base")
    {
        self::$cache[$module] = array();
    }
}