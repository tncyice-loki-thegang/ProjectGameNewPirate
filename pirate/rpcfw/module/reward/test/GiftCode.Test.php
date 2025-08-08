<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GiftCode.Test.php 18298 2012-04-09 02:55:38Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/test/GiftCode.Test.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-04-09 10:55:38 +0800 (一, 2012-04-09) $
 * @version $Revision: 18298 $
 * @brief 
 *  
 **/

require_once MOD_ROOT . '/reward/Reward.class.php';

class GiftCode extends PHPUnit_Framework_TestCase
{
	/**
	 * 
	 * Enter description here ...
	 * @var Reward
	 */
	private $reward;
	
	protected function setUp ()
	{
		parent::setUp();
	}
	
	protected function tearDown ()
	{
		EnUser::release();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
	}
	
	private function createUser($uid=0)
	{
		if ($uid==0)
		{
			$pid = 40000 + rand(0, 9999);
		}
		else
		{
			$pid = $uid;
		}
		$utid = 1;
		$uname = '123001';		
		UserLogic::createUser($pid, $utid, $uname, $uid);
		$users = UserLogic::getUsers($pid);
		$uid = $users[0]['uid'];
		return $uid;
	}
	
	/**
	 * @group giftCode
	 * Enter description here ...
	 */
	public function test_getGiftByCode_0()
	{
		$uid = 1234321;
		try
		{
			$this->createUser(1234321);
		}
		catch (Exception $e)
		{
			Logger::debug("%s", $e->getMessage());
		}				
		
		EnUser::release();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
		
		RPCContext::getInstance()->setSession('global.uid', $uid);		
		
		$code = 'test1-02tba5vjt7gtom';
		$reward = new Reward();
		$ret = $reward->getGiftByCode($code);
		
		var_dump($ret);		
	}

}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */