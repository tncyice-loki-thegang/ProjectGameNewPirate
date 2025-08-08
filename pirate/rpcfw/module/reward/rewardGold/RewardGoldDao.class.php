<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: RewardGoldDao.class.php 21234 2012-05-24 11:54:40Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/rewardGold/RewardGoldDao.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-24 19:54:40 +0800 (四, 2012-05-24) $
 * @version $Revision: 21234 $
 * @brief 
 *  
 **/

class RewardGoldDao
{
	const tblName = 't_reward_gold';

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