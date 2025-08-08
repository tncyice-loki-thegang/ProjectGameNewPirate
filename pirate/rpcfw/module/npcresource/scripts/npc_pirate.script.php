<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: npc_pirate.script.php 34669 2013-01-07 12:06:11Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/branches/pirate/rpcfw/newworldres/module/npcresource/scripts/npc_pirate.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-07 20:06:11 +0800 (星期一, 07 一月 2013) $
 * @version $Revision: 34669 $
 * @brief 
 *  
 **/

require_once dirname ( dirname( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/NPCReource.def.php";

if ( $argc < 2 )
{
	echo "Please input enough arguments:!npc_pirate.csv output\n";
	exit;
}


//数据对应表
$name = array (
		NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_ID					=>		0,	//海贼团ID
		NPCResourceDef::NPC_RESOURCE_CSV_ARMY_IDS					=>		3,	//海贼团部队表ID组
		NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_WEIGHTS				=>		4,	//海贼团权重
);

$file = fopen($argv[1].'/npc_pirate.csv', 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);


$npc_pirate = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$valary=array();
	foreach ( $name as $key => $v )
	{
		if ($key == NPCResourceDef::NPC_RESOURCE_CSV_ARMY_IDS)
		{
			$idary=array();
			$ary=explode('|', $data[$v]);
			foreach ($ary as $val)
			{
				$valary[$key][]= intval($val);
			}
		}
		else
		{
			$valary[$key] = intval($data[$v]);
		}
	}
	$pirate_id=$valary[NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_ID];
	unset($valary[NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_ID]);
	$npc_pirate[$pirate_id]=$valary;
}
fclose($file);

//输出文件
$file = fopen($argv[2].'/NPC_PIRATE', "w");
if ( $file == FALSE )
{
	echo $argv[2].'/NPC_PIRATE'. " open failed! exit!\n";
	exit;
}

fwrite($file, serialize($npc_pirate));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */