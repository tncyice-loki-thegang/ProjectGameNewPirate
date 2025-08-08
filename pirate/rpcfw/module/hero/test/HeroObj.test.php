<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroObj.test.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/test/HeroObj.test.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/user/User.class.php');


class HeroObjTest extends PHPUnit_Framework_TestCase
{

	private $user;
	private $allRecruitHero;
	private $htid;
	private $hid;
	private $newHtid = 10004;
	
	protected function setUp() 
	{
		parent::setUp ();
		$this->pid = 40000 + rand(0,9999);
      	$this->utid = 1;
		$this->uname = 't' . $this->pid;
		
		UserLogic::createUser($this->pid, $this->utid, $this->uname);
		$users = UserLogic::getUsers($this->pid);
		$uid = $users[0]['uid'];
		$this->uid = $uid;
		RPCContext::getInstance()->setSession('global.uid', $uid);
		$this->heroMgr = HeroManager::getInstance();
		$this->allRecruitHero = $this->heroMgr->getRecruitHeroes();
		$this->assertNotEmpty($this->allRecruitHero);
		$this->htid = key($this->allRecruitHero);
		$this->hid = $this->allRecruitHero[$this->htid]['hid'];
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown ();
		HeroManager::getInstance()->release();
		EnUser::release();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
	}

	/**
	 * @group getInfo
	 */
	public function test_getInfo_0()
	{
		//$info = $this->enHero->getInfo();
		//var_dump($info);
		//$this->assertArrayHasKey('maxHp', $info);

		$arrRecruitHeroes = HeroManager::getInstance()->getRecruitHeroes();
		$htid = 10001;
		$this->assertNotEmpty($arrRecruitHeroes);
		$this->assertArrayHasKey($htid, $arrRecruitHeroes);
				
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($htid);
		$res = $enHero->getInfo();
		$this->assertArrayHasKey('arrSkill', $res);
		//var_dump($res);
	}
	
	/**
	 * @group addSkill
	 */
	public function test_addskill_0()
	{
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$enHero->addSkill(2);
		$enHero->update();

		$res = HeroLogic::getByHid($this->hid);
		$skill = $res['va_hero']['skill'];
//		var_dump($daimonApple);
		$in = in_array(2, $skill);
		$this->assertSame(true, $in);	
	}
	
	/**
	 * @group removeSkill
	 */
	public function test_removeSkill_0()
	{
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$enHero->addSkill(2);
		$enHero->update();
		
		$enHero->removeSkill(2);
		$enHero->update();
		$res = HeroLogic::getByHid($this->hid);
		$skill = $res['va_hero']['skill'];
		$in = in_array(2, $skill);
		$this->assertSame(false, $in);
	}
	
	/**
	 * @group setHp
	 */
	public function test_setHp_0()
	{
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$enHero->setHp(191);
		$enHero->update();
		
		$res = HeroLogic::getByHid($this->hid);
		$this->assertEquals(191, $res['curHp']);
	}
	
	/**
	 * @group addHp
	 */
	public function test_addHp_0()
	{
		$user = EnUser::getInstance();
		$user->addBloodPackage(1000);
		$user->update();
		
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$resNum = $enHero->addHp(191);
		$enHero->update();
		
		$res = HeroLogic::getByHid($this->hid);
		$this->assertEquals(191, $res['curHp']);

		
	}
	
	/**
	 * @group addHp
	 * 加血， 但是血池量不够
	 */
	public function test_addHp_1()
	{
		$user = EnUser::getInstance();
		$userInfo = EnUser::getUser();
		$user->addBloodPackage(100-$userInfo['blood_package']);
		$user->update();
		
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$enHero->setHp(0);
		$resNum = $enHero->addHp(191);
		$enHero->update();
		
		$res = HeroLogic::getByHid($this->hid);
		$this->assertEquals(100, $res['curHp']);

		$user = UserLogic::getUser($this->uid);
		$this->assertEquals(0,$user['blood_package']);
	
	}
	
	/**
	 * @group addHp
	 * 已经到最大的血了
	 */
	public function test_addHp_2()
	{
		$user = EnUser::getInstance();
		$user->addBloodPackage(10000);
		$user->update();
		
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$resNum = $enHero->addHp(20000);
		$enHero->update();
		$resNum = $enHero->addHp(20000);
		$this->assertEquals(0, $resNum);
		
		$res = HeroLogic::getByHid($this->hid);
		$this->assertEquals($enHero->getMaxHp(), $res['curHp']);

		
	}
	
	/**
	 * @group setToMaxHp
	 */
	public function test_setToMaxHp_0()
	{
		$user = EnUser::getInstance();
		$user->addBloodPackage(1000);
		$user->update();
		
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$resNum = $enHero->setToMaxHp();
		$enHero->update();
		
		$res = HeroLogic::getByHid($this->hid);
		$this->assertEquals($enHero->getMaxHp(), $res['curHp']);
	}
	
	/**
	 * @group setToMaxHp
	 * 血池不够
	 */
	public function test_addToMaxHp_1()
	{
		$user = EnUser::getUser();
		$enUser = EnUser::getInstance();
		$enUser->addBloodPackage(1-$user['blood_package']);
		$enUser->update();
		
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$resNum = $enHero->setToMaxHp();
		$this->assertFalse($resNum);
		
		
	}
	
	/**
	 * @group addExp
	 */
	public function test_addExp_0()
	{
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$oldLevel = $enHero->getLevel();
		$allAttr = $enHero->getAllAttr(); 
		$oldExp = $allAttr['exp'];

		$enHero->addExp(1000);
		$enHero->update();
		
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$level = $enHero->getLevel();
		$allAttr = $enHero->getAllAttr(); 
		$exp = $allAttr['exp'];
		
		$this->assertEquals(5, $level);
		$this->assertEquals(0, $exp);
	}
	
	/**
	 * @group addExp
	 */
	public function test_addExp_1()
	{
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$oldLevel = $enHero->getLevel();
		$allAttr = $enHero->getAllAttr(); 
		$oldExp = $allAttr['exp'];
		
		$enHero->addExp(100000000);
		$enHero->update();
		
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$enHero->addExp(100000000);
		$level = $enHero->getLevel();
		$allAttr = $enHero->getAllAttr(); 
		$exp = $allAttr['exp'];
		
		$this->assertEquals(HeroConf::MAX_LEVEL, $level);
	}
	
	/**
	 * @group isPub
	 * Enter description here ...
	 */
	public function test_isPub_0()
	{
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$ret = $enHero->isPub();
		$this->assertFalse($ret);
		
		$enHero = HeroManager::getInstance()->getHeroObjByHtid(10003);
		$ret = $enHero->isPub();
		$this->assertFalse(!$ret);
	}
	
	/**
	 * @group rebirth
	 * 刚到转生的等级经验
	 */
	public function test_rebirth_0()
	{
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$enHero->addExp(177000);
		$enHero->update();
		$enHero->rebirth();
		$ret = $enHero->getAllAttr();
		$this->assertEquals(1, $ret['rebirthNum']);
		$this->assertEquals(0, $ret['exp']);
		$this->assertEquals(1, $ret['level']);
		
	}
	
	/**
	 * @group rebirth
	 * 经验超出
	 */
	public function test_rebirth_1()
	{
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$enHero->addExp(177000);
		$enHero->addExp(14);
		$enHero->update();
		$enHero->rebirth();
		$ret = $enHero->getAllAttr();
		
		$this->assertEquals(1, $ret['rebirthNum']);
		$this->assertEquals(14, $ret['exp']);
		$this->assertEquals(1, $ret['level']);
	}
	
	/**
	 * @group rebirth2
	 * 等级经验超出，
	 */
	public function test_rebirth_2()
	{
		
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$enHero->addExp(177000);
		$enHero->addExp(1005);
		$enHero->update();
		$enHero->rebirth();
		$ret = $enHero->getAllAttr();

		$this->assertEquals(1, $ret['rebirthNum']);
		$this->assertEquals(5, $ret['exp']);
		$this->assertEquals(5, $ret['level']);
	}
	
	/**
	 * @group addExp
	 * 等级经验超出，
	 */
	public function test_addExp_9()
	{
		
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$enHero->addExp(10000);
		$enHero->update();
		$ret = $enHero->getAllAttr();

		$this->assertEquals(0, $ret['rebirthNum']);
		$this->assertEquals(900, $ret['exp']);
		$this->assertEquals(14, $ret['level']);
	}
	
	/**
	 * @group rebirth2
	 * 等级经验超出，
	 */
	public function test_rebirth_3()
	{
		
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$enHero->addExp(177000);
		$enHero->addExp(10000);
		$enHero->update();
		$enHero->rebirth();
		$ret = $enHero->getAllAttr();

		$this->assertEquals(1, $ret['rebirthNum']);
		$this->assertEquals(900, $ret['exp']);
		$this->assertEquals(14, $ret['level']);
	}
	
	/**
	 * @group rebirth
	 * 酒馆英雄
	 */
	public function test_rebirth_4()
	{
		
		try 
		{
			$enHero = HeroManager::getInstance()->getHeroObjByHtid(10003);
			$enHero->rebirth($this->htid);
			$this->assertTrue(false);
		}
		catch (Exception $e)
		{
			$this->assertTrue(true);
		}
	}
	
	/**
	 * @group rebirth
	 * 等级不够
	 */
	public function test_rebirth_5()
	{
		
		try 
		{
			$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
			$enHero->rebirth();
			$this->assertTrue(false);
		}
		catch (Exception $e)
		{
			$this->assertTrue(true);
		}
	}
	
	
	/**
	 * @group recruit
	 */
	public function test_recruit_0()
	{
		$this->heroMgr->addNewHeroToPub($this->newHtid);
		
		//需要点钱
		$enUser = EnUser::getInstance();
        $enUser->addBelly(10000);
        $enUser->update();
        
        $enHero = $this->heroMgr->getHeroObjByHtid($this->newHtid);
        $enHero->recruit();
		$ret = $enHero->isRecruit();
		$this->assertTrue($ret);
	}
	
	/**
	 * @group recruit
	 * 超过能招募的最大数量
	 */
	public function test_recruit_1()
	{		
		
		//需要点钱
		$enUser = EnUser::getInstance();
        $enUser->addBelly(10000);
        $enUser->update();
        
        $curNum = count($this->heroMgr->getRecruitHeroes());
        
        for($i=0; $i<EnUser::getInstance()->getCanRecruitHeroNum()-$curNum; ++$i)
        {
        	$htid = $this->newHtid + $i;
        	$this->heroMgr->addNewHeroToPub($htid);
        	$enHero = $this->heroMgr->getHeroObjByHtid($htid);
        	$enHero->recruit();
        }
        
       
        $htid += 1;
        $this->heroMgr->addNewHeroToPub($htid);
        var_dump($htid);
		$enHero = $this->heroMgr->getHeroObjByHtid($htid);
		try
        {
			$enHero->recruit();
			$this->assertTrue(false);
        }
        catch (Exception $e)
        {
        	$this->assertTrue(true);
        }		
	}
	
	/**
	 * @group recruit
	 * 不能招募
	 */
	public function test_recruit_2()
	{
		//需要点钱
		$enUser = EnUser::getInstance();
        $enUser->addBelly(10000);
        $enUser->update();
        
        try
        {
			$enHero = $this->heroMgr->getHeroObjByHtid($this->htid);
			$enHero->recruit();
			$this->assertTrue(false);
        }
        catch (Exception $e)
        {
        	$this->assertTrue(true);
        }		
	}
	
	
	/**
	 * @group recruit
	 * 钱不够
	 */
	public function test_recruit_3()
	{
		$this->heroMgr->addNewHeroToPub($this->newHtid);
		
		//需要点钱
		$user = EnUser::getUser();
		$enUser = EnUser::getInstance();
        $enUser->addBelly(-$user['belly_num']);
        $enUser->update();
        
        $enHero = $this->heroMgr->getHeroObjByHtid($this->newHtid);
        
		try
		{
			$enHero->recruit();
			$this->assertTrue(false);
        }
        catch (Exception $e)
        {
        	$this->assertTrue(true);
        }		
	}
	
	/**
	 * @group recruit
	 * fire 再recruit
	 */
	public function test_recruit_4()
	{
		$this->heroMgr->addNewHeroToPub($this->newHtid);
		
		//需要点钱
		$enUser = EnUser::getInstance();
        $enUser->addBelly(10000);
        $enUser->update();
        
        $enHero = $this->heroMgr->getHeroObjByHtid($this->newHtid);
        $enHero->recruit();
		$ret = $enHero->isRecruit();
		$this->assertTrue($ret);
		
		$enHero->fire();
		$enHero->recruit();
		$ret = $enHero->isRecruit();
		$this->assertTrue($ret);
	}
	
	
	/**
	 * @group fire
	 */
	public function test_fire_0()
	{
		$enHero = $this->heroMgr->getHeroObjByHtid($this->htid);
		$enHero->fire();
		$ret = $enHero->isRecruit();
		$this->assertFalse($ret);
	}
	
	/**
	 * @group fire
	 */
	public function test_fire_1()
	{
		$enHero = $this->heroMgr->getHeroObjByHtid($this->htid);
		$enHero->fire();
		$ret = $enHero->isRecruit();
		$this->assertFalse($ret);
		
		try
		{
			$enHero->fire();
			$this->assertTrue(false);
		}
		catch (Exception $e)
		{
			$this->assertTrue(true);
		}
	}
	
	/**
	 * @group getHid
	 * Enter description here ...
	 */
	public function test_getHid_0()
	{
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$ret = $enHero->getHid();
		$this->assertNotEquals(0, $ret);
	}
	
	
	/**
	 * @group perfect
	 * just for perfect。不蛋痛
	 */
	public function test_perfect_0()
	{
		$enHero = HeroManager::getInstance()->getHeroObjByHtid($this->htid);
		$ret = $enHero->getVocation();
		//$this->assertNotEquals(0, $ret);
	}
	
	
	
	
	
	
	
	
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */