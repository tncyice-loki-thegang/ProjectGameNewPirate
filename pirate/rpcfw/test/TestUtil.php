<?php

/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 *
 **************************************************************************/

/**
 * @file $HeadURL$
 * @author $Author$(hoping@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief
 *
 **/

class TestUtil extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		Util::broadcast ( 'test', array () );
		Util::broadcastExecuteRequest ( 'test.myecho', 'string' );
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */