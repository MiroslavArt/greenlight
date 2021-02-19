<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

global $APPLICATION, $USER;
if (!$USER->CanDoOperation('edit_other_settings')) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$module_id = 'itrack.custom'; //обязательно, иначе права доступа не работают!

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight($module_id) < "F") {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

Loader::includeModule($module_id);
Loader::includeModule('iblock');

$request = HttpApplication::getInstance()->getContext()->getRequest();

#Описание опций

$arIblockList = [];
$res = CIBlock::GetList(['SORT' => 'ASC'], ['ACTIVE' => 'Y']);
while ($item = $res->Fetch()) {
    $arIblockList[$item['ID']] = $item['NAME'];
}

$aTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('ITRACK_CUSTOM_OPTIONS_TITLE_MAIN'),
        'ICON' => 'main_settings',
        'OPTIONS' => array(
            array(
                'CHECK_LOG',
                'Включить логирование',
                '',
                array('checkbox', 'N'),
            ),
            array(
                'FOLDER_LOG',
                Loc::getMessage('ITRACK_CUSTOM_OPTIONS_FOLDER_LOG'),
                '',
                array('text', 20),
            ),
            array('note' => Loc::getMessage('ITRACK_CUSTOM_OPTIONS_NOTE_MAIN')),
        ),
    ),
    array(
        'DIV' => 'edit3',
        'TAB' => Loc::getMessage('ITRACK_CUSTOM_MAIN_TAB_RIGHTS'),
        'TITLE' => Loc::getMessage('ITRACK_CUSTOM_MAIN_TAB_TITLE_RIGHTS'),
    ),
);
#Сохранение
if ($request->isPost() && $request['Update'] && check_bitrix_sessid()) {

    foreach ($aTabs as $aTab) {
        //Или можно использовать __AdmSettingsSaveOptions($MODULE_ID, $arOptions);
        foreach ($aTab['OPTIONS'] as $arOption) {
            if (!is_array($arOption)) //Строка с подсветкой. Используется для разделения настроек в одной вкладке
            {
                continue;
            }

            if ($arOption['note']) //Уведомление с подсветкой
            {
                continue;
            }

            //Или __AdmSettingsSaveOption($MODULE_ID, $arOption);
            $optionName = $arOption[0];

            $optionValue = $request->getPost($optionName);

            if ($arOption[3][0] === 'checkbox' && $optionValue !== "Y") {
                $optionValue = 'N';
            }

            Option::set($module_id, $optionName, is_array($optionValue) ? implode(',', $optionValue) : $optionValue);

        }
    }
}


#Визуальный вывод

$tabControl = new CAdminTabControl('tabControl', $aTabs);

?>
<? $tabControl->Begin(); ?>
<form method='post'
      action='<?php echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANG ?>'
      name='itrack_custom_settings'>
    <?= bitrix_sessid_post() ?>

    <?php foreach ($aTabs as $aTab) {
        if ($aTab['OPTIONS']) {
            $tabControl->BeginNextTab();
            __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
        }
    } ?>

    <?php
    $tabControl->BeginNextTab();

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php");

    $tabControl->Buttons(); ?>

    <input type="submit" name="Update" value="<?php echo GetMessage('MAIN_SAVE') ?>">
    <input type="reset" name="reset" value="<?php echo GetMessage('MAIN_RESET') ?>">
    <input type="hidden" name="Update" value="Y"/>

</form>
<?php $tabControl->End(); ?>

