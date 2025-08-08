<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArenaTest.php 26803 2012-09-07 02:59:13Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/arena/test/ArenaTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-07 10:59:13 +0800 (五, 2012-09-07) $
 * @version $Revision: 26803 $
 * @brief 
 * 
 **/

require_once MOD_ROOT . '/arena/index.php';
require_once MOD_ROOT . '/user/index.php';

class ArenaTest extends PHPUnit_Framework_TestCase
{
	private $arena;
	
	protected function setUp ()
	{
		$count = ArenaDao::getCount();
		if ($count<5)
		{
			throw new Exception("arena user number $count");
		}
		$this->createUser();
		$this->arena = new Arena;
	}
	
	private function createUser()
	{
		$pid = 40000 + rand(0,9999);
      	$utid = 1;
		$uname = 't' . $pid;
		
		UserLogic::createUser($pid, $utid, $uname);
		$users = UserLogic::getUsers($pid);
		$this->uid = $users[0]['uid'];
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		
		$userObj = EnUser::getUserObj();
		$fid = $userObj->getCurFormation();
		$fmt = new Formation();
		$arrFmt = array_fill(0, 9, 0);
		$arrFmt[0] = $userObj->getMasterHeroObj()->getHid(); 
		$fmt->changeCurFormation($fid, $arrFmt);
	}
	
	protected function tearDown ()
	{
		EnUser::release();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
	}

    /**
     * @group getOpponentPosition
     * pos最大不能超过总数加1
     */
    public function test_getOpponentPostion_0()
    {
        for($i=6; $i<=6; $i++)
        {
            $ret = ArenaLogic::getOpponentPosition($i);
            //var_dump($ret);
        }
    }
	
	/**
	 * @group enterArena
	 * Enter description here ...
	 */
	public function test_enterArena_0()
	{
		$arrRet = $this->arena->enterArena();
		var_dump($arrRet);
		$this->assertArrayHasKey('res', $arrRet);		
		$this->assertArrayHasKey('ret', $arrRet);
	}
	
	/**
	 * @group clearCdtime
	 * Enter description here ...
	 */
	public function test_clearCdtime_0()
	{
        $this->arena->enterArena();
		$arrRet = $this->arena->clearCdtime();
		$this->assertArrayHasKey('cost', $arrRet);
		$this->assertArrayHasKey('ret', $arrRet);
        $this->assertEquals(0, $arrRet['cost']);
	}
	
	/**
	 * @group clearCdtime
	 * Enter description here ...
	 */
	public function test_clearCdtime_1()
	{
        $this->arena->enterArena();
		ArenaDao::update($this->uid, array('fight_cdtime'=>Util::getTime()+120));
		$arrRet = $this->arena->clearCdtime();
        $this->assertArrayHasKey('cost', $arrRet);
		$this->assertArrayHasKey('ret', $arrRet);
        $this->assertEquals(2, $arrRet['cost']);
	}

    /**
     * @group challenge
     */
    public function test_challenge_0()
    {
        $arrInfo = $this->arena->enterArena();
        $pos = $arrInfo['res']['opponents'][0]['position'];
        $arrRet = $this->arena->challenge($pos);
        var_dump($arrRet);
    }
    
    /**
     * @group buyAddedChallenge
     */
    public function test_buyAddedChallenge_0()
    {
    	 $arrInfo = $this->arena->enterArena();
    	 $user = EnUser::getUserObj();
    	 $user->setVip(3);
    	 $user->update();
    	 $ret = $this->arena->buyAddedChallenge(1);
    	 $this->arrayHasKey('res', $ret);
    	 $this->arrayHasKey('ret', $ret);
    }
    
    /**
     * @group getPositionList
     */
    public function test_getPositionList_0()
    {
    	$ret = $this->arena->getPositionList();
    	var_dump($ret);
    }
    
    /**
     * @group getRewardLuckyList
     */
    public function test_getRewardLuckyList_0()
    {
    	$ret = $this->arena->getRewardLuckyList();
        var_dump($ret);
    }
    
    /**
     * @group testBC
     */
    public function test_bc()
    {
    	$ret = ArenaLogic::isBroadcastUpgradeContinue(0, 99);
    	var_dump($ret);
    	$ret = ArenaLogic::isBroadcastUpgradeContinue(2, 199);
    	var_dump($ret);
    	$ret = ArenaLogic::isBroadcastUpgradeContinue(2, 201);
    	var_dump($ret);
    	$ret = ArenaLogic::isBroadcastUpgradeContinue(201, 400);
    	var_dump($ret);
    	$ret = ArenaLogic::isBroadcastUpgradeContinue(201, 502);
    	var_dump($ret);
    	$ret = ArenaLogic::isBroadcastUpgradeContinue(201, 1500);
    	var_dump($ret);
    }
    
    public function test_getActiveRate()
    {
    	$ret = ArenaRound::getActiveRate();
    	var_dump($ret);
    }

	
	

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */