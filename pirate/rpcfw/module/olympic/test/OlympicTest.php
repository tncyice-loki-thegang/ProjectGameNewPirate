<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: OlympicTest.php 27754 2012-09-22 02:39:22Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/olympic/test/OlympicTest.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-22 10:39:22 +0800 (六, 2012-09-22) $
 * @version $Revision: 27754 $
 * @brief 
 *  
 **/

class OlympicTest extends PHPUnit_Framework_TestCase
{
	private $uid_1 = 20100;
	private $uid_2 = 20101;
	private $uid_3 = 20000;

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
	 * @group signUp
	 */
	public function test_signUp_0()
	{
		echo "\n== "."OlympicLogic::signUp_0 Start ================================"."\n";
		
//		$ret = OlympicDao::resetOlympicInfo();

//		$ret = OlympicLogic::signUp(4, 1);

//		$ret = OlympicLogic::challenge(4, 1);
//		var_dump($ret);

//		OlympicLogic::drawBlock();

//		$inst = new Olympic();
//		$inst->startFinals();

//		$inst->awardPrizes();

//		$ret = OlympicLogic::getFightInfo();
//		$ret = OlympicUtil::getNow();

//		$ret = OlympicLogic::getUserOlympicInfo();

//		$ret = OlympicLogic::getJackPot();

//		$ret = OlympicLogic::cheer(20000);

//		$ret = OlympicLogic::getAllCheerObj();

//		$ret = OlympicLogic::distribute500wBelly();

//		$ret = date('Ymd', OlympicUtil::getLastestHappyTime());
		$ret = OlympicUtil::getLastestHappyTime();


//		$ret = EnOlympic::getIntegralList(0, 100);

//		$ret = EnOlympic::getUserIntegralRank();

//		$inst->generatLucky();

		var_dump($ret);

		echo "== "."OlympicLogic::signUp_0 End =================================="."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */