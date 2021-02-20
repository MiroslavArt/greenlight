<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {die();}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;

global $APPLICATION, $USER;
Loc::loadMessages(__FILE__);
?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico"/>

    <?php $APPLICATION->ShowHead(); ?>
    <title><?php $APPLICATION->ShowTitle() ?></title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">


    <?php
    Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH . '/css/select2.min.css');
    Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH . '/css/jquery.fancybox.min.css');
    Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH . '/css/reset.css');
    Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH . '/css/style.css');
    Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH . '/css/style_mob.css');
    Asset::getInstance()->addString('<link href="'.DEFAULT_TEMPLATE_PATH.'/css/style_mob.css" media="only screen and (max-width: 1700px)" rel="stylesheet" />');

    Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH . '/template_styles.css');
    Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH . '/styles.css');
    ?>
    <?php
    Extension::load(['core', 'ajax', 'fx']);
    Asset::getInstance()->addJs(DEFAULT_TEMPLATE_PATH . '/js/jquery.min.js');
    Asset::getInstance()->addJs(DEFAULT_TEMPLATE_PATH . '/js/select2.full.min.js');
    Asset::getInstance()->addJs(DEFAULT_TEMPLATE_PATH . '/js/jquery.fancybox.min.js');
    Asset::getInstance()->addJs(DEFAULT_TEMPLATE_PATH . '/js/jquery.validate.min.js');
    Asset::getInstance()->addJs(DEFAULT_TEMPLATE_PATH . '/js/scripts.js');
    ?>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <? $APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            'AREA_FILE_SHOW' => 'file',
            'EDIT_TEMPLATE' => '',
            'PATH' => DEFAULT_TEMPLATE_PATH . '/include/google_tag.php'
        )
    );
    ?>
    <!-- /Global site tag (gtag.js) - Google Analytics-->
</head>
<body>
<?php if ($USER->IsAdmin()) { ?>
    <div id="panel"><?php $APPLICATION->ShowPanel(); ?></div>
<? } ?>