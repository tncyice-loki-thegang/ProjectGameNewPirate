<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroUtil.class.php 39830 2013-03-04 09:23:00Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/HeroUtil.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-04 17:23:00 +0800 (一, 2013-03-04) $
 * @version $Revision: 39830 $
 * @brief 
 *  
 **/



class HeroUtil
{
	public static function isHero($hid)
	{
		return $hid > 10000000;
	}
	
	public static function isHeroByHtid($htid)
	{
		return ($htid > 10000) && ($htid < 100000);
	}
	
	public static function isMasterHero($htid)
	{
		return isset(UserConf::$MASTER_HEROES[$htid]);
	}

	/**
	 * 检查是否为有效的htid
	 * Enter description here ...
	 * @param unknown_type $htid
	 */
	public static function checkHtid($htid)
	{
		if (isset(btstore_get()->CREATURES[$htid]))
		{
			return true;
		}
		return false;
	}

	/**
	 * 创建用户的时候，初始招募的英雄
	 * @param unknown_type $uid
	 * @param unknown_type $htid
	 */
	public static function recruitForInit ($uid, $htid, $arrField=null)
	{
		return HeroLogic::recruitForInit($uid, $htid, $arrField);
	}
	
	public static function getNumByLevel($uid, $level)
	{
		return HeroDao::getNumByLevel($uid, $level);
	}
	
	/**
	 * 系统支持的最大等级
	 * @return number
	 */
	public static function getMaxLevel()
	{
		return UserConf::MAX_LEVEL;
	}
	
	public static function getMasterByLevelInterval($minLevel, $maxLevel, $arrField, $limit=100)
	{
		return HeroDao::getMasterByLevelInterval($minLevel, $maxLevel, $arrField, $limit);	
	}
	
	public static function getEquipDef($type)
	{
		if (!in_array($type, HeroDef::$EQUIP_ITEM_TYPE))
		{
			return false;
		}
		
		switch ($type)
		{
			case 'arming':
				return ArmingDef::$ARMING_NO_ARMING;
			case 'dress':
				return FashionDressDef::$FASHION_NO_DRESS;
			case 'jewelry':
				return JewelryDef::$JEWELRY_NO_JEWELRY;
			case 'element':
				return ElementDef::$ELEMENT_NO_ELEMENT;
			default:
				return false;
		}
	}
	
	public static function getSetEquipFunc($type)
	{
		if (!in_array($type, HeroDef::$EQUIP_ITEM_TYPE))
		{
			return false;
		}
		
		switch ($type)
		{
			case 'arming':
				return 'HeroObj::setArmingByPosition';
			case 'dress':
				return 'HeroObj::setDressByPosition';
			case 'jewelry':
				return 'HeroObj::setJewelryByPosition';
			case 'element':
				return 'HeroObj::setElementByPosition';
			default:
				return false;
		}
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */