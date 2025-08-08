<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: AddOrder.php 25439 2012-08-09 10:57:50Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/AddOrder.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-09 18:57:50 +0800 (四, 2012-08-09) $
 * @version $Revision: 25439 $
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

class AddOrder extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 4)
		{
			exit('argv err.');
		}
		
		$uid = $arrOption[0];
		$orderId = $arrOption[1];
		$gold = $arrOption[2];
		$exGold = $arrOption[3];		
		
		RPCContext::getInstance()->executeTask($uid, 'user.addGold4BBpay', array($uid, $orderId, $gold, $exGold));
		Logger::info('add order:%s', $orderId);
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */