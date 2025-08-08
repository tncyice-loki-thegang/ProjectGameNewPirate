<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: port.validate.script.php 18082 2012-04-06 08:10:55Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/scripts/port.validate.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-04-06 16:10:55 +0800 (五, 2012-04-06) $
 * @version $Revision: 18082 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Port.def.php";
require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/conf/User.cfg.php";

$ports = btstore_get()->PORT->toArray();

foreach ( $ports as $port_id => $value )
{
	//validate town id
	if ( empty(btstore_get()->TOWN[$value[PortDef::PORT_TOWN_ID]]) )
	{
		echo "PORT:$port_id owned town id:" . $value[PortDef::PORT_TOWN_ID] . " is invalied\n";
	}

	//validate port type
	if ( empty($value[PortDef::PORT_TYPE]) || !in_array($value[PortDef::PORT_TYPE],
		 array_merge(array_keys(GroupConf::$GROUP), array(PortDef::PORT_TYPE_BASE,
		  PortDEF::PORT_TYPE_IN_FIGHT, PortDef::PORT_TYPE_NEUTRAL)) ) )
	{
		echo "PORT:$port_id type:" . $value[PortDef::PORT_TYPE] . " is invalied\n";
	}

	//validate port resource group
	if ( !empty($value[PortDef::PORT_RESOURCE_LIST]) )
	{
		foreach ( $value[PortDef::PORT_RESOURCE_LIST] as $resource_id )
		{
			if ( !isset(btstore_get()->PORTRESOURCE[$resource_id]) )
			{
				echo "PORT:$port_id resource id" . $resource_id . " is invalid\n";
			}
		}
	}

	//validate port attr
	if ( !empty($value[PortDef::PORT_ATTRS]) )
	{
		foreach ( $value[PortDef::PORT_ATTRS] as $attr_id => $attr_value )
		{
			if ( !in_array($attr_id, array_keys(PortDef::$PORT_ATTRS_DEFAULT)) )
			{
				echo "PORT:$port_id attr id" . $attr_id . " is invalid\n";
			}
		}
	}

	//validate port modulus
	if ( empty($value[PortDef::PORT_MODULUS]) )
	{
		echo "PORT:$port_id port modulus" . $value[PortDef::PORT_MODULUS] . " is invalid\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */