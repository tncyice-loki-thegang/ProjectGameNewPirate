<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TreasureHuntTest.php 18656 2012-04-14 05:57:23Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TreasureHuntTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-04-14 13:57:23 +0800 (六, 2012-04-14) $
 * @version $Revision: 18656 $
 * @brief 
 * 
 **/

class TreasureHuntTest extends BaseScript
{
	private $startPid = 15000;
	private $playerCount = 300;
	
	private $host = '192.168.1.206';

	private $port = 7777;
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		for($pid = $this->startPid; $pid < $this->startPid + $this->playerCount; $pid++)
		{
			try
			{
				$proxy = new RPCProxy($this->host, $this->port, 'true');
				$proxy->setClass('user');
				$ret = $proxy->login($pid);
				if ($ret != 'ok')
				{
					Logger::fatal("player:%d login failed", $pid);
					break;
				}
				
				$arrUser = $proxy->getUsers();
				if (empty($arrUser))
				{
					
					$uname = sprintf('u%d', $pid % $this->startPid);
					$ret = $proxy->createUser(1, $uname);
					if ($ret != 'ok')
					{
						Logger::fatal("player:%d create user:%s failed", $pid, $uname);
						continue;
					
		//break;
					}
					$arrUser = $proxy->getUsers();
				}
				
				$uid = $arrUser[0]['uid'];
				$ret = $proxy->userLogin($uid);
				if ($ret != 'ok')
				{
					Logger::fatal("user:%d login failed", $uid);
					break;
				}
				
				$proxy->setClass('treasure');
				$arrRet = $proxy->hunt(1,0);
			
			}
			catch ( Exception $e )
			{
				Logger::warning($e->getTraceAsString());
			}
		}
	
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */