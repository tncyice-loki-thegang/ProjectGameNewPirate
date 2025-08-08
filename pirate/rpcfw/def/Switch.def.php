<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Switch.def.php 40475 2013-03-11 03:55:39Z wuqilin $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Switch.def.php $
 * @author $Author: wuqilin $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-11 11:55:39 +0800 (一, 2013-03-11) $
 * @version $Revision: 40475 $
 * @brief
 *
 **/

class SwitchDef
{
	const HERO_UI = 1; //开启主UI伙伴面板，只开启伙伴标签，不开启主船标签。伙伴的转职按钮和技能选择按钮为灰不开启。
	const REINFORCE = 2; //开启主UI强化面板，只是开启伙伴装备标签，不开启宝石融合和主船标签。活动状态栏出现强化冷却CD。
	const PUB = 3; //NPC中的酒馆选项。未开启前点击提示：招募第一个伙伴后开启。此处这里只开启酒馆标签页下的邀请伙伴标签，不开启声望伙伴标签。
	const FORMATION = 4; //开启主UI阵型面板，阵型面板中所有功能开启，可以升级，切换阵型。
	const WORLD_MAP = 5; //开启世界地图按钮，可以进入世界地图。
	const BOAT = 6; //开启主船按钮，开启第一艘主船的两个舱室，船长室和一个水手室。可进行建筑升级。下属标签不开放。
	const PORT = 7; //开启港口按钮，可在港口区域察看其他玩家的主船，可以修改自己的留言，不开启港口攻打和征服，订单操作，不开启资源区。
	const FRIEND = 8; //开启主UI—社交—好友，点击别人模型时，有加好友的选项。
	const TRAIN = 9; //开启伙伴训练室，舱室中的训练室开启，开启突飞等全部功能。活动状态栏增加训练冷却。
	const MEDICAL = 10; //开启主船医疗室，舱室中的训练室开启。
	const FUNCTION_CHANGE = 11; //功能发生变化，战斗后要开始损血，强化概率发生变化。（可能跟前端没有多少关系） 这个是做什么用的？？？？
	const RESEARCH = 12; //开启主船的研究院，可以进行科技研究。
	const NPC_CHAT = 13; //开启NPC处的会谈功能。
	const PRACTISE = 14; //开启主UI人物挂机，开启人物挂机功能。
	const ATTACK_CONTINOUS = 15; //开启副本选择表界面下的连续攻击。
	const PET = 16; //开启宠物室功能，人物面板下的宠物标签开放。
	const TRANSFER = 17; //开启转职功能，转职按钮可以按，可以进行第一次转职，并学习第一个技能。
	const PUB_PRESTIGE = 18; //酒馆声望标签开启。
	const ARENA = 19; //开启竞技场功能和功能按钮。
	const TEAM = 20; //开启组队按妞
	const ACTIVE = 21; //开启活动按钮
	const GUILD = 22; //开启主UI公会按钮，公会开启，可以创建或加入公会。
	const PORT_RESOURCE_AND_VASSAL = 23; //开启港口资源，可以进入资源了，开启下属系统。可以进行征服操作。
	const EXPLORE = 24; //开启NPC处的宝石探索功能。
	const GEM_FUSE = 25; //开启强化面板下的宝石标签，可以进行宝石融合和镶嵌
	const EQUIPMENT = 26; //开启装备制作系统按钮。
	const TREASURE = 27; //开启寻宝系统按钮。
	const DAYTASK = 28; //开启每日任务按钮。
	const PORT_ATTACK = 29; //开启港口攻打按钮。
	const SECOND_BOAT = 30; //可以升级第二艘船。厨房的订单不开放。
	const ORDER_LIST = 31; //开启订单功能，可以在港口中选择其他玩家进行订单操作。
	const GOLD_BARN = 32; //开启舱室藏金室。
	const THIRD_BOAT = 33; //可以升级换第三艘船按钮。
	const FORTH_BOAT = 34; //可以升级换第四艘船按钮。
	const ARM_REFRESH = 35; //可以在NPC处进行装备洗炼。开启洗炼界面。
	const KITCHEN = 36; //厨房开启。
	const SAIL = 37; //船长室开启出航按钮和出航界面，出航功能开启。
	const FOR_LV_UP = 38; //阵型升级功能开启。
	const ACTIVE_DEGREE = 39; //活跃度功能开启。
	const ASTROLABE = 40; //星盘功能开启
	const ALLBLUE = 41; //allblue功能开启。
	const SOUL = 42; //开启影魂
	const FISH = 43; //养鱼系统
	const IMPEL_DOWN = 44;	// 推进城系统
	const JEWELRY_EQUIP = 45; //	宝物系统装备
	const JEWELRY_REINFORCE = 46; //	宝物系统强化
	const ABYSS_COPY = 47; //深渊副本
	const CRYSTAL = 48; //熔炼地狱
	const GEM_MATRIX = 49; //宝石迷阵
	const BLOOD = 50; //血战到底
	const FORMATION_EVOLUTION = 51; //阵型进阶
	const BOAT_BATTLE = 52; //进击の海军
	const CRUISE = 53; //神秘海域
	const HAKI = 54; //霸气系统
	const COPET = 55; //神秘副宠
	const SEA_SOUL = 56; //海魂系统
	const FORMATION_SUBSTITUTE = 58; //阵型替补
	const HAKI_MANLY = 60; //主角霸气
	const ELEMENT = 61; //元素系统
	// const  = 62; //究极挑战
	const ELVES_SYSTEM = 63; //精灵系统
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */