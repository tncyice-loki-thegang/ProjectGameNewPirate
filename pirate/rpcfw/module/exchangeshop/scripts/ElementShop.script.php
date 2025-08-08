<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: honourShop.script.php 32847 2012-12-11 08:11:06Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/honourshop/scripts/honourShop.script.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-11 16:11:06 +0800 (二, 2012-12-11) $
 * @version $Revision: 32847 $
 * @brief 
 *  
 **/

//require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/AllBlue.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!market.csv output\n";
	exit;
}
$ZERO = 0;
//数据对应表
$name = array (
	'id' => $ZERO,
	'itemTempId' => ++$ZERO,
	'getNum' => ++$ZERO,
	'needScore' => ++$ZERO,
);

$file = fopen($argv[1].'/element_exchange.csv', 'r');

$data = fgetcsv($file);

$honourShop = array();
while (TRUE)
{
	$data = fgetcsv($file);
	if (empty($data))
		break;
	$id = intval($data[0]);
	$array = array();
	foreach ( $name as $key => $v )
	{
		if($key == 'id')
		{
			continue;
		}
		$array[$key] = intval($data[$v]);
	}

	$honourShop[$id] = $array;
}
fclose($file); //var_dump($salary);
print_r($honourShop);

$file = fopen($argv[2].'/ELEMENT_EXCHANGE', 'w');
fwrite($file, serialize($honourShop));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */