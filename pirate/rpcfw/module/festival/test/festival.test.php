<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: festival.test.php 31058 2012-11-14 09:27:01Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/test/festival.test.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-11-14 17:27:01 +0800 (三, 2012-11-14) $
 * @version $Revision: 31058 $
 * @brief 
 *  
 **/
class FestivalTest extends PHPUnit_Framework_TestCase
{
	
	private $uid = 20101;
	private $festival;
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		$this->festival = new Festival();
		parent::setUp ();
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('global.uid');
	}
	
	/**
	 * @group getFestivalUserInfo 
	 */
	public function test_festival()
	{
		// 取得节日活动玩家信息
//		$this->festival->getFestivalUserInfo();
		// 翻牌
//		$this->festival->flopCards(2);	
	}

		/**
	 * @group getExchangePoint 
	 */
	public function test_getExchangePoint()
	{
		$this->festival->getExchangePoint();
		
		$this->festival->exchangeItem(11101);
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */