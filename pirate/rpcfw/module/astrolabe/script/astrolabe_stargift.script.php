<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: astrolabe_stargift.script.php 30116 2012-10-20 05:15:34Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/astrolabe/script/astrolabe_stargift.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-10-20 13:15:34 +0800 (六, 2012-10-20) $
 * @version $Revision: 30116 $
 * @brief 
 *  
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Astrolabe.def.php";
/*
 * 天赋星盘的天赋
*/

if ( $argc < 2 )
{
	echo "Please input enough arguments:!stargift.csv \n";
	exit;
}

$ZERO = 0;

//数据对应表
$basicstarts = array (
		'ast_id' => $ZERO,							//星盘id
		'phyFDmgRatio'  => ++$ZERO,	//物理伤害倍率
		'phyFEptRatio'  => ++$ZERO,	//物理免伤倍率
		'killFDmgRatio' => ++$ZERO,  //必杀伤害倍率
		'killFEptRatio' => ++$ZERO,	//必杀免伤倍率
		'mgcFDmgRatio'  => ++$ZERO,	//魔法伤害倍率
		'mgcFEptRatio'  => ++$ZERO,	//魔法免伤倍率
		'normalatt'     => ++$ZERO,	//普通攻击技能
		'norattneed'    => ++$ZERO,	//普通攻击技能条件
);

$file = fopen($argv[1].'/stargift.csv', 'r');
if ( $file == FALSE )
{
	echo $argv[1].'/stargift.csv' . " open failed! exit!\n";
	exit;
}
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$asttalent= array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$array = array();
	foreach ( $basicstarts as $key => $v )
	{
		
		if ($key=='phyFDmgRatio' ||$key=='phyFEptRatio' ||
				$key=='killFDmgRatio'||$key=='killFEptRatio'||
				$key=='mgcFDmgRatio' ||$key=='mgcFEptRatio')
		{
			//$array[$key] =floatval((intval($data[$v]))/10000);//AstrolabeDef::ASTROLABE_TALENT_TRANSFER_NUM
			$array[$key] =intval($data[$v]);
		}
		elseif ($key != 'ast_id')
		{
			$array[$key] =$data[$v];
		}
	}
	$asttalent[$data[0]] = $array;
}
fclose($file);

var_dump($asttalent);

//输出文件
$file = fopen($argv[2].'/ASTROLABE_STARGIFT', "w");
if ( $file == FALSE )
{
	echo $argv[2].'/ASTROLABE_STARGIFT'. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($asttalent));
fclose($file);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */