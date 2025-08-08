<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: groupwar.script.php 35772 2013-01-14 09:17:00Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/groupwar/scripts/groupwar.script.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2013-01-14 17:17:00 +0800 (一, 2013-01-14) $
 * @version $Revision: 35772 $
 * @brief 
 *  
 **/


$csvFile = 'group_battle.csv';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}

$index = 0;
$groupBattleConf = array(
		'userNum' => $index++,			//达到此人数用此行配置
		'maxGroupOnlineSize' => $index++,			//每个阵营允许的最大同时在线人数
		'startTime' => $index++,			//上半场开时间
		'battleDuration' => $index++,		//战斗持续时间
		'halfRoundInterval' => $index++,	//半场休息时间
		'prepareTime' => $index++,			//战斗准备时间
		'startDays'  => $index++,			//阵营战开启需要开服时间
		'battleDayArr' => $index++,			//每周开启时间数组
		
		'attackDefendArr' => $index++,		//每等级攻击改变属性数值数组
		'attackDefendMaxLevel' => $index++,	//攻防等级上限
		'inspireBaseProp' => $index++,		//鼓舞基础概率
		'inspireProbCoef' => $index++,		//鼓舞成功系数
		'inspireExperienceNum' => $index++,	//鼓舞所需阅历基础值
		'inspireGoldNum' => $index++,		//鼓舞所需金币
		'inspireCdTime' => $index++,		//鼓舞冷却时间		
		
		'joinMinLevel' => $index++,			//参战最低等级
		'joinCdTime' => $index++,			//参战冷却时间
		'joinCdBaseGold' => $index++,		//秒除参战冷却时间需要的基础金币
		'presenceIntervalMs' => $index++,	//玩家加入战场时间间隔
		'maxWaitQueue' => $index++,			//战斗队列人数上限
		
		'initResource' => $index++,			//阵营战初始资源点
		'joinScore' => $index++,			//阵营战基础战斗积分
		'plunderScore' => $index++,			//阵营战掠夺资源积分
		'killScoreArr' => $index++,			//阵营战击杀基础积分
		'streakCoefArr' => $index++,		//击杀额外连胜次数系数
		'killBelly' => $index++,			//阵营战击杀贝里基础值
		'killExperience' => $index++,		//阵营战击杀阅历基础值
		'killPrestige' => $index++,			//阵营战击杀声望基础值
		'killSoul' => $index++,				//阵营战击杀影魂基础值
		'killHonour' => $index++,			//阵营战击杀基础荣誉
		'streakHonourArr' => $index++,		//击杀连胜额外获得荣誉
		'joinHonour' => $index++,			//战斗基础荣誉
		'plunderHonour' => $index++,		//阵营战掠夺资源荣誉,
		
		'speed' => $index++, 				//默认移动速度
		'roadLength' => $index++,			//通道长度
		'collisionRange' => $index++,		//碰撞检测距离	
		
		'resourceRewardMax' => $index++,	//资源最终奖励系数上限		
		'resourceRewardMin' => $index++,	//资源最终奖励系数下限		
		'joinReadyTime' => $index++,		//进入战场准备时间
		
		'winBelly' => $index++,		//胜利阵营所有成员额外获得荣誉
		'winHonour' => $index++,		//胜利阵营所有成员额外获得荣誉
		
		'joinCdIncGold' => $index++,		//没多秒除参战冷却，需要的额外金币
		);

$file = fopen($argv[1]."/$csvFile", 'r');
if ( $file == FALSE )
{
	echo $argv[1]."/{$csvFile} open failed! exit!\n";
	exit;
}

$data = fgetcsv($file);
$data = fgetcsv($file);

$confList = array();

while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$conf = array();
	foreach ( $groupBattleConf as $attName => $index )
	{
		if(preg_match( '/^[a-zA-Z]*Arr$/' ,$attName ))
		{
			if(empty($data[$index]))
			{
				$reward[$attName] = array();
				continue;
			}
			$arr = explode(',', $data[$index]);
			if(is_numeric($arr[0]))
			{
				$conf[$attName] = $arr;
			}
			else
			{
				$conf[$attName] = array();
				foreach( $arr as $value )
				{
					$conf[$attName][] = explode('|', $value);
				}
			}
		}
		else
		{
			$conf[$attName] = $data[$index];
		}
	}
	$confList[] = $conf;
}


fclose($file);



//虽然csv文件中的配置可以有多行，但是只有个别键是不一样的， 所以还是整理一下格式
$diffKeys = array('userNum', 'presenceIntervalMs', 'roadLength');
reset($confList);
$groupBatlteConf = current($confList);
$groupBatlteConf['diff'] = array();
foreach($confList as $conf)
{
	$diffConf = array();
	foreach($conf as $key=>$value)
	{
		if( in_array($key, $diffKeys))
		{
			$diffConf[$key] = $value;
		}
	}
	$groupBatlteConf['diff'][] = $diffConf;
}

foreach($diffKeys as $key)
{
	unset($groupBatlteConf[$key]);
}
//var_dump($groupBatlteConf);


//输出文件
$outFileName = 'GROUP_BATTLE';
$file = fopen($argv[2].'/'.$outFileName, "w");
if ( $file == FALSE )
{
	echo $argv[2].'/'.$outFileName. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($groupBatlteConf));
fclose($file);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */