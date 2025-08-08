<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnFriendTest.php 28948 2012-10-12 03:16:14Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/friend/test/EnFriendTest.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-10-12 11:16:14 +0800 (五, 2012-10-12) $
 * @version $Revision: 28948 $
 * @brief 
 *  
 **/

class EnFriendTest extends PHPUnit_Framework_TestCase
{

	private $uid = 27923;
	
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
	 * @group getBestFriend
	 */
	public function test_getBestFriend()
	{
		EnFriend::getBestFriend();
	}
}

