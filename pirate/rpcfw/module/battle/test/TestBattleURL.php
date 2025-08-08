<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestBattleURL.php 18249 2012-04-08 09:36:43Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/battle/test/TestBattleURL.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-04-08 17:36:43 +0800 (日, 2012-04-08) $
 * @version $Revision: 18249 $
 * @brief
 *
 **/
class TestBattleURL extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$battle = new Battle ();
		echo $battle->getRecordUrl ( 123 );
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */