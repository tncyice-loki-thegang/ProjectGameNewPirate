<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IpParser.scripts.php 23014 2012-06-30 02:53:04Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/IpParser.scripts.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-06-30 10:53:04 +0800 (六, 2012-06-30) $
 * @version $Revision: 23014 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!ip.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'ip_s' => $ZERO,								// 开始ip
'ip_e' => ++$ZERO,								// 截止ip
'area_1' => ++$ZERO,							// 所在地_1
'area_2' => ++$ZERO								// 所在地_2
);


$inFile = $argv[1].'/ip.csv';
$outFile = $argv[1].'/ip_tmp.csv';
$cmd = "iconv -c -f GB2312 -t utf-8 ".$inFile." > ".$outFile;
exec($cmd);


$item = array();
$file = fopen($argv[1].'/ip_tmp.csv', 'r');



$ipList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	// 这个是中间存储用的临时数组
	$array = array();
	foreach ( $name as $key => $v )
	{
		// 普通数组
		if ($key == 'ip_s' || $key == 'ip_e')
		{
			$array[$key] = ip2long($data[$v]);
		}
		else 
		{
			if (!empty($data[$v]))
				$array[$key] = $data[$v];
		}
	}

	// 最终解析数组, 不需要再次进行操作的，就直接赋值
	$ipList[$array['ip_s']] = $array;
}


fclose($file); //var_dump($ipList);

echo 'ok';

$file = fopen($argv[2].'/IP', 'w');
fwrite($file, serialize($ipList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */