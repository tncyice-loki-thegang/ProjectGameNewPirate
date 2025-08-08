<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

class CreatureInfoKey
{
		
	/****************************************************************
	 * 英雄/怪物属性数值key值
	 ****************************************************************/
	
	/**
	 * 当前生命
	 */
	const curHp = 200;
	
	/**
	 * 技能，包括普通技能和恶魔果实技能
	 */
	const arrSkill = 201;
	
	/**
	 * 转生次数
	 */
	const reBirthNum = 202;
	
//	/**
//	 * 当前等级
//	 */
//	const curLevel = 203;
	
	/**
	 * hp百分比
	 */
	const hpRatio = 204;
	
	/**
	 * strenght百分比
	 */
	const stgRatio = 205;
	
	/**
	 * agile百分比
	 */
	const aglRatio = 206;
	
	/**
	 * 智慧百分比
	 */
	const itgRatio = 207;
	
	/**
	 * 最终生命
	 * Enter description here ...
	 * @var unknown_type
	 */
	const hpFinal = 208;
	
	/**
	 * 最终物理攻击
	 * @var unknown_type
	 */
	const phyAtkFinal = 209;
	
	/**
	 * 最终必杀攻击
	 * @var unknown_type
	 */
	const killAtkFinal = 210;
	
	/**
	 * 最终魔法攻击
	 * @var unknown_type
	 */
	const mgcAtkFinal = 211;
	
	/**
	 * 最终物理防御
	 * @var unknown_type
	 */
	const phyDfsFinal = 212;

	/**
	 * 最终必杀防御
	 * @var unknown_type
	 */
	const killDfsFinal = 213;
	
	/**
	 * 最终魔法防御
	 * @var unknown_type
	 */
	const mgcDfsFinal = 214;
	
	const normalAttRatio = 215;//调整普通攻击伤害比
	const normalDefRatio= 216;//调整普通攻击免伤比
	const ragerAttRatio=217;//调整怒气攻击伤害比
	const ragerDefRatio=218;//调整怒气攻击免伤比
	const treatRatio=219;	//调整治疗比率
	const treatedRatio=220;	//调整被治疗比率
	
	
	
	
/****************************************************
 *  以下值为脚本生成，不要手动修改
 *****************************************************/	
	/**
	 * 英雄模版ID
	 */
	const htid = 0;

	/**
	 * 英雄模板名称
	 */
	const tname = 1;

	/**
	 * 英雄名称
	 */
	const name = 2;

	/**
	 * 英雄基础等级
	 */
	const level = 3;

	/**
	 * 英雄生日
	 */
	const birthday = 4;

	/**
	 * 英雄描述
	 */
	const desc = 5;

	/**
	 * 英雄动作模型ID
	 */
	const atctionId = 6;

	/**
	 * 英雄头像图片ID
	 */
	const headId = 7;

	/**
	 * 英雄半身像ID
	 */
	const bustId = 8;

	/**
	 * 英雄全身像ID
	 */
	const photoId = 9;

	/**
	 * 英雄怒气头像ID
	 */
	const rageHeadId = 10;

	/**
	 * ID
	 */
	const expId = 11;

	/**
	 * 英雄职业
	 */
	const vocation = 12;

	/**
	 * 怒气获得基础值
	 */
	const rageGetBase = 13;

	/**
	 * 怒气获得修正值
	 */
	const rageGetAmend = 14;

	/**
	 * 怒气获得倍率
	 */
	const rageGetRatio = 15;

	/**
	 * 竦布寄躀D
	 */
	const parryID = 16;

	/**
	 * 然蠹寄躀D
	 */
	const charmID = 17;

	/**
	 * 壹寄躀D
	 */
	const choasID = 18;

	/**
	 * 状态效果ID
	 */
	const immuneBufferID = 19;

	/**
	 * 通攻击
	 */
	const normalAtk = 20;

	/**
	 * 怒气攻击技能
	 */
	const rageAtkSkill = 21;

	/**
	 * 魔果实技能(转生次数|技能ID)
	 */
	const devilFruitSkill = 22;

	/**
	 * 募价格
	 */
	const price = 23;

	/**
	 * 英雄基础生命
	 */
	const hp = 24;

	/**
	 * 英雄基础怒气
	 */
	const rage = 25;

	/**
	 * 英雄基础力量
	 */
	const strength = 26;

	/**
	 * 英雄基础敏捷
	 */
	const agile = 27;

	/**
	 * 英雄基础智慧
	 */
	const intelligence = 28;

	/**
	 * 英雄基础物理攻击
	 */
	const phyAttack = 29;

	/**
	 * 英雄基础必杀攻击
	 */
	const killAttack = 30;

	/**
	 * 英雄基础魔法攻击
	 */
	const mgcAttack = 31;

	/**
	 * 英雄基础物理防御
	 */
	const phyDefend = 32;

	/**
	 * 英雄基础必杀防御
	 */
	const killDefend = 33;

	/**
	 * 英雄基础魔法防御
	 */
	const mgcDefend = 34;

	/**
	 * 英雄基础致命一击率
	 */
	const ftlAtkRatio = 35;

	/**
	 * 英雄基础命中
	 */
	const hitRatingRatio = 36;

	/**
	 * 英雄基础闪避
	 */
	const dgeRatio = 37;

	/**
	 * 英雄基础格挡率
	 */
	const pryRatio = 38;

	/**
	 * 英雄物理攻击百分比
	 */
	const phyAtkRatio = 39;

	/**
	 * 英雄必杀攻击百分比
	 */
	const killAtkRatio = 40;

	/**
	 * 英雄魔法攻击百分比
	 */
	const mgcAtkRatio = 41;

	/**
	 * 英雄物理防御百分比
	 */
	const phyDfsRatio = 42;

	/**
	 * 英雄必杀防御百分比
	 */
	const killDfsRatio = 43;

	/**
	 * 英雄魔法防御百分比
	 */
	const mgcDfsRatio = 44;

	/**
	 * 英雄固定物理伤害倍率
	 */
	const phyFDmgRatio = 45;

	/**
	 * 英雄固定必杀伤害倍率
	 */
	const killFDmgRatio = 46;

	/**
	 * 英雄固定魔法伤害倍率
	 */
	const mgcFDmgRatio = 47;

	/**
	 * 英雄固定物理免伤倍率
	 */
	const phyFEptRatio = 48;

	/**
	 * 英雄固定必杀免伤倍率
	 */
	const killFEptRatio = 49;

	/**
	 * 英雄固定魔法免伤倍率
	 */
	const mgcFEptRatio = 50;

	/**
	 * 英雄基础风攻击
	 */
	const windAttack = 51;

	/**
	 * 英雄基础雷攻击
	 */
	const thdAttack = 52;

	/**
	 * 英雄基础水攻击
	 */
	const wtrAttack = 53;

	/**
	 * 英雄基础火攻击
	 */
	const fireAttack = 54;

	/**
	 * 英雄风攻击百分比
	 */
	const windAtkRatio = 55;

	/**
	 * 英雄雷攻击百分比
	 */
	const thdAtkRatio = 56;

	/**
	 * 英雄水攻击百分比
	 */
	const wtrAtkRatio = 57;

	/**
	 * 英雄火攻击百分比
	 */
	const fireAtkRatio = 58;

	/**
	 * 英雄风抗性
	 */
	const windResistance = 59;

	/**
	 * 英雄雷抗性
	 */
	const thdResistance = 60;

	/**
	 * 英雄水抗性
	 */
	const wtrResistance = 61;

	/**
	 * 英雄火抗性
	 */
	const fireResistance = 62;

	/**
	 * 英雄基础最终伤害
	 */
	const absoluteDamage = 63;

	/**
	 * 英雄基础最终免伤
	 */
	const absoluteDefend = 64;

	/**
	 * 系数
	 */
	const stgPhyAtkRatio = 65;

	/**
	 * 撕Ρ堵氏凳�
	 */
	const stgPhyDmgRatio = 66;

	/**
	 * 荼厣惫セ飨凳�
	 */
	const aglKillAtkRatio = 67;

	/**
	 * 荼厣鄙撕Ρ堵氏凳�
	 */
	const aglKillDmgRatio = 68;

	/**
	 * 腔勰Хüセ飨凳�
	 */
	const itgMgcAtkRatio = 69;

	/**
	 * 腔勰Хㄉ撕Ρ堵氏凳�
	 */
	const itgMgcDmgRatio = 70;

	/**
	 * 系数
	 */
	const stgPhyDfsRatio = 71;

	/**
	 * 吮堵氏凳�
	 */
	const stgPhyEptRatio = 72;

	/**
	 * 荼厣狈烙凳�
	 */
	const aglKillDfsRatio = 73;

	/**
	 * 荼厣泵馍吮堵氏凳�
	 */
	const aglKillEptDmgRatio = 74;

	/**
	 * 腔勰Хǚ烙凳�
	 */
	const itgMgcDfsRatio = 75;

	/**
	 * 腔勰Х馍吮堵氏凳�
	 */
	const itgMgcEptDmgRatio = 76;

	/**
	 * 每次转生增加力量
	 */
	const stgRebirth = 77;

	/**
	 * 每次转生增加敏捷
	 */
	const aglRebirth = 78;

	/**
	 * 每次转生增加智慧
	 */
	const itgRebirth = 79;

	/**
	 * 始转生等级
	 */
	const initLvlRebirth = 80;

	/**
	 * 转生等级间隔
	 */
	const lvlGapRebirth = 81;

	/**
	 * 英雄生命成长
	 */
	const hpIcs = 82;

	/**
	 * 英雄力量成长
	 */
	const stgIcs = 83;

	/**
	 * 英雄敏捷成长
	 */
	const aglIcs = 84;

	/**
	 * 英雄智慧成长
	 */
	const itgIcs = 85;

	/**
	 * 英雄致命一击率成长
	 */
	const ftlIcs = 86;

	/**
	 * 英雄命中成长
	 */
	const hitrIncs = 87;

	/**
	 * 英雄闪避成长
	 */
	const dgeIcs = 88;

	/**
	 * 英雄格挡成长
	 */
	const pryIcs = 89;

	/**
	 * 英雄物理攻击成长
	 */
	const phyAtkIcs = 90;

	/**
	 * 英雄必杀攻击成长
	 */
	const killAtkIcs = 91;

	/**
	 * 英雄魔法攻击成长
	 */
	const mgcAtkIcs = 92;

	/**
	 * 英雄物理防御成长
	 */
	const phyDfsIcs = 93;

	/**
	 * 英雄必杀防御成长
	 */
	const killDfsIcs = 94;

	/**
	 * 英雄魔法防御成长
	 */
	const mgcDfsIcs = 95;

	/**
	 * 英雄物理攻击百分比成长
	 */
	const phyAtkRatioIcs = 96;

	/**
	 * 英雄必杀攻击百分比成长
	 */
	const killAtkRatioIcs = 97;

	/**
	 * 英雄魔法攻击百分比成长
	 */
	const mgcAtkRatioIcs = 98;

	/**
	 * 英雄物理防御百分比成长
	 */
	const phyDfsRatioIcs = 99;

	/**
	 * 英雄必杀防御百分比成长
	 */
	const killDfsRatioIcs = 100;

	/**
	 * 英雄魔法防御百分比成长
	 */
	const mgcDfsRatioIcs = 101;

	/**
	 * 英雄物理伤害倍率成长
	 */
	const phyDmgRatioIcs = 102;

	/**
	 * 英雄必杀伤害倍率成长
	 */
	const killDmgRatioIcs = 103;

	/**
	 * 英雄魔法伤害倍率成长
	 */
	const mgcDmgRatioIcs = 104;

	/**
	 * 英雄物理免伤倍率成长
	 */
	const phyEptRatioIcs = 105;

	/**
	 * 英雄必杀免伤倍率成长
	 */
	const killEptRatioIcs = 106;

	/**
	 * 英雄魔法免伤倍率成长
	 */
	const mgcEptRatioIcs = 107;

	/**
	 * 英雄风攻击成长
	 */
	const windAtkIcs = 108;

	/**
	 * 英雄雷攻击成长
	 */
	const thdAtkIcs = 109;

	/**
	 * 英雄水攻击成长
	 */
	const wtrAtkIcs = 110;

	/**
	 * 英雄火攻击成长
	 */
	const fireAtkIcs = 111;

	/**
	 * 英雄风攻击百分比成长
	 */
	const windAtkRatioIcs = 112;

	/**
	 * 英雄雷攻击百分比成长
	 */
	const thdAtkRatioIcs = 113;

	/**
	 * 英雄水攻击百分比成长
	 */
	const wtrAtkRatioIcs = 114;

	/**
	 * 英雄火攻击百分比成长
	 */
	const fireAtkRatioIcs = 115;

	/**
	 * 英雄风抗性成长
	 */
	const windRstIcs = 116;

	/**
	 * 英雄雷抗性成长
	 */
	const thdRstIcs = 117;

	/**
	 * 英雄水抗性成长
	 */
	const wtrRstIcs = 118;

	/**
	 * 英雄火抗性成长
	 */
	const fireRstIcs = 119;

	/**
	 * 职业图标标识
	 */
	const particular = 120;

	/**
	 * 魔果实ID
	 */
	const devilApple = 121;

	/**
	 * 英雄星级
	 */
	const starLevel = 122;

	/**
	 * 英雄大头像
	 */
	const bigHeadImg = 123;

	/**
	 * BOSS头像ID
	 */
	const icon = 124;

	/**
	 * 英雄品质
	 */
	const quality = 125;

	/**
	 * 酶卸染楸鞩D
	 */
	const good_will_exp_id = 126;

	/**
	 * 酶卸燃侗饇可学习技能ID
	 */
	const good_will_skill = 127;

	/**
	 * 品类|额外增加好感度倍率|爱好物品示例物品
	 */
	const good_will_like = 128;

	/**
	 * 品类|额外减少好感度倍率|厌恶物品示例物品
	 */
	const good_will_mislike = 129;
	
	/**
	 * 名字颜色
	 */
	const nameColor = 130;

	/**
	 * 英雄原型ID
	 */
	const modelId = 131;
	
	const dodgeId = 132;
	
	const deathId = 133;
	
	const devilFruitPos = 134;
	
	const talentId = 135;
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */