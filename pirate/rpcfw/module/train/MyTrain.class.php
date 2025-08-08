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
 * Class       : MyTrain
 * Description : 训练数据持有类
 * Inherit     : 
 **********************************************************************************************************************/
class MyTrain
{

	private $m_train;							// 训练数据
	private $uid;								// 用户ID
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MyTrain
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
	 * 
	 * @param $uid								// 用户ID
	 */
	private function __construct() 
	{
		// 从 session 中取得训练信息
		$trainInfo = RPCContext::getInstance()->getSession('sailboat.train');
		// 获取用户ID，使用用户ID获取训练信息
		$this->uid = RPCContext::getInstance()->getSession('global.uid');
		// 如果没顺利取得训练信息
		if (!isset($trainInfo)) 
		{
			if (empty($this->uid)) 
			{
				Logger::fatal('Can not get train info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户训练信息
			$trainInfo = TrainDao::getTrainInfo($this->uid);
		}
		// 赋值给自己
		$this->m_train = $trainInfo;
		// 调整CD时间并将训练信息设置进session
		self::getCdEndTime();
	}

	/**
	 * 初始化新训练信息
	 */
	public function addNewTrainInfo()
	{
		// 初始化人物训练信息
		$this->m_train = TrainDao::addNewTrainInfo($this->uid);
		// 删除掉前端不用数据
		unset($this->m_train['status']);
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.train', $this->m_train);
		// 返回最新数据
		return $this->m_train;
	}

	/**
	 * 获取用户训练信息
	 */
	public function getUserTrainInfo()
	{
		// 更新CD时间
		self::getCdEndTime();
		// 更新突飞次数
		self::getTodayRapidTimes();
		// 获取持有化的数据
		return $this->m_train;
	}

	/**
	 * 开启新的训练栏位
	 */
	public function openTrainSlot()
	{
		// 添加一个训练栏位置
		++$this->m_train['train_slots'];
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.train', $this->m_train);
	}

	/**
	 * 增加突飞次数
	 */
	public function addRapidTimes()
	{
		// 设置时间和次数
		$this->m_train['rapid_date'] = Util::getTime();
		$this->m_train['rapid_times'] = self::getTodayRapidTimes() + 1;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.train', $this->m_train);
	}

	/**
	 * 开始训练
	 * @param int $heroID						英雄ID
	 * @param int $mode							训练模式
	 * @param int $lastTime						训练持续时刻
	 */
	public function startTrain($heroID, $mode, $lastTime)
	{
		// 获取训练开始时刻
		$startTime = Util::getTime();
		// 设置新训练的信息
		$trainInfo = array('id' => $heroID, 
		                   'train_start_time' => $startTime, 
		                   'train_mode' => $mode,
		                   'train_last_time' => $lastTime,
		                   'train_end_time' => $startTime + $lastTime);
		// 增加的训练位置也作为英雄ID
		$this->m_train['va_train_info'][$heroID] = $trainInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.train', $this->m_train);
		// 返回截止时刻
		return $startTime + $lastTime;
	}

	/**
	 * 终止训练
	 * @param int $heroID						英雄ID
	 */
	public function clearTrainInfo($heroID)
	{
		// 增加的训练位置也作为英雄ID
		unset($this->m_train['va_train_info'][$heroID]);
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.train', $this->m_train);
	}

	/**
	 * 调整训练开始时刻
	 * @param int $heroID						英雄ID
	 * @param int $time							训练开始时刻
	 */
	public function resetTrainStartTime($heroID, $time)
	{
		// 调整训练开始时刻
		$this->m_train['va_train_info'][$heroID]['train_start_time'] = $time;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.train', $this->m_train);
	}

	/**
	 * 获取今天的突飞次数
	 */
	public function getTodayRapidTimes()
	{
		// 如果还没开启训练室，那么就直接返回
		if ($this->m_train === false)
		{
			return ;
		}
		// 如果上次突飞的时间是今天之前
		if (!Util::isSameDay($this->m_train['rapid_date'], CopyConf::REFRESH_TIME))
		{
			$this->m_train['rapid_date'] = Util::getTime();
			$this->m_train['rapid_times'] = 0;
			// 设置进session
			RPCContext::getInstance()->setSession('sailboat.train', $this->m_train);
			// 直接更新下数据库，以防万一，以绝后患，未雨绸缪，反正这样不会有bug
			self::save();
		}
		// 返回次数
		return $this->m_train['rapid_times'];
	}

	/**
	 * 返回CD的截止时间
	 */
	public function getCdEndTime()
	{
		// 空值判断
		if ($this->m_train === false)
		{
			return ;
		}
		// 记录下当前时间
		$curTime = Util::getTime();
		// 如果时间已经小于当前时刻, 并且这个状态需要改变了，再进行改变
		if ($this->m_train['cd_time'] <= $curTime && 
		    $this->m_train['cd_status'] != TrainConf::RAPID_FREE) 
		{
			// 可以设置为空闲了
			$this->m_train['cd_status'] = TrainConf::RAPID_FREE;
			// 设置为当前时间
			$this->m_train['cd_time'] = $curTime;
			// 设置进session
			RPCContext::getInstance()->setSession('sailboat.train', $this->m_train);
		}
		// 返回CD时间
		return array('cd_time' => $this->m_train['cd_time'], 'cd_status' => $this->m_train['cd_status']);
	}

	/**
	 * 计算CD时间
	 * @param int $time							突飞耗费时刻
	 */
	public function addCdTime($addTime)
	{
		// 记录下当前时间
		$curTime = Util::getTime();
		// 现在时间开始，推算冻结时间
		$freezeTime = $curTime + btstore_get()->TRAIN_ROOM['rapid_max_cd'];
		// 先调整一下当前时刻
		self::getCdEndTime();
		// 需要在这个地方进行校准
		if ($this->m_train['cd_time'] < $curTime)
		{
			$this->m_train['cd_time'] = $curTime;
		}
		// 不管三七二十一，加上时间，判断在上层处理
		$this->m_train['cd_time'] += $addTime;
		// 看CD的状态是否需要改变
		if ($this->m_train['cd_time'] >= $freezeTime) 
		{
			// 如果时间超过了约定时间, 那么就设置为 忙碌
			$this->m_train['cd_status'] = TrainConf::RAPID_BUSY;
		}
		Logger::debug("The train CD status %s， endTime is %s", 
					  $this->m_train['cd_status'], $this->m_train['cd_time']);
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.train', $this->m_train);
	}

	/**
	 * 重置CD时刻
	 */
	public function resetCdTime()
	{
		// 可以设置为空闲了
		$this->m_train['cd_status'] = TrainConf::RAPID_FREE;
		// 设置为当前时间
		$this->m_train['cd_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.train', $this->m_train);
	}

	/**
	 * 将数据保存至数据库
	 */
	public function save()
	{
		// 更新到数据库
		TrainDao::updTrainInfo($this->uid, $this->m_train);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */