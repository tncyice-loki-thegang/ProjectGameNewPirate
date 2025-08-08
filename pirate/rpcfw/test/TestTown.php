<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestTown.php 16425 2012-03-14 02:58:27Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestTown.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:58:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16425 $
 * @brief
 *
 **/

require_once(LIB_ROOT . '/RPCProxy.class.php');

class TestTown extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		
		Logger::debug("%s", $arrOption);
		$arrOption = self::getOption ( $arrOption, 'u:' );
		$uid = intval ( $arrOption ['u'] );
		$proxy = new RPCProxy ( '192.168.1.228', 7777, true );
		$proxy->setRequestType ( RequestType::DEBUG );
		$proxy->setClass ( 'user' );
		$ret = $proxy->login ( $uid );
		if ($ret != 'ok')
		{
			echo "login failed\n";
			return;
		}

		$ret = $proxy->userLogin($uid);
		if($ret != 'ok')
		{
			echo "userLogin failed\n";
			return;
		}

		$proxy->setPublic(true);

		$ret = $proxy->enterTown ( 14, 1, 1 );
		if ($ret != 'ok')
		{
			echo "enter failed\n";
			return;
		}

		$proxy->setClass('town');
		$x = 1;
		$y = 10;
		$add = true;
		while(true)
		{
			$ret = $proxy->getReturnData();
			var_dump($ret);
			usleep(300000);
			if($add)
			{
				$x++;
				if($x >= 83)
				{
					$x = 82;
					$add = false;
				}
			}
			else 
			{
				$x--;
				if($x < 1)
				{
					$x = 1;
					$add = true;
				}
			}
			echo "move to $x, $y\n";
			$ret = $proxy->move (0, array( ($x << 16) | $y) );
			var_dump($ret);
		}

		$proxy->setClass ( 'user' );
		$ret = $proxy->leaveTown ();
		if ($ret != 'ok')
		{
			echo "leave failed\n";
			return;
		}


		echo "done\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
