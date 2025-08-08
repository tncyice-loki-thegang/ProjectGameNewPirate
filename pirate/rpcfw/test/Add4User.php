<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Add4User.php 26431 2012-08-31 02:49:43Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/Add4User.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-31 10:49:43 +0800 (五, 2012-08-31) $
 * @version $Revision: 26431 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */
class Add4User extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$num=1000;
		$offset = 0;
		$limit = 100;
		
		$exe = 100;
		$belly = 8000000;
		$gold = 2000;
		
		
		Logger::fatal('attention. add  for all user.');
		while ( $num-- > 0 )
		{
			$arrUserInfo = UserDao::getArrUser($offset, $limit, array('uid'));
			//Logger::debug('vip auto ics, arr user:%s', $arrUserInfo);
			if (empty($arrUserInfo))
			{
				Logger::fatal('attention. exit add');
				break;
			}
			
			foreach ($arrUserInfo as $userInfo)
			{
				Logger::debug('idyll userInfo:%s', $userInfo);
				
				$user = EnUser::getUserObj($userInfo['uid']);
				
				$user->addExecution($exe);
				$user->addBelly($belly);
				$user->addGold($gold);
				
				$user->update();
				
				Logger::info('uid:%d', $userInfo['uid']);
			
			}
			
			$offset += $limit;
			
			sleep(1);
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */