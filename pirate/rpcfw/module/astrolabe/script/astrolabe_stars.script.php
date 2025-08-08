<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: astrolabe_stars.script.php 32905 2012-12-12 04:45:55Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/astrolabe/script/astrolabe_stars.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-12-12 12:45:55 +0800 (三, 2012-12-12) $
 * @version $Revision: 32905 $
 * @brief 
 *  
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Creature.def.php";
require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Astrolabe.def.php";

/*
 * 导入星盘表
*/

if ( $argc < 2 )
{
	echo "Please input enough arguments:!stars.csv \n";
	exit;
}

$ZERO = 0;

//数据对应表
$starts = array (
		'astId' => $ZERO,								// 星座id
		'astName' => $ZERO+=2,							// 星座名称
		'astLevel' => $ZERO+=3,							// 星座阶别
		'isMain' => ++$ZERO,							// 是否为基本星座
		'trayId' => ++$ZERO,							// 星盘ID
		'astExpId' => ++$ZERO,							// 星座升级经验表ID
		'astOpenPremiss' => ++$ZERO,					// 开启星座需要星座ID级别组
		'astOpenUserOcc' => ++$ZERO,					// 开启星座需要主角转职次数
		'astOpenUserLevel' => ++$ZERO,					// 开启星座需要主角等级
		'astOpenTrayId' => ++$ZERO,						// 激活星座天赋ID
		'astAttr1' => ++$ZERO,							//属性1ID
		'astAttr1Value' => ++$ZERO,						//属性1每级增加属性
		'astAttr2' => ++$ZERO,							//属性2ID
		'astAttr2Value' => ++$ZERO,						//属性2每级增加属性
		'astAttr3' => ++$ZERO,							//属性3ID
		'astAttr3Value' => ++$ZERO,						//属性3每级增加属性
		'astAttr4' => ++$ZERO,							//属性4ID
		'astAttr4Value' => ++$ZERO,						//属性4每级增加属性
		'astAttr5' => ++$ZERO,							//属性5ID
		'astAttr5Value' => ++$ZERO,						//属性5每级增加属性
		'astLevelLimit' => ++$ZERO,						//星座等级上限
		'costitems' => ++$ZERO							//需要消耗物品ID组
);

//对属性值进行转换操作
 function transformAttr($id,$val)
 {
 	$newval=$val;
 	
 	switch ($id)
 	{
 		//力量、敏捷、智力 要除以100
 		case  CreatureInfoKey::strength:
 		case  CreatureInfoKey::agile:
 		case  CreatureInfoKey::intelligence:
 			$newval=$val/100;
 			break;
 		//生命百分比除以100
 		case  CreatureInfoKey::hpRatio:
 			$newval=$val/100;
 			break;
 		//生命、物攻、物防、必攻、必防、魔攻、魔防 要除以10000
 		case  CreatureInfoKey::phyAtkRatio:
 		case  CreatureInfoKey::phyDfsRatio:
 		case  CreatureInfoKey::killAtkRatio:
 		case  CreatureInfoKey::killDfsRatio:
 		case  CreatureInfoKey::mgcAtkRatio:
 		case  CreatureInfoKey::mgcDfsRatio:
 			$newval=$val/10000;
 			break;
 		//物理伤害倍率  物理免伤倍率 必杀伤害倍率 必杀免伤倍率 魔法伤害倍率 魔法免伤倍率 除以10000
 		case  CreatureInfoKey::phyFDmgRatio  : //调整物理伤害倍率	物理伤害倍率
		case  CreatureInfoKey::phyFEptRatio  : //调整物理免伤倍率	物理免伤倍率
		case  CreatureInfoKey::killFDmgRatio : //调整必杀伤害倍率	必杀伤害倍率
		case  CreatureInfoKey::killFEptRatio : //调整必杀免伤倍率	必杀免伤倍率
		case  CreatureInfoKey::mgcFDmgRatio  : //调整魔法伤害倍率	魔法伤害倍率
		case  CreatureInfoKey::mgcFEptRatio  : //调整魔法免伤倍率	魔法免伤倍率
 			$newval=$val/10000;
 			break;
 		default:
 			break;
 	}
 	return $newval;
 }
 
$file = fopen($argv[1].'/stars.csv', 'r');
if ( $file == FALSE )
{
	echo $argv[1].'/stars.csv' . " open failed! exit!\n";
	exit;
}
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$constellation = array();
$consDepends=array();//当某个星星升级时，需要去检测哪些星星是否可开启
$mainDepends=array();//当主星盘升级时，需要去检测哪些星星是否可开启
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$array = array();
	foreach ( $starts as $key => $v )
	{
		if ($key == 'astOpenPremiss')
		{
			$tmpary=array();
			$tmp= explode(',', $data[$v]);
			foreach ($tmp as $val)
			{
				$idlevel= explode('|', $val);
				$id=empty($idlevel[0]) ? 0 : $idlevel[0];
				$level = empty($idlevel[1]) ? 0 : $idlevel[1];
				$tmpary[$id]= intval($level);
			}
			$array[$key]=$tmpary;
				
			//生成依赖表
			foreach ($tmpary as $id => $lev)
			{
				if (empty($id))
					continue;
				if ( empty($consDepends[$id]) && $id > 0)
				{
					$consDepends[$id]=array( $array['astId']);
				}
				elseif ($id < 0 && !in_array($array['astId'],$mainDepends))
				{
					$mainDepends[$array['astId']]=$array['isMain'];
				}
				else
				{
					$consDepends[$id][]=$array['astId'];
				}
			}
				
		}
		elseif ($key == 'astName')
		{
			$array[$key] = $data[$v];
		}
		elseif ($key == 'astAttr1'||$key == 'astAttr2'||
				$key == 'astAttr3'||$key == 'astAttr4'||$key == 'astAttr5')
		{
			//代码里的id和实际策划传过来的id不一致，需要转换一下
			$id	=empty($data[$v])?0:$data[$v];//策划传来的id
			$newid=empty(AstrolabeDef::$mapAstrolabeattr[$id])?0:AstrolabeDef::$mapAstrolabeattr[$id];//程序里使用的id
			$array[$key] =$newid;
		}
		elseif ($key == 'astAttr1Value'||$key == 'astAttr2Value'||
				$key == 'astAttr3Value'||$key == 'astAttr4Value'||
				$key == 'astAttr5Value')
		{  
			$val=empty($data[$v])?0:$data[$v];
			$array[$key] =$val;
		}
		elseif ($key == 'costitems')
		{
			
			$tmpary=array();
			if (isset($data[$v]))
			{
				$tmp= explode(',', $data[$v]);
				foreach ($tmp as $val)
				{
					$items= explode('|', $val);
					$item=empty($items[0]) ? 0 : $items[0];
					$num = empty($items[1]) ? 0 : $items[1];
					$tmpary[$item]= intval($num);
				}
			}
			$array[$key]=$tmpary;
		}
		else
		{
			$array[$key] =intval($data[$v]);
		}
	}
	$constellation[$array['astId']] = $array;
}
fclose($file);

//输出文件
$file = fopen($argv[2].'/ASTROLABE_STARS', "w");
if ( $file == FALSE )
{
	echo $argv[2].'/ASTROLABE_STARS'. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($constellation));
fclose($file);

//输出依赖表
$file = fopen($argv[2].'/ASTROLABE_DEPEND', "w");
if ( $file == FALSE )
{
	echo $argv[2].'/ASTROLABE_DEPEND'. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($consDepends));
fclose($file);

//输出依赖表
$file = fopen($argv[2].'/ASTROLABE_DEPEND_MAIN', "w");
if ( $file == FALSE )
{
	echo $argv[2].'/ASTROLABE_DEPEND_MAIN'. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($mainDepends));
fclose($file);



/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */