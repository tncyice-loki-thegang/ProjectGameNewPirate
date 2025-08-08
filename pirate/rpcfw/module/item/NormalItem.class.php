<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: NormalItem.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/NormalItem.class.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

class NormalItem extends Item
{
	public function useInfo()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_USE_INFO);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */