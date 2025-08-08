<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestData.php 16425 2012-03-14 02:58:27Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestData.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:58:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16425 $
 * @brief
 *
 **/

require_once (LIB_ROOT . '/data/index.php');

class TestData extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$maxValue = 10000;
		$this->test ( 't_test', $maxValue );
		$this->test ( 't_test1', $maxValue );
		$this->testMemcache ( 'test', $maxValue );
	}

	private function testMemcache($key, $maxValue)
	{

		$memcache = memcache_connect ( '127.0.0.1', 11211 );
		$memcache->set ( $key, $maxValue );
		$startTime = microtime ( true );
		do
		{
			$ret = $memcache->decrement ( $key );
		}
		while ( $ret );

		do
		{
			$ret = $memcache->increment ( $key );
		}
		while ( $ret < $maxValue );
		$endTime = microtime ( true );
		echo sprintf ( "memcache cost:%f\n", $endTime - $startTime );
	}

	private function test($table, $maxValue)
	{

		$data = new CData ();
		$startTime = microtime ( true );
		do
		{
			$arrRet = $data->update ( $table )->set ( array ('value' => new DecOperator ( 1 ) ) )->where (
					array ('id', '=', 1 ) )->where ( array ('value', '>', 0 ) )->query ();
		}
		while ( $arrRet ['affected_rows'] );

		do
		{
			$arrRet = $data->update ( $table )->set ( array ('value' => new IncOperator ( 1 ) ) )->where (
					array ('id', '=', 1 ) )->where ( array ('value', '<', $maxValue ) )->query ();
		}
		while ( $arrRet ['affected_rows'] );
		$endTime = microtime ( true );
		echo sprintf ( "table:%s cost:%f\n", $table, $endTime - $startTime );
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
