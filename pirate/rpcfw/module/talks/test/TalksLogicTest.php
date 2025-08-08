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
class TalksLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 20103;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		parent::setUp ();		

		RPCContext::getInstance()->setSession('global.uid', $this->uid);

	}

//	/**
//	 * addNewTalksInfoForUser
//	 * openTalksWindow
//	 * getUserTalksInfo
//	 * 
//	 * @group getUserTalksInfo
//	 */
//	public function test_getUserTalksInfo_0()
//	{
//		echo "\n== "."TalksLogic::getUserTalksInfo_0 Start =============================="."\n";
//
//		EnTalks::addNewTalksInfoForUser($this->uid);
//
//		$ret = TalksLogic::getUserTalksInfo();
//		$this->assertTrue($ret['uid'] == '29945', "addNewTalksInfoForUser:ret uid not 29945.");
//		$this->assertTrue($ret['talk_times'] == '0', "addNewTalksInfoForUser:ret talk_times not 0.");
//		$this->assertTrue($ret['va_talks_info']['talk_win'] == array(), "addNewTalksInfoForUser:ret va_talks_info talk_win not array.");
//		$this->assertTrue($ret['va_talks_info']['out_heros'] == array(), "addNewTalksInfoForUser:ret va_talks_info out_heros not array.");
//
//		$eID_1 = EnTalks::openTalksWindow($this->uid);
//		$ret = TalksLogic::getUserTalksInfo();
//		$this->assertTrue($ret['uid'] == '29945', "openTalksWindow:ret uid not 29945.");
//		$this->assertTrue($ret['va_talks_info']['talk_win'][1] == $eID_1, "openTalksWindow:ret va_talks_info talk_win 1 not ".$eID_1);
//
//		$eID_2 = EnTalks::openTalksWindow($this->uid);
//		$ret = TalksLogic::getUserTalksInfo();
//		$this->assertTrue($ret['uid'] == '29945', "openTalksWindow:ret uid not 29945.");
//		$this->assertTrue($ret['va_talks_info']['talk_win'][1] == $eID_1, "openTalksWindow:ret va_talks_info talk_win 1 not ".$eID_1);
//		$this->assertTrue($ret['va_talks_info']['talk_win'][2] == $eID_2, "openTalksWindow:ret va_talks_info talk_win 2 not ".$eID_2);
//
//		$ret = EnTalks::openTalksWindow($this->uid);
//		$this->assertTrue($ret == null, "openTalksWindow:ret not null.");
//		$ret = TalksLogic::getUserTalksInfo();
//		$this->assertTrue($ret['uid'] == '29945', "openTalksWindow:ret uid not 29945.");
//		$this->assertTrue($ret['va_talks_info']['talk_win'][1] == $eID_1, "openTalksWindow:ret va_talks_info talk_win 1 not ".$eID_1);
//		$this->assertTrue($ret['va_talks_info']['talk_win'][2] == $eID_2, "openTalksWindow:ret va_talks_info talk_win 2 not ".$eID_2);
//		$this->assertFalse(isset($ret['va_talks_info']['talk_win'][3]), "openTalksWindow:ret va_talks_info talk_win 3 not empty.");
//
//		echo "== "."TalksLogic::getUserTalksInfo_0 End ================================"."\n";
//	}
//
//	/**
//	 * refresh
//	 * refreshAll
//	 * 
//	 * @group refresh
//	 */
//	public function test_refresh_0()
//	{
//		echo "\n== "."TalksLogic::refresh_0 Start ======================================="."\n";
//
//		EnTalks::addNewTalksInfoForUser($this->uid);
//		$eID_1 = EnTalks::openTalksWindow($this->uid);
//		echo 'Window 1 event is '.$eID_1."\n";
//		$eID_2 = EnTalks::openTalksWindow($this->uid);
//		echo 'Window 2 event is '.$eID_2."\n";
//		$user = EnUser::getUser();
//		$oldGold = $user['gold_num'];
//		echo 'User gold is '.$oldGold."\n"."\n";
//
//		$eID_1 = TalksLogic::refresh(1);
//		echo 'Window 1 event after refresh is '.$eID_1."\n";
//		$user = EnUser::getUser();
//		echo 'User gold after refresh is '.$user['gold_num']."\n"."\n";
//		$this->assertTrue($user['gold_num'] == $oldGold - 10, "refresh:ret user gold_num not ".($oldGold - 10));
//		$oldGold = $user['gold_num'];
//
//		$eID_2 = TalksLogic::refresh(2);
//		echo 'Window 2 event after refresh is '.$eID_2."\n";
//		$user = EnUser::getUser();
//		echo 'User gold after refresh is '.$user['gold_num']."\n"."\n";
//		$this->assertTrue($user['gold_num'] == $oldGold - 10, "refresh:ret user gold_num not ".($oldGold - 10));
//		$oldGold = $user['gold_num'];
//
//		$ret = TalksLogic::refreshAll();
//		echo 'Window 1 event after refresh is '.$ret[1]."\n";
//		echo 'Window 2 event after refresh is '.$ret[2]."\n";
//		$user = EnUser::getUser();
//		echo 'User gold after refresh is '.$user['gold_num']."\n";
//		$this->assertTrue($user['gold_num'] == $oldGold - 20, "refresh:ret user gold_num not ".($oldGold - 20));
//		$oldGold = $user['gold_num'];
//
//		echo "== "."TalksLogic::refresh_0 End ========================================="."\n";
//	}
//
//	/**
//	 * startTalks
//	 * 
//	 * @group startTalks
//	 */
//	public function test_startTalks_0()
//	{
//		echo "\n== "."TalksLogic::startTalks_0 Start ===================================="."\n";
//
//		EnTalks::addNewTalksInfoForUser($this->uid);
//		$eID_1 = EnTalks::openTalksWindow($this->uid);
//		$eID_2 = EnTalks::openTalksWindow($this->uid);
//		echo 'Window 1 event is '.$eID_1."\n";
//		echo 'Window 2 event is '.$eID_2."\n";
//		$ret = TalksLogic::getUserTalksInfo();
//		$this->assertTrue($ret['uid'] == '29945', "openTalksWindow:ret uid not 29945.");
//		$this->assertTrue($ret['va_talks_info']['talk_win'][1] == $eID_1, "openTalksWindow:ret va_talks_info talk_win 1 not ".$eID_1);
//		$this->assertTrue($ret['va_talks_info']['talk_win'][2] == $eID_2, "openTalksWindow:ret va_talks_info talk_win 2 not ".$eID_2);
//
//		$user = EnUser::getUser();
//		$oldGold = $user['gold_num'];
//		$oldBelly = $user['belly_num'];
//		$ret = TalksLogic::startTalks(1);
//		$user = EnUser::getUser();
//		if ($eID_1 == 1 || $eID_1 == 2 || $eID_1 == 8 || $eID_1 == 9 || $eID_1 == 10)
//		{
//			$this->assertTrue($user['belly_num'] == $oldBelly + 100, "startTalks:ret user belly_num not ".($oldBelly + 100));
//		}
//		else if ($eID_1 == 4)
//		{
//			$this->assertTrue($user['gold_num'] == $oldGold + 100, "startTalks:ret user gold_num not ".($oldGold + 100));
//		}
//		else if ($eID_1 == 3)
//		{
//			$this->assertTrue($user['belly_num'] == $oldBelly + 100, "startTalks:ret user belly_num not ".($oldBelly + 100));
//			$this->assertTrue($ret['bagInfo'][1]['item_template_id'] == 10010, "startTalks:ret user item_id not 10010.");
//			$this->assertTrue($ret['bagInfo'][1]['item_num'] == 1, "startTalks:ret user item_num not 1.");
//		}
//		else if ($eID_1 == 6)
//		{
//			$ret = TalksLogic::getUserTalksInfo();
//			$this->assertTrue($ret['va_talks_info']['out_heros'][0] == 10025, "startTalks:ret va_talks_info out_heros not 10025.");
//		}
//		else if ($eID_1 == 7)
//		{
//			$ret = TalksLogic::getUserTalksInfo();
//			$this->assertTrue($ret['va_talks_info']['out_heros'][0] == 10012, "startTalks:ret va_talks_info out_heros not 10012.");
//		}
//		$oldGold = $user['gold_num'];
//		$oldBelly = $user['belly_num'];
//
//		$ret = TalksLogic::startTalks(2);
//		$user = EnUser::getUser();
//		if ($eID_2 == 1 || $eID_2 == 2 || $eID_2 == 8 || $eID_2 == 9 || $eID_2 == 10)
//		{
//			$this->assertTrue($user['belly_num'] == $oldBelly + 100, "startTalks:ret user belly_num not ".($oldBelly + 100));
//		}
//		else if ($eID_2 == 4)
//		{
//			$this->assertTrue($user['gold_num'] == $oldGold + 100, "startTalks:ret user gold_num not ".($oldGold + 100));
//		}
//		else if ($eID_2 == 3 && $eID_1 != 3)
//		{
//			$this->assertTrue($user['belly_num'] == $oldBelly + 100, "startTalks:ret user belly_num not ".($oldBelly + 100));
//			$this->assertTrue($ret['bagInfo'][1]['item_template_id'] == 10010, "startTalks:ret user item_id not 10010.");
//			$this->assertTrue($ret['bagInfo'][1]['item_num'] == 1, "startTalks:ret user item_num not 1.");
//		}
//		else if ($eID_2 == 3 && $eID_1 == 3)
//		{
//			$this->assertTrue($user['belly_num'] == $oldBelly + 100, "startTalks:ret user belly_num not ".($oldBelly + 100));
//			$this->assertTrue($ret['bagInfo'][1]['item_template_id'] == 10010, "startTalks:ret user item_id not 10010.");
//			$this->assertTrue($ret['bagInfo'][1]['item_num'] == 1, "startTalks:ret user item_num not 1.");
//			$this->assertTrue($ret['bagInfo'][2]['item_template_id'] == 10010, "startTalks:ret user item_id not 10010.");
//			$this->assertTrue($ret['bagInfo'][2]['item_num'] == 1, "startTalks:ret user item_num not 1.");
//		}
//		else if ($eID_2 == 6 && $eID_1 != 7)
//		{
//			$ret = TalksLogic::getUserTalksInfo();
//			$this->assertTrue($ret['va_talks_info']['out_heros'][0] == 10025, "startTalks:ret va_talks_info out_heros not 10025.");
//		}
//		else if ($eID_2 == 7 && $eID_1 != 6)
//		{
//			$ret = TalksLogic::getUserTalksInfo();
//			$this->assertTrue($ret['va_talks_info']['out_heros'][0] == 10012, "startTalks:ret va_talks_info out_heros not 10012.");
//		}
//		else if ($eID_1 == 6 && $eID_2 == 6 )
//		{
//			$this->assertTrue(1, "startTalks:ret event hero are equal 6.");
//		}
//		else if ($eID_1 == 7 && $eID_2 == 7 )
//		{
//			$this->assertTrue(1, "startTalks:ret event hero are equal 7.");
//		}
//		else if ($eID_1 == 6 && $eID_2 == 7)
//		{
//			$ret = TalksLogic::getUserTalksInfo();
//			$this->assertTrue($ret['va_talks_info']['out_heros'][0] == 10025, "startTalks:ret va_talks_info out_heros 0 not 10025.");
//			$this->assertTrue($ret['va_talks_info']['out_heros'][1] == 10012, "startTalks:ret va_talks_info out_heros 1 not 10012.");
//		}
//		else if ($eID_1 == 7 && $eID_2 == 6)
//		{
//			$ret = TalksLogic::getUserTalksInfo();
//			$this->assertTrue($ret['va_talks_info']['out_heros'][0] == 10012, "startTalks:ret va_talks_info out_heros 0 not 10012.");
//			$this->assertTrue($ret['va_talks_info']['out_heros'][1] == 10025, "startTalks:ret va_talks_info out_heros 1 not 10025.");
//		}
////		$ret = TalksLogic::getUserTalksInfo();
////		var_dump($ret);
//
//		echo "== "."TalksLogic::startTalks_0 End ======================================"."\n";
//	}

	
	public function test_openFreeMode_0()
	{
		echo "\n== "."TalksLogic::openFreeMode_0 Start =================================="."\n";

//		$ret = TalksLogic::openFreeMode();
//		var_dump($ret);
//
//		$ret = TalksLogic::getHeroList();
		$ret = TalksLogic::getUserTalksInfo();
		var_dump($ret);

		echo "== "."TalksLogic::openFreeMode_0 End ===================================="."\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */