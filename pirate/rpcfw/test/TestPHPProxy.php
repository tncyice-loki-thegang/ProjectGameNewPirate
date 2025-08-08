<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestPHPProxy.php 37972 2013-02-04 05:14:45Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestPHPProxy.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2013-02-04 13:14:45 +0800 (一, 2013-02-04) $
 * @version $Revision: 37972 $
 * @brief
 *
 **/

class TestPHPProxy extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$proxy = new PHPProxy ( 'module' );
		$arrInfo = $proxy->getModuleInfo ( 'lcserver', 'game00' );
		var_dump ( $arrInfo );
		$arrInfo = $proxy->getZkInfo ( '/pirate/lcserver/lcserver#game00' );
		var_dump ( $arrInfo );
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */