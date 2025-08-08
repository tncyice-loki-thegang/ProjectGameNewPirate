<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 *
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief
 *
 **/

/**********************************************************************************************************************
 * Class       : SailboatDef
 * Description : 主船数据常量类
 * Inherit     :
 **********************************************************************************************************************/
class SailboatDef
{
	/******************************************************************************************************************
	 * 主船的舱室ID
	 ******************************************************************************************************************/
	const CAPTAIN_ROOM_ID = 1;					// 舰长室
	const KITCHEN_ID = 2;						// 厨房
	const TRAIN_ROOM_ID = 3;					// 训练室
	const PET_ID = 4;							// 宠物室
	const TRADE_ROOM_ID = 5;					// 贸易室
	const SCI_TECH_ID = 6;						// 科技室
	const MEDICAL_ROOM_ID = 7;					// 医务室
	const CASH_ROOM_ID = 8;						// 藏金室
	const SAILOR_01_ID = 9;						// 水手室1
	const SAILOR_02_ID = 10;					// 水手室2
	const SAILOR_03_ID = 11;					// 水手室3
	const SAILOR_04_ID = 12;					// 水手室4
	const SAILOR_05_ID = 13;					// 水手室5
	const SAILOR_06_ID = 14;					// 水手室6
	const SAILOR_07_ID = 15;					// 水手室7
	const SAILOR_08_ID = 16;					// 水手室8
	const SAILOR_09_ID = 17;					// 水手室9
	const SAILOR_10_ID = 18;					// 水手室10
	const BUILDING_LIST = 51;					// 建筑队列

	/******************************************************************************************************************
	 * 装备的位置
	 ******************************************************************************************************************/
	const CANNON_SLOT = 101;					// 船首武器的位置
	const WALLPIECE_SLOT = 102;					// 舷炮武器的位置
	const ARMOUR_SLOT = 103;					// 装甲的位置
	const SAILS_SLOT = 104;						// 风帆的位置
	const FIGUREHEAD_SLOT = 105;				// 船首像的位置

	//相应位置可装备的主船装备类型
	public static $EQUIP_POSITIONS = array (
		self::CANNON_SLOT => array (
			ItemDef::ITEM_BOAT_ARM_CANNON,
			),
		self::WALLPIECE_SLOT => array (
			ItemDef::ITEM_BOAT_ARM_WALLPIECE,
			),
		self::ARMOUR_SLOT => array (
			ItemDef::ITEM_BOAT_ARM_ARMOUR,
			),
		self::SAILS_SLOT => array (
			ItemDef::ITEM_BOAT_ARM_SAILS,
			),
		self::FIGUREHEAD_SLOT => array (
			ItemDef::ITEM_BOAT_ARM_FIGUREHEAD,
			),
	);

	/**
	 * 更新装备信息
	 */
	public static $UPD_EQUIP = array(
		self::WALLPIECE_SLOT => 'wallpiece_item_id',
		self::CANNON_SLOT => 'cannon_item_id',
		self::FIGUREHEAD_SLOT => 'figurehead_item_id',
		self::SAILS_SLOT => 'sails_item_id',
		self::ARMOUR_SLOT => 'armour_item_id'
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */