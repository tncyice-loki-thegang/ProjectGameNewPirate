<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: RewardSignDao.class.php 17108 2012-03-22 14:27:10Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-26/module/reward/RewardSignDao.class.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-22 22:27:10 +0800 (Thu, 22 Mar 2012) $
 * @version $Revision: 17108 $
 * @brief 
 *  
 **/

class GrowUpPlanDao
{
	const tblName = 't_grow_up_plan';

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