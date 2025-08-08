<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnAbyssCopy.class.php 40884 2013-03-18 06:53:24Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/EnAbyssCopy.class.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-18 14:53:24 +0800 (一, 2013-03-18) $
 * @version $Revision: 40884 $
 * @brief 
 *  
 **/


class EnAbyssCopy
{
	
	private static $myAbyssList = array();
	
	
	/**
	 *
	 * @return MyAbyssCopy
	 */
	public static function getMyAbyss ($uid = 0)
	{
		if ($uid == 0)
		{
			$uid = RPCContext::getInstance()->getUid();
			if ($uid == null)
			{
				Logger::fatal('uid and global.uid are 0');
				throw new Exception('fake');
			}
		}
	
		if (!isset(self::$myAbyssList[$uid]))
		{
			self::$myAbyssList[$uid] = new MyAbyssCopy($uid);
		}
		return self::$myAbyssList[$uid];
	}
	
	/**
	 * 判断城镇ID是否属于深渊本
	 * @param int $townId
	 * @return boolean
	 */
	public static function isAbyssCopy($townId)
	{
		return $townId >= AbyssCopyDef::MIN_ABYSS_TOWN_ID;
	}
	
	/**
	 * 清除缓存
	 */
	public static function clearCache()
	{
		self::$myAbyssList = array();
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */