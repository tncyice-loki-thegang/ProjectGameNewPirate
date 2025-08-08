<?php

class ElementItem extends Item
{
	public function getElementType()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_ELEMENT_TYPE);
	}
	
	public function equipReq()
	{
		return ItemAttr::getItemAttrs($this->m_item_template_id,
			array(ItemDef::ITEM_ATTR_NAME_HERO_LEVEL, ItemDef::ITEM_ATTR_NAME_REBIRTH_NUM));
	}
	
	public static function createItem($item_template_id)
	{
		$item_text[ItemDef::ITEM_ATTR_NAME_EXP] = 0;
		return $item_text;
	}
	
	public function getLevel()
	{
		$level = ItemDef::ITEM_ELEMENT_MIN_LEVEL;
		$cur_exp = $this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP];
		if ( empty($cur_exp) )
		{
			return $level;
		}
		else
		{
			$exp_table_id = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_ELEMENT_LEVEL_TABLE);
			if ( !isset(btstore_get()->EXP_TBL[$exp_table_id]) )
			{
				Logger::FATAL('invalid exp table id:%d', $exp_table_id);
				throw new Exception('config');
			}
			$exp_table = btstore_get()->EXP_TBL[$exp_table_id];			
			for ( $i = ItemDef::ITEM_ELEMENT_MIN_LEVEL+1; $i <= $this->getMaxLevel(); $i++ )
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
	
	private function getAttrs()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTRS);
	}
	
	public function getSkills()
	{
		$skill_array = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_ELEMENT_SKILLS)->toArray();		
		$level = self::getLevel()-1;
		if (!empty($skill_array))
		{
			if (is_numeric($skill_array[$level]))
			{
				return array($skill_array[$level]);
			} else return $skill_array[$level];			
		} else return array();
	}
	
	public function info()
	{
		$return = array();
		$element_attr_reinforce = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTR_REINFORCE);		
		$element_attr_reinforce = $element_attr_reinforce -> toArray();		
		$level = $this->getLevel();
		foreach ( $this->getAttrs() as $attr_id => $attr_value )
		{
			if ($attr_id==63) //没做新属性，先逃过
			{continue;}
			$attr_name = ItemDef::$ITEM_ATTR_IDS[$attr_id];
			if ( isset($return[$attr_name]) )
			{
				$return[$attr_name] += $attr_value + $element_attr_reinforce[$attr_id] * $level;
			}
			else
			{
				$return[$attr_name] = $attr_value + $element_attr_reinforce[$attr_id] * $level;
			}
		}
		return $return;
	}

	public function getNextLevelExp()
	{
		$curlevel=$this->getLevel();
		if ($curlevel >= $this->getMaxLevel())
		{
			return -1;
		}
		
		$exp_table_id = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_ELEMENT_LEVEL_TABLE);
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
		
		if ( empty($cur_exp) )
		{
			return $exp_table[$newlevel];
		}
		
		$excessexp = $cur_exp;
		for ( $i = ItemDef::ITEM_ELEMENT_MIN_LEVEL+1; $i <= $curlevel; $i++ )
		{
				$excessexp -= $exp_table[$i];
		}
		if ($excessexp < 0 || $excessexp > $exp_table[$newlevel])
		{
			Logger::FATAL('getNextLevelExp: invalid levelexp  curlevel:%d excessexp:%d curexp:%d needexp:%d',$curlevel, $excessexp,$cur_exp,$exp_table[$newlevel]);
			throw new Exception('fake');
		}
		Logger::info('costexp :%d excessexp:%d curlevel:%d item_template_id:%d item_id:%d',$exp_table[$newlevel],$excessexp,$curlevel,$this->m_item_template_id,$this->m_item_id);
		return $exp_table[$newlevel]-$excessexp;
	}

	public function getFuseExp()
	{
		$exp = ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_EXP);
		return $this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP] + $exp;
	}

	public function addExp($exp)
	{
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_EXP] += $exp;
	}

	public function getMaxLevel()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_NAME_ELEMENT_MAX_LEVEL);
	}
}