<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestDataProxy.php 16425 2012-03-14 02:58:27Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestDataProxy.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:58:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16425 $
 * @brief
 *
 **/

require_once (LIB_ROOT . '/data/index.php');

class TestDataProxy extends BaseScript
{

	private function testInsertInto()
	{

		$proxy = new PHPProxy ( 'data' );
		$uid = time ();
		$arrData = array ("uid" => $uid, "time" => time (), "command" => "insertInto",
				"table" => "t_user",
				"values" => array ("uid" => array ('=', $uid ),
						"uname" => array ('=', "HopingWhite" . $uid ),
						"rename_count" => array ('=', 1 ), "cur_blood_package" => array ('=', 0 ),
						'max_blood_package' => array ('=', 100 ), 'capability' => array ('=', 0 ) ) );
		$arrRet = $proxy->query ( $arrData );
		var_dump ( $arrRet );
		return $arrRet;
	}

	private function testInsertOrUpdate()
	{

		$proxy = new PHPProxy ( 'data' );
		$uid = 123;
		$arrData = array ("uid" => $uid, "time" => time (), "command" => "insertOrUpdate",
				"table" => "t_user",
				"values" => array ("uid" => array ('=', $uid ),
						"uname" => array ('=', "HopingWhite" . $uid ),
						"rename_count" => array ('=', 1 ), "cur_blood_package" => array ('=', 0 ),
						'max_blood_package' => array ('=', 100 ), 'capability' => array ('=', 0 ) ) );
		$arrRet = $proxy->query ( $arrData );
		var_dump ( $arrRet );
		return $arrRet;
	}

	private function testInsertIgnore()
	{

		$proxy = new PHPProxy ( 'data' );
		$uid = 123;
		$arrData = array ("uid" => $uid, "time" => time (), "command" => "insertIgnore",
				"table" => "t_user",
				"values" => array ("uid" => array ('=', $uid ),
						"uname" => array ('=', "HopingWhite" . $uid ),
						"rename_count" => array ('=', 1 ), "cur_blood_package" => array ('=', 0 ),
						'max_blood_package' => array ('=', 100 ), 'capability' => array ('=', 0 ) ) );
		$arrRet = $proxy->query ( $arrData );
		var_dump ( $arrRet );
		return $arrRet;
	}

	private function testSelect()
	{

		$proxy = new PHPProxy ( 'data' );
		$arrData = array ("uid" => 123, "time" => time (), "command" => "select",
				"table" => "t_user", "where" => array ('uid' => array ('=', 123 ) ),
				'orderBy' => 'uid', 'orderDir' => 'ASC', 'offset' => 0, 'limit' => 100 );
		$arrRet = $proxy->query ( $arrData );
		var_dump ( $arrRet );
		return $arrRet;
	}

	private function testSelectCount()
	{

		$proxy = new PHPProxy ( 'data' );
		$arrData = array ("uid" => 123, "time" => time (), "command" => "selectCount",
				"table" => "t_user", "where" => array ('uid' => array ('=', 123 ) ),
				'orderBy' => 'uid', 'orderDir' => 'ASC', 'offset' => 0, 'limit' => 100 );
		$arrRet = $proxy->query ( $arrData );
		var_dump ( $arrRet );
		return $arrRet;
	}

	private function testUpdate()
	{

		$proxy = new PHPProxy ( 'data' );
		$arrData = array ('command' => 'update', 'uid' => 123, 'time' => time (),
				'table' => 't_user', 'where' => array ('uid' => array ('=', 123 ) ),
				'values' => array ('cur_blood_package' => array ('-=', 1 ),
						'max_blood_package' => array ('+=', 1 ),
						'capability' => array ('=', time () ) ) );
		$arrRet = $proxy->query ( $arrData );
		var_dump ( $arrRet );
		return $arrRet;
	}

	function testPressure()
	{

		$data = new CData ();
		$arrPid = array ();
		for($counter = 0; $counter < 10; $counter ++)
		{
			$pid = pcntl_fork ();
			if ($pid == 0)
			{
				for($j = 0; $j < 1000; $j ++)
				{
					$arrRet = $data->select ( array ('uid', 'uname' ) )->from ( 't_user' )->where (
							'uid', '=', 123 )->query ();
				}
				return;
			}
			else
			{
				$arrPid [] = $pid;
			}
		}

		foreach ( $arrPid as $pid )
		{
			$status = 0;
			pcntl_waitpid ( $pid, $status );
			echo sprintf ( "process:%d is end\n", $pid );
		}
	}

	function testOrderBy()
	{

		$data = new CData ();
		$arrRet = $data->select ( array ('guild_id' ) )->from ( 't_guild' )->where ( 'guild_id',
				'>', 0 )->orderBy ( 'guild_level', true )->orderBy ( 'create_time', true )->query ();
		var_dump ( $arrRet );
	}

	function testDuplicateKey()
	{

		$data = new CData ();
		$arrBody = array ('uid' => 52225, 'guild_id' => 1, 'role_type' => 1, 'status' => 1,
				'va_info' => array (), 'day_belly_num' => 0, 'contribute_data' => 0,
				'last_belly_time' => 0, 'last_gold_time' => 0, 'last_banquet_time' => 0 );
		$arrUpdateKey = array ('status', 'guild_id', 'role_type' );
		$arrRet = $data->insertOrUpdate ( 't_guild_member' )->values ( $arrBody )->onDuplicateUpdateKey (
				$arrUpdateKey )->query ();
		var_dump ( $arrRet );
		$arrRet = $data->insertOrUpdate ( 't_guild_member' )->values ( $arrBody )->query ();
		var_dump ( $arrRet );
	}

	function testMcClient()
	{

		require_once (LIB_ROOT . '/McClient.class.php');
		$arrRet = McClient::get ( 'test' );
		var_dump ( $arrRet );
		$arrRet = McClient::set ( 'test', 123, 123, 123 );
		var_dump ( $arrRet );
		$arrRet = McClient::get ( 'test' );
		var_dump ( $arrRet );
	}

	function testDelete()
	{

		$data = new CData ();
		$arrRet = $data->deleteFrom ( 't_user' )->where ( 'uid', '=', 1234 )->query ();
		var_dump ( $arrRet );
	}

	function testSelectIn()
	{

		$data = new CData ();
		$arrRet = $data->select ( array ('col1', 'col2', 'col3' ) )->from ( 't_data' )->where (
				'col1', 'IN', array (1, 2, 3, 4 ) )->where ( 'col2', '=', 1 )->query ();
		var_dump ( $arrRet );
	}

	function testSelectInit()
	{

		$data = new CData ();
		for($i = 0; $i < 100; $i ++)
		{
			for($j = 0; $j < 100; $j ++)
			{
				$data->insertInto ( 't_data' )->values (
						array ('col1' => $i, 'col2' => $j, 'col3' => $i * $j ) )->query ();
			}
		}
	}

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$this->testSelectInit ();
		$this->testSelectIn ();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
