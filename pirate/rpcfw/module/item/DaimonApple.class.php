<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: DaimonApple.class.php 15757 2012-03-06 06:55:26Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/DaimonApple.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-03-06 14:55:26 +0800 (二, 2012-03-06) $
 * @version $Revision: 15757 $
 * @brief
 *
 **/

class DaimonAppleItem extends Item
{

	public function itemInfo()
	{
		return parent::itemInfo();
	}

	public function info()
	{
		$return = array();
		
		$dmApple_attr_reinforce = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_REINFORCE);
		$dmApple_attr_reinforce = $dmApple_attr_reinforce -> toArray();
		$level = $this->getLevel();
		foreach ( $this->getAttrs() as $attr_id => $attr_value )
		{
			$attr_name = ItemDef::$ITEM_ATTR_IDS[$attr_id];
			if ( isset($return[$attr_name]) )
			{
				$return[$attr_name] += $attr_value + $dmApple_attr_reinforce[$attr_id] * $level;
			}
			else
			{
				$return[$attr_name] = $attr_value + $dmApple_attr_reinforce[$attr_id] * $level;
			}
		}

		Logger::DEBUG('daimonapple:%d template_id:%d basic numerical:%s',
					$this->m_item_id, $this->m_item_template_id, $return);
		
		return $return;
	}

	public function getLevel()
	{
		$level = ItemDef::ITEM_DAIMONAPPLE_MIN_LEVEL;
		$cur_exp = $this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP];
		if ( empty($cur_exp) )
		{
			return $level;
		}
		else
		{
			$exp_table_id = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_LEVEL_TABLE);
			if ( !isset(btstore_get()->EXPERIENCE_LEVEL_UP[$exp_table_id]) )
			{
				Logger::FATAL('invalid exp table id:%d', $exp_table_id);
				throw new Exception('config');
			}
			$exp_table = btstore_get()->EXPERIENCE_LEVEL_UP[$exp_table_id];			
			for ( $i = ItemDef::ITEM_DAIMONAPPLE_MIN_LEVEL+1; $i <= $this->getMaxLevel(); $i++ )
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
	
	public function getMaxLevel()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_MAX_LEVEL);
	}
	
	public function getMaxLevelExp()
	{
		$exp_table_id = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_LEVEL_TABLE);
		if ( !isset(btstore_get()->EXPERIENCE_LEVEL_UP[$exp_table_id]) )
		{
			Logger::FATAL('getMaxLevelExp: invalid exp table id:%d', $exp_table_id);
			throw new Exception('fake');
		}
		$exp_table = btstore_get()->EXPERIENCE_LEVEL_UP[$exp_table_id];
		$maxLv = self::getMaxLevel();
		$maxExp = 0;
		foreach ($exp_table as $lv => $expLv)
		{
			if ($lv <= $maxLv)
			{
				$maxExp += $expLv;
			} else break;			
		}
		return $maxExp;
	}

	public function getFuseExp()
	{
		$exp = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_EXP);
		return $this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP] + $exp;
	}

	public function getSkills()
	{
		$skill_array = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_SKILLS)->toArray();		
		$level = self::getLevel();
		if (!empty($skill_array))
		{
			if (is_numeric($skill_array[$level]))
			{
				return array($skill_array[$level]);
			} else return $skill_array[$level];			
		} else return array();
	}

	public function getDaimonAppleReq()
	{
		return array (
			ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_BELLY => ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_BELLY),
			ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_GOLD => ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_GOLD),
			ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_ITEMS => ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_ITEMS),
		);
	}

	public function canErasure()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE);
	}

	private function getAttrs()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTRS);
	}
	
	public static function createItem($item_template_id)
	{
		$item_text = array();
		$item_text[ItemDef::ITEM_DAIMONAPPLE_GILD_LEVEL] = 0;
		$item_text[ItemDef::ITEM_ATTR_NAME_EXP] = 0;
		
		return $item_text;
	}
	
	public function getExp()
	{
		return $this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP];
	}
	
	public function setExp($exp)
	{
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP] = $exp;
	}
	
	public function addExp($exp)
	{
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP] += $exp;
	}
	
	public function subExp($exp)
	{
		self::addExp(-$exp);
	}
	
	public function getGildLevel()
	{
		return $this->m_item_text[ItemDef::ITEM_DAIMONAPPLE_GILD_LEVEL];
	}

	public function setGildLevel($level)
	{
		$this->m_item_text[ItemDef::ITEM_DAIMONAPPLE_GILD_LEVEL] = $level;
		$this->setItemText($this->m_item_text);
	}

	public function returnExpKernel()
	{
		$cfg = btstore_get()->ITEMS[$this->m_item_template_id];
		$retKernel = 0;
		foreach ($cfg[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_FINE_COST] as $key => $val)
		{
			if (self::getGildLevel()>$key)
			{
				$retKernel += $val;
			}
		}		
		$uid = RPCContext::getInstance()->getUid();
		$curExpKernel = AppleFactoryLogic::getInfo($uid);
		AppleFactoryLogic::updateExpKernel($uid, $curExpKernel['apple_experience'] + self::getFuseExp(), $curExpKernel['demon_kernel'] + $retKernel);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
