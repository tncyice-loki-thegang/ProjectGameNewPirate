<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestTeam.php 27286 2012-09-19 03:13:37Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestTeam.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-09-19 11:13:37 +0800 (三, 2012-09-19) $
 * @version $Revision: 27286 $
 * @brief
 *
 **/
class TestTeam extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$pid = 1344803;
		$uid = 21713;
		$roomId = 100020;
		$teamId = 20230;
		$proxy = new RPCProxy ( '192.168.1.200', 7777, true );
		$proxy->setClass ( 'user' );
		$arrRet = $proxy->login ( $pid );
		var_dump ( $arrRet );
		$arrRet = $proxy->userLogin ( $uid );
		var_dump ( $arrRet );
		$proxy->setClass ( 'team' );
		$arrRet = $proxy->enter ( $roomId );
		var_dump ( $arrRet );
		$proxy->setClass ( 'copy' );
		$arrRet = $proxy->joinTeam ( $roomId, $teamId );
		var_dump ( $arrRet );
		$arrRet = $proxy->createTeam ( $roomId, true, 1 );
		var_dump ( $arrRet );
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */