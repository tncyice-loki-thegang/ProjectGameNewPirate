<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IWorldwar.class.php 35991 2013-01-15 09:20:09Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldwar/IWorldwar.class.php $
 * @author $Author: YangLiu $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-15 17:20:09 +0800 (二, 2013-01-15) $
 * @version $Revision: 35991 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : IWorldwar
 * Description : 跨服争霸战
 * Inherit     :
 **********************************************************************************************************************/
interface IWorldwar
{

	/**
	 * 进入跨服赛晋级赛界面
	 */
	function enterWorldWar();


	/**
	 * 退出跨服赛晋级赛界面
	 * 
	 */
	function leaveWorldWar();


	/**
	 * 更新用户阵型信息
	 * 
	 * 
	 * @return ok								更新成功
	 *         err								更新失败
	 */
	function updateFormation();


	/**
	 * 使用金币清除更新CD时间 —— 只有在晋级赛可以秒CD
	 * 
	 * @return int								实际花费金币
	 * @return err:string						清除CD时间失败
	 */
	function clearUpdFmtCdByGold();


	/**
	 * 获取用户的跨服战信息
	 * 
	 * 
	 * @return array							擂台赛信息
	 * 
	 * <code>
	 * 		session: 							当前是第几届
	 * 		win_team_lose_times:				海选时候胜者组失败的次数
	 * 		lose_team_lose_times:				海选时候败者组失败的次数
	 * 		team:								组别, 初始为0, 胜者组为1, 负者组为 2, 都失败为9
	 * 		cheer_uid:							助威对象
	 * 		cheer_uid_server_id:				助威对象的所在服务器ID
	 * 		cheer_time:							助威时刻
	 * 		worship_times:						膜拜次数
	 * 		worship_time:						膜拜时刻
	 * 		update_fmt_time:					更新战斗力时刻
	 * 		sign_time:							报名时间
	 * 		sign_session:						报名参与第几届跨服赛
	 * 		group_pirze_id:						服内赛奖励ID
	 * 		group_pirze_time:					领取服内赛奖励时刻
	 * 		world_pirze_id:						跨服赛赛奖励ID
	 * 		world_pirze_time:					领取跨服赛奖励时刻
	 * </code>
	 */
	function getUserWorldWarInfo();


	/**
	 * 获取跨服战信息
	 * 
	 * @return array							跨服战信息
	 * 
	 * <code>
	 * 'round' => 								当前第几回合
	 * 'info' => 
	 * 	[
	 * 		server_name:						用户所在服务器名
	 *      uid:								用户ID
	 *      uname:								用户名称
	 * 		level								用户等级
	 *      htid:								主英雄ID
	 *      final_rank:							比赛结果名次
	 *      cheer_count:						支持人数
	 *      index:								所在位置
	 * 	]
	 * 'replay' => 								战报信息
	 * 	[
	 * 		replay:								战报ID
	 *      winer:								胜者
	 * 		loser:								败者
	 *      offensive:							先手ID
	 * 	]
	 * </code>
	 */
	function getWorldWarInfo();


	/**
	 * 报名
	 * 
	 * @return ok:sting							报名成功
	 * @return err:string						报名失败
	 */
	function signUp();


	/**
	 * 获取膜拜神殿信息
	 * 
	 * @return array
	 * {
	 * 		[
	 * 			rank:							名次
	 * 			server_name:					用户所在服务器名
	 *			uid:							用户ID
	 *			uname:							用户名称
	 *			htid:							主英雄模板ID
	 *			msg:							冠军留言
	 * 		]
	 * }
	 */
	function getTempleInfo();


	/**
	 * 膜拜
	 * 
	 * @param $type      						膜拜种类
	 * 
	 * @return array							膜拜成功,返回背包信息
	 *         err:string						膜拜失败
	 */
	function worship($type);


	/**
	 * 获取最近膜拜的人物
	 * 
	 * @return <code>
	 * 	[
	 *      uid									用户ID
	 *      uname								用户名
	 *		lv 									用户等级
	 * 		type								膜拜类型
	 * 	]
	 * </code>
	 */
	function getWorshipUsers();


	/**
	 * 留言
	 * 
	 * @param $msg      						留言
	 * 
	 * @return ok:sting							留言成功
	 *         err:string						留言失败
	 */
	function leaveMsg($msg);


	/**
	 * 获取所有助威的历史数据
	 * 
	 * @return array							自己的信息(非参赛者返回空)
	 * <code>
	 * [
	 * 		round =>[ 							第几轮
	 * 			type:							胜者组为1, 负者组为 2
	 * 			uname:							玩家名字
	 *			uid:							玩家ID
	 * 		]
	 * ]
	 * </code>
	 */
	function getHistoryCheerInfo();


	/**
	 * 查看战绩(获取自己跨服或者海选的所有战斗信息)
	 * 
	 * @return array                        	战绩
	 * <code>
	 * replay_id => 
	 * [
	 * 		   round:							第几回合
	 *         uid:								用户ID
	 *         uname:							用户名称
	 *         htid:							主英雄ID
	 *         score:							比分
	 *         replay:							战报id
	 *         resule:							战斗结果 win:胜利 lose:失败
	 *         team:							伟大航路 (1)  新世界(2)
	 * ]
	 * <code>
	 */
	function getHistoryFightInfo();


	/**
	 * 助威
	 * 
	 * @param $objUid:int						助威对象的ID
	 * @param $objUname:string					助威对象的名字
	 * @param $type:int							胜者组为1, 负者组为 2
	 * @param $serverId:int						助威对象的服务器ID (服内助威的时候传0即可)
	 * 
	 * @return ok								助威成功
	 *         err								助威失败
	 */
	function cheer($objUid, $objUname, $type, $serverId);


	/**
	 * 获取擂台赛奖励
	 *
	 * @param int $prizeID						用户选好的奖励ID
	 * 
	 * @return ok								领取成功
	 *         err								领取失败
	 */
	function getPrize($prizeID);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */