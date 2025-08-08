<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: initWorldResource.scripts.php 31651 2012-11-22 09:27:55Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldResource/scripts/initWorldResource.scripts.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-11-22 17:27:55 +0800 (四, 2012-11-22) $
 * @version $Revision: 31651 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/WorldResource.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!WORLDRESOURCE init_world_resource.sql output\n";
	exit;
}

$data = file_get_contents($argv[1]);
if ( $data == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$world_resources = unserialize($data);

$sql = "";
foreach ($world_resources as $world_resource )
{
	$world_resource_id = $world_resource[WorldResourceDef::WR_ID];

	$sql .= "INSERT IGNORE INTO `" . WorldResourceDef::WR_SQL_TABLE . "` (`" . WorldResourceDef::WR_SQL_RESOURCE_ID . "`, `" .
		WorldResourceDef::WR_SQL_GUILD_ID . "`, `" .
		WorldResourceDef::WR_SQL_CUR_GUILD_ID . "`, `" .
		WorldResourceDef::WR_SQL_SIGNUP_END_TIMER . "`, `" .
		WorldResourceDef::WR_SQL_BATTLE_END_TIMER . "`) values ($world_resource_id, 0, 0, 0 , 0);\n";
}

$file = fopen($argv[2], 'w');
fwrite($file, $sql);
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */