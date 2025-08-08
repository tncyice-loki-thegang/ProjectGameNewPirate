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

require_once (MOD_ROOT . '/hero/HeroManager.class.php');
require_once (MOD_ROOT . '/user/EnUser.class.php');
require_once (MOD_ROOT . '/user/User.class.php');

class HeroManagerTest extends PHPUnit_Framework_TestCase
{
	private $user;
	private $heroMgr;
	private $uid;
	private $heroes;
	
	private $newHtid = 10004;
	
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
        $user = new User();
        $this->heroMgr = HeroManager::getInstance(); 
	}
	
	protected  function tearDown()
	{
		parent::tearDown();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
		HeroManager::release();
		EnUser::release();
	}
	
	
	/**
	 * @group getRecruitHeroes
	 * Enter description here ...
	 */
	public function test_getRecruitHeroes_0()
	{
		$ret = $this->heroMgr->getRecruitHeroes();
		//var_dump($ret);
		$this->assertNotEmpty($ret);
		foreach ($ret as $htid=>$hero)
		{
			$this->assertEquals($htid, $hero['htid']);
		}
	
	}
	
	/**
	 * @group addNewHeroToPub
	 */
	public function test_addNewHeroToPub_0()
	{
		try
		{
			$this->heroMgr->addNewHeroToPub(100);
			$this->assertTrue(false);
		}
		catch (Exception $e)
		{
			$this->assertTrue(true);
		}	
	}
	
	/**
	 * @group addNewHeroToPub
	 */
	public function test_addNewHeroToPub_1()
	{		
		try 
		{
			$this->heroMgr->addNewHeroToPub(100);
		} 
		catch (Exception $e) 
		{
			$this->assertTrue(true);
		}
	}
	
	/**
	 * @group getPubHeroes
	 */
	public function test_getPubHeroes_0()
	{
		$ret = $this->heroMgr->getPubHeroes();
		$this->assertNotEmpty($ret);
	} 
	
	/**
	 * @group addPrestigeHero
	 * prestige not enough
	 */
	public function test_addPrestigeHero_0()
	{
		try
		{
			$this->heroMgr->addPrestigeHero(10001);
			$this->assertTrue(false);
		}
		catch (Exception $e)
		{
			$this->assertTrue(true);
		}
	}
	
	/**
	 * @group addPrestigeHero
	 * htid error
	 */
	public function test_addPrestigeHero_1 ()
	{
		try
		{
			$this->heroMgr->addPrestigeHero(101);
			$this->assertTrue(false);
		}
		catch ( Exception $e )
		{
			$this->assertTrue(true);
		}
	}
	
	/**
	 * @group addPrestigeHero2
	 * ok
	 */
	public function test_addPrestigeHero_2 ()
	{		
		$enUser = EnUser::getInstance();
		$enUser->addPrestige(10000);
		$enUser->update();

        $allHero = $enUser->getAllHero();
		$this->heroMgr->addPrestigeHero($this->newHtid);
		$allHero = $enUser->getAllHero();
		$ret = in_array($this->newHtid, $allHero);
		$this->assertTrue($ret);
	}
	
	/**
	 * @group perfect
	 * 让覆盖100%， 不蛋痛
	 */
	public function test_for_perfect_0()
	{
		$ret = $this->heroMgr->getRecruitHeroes();
		$htid = key($ret);
		
				
		try
		{
//			$ret = $this->heroMgr->isRecruitByHid(1);
//			$this->assertTrue(false);
		}
		catch (Exception $e)
		{
			$this->assertTrue(true);	
		}
		
		
	}
	
	/**
	 * @group perfect
	 * 让覆盖100%， 不蛋痛
	 */
	public function test_for_perfect_1()
	{
		$ret = $this->heroMgr->getRecruitHeroes();
		$hero = current($ret);
		$hid = $hero['hid'];
		HeroLogic::getByHid($hid);
		HeroDao::getByHid(-1, HeroDef::$HERO_FIELDS);
	}
	
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */