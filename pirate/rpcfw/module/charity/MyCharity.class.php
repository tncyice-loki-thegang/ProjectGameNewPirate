<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyCharity.class.php 27583 2012-09-20 08:56:33Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/charity/MyCharity.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-20 16:56:33 +0800 (四, 2012-09-20) $
 * @version $Revision: 27583 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyCharity
 * Description : 已完成福利数据持有列表
 * Inherit     : 
 **********************************************************************************************************************/
class MyCharity
{

	private $m_charity;							// 活跃度数据
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MyCharity
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
		$charityInfo = RPCContext::getInstance()->getSession('charity.list');
		// 获取用户ID，使用用户ID获取活跃度信息
		$uid = RPCContext::getInstance()->getUid();
		// 如果没顺利取得活跃度信息
		if (empty($charityInfo['uid'])) 
		{
			if (empty($uid)) 
			{
				Logger::fatal('Can not get charity info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户活跃度信息
			$charityInfo = CharityDao::getCharityInfo($uid);
			// 检查用户是否完成相应任务
			if ($charityInfo === false)
			{
				// 初始化人物活跃度信息
				$charityInfo = CharityDao::addNewCharityInfo($uid);
			}
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			CharityDao::setBufferWithoutSelect($uid, $charityInfo);
		}
		// 赋值给自己
		$this->m_charity = $charityInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('charity.list', $this->m_charity);
	}

	/**
	 * 
	 */
	public function getCharityInfo()
	{
		// 返回最新的数据
		return $this->m_charity;
	}

	/**
	 * 记录领取的宝箱
	 * 
	 * @param int $caseID						宝箱ID
	 */
	public function addPirzedTimes($caseID)
	{
		$this->m_charity['prize_id'] += CharityDef::$CASE_INDEX[$caseID];
		// 设置进session
		RPCContext::getInstance()->setSession('charity.list', $this->m_charity);
		// 返回给前端
		return $this->m_charity['prize_id'];
	}

	/**
	 * 记录领取工资的时刻
	 */
	public function setLastSalaryTime()
	{
		$this->m_charity['salary_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('charity.list', $this->m_charity);
	}
	
	public function setLastPrestigeSalaryTime()
	{
		$this->m_charity['prestige_salary_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('charity.list', $this->m_charity);
	}

	/**
	 * 更新数据库
	 */
	public function save()
	{
		// 更新到数据库
		CharityDao::updCharityInfo($this->m_charity['uid'], $this->m_charity);
		// 返回更新信息
		return $this->m_charity;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */