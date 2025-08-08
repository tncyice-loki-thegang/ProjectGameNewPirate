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

require_once (MOD_ROOT . '/formation/index.php');
require_once (MOD_ROOT . '/user/index.php');

class EnFormationTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;
	private $boatID = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		parent::setUp ();	

		RPCContext::getInstance()->setSession('global.boatid', $this->boatID);
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		RPCContext::getInstance()->setSession('user.user', UserLogic::getUser($this->uid));
	}

	/**
	 * @group getFormationInfo
	 */
	public function test_getFormationInfo_0()
	{
		echo "\n== "."EnFormation::getFormationInfo_0 Start =========="."\n";
		$ret = EnFormation::getFormationInfo($this->uid);
		var_dump($ret);
		echo "== "."EnFormation::getFormationInfo_0 End ============"."\n";
	}

	/**
	 * @group getBossFormationInfo
	 */
	public function test_getBossFormationInfo_0()
	{
		echo "\n== "."EnFormation::getBossFormationInfo_0 Start =========="."\n";
		$ret = EnFormation::getBossFormationInfo(1);
		var_dump($ret);
		echo "== "."EnFormation::getBossFormationInfo_0 End ============"."\n";
	}

	/**
	 * @group getNpcFormation
	 */
	public function test_getNpcFormation_0()
	{
		echo "\n== "."EnFormation::getNpcFormation_0 Start =========="."\n";
		$ret = EnFormation::getNpcFormation(4, array(7,0,0,0,0,0,0,0,0));
		var_dump($ret);
		echo "== "."EnFormation::getNpcFormation_0 End ============"."\n";
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */