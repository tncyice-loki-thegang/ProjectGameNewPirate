<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TopLimitParser.script.php 32612 2012-12-10 02:38:25Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/TopLimitParser.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-10 10:38:25 +0800 (一, 2012-12-10) $
 * @version $Revision: 32612 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!TOP_LIMIT.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// ID
't_name' => ++$ZERO,							// 名称
'times' => ++$ZERO								// 次数
);


$item = array();
$file = fopen($argv[1].'/top_limit.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$pack = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		$array[$key] = intval($data[$v]);
	}

	$pack[$array['id']] = $array['times'];
}
fclose($file); //var_dump($pack);


$file = fopen($argv[2].'/TOP_LIMIT', 'w');
fwrite($file, serialize($pack));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */