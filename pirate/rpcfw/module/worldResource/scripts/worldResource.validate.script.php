<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: worldResource.validate.script.php 18122 2012-04-07 02:49:24Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldResource/scripts/worldResource.validate.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-04-07 10:49:24 +0800 (六, 2012-04-07) $
 * @version $Revision: 18122 $
 * @brief
 *
 **/

require_once dirname ( dirname (  dirname ( dirname ( __FILE__ ) ) ) ) . "/def/WorldResource.def.php";

$worldResources = btstore_get()->WORLDRESOURCE->toArray();

foreach ( $worldResources as $world_resource_id => $info )
{
	//validate world resource army ids
	if ( empty($info[WorldResourceDef::WR_ARMY_IDS]) )
	{
		echo "WORLDRESOURCE $world_resource_id team ids is empty!\n";
	}
	else
	{
		foreach ( $info[WorldResourceDef::WR_ARMY_IDS] as $team_id )
		{
			if ( !isset(btstore_get()->TEAM[$team_id]) )
			{
				echo "WORLDRESOURCE $world_resource_id team id:$team_id is invalid!\n";
			}
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */