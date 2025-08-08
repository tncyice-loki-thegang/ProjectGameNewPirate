<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Drop.class.php 15023 2012-02-28 05:52:27Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/drop/Drop.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-02-28 13:52:27 +0800 (二, 2012-02-28) $
 * @version $Revision: 15023 $
 * @brief
 *
 **/

class Drop
{
	/**
	 * 执行掉落表逻辑,得到随机后掉落的物品列表
	 *
	 * @param int $drop_template_id			物品掉落表ID
	 *
	 * @return array(array(item_template_id, item_num))
	 *
	 * @see 得到的是物品模板ID, 并且没有执行任何的Add操作！
	 */
	public static function dropItem($drop_template_id)
	{
		$drop_template_id = intval($drop_template_id);

		$drop_items = array();
		//根据掉落表,得到掉落表ID
		if ( !isset(btstore_get()->DROPITEM[$drop_template_id]) )
		{
			Logger::FATAL('invalid drop_template_id:%d', $drop_template_id);
			throw new Exception('invalid drop id!');
		}
		$drop = btstore_get()->DROPITEM[$drop_template_id]->toArray();
		if ( empty($drop) )
		{
			Logger::warning("drop table id=%d invalid!", $drop_template_id);
			return $drop_items;
		}

		//计算掉落的数量
		$keys = Util::backSample($drop[DropDef::DROP_ITEM_TYPE_NUM_LIST], 1, DropDef::DROP_WEIGHT);
		$drop_items_num = $drop[DropDef::DROP_ITEM_TYPE_NUM_LIST][$keys[0]][DropDef::DROP_ITEM_TYPE_NUM];

		if ( $drop_items_num == 0 )
			return $drop_items;

		//计算掉落的物品列表
		$keys = Util::noBackSample($drop[DropDef::DROP_LIST], $drop_items_num);
		foreach ( $keys as $key )
		{
			$drop_items[] = array(
				DropDef::DROP_ITEM_TEMPLATE_ID	=>	$drop[DropDef::DROP_LIST][$key][DropDef::DROP_ITEM_TEMPLATE_ID],
				DropDef::DROP_ITEM_NUM			=>	$drop[DropDef::DROP_LIST][$key][DropDef::DROP_ITEM_NUM],
			);
		}

		Logger::DEBUG("dropItem:%d, items:%s", $drop_items_num, $drop_items);
		return $drop_items;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */