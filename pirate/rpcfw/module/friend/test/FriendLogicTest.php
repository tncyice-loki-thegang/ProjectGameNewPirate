<?php

require_once (MOD_ROOT . '/friend/index.php');

/**
 * FriendLogic test case.
 */
class FriendLogicTest extends PHPUnit_Framework_TestCase
{

	private $uid = 21300;
	
	/**
	 * @var FriendLogic
	 */
	private $FriendLogic;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		parent::setUp ();
		$this->FriendLogic = new FriendLogic(/* parameters */);
		
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('global.uid');
		$this->FriendLogic = null;
		parent::tearDown ();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{

	}

//	/**
//	 * Tests FriendLogic::addFriend()
//	 */
//	public function testAddFriend()
//	{
//
//		FriendLogic::addFriend ( 1, 2 );
//	}
//
//	/**
//	 * Tests FriendLogic::addBlackList()
//	 */
//	public function testAddBlackList()
//	{
//
//		FriendLogic::addBlackList ( 1, 2 );
//	}
//
//	/**
//	 * Tests FriendLogic::getFriendList()
//	 */
//	public function testGetFriendList()
//	{
//
//		FriendLogic::getFriendList ( 27923 );
//	}
//
//	/**
//	 * Tests FriendLogic::delFriend()
//	 */
//	public function testDelFriend()
//	{
//
//		FriendLogic::delFriend ( 1, 2 );
//	}
//
//	public function testLoginNotify()
//	{
//
//		FriendLogic::loginNotify ( 1 );
//	}
//
//	public function testLogoffNotify()
//	{
//
//		FriendLogic::logoffNotify ( 1 );
//	}
//
//	public function testDelUserFriend()
//	{
//
//		FriendLogic::delUserFriend ( 1 );
//	}

//	public function testGetBestFriend()
//	{
//
//		FriendLogic::getBestFriend (20108, 1, 10);
//	}
	public function testRecommendFriendList()
	{

		FriendLogic::recommendFriendList ($this->uid, 1, 10);
	}
	
	public function testAddRecommendFriendList()
	{
		$fuidAry = array(21416, 25629);
		FriendLogic::addRecommendFriendList ($this->uid, $fuidAry);
	}
	
}

