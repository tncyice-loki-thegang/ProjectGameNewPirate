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


class MyCopyTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
	}
	
	
	public function test_updateCopyInfo()
	{
		$ret = CopyDao::addUserDefeatNum(29946, 1, 1, 1);
		var_dump($ret);
	}

//	/**
//	 * @group addNewCopy
//	 */
//	public function test_addNewCopy_0()
//	{
//		// 删除旧数据
//		$data = new CData();
//		$data->delete()->from('t_copy')->where(array('uid', '=', $this->uid))->query();
//
//		echo "\n== "."MyCopy::addNewCopy_0 Start =========="."\n";
//		$copyID = 1;
//
//		$copyInst = new MyCopy();
//		$copyInst->addNewCopy($copyID);
//		$copyInst->save($copyID);
//
//		echo "== "."MyCopy::addNewCopy_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getUserCopies
//	 */
//	public function test_getUserCopies_0()
//	{
//		echo "\n== "."MyCopy::getUserCopies_0 Start =========="."\n";
//		$copyID = 1;
//
//		$copyInst = new MyCopy();
//		$ret = $copyInst->getUserCopies();
//		var_dump($ret);
//		$this->assertFalse(empty($ret), "getUserCopies:ret empty");
//		$this->assertTrue(count($ret) == '1', "getUserCopies:ret size not equal 1");
//		$this->assertTrue(isset($ret[$copyID]['uid']), "getUserCopies:ret uid empty");
//		$this->assertTrue(isset($ret[$copyID]['copy_id']), "getUserCopies:ret copy_id empty");
//		$this->assertTrue(isset($ret[$copyID]['raid_times']), "getUserCopies:ret raid_times empty");
//		$this->assertTrue($ret[$copyID]['raid_times'] == 0, "getUserCopies:ret status not equal 0");
//		$this->assertTrue(isset($ret[$copyID]['va_copy_info']['progress']), "getUserCopies:ret progress empty");
//		$this->assertTrue(isset($ret[$copyID]['va_copy_info']['defeat_id_times']), "getUserCopies:ret defeat_id_times empty");
//		$this->assertTrue(isset($ret[$copyID]['status']), "getUserCopies:ret status empty");
//		$this->assertTrue($ret[$copyID]['status'] == 1, "getUserCopies:ret status not equal 1");
//		echo "== "."MyCopy::getUserCopies_0 End ============"."\n";
//	}
//
//	/**
//	 * @group addNewCopy
//	 */
//	public function test_addNewCopy_1()
//	{
//		echo "\n== "."MyCopy::addNewCopy_1 Start =========="."\n";
//		$copyID = 1;
//
//		$copyInst = new MyCopy();
//		$copyInst->addNewCopy($copyID);
//		$copyInst->save($copyID);
//
//		echo "== "."MyCopy::addNewCopy_1 End ============"."\n";
//	}
//
//	/**
//	 * @group getUserCopies
//	 */
//	public function test_getUserCopies_1()
//	{
//		echo "\n== "."MyCopy::getUserCopies_1 Start =========="."\n";
//
//		$copyInst = new MyCopy();
//		$ret = $copyInst->getUserCopies();
//		$this->assertFalse(empty($ret), "getUserCopies:ret empty");
//		$this->assertTrue(count($ret) == '1', "getUserCopies:ret size not equal 1");
//		echo "== "."MyCopy::getUserCopies_1 End ============"."\n";
//	}
//
//	/**
//	 * @group addNewCopy
//	 */
//	public function test_addNewCopy_2()
//	{
//		echo "\n== "."MyCopy::addNewCopy_2 Start =========="."\n";
//		$copyID = 2;
//
//		$copyInst = new MyCopy();
//		$copyInst->addNewCopy($copyID);
//		$copyInst->save($copyID);
//
//		echo "== "."MyCopy::addNewCopy_2 End ============"."\n";
//	}
//
//	/**
//	 * @group getCopyInfo
//	 */
//	public function test_getCopyInfo_0()
//	{
//		echo "\n== "."MyCopy::getCopyInfo_0 Start =========="."\n";
//		$copyID = 2;
//
//		$copyInst = new MyCopy();
//		$ret = $copyInst->getCopyInfo($copyID);
//		var_dump($ret);
//		echo "== "."MyCopy::getCopyInfo_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getCopyInfo
//	 */
//	public function test_getCopyInfo_1()
//	{
//		echo "\n== "."MyCopy::getCopyInfo_1 Start =========="."\n";
//		$copyID = 3;
//	
//		$copyInst = new MyCopy();
//		$ret = $copyInst->getCopyInfo($copyID);
//		$this->assertFalse($ret, "getCopyInfo:ret not false");
//		echo "== "."MyCopy::getCopyInfo_1 End ============"."\n";
//	}
//
//	/**
//	 * @group updUserProgress
//	 */
//	public function test_updUserProgress_0()
//	{
//		echo "\n== "."MyCopy::updUserProgress_0 Start =========="."\n";
//		$copyID = 1;
//		$pro[2] = 2;
//
//		$copyInst = new MyCopy();
//		$copyInst->updUserProgress($copyID, $pro);
//		$copyInst->save($copyID);
//		
//		echo "== "."MyCopy::updUserProgress_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getCopyInfo
//	 */
//	public function test_getCopyInfo_2()
//	{
//		echo "\n== "."MyCopy::getCopyInfo_2 Start =========="."\n";
//		$copyID = 1;
//	
//		$copyInst = new MyCopy();
//		$ret = $copyInst->getCopyInfo($copyID);
//		var_dump($ret);
//
//		$this->assertTrue(isset($ret['va_copy_info']['progress']), "getUserCopies:ret progress empty");
//		$this->assertTrue($ret['va_copy_info']['progress'][2] == 2, "getUserCopies:ret progress not 2");
//		echo "== "."MyCopy::getCopyInfo_2 End ============"."\n";
//	}
//
//	/**
//	 * @group updUserDefeatNum
//	 */
//	public function test_updUserDefeatNum_0()
//	{
//		echo "\n== "."MyCopy::updUserDefeatNum_0 Start =========="."\n";
//		$copyID = 1;
//		$list[1] = 11;
//
//		$copyInst = new MyCopy();
//		$copyInst->updUserDefeatNum($copyID, $list);
//		$copyInst->save($copyID);
//
//		echo "== "."MyCopy::updUserDefeatNum_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getCopyInfo
//	 */
//	public function test_getCopyInfo_3()
//	{
//		echo "\n== "."MyCopy::getCopyInfo_3 Start =========="."\n";
//		$copyID = 1;
//	
//		$copyInst = new MyCopy();
//		$ret = $copyInst->getCopyInfo($copyID);
//		var_dump($ret);
//
//		$this->assertTrue(isset($ret['va_copy_info']['defeat_id_times']), "getUserCopies:ret defeat_id_times empty");
//		$this->assertTrue($ret['va_copy_info']['defeat_id_times'][1] == 11, "getUserCopies:ret defeat_id 1 times not 11");
//		echo "== "."MyCopy::getCopyInfo_3 End ============"."\n";
//	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */