<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedWorldResource.class.php 19904 2012-05-07 12:15:25Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixedWorldResource.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-07 20:15:25 +0800 (一, 2012-05-07) $
 * @version $Revision: 19904 $
 * @brief
 *
 **/

class FixedWorldResource extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$data = new CData();
		$data->noCache();
		$values = array(
			WorldResourceDef::WR_SQL_GUILD_ID => 0,
			WorldResourceDef::WR_SQL_CUR_GUILD_ID => 0,
			WorldResourceDef::WR_SQL_BATTLE_END_TIMER => 0,
		);
		$where = array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', 100001);
		$return = $data->update(WorldResourceDef::WR_SQL_TABLE)->set($values)->where($where)->query();
		echo "fixed worldresource end!\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */