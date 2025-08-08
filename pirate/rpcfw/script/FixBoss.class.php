<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixBoss.class.php 20283 2012-05-12 06:14:16Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/FixBoss.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-12 14:14:16 +0800 (六, 2012-05-12) $
 * @version $Revision: 20283 $
 * @brief
 *
 **/

class FixBoss extends BaseScript
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
				'start_time' => new IncOperator(GameConf::BOSS_OFFSET),
			);
		}
		else
		{
			$values = array(
				'start_time' => new DecOperator(abs(GameConf::BOSS_OFFSET)),
			);
		}
		$where = array ('boss_id', '=', $arrOption[0] );
		$return = $data->update('t_boss')->set($values)->where($where)->query();
		var_dump($return);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */