<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AllBlueTest.php 33208 2012-12-15 11:12:00Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/allblue/test/AllBlueTest.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-15 19:12:00 +0800 (六, 2012-12-15) $
 * @version $Revision: 33208 $
 * @brief 
 *  
 **/
class AllBlueTest extends PHPUnit_Framework_TestCase
{
	
	private $uid = 21300;
	private $allblue;
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		RPCContext::getInstance()->unsetSession('allblue.list');
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		$this->allblue = new AllBlue();
		parent::setUp ();
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('allblue.list');
		RPCContext::getInstance()->unsetSession('global.uid');
		MyAllBlue::release();
	}
	
	/**
	 * @group getAllBlueInfo 
	 */
	public function test_AllBlueTest()
	{
		// 取得玩家信息
		$this->allblue->getAllBlueInfo();
		// 金币开始采集
		$this->allblue->collectAllBule(0, TRUE, 1);
		$this->allblue->collectAllBule(1, TRUE, 1);
		$this->allblue->collectAllBule(2, TRUE, 1);
		$this->allblue->collectAllBule(3, TRUE, 1);
		$this->allblue->collectAllBule(4, TRUE, 1);
		$this->allblue->collectAllBule(5, TRUE, 1);
//		$this->allblue->collectAllBule(6, TRUE, 1);
		// 贝利开始采集
		$this->allblue->collectAllBule(1, FALSE, 0);
		$this->allblue->collectAllBule(2, FALSE, 0);
		$this->allblue->collectAllBule(2, FALSE, 0);
		$this->allblue->collectAllBule(3, FALSE, 0);
		$this->allblue->collectAllBule(4, FALSE, 0);
		$this->allblue->collectAllBule(5, FALSE, 1);
		
		// 攻打海王类
//		$this->allblue->atkSeaMonster(11001);
	}
	
	/**
	 * @group fish 
	 */
	public function test_Fish()
	{
		EnAllBlue::initAllBlueCollectTime();
		// 1354528040  Mon Dec  3 17:47:20 CST 2012
		// 1354428040  Sun Dec  2 14:00:40 CST 2012
		// 1354328040  Sat Dec  1 10:14:00 CST 2012
//	首次进allblue	最后一次养鱼时间	当前时间	相隔	已使用次数	可使用次数
//1	是	时间是3号	时间是3号	0	0	3
//2	是	时间是1号	时间是3号	2	0	3
//3	否	时间是3号	时间是3号	0	3	0
//4	否	时间是3号	时间是3号	0	0	3
//5	否	时间是2号	时间是3号	1	0	3
//6	否	时间是2号	时间是3号	1	3	3
//7	否	时间是1号	时间是3号	2	0	3
//8	否	时间是1号	时间是3号	2	3	3
		
//		self::updateAllBlue(0, 0, 1354528040);
//		$ret = $this->allblue->farmFishInfo();
//		print_r('1:'.$ret['fftimes']);
		
//		self::updateAllBlue(0, 0, 1354328040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('2:'.$ret['fftimes']);
//		
//		self::updateAllBlue(3, 0, 1354528040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('3:'.$ret['fftimes']);
//
//		self::updateAllBlue(0, 0, 1354528040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('4:'.$ret['fftimes']);

//		self::updateAllBlue(0, 0, 1354428040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('5:'.$ret['fftimes']);
//		self::updateAllBlue(3, 0, 1354428040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('6:'.$ret['fftimes']);
//		self::updateAllBlue(0, 0, 1354328040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('7:'.$ret['fftimes']);
//		self::updateAllBlue(3, 0, 1354328040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('8:'.$ret['fftimes']);

//	不是首次进allblue				之前剩余可使用次数	可使用次数
//1	是	时间是3号	时间是3号	0	0	3
//2	是	时间是1号	时间是3号	2	0	6
//3	否	时间是3号	时间是3号	0	3	3
//4	否	时间是2号	时间是3号	1	0	3
//5	否	时间是2号	时间是3号	1	3	6
//6	否	时间是1号	时间是3号	2	3	9
//7	否	时间是1号	时间是3号	2	4	10
//8	否	时间是1号	时间是3号	N	0	10
		// 1354528040  Mon Dec  3 17:47:20 CST 2012
		// 1354428040  Sun Dec  2 14:00:40 CST 2012
		// 1354328040  Sat Dec  1 10:14:00 CST 2012
//		self::updateAllBlue(0, 1, 1354528040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('1:'.$ret['fftimes']);
//		self::updateAllBlue(0, 1, 1354328040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('2:'.$ret['fftimes']);
//		self::updateAllBlue(3, 1, 1354528040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('3:'.$ret['fftimes']);

//		self::updateAllBlue(0, 1, 1354428040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('4:'.$ret['fftimes']);
//		self::updateAllBlue(3, 1, 1354428040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('5:'.$ret['fftimes']);
//		self::updateAllBlue(1, 1, 1354328040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('6:'.$ret['fftimes']);
//		self::updateAllBlue(4, 1, 1354328040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('7:'.$ret['fftimes']);
//		self::updateAllBlue(0, 1, 1354128040);
//		$ret = $this->allblue->farmFishInfo();
//		var_dump('8:'.$ret['fftimes']);
		
		
//		$this->allblue->catchKrills(0);
		
//		$this->allblue->refreshKrill(0);
		
//		$this->allblue->catchKrill(0);
				
//		$this->allblue->farmFish(0);
		
//		$this->allblue->openBoot(0);
		
//		$this->allblue->fishing(0);

//		$this->allblue->openFishQueue(1);
		
//		$this->allblue->friendList(1, 5);
		
//		$this->allblue->goFriendFishpond(20112);
		
//		$this->allblue->thiefFish(49806, 0);
		
//		$this->allblue->wishFish(49806, 0);
	}

	private static function updateAllBlue($times, $flg, $time)
	{
		$arrCond = array(array('uid', '=', 21300));
		$arrField = array('farmfish_times' => $times, 
							'farmfish_times_changeflg' => $flg,
							'farmfish_time' => $time);

		$data = new CData();
		$data->update('t_allblue')->set($arrField);
		foreach ( $arrCond as $cond )
		{
			$data->where($cond);
		}	
		$data->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */