<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EliteCopyLogicTest.php 32773 2012-12-11 03:11:22Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elitecopy/test/EliteCopyLogicTest.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-11 11:11:22 +0800 (二, 2012-12-11) $
 * @version $Revision: 32773 $
 * @brief 
 *  
 **/

class EliteCopyLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 20103;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
	}
	
	
	public function test_getEliteCopyInfo()
	{
		$ret = EliteCopyLogic::getEliteCopyInfo();
//		$ret = EliteCopyLogic::passByGold(100001);
//		$ret = EliteCopyLogic::byCoin();
//		$ret = EliteCopyLogic::getCopyPassList(100001);
//		$ret = EliteCopyLogic::leaveEliteCopy();

//		$ret = EliteCopyLogic::enterEliteCopy(100002);
//		$ret = EliteCopyLogic::attack(100006);
//		CopyLogic::clearFightCdByGold();
//		$ret = EliteCopyLogic::attack(100007);
//		CopyLogic::clearFightCdByGold();
//		$ret = EliteCopyLogic::attack(100008);
//		CopyLogic::clearFightCdByGold();
//		$ret = EliteCopyLogic::attack(100009);
//		CopyLogic::clearFightCdByGold();
//		$ret = EliteCopyLogic::attack(100010);
		

		var_dump($ret);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */