<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: initPortResource.script.php 31653 2012-11-22 09:58:43Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/scripts/initPortResource.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-11-22 17:58:43 +0800 (四, 2012-11-22) $
 * @version $Revision: 31653 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Port.def.php";

if ( $argc < 4 )
{
	echo "Please input enough arguments:!PORT PORTRESOURCE initPortResource.sql output\n";
	exit;
}

$data = file_get_contents($argv[1]);
if ( $data == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$ports = unserialize($data);

$data = file_get_contents($argv[2]);
if ( $data == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$portresources = unserialize($data);

$sql = "";

$count = 0;
$list_count = 50;

foreach ($ports as $port )
{
	$port_id = $port[PortDef::PORT_ID];
	$port_resource_groups = $port[PortDef::PORT_RESOURCE_GROUPS];

	foreach ( $port_resource_groups as $page_id => $port_resource_group_id )
	{
		foreach ( $portresources[$port_resource_group_id][PortDef::PORT_RESOURCE_LIST] as $resource_id => $value )
		{
			$sql .= "INSERT IGNORE INTO `" . PortDef::PORT_SQL_RESOURCE_TABLE .
				"` (`" . PortDef::PORT_SQL_PORT_ID . "`, `" . PortDef::PORT_SQL_PAGE_ID .
				"`, `" . PortDef::PORT_SQL_RESOURCE_ID . "`, `" . PortDeF::PORT_SQL_UID .
				"`, `" . portDef::PORT_SQL_OCCUPY_TIME . "`, `" . PortDef::PORT_SQL_DUE_TIMER .
				"`, `" . portDef::PORT_SQL_IS_EXCAVATE . "`, `" . PortDef::PORT_SQL_PLUNDER_PROTECTED_TIME .
				"`, `" . portDef::PORT_SQL_PLUNDER_TIME ."`, `" . PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE .
				"`) values ($port_id, $page_id, $resource_id, 0, 0, 0, 0, 0, 0,0);\n";
			$count++;
			if ( $count % $list_count == 0 )
			{
				$sql .= "SELECT SLEEP(1);\n";
			}
		}
	}
}

$file = fopen($argv[3], 'w');
fwrite($file, $sql);
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */