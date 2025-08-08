<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestGameProxy.php 20701 2012-05-18 08:13:41Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestGameProxy.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-05-18 16:13:41 +0800 (五, 2012-05-18) $
 * @version $Revision: 20701 $
 * @brief
 *
 **/
class TestGameProxy extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$proxy = new GameProxy ( 'http://192.168.1.200:8080/execute' );
		$proxy->init ( 'game00', 123434 );
		$userCount = $proxy->getTotalUserCount ();
		echo sprintf ( "total user count:%d\n", $userCount );

		$data = $proxy->getBattleRecord ( 52861 );
		echo sprintf ( "data:%s\n", $data );

		$proxy->closeUser ( 59805 );
		$proxy->addGold ( 59805, 123434, 10, 10 );
		$proxy->notifyNewGmResponse ( 59805 );
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */