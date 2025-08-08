<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedWorldResourceSignupTimer.class.php 21685 2012-05-30 09:31:24Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixedWorldResourceSignupTimer.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-30 17:31:24 +0800 (三, 2012-05-30) $
 * @version $Revision: 21685 $
 * @brief
 *
 **/

class FixedWorldResourceSignupTimer extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$data = new CData();
		$data->noCache();
		$value = array (
			WorldResourceDef::WR_SQL_SIGNUP_END_TIMER => 0
		);
		$where = array ( WorldResourceDef::WR_SQL_RESOURCE_ID, '=', 100001 );
		$return = $data->update(WorldResourceDef::WR_SQL_TABLE)->set($value)->where($where)->query();
		var_dump($return);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */