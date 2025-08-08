<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Boss.def.php 21599 2012-05-29 08:13:56Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Boss.def.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-29 16:13:56 +0800 (二, 2012-05-29) $
 * @version $Revision: 21599 $
 * @brief
 *
 **/

class BossDef
{
	/** memcache **/
	const MEMCACHE_PREFIX							=	'boss_memcache_';

	/** lock **/
	const LOCK_PREFIX								=	'boss_lock_';

	const BOSS_SESSION_KILLER						=	'boss.killer';

	/** flags **/
	const FLAGS_SUB_CD_TIME							=	1;
	const FLAGS_BOT									=	2;
	const FLAGS_BOT_SUB_CD_TIME						=	4;

	/** max loop time **/
	const MAX_LOOP_TIME								=	1024;

	const UID										=	'uid';
	const UNAME										=	'uname';
	const GROUP_ID									=	'group_id';
	const BOSS_ID									=	'boss_id';
	const ATTACK_LIST								=	'attack_list';
	const ATTACK_LIST_ORIGINAL						=	'attack_list_original';
	const ATTACK_LIST_SORTED						=	'attack_list_sorted';
	const ATTACK_GROUP								=	'attack_group';
	const BOSS_KILL									=	'boss_kill';
	const LAST_ATTACK_TIME							=	'last_attack_time';
	const LAST_INSPIRE_TIME							=	'last_inspire_time';
	const ATTACK_HP									=	'attack_hp';
	const BOSS_HP									=	'hp';
	const BOSS_MAX_HP								=	'max_hp';
	const BOSS_LEVEL								=	'level';
	const BOSS_INIT_LEVEL							=	'init_level';
	const BOSS_MAX_LEVEL							=	'max_level';
	const BOSS_MIN_LEVEL							=	'min_level';
	const INSPIRE									=	'inspire';
	const REVIVE									=	'revive';
	const FLAGS										=	'flags';
	const ARMY_ID									=	'army_id';
	const BOSS_START_TIME							=	'start_time';
	const BOSS_TOWN_USER_COUNT						=	'user_count';
	const BOT										=	'bot';
	const BOT_SUB_CDTIME							=	'bot_sub_cdtime';

	/** activity **/
	const ACTIVITY_START_TIME						=	'activity_start_time';
	const ACTIVITY_END_TIME							=	'activity_end_time';
	const ACTIVITY_DAY_START_TIMES					=	'activity_day_start_times';
	const ACTIVITY_DAY_END_TIMES					=	'activity_day_end_times';
	const ACTIVITY_DAY_LIST							=	'activity_day_list';
	const ACTIVITY_WEEK_LIST						=	'activity_week_list';

	/** reward **/
	const REWARD_ID									=	'reward_id';
	const REWARD_ORDER_LIST							=	'order_list';
	const REWARD_ORDER_LIST_NUM						=	'order_list_num';
	const REWARD_ORDER_LOW							=	'order_low';
	const REWARD_ORDER_UP							=	'order_up';
	const REWARD_BELLY								=	'belly';
	const REWARD_GOLD								=	'gold';
	const REWARD_PRESTIGE							=	'prestige';
	const REWARD_EXPERIENCE							=	'experience';
	const REWARD_ITEMS								=	'items';
	const REWARD_DROP_TEMPLATE_ID					=	'drop_template_id';
	const REWARD_BELLY_BASIC						=	'belly_basic';
	const REWARD_PRESTIGE_BASIC						=	'prestige_basic';
	const REWARD_EXPERIENCE_BASIC					=	'experience_basic';

	/** BOSS SQL DEFINE **/
	const BOSS_SQL_TABLE								=	't_boss';
	const BOSS_ATTACK_SQL_TABLE							=	't_boss_attack';
	const BOSS_SQL_BOSS_ID								=	'boss_id';
	const BOSS_SQL_UID									=	'uid';
	const BOSS_SQL_UNAME								=	'uname';
	const BOSS_SQL_GROUP_ID								=	'group_id';
	const BOSS_SQL_BOSS_HP								=	'hp';
	const BOSS_SQL_BOSS_LEVEL							=	'level';
	const BOSS_SQL_START_TIME							=	'start_time';
	const BOSS_SQL_LAST_ATTACK_TIME						=	'last_attack_time';
	const BOSS_SQL_LAST_INSPIRE_TIME					=	'last_inspire_time';
	const BOSS_SQL_ATTACK_HP							=	'attack_hp';
	const BOSS_SQL_INSPIRE								=	'inspire';
	const BOSS_SQL_REVIVE								=	'revive';
	const BOSS_SQL_FLAGS								=	'flags';
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */