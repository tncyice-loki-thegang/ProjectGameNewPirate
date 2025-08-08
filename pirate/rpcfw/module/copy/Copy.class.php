<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Copy.class.php 29999 2012-10-19 06:29:26Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/Copy.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-19 14:29:26 +0800 (五, 2012-10-19) $
 * @version $Revision: 29999 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : Copy
 * Description : 副本对外接口实现类
 * Inherit     : ICopy
 **********************************************************************************************************************/
class Copy implements ICopy
{
	/* (non-PHPdoc)
	 * @see ICopy::enterCopy()
	 */
	public function enterCopy($copyID) 
	{
		// 检查参数
		if ($copyID <= 0)
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		Logger::debug('Copy::enterCopy start.');
		// 进入副本
		CopyLogic::enterCopy($copyID);
		Logger::debug('Copy::enterCopy end.');
		
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see ICopy::leaveCopy()
	 */
	public function leaveCopy() 
	{
		Logger::debug('Copy::leaveCopy start.');
		// 离开副本，删掉信息
		RPCContext::getInstance()->unsetSession('global.copyId');
		Logger::debug('Copy::leaveCopy end.');
		
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see ICopy::getUserLatestCopyInfo()
	 */
	public function getUserLatestCopyInfo()
	{
		Logger::debug('Copy::getUserLatestCopyInfo start.');
		// 获取副本信息
		$copyInfo = CopyLogic::getUserLatestCopyInfo();
		Logger::debug('Copy::getUserLatestCopyInfo end.');
		return $copyInfo;
	}

	/* (non-PHPdoc)
	 * @see ICopy::getCopiesInfoByCopyChooseID()
	 */
	public function getCopiesInfoByCopyChooseID($ccID)
	{
		Logger::debug('Copy::getCopiesInfoByCopyChooseID start.');
		// 获取副本信息
		$copyInfo = CopyLogic::getCopiesInfoByCopyChooseID($ccID);

		Logger::debug('Copy::getCopiesInfoByCopyChooseID end.');
		return $copyInfo;
	}

	/* (non-PHPdoc)
	 * @see ICopy::getCopyInfo()
	 */
	public function getCopyInfo($copyID)
	{
		Logger::debug('Copy::getCopyInfo start.');
		$copyID = intval($copyID);
		// 检查参数
		if ($copyID <= 0)
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		// 获取副本信息
		$copyInfo = CopyLogic::getCopyInfo($copyID);
		Logger::debug('Copy::getCopyInfo end.');		
		return $copyInfo;
	}

	/* (non-PHPdoc)
	 * @see ICopy::getEnemyDefeatNum()
	 */
	public function getEnemyDefeatNum($enemyID)
	{
		Logger::debug('Copy::getEnemyDefeatNum start.');
		$enemyID = intval($enemyID);
		// 检查参数
		if ($enemyID <= 0)
		{
			Logger::fatal('Err para, %d!', $enemyID);
			throw new Exception('fake');
		}
		// 获取某个敌人被K的次数
		$ret = CopyLogic::getEnemyDefeatNum(RPCContext::getInstance()->getUid(), $enemyID);
		Logger::debug('Copy::getEnemyDefeatNum end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::attack()
	 */
	public function attack($copyID, $enemyID, $npcTeamID = null, $heroList = null)
	{
		Logger::debug('Copy::attack start.');
		$copyID = intval($copyID);
		$enemyID = intval($enemyID);
		// 检查参数
		if ($copyID <= 0 || $enemyID <= 0)
		{
			Logger::fatal('Err para, %d, %d!', $copyID, $enemyID);
			throw new Exception('fake');
		}
		// 攻击某个部队
		$ret = CopyLogic::attack($copyID, $enemyID, $npcTeamID, $heroList);
		Logger::debug('Copy::attack end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::navalAttack()
	 */
	public function navalAttack($enemyID) {
		// TODO Auto-generated method stub
		
	}

	/**
	 * 获取某用户的所有副本
	 * @param int $uid							用户ID
	 */
	public function getUserCopiesByUid($uid)
	{
		Logger::debug('Copy::getUserCopiesByUid start.');
		$uid = intval($uid);
		// 检查参数
		if ($uid <= 0)
		{
			Logger::fatal('Err para, %d!', $uid);
			throw new Exception('fake');
		}
		// 获取所有副本信息
		$copies = CopyLogic::getUserCopiesByUid($uid);
		Logger::debug('Copy::getUserCopiesByUid end.');
		return $copies;
	}

	/**
	 * 获取用户的所有副本
	 */
	public function getUserCopies()
	{
		Logger::debug('Copy::getUserCopies start.');
		// 获取所有副本信息
		$copies = CopyLogic::getUserCopies();
		Logger::debug('Copy::getUserCopies end.');
		return $copies;
	}

	/**
	 * 返回是否打过这个部队, 如果攻击过，就返回攻击过的次数，否则返回0
	 * @param int $enemyID						部队ID
	 */
	public function isEnemyDefeated($enemyID)
	{
		Logger::debug('Copy::isEnemyDefeated start.');
		// 检查参数
		if ($enemyID <= 0)
		{
			Logger::fatal('Err para, %d!', $enemyID);
			throw new Exception('fake');
		}
		// 获取是否打过这个部队
		$ret = CopyLogic::isEnemyDefeated($enemyID);
		Logger::debug('Copy::isEnemyDefeated end.');
		
		return $ret;
	}

	/**
	 * 查看是否打过这组部队
	 * @param array $enemyID					部队ID数组
	 */
	public function getEnemiesDefeatNum($enemyIDs)
	{
		Logger::debug('Copy::getEnemiesDefeatNum start.');
		// 参数出错
		if (!is_array($enemyIDs))
		{
			Logger::fatal('Err para!');
			throw new Exception('fake');
		}
		// 获取是否打过这组部队
		$ret = CopyLogic::getEnemiesDefeatNum($enemyIDs);
		Logger::debug('Copy::getEnemiesDefeatNum end.');

		return $ret;
	}

	/**
	 * 返回是否打过这个副本
	 * @param int $copyID						副本ID
	 */
	public function isCopyOver($copyID)
	{
		Logger::debug('Copy::isCopyOver start.');
		// 检查参数
		if ($copyID <= 0)
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		// 获取是否打过这个副本
		$ret = CopyLogic::isCopyOver($copyID);
		Logger::debug('Copy::isCopyOver end.');
		
		return $ret;
	}

	/**
	 * 返回所有的攻略和战报信息
	 * @param int $enemyID						部队ID
	 */
	public function getReplayList($enemyID)
	{
		Logger::debug('Copy::getReplayList start.');
		// 检查参数
		if ($enemyID <= 0)
		{
			Logger::fatal('Err para, %d!', $enemyID);
			throw new Exception('fake');
		}
		// 返回所有的攻略和战报信息
		$ret = CopyLogic::getReplayList($enemyID);
		Logger::debug('Copy::getReplayList end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::clearFightCdByGold()
	 */
	public function clearFightCdByGold()
	{
		Logger::debug('Copy::clearFightCdByGold start.');
		// 清除CD时间
		$ret = CopyLogic::clearFightCdByGold();
		Logger::debug('Copy::clearFightCdByGold end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::getPrize()
	 */
	public function getPrize($copyID, $caseID)
	{
		Logger::debug('Copy::getPrize start.');
		// 检查参数
		if ($copyID <= 0)
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		// 获取奖励信息
		$bagInfo = CopyLogic::getPrize($copyID, $caseID);
		Logger::debug('Copy::getPrize end.');

		return $bagInfo;
	}

	/* (non-PHPdoc)
	 * @see ICopy::startAutoAtk()
	 */
	public function startAutoAtk($copyID, $enemyID, $times)
	{
		Logger::debug('Copy::startAutoAtk start.');
		// 检查参数
		if ($copyID <= 0)
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		// 开始挂机操作
		$ret = AutoAtk::startAutoAtk($copyID, $enemyID, $times);
		Logger::debug('Copy::startAutoAtk end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::cancelAutoAtk()
	 */
	public function cancelAutoAtk() 
	{
		Logger::debug('Copy::cancelAutoAtk start.');
		// 取消挂机操作
		$ret = AutoAtk::cancelAutoAtk();
		Logger::debug('Copy::cancelAutoAtk end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::attackOnce()
	 */
	public function attackOnce($isLogin = false) 
	{
		Logger::debug('Copy::attackOnce start.');
		// 攻击一次
		$ret = AutoAtk::attackOnce($isLogin);
		Logger::debug('Copy::attackOnce end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::attackOnceByGold()
	 */
	public function attackOnceByGold() 
	{
		Logger::debug('Copy::attackOnceByGold start.');
		// 用金币攻击一次
		$ret = AutoAtk::attackOnceByGold();
		Logger::debug('Copy::attackOnceByGold end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::checkWhenLogin()
	 */
	public function checkWhenLogin() 
	{
		Logger::debug('Copy::checkWhenLogin start.');
		// 登陆时调用的方法
		$ret = AutoAtk::checkWhenLogin();
		Logger::debug('Copy::checkWhenLogin end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::endAttackByGold()
	 */
	public function endAttackByGold() 
	{
		Logger::debug('Copy::endAttackByGold start.');
		// 使用金币结束挂机
		$ret = AutoAtk::endAttackByGold();
		Logger::debug('Copy::endAttackByGold end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::getAutoAtkInfo()
	 */
	public function getAutoAtkInfo() 
	{
		Logger::debug('Copy::getAutoAtkInfo start.');
		// 获取挂机信息
		$ret = AutoAtk::getAutoAtkInfo();
		Logger::debug('Copy::getAutoAtkInfo end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::getAllCopiesID()
	 */
	public function getAllCopiesID()
	{
		Logger::debug('Copy::getAllCopiesID start.');
		// 获取所有副本ID
		$copiesID = CopyLogic::getAllCopiesID();
		Logger::debug('Copy::getAllCopiesID end.');
		return $copiesID;
	}

	/* (non-PHPdoc)
	 * @see ICopy::createTeam()
	 */
	public function createTeam($enemyID, $isAutoStart, $joinLimit) 
	{
		Logger::debug('Copy::createTeam start.');
		// 检查参数
		if ($enemyID <= 0)
		{
			Logger::fatal('Err para, %d!', $enemyID);
			throw new Exception('fake');
		}
		// 创建队伍
		$ret = CommonGroupBattle::createTeam($enemyID, $isAutoStart, $joinLimit);
		Logger::debug('Copy::createTeam end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::joinTeam()
	 */
	public function joinTeam($enemyID, $teamId) 
	{
		Logger::debug('Copy::joinTeam start.');
		// 检查参数
		if ($enemyID <= 0)
		{
			Logger::fatal('Err para, %d!', $enemyID);
			throw new Exception('fake');
		}
		// 加入队伍
		$ret = CommonGroupBattle::joinTeam($enemyID, $teamId);
		Logger::debug('Copy::joinTeam end.');

		return $ret;
	}

	/**
	 * 更新用户的杀敌信息
	 * 
	 * @param int $uid							用户ID
	 * @param int $enemyID						敌人ID
	 * @param int $atkRet						战斗模块的返回值
	 * @param int $isLeader						是否是队长
	 * 
	 * @throws Exception
	 */
	public function addGroupEnemyDefeatInfo($uid, $enemyID, $atkRet, $isLeader = false)
	{
		Logger::debug('Copy::addGroupEnemyDefeatInfo start.');
		// 内部调用，战斗后处理使用的函数
		GroupBattleBase::addGroupEnemyDefeatInfo($uid, $enemyID, $atkRet, $isLeader);

		Logger::debug('Copy::addGroupEnemyDefeatInfo end.');
	}

	/* (non-PHPdoc)
	 * @see ICopy::startAttack()
	 */
	public function groupAttack($teamList, $enemyID) 
	{
		Logger::debug('Copy::groupAttack start.');
		// 检查参数
		if ($enemyID <= 0)
		{
			Logger::fatal('Err para, %d!', $enemyID);
			throw new Exception('fake');
		}
		// 调用普通团战
		$inst = new CommonGroupBattle();
		$inst->startAttack($enemyID, $teamList);

		Logger::debug('Copy::groupAttack end.');
	}

	/* (non-PHPdoc)
	 * @see ICopy::startAutoAttack()
	 */
	public function startAutoAttack($teamList, $enemyID) 
	{
		Logger::debug('Copy::startAutoAttack start.');
		// 检查参数
		if ($enemyID <= 0)
		{
			Logger::fatal('Err para, %d!', $enemyID);
			throw new Exception('fake');
		}
		// 调用机器人的团战
		$inst = new AutoGroupBattle();
		$ret = $inst->startAttack($enemyID, $teamList);

		Logger::debug('Copy::startAutoAttack end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::getCommonGroupArmyDefeatNum()
	 */
	public function getCommonGroupArmyDefeatNum() 
	{
		Logger::debug('Copy::getCommonGroupArmyDefeatNum start.');
		// 得到攻击次数
		$ret = GroupBattleBase::getCommonGroupArmyDefeatNum();
		Logger::debug('Copy::getCommonGroupArmyDefeatNum end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::getActivityGroupArmyDefeatNum()
	 */
	public function getActivityGroupArmyDefeatNum() 
	{
		Logger::debug('Copy::getActivityGroupArmyDefeatNum start.');
		// 得到攻击次数
		$ret = GroupBattleBase::getActivityGroupArmyDefeatNum();
		Logger::debug('Copy::getActivityGroupArmyDefeatNum end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::getInviteSetting()
	 */
	public function getInviteSetting() 
	{
		Logger::debug('Copy::getInviteSetting start.');
		// 得到用户配置
		$ret = AutoGroupBattle::getInviteSetting();
		Logger::debug('Copy::getInviteSetting end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICopy::saveInviteSetting()
	 */
	public function saveInviteSetting($list) 
	{
		Logger::debug('Copy::saveInviteSetting start.');
		// 保存用户配置
		$ret = AutoGroupBattle::saveInviteSetting($list);
		Logger::debug('Copy::saveInviteSetting end.');

		return $ret;
	}

	public function getCommonGroupBattleInviteSetting()
	{
		Logger::debug('Copy::getCommonGroupBattleInviteSetting start.');
		// 得到用户配置
		$ret = AutoGroupBattle::getInviteSetting();
		Logger::debug('Copy::getCommonGroupBattleInviteSetting end.');

		return $ret;
	}
	
	public function saveCommonGroupBattleInviteSetting($friends)
	{
		Logger::debug('Copy::saveCommonGroupBattleInviteSetting start.');
		// 保存用户配置
		$ret = AutoGroupBattle::saveInviteSetting($friends);
		Logger::debug('Copy::saveCommonGroupBattleInviteSetting end.');

		return $ret;
	}
		
	public function startCommonGroupBattleAutoAttack($teamList, $enemyID) 
	{
		Logger::debug('Copy::startCommonGroupBattleAutoAttack start.');
		// 检查参数
		if ($enemyID <= 0)
		{
			Logger::fatal('Err para, %d!', $enemyID);
			throw new Exception('fake');
		}
		// 调用机器人的团战
		$inst = new AutoGroupBattle();
		$ret = $inst->startAttack($enemyID, $teamList);

		Logger::debug('Copy::startCommonGroupBattleAutoAttack end.');
		return $ret;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */