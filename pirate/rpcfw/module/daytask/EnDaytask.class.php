<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: EnDaytask.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/daytask/EnDaytask.class.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/




class EnDaytask
{
	//出航
	public static function sail ($num=1)
	{
		self::checkDaytask(DaytaskType::SAIL, $num);
	}
	
	//强化装备
	public static function reinforce ($num=1)
	{
		self::checkDaytask(DaytaskType::REINFORCE, $num);
	}
	
	//竞技场挑战
	public static function arenaChanllenge ($num=1)
	{
		self::checkDaytask(DaytaskType::ARENA_CHANLLENGE, $num);
	}
	
	//探索
	public static function treasure ($num=1)
	{
		self::checkDaytask(DaytaskType::TREASURE, $num);
	}
	
	//击败部队
	public static function beatSuccess ($num=1)
	{
		self::checkDaytask(DaytaskType::BEAT_SUCCESS, $num);
	}
	
	//攻击玩家
	public static function portAttack ($num=1)
	{
		self::checkDaytask(DaytaskType::PORT_ATTACK, $num);
	}
	
	//占领资源, 无论是否成功
	public static function occupyResourse ($num=1)
	{
		self::checkDaytask(DaytaskType::OCCUPY_RESOURSE, $num);
	}
	
	//花费金币
	public static function costGold ($num=1)
	{
		self::checkDaytask(DaytaskType::COST_GOLD, $num);
	}
	
	//厨房生产
	public static function kitchenProduce ($num=1)
	{
		self::checkDaytask(DaytaskType::KITCHEN_PRODUCE, $num);
	}
	
	//突飞伙伴
	public static function rapidHero ($num=1)
	{
		self::checkDaytask(DaytaskType::RAPID_HERO, $num);
	}
	
	//洗炼
	public static function refreshEquip ($num=1)
	{
		self::checkDaytask(DaytaskType::REFRESH_EQUIP, $num);
	}

	
	public static function checkDaytask($type, $num=1)
	{
		Logger::debug('daytask type:%d, num:%d', $type, $num);
		DaytaskLogic::checkAccept($type, $num);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */