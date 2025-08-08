<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SmeltingDao.class.php 32806 2012-12-11 06:03:57Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/SmeltingDao.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-11 14:03:57 +0800 (二, 2012-12-11) $
 * @version $Revision: 32806 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : SmeltingDao
 * Description : 装备制做数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class SmeltingDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblSmelting = 't_smelting';
	private static $tblGlobal = 't_global';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);

	/******************************************************************************************************************
     * t_smelting 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取用户的装备制做信息
	 * 
	 * @param int $uid							用户ID
	 * @return 返回相应信息
	 */
	public static function getSmeltingInfo($uid)
	{
		// 使用 uid 作为条件
		$whereUid = array("uid", "=", $uid);
		// 进行查询
		$data = new CData();
		$arrRet = $data->select(array('uid',
		                              'smelt_times', 
									  'smelt_accumulate',
		                              'last_smelt_time', 
		                              'cd_time',
		                              'gold_artificer_times', 
									  'gold_artificer_time',
		                              'artificer_time',
		                              'smelt_times_1', 
		                              'quality_1',
									  'smelt_times_2',
		                              'quality_2',
									  'va_smelt_info'))
		               ->from(self::$tblSmelting)
					   ->where($whereUid)
					   ->where(self::$status)->query();
		return isset($arrRet[0]) ? $arrRet[0] : false;
	}

	/**
	 * 更新用户的装备制做信息
	 * 
	 * @param int $uid							用户ID
	 * @param array $set						更新项目
	 */
	public static function updSmeltingInfo($uid, $set)
	{
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->update(self::$tblSmelting)
		               ->set($set)
		               ->where($where)->query();
		return $arrRet;
	}

	/**
	 * 添加新用户的装备制做信息
	 * 
	 * @param int $uid							用户ID
	 */
	public static function addNewSmeltingInfo($uid)
	{
		// 设置属性
		$arr = array('uid' => $uid,
					 'smelt_times' => 0,
					 'smelt_accumulate' => 0,
					 'last_smelt_time' => Util::getTime(),
		             'cd_time' => 0,
		             'gold_artificer_times' => 0,
		             'gold_artificer_time' => 0, 
		             'artificer_time' => 0,
					 'smelt_times_1' => 0,
		             'quality_1' => 0,
					 'smelt_times_2' => 0,
		             'quality_2' => 0,
					 'va_smelt_info' => array(
											'artificers' => array(),
											'integral' => array('red' => 0, 'purple' => 0)
										),
					 'status' => DataDef::NORMAL);

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblSmelting)
		               ->values($arr)->query();
		return $arr;
	}

	/******************************************************************************************************************
     * t_global 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取工匠离开时刻
	 * 
	 * @return 返回相应信息
	 */
	public static function getArtificerLeaveTime()
	{
		// 使用 Sqid 作为条件
		$whereSqid = array("sq_id", "=", SmeltingConf::ARTIFICER_SQ_NO);
		// 进行查询
		$data = new CData();
		$arrRet = $data->select(array('value_1', 
		                              'value_2', 
		                              'value_3'))
		               ->from(self::$tblGlobal)
					   ->where($whereSqid)
					   ->query();
		return isset($arrRet[0]) ? $arrRet[0] : false;
	}

	/**
	 * 更新工匠离开时刻
	 * 
	 * @param array $leaveTime					工匠离开时刻
	 * @param array $nextLeaveTime				下次工匠离开时刻
	 */
	public static function updArtificerLeaveTime($leaveTime, $nextLeaveTime)
	{
		// 设置属性
		$arr = array('value_1' => $leaveTime,
					 'value_2' => $nextLeaveTime);

		$whereSqid = array("sq_id", "=", SmeltingConf::ARTIFICER_SQ_NO);
		$data = new CData();
		$arrRet = $data->update(self::$tblGlobal)
		               ->set($arr)
		               ->where($whereSqid)->query();
		return $arrRet;
	}

	/**
	 * 初始化工匠离开时刻
	 * 
	 * @param array $leaveTime					工匠离开时刻
	 * @param array $nextLeaveTime				下次工匠离开时刻
	 */
	public static function initArtificerLeaveTime($leaveTime, $nextLeaveTime)
	{
		// 设置属性
		$arr = array('sq_id' => SmeltingConf::ARTIFICER_SQ_NO,
		             'value_1' => $leaveTime,
					 'value_2' => $nextLeaveTime,
		             'value_3' => 0,
		             'module_name' => 'smelting');

		// 更新到数据库
		$data = new CData();
		$arrRet = $data->insertIgnore(self::$tblGlobal)
		               ->values($arr)
		               ->query();
		return 0;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */