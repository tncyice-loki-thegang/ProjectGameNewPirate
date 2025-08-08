<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Item.class.php 18734 2012-04-16 12:04:24Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/Item.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-04-16 20:04:24 +0800 (一, 2012-04-16) $
 * @version $Revision: 18734 $
 * @brief
 *
 **/

class Item
{
	/**
	 *
	 * 物品ID, 系统唯一
	 * @var int
	 */
	protected $m_item_id;

	/**
	 *
	 * 物品模板ID
	 * @var int
	 */
	protected $m_item_template_id;


	/**
	 *
	 * 物品数量
	 * @var int
	 */
	protected $m_item_num;

	/**
	 *
	 * 物品产生时间
	 * @var int
	 */
	protected $m_item_time;

	/**
	 *
	 * 物品信息, AMF encode
	 * @var string
	 */
	protected $m_item_text;

	/**
	 *
	 * Item 初始化函数
	 * @param int $item_id
	 * @throws Exception				如果该物品不存在,则throw exception
	 *
	 * @return NULL
	 */
	public function Item($item)
	{
		if ( empty($item) )
		{
			Logger::FATAL('Item::Item is NULL');
			throw new Exception('fake');
		}
		$this->m_item_id = $item[ItemDef::ITEM_SQL_ITEM_ID];
		$this->m_item_template_id = $item[ItemDef::ITEM_SQL_ITEM_TEMPLATE_ID];
		$this->m_item_num = $item[ItemDef::ITEM_SQL_ITEM_NUM];
		$this->m_item_time = $item[ItemDef::ITEM_SQL_ITEM_TIME];
		$this->m_item_text = $item[ItemDef::ITEM_SQL_ITEM_TEXT];
	}

	/**
	 *
	 * 得到物品ID
	 *
	 * @return int
	 */
	public function getItemID()
	{
		return $this->m_item_id;
	}

	/**
	 *
	 * 得到物品的模板ID
	 *
	 * @return int
	 */
	public function getItemTemplateID()
	{
		return $this->m_item_template_id;
	}

	/**
	 *
	 * 得到物品的数量
	 *
	 * @return int
	 */
	public function getItemNum()
	{
		return $this->m_item_num;
	}

	/**
	 *
	 * 设置物品的数量
	 * @param int $item_num
	 *
	 * @return NULL
	 */
	public function setItemNum($item_num)
	{
		$this->m_item_num = $item_num;
	}

	/**
	 *
	 * 得到物品的生成时间
	 *
	 * @return int
	 */
	public function getItemTime()
	{
		return $this->m_item_time;
	}

	/**
	 *
	 * 得到物品的属性
	 *
	 * @return string
	 */
	public function getItemText()
	{
		return $this->m_item_text;
	}

	/**
	 *
	 * 设置物品的属性
	 * @param string $item_text
	 *
	 * @return NULL
	 */
	public function setItemText($item_text)
	{
		$this->m_item_text = $item_text;
	}

	/**
	 *
	 * 得到物品的类型
	 *
	 * @return int
	 */
	public function getItemType()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_TYPE);
	}

	/**
	 *
	 * 得到物品品质
	 *
	 * @return int
	 */
	public function getItemQuality()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_QUALITY);
	}

	/**
	 *
	 * 判断物品是否可用
	 *
	 * @return boolean		TRUE 表示可以出售，FALSE表示不可以出售
	 */
	public function canSell()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_SELL_TYPE)
			!= ItemDef::ITEM_CAN_NOT_SELL;
	}

	/**
	 *
	 * 得到物品的出售信息
	 *
	 * @return array		sell_pirce表示出售的价格, sell_type表示出售的类型
	 */
	public function sellInfo()
	{
		$return['sell_price'] = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_SELL_PRICE);
		$return['sell_type'] = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_SELL_TYPE);
		return $return;
	}

	/**
	 *
	 * 判断物品是否可用
	 *
	 * @return boolean		TRUE 表示可以使用，FALSE表示不可以使用
	 */
	public function canUse()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::Item_ATTR_NAME_USE)
			== ItemDef::ITEM_CAN_USE;
	}

	/**
	 *
	 * 物品使用需求
	 *
	 * @return array
	 */
	public function useReqInfo()
	{
		return array();
	}

	/**
	 *
	 * 使用物品
	 * @see 本函数为abstract function, 子类需要实现该函数
	 */
	public function useInfo()
	{
		Logger::FATAL("invoke fall down item basic class!item_template_id=%s!", $this->m_item_template_id);
		return FALSE;
	}

	/**
	 *
	 * 判断物品是否可以摧毁
	 *
	 * @return boolean		TRUE 表示可以摧毁, 否则表示不可以摧毁
	 */
	public function canDestory()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_DESTORY)
			== ItemDef::ITEM_CAN_DESTORY;
	}

	/**
	 *
	 * 判断物品是否可以叠加
	 *
	 * @return boolean		TRUE 表示可以叠加, 否则表示不可以叠加
	 */
	public function canStackable()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_STACKABLE)
			!= ItemDef::ITEM_CAN_NOT_STACKABLE;
	}

	/**
	 *
	 * 得到物品的叠加上限
	 *
	 * @return int
	 */
	public function getStackable()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_STACKABLE);
	}

	/**
	 *
	 * 删除物品
	 *
	 * @return boolean		TRUE 表示删除成功, FALSE表示删除失败
	 */
	public function deleteItem()
	{
		$this->m_item_id = 0;
		$this->m_item_template_id = 0;
		$this->m_item_num = 0;
		$this->m_item_time = 0;
		$this->m_item_text = '';
		return TRUE;
	}

	/**
	 *
	 * 物品信息
	 *
	 * @param boolean					TRUE表示简单版本,供最终返回给前端的接口使用
	 *
	 * @return array
	 * <code>
	 * 	[
	 * 			item_id:int
	 * 			item_template_id:int
	 * 			item_num:int
	 * 			item_time:int
	 * 			va_item_text:int
	 * 	]
	 * </code>
	 */
	public function itemInfo()
	{
		return array(
			ItemDef::ITEM_SQL_ITEM_ID 				=> $this->m_item_id,
			ItemDef::ITEM_SQL_ITEM_TEMPLATE_ID 		=> $this->m_item_template_id,
			ItemDef::ITEM_SQL_ITEM_NUM				=> $this->m_item_num,
			ItemDef::ITEM_SQL_ITEM_TIME				=> $this->m_item_time,
			ItemDef::ITEM_SQL_ITEM_TEXT				=> $this->m_item_text,
		);
	}

	/**
	 *
	 * 生成物品扩展属性
	 *
	 */
	public static function createItem($item_template_id)
	{
		return array();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
