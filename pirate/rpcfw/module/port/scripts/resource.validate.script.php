<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: resource.validate.script.php 18048 2012-04-06 03:05:16Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/scripts/resource.validate.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-04-06 11:05:16 +0800 (五, 2012-04-06) $
 * @version $Revision: 18048 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Port.def.php";

$resources = btstore_get()->PORTRESOURCE->toArray();

foreach ( $resources as $resource_group_id => $resource_info )
{
	foreach ( $resource_info[PortDef::PORT_RESOURCE_LIST] as $resource_id => $resource_info )
	{
		if ( empty($resource_info[PortDef::PORT_RESOURCE_TIME]) )
		{
			echo "PORT RESOURCE:group id:$resource_group_id resource id:$resource_id " .
			"resource output time " . $resource_info[PortDef::PORT_RESOURCE_TIME] . " is invalid\n";
		}

		if ( empty($resource_info[PortDef::PORT_RESOURCE_OUTPUT]) )
		{
			echo "PORT RESOURCE:group id:$resource_group_id resource id:$resource_id " .
			"resource output " . $resource_info[PortDef::PORT_RESOURCE_OUTPUT] . " is invalid\n";
		}

		if ( empty($resource_info[PortDef::PORT_RESOURCE_ARMY]) || !isset(
			btstore_get()->ARMY[$resource_info[PortDef::PORT_RESOURCE_ARMY]]) )
		{
			echo "PORT RESOURCE:group id:$resource_group_id resource id:$resource_id " .
			"army " . $resource_info[PortDef::PORT_RESOURCE_ARMY] . " is invalid\n";
		}

		if ( $resource_info[PortDef::PORT_RESOURCE_TIME] <
			$resource_info[PortDef::PORT_RESOURCE_PROTECTED_TIME] )
		{
			echo "PORT RESOURCE:group id:$resource_group_id resource id:$resource_id " .
			"resource output time  " . $resource_info[PortDef::PORT_RESOURCE_TIME] .
			" < resource protected time " . $resource_info[PortDef::PORT_RESOURCE_PROTECTED_TIME]
			. " is invalid\n";
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */