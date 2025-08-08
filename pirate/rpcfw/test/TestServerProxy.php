<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestServerProxy.php 31523 2012-11-21 09:08:38Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestServerProxy.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-11-21 17:08:38 +0800 (三, 2012-11-21) $
 * @version $Revision: 31523 $
 * @brief
 *
 **/

class TestServerProxy extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$proxy = new ServerProxy ();
		$proxy->init ( 'game06', Util::genLogId () );
		$arrRet = $proxy->getUserByPid ( 20, array ('uid' ) );
		var_dump ( $arrRet );
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */