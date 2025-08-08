<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Reward.Test.php 36883 2013-01-24 03:42:55Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/test/Reward.Test.php $
 * @author $Author: lijinfeng $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-24 11:42:55 +0800 (四, 2013-01-24) $
 * @version $Revision: 36883 $
 * @brief 
 *  
 **/

require_once MOD_ROOT . '/reward/Reward.class.php';

class RewardTest extends PHPUnit_Framework_TestCase
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
		$uid = $this->createUser();
		RPCContext::getInstance()->setSession('global.uid', $uid);
		$this->uid = $uid;
		$this->reward = new Reward();
		
		echo("RewardTest setup");
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
		$uname = 't' . $pid;		
		UserLogic::createUser($pid, $utid, $uname);
		$users = UserLogic::getUsers($pid);
		$uid = $users[0]['uid'];
		return $uid;
	}
	
	protected function tearDown ()
	{
		EnUser::release();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
	}

	/**
	 * @group getSignInfo
	 */
	public function test_getSignInfo_0()
	{
		$ret = $this->reward->getSignInfo();
		var_dump($ret);
	}


	/**
	 * @group sign
	 */
	public function test_sign_0()
	{
		$ret = $this->reward->getSignInfo();
		$ret = $this->reward->sign(1);
		var_dump($ret);
	}

	/**
	 * @group getGiftInfo
	 */
	public function test_getGiftInfo_0()
	{
		$ret = $this->reward->getGiftInfo();
		var_dump($ret);
	}
	
	/**
	 * @group getGift
	 */
	public function test_getGift_0()
	{
		$ret = $this->reward->getGiftInfo();
		$id = $ret[0]['id'];
		$step = $ret[0]['step'];
		
		RewardGiftDao::update($this->uid, $id, array('accumulate_time'=>62));
		RPCContext::getInstance()->resetSession();

		$ret = $this->reward->getGift($id, $step);
		var_dump($ret);
	}

	/**
	 * @group getGiftAll
	 */
	public function test_getGiftAll_0()
	{
		$ret = $this->reward->getGiftInfo();
		$id = $ret[0]['id'];
		$step = $ret[0]['step'];

		$all = count(btstore_get()->REWARD_GIFT[$id]['gift']);

		for ($i=0; $i<$all; $i++)
		{
			RewardGiftDao::update($this->uid, $id, array('accumulate_time'=>20000));
			RPCContext::getInstance()->resetSession();
			$ret = $this->reward->getGift($id, $i);
		}
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
			//
		}				
		
		EnUser::release();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
		
		RPCContext::getInstance()->setSession('global.uid', $uid);		
		
		$code = 'test1-00coe2majdgz7h';
		$ret = $this->reward->getGiftByCode($code);
		
		var_dump($ret);		
	}
	
	
	public function test_getSpringFestivalWelfare_0()
	{
		
		RPCContext::getInstance()->setSession('global.uid', $uid);
		$this->reward = new Reward();
		
		$ret = $this->reward->getSprFestWelfareInfo();
		var_dump($ret);
	}
	
	
	public function test_recieveSprFestWelfare_0()
	{
		
		RPCContext::getInstance()->setSession('global.uid', $uid);
		$this->reward = new Reward();
		
		$ret = $this->reward->recieveSprFestWelfare(1);
		var_dump($ret);
	}

}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */