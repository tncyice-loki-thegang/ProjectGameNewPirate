<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TreasureNpcTest.php 35750 2013-01-14 08:32:53Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasurenpc/test/TreasureNpcTest.php $
 * @author $Author$(lijinfeng@babeltime.com)
 * @date $Date: 2013-01-14 16:32:53 +0800 (一, 2013-01-14) $
 * @version $Revision: 35750 $
 * @brief 
 *  
 **/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */


class TreasureNpcTest extends PHPUnit_Framework_TestCase
{
	
	private $m_uid;
	
	
	protected function setUp() 
	{
		parent::setUp ();
		$m_uid = 74101;
		RPCContext::getInstance()->setSession('global.uid', $this->m_uid);
	}
	
	
	/**
	 * @group getInfo
	 */
	public function test_getInfo_0()
	{
		RPCContext::getInstance()->setSession('global.uid', 74101);
		
		$test = new TreasureNpc();
		//$ret = $test->getTreasureNpc();
		
		$ret = $test->huntTreasureNpc(6);
		var_dump($ret);
	}
	
	
	
	protected function tearDown()
	{
		parent::tearDown ();
        RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
	}
	
}