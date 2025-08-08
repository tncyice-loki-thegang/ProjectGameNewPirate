<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Astrolabe.def.php 33682 2012-12-25 05:38:38Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Astrolabe.def.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-12-25 13:38:38 +0800 (二, 2012-12-25) $
 * @version $Revision: 33682 $
 * @brief 
 *  
 **/
class AstrolabeDef
{
	const	ASTROLABE_INVALIDATE_ID								= -1;
	const	ASTROLABE_SPECIAL_CONS_ID							= -1;//星座表里依赖其他星座时，如果星座id为-1 则代表依赖主星盘
	
	const 	ASTROLABE_SQL_TABLE_AST_INFO						=	't_astrolabe_info';	//星盘信息表
	const 	ASTROLABE_SQL_TABLE_CONS_INFO						=	't_constellation_info';	//星座信息表
	const 	ASTROLABE_SQL_TABLE_AST_STONE						=	't_astrolabe_stone';	//星灵石信息表
	
	const 	ASTROLABE_SQL_FIELD_UID								=	'uid';//玩家的uid
	
	
	//数据库的星盘表
	const 	ASTROLABE_SQL_FIELD_AST_ID							=	'ast_id';//星盘id
	const 	ASTROLABE_SQL_FIELD_AST_TYPE						=	'ast_type';//星盘的类型 0 主星盘 1 天赋星盘
	const 	ASTROLABE_SQL_FIELD_AST_LEVEL						=	'ast_lev';//星盘的等级
	const 	ASTROLABE_SQL_FIELD_AST_STATUS						=	'ast_stat';//星盘的状态
	const 	ASTROLABE_SQL_FIELD_AST_SKILL_STATUS				=	'skill_stat';//对应普通技能的状态
	const 	ASTROLABE_SQL_FIELD_AST_EXP_TIME					=	'exp_time';//每天可以根据主星盘的等级给玩家一定经验,记录领取的时间
	const 	ASTROLABE_SQL_FIELD_AST_ALL_EXP						=	'all_levlup_exp';//升级主星盘所用过的所有经验
	
	//数据库的星座表
	const 	ASTROLABE_SQL_FIELD_CONS_ID							=	'cons_id';//星座id
	const 	ASTROLABE_SQL_FIELD_CONS_LEV						=	'cons_lev';//该星座的等级
	const 	ASTROLABE_SQL_FIELD_CONS_STAT						=	'cons_stat';//该星座的状态 0未激活 1可以激活 2已激活
	const 	ASTROLABE_SQL_FIELD_CONS_TYPE						=	'cons_type';//该星座的类型  1主星座  0天赋星座
	const 	ASTROLABE_SQL_FIELD_CONS_ALLEXP						=	'all_levlup_exp';//升级该天赋星座所用过的所有经验
	
	//数据库的灵石表
	const 	ASTROLABE_SQL_FIELD_STONE_NUM						=	'stone_num';//星灵石
	const 	ASTROLABE_SQL_FIELD_BELLY_COUNT						=	'belly_count';//今日贝里购买次数
	const 	ASTROLABE_SQL_FIELD_VIP_COUNT						=	'vip_count';//今日VIP购买次数
	const 	ASTROLABE_SQL_FIELD_BELLY_TIME						=	'belly_time';//上次贝里购买的时间
	const 	ASTROLABE_SQL_FIELD_VIP_TIME						=	'vip_time';//上次vip购买的时间
	
	
	//给前端的返回信息
	const 	ASTROLABE_RET_TALAST_ID								=	'talent_ast_id';//返回给前端的值，当前装备的天赋星盘id
	const 	ASTROLABE_RET_BASAST_LEV							=	'basic_ast_lev';//返回给前端的值，基本星盘等级
	const   ASTROLABE_RET_AST_STATUS							=   'ast_status';//返回给前端的值，星盘状态
	const   ASTROLABE_RET_CONS_STATUS							=   'cons_status';//返回给前段的值，天赋星盘里星座状态
	const   ASTROLABE_RET_CONS_LEV								=   'cons_lev';//返回给前段的值，天赋星盘里星座等级
	
	//给后端提供属性和技能接口会用到
	const 	ASTROLABE_CONSTE_ATTR_ID							=	'cons_attr_id' ;//星座对应的属性id
	const 	ASTROLABE_CONSTE_ATTR_VAL							=	'cons_attr_val' ;//星座对应的属性值
	
	
	//星座的状态
	const 	ASTROLABE_CONSTE_STATUS_UNKHOWN                     = -1 ;//数据库里没有检测到它的激活状态
	const 	ASTROLABE_CONSTE_STATUS_NOT_ACTIVE                  = 0 ; //不能激活
	const 	ASTROLABE_CONSTE_STATUS_CAN_ACTIVE                  = 1 ; //能激活，当尚未激活
	const 	ASTROLABE_CONSTE_STATUS_OK_ACTIVE                  	= 2 ; //已经激活
	
	//星座的类型
	const 	ASTROLABE_CONSTE_TYPE_MAIN                     		= 1 ;//主星座
	const 	ASTROLABE_CONSTE_AST_TYPE_TALENT                 	= 0 ; //天赋星座
	
	//星盘的状态
	const 	ASTROLABE_AST_STATUS_UNKHOWN                     	= -1 ;//数据库里没有检测到它的激活状态
	const 	ASTROLABE_AST_STATUS_NOT_ACTIVE                  	= 0 ; //不能激活
	const 	ASTROLABE_AST_STATUS_CAN_ACTIVE                 	= 1 ; //能激活，当尚未激活
	const 	ASTROLABE_AST_STATUS_OK_ACTIVE                   	= 2 ; //已经激活
	const 	ASTROLABE_AST_STATUS_EQUIPED                   	    = 3 ; //已经装备
	
	//星盘的类型
	const 	ASTROLABE_AST_TYPE_MAIN                     		= 0 ;//主星盘
	const 	ASTROLABE_AST_TYPE_TALENT                 			= 1 ; //天赋星盘
	
    //星盘技能状态
	const 	ASTROLABE_AST_SKILL_STATUS_NOT_ACTIVE               = 0 ;// 不能激活
	const 	ASTROLABE_AST_SKILL_STATUS_OK_ACTIVE                = 1 ; //已经激活
	
	//主星盘的id
	const 	ASTROLABE_AST_MAIN_ID                				= 1 ; 
	
	//主星盘算阶数是要用的数值
	const 	ASTROLABE_AST_MAIN_STAGE               				= 10 ; 
	
	
	//灵石购买类型
	const 	ASTROLABE_STONE_BUY_TYPE_BELLY                		= 0 ;//贝里购买
	const 	ASTROLABE_STONE_BUY_TYPE_GOLD                		= 1 ;//金币购买
	const 	ASTROLABE_STONE_BUY_TYPE_VIP                		= 2 ;//vip购买
	const 	ASTROLABE_STONE_BUY_TYPE_ADV                		= 3 ;//高级购买
	const 	ASTROLABE_STONE_BUY_TYPE_BAIJIN                		= 4 ;//白金购买
	
	//读星盘天赋时，需要将整数除以10000转换成浮点数
	const 	ASTROLABE_TALENT_TRANSFER_NUM                		= 10000 ;
	
	static $mapAstrolabeattr = array(
			1	=> CreatureInfoKey::hp  , //调整生命基础值	生命基础值
			2	=> CreatureInfoKey::hpRatio  , //调整生命值百分比	生命值百分比
			3	=> CreatureInfoKey::strength  , //调整力量值点数	力量
			4	=> CreatureInfoKey::agile  , //调整敏捷值点数	敏捷
			5	=> CreatureInfoKey::intelligence  , //调整智慧值点数	智慧
			6	=> CreatureInfoKey::phyAttack  , //调整物理攻击基础值	物理攻击基础值
			7	=> CreatureInfoKey::killAttack  , //调整必杀攻击基础值	必杀攻击基础值
			8	=> CreatureInfoKey::mgcAttack  , //调整魔法攻击基础值	魔法攻击基础值
			9	=> CreatureInfoKey::phyDefend  , //调整物理防御基础值	物理防御基础值
			10	=> CreatureInfoKey::killDefend  , //调整必杀防御基础值	必杀防御基础值
			11	=> CreatureInfoKey::mgcDefend  , //调整魔法防御基础值	魔法防御基础值
			12	=> CreatureInfoKey::rage  , //调整怒气值点数	怒气值点数
			13	=> CreatureInfoKey::hitRatingRatio  , //调整命中率	命中率
			14	=> CreatureInfoKey::ftlAtkRatio  , //调整致命一击率	致命一击率
			15	=> CreatureInfoKey::pryRatio  , //调整格挡率	格挡率
			16	=> CreatureInfoKey::dgeRatio  , //调整闪避率	闪避率
			17	=> CreatureInfoKey::windAttack  , //调整风属性攻击基础值	风属性攻击基础值
			18	=> CreatureInfoKey::thdAttack  , //调整雷属性攻击基础值	雷属性攻击基础值
			19	=> CreatureInfoKey::wtrAttack  , //调整水属性攻击基础值	水属性攻击基础值
			20	=> CreatureInfoKey::fireAttack  , //调整火属性攻击基础值	火属性攻击基础值
			21	=> CreatureInfoKey::windResistance  , //调整风属性抗性	风属性抗性
			22	=> CreatureInfoKey::thdResistance  , //调整雷属性抗性	雷属性抗性
			23	=> CreatureInfoKey::wtrResistance  , //调整水属性抗性	水属性抗性
			24	=> CreatureInfoKey::fireResistance  , //调整火属性抗性	火属性抗性
			25	=> CreatureInfoKey::phyAtkRatio  , //调整物理攻击百分比	物理攻击百分比
			26	=> CreatureInfoKey::phyDfsRatio  , //调整物理防御百分比	物理防御百分比
			27	=> CreatureInfoKey::killAtkRatio  , //调整必杀攻击百分比	必杀攻击百分比
			28	=> CreatureInfoKey::killDfsRatio  , //调整必杀防御百分比	必杀防御百分比
			29	=> CreatureInfoKey::mgcAtkRatio  , //调整魔法攻击百分比	魔法攻击百分比
			30	=> CreatureInfoKey::mgcDfsRatio  , //调整魔法防御百分比	魔法防御百分比
			31	=> CreatureInfoKey::windAtkRatio  , //调整风属性攻击百分比	风属性攻击百分比
			32	=> CreatureInfoKey::thdAtkRatio  , //调整雷属性攻击百分比	雷属性攻击百分比
			33	=> CreatureInfoKey::wtrAtkRatio  , //调整水属性攻击百分比	水属性攻击百分比
			34	=> CreatureInfoKey::fireAtkRatio  , //调整火属性攻击百分比	火属性攻击百分比
			35	=> CreatureInfoKey::phyFDmgRatio  , //调整物理伤害倍率	物理伤害倍率
			36	=> CreatureInfoKey::phyFEptRatio  , //调整物理免伤倍率	物理免伤倍率
			37	=> CreatureInfoKey::killFDmgRatio  , //调整必杀伤害倍率	必杀伤害倍率
			38	=> CreatureInfoKey::killFEptRatio  , //调整必杀免伤倍率	必杀免伤倍率
			39	=> CreatureInfoKey::mgcFDmgRatio  , //调整魔法伤害倍率	魔法伤害倍率
			40	=> CreatureInfoKey::mgcFEptRatio  , //调整魔法免伤倍率	魔法免伤倍率
			41	=> CreatureInfoKey::absoluteDamage  , //调整最终伤害	最终伤害
			42	=> CreatureInfoKey::absoluteDefend  , //调整减免最终伤害	减免最终伤害
			43	=> CreatureInfoKey::stgRatio  , //调整力量值百分比	力量值百分比
			44	=> CreatureInfoKey::aglRatio  , //调整敏捷值百分比	敏捷值百分比
			45	=> CreatureInfoKey::itgRatio  , //调整智慧值百分比	智慧值百分比
			46	=> CreatureInfoKey::hpFinal  , //调整最终生命	最终生命
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */