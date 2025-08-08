<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ImpelLogicTest.php 39014 2013-02-22 02:26:08Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/impeldown/test/ImpelLogicTest.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-22 10:26:08 +0800 (五, 2013-02-22) $
 * @version $Revision: 39014 $
 * @brief 
 *  
 **/

class ImelLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 20103;

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
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
	}

	protected function tearDown()
	{
	}

	/**
	 * @group getImpelDownInfo
	 */
	public function test_getImpelDownInfo_0()
	{
		echo "\n== "."ImelLogicTest::getImpelDownInfo_0 Start ====================="."\n";


//		$ret = ImpelDownLogic::savingAce(4);

//		$ret = ImpelDownLogic::refreshNpcList(3);

//		$ret = ImpelDownLogic::getTop(0, 10);

//		$ret = ImpelDownLogic::getSelfOrder();

//		$ret = ImpelDownLogic::savingAce(3, array(1030062,0,10088531,0,0,0,0,0,0), 10008);

		$ret = ImpelDownLogic::getImpelDownInfo();

//		$ret = ImpelDownLogic::getPrize();

//		$ret = ImpelDownLogic::refreshNpcListByGold(3);

//		$ret = ImpelDownLogic::buyChallengeTime();

		var_dump($ret);


		echo "== "."ImelLogicTest::getImpelDownInfo_0 End ======================="."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */