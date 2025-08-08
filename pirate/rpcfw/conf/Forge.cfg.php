<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Forge.cfg.php 24325 2012-07-20 07:27:26Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Forge.cfg.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-07-20 15:27:26 +0800 (五, 2012-07-20) $
 * @version $Revision: 24325 $
 * @brief
 *
 **/

class ForgeConfig
{
	const FORGE_TIMER_UID								=		3;

	/*****强化*****/
	//初始强化概率上限
	const INIT_REINFORCE_PROBABILITY_UPPER				=		95;
	//初始强化概率下限
	const INIT_REINFORCE_PROBABILITY_LOWER				=		83;

	//强化概率rand上限
	const RAND_REINFORCE_PROBABILITY_UPPER				=		10;
	//强化概率rand下限
	const RAND_REINFORCE_PROBABILITY_LOWER				=		5;

	//强化概率负向
	const MINIUS_REINFORCE_PROBABILITY					=		95;
	//强化概率正向
	const PLUS_REINFORCE_PROBABILITY					=		83;

	//强化概率刷新时间(s)
	const REFRESH_TIME_REINFORCE_PROBABILITY			=		1800;

	//强化冷却时间一金币可以消除的秒数
	const ARM_REINFORCE_RESET_SECOND					=		60;

	//当前允许的最大强化等级
	const ARM_MAX_REINFORCE_LEVEL						=		200;

	//当前允许的主船装备的最大强化等级
	const BOAT_ARM_MAX_REINFORCE_LEVEL					=		10;

	//物品强化封冻时间(s)
	const ARM_REINFORCE_FREEZE_TIME						=		600;

	//强化最大支付金币(100-85)
	const ARM_REINFORCE_GOLD_MAX						=		15;

	//默认的强化增加时间
	const ARM_REINFORCE_INC_TIME						=		60;
	/**************/

	/*****弱化*****/
	//物品可以允许的降级数
	public static $ARM_WEAKENING_LEVEL_ALLOWED			=		array(1,10);

	//物品降级belly回收百分比
	const ARM_WEAKING_RECOVERY_PERCENT					=		1.0;
	/**************/

	/*****洗练*****/
	//随机洗练所需金币
	const ARM_RAND_REFRESH_POTENTIALITY					=		10;
	/**************/

	/*****装备转移*****/
	const ARM_TRANSFER_GOLD_PRETIME						=		10;
	//装备转移最大所需金币
	const ARM_TRANSFER_MAX_GOLD							=		100;

	/*****刷新时间****/
	const FORGE_RESET_INTERVAL							=		86400;
	const FORGE_RESET_DATE								=		'04:00:00';
	//得到装备潜能转移刷新价格
	const FORGE_POTENTIALITY_TRANSFER_RESET_INTERVAL	=		604800;

	/**
	 *
	 * 合理的固定潜能洗练方式(目前装备洗练不开放)
	 *
	 * @var array(int)
	 */
	public static $VALID_FIXED_REFRESH_TYPES			=
		array(
			ForgeDef::FIXED_REFRESH_TYPE_NORMAIL,
			ForgeDef::FIXED_REFRESH_TYPE_BRONZE,
			ForgeDef::FIXED_REFRESH_TYPE_SILVER,
			ForgeDef::FIXED_REFRESH_TYPE_GOLD,
		);

	/**
	 *
	 * 合理的潜能转移方式
	 *
	 * @var array(int)
	 */
	public static $VALID_POTENTIALITY_TRANSFER_TYPES	=
		array(
			ForgeDef::POTENTIALITY_TRANSFER_TYPE_GOLD,
			ForgeDef::POTENTIALITY_TRANSFER_TYPE_ITEM,
			ForgeDef::POTENTIALITY_TRANSFER_TYPE_FREE,
		);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */