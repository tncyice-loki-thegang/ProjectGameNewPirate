<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: CommonGroupBattle.class.php 32822 2012-12-11 07:04:21Z YangLiu $
 *
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/CommonGroupBattle.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-11 15:04:21 +0800 (二, 2012-12-11) $
 * @version $Revision: 32822 $
 * @brief
 *
 **/

/**********************************************************************************************************************
 * Class       : CommonGroupBattle
 * Description : 普通多人战类
 * Inherit     : GroupBattleBase
 **********************************************************************************************************************/
class CommonGroupBattle extends GroupBattleBase
{
	/**
	 * 创建队伍
	 * 
	 * @param int $enemyID						部队ID
	 * @param bool $isAutoStart					是否自动开战
	 * @param int $joinLimit					组队限制 （公会还是阵营）
	 */
	public static function createTeam($enemyID, $isAutoStart, $joinLimit)
	{
		// 进行例行检查
		$ret = parent::beforeAttackCheck($enemyID);
		// 检查通过的话
		if ($ret == 'ok')
		{
			// 获取人物数据
			$user = EnUser::getUserObj();
			// 设置战斗力信息
			RPCContext::getInstance()->setSession("global.fightForce", $user->getFightForce());
			// 完成所有检查，可以创建队伍
			RPCContext::getInstance()->createTeam($isAutoStart, $joinLimit);
			RPCContext::getInstance()->getFramework()->resetCallback();
	
			// 返回当前血包数量
			RPCContext::getInstance()->sendMsg(array($user->getUid()), 
			                                   're.copy.getCurrentBloodbag', 
			                                   array($user->getBloodPackage()));
		}
		return $ret;
	}

	/**
	 * 加入队伍
	 * 
	 * @param int $enemyID						部队ID
	 * @param bool $teamId						创建好的小队ID
	 */
	public static function joinTeam($enemyID, $teamId)
	{
		// 进行例行检查
		$ret = parent::beforeAttackCheck($enemyID);
		// 检查通过的话
		if ($ret == 'ok')
		{
			// 获取人物数据
			$user = EnUser::getUserObj();
			// 设置战斗力信息
			RPCContext::getInstance()->setSession("global.fightForce", $user->getFightForce());
			// 完成所有检查，可以加入队伍
			RPCContext::getInstance()->joinTeam($teamId);
			RPCContext::getInstance()->getFramework()->resetCallback();
	
			// 返回当前血包数量
			RPCContext::getInstance()->sendMsg(array($user->getUid()), 
			                                   're.copy.getCurrentBloodbag', 
			                                   array($user->getBloodPackage()));
		}
		return $ret;
	}

	/**
	 * 计算战斗结果 
	 * 有些工作需要在这里做 —— 譬如掉落道具，增加经验之类
	 * 
	 * @param int $enemyID						敌人军团ID
	 * @param array $atkRet						战斗模块返回值
	 * @param array $teamList					uid列表
	 * 
	 * @throws Exception
	 */
	protected static function calculateGroupFightRet($enemyID, $atkRet, $teamList)
	{
		// 给每个人奖励
		for ($index = 0; $index < count($teamList); ++$index)
		{
			// 判断是否是队长
			$isLeader = $index == 0 ? true : false;
			// 调用父类计算方法来计算结果
			RPCContext::getInstance()->executeTask($teamList[$index], 
			                                       'copy.addGroupEnemyDefeatInfo',
			                                       array($teamList[$index], $enemyID, $atkRet, $isLeader));
		}
	}

	/**
	 * 开战
	 * 
	 * @param int $enemyID						敌人ID
	 * @param array $teamList					玩家组队uid的数组
	 * @throws Exception
	 */
	public static function startAttack($enemyID, $teamList)
	{
		/**************************************************************************************************************
 		 * 最后一道关卡，如果这里不对了，直接短路，不再进行战斗操作 —— 其实只用于机器人组队的时候，因为大家组队，没有一个人可以有这种权限干掉整个队伍
 		 **************************************************************************************************************/
		if (!self::otherCheck($teamList))
		{
			return 'err';
		}

		/**************************************************************************************************************
 		 * 组队战斗
 		 **************************************************************************************************************/
		$atkRet = parent::doAttack($enemyID, $teamList);
		// 计算战斗结果
		self::calculateGroupFightRet($enemyID, $atkRet, $teamList);
	}

	/**
	 * 其他子类特有的检查
	 * 
	 * @throws Exception
	 */
	protected static function otherCheck($list)
	{
		return true;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */