<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Myboat.class.php 17994 2012-04-05 08:48:52Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sailboat/Myboat.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-04-05 16:48:52 +0800 (四, 2012-04-05) $
 * @version $Revision: 17994 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyBoat
 * Description : 主船数据持有类
 * Inherit     : 
 **********************************************************************************************************************/
class MyBoat
{

	protected static $m_boatInfo;				// 主船数据

	/**
	 * 构造函数，获取 session 信息
	 */
	protected function __construct() 
	{
		// 保存主船信息
		self::$m_boatInfo = self::getCurBoatInfo();
		// 如果没顺利取得主船信息
		if (empty(self::$m_boatInfo['uid']))
		{
			Logger::FATAL('Can not get boat info.');
			throw new Exception('fake');
		}
		// 调整建筑队列的时间
		self::adjustBuilderTime();
		// 设置进session
		RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);
		RPCContext::getInstance()->setSession('global.boatType', self::$m_boatInfo['boat_type']);
	}

	/**
	 * 获取主船信息
	 */
	public function getBoatInfo()
	{
		// 调整建筑队列的时间
		self::adjustBuilderTime();
		// 设置进session
		RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);
		RPCContext::getInstance()->setSession('global.boatType', self::$m_boatInfo['boat_type']);
		// 获取持有化的数据
		return self::$m_boatInfo;
	}

	/**
	 * 将主船信息写入数据库
	 */
	public function save()
	{
		// 操作数据库
		SailboatDao::updateBoatInfo(self::$m_boatInfo['uid'], self::$m_boatInfo);
	}

	/**
	 * 调整、校准建筑队列的状态和时间
	 * 
	 * @param array $boatInfo					主船数据
	 */
	protected function adjustBuilderTime()
	{
		// 获取建筑队列状态，查看现有的建筑队列时间
		$builderList = self::$m_boatInfo['va_boat_info']['list_info'];
		// 记录下当前时间
		$curTime = Util::getTime();
		// 查看每一个建筑队列的状态
		foreach ($builderList as $key => $builder)
		{
			// 先看下建筑队列是否需要更新状态, 如果已经更新过状态了，就不需要再更新了
			if ($builder['endtime'] <= $curTime && 
			    $builderList[$key]['state'] != SailboatConf::BUILDING_FREE) 
			{
				// 如果建筑队列时间已经小于当前时间, 那么就设置为 空闲
				$builderList[$key]['state'] = SailboatConf::BUILDING_FREE;
				$builderList[$key]['endtime'] = $curTime;
				Logger::debug("The BuildList status %s， endTime is %s", 
							  $builderList[$key]['state'], $builderList[$key]['endtime']);
			}
		}
		// 更新CD时刻
		self::$m_boatInfo['va_boat_info']['list_info'] = $builderList;
	}

	/**
	 * 获取当前主船信息，从 session (包括主船中各个节点的开启工作)
	 * 
	 * @throws Exception
	 */
	public static function getCurBoatInfo()
	{
		// 从session中取得主船信息
		$boatInfo = RPCContext::getInstance()->getSession('boat.boatInfo');
		// 检查获取的参数值，如果没获得，则需要获得，放入session
		if (empty($boatInfo)) 
		{
			// 获取用户ID，使用用户ID获取主船信息
			$uid = RPCContext::getInstance()->getSession('global.uid');
			if (empty($uid))
			{
				Logger::fatal('Can not get boat info from session!');
				throw new Exception('fake');
			}
			// 通过用户ID获取用户主船信息
			$boatInfo = SailboatDao::getBoatInfoByUid($uid, SailboatConf::$SEL_ALL);
			// 什么都没或得到，那么就直接失败
			if (empty($boatInfo['uid'])) 
			{
				// 判断是否已经接过了开启任务
				if (!EnSwitch::isOpen(SwitchDef::BOAT))
				{
					Logger::fatal('Can not get boat info before get task.');
					throw new Exception('fake');
				}
				// 完成任务了，弄一条新船
				$boatInfo = self::addNewBoat($uid);
			}
			// 设置进session
			RPCContext::getInstance()->setSession('boat.boatInfo', $boatInfo);
		}
		// 返回主船信息
		return $boatInfo;
	}

	/**
	 * 创建一条新船
	 */
	static private function addNewBoat($uid)
	{
    	// 设置建筑队列字段
    	$listInfo = array_fill(0, SailboatConf::BUILD_INIT_NUM, 
    	                       array('state' => SailboatConf::BUILDING_FREE, 'endtime' => Util::getTime()));

    	// 设置活动字段
    	$vaArr = array('cabin_id_lv' => array(), 'list_info' => $listInfo,
    	               'all_design' => array(), 'now_design' => array(), 'now_skill' => array());
		// 设置属性
		$boatInfo = array('uid' => $uid,
					      'boat_type' => SailboatConf::REFIT_ID_01,
					      'wallpiece_item_id' => 0,
					      'cannon_item_id' => 0,
						  'figurehead_item_id' => 0,
					      'sails_item_id' => 0,
					      'armour_item_id' => 0,
					      'va_boat_info' => $vaArr,
					      'status' => DataDef::NORMAL);

		// 如果没有船，则需要造一条
		$ret = SailboatDao::makeNewBoat($boatInfo);

		// 同时初始化船长室信息
		EnCaptain::addNewCaptainInfoForUser($uid);

		// 设置进session
		RPCContext::getInstance()->setSession('boat.boatInfo', $boatInfo);
		RPCContext::getInstance()->setSession('global.boatType', self::$m_boatInfo['boat_type']);
		// 返回创建的主船信息
		return $boatInfo;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */