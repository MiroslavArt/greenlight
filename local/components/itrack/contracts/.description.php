<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("CONTRACTS_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("CONTRACTS_COMPONENT_DESCRIPTION"),
	"ICON" => "/images/eadd.gif",
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "itrack",
		"CHILD" => array(
			"ID" => "contracts",
			"NAME" => GetMessage("CONTRACTS_COMPONENT_NAME"),
		),
	),
);
?>