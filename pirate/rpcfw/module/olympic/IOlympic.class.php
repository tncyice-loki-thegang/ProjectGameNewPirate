<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IOlympic.class.php 26911 2012-09-10 08:48:27Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/olympic/IOlympic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-10 16:48:27 +0800 (一, 2012-09-10) $
 * @version $Revision: 26911 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : IOlympic
 * Description : 擂台赛接口声明
 * Inherit     :
 **********************************************************************************************************************/
interface IOlympic
{
	/**
	 * 进入擂台赛场地
	 * 
	 */
	function enterArena();

	/**
	 * 退出擂台赛场地
	 * 
	 */
	function levelArena();

	/**
	 * 报名参赛 
	 * 
	 * 1 : 西海
	 * 2 : 南海
	 * 3 : 北海
	 * 4 : 中立
	 * 
	 * @param $groupID:int						阵营
	 * @param $index:int						位置
	 * 
	 * 
	 * @return err:string						不能报名 (报名时间已过？)
	 * 		   lock:string						不能攻击，稍后再来
	 * 		   full:string						位置已经被占用
	 *         其他场合:
	 * <code>
	 * 	[
	 *      winer =>
	 *      [ 
	 *        uid:								胜者的用户ID
	 *        uname:							胜者的用户名称
	 *        htid:								胜者的主英雄ID
	 *      ]
	 *      index =>
	 *      [ 
	 *        index:							位置
	 *        groupID:							阵营
	 *      ]
	 * 	]
	 * </code>
	 *         
	 */
	function signUp($groupID, $index);

	/**
	 * 挑战     (胜利的时候同阵营推送消息，失败的时候不推送)
	 * 
	 * 1 : 西海
	 * 2 : 南海
	 * 3 : 北海
	 * 4 : 中立
	 * 
	 * @param $groupID:int						阵营
	 * @param $index:int						位置
	 * 
	 * 
	 * @return err:string						不能报名 (报名时间已过？)
	 * 		   lock:string						不能攻击，稍后再来
	 *         其他场合:
	 * <code>
	 * 	[
	 *      winer =>
	 *      [ 
	 *        uid:								胜者的用户ID
	 *        uname:							胜者的用户名称
	 *        htid:								胜者的主英雄ID
	 *      ]
	 *      loser =>
	 *      [ 
	 *        uid:								负者的用户ID
	 *        uname:							负者的用户名称
	 *        htid:								负者的主英雄ID
	 *      ]
	 *      replay:								战报ID
	 *      offensive:							先手的UID
	 *      index =>
	 *      [ 
	 *        index:							位置
	 *        groupID:							阵营
	 *      ]
	 * 	]
	 * </code>
	 *         
	 */
	function challenge($groupID, $index);

	/**
	 * 获取报名情况 （包含中立信息）
	 * 
	 * 1 : 西海
	 * 2 : 南海
	 * 3 : 北海
	 * 4 : 中立
	 * 
	 * 
	 * @return array							擂台赛信息
	 * 
	 * <code>
	 * 'now' => 								当前处于的阶段
	 * 											没到比赛时刻：1
	 *											报名时刻 : 2
	 *											16/1 决赛: 3
	 *											8/1 决赛: 4
	 *											4/1 决赛: 5
	 *											半决赛: 6
	 *											 决赛: 7
	 * 'info' => 
	 * 	[
	 * 		sign_up_index:						报名位置
	 *      final_rank:							比赛结果名次
	 * 		group_id:							阵营ID
	 *      uid:								用户ID
	 *      uname:								用户名称
	 *      htid:								主英雄ID
	 * 	]
	 * 'cd' => 									战斗CD
	 * 'replay' => 								战报信息
	 * 	[
	 * 		replay:								战报ID
	 *      winer:								胜者
	 * 		loser:								败者
	 *      offensive:							先手ID
	 * 	]
	 * </code>
	 */
	function getFightInfo();

	/**
	 * 清除CD时间
	 * 
	 * @return int								清空成功，实际耗费的金币值
	 *         err								清空失败
	 */
	function clearCdByGold();

	/**
	 * 助威
	 * 
	 * @param $objUid:int						助威对象的ID
	 * 
	 * @return ok								助威成功
	 *         err								助威失败
	 */
	function cheer($objUid);

	/**
	 * 获取用户擂台赛信息
	 * 
	 * <code>
	 * 	[
	 *    uid:									用户ID
	 *    cd_time:								CD时间
	 *    integral:								积分
	 *    integral_time:						最近的积分改变时刻
	 *    cheer_times:							助威次数
	 *    cheer_uid:							助威对象
	 *    cheer_time:							助威时刻
	 *    va_olympic:							备用
	 * 	]
	 * </code>
	 */
	function getUserOlympicInfo();

	/**
	 * 获取当前用户排行
	 * 
	 * @return int								当前排名信息
	 */
	function getSelfOrder();

	/**
	 * 获取积分排行信息
	 * 
	 * @return $start							积分开始
	 * @return $offset							积分偏移
	 * 
	 * <code>
	 * 	[
	 *    uid:									用户ID
	 *    integral:								积分
	 *    integral_time:						最近的积分改变时刻
	 *    guild_name:							公会名称
	 *    level:								等级
	 *    uname:								用户名
	 *    utid:									用户模板ID
	 *    guild_id:								公会ID
	 *    group_id:								阵营ID
	 * 	]
	 * </code>
	 */
	function getTop($start, $offset);

	/**
	 * 获取所有八强进入者的被助威次数
	 * 
	 * @return array							被助威次数
	 * 
	 * <code>
	 * {
	 * 'uid' => 次数
	 * }
	 * </code>
	 */
	function getAllCheerObj();

	/**
	 * 获取奖池数据
	 * 
	 * @return int								返回当前奖池数据
	 */
	function getJackPot();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */