<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: initPort.scripts.php 31653 2012-11-22 09:58:43Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/scripts/initPort.scripts.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-11-22 17:58:43 +0800 (四, 2012-11-22) $
 * @version $Revision: 31653 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Port.def.php";

if ( $argc < 2 )
{
	echo "Please input enough arguments:!PORT initPort.sql output\n";
	exit;
}

$data = file_get_contents($argv[1]);
if ( $data == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$ports = unserialize($data);

$sql = "";
foreach ($ports as $port )
{
	$port_id = $port[PortDef::PORT_ID];

	$sql .= "INSERT IGNORE INTO `" . PortDef::PORT_SQL_TABLE . "` (`" . PortDef::PORT_SQL_PORT_ID . "`, `" .
		PortDef::PORT_SQL_GUILD_ID . "`) values ($port_id, 0);\n";
}

$file = fopen($argv[2], 'w');
fwrite($file, $sql);
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */