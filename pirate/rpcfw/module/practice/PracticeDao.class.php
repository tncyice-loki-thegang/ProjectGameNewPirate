<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PracticeDao.class.php 23875 2012-07-16 05:30:09Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/practice/PracticeDao.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-07-16 13:30:09 +0800 (一, 2012-07-16) $
 * @version $Revision: 23875 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : PracticeDao
 * Description : 人物挂机数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class PracticeDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblPractice = 't_practice';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);
	// 数据缓存区
	private static $buffer = array();

	/******************************************************************************************************************
     * t_practice 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取用户的挂机信息
	 * 
	 * @param int $uid							用户ID
	 * @return 返回相应信息
	 */
	public static function getPracticeInfo($uid)
	{
		// 优先检查缓冲区数据
		if (isset(self::$buffer[$uid]))
		{
			// 缓冲区如果有的话，那么就使用缓冲区的数据
			return self::$buffer[$uid];
		}
		// 使用 uid 作为条件
		$whereUid = array("uid", "=", $uid);
		// 进行查询
		$data = new CData();
		$arrRet = $data->select(array('uid',
		                              'exp', 
		                              'lv', 
									  'lv_change_time',
		                              'start_time', 
									  'open_full_day',
									  'acc_times',
									  'acc_times_after_lv',
									  'total_acc_times',
									  'last_acc_time'))
		               ->from(self::$tblPractice)
					   ->where($whereUid)
					   ->where(self::$status)->query();

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
	 * 更新用户的挂机信息
	 * 
	 * @param int $uid							用户ID
	 * @param array $set						更新项目
	 */
	public static function updPracticeInfo($uid, $set)
	{
		Logger::debug("updPracticeInfo called, buffer is %s, set is %s.", self::$buffer, $set);
		// 缓存不为空，则进行比较
		if (!empty(self::$buffer[$uid]))
		{
			// 判断，如果什么都没改动，那么直接返回
			if (self::$buffer[$uid] == $set)
			{
				Logger::debug("Upd practice array diff ret is same.");
				return $set;
			}
		}
		// 检查缓冲区数据, 更新缓冲区的数据
		self::$buffer[$uid] = $set;

		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->update(self::$tblPractice)
		               ->set($set)
		               ->where($where)->query();
		return $arrRet;
	}

	/**
	 * 添加新用户的挂机信息
	 * 
	 * @param string $uid						用户ID
	 */
	public static function addNewPracticeInfo($uid)
	{
		// 获取开始时刻
		$curTime = Util::getTime();
		// 获取人物当前等级
		$lv = EnUser::getUserObj($uid)->getLevel();
		// 免费给予八小时经验
		$exp = $lv * PracticeConf::MINUTE_EXP * PracticeConf::EIGHT_HOURS_MIN;
		// 设置属性
		$arr = array('uid' => $uid,
					 'exp' => $exp,
		             'lv' => $lv,
		             'lv_change_time' => $curTime, 
					 'start_time' => $curTime - PracticeConf::NORMAL_MODE_TIME,
					 'open_full_day' => 0,
					 'acc_times' => 0,
					 'acc_times_after_lv' => 0,
					 'total_acc_times' => 0,
					 'last_acc_time' => 0,
					 'status' => DataDef::NORMAL);

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblPractice)
		               ->values($arr)->query();

		// 给缓冲区也插入一条数据
		self::$buffer[$uid] = $arr;
		// 缓冲区不需要状态字段
		unset(self::$buffer[$uid]['status']);
		return $arr;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */