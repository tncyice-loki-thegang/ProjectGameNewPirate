<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Daytask.def.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/daytask/Daytask.def.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

class DaytaskStatus
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
};

class DaytaskType
{
	//出航
	const SAIL = 0;

	//强化装备
	const REINFORCE = 1;

	//竞技场挑战
	const ARENA_CHANLLENGE = 2;
	
	//探索
	const TREASURE = 3;
	
	//击败部队
	const BEAT_SUCCESS = 4;
	
	//攻击玩家
	const PORT_ATTACK = 5;
	
	//占领资源
	const OCCUPY_RESOURSE = 6;
	
	//花费金币
	const COST_GOLD = 7;
	
	//厨房生产
	const KITCHEN_PRODUCE = 8;
	
	//突飞伙伴
	const RAPID_HERO = 9;
	
	//洗炼
	const REFRESH_EQUIP = 10;
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */