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
interface ICopy
{
	/**
	 * 进入副本
	 * 
	 * @param int $copyID						副本ID
	 * @return ok
	 */
	function enterCopy($copyID);

	/**
	 * 离开副本
	 * 
	 * @return ok
	 */
	function leaveCopy();

	/**
	 * 获取用户最后打到的副本信息
	 * 
	 * @return 参照getCopyInfo					最新的副本信息
	 */
	function getUserLatestCopyInfo();

	/**
	 * 获取用户最后打到的副本信息
	 * 
	 * @param int $ccID							副本选择表ID
	 * 
	 * @return 参照getCopyInfo					副本信息数组
	 *         false							没获得到内容
	 */
	function getCopiesInfoByCopyChooseID($ccID);

	/**
	 * 获取副本信息
	 * @param int $copyID						副本ID
	 * 
	 * @return array							副本信息
	 * 
	 * <code>
	 * 	[
	 * 		copyInfo:							副本信息
	 * 		[
	 *          uid => 用户ID
	 *          copy_id => 副本ID
	 * 			raid_times => 玩此副本的次数
	 *          score => 已经获得的副本分数
	 *          prized_num => 已经领取的奖励个数
	 *          va_copy_info => 当前打到的部队ID(progress) /
	 *                          击败部队的ID和次数(defeat_id_times) /
	 *                          击败部队的最好成绩(id_appraisal) /
	 *                          已经达成的奖励ID数组(prize_ids)
	 *      ]
	 * 		rpEnemies:							该副本刷新点信息
	 * 		[
	 * 			refreshPoint => 刷新点ID
	 *          enemyID => 部队ID
	 * 		]
	 * 	]
	 * </code>
	 */
	function getCopyInfo($copyID);

	/**
	 * 获取用户所有副本信息
	 * 
	 * @return array							副本信息
	 * 
	 * <code>
	 * 	[
	 * 		copy_id => 副本ID
	 *      raid_times => 玩此副本的次数
	 *      va_copy_info {
	 *                     progress				当前打到的部队ID
	 *                     defeat_id_times 		击败部队的ID和次数
	 *                     id_appraisal         击败部队的最好成绩
	 *                     prize_ids            已经达成的奖励ID数组
	 *                   }
	 * 	]
	 * </code>
	 */
	function getUserCopies();

	/**
	 * 获取可以进入的所有副本ID
	 */
	function getAllCopiesID();

	/**
	 * 攻击某个部队
	 * @param int $copyID						副本ID
	 * @param int $enemyID						敌人部队ID
	 * @param int $npcTeamID					玩家选择的NPC小队ID
	 * @param array $heroList					英雄ID序列
	 * 
	 * @return 
	 *         hp								血量不足
	 *         cd								CD时刻未到
	 * 
	 * 		   array							战斗评价
	 * 
	 * <code>
	 * 	{
	 *        fightRet							战斗模块返回值
	 *        bloodPackage                      该人物的血包的值
	 *        curHp	 {							英雄战斗后血量
	 *               	id => hp
	 *               }
	 *        cd 								CD 时间
	 *        reward {                      	 战斗后的奖励
	 *               	belly => 游戏币
	 *               	exp => 经验
	 *               	experience => 阅历
	 *               	prestige => 威望
	 * 					arrHero:[{
	 * 						hid => 英雄id
	 * 						htid => 形象id
	 * 						level => 原等级
	 * 						uplevel => 提升等级
	 * 						exp => 当前经验
	 * 						upexp => 获得经验
	 * 						}]
	 *              	equip {
	 *              			bag => bagInfo
	 *                    		heroID => 掉落的英雄ID
	 *                     	  }
	 *                }
	 *       appraisal							战斗评价(数字表示)
	 *       prizeIDs [							副本奖励ID
	 *       ]
	 * 	}
	 * </code>
	 */
	function attack($copyID, $enemyID, $npcTeamID = null, $heroList = null);

	/**
	 * 获取某部队被k的次数 (服务器的总攻击次数，和当日玩家对于这个部队的攻击次数)
	 * 
	 * @param int $enemyID						敌人部队ID
	 * 
	 * @return array							队伍被攻击的次数
	 * 
	 * <code>
	 * 	[
	 * 		userDefeat:int						玩家攻击过的次数
	 * 		serverDefeat:int					服务器攻击过的次数
	 * 	]
	 * </code>
	 */
	function getEnemyDefeatNum($enemyID);

	/**
	 * 获取某部队被攻击的次数
	 * 
	 * @param int $enemyID						部队ID
	 * 
	 * @return int								返回部队攻击过的次数
	 */
	function isEnemyDefeated($enemyID);

	/**
	 * 获取某组部队被攻击的次数
	 * 
	 * @param array $enemyID					部队ID数组
	 * 
	 * @return array							返回部队组攻击过的次数
	 * 
	 * <code>
	 * 	[{
	 * 		部队ID => 对应的被攻击次数
	 * 	}]
	 * </code>
	 */
	function getEnemiesDefeatNum($enemyIDs);

	/**
	 * 返回所有的攻略和战报信息
	 * 
	 * @param int $enemyID						敌人部队ID
	 * 
	 * @return									攻略和战报信息
	 * 
	 * <code>
	 *  array									队伍被攻击的次数
	 * 	[
	 * 		replayList:array					战报数组
	 *          => 用户ID(uid) /
	 *             用户名(uname) /
	 *             用户等级(level) /
	 *             阵营(group_id) /
	 *             战报ID(fight_replay_id)
	 * 		rankList:array						首杀数组
	 *          => 用户ID(uid) /
	 *             用户名(uname) /
	 *             用户等级(level) /
	 *             名次(rank) /
	 *             战报ID(fight_replay_id)
	 * 	]
	 * </code>
	 */
	function getReplayList($enemyID);

	/**
	 * 清除CD时间
	 * 
	 * @return int								清空成功，实际耗费的金币值
	 *         err								清空失败
	 */
	function clearFightCdByGold();

	
	/**
	 * 领取奖励
	 * 
	 * @param int $copyID						副本ID
	 * @param int $caseID						宝箱ID
	 * 
	 * @return array							背包信息
	 *         err:string						背包满了
	 * 
	 * @see IBag::receiveItem
	 */
	function getPrize($copyID, $caseID);

	/**
	 * 开始挂机
	 * 
	 * @param int $copyID						副本ID
	 * @param int $enemyID						部队ID
	 * @param int $times						次数
	 */
	function startAutoAtk($copyID, $enemyID, $times);

	/**
	 * 取消挂机
	 * 
	 * @throws Exception
	 * 
	 * @return 
	 * 请参照 <attackOnce>
	 */
	function cancelAutoAtk();

	/**
	 * 进行一次自动攻击操作
	 * 
	 * @throws Exception
	 * 
	 * @return 							本次挂机的结果
	 * 
	 * <code>
	 * 'ok' : string					挂机结束
	 * </code>
	 * <code>
	 * 'err' : string					还没到请求间隔时间呢 —— 别着急哈
	 * </code>
	 * <code>
	 * array							挂机返回的好处
	 * 	[
	 * 		items {[
	 * 					item:			[全部掉落的itemID]
	 * 					bag：			@see IBag::receiveItem
	 *             ]}
	 *      last_atk_time				最近一次挂机攻打的时刻
	 *      once_times                  本次攻打，后台计算的真实数据中一共攻打了几次
	 * 		add_exp：					一次挂机掉落的经验
	 * 		experience:					一次挂机掉落的阅历
	 * 		exp:						英雄当前经验
	 * 		lv: 						英雄当前等级
	 *      va_auto_atk_info  [
	 * 					belly:			挂机掉落的游戏币
	 * 					exp：			挂机掉落的经验
	 * 					experience:		挂机掉落的阅历
	 * 					items：			挂机掉落的所有物品
	 *            ]
	 * 	]
	 * </code>
	 */
	function attackOnce($isLogin = false);

	/**
	 * 使用金币立刻进行一次挂机
	 * 
	 * @return 
	 * 请参照 <attackOnce>
	 */
	function attackOnceByGold();

	/**
	 * 登陆时检查并处理挂机
	 * 
	 * @return 							登陆期间的挂机结果
	 *
	 * <code>
	 * array							用户挂机信息
	 * 	[
	 *      copy_id						副本ID
	 *      army_id                     部队ID
	 *      start_time                  开始时刻
	 *      times                     	准备挂机的次数
	 *      annihilate                 	已经挂机的次数
	 *      last_atk_time             	上次攻击时刻
	 *      va_auto_atk_info  [
	 * 					belly:			挂机掉落的游戏币
	 * 					exp：			挂机掉落的经验
	 * 					experience:		挂机掉落的阅历
	 * 					items：			挂机掉落的所有物品
	 *            ]
	 * 	]
	 * </code>
	 */
	function checkWhenLogin();

	/**
	 * 获取用户挂机信息
	 * 
	 * @return							用户挂机信息 
	 * 
	 * <code>
	 * array							用户挂机信息
	 * 	[
	 * 		uid 						用户ID
	 *      copy_id						副本ID
	 *      army_id                     部队ID
	 *      start_time                  开始时刻
	 *      times                     	准备挂机的次数
	 *      annihilate                 	已经挂机的次数
	 *      last_atk_time             	上次攻击时刻
	 *      va_auto_atk_info  [
	 * 					belly:			挂机掉落的游戏币
	 * 					exp：			挂机掉落的经验
	 * 					experience:		挂机掉落的阅历
	 * 					items：			挂机掉落的所有物品
	 *            ]
	 * 	]
	 * </code>
	 */
	function getAutoAtkInfo();

	/**
	 * 使用金币，结束全部挂机
	 * 
	 * @return							挂机结果
	 * 
	 * <code>
	 * 'ok' : string					挂机结束
	 * </code>
	 * <code>
	 * array							挂机返回的好处
	 * 	[
	 * 		items {
	 * 					item:			[全部掉落的itemID]
	 * 					bag：			@see IBag::receiveItem
	 *             }]
	 *      last_atk_time				最近一次挂机攻打的时刻
	 *      once_times                  本次攻打，后台计算的真实数据中一共攻打了几次
	 *      va_auto_atk_info  [
	 * 					belly:			挂机掉落的游戏币
	 * 					exp：			挂机掉落的经验
	 * 					experience:		挂机掉落的阅历
	 * 					items：			挂机掉落的所有物品
	 *            ]
	 * 	]
	 * </code>
	 */
	function endAttackByGold();

	/**
	 * 组队攻击
	 * 
	 * @param array $teamList					小队信息 (玩家组队uid的数组)
	 * @param int $enemyID						敌人部队ID
	 * 
	 * @return array							战斗评价
	 * 
	 * <code>
	 * 	{
	 *        ... 其他战斗模块的返回值
	 *        reward {[                      	 战斗后的奖励
	 * 				arrHero:[{
	 * 					hid:英雄id
	 * 					htid:形象id
	 * 					initial_level:等级初量
	 * 					current_level:等级终量
	 * 					current_exp:经验终量
	 * 					add_exp:经验增量
	 * 				}]
	 * 				prestige:获得的威望
	 * 				exp:经验
	 * 				expericne:阅历
	 * 				belly:游戏币
	 * 				curHp:[ 英雄的当前血量
	 * 					id => hp
	 * 				]
	 * 				bloodPackage：剩余血包数
	 * 				equip:{
	 * 					item:[全部掉落的itemID]
	 * 					bag：{背包信息}
	 * 				}
	 * 				]}
	 * 	}
	 * </code>
	 */
	function startAutoAttack($teamList, $enemyID);

	/**
	 * 返回用户剩余的普通军团怪攻打次数
	 * 
	 * @return int								用户剩余的攻打次数 (普通怪的时候)
	 */
	function getCommonGroupArmyDefeatNum();

	/**
	 * 返回用户剩余的活动军团怪攻打次数
	 * 
	 * @return array 军团ID => 次数s				活动怪的时候
	 */
	function getActivityGroupArmyDefeatNum();

	/**
	 * 创建队伍
	 * 
	 * @param int $enemyID						部队ID
	 * @param bool $isAutoStart					是否自动开战
	 * @param int $joinLimit					组队限制 （公会还是阵营）
	 * 
	 * @return int								当前血包数量
	 *         hp								血量不足
	 *         err								创建失败
	 */
	function createTeam($enemyID, $isAutoStart, $joinLimit);

	/**
	 * 加入队伍
	 * 
	 * @param int $enemyID						部队ID
	 * @param int $teamId						创建好的小队ID
	 * 
	 * @return int								当前血包数量
	 *         hp								血量不足
	 *         err								加入失败
	 */
	function joinTeam($enemyID, $teamId);

	/**
	 * 获取用户配置好的机器人列表
	 * 
	 * @return array							已经配置好的uid数组 (包括详细信息, 以uid为key)
	 */
	function getInviteSetting();

	/**
	 * 设置用户配置好的机器人列表
	 * 
	 * @return ok								保存设置成功
	 *         err								保存设置失败
	 */
	function saveInviteSetting($list);

	/**
	 * 暂无
	 * @param int $enemyID						敌人部队ID
	 */
	function navalAttack($enemyID);
	
	function getCommonGroupBattleInviteSetting();
	
	function saveCommonGroupBattleInviteSetting($friends);
	
	function startCommonGroupBattleAutoAttack($teamList, $enemyID);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */