<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: VipModifyTest.php 20118 2012-05-09 13:20:32Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/VipModifyTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-09 21:20:32 +0800 (三, 2012-05-09) $
 * @version $Revision: 20118 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript VipMOdifyTest uname vip
 * Enter description here ...
 * @author idyll
 *
 */

class VipModifyTest extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (!isset($arrOption[0]) && !isset($arrOption[1]))
		{
			exit('usage: uname vip');
		}
		
		$uname = $arrOption[0];
		$vip = $arrOption[1];
		
		echo 'name:' . $uname . "\tvip:" . $vip .   "\n";				
		$uid = UserDao::unameToUid($uname);
		
		//kick 
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);

		$orderId = rand(10000, 99999);
		$orderId = 'test_' . $orderId;
		
		Logger::info('set vip for uname:%s, uid %d, orderId: %s, $vip: %d', $uname, $uid, $orderId, $vip);
		
		$user = EnUser::getUserObj($uid);
		if ($user->getVip() >= $vip)
		{
			Logger::warning('fail to set vip %d, cur vip is %d', $vip, $user->getVip());
			throw new Exception('fake');
		}
		
		$sumGold = User4BBpayDao::getSumGoldByUid($uid);
		$costGold = btstore_get()->VIP[$vip]['total_cost'];
		$needGold = $costGold - $sumGold;
		User4BBpayDao::update($uid, $orderId, $needGold, 0);		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */