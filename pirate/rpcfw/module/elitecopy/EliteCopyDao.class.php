<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EliteCopyDao.class.php 26734 2012-09-06 03:48:56Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elitecopy/EliteCopyDao.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-06 11:48:56 +0800 (四, 2012-09-06) $
 * @version $Revision: 26734 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EliteCopyDao
 * Description : 精英副本数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class EliteCopyDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblEliteCopy = 't_elite_copy';
	private static $tblCopyPass = 't_copy_pass';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);
	// 数据缓存区
	private static $buffer = array();


	/******************************************************************************************************************
     * t_elite_copy 表操作
     ******************************************************************************************************************/
	/**
	 * 获取用户的全部精英副本信息
	 * 
	 * @param string $uid						用户ID
	 * 
	 * @return 返回相应信息
	 */
	public static function getEliteCopyInfo($uid)
	{
		// 优先检查缓冲区数据
		if (isset(self::$buffer[$uid]))
		{
			// 缓冲区如果有的话，那么就使用缓冲区的数据
			return self::$buffer[$uid];
		}
		// 使用 uid 作为条件
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->select(array('uid', 
		                              'challenge_time', 
		                              'challenge_times', 
		                              'coins', 
		                              'buy_coin_times', 
									  'progress',
									  'va_copy_info'))
		               ->from(self::$tblEliteCopy)
					   ->where($where)->where(self::$status)->query();
		// 检查返回值
		if (isset($arrRet[0]))
		{
			// 将检索的结果放到缓冲区里面
			self::$buffer[$uid] = $arrRet[0];
			return $arrRet[0];
		}
		// 没检索结果的时候，直接返回false
		return false;
	}

	/**
	 * 有些时刻，没有从数据库进行检索，而是从session里面获取数据，那么数据库类的buffer就是空的
	 * 这时候需要调用这个方法，对缓存进行初始化
	 * 
	 * @param array $set						缓存数据
	 */
	public static function setBufferWithoutSelect($uid, $set)
	{
		Logger::debug("SetBufferWithoutSelect called, buffer is %s, set is %s.", self::$buffer, $set);
		// 只有没有缓冲区数据的时候，才保存缓冲区数据
		if (empty(self::$buffer[$uid]))
		{
			self::$buffer[$uid] = $set;
		}
	}

	/**
	 * 更新用户精英副本信息
	 * 
	 * @param string $uid						用户ID
	 * @param array $set						更新项目
	 */
	public static function updEliteCopyInfo($uid, $set)
	{
		Logger::debug("updEliteCopyInfo called, buffer is %s, set is %s.", self::$buffer, $set);
		// 缓存不为空，则进行比较
		if (!empty(self::$buffer[$uid]))
		{
			// 判断，如果什么都没改动，那么直接返回
			if (self::$buffer[$uid] == $set)
			{
				Logger::debug("Upd pet array diff ret is same.");
				return $set;
			}
		}

		// 检查缓冲区数据, 更新缓冲区的数据
		self::$buffer[$uid] = $set;
		// 将用户ID作为条件
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->update(self::$tblEliteCopy)
		               ->set($set)
		               ->where($where)->query();
		return $arrRet;
	}

	/**
	 * 添加用户新精英副本信息
	 * 
	 * @param string $uid						
	 */
	public static function addEliteCopyInfo($uid)
	{
		// 设置属性
		$arr = array('uid' => $uid,
					 'challenge_time' => Util::getTime(),
		             'challenge_times' => EliteCopyConf::CHALLANGE_TIMES,
		             'coins' => EliteCopyConf::COINS, 
		             'buy_coin_times' => 0, 
					 'progress' => 0,
					 'va_copy_info' => array(),
					 'status' => DataDef::NORMAL);

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblEliteCopy)
		               ->values($arr)->query();

		// 给缓冲区也插入一条数据
		self::$buffer[$uid] = $arr;
		// 缓冲区好像不需要状态字段
		unset(self::$buffer[$uid]['status']);
		return $arr;
	}


	/******************************************************************************************************************
     * t_copy_pass 表操作
     ******************************************************************************************************************/
	/**
	 * 获取该部队的攻略列表
	 * 
	 * @param int $copyID						副本ID
	 */
	public static function getCopyPass($copyID)
	{
		// 使用 armyID 作为条件
		$data = new CData();
		$arrRet = $data->select(array('copy_id', 'uid', 'lv'))
		               ->from(self::$tblCopyPass)
					   ->where(array("copy_id", "=", $copyID))
					   ->orderBy("pass_time", false)
					   ->query();

		// 根本没有这条数据的时候，返回0
		return empty($arrRet) ? array() : $arrRet;
	}

	/**
	 * 修改该副本通关列表
	 * 
	 * @param int $oldUid						旧用户ID
	 * @param int $newUid						新用户ID
	 * @param int $newLv						新用户等级
	 * @param int $copyID						精英副本ID
	 */
	public static function updateCopyPass($oldUid, $newUid, $newLv, $copyID)
	{
		// 设置空白数据段
		$value = array('copy_id' => $copyID,
					   'uid' => $newUid,
		               'lv' => $newLv,
		               'pass_time' => Util::getTime());
		// 更新到数据库
		$data = new CData();
		$arrRet = $data->Update(self::$tblCopyPass)
		               ->set($value)
		               ->where(array("uid", "=", $oldUid))
		               ->where(array("copy_id", "=", $copyID))
		               ->query();
		return $arrRet;
	}

	/**
	 * 插入该副本通关列表
	 * 
	 * @param int $uid							用户ID
	 * @param int $copyID						精英副本ID
	 * @param int $lv							用户等级
	 */
	public static function addNewCopyPass($uid, $copyID, $lv)
	{
		// 设置属性
		$arr = array('copy_id' => $copyID,
					 'uid' => $uid,
		             'lv' => $lv,
		             'pass_time' => Util::getTime());

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblCopyPass)
		               ->values($arr)->query();
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */