<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnHonourShop.class.php 33512 2012-12-20 06:06:31Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/honourshop/EnHonourShop.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-20 14:06:31 +0800 (四, 2012-12-20) $
 * @version $Revision: 33512 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnHonourShop
 * Description : 荣誉商店内部接口类
 * Inherit     :
 **********************************************************************************************************************/
class EnHonourShop
{
	
	/**
	 * 增加荣誉
	 */
	public static function addHonourPoint($uid, $honourPoint)
	{
		return HonourShopLogic::addHonourPoint($uid, $honourPoint);
	}
	
	/**
	 * 获取当前用户荣誉
	 */
	public static function getUserHonourPoint()
	{
		return HonourShopLogic::getUserHonourPoint();
	}
	
	/**
	 * 增加荣誉
	 */
	public static function addFinallyHonourPoint($uid, $honourPoint)
	{
		return HonourShopLogic::addFinallyHonourPoint($uid, $honourPoint);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */