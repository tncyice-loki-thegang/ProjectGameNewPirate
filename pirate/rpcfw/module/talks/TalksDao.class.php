<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : TalksDao
 * Description : 会谈数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class TalksDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblTalks = 't_talks';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);

	/**
	 * 获取用户的会谈信息
	 * @param string $uid						用户ID
	 * @return 返回相应信息
	 */
	public static function getTalksInfo($uid)
	{
		// 使用 uid 作为条件
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->select(array('uid', 
		                              'talk_times', 
									  'talk_accumulate',
		                              'talk_date', 
		                              'refresh_times', 
		                              'refresh_date', 
		                              'open_free_mode', 
		                              'va_talks_info'))
		               ->from(self::$tblTalks)
					   ->where($where)->where(self::$status)->query();

		return isset($arrRet[0]) ? $arrRet[0] : false;
	}

	/**
	 * 更新用户会谈信息
	 * @param string $uid						用户ID
	 * @param array $set						更新项目
	 */
	public static function updTalksInfo($uid, $set)
	{
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->update(self::$tblTalks)
		               ->set($set)
		               ->where($where)->query();
		return $arrRet;
	}

	/**
	 * 添加新会谈信息
	 * @param string $uid						
	 */
	public static function addNewTalksInfo($arr)
	{
		$data = new CData();
		$arrRet = $data->insertInto(self::$tblTalks)
		               ->values($arr)
		               ->query();
		return $arr;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */