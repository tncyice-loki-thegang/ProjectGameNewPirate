<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FashionDressItem.class.php 36864 2013-01-24 02:47:23Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/FashionDressItem.class.php $
 * @author $Author: HongyuLan $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-24 10:47:23 +0800 (四, 2013-01-24) $
 * @version $Revision: 36864 $
 * @brief 
 *  
 **/

//时装
class FashionDressItem extends Item
{
	public function getFashionType()
	{
		//TODO
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_FASHION_EQUIP_TYPE);
	}
	public function equipReq()
	{
		return ItemAttr::getItemAttrs($this->m_item_template_id,
				array(ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_LVLIMIT, ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_HERO_IDS));
	}
	public function info()
	{
		$growupAttr = btstore_get()->ITEMS[$this->m_item_template_id]['grownUp'];	
		$return = array();
		foreach ( $this->getAttrs() as $attr_id => $attr_value )
		{
			$attr_name = ItemDef::$ITEM_ATTR_IDS[$attr_id];
			if ( isset($return[$attr_name]) )
			{
				$return[$attr_name] += $attr_value;				
			}
			else
			{
				$return[$attr_name] = $attr_value + ( $this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] ) * $growupAttr[$attr_id];
			}	
		}
		Logger::DEBUG('FashionDressItem:%d template_id:%d basic numerical:%s',
		$this->m_item_id, $this->m_item_template_id, $return);
		return $return;
	}
	private function getAttrs()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_PROPERTY);
	}
	
	/**
	 * 设置该当前的强化等级
	 */
	public function setReinforceLevel($level)
	{
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL]=$level;
	}
	/**
	 * 获得该当前的强化等级
	 */
	public function getReinforceLevel()
	{
		$info=$this->m_item_text;
		if (empty($info))
		{
			return 0;
		}
		if (!isset($info[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL]))
		{
			return 0;
		}
		return $info[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL];
	}
	
	public static function createItem($item_template_id)
	{
		$item_text = array();

		//初始化物品强化等级
		$item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] = ItemDef::ITEM_REINFORCE_LEVEL_DEFAULT;

		return $item_text;
	}
	
	public static function getStrengthId($item_template_id)
	{
		return ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_STRENGTH_ID);
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */