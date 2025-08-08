<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: delHeroHid.php 24274 2012-07-19 10:52:53Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/delHeroHid.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-19 18:52:53 +0800 (四, 2012-07-19) $
 * @version $Revision: 24274 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript AddHeroTest.php uid htid
 * Enter description here ...
 * @author idyll
 *
 */

class AddHeroHid extends BaseScript
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
			exit("usage: uid hid\n");
		}
		
		if (isset($arrOption[1]))
		{
			$hid = intval($arrOption[1]);
		}
		else
		{
			exit("usage: uid hid\n");
		}		
		
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);		
		
		$user = UserDao::getUserFieldsByUid($uid, array('va_user'));
		if (empty($user))
		{
			echo "fail to get user by uid:" . $uid . "\n";
			exit(0);
		}
		
		$va_user = $user['va_user'];
		
		$pos = array_search($hid, $va_user['recruit_hero_order']);
		if ($pos===false)
		{
			echo "has not the hero " . $hid . "\n";
			exit(0);
		}
		
		unset($va_user['recruit_hero_order'][$pos]);
		
		$va_user['recruit_hero_order'][$pos] = array_merge($va_user['recruit_hero_order']);

		UserDao::updateUser($uid, array('va_user'=>$va_user));
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */