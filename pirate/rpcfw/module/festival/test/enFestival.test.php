<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: enFestival.test.php 31615 2012-11-22 07:05:00Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/test/enFestival.test.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-11-22 15:05:00 +0800 (四, 2012-11-22) $
 * @version $Revision: 31615 $
 * @brief 
 *  
 **/
class enFestivalLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 20101;
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		parent::setUp ();
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('global.uid');
	}
	
	/**
	 * @group getOverRide
	 */
	public function test_getOverRide()
	{
//		EnFestival::getOverRide(1);
//		EnFestival::getOverRide(2);
//		EnFestival::getOverRide(3);
//		EnFestival::getOverRide(4);
//		EnFestival::getOverRide(5);
//		EnFestival::getOverRide(6);
//		EnFestival::getOverRide(7);
//		EnFestival::getOverRide(8);
//		EnFestival::getOverRide(9);
//		EnFestival::getOverRide(10);
//		EnFestival::getOverRide(11);
//		EnFestival::getOverRide(12);
	}
	
	/**
	 * @group getOverRide
	 */
	public function test_addPoint()
	{
//		EnFestival::addSailPoint();
//		EnFestival::addEliteCopyAtkPoint();
//		EnFestival::addResourcePoint();
//		EnFestival::addCookPoint();
//		EnFestival::addCopyPoint();

		// 积分商城
		EnFestival::addSailPoint();
//		EnFestival::addCookPoint();
//		EnFestival::addOrderPoint();
//		EnFestival::addDaytaskPoint();
//		EnFestival::addSalaryPoint();
//		EnFestival::addSlavePoint();
//		EnFestival::addPeinforcePoint();
//		EnFestival::addElCopyPoint();
//		EnFestival::addExplorPoint();
//		EnFestival::addArenaPoint();
//		EnFestival::addRobPoint();
//		EnFestival::addAtkPortPoint();
//		EnFestival::addDonatePoint();
//		EnFestival::addResourcePoint();
//		EnFestival::addTalkPoint();
//		EnFestival::addTreasurePoint();
//		EnFestival::addSmeltingPoint();
//		EnFestival::addRapidPoint();
//		EnFestival::addGoldWillPoint();
//		EnFestival::addGoldSoulPoint();
//		EnFestival::addAstroPoint();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
