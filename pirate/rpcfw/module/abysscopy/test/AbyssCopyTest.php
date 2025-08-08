<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AbyssCopyTest.php 40917 2013-03-18 11:20:13Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/test/AbyssCopyTest.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-18 19:20:13 +0800 (一, 2013-03-18) $
 * @version $Revision: 40917 $
 * @brief 
 *  
 **/


require_once (MOD_ROOT . '/abysscopy/AbyssCopy.class.php');
require_once (MOD_ROOT . '/abysscopy/AbyssCopyDAO.class.php');
require_once (MOD_ROOT . '/abysscopy/MyAbyssCopy.class.php');

class AbyssCopyTest extends PHPUnit_Framework_TestCase
{
	private $uid = 23769;
	
	/**
	 * 
	 * @var AbyssCopy
	 */
	private $abyssObj = NULL;
	
	protected function setUp()
	{
		parent::setUp();
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		
		$this->abyssObj = new AbyssCopy();
	}
	
	protected function tearDown()
	{
		unset( $this->abyssObj );
		$this->abyssObj = NULL;
	}
	
	
	
	public function test_week_refresh()
	{
		Logger::info('test_week_refresh');
		
		$rightInfo = self::unsetUser($this->uid);
		$userConf = AbyssCopy::getUserConf();
		$copyId = 1;
	
		//初始
		$ret = $this->getUserInfo();
		$this->assertEquals($rightInfo, $ret);
		
		//买一次数		
		$ret = $this->abyssObj->buyChallengeNum(2);
		$rightInfo['weekBuyNum'] = 2;
		$rightInfo['weekClgNum'] += 2;
		$ret = $this->getUserInfo();
		$this->assertEquals($rightInfo, $ret);
		
		//用掉一些
		for($i = 0; $i < $rightInfo['weekClgNum']-3; $i++)
		{
			$this->passCopy($copyId);
		}
		$rightInfo['passed'] = array($copyId);
		
		//累计
		$lastEnterTime = Util::getTime();
		$rightInfo['weekClgNum'] = 3;
		while(true)
		{
			$lastEnterTime -= 86400;
			self::setUserInfo($this->uid, array('last_enter_time' => $lastEnterTime));
			//$rightInfo['weekClgNum'] = 3 + $userConf['baseChallengeNum'];
			//$rightInfo['weekBuyNum'] = 0;
			$ret = $this->getUserInfo();
			$weekDay = date('l', $lastEnterTime);
			if($ret['weekClgNum'] == $rightInfo['weekClgNum'])
			{
				echo $weekDay." not fresh\n";
			}
			else
			{
				echo $weekDay." fresh\n";
				$rightInfo['weekClgNum'] = 3 + $userConf['baseChallengeNum'];
				$rightInfo['weekBuyNum'] = 0;
				$this->assertEquals($rightInfo, $ret);
				break;
			}
		}
		
		
		$this->assertEquals('Friday', $weekDay);
		
		
		//不能累计超过上限
		$this->passCopy($copyId);
		self::setUserInfo($this->uid, array('last_enter_time' => Util::getTime()-86400*14));
		$rightInfo['weekClgNum'] = $userConf['maxChallengeNum'];
		$rightInfo['weekBuyNum'] = 0;
		$ret = $this->getUserInfo();
		$this->assertEquals($rightInfo, $ret);

	}
	
	
	public function test_buy_num()
	{
		Logger::info('test_buy_num');
	
		$rightInfo = self::unsetUser($this->uid);
		$userConf = AbyssCopy::getUserConf();
		$copyId = 1;
	
		//初始
		$ret = $this->getUserInfo();
		$this->assertEquals($rightInfo, $ret);
	

		//购买次数到上限
		$rightInfo['weekClgNum'] = $userConf['maxChallengeNum']-3;
		$rightInfo['weekBuyNum'] = 2;

		self::setUserInfo($this->uid, array('left_clg_num' => $rightInfo['weekClgNum'], 'last_enter_time'=>Util::getTime()));
		
		$ret = $this->abyssObj->buyChallengeNum($rightInfo['weekBuyNum']);		
		$this->assertEquals('ok', $ret['ret']);
		
		$rightInfo['weekClgNum'] += $rightInfo['weekBuyNum'];
		$ret = $this->getUserInfo();
		$this->assertEquals($rightInfo, $ret);

		//不能超过上限
		try
		{
			$ret = $this->abyssObj->buyChallengeNum(2);
			$this->assertTrue(0);
		}
		catch ( Exception $e )
		{
			$this->assertEquals( 'fake',  $e->getMessage());
		}
		$ret = $this->getUserInfo();
		$this->assertEquals($rightInfo, $ret);

	}
	
	public function test_no_num()
	{
		Logger::info('test_no_num');
	
		$rightInfo = self::unsetUser($this->uid);
		$userConf = AbyssCopy::getUserConf();
		$copyId = 1;
	
		//初始
		$ret = $this->getUserInfo();
		$this->assertEquals($rightInfo, $ret);
		
		
		//用完次数不让进了
		for($i = 0; $i < $rightInfo['weekClgNum'] + $rightInfo['weekExeNum']; $i++)
		{
			$this->passCopy($copyId);
		}
		try
		{
			$ret = $this->abyssObj->create($copyId, true, 1);
			$this->assertTrue(0);
		}
		catch ( Exception $e )
		{
			$this->assertEquals( 'fake',  $e->getMessage());
		}
	}
	
	
	public function getUserInfo()
	{
		$myAbyss = new MyAbyssCopy($this->uid);		
		return $myAbyss->getInfo();
	}
	public function passCopy($copyId)
	{
		$myAbyss = new MyAbyssCopy($this->uid);
		$myAbyss->passCopy($copyId);
		$myAbyss->update();
	}
	public static function setUserInfo($uid, $values)
	{
		EnAbyssCopy::clearCache();
		AbyssCopyDAO::update($uid, $values);
	}
	
	public static function unsetUser($uid)
	{
		$values = array(
				'uid' => $uid,
				'week_buy_num' => 0 ,
				'left_clg_num' => 0,
				'left_exe_num' => 0,
				'last_enter_time' => Util::getTime()-86400*7,
				'cur_copy_uuid' => 0,
				'va_abyss' => array(
						'passed' => array()
				)
		);
	
		AbyssCopyDAO::update($uid, $values);
		
		EnAbyssCopy::clearCache();
		
		$userConf = AbyssCopy::getUserConf();
		$rightInfo = array(
				'weekBuyNum' => 0,
				'weekClgNum' => $userConf['baseChallengeNum'],
				'weekExeNum' => $userConf['baseExerciseNum'],
				'curCopyUUID' => 0,
				'passed' => array(),
		);
		return $rightInfo;
	}
	
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */