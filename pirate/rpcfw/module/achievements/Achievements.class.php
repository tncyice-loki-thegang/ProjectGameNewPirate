<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Achievements.class.php 40245 2013-03-07 09:12:58Z lijinfeng $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/Achievements.class.php $
 * @author $Author: lijinfeng $(liuyang@babeltime.com)
 * @date $Date: 2013-03-07 17:12:58 +0800 (四, 2013-03-07) $
 * @version $Revision: 40245 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : Achievements
 * Description : 成就对外接口实现类
 * Inherit     : IAchievements
 **********************************************************************************************************************/
class Achievements implements IAchievements
{

	/* (non-PHPdoc)
	 * @see IAchievements::getShowAchievements()
	 */
	public function getShowAchievements() 
	{
		Logger::debug('Achievements::getShowAchievements start.');
		// 返回用户所在展示的所有成就
		$ret = AchievementsLogic::getShowAchievements();
		
		Logger::debug('Achievements::getShowAchievements end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IAchievements::getLastOnlineAchievements()
	 */
	public function getLastOnlineAchievements() 
	{
		Logger::debug('Achievements::getLastOnlineAchievements start.');
		// 检查是否获得了成就
		$ret = EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::TOTAL_ONLINE_TIME, 1);

		Logger::debug('Achievements::getLastOnlineAchievements end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IAchievements::getKeepOnlineAchievements()
	 */
	public function getKeepOnlineAchievements() 
	{
		Logger::debug('Achievements::getKeepOnlineAchievements start.');
		// 检查是否获得了成就
		$ret = EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::KEEP_ONLINE_TIME, 1);

		Logger::debug('Achievements::getKeepOnlineAchievements end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IAchievements::getAchievementsByIDs()
	 */
	public function getAchievementsByIDs($achieveIDs) 
	{
		Logger::debug('Achievements::getAchievementsByIDs start.');
		// 返回指定成就
		$ret = AchievementsLogic::getAchievementsByIDs($achieveIDs);

		Logger::debug('Achievements::getAchievementsByIDs end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IAchievements::getAchievementPoints()
	 */
	public function getAchievementPoints() 
	{
		Logger::debug('Achievements::getAchievementPoints start.');
		// 返回用户当前的成就点数
		$ret = AchievementsLogic::getAchievementPoints();
		
		Logger::debug('Achievements::getAchievementPoints end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IAchievements::getAchievementsPointsByType()
	 */
	public function getAchievementsPointsByType() 
	{
		Logger::debug('Achievements::getAchievementsPointsByType start.');
		// 返回用户当前的成就点数
		$ret = AchievementsLogic::getAchievementsPointsByType();
		
		Logger::debug('Achievements::getAchievementsPointsByType end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IAchievements::getShowName()
	 */
	public function getShowName() 
	{
		Logger::debug('Achievements::getShowName start.');
		// 返回正在展示的称号
		$ret = AchievementsLogic::getShowName();
		
		Logger::debug('Achievements::getShowName end.');
		return $ret;
	}
	
	/**
	 * 返回当前称号的加成属性值
	 * @return array,
	 */
	public function getCurrentTitleAttrs($uid)
	{
		if(empty($uid))
			return array();
		
		return AchievementsLogic::getShowTitleAttr($uid);			
	}
	
	

	/* (non-PHPdoc)
	 * @see IAchievements::getNameList()
	 */
	public function getNameList() 
	{
		Logger::debug('Achievements::getNameList start.');
		// 返回用户所有的称号
		$ret = AchievementsLogic::getNameList();
		
		Logger::debug('Achievements::getNameList end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IAchievements::delShowAchievements()
	 */
	public function delShowAchievements($achieveID) 
	{
		Logger::debug('Achievements::delShowAchievements start.');
		// 去掉展示的某个成就
		AchievementsLogic::delShowAchievements($achieveID);
		
		Logger::debug('Achievements::delShowAchievements end.');
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IAchievements::setShowAchievements()
	 */
	public function setShowAchievements($achieveID) 
	{
		Logger::debug('Achievements::setShowAchievements start.');
		// 展示某个成就
		AchievementsLogic::setShowAchievements($achieveID);
		
		Logger::debug('Achievements::setShowAchievements end.');
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IAchievements::delShowName()
	 */
	public function delShowName()
	{
		Logger::debug('Achievements::delShowName start.');
		// 去掉展示的某个称号
		AchievementsLogic::delShowName();
		
		EnUser::modifyBattleInfo();
		
		Logger::debug('Achievements::delShowName end.');
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IAchievements::setShowName()
	 */
	public function setShowName($titleID) 
	{
		Logger::debug('Achievements::setShowName start.');
		// 展示某个称号
		AchievementsLogic::setShowName($titleID);
		
		EnUser::modifyBattleInfo();
		
		Logger::debug('Achievements::setShowName end.');
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IAchievements::getLatestAchievements()
	 */
	public function getLatestAchievements($num) 
	{
		Logger::debug('Achievements::getLatestAchievements start.');
		// 检查参数
		if ($num <= 0)
		{
			Logger::fatal('Err para, %d!', $num);
			throw new Exception('fake');
		}
		// 获取近期获得的成就
		$ret = AchievementsLogic::getLatestAchievements($num);
		
		Logger::debug('Achievements::getLatestAchievements end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IAchievements::fetchSalary()
	 */
	public function fetchSalary() 
	{
		Logger::debug('Achievements::fetchSalary start.');
		// 领工资
		$ret = AchievementsLogic::fetchSalary();
		
		Logger::debug('Achievements::fetchSalary end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IAchievements::excuteNotify()
	 */
	public function excuteNotify($uid, $type, $value_1, $value_2 = 1) 
	{
		Logger::debug('Achievements::excuteNotify start.');
		// 获取uid
		$tmp = RPCContext::getInstance()->getSession('global.uid');
		// 如果没设置当前用户，那么需要设置一下当前用户
		if (empty($tmp))
		{
			RPCContext::getInstance()->setSession('global.uid', $uid);
		}
		// 调用公用的Notify方法
		EnAchievements::__notify($type, $value_1, $value_2);

		Logger::debug('Achievements::excuteNotify end.');
	}

	/* (non-PHPdoc)
	 * @see IAchievements::getPrizeStatus()
	 */
	public function getPrizeStatus() 
	{
		Logger::debug('Achievements::getPrizeStatus start.');
		// 获取奖励详情
		$ret = AchievementsLogic::getPrizeStatus();

		Logger::debug('Achievements::getPrizeStatus end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IAchievements::fetchPrize()
	 */
	public function fetchPrize($prizeID) 
	{
		Logger::debug('Achievements::fetchPrize start.');
		// 获取奖励
		$ret = AchievementsLogic::fetchPrize($prizeID);

		Logger::debug('Achievements::fetchPrize end.');
		return $ret;
	}
	
	public function setUserShowTitleID($titleID)
	{
		return 'ok';
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */