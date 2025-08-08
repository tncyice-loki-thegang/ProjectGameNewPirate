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

require_once (LIB_ROOT . '/data/NewData.class.php');
require_once (MOD_ROOT . '/kitchen/index.php');
require_once (MOD_ROOT . '/user/index.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');

restore_error_handler();

class KitchenLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;
	private $boatID = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		parent::setUp ();	

		RPCContext::getInstance()->setSession('global.boatid', $this->boatID);
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		RPCContext::getInstance()->setSession('user.user', UserLogic::getUser($this->uid));

//		$data = new CData();
//		$ret = $data->delete()->from('t_kitchen')->where(array('uid', '=', $this->uid))->query();
//		$ret = $data->delete()->from('t_kitchen')->where(array('uid', '=', 29946))->query();
	}

	/**
	 * @group getUserOrderInfo
	 */
	public function test_getUserOrderInfo_0()
	{
		echo "\n== "."KitchenLogic::getUserOrderInfo_0 Start ============================"."\n";

//		EnKitchen::addNewKitchenInfoForUser($this->uid);
//		EnKitchen::addNewKitchenInfoForUser(29946);

		$curTime = Util::getTime();
		$ret = KitchenLogic::getUserOrderInfo($this->uid); var_dump($ret);
		$this->assertTrue($ret['uid'] == '29945', "getUserOrderInfo:ret uid not 29945.");
		$this->assertTrue($ret['cook_cd_time'] == '0', "getUserOrderInfo:ret cook_cd_time not 0.");
		$this->assertTrue($ret['order_cd_time'] == '0', "getUserOrderInfo:ret order_cd_time not 0.");
		$this->assertTrue($ret['lv'] == '1', "getUserOrderInfo:ret lv not 1.");
		$this->assertTrue($ret['exp'] == '0', "getUserOrderInfo:ret exp not 0.");
		$this->assertTrue($ret['gold_cook_times'] == '0', "getUserOrderInfo:ret gold_cook_times not 0.");
		$this->assertTrue($ret['gold_cook_date'] == '0', "getUserOrderInfo:ret gold_cook_date not 0.");
		$this->assertTrue($ret['cook_times'] == '0', "getUserOrderInfo:ret cook_times not 0.");
		$this->assertTrue($ret['cook_date'] == '0', "getUserOrderInfo:ret cook_date not 0.");
		$this->assertTrue($ret['be_order_times'] == '0', "getUserOrderInfo:ret be_order_times not 0.");
		$this->assertTrue($ret['order_times'] == '0', "getUserOrderInfo:ret order_times not 0.");
		$this->assertTrue($ret['belly'] == '0', "getUserOrderInfo:ret belly not 0.");
		//$this->assertTrue($ret['order_date'] == '0', "getUserOrderInfo:ret order_date not 0.");

		$ret = KitchenLogic::getUserKitchenInfo($this->uid);
		$this->assertTrue($ret['uid'] == '29945', "getUserOrderInfo:ret uid not 29945.");
		$this->assertTrue($ret['cook_cd_time'] == '0', "getUserOrderInfo:ret cook_cd_time not 0.");
		$this->assertTrue($ret['order_cd_time'] == '0', "getUserOrderInfo:ret order_cd_time not 0.");
		$this->assertTrue($ret['lv'] == '1', "getUserOrderInfo:ret lv not 1.");
		$this->assertTrue($ret['exp'] == '0', "getUserOrderInfo:ret exp not 0.");
		$this->assertTrue($ret['gold_cook_times'] == '0', "getUserOrderInfo:ret gold_cook_times not 0.");
		$this->assertTrue($ret['gold_cook_date'] == '0', "getUserOrderInfo:ret gold_cook_date not 0.");
		$this->assertTrue($ret['cook_times'] == '0', "getUserOrderInfo:ret cook_times not 0.");
		$this->assertTrue($ret['cook_date'] == '0', "getUserOrderInfo:ret cook_date not 0.");
		$this->assertTrue($ret['be_order_times'] == '0', "getUserOrderInfo:ret be_order_times not 0.");
		$this->assertTrue($ret['order_times'] == '0', "getUserOrderInfo:ret order_times not 0.");
		//$this->assertTrue($ret['order_date'] == '0', "getUserOrderInfo:ret order_date not 0.");
		$this->assertTrue($ret['belly'] == '0', "getUserOrderInfo:ret belly not 0.");
		$this->assertTrue($ret['va_kitchen_info']['stock'] == array(), "getUserOrderInfo:ret va_kitchen_info not array.");

		echo "== "."KitchenLogic::getUserOrderInfo_0 End =============================="."\n";
	}

//	/**
//	 * placeOrder
//	 * clearCDByGold
//	 * getCdEndTime
//	 * getCDTime
//	 * addCDTime
//	 * adjustOrderTimes
//	 * 
//	 * @group placeOrder
//	 */
//	public function test_placeOrder_0()
//	{
//		echo "\n== "."KitchenLogic::placeOrder_0 Start =================================="."\n";
//
//		EnKitchen::addNewKitchenInfoForUser($this->uid);
//		EnKitchen::addNewKitchenInfoForUser(29946);
//
//		restore_error_handler();
//		$curTime = Util::getTime();
//		$ret = KitchenLogic::placeOrder(29946, 1);
//		$this->assertTrue($ret['cdTime'] == $curTime + btstore_get()->KITCHEN['order_cd_up'], "placeOrder:ret cdTime not ".($curTime + btstore_get()->KITCHEN['order_cd_up']));
//		$this->assertTrue($ret['targetUserBeOrderTimes'] == '1', "placeOrder:ret targetUserBeOrderTimes not 1.");
//		$this->assertTrue(isset($ret['userBelly']), "placeOrder:ret not have userBelly.");
//		$this->assertTrue(isset($ret['targetUserBelly']), "placeOrder:ret not have targetUserBelly.");
//
//		try {
//			KitchenLogic::placeOrder(29946, 1);
//			$this->assertTrue(0, "placeOrder not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "placeOrder not fake");
//		}
//
//		$userInfo = EnUser::getUser();
//		$oldGold = $userInfo['gold_num'];
//		$ret = KitchenLogic::clearCDByGold('order');
//		$this->assertTrue($ret == 1, "clearCDByGold:ret not 1.");
//		$userInfo = EnUser::getUser();
//		$this->assertTrue($oldGold == $userInfo['gold_num'] + 1, "user gold not sub 1.");
//
//		$curTime = Util::getTime();
//		$ret = KitchenLogic::placeOrder(29946, 1);
//		$this->assertTrue($ret['cdTime'] == $curTime + btstore_get()->KITCHEN['order_cd_up'], "placeOrder:ret cdTime not ".($curTime + btstore_get()->KITCHEN['order_cd_up']));
//		$this->assertTrue($ret['targetUserBeOrderTimes'] == '2', "placeOrder:ret targetUserBeOrderTimes not 2.");
//		$this->assertTrue(isset($ret['userBelly']), "placeOrder:ret not have userBelly.");
//		$this->assertTrue(isset($ret['targetUserBelly']), "placeOrder:ret not have targetUserBelly.");
//
//		$ret = KitchenLogic::getUserKitchenInfo(29946);
//		$this->assertTrue($ret['be_order_times'] == '2', "getUserOrderInfo:ret be_order_times not 2.");
//
//
//		echo "== "."KitchenLogic::placeOrder_0 End ===================================="."\n";
//	}

	/**
	 * cook
	 * clearCDByGold
	 * addExp
	 * addCookTimes
	 * getTodayCookTimes
	 * 
	 * @group cook
	 */
	public function test_cook_0()
	{
		echo "\n== "."KitchenLogic::cook_0 Start ========================================"."\n";

//		EnKitchen::addNewKitchenInfoForUser($this->uid);

		$curTime = Util::getTime();
		$ret = KitchenLogic::cook(1, false); var_dump($ret);
		$this->assertTrue($ret['uid'] == '29945', "getUserOrderInfo:ret uid not 29945.");
		$this->assertTrue($ret['cook_cd_time'] == $curTime + btstore_get()->KITCHEN['cook_cd_up'], "cook:ret cook_cd_time not ".($curTime + btstore_get()->KITCHEN['cook_cd_up']));
		$this->assertTrue($ret['order_cd_time'] == '0', "getUserOrderInfo:ret order_cd_time not 0.");
		$this->assertTrue($ret['lv'] == '1', "getUserOrderInfo:ret lv not 1.");
		$this->assertTrue($ret['exp'] == '1', "getUserOrderInfo:ret exp not 1.");
		$this->assertTrue($ret['gold_cook_times'] == '0', "getUserOrderInfo:ret gold_cook_times not 0.");
		$this->assertTrue($ret['gold_cook_date'] == '0', "getUserOrderInfo:ret gold_cook_date not 0.");
		$this->assertTrue($ret['cook_times'] == '1', "getUserOrderInfo:ret cook_times not 1.");
		$this->assertTrue($ret['cook_date'] == $curTime, "getUserOrderInfo:ret cook_date not ".$curTime);
		$this->assertTrue($ret['be_order_times'] == '0', "getUserOrderInfo:ret be_order_times not 0.");
		$this->assertTrue($ret['order_times'] == '0', "getUserOrderInfo:ret order_times not 0.");
		$this->assertTrue($ret['order_date'] == '0', "getUserOrderInfo:ret order_date not 0.");
		$this->assertTrue($ret['va_kitchen_info']['stock'][1]['id'] == 1, "getUserOrderInfo:ret va_kitchen_info id not 1.");
		echo "第1次下厨，产量是 :".$ret['va_kitchen_info']['stock'][1]['num']."\n";;
	
		try {
			KitchenLogic::cook(1, false);
			$this->assertTrue(0, "cook not throw");
		}
		catch (Exception $e)
		{
			$this->assertTrue($e->getMessage() == 'fake', "cook not fake");
		}
		$userInfo = EnUser::getUser();
		$oldGold = $userInfo['gold_num'];
		$ret = KitchenLogic::clearCDByGold('cook');
		$userInfo = EnUser::getUser();
		$this->assertTrue($oldGold == $userInfo['gold_num'] + 3, "user gold not sub 3.");

		$curTime = Util::getTime();
		$ret = KitchenLogic::cook(1, false);
		$this->assertTrue($ret['uid'] == '29945', "getUserOrderInfo:ret uid not 29945.");
		$this->assertTrue($ret['cook_cd_time'] == $curTime + btstore_get()->KITCHEN['cook_cd_up'], "cook:ret cook_cd_time not ".($curTime + btstore_get()->KITCHEN['cook_cd_up']));
		$this->assertTrue($ret['order_cd_time'] == '0', "getUserOrderInfo:ret order_cd_time not 0.");
		$this->assertTrue($ret['lv'] == '1', "getUserOrderInfo:ret lv not 1.");
		$this->assertTrue($ret['exp'] == '2', "getUserOrderInfo:ret exp not 2.");
		$this->assertTrue($ret['gold_cook_times'] == '0', "getUserOrderInfo:ret gold_cook_times not 0.");
		$this->assertTrue($ret['gold_cook_date'] == $curTime, "getUserOrderInfo:ret gold_cook_date not ".$curTime);
		$this->assertTrue($ret['cook_times'] == '2', "getUserOrderInfo:ret cook_times not 2.");
		$this->assertTrue($ret['cook_date'] == $curTime, "getUserOrderInfo:ret cook_date not ".$curTime);
		$this->assertTrue($ret['be_order_times'] == '0', "getUserOrderInfo:ret be_order_times not 0.");
		$this->assertTrue($ret['order_times'] == '0', "getUserOrderInfo:ret order_times not 0.");
		$this->assertTrue($ret['order_date'] == '0', "getUserOrderInfo:ret order_date not 0.");
		$this->assertTrue($ret['va_kitchen_info']['stock'][1]['id'] == 1, "getUserOrderInfo:ret va_kitchen_info id not 1.");
		echo "第2次下厨，产量是 :".$ret['va_kitchen_info']['stock'][1]['num']."\n";;

		$ret = KitchenLogic::clearCDByGold('cook');

		$curTime = Util::getTime();
		$ret = KitchenLogic::cook(1, false);
		$this->assertTrue($ret['cook_cd_time'] == $curTime + btstore_get()->KITCHEN['cook_cd_up'], "cook:ret cook_cd_time not ".($curTime + btstore_get()->KITCHEN['cook_cd_up']));
		$this->assertTrue($ret['lv'] == '2', "getUserOrderInfo:ret lv not 2.");
		$this->assertTrue($ret['exp'] == '0', "getUserOrderInfo:ret exp not 0.");
		$this->assertTrue($ret['cook_times'] == '3', "getUserOrderInfo:ret cook_times not 3.");
		$this->assertTrue($ret['va_kitchen_info']['stock'][1]['id'] == 1, "getUserOrderInfo:ret va_kitchen_info id not 1.");
		echo "第3次下厨，产量是 :".$ret['va_kitchen_info']['stock'][1]['num']."\n";;

		$ret = KitchenLogic::clearCDByGold('cook');

		$userInfo = EnUser::getUser();
		$oldGold = $userInfo['gold_num'];
		$ret = KitchenLogic::cook(1, true);
		$this->assertTrue($ret['lv'] == '2', "getUserOrderInfo:ret lv not 2.");
		$this->assertTrue($ret['exp'] == '1', "getUserOrderInfo:ret exp not 1.");
		$this->assertTrue($ret['cook_times'] == '4', "getUserOrderInfo:ret cook_times not 4.");
		echo "第4次下厨,暴击！，产量是 :".$ret['va_kitchen_info']['stock'][1]['num']."\n";;
		$userInfo = EnUser::getUser();
		$this->assertTrue($oldGold == $userInfo['gold_num'] + 1, "user gold not sub 1.");

		echo "== "."KitchenLogic::cook_0 End =========================================="."\n";
	}
//
//	/**
//	 * goldCook
//	 * 
//	 * @group goldCook
//	 */
//	public function test_goldCook_0()
//	{
//		echo "\n== "."KitchenLogic::goldCook_0 Start ===================================="."\n";
//
//		EnKitchen::addNewKitchenInfoForUser($this->uid);
//
//		$curTime = Util::getTime();
//		$ret = KitchenLogic::cook(1, false);
//		$this->assertTrue($ret['uid'] == '29945', "getUserOrderInfo:ret uid not 29945.");
//		$this->assertTrue($ret['cook_cd_time'] == $curTime + btstore_get()->KITCHEN['cook_cd_up'], "cook:ret cook_cd_time not ".($curTime + btstore_get()->KITCHEN['cook_cd_up']));
//		echo "第1次下厨，产量是 :".$ret['va_kitchen_info']['stock'][1]['num']."\n";
//		
//		$userInfo = EnUser::getUser();
//		$oldGold = $userInfo['gold_num'];
//		$ret = KitchenLogic::goldCook(1, false);
//		$this->assertTrue($ret['uid'] == '29945', "getUserOrderInfo:ret uid not 29945.");
//		$this->assertTrue($ret['cook_cd_time'] == $curTime + btstore_get()->KITCHEN['cook_cd_up'], "cook:ret cook_cd_time not ".($curTime + btstore_get()->KITCHEN['cook_cd_up']));
//		$this->assertTrue($ret['order_cd_time'] == '0', "getUserOrderInfo:ret order_cd_time not 0.");
//		$this->assertTrue($ret['lv'] == '1', "getUserOrderInfo:ret lv not 1.");
//		$this->assertTrue($ret['exp'] == '2', "getUserOrderInfo:ret exp not 2.");
//		$this->assertTrue($ret['gold_cook_times'] == '1', "getUserOrderInfo:ret gold_cook_times not 1.");
//		$curTime = Util::getTime();
//		$this->assertTrue($ret['gold_cook_date'] == $curTime, "getUserOrderInfo:ret gold_cook_date not ".$curTime);
//		$this->assertTrue($ret['cook_times'] == '1', "getUserOrderInfo:ret cook_times not 1.");
//		$this->assertTrue($ret['cook_date'] == $curTime, "getUserOrderInfo:ret cook_date not ".$curTime);
//		$this->assertTrue($ret['be_order_times'] == '0', "getUserOrderInfo:ret be_order_times not 0.");
//		$this->assertTrue($ret['order_times'] == '0', "getUserOrderInfo:ret order_times not 0.");
//		$this->assertTrue($ret['order_date'] == '0', "getUserOrderInfo:ret order_date not 0.");
//		$this->assertTrue($ret['va_kitchen_info']['stock'][1]['id'] == 1, "getUserOrderInfo:ret va_kitchen_info id not 1.");
//		echo "第1次金币下厨，产量是 :".$ret['va_kitchen_info']['stock'][1]['num']."\n";
//		$userInfo = EnUser::getUser();
//		$this->assertTrue($oldGold == $userInfo['gold_num'] + 4, "user gold not sub 4.");
//
//		$userInfo = EnUser::getUser();
//		$oldGold = $userInfo['gold_num'];
//		$curTime = Util::getTime();
//		$ret = KitchenLogic::goldCook(1, true);
//		$this->assertTrue($ret['lv'] == '2', "getUserOrderInfo:ret lv not 2.");
//		$this->assertTrue($ret['exp'] == '0', "getUserOrderInfo:ret exp not 0.");
//		$this->assertTrue($ret['gold_cook_times'] == '2', "getUserOrderInfo:ret gold_cook_times not 2.");
//		$this->assertTrue($ret['gold_cook_date'] == $curTime, "getUserOrderInfo:ret gold_cook_date not ".$curTime);
//		$this->assertTrue($ret['cook_times'] == '1', "getUserOrderInfo:ret cook_times not 1.");
//		$this->assertTrue($ret['cook_date'] == $curTime, "getUserOrderInfo:ret cook_date not ".$curTime);
//		echo "第2次金币下厨，暴击！，产量是 :".$ret['va_kitchen_info']['stock'][1]['num']."\n";
//		$userInfo = EnUser::getUser();
//		$this->assertTrue($oldGold == $userInfo['gold_num'] + 7, "user gold not sub 7.");
//
//
//		echo "== "."KitchenLogic::goldCook_0 End ======================================"."\n";
//	}
//
//	/**
//	 * sell
//	 * sellAll
//	 * 
//	 * @group sell
//	 */
//	public function test_sell_0()
//	{
//		echo "\n== "."KitchenLogic::sell_0 Start ========================================"."\n";
//
//		EnKitchen::addNewKitchenInfoForUser($this->uid);
//
//		$ret = KitchenLogic::goldCook(1, false);
//		$ret = KitchenLogic::goldCook(1, false);
//		$num = $ret['va_kitchen_info']['stock'][1]['num'];
//
//		$ret = KitchenLogic::sell(1);
//		$this->assertTrue($ret == $num * 864, "getUserOrderInfo:ret not ".$num * 864);
//		$ret = KitchenLogic::getUserKitchenInfo($this->uid);
//		$this->assertTrue($ret['va_kitchen_info']['stock'][1]['num'] == 0, "getUserOrderInfo:ret va_kitchen_info num not 0.");
//
//		$ret = KitchenLogic::goldCook(1, false);
//		$ret = KitchenLogic::goldCook(1, false);
//		$ret = KitchenLogic::goldCook(2, false);
//		$ret = KitchenLogic::goldCook(2, false);
//		$num1 = $ret['va_kitchen_info']['stock'][1]['num'];
//		$num2 = $ret['va_kitchen_info']['stock'][2]['num'];
//
//		$curTime = Util::getTime();
//		$ret = KitchenLogic::sellAll();
//		$this->assertTrue($ret == $num1 * 864 + $num2 * 918, "getUserOrderInfo:ret not ".($num1 * 864 + $num2 * 918));
//
//		$ret = KitchenLogic::getUserKitchenInfo($this->uid);
//		$this->assertTrue($ret['uid'] == '29945', "getUserOrderInfo:ret uid not 29945.");
//		$this->assertTrue($ret['gold_cook_times'] == '0', "getUserOrderInfo:ret gold_cook_times not 0.");
//		$this->assertTrue($ret['gold_cook_date'] == $curTime, "getUserOrderInfo:ret gold_cook_date not ".$curTime);
//		$this->assertTrue($ret['cook_times'] == '0', "getUserOrderInfo:ret cook_times not 0.");
//		$this->assertTrue($ret['cook_date'] == $curTime, "getUserOrderInfo:ret cook_date not ".$curTime);
//		$this->assertTrue($ret['va_kitchen_info']['stock'][1]['num'] == 0, "getUserOrderInfo:ret va_kitchen_info num not 0.");
//		$this->assertTrue($ret['va_kitchen_info']['stock'][2]['num'] == 0, "getUserOrderInfo:ret va_kitchen_info num not 0.");
//
//		echo "== "."KitchenLogic::sell_0 End =========================================="."\n";
//	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */