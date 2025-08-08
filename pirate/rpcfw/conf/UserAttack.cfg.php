<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UserAttack.cfg.php 27057 2012-09-12 09:25:09Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/UserAttack.cfg.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-09-12 17:25:09 +0800 (三, 2012-09-12) $
 * @version $Revision: 27057 $
 * @brief
 *
 **/

class UserAttackConfig
{
	//保护时间基础值(s)
	const PROTECT_TIME_BASIC							=			1800;

	//保护时间系数
	public static $PROTECT_TIME_MODULUS		= array (
		0 => 0,
		5 => 500,
		20 => 1000,
		40 => 2000,
		50 => 4000,
		80 => 6000,
		100 => 8000,
		150 => 9000,
	);

	const MODULUS										=			10000;

	//攻击值声望基础
	const PRESTIGE_BASIC								=			10;

	//等级差除数
	const PRESTIGE_ATTACK_DIVIDE						=			5;

	//紫名玩家攻击值最低值
	const ATTACK_VALUE_COLOR_PURPLEP_MIN				=			251;

	//胜负声望系数
	public static $PERSTIGE_SUCCESS_MODULUS 	= array (
		0	=> 5000,
		1	=> 0
	);

	//攻击所需行动力
	const ATTAK_EXECUTION								=			1;

	//攻击增加的战斗CD
	const ATTACK_FIGHT_CDTIME							=			10;

	//攻打战斗背景ID
	const USER_ATTACK_BATTLE_BG_ID						=			27;

	//攻打战斗音乐ID
	const USER_ATTACK_MUSIC_ID							=			17;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */