<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: boss.validate.script.php 18074 2012-04-06 07:40:26Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/boss/scripts/boss.validate.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-04-06 15:40:26 +0800 (五, 2012-04-06) $
 * @version $Revision: 18074 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Boss.def.php";

$bosses = btstore_get()->BOSS->toArray();

foreach ( $bosses as $boss_id => $boss )
{
	//validate boss town id
	if ( !isset(btstore_get()->TOWN[$boss_id]) )
	{
		echo "BOSS:$boss_id town id:" . $boss_id . " is not isset!\n";
	}

	//validate boss army id
	if ( !isset(btstore_get()->ARMY[$boss[BossDef::ARMY_ID]]) )
	{
		echo "BOSS:$boss_id army id:" . $boss[BossDef::ARMY_ID] . " is not isset!\n";
	}

	//validate init level
	if ( empty($boss[BossDef::BOSS_INIT_LEVEL]) )
	{
		echo "BOSS:$boss_id init level:" . $boss[BossDef::BOSS_INIT_LEVEL] . " is invalid!\n";
	}

	//validate min level
	if ( empty($boss[BossDef::BOSS_MIN_LEVEL]) )
	{
		echo "BOSS:$boss_id min level:" . $boss[BossDef::BOSS_MIN_LEVEL] . " is invalid!\n";
	}

	//validate max level
	if ( empty($boss[BossDef::BOSS_MAX_LEVEL]) )
	{
		echo "BOSS:$boss_id max level:" . $boss[BossDef::BOSS_MAX_LEVEL] . " is invalid!\n";
	}

	//validate min_level <= init_level <= max_level
	if ( $boss[BossDef::BOSS_INIT_LEVEL] < $boss[BossDef::BOSS_MIN_LEVEL] ||
		$boss[BossDef::BOSS_INIT_LEVEL] > $boss[BossDef::BOSS_MAX_LEVEL] )
	{
		echo "BOSS:$boss_id min level:" . $boss[BossDef::BOSS_MIN_LEVEL] . " <= init level:"
		. $boss[BossDef::BOSS_INIT_LEVEL] . " <= max level:" .
		$boss[BossDef::BOSS_MAX_LEVEL] . " is not match!\n";
	}

	//validate reward id
	if ( !isset(btstore_get()->BOSS_REWARD[$boss[BossDef::REWARD_ID]]) )
	{
		echo "BOSS:$boss_id reward id:" . $boss[BossDef::REWARD_ID] . " is invalid\n";
	}

	//validate activity time
	if ( $boss[BossDef::ACTIVITY_START_TIME] >= $boss[BossDef::ACTIVITY_END_TIME] )
	{
		echo "BOSS:$boss_id activity time interval is invalid, start time:"
			. $boss[BossDef::ACTIVITY_START_TIME]
			. " >= end time:" . $boss[BossDef::ACTIVITY_END_TIME] . "\n";
	}

	//validate activity day list
	if ( !empty($boss[BossDef::ACTIVITY_DAY_LIST]) )
	{
		foreach ( $boss[BossDef::ACTIVITY_DAY_LIST] as $day )
		{
			if ( !in_array($day, range(1,31)) )
			{
				echo "BOSS:$boss_id activity day list:" . $day  . " is invalid\n";
			}
		}
	}

	//validate activity week list
	if ( !empty($boss[BossDef::ACTIVITY_WEEK_LIST]) )
	{
		foreach ( $boss[BossDef::ACTIVITY_WEEK_LIST] as $week )
		{
			if ( !in_array($week, range(1,7)) )
			{
				echo "BOSS:$boss_id activity week list:" . $week  . " is invalid\n";
			}
		}
	}

	//validate activity day start time is empty
	if ( empty($boss[BossDef::ACTIVITY_DAY_START_TIMES]) )
	{
		echo "BOSS:$boss_id activity day start time is empty\n!";
	}

	//validate activity day end time is empty
	if ( empty($boss[BossDef::ACTIVITY_DAY_END_TIMES]) )
	{
		echo "BOSS:$boss_id activity day end time is empty\n!";
	}

	//validate activity day times
	if ( count($boss[BossDef::ACTIVITY_DAY_START_TIMES]) != count($boss[BossDef::ACTIVITY_DAY_END_TIMES]))
	{
		echo "BOSS:$boss_id activity day start time array count:"
		. count($boss[BossDef::ACTIVITY_DAY_START_TIMES]) . " != "
		. "activity day end time array count:"
		. count($boss[BossDef::ACTIVITY_DAY_END_TIMES]) . "\n";
	}
	else
	{
		for ( $i = 0; $i < count($boss[BossDef::ACTIVITY_DAY_START_TIMES]); $i++ )
		{
			if ( $boss[BossDef::ACTIVITY_DAY_START_TIMES][$i] >= $boss[BossDef::ACTIVITY_DAY_END_TIMES] )
			{
				echo "BOSS:$boss_id activity day time index:$i is invalid start >= end\n";
			}
			if ( $i > 0 && $boss[BossDef::ACTIVITY_DAY_END_TIMES][$i-1] > $boss[BossDef::ACTIVITY_DAY_START_TIMES] )
			{
				echo "BOSS:$boss_id activity day index:$i start time <= index:" . $i-1 . " end time\n";
			}
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */