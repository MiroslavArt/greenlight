<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Application,
    \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Loader,
    \Bitrix\Main\Config\Option,
    \Bitrix\Main\ModuleManager,
    \Bitrix\Main\Entity\Base,
    \Bitrix\Main\IO\Directory;
use Bitrix\Main\Diag\Debug;
use Itrack\Custom\Models;

Loc::loadMessages(__FILE__);

class itrack_custom extends CModule
{
    var $MODULE_ID = "itrack.custom";
    var $PARTNER_NAME = "itrack";
    var $PARTNER_URI = "https://itrack.ru/";

    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_ID = "itrack";
    var $MODULE_SORT = 100;
    var $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
    var $MODULE_GROUP_RIGHTS;

    protected $module_dir = '';
    protected $isLocal = '';
    protected $exclusionAdminFiles = [];

    const PREFIX = 'itrack_custom';

    public function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . '/version.php');
        //формируем список файлов которые исключаем
        $this->exclusionAdminFiles = [
            '..',
            '.',
            'menu.php',
            'operation_description.php',
            'task_description.php'
        ];

        $this->module_dir = Loader::getLocal('modules/' . $this->MODULE_ID);

        $this->isLocal = (bool)strpos($this->module_dir, '/local/modules/');

        $this->MODULE_NAME = Loc::getMessage($this->MODULE_ID . '_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage($this->MODULE_ID . '_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage($this->MODULE_ID . '_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage($this->MODULE_ID . '_PARTNER_URI');
        $this->MODULE_VERSION = empty($arModuleVersion['VERSION']) ? '' : $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = empty($arModuleVersion['VERSION_DATE']) ? '' : $arModuleVersion['VERSION_DATE'];
        $this->MODULE_SORT = 10;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';


    }

    /**
     * Проверим версию установленной версии битрикс
     * @return bool
     */
    public function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }


    /**
     * str_ireplace — Регистронезависимый вариант функции str_replace()
     * @param bool $notDocumentRoot
     * @return mixed|string
     */
    public function getPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        }

        return dirname(__DIR__);
    }


    public function InstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {


        }

    }

    public function UnInstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {


            Option::delete($this->MODULE_ID);

        }


    }

    /**
     * Установка агентов
     */
    public function InstallAgents()
    {


    }

    /**
     * Удаление агентов
     */
    public function UnInstallAgents()
    {
        $oAgent = new CAgent();
        $oAgent->RemoveModuleAgents($this->MODULE_ID);
    }

    /**
     * Установка обработчиков событий
     */
    public function InstallDependences()
    {


    }

    /**
     * Удаление обработчиков событий
     */
    public function UnInstallDependences()
    {


    }

    public function InstallFiles()
    {

        try {
            if (Directory::isDirectoryExists($path = $this->GetPath() . '/admin') && $dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->exclusionAdminFiles, true) || is_dir($path . '/' . $item)) {
                        continue;
                    }

                    if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::PREFIX . '_' . $item)) {
                        file_put_contents($file, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/' . ($this->isLocal ? 'local' : 'bitrix') . '/modules/' . $this->MODULE_ID . '/admin/' . $item . '");?' . '>');
                    }
                }
                closedir($dir);

            }
        } catch (Exception $e) {

        }

        return true;
    }

    public function UnInstallFiles()
    {

        if (Directory::isDirectoryExists($path = $this->GetPath() . '/admin') && $dir = opendir($path)) {
            while (false !== $item = readdir($dir)) {
                if (in_array($item, $this->exclusionAdminFiles, true) || is_dir($path . '/' . $item)) {
                    continue;
                }

                if (file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::PREFIX . '_' . $item)) {
                    unlink($file);
                }
            }
            closedir($dir);
        }


        return true;
    }


    public function DoInstall()
    {
        global $APPLICATION;
        if ($this->isVersionD7()) {

            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallDB();
            $this->InstallFiles();
            $this->InstallAgents();
            $this->InstallDependences();


        } else {
            $APPLICATION->ThrowException(Loc::getMessage($this->MODULE_ID . '_INSTALL_ERROR_VERSION'));
        }


        return true;

    }

    public function DoUninstall()
    {

        global $APPLICATION;

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if ($request['step'] < '2') {
            $APPLICATION->IncludeAdminFile(Loc::getMessage($this->MODULE_ID . '_UNINSTALL_TITLE'), $this->getPath() . '/install/unstep1.php');
        } elseif ($request['step'] === '2') {

            if ($request['savedata'] !== 'Y') {
                $this->UnInstallDB();
            }
            $this->UnInstallFiles();
            $this->UnInstallAgents();
            $this->UnInstallDependences();


            ModuleManager::unRegisterModule($this->MODULE_ID);

            $APPLICATION->IncludeAdminFile(Loc::getMessage($this->MODULE_ID . '_UNINSTALL_TITLE'), $this->getPath() . '/install/unstep2.php');
        }
    }
}