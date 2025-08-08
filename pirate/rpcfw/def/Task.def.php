<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Task.def.php 39477 2013-02-27 05:59:46Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Task.def.php $
 * @author $Author: yangwenhai $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-27 13:59:46 +0800 (三, 2013-02-27) $
 * @version $Revision: 39477 $
 * @brief 
 *  
 **/

class TaskOperateType
{
	const ARMING = 1; //装备武器操作	对任意英雄进行一次装备的操作，需要装备成功。
	const REINFORCE = 2; //强化操作	进行一次强化装备的操作。
	const RECRUIT = 3; //招募伙伴	招募一个普通伙伴
	const FORMATION = 4; //上阵操作	将伙伴拖拽到阵型上。
	const UPGRADE_BUILDING = 5; //升级建筑	升级任意一个舱室。或者说进行升级建筑的操作一次。 (多次)
	const SAIL = 6; //出航操作	在主船里进行一次出航操作。
	const ENTER_PORT = 7; //进入港口
	const HERO_TRAIN = 8; //伙伴训练操作	对一个伙伴拖到伙伴室进行一次训练操作。
	const UPGRADE_SCI = 9; //研究院	升级一项科技。
	const PET_TRAIN = 10; //宠物训练	拖拽一个宠物进行训练
	const TRANSFER = 11; //进行第一次转职	对主角进行一次转职操作。
	const LEARN_SKILL = 12; //学习第一个技能	学习一个技能。
	//const PUB_PRESITGE = 13; 	//酒馆声望	查看酒馆声望面板（不好做可以讨论下）
	const ARENA_CHALLENGE = 14; //竞技场	在竞技场里进行一次挑战操作，无论胜负。
	const JOIN_OR_CREATE_GUILD = 15; //加入公会	加入一个公会，创建也算。接任务的时候如果玩家已经在公会里了，也算完成。
	const EXPLORE = 16; //宝石探索	进行一次宝石探索操作。
	const GEM_ENCHASE = 17; //宝石镶嵌
	const ARMING_PRODUCE = 18; //装备制作	进行一次装备制作操作
	const TREASURE = 19; //寻宝操作	进行一次寻宝操作
	const DAYTASK_COMPLETE = 20; //每日任务	完成一次每日任务
	const PORT_ATTACK = 21; //港口攻打	进行一次港口攻打，无论胜负
	const BOAT_UPGRADE = 22; //主船改造升级	进行一次主船改造  (多次)
	const KITCHEN_PRODUCE = 23; //厨房生产	进行一次厨房生产
	const OREDER = 24; //订单	进行一次下订单的操作
	const ARM_REFRESH = 25; //洗炼	进行一次洗炼（普通和定向都算）
	const CHAT = 26; //会谈操作	进行一次会谈操作	
	const ADD_PRESTIGE_HERO = 27; //添加声望伙伴
	const GEM_FUSE = 28; //宝石融合
	const CAPTAIN_ROOM_OPEN = 29; // 船长室开启
	const SAILOR_ROOM_OPEN = 30; // 水手室开启
	const MEDICAL_ROOM_OPEN = 31; // 医疗室开启
	const ST_ROOM_OPEN = 32; // 研究院开启
	const PET_ROOM_OPEN = 33; // 宠物室开启
	const KITCHEN_ROOM_OPEN = 34; // 厨房开启
	const CASH_ROOM_OPEN = 35; // 藏金室开启
	const TRAIN_ROOM_OPEN = 36; // 训练室开启
	const TRADE_ROOM_OPEN = 37; // 贸易室开启
	const AUTO_ATK = 38; // 副本挂机
	const FORMATION_LV = 39; //阵型升级
	const HERO_RAPID = 40; //英雄突飞
	const OCCUPY_RESOURCE = 41;//占领资源
	const CONQUER = 42;//征服
	const ENTER_RESOURCE_SCENE = 43; //进入资源场景
	const GIFT_EXCHANGE = 44; //兑换礼物
	const SOUL_HARVEST = 45; //收获影魂
	const AST_LEVELUP = 46; //星座升级
	const ALLBLUE_HARVEST = 47;//allblue采集
	const JEWELRY_REINFORCE = 48;//宝物强化
	const IMPEL_DEFEAT = 49;//推进城击败任意部队
	const CRYSTAL_SUMMON = 50; //精炼远古晶石
	// const  = 51; //血战到底
	const HAKI_TRIAL = 52; //霸气试炼
	const SEASOUL_COMPOSE = 53; //海魂之力
	// const  = 54; //幻之第六人
	const ALLBLUE_DONATE = 55; //获得allblue积分
	const HAKI_MASTER = 56; //查看主角霸气强化
}

class TaskStatus
{
	//不支持的状态
	const UNSUPPORTED = -1;
	
	const DELETE = 0;
	//可交
	const CAN_SUBMIT = 1;
	
	//可接
	const CAN_ACCEPT = 2;
	
	//已接
	const ACCEPT = 3;
	
	//完成
	const COMPLETE = 4;
	
	//放弃, 这里暂时设置为0
	//const abandon = 5;
}

class TaskMainType
{
	//每日
	const DAY = 1;
	//每周 
	const WEEK = 2;
	//每月
	const MONTH = 3;
	//主线
	const MAIN = 4;
	//支线
	const NORMAL = 5;
	//活动
	const ACTIVITY = 6; 
	//奖励任务
	const REWARD = 100;
}

//接受任务条件类型
class TaskAcceptType
{
	//等级
	const LEVEL = 1;
	//性别
	const GENDER = 2;
	//威望
	const PRESTIGE = 3;
	//成就
	const SUCCESS = 4;
	//通关某部队
	const BEAT_ARMY = 5;
	//副本
	const COPY = 6;

	//时间限制
	const PERIOD = 7;
	
	//前置taskId
	const PRE_TASK_ID = 8;
	
	//任务为奖励任务的时候不可接受
	const IS_REWARD = 9;
}


//任务完成条件类型
class TaskCompleteType
{
    //打败部队计数
    const BEAT_ARMY = 1;
    
    //击败部队取物
    const BEAT_ARMY_ITEM = 2;

    //上交任务物品
    const ITEM = 3;

    //寻人
    const FIND_NPC = 4;

    //击败某部队任务需要评价
    const BEAT_ARMY_LEVEL = 5;

    //进行某操作次数
    const OPERATE = 6;

    //建筑升级
    const BUILDING_UPGRADE = 7;

    //人物属性
    const USER_PROPERTY = 8;

   //英雄升级
   const HERO_UPGRADE = 11;

   //连续登录
   const LOGIN = 12;

}

class TaskDataType
{
	/**
	 * 部队
	 */
	const ARMY = 1;
	
	/**
	 * 物品
	 * @var uint
	 */
	const ITEM = 2;
	
	/**
	 * 击败某部队次数，需要评价
	 * @var uint
	 */
	const BEAT_ARMY_LEVEL = 3;
	
	/**
	 * 操作
	 * @var uint
	 */
	const OPERATE = 4;
	
	/**
	 * 
	 * 建筑升级
	 * @var uint
	 */
    const BUILDING_UPGRADE = 5;

    /**
     * 
     * 用户属性
     * @var uint
     */
    const USER_PROPERTY = 6;


   /**
    * 英雄升级
    * @var uint
    */
   const HERO_UPGRADE = 7;


   /**
    * 用户登录
    * @var uint
    */
   const LOGIN = 8;
	
}

//任务奖励
class TaskRewardType
{
	
	const BELLY = 1;
	const EXPERIENCE = 2;
	const PRESTIGE = 3;
	const EXP = 4;
	const FOOD = 5;
	const TITLE = 6;
	const DROPTABLE_ID = 7;
	const TASK_ID = 8;
	const HERO = 9;
}

class TaskCountReward
{
	const REWARD_FIXED = 1;
	const REWARD_LEVEL = 2;
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */