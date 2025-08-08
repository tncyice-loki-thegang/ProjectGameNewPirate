<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: QueryCache.cfg.php 38741 2013-02-20 06:37:13Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/QueryCache.cfg.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2013-02-20 14:37:13 +0800 (三, 2013-02-20) $
 * @version $Revision: 38741 $
 * @brief
 *
 **/
class QueryCacheConf
{

	/**
	 * 是否启用QueryCache
	 * @var string
	 */
	const QUERY_CACHE = 'LocalQueryCache';

	static $ARR_TABLE_DEF = array ('t_bag' => array (0 => 'uid', 1 => 'gid' ), 
			't_guild' => array (0 => 'guild_id' ), 't_guild_member' => array (0 => 'uid' ), 
			't_guild_record' => array (0 => 'grid' ), 't_battle_record' => array (0 => 'brid' ), 
			't_arena_lucky' => array (0 => 'begin_date' ), 't_captain' => array (0 => 'uid' ), 
			't_user_enter_town' => array (0 => 'town_id', 1 => 'uid' ), 
			't_activity' => array (0 => 'activity_id' ), 
			't_copy' => array (0 => 'uid', 1 => 'copy_id' ), 
			't_first_down' => array (0 => 'army_id', 1 => 'rank' ), 
			't_replay' => array (0 => 'uid', 1 => 'army_id' ), 
			't_server_defeat' => array (0 => 'army_id', 1 => 'group_id', 2 => 'rp_id' ), 
			't_user_defeat' => array (0 => 'uid', 1 => 'army_id', 2 => 'rp_id' ), 
			't_forge' => array (0 => 'uid' ), 't_hero' => array (0 => 'hid' ), 
			't_item' => array (0 => 'item_id' ), 't_kitchen' => array (0 => 'uid' ), 
			't_pet' => array (0 => 'uid' ), 't_port_berth' => array (0 => 'uid' ), 
			't_port_resource' => array (0 => 'port_id', 1 => 'page_id', 2 => 'resource_id' ), 
			't_port' => array (0 => 'port_id' ), 't_sailboat' => array (0 => 'uid' ), 
			't_sci_tech' => array (0 => 'uid' ), 't_talks' => array (0 => 'uid' ), 
			't_train' => array (0 => 'uid' ), 't_user' => array (0 => 'uid' ), 
			't_world_resource' => array (0 => 'resource_id' ), 't_timer' => array (0 => 'tid' ), 
			't_practice' => array (0 => 'uid' ), 't_global' => array (0 => 'sq_id' ), 
			't_smelting' => array (0 => 'uid' ), 
			't_guild_achieve' => array (0 => 'guild_id', 1 => 'achieve_id' ), 
			't_user_title' => array (0 => 'uid', 1 => 'title_id' ), 
			't_user_achieve' => array (0 => 'uid', 1 => 'achieve_id' ), 
			't_boss_attack' => array (0 => 'boss_id', 1 => 'uid' ), 't_task' => array (0 => 'kid' ), 
			't_copy_pass' => array (0 => 'copy_id', 1 => 'uid' ), 
			't_elite_copy' => array (0 => 'uid' ), 
			't_open_prize' => array (0 => 'uid', 1 => 'prize_id' ), 
			't_exchange' => array (0 => 'uid' ), 't_active' => array (0 => 'uid' ), 
			't_hero_copy' => array (0 => 'uid', 1 => 'copy_id' ), 
			't_user_olympic' => array (0 => 'uid' ), 't_charity' => array (0 => 'uid' ) );
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */