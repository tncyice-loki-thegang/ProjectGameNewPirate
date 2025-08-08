<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyActive.class.php 30920 2012-11-12 08:51:11Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/active/MyActive.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-11-12 16:51:11 +0800 (一, 2012-11-12) $
 * @version $Revision: 30920 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyActive
 * Description : 已完成活跃度数据持有列表
 * Inherit     : 
 **********************************************************************************************************************/
class MyActive
{

	private $m_active;							// 活跃度数据
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MyActive
	 */
	public static function getInstance()
	{
  		if (!self::$_instance instanceof self)
  		{
     		self::$_instance = new self();
  		}
  		return self::$_instance;
	}

	/**
	 * 毁掉单例，单元测试对应
	 */
	public static function release() 
	{
		if (self::$_instance != null) 
		{
			self::$_instance = null;
		}
	}

	/**
	 * 构造函数，获取 session 信息
	 */
	private function __construct() 
	{
		// 从 session 中取得活跃度信息
		$activeInfo = RPCContext::getInstance()->getSession('active.list');
		// 获取用户ID，使用用户ID获取活跃度信息
		$uid = RPCContext::getInstance()->getUid();
		// 如果没顺利取得活跃度信息
		if (empty($activeInfo['uid'])) 
		{
			if (empty($uid)) 
			{
				Logger::fatal('Can not get active info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户活跃度信息
			$activeInfo = ActiveDao::getActiveInfo($uid);
			// 检查用户是否完成相应任务
			if ($activeInfo === false)
			{
				// 如果完成任务还尚未开启活跃度
				if (EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
				{
					Logger::debug('Open active degree.');
					// 初始化人物活跃度信息
					$activeInfo = ActiveDao::addNewActiveInfo($uid);
				}
				// 如果尚未完成任务，那么就不应该获取这个数据
				else 
				{
					Logger::fatal('Can not get active degree info before task!');
					throw new Exception('fake');
				}
			}
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			ActiveDao::setBufferWithoutSelect($uid, $activeInfo);
		}
		// 赋值给自己
		$this->m_active = $activeInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('active.list', $this->m_active);
	}

	/**
	 * 获取活跃度信息
	 */
	public function getActiveInfo()
	{
		// 需要在这里查询是否需要每天更新
		self::checkAllTimes();
		// 返回最新的数据
		return $this->m_active;
	}

	/**
	 * 增加次数
	 * 
	 * @param int $type							类型
	 */
	public function addTimes($type)
	{
		// 需要在这里查询是否需要每天更新
		self::checkAllTimes();
		// 查看次数是否已经满了，满了的话就不再更新数据库了
		// 先查看旧有数据
		if (isset($this->m_active[ActiveConf::$ACT_NAME[$type]]))
		{
			if ($this->m_active[ActiveConf::$ACT_NAME[$type]] < btstore_get()->ACTIVE_DEGREE[$type]['times'])
			{
				// 只有没有达到次数的是，需要增加次数更新数据库
				++$this->m_active[ActiveConf::$ACT_NAME[$type]];
				// 设置进session
				RPCContext::getInstance()->setSession('active.list', $this->m_active);
				// 保存到数据库
				self::save();
			}
		}
		// 查看新的VA字段
		else if (isset($this->m_active['va_active_info'][ActiveConf::$ACT_NAME[$type]]))
		{
			if ($this->m_active['va_active_info'][ActiveConf::$ACT_NAME[$type]] < btstore_get()->ACTIVE_DEGREE[$type]['times'])
			{
				// 只有没有达到次数的是，需要增加次数更新数据库
				++$this->m_active['va_active_info'][ActiveConf::$ACT_NAME[$type]];
				// 设置进session
				RPCContext::getInstance()->setSession('active.list', $this->m_active);
				// 保存到数据库
				self::save();
			}
		}
		// 什么都没有的前提下，如果表里面仍然有，说明是旧有数据需要进行修复
		else if (isset(btstore_get()->ACTIVE_DEGREE[$type]))
		{
			// 初始化数据库，赋值并给予初始次数
			$this->m_active['va_active_info'][ActiveConf::$ACT_NAME[$type]] = 1;
			// 设置进session
			RPCContext::getInstance()->setSession('active.list', $this->m_active);
			// 保存到数据库
			self::save();
		}
		return;
	}

	/**
	 * 查看是否过了一天，需要重新计算次数
	 */
	public function checkAllTimes()
	{
		// 如果上次更新的时间是今天之前
		if (!Util::isSameDay($this->m_active['update_time'], ActiveConf::REFRESH_TIME))
		{
			// 调整次数
			self::clearAllTimes();
			// 设置进session
			RPCContext::getInstance()->setSession('active.list', $this->m_active);
			// 如果改变了，那么直接更新数据库
			self::save();
		}
	}

	/**
	 * 单纯的清空所有次数
	 */
	public function clearAllTimes()
	{
		$this->m_active['sail_times'] = 0;
		$this->m_active['cook_times'] = 0;
		$this->m_active['copy_atk_times'] = 0;
		$this->m_active['elite_atk_times'] = 0;
		$this->m_active['conquer_times'] = 0;
		$this->m_active['port_atk_times'] = 0;
		$this->m_active['arena_times'] = 0;
		$this->m_active['play_slave_times'] = 0;
		$this->m_active['order_times'] = 0;
		$this->m_active['hero_rapid_times'] = 0;
		$this->m_active['day_task_times'] = 0;
		$this->m_active['fetch_salary'] = 0;
		$this->m_active['reinforce_times'] = 0;
		$this->m_active['explore_times'] = 0;
		$this->m_active['treasure_times'] = 0;
		$this->m_active['smelting_times'] = 0;
		$this->m_active['talks_times'] = 0;
		$this->m_active['resource_times'] = 0;
		$this->m_active['rob_times'] = 0;
		$this->m_active['goodwill_gift_times'] = 0;
		$this->m_active['donate_times'] = 0;
		$this->m_active['prized_num'] = 0;
		// 清空所有VA字段中的次数
		foreach ($this->m_active['va_active_info'] as $key => $times)
		{
			$this->m_active['va_active_info'][$key] = 0;
		}
		// 返回清空后的结果
		return $this->m_active;
	}

	/**
	 * 增加领取奖励次数
	 * 
	 * @param int $caseID						宝箱ID
	 */
	public function addPirzedTimes($caseID)
	{
		$this->m_active['prized_num'] += ActiveDef::$CASE_INDEX[$caseID];
		// 设置进session
		RPCContext::getInstance()->setSession('active.list', $this->m_active);
	}

	/**
	 * 计算活跃度点数
	 * 
	 * @param array $activeInfo					已经获取的活跃度列表
	 */
	static public function calculateActivePoint($activeInfo)
	{
		// 初始化返回值
		$point = 0;
		// 查看所有项目
		foreach ($activeInfo as $type => $times)
		{
			// 没设置这个项目 —— 就是数据库的非计数项，那么直接查看下一项就行了
			if (!isset(ActiveConf::$ACT_INDEX[$type]) || 
			    !isset(btstore_get()->ACTIVE_DEGREE[ActiveConf::$ACT_INDEX[$type]]))
			{
				continue;
			}
			// 查表，查看次数
			if ($times >= btstore_get()->ACTIVE_DEGREE[ActiveConf::$ACT_INDEX[$type]]['times'])
			{
				// 次数够了的话，加上此项分数
				$point += btstore_get()->ACTIVE_DEGREE[ActiveConf::$ACT_INDEX[$type]]['point'];
			}
		}
		// 查看策划们后加的项目
		foreach ($activeInfo['va_active_info'] as $type => $times)
		{
			// 没设置这个项目 —— 就是数据库的非计数项，那么直接查看下一项就行了
			if (!isset(ActiveConf::$ACT_INDEX[$type]) || 
			    !isset(btstore_get()->ACTIVE_DEGREE[ActiveConf::$ACT_INDEX[$type]]))
			{
				continue;
			}
			// 查表，查看次数
			if ($times >= btstore_get()->ACTIVE_DEGREE[ActiveConf::$ACT_INDEX[$type]]['times'])
			{
				// 次数够了的话，加上此项分数
				$point += btstore_get()->ACTIVE_DEGREE[ActiveConf::$ACT_INDEX[$type]]['point'];
			}
		}
		// 返回
		return $point;
	}

	/**
	 * 更新数据库
	 */
	public function save()
	{
		// 设置更新时刻
		$this->m_active['update_time'] = Util::getTime();
		// 更新到数据库
		ActiveDao::updActiveInfo($this->m_active['uid'], $this->m_active);
		// 返回更新信息
		return $this->m_active;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */