<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldResourceReward.php 16420 2012-03-14 02:53:05Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/WorldResourceReward.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:53:05 +0800 (三, 2012-03-14) $
 * @version $Revision: 16420 $
 * @brief
 *
 **/

require MOD_ROOT . '/worldResource/index.php';

/**
 *
 * 产生奖励
 *
 */
class WorldResourceReward extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$resources = btstore_get()->WORLDRESOURCE;
		foreach ( $resources as $resource_id => $value )
		{
			$world_resource_obj = new WorldResource();
			$world_resource_obj->reward($resource_id);
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */