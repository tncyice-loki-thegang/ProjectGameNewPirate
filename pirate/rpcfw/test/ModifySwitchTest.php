<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ModifySwitchTest.php 22289 2012-06-12 09:51:11Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/ModifySwitchTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-06-12 17:51:11 +0800 (二, 2012-06-12) $
 * @version $Revision: 22289 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript xx uid switch_id
 * Enter description here ...
 * @author idyll
 *
 */

class ModifySwitchTest extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$uid = 0;
		$hid = 0;
		if (isset($arrOption[0]))
		{
			$uid = intval($arrOption[0]);
		}
		else
		{
			exit("usage: uid switch_id\n");
		}
		
		if (isset($arrOption[1]))
		{
			$switchId = intval($arrOption[1]);
		}
		else
		{
			exit("usage: uid switch_id\n");
		}		
		
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);		
		
		RPCContext::getInstance()->setSession('global.uid', $uid);
		SwitchLogic::setArrValue(array($switchId));
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */