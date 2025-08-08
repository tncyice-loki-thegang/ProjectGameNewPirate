<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Boss.cfg.php 40401 2013-03-09 13:19:47Z wuqilin $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Boss.cfg.php $
 * @author $Author: wuqilin $(jhd@babeltime.com)
 * @date $Date: 2013-03-09 21:19:47 +0800 (六, 2013-03-09) $
 * @version $Revision: 40401 $
 * @brief
 *
 **/

class BossConfig
{
	//发奖前等待时间
	const BOSS_REWARD_SLEEP_TIME						=		3;

	//boss 通知前端的概率
	const BOSS_SEND_PROBABILITY							=		1000;
	//boss 通知前端的最大概率
	const BOSS_SEND_MAX_PROBABILITY						=		10000;

	//boss bot 设置前置时间
	const BOSS_BOT_SET_TIME_PRE							=		300;

	//boss bot 设置后置时间
	const BOSS_BOT_SET_TIME_SUFFIX						=		3600;

	//boss bot 减少cd后的攻击间隔
	const BOSS_BOT_SUB_ATTACK_TIME						=		20;

	//boss bot 未减少cd后的攻击间隔
	const BOSS_BOT_ATTACK_TIME							=		50;

	//boss bot 排行偏移
	const BOSS_BOT_ORDER_EXCURSION						=		4;

	//boss战战斗冷却时间(s)
	const FREEZE_TIME									=		45;

	//boss攻击血量显示列表长度
	const BOSS_ATTACK_LIST_MAX_NUM						=		10;

	//战斗开始前提示时间(s)
	const BOSS_COMING_TIME								=		300;

	//boss更新延迟时间(s)
	const BOSS_END_TIME_SHIFT							=		300;

	//最大鼓舞次数
	const MAX_INSPIRE_NUM								=		10;

	//鼓舞(阅历)所需要的阅历
	const INSPIRE_REQ_EXPERIENCE						=		10;
	//鼓舞(阅历)成功几率
	const INSPIRE_EXPERIENCE_RAND						=		8000;
	//鼓舞(阅历)减少的几率
	const INSPIRE_EXPERIENCE_DEC_RAND					=		600;
	//鼓舞(阅历)最大成功几率
	const INSPIRE_EXPERIENCE_MAX_RAND					=		10000;

	//鼓舞(金币)所需要的金币
	const INSPIRE_REQ_GOLD								=		5;

	//减少cd可以减少的时间(s)
	const SUB_CDTIME									=		30;
	//减少cd所需要的金币
	const SUB_CDTIME_REQ_GOLD							=		10;

	//复活开启所需要的VIP LEVEL
	const REVIVE_REQ_VIP_LEVEL							=		6;
	//复活所需的金币
	const REVIVE_REQ_GOLD								=		5;
	//复活所需的金币增加量
	const REVIVE_REQ_INC_GOLD							=		5;

	//鼓舞每等级增加的物理攻击系数
	const INSPIRE_INC_PHYSICAL_ATTACK_PRECENT			=		100;
	//鼓舞每等级增加的必杀攻击系数
	const INSPIRE_INC_KILL_ATTACK_PRECENT				=		100;
	//鼓舞每等级增加的魔法攻击系数
	const INSPIRE_INC_MAGIC_ATTACK_PRECENT				=		100;

	//每次攻击的声望
	const PRE_ATTACK_MAX_PRESTIGE						=		20;
	const PRE_ATTACK_MIN_PRESTIGE						=		5;
	const PRE_ATTACK_PRESTIGE_MODULUS					=		50000;

	//排名第一的阵营的奖励系数
	const GROUP_FIRST_REWARD_MODULUS					=		1.1;
	//排名第二的阵营的奖励系数
	const GROUP_SECOND_REWARD_MODULUS					=		1.05;
	//排名第三的阵营的奖励系数
	const GROUP_THIRD_REWARD_MODULUS					=		1.0;
	//默认的奖励系数
	const GROUP_DEFAULT_REWARD_MODULUS					=		1.0;
	
	//最大战斗回合数
	const BATTLE_ROUND = 20;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */