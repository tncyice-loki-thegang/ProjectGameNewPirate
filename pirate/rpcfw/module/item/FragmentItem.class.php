<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FragmentItem.class.php 23330 2012-07-05 09:43:34Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/FragmentItem.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-07-05 17:43:34 +0800 (四, 2012-07-05) $
 * @version $Revision: 23330 $
 * @brief
 *
 **/

class FragmentItem extends Item
{
	/**
	 * (non-PHPdoc)
	 * @see Item::useReqInfo()
	 */
	public function useReqInfo()
	{
		return array();
	}

	/**
	 * (non-PHPdoc)
	 * @see Item::useInfo()
	 */
	public function useInfo()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_USE_INFO);
	}

	/**
	 *
	 * 得到装备的兑换ID
	 */
	public function getArmExchangeId()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_EXCHANGE_ID);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */