<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TestBossReward.class.php 22346 2012-06-13 09:28:25Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestBossReward.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-06-13 17:28:25 +0800 (三, 2012-06-13) $
 * @version $Revision: 22346 $
 * @brief
 *
 **/

class TestBossReward extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		Util::asyncExecute('boss.reward', array(15, 1339573500,	1339574400, 65973));
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */