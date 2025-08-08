<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MapItemHero.def.php 38757 2013-02-20 07:23:01Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/MapItemHero.def.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-20 15:23:01 +0800 (三, 2013-02-20) $
 * @version $Revision: 38757 $
 * @brief 
 *  
 **/




class MapItemHeroDef
{
	static $ItemHero = array( 
ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK            =>  CreatureInfoKey::phyAttack         ,   
ItemDef::ITEM_ATTR_NAME_KILL_ATTACK                =>  CreatureInfoKey::killAttack        ,  
ItemDef::ITEM_ATTR_NAME_MAGIC_ATTACK               =>  CreatureInfoKey::mgcAttack         ,  
ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE           =>  CreatureInfoKey::phyDefend         ,  
ItemDef::ITEM_ATTR_NAME_KILL_DEFENCE               =>  CreatureInfoKey::killDefend        ,  
ItemDef::ITEM_ATTR_NAME_MAGIC_DEFENCE              =>  CreatureInfoKey::mgcDefend         ,  
ItemDef::ITEM_ATTR_NAME_HP                         =>  CreatureInfoKey::hp                ,  
ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTRCK_PERCENT    =>  CreatureInfoKey::phyAtkRatio       ,  
ItemDef::ITEM_ATTR_NAME_KILL_ATTACK_PERCENT        =>  CreatureInfoKey::killAtkRatio      ,  
ItemDef::ITEM_ATTR_NAME_MAGIC_ATTACK_PERCENT       =>  CreatureInfoKey::mgcAtkRatio       ,  
ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE_PERCENT   =>  CreatureInfoKey::phyDfsRatio       ,  
ItemDef::ITEM_ATTR_NAME_KILL_DEFENCE_PERCENT       =>  CreatureInfoKey::killDfsRatio      ,  
ItemDef::ITEM_ATTR_NAME_MAGIC_DEFENCE_PERCENT      =>  CreatureInfoKey::mgcDfsRatio       ,  
ItemDef::ITEM_ATTR_NAME_HP_PERCENT                 =>  CreatureInfoKey::hpRatio           ,  
ItemDef::ITEM_ATTR_NAME_STRENGTH                   =>  CreatureInfoKey::strength          ,  
ItemDef::ITEM_ATTR_NAME_STRENGTH_PERCENT           =>  CreatureInfoKey::stgRatio          ,  
ItemDef::ITEM_ATTR_NAME_AGILITY                    =>  CreatureInfoKey::agile             ,  
ItemDef::ITEM_ATTR_NAME_AGILITY_PERCENT            =>  CreatureInfoKey::aglRatio          ,  
ItemDef::ITEM_ATTR_NAME_INTELLIGENCE               =>  CreatureInfoKey::intelligence      ,  
ItemDef::ITEM_ATTR_NAME_INTELLIGENCE_PERCENT       =>  CreatureInfoKey::itgRatio          ,  
ItemDef::ITEM_ATTR_NAME_HIT_RATING                 =>  CreatureInfoKey::hitRatingRatio    ,  
ItemDef::ITEM_ATTR_NAME_FATAL                      =>  CreatureInfoKey::ftlAtkRatio       ,  
ItemDef::ITEM_ATTR_NAME_PARRY                      =>  CreatureInfoKey::pryRatio          ,  
ItemDef::ITEM_ATTR_NAME_DODGE                      =>  CreatureInfoKey::dgeRatio          ,  
ItemDef::ITEM_ATTR_NAME_WIND_ATTACK                =>  CreatureInfoKey::windAttack        ,  
ItemDef::ITEM_ATTR_NAME_THUNDER_ATTACK             =>  CreatureInfoKey::thdAttack         ,  
ItemDef::ITEM_ATTR_NAME_WATER_ATTACK               =>  CreatureInfoKey::wtrAttack         ,  
ItemDef::ITEM_ATTR_NAME_FIRE_ATTACK                =>  CreatureInfoKey::fireAttack        ,  
ItemDef::ITEM_ATTR_NAME_WIND_RESISTANCE            =>  CreatureInfoKey::windResistance    ,  
ItemDef::ITEM_ATTR_NAME_THUNDER_RESISTANCE         =>  CreatureInfoKey::thdResistance     ,  
ItemDef::ITEM_ATTR_NAME_WATER_RESISTANCE           =>  CreatureInfoKey::wtrResistance     ,  
ItemDef::ITEM_ATTR_NAME_FIRE_RESISTANCE            =>  CreatureInfoKey::fireResistance    ,
ItemDef::ITEM_ATTR_NAME_DAMAGE 					   => CreatureInfoKey::absoluteDamage,
ItemDef::ITEM_ATTR_NAME_AVOID_DAMAGE               => CreatureInfoKey::absoluteDefend,
ItemDef::ITEM_ATTR_NAME_RAGE		               => CreatureInfoKey::rage,			
			
	ItemDef::ITEM_ATTR_NAME_PYH_ATT_GIFT => CreatureInfoKey::phyFDmgRatio, 	// 物理伤害倍率
	ItemDef::ITEM_ATTR_NAME_PYH_DEF_GIFT => CreatureInfoKey::phyFEptRatio, 	// 物理免伤倍率
	ItemDef::ITEM_ATTR_NAME_KILL_ATT_GIFT => CreatureInfoKey::killFDmgRatio, 	// 必杀伤害倍率
	ItemDef::ITEM_ATTR_NAME_KILL_DEF_GIFT => CreatureInfoKey::killFEptRatio, 	// 必杀免伤倍率
	ItemDef::ITEM_ATTR_NAME_MAG_ATT_GIFT => CreatureInfoKey::mgcFDmgRatio, 	// 魔法伤害倍率
	ItemDef::ITEM_ATTR_NAME_MAG_DEF_GIFT => CreatureInfoKey::mgcFEptRatio, 	// 魔法免伤倍率
	
	ItemDef::ITEM_ATTR_NAME_NORMAL_ATT_RATIO => CreatureInfoKey::normalAttRatio, 	// 调整普通攻击伤害比
	ItemDef::ITEM_ATTR_NAME_NORMAL_DEF_RATIO => CreatureInfoKey::normalDefRatio, 	// 调整普通攻击免伤比
	ItemDef::ITEM_ATTR_NAME_RAGER_ATT_RATIO => CreatureInfoKey::ragerAttRatio, 	// 调整怒气攻击伤害比
	ItemDef::ITEM_ATTR_NAME_RAGER_DEF_RATIO => CreatureInfoKey::ragerDefRatio, 	// 调整怒气攻击免伤比
	ItemDef::ITEM_ATTR_NAME_TREAT_RATIO => CreatureInfoKey::treatRatio, 	// 调整治疗比率
	ItemDef::ITEM_ATTR_NAME_TREATED_RATIO => CreatureInfoKey::treatedRatio)	// 调整被治疗比率
	
	;
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */