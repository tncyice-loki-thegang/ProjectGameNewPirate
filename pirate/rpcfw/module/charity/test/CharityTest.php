<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: CharityTest.php 27422 2012-09-19 08:56:29Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/charity/test/CharityTest.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-19 16:56:29 +0800 (三, 2012-09-19) $
 * @version $Revision: 27422 $
 * @brief 
 *  
 **/

class CharityTest extends PHPUnit_Framework_TestCase
{
	private $uid_1 = 20104;
	private $uid_2 = 20110;
	private $uid_3 = 20103;

	protected static function getMethod($name) 
	{
		$class = new ReflectionClass('ActiveLogic');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		parent::setUp ();
		RPCContext::getInstance()->setSession('global.uid', $this->uid_2);
	}

	protected function tearDown()
	{
	}

	/**
	 * @group getCharityInfo
	 */
	public function test_getCharityInfo_0()
	{
		echo "\n== "."CharityLogic::getCharityInfo_0 Start ========================"."\n";

		$ret = CharityLogic::getCharityInfo();

//		$ret = CharityLogic::fetchtVipSalary();

//		$ret = CharityLogic::fetchCharity(0);

//		$ret = EnCharity::addGoldRecord(5000);
		var_dump($ret);


		echo "== "."CharityLogic::getCharityInfo_0 End =========================="."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */