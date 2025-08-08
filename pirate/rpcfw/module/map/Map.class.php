<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Map.class.php 16431 2012-03-14 03:05:55Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/map/Map.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 11:05:55 +0800 (三, 2012-03-14) $
 * @version $Revision: 16431 $
 * @brief
 *
 **/



class Map implements IMap
{
	/* (non-PHPdoc)
	 * @see IMap::mapInfo()
	 */
	public function mapInfo() {

		//检查功能是否开启
		if ( EnSwitch::isOpen(SwitchDef::WORLD_MAP) == FALSE )
		{
			Logger::FATAL('map info is not open!');
			throw new Exception('fake');
		}

		//PORT
		$port = new Port();
		$port_id = $port->getPort();

		$move_cd = $port->getMoveCD();

		//ENTER TOWN LIST
		$city = new City();
		$enter_town_list = $city->getEnterTownList();

		//WORLD RESOURCE
		$worldresource = new WorldResource();
		$resource_infos = $worldresource->worldResourceInfos();

		return array (
			'port_id' => $port_id,
			'move_cd' => $move_cd,
			'enter_town_list' => $enter_town_list,
			'world_resource_infos' => $resource_infos,
			'port_infos' => array(),
		);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */