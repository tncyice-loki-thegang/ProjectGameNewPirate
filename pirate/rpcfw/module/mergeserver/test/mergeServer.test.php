<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: mergeServer.test.php 36441 2013-01-19 07:13:46Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mergeserver/test/mergeServer.test.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-19 15:13:46 +0800 (六, 2013-01-19) $
 * @version $Revision: 36441 $
 * @brief 
 *  
 **/

class mergeServerTest extends PHPUnit_Framework_TestCase
{
	private $uid = 49908;
	private $ms;
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		RPCContext::getInstance()->setSession('global.serverId', '4');
		$this->ms = new MergeServer();
		parent::setUp ();
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('global.uid');
		RPCContext::getInstance()->unsetSession('global.serverId');
	}
	
	/**
	 * @group getRewardLast
	 */
	public function test_getRewardLast()
	{
		 $ret = $this->ms->getRewardLast();
		 var_dump($ret);
	}
	
//	/**
//	 * @group getReward
//	 */
//	public function test_getReward()
//	{
//		$this->ms->getIsCompensation();
//	}
//	
//	/**
//	 * @group getCompensation
//	 */
//	public function test_getCompensation()
//	{
//		$this->ms->getCompensation();
//	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */