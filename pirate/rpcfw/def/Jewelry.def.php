<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Jewelry.def.php 40024 2013-03-06 04:18:04Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Jewelry.def.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-03-06 12:18:04 +0800 (三, 2013-03-06) $
 * @version $Revision: 40024 $
 * @brief 
 *  
 **/

class  JewelryDef
{
	
	const JEWELRY_POSITION_1				=			1; //宝物类型1,装备在”宝物位置1”装备位上 WATER
	const JEWELRY_POSITION_2				=			2; //宝物类型2,装备在”宝物位置2”装备位上 FIRE
	const JEWELRY_POSITION_3				=			3; //宝物类型3,装备在”宝物位置3”装备位上 WIND
	const JEWELRY_POSITION_4				=			4; //宝物类型3,装备在”宝物位置4”装备位上 THUNDER
	const JEWELRY_POSITION_5				=			5; //宝物类型3,装备在”宝物位置5”装备位上 LIGHT
	const JEWELRY_POSITION_6				=			6; //宝物类型3,装备在”宝物位置6”装备位上 DARK
	
	public static $JEWELRY_POSITIONS		=			array (
			self::JEWELRY_POSITION_1			=>		array (
					ItemDef::ITEM_JEWELRY_TYPE_1,
			),
			self::JEWELRY_POSITION_2			=>		array (
					ItemDef::ITEM_JEWELRY_TYPE_2,
			),
			self::JEWELRY_POSITION_3			=>		array (
					ItemDef::ITEM_JEWELRY_TYPE_3,
			),
			self::JEWELRY_POSITION_4			=>		array (
					ItemDef::ITEM_JEWELRY_TYPE_4,
			),
			self::JEWELRY_POSITION_5			=>		array (
					ItemDef::ITEM_JEWELRY_TYPE_5,
			),
			self::JEWELRY_POSITION_6			=>		array (
					ItemDef::ITEM_JEWELRY_TYPE_6,
			),
	);
	
	//默认所有装备为空
	public static $JEWELRY_NO_JEWELRY = array (
			self::JEWELRY_POSITION_1			=>	ItemDef::ITEM_ID_NO_ITEM,
			self::JEWELRY_POSITION_2			=>	ItemDef::ITEM_ID_NO_ITEM,
			self::JEWELRY_POSITION_3			=>	ItemDef::ITEM_ID_NO_ITEM,
			self::JEWELRY_POSITION_4			=>	ItemDef::ITEM_ID_NO_ITEM,
			self::JEWELRY_POSITION_5			=>	ItemDef::ITEM_ID_NO_ITEM,
			self::JEWELRY_POSITION_6			=>	ItemDef::ITEM_ID_NO_ITEM,
	);
	
	

	const JEWELRY_SQL_TABLE						= 't_jewelry';
	const JEWELRY_SQL_UID						= 'uid'; 		//表结构 uid
	const JEWELRY_SQL_ELEMENT					= 'element'; 	//表结构  元素石
	const JEWELRY_SQL_ENERGY					= 'energy';     //表结构  能量石
	const JEWELRY_SQL_VIP_FREE_NUM				= 'vip_free_num';//已经使用的vip免费次数
	const JEWELRY_SQL_VIP_TIME					= 'vip_time';    //使用vip免费次数的时间
	
	const JEWELRY_STATUS_SEAL 					= 0; 			//封印状态
	const JEWELRY_STATUS_UNSEAL 				= 1; 			//解封状态
	const JEWELRY_STATUS_OPEN 					= 2; 			//开启状态
	
	const JEWELRY_REFRESH_TYPE_GOLD 		    = 0; 			//金币洗练
	const JEWELRY_REFRESH_TYPE_ENERGY 		    = 1; 			//能量石洗练
	const JEWELRY_REFRESH_TYPE_ITEM 		    = 2; 			//洗练石洗练
	
	
	const JEWELRY_REINFORCE_FACTOR				= 2;			//强化上限放大因子
	const JEWELRY_REINFORCE_ROLL_MAX			= 10000;		//roll基数
	
	const JEWELRY_SEALTRANSFER_TYPE_FREE		=0;				//封印属性转移，vip免费
	const JEWELRY_SEALTRANSFER_TYPE_GOLD		=1;				//封印属性转移，花费金币
	const JEWELRY_SEALTRANSFER_TYPE_ITEM		=2;				//封印属性转移，花费物品
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */