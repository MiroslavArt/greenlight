<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset;
\Bitrix\Main\UI\Extension::load("ui.alerts");
?>
<?php if ($arResult['IS_AJAX'] == 'Y') {
    $APPLICATION->RestartBuffer();
    ob_start();
}
?>

<?php

if(!empty($arParams['PAGE_TYPE']) && $arParams['PAGE_TYPE'] == 'contracts-list') {
    include 'types/contracts-list.php';
} else {
    switch ($arResult['COMPANY_TYPE']) {
        case 'broker':
            include 'types/broker.php';
            break;
        case 'client':
            include 'types/client.php';
            break;
        case 'insurer':
            include 'types/insurer.php';
            break;
        case 'adjuster':
            include 'types/adjuster.php';
            break;
    }
}
 ?>

<?php
if ($arResult['IS_AJAX'] == 'Y') {
    $html = ob_get_contents();
    ob_end_clean();
    $arResult['html'] = $html;
    $arResult['htmlContainer'] = '#contracts-list';
    $arResult['success'] = true;
    if (!empty($arResult['ERROR'])) {
        unset($arResult['success']);
        $arResult['errorMessage'] = true;
    }
    echo json_encode($arResult);
    die();
}
