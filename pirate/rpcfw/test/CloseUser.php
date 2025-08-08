<?php

/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: CloseUser.php 38205 2013-02-06 03:25:38Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/CloseUser.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-06 11:25:38 +0800 (三, 2013-02-06) $
 * @version $Revision: 38205 $
 * @brief
 *
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class CloseUser extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$uid = 0;
		if (isset ( $arrOption [0] ))
		{
			$uid = intval ( $arrOption [0] );
		}
		else
		{
			exit ( "usage: uid \n" );
		}

		$proxy = new ServerProxy ();
		$proxy->closeUser ( $uid );
		sleep ( 1 );

		$this->delConnection ( $uid );
		sleep ( 1 );

		echo "ok\n";
	}

	private function delConnection($uid)
	{

		$proxy = new PHPProxy ( 'lcserver' );
		$proxy->setDummyReturn ( true );
		$proxy->delConnection ( $uid );
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */