<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IAchievements.class.php 21366 2012-05-25 09:26:33Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/IAchievements.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-05-25 17:26:33 +0800 (五, 2012-05-25) $
 * @version $Revision: 21366 $
 * @brief 
 *  
 **/
interface IAchievements
{
	/**
	 * 获取成就点数
	 * 
	 * @return int								用户当前成就点数
	 */
	function getAchievementPoints();

	/**
	 * 返回各个类型的成就点数
	 * 
	 * @return array 							已经获取的成就点数列表
	 * 
	 * <code>
	 * 	[
	 * 		成就类型:成就点数
	 * 	]
	 * </code>
	 */
	function getAchievementsPointsByType();

	/**
	 * 获取展列的成就
	 * 
	 * @return 参照 getLatestAchievements
	 */
	function getShowAchievements();

	/**
	 * 获取最近得到的成就
	 *
	 * @param int $num							获取的个数
	 * 
	 * @return array							成就信息
	 * 
	 * <code>
	 * 	[
	 * 		achieve_id:							成就ID
	 * 		is_show:							是否正在展示
	 * 		is_get:								是否已经达成
	 * 		get_time:							获取时刻
	 * 		va_a_info:							尚未达成时，记录已完成的达成条件
	 * 	]
	 * </code>
	 */
	function getLatestAchievements($num);

	/**
	 * 获取持续在线时间类成就
	 * 
	 * @return array							获取的成就ID组
	 *         int 								还没到获取成就的时间，仍需要这么长时间才可以获取到成就
	 */
	function getLastOnlineAchievements();

	/**
	 * 获取连续在线时间类成就
	 * 
	 * @return array							获取的成就ID组
	 *         int 								还没到获取成就的时间，仍需要这么长时间才可以获取到成就
	 */
	function getKeepOnlineAchievements();

	/**
	 * 获取一组成就信息
	 * 
	 * @param array $achieveIDs					成就ID数组
	 * 
	 * @return 参照 getLatestAchievements
	 */
	function getAchievementsByIDs($achieveIDs);

	/**
	 * 获取展示中的称号
	 * 
	 * @return array							称号信息
	 * 
	 * <code>
	 * 	[
	 * 		title_id:							称号ID
	 * 		is_show:							是否正在展示
	 * 		get_time:							获取时刻
	 * 	]
	 * </code>
	 */
	function getShowName();

	/**
	 * 获取所有称号列表
	 * 
	 * @return 参照 getShowName
	 */
	function getNameList();

	/**
	 * 删除正在展示的成就
	 * 
	 * @param int $achieveID					成就ID
	 * 
	 * @return ok：string
	 */
	function delShowAchievements($achieveID);

	/**
	 * 展示成就
	 * 
	 * @param int $achieveID					成就ID
	 * 
	 * @return ok：string
	 */
	function setShowAchievements($achieveID);

	/**
	 * 删除正在展示的称号
	 * 
	 * @return ok：string
	 */
	function delShowName();

	/**
	 * 展示称号
	 * 
	 * @param int $titleID						称号ID
	 * 
	 * @return ok：string
	 */
	function setShowName($titleID);
	
	/**
	 * 领取工资
	 * 
	 * @return ok：string						领取成功
	 * 		   err：string						各种原因，领取失败
	 */
	function fetchSalary();

	/**
	 * 执行当前用户的成就检测操作
	 */
	function excuteNotify($uid, $type, $value_1, $value_2 = 1);

	/**
	 * 获取奖励状态
	 * 
	 * @return array							奖励状态
	 * 
	 * <code>
	 *  pirze_id => 
	 * 	[
	 * 		id:									就是奖励ID了
	 * 		num:								已经获取走的个数
	 * 		get:								是否可以获取
	 * 	]
	 * </code>
	 * 
	 * @return err:string						不在活动中
	 */
	function getPrizeStatus();

	/**
	 * 领取奖励
	 * 
	 * @return bagInfo：array					领取成功，返回背包信息
	 * 		   err：string						各种原因，领取失败
	 */
	function fetchPrize($prizeID);
	
	function setUserShowTitleID($titleID);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */