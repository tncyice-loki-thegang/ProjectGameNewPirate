<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: AddOrder4FH.php 21742 2012-06-01 07:38:21Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/AddOrder4FH.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-06-01 15:38:21 +0800 (五, 2012-06-01) $
 * @version $Revision: 21742 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript GoldModifyTest.php uid gold
 * 补充值返还
 * Enter description here ...
 * @author idyll
 *
 */

class AddOrder4FH extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 2)
		{
			exit('argv err.');
		}
		
		$uid = $arrOption[0];		
		$exGold = $arrOption[1];
		$gold = 0;
		
		$orderId = "TEST_10_" . strftime("%Y%m%d%H%M%S") . rand(10000, 99999);
		
		RPCContext::getInstance()->executeTask($uid, 'user.addGold4BBpay', array($uid, $orderId, $gold, $exGold));
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */