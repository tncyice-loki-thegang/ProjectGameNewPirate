<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MailTemplate.cfg.php 16403 2012-03-14 02:37:05Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-25/conf/MailTemplate.cfg.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:37:05 +0800 (Wed, 14 Mar 2012) $
 * @version $Revision: 16403 $
 * @brief
 *
 **/

class MailTemplateID
{
	const CONQUER_SUCCESS							=			1;
	const CONQUER_FAILED							=			2;
	const PILLAGE_SUCCESS							=			3;
	const PILLAGE_FAILED							=			4;
	const MOVE_PORT									=			5;
	const BEING_CONQUER_DEFEND_FAILD				=			6;
	const BEING_CONQUER_DEFEND_SUCCESS				=			7;
	const BEING_PILLAGE_DEFEND_FAILD				=			8;
	const BEING_PILLAGE_DEFEND_SUCCESS				=			9;
	const MASTER_MOVE_PORT							=			10;
	const SUBORDINATE_MOVE_PORT						=			11;
	const REVOLT_MASTER_SUCCESS						=			12;
	const REVOLT_MASTER_FAILED						=			13;
	const SUBORDINATE_REVOLT_DEFEND_FAILED			=			14;
	const SUBORDINATE_REVOLT_DEFEND_SUCCESS			=			15;
	const SUBORDINATE_GIVENUP						=			16;

	const ATTACK_PORT_RESOURCE_DEFAULT_FAILED		=			101;
	const ATTACK_PORT_RESOURCE_SUCCESS				=			102;
	const ATTACK_PORT_RESOURCE_FAILED				=			103;
	const PORT_RESOURCE_DUE							=			104;
	const DEFEND_PORT_RESOURCE_FAILED				=			105;
	const DEFEND_PORT_RESOURCE_SUCCESS				=			106;

	const APPLY_GUILD_SUCCESS						=			201;
	const APPLY_GUILD_FAILED						=			202;
	const KICKOUT_GUILD								=			203;
	const GUILD_BANQUET_SUCCESS						=			204;
	const NOT_GUILD_BANQUET							=			205;
	const WORLD_RESOURCE_AWARD						=			206;
	const WORLD_RESOURCE_SUCCESS					=			207;
	const WORLD_RESOURCE_FAILD						=			208;

	const DEFEND_USER_FAILED						=			301;
	const DEFEND_USER_SUCCESS						=			302;
	const ATTACK_USER_SUCCESS						=			303;
	const ATTACK_USER_FAILED						=			304;

	const ADD_FRIEND								=			401;

	const ARENA_LUCKY_AWARD							=			501;
	const ARENA_AWARD								=			502;

	const ACHIEVEMENT								=			601;

	const TREASURE_REWARD							=			701;
	const TREASURE_ATTACK_FAILED					=			702;
	const TREASURE_ATTACK_SUCCESS					=			703;
	const TREASURE_DEFEND_FAILED					=			704;
	const TREASURE_DEFEND_SUCCESS					=			705;

	const BOAT_BEING_ORDER_SUCCESS					=			801;

	const BOSS_KILL									=			901;
	const BOSS_ATTACK_HP_FIRST						=			902;
	const BOSS_ATTACK_HP_SECOND						=			903;
	const BOSS_ATTACK_HP_THIRD						=			904;
	const BOSS_ATTACK_HP_OTHER						=			905;

	const TRAIN_BEING_BRUSH_TOILET					=			1001;
	const TRAIN_BEING_PACIFY						=			1002;
	const TRAIN_BEING_ITCH							=			1003;
	const TRAIN_BEING_PLAY_GAME						=			1004;
	const TRAIN_BEING_BEAT							=			1005;
	const TRAIN_BEING_PRAISE						=			1006;
	const TRAIN_BEING_RIDE							=			1007;
	const TRAIN_BEING_PLAY_BALL						=			1008;
	const TRAIN_BEING_SHOWTIME						=			1009;
	const TRAIN_BRUSH_TOILET						=			1010;
	const TRAIN_PACIFY								=			1011;
	const TRAIN_ITCH								=			1012;
	const TRAIN_PLAY_GAME							=			1013;
	const TRAIN_BEAT								=			1014;
	const TRAIN_PRAISE								=			1015;
	const TRAIN_RIDE								=			1016;
	const TRAIN_PLAY_BALL							=			1017;
	const TRAIN_SHOWTIME							=			1018;
}
/* vim: set ts=>4 sw=>4 sts=>4 tw=>100 noet: */