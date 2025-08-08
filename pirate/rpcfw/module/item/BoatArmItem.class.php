<?php
/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BoatArmItem.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/BoatArmItem.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

class BoatArmItem extends Item
{
	/**
	 *
	 * 得到装备的类型
	 *
	 * @return int
	 */
	public function getArmType()
	{
		return ItemAttr::getIteMAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_ARM_TYPE);
	}

	/**
	 *
	 * 得到物品信息，供后端使用
	 *
	 * @return array
	 */
	public function info()
	{
		$array = array();
		//计算武器本身的数值
		foreach ( ItemDef::$ITEM_BOAT_ARM_ATTRS_CALC as $key => $value)
		{
			$array[$key] = ItemAttr::getItemAttr($this->m_item_template_id, $key);
		}
		return $array;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */