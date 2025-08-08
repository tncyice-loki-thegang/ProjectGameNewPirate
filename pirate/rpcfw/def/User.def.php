<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: User.def.php 39837 2013-03-04 10:28:34Z wuqilin $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/User.def.php $
 * @author $Author: wuqilin $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-04 18:28:34 +0800 (一, 2013-03-04) $
 * @version $Revision: 39837 $
 * @brief
 *
 **/

class UserDef
{
	/**
	 * 用户已经删除
	 */
	const STATUS_DELETED = 0;

	/**
	 * online
	 */
	const STATUS_ONLINE = 1;

	/**
	 * offline
	 */
	const STATUS_OFFLINE = 2;

	/**
	 * suspend
	 */
	const STATUS_SUSPEND = 3;
	
	/**
	 * 封号
	 */
	const STATUS_BAN = 4;

	/**
	 * 最后在town
	 * Enter description here ...
	 * @var unknown_type
	 */
	const LAST_PLACE_TOWN = 1;

	/**
	 * 最后在club
	 */
	const LAST_PLACE_CLUB = 2;

	/**
	 * 最后在世界boss
	 * @var int
	 */
	const LAST_PLACE_BOSS = 3;

	/**
	 * 最后在深渊副本中
	 * @var int
	 */
	const LAST_PLACE_ABYSS = 4;
	

	/**
	 * 随机名字可用
	 */
	const RANDOM_NAME_STATUS_OK = 0;

	/**
	 * 随机名字已经被使用
	 */
	const RANDOM_NAME_STATUS_USED = 1;

	/**
	 * 删除时间最大值, 表示没有删除。
	 * 这个是2030-1-1
	 */
	const DTIME_NOT_DEL = 1893427200;

    /**
     * user表传给前端的字段
     */
	public static $USER_FIELDS = array(
		'uid',
		'pid',
		'create_time',	    
		'uname',
		'utid',
		'birthday',
		'group_id',
		'guild_id',
		'cur_execution',
		'execution_time',
		'last_buy_execution_time',
		'last_date_buy_execution_num',
		'cur_formation',
		'fight_cdtime',
		'protect_cdtime',
		'protect_cdtime_base',
		'atk_value',
		'vip',
		'recruit_num',
		'watch_num',
		'belly_num',
		'gold_num',
		'reward_point',
		'gift_cash',
		'prestige_num',
		'experience_num',
		'food_num',
		'blood_package',
		'last_login_time',
		'last_place_type',
		'last_place_data',
		'last_town_id',
		'last_xy',
		'msg',
		'last_salary_time',
		'copy_id',
		'last_copy_time',
		'copy_execution',
		'copy_execution_time',
		'vassal_execution',
		'vassal_execution_time',
		'attack_execution',
		'attack_execution_time',
		'resource_execution',
		'resource_execution_time',	
		'online_accum_time',	
		'achieve_point',
		'last_achieve_time',
		'status',
		'ban_chat_time',	
		'va_user',
		'mute',
		'gem_exp',			
		'visible_type',	
		'show_dress',				
        );
}

/**
 * 订单类型
 * Enter description here ...
 * @author idyll
 *
 */
class OrderType
{
	const NORMAL_ORDER  = 0;
	
	const ONLINE_REWARD_GOLD = 1;
	
	/**
	 * 福利充值
	 * @var unknown_type
	 */
	const Fuli_ORDER = 101;
	
	/**
	 * 错单处理
	 * @var unknown_type
	 */
	const ERROR_FIX_ORDER = 102;
}


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */