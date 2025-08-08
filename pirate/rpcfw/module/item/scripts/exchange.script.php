<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: exchange.script.php 23295 2012-07-05 06:59:45Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/exchange.script.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2012-07-05 14:59:45 +0800 (四, 2012-07-05) $
 * @version $Revision: 23295 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Exchange.def.php";

if ( $argc < 2 )
{
	echo "Please input enough arguments:! exchange.csv output\n";
	exit;
}

$name = array (
ExchangeDef::EXCHANGE_ID								=>			0,
ExchangeDef::EXCHANGE_ITEM_ID							=>			1,
ExchangeDef::EXCHANGE_REQ_EXPERIENCE					=>			2,
ExchangeDef::EXCHANGE_VALUE								=>			3,
ExchangeDef::EXCHANGE_DROP_LIST							=>			4,
ExchangeDef::EXCHANGE_ARGS								=>			9,
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

	$start = $name[ExchangeDef::EXCHANGE_DROP_LIST];
	$exchange[ExchangeDef::EXCHANGE_DROP_LIST] = array();
	for ( $i = $start; $i < $start + 5; $i++ )
	{
		$exchange[ExchangeDef::EXCHANGE_DROP_LIST][] = intval($data[$i]);
	}

	$start = $name[ExchangeDef::EXCHANGE_ARGS];
	$exchange[ExchangeDef::EXCHANGE_ARGS] = array();
	for ( $i = $start; $i < $start + 3; $i++ )
	{
		$exchange[ExchangeDef::EXCHANGE_ARGS][] = intval($data[$i]);
	}

	$exchanges[$exchange[ExchangeDef::EXCHANGE_ID]] = $exchange;
}

$file = fopen($argv[2], 'w');
fwrite($file, serialize($exchanges));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */