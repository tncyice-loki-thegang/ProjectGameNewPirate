<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Property.def.php 31881 2012-11-26 02:40:18Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Property.def.php $
 * @author $Author: HongyuLan $(hoping@babeltime.com)
 * @date $Date: 2012-11-26 10:40:18 +0800 (一, 2012-11-26) $
 * @version $Revision: 31881 $
 * @brief
 *
 **/
class PropertyDef
{

	const HP_BASE = 1;

	const HP_ADDITION = 2;

	const STRENGHT_BASE = 3;

	const AGILE_BASE = 4;

	const INTELLIGENCE_BASE = 5;

	const PHYSICAL_ATTACK_BASE = 6;

	const KILL_ATTACK_BASE = 7;

	const MAGIC_ATTACK_BASE = 8;

	const PHYSICAL_DEFEND_BASE = 9;

	const KILL_DEFEND_BASE = 10;

	const MAGIC_DEFEND_BASE = 11;

	const RAGE_BASE = 12;

	const HIT_BASE = 13;

	const FATAL_BASE = 14;

	const PARRY_BASE = 15;

	const DODGE_BASE = 16;

	const WIND_ATTACK_BASE = 17;

	const THUNDER_ATTACK_BASE = 18;

	const WATER_ATTACK_BASE = 19;

	const FIRE_ATTACK_BASE = 20;

	const WIND_DEFEND_BASE = 21;

	const THUNDER_DEFEND_BASE = 22;

	const WATER_DEFEND_BASE = 23;

	const FIRE_DEFEND_BASE = 24;

	const PHYSICAL_ATTACK_ADDITION = 25;

	const PHYSICAL_DEFEND_ADDITION = 26;

	const KILL_ATTACK_ADDITION = 27;

	const KILL_DEFEND_ADDITION = 28;

	const MAGIC_ATTACK_ADDITION = 29;

	const MAGIC_DEFEND_ADDITION = 30;

	const WIND_ATTACK_ADDITION = 31;

	const THUNDER_ATTACK_ADDITION = 32;

	const WATER_ATTACK_ADDITION = 33;

	const FIRE_ATTACK_ADDITION = 34;

	const PHYSICAL_ATTACK_RATIO = 35;

	//const PHYSICAL_DEFEND_RATIO = 36;

	const KILL_ATTACK_RATIO = 37;

	//const KILL_DEFEND_RATIO = 38;

	const MAGIC_ATTACK_RATIO = 39;

	//const MAGIC_DEFEND_RATIO = 40;

	const ABSOLUTE_ATTACK = 41;

	const ABSOLUTE_DEFEND = 42;

	const STRENGTH_ADDITION = 43;

	const AGILE_ADDITON = 44;

	const INTELLIGENCE_ADDITION = 45;

	const CHARM_SKILL = 46;

	const PARRY_SKILL = 47;

	const CHAOS_SKILL = 48;

	const RAGE_SKILL = 57;

	const IMMUNED_BUFFER_LIST = 49;

	const KILL_DAMAGE_IGNORE_RATIO = 50;

	const MAGIC_DAMAGE_IGNORE_RATIO = 51;

	const PHYSICAL_DAMAGE_IGNORE_RATIO = 52;

	const RAGE_RATIO = 53;

	const RAGE_AMEND = 54;

	const CURR_HP = 55;
	
	const RAGE_GET_BASE = 56;
	
	const RAGE_GET_RATIO = 57;
	
	const RAGE_GET_AMEND = 58;
	
	const ABSOLUTE_KILL_ATTACK = 59;
	const ABSOLUTE_KILL_DEFEND = 60;
	const ABSOLUTE_MAGIC_ATTACK = 61;
	const ABSOLUTE_MAGIC_DEFEND = 62;
	const ABSOLUTE_PHYSICAL_ATTACK = 63;
	const ABSOLUTE_PHYSICAL_DEFEND = 65;
		
	static $ARR_INDEX2KEY = array (self::ABSOLUTE_ATTACK => 'absoluteAttack',
			self::ABSOLUTE_DEFEND => 'absoluteDefend', self::AGILE_ADDITON => 'agileAddition',
			self::AGILE_BASE => 'agile', self::DODGE_BASE => 'dodge', self::FATAL_BASE => 'fatal',
			self::FIRE_ATTACK_ADDITION => 'fireAttackAddition',
			self::FIRE_ATTACK_BASE => 'fireAttackBase', self::FIRE_DEFEND_BASE => 'fireDefendBase',
			self::HIT_BASE => 'hit', self::HP_ADDITION => 'hpAddition', self::HP_BASE => 'maxHp',
			self::INTELLIGENCE_ADDITION => 'intelligenceAddition',
			self::INTELLIGENCE_BASE => 'intelligence',
			self::KILL_ATTACK_ADDITION => 'killAttackAddition',
			self::KILL_ATTACK_BASE => 'killAttackBase', self::KILL_ATTACK_RATIO => 'killAttackRatio',
			self::KILL_DEFEND_ADDITION => 'killDefendAddition',
			self::KILL_DEFEND_BASE => 'killDefendBase', 
			//self::KILL_DEFEND_RATIO => 'killDefendRatio',
			self::MAGIC_ATTACK_ADDITION => 'magicAttackAddition',
			self::MAGIC_ATTACK_BASE => 'magicAttackBase',
			self::MAGIC_ATTACK_RATIO => 'magicAttackRatio',
			self::MAGIC_DEFEND_ADDITION => 'magicDefendAddition',
			self::MAGIC_DEFEND_BASE => 'magicDefendBase',
			//self::MAGIC_DEFEND_RATIO => 'magicDefendRatio', 
			self::PARRY_BASE => 'parry',
			self::PHYSICAL_ATTACK_ADDITION => 'physicalAttackAddition',
			self::PHYSICAL_ATTACK_BASE => 'physicalAttackBase',
			self::PHYSICAL_ATTACK_RATIO => 'physicalAttackRatio',
			self::PHYSICAL_DEFEND_ADDITION => 'physicalDefendAddition',
			self::PHYSICAL_DEFEND_BASE => 'physicalDefendBase',
			//self::PHYSICAL_DEFEND_RATIO => 'physicalDefendRatio', 
			self::RAGE_BASE => 'currRage',
			self::STRENGHT_BASE => 'strength', self::STRENGTH_ADDITION => 'strengthAddition',
			self::THUNDER_ATTACK_ADDITION => 'thunderAttackAddition',
			self::THUNDER_ATTACK_BASE => 'thunderAttackBase',
			self::THUNDER_DEFEND_BASE => 'thunderDefendBase',
			self::WATER_ATTACK_ADDITION => 'waterAttackAddition',
			self::WATER_ATTACK_BASE => 'waterAttackBase',
			self::WATER_DEFEND_BASE => 'waterDefendBase',
			self::WIND_ATTACK_ADDITION => 'windAttackAddition',
			self::WIND_ATTACK_BASE => 'windAttackBase', self::WIND_DEFEND_BASE => 'windDefendBase',
			self::CHARM_SKILL => 'charmSkill', self::PARRY_SKILL => 'parrySkill',
			self::CHAOS_SKILL => 'chaosSkill', self::RAGE_SKILL => 'rageSkill',
			self::IMMUNED_BUFFER_LIST => 'arrImmunedEffect',
			self::KILL_DAMAGE_IGNORE_RATIO => 'killDamageIgnoreRatio',
			self::MAGIC_DAMAGE_IGNORE_RATIO => 'magicDamageIgnoreRatio',
			self::PHYSICAL_DAMAGE_IGNORE_RATIO => 'physicalDamageIgnoreRatio',
			self::RAGE_RATIO => 'rageRatio', self::RAGE_AMEND => 'rageAmend',
			self::CURR_HP => 'currHp',
			self::RAGE_GET_BASE => 'rageBase',
			self::RAGE_GET_AMEND => 'rageAmend',
			self::RAGE_GET_RATIO => 'rageRatio',
			
			self::ABSOLUTE_KILL_ATTACK      => 'absoluteKillAttack'      ,
			self::ABSOLUTE_KILL_DEFEND  	=> 'absoluteKillDefend'		 ,
			self::ABSOLUTE_MAGIC_ATTACK     => 'absoluteMagicAttack'	 ,
			self::ABSOLUTE_MAGIC_DEFEND 	=> 'absoluteMagicDefend'	 ,
			self::ABSOLUTE_PHYSICAL_ATTACK 	=> 'absolutePhysicalAttack'	 ,
			self::ABSOLUTE_PHYSICAL_DEFEND 	=> 'absolutePhysicalDefend'	 ,
			
	);
			
			
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */