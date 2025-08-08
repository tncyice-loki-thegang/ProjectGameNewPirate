<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IMap.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/map/IMap.class.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

interface IMap
{
	/**
	 *
	 * 得到地图信息
	 *
	 * return array
	 * <code>
	 * {
	 * 		'move_cd':int
	 * 		'port_id':int
	 * 		'enter_town_list':array
	 * 		[
	 *			town_id:int
	 * 		]
	 * 		'world_resource_infos':array
	 * 		[
	 * 			world_resource_id:array
	 * 			{
	 * 				'guild_id':int
	 * 				'guild_name':string
	 * 				'guild_emblem':int
	 * 				'guild_level':int
	 * 			}
	 * 		]
	 * 		'port_infos':array
	 * 		[
	 * 			port_id:
	 * 			{
	 * 			}
	 * 		]
	 * }
	 * </code>
	 */
	public function mapInfo();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */