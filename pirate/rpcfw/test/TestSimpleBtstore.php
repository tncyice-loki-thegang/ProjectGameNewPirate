<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestSimpleBtstore.php 36948 2013-01-24 08:12:34Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestSimpleBtstore.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2013-01-24 16:12:34 +0800 (四, 2013-01-24) $
 * @version $Revision: 36948 $
 * @brief
 *
 **/
class TestSimpleBtstore extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$store = btstore_get ();
		$store->CREATEURES;
	}

}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */