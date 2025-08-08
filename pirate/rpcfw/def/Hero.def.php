<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Hero.def.php 38722 2013-02-20 05:52:17Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Hero.def.php $
 * @author $Author: yangwenhai $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-20 13:52:17 +0800 (三, 2013-02-20) $
 * @version $Revision: 38722 $
 * @brief
 *
 **/

class HeroDef
{       
     /**
     * 英雄表字段
     */
    public static $HERO_FIELDS = array(
    	'hid',
        'htid',
    	'uid',
    	'curHp',
        'status',
        'level',
    	'upgrade_time',
        'rebirthNum',
        'exp',
    	'all_exp',
    	'va_hero',
        );

	/**
	 * 英雄在酒馆
	 */
	const STATUS_PUB = 1;

	/**
	 * 英雄被招募招募
	 */
	const STATUS_RECRUIT = 2;
	
	/**
	 * 星盘特殊处理属性
	 * Enter description here ...
	 * @var unknown_type
	 */
	public static $AST_EXPT = array(
		CreatureInfoKey::phyFDmgRatio,
		CreatureInfoKey::phyFEptRatio,
		CreatureInfoKey::killFDmgRatio,
		CreatureInfoKey::killFEptRatio,
		CreatureInfoKey::mgcFDmgRatio,
		CreatureInfoKey::mgcFEptRatio,	
		
		CreatureInfoKey::hpFinal,
		CreatureInfokey::hpRatio,
	);
	
	public static $EQUIP_ITEM_TYPE = array(
			'arming',
			'dress',
			'jewelry',
			'element',
			);

};

class GoodwillAttr
{
	/**
	 * 生命基础
	 * Enter description here ...
	 * @var unknown_type
	 */
	const HP_BASE = 1;	
	
	/**
	 * 物理攻击基础
	 * Enter description here ...
	 * @var unknown_type
	 */
	const PHY_ATK_BASE = 6;
	
	/**
	 * 必杀攻击基础
	 * Enter description here ...
	 * @var unknown_type
	 */
	const KILL_ATK_BASE = 7;
	
	/**
	 * 魔法攻击基础
	 * Enter description here ...
	 * @var unknown_type
	 */
	const MGC_ATK_BASE = 8;
	  
	/**
	 * 物理防御基础
	 * Enter description here ...
	 * @var unknown_type
	 */
	const PHY_DEFEND_BASE = 9;
	
	/**
	 * 魔法防御基础
	 * Enter description here ...
	 * @var unknown_type
	 */
	const MGC_DEFEND_BASE = 10;
	
	/**
	 * 必杀防御基础
	 * Enter description here ...
	 * @var unknown_type
	 */
	const KILL_DEFEND_BASE = 11;
	
	/**
	 * 最终生命
	 * Enter description here ...
	 * @var unknown_type
	 */
	const HP_FINAL = 46;	
	
	public static $arrAttrGwMap = array(
		self::HP_BASE => CreatureInfoKey::hp,
		self::PHY_ATK_BASE => CreatureInfoKey::phyAttack,
		self::KILL_ATK_BASE => CreatureInfoKey::killAttack,
		self::MGC_ATK_BASE => CreatureInfoKey::mgcAttack,
		self::PHY_DEFEND_BASE => CreatureInfoKey::phyDefend, 
		self::MGC_DEFEND_BASE => CreatureInfoKey::mgcDefend, 
		self::KILL_DEFEND_BASE => CreatureInfoKey::killDefend,		
	);
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */