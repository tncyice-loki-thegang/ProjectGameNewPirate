<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: HonourShopTest.php 34665 2013-01-07 11:52:38Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/honourshop/test/HonourShopTest.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-07 19:52:38 +0800 (一, 2013-01-07) $
 * @version $Revision: 34665 $
 * @brief 
 *  
 **/

class HonourShopTest extends PHPUnit_Framework_TestCase
{
	
	private $uid = 21300;
	private $honour;
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		$this->honour = new HonourShop();
		parent::setUp ();
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('global.uid');
		MyAllBlue::release();
	}
	
	/**
	 * @group honourInfo 
	 */
	public function test_honourInfo()
	{
		// 取得玩家信息
		$ret = array();
		$ret = $this->honour->honourInfo();
		var_dump($ret);
		$ret = $this->honour->exItemByHonour(60014);
		var_dump($ret);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */