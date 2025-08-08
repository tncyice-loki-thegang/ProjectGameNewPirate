<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Festival.def.php 31613 2012-11-22 07:01:26Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Festival.def.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-11-22 15:01:26 +0800 (四, 2012-11-22) $
 * @version $Revision: 31613 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : FestivalDef
 * Description : 节日活动数据常量�?
 * Inherit     : 
 **********************************************************************************************************************/
class FestivalDef
{
	const DEF_OVERRIDE = 1;								// 基准倍率	
	const FESTIVAL_CARD_NUM = 5;						// 牌的个数	
	
	const FESTIVAL_ID = 'festival_id';					// 节日活动配置表ID
	const FESTIVAL_BEGIN_DATA = 'festival_begin_date';	// 节日活动开始时间
	const FESTIVAL_END_DATA = 'festival_end_date';		// 节日活动结束时间
	const FESTIVAL_SAIL_REWARD = 'festival_sail_reward';// 活动期出航加成
	const FESTIVAL_FOOD_REWARD = 'festival_food_reward';// 活动期菜肴卖加成
	const FESTIVAL_COPY_REWARD = 'festival_copy_reward';// 活动期副本战斗阅历加成
	const FESTIVAL_BATTLE_REWARD = 'festival_battle_reward';	// 活动期战斗经验加成
	const FESTIVAL_RESOURCE_REWARD = 'festival_resource_reward';// 活动期港口资源矿收入加成
	const FESTIVAL_TRAIN_REWARD = 'festival_train_reward';		// 活动期伙伴训练经验加成
	const FESTIVAL_RAPID_REWARD = 'festival_rapid_reward';		// 活动期伙伴突飞经验加成
	const FESTIVAL_POINT_MAX = 'festival_point_max';			// 活动期间每日活动积分上限
	const FESTIVAL_FLOPCARD_POINT = 'festival_flopcard_point';	// 翻牌需要积分

	const FESTIVAL_PIC = 'festival_pic';						// 活动图片
	const FESTIVAL_INTRODUCE1 = 'festival_introduce1';			// 活动收益描述
	const FESTIVAL_INTRODUCE2 = 'festival_introduce2';			// 活动介绍
	const FESTIVAL_PRACTICE = 'festival_practice';				// 历练经验加成
	const FESTIVAL_TREASURE_PURPLESTAR = 'festival_treasure_purplestar';// 寻宝紫星加成
	const FESTIVAL_MAKEITEM_PURPLESTAR = 'festival_makeitem_purplestar';// 装备制作紫星加成
	const FESTIVAL_FLOPCARD_ONOFF = 'festival_flopcard_onoff';	// 活动翻牌是否开启
	const FESTIVAL_TREASURE_REDSTAR = 'festival_treasure_redstar';// 寻宝红星加成
	const FESTIVAL_MAKEITEM_REDSTAR = 'festival_makeitem_redstar';// 装备制作红星加成
	const FESTIVAL_EXCHANGEITEMS = 'festival_exchangeitems'; 	  // 积分兑换活动物品积分组

	// 节日活动翻牌 
	const FESTIVAL_CARD_ID = 'festival_card_id';		// 牌ID
	const FESTIVAL_CARD_NAME = 'festival_card_name';	// 奖励模板名称
	const FESTIVAL_CARD_BELLY = 'festival_card_belly';	// 奖励贝里基础值
	const FESTIVAL_CARD_EXPE = 'festival_card_expe';	// 奖励阅历基础值
	const FESTIVAL_CARD_GOLD = 'festival_card_gold';	// 奖励金币
	const FESTIVAL_CARD_EXEC = 'festival_card_exec';	// 奖励行动力
	const FESTIVAL_CARD_PRES = 'festival_card_pres';	// 奖励声望
	const FESTIVAL_CARD_ITEM = 'festival_card_item';	// 奖励掉落表ID组
	const FESTIVAL_CARD_WEIGHT = 'festival_card_weight';// 奖励掉落权重
	
	const FESTIVAL_SONCARD = 'festival_soncard';		// 子牌

	const FESTIVAL_TYPE_SAIL = 1;  	                    // 活动期出航加成
	const FESTIVAL_TYPE_KITCHEN = 2;					// 活动期菜肴卖加成
	const FESTIVAL_TYPE_COPY = 3;						// 活动期副本战斗阅历加成
	const FESTIVAL_TYPE_BATTLE = 4;						// 活动期战斗经验加成
	const FESTIVAL_TYPE_RESOURCE = 5;					// 活动期港口资源矿收入加成
	const FESTIVAL_TYPE_TRAIN = 6;						// 活动期伙伴训练经验加成
	const FESTIVAL_TYPE_RAPID = 7;						// 活动期伙伴突飞经验加成
	const FESTIVAL_TYPE_PRACTICE = 8;					// 历练经验加成
	const FESTIVAL_TYPE_TREASURE_PURPLESTAR = 9;		// 寻宝紫星加成
	const FESTIVAL_TYPE_MAKEITEM_PURPLESTAR = 10;		// 装备制作紫星加成
	const FESTIVAL_TYPE_TREASURE_REDSTAR = 11;			// 寻宝红星加成
	const FESTIVAL_TYPE_MAKEITEM_REDSTAR = 12;			// 装备制作红星加成
	
	// 节日商城积分
	const FESTIVAL_SERVER_OPENDATE = 'festival_server_opendate';// 活动开启需要开服时间
	
	const FESTIVAL_EXPOINT_ID = 'festival_expoint_id';	// 节日积分兑换ID
	const FESTIVAL_EXPOINT_NAME = 'festival_expoint_name';		// 节日积分兑换名称
	const FESTIVAL_EXPOINT_POINT = 'festival_expoint_point';	// 节日积分
	const FESTIVAL_EXPOINT_BASEGOLD = 'festival_expoint_basegold';// 金币基础值
	
	const FESTIVAL_EXPOINT_GOLD = 1;					// 金币
	const FESTIVAL_EXPOINT_SAIL = 2;					// 出航
	const FESTIVAL_EXPOINT_COOK = 3;					// 厨房生产
	const FESTIVAL_EXPOINT_ORDER = 4;					// 订单
	const FESTIVAL_EXPOINT_DAY_TASK = 5;				// 每日任务
	const FESTIVAL_EXPOINT_SALARY = 6;					// 领取悬赏工资
	const FESTIVAL_EXPOINT_SLAVE = 7;					// 调教下属
	const FESTIVAL_EXPOINT_REINFORCE = 8;				// 装备强化
	const FESTIVAL_EXPOINT_ELITE_COPY = 9;				// 挑战精英副本
	const FESTIVAL_EXPOINT_EXPLOR = 10;					// 探索宝石
	const FESTIVAL_EXPOINT_ARENA = 11;					// 竞技场战斗
	const FESTIVAL_EXPOINT_ROB = 12;					// 寻宝打劫
	const FESTIVAL_EXPOINT_PORT_ATK = 13;				// 港口攻打
	const FESTIVAL_EXPOINT_DONATE = 14;					// 工会捐献
	const FESTIVAL_EXPOINT_RESOURCE = 15;				// 占领资源
	const FESTIVAL_EXPOINT_TALKS = 16;					// 会谈
	const FESTIVAL_EXPOINT_TREASURE = 17;				// 寻宝
	const FESTIVAL_EXPOINT_SMELTING = 18;				// 装备制作
	const FESTIVAL_EXPOINT_RAPID = 19;					// 突飞伙伴
	const FESTIVAL_EXPOINT_GOOD_WILL = 20;				// 金币赠送
	const FESTIVAL_EXPOINT_GOOD_SOUL = 21;				// 免费金币聚魂（隐藏）
	const FESTIVAL_EXPOINT_GOOD_ASTRO = 22;				// 领取星盘祝福
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
