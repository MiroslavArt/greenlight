<?php

namespace Itrack\Custom\UserAccess\Tables;

class UserContractAcceptanceTable extends AbstractUserAccessTable
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'e_user_contract_acceptance_access';
	}
}
