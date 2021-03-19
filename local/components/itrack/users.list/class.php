<?php

use \Itrack\Custom\CUserRole;
use \Itrack\Custom\InfoBlocks\Company;
use \Itrack\Custom\UserFieldValueTable;
use \Bitrix\Iblock\Elements\ElementCompanyTable;
use \Bitrix\Main\Loader;
use \Bitrix\Main\UserGroupTable;
use \Bitrix\Main\Entity;
use \Bitrix\Main\Entity\Query;
use \Bitrix\Main\Entity\ReferenceField;
use \Bitrix\Main\ORM\Query\Join;

class ItrUsersList extends CBitrixComponent
{
    public function executeComponent()
    {
		$request = \Bitrix\Main\Context::getCurrent()->getRequest();
		$searchQuery = $request->get("search") ?: '';

		$this->arResult['ITEMS'] = $this->getList($searchQuery);

		$this->includeComponentTemplate();
    }

    private function getList(string $searchQuery = '')
    {
		$q = $this->getBaseQuery();
		$this->filterQueryByUserRoles($q);
		$this->filterQueryBySearch($q, $searchQuery);
		$this->filterQueryByCompany($q);

		$result = $q->exec();
		$items = [];
		$companyIds = [];

		while ($row = $result->fetch())
		{
			$companyIds[] = $row["COMPANY_ID"];

			$userId = $row['ID'];
			$group = $row['GROUP_CODE'];

			$isSuper = in_array($group, CUserRole::getSuperGroups()) || $items[$userId]['IS_SUPER'];

			$groups = $items[$userId]['GROUPS'] ?: [];
			$groups[] = $group;

			unset($row['GROUP_CODE']);
			$items[$userId] = $row;
			$items[$userId]['GROUPS'] = $groups;
			$items[$userId]['IS_SUPER'] = $isSuper;
		}

		$companyLogos = $this->getCompanyLogos(array_unique($companyIds));

		foreach ($items as &$item) {
			$item["COMPANY_LOGO"] = $companyLogos[$item["COMPANY_ID"]];
		}

		return $items;
    }

    private function getBaseQuery() {
		return UserGroupTable::query()
			->setSelect([
				'ID' => 'USER.ID',
				'LOGIN' => 'USER.LOGIN',
				'GROUP_CODE' => 'GROUP.STRING_ID',
				'FIO' => 'USER.NAME',
				//'F' => 'USER.LAST_NAME',
				//'O' => 'USER.SECOND_NAME',
				'LAST_LOGIN' => 'USER.LAST_LOGIN',
				'COMPANY_ID' => 'IB_COMPANY.ID',
				'COMPANY_NAME' => 'IB_COMPANY.NAME'
			])
			->registerRuntimeField(new ReferenceField(
				'UF_VAL',
				UserFieldValueTable::class,
				Join::on('this.USER_ID', 'ref.VALUE_ID')
			))
			->registerRuntimeField((new ReferenceField(
				'IB_COMPANY',
				ElementCompanyTable::class,
				Join::on('this.UF_VAL.UF_COMPANY', 'ref.ID')
			))->configureJoinType(Join::TYPE_INNER))
		;
	}

	private function filterQueryByCompany($q) {
		$company = $this->arParams['COMPANY'];

		if ($company !== false) {
			$q->where('IB_COMPANY.ID', $company);
		}
	}

	private function filterQueryByUserRoles($q) {
    	$tab = $this->arParams["TAB"];

		switch($tab) {
			case CUserRole::getBrokerGroupCode():
				$q->whereIn('GROUP.STRING_ID', [
					CUserRole::getBrokerGroupCode(),
					CUserRole::getSuperBrokerGroupCode(),
				]);
				break;

			case CUserRole::getClientGroupCode():
				$q->whereIn('GROUP.STRING_ID', [
					CUserRole::getClientGroupCode(),
					CUserRole::getSuperClientGroupCode(),
				]);
				break;

			case CUserRole::getInsurerGroupCode():
				$q->whereIn('GROUP.STRING_ID', [
					CUserRole::getInsurerGroupCode(),
					CUserRole::getSuperInsurerGroupCode(),
				]);
				break;

			case CUserRole::getAdjusterGroupCode():
				$q->whereIn('GROUP.STRING_ID', [
					CUserRole::getAdjusterGroupCode(),
					CUserRole::getSuperAdjusterGroupCode(),
				]);
				break;
		}
	}

	private function filterQueryBySearch($q, string $searchQuery)
	{
		if ($searchQuery) {
			$searchQuery = "%$searchQuery%";

			$q->where(
				Query::filter()
					->logic('or')
					->where(Query::filter()->whereLike('IB_COMPANY.NAME', $searchQuery))
					->where(Query::filter()->whereLike('USER.LOGIN', $searchQuery))
					->where(Query::filter()->whereLike('USER.NAME', $searchQuery))
					->where(Query::filter()->whereLike('USER.SECOND_NAME', $searchQuery))
					->where(Query::filter()->whereLike('USER.LAST_NAME', $searchQuery))
			);
		}
	}

    private function getCompanyLogos(array $ids) {
    	$result = [];
    	$notFoundInCache = [];
    	$ttl = 86400;
		$initDir = "itrack";


		$cache = \Bitrix\Main\Data\Cache::createInstance();


    	foreach ($ids as $id) {
    		$cacheId = "COMPANY_LOGO_$id";

			if ($cache->initCache($ttl, $cacheId, $initDir)) {
				$result[$id] = $cache->getVars();
			}
			else {
				$notFoundInCache[] = $id;
			}
		}

    	if (count($notFoundInCache) && Loader::includeModule("iblock")) {
			$companies = Company::getElementsByConditions([ "ID" => $notFoundInCache ]);
			foreach ($companies as $company) {
				$logoFileId = $company['PROPERTIES']['LOGO']['VALUE'];
				$companyId = $company['ID'];
				$cacheId = "COMPANY_LOGO_$companyId";

				$cache->initCache($ttl, $cacheId, $initDir);
				$cache->startDataCache();
				$cache->endDataCache($logoFileId);

				$result[$companyId] = $logoFileId;
			}
		}

		return $result;
	}
}
