<?php
/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: User.cfg.php 40234 2013-03-07 07:45:41Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/User.cfg.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-07 15:45:41 +0800 (四, 2013-03-07) $
 * @version $Revision: 40234 $
 * @brief
 *
 **/
class UserConf
{
	/**
	 * 是否缓存battle信息
	 */
	const USE_BATTLE_CACHED = true;
	
	/**
	 * 是否写battle cache 日志
	 * @var unknown_type
	 */
	const LOG_BATTLE_HIT = true;
	
	/**
	 * 缓存的battle时间失效时间
	 */
	const BATTLE_EXPIRED_TIME =  61200;

	const SAFE_DEL_TIME = 600;

	/**
	 * 最大登录重试次数
	 * @var int
	 */
	const MAX_LOGIN_RC = 10;

	/**
	 *
	 * 登录重试间隔
	 * @var int
	 */
	const LOGIN_RC_INTERVAL = 100000;

	/**
     * 用户配置信息
     * <code>
     * array
     * {
     * 0:用户模版id，不可重复
	 * array
     * {
     * 性别 ：0：女 1：男
	 * 主英雄htid
	 * }
	 * }
	 * </code>
     *
     */
	public static $USER_INFO = array(
		1 => array(
			0,
			11002,
		),

		2 => array(
			1,
			11001,
		),

		3 => array(
			1,
			11003,
            ),

		4 => array(
			0,
			11006,
		),

		5 => array(
			0,
			11005,
		),

		 6 => array(
        	1,
			11004,
            ),
	);

	/**
	 * 主角英雄
	 */
	public static $MASTER_HEROES = array(11001=>1, 11002=>1, 11003=>1, 11004=>1, 11005=>1, 11006=>1);

	/**
	 * 保存用户登录日期的总数
	 */
	const LOGIN_DATE_NUM = 15;

	/**
	 * 能英雄的最打数量
	 */
	const CAN_RECRUIT_NUM = 1000;

	/**
	 * 服务器可创建用户数量
	 * @var int
	 */
	const MAX_USER_NUM = 1;

	/**
	 * 用户名字最大长度
	 * @var int
	 */
	const MAX_USER_NAME_LEN = 15;

	/**
	 * 用户名字最小长度
	 * @var int
	 */
	const MIN_USER_NAME_LEN = 1;

	/**
	 * 是否可以删除用户
	 */
	const ENABLE_DEL = false;

	/**
	 * 删除用户需要等待天数
	 */
	const SUSPEND_DAY = 0.000694;

	/**
	 * 返回随机名最大的数量
	 */
	const NUM_RANDOM_NAME = 20;

	/**
	 * 从数据库选择随机名范围
	 */
	const RANDOM_NAME_OFFSET = 1000;

	/**
	 * 人物最大等级
	 */
	const MAX_LEVEL = 99999;

	/**
	 * 初始阅历
	 */
	const INIT_EXPERIENCE = 1000000;

	/**
	 * 初始食物
	 */
	const INIT_FOOD = 0;

	/**
	 * 初始的belly
	 */
	const INIT_BELLY = 10000000;

	/**
	 * 初始金币
	 */
	const INIT_GOLD = 1000000;

	/**
	 * 初始vip
	 */
	const INIT_VIP = 9;

	/**
	 * 初始的威望
	 */
	const INIT_PRESTIGE = 0;

	/**
	 * 初始行动力
	 */
	const INIT_EXECUTION = 100000;

	/**
	 * 第一次登录接受任务id
	 */
	const INIT_TASK_ID = 15;

	/**
	 * 最大行动力
	 */
	const MAX_EXECUTION = 100000;

	/**
	 * 恢复一点行动力需要多少秒
	 */
	const SECOND_PER_EXECUTION = 120;

	/**
	 * 初始招募的英雄htid
	 */
	static $INIT_RECRUIT_HERO = array();

	/**
	 * 初始所有英雄htid
	 */
	static $INIT_ALL_HERO = array(10062,10024,10021,10066);

	/**
	 * 初始可以观战的数量
	 */
	const WATCH_INIT = 0;

	/**
	 * 贝里最大上限
	 */
	const BELLY_MAX = 2000000000;

	/**
	 * 金币最大上限值
	 */
	const GOLD_MAX = 214748364;

	/**
	 *礼金最大上限
	 */
	const GIFT_CASH_MAX = 214748364;

	/**
	 * 积分最大上限
	 */
	const REWARD_POINT_MAX = 214748364;

	/**
	 * 威望最大上限
	 */
	const PRESTIGE_MAX = 2000000000;

	/**
	 * 阅历最大上限
	 */
	const EXPERIENCE_MAX = 2000000000;

	/**
	 * 食物最大上限
	 */
	const FOOD_MAX = 214748364;

	/**
	 * 血池最大上限
	 */
	const BLOOD_PACKAGE_MAX = 2099999999;

	/**
	 * 最大行动力
	 */
	const MAX_MOVEMENT = 1000;

	/**
	 * 初始group id
	 */
	const INIT_GROUP_ID = 0;

	/**
	 * 人物血池等级系数
	 */
	const BLOOD_PACKAGE_LEVEL = 0;

	/**
	 * 主角英雄初始等级
	 */
	const INIT_MASTER_HERO_LEVEL = 1;

	/**
	 * 初始攻击值
	 */
	const INIT_ATK_VALUE = 50;

	/**
	 * 缺省的消息
	 */
	const DEFAULT_MSG = UserMsg::DEFAULT_MSG;

	/**
	 * 展示时装
	 * @var unknown_type
	 */
	const SHOW_DRESS = 1;

	/**
	 * msg 最大长度
	 */
	const MSG_MAX_LEN = 30;

	public static $Vip = array(
		0 => array(
			'gold_need' => 0,
			'recruit' => 0,
			'watch' => 0,
			'train_item' => 0,
			'purchase_power' =>0,
			'building_item' => 0
		),

		1 => array(
			'gold_need' => 100,
			'recruit' => 1,
			'watch' => 1,
			'train_item' => 1,
			'purchase_power' =>1,
			'building_item' => 1
		)
	);

	/**
	 * 威望值对应的金矿的最大数量
	 * Enter description here ...
	 * @var unknown_type
	 */
	public static $GOLD_MINE_MAX = array(
	0 => 1,
	50000 => 2,
	);

	/**
	 * 排行榜最大数量
	 * @var unknown_type
	 */
	const MAX_TOP = 100;

	/**
	 * 保留的pid的最大值,
	 */
	const PID_MAX_RETAIN = 10;

	/**
	 * 随机阵营加金币数量
	 */
	const GOLD_4_RANDOM_GROUP = 100;

	//session有效时间 user.login检查
	const LOGIN_DIFF_TIME = 900; //秒

	/**
	 * 初始的衣服模板id, 给主角英雄穿上
	 */
	const INIT_CLOTHING_TID = 14001;

	/**
	 * 服务器人数限制
	 */
	const MAX_ONLINE_USER = 1000;

	/**
	 * blood 价格
	 */
	const BLOOD_PER_BELLY = 50;

	/**
	 * $VISIBLE_COUNT 数组中的第三个
	 * Enter description here ...
	 * @var unknown_type
	 */
	const DEFAULT_VISIBLE_TYPE = 3;

	/**
	 * 默认宝石经验
	 * @var unknown_type
	 */
	const DEFAULT_GEM_EXP = 0;

	/**
	 * 城镇中显示人数
	 * Enter description here ...
	 * @var unknown_type
	 */
	public static $VISIBLE_COUNT = array(
		0,
		30,
		60,
		100,
		10000,
		15,
	);

	/**
	 * 开始位置花金币的基础值
	 * Enter description here ...
	 * @var unknown_type
	 */
	const OPEN_HERO_POS_COST_BASE = 50;

	/**
	 * 充值奖励持续多少天
	 * Enter description here ...
	 * @var unknown_type
	 */
	const PAY_REWARD_PERIOD = 900;

	public static $PAY_REWARD_ITEM = array(20018, 45025, 45026, 45027, 45029, 45031);


	/**
	 * 金币增加好感度最大次数
	 */
	const GOODWILL_NUM_BY_GOLD = 99999;

	/**
	 * 金币增加好感度消耗金币基础值
	 * Enter description here ...
	 * @var unknown_type
	 */
	const GOODWILL_BY_GOLD_BASE = 10;

	/**
	 * 保存每天消耗金币数记录的个数
	 * Enter description here ...
	 * @var unknown_type
	 */
	const SPEND_GOLD_DATE_NUM = 15;

	const BAN_MSG_MAX_LEN = 30;

	/**
	 * 每天好感度传承次数
	 * Enter description here ...
	 * @var unknown_type
	 */
	const NUM_HERIAGE_GOODWILL = 1;

	/**
	 * opclient登录奖励物品
	 * Enter description here ...
	 * @var unknown_type
	 */
	public static $OPCLIENT_LOGIN_REWARD_ITEM = array(
		70301 => 1,
		70401 => 1,
		120009 => 1,
	);

	/**
	 * 前端配置数组最大值
	 * Enter description here ...
	 * @var unknown_type
	 */
	const VA_CONFIG_SIZE = 10;

	/**
	 * 前端arr配置数组最大值
	 * Enter description here ...
	 * @var unknown_type
	 */
	const ARR_CONFIG_SIZE = 50;
	
	/**
	 * 缓存battle信息表
	 * @var unknown_type
	 */
	const BATTLE_ARR_HERO_CACHE_TIME = 7200;
}

class ExtraExecutionConf
{
	/**
	 * 各种令刷新开始时刻
	 */
	public static $EXECUTION_RESET_DEFAULT = array(
		'12:00:00',
		'19:30:00'
	);

	public static $EXECUTION_CFG = array(
		'vassal_execution' => 1,
		'copy_execution' => 1,
		'resource_execution' => 1,
		'attack_execution' => 1,
	);
}

class GroupConf
{
	public static $GROUP = array(1=>array(), 2=>array(), 3=>array());
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
