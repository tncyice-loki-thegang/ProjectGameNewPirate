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

require_once (LIB_ROOT . '/data/index.php');
require_once (MOD_ROOT . '/copy/index.php');
require_once (MOD_ROOT . '/copy/EnCopy.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class EnCopyTest extends PHPUnit_Framework_TestCase
{
	private $uid = 59135;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
	}

	
	public function test_getList_0()
	{
//		$ret = EnCopy::getCopyList(0, 100);
//		var_dump($ret);

		$ret = EnCopy::clearServerFight();
		var_dump($ret);
	}

//	/**
//	 * @group enterTownOpenCopies
//	 */
//	public function test_enterTownOpenCopies_0()
//	{
//		$arr = array(
//			'server' =>
//				 array (
//				   'round' => 4,
//				   'team1' =>
//				   array (
//				     0 =>
//					     array (
//					       'hid' => 10011409,
//					       'hp' => 585,
//					       'costHp' => 21,
//					     ),
//				     1 =>
//					     array (
//					       'hid' => 10011408,
//					       'hp' => 567,
//					       'costHp' => 33,
//					     ),
//				   ),
//				   'team2' =>
//				   array (
//				     0 =>
//					     array (
//					       'hid' => 100001,
//					       'hp' => 0,
//					       'costHp' => 550,
//					     ),
//				   ),
//				   'appraisal' => 'S',
//				   'uid1' => 29945,
//				   'uid2' => 16,
//				   'reward' =>
//				   array (
//				     'arrHero' =>
//				     array (
//				     ),
//				     'belly' => '20',
//				     'exp' => '10',
//				     'experience' => '2',
//				     'prestige' => '',
//				     'equip' =>
//				     array (
//				       'item' =>
//				       array (
//				       ),
//				       'bag' =>
//				       array (
//				       ),
//				       'heroID' => '',
//				     ),
//				   ),
//				   'brid' => 10621,
//				 )
//			);
//
//		echo "\n== "."CopyLogic::getDefeatScore Start =========="."\n";
//		
//		$ret = CopyLogic::getDefeatScore(1, 4, $arr);
//		var_dump($ret);
//		
//		$ret = CopyLogic::getDefeatScore(1, 5, $arr);
//		var_dump($ret);
//
//		$ret = CopyLogic::getDefeatScore(1, 6, $arr);
//		var_dump($ret);
//
//		$ret = CopyLogic::getDefeatScore(1, 7, $arr);
//		var_dump($ret);
//
//		$ret = CopyLogic::getDefeatScore(1, 8, $arr);
//		var_dump($ret);
//
//		$ret = CopyLogic::getDefeatScore(1, 9, $arr);
//		var_dump($ret);
//
//		$ret = CopyLogic::getPrize(1, 0);
//		var_dump($ret);
//
//		echo "== "."CopyLogic::getDefeatScore End ============"."\n";
//	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */