<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IActive.class.php 32950 2012-12-12 07:55:11Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/active/IActive.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-12 15:55:11 +0800 (三, 2012-12-12) $
 * @version $Revision: 32950 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : IActive
 * Description : 活跃度接口声明
 * Inherit     :
 **********************************************************************************************************************/
interface IActive
{
	/**
	 * 获取活跃度信息
	 * 
	 * @return array							活跃度信息
	 * 
	 * <code>
	 * 	[
	 * 		uid:								用户ID
	 * 		point:								活跃度得分
	 *      sail_times:							出航次数
	 *      cook_times:							厨房生产次数
	 *      copy_atk_times:						副本战斗次数
	 *      elite_atk_times:					精英副本战斗次数
	 *      conquer_times:						港口征服次数
	 *      port_atk_times:						港口攻打次数
	 *      arena_times:						竞技场攻打次数
	 *      play_slave_times:					调教下属次数
	 *      order_times:						订单次数
	 *      hero_rapid_times:					伙伴突飞次数
	 *      day_task_times:						每日任务次数
	 *      fetch_salary:						领取工资
	 *      reinforce_times:					装备强化次数
	 *      explore_times:						宝石探索次数
	 *      treasure_times:						寻宝次数
	 *      smelting_times:						装备制作次数
	 *      talks_times:						会谈次数
	 *      resource_times:						占领资源次数
	 *      donate_times:						捐献次数
	 *      rob_times:							打劫次数
	 *      prized_num:							已经领取的奖励个数
	 *      update_time:						上次表更新的时刻
	 *      va_copy_info : {
	 * 			astro_exp_times:				星盘次数
	 * 			gold_soul_times:                金币聚魂次数
	 * 		}
	 * 	]
	 * </code>
	 */
	function getActiveInfo();

	/**
	 * 领取奖励
	 * 
	 * @param $prizeID:integer					箱子ID，从0开始计数
	 * 
	 * @return bagInfo：array					领取成功，返回背包信息
	 * 		   err：string						各种原因，领取失败
	 */
	function fetchPrize($prizeID);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */