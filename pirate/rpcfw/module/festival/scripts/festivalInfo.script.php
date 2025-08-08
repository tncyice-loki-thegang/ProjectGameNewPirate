<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: festivalInfo.script.php 29607 2012-10-16 07:46:38Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/scripts/festivalInfo.script.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-10-16 15:46:38 +0800 (二, 2012-10-16) $
 * @version $Revision: 29607 $
 * @brief 
 *  
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Festival.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!jierihuodong.csv output\n";
	exit;
}

$ZERO = 0;
//数据对应表
$name = array (
FestivalDef::FESTIVAL_ID => $ZERO,								// 节日活动ID
FestivalDef::FESTIVAL_BEGIN_DATA => ++$ZERO,					// 节日活动开始时间
FestivalDef::FESTIVAL_END_DATA => ++$ZERO,						// 节日活动结束时间
FestivalDef::FESTIVAL_SAIL_REWARD => ++$ZERO,					// 活动期出航加成
FestivalDef::FESTIVAL_FOOD_REWARD => ++$ZERO,					// 活动期菜肴卖加成
FestivalDef::FESTIVAL_COPY_REWARD => ++$ZERO,					// 活动期副本战斗阅历加成
FestivalDef::FESTIVAL_BATTLE_REWARD => ++$ZERO,					// 活动期战斗经验加成
FestivalDef::FESTIVAL_RESOURCE_REWARD => ++$ZERO,				// 活动期港口资源矿收入加成
FestivalDef::FESTIVAL_TRAIN_REWARD => ++$ZERO,					// 活动期伙伴训练经验加成
FestivalDef::FESTIVAL_RAPID_REWARD => ++$ZERO,					// 活动期伙伴突飞经验加成
FestivalDef::FESTIVAL_POINT_MAX => ++$ZERO,						// 活动期间翻牌次数上限
FestivalDef::FESTIVAL_FLOPCARD_POINT => ++$ZERO,				// 翻牌需要积分
FestivalDef::FESTIVAL_PIC => ++$ZERO,							// 活动图片
FestivalDef::FESTIVAL_INTRODUCE1 => ++$ZERO	,					// 活动收益描述
FestivalDef::FESTIVAL_INTRODUCE2 => ++$ZERO,					// 活动介绍
FestivalDef::FESTIVAL_PRACTICE => ++$ZERO,						// 历练经验加成
FestivalDef::FESTIVAL_TREASURE_PURPLESTAR => ++$ZERO,			// 寻宝紫星加成
FestivalDef::FESTIVAL_MAKEITEM_PURPLESTAR => ++$ZERO,			// 装备制作紫星加成
FestivalDef::FESTIVAL_FLOPCARD_ONOFF => ++$ZERO,				// 活动翻牌是否开启
FestivalDef::FESTIVAL_TREASURE_REDSTAR => ++$ZERO,				// 寻宝红星加成
FestivalDef::FESTIVAL_MAKEITEM_REDSTAR => ++$ZERO,				// 装备制作红星加成
);

$file = fopen($argv[1].'/jierihuodong.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$festival = array();
while (TRUE)
{
	$data = fgetcsv($file);
	if (empty($data))
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if($key == FestivalDef::FESTIVAL_PIC || 
			$key == FestivalDef::FESTIVAL_INTRODUCE1 || 
			$key == FestivalDef::FESTIVAL_INTRODUCE2)
		{
			continue;
		}
		$array[$key] = intval($data[$v]);
	}

	$festival[$array[FestivalDef::FESTIVAL_ID]] = $array;

}
fclose($file); //var_dump($salary);
print_r($festival);

$file = fopen($argv[2].'/JIERIHUODONG', 'w');
fwrite($file, serialize($festival));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
