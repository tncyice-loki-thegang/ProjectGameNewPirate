<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Reward.def.php 38722 2013-02-20 05:52:17Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/Reward.def.php $
 * @author $Author: yangwenhai $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-20 13:52:17 +0800 (三, 2013-02-20) $
 * @version $Revision: 38722 $
 * @brief 
 *  
 **/

class RewardType
{
	//1-贝里、2-阅历、3-金币、4-行动力、5-物品、6-等级*贝里、7-等级*阅历、 8-声望、20-多个物品、21-能量石、22-元素石、23-刻印石
	const BELLY = 1;
	
	const EXPERIENCE = 2;
	
	const GOLD = 3;
	
	const EXECUTION = 4;
	
	const ITEM = 5;
	
	const BELLY_MUL_LEVEL = 6;
	
	const EXPERIENCE_MUL_LEVEL = 7;
	
	const PRESTIGE = 8;
	
	const ITEM_MULTI = 20;

	const JEW_ENERGY = 21;
	
	const JEW_ELEMENT = 22;
	
	const GEM_CARVED = 23;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */