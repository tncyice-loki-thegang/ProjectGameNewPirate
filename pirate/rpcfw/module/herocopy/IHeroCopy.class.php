<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IHeroCopy.class.php 26659 2012-09-05 02:23:47Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/herocopy/IHeroCopy.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-05 10:23:47 +0800 (三, 2012-09-05) $
 * @version $Revision: 26659 $
 * @brief 
 *  
 **/

interface IHeroCopy
{
	/**
	 * 获取用户英雄副本信息
	 * 
	 * @return array							英雄副本信息
	 * 
	 * <code>
	 * 	[
	 *      uid => 用户ID
	 *      copy_id => 副本ID	
	 * 		is_over => 是否通关
	 *      coins => 失败次数
	 *      buy_coin_times => 购买失败次数
	 *      va_copy_info => 当前打到的部队ID(progress)/击败部队的ID和次数(defeat_id_times)
	 * 	]
	 * </code>
	 */
	function getHeroCopyInfo();

	/**
	 * 根据副本ID，获取用户英雄副本信息
	 * 
	 * @return array							英雄副本信息
	 * 
	 * <code>
	 * 	[
	 *      uid => 用户ID
	 *      copy_id => 副本ID	
	 * 		is_over => 是否通关
	 *      coins => 失败次数
	 *      buy_coin_times => 购买失败次数
	 *      va_copy_info => 当前打到的部队ID(progress)/击败部队的ID和次数(defeat_id_times)
	 * 	]
	 * </code>
	 */
	function getHeroCopyInfoByID($copyID);

	/**
	 * 获取用户英雄副本ID
	 * 
	 * @return array							英雄副本ID
	 */
	function getAllCopiesID();

	/**
	 * 进入英雄副本
	 * 
	 * @param int $copyID						副本ID
	 * 
	 * @return string:ok						进入副本成功
	 * 		   string:err						进入副本失败
	 */
	function enterHeroCopy($copyID);

	/**
	 * 离开英雄副本
	 * 
	 * @return string:ok						离开副本成功
	 */
	function leaveHeroCopy();

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
	 * 			}
	 * 			</code>
	 */
	function attack($enemyID);

	/**
	 * 购买币子，啊不，失败次数
	 * 
	 * @param int $copyID						副本ID
	 * 
	 * @return int								实际消耗的金币数
	 * 		   string:err						购买失败
	 */
	function byCoin($copyID);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */