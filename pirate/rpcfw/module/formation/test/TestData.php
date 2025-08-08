<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/
require_once (MOD_ROOT . '/user/index.php');
require_once (MOD_ROOT . '/talks/index.php');

class TestData extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		parent::setUp ();	
	}

	/**
	 * @group insertUser
	 */
	public function test_insertUser_0()
	{
		$arrUserInfo = array(
		    'uid' => $this->uid,
            'uname' => 'liuyang_1',
            'pid' => 20,
            'utid' => 1,
            'status' => UserDef::STATUS_OFFLINE,
		    'dtime' => 1893427200,
			'gold_num' => UserConf::INIT_GOLD,
			'belly_num' => UserConf::INIT_BELLY,
			'prestige_num' => UserConf::INIT_PRESTIGE,
			'master_hid' => 0,
		    'msg' => 'oh',
			'food_num' => UserConf::INIT_FOOD,
			'experience_num' => 99999999,
			'cur_execution' => UserConf::INIT_EXECUTION,
			'cur_formation' => 10001,
		    'guild_id' => 29945,
			'vip' => 10,
		    'va_user' => array('heroes' => array(11003, 10001, 10002, 10003), 
		                       'state' => array(), 
		                       'recruit_hero_order'=>array(11003, 10001), 
		                       'login_date'=>array()));

		echo "\n== "."TestData::insertUser Start ========================================"."\n";

		$data = new CData();
		$data->insertOrUpdate('t_user')->values($arrUserInfo)->query();

		echo "== "."TestData::insertUser End =========================================="."\n";
	}

	public function test_insertHero_0()
	{
		$arrHeroInfo = array(
		    'uid' => $this->uid,
            'curHp' => 599,
            'status' => 2,
		    'level' => 10,
		    'va_hero' => array('skill' => array(),
		                       'daimonApple' => array(),
		                       'arming' => array()));
		$htID = array(29999 => 11003, 30000 => 10001, 30001 => 10002, 30002 => 10003);

		echo "\n== "."TestData::insertHero Start ========================================"."\n";

		for ($i = 29999; $i <= 30002; ++$i)
		{
			$arrHeroInfo['hid'] = $i;
			$arrHeroInfo['htid'] = $htID[$i];
			$data = new CData();
			$data->insertOrUpdate('t_hero')->values($arrHeroInfo)->query();
		}

		echo "== "."TestData::insertHero End =========================================="."\n";
	}

	public function test_insertSailboat_0()
	{
    	// 设置舱室字段
    	$cabinInfo = array(
			SailboatDef::CAPTAIN_ROOM_ID => array('level' => 10),
			SailboatDef::KITCHEN_ID => array('level' => 7),
			SailboatDef::TRAIN_ROOM_ID => array('level' => 10),
			SailboatDef::PET_ID => array('level' => 41),
			SailboatDef::TRADE_ROOM_ID => array('level' => 8),
			SailboatDef::SCI_TECH_ID => array('level' => 12),
			SailboatDef::MEDICAL_ROOM_ID => array('level' => 34),
			SailboatDef::CASH_ROOM_ID => array('level' => 10),
			SailboatDef::SAILOR_01_ID => array('level' => 1),
			SailboatDef::SAILOR_02_ID => array('level' => 2),
			SailboatDef::SAILOR_03_ID => array('level' => 3),
			SailboatDef::SAILOR_04_ID => array('level' => 4),
			SailboatDef::SAILOR_05_ID => array('level' => 5),
			SailboatDef::SAILOR_06_ID => array('level' => 6),
			SailboatDef::SAILOR_07_ID => array('level' => 7),
			SailboatDef::SAILOR_08_ID => array('level' => 8),
			SailboatDef::SAILOR_09_ID => array('level' => 9),
			SailboatDef::SAILOR_10_ID => array('level' => 0)
		);

    	// 设置建筑队列字段
    	$listInfo = array_fill(0, SailboatConf::BUILD_INIT_NUM, array('state' => SailboatConf::BUILDING_FREE, 'endtime' => 0));

    	// 设置活动字段
    	$vaArr = array('cabin_id_lv' => $cabinInfo, 'list_info' => $listInfo,
    	               'all_design' => array(), 'now_design' => array(), 'now_skill' => array());
		// 设置属性
		$arrBoatInfo = array('uid' => $this->uid,
					 		 'boat_type' => 1,
		                     'figurehead_item_id' => 0,
					 		 'cannon_item_id' => 0,
					 		 'wallpiece_item_id' => 0,
					 		 'sails_item_id' => 0,
					 		 'armour_item_id' => 0,
					 		 'va_boat_info' => $vaArr,
					 		 'status' => 1);

		echo "\n== "."TestData::insertSailboat Start ===================================="."\n";

		$data = new CData();
		$data->insertOrUpdate('t_sailboat')->values($arrBoatInfo)->query();

		echo "== "."TestData::insertSailboat End ======================================"."\n";
	}

	public function test_insertCopy_0()
	{
		// 设置VA字段信息
		$va_info = array('progress' => array(),
		                 'defeat_id_times' => array(102 => 1, 101 => 3, 17 => 1, 18 => 1),
		                 'id_appraisal' => array(), 
		                 'prize_ids' => array());
		// 设置插入数据
		$arr = array('uid' => 29945,
					 'copy_id' => 2,
					 'raid_times' => 1,
					 'score' => 0,
					 'prized_num' => 0,
					 'va_copy_info' => $va_info,
					 'status' => 1);
		
		echo "\n== "."TestData::insertCopy Start ========================================"."\n";
		$data = new CData();
		$data->insertOrUpdate('t_copy')->values($arr)->query();
		echo "== "."TestData::insertCopy End =========================================="."\n";
	}

	public function test_insertTalks_0()
	{
//		RPCContext::getInstance()->setSession('global.uid', $this->uid);
//		RPCContext::getInstance()->setSession('user.user', UserLogic::getUser($this->uid));


		// 设置属性
		$arr = array('uid' => 29945,
		             'talk_times' => 0,
		             'talk_date' => 0,
		             'open_free_mode' => 0,
					 'va_talks_info' => array('talk_win' => array(),
		                                      'out_heros' => array(10025)),
		             'status' => 1);

		echo "\n== "."TestData::insertTalks Start ======================================="."\n";
		$data = new CData();
		$arrRet = $data->insertOrUpdate('t_talks')
		               ->values($arr)->query();
		echo "== "."TestData::insertTalks End ========================================="."\n";
		               
//		EnTalks::openTalksWindow($this->uid);
//		EnTalks::openTalksWindow($this->uid);
//		EnTalks::openTalksWindow($this->uid);
		return $arrRet;
	}
/*
	public function test_insertST_0()
	{
		$arrSTInfo = array(
		    'uid' => 52436,
		    'cd_time' => 0,
		    'va_st_info' => array('st_id_lv' => array(10001 => array('lv' => 2, 'id' => 10001), 
		                                              10002 => array('lv' => 2, 'id' => 10002),
		                                              10005 => array('lv' => 2, 'id' => 10005))));

		echo "\n== "."TestData::insertST_0 Start =========="."\n";

		$data = new CData();
		$data->insertOrUpdate('t_sci_tech')->values($arrSTInfo)->query();

		echo "== "."TestData::insertST_0 End ============"."\n";
	}*/
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */