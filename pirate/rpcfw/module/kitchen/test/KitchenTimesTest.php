<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: KitchenTimesTest.php 37381 2013-01-29 02:53:21Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/kitchen/test/KitchenTimesTest.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-29 10:53:21 +0800 (二, 2013-01-29) $
 * @version $Revision: 37381 $
 * @brief 
 *  
 **/

class KitchenTimesTest extends PHPUnit_Framework_TestCase
{
	private $uid = 20103;

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
		echo "\n== "."KitchenLogic::getUserKitchenInfo Start =========="."\n";

		$ret = KitchenLogic::getUserKitchenInfo();
//		$ret = KitchenLogic::__checkOrderUpdateTime(1359013565);
		var_dump($ret);

		echo "== "."KitchenLogic::getUserKitchenInfo End ============"."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */