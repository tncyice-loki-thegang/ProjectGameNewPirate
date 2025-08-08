<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: UserLogicTest.php 15476 2012-03-02 04:29:08Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/test/UserLogicTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-02 12:29:08 +0800 (五, 2012-03-02) $
 * @version $Revision: 15476 $
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/user/index.php');
require_once (MOD_ROOT . '/hero/index.php');

class UserLogicTest extends PHPUnit_Framework_TestCase {

	private $pid ;
    private $utid ;
	private $uname ;
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();
		$this->user = new UserLogic(/* parameters */);
        $this->pid = 40000 + rand(0,9999);
        $this->utid = 1;
		$this->uname = 't' . $this->pid;
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{

		$this->user = null;
		parent::tearDown ();
	}

	function __construct() {
	
	}
	
	/**
	 * 
	 */
	function __destruct() {
	
	}
	
	/**
	 * @group login
	 */
	public function test_Login_0()
	{
		$ret = $this->user->login('i0ife75v80bvl845l2sdjrl957', '192.168.1.44');
        $this->assertSame ('', $ret, "return:$ret");
	}
	
	/**
	 * @group createUser
	 * check uname
	 */
	public function test_createUser_1()
	{
		$this->uname = '六四';
		$ret = $this->user->createUser($this->pid, $this->utid, $this->uname);
		$this->assertTrue($ret!='ok', "createUser return $ret");
	}
	
	/**
	 * @group createUser 
	 * check utid
	 */
	public function test_createUser_2()
	{
		$this->utid = 100;
		try 
		{
			$ret = $this->user->createUser($this->pid, $this->utid, $this->uname);
			$this->assertTrue(false);	
		}
		catch (Exception $e)
		{
			$this->assertSame('fake',$e->getMessage());
		}
		
	}

	/**
	 * @group createUser
	 * check max user num
	 */
    public function test_createUser_3()
	{
		for ($i=0; $i<UserConf::MAX_USER_NUM; ++$i)
		{
			$ret = $this->user->createUser($this->pid, $this->utid, $this->uname . $i);
		}
		
		try 
		{
			$ret = $this->user->createUser($this->pid, $this->utid, $this->uname . $i);
			$this->assertTrue(false);
		}
		catch (Exception $e)
		{            
			$this->assertSame('fake',$e->getMessage());
		}
	}

	/**
	 * @group createUser 
	 * check name_used
	 */
    public function test_createUser_4()
    {
        $ret = $this->user->createUser($this->pid, $this->utid, $this->uname);
        $ret = $this->user->createUser($this->pid, $this->utid, $this->uname);
        $this->assertTrue($ret=='name_used', "return:$ret");
    }

    /**
     * @group createUser 
     * check suc 
     */
    public function test_createUser_5()
    {
        $ret = $this->user->createUser($this->pid, $this->utid, $this->uname);
        $this->assertSame('ok', $ret, "return:$ret");
    }

    /**
     * @group getUsers
     * Enter description here ...
     */
    public function test_getUsers_0( ) 
    {
        $this->user->createUser($this->pid, $this->utid, $this->uname);
        $ret = $this->user->getUsers($this->pid);
        $this->assertSame(1, count($ret));
        $this->assertArrayHasKey('uid', $ret[0]);
        $this->assertArrayHasKey('utid', $ret[0]);
        $this->assertArrayHasKey('uname', $ret[0]);
        $this->assertArrayHasKey('level', $ret[0]);
        $this->assertArrayHasKey('dtime', $ret[0]);
    }


    /**
     * @group delUser
     * Enter description here ...
     */
    public function test_delUser_0()
    {
        $this->user->createUser($this->pid, $this->utid, $this->uname);
        $users = $this->user->getUsers($this->pid);
        $uid = $users[0]['uid'];
        $ret = $this->user->delUser($this->pid, $uid);
        $this->assertSame($ret, 'ok', "return:$ret");
        //$this->assertTrue(false, "check tid for pid:$this->pid");
    }
    
    /**
     * @group delUser
     * Enter description here ...
     */
	public function test_delUser_1()
    {
        $this->user->createUser($this->pid, $this->utid, $this->uname);
        $users = $this->user->getUsers($this->pid);
        $uid = $users[0]['uid'];
        $ret = $this->user->delUser($this->pid, $uid);
        $this->assertSame($ret, 'ok', "return:$ret");
        $ret = $this->user->delUser($this->pid, $uid);
        $this->assertSame($ret, 'fake', "return:$ret");
        
        //$this->assertTrue(false, "check tid for pid:$this->pid");
    }

    /**
     * @group cancelDel
     * Enter description here ...
     */
    public function test_cancelDel_0()
    {
        $this->user->createUser($this->pid, $this->utid, $this->uname);
        $users = $this->user->getUsers($this->pid);
        $uid = $users[0]['uid'];
        $this->user->delUser($this->pid, $uid);
        $ret = $this->user->cancelDel($uid);
        $this->assertSame($ret, 'ok', "return:$ret");
        //$this->assertTrue(false, "check, no tid for pid:$this->pid");
    }

    /**
     * @group clearUser
     * Enter description here ...
     */
    public function test_clearUser_0()
    {
        $this->user->createUser($this->pid, $this->utid, $this->uname);
        $users = $this->user->getUsers($this->pid);
        $uid = $users[0]['uid'];
        //$this->user::clearUser($uid);
        $ret = UserLogic::clearUser($uid);
        $this->assertSame('fail',$ret, "return:$ret");
    }

    /**
     * @group clearUser
     * Enter description here ...
     */
    public function test_clearUser_1()
    {
        $this->user->createUser($this->pid, $this->utid, $this->uname);
        $users = $this->user->getUsers($this->pid);
        $uid = $users[0]['uid'];
        $this->user->delUser($this->pid, $uid);
        $ret = UserLogic::clearUser($uid);
        $this->assertSame('ok',$ret, "return:$ret");
    }
    
    /**
     * @group getRandomName
     * Enter description here ...
     */
    public function test_getRandomName()
    {
    	$ret = $this->user->getRandomName(10);
//        var_dump($ret);
    	$this->assertSame(10, count($ret));
    	$ret = $this->user->getRandomName(25);
//    	var_dump($ret);
    	$this->assertSame(20, count($ret));
   	//var_dump($ret);

    }
    
    /**
     * @group userLogin
     */
    public function test_userLogin_0()
    {
    	$this->user->createUser($this->pid, $this->utid, $this->uname);
        $users = $this->user->getUsers($this->pid);
        $uid = $users[0]['uid'];
        $ret = $this->user->userLogin($uid, $this->pid);
        $this->assertSame('ok', $ret[0]);
    }
    
    
     /**
     * @group getUser
     * Enter description here ...
     */
    public function test_getUser_0( ) 
    {
        $this->user->createUser($this->pid, $this->utid, $this->uname);
        $users = $this->user->getUsers($this->pid);
        $uid = $users[0]['uid'];
        $ret = $this->user->getUser($uid);
        $this->assertArrayHasKey('utid', $ret);
        $this->assertArrayHasKey('uname', $ret);
        $this->assertArrayHasKey('level', $ret);
        $this->assertArrayHasKey('recruit_num', $ret);
        //var_dump($ret);
    }
}

?>