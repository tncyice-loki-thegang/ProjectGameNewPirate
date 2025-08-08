<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: captainRoom.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/captain/scripts/captainRoom.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!CAPTAIN_ROOM.csv output\n";
	exit;
}


$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 船长室ID
'name' => ++$ZERO,								// 船长室名称
'detail' => ++$ZERO,							// 船长室描述
'res_id' => ++$ZERO,							// 船长室资源ID
'ico_id' => ++$ZERO,							// 船长室图标ID
'init_lv' => ++$ZERO,							// 船长室初始等级
'lv_up_cost_id' => ++$ZERO,						// 升级费用表
'sailing_img' => ++$ZERO,						// 出航图片资源ID
'sail_belly_base' => ++$ZERO,					// 船长室出航游戏币基础值
'tired_coefficient' => ++$ZERO,					// 疲劳度系数
'sail_times_base' => ++$ZERO,					// 船长室出航次数基础值
'sail_times_max' => ++$ZERO,					// 船长室出航次数最大值
'gold_sail_times_base' => ++$ZERO,				// 船长室金币出航基础值
'gold_sail_times_up' => ++$ZERO,				// 船长室金币出航增长值
'gold_per_cd' => ++$ZERO,						// 秒出航时间CD每1金币对应时间
'sail_cd_up' => ++$ZERO,						// 出航冷却时间
'sail_guild_sc_wight' => ++$ZERO,				// 出航捐献公会科技权重
'sail_gold_base' => ++$ZERO,					// 主船出航金币基础权重
'answer_id' => ++$ZERO,							// 出航答题包
'subordinate_num_lvs' => ++$ZERO,				// 下属数量上限和等级数组
'teach_times_max' => ++$ZERO,					// 每个下属每日调教次数上限
'tribute_coefficient' => ++$ZERO				// 下属出航收入贡献主人系数
);


$item = array();
$file = fopen($argv[1].'/captain_room.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$petRoom = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		// 竖线数组
		if ($key == 'subordinate_num_lvs')
		{
			$array[$key] = explode(',', $data[$v]);
		}
		else 
		{
			$array[$key] = $data[$v];
		}
	}

	// 下属数量上限和等级数组
	$tmp = array();
	$i = 0;
	for ($index = 0; $index < count($array['subordinate_num_lvs']); ++$index)
	{
		$tmpLock = explode('|', $array['subordinate_num_lvs'][$index]);
		// 前面是属城数量上限，后面是成就等级
		$tmp[$index]['max'] = intval($tmpLock[0]);
		$tmp[$index]['lv'] = intval($tmpLock[1]);
	}
	$array['subordinate_num_lvs'] = $tmp;

	$petRoom = $array;
}
fclose($file); //var_dump($petRoom);


$file = fopen($argv[2].'/CAPTAIN_ROOM', 'w');
fwrite($file, serialize($petRoom));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */