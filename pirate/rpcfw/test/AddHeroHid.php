<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: AddHeroHid.php 22668 2012-06-21 06:25:55Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/AddHeroHid.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-06-21 14:25:55 +0800 (四, 2012-06-21) $
 * @version $Revision: 22668 $
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
		
		if (in_array($hid, $va_user['recruit_hero_order']))
		{
			echo "has the hero " . $hid . "\n";
			exit(0);
		}
		
		$va_user['recruit_hero_order'][] = $hid;

		UserDao::updateUser($uid, array('va_user'=>$va_user));
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */