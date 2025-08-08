<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Smelting.class.php 40138 2013-03-06 10:47:41Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/Smelting.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-03-06 18:47:41 +0800 (三, 2013-03-06) $
 * @version $Revision: 40138 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : Smelting
 * Description : 装备制作对外接口实现类
 * Inherit     : ISmelting
 **********************************************************************************************************************/
class Smelting implements ISmelting
{
	/* (non-PHPdoc)
	 * @see ISmelting::getSmeltingInfo()
	 */
	public function getSmeltingInfo() 
	{
		Logger::debug('Smelting::getSmeltingInfo Start.');
		// 获取用户的装备制作信息
		$ret = SmeltingLogic::getSmeltingInfo();

		Logger::debug('Smelting::getSmeltingInfo End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISmelting::smeltingAll()
	 */
	public function smeltingAll($type, $itemType) 
	{
		Logger::debug('Smelting::smeltingAll Start.');
		// 熔炼
		$ret = SmeltingLogic::smeltingAll($type, $itemType);
		
		Logger::debug('Smelting::smeltingAll End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISmelting::smeltingOnce()
	 */
	public function smeltingOnce($type, $itemType) 
	{
		Logger::debug('Smelting::smeltingOnce Start.');
		// 熔炼
		$ret = SmeltingLogic::smeltingOnce($type, $itemType);
		
		Logger::debug('Smelting::smeltingOnce End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISmelting::getSmeltingItem()
	 */
	function getSmeltingItem($itemType)
	{
		Logger::debug('Smelting::getSmeltingItem Start.');
		// 获取熔炼的货
		$ret = SmeltingLogic::getSmeltingItem($itemType);
		// 通知成就系统
		EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::SMELTING_TIMES, 1);

		Logger::debug('Smelting::getSmeltingItem End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISmelting::inviteArtificer()
	 */
	public function inviteArtificer() 
	{
		Logger::debug('Smelting::inviteArtificer Start.');
		// 花钱聘请一位工匠
		$ret = SmeltingLogic::inviteArtificer();

		Logger::debug('Smelting::inviteArtificer End.');
		return $ret;
	}

    /* (non-PHPdoc)
	 * @see ISmelting::clearCDByGold()
	 */
	public function clearCDByGold() 
	{
		Logger::debug('Smelting::clearCDByGold Start.');
		// 清除装备制作时刻，并返回实际使用金币数
		$ret = SmeltingLogic::clearCDByGold();

		Logger::debug('Smelting::clearCDByGold End.');
		return $ret;
	}

    /* (non-PHPdoc)
	 * @see ISmelting::integralExchange()
	 */
	public function integralExchange($itemTID) 
	{
		Logger::debug('Smelting::integralExchange Start.');
		$itemTID = intval($itemTID);
		// 检查参数
		if ($itemTID <= 0)
		{
			Logger::fatal('Err para, %d!', $itemTID);
			throw new Exception('fake');
		}
		// 积分换好礼
		$ret = SmeltingLogic::integralExchange($itemTID);

		Logger::debug('Smelting::integralExchange End.');
		return $ret;
	}

    /* (non-PHPdoc)
	 * @see ISmelting::refreshArtificer()
	 */
	public function refreshArtificer() 
	{
		Logger::debug('Smelting::refreshArtificer Start.');
		// 获取当前时刻
		$curTime = Util::getTime();
		// 获取工匠离开时刻
		$ret = SmeltingDao::getArtificerLeaveTime();
		// 如果工匠时间忘记了初始化 ，罪过啊罪过……
		if ($ret === false)
		{
			// 获取当日日期
			$curYmd = date("Y-m-d ", $curTime);
			// 获取开始时刻
			$startTime = $curYmd.'04:00:00';
			// 设置工匠离开时刻
			$leaveTime = strtotime($startTime);
			// 设置下次刷新时刻
			$refreshTime = $leaveTime + SmeltingConf::NEXT_REFRESH_TIME;
			// 设置数据库
			SmeltingDao::initArtificerLeaveTime($leaveTime, $refreshTime);
			
		}
		// 初始化以后就正常了
		else 
		{
			// 获取到工匠离开时刻
			$leaveTime = $ret[SmeltingConf::ARTIFICER_LEAVE_TIME];
			// 获取下次刷新时刻 
			$refreshTime = $ret[SmeltingConf::ARTIFICER_REFRESH_TIME];
			Logger::debug('Artificer refresh time is %d, leave time is %d.', $refreshTime, $leaveTime);
			// 判断重复请求
			if (!empty($refreshTime) && $refreshTime > $curTime)
			{
				return ;
			}
			// 设置工匠离开时刻
			$leaveTime = $refreshTime;
			// 设置下次刷新时刻
			$refreshTime = $refreshTime + SmeltingConf::NEXT_REFRESH_TIME;
			// 设置回数据库
			SmeltingDao::updArtificerLeaveTime($leaveTime, $refreshTime);
		}
		Logger::debug('Next artificer refresh time is %d, leave time is %d.', $refreshTime, $leaveTime);
		// 设置下次刷新的Timer
		TimerTask::addTask(0, $refreshTime, 'smelting.refreshArtificer', array());

		Logger::debug('Smelting::refreshArtificer End.');
	}


}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */