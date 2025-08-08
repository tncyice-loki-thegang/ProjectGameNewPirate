<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Arming.def.php 16414 2012-03-14 02:48:18Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Arming.def.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:48:18 +0800 (三, 2012-03-14) $
 * @version $Revision: 16414 $
 * @brief
 *
 **/

class ArmingDef
{
	//武器
	const ARMING_ARM_POSITION				=			1;
	//戒指
	const ARMING_RING_POSITION				=			2;
	//书
	const ARMING_BOOK_POSITION				=			3;
	//衣服
	const ARMING_CLOTHING_POSITION			=			4;
	//帽子
	const ARMING_HAT_POSITION				=			5;
	//披风
	const ARMING_MANTLE_POSITION			=			6;
	//项链
	const ARMING_NECKLACE_POSITION			=			7;
	//耳环
	const ARMING_EARRING_POSITION			=			8;

	public static $ARMING_POSITIONS				=			array (
		self::ARMING_ARM_POSITION			=>		array (
			ItemDef::ITEM_ARM_ARM,
		),
		self::ARMING_RING_POSITION			=>		array (
			ItemDef::ITEM_ARM_RING,
		),
		self::ARMING_BOOK_POSITION			=>		array (
			ItemDef::ITEM_ARM_BOOK,
		),
		self::ARMING_CLOTHING_POSITION		=>		array (
			ItemDef::ITEM_ARM_CLOTHING,
		),
		self::ARMING_HAT_POSITION			=>		array (
			ItemDef::ITEM_ARM_HAT,
		),
		self::ARMING_MANTLE_POSITION		=>		array (
			ItemDef::ITEM_ARM_MANTLE,
		),
		self::ARMING_NECKLACE_POSITION		=>		array (
			ItemDef::ITEM_ARM_NECKLACE,
		),
		self::ARMING_EARRING_POSITION		=>		array (
			ItemDef::ITEM_ARM_EARRING,
		)
	);

	//默认所有装备为空
	public static $ARMING_NO_ARMING = array (
		self::ARMING_ARM_POSITION			=>	ItemDef::ITEM_ID_NO_ITEM,
		self::ARMING_RING_POSITION			=>	ItemDef::ITEM_ID_NO_ITEM,
		self::ARMING_BOOK_POSITION			=>	ItemDef::ITEM_ID_NO_ITEM,
		self::ARMING_CLOTHING_POSITION		=>	ItemDef::ITEM_ID_NO_ITEM,
		self::ARMING_HAT_POSITION			=>	ItemDef::ITEM_ID_NO_ITEM,
		self::ARMING_MANTLE_POSITION		=>	ItemDef::ITEM_ID_NO_ITEM,
		self::ARMING_NECKLACE_POSITION		=>	ItemDef::ITEM_ID_NO_ITEM,
		self::ARMING_EARRING_POSITION		=>	ItemDef::ITEM_ID_NO_ITEM
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */