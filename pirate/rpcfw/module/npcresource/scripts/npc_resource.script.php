<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: npc_resource.script.php 35093 2013-01-09 09:38:12Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/branches/pirate/rpcfw/newworldres/module/npcresource/scripts/npc_resource.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-09 17:38:12 +0800 (星期三, 09 一月 2013) $
 * @version $Revision: 35093 $
 * @brief 
 *  
 **/


require_once dirname ( dirname( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/NPCReource.def.php";

if ( $argc < 2 )
{
	echo "Please input enough arguments:!npc_res.csv output\n";
	exit;
}

//数据对应表
$name = array (
		NPCResourceDef::NPC_RESOURCE_CSV_ENTER_MIN_LEVEL			=>		0,	//进入矿区最低等级
		NPCResourceDef::NPC_RESOURCE_CSV_START_TIME					=>		1,	//矿区开放开始时间
		NPCResourceDef::NPC_RESOURCE_CSV_END_TIME					=>		2,	//矿区结束时间
		NPCResourceDef::NPC_RESOURCE_CSV_PLUNDER_COUNT				=>		3,	//每日掠夺次数
		NPCResourceDef::NPC_RESOURCE_CSV_OCCUPY_COUNT				=>		4,	//每日占领次数
		NPCResourceDef::NPC_RESOURCE_CSV_ATTACK_INTERVAL			=>		5,	//NPC进攻间隔时间
		NPCResourceDef::NPC_RESOURCE_CSV_OCCUPY_PROTECT_TIME		=>		6,	//占领保护时间
		NPCResourceDef::NPC_RESOURCE_CSV_PLUNDER_FAIL_CD			=>		7,	//掠夺失败CD
		NPCResourceDef::NPC_RESOURCE_CSV_MAX_OCCUPY_TIME			=>		8,	//资源矿最大占领时间
		NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_COUNT				=>		9,	//每日选出海贼团数量
		NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_LEV_LIMIT			=>		10,	//海贼团配置等级段组
		NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_ID_ARY				=>		11,	//海贼团配置ID组
		NPCResourceDef::NPC_RESOURCE_CSV_RES_PAGE_COUNT				=>		12,	//矿区页数
		NPCResourceDef::NPC_RESOURCE_CSV_RES_ID_ATTR_ARY			=>		14,	//资源矿属性组
		NPCResourceDef::NPC_RESOURCE_CSV_MANUAL_COST				=>		16, //手动执行npc进攻是的花费
		NPCResourceDef::NPC_RESOURCE_CSV_MANUAL_CD					=>		17, //手动执行npc进攻的冷却cd（秒）
		NPCResourceDef::NPC_RESOURCE_CSV_INCOME_RATE_1				=>		18,	//收益系数1
		NPCResourceDef::NPC_RESOURCE_CSV_INCOME_RATE_2				=>		19, //收益系数2
);

$file = fopen($argv[1].'/npc_res.csv', 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);


$npc_res = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$valary=array();
	foreach ( $name as $key => $v )
	{
		if ($key == NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_LEV_LIMIT)
		{
			$levelary=array();
			$ary=explode(',', $data[$v]);
			foreach ($ary as $val)
			{
				$levels=explode('|', $val);
				$levelmin=empty($levels[0]) ? 0 : $levels[0];
				$levelmax = empty($levels[1]) ? 0 : $levels[1];
				
				$levelary[]=array(NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_LEV_MIN=>$levelmin,
						NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_LEV_MAX=>$levelmax);
			}
			$valary[$key]=$levelary;
		}
		elseif ($key == NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_ID_ARY)
		{
			$idary=array();
			$ary=explode(',', $data[$v]);
			foreach ($ary as $val)
			{
				$ids=explode('|', $val);
				$idary=array();
				foreach ($ids as $val)
				{
					$idary[]=intval($val);
				}
				$valary[$key][]= $idary;
			}
		}
		elseif ($key == NPCResourceDef::NPC_RESOURCE_CSV_RES_ID_ATTR_ARY)
		{
			$idary=array();
			$ary=explode(',', $data[$v]);
			foreach ($ary as $val)
			{
				$idattr	=explode('|', $val);
				$resid	=empty($idattr[0]) ? 0 : $idattr[0];
				$pageid	=empty($idattr[1]) ? 0 : $idattr[1];
				$attr 	= empty($idattr[4])? 0 : $idattr[4];
				$valary[$key][intval($pageid)][intval($resid)]=intval($attr);
				//$valary[$key][intval($resid)]= array(NPCResourceDef::NPC_RESOURCE_CSV_RES_PAGE_ID=>intval($pageid),
				//		NPCResourceDef::NPC_RESOURCE_CSV_RES_ATTR=>intval($attr));
			}
		}
		elseif ($key == NPCResourceDef::NPC_RESOURCE_CSV_MANUAL_COST)
		{
			$cost=explode(',', $data[$v]);
			$gold=empty($cost[0]) ? 0 : $cost[0];
			$belly= empty($cost[1]) ? 0 : $cost[1];
			$valary[$key]=array('gold'=>$gold,'belly'=>$belly);
		}
		else
		{
			$valary[$key] = intval($data[$v]);
		}
	}

	$npc_res=$valary;
}
fclose($file);

//输出文件
$file = fopen($argv[2].'/NPC_RES', "w");
if ( $file == FALSE )
{
	echo $argv[2].'/NPC_RES'. " open failed! exit!\n";
	exit;
}

fwrite($file, serialize($npc_res));
fclose($file);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */