<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixMasterHid0.php 24297 2012-07-20 03:08:30Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixMasterHid0.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-20 11:08:30 +0800 (五, 2012-07-20) $
 * @version $Revision: 24297 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class FixMasterHid0 extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$uid = 0;
		$level = 0;
		
		if (count($arrOption)<1)
		{
			exit("usage: uid level=1\n");
		}
		
		$uid = intval($arrOption[0]);
		$level = UserConf::INIT_MASTER_HERO_LEVEL;
		
		if (isset($arrOption[1]))
		{
			$level = intval($arrOption);
		}
		
		
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);

		$ret = UserDao::getUserFieldsByUid($uid, array('utid', 'master_hid', 'uid', 'va_user'));
		if (empty($ret))
		{
			exit("fail to find by uid\n");
		}
		
		if ($ret['master_hid']!=0)
		{
			exit("master_hid !=0\n");
		}
		
		$htid = UserConf::$USER_INFO[$ret['utid']][1];
		$masterHid = HeroUtil::recruitForInit($uid, $htid, array('level'=>$level));		

		$va_user = $ret['va_user'];
		$va_user['recruit_hero_order'] = array($masterHid);
		UserDao::updateUser($uid, array('master_hid'=>$masterHid, 'va_user'=>$va_user));
		
		
		FormationLogic::addNewFormation($uid, FormationConf::INIT_FOR_ID, $masterHid);
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */