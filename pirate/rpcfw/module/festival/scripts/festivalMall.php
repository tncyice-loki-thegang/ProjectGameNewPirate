<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: festivalMall.php 31614 2012-11-22 07:03:25Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/scripts/festivalMall.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-11-22 15:03:25 +0800 (四, 2012-11-22) $
 * @version $Revision: 31614 $
 * @brief 
 *  
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Festival.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!jieri_shop.csv output\n";
	exit;
}

$ZERO = 0;
//数据对应表
$name = array (
FestivalDef::FESTIVAL_ID => $ZERO,								// 节日活动ID
FestivalDef::FESTIVAL_BEGIN_DATA => ++$ZERO,					// 节日活动开始时间
FestivalDef::FESTIVAL_END_DATA => ++$ZERO,						// 节日活动结束时间
FestivalDef::FESTIVAL_PIC => ++$ZERO,							// 活动图片
FestivalDef::FESTIVAL_INTRODUCE1 => ++$ZERO	,					// 活动收益描述
FestivalDef::FESTIVAL_INTRODUCE2 => ++$ZERO,					// 活动介绍
FestivalDef::FESTIVAL_EXCHANGEITEMS => ++$ZERO,					// 积分兑换活动物品积分组
FestivalDef::FESTIVAL_SERVER_OPENDATE => ++$ZERO,				// 活动开启需要开服时间
);

$file = fopen($argv[1].'/jieri_shop.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$festival = array();
while (TRUE)
{
	$data = fgetcsv($file);
	if (empty($data))
		break;

	$itemArray = array();
	$tempArray = explode(",", $data[$name[FestivalDef::FESTIVAL_EXCHANGEITEMS]]);
	foreach ($tempArray as $key => $value)
	{
		$ary = explode("|", $value);
		$itemArray[$ary[0]] = $ary[1];
	}
	
	$array = array();
	foreach ( $name as $key => $v )
	{
		if($key == FestivalDef::FESTIVAL_PIC || 
		$key == FestivalDef::FESTIVAL_INTRODUCE1 || 
		$key == FestivalDef::FESTIVAL_INTRODUCE2)
		{
			continue;
		}
		
		if($key == FestivalDef::FESTIVAL_EXCHANGEITEMS)
		{
			$array[$key] = $itemArray;
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$festival[$array[FestivalDef::FESTIVAL_ID]] = $array;

}
fclose($file); //var_dump($salary);
print_r($festival);

$file = fopen($argv[2].'/FESTIVALMALL', 'w');
fwrite($file, serialize($festival));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */