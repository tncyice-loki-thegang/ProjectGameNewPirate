<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: armItem.validate.script.php 21135 2012-05-23 11:48:16Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/armItem.validate.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-23 19:48:16 +0800 (三, 2012-05-23) $
 * @version $Revision: 21135 $
 * @brief
 *
 **/

require_once dirname ( dirname( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Item.def.php";
require_once dirname ( dirname( dirname ( dirname ( __FILE__ ) ) ) ) . "/conf/Forge.cfg.php";

$all_items = btstore_get()->ITEMS->toArray();

foreach ( $all_items as $item_id => $item_info )
{
	if ( $item_info[ItemDef::ITEM_ATTR_NAME_TYPE] == ItemDef::ITEM_ARM )
	{
		//validate arm item stackable
		if ( $item_info[ItemDef::ITEM_ATTR_NAME_STACKABLE] != ItemDef::ITEM_CAN_NOT_STACKABLE )
		{
			echo "ARM ITEM:$item_id can stackable:" . $item_info[ItemDef::ITEM_ATTR_NAME_STACKABLE] . "\n";
		}

		//validate arm item arm type
		if ( !in_array($item_info[ItemDef::ITEM_ATTR_NAME_ARM_TYPE], ItemDef::$ITEM_VALID_ARM_TYPES) )
		{
			echo "ARM ITEM:$item_id invalid item arm type" . $item_info[ItemDef::ITEM_ATTR_NAME_ARM_TYPE]
				. "\n";
		}

		//validate arm item hero
		//TODO

		//validate reinforce fee
		if ( !isset(btstore_get()->REINFORCEFEE[$item_info[ItemDef::ITEM_ATTR_NAME_REINFORCE_FEE]]) &&
			!empty($item_info[ItemDef::ITEM_ATTR_NAME_REINFORCE_FEE]) )
		{
			echo "ARM ITEM:$item_id invalid item reinforce id:" . $item_info[ItemDef::ITEM_ATTR_NAME_REINFORCE_FEE]
				. "\n";
		}

		//validate enchanse req
		$last_hole_id = 0;
		foreach ( $item_info[ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ] as $hole_id => $reinforce_level )
		{
			if ( $reinforce_level < ItemDef::ITEM_REINFORCE_LEVEL_DEFAULT ||
				$reinforce_level > ForgeConfig::ARM_MAX_REINFORCE_LEVEL )
			{
				echo "ARM ITEM:$item_id invalid item enchase req:hole_id:" . $hole_id
				. "=>reinforce_level:$reinforce_level\n";
			}

			if ( !empty($last_hole_id) &&
				$reinforce_level < $item_info[ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ][$last_hole_id] )
			{
				echo "ARM ITEM:$item_id invalid item enchase req:hole_id:" . $hole_id
				. "=>reinforce_level:" . $reinforce_level . " < hole_id:" . $hole_id
				. "=>reinforce_level:" . $item_info[ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ][$last_hole_id]
				. "\n";
			}

			$last_hole_id = $hole_id;
		}

		//validate fixed potentiality
		if ( !empty($item_info[ItemDef::ITEM_ATTR_NAME_FIXED_POTENTIALITY]) &&
			!isset(btstore_get()->FIXEDPOTENTIALITY[$item_info[ItemDef::ITEM_ATTR_NAME_FIXED_POTENTIALITY]]) )
		{
			echo "ARM ITEM:$item_id invalid fixed potentiality id:" . $item_info[ItemDef::ITEM_ATTR_NAME_FIXED_POTENTIALITY]
				. "\n";
		}

		//validate fixed potentiality and refresh
		if ( !empty($item_info[ItemDef::ITEM_ATTR_NAME_FIXED_POTENTIALITY]) &&
			( $item_info[ItemDef::ITEM_ATTR_NAME_REFRESH_RANDPOTENTIALITY_ENABLE] ==
			ItemDef::ITEM_CAN_RANDOM_REFRESH_POTENTIALITY ||
			$item_info[ItemDef::ITEM_ATTR_NAME_REFRESH_FIXEDPOTENTIALITY_ENABLE] ==
			ItemDef::ITEM_CAN_FIXED_REFRESH_POTENTIALITY ) )
		{
			echo "ARM ITEM:$item_id fixed potentiality id:" . $item_info[ItemDef::ITEM_ATTR_NAME_FIXED_POTENTIALITY]
				. " can fixed refresh or random refresh\n";
		}

		//validate random potentiality
		if ( !empty($item_info[ItemDef::ITEM_ATTR_NAME_RANDPOTENTIALITY]) &&
			!isset(btstore_get()->POTENTIALITY[$item_info[ItemDef::ITEM_ATTR_NAME_RANDPOTENTIALITY]]) )
		{
			echo "ARM ITEM:$item_id invalid random potentiality id:" . $item_info[ItemDef::ITEM_ATTR_NAME_RANDPOTENTIALITY]
				. "\n";
		}

		//validate init reinforce level
		if ( !empty($item_info[ItemDef::ITEM_ATTR_NAME_INIT_REINFORCE_LEVEL]) &&
			$item_info[ItemDef::ITEM_ATTR_NAME_INIT_REINFORCE_LEVEL] >=
			ForgeConfig::ARM_MAX_REINFORCE_LEVEL )
		{
			echo "ARM ITEM:$item_id init reinforce level:" . $item_info[ItemDef::ITEM_ATTR_NAME_INIT_REINFORCE_LEVEL] .
				">max reinforce level:" . ForgeConfig::ARM_MAX_REINFORCE_LEVEL . "\n";
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */