<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

require_once (LIB_ROOT . '/data/index.php');
require_once (MOD_ROOT . '/copy/index.php');
require_once (MOD_ROOT . '/copy/CopyManager.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class CopyLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;

	protected static function getMethod($name) 
	{
		$class = new ReflectionClass('CopyManager');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}


	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
	}

	/**
	 * @group inTimeScale
	 */
	public function test_inTimeScale_0() 
	{
		echo "\n== "."CopyManager::inTimeScale_0 Start =========="."\n";
		$actID = 2;

		$foo = self::getMethod('inTimeScale');
		$ret = $foo->invokeArgs(null, array($actID));
		var_dump($ret);
//		$this->assertFalse($ret, "inTimeScale:ret not false");
		echo "== "."CopyManager::inTimeScale_0 End ============"."\n";
	}

	/**
	 * @group refreshEnemy
	 */
	public function test_refreshEnemy_0()
	{
		echo "\n== "."CopyManager::refreshEnemy_0 Start =========="."\n";
		$actID = 5;

		$foo = self::getMethod('refreshEnemy');
		$ret = $foo->invokeArgs(null, array($actID));
		var_dump($ret);

		echo "== "."CopyManager::refreshEnemy_0 End ============"."\n";
	}

	/**
	 * @group adjustRefreshTime
	 */
	public function test_adjustRefreshTime_0() 
	{
		echo "\n== "."CopyManager::adjustRefreshTime_0 Start =========="."\n";
		$actID = 1;

		$foo = self::getMethod('adjustRefreshTime');
		$ret = $foo->invokeArgs(null, array($actID));
		var_dump($ret);
		echo "== "."CopyManager::adjustRefreshTime_0 End ============"."\n";
	}

	/**
	 * @group adjustRefreshTime
	 */
	public function test_adjustRefreshTime_1() 
	{
		echo "\n== "."CopyManager::adjustRefreshTime_1 Start =========="."\n";
		$actID = 2;

		$foo = self::getMethod('adjustRefreshTime');
		$ret = $foo->invokeArgs(null, array($actID));
		var_dump($ret);
		echo "== "."CopyManager::adjustRefreshTime_1 End ============"."\n";
	}

	/**
	 * @group adjustRefreshTime
	 */
	public function test_adjustRefreshTime_2() 
	{
		echo "\n== "."CopyManager::adjustRefreshTime_2 Start =========="."\n";
		$actID = 4;

		$foo = self::getMethod('adjustRefreshTime');
		$ret = $foo->invokeArgs(null, array($actID));
		var_dump($ret);
		echo "== "."CopyManager::adjustRefreshTime_2 End ============"."\n";
	}

	/**
	 * @group getLatestEnemies
	 */
	public function test_getLatestEnemies_0()
	{
		echo "\n== "."CopyManager::getLatestEnemies_0 Start =========="."\n";
		$copyID = 1;
		$ret = CopyManager::getLatestEnemies($copyID);
		var_dump($ret);
		
		echo "== "."CopyManager::getLatestEnemies_0 End ============"."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */