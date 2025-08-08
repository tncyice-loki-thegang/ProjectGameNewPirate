<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedMem.class.php 19829 2012-05-05 08:17:27Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-46/test/FixedMem.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-05 16:17:27 +0800 (Sat, 05 May 2012) $
 * @version $Revision: 19829 $
 * @brief
 *
 **/

class BossReward extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		Util::asyncExecute('boss.reward', array(16, 1336741200, 1336742100, 25991));
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
