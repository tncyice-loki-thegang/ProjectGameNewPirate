<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ExploreDao.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/explore/ExploreDao.class.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

class ExploreDao
{
	const tblName = 't_explore';
	
	public static function getInfo($uid, $exploreId, $arrField)
	{
		$whereUid = array('uid', '=', $uid);
		$whereEid = array('exploreId', '=', $exploreId);
		$data = new CData();
		$retArr = $data->select($arrField)->from(self::tblName)
			->where($whereUid)->where($whereEid)->query();
		if (!empty($retArr))
		{
			return $retArr[0];
		}
		return array();
	}
	
	public static function insert($uid, $exploreId, $arrField)
	{
		$data = new CData();
		$arrField['uid'] = $uid;
		$arrField['exploreId'] = $exploreId;
		$arrRet = $data->insertInto(self::tblName)->values($arrField)->query();
		return $arrRet;
	}
	
	public static function update($uid, $exploreId, $arrField)
	{
		$whereUid = array('uid', '=', $uid);
		$whereEid = array('exploreId', '=', $exploreId);
		$data = new CData();
		$arrRet = $data->update(self::tblName)->set($arrField)
			->where($whereUid)->where($whereEid)->query();
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */