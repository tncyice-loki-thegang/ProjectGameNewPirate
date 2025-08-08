<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ItemManager.class.php 38954 2013-02-21 09:16:50Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/ItemManager.class.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-02-21 17:16:50 +0800 (四, 2013-02-21) $
 * @version $Revision: 38954 $
 * @brief
 *
 **/

class ItemManager
{
	/**
	 *
	 * ItemManager实例
	 * @var ItemManager
	 */
	private static $m_instance;

	/**
	 *
	 * 维护的items的缓存
	 * @var array(Item)
	 */
	private $m_items = array();

	private $m_original_items = array();

	/**
	 *
	 * 私有构造函数
	 */
	private function __construct(){}

	/**
	 *
	 *  得到ItemManager实例
	 */
	public static function getInstance()
    {
		if(self::$m_instance == null)
		{
			self::$m_instance = new ItemManager();
		}
		return self::$m_instance;
	}

	/**
	 *
	 * 生成物品对象
	 * @param int $item_id
	 * @throws Exception			如果物品类型不存在,则throw Exception
	 *
	 * @return Item
	 */
	public function getItem($item_id)
	{
		$item = NULL;
		if ( $item_id <= 0 )
		{
			return $item;
		}

		//如果$this->m_items[$item_id] = NULL但是$this->m_original_items[$item_id] != NULL 表示物品在前边的操作中被删除
		if ( isset($this->m_items[$item_id]) || isset($this->m_original_items[$item_id]) )
		{
			return $this->m_items[$item_id];
		}
		else
		{
			$info = ItemStore::getItem($item_id);
			$item = $this->__getItem($info);
			if ( $item == NULL )
			{
				Logger::WARNING('item:%d is not exists', $item_id);
				return $item;
			}
			else
			{
				$this->m_items[$item_id] = $item;
				$this->m_original_items[$item_id] = serialize($item);
				return $this->m_items[$item_id];
			}
		}
	}

	/**
	 *
	 * 生成物品对象
	 *
	 * @param array(int) $item_ids
	 *
	 * @throws Exception			如果物品类型不存在,则throw Exception
	 *
	 * @return array
	 * <code>
	 * [
	 * 		item_id:Item
	 * ]
	 * </code>
	 */
	public function getItems($item_ids)
	{
		$item_ids_bak = $item_ids;
		foreach ( $item_ids as $key => $item_id )
		{
			if ( $item_id <= ItemDef::ITEM_ID_NO_ITEM )
			{
				unset($item_ids[$key]);
			}
			else if ( isset($this->m_items[$item_id]) || isset($this->m_original_items[$item_id]) )
			{
				unset($item_ids[$key]);
			}
		}

		$iteminfos = array();
		if ( !empty($item_ids) )
		{
			$iteminfos = ItemStore::getItems($item_ids);
		}
		$return = array();
		foreach ($item_ids_bak as $item_id)
		{
			if ( isset($this->m_items[$item_id]) || isset($this->m_original_items[$item_id]) )
			{
				$return[$item_id] = $this->m_items[$item_id];
			}
			else if ( $item_id != ItemDef::ITEM_ID_NO_ITEM )
			{
				if ( !isset($iteminfos[$item_id]) )
				{
					Logger::FATAL('item:%d is not exist!', $item_id);
					$return[$item_id] = NULL;
				}
				else
				{
					$item = $this->__getItem($iteminfos[$item_id]);
					$this->m_items[$item_id] = $item;
					$this->m_original_items[$item_id] = serialize($item);
					$return[$item_id] = $item;
				}
			}
		}
		return $return;
	}

	private function __getItem($iteminfo)
	{
		if ( empty($iteminfo) )
		{
			return NULL;
		}
		$item = NULL;
		$item_type = ItemAttr::getItemAttr($iteminfo[ItemDef::ITEM_SQL_ITEM_TEMPLATE_ID], ItemDef::ITEM_ATTR_NAME_TYPE);
		switch ( $item_type )
		{
			case ItemDef::ITEM_ARM:
				$item = new ArmItem($iteminfo);
				break;
			case ItemDef::ITEM_GEM:
				$item = new GemItem($iteminfo);
				break;
			case ItemDef::ITEM_CARD:
				$item = new CardItem($iteminfo);
				break;
			case ItemDef::ITEM_GIFT:
				$item = new GiftItem($iteminfo);
				break;
			case ItemDef::ITEM_RANDGIFT:
				$item = new RandGiftItem($iteminfo);
				break;
			case ItemDef::ITEM_DAIMONAPPLE:
				$item = new DaimonAppleItem($iteminfo);
				break;
			case ItemDef::ITEM_SHIPBLUEPRINT:
				$item = new ShipBlueprintItem($iteminfo);
				break;
			case ItemDef::ITEM_MISSION:
				$item = new MissionItem($iteminfo);
				break;
			case ItemDef::ITEM_DIRECT:
				$item = new DirectItem($iteminfo);
				break;
			case ItemDef::ITEM_BOATARM:
				$item = new BoatArmItem($iteminfo);
				break;
			case ItemDef::ITEM_PETEGG:
				$item = new PetEggItem($iteminfo);
				break;
			case ItemDef::ITEM_NORMAL:
				$item = new NormalItem($iteminfo);
				break;
			case ItemDef::ITEM_FRAGMENT:
				$item = new FragmentItem($iteminfo);
				break;
			case ItemDef::ITEM_GOODWILL:
				$item = new GoodWillItem($iteminfo);
				break;
			case ItemDef::ITEM_FISH:
				$item = new FishItem($iteminfo);
				break;
			case ItemDef::ITEM_FASHION_DRESS:
				$item = new FashionDressItem($iteminfo);
				break;
			case ItemDef::ITEM_JEWELRY:
				$item = new JewelryItem($iteminfo);
				break;
			case ItemDef::ITEM_MOUNT:
				$item = new MountItem($iteminfo);
				break;
			case ItemDef::ITEM_ELEMENT:
				$item = new ElementItem($iteminfo);
				break;
				// case ItemDef::ITEM_DEMON:
					// $item = new DemonItem($iteminfo);
					// break;				
			default:
				Logger::FATAL('Invalid item type=%d', $item_type);
				throw new Exception('fake');
				break;
		}
		return $item;
	}

	/**
	 *
	 * 得到物品的信息
	 *
	 * @param int $item_id
	 *
	 * @return array		物品的具体信息
	 */
	public function itemInfo($item_id)
	{
		$item = $this->getItem($item_id);
		if ( $item === NULL )
		{
			return array();
		}
		else
		{
			return $item->itemInfo();
		}
	}

	/**
	 *
	 * 得到物品的信息
	 * @param array(int) $item_ids
	 *
	 * @return array		物品的具体信息
	 */
	public function itemInfos($item_ids)
	{
		$return = array();
		foreach ( $item_ids as $item_id )
		{
			$itemInfo = $this->itemInfo($item_id);
			if ( !empty($itemInfo) )
			{
				$return[$item_id] = $itemInfo;
			}
		}
		return $return;
	}


	/**
	 *
	 * 掉落物品
	 * @param int $drop_template_id			掉落物品表模板ID
	 *
	 * @return array(int)		掉落的物品的IDs
	 */
	public function dropItem($drop_template_id)
	{
		$array = array();
		$items = Drop::dropItem($drop_template_id);
		foreach ($items as $item)
		{
			$array = array_merge($array, $this->addItem($item[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID], $item[ItemDef::ITEM_ATTR_NAME_ITEM_NUM]));
		}
		Logger::DEBUG('ItemManager:dropItem drop_template_id:%d, items:%s', $drop_template_id, $array);
		return $array;
	}

	/**
	 *
	 * 掉落物品
	 * @param array(int) $drop_template_ids			掉落物品表模板ID array
	 *
	 * @return array(int)		掉落的物品的IDs
	 */
	public function dropItems($drop_template_ids)
	{
		$array = array();
		foreach ($drop_template_ids as $drop_template_id)
		{
			$items = self::getInstance()->dropItem($drop_template_id);
			$array = array_merge($array, $items);
		}
		return $array;
	}

	/**
	 *
	 * 增加物品
	 * @param array $item_templates			物品模板
	 * <code>
	 * {
	 * 		item_template_id:item_number
	 * }
	 * </code>
	 *
	 * @return array(int) item_ids			物品ID列表
	 */
	public function addItems($item_templates)
	{
		$item_ids = array();
		foreach ( $item_templates as $item_template_id => $item_num )
		{
			$item_ids = array_merge($item_ids, self::addItem($item_template_id, $item_num));
		}
		return $item_ids;
	}

	/**
	 *
	 * 增加物品
	 * @param int $item_template_id			物品模板ID
	 * @param int $item_num					物品数量
	 *
	 * @return array(int) item_ids			物品ID列表
	 */
	public function addItem($item_template_id, $item_num = 1)
	{
		$stackable = ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_STACKABLE);
		$item_ids = array();
		//物品不可叠加
		if ( $stackable == ItemDef::ITEM_CAN_NOT_STACKABLE )
		{
			for ( $i = 0; $i < $item_num; $i++ )
			{
				$attrs = array();
				switch ( ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_TYPE) )
				{
					case ItemDef::ITEM_ARM:
						$attrs = ArmItem::createItem($item_template_id);
						break;
					case ItemDef::ITEM_GEM:
						$attrs = GemItem::createItem($item_template_id);
						break;
					case ItemDef::ITEM_JEWELRY:
						$attrs = JewelryItem::createItem($item_template_id);
						break;
					case ItemDef::ITEM_FASHION_DRESS:
						$attrs = FashionDressItem::createItem($item_template_id);
						DressLogic::addDressRoom($item_template_id);
						break;
					case ItemDef::ITEM_DAIMONAPPLE:
						$attrs = DaimonAppleItem::createItem($item_template_id);
						break;
					case ItemDef::ITEM_ELEMENT:
						$attrs = ElementItem::createItem($item_template_id);
						break;						
					default:
						break;
				}
				$item_id = $this->__addItem($item_template_id, 1, $attrs);
				$item_ids[] = $item_id;
			}
		}
		else
		{
			//可叠加的物品应该没有任何的特化属性
			for ( $i = 0; $i < intval($item_num/$stackable); $i++ )
			{
				$item_ids[] = $this->__addItem($item_template_id, $stackable);
			}

			if ( $item_num % $stackable != 0 )
			{
				$item_ids[] = $this->__addItem($item_template_id, $item_num % $stackable);
			}
		}
		return $item_ids;
	}

	private function __addItem($item_template_id, $item_num, $item_text = array())
	{
		if ( !is_array($item_text) )
		{
			throw new Exception('ItemStore::addItem item_text is not array!');
		}

		$item_id = IdGenerator::nextId(ItemDef::ITEM_SQL_ITEM_ID);
		$values = array();
		$values[ItemDef::ITEM_SQL_ITEM_ID] = $item_id;
		$values[ItemDef::ITEM_SQL_ITEM_TEMPLATE_ID] = $item_template_id;
		$values[ItemDef::ITEM_SQL_ITEM_NUM] = $item_num;
		$values[ItemDef::ITEM_SQL_ITEM_TIME] = Util::getTime();
		$values[ItemDef::ITEM_SQL_ITEM_TEXT] = $item_text;
		$values[ItemDef::ITEM_SQL_ITEM_DELETED] = 0;
		$item = $this->__getItem($values);
		$this->m_items[$item_id] = $item;
		return $item_id;
	}

	/**
	 *
	 * 减少物品
	 * @param int $item_id
	 * @param int $item_number
	 *
	 * @return boolean
	 */
	public function decreaseItem($item_id, $item_number)
	{
		$item = $this->getItem($item_id);
		if ( $item === NULL )
		{
			return FALSE;
		}
		$stackable = ItemAttr::getItemAttr($item->getItemTemplateID(), ItemDef::ITEM_ATTR_NAME_STACKABLE);
		if ( $stackable == ItemDef::ITEM_CAN_NOT_STACKABLE )
		{
			if ( $item_number != ItemDef::ITEM_CAN_NOT_STACKABLE )
			{
				return FALSE;
			}
			else
			{
				return $this->deleteItem($item_id);
			}
		}
		else
		{
			$number = $item->getItemNum();
			if ( $number < $item_number )
			{
				return FALSE;
			}
			else if ( $item_number == $number )
			{
				return $this->deleteItem($item_id);
			}
			else
			{
				$item->setItemNum($number - $item_number);
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 *
	 * 物品使用
	 * @param int $item_id		物品ID
	 * @param int $number		物品数量
	 *
	 * @return mixed
	 */
	public function useItem($item_id, $number)
	{

	}

	/**
	 *
	 * 合并物品
	 *
	 * @param int $src_item_id
	 * @param int $des_item_id
	 *
	 * @return boolean
	 */
	public function unionItem($src_item_id, $des_item_id)
	{
		//两个位置都必须有物品才可以合并,否则应该是交换操作
		if ( empty($src_item_id) || empty($des_item_id) )
		{
			return FALSE;
		}

		$src_item = $this->getItem($src_item_id);
		$des_item = $this->getItem($des_item_id);

		//只有同种物品才可以合并
		if ( $src_item->getItemTemplateID() != $des_item->getItemTemplateID() )
		{
			return FALSE;
		}

		$max_superimposed_num = $src_item->getStackable();

		Logger::DEBUG('ItemManager::unionItem src_item_id:%s, des_item_id:%d.', $src_item_id, $des_item_id);

		if ( $src_item->getItemNum() + $des_item->getItemNum() > $max_superimposed_num )
		{
			$item_num  = $src_item->getItemNum() + $des_item->getItemNum() - $max_superimposed_num;
			$des_item->setItemNum($max_superimposed_num);
			$src_item->setItemNum($item_num);
		}
		else
		{
			$des_item->setItemNum($src_item->getItemNum() + $des_item->getItemNum());
			$this->deleteItem($src_item_id);
		}
		return TRUE;
	}

	/**
	 *
	 * 摧毁物品
	 * @param int $item_id
	 *
	 * @return boolean					TRUE 表示摧毁成功, FALSE表示摧毁失败
	 */
	public function destoryItem($item_id)
	{
		$item = $this->getItem($item_id);
		if ( empty($item) )
		{
			return TRUE;
		}

		if ( $item->canDestory() == TRUE )
		{
			$this->deleteItem($item_id);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 *
	 * 删除物品
	 * @param int $item_id
	 *
	 * @return boolean					TRUE 表示删除成功, FALSE表示删除失败
	 */
	public function deleteItem($item_id)
	{
		unset($this->m_items[$item_id]);
		$this->m_items[$item_id] = NULL;
		return TRUE;
	}

	/**
	 *
	 * 拆分物品
	 * @param int $item_id
	 * @param int $item_num
	 *
	 * @return int $item_id
	 */
	public function splitItem($item_id, $item_num)
	{
		$item = $this->getItem($item_id);
		$o_item_num = $item->getItemNum();
		if ( $o_item_num < $item_num )
		{
			return ItemDef::ITEM_ID_NO_ITEM;
		}
		else if ( $o_item_num == $item_num )
		{
			return $item_id;
		}
		else
		{
			$n_item_id = ItemDef::ITEM_ID_NO_ITEM;
			$n_item_ids = $this->addItem($item->getItemTemplateID(), $item_num);
			if ( count($n_item_ids) != 1 )
			{
				Logger::FATAL('split item failed!item_id:%d, item_template_id:%d', $item_id,
					$item->getItemTemplateID());
				return ItemDef::ITEM_ID_NO_ITEM;
			}
			else
			{
				$n_item_id = $n_item_ids[0];
			}
			$item->setItemNum($o_item_num - $item_num);
			Logger::DEBUG('split new item:%d!', $n_item_id);
			return $n_item_id;
		}
	}

	/**
	 *
	 * 得到物品的最大叠加数量
	 *
	 * @param int $item_template_id
	 *
	 * @return int
	 */
	public function getItemStackable($item_template_id)
	{
		return ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_STACKABLE);
	}

	/**
	 *
	 * 得到物品的类型
	 *
	 * @param int $item_template_id
	 *
	 * @return int
	 */
	public function getItemType($item_template_id)
	{
		return ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_TYPE);
	}

	/**
	 *
	 * 得到物品的品质
	 *
	 * @param int $item_template_id
	 *
	 * @return int
	 */
	public function getItemQuality($item_template_id)
	{
		return ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_QUALITY);
	}

	/**
	 *
	 * 得到装备的类型
	 *
	 * @param int $item_template_id
	 *
	 * @return int
	 */
	public function getArmItemType($item_template_id)
	{
		return ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_ARM_TYPE);
	}

	/**
	 *
	 * 判断一个物品是否是任务物品
	 *
	 * @param int $item_template_id
	 *
	 * @return boolean
	 */
	public function isMissionItem($item_template_id)
	{
		return ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_TYPE) == ItemDef::ITEM_MISSION;
	}

	/**
	 *
	 * 得到物品模板信息
	 *
	 * @param array(int) $item_ids
	 *
	 * @return array
	 * <code>
	 * [
	 * 		item_template_id:item_num
	 * ]
	 * </code>
	 */
	public function getTemplateInfoByItemIds($item_ids)
	{
		$return = array();
		foreach ( $item_ids as $item_id )
		{
			$item = $this->getItem($item_id);
			$item_template_id = $item->getItemTemplateId();
			$item_num = $item->getItemNum();
			if ( isset($return[$item_template_id]) )
			{
				$return[$item_template_id] += $item_num;
			}
			else
			{
				$return[$item_template_id] = $item_num;
			}
		}
		return $return;
	}

	public function rollback()
	{
		$this->m_items = unserialize($this->m_original_items);
	}

	public function update()
	{
		foreach ( $this->m_items as $item_id => $item )
		{
			if ( !isset($this->m_original_items[$item_id]) && !empty($item) )
			{
				ItemStore::addItem($item->getItemID(), $item->getItemTemplateID(), $item->getItemTime(),
					$item->getItemNum(), $item->getItemText());
				$this->m_original_items[$item_id] = serialize($this->m_items[$item_id]);
			}
			else if ( isset($this->m_original_items[$item_id]) && empty($item) )
			{
				ItemStore::deleteItem($item_id);
				unset($this->m_original_items[$item_id]);
			}
			else if ( !isset($this->m_original_items[$item_id]) && empty($item))
			{

			}
			else
			{
				$values = array();
				$o_item = unserialize($this->m_original_items[$item_id]);
				if ( $item->getItemNum() != $o_item->getItemNum() )
				{
					$values[ItemDef::ITEM_SQL_ITEM_NUM] = $item->getItemNum();
				}
				if ( serialize($item->getItemText()) !=
					 serialize($o_item->getItemText()) )
				{
					$values[ItemDef::ITEM_SQL_ITEM_TEXT] = $item->getItemText();
				}
				if (!empty($values))
				{
					ItemStore::updateItem($item_id, $values);
					$this->m_original_items[$item_id] = serialize($this->m_items[$item_id]);
				}
			}
		}
	}

	/*
	 * 为探索那块提供接口,输入模版id，返回品质、卖出类型、卖出价格、基本经验
	 * 如果出错，则返回空array
	 */
	public function getExploreInfo($item_template_id)
	{
		$return=array();
		//是不是宝石
		$item_type = ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_TYPE);
		if ($item_type==ItemDef::ITEM_GEM)
		{
			$return['isgem']		=1;
			$return['quality'] 		= ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_QUALITY);//品质
			$return['sell_type'] 	= ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_SELL_TYPE);//卖出类型
			$return['sell_price'] 	= ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_SELL_PRICE);//卖出对应的数值（现在应该是贝里）
			$return['exp'] 			= ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_EXP);//基础经验值
		}
		else
		{
			$return['isgem']		=0;
			$return['quality'] 		= ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_QUALITY);//品质
			$return['sell_type'] 	= ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_SELL_TYPE);//卖出类型
			$return['sell_price'] 	= ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_SELL_PRICE);//卖出对应的数值（现在应该是贝里）
			$return['exp'] 			= 0;//基础经验值
		}
		return $return;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */