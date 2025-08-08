<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: gem_exchange.script.php 26659 2012-09-05 02:23:47Z YangLiu $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/gem_exchange.script.php $
 * @author $Author: YangLiu $(jhd@babeltime.com)
 * @date $Date: 2012-09-05 10:23:47 +0800 (三, 2012-09-05) $
 * @version $Revision: 26659 $
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
ExchangeDef::EXCHANGE_GEM_ITEM_ID						=>			0,
ExchangeDef::EXCHANGE_REQ_GEM_ITEM_QUALITY				=>			1,
ExchangeDef::EXCHANGE_REQ_GEM_ITEM_NUM					=>			2,
ExchangeDef::EXCHANGE_REQ_GEM_ITEM_NEED_POINT			=>			3,
ExchangeDef::EXCHANGE_REQ_GEM_ITEM_NEED_GEM_ID			=>			4,
ExchangeDef::EXCHANGE_REQ_GEM_ITEM_EXCHANGE_LEVEL		=>			5,
ExchangeDef::EXCHANGE_REQ_GEM_ITEM_NEED_GEM_LEVEL		=>			6,
ExchangeDef::EXCHANGE_REQ_GEM_ITEM_NEED_ESSENCE			=>			7,
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
	if ( !is_numeric($data[$name[ExchangeDef::EXCHANGE_GEM_ITEM_ID]])
		&& is_string($data[$name[ExchangeDef::EXCHANGE_GEM_ITEM_ID]]) )
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

	$exchanges[$exchange[ExchangeDef::EXCHANGE_GEM_ITEM_ID]] = $exchange;
}

$file = fopen($argv[2], 'w');
fwrite($file, serialize($exchanges));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */