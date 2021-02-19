<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//delayed function must return a string
if (empty($arResult))
    return "";

$strReturn = '<p class="breadcrumbs">';
$itemSize = count($arResult);
for ($index = 0; $index < $itemSize; $index++) {
    if ($index > 0)
        $strReturn .= '';

    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);
    if ($arResult[$index]["LINK"] <> "" && $index + 1 != $itemSize)
        $strReturn .= '<a href="' . $arResult[$index]["LINK"] . '" title="' . $title . '">' . $title . '</a> / ';
    else
        $strReturn .= $title;
}

$strReturn .= '</p>';
return $strReturn;
?>
