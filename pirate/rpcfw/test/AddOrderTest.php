<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: AddOrderTest.php 21394 2012-05-25 12:24:17Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/AddOrderTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-25 20:24:17 +0800 (五, 2012-05-25) $
 * @version $Revision: 21394 $
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

class AddOrderTest extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 3)
		{
			exit('argv err.');
		}
		
		$uid = $arrOption[0];		
		$gold = $arrOption[1];
		$exGold = $arrOption[2];
		
		$orderId = "TEST_02_" . strftime("%Y%m%d%H%M%S") . rand(10000, 99999);
		
		RPCContext::getInstance()->executeTask($uid, 'user.addGold4BBpay', array($uid, $orderId, $gold, $exGold));
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */