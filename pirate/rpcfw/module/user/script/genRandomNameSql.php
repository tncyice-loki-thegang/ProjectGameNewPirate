<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: genRandomNameSql.php 30984 2012-11-13 08:34:35Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/script/genRandomNameSql.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-11-13 16:34:35 +0800 (二, 2012-11-13) $
 * @version $Revision: 30984 $
 * @brief 
 *  
 **/


$wsNum = 0;
$hexieNum = 0;

function readName($filename)
{
	$arrRet = array();
	$handle = fopen($filename, 'r');
	
	global $wsNum;
	global $hexieNum;
	
	if ($handle)
	{
		while ( !feof($handle) )
		{
			$str = fgets($handle);
			$str = trim($str);
			$str = mb_convert_encoding($str, 'UTF-8', 'gbk');
			$len = strlen($str);

			if ($len==0 )// || $len>6)
			{				
				continue;
			}
			
			if (mb_strlen($str, 'utf8') > 6)
			{
				var_dump($str . " len err");
			}
			
			//skip whitespace
			if (false!==strpos($str, ' '))
			{
				++$wsNum;
				continue;
			}
			
			//过滤敏感词
			$ret = trie_filter_search($str);
			if (!empty($ret))
			{
				++$hexieNum;
				continue;
			}
			
			
			$arrRet[] = $str;
			
		}
		fclose($handle);
	}	
	$arrRet = array_unique($arrRet);
	shuffle($arrRet);
	return $arrRet;	
}

$inputPath = $argv[1];

$arrMan = readName($inputPath . '/random_name_man.csv');
$arrWoman = readName($inputPath . '/random_name_woman.csv');

$same = array_intersect($arrMan, $arrWoman);
var_dump($same);

echo "男女名字中相同名字个数：" . count($same) . "\n";

$handle = fopen('insert_random_name.sql', 'w') or die('fail to open file');
fwrite($handle, "set names utf8;\n");

$num = 0;
foreach ($arrMan as $key=>$man)
{
	if (isset($same[$key]))
	{
		continue;
	}
	$sql = "INSERT IGNORE INTO t_random_name  (name,status, gender)  VALUES(\"$man\", 0, 1);\n";
	fwrite($handle, $sql );
	$num ++ ;
	if ($num%50 == 0)
	{
		fwrite($handle, "select sleep(1);\n");
	}
}

foreach ($arrWoman as $woman)
{	
	$sql = "INSERT IGNORE INTO t_random_name  (name,status, gender)  VALUES(\"$woman\", 0, 0);\n";
	fwrite($handle, $sql);
	$num ++ ;
	if ($num%50 == 0)
	{
		fwrite($handle, "select sleep(1);\n");
	}
}
fclose($handle);

echo "有空格的名字有：" . $wsNum . "个\n";
echo "有敏感词的名字有：" . $hexieNum . "个\n";
echo "名字总个数：" . $num . "\n";





/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */