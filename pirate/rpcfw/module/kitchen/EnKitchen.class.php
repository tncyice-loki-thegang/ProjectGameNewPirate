<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/



/**********************************************************************************************************************
 * Class       : EnKitchen
 * Description : 训练内部接口类
 * Inherit     : 
 **********************************************************************************************************************/
class EnKitchen
{
	/**
	 * 添加一条新的厨房记录
	 * @param int $uid							用户ID
	 */
	public static function addNewKitchenInfoForUser($uid)
	{
		// 插入一个空白用户信息到数据库中
		return KitchenDao::addNewKitchenInfo($uid);
	}

	/**
	 * 增加当日制作次数
	 */
	public static function subCookTimes($value)
	{
		// 获取厨房信息
		$kitchenInfo = KitchenDao::getKitchenInfo(KitchenLogic::getUid());
		// 增加次数，放在累积次数里面
		$kitchenInfo['cook_accumulate'] += $value;
		// 更新数据库
		KitchenDao::updKitchenInfo(KitchenLogic::getUid(), array('cook_accumulate' => $kitchenInfo['cook_accumulate']));
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */