<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyHeroCopy.class.php 26915 2012-09-10 09:07:00Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/herocopy/MyHeroCopy.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-10 17:07:00 +0800 (一, 2012-09-10) $
 * @version $Revision: 26915 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyHeroCopy
 * Description : 英雄副本数据持有类
 * Inherit     :
 **********************************************************************************************************************/
class MyHeroCopy
{
	private $m_copyList;						// 英雄副本数据
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * 
	 * @return MyHeroCopy
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
		// 从 session 中取得英雄副本信息
		$heroCopyInfo = RPCContext::getInstance()->getSession('copy.hero');
		// 获取用户ID，使用用户ID获取英雄副本信息
		$uid = RPCContext::getInstance()->getUid();
		// 如果没顺利取得英雄副本信息
		if (empty($heroCopyInfo))
		{
			if (empty($uid)) 
			{
				Logger::warning('Can not get hero copy info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取英雄副本信息
			$heroCopyInfo = HeroCopyDao::getUserCopies($uid);
			// 判断英雄副本信息是否为空
			if (empty($heroCopyInfo))
			{
				// 检查是否到了水之都
				$city = new City();
				// 如果到了水之都，就初始化数据，如果没有到水之都呢，就直接fake掉
				if ($city->isEnterTown(HeroCopyConf::WATER_TOWN_ID))
				{
					Logger::debug('Open hero copy.');
					// 初始化人物英雄副本信息, 插入一条新数据
					$heroCopyInfo = self::addNewCopy(HeroCopyConf::FIRST_COPY_ID);
				}
				else 
				{
					Logger::warning('Can not init hero copy info before move to water town!');
					throw new Exception('fake');
				}
			}
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			CopyDao::setBufferWithoutSelect($uid, $heroCopyInfo);
		}
		// 赋值给自己
		$this->m_copyList = $heroCopyInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.hero', $this->m_copyList);
	}

	/**
	 * 为用户增加一个副本
	 * 
	 * @param int $copyID						副本ID
	 */
	public function addNewCopy($copyID)
	{
		Logger::debug('Add a new copy, id is %d.', $copyID);

		// 看看是不是已经有了，如果没有再进行插入操作
		if (!empty($this->m_copyList[$copyID]))
		{
			return ;
		}

		// 设置VA字段信息
		$va_info = array('progress' => array(), 'defeat_id_times' => array());
		// 设置插入数据
		$arr = array('uid' => RPCContext::getInstance()->getUid(),
					 'copy_id' => $copyID,
					 'is_over' => 0,
					 'coins' => 0,
					 'buy_coin_times' => 0,
					 'va_copy_info' => $va_info,
					 'status' => DataDef::NORMAL);
		// 增加一个副本信息
		$this->m_copyList[$copyID] = $arr;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.hero', $this->m_copyList);
		// 更新数据库
		HeroCopyDao::updateCopyInfo($arr);
		// 返回所有副本信息
		return $this->m_copyList;
	}

	/**
	 * 获取用户英雄副本信息
	 */
	public function getUserHeroCopyInfo()
	{
		// 返回前端
		return $this->m_copyList;
	}

	/**
	 * 开始攻击某个副本
	 * 
	 * @param int $copyID						副本ID
	 */
	public function startFight($copyID)
	{
    	// 清空失败次数
    	$this->m_copyList[$copyID]['coins'] = HeroCopyConf::COINS;
    	// 清空失败购买次数
    	$this->m_copyList[$copyID]['buy_coin_times'] = 0;
		// 获取该副本的头一个敌人部队ID
		$enemyID = btstore_get()->HERO_COPY[$copyID]['army_id_01'];
		// 记录进度，防止越着打
		$this->m_copyList[$copyID]['va_copy_info']['progress'] = $enemyID;
		// 清空以前的次数
		$this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'] = array();
		// 设置进session
		RPCContext::getInstance()->setSession('copy.hero', $this->m_copyList);
	}

	/**
	 * 减去失败次数
	 * 
	 * @param int $copyID						当前正在攻打的副本ID
	 */
	public function subCoin($copyID)
	{
		--$this->m_copyList[$copyID]['coins'];
		// 设置进session
		RPCContext::getInstance()->setSession('copy.hero', $this->m_copyList);
	}

	/**
	 * 增加失败次数
	 * 
	 * @param int $copyID						当前正在攻打的副本ID
	 */
	public function addCoin($copyID)
	{
		++$this->m_copyList[$copyID]['coins'];
		++$this->m_copyList[$copyID]['buy_coin_times'];
		// 设置进session
		RPCContext::getInstance()->setSession('copy.hero', $this->m_copyList);
		// 返回现有次数 
		return $this->m_copyList[$copyID]['coins'];
	}

	/**
	 * 通关副本
	 * 
	 * @param int $copyID						当前正在攻打的副本ID
	 */
	public function setCopyOver($copyID)
	{
		++$this->m_copyList[$copyID]['is_over'];
		// 设置进session
		RPCContext::getInstance()->setSession('copy.hero', $this->m_copyList);
	}

	/**
	 * 控制台专用，设置副本为未通关
	 * 
	 * @param int $copyID						当前正在攻打的副本ID
	 */
	public function unsetCopyOver($copyID)
	{
		$this->m_copyList[$copyID]['is_over'] = 0;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.hero', $this->m_copyList);
	}

	/**
	 * 重置英雄副本内容
	 */
	public function resetCopyInfo($copyID)
	{
		if (isset($this->m_copyList[$copyID]))
		{
    		// 将进度设置为0
    		self::startFight($copyID);
    		// 保存
    		self::save($copyID);
		}
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
		if (isset($this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'][$enemyID]))
		{
			++$this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'][$enemyID];
		}
		// 没设置则从头开始
		else 
		{
			// 只有第一次打这个怪，才需要更新进度
			$this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'][$enemyID] = 1;
			// 获取下一个部队ID
			$nextEnemyID = btstore_get()->ARMY[$enemyID]['next_enemies'][0];
			// 记录这个部队ID
			$this->m_copyList[$copyID]['va_copy_info']['progress'] = $nextEnemyID;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('copy.hero', $this->m_copyList);
	}

	/**
	 * 将数据保存到数据库
	 */
	public function save($copyID)
	{
		// 更新到数据库
		HeroCopyDao::updateCopyInfo($this->m_copyList[$copyID]);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */