<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroGoodwillModify.php 24260 2012-07-19 09:10:27Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/HeroGoodwillModify.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-19 17:10:27 +0800 (四, 2012-07-19) $
 * @version $Revision: 24260 $
 * @brief 
 *  
 **/

/**
 * 用法： hid status
 * Enter description here ...
 * @author idyll
 *
 */

class HeroGoodwillModify extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$uid = 0;
		$hid = 0;
		$status = 0;
		
		if (count($arrOption)!=4)
		{
			exit("usage: uid hid status\n");
		}
	
		$uid = intval($arrOption[0]);
		$hid = intval($arrOption[1]);
		$gwLevel = intval($arrOption[2]);
		$gwExp = intval($arrOption[3]);		
		
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);		
		
		$ret = HeroDao::getByHid($hid, array('uid', 'va_hero'));
		if (empty($ret))
		{
			exit("fail to get hero\n");
		}
		
		if ($ret['uid']!=$uid)
		{
			exit("uid hid err\n");
		}
		
		$va_hero = $ret['va_hero'];
		$va_hero['goodwill']['level'] = $gwLevel;
		$va_hero['goodwill']['exp'] = $gwExp;
		
		HeroDao::update($hid, array('va_hero'=>$va_hero));
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */