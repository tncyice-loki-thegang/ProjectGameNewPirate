<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ElvesDao.class.php 36726 2013-01-23 02:42:23Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elves/ElvesDao.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-23 10:42:23 +0800 (三, 2013-01-23) $
 * @version $Revision: 36726 $
 * @brief 
 *  
 **/

class ElvesDao
{
	const tbl = 't_elves';
	public static function insert($arrField)
	{
		$data = new CData();
		return $data->insertInto(self::tbl)->values($arrField)->query();
	}
	
	public static function update($uid, $arrField)
	{
		$data = new CData();
		return $data->update(self::tbl)->set($arrField)->where('uid', '=', $uid)->query();
	}
	
	public static function get($uid, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tbl)->where('uid', '=', $uid)->query();
		if (!empty($ret))
		{
			return $ret[0];
		}
		return $ret;
	}
	
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */