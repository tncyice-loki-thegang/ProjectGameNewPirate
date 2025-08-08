<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: RewardSignDao.class.php 17286 2012-03-24 08:47:41Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/rewardSign/RewardSignDao.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-24 16:47:41 +0800 (六, 2012-03-24) $
 * @version $Revision: 17286 $
 * @brief 
 *  
 **/

class RewardDailySignDao
{
	const tblName = 't_reward_dailysign';

	public static function get($uid, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)->where('uid', '=', $uid)->query();
		if (empty($ret))
		{
			return array();
		}
		return $ret[0];
	}
	
	public static function insert($uid, $arrField)
	{
		$arrField['uid'] = $uid;
		$data = new CData();
		$data->insertInto(self::tblName)->values($arrField)->query();
	}
	
	public static function update($uid, $arrField)
	{
		$data = new CData();
		$data->update(self::tblName)->set($arrField)->where('uid', '=', $uid)->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */