<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MailTemplate.def.php 37110 2013-01-25 09:35:35Z ZhichaoJiang $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/MailTemplate.def.php $
 * @author $Author: ZhichaoJiang $(jhd@babeltime.com)
 * @date $Date: 2013-01-25 17:35:35 +0800 (五, 2013-01-25) $
 * @version $Revision: 37110 $
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

	const EXPLOIT_RESOURCE_DEFEND_FAILED			=			107;
	const EXPLOIT_RESOURCE_DEFEND_SUCCESS			=			108;
	const EXPLOIT_RESOURCE_ATTACK_FAILED			=			109;
	const EXPLOIT_RESOURCE_ATTACK_SUCCESS			=			110;
	const EXPLOIT_RESOURCE_NOBATTLE_DEFEND_SUCCESS	=			111;
	const EXPLOIT_RESOURCE_NOBATTLE_ATTACK_SUCCESS	=			112;

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
	const BOSS_BOT									=			906;
	const BOSS_BOT_SUB_TIME							=			907;

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

	const GIFT_ITEM									=			1101;

	//CHANLLEDGE
	const CHANLLEDGE_TOP_32							=			1201;
	const CHANLLEDGE_TOP_16							=			1202;
	const CHANLLEDGE_TOP_8							=			1203;
	const CHANLLEDGE_TOP_4							=			1204;
	const CHANLLEDGE_TOP_2							=			1205;
	const CHANLLEDGE_TOP_1							=			1206;
	const CHANLLEDGE_CHEER_TOP_8					=			1207;
	const CHANLLEDGE_CHEER_TOP_4					=			1208;
	const CHANLLEDGE_CHEER_TOP_2					=			1209;
	const CHANLLEDGE_CHEER_TOP_1					=			1210;
	const CHANLLEDGE_LUCKYPRIZE						=			1211;
	const CHANLLEDGE_SUPERLUCKYPRIZE				=			1212;
	const CHANLLEDGE_PRIZEPOOL						=			1213;
	
	const VIP_UP									=			1301;
	
	//MERGESERVER
	const MERGESERVER_REWARD						=			1401;
	
	//ALLBLUE FISH
	const ALLBLUE_STEAL_FISH						=			1501;
	const ALLBLUE_STOLEN_FISH						=			1502;
	const ALLBLUE_WISH_FISH							=			1503;
	const ALLBLUE_WISHED_FISH						=			1504;
	const ALLBLUE_STEAL_SUBORDINATE_FISH			=			1505;
	const ALLBLUE_STOLEN_SUBORDINATE_FISH			=			1506;
	
	//GROUPWAR
	const GROUPWAR_REWARD							=			1601;
	
	//WORLDWAR
	const GROUPWAR_CHEER							=			1701;
	const GROUPWAR_TOP_32							=			1702;
	const GROUPWAR_TOP_16							=			1703;
	const GROUPWAR_TOP_8							=			1704;
	const GROUPWAR_TOP_4							=			1705;
	const GROUPWAR_TOP_2							=			1706;
	const GROUPWAR_TOP_1							=			1707;
	const WORLDWAR_CHEER							=			1708;
	const WORLDWAR_TOP_32							=			1709;
	const WORLDWAR_TOP_16							=			1710;
	const WORLDWAR_TOP_8							=			1711;
	const WORLDWAR_TOP_4							=			1712;
	const WORLDWAR_TOP_2							=			1713;
	const WORLDWAR_TOP_1							=			1714;
	
	//NEW WORLD RESOURCE
	const NEWWORLD_RESOURCE_DEF_FAIL				=			1801;
	const NEWWORLD_RESOURCE_ACKNPC_SUCCESS			=			1802;
	const NEWWORLD_RESOURCE_ACKNPC_FAIL				=			1803;
	const NEWWORLD_RESOURCE_ROB_SUCCESS				=			1804;
	const NEWWORLD_RESOURCE_EXPIRE	 				=			1805;
}
/* vim: set ts=>4 sw=>4 sts=>4 tw=>100 noet: */