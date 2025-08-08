<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: consoleTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/console/test/consoleTest.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

//require_once (LIB_ROOT . '/data/index_phpunit.php');
require_once (LIB_ROOT . '/data/index.php');
require_once (MOD_ROOT . '/console/index.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class consoleTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		parent::setUp ();
		RPCContext::getInstance()->setSession('global.uid', $this->uid);

		// EnKitchen::addNewKitchenInfoForUser($this->uid);
		// EnTalks::addNewTalksInfoForUser($this->uid);
		// EnCaptain::addNewCaptainInfoForUser($this->uid);
	}

	protected function tearDown()
	{
	}

	/**
	 * @group openCopy
	 */
	public function test_openCopy_0()
	{
		echo "\n== "."Console::openCopy_0 Start ========================================="."\n";

		$tmp = new Console();
		$tmp->openCopy(99);

		$tmp->defeatEnemy(916);

		echo "== "."Console::openCopy_0 End ==========================================="."\n";
	}

	/**
	 * @group resetTimes
	 */
	public function test_resetTimes_0()
	{
		echo "\n== "."Console::resetTimes_0 Start ======================================="."\n";

		$tmp = new Console();
		$tmp->resetSmeltingTimes();
		$tmp->resetArtificerTimes();

		$tmp->resetTalksTimes();
		$tmp->resetSailsTimes();

		$tmp->resetCooksTimes();
		$tmp->resetOrdersTimes();
		$tmp->resetBeOrdersTimes();

		echo "== "."Console::resetTimes_0 End ========================================="."\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */