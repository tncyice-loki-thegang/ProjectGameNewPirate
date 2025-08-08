<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ItemDAO.class.php 19843 2012-05-07 02:31:08Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/ItemDAO.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-07 10:31:08 +0800 (一, 2012-05-07) $
 * @version $Revision: 19843 $
 * @brief
 *
 **/



class ItemDAO
{
	/**
	 *
	 * select物品
	 *
	 * @param array $select
	 * @param array $wheres
	 *
	 * @return array Item数据
	 */
	public static function selectItem($select, $wheres)
	{
		$data = new CData();
		$data->select($select)->from(ItemDef::ITEM_TABLE_NAME);
		foreach ( $wheres as $where )
		{
			$data->where($where);
		}
		$return = $data->query();
		return $return;
	}

	/**
	 *
	 * 插入物品
	 * @param array $values
	 * @throws Exception
	 */
	public static function insertItem($values)
	{
		$data = new CData();
		$return = $data->insertInto(ItemDef::ITEM_TABLE_NAME)->values($values)->query();
		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			throw new Exception('item insert fake');
		}
		return TRUE;
	}

	/**
	 *
	 * 更新物品数据
	 * @param array $where
	 * @param array $values
	 * @throws Exception
	 */
	public static function updateItem($where, $values)
	{
		$data = new CData();
		$return = $data->update(ItemDef::ITEM_TABLE_NAME)->set($values)->where($where)->query();
		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			throw new Exception('item update fake');
		}
		return TRUE;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
