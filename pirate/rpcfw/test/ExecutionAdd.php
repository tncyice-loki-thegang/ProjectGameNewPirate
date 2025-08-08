<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ExecutionAdd.php 26700 2012-09-05 08:52:22Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/ExecutionAdd.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-05 16:52:22 +0800 (三, 2012-09-05) $
 * @version $Revision: 26700 $
 * @brief 
 *  
 **/

/**
 * vip加1,一直加到5
 * Enter description here ...
 * @author idyll
 *
 */
class ExecutionAdd extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$num=1000;
		$offset = 0;
		$limit = 100;
		
		//最近两天登录过的
		$TIME = 86400 * 2;		
		$exe = 10;
		
		Logger::fatal('attention. add execution for all user.');
		while ( $num-- > 0 )
		{
			$arrUserInfo = UserDao::getArrUser($offset, $limit, array('uid', 'last_login_time'));
			//Logger::debug('vip auto ics, arr user:%s', $arrUserInfo);
			if (empty($arrUserInfo))
			{
				Logger::fatal('attention. exit addExecution');
				break;
			}
			
			foreach ($arrUserInfo as $userInfo)
			{
				Logger::debug('idyll userInfo:%s', $userInfo);
				if ($userInfo['last_login_time'] + $TIME < Util::getTime())
				{
					Logger::debug('because of login time, skip uid:%d', $userInfo['uid']);
					continue;
				}
				
				
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