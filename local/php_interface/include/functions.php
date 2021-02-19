<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Itrack\Base\Helpers\CIBlockClass;

if (!function_exists('p_d')) {
    function p_d($var, $isDeb = false)
    {
        global $USER;

        if ($USER->IsAdmin() && $var) {
            if ($isDeb) {
                echo "<div style='color: #acacac '><pre>";
                var_dump($var);
                echo "</pre></div>";
            } else {
                echo "<div style='color: #acacac '><pre>";
                print_r($var);
                echo "</pre></div>";
            }

        }

    }
}


/**
 * Склонение окончания
 * $str = num2word(100, array('позиция', 'позиции', 'позиций')); Пример вызова
 * @param $num
 * @param $words
 * @return mixed
 */
function num2word($num, $words)
{
    $num = $num % 100;
    if ($num > 19) {
        $num = $num % 10;
    }
    switch ($num) {
        case 1:
        {
            return ($words[0]);
        }
        case 2:
        case 3:
        case 4:
        {
            return ($words[1]);
        }
        default:
        {
            return ($words[2]);
        }
    }
}

function urlYoutube($url)
{
    $arResult = [];

    if (!empty($url)) {

        $values = '';

        if (preg_match("/(http|https):\/\/(www.youtube|youtube|youtu)\.(be|com)\/([^<\s]*)/", $url, $match)) {
            if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id)) {
                $values = $id[1];
            } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id)) {
                $values = $id[1];
            } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $id)) {
                $values = $id[1];
            } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $id)) {
                $values = $id[1];
            } else if (preg_match('/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^\&\?\/]+)/', $url, $id)) {
                $values = $id[1];
            }
        }

        if (!empty($values)) {
            $arResult['VIDEO_LINK'] = '//www.youtube.com/embed/' . $values . '?rel=0&controls=0&showinfo=0&autoplay=0';
            $arResult['IMG_LINK'] = '//img.youtube.com/vi/' . $values . '/mqdefault.jpg';
            $arResult['BIG_IMG_LINK'] = '//img.youtube.com/vi/' . $values . '/hqdefault.jpg';
            $arResult['VIDEO_ID'] = $values;
        }


    }

    return $arResult;
}

function setFormatPhone($phone)
{
    $phone = preg_replace("/[^0-9]/", "", $phone);

    if (strlen($phone) == 7)
        $phone = preg_replace("/([0-9]{3})([0-9]{2})([0-9]{2})/", "$1-$2-$3", $phone);
    elseif (strlen($phone) == 10)
        $phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1)$2-$3", $phone);
    elseif (strlen($phone) == 11) {
        $phone = preg_replace("/([0-9])([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2})/", "$1 ($2) $3 $4 $5", $phone);
        $first = substr($phone, 0, 1);
        if (in_array($first, array(7, 8)))
            $phone = '+7' . substr($phone, 1);
    }

    return $phone;
}

function getArrayFilterForPath($path)
{
    $previousDirectory = '/';
    $directories = array_filter(explode('/', $path));

    $elements = count($directories);

    if ($elements > 1) {
        foreach ($directories as &$directory) {
            $directory = $previousDirectory . $directory . '/';
            $previousDirectory = $directory;
        }
        $arResult = $directories;
    } else {
        $arResult = [$path];
    }

    return array_unique($arResult);
}

function includeFilePath($path){
	global $APPLICATION;

	$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
			"AREA_FILE_SHOW" => "file",
			"AREA_FILE_SUFFIX" => "inc",
			"EDIT_TEMPLATE" => "",
			"PATH" => SITE_DIR . "include/{$path}.inc"
		)
	);
}