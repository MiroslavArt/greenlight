<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
$this->setFrameMode(false);

if (!empty($_REQUEST["edit"]))
	$componentPage = "form";
else
	$componentPage = "list";

$arParams["EDIT_URL"] = $APPLICATION->GetCurPage("", array("edit", "delete", "CODE"));
$arParams["LIST_URL"] = $arParams["EDIT_URL"];

$arDefaultUrlTemplates404 = array(
    "list" => "",
    "search" => "search/",
    "detail" => "#ELEMENT_ID#/",
    "edit" => "",
    'useful-documents' => "#ELEMENT_ID#/useful-documents/",
    'useful-documents-add' => "#ELEMENT_ID#/useful-documents/add/",
    "contract" => "#ELEMENT_ID#/contract/#CONTRACT_ID#/",
    "contract-useful-documents" => "#ELEMENT_ID#/contract/#CONTRACT_ID#/useful-documents/",
    "contract-useful-documents-add" => "#ELEMENT_ID#/contract/#CONTRACT_ID#/useful-documents/add/",
    "lost" => "#ELEMENT_ID#/contract/#CONTRACT_ID#/lost-#LOST_ID#/",
    "lost-curators" => "#ELEMENT_ID#/contract/#CONTRACT_ID#/lost-#LOST_ID#/curators/",
    "lost-document" => "#ELEMENT_ID#/contract/#CONTRACT_ID#/lost-#LOST_ID#/lost-document-#LOST_DOCUMENT_ID#/",
    "lost-document-history" => "#ELEMENT_ID#/contract/#CONTRACT_ID#/lost-#LOST_ID#/lost-document-#LOST_DOCUMENT_ID#/status/",
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array(
    "ELEMENT_ID",
    "ELEMENT_CODE",
    "CONTRACT_ID",
    "LOST_ID",
    "LOST_DOCUMENT_ID"
);

if($arParams["SEF_MODE"] == "Y")
{
    $arVariables = array();

    $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
    $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

    $engine = new CComponentEngine($this);
    if (CModule::IncludeModule('iblock'))
    {
        $engine->addGreedyPart("#SECTION_CODE_PATH#");
        $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
    }
    $componentPage = $engine->guessComponentPath(
        $arParams["SEF_FOLDER"],
        $arUrlTemplates,
        $arVariables
    );

    $b404 = false;
    if(!$componentPage)
    {
        $componentPage = "list";
        $b404 = true;
    }

    if($b404 && CModule::IncludeModule('iblock'))
    {
        $folder404 = str_replace("\\", "/", $arParams["SEF_FOLDER"]);
        if ($folder404 != "/")
            $folder404 = "/".trim($folder404, "/ \t\n\r\0\x0B")."/";
        if (substr($folder404, -1) == "/")
            $folder404 .= "index.php";

        if ($folder404 != $APPLICATION->GetCurPage(true))
        {
            \Bitrix\Iblock\Component\Tools::process404(
                ""
                ,($arParams["SET_STATUS_404"] === "Y")
                ,($arParams["SET_STATUS_404"] === "Y")
                ,($arParams["SHOW_404"] === "Y")
                ,$arParams["FILE_404"]
            );
        }
    }

    CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

    $arResult = array(
        "FOLDER" => $arParams["SEF_FOLDER"],
        "URL_TEMPLATES" => $arUrlTemplates,
        "VARIABLES" => $arVariables,
        "ALIASES" => $arVariableAliases,
    );

    foreach ($arUrlTemplates as $url => $value) {
        $arResult["PATH_TO"][$url] = CComponentEngine::MakePathFromTemplate($arParams["SEF_FOLDER"] . $value, $arVariables);
    }

    if(in_array($componentPage, ['useful-documents-add', 'contract-useful-documents-add'])) {
        $componentPage = 'useful-documents';
        $arResult['ACTION'] = 'add';
    }
}
else
{
    $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
    CComponentEngine::initComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

    $componentPage = "";

    if(isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0)
        $componentPage = "detail";
    elseif(isset($arVariables["ELEMENT_CODE"]) && strlen($arVariables["ELEMENT_CODE"]) > 0)
        $componentPage = "detail";
    if(isset($arVariables["CONTRACT_ID"]) && intval($arVariables["CONTRACT_ID"]) > 0)
        $componentPage = "contract";
    if(isset($arVariables["LOST_ID"]) && intval($arVariables["LOST_ID"]) > 0)
        $componentPage = "lost";
    else
        $componentPage = "list";

    $arResult = array(
        "FOLDER" => "",
        "URL_TEMPLATES" => array(
            "list" => htmlspecialcharsbx($APPLICATION->GetCurPage()),
            "detail" => htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arVariableAliases["ELEMENT_ID"]."=#ELEMENT_ID#"),
        ),
        "VARIABLES" => $arVariables,
        "ALIASES" => $arVariableAliases
    );
}

$this->IncludeComponentTemplate($componentPage);