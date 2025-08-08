<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: SoulDao.class.php 25290 2012-08-08 02:30:21Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/soul/SoulDao.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-08 10:30:21 +0800 (三, 2012-08-08) $
 * @version $Revision: 25290 $
 * @brief 
 *  
 **/

class SoulDao
{
	const tblName = 't_soul';
	
	public static function  insert($uid, $arrField)
	{
		$data = new CData();
		$arrField['uid'] = $uid;
		$data->insertInto(self::tblName)->values($arrField)->query();
	}
	
	public static function update($uid, $arrField)
	{
		$data = new CData();
		$data->update(self::tblName)->set($arrField)->where('uid', '=', $uid)->query();
	}
	
	public static function get($uid, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)->where('uid', '=', $uid)->query();
		if (empty($ret))
		{
			return $ret;
		}
		return $ret[0];
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */