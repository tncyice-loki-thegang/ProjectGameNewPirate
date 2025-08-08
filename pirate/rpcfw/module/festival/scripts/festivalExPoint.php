<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: festivalExPoint.php 31548 2012-11-21 10:09:26Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/scripts/festivalExPoint.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-11-21 18:09:26 +0800 (三, 2012-11-21) $
 * @version $Revision: 31548 $
 * @brief 
 *  
 **/
require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Festival.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!huodong_jifen.csv output\n";
	exit;
}

$ZERO = 0;
//数据对应表
$name = array (
FestivalDef::FESTIVAL_EXPOINT_ID => $ZERO,						// 节日积分兑换ID
FestivalDef::FESTIVAL_EXPOINT_NAME => ++$ZERO,					// 节日积分兑换名称
FestivalDef::FESTIVAL_EXPOINT_POINT => ++$ZERO,					// 节日积分
FestivalDef::FESTIVAL_EXPOINT_BASEGOLD => ++$ZERO,					// 基本兑换基础值
);

$file = fopen($argv[1].'/huodong_jifen.csv', 'r');
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
		$array[$key] = intval($data[$v]);
	}

	$festival[$array[FestivalDef::FESTIVAL_EXPOINT_ID]] = $array;

}
fclose($file); //var_dump($salary);
print_r($festival);

$file = fopen($argv[2].'/FESTIVALEXPOINT', 'w');
fwrite($file, serialize($festival));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */