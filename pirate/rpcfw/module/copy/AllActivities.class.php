<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : AllActivities
 * Description : 活动数据持有类
 * Inherit     : 
 **********************************************************************************************************************/
class AllActivities
{
	private $m_actInfo;							// 副本数据
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return AllActivities
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
	 * 获取所有活动信息
	 */
	public function getActivitiesInfo()
	{
		return $this->m_actInfo;
	}

	/**
	 * 使用活动ID，获取某个活动的信息
	 */
	public function getActivityInfo($actID)
	{
		return isset($this->m_actInfo[$actID]) ? $this->m_actInfo[$actID] : false;
	}

	/**
	 * 加入一条新活动记录
	 * @param int $actID						活动ID
	 * @param int $sTime						活动开始时刻
	 */
	public function addNewActivity($actID, $sTime)
	{
		// 设置VA字段信息
		$va_info = array(array('refreshPoint' => 0, 'enemyID' => 0));
		// 设置插入数据
		$arr = array('activity_id' => $actID,
					 'next_refresh_time' => $sTime,
					 'va_activity_info' => $va_info,
					 'status' => CopyDef::INIT);

		// 添加一条新数据， 更新至缓存
		$this->m_actInfo[$actID] = $arr;
	}

	/**
	 * 更新数据库
	 * @param int $actID						活动ID
	 */
	public function save($actID)
	{
		// insert or update  高科技啊！
		return CopyDao::updateActInfo($actID, $this->m_actInfo[$actID]);
	}

	/**
	 * 更新活动刷新时刻
	 * @param int $actID						活动ID
	 * @param int $nexTime						下次刷新时间
	 */
	public function updActRefreshTime($actID, $nexTime)
	{
		// 设置下次刷新时间
		$this->m_actInfo[$actID]['next_refresh_time'] = $nexTime;
	}

	/**
	 * 更新活动刷新时刻
	 * @param int $actID						活动ID
	 * @param int $state						状态
	 */
	public function updActStatus($actID, $state)
	{
		// 设置下次刷新时间
		$this->m_actInfo[$actID]['status'] = $state;
	}

	/**
	 * 更新活动的刷新点信息
	 * @param int $actID						活动ID
	 * @param array $armyInfo					刷新点数据
	 */
	public function updActRefreshPoints($actID, $armyInfo)
	{
		// 设置刷新点信息
		$this->m_actInfo[$actID]['va_activity_info'] = $armyInfo;
	}

	/**
	 * 构造函数，从数据库获取所有活动信息
	 */
	private function __construct()
	{
		// 获取所有活动数据
		$this->m_actInfo = CopyDao::getAllActivities();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */