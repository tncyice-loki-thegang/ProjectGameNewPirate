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


class TestBT extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$res = btstore_get()->ACHIEVE[240001];
		var_dump($res);
	}

}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
