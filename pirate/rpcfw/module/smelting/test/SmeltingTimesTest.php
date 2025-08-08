<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SmeltingTimesTest.php 40125 2013-03-06 09:59:05Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/test/SmeltingTimesTest.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-03-06 17:59:05 +0800 (三, 2013-03-06) $
 * @version $Revision: 40125 $
 * @brief 
 *  
 **/

class SmeltingTimesTest extends PHPUnit_Framework_TestCase
{
	private $uid = 20124;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
	}

	/**
	 * @group groupAttack
	 */
	public function test_getUserKitchenInfo()
	{
		echo "\n== "."SmeltingLogic::getSmeltingInfo Start =========="."\n";

//		$ret = SmeltingLogic::getSmeltingInfo();
//		var_dump($ret);
		
		$ret = SmeltingLogic::smeltingAll(8, SmeltingConf::TYPE_RING);
		var_dump($ret);

		echo "== "."SmeltingLogic::getSmeltingInfo End ============"."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */