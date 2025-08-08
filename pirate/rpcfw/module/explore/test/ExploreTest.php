<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ExploreTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/explore/test/ExploreTest.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 * 
 **/

require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once MOD_ROOT . '/explore/Explore.class.php';

class ExploreTest extends PHPUnit_Framework_TestCase
{
	private $uid;
	private $exploreId = 1001;
	
	protected function setUp ()
	{
		parent::setUp();
		$this->pid = 40000 + rand(0, 9999);
		$this->utid = 1;
		$this->uname = 't' . $this->pid;
		
		UserLogic::createUser($this->pid, $this->utid, $this->uname);
		$users = UserLogic::getUsers($this->pid);
		$this->uid = $users[0]['uid'];
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		RPCContext::getInstance()->setSession('global.townId', 1);

		SwitchLogic::setValue(SwitchDef::EXPLORE);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown ()
	{
		parent::tearDown();
		EnUser::release();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
		RPCContext::getInstance()->unsetSession('global.townId');
	}
	
	/**
	 * @group getExplore
	 * Enter description here ...
	 */
	public function test_getExplore_0 ()
	{
		var_dump($this->uid);
		var_dump(RPCContext::getInstance()->getSession('switch.info'));
		
		$exp = new Explore();
		$ret = $exp->getExplore($this->exploreId);
		var_dump($ret);
	}

	/**
	 * @group explorePos
	 */
	public function test_explorePos_0()
	{
		$exp = new Explore();
		$exp->getExplore($this->exploreId);
		$ret = $exp->explorePos($this->exploreId, 0);
		var_dump($ret);
		
	}

	/**
	 * @group sell
	 */
	public function test_sell_0()
	{
		$exp = new Explore();
		$exp->getExplore($this->exploreId);
		$ret = $exp->explorePos($this->exploreId, 0);
		$ret = $exp->explorePos($this->exploreId, 0);
		$ret = $exp->explorePos($this->exploreId, 0);
		$ret = $exp->explorePos($this->exploreId, 0);
		$info = $exp->getExplore($this->exploreId);

		var_dump($info);
		
		$ret = $exp->sell($this->exploreId, array($info['items'][0]['item_id']));
		var_dump($ret);
		
	}

	/**
	 * @group moveAllToBag
	 */
	public function test_moveAlltoBay()
	{
		$exp = new Explore();
		$exp->getExplore($this->exploreId);
		$ret = $exp->explorePos($this->exploreId, 0);
		$ret = $exp->explorePos($this->exploreId, 0);
		$ret = $exp->explorePos($this->exploreId, 0);
		$ret = $exp->explorePos($this->exploreId, 0);
		$ret = $exp->moveAllToBag($this->exploreId);
		var_dump($ret);
	}

	/**
	 * @group getBoxByGold
	 */
	public function test_getBoxByGold_0()
	{
		$exp = new Explore();
		$exp->getExplore($this->exploreId);
		$user = EnUser::getUserObj();
		$user->setVip(5);
		$ret = $exp->getBoxByGold($this->exploreId, 2);
		var_dump($ret);
	}

}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */