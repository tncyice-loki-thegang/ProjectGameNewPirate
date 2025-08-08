<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TreasureHuntTest.php 18656 2012-04-14 05:57:23Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL:
 * svn://192.168.1.80:3698/C/branches/pirate/rpcfw/rpcfw_explore/test/TreasureHuntTest.php
 * $
 * 
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 *         @date $Date: 2012-04-14 13:57:23 +0800 (Sat, 14 Apr 2012) $
 * @version $Revision: 18656 $
 *          @brief
 *         
 *         
 */
class TestExplore extends BaseScript
{
	private $host = '192.168.1.206';
	private $port = 7777;
	
	/*
	 * (non-PHPdoc) @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		if (count($arrOption) < 4)
		{
			exit("usage: pid belly quality num\n");
		}
			
		$pid = $arrOption[0];
		$belly = $arrOption[1];
		$res = $arrOption[2];		
		$num = $arrOption[3];
		
		$proxy = new RPCProxy($this->host, $this->port, 'true');
		$proxy->setClass('user');
		$ret = $proxy->login($pid);
		if ($ret != 'ok')
		{
			Logger::fatal("player:%d login failed", $pid);
			break;
		}
		
		$arrUser = $proxy->getUsers();
		$uid = $arrUser[0]['uid'];
		$lastTown = $arrUser[0]['last_town_id'];
		$last_x = $arrUser[0]['last_x'];
		$last_y = $arrUser[0]['last_y'];
		
		$ret = $proxy->userLogin($uid);
		if ($ret != 'ok')
		{
			Logger::fatal("user:%d login failed", $uid);
			break;
		}
		
		$proxy->setClass('city');
		$proxy->enterTown($lastTown, $last_x, $last_y);
		
		$proxy->setClass('explore');
		
		for($i=0; $i<$num; ++$i)
		{
			$arrRet = $proxy->quickExplore(1004, $belly, $res);
			$proxy->moveToBag(1004);
			echo "num: $i\n";
		}		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */