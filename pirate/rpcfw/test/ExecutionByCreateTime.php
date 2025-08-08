<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ExecutionByCreateTime.php 23047 2012-07-01 04:58:24Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/ExecutionByCreateTime.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-01 12:58:24 +0800 (日, 2012-07-01) $
 * @version $Revision: 23047 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */
class ExecutionByCreateTime extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$num=1000;
		$offset = 0;
		$limit = 100;
		
		$exe = 10;
		
		Logger::fatal('attention. add execution for all user.');
		while ( $num-- > 0 )
		{
			$arrUserInfo = UserDao::getArrUser($offset, $limit, array('uid', 'create_time'));
			//Logger::debug('vip auto ics, arr user:%s', $arrUserInfo);
			if (empty($arrUserInfo))
			{
				Logger::fatal('attention. exit addExecution');
				break;
			}
			
			foreach ($arrUserInfo as $userInfo)
			{
				if ($userInfo['create_time'] > 1341116100)
				{
					Logger::debug('skip %s', $userInfo);
					continue;
				}
				
				Logger::debug('idyll userInfo:%s', $userInfo);
				
				$user = EnUser::getUserObj($userInfo['uid']);
				
				$user->addExecution($exe);
				$user->update();
				
				Logger::info('add execution %d for uid %d', $exe, $userInfo['uid']);
			
			}
			
			$offset += $limit;
			
			sleep(1);
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */