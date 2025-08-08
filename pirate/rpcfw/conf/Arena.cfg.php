<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Arena.cfg.php 39866 2013-03-05 02:53:39Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Arena.cfg.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-05 10:53:39 +0800 (二, 2013-03-05) $
 * @version $Revision: 39866 $
 * @brief 
 *  
 **/



class ArenaConf
{	
	/**
	 * memcahce 超时时间
	 * @var unknown_type
	 */
	const MEM_EXPIRED_TIME = 900;
	
	/**
	 * 取用户名次前面几个做为对手
	 */
	const OPPONENT_BEFOR = 3;
	
	/**
	 * 取用户名次后面几个做为对手
	 */
	const OPPONENT_AFTER = 2;	
	
	/**
	 * 每天免费挑战次数
	 */
	const FREE_CHALLENGE_NUM = 20;
	
	/**
	 * 每次挑战的基础价格
	 */
	//const BASE_CHALLENGE_PRICE = 2;
	
	/**
	 * 最高价格
	 */
	//const MAX_CHALLENGE_PRICE = 10;
	
	/**
	 * suc cd time 
	 * second
	 */
	const FIGHT_SUC_CDTIME = 0;
	
	/**
	 * fail cd time
	 * second
	 */
	const FIGHT_FAIL_CDTIME = 180;
	
	/**
	 * msg数量
	 */
	const MSG_NUM = 10;
	
	/**
	 * 每金币消耗多少cdtime second
	 */
	const CDTIME_PER_GOLD = 60;
	
	/**
	 * 排行榜数量
	 */
	const NUM_OF_POSITION_LIST = 10;
	
	/**
	 * 幸运排名取值
	 * 里层数组第一、二个为排名取值范围，第三个为奖励金币数量
	 * 如：从1到500中取一个随机数， 奖100金
	 */
	static $LUCKY_POSITION_CONFIG  = array(
        array(1,	500,	70011),
        array(1,	500,	70011),
        array(1,	500,	70011),
        array(1,	20,		70010),
        array(21,	50,		70010),
        array(51,	100,	70010),
        array(101,	200,	70010),
        array(201,	300,	70010),
        array(301,	400,	70010),
        array(401,	500,	70010),
        );
        
    const LUCKY_ITEM_ID = 120008;
        
    /**
     * 使用物品得到多少金
     * itemId => gold num
     * Enter description here ...
     * @var unknown_type
     */
    static $ITEM_GOLD = array(
    	70011 => 100,
    	70010 => 50,
    );
	
	/**
	 * 奖励此数（包括）之前的排名
	 */
	const REWARD_BEFOR_POSITION = 10000;
	
	/**
	 * 每次从数据库取多少数据,要比REWARD_BEFOR_POSITION小
	 * 不能超过100
	 */
	const NUM_OF_QUERY = 100;	
	
	/**
	 * 发奖用，一次连续发多少个用户, 只是修改数据库的一个字段，值可以适当大一点
	 */
	const NUM_OF_REWARD_PER = 10;
	
	/**
	 * 每次发 NUM_OF_REWARD_PER 后休眠多少毫秒
	 */
	const SLEEP_MTIME = 50; 
	
	/**
	 * 默认奖励的belly
	 */
	const DEFAULT_REWARD_BELLY = 1000;
	
	/**
	 * 默认奖励的experience
	 */
	const DEFAULT_REWARD_EXPERIENCE = 0;
	
	/**
	 * 默认奖励的prestige
	 */
	const DEFAULT_REWARD_PRESTIGE = 0;
	
	/**
	 * 默认奖励gold
	 */
	const DEFAULT_REWARD_GOLD = 0;
	
	/**
	 * 挑战失败奖励威望
	 */
	const REWARD_FAIL_CHALLENGE_PRESTIGE = 10;
	
	/**
	 * 挑战成功奖励威望
	 */
	const REWARD_SUC_CHALLENGE_PRESTIGE = 10;
	
	/**
	 * 挑战失败奖励阅历
	 */
	const REWARD_FAIL_CHALLENGE_EXPERIENCE = 10;
	
	/**
	 * 挑战成功奖励阅历
	 */
	const REWARD_SUC_CHALLENGE_EXPERIENCE = 10;
	
	/**
	 * 竞技场战斗背景ID 
	 */
	const BATTLE_BJID = 31;
	
	/**
	 * 背景音乐id
	 */
	const BATTLE_MUSIC_ID = 38;
	
	//3600*10 是随意取的值。用来区分是否是上一轮发的奖。
	//最大可取值 每轮天数×24 - 发奖锁定时间	
	//别取太小了，不然出错重做的时候吧当前期的当上一期就悲剧了。
	const REWARD_REDO_LIMIT_HOURS = 15;
	
	//重发的时候保留多少个小时，用来区分上一次发奖
	// REWARD_REDO_LIMIT_HOURS 必须大于 ArenaDateConf::LAST_DAYS*24 - REWARD_REDO_LIMIT_HOURS_RETAIN
	const REWARD_REDO_LIMIT_HOURS_RETAIN = 6;
	
	
	/**
	 * 显示击败次数
	 * Enter description here ...
	 * @var unknown_type
	 */
	const DEFEATED_NOTICE_NUM = 3;
	
	/**
	 * 刷新可挑战对手消耗的金币
	 * @var unknown_type
	 */
	const REFRESH_OPPONENTS_GOLD = 5;
	
}



/**
 * 广播优先级
 * 当都符合广播条件时， 选择优先级最高的广播，
 * 越小优先级越高
 * Enter description here ...
 * @author idyll
 *
 */
class ArenaBroadcast
{
	/**
	 * 第一名优先级
	 * Enter description here ...
	 * @var unknown_type
	 */	
	const PRI_TOP1 = 0;
	
	/**
	 * 连胜被终止优先级
	 * Enter description here ...
	 * @var unknown_type
	 */
	const PRI_CONTINUE_END = 1;
	
	/**
	 * 连胜次数优先级
	 * Enter description here ...
	 * @var unknown_type
	 */
	const PRI_CONTINUE_SUC = 2;
	
	/**
	 * 连续上升名次优先级
	 * Enter description here ...
	 * @var unknown_type
	 */
	const PRI_UPGRADE_CONTINUE = 3;
	
	/**
	 * 连胜
	 * Enter description here ...
	 * @var unknown_type
	 */
	public static $ARR_CONTINUE_SUC = array(
		15 => 0,
		20 => 1,
		30 => 2,
		50 => 3,
	);
	
	/**
	 * 连续上升名次
	 * Enter description here ...
	 * @var unknown_type
	 */
	public static $ARR_UPGRADE_CONTINUE = array(
		200 => 0,
		500 => 1,
		800 => 2,
		1000 => 3,
	);
	
	/**
	 * 终结连胜>=15时候，广播
	 * Enter description here ...
	 * @var unknown_type
	 */
	const MIN_CONTINUE_END = 15;
};

class ArenaActivity
{
	const BEGIN_TIME = '2017-12-10 04:00:00';

	const END_TIME = '2018-12-10 04:00:00';
	
	const RATE = 2;
}


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
