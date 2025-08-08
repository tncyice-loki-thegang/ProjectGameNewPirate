<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnHonourTest.php 33505 2012-12-20 05:44:52Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/honourshop/test/EnHonourTest.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-20 13:44:52 +0800 (四, 2012-12-20) $
 * @version $Revision: 33505 $
 * @brief 
 *  
 **/
class HonourShopTest extends PHPUnit_Framework_TestCase
{
	private $uid = 49806;
	
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
	 * @group honourInfo 
	 */
	public function test_honourInfo()
	{
		$ret = EnHonourShop::addHonourPoint(49806, 10);
		var_dump($ret);
		$ret = EnHonourShop::addFinallyHonourPoint(49806, 10);
		$ret = EnHonourShop::getUserHonourPoint();
		var_dump($ret);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */