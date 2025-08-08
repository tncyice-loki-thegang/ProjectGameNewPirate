<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Guild.def.php 39840 2013-03-04 10:33:26Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Guild.def.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-03-04 18:33:26 +0800 (一, 2013-03-04) $
 * @version $Revision: 39840 $
 * @brief
 *
 **/
class GuildDef
{

	/**
	 * 宴会升级表
	 * @var int
	 */
	const BANQUET_LEVEL_ID = 10005;

	/**
	 * 最大的城镇id
	 * @var int
	 */
	const MIN_GUILD_ID = 10000;
	
	/**
	 * 最大的公会id
	 * @var int
	 */
	const MAX_GUILD_ID = 1000000;

	static $ARR_GUILD_FIELD = array ('guild_id', 'name', 'group_id', 'creator_uid', 'create_time',
			'president_uid', 'gold_member_num', 'default_tech', 'guild_level', 'guild_data',
			'exp_level', 'exp_data', 'experience_level', 'experience_data', 'resource_level',
			'resource_data', 'banquet_level', 'reward_point', 'current_emblem_id',
			'last_banquet_time', 'week_contribute_data', 'last_contribute_time', 'va_info',
			'vip_reward_time', 'last_level_time' );

	static $ARR_MEMBER_FIELD = array ('guild_id', 'uid', 'role_type', 'contribute_data',
			'day_belly_num', 'last_belly_time', 'last_gold_time', 'last_banquet_time', 'status',
			'va_info' );

	static $ARR_VALID_TECH = array (GuildTech::EXPERIENCE, GuildTech::GUILD, GuildTech::EXP,
			GuildTech::RESOURCE );

	const DEFAULT_PASSWD = 'rxhzw';
}

class GuildMemberStatus
{

	const OK = 1;

	const DEL = 0;
}

class GuildStatus
{

	const OK = 1;

	const DEL = 0;
}

class GuildRoleType
{

	/**
	 * 平民
	 * @var int
	 */
	const NONE = 0;

	/**
	 * 会长
	 * @var int
	 */
	const PRESIDENT = 1;

	/**
	 * 副会长
	 * @var int
	 */
	const VICE_PRESIDENT = 2;
}

class GuildApplyStatus
{

	const OK = 1;

	const CANCEL = 2;

	const REFUSED = 3;

	const AGREED = 4;
}

class GuildPrivType
{

	const CHECK_APPLY = 1;

	const MODIFY_POST = 2;

	const KICK_MEMBER = 3;

	const SET_VP = 4;

	const GUILD_BATTLE = 5;

	const START_BANQUET = 6;

	const CHANGE_EMBLEM = 7;

	const SET_DEFAULT_TECH = 8;

	const ROLE_TRANS = 9;

	const DEV_BANQUET_TECH = 10;

	const BUY_MEMBER_NUM = 11;

	const BUY_EMBLEM = 12;

	const APPLY_LIST = 13;

	const DISMISS = 14;

	const MODIFY_PASSWD = 15;
}

/**
 * 公会科技
 * @author Hoping
 *
 */
class GuildTech
{

	/**
	 * 公会等级
	 * @var unknown_type
	 */
	const GUILD = 1;

	/**
	 * 公会阅历
	 * @var int
	 */
	const EXPERIENCE = 2;

	/**
	 * 公会威望
	 * @var int
	 */
	const EXP = 3;

	/**
	 * 资源科技
	 * @var int
	 */
	const RESOURCE = 4;

	/**
	 * 科技数据库映射关系
	 * @var array
	 */
	static $TECH_DB_MAP = array (
			self::EXPERIENCE => array ('level' => 'experience_level', 'data' => 'experience_data' ),
			self::EXP => array ('level' => 'exp_level', 'data' => 'exp_data' ),
			self::GUILD => array ('level' => 'guild_level', 'data' => 'guild_data' ),
			self::RESOURCE => array ('level' => 'resource_level', 'data' => 'resource_data' ) );

	/**
	 * 科技升级id
	 * @var int
	 */
	static $TECH_LEVEL_MAP = array (self::EXPERIENCE => 10004, self::EXP => 10003,
			self::GUILD => 10001, self::RESOURCE => 10002 );
}

class GuildContributeType
{

	const BELLY = 1;

	const GOLD = 2;

	const SAIL = 3;
}

/**
 * 公会官职
 * @author Hoping
 *
 */
class GuildOfficialType
{

	/**
	 * 总队长
	 * @var int
	 */
	const CORE_CAPTAIN = 1;

	/**
	 * 精英队长
	 * @var int
	 */
	const MAIN_CAPTAIN = 2;

	/**
	 * 分区队长
	 * @var int
	 */
	const SUB_CAPTAIN = 3;

	/**
	 * 小队长
	 * @var int
	 */
	const LITTLE_CAPTAIN = 4;

	/**
	 * 正式海盗
	 * @var int
	 */
	const FORMAL_PIRATE = 5;

	/**
	 * 见习海盗
	 * @var int
	 */
	const INFORMAL_PIRATE = 6;
}

class EmblemType
{

	const NORMAL = 1;

	const GOLD = 2;

	static $ARR_VALIB = array (self::NORMAL, self::GOLD );
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */