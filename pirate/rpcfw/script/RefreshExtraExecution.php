<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: RefreshExtraExecution.php 26177 2012-08-24 06:51:40Z HongyuLan $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/RefreshExtraExecution.php $
 * @author $Author: HongyuLan $(liuyang@babeltime.com)
 * @date $Date: 2012-08-24 14:51:40 +0800 (五, 2012-08-24) $
 * @version $Revision: 26177 $
 * @brief 
 *  
 **/

/**
 * 这个脚本每日执行，给前端推送补给时刻
 *
 */
class RefreshExtraExecution extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		// 给前端推送数据
		//$proxy = new ServerProxy();
		//$proxy->broadcastExecuteRequest('user.extraExecution', array());
		
		Logger::info('begin refresh extra execution');
		
		$offset = 0;
		$limit = CData::MAX_FETCH_SIZE;
		
		//最多循环10W次， 数据库最只能有有10W * $limit用户
		$MAX_LOOP = 100000;
		$uidStart = FrameworkConfig::MIN_UID;
		
		do 
		{
			Logger::trace('uid start:%d', $uidStart);
			$arrUserInfo = UserDao::getArrUserByOffsetUid($uidStart, $limit, array('uid', 'status'));
			
			foreach ($arrUserInfo as $userInfo)
			{
				if ($userInfo['status']==UserDef::STATUS_ONLINE)
				{
					RPCContext::getInstance()->executeTask($userInfo['uid'], 'user.extraExecution', array());
				}
			}
			
			//所有用户已经处理完了
			if ($limit != count($arrUserInfo))
			{
				break;
			}
			
			$end = end($arrUserInfo);
			$endUid = $end['uid'];			
			
			$uidStart = $endUid + 1;
		}
		while (--$MAX_LOOP > 0);
		
		Logger::info('end refresh extra execution');
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */