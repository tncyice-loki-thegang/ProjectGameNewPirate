<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Active.def.php 32947 2012-12-12 07:49:07Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Active.def.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-12 15:49:07 +0800 (三, 2012-12-12) $
 * @version $Revision: 32947 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : ActiveDef
 * Description : 活跃度数据常量类
 * Inherit     : 
 **********************************************************************************************************************/
class ActiveDef
{
	/**
	 * 宝箱
	 */
	public static $CASE_INDEX = array(
		1,
		2,
		4,
		8
	);

	const SAIL = 2;								// 出航
	const COOK = 3;								// 厨房生产
	const ORDER = 4;							// 订单
	const DAY_TASK = 5;							// 每日任务
	const SALARY = 6;							// 领取悬赏工资
	const SLAVE = 7;							// 调教下属
	const REINFORCE = 8;						// 装备强化
	const ELITE_COPY = 9;						// 挑战精英副本
	const EXPLOR = 10;							// 探索宝石
	const ARENA = 11;							// 竞技场战斗
	const ROB = 12;								// 寻宝打劫
	const PORT_ATK = 13;						// 港口攻打
	const DONATE = 14;							// 工会捐献
	const RESOURCE = 15;						// 占领资源
	const TALKS = 16;							// 会谈
	const TREASURE = 17;						// 寻宝
	const SMELTING = 18;						// 装备制作
	const RAPID = 19;							// 突飞伙伴
	const GOOD_WILL = 20;						// 好感度赠送礼物
	const ASTRO_EXP = 22;						// 星盘祝福
	const SOUL_GOLD = 23;						// 金币聚魂
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */