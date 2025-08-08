<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : SailboatProperty
 * Description : 主船属性类
 * Inherit     : 
 **********************************************************************************************************************/
class SailboatProperty
{
	private static $m_refitID;					// 图纸ID
	private static $m_equips;					// 装备信息
	private static $m_level;					// 当前等级
	private static $_instance = NULL;			// 单例实例
	
	private function __construct()
	{
	}

	private function resetInfo()
	{
		// 保存图纸ID
		self::$m_refitID = SailboatInfo::getInstance()->getCurBoatTemplate();
		// 获取装备信息
		self::$m_equips = SailboatLogic::getAllItemInfo();
		// 获取当前等级
		$userInfo = EnUser::getUser();
		self::$m_level = $userInfo['level'];
	}

	/**
	 * 获取本类唯一实例
	 */
	public static function getInstance()
	{
  		if (self::$_instance != null)
  		{
     		self::$_instance = new self();
  		}
  		return self::$_instance;
	}

	/**
	 * 返回某船的战斗数值
	 */
	public function getAllProperty() 
	{
		// 返回战斗属性
		return array('currHp' => self::getHP(), 
					 'damge' => self::getDmg(),
		             'physicalAttackBase' => self::getAtt(),
		             'physicalDefendBase' => self::getDef(),
		             'fatal' => self::getFatal(),
		             'hit' => self::getHitRating(),
		             'dodge' => self::getDodge(),
		             'physicalAttackAddition' => self::getAttPer(),
		             'physicalDefendAddition' => self::getDefPer(),
		             'physicalAttackRatio' => self::getDmgRatio(),
		             'physicalDamageIgnoreRatio' => self::getExemptDmgRatio());
	}

	/**
	 * 主船的物理伤害
	 * 物理伤害 = （物理攻击总值-物理防御总值）（1+物理伤害倍率-物理免伤倍率+技能物理攻击伤害倍率）
	 */
	public function getDmg()
	{
		// 返回结果
		return 
		// 物理攻击总值-物理防御总值
		(self::getAtt() - self::getDef()) * 
		// 1+物理伤害倍率-物理免伤倍率+技能物理攻击伤害倍率
		(1 + self::getDmgRatio() - self::getExemptDmgRatio() + SciTechLogic::getSciTechAttr(PropertyDef::PHYSICAL_ATTACK_RATIO));
	}

	/**
	 * 主船的基础生命值
	 * 主船的最终生命 = 主船基础生命值 + 主船生命成长 * 主船等级 + 装备基础生命值 + 装备生命成长总值 + 技能提供的生命基础值
	 */
	public function getHP() 
	{
		// 获取最新信息
		self::resetInfo();
		// 返回结果
		return 
		// 基础生命值
		btstore_get()->BOAT[self::$m_refitID]['base_hp'] +
		// 获取加成值
		btstore_get()->BOAT[self::$m_refitID]['base_hp_up'] * self::$m_level +
		// 获取装备加成值
		isset(self::$m_equips[ItemDef::ITEM_ATTR_NAME_HP]) ? self::$m_equips[ItemDef::ITEM_ATTR_NAME_HP] : 0 +
		// 技能提供的生命基础值
		SciTechLogic::getSciTechAttr(PropertyDef::HP_BASE);
	}

	/**
	 * 主船的基础攻击力
	 * 物理攻击总值 = （主船基础物理攻击 + 主船的等级*主船物理攻击成长 + 装备物理攻击基础值 + 装备物理攻击成长总值 + 技能提供的物理攻击基础值）*
	 *           （1 + 主船基础物理攻击百分比+主船当前等级*主船物理攻击百分比成长 + 技能提供的物理攻击百分比）
	 */
	public function getAtt()
	{
		// 获取最新信息
		self::resetInfo();
		// 返回结果
		return 
		// 基础攻击力
		( btstore_get()->BOAT[self::$m_refitID]['base_atk'] +
		// 获取加成值
		  btstore_get()->BOAT[self::$m_refitID]['base_atk_up'] * self::$m_level +
		// 获取装备加成值
		  isset(self::$m_equips[ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK]) ? self::$m_equips[ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK] : 0 +
		// 技能提供的攻击力基础值
		  SciTechLogic::getSciTechAttr(PropertyDef::PHYSICAL_ATTACK_BASE) ) *
		// 主船基础物理攻击百分比
		( 1 + btstore_get()->BOAT[self::$m_refitID]['base_atk_per'] +
		// 获取加成值
		  btstore_get()->BOAT[self::$m_refitID]['base_atk_per_up'] * self::$m_level +
		// 技能提供的物理攻击百分比
		  SciTechLogic::getSciTechAttr(PropertyDef::PHYSICAL_ATTACK_ADDITION) );
	}

	/**
	 * 主船基础物理防御
	 * 物理防御总值 = （主船基础物理防御 + 主船的等级 * 主船物理防御成长 + 装备物理防御基础值+装备物理防御成长总值+技能提供的物理防御基础值） * 
	 *           （1+主船基础物理防御百分比+主船当前等级*主船物理防御百分比成长+技能提供的物理防御百分比）  
	 */
	public function getDef()
	{
		// 获取最新信息
		self::resetInfo();
		// 返回结果
		return 
		// 基础物理防御
		( btstore_get()->BOAT[self::$m_refitID]['base_def'] +
		// 获取加成值
		  btstore_get()->BOAT[self::$m_refitID]['base_def_up'] * self::$m_level +
		// 获取装备加成值
		  isset(self::$m_equips[ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE]) ? self::$m_equips[ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE] : 0 +
		// 技能提供的防御力基础值
		  SciTechLogic::getSciTechAttr(PropertyDef::PHYSICAL_DEFEND_BASE) ) *
		// 主船基础物理防御百分比
		( 1 + btstore_get()->BOAT[self::$m_refitID]['base_def_per'] +
		// 获取加成值
		  btstore_get()->BOAT[self::$m_refitID]['base_def_per_up'] * self::$m_level +
		// 技能提供的物理防御百分比
		  SciTechLogic::getSciTechAttr(PropertyDef::PHYSICAL_DEFEND_ADDITION) );
	}

	/**
	 * 主船基础致命一击率
	 * 致命一击总值 = 主船基础致命 + 主船致命成长 * 主船等级 + 装备基础致命 + 装备致命成长总值 + 技能提供的致命一击基础值
	 */
	public function getFatal()
	{
		// 获取最新信息
		self::resetInfo();
		// 返回结果
		return 
		// 基础致命 
		btstore_get()->BOAT[self::$m_refitID]['base_fatal'] +
		// 获取加成值
		btstore_get()->BOAT[self::$m_refitID]['base_fatal_up'] * self::$m_level +
		// 获取装备加成值
		isset(self::$m_equips[ItemDef::ITEM_ATTR_NAME_FATAL]) ? self::$m_equips[ItemDef::ITEM_ATTR_NAME_FATAL] : 0 +
		// 技能提供的致命一击基础值
		SciTechLogic::getSciTechAttr(PropertyDef::FATAL_BASE);
	}

	/**
	 * 主船基础命中
	 * 命中总值 = 主船基础命中 + 主船命中成长 * 主船等级 + 装备基础命中 + 装备命中成长总值  + 技能提供的命中基础值
	 */
	public function getHitRating() 
	{
		// 获取最新信息
		self::resetInfo();
		// 返回结果
		return 
		// 基础命中
		btstore_get()->BOAT[self::$m_refitID]['base_hit'] +
		// 获取加成值
		btstore_get()->BOAT[self::$m_refitID]['base_hit_up'] * self::$m_level +
		// 获取装备加成值
		isset(self::$m_equips[ItemDef::ITEM_ATTR_NAME_HIT_RATING]) ? self::$m_equips[ItemDef::ITEM_ATTR_NAME_HIT_RATING] : 0 +
		// 技能提供的命中基础值
		SciTechLogic::getSciTechAttr(PropertyDef::HIT_BASE);
	}

	/**
	 * 主船基础闪避
	 * 闪避总值 = 主船基础闪避 + 主船闪避成长 * 主船等级+ 装备基础闪避 + 装备闪避成长总值 + 技能提供的闪避基础值
	 */
	public function getDodge() 
	{
		// 获取最新信息
		self::resetInfo();
		// 返回结果
		return 
		// 基础闪避
		btstore_get()->BOAT[self::$m_refitID]['base_dodge'] +
		// 获取加成值
		btstore_get()->BOAT[self::$m_refitID]['base_dodge_up'] * self::$m_level +
		// 获取装备加成值
		isset(self::$m_equips[ItemDef::ITEM_ATTR_NAME_DODGE]) ? self::$m_equips[ItemDef::ITEM_ATTR_NAME_DODGE] : 0 +
		// 技能提供的闪避基础值
		SciTechLogic::getSciTechAttr(PropertyDef::DODGE_BASE);
	}

	/**
	 * 主船物理攻击百分比
	 */
	public function getAttPer()
	{
		return btstore_get()->BOAT[self::$m_refitID]['base_atk_per'];
	}

	/**
	 * 主船物理防御百分比
	 */
	public function getDefPer()
	{
		return btstore_get()->BOAT[self::$m_refitID]['base_def_per'];
	}

	/**
	 * 物理伤害倍率
	 * 物理伤害倍率 = 主船固定物理伤害倍率 + 主船当前等级*主船物理伤害倍率成长
	 */
	public function getDmgRatio()
	{
		// 获取最新信息
		self::resetInfo();
		// 返回结果
		return 
		// 基础物理伤害倍率
		btstore_get()->BOAT[self::$m_refitID]['base_dmg_ratio'] +
		// 获取加成值
		btstore_get()->BOAT[self::$m_refitID]['base_dmg_up'] * self::$m_level;
	}

	/**
	 * 物理免伤倍率
	 * 物理免伤倍率 = 主船固定物理免伤倍率 + 主船当前等级*主船物理免伤倍率成长
	 */
	public function getExemptDmgRatio()
	{
		// 获取最新信息
		self::resetInfo();
		// 返回结果
		return 
		// 基础物理免伤倍率
		btstore_get()->BOAT[self::$m_refitID]['base_no_dmg_ratio'] +
		// 获取加成值
		btstore_get()->BOAT[self::$m_refitID]['base_no_dmg_up'] * self::$m_level;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */