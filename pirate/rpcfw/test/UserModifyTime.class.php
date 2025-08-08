<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UserModifyTime.class.php 23010 2012-06-29 09:55:49Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/UserModifyTime.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2012-06-29 17:55:49 +0800 (五, 2012-06-29) $
 * @version $Revision: 23010 $
 * @brief
 *
 **/

class UserModifyTime extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$MAX_EXEC_TIME = 65536;
		$USER_PRE_QUERY = 64;
		$lastUid = 0;
		$uidCount = 0;
		$uidStart = 0;


		for ( $i = 0; $i < $MAX_EXEC_TIME; $i++ )
		{
			$uids = UserDao::getArrUser($uidStart, $USER_PRE_QUERY, array('uid', 'create_time', 'execution_time'));
			if ( empty($uids) )
			{
				Logger::INFO('EXEC END!LAST UID:%d, USER COUNT:%d', $lastUid, $uidCount);
				return;
			}
			else
			{
				foreach ( $uids as $info )
				{
					if ($info['execution_time'] > 1340964000)
					{
						$proxy = new ServerProxy();
						$proxy->closeUser($info['uid']);
						Logger::info('old user info:%s', $info);
						UserDao::updateUser($info['uid'], array('execution_time'=>1340964000));
					}
				}
				$uidStart += count($uids);
			}
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */