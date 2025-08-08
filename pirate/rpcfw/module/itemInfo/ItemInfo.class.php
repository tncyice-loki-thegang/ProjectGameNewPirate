<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ItemInfo.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/itemInfo/ItemInfo.class.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

class ItemInfo implements IItemInfo
{
	/* (non-PHPdoc)
	 * @see IItemInfo::getItemInfos()
	 */
	public function getItemInfos($item_ids)
	{
		//格式化输入
		foreach ( $item_ids as $key => $item_id )
		{
			$item_ids[$key] = intval($item_id);
		}
		$item_ids = array_unique($item_ids);

		$return = array();
		$item_manager = ItemManager::getInstance();
		$items = $item_manager->getItems($item_ids);
		foreach ( $item_ids as $item_id )
		{
			if ($items[$item_id] === NULL )
			{
				$return[$item_id] = array();
			}
			else
			{
				$return[$item_id] = $items[$item_id]->itemInfo();
			}
		}
		return $return;
	}

	/* (non-PHPdoc)
	 * @see IItemInfo::getItemInfo()
	 */
	public function getItemInfo($item_id)
	{
		//格式化输入
		$item_id = intval($item_id);

		$item_manager = ItemManager::getInstance();
		$item = $item_manager->getItem($item_id);
		if ($item === NULL )
		{
			return array();
		}
		else
		{
			return $item->itemInfo();
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */