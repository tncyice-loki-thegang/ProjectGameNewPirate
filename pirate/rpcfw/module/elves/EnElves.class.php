<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnElves.class.php 38161 2013-02-05 08:30:11Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elves/EnElves.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-05 16:30:11 +0800 (二, 2013-02-05) $
 * @version $Revision: 38161 $
 * @brief 
 *  
 **/

class EnElves
{
	/**
	 * 是否有矿精灵
	 * @param unknown_type $uid
	 */
	public static function hasResourceElf($uid=0)
	{
		return self::hasElf(ElvesDef::RESOURCE_ID, $uid);
	}
	
	/**
	 * 是否有鱼精灵
	 * @param unknown_type $uid
	 */
	public static function hasFishElf($uid=0)
	{
		return self::hasElf(ElvesDef::FISH_ID, $uid);
	}
	
	/**
	 * 是否有寻宝精灵
	 * @param unknown_type $uid
	 */
	public static function hasTreasureElf($uid=0)
	{
		return self::hasElf(ElvesDef::TREASURE_ID, $uid);
	}
	
	/**
	 * 是否有 $id 的守护精灵
	 * @param unknown_type $id
	 * @param unknown_type $uid
	 */
	public static function hasElf($id, $uid=0)
	{
		if ($uid==0)
		{
			$uid = RPCContext::getInstance()->getUid();
		}
		
		$elves = new ElvesObj($uid);
		return $elves->hasElf($id);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */