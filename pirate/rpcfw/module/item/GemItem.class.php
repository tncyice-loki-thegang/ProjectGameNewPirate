<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GemItem.class.php 33560 2012-12-21 07:04:55Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/GemItem.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2012-12-21 15:04:55 +0800 (五, 2012-12-21) $
 * @version $Revision: 33560 $
 * @brief
 *
 **/

class GemItem extends Item
{
	public function info()
	{
		$gem_attr = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_GEM_ATTR);
		$gem_attr = $gem_attr->toArray();
		$gem_attr_reinforce = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_GEM_ATTR_REINFORCE);
		$gem_attr_reinforce = $gem_attr_reinforce->toArray();
		$level = $this->getLevel();
		// $printLevel = $this->getPrintLevel();
		foreach ( $gem_attr as $attr_id => $attr_value )
		{
			$gem_attr[$attr_id] = $attr_value + $gem_attr_reinforce[$attr_id] * ($level - ItemDef::ITEM_GEM_MIN_LEVEL);
		}
		return $gem_attr;
	}

	/**
	 *
	 * 得到宝石的当前等级
	 *
	 * @throws Exception
	 *
	 * @return int
	 */
	public function getLevel()
	{
		$cur_exp = $this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP];
		if ( empty($cur_exp) )
		{
			return ItemDef::ITEM_GEM_MIN_LEVEL;
		}
		else
		{
			$exp_table_id = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_GEM_LEVEL_TABLE);
			if ( !isset(btstore_get()->EXP_TBL[$exp_table_id]) )
			{
				Logger::FATAL('invalid exp table id:%d', $exp_table_id);
				throw new Exception('config');
			}
			$exp_table = btstore_get()->EXP_TBL[$exp_table_id];

			$level = ItemDef::ITEM_GEM_MIN_LEVEL;
			for ( $i = ItemDef::ITEM_GEM_MIN_LEVEL+1; $i <= $this->getMaxLevel(); $i++ )
			{
				if ( $cur_exp < $exp_table[$i] )
				{
					break;
				}
				else
				{
					$cur_exp -= $exp_table[$i];
					$level++;
				}
			}
			return $level;
		}
	}
	/**
	 *
	 * 得到升到下一级所需经验
	 *
	 * @throws Exception
	 *
	 * @return int
	 */
	public function getNextLevelExp()
	{
		$curlevel=$this->getLevel();
		if ($curlevel >= $this->getMaxLevel())
		{
			return -1;
		}
		
		$exp_table_id = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_GEM_LEVEL_TABLE);
		if ( !isset(btstore_get()->EXP_TBL[$exp_table_id]) )
		{
			Logger::FATAL('getNextLevelExp: invalid exp table id:%d', $exp_table_id);
			throw new Exception('fake');
		}
		$exp_table = btstore_get()->EXP_TBL[$exp_table_id];
		
		$newlevel = $curlevel+1;
		if (!isset($exp_table[$newlevel]))
		{
			Logger::FATAL('getNextLevelExp: invalid exp table level:%d', $newlevel);
			throw new Exception('fake');
		}
		$cur_exp = $this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP];
		
		//如果当前经验为空，则直接读经验表
		if ( empty($cur_exp) )
		{
			return $exp_table[$newlevel];
		}
		
		//否则，需要减去当前已经有的经验
		$excessexp=$cur_exp;
		for ( $i = ItemDef::ITEM_GEM_MIN_LEVEL+1; $i <= $curlevel; $i++ )
		{
				$excessexp -= $exp_table[$i];
		}
		if ($excessexp < 0||$excessexp > $exp_table[$newlevel])
		{
			Logger::FATAL('getNextLevelExp: invalid levelexp  curlevel:%d excessexp:%d curexp:%d needexp:%d',$curlevel, $excessexp,$cur_exp,$exp_table[$newlevel]);
			throw new Exception('fake');
		}
		Logger::info('costexp :%d excessexp:%d curlevel:%d item_template_id:%d item_id:%d',$exp_table[$newlevel],$excessexp,$curlevel,$this->m_item_template_id,$this->m_item_id);
		return $exp_table[$newlevel]-$excessexp;
	}

	/**
	 *
	 * 得到宝石的当前融合经验
	 *
	 * @return int
	 */
	public function getFuseExp()
	{
		$exp = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_EXP);
		return $this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP] + $exp;
	}

	/**
	 *
	 * 增加宝石的当前经验
	 *
	 * @param int $exp
	 *
	 * @return NULL
	 */
	public function addExp($exp)
	{
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP] += $exp;
	}

	/**
	 *
	 * 得到宝石的最大等级
	 *
	 * @return int
	 */
	public function getMaxLevel()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_GEM_MAX_LEVEL);
	}

	/**
	 *
	 * 得到镶嵌需求
	 *
	 * @return array
	 * <code>
	 * 		ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_BELLY:int
	 *  	ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_GOLD:int
	 * </code>
	 */
	public function getEnchaseReq()
	{
		return ItemAttr::getItemAttrs($this->m_item_template_id,
			array(ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_BELLY, ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_GOLD));
	}

	/**
	 *
	 * 得到摘除需求
	 *
	 * @return array
	 * <code>
	 * 		ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_BELLY:int
	 *  	ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_GOLD:int
	 * </code>
	 */
	public function getSplitReq()
	{
		return ItemAttr::getItemAttrs($this->m_item_template_id,
			array(ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_BELLY, ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_GOLD));
	}

	public static function createItem($item_template_id)
	{
		$item_text = array();
		//产生宝石经验
		$item_text[ItemDef::ITEM_ATTR_NAME_EXP] = 0;
		$item_text[ItemDef::ITEM_ATTR_NAME_GEM_PRINT_LEVEL] = 0;

		return $item_text;
	}
	
	public function getPrintLevel()
	{
		if (!isset($this->m_item_text[ItemDef::ITEM_ATTR_NAME_GEM_PRINT_LEVEL]))
		{
			$this->m_item_text[ItemDef::ITEM_ATTR_NAME_GEM_PRINT_LEVEL] = 0;
		}
		$this->setItemText($this->m_item_text);
		return $this->m_item_text[ItemDef::ITEM_ATTR_NAME_GEM_PRINT_LEVEL];
	}

	public function setPrintLevel($level)
	{		
		if (!isset($this->m_item_text[ItemDef::ITEM_ATTR_NAME_GEM_PRINT_LEVEL]))
		{
			$this->m_item_text[ItemDef::ITEM_ATTR_NAME_GEM_PRINT_LEVEL] = 0;
		}		
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_GEM_PRINT_LEVEL] = $level;
		$this->setItemText($this->m_item_text);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */