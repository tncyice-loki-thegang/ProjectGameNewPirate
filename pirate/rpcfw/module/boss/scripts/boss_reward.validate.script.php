<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: boss_reward.validate.script.php 19254 2012-04-25 03:27:00Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/boss/scripts/boss_reward.validate.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-04-25 11:27:00 +0800 (三, 2012-04-25) $
 * @version $Revision: 19254 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Boss.def.php";

$boss_rewards = btstore_get()->BOSS_REWARD->toArray();

foreach ( $boss_rewards as $reward_id => $reward_info )
{
	//validate order list num
	if ( $reward_info[BossDef::REWARD_ORDER_LIST_NUM] !=
		count($reward_info[BossDef::REWARD_ORDER_LIST]) )
	{
		echo "BOSS REWARD:$reward_id order list num != count(order list)\n";
	}

	//validate order list
	$orders = array();
	for ( $i = 0; $i < count($reward_info[BossDef::REWARD_ORDER_LIST]); $i++ )
	{
		$list = $reward_info[BossDef::REWARD_ORDER_LIST][$i];

		//validate order low and order up
		if ( $list[BossDef::REWARD_ORDER_LOW] > $list[BossDef::REWARD_ORDER_UP] )
		{
			echo "BOSS REWARD:$reward_id index:$i order low:" . $list[BossDef::REWARD_ORDER_LOW]
			. " > " . $list[BossDef::REWARD_ORDER_UP] . " is invalid\n";
		}

		//validate order reward item
		if ( !empty($list[BossDef::REWARD_DROP_TEMPLATE_ID]) )
		{
			$drop_template_id = $list[BossDef::REWARD_DROP_TEMPLATE_ID];
			if ( !isset(btstore_get()->DROPITEM[$drop_template_id]) )
			{
				echo "BOSS REWARD:$reward_id index:$i reward item:$drop_template_id is invalid\n";
			}
		}

		foreach ( range($list[BossDef::REWARD_ORDER_LOW], $list[BossDef::REWARD_ORDER_UP]) as $order )
		{
			if ( in_array($order, $orders) )
			{
				echo "BOSS REWARD:$reward_id index:$i order range is invalid\n";
			}
		}

		$orders = array_merge($orders, range($list[BossDef::REWARD_ORDER_LOW], $list[BossDef::REWARD_ORDER_UP]));
	}

	//validate order 0
	if ( !in_array(0, $orders) )
	{
		echo "BOSS REWARD:$reward_id order 0 is not isset\n";
	}

	//validate order 1
	if ( !in_array(1, $orders) )
	{
		echo "BOSS REWARD:$reward_id order 1 is not isset\n";
	}

	//validate order 2
	if ( !in_array(2, $orders) )
	{
		echo "BOSS REWARD:$reward_id order 2 is not isset\n";
	}

	//validate order 3
	if ( !in_array(3, $orders) )
	{
		echo "BOSS REWARD:$reward_id order 3 is not isset\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */