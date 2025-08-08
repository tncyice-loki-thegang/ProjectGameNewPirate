<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: SQLTable.conf.php 39824 2013-03-04 08:45:34Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/mergeServer/conf/SQLTable.conf.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2013-03-04 16:45:34 +0800 (一, 2013-03-04) $
 * @version $Revision: 39824 $
 * @brief
 *
 **/

class SQLTableConf
{
	/**
	 *
	 * 需要处理的ID
	 *
	 * @var array(id => table_name)
	 *
	 */
	public static $SQLMODIFYID = array(
		'uid' => 't_user',
		'item_id' => 't_item',
		'guild_id' => 't_guild',
		'hid' => 't_hero',
		'kid' => 't_task',
	);

	/**
	 *
	 * 需要处理的主要表
	 *
	 * @var array
	 */
	public static $SQLMODIFYMAINTABLE = array(
		't_guild',
		't_user',
	);

	/**
	 *
	 * 需要处理的主要表的相关表列表
	 *
	 * @var array
	 * <code>
	 * 		'table column id' => 'relative id'
	 * </code>
	 */
	public static $SQLMODIFYTABLE = array(
		't_guild'			=> array(
			't_guild' => array(
				'guild_id' => 'guild_id',
				'creator_uid' => 'uid',
				'president_uid' => 'uid',
			),
			't_guild_achieve' => array(
				'guild_id' => 'guild_id',
			),
		),

		't_user' 			=> array(
			't_user' => array (
				'uid' => 'uid',
				'master_hid' => 'hid',
				'guild_id' => 'guild_id',
			),
			't_guild_member' => array(
				'uid' => 'uid',
				'guild_id' => 'guild_id',
			),
			't_open_prize' => array(
				'uid' => 'uid',
			),
			't_user_title' => array(
				'uid' => 'uid',
			),
			't_user_achieve' => array(
				'uid' => 'uid',
			),
			't_bag' => array(
				'uid' => 'uid',
				'item_id' => 'item_id',
			),
			't_captain' => array(
				'uid' => 'uid',
			),
			't_user_enter_town' => array(
				'uid' => 'uid',
			),
			't_auto_atk' => array(
				'uid' => 'uid',
			),
			't_copy' => array(
				'uid' => 'uid',
			),
			't_elite_copy' => array(
				'uid' => 'uid',
			),
			't_festival' => array(
				'uid' => 'uid',
			),
			't_forge' => array(
				'uid' => 'uid',
			),

			't_hero' => array(
				'hid' => 'hid',
				'uid' => 'uid',
			),

			't_hero_formation' => array(
				'uid' => 'uid',
				'hid1' => 'hid',
				'hid2' => 'hid',
				'hid3' => 'hid',
				'hid4' => 'hid',
				'hid5' => 'hid',
				'hid6' => 'hid',
				'hid7' => 'hid',
				'hid8' => 'hid',
				'hid9' => 'hid',
			),
			't_friend' => array(
				'uid' => 'uid',
				'fuid' => 'uid',
			),
			't_hero_copy' => array(
				'uid' => 'uid',
			),
			't_kitchen' => array(
				'uid' => 'uid',
			),
			't_user_olympic' => array(
				'uid' => 'uid',
			),
			't_pet' => array(
				'uid' => 'uid',
			),
			't_practice' => array(
				'uid' => 'uid',
			),

			't_reward_gift' => array(
				'uid' => 'uid',
			),

			't_reward_gold' => array(
				'uid' => 'uid',
			),

			't_sailboat' => array(
				'uid' => 'uid',
			),
			't_sci_tech' => array(
				'uid' => 'uid',
			),
			't_smelting' => array(
				'uid' => 'uid',
			),
			't_soul' => array(
				'uid' => 'uid',
			),

			't_switch' => array(
				'uid' => 'uid',
			),

			't_talks' => array(
				'uid' => 'uid',
			),

			't_task' => array(
				'uid' => 'uid',
				'kid' => 'kid',
			),

			't_train' => array(
				'uid' => 'uid',
			),

			't_treasure' => array(
				'uid' => 'uid',
			),

			't_bbpay_gold' => array(
				'uid' => 'uid',
			),

			't_port_berth' => array(
				'uid' => 'uid',
			),

			't_daytask_info' => array(
				'uid' => 'uid',
			),

			//ALLBLUE
			't_allblue' => array(
				'uid' => 'uid',
			),

			//星盘
			't_astrolabe_info' => array(
				'uid' => 'uid',
			),

			't_astrolabe_stone' => array(
				'uid' => 'uid',
			),

			't_constellation_info' => array(
				'uid' => 'uid',
			),

			't_reward_sign' => array(
				'uid' => 'uid',
			),

			't_explore' => array(
				'uid' => 'uid',
			),

			't_charity' => array(
				'uid' => 'uid',
			),

			't_group_battle' => array(
				'uid' => 'uid',
			),

			't_honourshop' => array(
				'uid' => 'uid',
			),

			't_dig_activity' => array(
				'uid' => 'uid',
			),

			't_elves' => array(
				'uid' => 'uid',
			),

			't_reward_sprfestwelfare' => array(
				'uid' => 'uid',
			),

			't_impel' => array(
				'uid' => 'uid',
			),

			't_jewelry'  => array(
				'uid' => 'uid',
			)
		),
	);

	/**
	 *
	 * 需要删除的表
	 *
	 * @var array
	 */
	public static $SQLDELETE = array(
		't_guild_record',
		't_guild_apply',
		't_arena_msg',
		't_active',
		't_battle_record',
		't_boss_attack',
		't_boss',
		't_activity',
		't_first_down',
		't_group_battle',
		't_replay',
		't_server_defeat',
		't_user_defeat',
		't_daytask_task',
		't_copy_pass',
		't_exchange',
		't_mail',
		't_olympic_log',
		't_olympic',
		't_arena',
		't_arena_lucky',
		't_global',
		't_timer',
		't_repurchase',
		't_seller',
		't_random_name',
		't_vassal',
		't_world_resource_attack',
		't_world_resource',
		't_port_resource',
		't_port',
		't_merge_server',
		't_pay_back_info',
		't_pay_back_user',
	);

	/**
	 *
	 * 需要处理va字段的表
	 *
	 * @var array
	 */
	public static $SQLMODIFYVA = array(
		't_item'	=>		array(
			'va_item_text'	=>	array (
				'callback' => 'item',
			),
		),

		't_hero'	=>		array(
			'va_hero'	=>	array (
				'callback' => 'hero',
			),
		),

		't_user'	=>		array(
			'va_user'	=>	array (
				'callback' => 'user',
			),
		),

		't_train'	=>		array(
			'va_train_info'	=>	array (
				'callback' => 'train',
			),
		),

		't_explore' => array(
			'va_explore' => array(
				'callback' => 'explore',
			)
		),

		't_group_battle' => array(
			'va_copy_info' => array(
				'callback' => 'group_battle',
			)
		),

		't_allblue' => array(
			'va_farmfish_queueInfo' => array(
				'callback' => 'allblue',
			)
		)
	);

	/**
	 *
	 * 需要处理物品的表
	 *
	 * @var array
	 */
	public static $SQLMODIFYITEM = array (
		't_bag'		=>		'bag2item',
		't_hero'	=>		'hero2item',
		't_item'	=>		'item2item',
		't_explore' =>		'explore2item',
	);

	/**
	 *
	 * 需要处理名字的表
	 *
	 * @var array
	 */
	public static $SQLMODIFYNAME = array (
		't_user'	=> 		'uname',
		't_guild'	=>		'name',
	);

	/**
	 *
	 * 需要增加game_id的字段
	 * @var array
	 */
	public static $SQLADDGAMEID = array (
		't_user'
	);

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */