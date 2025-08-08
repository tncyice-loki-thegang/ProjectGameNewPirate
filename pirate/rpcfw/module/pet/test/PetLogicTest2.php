<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PetLogicTest2.php 32722 2012-12-10 10:34:43Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/pet/test/PetLogicTest2.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-10 18:34:43 +0800 (一, 2012-12-10) $
 * @version $Revision: 32722 $
 * @brief 
 *  
 **/


class PetLogicTest2 extends PHPUnit_Framework_TestCase
{
	private $uid = 21483;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		parent::setUp ();
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
	}

	protected function tearDown()
	{
	}
	
	
	
	/**
	 * @group testNewPet
	 */
	public function test_testNewPet_0()
	{
		echo "\n== "."PetLogic::testNewPet_0 Start ===================="."\n";
		
//		$bag = BagManager::getInstance()->getBag();
//		$bag->addItemByTemplateID(200007, 999);
//		$bag->addItemByTemplateID(200001, 9);
//		$bag->addItemByTemplateID(200002, 9);
//		$bag->addItemByTemplateID(120010, 400);
//		var_dump($bag->update());


//		$ret = EnPet::getUserCurPetAllAttr($this->uid);

//		$ret = PetLogic::openWarehouseSlot();

//		$ret = PetLogic::putInWarehouse(4);
//		var_dump($ret);
//		$ret = PetLogic::putInWarehouse(8);
//		var_dump($ret);
//		$ret = PetLogic::putInWarehouse(11);
//		var_dump($ret);
//		$ret = PetLogic::putInWarehouse(13);
//		var_dump($ret);

//		$ret = PetLogic::getOutWarehouse(4);
//		var_dump($ret);
	
//		$ret = PetLogic::feedingOnce(4, 200007);
//		var_dump($ret);

//		$ret = PetLogic::feedingAll(4);
//		var_dump($ret);

//		$ret = PetLogic::evolution(4);
//		var_dump($ret);

//		$ret = PetLogic::refreshQualifications(4, 200002);
//		var_dump($ret);

//		$ret = PetLogic::commitRefresh(4);
//		var_dump($ret);

//		$ret = PetLogic::rollbackRefresh(4);
//		var_dump($ret);
		
//		$ret = PetLogic::getUserPetInfo();
//		var_dump($ret);

		$ret = PetLogic::transfer(14, 15, 0);
		var_dump($ret);


		echo "== "."PetLogic::testNewPet_0 End ======================"."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */