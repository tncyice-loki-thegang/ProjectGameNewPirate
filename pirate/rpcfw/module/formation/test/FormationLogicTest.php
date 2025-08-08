<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FormationLogicTest.php 15691 2012-03-05 12:03:49Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/formation/test/FormationLogicTest.php $
 * @author $Author: YangLiu $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-05 20:03:49 +0800 (一, 2012-03-05) $
 * @version $Revision: 15691 $
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/formation/index.php');
require_once (MOD_ROOT . '/user/index.php');

class FormationLogicTest extends PHPUnit_Framework_TestCase
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

		$user = EnUser::getInstance();
		$user->addExperience(7500);
		$user->update();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown ();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
		RPCContext::getInstance()->unsetSession('global.boatid');
	}

	/**
	 * @group openNewFormation
	 */
	public function test_openNewFormation_0()
	{
		// 删除旧数据
		$data = new CData();
		$data->delete()->from('t_hero_formation')->where(array('uid', '=', $this->uid))->query();

		echo "\n== "."FormationLogic::openNewFormation_0 Start =========="."\n";
		// 开启阵型
		$ret = FormationLogic::openNewFormation(10);
		$this->assertTrue(isset($ret[1]), "openNewFormation:ret empty");
		$this->assertTrue(isset($ret[1]['fid']), "openNewFormation:ret fid empty");
		$this->assertTrue($ret[1]['fid'] == 1, "openNewFormation:ret fid not 1");
		$this->assertTrue(isset($ret[1]['level']), "openNewFormation:ret level empty");
		$this->assertTrue(isset($ret[1]['hid1']), "openNewFormation:ret hid1 empty");
		$this->assertTrue(isset($ret[1]['hid9']), "openNewFormation:ret hid9 empty");

		echo "== "."FormationLogic::openNewFormation_0 End ============"."\n";
	}

	/**
	 * @group getAllFormation
	 */
	public function test_getAllFormation_0()
	{
		echo "\n== "."FormationLogic::getAllFormation_0 Start =========="."\n";
		// 获取所有阵型
		$ret = FormationLogic::getAllFormation();
		$this->assertTrue(isset($ret[1]), "getAllFormation:ret empty");
		$this->assertTrue(isset($ret[1]['fid']), "getAllFormation:ret fid empty");
		$this->assertTrue($ret[1]['fid'] == 1, "getAllFormation:ret fid not 1");
		$this->assertTrue(isset($ret[1]['level']), "getAllFormation:ret level empty");
		$this->assertTrue(isset($ret[1]['hid1']), "getAllFormation:ret hid1 empty");
		$this->assertTrue(isset($ret[1]['hid9']), "getAllFormation:ret hid9 empty");

		echo "== "."FormationLogic::getAllFormation_0 End ============"."\n";
	}

	/**
	 * @group getUserFormationByID
	 */
	public function test_getUserFormationByID_0()
	{
		echo "\n== "."FormationLogic::getUserFormationByID_0 Start =========="."\n";
		// 获取某用户某个阵型
		$ret = FormationLogic::getUserFormationByID(28823, 1);
		$this->assertTrue(isset($ret), "getUserFormationByID:ret empty");
		$this->assertTrue(isset($ret['fid']), "getUserFormationByID:ret fid empty");
		$this->assertTrue($ret['fid'] == 1, "getUserFormationByID:ret fid not 1");
		$this->assertTrue(isset($ret['level']), "getUserFormationByID:ret level empty");
		$this->assertTrue(isset($ret['hid1']), "getUserFormationByID:ret hid1 empty");
		$this->assertTrue(isset($ret['hid9']), "getUserFormationByID:ret hid9 empty");

		echo "== "."FormationLogic::getUserFormationByID_0 End ============"."\n";
	}

	/**
	 * @group getUserFormationByID
	 */
	public function test_getUserFormationByID_1()
	{
		echo "\n== "."FormationLogic::getUserFormationByID_1 Start =========="."\n";
		// 获取某用户某个阵型
		$ret = FormationLogic::getUserFormationByID($this->uid, 1);
		$this->assertTrue(isset($ret), "getUserFormationByID:ret empty");
		$this->assertTrue(isset($ret['fid']), "getUserFormationByID:ret fid empty");
		$this->assertTrue($ret['fid'] == 1, "getUserFormationByID:ret fid not 1.");
		$this->assertTrue(isset($ret['level']), "getUserFormationByID:ret level empty");
		$this->assertTrue($ret['level'] == 1, "getFormationByID:ret level not 1.");
		$this->assertTrue(isset($ret['hid1']), "getUserFormationByID:ret hid1 empty");
		$this->assertTrue(isset($ret['hid9']), "getUserFormationByID:ret hid9 empty");

		echo "== "."FormationLogic::getUserFormationByID_1 End ============"."\n";
	}

	/**
	 * @group setCurFormation
	 */
	public function test_setCurFormation_0()
	{
		echo "\n== "."FormationLogic::setCurFormation_0 Start =========="."\n";
		// 设置当前阵型
		$ret = FormationLogic::setCurFormation(2, array(0 => '0', 1 => '30001', 2 => '0',
		                                                3 => '0', 4 => '0', 5 => '0',
		                                                6 => '0', 7 => '0', 8 => '0'));
		$this->assertTrue(isset($ret), "setCurFormation:ret empty");
		$this->assertTrue($ret == 'ok', "setCurFormation:ret empty");

		echo "== "."FormationLogic::setCurFormation_0 End ============"."\n";
	}

	/**
	 * @group plusFormationLv
	 */
	public function test_plusFormationLv_0()
	{
		echo "\n== "."FormationLogic::plusFormationLv_0 Start =========="."\n";
		$ret = FormationLogic::plusFormationLv(2);
		$this->assertTrue(isset($ret), "plusFormationLv:ret empty");
		$this->assertTrue($ret == '2', "plusFormationLv:ret empty");
		
		echo "== "."FormationLogic::plusFormationLv_0 End ============"."\n";
	}

	/**
	 * @group changeCurFormation
	 */
	public function test_changeCurFormation_0()
	{
		echo "\n== "."FormationLogic::changeCurFormation_0 Start =========="."\n";

		try {
			FormationLogic::changeCurFormation(2, array(0 => '30000', 1 => '0', 2 => '0',
		                                                3 => '0', 4 => '0', 5 => '0',
		                                                6 => '30001', 7 => '0', 8 => '0'));
		}
		catch (Exception $e)
		{
			$this->assertTrue($e->getMessage() == 'fake', "changeCurFormation:ret not fake");
		}

		echo "== "."FormationLogic::changeCurFormation_0 End ============"."\n";
	}

	/**
	 * @group changeCurFormation
	 */
	public function test_changeCurFormation_1()
	{
		echo "\n== "."FormationLogic::changeCurFormation_1 Start =========="."\n";

		$ret = FormationLogic::changeCurFormation(2, array(0 => '30001', 1 => '0', 2 => '30000',
		                                                   3 => '0', 4 => '0', 5 => '0',
		                                                   6 => '0', 7 => '0', 8 => '0'));
		$this->assertTrue(isset($ret), "changeCurFormation:ret empty");
		$this->assertTrue($ret['affected_rows'] == '1', "changeCurFormation:ret affected_rows not 1");

		echo "== "."FormationLogic::changeCurFormation_1 End ============"."\n";
	}

	/**
	 * @group setCurFormation
	 */
	public function test_setCurFormation_1()
	{
		echo "\n== "."FormationLogic::setCurFormation_1 Start =========="."\n";
		// 设置当前阵型
		$ret = FormationLogic::setCurFormation(1, array(0 => '0', 1 => '30001', 2 => '0',
		                                                3 => '0', 4 => '0', 5 => '0',
		                                                6 => '0', 7 => '0', 8 => '0'));
		$this->assertTrue(isset($ret), "setCurFormation:ret empty");
		$this->assertTrue($ret == 'ok', "setCurFormation:ret empty");

		echo "== "."FormationLogic::setCurFormation_1 End ============"."\n";
	}

	/**
	 * @group plusFormationLv
	 */
	public function test_plusFormationLv_1()
	{
		echo "\n== "."FormationLogic::plusFormationLv_1 Start =========="."\n";
		$ret = FormationLogic::plusFormationLv(1);
		$this->assertTrue(isset($ret), "plusFormationLv:ret empty");
		$this->assertTrue($ret == '2', "plusFormationLv:ret empty");
		
		echo "== "."FormationLogic::plusFormationLv_1 End ============"."\n";
	}

	/**
	 * @group changeCurFormation
	 */
	public function test_changeCurFormation_2()
	{
		echo "\n== "."FormationLogic::changeCurFormation_2 Start =========="."\n";
		
		$ret = FormationLogic::changeCurFormation(1, array(0 => '0', 1 => '30001', 2 => '0',
		                                                   3 => '0', 4 => '30000', 5 => '0',
		                                                   6 => '0', 7 => '0', 8 => '0'));
		$this->assertTrue(isset($ret), "changeCurFormation:ret empty");
		$this->assertTrue($ret['affected_rows'] == '1', "changeCurFormation:ret affected_rows not 1");

		echo "== "."FormationLogic::changeCurFormation_2 End ============"."\n";
	}

	/**
	 * @group delHeroFromFormation
	 */
	public function test_delHeroFromFormation_0()
	{
		echo "\n== "."FormationLogic::delHeroFromFormation_0 Start =========="."\n";

		$ret = FormationLogic::delHeroFromFormation($this->uid, 30000);
		$this->assertTrue(isset($ret), "delHeroFromFormation:ret empty");
		$this->assertFalse($ret[1]['hid2'] == 6, "delHeroFromFormation:ret empty");
		$this->assertFalse($ret[2]['hid5'] == 6, "delHeroFromFormation:ret empty");

		echo "== "."FormationLogic::delHeroFromFormation_0 End ============"."\n";
	}

	/**
	 * @group getFormationByID
	 */
	public function test_getFormationByID_0()
	{
		echo "\n== "."FormationLogic::getFormationByID_0 Start =========="."\n";
		
		$ret = FormationLogic::getFormationByID(1);
		$this->assertTrue(isset($ret), "getFormationByID:ret empty");
		$this->assertTrue(isset($ret['fid']), "getFormationByID:ret fid empty");
		$this->assertTrue($ret['fid'] == 1, "getFormationByID:ret fid not 1");
		$this->assertTrue(isset($ret['level']), "getFormationByID:ret level empty");
		$this->assertTrue($ret['level'] == 2, "getFormationByID:ret level not 2.");
		$this->assertTrue(isset($ret['hid1']), "getFormationByID:ret hid1 empty");
		$this->assertTrue(isset($ret['hid9']), "getFormationByID:ret hid9 empty");

		echo "== "."FormationLogic::getFormationByID_0 End ============"."\n";
	}

	/**
	 * @group getFormationAttr
	 */
	public function test_getFormationAttr_0()
	{
		echo "\n== "."FormationLogic::getFormationAttr_0 Start =========="."\n";
		
		$ret = FormationLogic::getFormationAttr(1);
		$this->assertTrue(isset($ret), "getFormationAttr:ret empty");
		$this->assertTrue(isset($ret[7]), "getFormationAttr:ret 7 empty");
		$this->assertTrue($ret[7] == 30, "getFormationAttr:ret 7 not 30.");

		echo "== "."FormationLogic::getFormationAttr_0 End ============"."\n";
	}

	/**
	 * @group getFormationAttr
	 */
	public function test_getFormationAttr_1()
	{
		echo "\n== "."FormationLogic::getFormationAttr_1 Start =========="."\n";

		$ret = FormationLogic::getFormationAttr(1);
		$this->assertTrue(isset($ret), "getFormationAttr:ret empty");
		$this->assertTrue(isset($ret[7]), "getFormationAttr:ret 6 empty");
		$this->assertTrue($ret[7] == 30, "getFormationAttr:ret 6 not 20.");

		echo "== "."FormationLogic::getFormationAttr_1 End ============"."\n";
	}

	/**
	 * @group getFormationAttr
	 */
	public function test_getFormationAttr_2()
	{
		echo "\n== "."FormationLogic::getFormationAttr_2 Start =========="."\n";

		$ret = FormationLogic::getFormationAttr(2);
		$this->assertTrue(isset($ret), "getFormationAttr:ret empty");
		$this->assertTrue(isset($ret[9]), "getFormationAttr:ret 5 empty");
		$this->assertTrue($ret[9] == 30, "getFormationAttr:ret 5 not 20.");

		echo "== "."FormationLogic::getFormationAttr_2 End ============"."\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */