<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TreasureDao.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasure/TreasureDao.class.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/




class TreasureDao
{
	const tblName = 't_treasure';
	
	public static function getByUid($uid, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)->where('uid', '=', $uid)->query();
		if (!empty($ret))
		{
			return $ret[0];
		}
		return $ret;
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

	public static function updateRobbed($uid, $arrField)
	{
		$data = new CData();
		$whereNotEnd = array('return_end_time', '>=', Util::getTime());
		$whereUid = array('uid', '=', $uid);
		$ret = $data->update(self::tblName)->set($arrField)->where($whereUid)
			->where($whereNotEnd)->query();
	}

	public static function getNotReturn($arrField)
	{
		$data = new CData();
		$where = array('return_end_time', '>', Util::getTime());
		$arrRet = $data->select($arrField)->from(self::tblName)->where($where)->query();
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */