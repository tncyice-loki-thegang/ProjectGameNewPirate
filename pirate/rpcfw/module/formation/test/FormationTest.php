<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/formation/index.php');
require_once (MOD_ROOT . '/user/index.php');

class FormationLogicTest extends PHPUnit_Framework_TestCase
{
	//private $user;
	//private $hero;
	private $uid;
	private $form;
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
		$this->form = new Formation();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown ();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
	}
	
	/**
	 * @group getAllFormation
	 */
	public function test_getAllFormation()
	{
		$res = $this->form->getAllFormation();
		var_dump($res);
	}
	
	/**
	 * @group update 
	 */
	public function test_update()
	{
		$res = $this->form->getAllFormation();
		$fid = 1;
		$arr = $res[1][1];
		unset($arr['level']);
		unset($arr['fid']);
		//var_dump($arr);
		$this->form->update($fid, $arr);
		$this->assertTrue(true);
		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */