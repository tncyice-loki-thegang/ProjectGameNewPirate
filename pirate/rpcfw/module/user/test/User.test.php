<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: User.test.php 17910 2012-04-01 07:09:46Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/test/User.test.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-04-01 15:09:46 +0800 (日, 2012-04-01) $
 * @version $Revision: 17910 $
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
		$arrReq = array('timestamp' => "1333263137", 
		'pid' => "759472693aab8163", 'ptype' => "0", 
		'hash' => "2d72c5aa77c5c6049492f21c3477aa38", 'host' => "192.168.1.221", 'port' => "7777");
		
		$user = new User();
		$ret = $user->login($arrReq);
		var_dump($ret);		
	}
	
}

?>