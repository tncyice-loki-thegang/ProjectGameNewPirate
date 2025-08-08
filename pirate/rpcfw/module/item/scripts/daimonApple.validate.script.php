<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: daimonApple.validate.script.php 21135 2012-05-23 11:48:16Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/daimonApple.validate.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-23 19:48:16 +0800 (三, 2012-05-23) $
 * @version $Revision: 21135 $
 * @brief
 *
 **/

require_once dirname ( dirname( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Item.def.php";

$all_items = btstore_get()->ITEMS->toArray();

foreach ( $all_items as $item_id => $item_info )
{
	if ( $item_info[ItemDef::ITEM_ATTR_NAME_TYPE] == ItemDef::ITEM_DAIMONAPPLE )
	{
		//validate daimonapple item stackable
		if ( $item_info[ItemDef::ITEM_ATTR_NAME_STACKABLE] != ItemDef::ITEM_CAN_NOT_STACKABLE )
		{
			echo "DAIMONAPPLE ITEM:$item_id can stackable:" . $item_info[ItemDef::ITEM_ATTR_NAME_STACKABLE] . "\n";
		}

		//validate daimonapple item attrs
		foreach ( $item_info[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTRS] as $attr_id => $value )
		{
			if ( !in_array($attr_id, array_keys(ItemDef::$ITEM_ATTR_IDS)) )
			{
				echo "DAIMONAPPLE ITEM:$item_id has invalid attr_id" . $attr_id . "\n";
			}
		}

		//validate daimonapple item enchase item
		foreach ( $item_info[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_ITEMS] as $erasure_item_id => $item_num )
		{
			if ( !isset($all_items[$erasure_item_id]) )
			{
				echo "DAIMONAPPLE ITEM:$item_id erasure item id:$erasure_item_id invalid\n";
			}
		}

		//validate daimonapple item skills
		//TODO
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */