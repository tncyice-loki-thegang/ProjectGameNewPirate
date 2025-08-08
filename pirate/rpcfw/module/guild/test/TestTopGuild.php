<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestTopGuild.php 22173 2012-06-11 11:31:38Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/guild/test/TestTopGuild.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-06-11 19:31:38 +0800 (一, 2012-06-11) $
 * @version $Revision: 22173 $
 * @brief
 *
 **/
class TestTopGuild extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$arrRet = EnGuild::getTopGuild ( 10 );
		var_dump ( $arrRet );
	}

}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */