<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: arm_exchange.script.php 28708 2012-10-11 08:02:17Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/arm_exchange.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-10-11 16:02:17 +0800 (四, 2012-10-11) $
 * @version $Revision: 28708 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Exchange.def.php";

if ( $argc < 2 )
{
	echo "Please input enough arguments:! gem_exchange.csv output\n";
	exit;
}

$name = array (
ExchangeDef::EXCHANGE_ID								=>			0,
ExchangeDef::EXCHANGE_ITEM_ID							=>			1,
ExchangeDef::EXCHANGE_TARGET_ITEM_ID					=>			2,
ExchangeDef::EXCHANGE_REQ_ITEMS							=>			3,
ExchangeDef::EXCHANGE_REQ_BELLY							=>			4,
ExchangeDef::EXCHANGE_REQ_EXPERIENCE					=>			5,
ExchangeDef::EXCHANGE_REQ_PURPLE_SOUL					=>			6,
);

$exchanges = array();
$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	//如果EXCHANGE ID是string,则忽略,主要针对表头
	if ( !is_numeric($data[$name[ExchangeDef::EXCHANGE_ID]])
		&& is_string($data[$name[ExchangeDef::EXCHANGE_ID]]) )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}

	$exchange = array();
	foreach ( $name as $key => $v )
	{
		$exchange[$key] = $data[$v];
		//如果是数字,则intval
		if ( is_numeric($exchange[$key]) || empty($exchange[$key]) )
			$exchange[$key] = intval($exchange[$key]);
	}

	$exchange_req_items = array();
	if ( !empty($data[$name[ExchangeDef::EXCHANGE_REQ_ITEMS]]) )
	{
		$array = explode(',', $data[$name[ExchangeDef::EXCHANGE_REQ_ITEMS]]);
		foreach ( $array as $value )
		{
			if ( !empty($value) )
			{
				$value = explode('|', $value);
				$item_id = intval($value[0]);
				$item_num = intval($value[1]);
				$exchange_req_items[$item_id] = $item_num;
			}
		}
	}
	$exchange[ExchangeDef::EXCHANGE_REQ_ITEMS] = $exchange_req_items;

	$exchanges[$exchange[ExchangeDef::EXCHANGE_ID]] = $exchange;
}

$file = fopen($argv[2], 'w');
fwrite($file, serialize($exchanges));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */