<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IEliteCopy.class.php 21674 2012-05-30 08:11:20Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elitecopy/IEliteCopy.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-05-30 16:11:20 +0800 (三, 2012-05-30) $
 * @version $Revision: 21674 $
 * @brief 
 *  
 **/

interface IEliteCopy
{
	/**
	 * 获取用户精英副本信息
	 * 
	 * @return array							精英副本信息
	 * 
	 * <code>
	 * 	[
	 *      uid => 用户ID
	 *      challenge_time => 挑战时刻
	 * 		challenge_times => 挑战次数
	 *      max_coin => 最大挑战次数
	 *      progress => 打到的精英副本ID(可以攻打的最远的精英副本ID)
	 *      va_copy_info => 英副本信息 => 包括 精英副本ID (copy_id)， 是否已经通关 (is_end)， 进度 (enemy_id)
	 * 	]
	 * </code>
	 */
	function getEliteCopyInfo();

	/**
	 * 进入精英副本
	 * 
	 * @param int $copyID						副本ID
	 * 
	 * @return string:ok						进入副本成功
	 * 		   string:err						进入副本失败
	 */
	function enterEliteCopy($copyID);

	/**
	 * 离开精英副本
	 * 
	 * @param int $copyID						副本ID
	 * 
	 * @return string:ok						离开副本成功
	 */
	function leaveEliteCopy();
	
	/**
	 * 有多少副本可以重来
	 * 
	 * @param int $copyID						副本ID
	 * 
	 * @return string:ok						重置副本成功
	 * 		   string:err						重置副本失败
	 */
	function restartEliteCopy($copyID);

	/**
	 * 获取最近通关副本的人物
	 * 
	 * @param int $copyID						副本ID
	 * 
	 * <code>
	 * 	[
	 *      uid => 用户ID
	 *      uname => 用户名
	 * 		group => 阵营名
	 * 	]
	 * </code>
	 */
	function getPassUsers($copyID);

	/**
	 * 攻击
	 * 
	 * @param int $enemyID						部队ID
	 * 
	 * @return 
	 *         cd								CD时刻未到
	 * 
	 * 		   array							战斗评价
	 * 			<code>
	 * 			{
	 *        			fightRet				战斗模块返回值
	 *                  cd						CD 时间
	 *                  appraisal				战斗评价
	 *        			reward {                战斗后的奖励
	 *             			bag => bagInfo
	 *             			itemIDs => 掉落物品信息
	 *             		}
	 * 			}
	 * 			</code>
	 */
	function attack($enemyID);

	/**
	 * 使用金币通关副本
	 * 
	 * @param int $copyID						副本ID
	 * 
	 * @return array							副本奖励
	 * 			<code>
	 * 			{
	 *             	bag => bagInfo
	 *             	itemIDs => 掉落物品信息
	 * 			}
	 * 			</code>
	 */
	function passByGold($copyID);

	/**
	 * 购买币子，啊不，失败次数
	 * 
	 * @return int								实际消耗的金币数
	 * 		   string:err						购买失败
	 */
	function byCoin();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */