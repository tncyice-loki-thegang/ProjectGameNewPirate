<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: AddHeroTest.php 21709 2012-05-31 09:24:55Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/AddHeroTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-31 17:24:55 +0800 (四, 2012-05-31) $
 * @version $Revision: 21709 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript AddHeroTest.php uid htid
 * Enter description here ...
 * @author idyll
 *
 */

class AddHeroTest extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$uid = 0;
		$htid = 0;
		if (isset($arrOption[0]))
		{
			$uid = intval($arrOption[0]);
		}
		else
		{
			exit("usage: uid htid\n");
		}
		
		if (isset($arrOption[1]))
		{
			$htid = intval($arrOption[1]);
		}
		else
		{
			exit("usage: uid htid\n");
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
		
		if (in_array($htid, $va_user['heroes']))
		{
			echo "has the hero " . $htid . "\n";
			exit(0);
		}
		
		$va_user['heroes'][] = $htid;

		UserDao::updateUser($uid, array('va_user'=>$va_user));
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */