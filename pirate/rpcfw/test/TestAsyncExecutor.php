<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestAsyncExecutor.php 16425 2012-03-14 02:58:27Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestAsyncExecutor.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:58:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16425 $
 * @brief
 *
 **/

class TestAsyncExecutor extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		Util::asyncExecute ( 'test', '/home/pirate/test.php', array(1000000) );
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */