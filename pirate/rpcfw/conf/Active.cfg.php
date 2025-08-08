<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Active.cfg.php 32948 2012-12-12 07:49:58Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Active.cfg.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-12 15:49:58 +0800 (三, 2012-12-12) $
 * @version $Revision: 32948 $
 * @brief 
 *  
 **/

class ActiveConf
{
	const REFRESH_TIME = 14400;					// 每天的刷新后开始时刻

	public static $ACT_INDEX = array(
		"sail_times" => 2,
		"cook_times" => 3,
		"order_times" => 4,
		"day_task_times" => 5,
		"fetch_salary" => 6,
		"play_slave_times" => 7,
		"reinforce_times" => 8,
		"elite_atk_times" => 9,
		"explore_times" => 10,
		"arena_times" => 11,
		"rob_times" => 12,
		"port_atk_times" => 13,
		"donate_times" => 14,
		"resource_times" => 15,
		"talks_times" => 16,
		"treasure_times" => 17,
		"smelting_times" => 18,
		"hero_rapid_times" => 19,
		"goodwill_gift_times" => 20,
		"astro_exp_times" => 22,
		"gold_soul_times" => 23,
		"impel_prize_times" => 24, 
		"card_salary_times" => 25, 
		"bejeweled_times" => 26, 
		"summon_crystal" => 27, 
		"allblue_collect_times" => 28, 
		"cruise_times" => 29, 
		"haki_times" => 30, 
		"dailyworship_times" => 31, 
		"reforceworldboat_times" => 32, 
		"buyguildtechpoint_times" => 33, 
		"buyelementpoint_times" => 34, 
		"allblue_catchfish_times" => 35, 
		"elves_feed" => 36, 
		"tm_get_hero_income" => 37,
	);

	public static $ACT_NAME = array(
		2 => "sail_times",
		3 => "cook_times",
		4 => "order_times",
		5 => "day_task_times",
		6 => "fetch_salary",
		7 => "play_slave_times",
		8 => "reinforce_times",
		9 => "elite_atk_times",
		10 => "explore_times",
		11 => "arena_times",
		12 => "rob_times",
		13 => "port_atk_times",
		14 => "donate_times",
		15 => "resource_times",
		16 => "talks_times",
		17 => "treasure_times",
		18 => "smelting_times",
		19 => "hero_rapid_times",
		20 => "goodwill_gift_times",
		22 => "astro_exp_times",
		23 => "gold_soul_times",
		24 => "impel_prize_times", 
		25 => "card_salary_times", 
		26 => "bejeweled_times", 
		27 => "summon_crystal", 
		28 => "allblue_collect_times", 
		29 => "cruise_times", 
		30 => "haki_times", 
		31 => "dailyworship_times", 
		32 => "reforceworldboat_times", 
		33 => "buyguildtechpoint_times", 
		34 => "buyelementpoint_times", 
		35 => "allblue_catchfish_times", 
		36 => "elves_feed", 
		37 => "tm_get_hero_income",
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */