<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TrainDao.class.php 17129 2012-03-23 03:17:18Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/train/TrainDao.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-03-23 11:17:18 +0800 (五, 2012-03-23) $
 * @version $Revision: 17129 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : TrainDao
 * Description : 训练室数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class TrainDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblTrain = 't_train';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);


	/**
	 * 获取用户的训练信息
	 * @param string $uid						用户ID
	 * @return 返回相应信息
	 */
	public static function getTrainInfo($uid)
	{
		// 使用 uid 作为条件
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->select(array('uid', 
		                              'cd_time', 
		                              'cd_status', 
		                              'train_slots', 
									  'rapid_times',
									  'rapid_date',
		                              'va_train_info'))
		               ->from(self::$tblTrain)
					   ->where($where)->where(self::$status)->query();
		return isset($arrRet[0]) ? $arrRet[0] : false;
	}

	/**
	 * 更新用户训练信息
	 * @param string $uid						用户ID
	 * @param array $set						更新项目
	 */
	public static function updTrainInfo($uid, $set)
	{
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->update(self::$tblTrain)
		               ->set($set)
		               ->where($where)->query();
		return $arrRet;
	}

	/**
	 * 添加新训练室信息，需要在创建主船时候调用
	 * @param string $uid						
	 */
	public static function addNewTrainInfo($uid)
	{
		// 设置属性
		$arr = array('uid' => $uid,
					 'cd_time' => 0,
		             'cd_status' => TrainConf::RAPID_FREE,
		             'train_slots' => btstore_get()->TRAIN_ROOM['init_train_slot'], 
					 'rapid_times' => 0,
					 'rapid_date' => 0,
		             'va_train_info' => array(), 
					 'status' => DataDef::NORMAL);

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblTrain)
		               ->values($arr)->query();
		return $arr;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */