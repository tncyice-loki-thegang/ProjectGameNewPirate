<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyEliteCopy.class.php 32599 2012-12-10 02:05:17Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elitecopy/MyEliteCopy.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-10 10:05:17 +0800 (一, 2012-12-10) $
 * @version $Revision: 32599 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyEliteCopy
 * Description : 精英副本数据持有类
 * Inherit     :
 **********************************************************************************************************************/
class MyEliteCopy
{
	private $m_copy;							// 精英副本数据
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * 
	 * @return MyEliteCopy
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
		// 从 session 中取得成就信息
		$eliteCopyInfo = RPCContext::getInstance()->getSession('copy.elite');
		// 获取用户ID，使用用户ID获取精英副本信息
		$uid = RPCContext::getInstance()->getUid();
		// 如果没顺利取得精英副本信息
		if (empty($eliteCopyInfo))
		{
			if (empty($uid)) 
			{
				Logger::fatal('Can not get elite copy info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取精英副本信息
			$eliteCopyInfo = EliteCopyDao::getEliteCopyInfo($uid);
			// 判断精英副本信息是否为空
			if ($eliteCopyInfo === false)
			{
				Logger::debug('Open elite copy.');
				// 初始化人物装备制作信息, 插入一条新数据
				$eliteCopyInfo = EliteCopyDao::addEliteCopyInfo($uid);
			}
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			EliteCopyDao::setBufferWithoutSelect($uid, $eliteCopyInfo);
		}
		// 赋值给自己
		$this->m_copy = $eliteCopyInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
	}

	/**
	 * 修改用户精英副本数据，如果用户打过相应的怪还没有精英副本的话，需要插入新副本数据
	 */
	private function fixUserEliteInfo()
	{
		// 循环查询所有精英副本数据
		foreach (btstore_get()->ELITE_COPY->toArray() as $key => $eliteCopy)
		{
			// 略过没用的key
			if ($key == 'task' || $key == 'enemy')
			{
				continue;
			}
			// 如果没有这个副本才需要进行判断，需要不需要插入新的副本
			if (!isset($this->m_copy['va_copy_info'][$eliteCopy['id']]))
			{
				// 获取对应副本地址 
				$copyID = btstore_get()->ARMY[$eliteCopy['enemy_open']]['copy_id'];
				// 判断开启副本所需击败的部队已经击败
				$copyInst = new MyCopy();
				if ($copyInst->isCopyEnemyDefeated($copyID, $eliteCopy['enemy_open']))
				{
					// 如果已经击败过所需击败的部队，开启新的精英副本
					self::addNewCopy($eliteCopy['id']);
					self::save();
				}
			}
		}
		// 修复已有的账号，如果progress为零，那么看是否给赋值
		if (isset($this->m_copy['va_copy_info'][EliteCopyConf::FIRST_COPY_ID]) && 
			$this->m_copy['progress'] == 0)
		{
			$this->m_copy['progress'] = EliteCopyConf::FIRST_COPY_ID;
			// 设置进session
			RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
		}
		// 对进度进行自修复
		// 获取当前进度
		$progressID = $this->m_copy['progress'];
		// 如果这个副本已经通关了的话
		while ($this->m_copy['va_copy_info'][$progressID]['is_end'])
		{
			// 那么获取可以挑战的下一个副本ID
			$progressID = btstore_get()->ELITE_COPY[$progressID]['next_copy'];
			// 如果还可以攻击下个副本
			if (isset($this->m_copy['va_copy_info'][$progressID]))
			{
				// 更新进度
				$this->m_copy['progress'] = $progressID;
				// 设置进session
				RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
			}
			// 用户尚未拥有这个副本
			else
			{
				break;
			}
		}
	}

	/**
	 * 获取用户精英副本信息
	 */
	public function getUserEliteInfo()
	{
		// 调整下最新的挑战次数
		self::getTodayChallengeTimes();
		// 调整下VA中精英副本的数据
		self::fixUserEliteInfo();
		// 返回前端
		return $this->m_copy;
	}

	/**
	 * 添加新的副本信息
	 * 
	 * @param int $copyID						副本ID
	 */
	public function addNewCopy($copyID)
	{
		// 如果没有设置过这个副本，那么设置新副本
		if (!isset($this->m_copy['va_copy_info'][$copyID]))
		{
			$this->m_copy['va_copy_info'][$copyID] = array('copy_id' => $copyID, 'defeat_id_times' => array(),
			                                               'is_end' => 0, 'enemy_id' => 0);
			// 设置进session
			RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
		}
		// 如果已经有这个副本了，那么直接返回
		else 
		{
			return ;
		}
		// 如果是头一个精英副本，那么更新进度 —— 进度的意思是，当前可以攻打的最远精英副本
		if ($copyID == EliteCopyConf::FIRST_COPY_ID)
		{
			$this->m_copy['progress'] = $copyID;
			// 设置进session
			RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
		}
		Logger::debug('Add new elite copy, copy id is %d.', $copyID);
	}

	/**
	 * 开始攻击某个副本
	 * 
	 * @param int $copyID						副本ID
	 */
	public function startFight($copyID)
	{
    	// 清空失败次数
    	$this->m_copy['coins'] = EliteCopyConf::COINS;
    	// 清空失败购买次数
    	$this->m_copy['buy_coin_times'] = 0;
		// 获取该副本的头一个敌人部队ID
		$enemyID = btstore_get()->ELITE_COPY[$copyID]['army_id_01'];
		// 记录进度，防止越着打
		$this->m_copy['va_copy_info'][$copyID]['enemy_id'] = $enemyID;
		// 清空以前的次数
		$this->m_copy['va_copy_info'][$copyID]['defeat_id_times'] = array();
		// 设置进session
		RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
	}

	/**
	 * 获取当前的可挑战次数
	 */
	public function getTodayChallengeTimes()
	{
		// 如果上次出航的时间是今天之前
		if (!Util::isSameDay($this->m_copy['challenge_time'], EliteCopyConf::REFRESH_TIME))
		{
			// 获取间隔的天数
			$days = Util::getDaysBetween($this->m_copy['challenge_time'], EliteCopyConf::REFRESH_TIME);
			// 调整次数
			$this->m_copy['challenge_time'] = Util::getTime();
			$this->m_copy['challenge_times'] += EliteCopyConf::CHALLANGE_TIMES * $days;
			// 判断是否累积超过了最大值
			if ($this->m_copy['challenge_times'] > btstore_get()->TOP_LIMIT[TopLimitDef::ELITE_COPY_MAX_TIME])
			{
				// 如果超过了，就给最大值，不能再多给次数了
				$this->m_copy['challenge_times'] = btstore_get()->TOP_LIMIT[TopLimitDef::ELITE_COPY_MAX_TIME];
			}
			// 设置进session
			RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
			// 如果改变了，那么直接更新数据库
			self::save();
		}
		// 返回次数
		return $this->m_copy['challenge_times'];
	}

	/**
	 * 重置挑战次数，控制台专用
	 */
	public function resetChallengeTimes()
	{
		// 控制台专用
		$this->m_copy['challenge_time'] = Util::getTime();
		$this->m_copy['challenge_times'] = EliteCopyConf::CHALLANGE_TIMES;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
		self::save();
	}

	/**
	 * 减去一次挑战次数
	 */
	public function subChallengeTimes()
	{
		// 成功通关，去掉一次挑战次数
		--$this->m_copy['challenge_times'];
		$this->m_copy['challenge_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
		// 返回剩余次数
		return $this->m_copy['challenge_times'];
	}

	/**
	 * 获取当前的失败次数
	 */
	public function getCurCoinNum()
	{
		return $this->m_copy['coins'];
	}

	/**
	 * 减去失败次数
	 */
	public function subCoin()
	{
		--$this->m_copy['coins'];
		// 设置进session
		RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
	}

	/**
	 * 增加失败次数
	 */
	public function addCoin()
	{
		++$this->m_copy['coins'];
		++$this->m_copy['buy_coin_times'];
		// 设置进session
		RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
		// 返回现有次数 
		return $this->m_copy['coins'];
	}

	/**
	 * 重置精英副本内容
	 */
	public function resetCopyInfo()
	{
    	// 清空失败次数
    	$this->m_copy['coins'] = EliteCopyConf::COINS;
    	// 清空失败购买次数
    	$this->m_copy['buy_coin_times'] = 0;
    	// 清空进度情况
    	foreach ($this->m_copy['va_copy_info'] as $copyID => $v)
    	{
    		// 将进度设置为0
    		$this->m_copy['va_copy_info'][$copyID]['enemy_id'] = 0;
			$this->m_copy['va_copy_info'][$copyID]['defeat_id_times'] = array();
    	}
		// 设置进session
		RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
	}

	/**
	 * 更新副本进度
	 */
	public function upgradeProgress($copyID)
	{
		// 获取可以挑战的下一个副本ID
		$nextCopyID = btstore_get()->ELITE_COPY[$copyID]['next_copy'];
		// 检查是否可以攻击下一个副本
		if (!empty($this->m_copy['va_copy_info'][$nextCopyID]))
		{
			$this->m_copy['progress'] = $nextCopyID;
		}
		// 标志当前副本的结束
		$this->m_copy['va_copy_info'][$copyID]['is_end'] = 1;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
	}

	/**
	 * 查看副本进度，是否是第一次通关副本
	 * 
	 * @param int $copyID						副本ID
	 */
	public function needPassCopy($copyID)
	{
		// 查看副本进度
		if ($copyID >= $this->m_copy['progress'])
		{
			return true;
		}
		return false;
	}

	/**
	 * 保存部队ID
	 * 
	 * @param int $copyID						副本ID
	 * @param int $enemyID						部队ID
	 */
	public function saveEnemyID($copyID, $enemyID)
	{
		// 记录这次的攻击次数
		if (isset($this->m_copy['va_copy_info'][$copyID]['defeat_id_times'][$enemyID]))
		{
			++$this->m_copy['va_copy_info'][$copyID]['defeat_id_times'][$enemyID];
		}
		// 没设置则从头开始
		else 
		{
			// 只有第一次打这个怪，才需要更新进度
			$this->m_copy['va_copy_info'][$copyID]['defeat_id_times'][$enemyID] = 1;
			// 获取下一个部队ID
			$nextEnemyID = btstore_get()->ARMY[$enemyID]['next_enemies'][0];
			// 记录这个部队ID
			$this->m_copy['va_copy_info'][$copyID]['enemy_id'] = $nextEnemyID;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('copy.elite', $this->m_copy);
	}

	/**
	 * 将数据保存到数据库
	 */
	public function save()
	{
		// 更新到数据库
		EliteCopyDao::updEliteCopyInfo($this->m_copy['uid'], $this->m_copy);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */