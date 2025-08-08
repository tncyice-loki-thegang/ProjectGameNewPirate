<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Bag.cfg.php 38696 2013-02-20 02:24:00Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Bag.cfg.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-02-20 10:24:00 +0800 (三, 2013-02-20) $
 * @version $Revision: 38696 $
 * @brief
 *
 **/

class BagConfig
{
	//用户背包最大数量
	const USER_BAG_GRID_NUM					=					360;
	//临时背包最大数量
	const TMP_BAG_GRID_NUM					=					3600;
	//任务背包最大数量
	const MISSION_BAG_GRID_NUM				=					360;
	//仓库最大数量
	const DEPOT_BAG_GRID_NUM				=					360;

	//用户背包初始格子数
	const BAG_UNLOCK_GID_START				=					300;
	//用户背包解锁初始价格
	const BAG_UNLOCK_GOLD					=					2;
	//用户背包解锁价格增加值
	const BAG_UNLOCK_GOLD_STEP				=					1;
	//用户背包解锁所需物品
	const BAG_UNLOCK_ITEM_ID				=					120009;
	
	//仓库背包初始格子数
	const DEPOT_BAG_UNLOCK_GID_START		=					300;
	//用户背包解锁初始价格
	const DEPOT_BAG_UNLOCK_GOLD				=					4;
	//用户背包解锁价格增加值
	const DEPOT_BAG_UNLOCK_GOLD_STEP		=					2;	
	//用户仓库解锁所需物品
	const DEPOT_BAG_UNLOCK_ITEM_ID			=					120035;

	//临时背包物品过期时间	60*60*24*3(s)
	const TMP_BAG_EXPIRE_TIME				=					259200;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */