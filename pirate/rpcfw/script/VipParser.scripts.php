<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: VipParser.scripts.php 40214 2013-03-07 04:26:34Z yangwenhai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/VipParser.scripts.php $
 * @author $Author: yangwenhai $(liuyang@babeltime.com)
 * @date $Date: 2013-03-07 12:26:34 +0800 (四, 2013-03-07) $
 * @version $Revision: 40214 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!vip.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'vip_lv' => $ZERO,								// VIP等级
'total_cost' => ++$ZERO,						// 累积充值金额
'builder_open_gold' => ++$ZERO,					// 建筑序列|开启需要金币
'train_slots' => ++$ZERO,						// 训练位数量|开启训练位金币
'train_time' => ++$ZERO,						// 训练时间
'train_mode' => ++$ZERO,						// 训练强度
'pet_slots' => ++$ZERO,							// 宠物携带栏位|开启携带位金币
'execution_gold' => ++$ZERO,					// 购买行动力|购买需要金币
'rapid_open_lv' => ++$ZERO,						// 伙伴金币突飞开启等级
'pet_rapid_open_lv' => ++$ZERO,					// 宠物金币突飞开启等级
'pet_skill_lock' => ++$ZERO,					// 宠物技能锁定数|锁定需要金币
'talks_refresh' => ++$ZERO,						// 会谈同时刷新开启等级
'guild_contribute' => ++$ZERO,					// 公会积分每日自动贡献
'guild_donate' => ++$ZERO,						// 金币捐献公会积分金币上限|对应获得声望
'sail_reward_open' => ++$ZERO,					// 出航奇遇事件奖励显示开启等级
'accelerate_times' => ++$ZERO,					// 人物挂机加速次数
'day_mode_open' => ++$ZERO,						// 人物挂机可选择24小时开通金币
'arena_times_gold' => ++$ZERO,					// 可购买竞技场次数与金币花费
'reinforce_100_open' => ++$ZERO,				// 强制强化概率100%开通金币
'reinforce_transfer' => ++$ZERO,				// 强化等级转移可使用金币
'artificer_times_gold' => ++$ZERO,				// 制作每日开启工匠次数和所需金币
'gold_smelt_open' => ++$ZERO,					// 高级制作开启和金币数量
'treasure_refresh_gold' => ++$ZERO,				// 挖宝金币刷新次数和所需金币
'explore_vip' => ++ $ZERO,						// 开启探索场景事件数组
'sail_max_time' => ++ $ZERO,					// 每日金币出航最大次数
'cook_max_time' => ++ $ZERO,					// 每日金币生产最大次数
'refresh_potentiality' => ++ $ZERO,				// 潜能洗炼
'talks_free_mode' => ++ $ZERO,			 		// 会谈免费模式开启|花费金币
'talks_refresh_times' => ++$ZERO,				// 会谈刷新次数
'arm_transfer_open' => ++$ZERO,					// 是否开启装备传承
'day_task_gold' => ++$ZERO,						// 每日任务直接完成所需金币
'arena_can_skip' => ++$ZERO,					// 竞技场可否跳过战斗
'boss_auto_atk' => ++$ZERO,						// 世界Boss是否开启自动攻击
'boss_atk_gold' => ++$ZERO,						// 世界Boss离线攻击花费金币
'st_cd_gold' => ++$ZERO,						// 开启科技CD花费金币
'auto_explore' => ++$ZERO,                      // 开启宝石一键探索
'goodwill_vip_num' => ++$ZERO,					// 每日免费赠送次数(好感度) 
'potentiality_transfer' => ++$ZERO,				// 每周免费潜能转移次数金币
'treasure_open_gold' => ++$ZERO, 				// 开启红色藏宝图需要金币
'free_create_soul' => ++$ZERO, 					// 免费金币造魂次数
'elitecopy_pass_gold' => ++$ZERO, 				// 金币通关精英本花费
'skip_fight' => ++$ZERO, 						// 副本跳过战斗动画
'is_open_fish_boot' => ++$ZERO, 				// 是否开启养鱼保护罩功能
'free_fishing_times' => ++$ZERO, 				// 免费金币捕捞育苗次数
'max_warehouse_num' => ++$ZERO, 				// 宠物仓库数量
'free_farmfish_times' => ++$ZERO, 				// vip养鱼额外次数
'free_open_fishqueie' => ++$ZERO, 				// 第1次开启第2养鱼序列是否免费
'quick_explore' => ++$ZERO,						// 极速探索
'limit_speaker' => ++$ZERO, 					// 是否开启全服喇叭
'gold_dig_day_max' => ++$ZERO, 					// 每天挖宝次数限制
'impel_down_gold_prize_times' => ++$ZERO, 		// 推进城金币领奖次数
'impel_down_free_prize_times' => ++$ZERO, 		// vip免费领奖次数
'abyss_max_buy' => ++$ZERO, 					// 每周购买深渊副本挑战次数
'abyss_card_times' => ++$ZERO, 					// 深渊副本翻牌次数
'Seal_freeFreshNum' => ++$ZERO, 				// 每周免费封印转移次数
'smelting_all' =>  ++$ZERO, 					// 装备制作一键制作开启
);

$rapidStartLv = 0;
$petRapidStartLv = 0;
$talksStartLv = 0;


$item = array();
$file = fopen($argv[1].'/vip.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$vipList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	// 这个是中间存储用的临时数组
	$array = array();
	foreach ( $name as $key => $v )
	{
		// 普通数组
		if ($key == 'train_time' || $key == 'train_mode' || $key == 'day_mode_open')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		}
		// 竖线数组
		else if ($key == 'builder_open_gold' || $key == 'train_slots' ||
		         $key == 'pet_slots' || $key == 'execution_gold' || $key == 'gold_smelt_open' ||
		         $key == 'pet_skill_lock' || $key == 'guild_donate' || $key == 'arena_times_gold' || 
		         $key=='explore_vip' || $key == 'refresh_potentiality')
		{
			$array[$key] = explode(',', $data[$v]);
		}
		// 什么转化都不做
		else if ($key == 'artificer_times_gold' || $key == 'treasure_refresh_gold' || 
				 $key == 'potentiality_transfer' || $key == 'Seal_freeFreshNum'||$key == 'smelting_all')
		{
			$array[$key] = $data[$v];
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}
	// 检查策划们的错误配置
	if (empty($array['builder_open_gold']))
	{
		continue;
	}

	// 最终解析数组, 不需要再次进行操作的，就直接赋值
	$ret = array();
	// VIP等级
	$ret['vip_lv'] = $array['vip_lv'];
	// 累积充值金额
	$ret['total_cost'] = $array['total_cost'];

	// 建筑序列|开启需要金币
	$tmp = array();
	for ($index = 0; $index < count($array['builder_open_gold']); ++$index)
	{
		$tmpTrain = explode('|', $array['builder_open_gold'][$index]);
		$tmp[intval($tmpTrain[0])]['num'] = intval($tmpTrain[0]);
		$tmp[intval($tmpTrain[0])]['gold'] = intval($tmpTrain[1]);
	}
	$ret['builder_open_gold'] = $tmp;

	// 训练位数量|开启训练位金币
	$tmp = array();
	for ($index = 0; $index < count($array['train_slots']); ++$index)
	{
		$tmpTrain = explode('|', $array['train_slots'][$index]);
		$tmp[intval($tmpTrain[0])]['num'] = intval($tmpTrain[0]);
		$tmp[intval($tmpTrain[0])]['gold'] = intval($tmpTrain[1]);
	}
	$ret['train_slots'] = $tmp;

	// 训练室训练时间开启所需VIP等级数组
	$ret['train_time'] = $array['train_time'];

	// 训练室各档次训练强度开启需要VIP等级数组
	$ret['train_mode'] = $array['train_mode'];

	// 宠物携带栏位|开启携带位金币
	$tmp = array();
	for ($index = 0; $index < count($array['pet_slots']); ++$index)
	{
		$tmpTrain = explode('|', $array['pet_slots'][$index]);
		$tmp[intval($tmpTrain[0])]['num'] = intval($tmpTrain[0]);
		$tmp[intval($tmpTrain[0])]['gold'] = intval($tmpTrain[1]);
	}
	$ret['pet_slots'] = $tmp;

	// 购买行动力|购买需要金币
	$tmpExecution = explode('|', $array['execution_gold'][0]);
	$ret['execution_gold']['num'] = intval($tmpExecution[0]);
	$ret['execution_gold']['gold'] = intval($tmpExecution[1]);

	// 伙伴金币突飞开启等级
	$ret['rapid_open_lv'] = $array['rapid_open_lv'];
	// 如果还没记录过，并且是这个等级开启，那么记录下这个vip的等级
	if ($rapidStartLv == 0 && $array['rapid_open_lv'] != 0)
	{
		$rapidStartLv = $ret['vip_lv'];
	}

	// 宠物金币突飞开启等级
	$ret['pet_rapid_open_lv'] = $array['pet_rapid_open_lv'];
	// 如果还没记录过，并且是这个等级开启，那么记录下这个vip的等级
	if ($petRapidStartLv == 0 && $array['pet_rapid_open_lv'] != 0)
	{
		$petRapidStartLv = $ret['vip_lv'];
	}

	// 宠物技能锁定数|锁定需要金币
	$tmp = array();
	for ($index = 0; $index < count($array['pet_skill_lock']); ++$index)
	{
		$tmpExecution = explode('|', $array['pet_skill_lock'][$index]);
		$tmp[intval($tmpExecution[0])]['num'] = intval($tmpExecution[0]);
		$tmp[intval($tmpExecution[0])]['gold'] = intval($tmpExecution[1]);
	}
	$ret['pet_skill_lock'] = $tmp;

	// 会谈同时刷新开启等级
	$ret['talks_refresh'] = $array['talks_refresh'];
	// 如果还没记录过，并且是这个等级开启，那么记录下这个vip的等级
	if ($talksStartLv == 0 && $array['talks_refresh'] != 0)
	{
		$talksStartLv = $ret['vip_lv'];
	}

	// 公会积分每日自动贡献
	$ret['guild_contribute'] = $array['guild_contribute'];

	// 金币捐献公会积分金币上限|对应获得声望
	$tmp = array();
	for ($index = 0; $index < count($array['guild_donate']); ++$index)
	{
		$tmpDonate = explode('|', $array['guild_donate'][$index]);
		$tmp[intval($tmpDonate[0])]['gold'] = intval($tmpDonate[0]);
		$tmp[intval($tmpDonate[0])]['prestige'] = intval($tmpDonate[1]);
	}
	$ret['guild_donate'] = $tmp;

	// 出航奇遇事件奖励显示开启等级
	$ret['sail_reward_open'] = $array['sail_reward_open'];
	// 人物挂机加速次数
	$ret['accelerate_times'] = $array['accelerate_times'];
	// 人物挂机可选择24小时开通金币 —— 0 为不能开通
	$ret['day_mode_open'] = $array['day_mode_open'];

	// 可购买竞技场次数与金币花费
	$tmpArena = explode('|', $array['arena_times_gold'][0]);
	$ret['arena_times_gold']['num'] = intval($tmpArena[0]);
	$ret['arena_times_gold']['gold'] = intval($tmpArena[1]);

	// 强制强化概率100%开通金币 —— 0 为不能开通
	$ret['reinforce_100_open'] = $array['reinforce_100_open'];
	// 强化等级转移可使用金币 —— 0 为不能开通
	$ret['reinforce_transfer'] = $array['reinforce_transfer'];

	// 制作每日开启工匠次数|所需金币
	$tmp = array();
	$tmpArtificer = explode('|', $array['artificer_times_gold']);
	$tmp['times'] = intval($tmpArtificer[0]);
	$tmp['gold'] = intval($tmpArtificer[1]);
	$ret['artificer_times_gold'] = $tmp;

	// 高级制作开启|金币数量
	$tmp = array();
	for ($index = 0; $index < count($array['gold_smelt_open']); ++$index)
	{
		$tmpSmelt = explode('|', $array['gold_smelt_open'][$index]);
		$tmp[intval($tmpSmelt[0])]['type'] = intval($tmpSmelt[0]);
		$tmp[intval($tmpSmelt[0])]['gold'] = intval($tmpSmelt[1]);
		$tmp[intval($tmpSmelt[0])]['base'] = intval($tmpSmelt[2]);
	}
	$ret['gold_smelt_open'] = $tmp;

	// 挖宝金币刷新次数|所需金币
	$tmp = array();
	$tmpTreisure = explode('|', $array['treasure_refresh_gold']);
	$tmp['times'] = intval($tmpTreisure[0]);
	$tmp['gold'] = intval($tmpTreisure[1]);
	$ret['treasure_refresh_gold'] = $tmp;
	
	// 开启探索场景事件数组
	$tmp = array();
	for ($index = 0; $index < count($array['explore_vip']); ++$index)
	{
		if (empty($array['explore_vip'][$index]))
		{
			//nothing	
		}
		else
		{
			$tmpSmelt = explode('|', $array['explore_vip'][$index]);
			$tmp[intval($tmpSmelt[0])]['exporeId'] = intval($tmpSmelt[0]);
			$tmp[intval($tmpSmelt[0])]['pos'] = intval($tmpSmelt[1])-1;
			$tmp[intval($tmpSmelt[0])]['gold'] = intval($tmpSmelt[2]);
		}
	}
	$ret['explore_vip'] = $tmp;

	// 每日金币出航最大次数
	$ret['sail_max_time'] = $array['sail_max_time'];
	// 每日金币生产最大次数
	$ret['cook_max_time'] = $array['cook_max_time'];

	// 潜能洗炼
	$tmp = array();
	for ($index = 0; $index < count($array['refresh_potentiality']); ++$index)
	{
		$tmpExecution = explode('|', $array['refresh_potentiality'][$index]);
		$tmp[intval($tmpExecution[0])] = intval($tmpExecution[1]);
	}
	$ret['refresh_potentiality'] = $tmp;

	// 会谈免费模式开启|花费金币
	$ret['talks_free_mode'] = $array['talks_free_mode'];
	// 会谈刷新次数, 0为无限大
	$ret['talks_refresh_times'] = $array['talks_refresh_times'];
	// 是否开启装备传承
	$ret['arm_transfer_open'] = $array['arm_transfer_open'];
	// 每日任务直接完成所需金币
	$ret['day_task_gold'] = $array['day_task_gold'];
	// 竞技场可否跳过战斗
	$ret['arena_can_skip'] = $array['arena_can_skip'];
	// 世界Boss是否开启自动攻击
	$ret['boss_auto_atk'] = $array['boss_auto_atk'];
	// 世界Boss离线攻击花费金币
	$ret['boss_atk_gold'] = $array['boss_atk_gold'];
	// 开启科技CD花费金币
	$ret['st_cd_gold'] = $array['st_cd_gold'];
    // 一键探索
	$ret['auto_explore'] = $array['auto_explore'];
	// 增加好感度次数 
	$ret['goodwill_vip_num']  = $array['goodwill_vip_num'];  	

	// 每周免费潜能转移次数金币
	$tmp = array();
	$tmpPt = explode('|', $array['potentiality_transfer']);
	$tmp['free_ptransfer_time'] = intval($tmpPt[0]);
	$tmp['gold'] = intval($tmpPt[1]);
	$tmp['item_id'] = intval($tmpPt[2]);
	$ret['potentiality_transfer'] = $tmp;

	// 开启红色藏宝图需要金币
	$ret['treasure_open_gold']  = $array['treasure_open_gold'];
	//免费造魂
	$ret['free_create_soul']  = $array['free_create_soul'];
	// 金币通关精英本花费
	$ret['elitecopy_pass_gold'] = $array['elitecopy_pass_gold'];
	// 副本跳过战斗动画
	$ret['skip_fight'] = $array['skip_fight'];
	// 是否开启养鱼保护罩功能
	$ret['is_open_fish_boot'] = $array['is_open_fish_boot'];
	// 免费金币捕捞育苗次数
	$ret['free_fishing_times'] = $array['free_fishing_times'];
	// 宠物仓库数量
	$ret['max_warehouse_num'] = $array['max_warehouse_num'];
	// vip养鱼额外次数
	$ret['free_farmfish_times'] = $array['free_farmfish_times'];
	// 第1次开启第2养鱼序列是否免费
	$ret['free_open_fishqueie'] = $array['free_open_fishqueie'];
	// 急速探索
	$ret['quick_explore'] = $array['quick_explore'];
	// 是否开启全服喇叭
	$ret['limit_speaker'] = $array['limit_speaker'];
	// 每天挖宝次数限制
	$ret['gold_dig_day_max'] = $array['gold_dig_day_max'];
	// 推进城金币领奖次数
	$ret['impel_down_gold_prize_times'] = $array['impel_down_gold_prize_times'];
	// 推进城vip免费领奖次数
	$ret['impel_down_free_prize_times'] = $array['impel_down_free_prize_times'];
	// 深渊副本购买挑战次数
	$ret['abyss_max_buy'] = $array['abyss_max_buy'];
	// 深渊副本翻牌次数
	$ret['abyss_card_times'] = $array['abyss_card_times'];
	// 每周免费封印转移次数
	$info = explode('|', $array['Seal_freeFreshNum']);
	$ret['Seal_freeFreshNum']['free_num'] = intval($info[0]);
	$ret['Seal_freeFreshNum']['gold'] = intval($info[1]);
	$ret['Seal_freeFreshNum']['itemid'] = intval($info[2]);

	// 开启一键熔炼所需 vip等级|等级
	$tmp = explode('|', $array['smelting_all']);
	$ret['smelting_all']['can'] = intval($tmp[0]);
	$ret['smelting_all']['lv'] = intval($tmp[1]);

	$vipList[$ret['vip_lv']] = $ret;
}
// 记录开启等级
//$vipList['rapid_gold_vip'] = $rapidStartLv;
//$vipList['pet_rapid_gold_vip'] = $petRapidStartLv;
//$vipList['talks_refresh_all_lv'] = $talksStartLv;


fclose($file); // var_dump($vipList);


$file = fopen($argv[2].'/VIP', 'w');
fwrite($file, serialize($vipList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */