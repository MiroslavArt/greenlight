<?php

namespace Itrack\Custom\UserAccess\Tables;

class UserLostAcceptanceTable extends AbstractUserAccessTable
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'e_user_lost_acceptance_access';
	}
}
