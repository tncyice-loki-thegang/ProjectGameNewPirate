<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FestivalDao.class.php 26655 2012-09-04 09:52:24Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/FestivalDao.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-09-04 17:52:24 +0800 (二, 2012-09-04) $
 * @version $Revision: 26655 $
 * @brief 
 *  
 **/

class FestivalDAO
{
	
	const tblName = 't_festival';

	/**
	 * 节日活动用户信息
	 * 
	 * @param array $arrCond					检索条件
	 * @param array $arrField					检索项目
	 * @return array $arrRet					节日活动用户信息
	 */
	public static function selectFestival($arrCond, $arrField)
	{
		$data = new CData();
		$data->select($arrField)->from(self::tblName);
		foreach ( $arrCond as $cond )
		{
			$data->where($cond);
		}	
		$arrRet = $data->query();
		if (!empty($arrRet))
		{
			$arrRet = $arrRet[0];
		}
		return $arrRet;
	}	
	
	/**
	 * 登录节日活动用户信息
	 * 
	 * @param array $arrField					插入项目
	 */
	public static function insertFestivalInfo($arrField)
	{
		$data = new CData();
		$data->insertInto(self::tblName)->values($arrField)->query();
	}

	/**
	 * 更新节日活动信息
	 * 
	 * @param array $arrCond					更新条件
	 * @param array $arrField					更新项目
	 */
	public static function updateFestival($arrCond, $arrField)
	{
		$data = new CData();
		$data->update(self::tblName)->set($arrField);
		foreach ( $arrCond as $cond )
		{
			$data->where($cond);
		}	
		$data->query();
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */