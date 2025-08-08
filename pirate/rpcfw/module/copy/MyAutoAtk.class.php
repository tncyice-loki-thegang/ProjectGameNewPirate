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
 * Class       : MyAutoAtk
 * Description : 挂机数据持有类
 * Inherit     : 
 **********************************************************************************************************************/
class MyAutoAtk
{
	private $uid;								// 用户ID
	private $m_autoAtk;							// 用户挂机信息
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MyAutoAtk
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
	function __construct() {

		// 从 session 中取得挂机信息
		$autoAtk = RPCContext::getInstance()->getSession('copy.auto_atk');
		// 获取用户ID，使用用户ID获取挂机信息
		$this->uid = RPCContext::getInstance()->getSession('global.uid');
		// 如果没顺利取得挂机信息
		if (!isset($autoAtk)) 
		{
			if (empty($this->uid)) 
			{
				Logger::fatal('Can not get auto attack info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户挂机信息
			$autoAtk = CopyDao::getAutoAtkInfo($this->uid);
			// 判断副本挂机信息是否为空
			if ($autoAtk === false)
			{
				// 没完成任务的话，不允许进行副本挂机
				if (!EnSwitch::isOpen(SwitchDef::ATTACK_CONTINOUS))
				{
					Logger::fatal('Can not get auto attack info before get task!');
					throw new Exception('fake');
				}
			}
		}
		// 赋值给自己
		$this->m_autoAtk = $autoAtk;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.auto_atk', $this->m_autoAtk);
	}

	/**
	 * 返回挂机信息
	 */
	public function getAutoAtkInfo()
	{
		// 返回挂机信息
		return $this->m_autoAtk;
	}

	/**
	 * 结束挂机
	 */
	public function stopAutoAtking()
	{
		// 返回已经获取的所有好处
		$ret = $this->m_autoAtk['va_auto_atk_info'];
		// 清空所有挂机信息
		$this->m_autoAtk['copy_id'] = 0;
		$this->m_autoAtk['army_id'] = 0;
		$this->m_autoAtk['start_time'] = 0;
		$this->m_autoAtk['times'] = 0;
		$this->m_autoAtk['annihilate'] = 0;
		$this->m_autoAtk['last_atk_time'] = 0;
		$this->m_autoAtk['va_auto_atk_info'] = array('belly' => 0, 
					                                 'exp' => 0, 
					                                 'experience' => 0,
		                                             'items' => array());
		// 设置进session
		RPCContext::getInstance()->setSession('copy.auto_atk', $this->m_autoAtk);
		// 前端需要显示
		return $ret;
	}

	/**
	 * 保存挂机获取的道具信息
	 * 
	 * @param array $items						挂机获得的道具
	 */
	public function keepingItems($items)
	{
		$this->m_autoAtk['va_auto_atk_info']['items'][] = $items;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.auto_atk', $this->m_autoAtk);
	}

	/**
	 * 返回挂机获取的所有收益信息
	 */
	public function getAllGetInfo()
	{
		return $this->m_autoAtk['va_auto_atk_info'];
	}

	/**
	 * 进行一次挂机操作
	 * 
	 * @param int $belly						挂机获得的游戏币
	 * @param int $exp							挂机获得的经验
	 * @param int $experience					挂机获得的阅历
	 */
	public function attackOnce($belly, $exp, $experience)
	{
		// 通知任务系统，挂机开始
		TaskNotify::operate(TaskOperateType::AUTO_ATK);
		// 调整值
		$this->m_autoAtk['annihilate'] += 1;
		$this->m_autoAtk['last_atk_time'] = Util::getTime();
		$this->m_autoAtk['va_auto_atk_info']['belly'] += $belly;
		$this->m_autoAtk['va_auto_atk_info']['exp'] += $exp;
		$this->m_autoAtk['va_auto_atk_info']['experience'] += $experience;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.auto_atk', $this->m_autoAtk);
		return $this->m_autoAtk['last_atk_time'];
	}
	
	/**
	 * 开始自动攻击
	 * 
	 * @param int $uid							用户ID
	 * @param int $copyID						副本ID
	 * @param int $enemyID						部队ID
	 * @param int $times						次数
	 */
	public static function startAutoAtk($uid, $copyID, $enemyID, $times)
	{
		// 获取当前时刻
		$curTime = Util::getTime();
		// 设置空白数据段
		$value = array('uid' => $uid,
					   'copy_id' => $copyID,
					   'army_id' => $enemyID,
					   'start_time' => $curTime,
					   'times' => $times,
					   'annihilate' => 0,
					   'last_atk_time' => $curTime,
					   'va_auto_atk_info' => array('belly' => 0, 
					                               'exp' => 0, 
					                               'experience' => 0,
		                                           'items' => array()),
					   'status' => DataDef::NORMAL);
		// 攻击信息
		CopyDao::startAutoAtk($value);
		// 如果已经有值了，赋新值
  		if (self::$_instance instanceof self)
  		{
     		self::$_instance->m_autoAtk = $value;
  		}
		// 设置进session
		RPCContext::getInstance()->setSession('copy.auto_atk', $value);

		return 'ok';
	}

	/**
	 * 将挂机信息写入数据库
	 * 
	 * @throws Exception
	 */
	public function save()
	{
		// 将当前内容写入数据库
		if (empty($this->m_autoAtk))
		{
			$autoAtk = RPCContext::getInstance()->getSession('copy.auto_atk');
			if (empty($this->m_autoAtk)) 
			{
				// 获取用户ID，使用用户ID获取挂机信息
				$uid = RPCContext::getInstance()->getSession('global.uid');
				if (empty($uid)) {
					Logger::fatal('Can not get attack info from session!');
					throw new Exception('fake');
				}
				// 通过用户ID获取用户挂机信息
				$autoAtk = CopyDao::getAutoAtkInfo($uid);
			}
			// 赋值给自己
			$this->m_autoAtk = $autoAtk;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('copy.auto_atk', $this->m_autoAtk);
		// 操作数据库
		CopyDao::updateAutoAtk($this->m_autoAtk['uid'], $this->m_autoAtk);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */