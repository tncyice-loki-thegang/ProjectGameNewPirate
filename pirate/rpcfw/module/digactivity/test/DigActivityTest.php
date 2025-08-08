<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: DigActivityTest.php 37665 2013-01-30 11:08:05Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/digactivity/test/DigActivityTest.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-01-30 19:08:05 +0800 (三, 2013-01-30) $
 * @version $Revision: 37665 $
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/digactivity/DigActivity.class.php');
require_once (MOD_ROOT . '/digactivity/DigActivityDAO.php');

class DigActivityTest extends PHPUnit_Framework_TestCase
{
	private $uid = 23769;
	
	protected function setUp() 
	{
		parent::setUp ();
		RPCContext::getInstance()->setSession('global.uid', $this->uid);		
	}
	
	protected function tearDown()
	{
	}
	
	
	public function dig($type, $batchNum)
	{
		$digActivity = new DigActivity();
		return $digActivity->dig($type, $batchNum);
	}
	public function getUserInfo()
	{
		$digActivity = new DigActivity();
		$ret = $digActivity->getAllInfo();
		Logger::info('userInfo:%s', $ret);
		return $ret;
	}
	public function getConf()
	{		
		$digActivity = new DigActivity();
		return $digActivity->getConf();	
	}
	
	
	public function test_drop()
	{

		$conf = self::getConf();
		$type = $conf['type'];
		
		self::resetUser($this->uid);
		$values = array(
				'last_dig_time' => time(),
				'free_num' => 3);
		
		DigActivityDAO::update($this->uid, $values);
		
		
		for($i = 0; $i < 3; $i++)
		{
			$ret = $this->dig($type, 1);
			$info = $this->getUserInfo();
			$this->assertEquals( 0, $info['today_gold_dig']);
			$this->assertEquals( 0, $info['all_dig_num']);
			//self::printArray($info);
		}
		
		for($i = 0; $i < 50; $i++)
		{
			$ret = $this->dig($type, 1);
			$info = $this->getUserInfo();
			$this->assertEquals( $i+1, $info['today_gold_dig']);
			$this->assertEquals( $i+1, $info['all_dig_num']);
			self::printArray($info);
		}
		//exit(0);
	}

	//每天更新
	public function test_day_refresh()
	{
		Logger::info('test_day_refresh');
		$conf = self::getConf();
		$type = $conf['type'];
		
		self::resetUser($this->uid);
		
		
		$preInfo = $this->getUserInfo($this->uid);	
		Logger::debug('preInfo:%s', $preInfo);		

		$ret = $this->dig($type, $preInfo['free_num'] - 1);
		$this->assertTrue( isset($ret['grid'])  );
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( 1, $ret['free_num']);
		$this->assertEquals( 0, $ret['all_dig_num']);
		
		
		$values = array(				
				'last_dig_time' => time()-86400);
		DigActivityDAO::update($this->uid, $values);
		
		unset($digActivity);
		
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( $conf['freeNum'], $ret['free_num']);
		$this->assertEquals( 0, $ret['today_accum_dig']);
		$this->assertEquals( 0, $ret['today_gold_dig']);
		
	
		$ret = $this->dig($type, $conf['freeNum']+1);
		$this->assertTrue( isset($ret['grid'])  );
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( 0, $ret['free_num']);	
		$this->assertEquals( 1, $ret['today_accum_dig']+$ret['today_gold_dig']);
		$this->assertEquals( 1, $ret['all_dig_num']);
		
	}
	
	//每次活动之前更新
	public function tes1t_activity_refresh()
	{
		$conf = self::getConf();
		$type = $conf['type'];
		
		self::resetUser($this->uid);
		
		
		$preInfo = $this->getUserInfo($this->uid);
		
		$ret = $this->dig($type, $preInfo['free_num'] - 1);
		$this->assertTrue( isset($ret['grid'])  );
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( 1, $ret['free_num']);
		$this->assertEquals( $preInfo['free_num']-1, $ret['all_dig_num']);
		
		
		$values = array(
				'last_dig_time' => 0);
		DigActivityDAO::update($this->uid, $values);
		
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( $conf['freeNum'], $ret['free_num']);
		$this->assertEquals( 0, $ret['today_accum_dig']);
		$this->assertEquals( 0, $ret['today_gold_dig']);
		$this->assertEquals( 0, $ret['all_dig_num']);
		$this->assertEquals( 0, $ret['drop_b_num']);
		$this->assertEquals( 0, $ret['used_pay_gold']);
		$this->assertEquals( 0, $ret['used_spend_gold']);
		
		
		$ret = $this->dig($type, $conf['freeNum']+1);
		$this->assertTrue( isset($ret['grid'])  );
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( 0, $ret['free_num']);
		$this->assertEquals( 1, $ret['today_accum_dig']+$ret['today_gold_dig']);
		$this->assertEquals( 1, $ret['all_dig_num']);
		
	}
	

	public function test_dig_by_accum()
	{
		$conf = self::getConf();
		if($conf['type'] != DigActivity::$TYPE['ACCUM'])
		{
			echo "no my type\n";
			return;
		}
	
		self::resetUser($this->uid);
		
		
		$preInfo = $this->getUserInfo($this->uid);
		
		
		$leftNum = 4;
		$this->assertEquals( intval($conf['freeNum']),   $preInfo['free_num']);
		$this->assertEquals( $leftNum,  $preInfo['accum_num']);
		
		
		printf("dig:%d \n", $preInfo['free_num'] - 1);
		$ret = $this->dig(DigActivity::$TYPE['ACCUM'], $preInfo['free_num'] - 1);
		$this->assertTrue( isset($ret['grid'])  );
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( 1, $ret['free_num']);
		$this->assertEquals( $preInfo['accum_num'], $ret['accum_num']);
		$this->assertEquals( $preInfo['free_num']-1, $ret['today_accum_dig']);
		$this->assertEquals( $preInfo['free_num']-1, $ret['all_dig_num']);
		
		printf("dig:%d \n", 3);
		$ret = $this->dig(DigActivity::$TYPE['ACCUM'], 3);
		$this->assertTrue( isset($ret['grid'])  );
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( 0, $ret['free_num']);
		$this->assertEquals( $preInfo['accum_num']-2, $ret['accum_num']);
		$this->assertEquals( 2, $ret['today_accum_dig']);
		$this->assertEquals( 2, $ret['all_dig_num']);
		
		printf("dig:%d \n", $leftNum);
		try
		{
			$ret = $this->dig(DigActivity::$TYPE['ACCUM'], $leftNum);
			$this->assertTrue(0);
		}
		catch ( Exception $e )
		{						
			$this->assertEquals( 'fake',  $e->getMessage());
		}
		
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( 0, $ret['free_num']);
		$this->assertEquals( $preInfo['accum_num']-2, $ret['accum_num']);
		$this->assertEquals( 2, $ret['today_accum_dig']);
		$this->assertEquals( 2, $ret['all_dig_num']);
	}
	
	public function test_dig_by_gold()
	{
		$conf = self::getConf();
		if($conf['type'] != DigActivity::$TYPE['GOLD'])
		{
			echo "no my type\n";
			return;
		}
	
		self::resetUser($this->uid);
	
		
		$preInfo = $this->getUserInfo($this->uid);
	
	
		$leftNum = 4;
		$this->assertEquals( intval($conf['freeNum']),   $preInfo['free_num']);
		$this->assertEquals( 0,  $preInfo['today_gold_dig']);
	
	
		printf("dig:%d \n", $preInfo['free_num'] - 1);
		$ret = $this->dig(DigActivity::$TYPE['GOLD'], $preInfo['free_num'] - 1);
		$this->assertTrue( isset($ret['grid'])  );
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( 1, $ret['free_num']);
		$this->assertEquals( 0, $preInfo['today_gold_dig']);
		$this->assertEquals( 0, $ret['today_gold_dig']);
		$this->assertEquals( 0, $ret['all_dig_num']);
	
		printf("dig:%d \n", 3);
		$ret = $this->dig(DigActivity::$TYPE['GOLD'], 3);
		$this->assertTrue( isset($ret['grid'])  );
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( 0, $ret['free_num']);
		$this->assertEquals( 2,  $ret['today_gold_dig']);
		$this->assertEquals( 2, $ret['all_dig_num']);
	
		if($conf['goldDayMax'] >= DigActivity::INFINITE_NUM)
		{
			return;
		}
		printf("dig:%d \n", $conf['goldDayMax']);
		try
		{
			$ret = $this->dig(DigActivity::$TYPE['GOLD'], $conf['goldDayMax']);
			$this->assertTrue(0);
		}
		catch ( Exception $e )
		{
			$this->assertEquals( 'fake',  $e->getMessage());
		}
	
		$ret = $this->getUserInfo($this->uid);
		$this->assertEquals( 0, $ret['free_num']);
		$this->assertEquals( 2,  $ret['today_gold_dig']);
		$this->assertEquals( 2, $ret['all_dig_num']);
	}
	
	
	
	public function printArray($arr)
	{
		$str = '';
		foreach($arr as $key => $value)
		{
			if($key == 'va_dig')
			{
				$str .= sprintf('%s[%s]  ', $key, implode(',',$value['black_list']) );
			}
			else
			{
				$str .= sprintf('%s[%s]  ', $key, $value);
			}
		}
		echo "$str\n";
	}
	
	public function resetUser($uid)
	{
		$values = array(
				'uid' => $uid,
				'free_num' => 0,
				'accum_num' => 0,
				'last_dig_time' => 0,
				'today_accum_dig' => 0,
				'today_gold_dig' => 0,
				'all_dig_num' => 0,
				'used_spend_gold' => 0,
				'used_pay_gold' =>0,
				'drop_b_num' => 0,
				'va_dig' => array('black_list'=>array()) );
	
		DigActivityDAO::update($uid, $values);
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */