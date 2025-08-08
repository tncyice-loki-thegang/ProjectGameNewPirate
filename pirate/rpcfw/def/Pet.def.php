<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Pet.def.php 32562 2012-12-07 10:17:54Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Pet.def.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-07 18:17:54 +0800 (五, 2012-12-07) $
 * @version $Revision: 32562 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : PetDef
 * Description : 宠物数据常量类
 * Inherit     : 
 **********************************************************************************************************************/
class PetDef
{
	const LOCK = 1;								// 技能锁定
	const UNLOCK = 0;							// 技能解锁

	const IN_WARE_HOUSE = 1;					// 在仓库里面
	const OUT_WARE_HOUSE = 0;					// 不在仓库里

	const LITTLE_WHITE_PERCENT = 10000;			// 策划专用百分比计算常量

	const ATK_PHY = 47;							// 物理攻击
	const DEF_PHY = 50;							// 物理防御
	const ATK_SPE = 48;							// 必杀攻击
	const DEF_SPE = 51;							// 必杀防御
	const ATK_MGC = 49;							// 魔法攻击
	const DEF_MGC = 52;							// 魔法防御
	const PET_HP = 46;							// 最大血量

	const TRANSFER_GOLD = 0;					// 金币传承
	const TRANSFER_ITEM = 1;					// 道具传承

	/**
	 * 宠物的四个属性
	 */
	public static $ATTR_INDEX = array(
		0 => 'pow',
		1 => 'sen',
		2 => 'int',
	    3 => 'phy'
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */