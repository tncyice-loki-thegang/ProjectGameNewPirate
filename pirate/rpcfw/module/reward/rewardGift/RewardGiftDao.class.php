<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: RewardGiftDao.class.php 17286 2012-03-24 08:47:41Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/rewardGift/RewardGiftDao.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-24 16:47:41 +0800 (六, 2012-03-24) $
 * @version $Revision: 17286 $
 * @brief 
 *  
 **/

class RewardGiftDao
{
	const tblName = 't_reward_gift';
	
	public static function getArr($uid, $arrId, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)->where('uid', '=', $uid)
			->where('id', 'in', $arrId)->query();
		if (empty($ret))
		{
			return $ret;
		}
		return Util::arrayIndex($ret, 'id');
	}
	
	public static function insert($uid, $id, $arrField)
	{
		$arrField['id'] = $id;
		$arrField['uid'] = $uid;
		$data = new CData();
		$data->insertInto(self::tblName)->values($arrField)->query();
	}
	
	public static function update($uid, $id, $arrField)
	{
		$data = new CData();
		$data->update(self::tblName)->set($arrField)->
			where('uid', '=', $uid)->where('id', '=', $id)->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */