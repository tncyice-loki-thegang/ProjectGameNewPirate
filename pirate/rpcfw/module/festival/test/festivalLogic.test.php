<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: festivalLogic.test.php 26655 2012-09-04 09:52:24Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/test/festivalLogic.test.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-09-04 17:52:24 +0800 (二, 2012-09-04) $
 * @version $Revision: 26655 $
 * @brief 
 *  
 **/
class FestivalLogicTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @group checkFestivalDate
	 */
	public function test_checkFestivalDate()
	{
		// 节日活动时间20120829040000~20120901040000
		$testDate = strtotime("20120101121212");
		$ret = FestivalLogic::checkFestivalDate($testDate);
		$this->assertEmpty($ret);
	
		$testDate = strtotime("20120829040000");
		$ret = FestivalLogic::checkFestivalDate($testDate);
		$this->assertEmpty($ret);

		$testDate = strtotime("20120901040000");
		$ret = FestivalLogic::checkFestivalDate($testDate);
		$this->assertEmpty($ret);

		$testDate = strtotime("20120831040000");
		$ret = FestivalLogic::checkFestivalDate($testDate);
		$this->assertNotEmpty($ret);
	}
	
	/**
	 * @group getFestival
	 */
	public function test_getFestival_1()
	{
		$ret = FestivalLogic::getFestival(1001);
		$this->assertNotEmpty($ret);
	}
	
	/**
	 * @group getFestival
	 */
	public function test_getFestival_2()
	{
		// 执行前提数据库中�?002的数�?
		$ret = FestivalLogic::getFestival(1002);
		$this->assertNotEmpty($ret);
	}

	/**
	 * @group checkFlopCardsTimes
	 */
	public function test_checkFlopCardsTimes()
	{
		// 没有剩余翻牌次数
		$ret = FestivalLogic::checkFlopCardsTimes(1004);
		$this->assertFalse($ret);
		
		// 有剩余翻牌次�?
		$ret = FestivalLogic::checkFlopCardsTimes(1003);
		$this->assertTrue($ret);
	}
	
	/**
	 * @group updateFlopCardsResult
	 */
	public function test_updateFlopCardsResult()
	{
		// 更新翻牌结果、翻牌次�?1
		FestivalLogic::updateFlopCardsResult(1003);
	}
	
	/**
	 * @group clearFlopCardsResult
	 */
	public function test_clearFlopCardsResult()
	{
		// 清空积分、翻牌次数�?翻牌结果
		FestivalLogic::clearFlopCardsResult(1005);
	}
	
	/**
	 * @group addRewardPoint
	 */
	public function test_addRewardPoint()
	{
		RPCContext::getInstance()->setSession('global.uid', 1005);
		// 更新活动积分
		FestivalLogic::addRewardPoint(1005);
	}
	
	/**
	 * @group updateFpCardsRetToUser
	 */
	public function test_updateFpCardsRetToUser()
	{
		RPCContext::getInstance()->setSession('global.uid', 20101);
		// 更新活动积分
		$cards = array(FestivalDef::FESTIVAL_CARD_BELLY => "3",
						FestivalDef::FESTIVAL_CARD_EXPE => "3",
						FestivalDef::FESTIVAL_CARD_GOLD => "3",
						FestivalDef::FESTIVAL_CARD_EXEC => "8",
						FestivalDef::FESTIVAL_CARD_PRES => "10",
						FestivalDef::FESTIVAL_CARD_ITEM => array(600704,600705));
		FestivalLogic::updateFpCardsRetToUser(20101, $cards);
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
