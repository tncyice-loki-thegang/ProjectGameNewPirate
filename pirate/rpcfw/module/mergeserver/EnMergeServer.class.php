<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnMergeServer.class.php 32110 2012-12-01 05:21:40Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mergeserver/EnMergeServer.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-01 13:21:40 +0800 (六, 2012-12-01) $
 * @version $Revision: 32110 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnMergeServer
 * Description : 合服活动内部接口类
 * Inherit     :
 **********************************************************************************************************************/
class EnMergeServer
{
	/**
	 * 合服活动-新的征程
	 * 合服活动期间内，用户登陆了几次
	 * 
	 * @param  NULL
	 * @return NULL
	 */
	public static function mServerUseLoginCount()
	{
		MergeServerLogic::mServerUseLoginCount();
	}

	/**
	 * 合服活动-新的王者
	 * 数据互通后开服6天内，对参与竞技场排名战的玩家，发放双倍奖励。
	 * 
	 * @param  NULL
	 * @return int $override				合服期间竞技场奖励倍率
	 */
	public static function theNewKing()
	{
		return MergeServerLogic::theNewKing();
	}
	
	/**
	 * 合服活动-开心厨房麻辣出航
	 * 活动期间参与出航和厨房生产，将会获得1.5倍的贝里奖励。
	 * 
	 * @param  NULL
	 * @return int $override				合服期间开心厨房麻辣出航奖励倍率
	 */
	public static function theKitchenSail()
	{
		return MergeServerLogic::theKitchenSail();
	}
	
	/**
	 * 合服活动-充值返还
	 * 
	 * @param  $uid							用户uid
	 * @param  $gold						充值金币数
	 * @return NULL
	 */
	public static function isMserverRecharge($uid, $gold)
	{
		return MergeServerLogic::recharge($uid, $gold);
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */