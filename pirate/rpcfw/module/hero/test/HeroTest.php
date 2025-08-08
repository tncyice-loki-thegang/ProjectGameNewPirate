<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/test/HeroTest.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

require_once MOD_ROOT . '/hero/index.php';

class HeroTest extends PHPUnit_Framework_TestCase
{
	protected function setUp() 
	{
		parent::setUp ();
		$this->user = new UserLogic(/* parameters */);
		$this->hero = new HeroLogic();
        $this->pid = 40000 + rand(0,9999);
        $this->utid = 1;
		$this->uname = 't' . $this->pid;
		
		
		$this->user->createUser($this->pid, $this->utid, $this->uname);
    	$users = $this->user->getUsers($this->pid);
        $this->uid = $users[0]['uid'];
        RPCContext::getInstance()->setSession('global.uid', $this->uid); 
        
        $this->hero = new Hero();
	}
	
	protected  function tearDown()
	{
		parent::tearDown();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
		EnUser::release();
	}

	public function test_getPubHeroes()
	{
		$ret = $this->hero->getPubHeroes();
		var_dump($ret);
	}
	
	public function test_getRecruitHeroes()
	{
		$ret = $this->hero->getRecruitHeroes();
		var_dump($ret);
	}
	
	public function test_getMasterHeroProperty()
	{
		$ret = $this->hero->getMasterHeroProperty();
		var_dump($ret);
	}
	
	public function test_masterTransfer()
	{
		$user = EnUser::getUserObj();
		$user->getMasterHeroObj()->addExp(400000);
		$user->update();
		$user->addExperience(20000);
		$ret = $this->hero->masterTransfer();
		var_dump($ret);
	}
	
	public function test_masterLearnSkill()
	{
		$ret = $this->hero->masterLearnSkill(258);
		var_dump($ret);
	}	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */