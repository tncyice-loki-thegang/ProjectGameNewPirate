<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixBag.class.php 28725 2012-10-11 08:57:01Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixBag.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-10-11 16:57:01 +0800 (四, 2012-10-11) $
 * @version $Revision: 28725 $
 * @brief
 *
 **/

class FixBag extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		//最大循环执行次数
		$MAX_EXEC_TIME = 65536;
		//每次QUERY拉取的数量
		$USER_PRE_QUERY = DataDef::MAX_FETCH;

		$lastUid = 0;
		$uidCount = 0;
		$uidStart = 0;

		for ( $i = 0; $i < $MAX_EXEC_TIME; $i++ )
		{
			$uids = UserDao::getArrUser($uidStart, $USER_PRE_QUERY, array('uid'));
			if ( empty($uids) )
			{
				Logger::INFO('EXEC END!LAST UID:%d, USER COUNT:%d', $lastUid, $uidCount);
				return;
			}
			else
			{
				foreach ( $uids as $info )
				{
					$uid = $info['uid'];
					$bagInfo = BagDAO::selectBag(array('uid', 'gid'), array('uid', '=', $uid));
					if ( empty($bagInfo) )
					{
						Logger::INFO('user:%d not user bag!', $uid);
						continue;
					}
					else
					{
						$max_gid = 12;
						$used_gid = array();
						foreach ( $bagInfo as $data )
						{
							if ( $max_gid < $data['gid'] && $data['gid'] < 1000000 )
							{
								$max_gid = $data['gid'];
							}
							$used_gid[] = $data['gid'];
						}
						for ( $k = 1; $k <= $max_gid; $k++ )
						{
							if ( !in_array($k, $used_gid) )
							{
								$values = array('item_id' => 0,
				                    'uid' => $uid,
				                	'gid' => $k
				                );
								BagDAO::insertOrupdateBag($values);
								Logger::INFO('insert bag gid:%d to uid:%d!', $k, $uid);
							}
						}
					}
				}
				$uidStart += count($uids);
			}
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */