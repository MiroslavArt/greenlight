<?php

namespace Itrack\Custom\UserAccess\Tables;

class UserLostNotificationTable extends AbstractUserAccessTable
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'e_user_lost_notification_access';
	}
}
