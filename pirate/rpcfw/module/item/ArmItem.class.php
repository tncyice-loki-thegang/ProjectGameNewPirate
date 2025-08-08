<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArmItem.class.php 35921 2013-01-15 06:45:01Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/ArmItem.class.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-01-15 14:45:01 +0800 (二, 2013-01-15) $
 * @version $Revision: 35921 $
 * @brief
 *
 **/




class ArmItem extends Item
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
	 * 得到装备的强化等级
	 *
	 * @return int
	 */
	public function getReinforceLevel()
	{		
		return $this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL];
	}

	public function getGildLevel()
	{
		return $this->m_item_text[ItemDef::ITEM_ATTR_NAME_GILD_LEVEL];
	}

	/**
	 *
	 * 得到装备强化增加的冷却时间
	 *
	 * @return int
	 */
	public function getReinforceIncTime()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_REINFORCE_INC_TIME);
	}

	/**
	 *
	 * 得到装备随机洗练的需求的belly
	 *
	 * @return int
	 */
	public function getRandRefreshReqBelly()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_RAND_REFRESH_BELLY);
	}

	/**
	 *
	 * 得到装备固定洗练的需求的belly
	 *
	 * @return int
	 */
	public function getFixedRefreshReqBelly()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_FIXED_REFRESH_BELLY);
	}

	/**
	 *
	 * 得到装备强化的需求
	 *
	 * @return array
	 */
	public function reinforceReq()
	{
		$fee_id = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_REINFORCE_FEE);
		$fee = btstore_get()->REINFORCEFEE[$fee_id];
		if ( empty ($fee) )
		{
			Logger::FATAL('invalid reinforce fee id:%d', $fee_id);
			throw new Exception('config');
		}
		return $fee;
	}
	
	public function gildReq()
	{
		$gildId = btstore_get()->ITEMS[$this->m_item_template_id][ItemDef::ITEM_ATTR_NAME_GILDING_ID];
		$info = btstore_get()->EQUIP_GILD_COST[$gildId];
		if ( empty ($info) )
		{
			Logger::FATAL('invalid gild Req id:%d', $gildId);
			throw new Exception('config');
		}
		return $info;
	}

	/**
	 *
	 * 得到装备需求
	 *
	 * @return array
	 */
	public function equipReq()
	{
		return ItemAttr::getItemAttrs($this->m_item_template_id,
			array(ItemDef::ITEM_ATTR_NAME_HERO_LEVEL, ItemDef::ITEM_ATTR_NAME_HERO_VOCATION,ItemDef::ITEM_ATTR_NAME_REBIRTH_NUM));
	}

	/**
	 *
	 * 得到装备的随机潜能id
	 *
	 * @return int
	 */
	public function getRandPotentialityId()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_RANDPOTENTIALITY);
	}

	/**
	 *
	 * 得到装备的兑换ID
	 */
	public function getArmExchangeId()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_EXCHANGE_ID);
	}

	/**
	 * (non-PHPdoc)
	 * @see Item::itemInfo()
	 */
	public function itemInfo()
	{
		$return = parent::itemInfo();
		if ( !empty($return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]) )
		{
			foreach ( $return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE] as
				$hold_id => $item_id )
			{
				if ( !empty($item_id) )
				{
					$item = ItemManager::getInstance()->getItem($item_id);
					if ( $item !== NULL )
					{
						$return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE][$hold_id] =
							$item->itemInfo();
					}
					else
					{
						Logger::FATAL('invalid gem item:%d in item_id:%d', $item_id, $this->m_item_id);
					}
				}
			}
		}

		//得到随机潜能id
		$potentiality_id = $this->getRandPotentialityId();

		if ( !empty($return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_POTENTIALITY]) )
		{
			foreach ( $return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_POTENTIALITY] as $attr_id => $attr_value )
			{
				$return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_POTENTIALITY][$attr_id]
					= self::getPotentialityValue($potentiality_id, $attr_id, $attr_value);
			}
		}
		if ( !empty($return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH]) )
		{
			foreach ( $return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH] as $attr_id => $attr_value )
			{
				$return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH][$attr_id]
					= self::getPotentialityValue($potentiality_id, $attr_id, $attr_value);
			}
		}
		if ( !empty($return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_POTENTIALITY_RAND_REFRESH]) )
		{
			foreach ( $return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_POTENTIALITY_RAND_REFRESH] as $attr_id => $attr_value )
			{
				$return[ItemDef::ITEM_SQL_ITEM_TEXT][ItemDef::ITEM_ATTR_NAME_POTENTIALITY_RAND_REFRESH][$attr_id]
					= self::getPotentialityValue($potentiality_id, $attr_id, $attr_value);
			}
		}
		return $return;
	}

	public function info()
	{
		$array = array();
		//计算武器本身的数值
		foreach ( ItemDef::$ITEM_ARM_ATTRS_CALC as $key => $value)
		{
			$array[$key] = ItemAttr::getItemAttr($this->m_item_template_id, $value[0]) +
				( $this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] ) *
				ItemAttr::getItemAttr($this->m_item_template_id, $value[1]) +
			( $this->m_item_text[ItemDef::ITEM_ATTR_NAME_GILD_LEVEL] ) *
			(ItemAttr::getItemAttr($this->m_item_template_id, $value[0]));
			
		}
		
		foreach ( ItemDef::$ITEM_ARM_ATTRS_NO_CALC as $key )
		{
			$array[$key] = ItemAttr::getItemAttr($this->m_item_template_id, $key);
		}

		Logger::DEBUG('arm:%d template_id:%d basic numerical:%s',
				$this->m_item_id, $this->m_item_template_id, $array);

		//计算潜能
		//计算固定潜能
		$fixed_potentiality_id = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_FIXED_POTENTIALITY);
		if ( $fixed_potentiality_id != 0 )
		{
			$attrs = Potentiality::fixedPotentiality($fixed_potentiality_id);
			foreach ( $attrs as $attr_id => $attr_value )
			{
				$attr_name = ItemDef::$ITEM_ATTR_IDS[$attr_id];
				if ( isset($array[$attr_name]) )
				{
					$array[$attr_name] += $attr_value;
				}
				else
				{
					$array[$attr_name] = $attr_value;
				}
			}
			Logger::DEBUG('arm:%d template_id:%d basic and fixed potentiality numerical:%s',
					$this->m_item_id, $this->m_item_template_id, $array);
		}
		//计算随机潜能
		else
		{
			if ( isset( $this->m_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY]) )
			{
				//得到随机潜能id
				$potentiality_id = $this->getRandPotentialityId();
				foreach ( $this->m_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY] as $attr_id => $attr_value )
				{
					$attr_name = ItemDef::$ITEM_ATTR_IDS[$attr_id];
					if ( !isset($array[$attr_name]) )
					{
						$array[$attr_name] = 0;
					}
					$array[$attr_name] += self::getPotentialityValue($potentiality_id, $attr_id, $attr_value);
				}
			}
			Logger::DEBUG('arm:%d template_id:%d basic and rand potentiality numerical:%s',
					$this->m_item_id, $this->m_item_template_id, $array);
		}
		//计算宝石
		if ( isset($this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]) )
		{
			foreach ( $this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE] as $hole_id => $gem_item_id )
			{
				if ( !empty($gem_item_id) )
				{
					$gem_item = ItemManager::getInstance()->getItem($gem_item_id);
					//如果宝石不存在,则在某次出现了错误
					if ( $gem_item === NULL )
					{
						Logger::FATAL('gem_item:%d not exist in arm_item:%d!', $gem_item_id, $this->m_item_id);
					}
					else
					{
						$attrs = $gem_item->info();
						foreach ( $attrs as $attr_id => $attr_value )
						{
							$attr_name = ItemDef::$ITEM_ATTR_IDS[$attr_id];
							if ( isset($array[$attr_name]) )
							{
								$array[$attr_name] += $attr_value;
							}
							else
							{
								$array[$attr_name] = $attr_value;
							}
						}
					}
				}
			}
		}
		Logger::DEBUG('arm:%d template_id:%d basic and potentiality and gem numerical:%s',
					$this->m_item_id, $this->m_item_template_id, $array);
		return $array;
	}

	public function setReinforceLevel($level)
	{
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] = $level;
		$this->setItemText($this->m_item_text);
	}
	
	public function setGildLevel($level)
	{
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_GILD_LEVEL] = $level;
		$this->setItemText($this->m_item_text);
	}

	
	/**
	 *
	 * 物品强化
	 * @param int $reinfore_upper_limit		强化等级上限
	 *
	 * @return boolean						TRUE表示强化成功, FALSE表示强化失败
	 */
	public function reinforce($reinfore_upper_limit)
	{
		if ( $this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] == $reinfore_upper_limit )
		{
			return FALSE;
		}
		else if ( $this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] > $reinfore_upper_limit )
		{
			Logger::FATAL('Item level is invalid:item_id:%d, item_template_id:%d, level:%d',
					$this->m_item_id, $this->m_item_template_id,
					$this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL]);
			return FALSE;
		}
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL]++;
		$this->setItemText($this->m_item_text);
		return TRUE;
	}

	/**
	 *
	 * 弱化物品
	 *
	 * @param int $level					弱化等级数
	 *
	 * @return boolean						TRUE表示弱化成功, FALSE表示弱化失败
	 */
	public function weakening($level)
	{
		if ( $level < 0 )
		{
			return FALSE;
		}

		if ( $this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] == ItemDef::ITEM_REINFORCE_LEVEL_DEFAULT )
		{
			return FALSE;
		}
		else if ( $this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] - $level < ItemDef::ITEM_REINFORCE_LEVEL_DEFAULT )
		{
			Logger::FATAL('Item level is invalid:item_id:%d, item_template_id:%d, level:%d',
					$this->m_item_id, $this->m_item_template_id,
					$this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL]);
			return FALSE;
		}
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] -= $level;
		$this->setItemText($this->m_item_text);
		return TRUE;
	}

	/**
	 *
	 * 是否可以随机洗潜能
	 *
	 * @return boolean						TRUE表示可以随机洗潜能,FALSE表示不能
	 */
	public function canRandomRefresh()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_REFRESH_RANDPOTENTIALITY_ENABLE)
			== ItemDef::ITEM_CAN_RANDOM_REFRESH_POTENTIALITY;
	}

	/**
	 *
	 * 是否可以固定洗潜能
	 *
	 * @return boolean						TRUE表示可以固定洗潜能,FALSE表示不能
	 */
	public function canFixedRefresh()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_REFRESH_FIXEDPOTENTIALITY_ENABLE)
			== ItemDef::ITEM_CAN_FIXED_REFRESH_POTENTIALITY;
	}

	/**
	 *
	 * 洗练
	 * @param boolean $fixed				是否固定洗练
	 * @param int $type						洗练方式
	 *
	 * @return array
	 */
	private function refresh($type, $fixed = FALSE)
	{
		//得到随机潜能id
		$potentiality_id = $this->getRandPotentialityId();

		//随机洗属性
		if ( $fixed == FALSE && $this->canRandomRefresh() == TRUE )
		{
			$potentialitys = Potentiality::randPotentiality($potentiality_id);
			$this->m_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_RAND_REFRESH] = $potentialitys;
			$this->m_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH] =
				$this->m_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY];
			$return = $this->m_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_RAND_REFRESH];
		}
		else if ( $fixed == TRUE && $this->canFixedRefresh() == TRUE )
		{
			$original = $this->m_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY];
			$potentialitys = Potentiality::refreshPotentiality($potentiality_id, $original, $type);
			Logger::DEBUG('fixed refresh potentiality:%s', $potentialitys);
			$this->m_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH] = $potentialitys;
			$return = $this->m_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH];
		}
		else
		{
			Logger::Debug('item_template_id:%d can not refresh!', $this->m_item_template_id);
			throw new Exception('invalid attr_id!');
		}
		$this->setItemText($this->m_item_text);
		foreach ( $return as $attr_id => $attr_value )
		{
			$return[$attr_id] = self::getPotentialityValue($potentiality_id, $attr_id, $attr_value);
		}
		return $return;
	}

	/**
	 *
	 * 随机洗属性
	 *
	 * @param int $specail				是否使用金币洗练
	 *
	 * @return array
	 */
	public function randRefresh($special = FALSE)
	{
		return $this->refresh($special);
	}

	/**
	 *
	 * 固定洗属性
	 *
	 * @param int $type				固定洗练方式
	 *
	 * @return array
	 */
	public function fixedRefresh($type)
	{
		return $this->refresh($type, TRUE);
	}

	/**
	 *
	 * 属性替换
	 *
	 * @return boolean
	 */
	private function refreshAffirm($attr_name)
	{
		if ( !isset($this->m_item_text[$attr_name]) )
		{
			return FALSE;
		}

		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY] = $this->m_item_text[$attr_name];

		unset($this->m_item_text[$attr_name]);
		if ( $attr_name == ItemDef::ITEM_ATTR_NAME_POTENTIALITY_RAND_REFRESH )
		{
			unset($this->m_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH]);
		}
		$this->setItemText($this->m_item_text);
		return TRUE;
	}

	public function fixedRefreshAffirm()
	{
		return $this->refreshAffirm(ItemDef::ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH);
	}

	public function randRefreshAffirm()
	{
		return $this->refreshAffirm(ItemDef::ITEM_ATTR_NAME_POTENTIALITY_RAND_REFRESH);
	}

	/**
	 *
	 * 判断是否可以镶嵌
	 * @param int $hole_id
	 *
	 * @return boolean					TRUE表示可以镶嵌,FALSE表示不可以镶嵌
	 */
	public function canEnchase($hole_id)
	{
		$enchase_req = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ);
		if ( !isset($enchase_req[$hole_id]) ||
			$enchase_req[$hole_id] > $this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] )
		{
			Logger::DEBUG('invalid hole_id!');
			return FALSE;
		}
		//如果当前孔中有宝石,则不可以镶嵌
		else if ( !empty($this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]) &&
			!empty($this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE][$hole_id]) )
		{
			$gem_item_id = $this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE][$hole_id];
			$gem_item = ItemManager::getInstance()->getItem($gem_item_id);
			if ( $gem_item === NULL )
			{
				Logger::FATAL('arm item_id:%d gem item_id:%d is not exist!', $this->m_item_id,
					$gem_item_id);
				return TRUE;
			}
			else
			{
				Logger::DEBUG('gem in the hole!');
				return FALSE;
			}
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 *
	 * 镶嵌
	 *
	 * @param int $item_id		物品模板ID
	 * @param int $hole_id		镶嵌孔ID
	 *
	 * @return boolean
	 */
	public function enchase($item_id, $hole_id)
	{
		$item = ItemManager::getInstance()->getItem($item_id);
		$item_template_id = $item->getItemTemplateId();
		if ( ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_TYPE) !=
			ItemDef::ITEM_GEM )
		{
			Logger::DEBUG('invalid item_id:%d, need a gem!', $item_id);
			return FALSE;
		}
		if ( $this->canEnchase($hole_id) == FALSE )
		{
			return FALSE;
		}
		$gem_arm_type = ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_GEM_ARM_TYPE)->toArray();
		if ( in_array($this->getArmType(), $gem_arm_type) ||
			in_array(ItemDef::ITEM_GEM_ALL_ARM_TYPE, $gem_arm_type) )
		{
			if ( !isset($this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]) )
			{
				$this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE] = array();
			}
			$this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE][$hole_id] = $item_id;
			$this->setItemText($this->m_item_text);
			return TRUE;
		}
		else
		{
			Logger::debug('invalid gem type!');
			return FALSE;
		}
	}

	/**
	 *
	 * 插入多个宝石
	 *
	 * @param array(int) $item_ids
	 *
	 * @return boolean
	 */
	public function enchaseGems($item_ids)
	{
		$enchase_req = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ);
		if ( isset($this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]) )
		{
			$arm_enchase = $this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE];
		}
		else
		{
			$arm_enchase = array();
		}

		foreach ( $item_ids as $item_id )
		{
			$can_enchase = FALSE;
			//检查传入的ID是否为空
			if ( $item_id == ItemDef::ITEM_ID_NO_ITEM )
			{
				continue;
			}
			//需要插入的是否为宝石
			$gem_item = ItemManager::getInstance()->getItem($item_id);
			if ( $gem_item === NULL || $gem_item->getItemType() != ItemDef::ITEM_GEM )
			{
				return FALSE;
			}
			foreach ( $enchase_req as $hole_id => $req_reinforce_level )
			{
				if ( $req_reinforce_level <= $this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] )
				{
					if ( empty($arm_enchase[$hole_id]) )
					{
						$arm_enchase[$hole_id] = $item_id;
						$can_enchase = TRUE;
						break;
					}
				}
			}
			if ( $can_enchase == FALSE )
			{
				return FALSE;
			}
		}
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE] = $arm_enchase;
		$this->setItemText($this->m_item_text);
		return TRUE;
	}

	public function clearAllGem()
	{
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE] = array();
		$this->setItemText($this->m_item_text);
	}

	/**
	 *
	 * 是否没有镶嵌宝石
	 *
	 * @return boolean			TRUE表示没有镶嵌, FALSE表示有镶嵌
	 */
	public function noEnchase()
	{
		if ( !isset($this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]) )
		{
			return TRUE;
		}
		else
		{
			$gem_item_ids = array_values($this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]);
			$gem_items = ItemManager::getInstance()->getItems($gem_item_ids);
			foreach ( $this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE] as $value )
			{
				if ( !empty($value) || !empty($gem_items[$value]) )
				{
					return FALSE;
				}
			}
			return TRUE;
		}
	}

	/**
	 *
	 * 得到镶嵌的宝石列表
	 *
	 * @return array
	 * <code>
	 * [
	 * 		hole_id:item_id					镶嵌孔ID：宝石ID
	 * ]
	 * </code>
	 */
	public function getGemItems()
	{
		$enchase_req = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ);
		if ( isset($this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]) )
		{
			$arm_enchase = $this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE];
		}
		else
		{
			$arm_enchase = array();
		}
		foreach ( $enchase_req as $hole_id => $req_reinforce_level )
		{
			if ( $req_reinforce_level <= $this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] )
			{
				if ( !isset($arm_enchase[$hole_id]) )
				{
					$arm_enchase[$hole_id] = ItemDef::ITEM_ID_NO_ITEM;
				}
			}
		}
		return $arm_enchase;
	}

	/**
	 *
	 * 摘除
	 *
	 * @param int $hole_id					镶嵌孔ID
	 *
	 * @return int $item_id					宝石ID
	 */
	public function split($hole_id)
	{
		if ( isset($this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]) &&
			isset($this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE][$hole_id]) )
		{
			$gem_item_id = $this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE][$hole_id];
			unset($this->m_item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE][$hole_id]);
			$this->setItemText($this->m_item_text);
			return $gem_item_id;
		}
		else
		{
			return ItemDef::ITEM_ID_NO_ITEM;
		}
	}

	/**
	 *
	 * 得到物品的出售信息
	 *
	 * @return array		sell_pirce表示出售的价格, sell_type表示出售的类型
	 */
	public function sellInfo()
	{
		$return = parent::sellInfo();
		if ( $return['sell_type'] != TradeDef::TRADE_SELL_TYPE_BELLY )
		{
			Logger::FATAL("arm item sell type is %d, but need %d",
				$return['sell_type'], TradeDef::TRADE_SELL_TYPE_BELLY);
			throw new Exception('fake');
		}
		//得到装备的当前强化等级
		$reinforceLevel = $this->getReinforceLevel();

		//得到物品强化信息
		$reinforceInfo = $this->reinforceReq();
		//计算所应该加的Belly数量
		$belly = 0;
		for ( $i = $reinforceLevel-1; $i >= 0; $i-- )
		{
			$belly += $reinforceInfo[$reinforceLevel - $i][ItemDef::REINFORCE_FEE_BELLY]
					* ForgeConfig::ARM_WEAKING_RECOVERY_PERCENT;
		}
		$return['sell_price'] += intval($belly);
		return $return;
	}

	/**
	 *
	 * 得到潜能的值(数据库中储存的是该潜能属性的价值)
	 *
	 * @param int $potentiality_id
	 * @param int $attr_id
	 * @param int $attr_value
	 *
	 */
	private static function getPotentialityValue($potentiality_id, $attr_id, $attr_value)
	{
		$value = floor($attr_value / Potentiality::getPotentialityAttrValue($potentiality_id, $attr_id));
		return $value <= 1 ? 1 : $value;
	}

	/**
	 *
	 * 产生物品
	 *
	 * @param int $item_template_id		物品模板ID
	 *
	 * @return attrs 物品模板所指定的随机属性
	 */
	public static function createItem($item_template_id)
	{
		$item_text = array();
		
		// 产生潜能属性
		$potentiality_id = ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_RANDPOTENTIALITY);
		if ( $potentiality_id != ItemDef::ITEM_INVALID_POTENTIALITY_ID )
		{
			$potentialitys = Potentiality::randPotentiality($potentiality_id);
			$item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY] = $potentialitys;
		}

		// 初始化物品强化等级
		$init_reinforce_level = ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_INIT_REINFORCE_LEVEL);
		if ( !empty($init_reinforce_level) )
		{
			$item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] = $init_reinforce_level;
		}
		else
		{
			$item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] = ItemDef::ITEM_REINFORCE_LEVEL_DEFAULT;
		}
		
		$item_text[ItemDef::ITEM_ATTR_NAME_GILD_LEVEL] = ItemDef::ITEM_GILD_LEVEL_DEFAULT;
		
		return $item_text;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */