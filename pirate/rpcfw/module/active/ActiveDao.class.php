<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ActiveDao.class.php 32949 2012-12-12 07:54:41Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/active/ActiveDao.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-12 15:54:41 +0800 (三, 2012-12-12) $
 * @version $Revision: 32949 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : ActiveDao
 * Description : 活跃度数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class ActiveDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblActive = 't_active';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);
	// 数据缓存区
	private static $buffer = array();

	/******************************************************************************************************************
     * t_active 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取用户的全部活跃度等级
	 * 
	 * @param string $uid						用户ID
	 * @return 返回相应信息
	 */
	public static function getActiveInfo($uid)
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
		                              'sail_times', 
		                              'cook_times', 
		                              'copy_atk_times', 
		                              'elite_atk_times', 
									  'conquer_times',
									  'port_atk_times',
		                              'arena_times', 
		                              'play_slave_times', 
		                              'order_times', 
		                              'hero_rapid_times', 
		                              'day_task_times', 
		                              'fetch_salary', 
		                              'reinforce_times', 
		                              'explore_times', 
		                              'treasure_times', 
		                              'smelting_times', 
		                              'talks_times', 
		                              'resource_times', 
		                              'rob_times', 
									  'goodwill_gift_times',
		                              'donate_times', 
		                              'prized_num', 
		                              'update_time', 
		                              'va_active_info'))
		               ->from(self::$tblActive)
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
	 * 更新用户活跃度信息
	 * 
	 * @param string $uid						用户ID
	 * @param array $set						更新项目
	 */
	public static function updActiveInfo($uid, $set)
	{
		Logger::debug("updActiveInfo called, buffer is %s, set is %s.", self::$buffer, $set);
		// 缓存不为空，则进行比较
		if (!empty(self::$buffer[$uid]))
		{
			// 判断，如果什么都没改动，那么直接返回
			if (self::$buffer[$uid] == $set)
			{
				Logger::debug("Upd active array diff ret is same.");
				return $set;
			}
		}

		// 检查缓冲区数据, 更新缓冲区的数据
		self::$buffer[$uid] = $set;
		// 将用户ID作为条件
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->update(self::$tblActive)
		               ->set($set)
		               ->where($where)->query();
		return $arrRet;
	}

	/**
	 * 添加新活跃度信息
	 * 
	 * @param string $uid						
	 */
	public static function addNewActiveInfo($uid)
	{
		// 设置属性
		$arr = array('uid' => $uid,
		             'sail_times' => 0, 
		             'cook_times' => 0, 
		             'copy_atk_times' => 0, 
		             'elite_atk_times' => 0, 
		             'conquer_times' => 0, 
		             'port_atk_times' => 0, 
		             'arena_times' => 0, 
		             'play_slave_times' => 0, 
		             'order_times' => 0, 
		             'hero_rapid_times' => 0, 
		             'day_task_times' => 0, 
		             'fetch_salary' => 0, 
		             'reinforce_times' => 0, 
		             'explore_times' => 0, 
		             'treasure_times' => 0, 
		             'smelting_times' => 0, 
		             'talks_times' => 0, 
		             'resource_times' => 0, 
		             'rob_times' => 0, 
					 'goodwill_gift_times' => 0,
		             'donate_times' => 0, 
		             'prized_num' => 0, 
		             'update_time' => 0, 
		             'va_active_info' => array(
						'astro_exp_times' => 0, 
						'gold_soul_times' => 0, 
						'impel_prize_times' =>0, 
						'card_salary_times' =>0, 
						'bejeweled_times' =>0, 
						'summon_crystal' =>0, 
						'allblue_collect_times' =>0, 
						'cruise_times' =>0, 
						'haki_times' =>0, 
						'dailyworship_times' =>0, 
						'reforceworldboat_times' =>0, 
						'buyguildtechpoint_times' =>0, 
						'buyelementpoint_times' =>0, 
						'allblue_catchfish_times' =>0, 
						'elves_feed' =>0, 
						'tm_get_hero_income' =>0, ), 
					 'status' => DataDef::NORMAL);

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblActive)
		               ->values($arr)->query();

		// 给缓冲区也插入一条数据
		self::$buffer[$uid] = $arr;
		// 缓冲区好像不需要状态字段
		unset(self::$buffer[$uid]['status']);
		return $arr;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */