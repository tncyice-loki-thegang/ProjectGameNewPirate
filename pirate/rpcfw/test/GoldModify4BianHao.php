<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GoldModify4BianHao.php 20247 2012-05-11 11:37:57Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/GoldModify4BianHao.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-11 19:37:57 +0800 (五, 2012-05-11) $
 * @version $Revision: 20247 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript GoldModify4BianHao.php
 * Enter description here ...
 * @author idyll
 *
 */

class GoldModify4BianHao extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$gold = 5000;
		
		//$allName = array('乌索洛特', '珍妮芙米迪亚', '热血海贼03', '热血海贼04', '热血海贼05', '热血海贼06', '热血海贼07', '热血海贼08', '米兹塔维拉', '热血海贼10');
		
		$allName = array('九蛇丶籹帝', '俞氏', '神、慢点', '小白', '波波');

		foreach ($allName as $uname)
		{
			$uid = UserDao::unameToUid($uname);
			
			$proxy = new ServerProxy();
			$proxy->closeUser($uid);
			sleep(1);
			
			Logger::warning('set gold %d for uid:%d by script', $gold, $uid);
			UserDao::updateUser($uid, array('gold_num' => $gold));
		}
		echo "end\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */