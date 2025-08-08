<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GoodWillItem.class.php 23295 2012-07-05 06:59:45Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/GoodWillItem.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2012-07-05 14:59:45 +0800 (四, 2012-07-05) $
 * @version $Revision: 23295 $
 * @brief
 *
 **/

class GoodWillItem extends Item
{
	/**
	 *
	 * 得到物品的增加的好感度
	 *
	 * @return int
	 */
	public function getGoodWill()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_GOOD_WILL);
	}

	/**
	 *
	 * 得到好感度物品的类型
	 *
	 * @return int
	 */
	public function getGoodWillType()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_GOOD_WILL_TYPE);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */