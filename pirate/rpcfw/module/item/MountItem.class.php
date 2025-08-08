<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PetEggItem.class.php 37625 2013-01-30 07:50:13Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/MountItem.class.php $
 * @author $Author: yangwenhai $(hoping@babeltime.com)
 * @date $Date: 2013-01-30 15:50:13 +0800 (三, 2013-01-30) $
 * @version $Revision: 37625 $
 * @brief 
 *  
 **/

class MountItem extends Item
{
	public function useInfo()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_USE_INFO);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */