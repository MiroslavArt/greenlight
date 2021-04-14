<?php

use \Bitrix\Main\Application;
use \Bitrix\Main\Context;
use Itrack\Custom\CUserEx;
use \Itrack\Custom\CUserRole;
use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Lost;
use Itrack\Custom\Participation\CLostParticipant;
use Itrack\Custom\Participation\CParticipation;
use Itrack\Custom\Participation\CLost;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity;
use \Bitrix\Main\Entity\Query;
use \Bitrix\Main\Entity\ReferenceField;
use \Bitrix\Main\ORM\Query\Join;

class ItrStatistics extends CBitrixComponent
{
	private const START_STATUS = "red";
	private const FINISH_STATUS = "green";

	private $userId;
	private $companyId;

	/** @var CUserRole $editorRole */
	private $userRole;

    public function executeComponent()
    {
		$this->userId = $GLOBALS["USER"]->GetID();
		$this->companyId = CUserEx::getUserCompanyId($this->userId);
		$this->userRole = new CUserRole($this->userId);

		$session = Application::getInstance()->getSession();
    	$request = Context::getCurrent()->getRequest();

		$isAjax = $request->isAjaxRequest() && $request->get("rivals") === "y";

		if ($isAjax) {
			$GLOBALS["APPLICATION"]->RestartBuffer();
			$this->handleAjaxRequest($request);
			die();
		}

		$arSort = $this->handleSort($request, $session);

		$arItems = $this->assembleItemList();

		$filterStr = $request->get("search");
		$arItems = $this->filterItemList($arItems, $filterStr);

		$this->sortItemList($arItems, $arSort["FIELD"], $arSort["ORDER"]);


		$this->arResult["SORT"] = $arSort;
		$this->arResult["SORT"]["URLS"] = $this->generateSortUrls($arSort["FIELD"], $arSort["ORDER"]);
		$this->arResult["ITEMS"] = $arItems;


		$this->includeComponentTemplate();
    }


	private function handleSort($request, $session)
	{
		function checkParam($p) {
			return isset($p) && trim($p) !== "" ? $p : false;
		}

		$params = [
			"FIELD" => "STATUS",
			"ORDER" => "ASC"
		];

		$result = [];
		foreach($params as $param => $defaultValue) {
			$fromSession = checkParam($session["SORT_$param"]);
			$fromRequest = checkParam($request["SORT_$param"]);

			if 		($fromRequest) 	$result[$param] = $fromRequest;
			elseif 	($fromSession) 	$result[$param] = $fromSession;
			else 					$result[$param] = $defaultValue;

			$session->set("SORT_$param", $result[$param]);
		}

		return $result;
	}

	private function generateSortUrls(string $sortField = "STATUS", string $sortOrder = "ASC") {
    	$arFields = [
			"STATUS",
			"CLIENT",
			"INSURER",
			"CONTRACT_TYPE",
			"RESULT",
			"COMPENSATION",
			"DATE_OPENED",
			"DATE_CLOSED",
			"DATE_DIFF",
		];

    	$result = [];
    	foreach ($arFields as $fieldCode) {
			$isOrderAsc = $sortOrder === "ASC";
			$orderForUrl = $sortField === $fieldCode ? ($isOrderAsc ? "DESC" : "ASC") : "ASC";

			$result[$fieldCode] = $GLOBALS["APPLICATION"]->GetCurPageParam(
				"SORT_FIELD=$fieldCode&SORT_ORDER=$orderForUrl",
				["SORT_FIELD", "SORT_ORDER"]
			);
		}

    	return $result;
	}

    private function assembleItemList() {
		$arLosts = $this->getLosts();
		$lostIds = array_keys($arLosts);

		$statuses = $this->getStatusListOfLost();
		$history = $this->getHistoryOfLosts($lostIds);
		$lostLeaders = $this->getLeadersOfLosts($lostIds);

		$clientCode = CUserRole::getClientGroupCode();
		$insurerCode = CUserRole::getInsurerGroupCode();


		$result = [];
		foreach ($arLosts as $lostId => $arLost) {
			$status = $arLost["STATUS"];
			$compensationRaw = $arLost["COMPENSATION"];
			$compensationFormatted = isset($compensationRaw) ? number_format($compensationRaw, 0, "", " ") : "";

			/** @var DateTime $dateOpened */
			$dateOpened = $history[$lostId][self::START_STATUS];
			/** @var DateTime $dateClosed */
			$dateClosed = $history[$lostId][self::FINISH_STATUS];

			$result[] = [
				"ID" => $lostId,
				"STATUS" => $statuses[$status]["UF_XML_ID"],
				"CLIENT" => $lostLeaders[$lostId][$clientCode]["NAME"],
				"INSURER" => $lostLeaders[$lostId][$insurerCode]["NAME"],
				"LOGO" => $lostLeaders[$lostId][$insurerCode]["LOGO"],
				"CONTRACT_TYPE" => $arLost["CONTRACT_TYPE"],
				"CONTRACT_CODE" => $arLost["CONTRACT_CODE"],
				"RESULT" => $arLost["RESULT"],
				"COMPENSATION" => $compensationFormatted,
				"DATE_OPENED" => $dateOpened ? $dateOpened->format("d.m.Y") : "",
				"DATE_CLOSED" => $dateClosed ? $dateClosed->format("d.m.Y") : "",
				"DATE_DIFF" => $history[$lostId]["diff"],
				"SORT" => [
					"STATUS" => $statuses[$status]["UF_SORT"],
					"CLIENT" => $lostLeaders[$lostId][$clientCode]["NAME"],
					"INSURER" => $lostLeaders[$lostId][$insurerCode]["NAME"],
					"CONTRACT_TYPE" => $arLost["CONTRACT_TYPE"],
					"RESULT" => $arLost["RESULT"],
					"COMPENSATION" => $compensationRaw,
					"DATE_OPENED" => $dateOpened ? $dateOpened->format("Y-m-d") : "",
					"DATE_CLOSED" => $dateClosed ? $dateClosed->format("Y-m-d") : "",
					"DATE_DIFF" => $history[$lostId]["diff"],
				]
			];
		}

		return $result;
	}

	private function filterItemList(array $arItems, ?string $filterStr) {
		if (is_null($filterStr) || !strlen($filterStr)) return $arItems;


		return array_filter($arItems, function($item) use ($filterStr) {
			$fieldsToFilter = [
				"CLIENT",
				"INSURER",
				"CONTRACT_TYPE",
				"RESULT",
				"DATE_OPENED",
				"DATE_CLOSED"
			];

			foreach ($fieldsToFilter as $field) {
				if (mb_stripos($item[$field], $filterStr) !== false) {
					return true;
				}
			}

			return false;
		});
	}

	private function sortItemList(array &$arItems, string $sortField, string $sortOrder) {
		uasort($arItems, function ($a, $b) use ($sortField, $sortOrder) {
			$a = $a["SORT"][$sortField];
			$b = $b["SORT"][$sortField];

			if ($a == $b) return 0;


			return ($sortOrder === "ASC")
				? ($a > $b ? 1 : -1)
				: ($a < $b ? 1 : -1)
				;
		});
	}

    private function getStatusListOfLost() {
		$entityDataClass = $this->getHlDataClass("e_lost_status");
		$res = $entityDataClass::getList();

		$statusList = [];
		while ($row = $res->fetch()) {
			$code = $row["UF_XML_ID"];
			$statusList[$code] = $row;
		}

		return $statusList;
	}

	private function getCompanyTypes() {
		$arParties = Company::getPropertyList("TYPE");

		$companyPartyCodesById = [];
		foreach ($arParties as $arParty) {
			$id = $arParty["ID"];
			$companyPartyCodesById[$id] = $arParty["XML_ID"];
		}

		return $companyPartyCodesById;
	}

	private function getLeadersOfLosts(array $arIds) {
		$companyPartyCodesById = $this->getCompanyTypes();


    	$arLeaders = CLostParticipant::getElementsByConditions([
    		"PROPERTY_TARGET_ID" => $arIds,
			"!PROPERTY_IS_LEADER" => false,
		],
		[],
		[
			"PROPERTY_TARGET_ID",
			"PROPERTY_PARTICIPANT_ID.NAME",
			"PROPERTY_PARTICIPANT_ID.PROPERTY_LOGO",
			"PROPERTY_PARTICIPANT_ID.PROPERTY_TYPE"
		]);


    	$result = [];
		foreach ($arLeaders as $arLeader) {
			$partyId = $arLeader["PROPERTY_PARTICIPANT_ID_PROPERTY_TYPE_ENUM_ID"];
			$lostId = $arLeader["PROPERTY_TARGET_ID_VALUE"];
			$party = $companyPartyCodesById[$partyId];

			$result[$lostId][$party] = [
				"NAME" => $arLeader["PROPERTY_PARTICIPANT_ID_NAME"],
				"LOGO" => $arLeader["PROPERTY_PARTICIPANT_ID_PROPERTY_LOGO_VALUE"],
			];
    	}

		return $result;
	}

	private function getLosts() {
		$targetIds = [];

		$arTargets = $this->userRole->isSuperUser()
			? CParticipation::getTargetsByCompany($this->companyId, CLost::class)["TARGETS"]
			: CParticipation::getTargetsByUser($this->userId, CLost::class)["TARGETS"]
		;

		foreach ($arTargets as $arTarget) {
			$targetIds[] = $arTarget["ID"];
		}


		$arLosts = Lost::getElementsByConditions([
			"ID" => $targetIds ?: false
		], [], [
			"ID",
			"PROPERTY_STATUS",
			"PROPERTY_RESULT",
			"PROPERTY_COMPENSATION",
			"PROPERTY_CONTRACT.NAME",
			"PROPERTY_CONTRACT.PROPERTY_TYPE",
		]);


		$result = [];
		foreach($arLosts as $arLost) {
			$id = $arLost["ID"];
			$result[$id] = [
				"STATUS" 		=> $arLost["PROPERTY_STATUS_VALUE"],
				"CONTRACT_CODE" => $arLost["PROPERTY_CONTRACT_NAME"],
				"CONTRACT_TYPE" => $arLost["PROPERTY_CONTRACT_PROPERTY_TYPE_VALUE"],
				"RESULT" 		=> $arLost["PROPERTY_RESULT_VALUE"],
				"COMPENSATION" 	=> $arLost["PROPERTY_COMPENSATION_VALUE"],
			];
		}

		return $result;
	}

	private function getHistoryOfLosts(array $ids) {
		$historyTable = $this->getHlDataClass("e_history_lost_status");
		$statusesTable = $this->getHlDataClass("e_lost_status");

		$res = $historyTable::query()
			->setSelect([
				"LOST_ID" => "UF_LOST_ID",
				"STATUS" => "STATUSES.UF_XML_ID",
			])
			->addSelect(Query::expr()->max("UF_DATE"), "DATE")
			->registerRuntimeField(new ReferenceField(
				'STATUSES',
				$statusesTable,
				Join::on('this.UF_CODE_ID', 'ref.ID')
			))
			->whereIn("STATUSES.UF_XML_ID", [ self::START_STATUS, self::FINISH_STATUS ] )
			->whereIn("UF_LOST_ID", $ids)
			->exec();

		$history = [];
		while ($row = $res->fetch()) {
			$lostId = $row["LOST_ID"];
			$statusCode = $row["STATUS"];
			$history[$lostId][$statusCode] = $row["DATE"];
		}

		foreach($history as $lostId => &$arStatuses) {
			foreach([self::START_STATUS, self::FINISH_STATUS] as $statusCode) {
				if ($date = $arStatuses[$statusCode]) {
					$arStatuses[$statusCode] = new DateTime( $date->format("d.m.Y") );
				}
			}

			if (!$arStatuses[self::START_STATUS]) continue;

			$dateToCountDiff = $arStatuses[self::FINISH_STATUS] ?: new DateTime();

			$diff = date_diff(
				$arStatuses[self::START_STATUS],
				$dateToCountDiff
			);

			$arStatuses["diff"] = $diff->days + 1;
		}

		return $history;
	}

	private function getHlDataClass($table) {
		Loader::IncludeModule("highloadblock");

		$hlblock = HighloadBlockTable::getList(["filter" => ["TABLE_NAME" => $table]])->fetch();
		$entity = HighloadBlockTable::compileEntity($hlblock);
		return $entity->getDataClass();
	}

	private function handleAjaxRequest($request) {
		$targetId = $request->get("target-id");

		$arParticipants = $this->getParticipants($targetId);

		$arRivals = array_merge(
			$arParticipants[CUserRole::getInsurerGroupCode()],
			$arParticipants[CUserRole::getAdjusterGroupCode()]
		);


		$this->arResult["ITEMS"] = $arRivals;

		$this->includeComponentTemplate("rivals-popup");
	}

	private function getParticipants(int $lostId) {
		$companyPartyCodesById = $this->getCompanyTypes();

		$arParticipants = CLostParticipant::getElementsByConditions([
			"PROPERTY_TARGET_ID" => $lostId
		],
		[],
		[
			"ID",
			"PROPERTY_PARTICIPANT_ID",
			"PROPERTY_PARTICIPANT_ID.NAME",
			"PROPERTY_PARTICIPANT_ID.PROPERTY_TYPE",
			"PROPERTY_PARTICIPANT_ID.PROPERTY_LOGO",
			"PROPERTY_CURATORS",
			"PROPERTY_CURATOR_LEADER",
		]);

		$currParticipantFound = false;

		$result = [];
		foreach ($arParticipants as $arParticipant) {
			$participantId = $arParticipant["PROPERTY_PARTICIPANT_ID_VALUE"];
			$partyId = $arParticipant["PROPERTY_PARTICIPANT_ID_PROPERTY_TYPE_ENUM_ID"];
			$party = $companyPartyCodesById[$partyId];

			$curator = $arParticipant["PROPERTY_CURATORS_VALUE"];
			$curatorLeader = $arParticipant["PROPERTY_CURATOR_LEADER_VALUE"];

			$participationConfirmed = $this->userRole->isSuperUser()
				? $this->companyId == $participantId
				: $this->userId == $curator || $this->userId == $curatorLeader
			;

			if ($participationConfirmed) {
				$currParticipantFound = true;
			}

			$result[$party][$participantId] = [
				"NAME" => $arParticipant["PROPERTY_PARTICIPANT_ID_NAME"],
				"LOGO" => $arParticipant["PROPERTY_PARTICIPANT_ID_PROPERTY_LOGO_VALUE"]
			];
		}

		if (!$currParticipantFound) {
			return [];
		}

		return $result;
	}


}
