<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MergeServerDao.class.php 27528 2012-09-20 06:58:36Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mergeserver/MergeServerDao.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-09-20 14:58:36 +0800 (四, 2012-09-20) $
 * @version $Revision: 27528 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MergeServerDAO
 * Description : 合服活动数据交互实现类， 对内接口实现类
 * Inherit     :
 **********************************************************************************************************************/
class MergeServerDAO
{
	
	const tblName = 't_mergeserver_reward';

	/**
	 * 合服活动用户信息
	 * 
	 * @param array $arrCond					检索条件
	 * @param array $arrField					检索项目
	 * @return array $arrRet					节日活动用户信息
	 */
	public static function selectMserver($arrCond, $arrField)
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
	 * 登录合服活动用户信息
	 * 
	 * @param array $arrField					插入项目
	 */
	public static function insertMserver($arrField)
	{
		$data = new CData();
		$data->insertInto(self::tblName)->values($arrField)->query();
	}

	/**
	 * 更新合服活动信息
	 * 
	 * @param array $arrCond					更新条件
	 * @param array $arrField					更新项目
	 */
	public static function updateMserver($arrCond, $arrField)
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