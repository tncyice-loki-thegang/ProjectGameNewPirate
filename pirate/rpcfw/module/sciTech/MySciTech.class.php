<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MySciTech.class.php 39846 2013-03-04 10:47:57Z HongyuLan $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sciTech/MySciTech.class.php $
 * @author $Author: HongyuLan $(liuyang@babeltime.com)
 * @date $Date: 2013-03-04 18:47:57 +0800 (一, 2013-03-04) $
 * @version $Revision: 39846 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MySciTech
 * Description : 科技数据持有类
 * Inherit     : 
 **********************************************************************************************************************/
class MySciTech
{

	private $m_sci_tech;						// 科技数据
	private $uid;								// 用户ID

	/**
	 * 构造函数，获取 session 信息
	 * 
	 * @param $uid								// 用户ID
	 */
	function __construct() 
	{
		// 从 session 中取得科技信息
		$stInfo = RPCContext::getInstance()->getSession('sailboat.sci');
		// 获取用户ID，使用用户ID获取科技信息
		$this->uid = RPCContext::getInstance()->getSession('global.uid');
		// 如果没顺利取得科技信息
		if (empty($stInfo['uid'])) 
		{
			if (empty($this->uid)) 
			{
				Logger::fatal('Can not get sci_tech info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户科技信息
			$stInfo = SciTechDao::getStInfo($this->uid);
			// 检查用户是否完成相应任务
			if ($stInfo === false)
			{
				// 如果完成任务还尚未开启研究院
				if (EnSwitch::isOpen(SwitchDef::RESEARCH))
				{
					Logger::debug('Open sci_tech cabin.');
					// 初始化人物科技信息
					$stInfo = SciTechDao::addNewStInfo($this->uid);
				}
				// 如果尚未完成任务，那么就不应该获取这个数据
				else 
				{
					// 删除掉，因为在开启前前端就需要显示科技CD时间
// 20120529			Logger::fatal('Can not get sci_tech cabin level!');
//					throw new Exception('fake');
				}
			}
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			SciTechDao::setBufferWithoutSelect($this->uid, $stInfo);
		}
		// 赋值给自己
		$this->m_sci_tech = $stInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.sci', $this->m_sci_tech);
	}

	/**
	 * 返回CD的截止时间
	 */
	public function getCdEndTime()
	{
		// 空值判断
		if ($this->m_sci_tech === false)
		{
			return 'err';
		}
		// 记录下当前时间
		$curTime = Util::getTime();
		// 如果时间已经小于当前时刻, 并且这个状态需要改变了，再进行改变
		if ($this->m_sci_tech['cd_time'] <= $curTime && 
		    $this->m_sci_tech['cd_status'] != SailboatConf::BUILDING_FREE) 
		{
			// 可以设置为空闲了
			$this->m_sci_tech['cd_status'] = SailboatConf::BUILDING_FREE;
			// 设置为当前时间
			$this->m_sci_tech['cd_time'] = $curTime;
			// 设置进session
			RPCContext::getInstance()->setSession('sailboat.sci', $this->m_sci_tech);
		}
		// 返回CD时间
		return array('cd_time' => $this->m_sci_tech['cd_time'], 
		             'credit' => $this->m_sci_tech['open_credit_mode'],
		             'cd_status' => $this->m_sci_tech['cd_status']);
	}

	/**
	 * 计算CD时间
	 * 
	 * @param int $time							研究耗费时刻
	 */
	public function addCdTime($addTime)
	{
		// 记录下当前时间
		$curTime = Util::getTime();
		// 现在时间开始，推算冻结时间
		$freezeTime = $curTime;
		// 信用卡机制，可以冻结时间后延
		if ($this->m_sci_tech['open_credit_mode'] == 1)
		{
			$freezeTime += SailboatConf::BUILDING_MAX_TIME;
		}
		// 先调整一下当前时刻
		$this->getCdEndTime();
		// 需要在这个地方进行校准
		if ($this->m_sci_tech['cd_time'] < $curTime)
		{
			$this->m_sci_tech['cd_time'] = $curTime;
		}
		// 不管三七二十一，加上时间，判断在上层处理
		$this->m_sci_tech['cd_time'] += $addTime;
		// 看CD的状态是否需要改变
		if ($this->m_sci_tech['cd_time'] >= $freezeTime) 
		{
			// 如果时间超过了约定时间, 那么就设置为 忙碌
			$this->m_sci_tech['cd_status'] = SailboatConf::BUILDING_BUSY;
		}
		Logger::debug("The sci_tech CD status %s， endTime is %s", 
					  $this->m_sci_tech['cd_status'], $this->m_sci_tech['cd_time']);
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.sci', $this->m_sci_tech);
		// 返回最新的CD时间
		return array('cd_time' => $this->m_sci_tech['cd_time'], 
		             'credit' => $this->m_sci_tech['open_credit_mode'],
		             'cd_status' => $this->m_sci_tech['cd_status']);
	}

	/**
	 * 获取当前CD时刻
	 */
	public function getLatestCD() 
	{
		// 获取CD截止时刻
		$endTime = $this->getCdEndTime();
		// 获取当前CD时刻
		$cd = $endTime['cd_time'] - Util::getTime();
		return $cd < 0 ? 0 : $cd;
	}

	/**
	 * 重置CD时刻
	 */
	public function resetCdTime()
	{
		// 可以设置为空闲了
		$this->m_sci_tech['cd_status'] = SailboatConf::BUILDING_FREE;
		// 设置为当前时间
		$this->m_sci_tech['cd_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.sci', $this->m_sci_tech);
	}

	/**
	 * 获取用户科技信息
	 */
	public function getUserStInfo()
	{
		// 获取持有化的数据
		return $this->m_sci_tech;
	}

	/**
	 * 开启一个新科技
	 * @param int $stID							科技ID
	 */
	public function openNewTech($stID)
	{
		// 如果还没设置上，那么开启一个新的
		if (empty($this->m_sci_tech['va_st_info']['st_id_lv'][$stID]))
		{
			$this->m_sci_tech['va_st_info']['st_id_lv'][$stID]['id'] = $stID;
			$this->m_sci_tech['va_st_info']['st_id_lv'][$stID]['lv'] = 0;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.sci', $this->m_sci_tech);
	}

	/**
	 * 提升科技等级
	 * @param int $stID							科技ID
	 */
	public function addSciTechLv($stID)
	{
		// 必须先存在这个科技，才能提升等级啊
		if (isset($this->m_sci_tech['va_st_info']['st_id_lv'][$stID]))
		{
			++$this->m_sci_tech['va_st_info']['st_id_lv'][$stID]['lv'];
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.sci', $this->m_sci_tech);
	}

	/**
	 * 加满所有科技
	 * 控制台专用
	 */
	public function upSciTechLv($level)
	{
		foreach ($this->m_sci_tech['va_st_info']['st_id_lv'] as $stID => $lv)
		{
			$this->m_sci_tech['va_st_info']['st_id_lv'][$stID]['lv'] = $level;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.sci', $this->m_sci_tech);
	}

	/**
	 * 判断是否开启科技CD信用卡模式
	 */
	public function isOpenCreditMode()
	{
		return $this->m_sci_tech['open_credit_mode'] == 1;
	}

	/**
	 * 开启科技CD信用卡模式
	 */
	public function openCreditMode()
	{
		$this->m_sci_tech['open_credit_mode'] = 1;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.sci', $this->m_sci_tech);
	}

	/**
	 * 将数据保存至数据库
	 */
	public function save()
	{
		// 通知人物模块，重置战斗信息
		EnUser::modifyBattleInfo();
		// 更新到数据库
		SciTechDao::updStInfo($this->uid, $this->m_sci_tech);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */