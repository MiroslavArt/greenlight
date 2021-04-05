<?php


namespace Itrack\Custom;


use Itrack\Custom\InfoBlocks\Company;
use Itrack\Custom\InfoBlocks\Lost;
use Itrack\Custom\InfoBlocks\NotificationTemplate;
use Itrack\Custom\Participation\CLost;
use Itrack\Custom\Participation\CParticipation;
use Itrack\Custom\UserAccess\Tables\UserLostNotificationTable;

class CNotification
{
	public static function send(string $code, array $users, string $comment, int $lostId, int $documentId = null) {
		$text = self::compileText($code, $comment, $lostId, $documentId);

		$usersWithEnabledNotification = UserLostNotificationTable::getAllUsers($lostId);
		$users = array_intersect($users, $usersWithEnabledNotification);

		$userEmails = CUserEx::getEmails($users);

		foreach ($users as $userId) {
			\CEvent::Send(
				"WORKFLOW_NOTIFICATION",
				SITE_ID,
				[
					"CURATOR_EMAIL" => $userEmails[$userId],
					"TEXT" => $text
				]
			);
		}
	}

	private static function compileText(string $code, string $comment, int $lostId, int $documentId = null) {
		list($title, $template, $link) = self::getTemplate($code);

		$template = str_replace("#COMMENT#", $comment, $template);

		$clientId = self::getClientId($lostId);
		$link = str_replace("#CLIENT#", $clientId, $link);

		$contractId = self::getContractId($lostId);
		$link = str_replace("#CONTRACT#", $contractId, $link);


		if ($lostId) {
			$link = str_replace("#LOST#", $lostId, $link);
		}

		if ($documentId) {
			$link = str_replace("#DOC#", $documentId, $link);
		}

		$link = self::getDomain() . $link;

		return str_replace("#LINK#", $link, $template);
	}

	private static function getClientId(int $lostId)
	{

		$companyParties = Company::getPropertyList("TYPE");

		$clientPartyId = null;
		foreach ($companyParties as $companyParty) {
			if ($companyParty["XML_ID"] === CUserRole::getClientGroupCode()) {
				$clientPartyId = $companyParty["ID"];
				break;
			}
		}


		$participants = (new CParticipation(new CLost($lostId)))->getParticipants([
			"ID",
			"IBLOCK_ID",
			"PROPERTY_PARTICIPANT_ID.PROPERTY_TYPE",
		]);

		foreach ($participants as $arParticipant) {
			$partyId = $arParticipant["PROPERTY_PARTICIPANT_ID_PROPERTY_TYPE_ENUM_ID"];

			if ($clientPartyId === $partyId) {
				return $arParticipant["PROPERTIES"]["PARTICIPANT_ID"]["VALUE"];
			}
		}
	}

	private static function getDomain() {
		$context = \Bitrix\Main\Application::getInstance()->getContext()::getCurrent();
		$server = $context->getServer();

		$isHttps = !empty($server->get("HTTPS")) && $server->get("HTTPS") !== "off";

		return sprintf(
			"%s://%s",
			$isHttps ? "https": "http",
			$server->getServerName()
		);
	}

	private static function getTemplate(string $code) {
		$notification = NotificationTemplate::getElementByCode($code);

		return [
			$notification["NAME"],
			$notification["~DETAIL_TEXT"],
			$notification["PROPERTIES"]["LINK"]["VALUE"]
		];
	}

	private static function getContractId(int $lostId): int {
		return Lost::getElementsByConditions(["PROPERTY_CONTRACT"])[0]["PROPERTIES"]["CONTRACT"]["VALUE"];
	}
}
