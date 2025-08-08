<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestLocker.php 16425 2012-03-14 02:58:27Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestLocker.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:58:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16425 $
 * @brief
 *
 **/

class TestLocker extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$locker1 = new Locker ( "192.168.1.200", 3300 );
		$locker1->lock ( "test1" );
		Logger::debug("test1 locked for locker1");
		$locker1->lock ( "test2" );
		Logger::debug("test2 locked for locker1");
		$locker1->unlock("test1");
		Logger::debug("test1 unlocked for locker1");

		$locker2 = new Locker ( "192.168.1.200", 3300 );
		$locker2->lock ( "test1" );
		Logger::debug("test1 locked for locker2");
		$locker2->lock ( "test2" );
		Logger::debug("test2 locked for locker2");

		$locker2->unlock("test2");
		$locker1->unlock("test1");
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
