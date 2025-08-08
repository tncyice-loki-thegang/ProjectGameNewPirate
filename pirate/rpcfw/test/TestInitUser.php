<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestInitUser.php 18472 2012-04-10 16:34:13Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestInitUser.php $
 * @author $Author: HongyuLan $(hoping@babeltime.com)
 * @date $Date: 2012-04-11 00:34:13 +0800 (三, 2012-04-11) $
 * @version $Revision: 18472 $
 * @brief
 *
 **/

/**
 *  警告
 *  使用前检查一下代码
 */


require_once (LIB_ROOT . '/RPCProxy.class.php');

class TestInitUser extends BaseScript
{

	private $host = '192.168.3.23';

	private $port = 7777;

	private $startPid = 15000;

	private $playerCount = 999;

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		for($pid = $this->startPid; $pid < $this->startPid + $this->playerCount; $pid ++)
		{
			try
			{
				$proxy = new RPCProxy ( $this->host, $this->port, 'true' );
				$proxy->setClass ( 'user' );
				$ret = $proxy->login ( $pid );
				if ($ret != 'ok')
				{
					Logger::fatal ( "player:%d login failed", $pid );
					break;
				}

				$arrUser = $proxy->getUsers ();
				if (empty ( $arrUser ))
				{

					$uname = sprintf ( 'u%d', $pid % $this->startPid );
					$ret = $proxy->createUser ( 1, $uname );
					if ($ret != 'ok')
					{
						Logger::fatal ( "player:%d create user:%s failed", $pid, $uname );
						continue;
						//break;
					}
					$arrUser = $proxy->getUsers ();
				}

			/*
			$uid = $arrUser [0] ['uid'];
			$ret = $proxy->userLogin ( $uid );
			if ($ret != 'ok')
			{
				Logger::fatal ( "user:%d login failed", $uid );
				break;
			}

			$proxy->setClass ( 'hero' );
			$arrRet = $proxy->getRecruitHeroes ();

			$proxy->setClass ( 'formation' );
			$proxy->changeCurFormation ( 10001,
					array ($arrRet [0] ['hid'], $arrRet [1] ['hid'], 0, 0, 0, 0, 0, 0, 0 ) );

			$proxy->setClass ( 'city' );
			$proxy->enterTown ( 1, 19, 17 );

			$proxy->setClass ( 'task' );
			$arrRet = $proxy->accept ( 16 );

			$arrRet = $proxy->complete ( 16 );

			$proxy->complete ( 26 );

			$proxy->accept ( 17 );

			$proxy->setClass ( 'copy' );
			$proxy->attack ( 2, 16 );
			Logger::debug("player:%d created", $pid);
			 */
			}
			catch ( Exception $e )
			{
				Logger::warning ( $e->getTraceAsString () );
			}
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
