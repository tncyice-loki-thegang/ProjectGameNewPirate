<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GiftItem.class.php 6129 2011-10-13 07:07:31Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/GiftItem.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2011-10-13 15:07:31 +0800 (四, 2011-10-13) $
 * @version $Revision: 6129 $
 * @brief
 *
 **/

class GiftItem extends Item
{
	/**
	 * (non-PHPdoc)
	 * @see Item::useReqInfo()
	 */
	public function useReqInfo()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_USE_REQ);
	}

	/**
	 * (non-PHPdoc)
	 * @see Item::useInfo()
	 */
	public function useInfo()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_USE_INFO);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
