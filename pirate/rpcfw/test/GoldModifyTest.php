<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GoldModifyTest.php 20198 2012-05-11 03:12:42Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/GoldModifyTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-11 11:12:42 +0800 (五, 2012-05-11) $
 * @version $Revision: 20198 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript GoldModifyTest.php uid gold
 * gold 表示设置为多少金币
 * Enter description here ...
 * @author idyll
 *
 */

class GoldModifyTest extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$uid = 0;
		$gold = 0;
		if (isset($arrOption[0]))
		{
			$uid = intval($arrOption[0]);
		}
		else
		{
			exit("usage: uid gold\n");
		}
		
		if (isset($arrOption[1]))
		{
			$gold = intval($arrOption[1]);
		}
		else
		{
			exit("usage: uid gold\n");
		}		
		
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);		
		
		Logger::warning('set gold %d for uid:%d by script', $gold, $uid);
		UserDao::updateUser($uid, array('gold_num'=>$gold));		
		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */