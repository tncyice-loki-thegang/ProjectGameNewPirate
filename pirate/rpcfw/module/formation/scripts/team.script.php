<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: team.script.php 18094 2012-04-06 09:15:21Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/formation/scripts/team.script.php $
 * @author $Author: YangLiu $(lanhongyu@babeltime.com)
 * @date $Date: 2012-04-06 17:15:21 +0800 (五, 2012-04-06) $
 * @version $Revision: 18094 $
 * @brief 
 *  
 **/

//读怪物小队表
if ( $argc < 3 )
{
	echo "Please input enough arguments:!TEAM.csv output\n";
}

$fileName = $argv[1].'/team.csv';
$armyFile = $argv[2].'/TEAM';

//把字符串转为数组
/*
 字符串样例如下：
B1,0,0,
B2,0,0,
0,0,0,
 * 
 */
function change33ArrTo19Arr($str)
{
	//去掉换行
	$str = str_replace(array("\r", "\n"), "", $str);
	//去掉末尾字符","
	if ($str[strlen($str)-1] == ',')
	{
		$str = substr($str, 0, strlen($str)-1);
	}
	
	$arr = explode(',', $str);
	if (count($arr) != 9)
	{
		exit("the number of elements error:" . $str);
	}
	return $arr;
}

$name = array (
'id' => 0,										// 怪物小队ID
't_name' => 1,									// 怪物小队模板名称
'display_name' => 2,							// 怪物小队显示名称
'display_lv' => 3,								// 怪物小队显示等级
'display_f_id' => 4,							// 怪物小队显示阵型
'type' => 5,									// 怪物小队类型（1:怪物；2：NPC。）
'f_id' => 6,									// 阵型ID 
'f_lv' => 7,									// 阵型等级
'monster_id' => 8								// 位置怪物ID
);


$inFile = $argv[1].'/team.csv';
$outFile = $argv[1].'/team_tmp.csv';
$cmd = "iconv -c -f GB2312 -t utf-8 ".$inFile." > ".$outFile;
exec($cmd);


$handle = fopen($outFile, 'r') 
	or die("fail to open $outFile");

//忽略前两行
$data = fgetcsv($handle);
$data = fgetcsv($handle);

$arrNewMonster = array();

while (($data=fgetcsv($handle))!=false)
{
	// army id
	$army['aid'] = intval($data[$name['id']]);

	// 阵型id和等级
	$army['fid'] = intval($data[$name['f_id']]);
	$army['fmtLevel'] = intval($data[$name['f_lv']]);

	// 显示名称和等级
	$army['display_name'] = $data[$name['display_name']];
	$army['display_lv'] = intval($data[$name['display_lv']]);

	// 阵型中怪物的位置
	$fmt = $data[$name['monster_id']];
	$arrFmt = change33ArrTo19Arr($fmt);


//	//对修正等级的怪物产生新的怪物
//	//处理阵型的id
//	for($i = 0; $i < 9; ++$i)
//	{
//		$t = $arrFmt[$i];
//		if (strlen($t) == 1)
//		{
//			if ($t != 0 && $t != 1)
//			{
//				exit("fmt's value error:" . $fmt);
//			}
//			else
//			{
//				$arrFmt[$i] = intval($t);
//			}
//		}
//		else
//		{
//			// 解析怪物ID
//			if ($t[0] === "A")
//			{
//				$num = 10000;
//			}
//			else if ($t[0] === "B")
//			{	
//				$num = 100000;
//			}
//			else
//			{
//				exit("fmt's value error:" . $fmt);
//			}
//
//			$arrFmt[$i] = $num + intval(substr($t, 1));
//		}
//	}

	$army['fmt'] = $arrFmt;
	$allArmy[$army['aid']] = $army;
}


//var_dump($allArmy);
$handle = fopen($armyFile, "w");
fwrite($handle, serialize($allArmy));
fclose($handle);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */