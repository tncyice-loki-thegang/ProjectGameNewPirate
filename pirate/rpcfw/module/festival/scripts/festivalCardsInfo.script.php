<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: festivalCardsInfo.script.php 26932 2012-09-10 10:15:48Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/scripts/festivalCardsInfo.script.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-09-10 18:15:48 +0800 (一, 2012-09-10) $
 * @version $Revision: 26932 $
 * @brief 
 *  
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Festival.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!festival.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
FestivalDef::FESTIVAL_CARD_ID => $ZERO,					// 牌ID
FestivalDef::FESTIVAL_CARD_NAME => ++$ZERO,				// 奖励模板名称
FestivalDef::FESTIVAL_CARD_BELLY => ++$ZERO,			// 奖励贝里基础值
FestivalDef::FESTIVAL_CARD_EXPE => ++$ZERO,				// 奖励阅历基础值
FestivalDef::FESTIVAL_CARD_GOLD => ++$ZERO,				// 奖励金币
FestivalDef::FESTIVAL_CARD_EXEC => ++$ZERO,				// 奖励行动力
FestivalDef::FESTIVAL_CARD_PRES => ++$ZERO,				// 奖励声望
FestivalDef::FESTIVAL_CARD_ITEM => ++$ZERO,				// 奖励掉落表ID组
FestivalDef::FESTIVAL_CARD_WEIGHT => ++$ZERO			// 奖励掉落权重
);


$file = fopen($argv[1].'/jierihuodong_jiangli.csv', 'r');
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
	// ID
	$array[FestivalDef::FESTIVAL_CARD_ID] = intval($data[$name[FestivalDef::FESTIVAL_CARD_ID]]);
	// 权重总和
	if (trim($data[$name[FestivalDef::FESTIVAL_CARD_WEIGHT]]) !== "")
	{
		$array[FestivalDef::FESTIVAL_CARD_WEIGHT] = 0;
		$tempArray = explode(",", $data[$name[FestivalDef::FESTIVAL_CARD_WEIGHT]]);
		foreach ( $tempArray as $tempKey => $value )
		{
			$array[FestivalDef::FESTIVAL_CARD_WEIGHT] += intval($value);
		}
	}

	$array[FestivalDef::FESTIVAL_SONCARD] = array();
	// 根据物品个数循环
	$itemArray = explode(",", $data[$name[FestivalDef::FESTIVAL_CARD_ITEM]]);
	$weightArray = explode(",", $data[$name[FestivalDef::FESTIVAL_CARD_WEIGHT]]);

	for ($i = 0; $i < count($itemArray); $i++) {
		$tempArray = array();
		foreach ($name as $key => $v)
		{
			if ($key == FestivalDef::FESTIVAL_CARD_NAME)
			{
				continue;
			}
			if ($key == FestivalDef::FESTIVAL_CARD_ITEM)
			{
				// 装备表ID
				if (trim($itemArray[$i]) !== "")
				{
					$tempArray[$key] = intval($itemArray[$i]);
				}
			} 
			else if ($key == FestivalDef::FESTIVAL_CARD_WEIGHT)
			{
				// 权重
				if (trim($weightArray[$i]) !== "")
				{
					$tempArray[$key] = intval($weightArray[$i]);
				}
			}
			else
			{
				$tempArray[$key] = intval($data[$v]);
			}
		}
		
		$array[FestivalDef::FESTIVAL_SONCARD][] = $tempArray;
	}
	$festival[$array[FestivalDef::FESTIVAL_CARD_ID]] = $array;
}
fclose($file); //var_dump($salary);


$file = fopen($argv[2].'/JIERIHUODONG_JIANGLI', 'w');
fwrite($file, serialize($festival));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
