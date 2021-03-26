<?php

namespace Itrack\Custom\Participation;

use Itrack\Custom\InfoBlocks\BaseInfoBlockClass;
use Exception;

abstract class ATarget extends BaseInfoBlockClass implements ITarget
{
	/** @var int */
	protected $targetId;

	protected static $ibBlockType = "main_data";

	public function __construct(int $targetId)
	{
		$this->targetId = $targetId;
	}

	public function getId()
	{
		return $this->targetId;
	}

	public function checkAddArray(array $arAdd)
	{
		foreach ($arAdd as $party => $arCompanies) {
			$leadersInParty = [];

			foreach ($arCompanies as $companyId => $arCompany) {

				$leadersWithinOneCompany = count($arCompany["LEADERS"]);
				if ($leadersWithinOneCompany > 1) {
					throw new Exception("There can be only one leader within one company id=$companyId");
				} elseif ($leadersWithinOneCompany <= 0) {
					throw new Exception("There must be a leader within a company id=$companyId");
				}

				if (!$arCompany["IS_LEADER"]) continue;

				$leadersInParty[] = $companyId;
			}

			$leadersWithinOneParty = count($leadersInParty);
			if ($leadersWithinOneParty > 1) {
				throw new Exception("There can be only one leader within one party ($party)");
			} elseif ($leadersWithinOneParty <= 0) {
				throw new Exception("There must be a leader within a $party party");
			}
		}
	}

}
