<?php
$MODULE_ID = 'itrack.custom';

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Config;

global $APPLICATION;

if (!check_bitrix_sessid()) {
    return;
}

Loc::loadMessages(__FILE__);

$cache_type = Config\Configuration::getInstance()->get('cache');


if ($ex = $APPLICATION->GetException()) {
    echo CAdminMessage::ShowMessage([
        'TYPE' => 'ERROR',
        'MESSAGE' => Loc::getMessage('MOD_INST_ERR'),
        'DETAILS' => $ex->GetString(),
        'HTML' => true,
    ]);
} else {
    echo CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
}


if (!$cache_type['type'] || $cache_type == 'none') {

    echo CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage($MODULE_ID . "_D7_NO_CACHE"), "TYPE" => "ERROR"));
}
?>
<form action="<?= $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<?php echo LANGUAGE_ID ?>">
    <input type="submit" name="" value="<?php echo Loc::getMessage('MOD_BACK') ?>">
</form>
