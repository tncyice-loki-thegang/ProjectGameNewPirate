<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnSmelting.class.php 36855 2013-01-24 02:32:39Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/EnSmelting.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-24 10:32:39 +0800 (四, 2013-01-24) $
 * @version $Revision: 36855 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnSmelting
 * Description : 装备制作内部接口类
 * Inherit     : 
 **********************************************************************************************************************/
class EnSmelting
{
	/**
	 * 给当前用户加上积分
	 * 
	 * @param int $redIntegral					红色积分种类
	 * @param int $purpleIntegral				紫色积分数量
	 */
	public static function addIntegral($redIntegral, $purpleIntegral)
	{
		// 如果开启了装备制作，才允许增加积分
		if (EnSwitch::isOpen(SwitchDef::EQUIPMENT))
		{
			MySmelting::getInstance()->addIntegral(SmeltingConf::COLOR_RED, $redIntegral);
			MySmelting::getInstance()->addIntegral(SmeltingConf::COLOR_PURPLE, $purpleIntegral);
			MySmelting::getInstance()->save();
		}
	}

	/**
	 * 给当前用户加上积分
	 * 
	 * @param int $redIntegral					红色积分种类
	 * @param int $purpleIntegral				紫色积分数量
	 */
	public static function addIntegralWithoutFestival($redIntegral, $purpleIntegral)
	{
		// 如果开启了装备制作，才允许增加积分
		if (EnSwitch::isOpen(SwitchDef::EQUIPMENT))
		{
			MySmelting::getInstance()->addIntegralWithoutFestival(SmeltingConf::COLOR_RED, $redIntegral);
			MySmelting::getInstance()->addIntegralWithoutFestival(SmeltingConf::COLOR_PURPLE, $purpleIntegral);
			MySmelting::getInstance()->save();
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */