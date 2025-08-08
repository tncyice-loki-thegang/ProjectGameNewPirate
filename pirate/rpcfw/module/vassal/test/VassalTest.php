<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: VassalTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/vassal/test/VassalTest.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/vassal/index.php');
require_once (MOD_ROOT . '/user/index.php');

class VassalTest extends PHPUnit_Framework_TestCase 
{
	private $uid;
	private $otherUid;
	protected function setUp() 
	{
		parent::setUp ();
		$this->pid = 40000 + rand(0,9999);
     	$this->utid = 1;
		$this->uname = 't' . $this->pid;
		
		UserLogic::createUser($this->pid, $this->utid, $this->uname);
		$users = UserLogic::getUsers($this->pid);
		$this->uid = $users[0]['uid'];
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		
		
		//otheruid
		$this->pid = 40000 + rand(0,9999);
     	$this->utid = 1;
		$this->uname = 't' . $this->pid;
		
		UserLogic::createUser($this->pid, $this->utid, $this->uname);
		$users = UserLogic::getUsers($this->pid);
		$this->otherUid = $users[0]['uid'];

        //tuid
		$this->pid = 40000 + rand(0,9999);
     	$this->utid = 1;
		$this->uname = 't' . $this->pid;
		
		UserLogic::createUser($this->pid, $this->utid, $this->uname);
		$users = UserLogic::getUsers($this->pid);
		$this->tuid = $users[0]['uid'];

	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown ();
		RPCContext::getInstance()->unsetSession('global.uid');
		EnUser::release();
		HeroManager::getInstance()->release();
	}
	
	private function makeVsl()
	{
		VassalDao::updateOrInsert($this->uid, $this->otherUid, array('status'=>VassalDef::STATUS_OK));
	}

    private function makeMst()
    {
        VassalDao::updateOrInsert($this->tuid, $this->uid, array('status'=>VassalDef::STATUS_OK));
    }
        
	
    /**
     * @group getVassalAll
     */
    public function test_getVassalAll_0() 
    {
        $this->makeVsl();
        $this->makeMst();
        $vsl = new Vassal();
        $ret = $vsl->getVassalAll();
        $this->assertArrayHasKey('train_num_per_vassal', $ret);
        $this->assertArrayHasKey('vassal', $ret);
        $this->assertArrayHasKey('master', $ret);
//        var_dump($ret);
    }

    /**
     * @group train
     */
    public function test_train_0()
    {
        $omu = EnUser::getUser();
        $ovu = EnUser::getUser($this->otherUid);
        
        $this->makeVsl();
        $vsl = new Vassal();
        $ret = $vsl->train(1, $this->otherUid);
//        var_dump($ret);
        $this->assertArrayHasKey('vassal_belly', $ret);
        $this->assertArrayHasKey('master_belly', $ret);
        $this->assertEquals('ok', $ret['ret']);

        $nmu = EnUser::getUser();
        $nvu = EnUser::getUser($this->otherUid);

        //      var_dump($this->otherUid);
        $this->assertEquals($nmu['belly_num']-$omu['belly_num'], $ret['master_belly']);
//        $this->assertEquals($nvu['belly_num']-$ovu['belly_num'], $ret['vassal_belly']);
    }

    /**
     * @group relieve
     */
    public function test_relieve_0()
    {
        $this->makeVsl();
        $vsl = new Vassal();
        $ret = $vsl->relieve($this->otherUid);
        $this->assertEquals('ok', $ret);
        $ret = VassalDao::getVslData($this->uid, $this->otherUid, array('status'));
//        $this->assertEquals(VassalDef::STATUS_RELIEVE, $ret['status']);
    }

    /**
     * @group conquer
     */
    public function test_conquer_0()
    {
    	$rctHeroes = HeroManager::getInstance()->getRecruitHeroes();
    	$this->assertNotEmpty($rctHeroes);
    	$hattr = current($rctHeroes);
    	$hid = $hattr['hid'];
    	$ret = FormationLogic::setCurFormation(2, array(0 => '0', 1 => $hid, 2 => '0',
		                                                3 => '0', 4 => '0', 5 => '0',
		                                                6 => '0', 7 => '0', 8 => '0'));

        $userObj = EnUser::getUserObj();
        $userObj->addExp(10000000);
        $userObj->update();
        
        $vsl = new Vassal();
        $ret = $vsl->conquer($this->otherUid);

        var_dump($ret);

        //这里走vassal更新数据，所以没法判断
        /* $ret = VassalDao::getVslData($this->uid, $this->otherUid, array('status')); */
        /* var_dump($ret); */
        /* $this->assertEquals(VassalDef::STATUS_RELIEVE, $ret['status']); */
    }


    /**
     * @group getInfoByUid
     */
    public function test_getInfoByUid_0()
    {
        $this->makeVsl();
        $this->makeMst();

        $vsl = new Vassal();
        $ret = $vsl->getInfoByUid($this->uid);
        $this->assertArrayHasKey('user', $ret);
        $this->assertArrayHasKey('vassal', $ret);
        $this->assertArrayHasKey('master', $ret);

        var_dump($ret['user']['msg']);
        var_dump(mb_strlen($ret['user']['msg'], 'utf-8'));


        var_dump($ret);
    }


    


    




}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */