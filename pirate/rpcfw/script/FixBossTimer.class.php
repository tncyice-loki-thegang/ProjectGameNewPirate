<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixBossTimer.class.php 20279 2012-05-12 03:49:48Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/FixBossTimer.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-12 11:49:48 +0800 (六, 2012-05-12) $
 * @version $Revision: 20279 $
 * @brief
 *
 **/

class FixBossTimer extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$data = new CData();
		$values = array();
		if ( GameConf::BOSS_OFFSET > 0 )
		{
			$values = array(
				'execute_time' => new IncOperator(GameConf::BOSS_OFFSET),
			);
		}
		else
		{
			$values = array(
				'execute_time' => new DecOperator(abs(GameConf::BOSS_OFFSET)),
			);
		}
		$where = array ('tid', '=', $arrOption[0] );
		$return = $data->update('t_timer')->set($values)->where($where)->query();
		var_dump($return);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */