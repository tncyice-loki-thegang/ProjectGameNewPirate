<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Guild.cfg.php 39843 2013-03-04 10:36:47Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Guild.cfg.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-03-04 18:36:47 +0800 (一, 2013-03-04) $
 * @version $Revision: 39843 $
 * @brief
 *
 **/
class GuildConf
{

	/**
	 * 宴会奖励计算的最大offset
	 * @var int
	 */
	const MAX_FINAL_REWARD_OFFSET = 300;

	/**
	 * 工会宴会更新时间us
	 * @var int
	 */
	const BANQUET_UPDATE_INTERVAL = 20000;

	/**
	 * 可以弹劾的时间
	 * @var int
	 */
	const MAX_IMPEACHMENT_TIME = 604800;

	/**
	 * 弹劾所需要的金币
	 * @var int
	 */
	const IMPEACH_GOLD_NUM = 100;

	/**
	 * 主场景id
	 * @var int
	 */
	const MAIN_BGID = 31;

	/**
	 * 子场景id
	 * @var int
	 */
	const SUB_BGID = 31;

	/**
	 * 主音乐id
	 * @var int
	 */
	const MAIN_MUSIC_ID = 9;

	/**
	 * 子音乐id
	 * @var int
	 */
	const SUB_MUSIC_ID = 9;

	/**
	 * 前端战斗结果的回调
	 * @var int
	 */
	const TYPE_RESULT = 6;

	/**
	 * 鼓舞cd时间
	 * @var int
	 */
	const INSPIRE_CDTIME = 10;

	/**
	 * 资源更新
	 * @var string
	 */
	const BATTLE_NOTIFY = 'worldResource.battleFieldUpdate';

	/**
	 * 用户加入某个公会
	 * @var string
	 */
	const JOIN_NOTIFY = 'guild.joinGuild';

	/**
	 * 用户被踢时的回调
	 * @var string
	 */
	const KICK_CALLBACK = 're.guild.expel';

	/**
	 * 设置公会职务
	 * @var string
	 */
	const ROLE_NOTIFY = 'guild.setJob';

	/**
	 * 公会举办宴会
	 * @var string
	 */
	const BANQUET_NOTIFY = 'guild.banquetReady';

	/**
	 * 最大记录数
	 * @var int
	 */
	const MAX_RECORD_NUM = 5;

	/**
	 * 擂台个数
	 * @var unknown_type
	 */
	const MAX_ARENA_COUNT = 3;

	/**
	 * 基础连胜
	 * @var int
	 */
	const BASE_MAX_WIN = 3;

	/**
	 * 可以举办宴会的最小等级
	 * @var int
	 */
	const MIN_BANQUET_LEVEL = 1;

	/**
	 * 宴会持续时间
	 * @var int
	 */
	const BANQUET_TIME = 300;

	/**
	 * 宴会刷新时间
	 * @var int
	 */
	const BANQUET_REFRESH_TIME = 10;

	/**
	 * 开一次宴会的消耗
	 * @var int
	 */
	const BANQUET_COST = 1000;

	/**
	 * 宴会创建的最小提前时间
	 * @var int
	 */
	const MIN_BANQUET_TIME = 600;

	/**
	 * 俱乐部默认位置x
	 * @var int
	 */
	const CLUB_X = 1;

	/**
	 * 俱乐部默认位置y
	 * @var int
	 */
	const CLUB_Y = 2;

	/**
	 * 每个用户最大可以创建的公会数
	 * @var int
	 */
	const MAX_CREATE_NUM_PER_USER = 10;

	/**
	 * 服务器最大可以拥有的公会数量
	 * @var int
	 */
	const MAX_GUILD_NUM = 1000;

	/**
	 * 最大公会等级
	 * @var int
	 */
	const MAX_GUILD_LEVEL = 100;

	/**
	 * 创建所需要的贝里数
	 * @var int
	 */
	const CREATE_BELLY_COST = 10000;

	/**
	 * 名称的最大长度
	 * @var int
	 */
	const MAX_NAME_LENGTH = 6;

	/**
	 * 可申请公会的等级限制
	 * @var int
	 */
	const APPLY_LEVEL_LIMIT = 20;

	/**
	 * 一个用户最大可以拥有的公会数量
	 * @var int
	 */
	const MAX_OWN_GUILD = 1;

	/**
	 * 一个用户最大可同时申请的公会数量
	 * @var int
	 */
	const MAX_APPLY_COUNT = 3;

	/**
	 * 公会的宣言的最大长度
	 * @var int
	 */
	const MAX_SLOGAN_LENGTH = 80;

	/**
	 * 公会公告的最大长度
	 * @var int
	 */
	const MAX_POST_LENGTH = 80;

	/**
	 * 默认科技id
	 */
	const DEFAULT_TECH_ID = 1;

	/**
	 * 默认公会等级
	 */
	const DEFAULT_GUILD_LEVEL = 1;

	/**
	 * 默认威望等级
	 */
	const DEFAULT_EXP_LEVEL = 1;

	/**
	 * 默认阅历等级
	 */
	const DEFAULT_EXPERIENCE_LEVEL = 1;

	/**
	 * 默认宴会等级
	 */
	const DEFAULT_BANQUET_LEVEL = 1;

	/**
	 * 默认资源等级
	 * @var int
	 */
	const DEFAULT_RESOURCE_LEVEL = 1;

	/**
	 * 默认的公会会微id
	 * @var int
	 */
	const DEFAULT_EMBLEM_ID = 1;

	/**
	 * 最大成员数
	 * @var int
	 */
	const MAX_MEMBER_NUM = 400;

	/**
	 * 最大科技记录数
	 * @var int
	 */
	const MAX_TECH_RECORD_NUM = 4;

	/**
	 * 计算自然天的时间位移
	 */
	const DAY_OFFSET = 14400;

	/**
	 * 可以创建工会的最小等级
	 */
	const CREATE_LEVEL_LIMIT = 10;

	/**
	 * 鼓舞消耗金币数量
	 * @var int
	 */
	const INSPIRE_GOLD_NUM = 5;

	/**
	 * 开启新的旗子
	 * @var int
	 */
	const OPEN_FLAG_GOLD_NUM = 50;

	/**
	 * 鼓舞消耗阅历数量
	 * @var int
	 */
	const INSPIRE_EXPERIENCE_NUM = 5;

	/**
	 * 宴会开始的回调函数
	 * @var unknown_type
	 */
	const NOTIFY_CALLBACK = 'guild.startBanquet';

	/**
	 * 公会的城镇模板id
	 * @var int
	 */
	const GUILD_TEMPLATE_ID = 20;
	
	/**
	 * 用户权限表
	 * @var array
	 */
	static $ARR_PRIV = array (
			GuildPrivType::CHANGE_EMBLEM => array (GuildRoleType::PRESIDENT,
					GuildRoleType::VICE_PRESIDENT ),
			GuildPrivType::CHECK_APPLY => array (GuildRoleType::PRESIDENT,
					GuildRoleType::VICE_PRESIDENT ),
			GuildPrivType::DEV_BANQUET_TECH => array (GuildRoleType::PRESIDENT,
					GuildRoleType::VICE_PRESIDENT ),
			GuildPrivType::GUILD_BATTLE => array (GuildRoleType::PRESIDENT,
					GuildRoleType::VICE_PRESIDENT ),
			GuildPrivType::KICK_MEMBER => array (GuildRoleType::PRESIDENT,
					GuildRoleType::VICE_PRESIDENT ),
			GuildPrivType::MODIFY_POST => array (GuildRoleType::PRESIDENT,
					GuildRoleType::VICE_PRESIDENT ),
			GuildPrivType::ROLE_TRANS => array (GuildRoleType::PRESIDENT ),
			GuildPrivType::SET_DEFAULT_TECH => array (GuildRoleType::PRESIDENT,
					GuildRoleType::VICE_PRESIDENT ),
			GuildPrivType::START_BANQUET => array (GuildRoleType::PRESIDENT,
					GuildRoleType::VICE_PRESIDENT ),
			GuildPrivType::SET_VP => array (GuildRoleType::PRESIDENT ),
			GuildPrivType::BUY_MEMBER_NUM => array (GuildRoleType::PRESIDENT ),
			GuildPrivType::BUY_EMBLEM => array (GuildRoleType::PRESIDENT ),
			GuildPrivType::APPLY_LIST => array (GuildRoleType::PRESIDENT,
					GuildRoleType::VICE_PRESIDENT ),
			GuildPrivType::DISMISS => array (GuildRoleType::PRESIDENT ),
			GuildPrivType::MODIFY_PASSWD => array (GuildRoleType::PRESIDENT ), );

	/**
	 * 最大金币购买次数
	 * @var int
	 */
	const MAX_GOLD_MEMBER_NUM = 5;

	/**
	 * 公会等级与vp之间的关系
	 * @var int
	 */
	static $ARR_VP_NUM = array (1 => 2, 50 => 3, 100 => 4, 150 => 5 );

	/**
	 * 职位与排名之间的关系
	 * @var array
	 */
	static $ARR_OFFICIAL_RANK = array (6 => GuildOfficialType::CORE_CAPTAIN,
			20 => GuildOfficialType::MAIN_CAPTAIN, 50 => GuildOfficialType::SUB_CAPTAIN,
			100 => GuildOfficialType::LITTLE_CAPTAIN, 150 => GuildOfficialType::FORMAL_PIRATE,
			999 => GuildOfficialType::INFORMAL_PIRATE );

	/**
	 * 官职系统
	 * @var array
	 */
	static $ARR_OFFICIAL_RESOURCE_COEF = array (GuildOfficialType::CORE_CAPTAIN => 1.25,
			GuildOfficialType::MAIN_CAPTAIN => 1.15, GuildOfficialType::SUB_CAPTAIN => 1.1,
			GuildOfficialType::LITTLE_CAPTAIN => 1, GuildOfficialType::FORMAL_PIRATE => 0.9,
			GuildOfficialType::INFORMAL_PIRATE => 0.8 );

	/**
	 * 角色系数
	 * @var array
	 */
	static $ARR_ROLE_RESOURCE_COEF = array (GuildRoleType::PRESIDENT => 1.35,
			GuildRoleType::VICE_PRESIDENT => 1.25 );

	/**
	 * 宴会举办的通知时间
	 * @var array
	 */
	static $ARR_NOTIFY_TIME = array (180, 120, 60, - 60, - 120, - 180, - 240 );

	/**
	 * 战旗和buffer之间的关系
	 * @var array
	 */
	static $MAP_FLAG_BUFFER = array (
			1 => array ('attackLevel' => 0, 'defendLevel' => 0, 'maxWin' => 2 ),
			2 => array ('attackLevel' => 10, 'defendLevel' => 0, 'maxWin' => 0 ),
			3 => array ('attackLevel' => 0, 'defendLevel' => 10, 'maxWin' => 0 ),
			4 => array ('attackLevel' => 10, 'defendLevel' => 10, 'maxWin' => 0 ) );
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
