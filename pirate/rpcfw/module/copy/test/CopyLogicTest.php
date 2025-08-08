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
require_once (MOD_ROOT . '/formation/index.php');
require_once (MOD_ROOT . '/copy/index.php');
require_once (MOD_ROOT . '/copy/EnCopy.class.php');
require_once (MOD_ROOT . '/copy/MyCopy.class.php');
require_once (MOD_ROOT . '/copy/CopyLogic.class.php');
require_once (MOD_ROOT . '/copy/CopyDao.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class CopyLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 31033;

	protected static function getMethod($name) 
	{
		$class = new ReflectionClass('CopyLogic');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}


	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		RPCContext::getInstance()->setSession('user.user', UserLogic::getUser($this->uid));
	}

//	/**
//	 * getLatestRefreshEnemies
//	 */
//	public function test_getLatestRefreshEnemies_0()
//	{
//		// 删除旧数据
//		$data = new CData();
//		$data->delete()->from('t_first_down')->where(array('army_id', '=', 2))->query();
//		$data->delete()->from('t_user_defeat')->where(array('uid', '=', $this->uid))->query();
//		$data->delete()->from('t_replay')->where(array('army_id', '=', 2))->query();
//
//		echo "\n== "."CopyLogic::getLatestRefreshEnemies_0 Start =========="."\n";
//		$copyID = 1;
//
//		$ret = CopyLogic::getLatestRefreshEnemies($copyID);
//		var_dump($ret);
//		
//		echo "== "."CopyLogic::getLatestRefreshEnemies_0 End ============"."\n";
//	}
//
//	/**
//	 * getLatestRefreshEnemies
//	 */
//	public function test_getLatestRefreshEnemies_1()
//	{
//		echo "\n== "."CopyLogic::getLatestRefreshEnemies_1 Start =========="."\n";
//		$copyID = 2;
//	
//		$ret = CopyLogic::getLatestRefreshEnemies($copyID);
//		var_dump($ret);
//		
//		echo "== "."CopyLogic::getLatestRefreshEnemies_1 End ============"."\n";
//	}
//
//	/**
//	 * @group getUserCopies
//	 */
//	public function test_getUserCopies_0()
//	{
//		echo "\n== "."CopyLogic::getUserCopies_0 Start =========="."\n";
//		$copyID = 1;
//
//		$ret = CopyLogic::getUserCopies();
////		var_dump($ret);
//
//		$this->assertFalse(empty($ret), "getUserCopies:ret empty");
//		$this->assertTrue(isset($ret[$copyID]['uid']), "getUserCopies:ret uid empty");
//		$this->assertTrue(isset($ret[$copyID]['copy_id']), "getUserCopies:ret copy_id empty");
//		$this->assertTrue(isset($ret[$copyID]['raid_times']), "getUserCopies:ret raid_times empty");
//		$this->assertTrue($ret[$copyID]['raid_times'] == 0, "getUserCopies:ret status not equal 0");
//		$this->assertTrue(isset($ret[$copyID]['va_copy_info']['progress']), "getUserCopies:ret progress empty");
//		$this->assertTrue(isset($ret[$copyID]['va_copy_info']['defeat_id_times']), "getUserCopies:ret defeat_id_times empty");
//		$this->assertTrue(isset($ret[$copyID]['status']), "getUserCopies:ret status empty");
//		$this->assertTrue($ret[$copyID]['status'] == 1, "getUserCopies:ret status not equal 1");
//		echo "== "."CopyLogic::getUserCopies_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getCopyInfo
//	 */
//	public function test_getCopyInfo_0()
//	{
//		echo "\n== "."CopyLogic::getCopyInfo_0 Start =========="."\n";
//		$copyID = 1;
//		
//		$ret = CopyLogic::getCopyInfo($copyID);
//		var_dump($ret);
//
//		echo "== "."CopyLogic::getCopyInfo_0 End ============"."\n";
//	}
//
//	/**
//	 * getUserPlayTimes
//	 */
//	public function test_getUserPlayTimes_0()
//	{
//		echo "\n== "."CopyLogic::getUserPlayTimes_0 Start =========="."\n";
//		$copyID = 1;
//
//		$ret = CopyLogic::getUserPlayTimes($copyID);
//		$this->assertTrue($ret == 0, "getUserPlayTimes:ret not 0");
//
//		echo "== "."CopyLogic::getUserPlayTimes_0 End ============"."\n";
//	}
//
//	/**
//	 * @group addKillNum
//	 */
//	public function test_addKillNum_0() 
//	{
//		echo "\n== "."CopyLogic::addKillNum_0 Start =========="."\n";
//		$copyID = 1;
//
//		$foo = self::getMethod('addKillNum');
//		$ret = $foo->invokeArgs(null, array($copyID, 128));
//		$copyInst = new MyCopy();
//		$copyInst->save($copyID);
//
//		echo "== "."CopyLogic::addKillNum_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getCopyInfo
//	 */
//	public function test_getCopyInfo_1()
//	{
//		echo "\n== "."CopyLogic::getCopyInfo_1 Start =========="."\n";
//		$copyID = 1;
//
//		$ret = CopyLogic::getCopyInfo($copyID);
//
//		$this->assertFalse(empty($ret), "getCopyInfo:ret empty");
//		$this->assertTrue(isset($ret['copyInfo']['va_copy_info']['defeat_id_times']), "getCopyInfo:ret defeat_id_times empty");
//		$this->assertTrue($ret['copyInfo']['va_copy_info']['defeat_id_times'][128] == 1, "getCopyInfo:ret defeat_id_times 128 not 1");
//
//		echo "== "."CopyLogic::getCopyInfo_1 End ============"."\n";
//	}
//
//	/**
//	 * @group saveProgress
//	 */
//	public function test_saveProgress_0() 
//	{
//		echo "\n== "."CopyLogic::saveProgress_0 Start =========="."\n";
//		$copyID = 1;
//
//		$foo = self::getMethod('saveProgress');
//		$ret = $foo->invokeArgs(null, array($copyID, 2));
//		$copyInst = new MyCopy();
//		$copyInst->save($copyID);
//
//		echo "== "."CopyLogic::saveProgress_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getCopyInfo
//	 */
//	public function test_getCopyInfo_2()
//	{
//		echo "\n== "."CopyLogic::getCopyInfo_2 Start =========="."\n";
//		$copyID = 1;
//
//		$ret = CopyLogic::getCopyInfo($copyID);
////		var_dump($ret);
//		$this->assertFalse(empty($ret), "getCopyInfo:ret empty");
//		$this->assertTrue(isset($ret['copyInfo']['va_copy_info']['progress'][3]), "getCopyInfo:ret progress empty");
//
//		echo "== "."CopyLogic::getCopyInfo_2 End ============"."\n";
//	}
//
//	/**
//	 * @group isCopyOver
//	 */
//	public function test_isCopyOver_0() 
//	{
//		echo "\n== "."CopyLogic::isCopyOver_0 Start =========="."\n";
//		$copyID = 1;
//
//		$foo = self::getMethod('isCopyOver');
//		$ret = $foo->invokeArgs(null, array($copyID));
//		$this->assertTrue($ret == 'no', "isCopyOver:ret not no");
//		echo "== "."CopyLogic::isCopyOver_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getVictoryConditions.
//	 */
//	public function test_getVictoryConditions_0()
//	{
//		echo "\n== "."CopyLogic::getVictoryConditions_0 Start =========="."\n";
//		$enemyID = 2;
//
//		$foo = self::getMethod('getVictoryConditions');
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		var_dump($ret);
//		$this->assertTrue(isset($ret), "getVictoryConditions:ret empty");
//		$this->assertTrue(isset($ret['monster_hp']), "getVictoryConditions:ret not have monster_hp");
//
//		echo "== "."CopyLogic::getVictoryConditions_0 End ============"."\n";
//	}
//
//	/**
//	 * @group addUserKillNum.
//	 */
//	public function test_addUserKillNum_0()
//	{
//		echo "\n== "."CopyLogic::addUserKillNum_0 Start =========="."\n";
//		$enemyID = 2;
//
//		$foo = self::getMethod('addUserKillNum');
//		$foo->invokeArgs(null, array($enemyID));
//
//		echo "== "."CopyLogic::addUserKillNum_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getEnemyDefeatNum.
//	 */
//	public function test_getEnemyDefeatNum_0()
//	{
//		echo "\n== "."CopyLogic::getEnemyDefeatNum_0 Start =========="."\n";
//		$enemyID = 2;
//
//		$foo = self::getMethod('getEnemyDefeatNum');
//		$ret = $foo->invokeArgs(null, array($this->uid, $enemyID));
//		$this->assertTrue(isset($ret['userDefeat']), "getEnemyDefeatNum:ret not have userDefeat");
//		$this->assertTrue(isset($ret['serverDefeat']), "getEnemyDefeatNum:ret not have serverDefeat");
//		$this->assertTrue($ret['userDefeat']['annihilate'] == 1, "getEnemyDefeatNum:ret annihilate@userDefeat not 1");
//
//		echo "== "."CopyLogic::getEnemyDefeatNum_0 End ============"."\n";
//	}
//
//	/**
//	 * @group addServerKillNum
//	 */
//	public function test_addServerKillNum_0()
//	{
//		echo "\n== "."CopyLogic::addServerKillNum_0 Start =========="."\n";
//		$enemyID = 2;
//
//		CopyDao::clearServerDefeatNum($enemyID, 0, 0);
//
//		$foo = self::getMethod('addServerKillNum');
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		$this->assertTrue($ret, "addServerKillNum:ret not true");
//
//		echo "== "."CopyLogic::addServerKillNum_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getEnemyDefeatNum.
//	 */
//	public function test_getEnemyDefeatNum_1()
//	{
//		echo "\n== "."CopyLogic::getEnemyDefeatNum_1 Start =========="."\n";
//		$enemyID = 2;
//
//		$foo = self::getMethod('getEnemyDefeatNum');
//		$ret = $foo->invokeArgs(null, array($this->uid, $enemyID));
//		$this->assertTrue(isset($ret['userDefeat']), "getEnemyDefeatNum:ret not have userDefeat");
//		$this->assertTrue(isset($ret['serverDefeat']), "getEnemyDefeatNum:ret not have serverDefeat");
//		$this->assertTrue($ret['serverDefeat'] == 19, "getEnemyDefeatNum:ret annihilate@serverDefeat not 19");
//
//		echo "== "."CopyLogic::getEnemyDefeatNum_1 End ============"."\n";
//	}
//
//	/**
//	 * @group checkFirstDown.
//	 */
//	public function test_checkFirstDown_0()
//	{
//		echo "\n== "."CopyLogic::checkFirstDown_0 Start =========="."\n";
//		$enemyID = 2;
//
//		$foo = self::getMethod('checkFirstDown');
//		$ret = $foo->invokeArgs(null, array($this->uid, $enemyID));
//		$this->assertTrue($ret, "checkFirstDown:ret not true");
//		
//		echo "== "."CopyLogic::checkFirstDown_0 End ============"."\n";
//	}
//
//	/**
//	 * @group checkFirstDown.
//	 */
//	public function test_checkFirstDown_1()
//	{
//		echo "\n== "."CopyLogic::checkFirstDown_1 Start =========="."\n";
//		$enemyID = 2;
//
//		$foo = self::getMethod('checkFirstDown');
//		$ret = $foo->invokeArgs(null, array($this->uid, $enemyID));
//		$this->assertFalse($ret, "checkFirstDown:ret not false");
//		
//		echo "== "."CopyLogic::checkFirstDown_1 End ============"."\n";
//	}
//
//	/**
//	 * @group checkFirstDown.
//	 */
//	public function test_checkFirstDown_2()
//	{
//		echo "\n== "."CopyLogic::checkFirstDown_2 Start =========="."\n";
//		$enemyID = 2;
//
//		$foo = self::getMethod('checkFirstDown');
//		$ret = $foo->invokeArgs(null, array(11, $enemyID));
//		$ret = $foo->invokeArgs(null, array(12, $enemyID));
//		$ret = $foo->invokeArgs(null, array(13, $enemyID));
//		$ret = $foo->invokeArgs(null, array(14, $enemyID));
//		$ret = $foo->invokeArgs(null, array(15, $enemyID));
//		$ret = $foo->invokeArgs(null, array(16, $enemyID));
//		$ret = $foo->invokeArgs(null, array(17, $enemyID));
//		$ret = $foo->invokeArgs(null, array(18, $enemyID));
//		$ret = $foo->invokeArgs(null, array(19, $enemyID));
//		$ret = $foo->invokeArgs(null, array(10, $enemyID));
//		$this->assertFalse($ret, "checkFirstDown:ret not false");
//		
//		echo "== "."CopyLogic::checkFirstDown_2 End ============"."\n";
//	}
//
//	/**
//	 * @group checkServerDefeat.
//	 */
//	public function test_checkServerDefeat_0()
//	{
//		echo "\n== "."CopyLogic::checkServerDefeat_0 Start =========="."\n";
//		$enemyID = 1;
//
//		$foo = self::getMethod('checkServerDefeat');
//		$ret = $foo->invokeArgs(null, array($enemyID, $this->uid, false));
//		$this->assertTrue(isset($ret['ret']), "checkServerDefeat:ret empty");
//		$this->assertTrue($ret['ret'] == 'user_ok', "checkServerDefeat:ret not user_ok");
//	
//		echo "== "."CopyLogic::checkServerDefeat_0 End ============"."\n";
//	}
//
//	/**
//	 * @group checkServerDefeat.
//	 */
//	public function test_checkServerDefeat_1()
//	{
//		echo "\n== "."CopyLogic::checkServerDefeat_1 Start =========="."\n";
//		$enemyID = 2;
//
//		$foo = self::getMethod('addServerKillNum');
//		$ret = $foo->invokeArgs(null, array($enemyID));
//
//		$foo = self::getMethod('checkServerDefeat');
//		$ret = $foo->invokeArgs(null, array($enemyID, $this->uid, false));
//		$this->assertTrue(isset($ret['ret']), "checkServerDefeat:ret empty");
//		$this->assertTrue($ret['ret'] == 'no', "checkServerDefeat:ret not no");
//	
//		echo "== "."CopyLogic::checkServerDefeat_1 End ============"."\n";
//	}
//
//	/**
//	 * @group checkServerDefeat.
//	 */
//	public function test_checkServerDefeat_2()
//	{
//		echo "\n== "."CopyLogic::checkServerDefeat_2 Start =========="."\n";
//		$enemyID = 2;
//
//		$foo = self::getMethod('checkServerDefeat');
//		$ret = $foo->invokeArgs(null, array($enemyID, $this->uid, true));
//		$this->assertTrue(isset($ret['ret']), "checkServerDefeat:ret empty");
//		$this->assertTrue($ret['ret'] == 'force_ok', "checkServerDefeat:ret not force_ok");
//	
//		echo "== "."CopyLogic::checkServerDefeat_2 End ============"."\n";
//	}
//
//	/**
//	 * @group checkUserFormation.
//	 */
//	public function test_checkUserFormation_0()
//	{
//		echo "\n== "."CopyLogic::checkUserFormation_0 Start =========="."\n";
//
//		$foo = self::getMethod('checkUserFormation');
//		$ret = $foo->invokeArgs(null, array(array()));
//		$this->assertTrue(isset($ret), "checkUserFormation:ret empty");
//		$this->assertTrue($ret == 'for_empty', "checkUserFormation:ret not for_empty");
//	
//		echo "== "."CopyLogic::checkUserFormation_0 End ============"."\n";		
//	}
//
//	/**
//	 * @group canAttack.
//	 */
//	public function test_canAttack_0()
//	{
//		echo "\n== "."CopyLogic::canAttack_0 Start =========="."\n";
//		$enemyID = 2;
//		$copyID = 1;
//
//		$foo = self::getMethod('canAttack');
//		$ret = $foo->invokeArgs(null, array($enemyID, $this->uid, $copyID));
//		$this->assertTrue($ret, "canAttack:ret not true");
//	
//		echo "== "."CopyLogic::canAttack_0 End ============"."\n";
//	}
//
//	/**
//	 * @group canAttack.
//	 */
//	public function test_canAttack_1()
//	{
//		echo "\n== "."CopyLogic::canAttack_1 Start =========="."\n";
//		$enemyID = 3;
//		$copyID = 1;
//
//		$foo = self::getMethod('canAttack');
//		$ret = $foo->invokeArgs(null, array($enemyID, $this->uid, $copyID));
//		$this->assertFalse($ret, "canAttack:ret not false");
//	
//		echo "== "."CopyLogic::canAttack_1 End ============"."\n";
//	}
//
//	/**
//	 * @group calculateFightRet.
//	 */
//	public function test_calculateFightRet_0()
//	{
//		echo "\n== "."CopyLogic::calculateFightRet_0 Start =========="."\n";
//
//		$foo = self::getMethod('calculateFightRet');
//		$ret = $foo->invokeArgs(null, array(array('teamId' => 1, 'appraisal' => 'A')));
//		$this->assertTrue($ret['belly'] == '100', "calculateFightRet:ret belly not 100");
//		$this->assertTrue($ret['exp'] == '100', "calculateFightRet:ret exp not 100");
//		$this->assertTrue($ret['experience'] == '100', "calculateFightRet:ret experience not 100");
//		$this->assertTrue($ret['prestige'] == '100', "calculateFightRet:ret prestige not 100");
//
//		echo "== "."CopyLogic::calculateFightRet_0 End ============"."\n";
//	}
//
//	/**
//	 * @group calculateFightRet.
//	 */
//	public function test_calculateFightRet_1()
//	{
//		echo "\n== "."CopyLogic::calculateFightRet_1 Start =========="."\n";
//
//		$foo = self::getMethod('calculateFightRet');
//		$ret = $foo->invokeArgs(null, array(array('teamId' => 1, 'appraisal' => 'F')));
//		$this->assertTrue($ret['exp'] == '25', "calculateFightRet:ret exp not 25");
//
//		echo "== "."CopyLogic::calculateFightRet_1 End ============"."\n";
//	}
//
//	/**
//	 * @group needOpenNewCopies.
//	 */
//	public function test_needOpenNewCopies_0()
//	{
//		echo "\n== "."CopyLogic::needOpenNewCopies_0 Start =========="."\n";
//		$enemyID = 1;
//
//		$foo = self::getMethod('needOpenNewCopies');
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		
//		echo "== "."CopyLogic::needOpenNewCopies_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getUserCopies
//	 */
//	public function test_getUserCopies_1()
//	{
//		echo "\n== "."CopyLogic::getUserCopies_1 Start =========="."\n";
//
//		$ret = CopyLogic::getUserCopies();
//		$this->assertTrue(count($ret) == 2, "getUserCopies:ret count not equal 2");
//
//		echo "== "."CopyLogic::getUserCopies_1 End ============"."\n";
//	}
//
//	/**
//	 * @group needOpenNewCopies.
//	 */
//	public function test_needOpenNewCopies_1()
//	{
//		echo "\n== "."CopyLogic::needOpenNewCopies_1 Start =========="."\n";
//		$enemyID = 9;
//
//		$foo = self::getMethod('needOpenNewCopies');
//		$ret = $foo->invokeArgs(null, array($enemyID));
//		
//		echo "== "."CopyLogic::needOpenNewCopies_1 End ============"."\n";
//	}
//
//	/**
//	 * @group getUserCopies.
//	 */
//	public function test_getUserCopies_2()
//	{
//		echo "\n== "."CopyLogic::getUserCopies_2 Start =========="."\n";
//
//		$ret = CopyLogic::getUserCopies();
//		$this->assertTrue(count($ret) == 3, "getUserCopies:ret count not equal 3");
//
//		echo "== "."CopyLogic::getUserCopies_2 End ============"."\n";
//	}

	/**
	 * @group isEnemyDefeated.
	 */
	public function test_isEnemyDefeated_0()
	{
		echo "\n== "."CopyLogic::isEnemyDefeated_0 Start =========="."\n";
		$enemyID = 706;
		
		$ret = CopyLogic::isEnemyDefeated($enemyID);
		$this->assertTrue($ret == 0, "isEnemyDefeated:ret not 0");
		
		echo "== "."CopyLogic::isEnemyDefeated_0 End ============"."\n";
	}
//
//	/**
//	 * @group attack.
//	 */
//	public function test_attack_0()
//	{
//		echo "\n== "."CopyLogic::attack_0 Start =========="."\n";
//		$enemyID = 2;
//		$copyID = 1;
//		FormationLogic::changeCurFormation(10001, array(0 => '0', 1 => '7', 2 => '0',
//                                                        3 => '0', 4 => '6', 5 => '0',
//                                                        6 => '0', 7 => '0', 8 => '0'));
//
//		$ret = CopyLogic::attack($copyID, $enemyID);
//		var_dump($ret);
//	
//		echo "== "."CopyLogic::attack_0 End ============"."\n";
//	}
//
//	/**
//	 * @group getCopyInfo
//	 */
//	public function test_getCopyInfo_3()
//	{
//		echo "\n== "."CopyLogic::getCopyInfo_3 Start =========="."\n";
//		$copyID = 1;
//
//		$ret = CopyLogic::getCopyInfo($copyID);
//
//		$this->assertFalse(empty($ret), "getCopyInfo:ret empty");
//		$this->assertTrue($ret['copyInfo']['va_copy_info']['defeat_id_times'][2] == 1, "getCopyInfo:ret defeat_id_times 2 not 1");
//
//		echo "== "."CopyLogic::getCopyInfo_3 End ============"."\n";
//	}
//
//	/**
//	 * @group isEnemyDefeated.
//	 */
//	public function test_isEnemyDefeated_1()
//	{
//		echo "\n== "."CopyLogic::isEnemyDefeated_1 Start =========="."\n";
//		$enemyID = 2;
//		
//		$ret = CopyLogic::isEnemyDefeated($enemyID);
//		$this->assertFalse($ret == 0, "isEnemyDefeated:ret not 0");
//
//		echo "== "."CopyLogic::isEnemyDefeated_1 End ============"."\n";
//	}
//
//	/**
//	 * @group getEnemyDefeatNum.
//	 */
//	public function test_getEnemyDefeatNum_2()
//	{
//		echo "\n== "."CopyLogic::getEnemyDefeatNum_2 Start =========="."\n";
//		$enemyID = 2;
//
//		$foo = self::getMethod('getEnemyDefeatNum');
//		$ret = $foo->invokeArgs(null, array($this->uid, $enemyID));
//		$this->assertTrue(isset($ret['userDefeat']), "getEnemyDefeatNum:ret not have userDefeat");
//		$this->assertTrue(isset($ret['serverDefeat']), "getEnemyDefeatNum:ret not have serverDefeat");
//		$this->assertTrue($ret['userDefeat']['annihilate'] == 2, "getEnemyDefeatNum:ret annihilate@userDefeat not 2");
//
//		echo "== "."CopyLogic::getEnemyDefeatNum_2 End ============"."\n";
//	}
//
//	/**
//	 * @group attack.
//	 */
//	public function test_attack_1()
//	{
//		echo "\n== "."CopyLogic::attack_1 Start =========="."\n";
//		$enemyID = 1;
//		$copyID = 1;
//
//		$ret = CopyLogic::attack($copyID, $enemyID);
////		var_dump($ret);
//		$this->assertTrue($ret == 'err', "attack:ret not err");
//
//		echo "== "."CopyLogic::attack_1 End ============"."\n";
//	}
//
//	/**
//	 * @group attack.
//	 */
//	public function test_attack_2()
//	{
//		echo "\n== "."CopyLogic::attack_2 Start =========="."\n";
//		$enemyID = 1;
//		$copyID = 1;
//
//		EnCopy::ClearFightCdByGold();
//
//		$ret = CopyLogic::attack($copyID, $enemyID);
//		var_dump($ret);
//
//		echo "== "."CopyLogic::attack_2 End ============"."\n";
//	}
//
//	/**
//	 * @group checkSaveReplay.
//	 */
//	public function test_checkSaveReplay_0()
//	{
//		echo "\n== "."CopyLogic::checkSaveReplay_0 Start =========="."\n";
//		$enemyID = 2;
//
//		$foo = self::getMethod('checkSaveReplay');
//		$foo->invokeArgs(null, array($this->uid, $enemyID, 123));
//		
//		echo "== "."CopyLogic::checkSaveReplay_0 End ============"."\n";
//	}
//
//	/**
//	 * @group checkSaveReplay.
//	 */
//	public function test_checkSaveReplay_1()
//	{
//		echo "\n== "."CopyLogic::checkSaveReplay_1 Start =========="."\n";
//		$enemyID = 2;
//
//		sleep(1);
//		$foo = self::getMethod('checkSaveReplay');
//		$foo->invokeArgs(null, array(2, $enemyID, 2));
//		sleep(1);
//		$foo->invokeArgs(null, array(3, $enemyID, 3));
//		$foo->invokeArgs(null, array(4, $enemyID, 4));
//		$foo->invokeArgs(null, array(5, $enemyID, 5));
//		$foo->invokeArgs(null, array(6, $enemyID, 6));
//		$foo->invokeArgs(null, array(7, $enemyID, 7));
//		$foo->invokeArgs(null, array(8, $enemyID, 8));
//		$foo->invokeArgs(null, array(9, $enemyID, 9));
//		$foo->invokeArgs(null, array(10, $enemyID, 10));
//		$foo->invokeArgs(null, array(11, $enemyID, 11));
//		$foo->invokeArgs(null, array(12, $enemyID, 12));
//		$foo->invokeArgs(null, array(13, $enemyID, 13));
//		$foo->invokeArgs(null, array(14, $enemyID, 14));
//		$foo->invokeArgs(null, array(15, $enemyID, 15));
//		
//		echo "== "."CopyLogic::checkSaveReplay_1 End ============"."\n";
//	}
//
//	/**
//	 * @group checkSaveReplay.
//	 */
//	public function test_checkSaveReplay_3()
//	{
//		echo "\n== "."CopyLogic::checkSaveReplay_3 Start =========="."\n";
//		$enemyID = 2;
//
//		sleep(1);
//		$foo = self::getMethod('checkSaveReplay');
//		$foo->invokeArgs(null, array(199, $enemyID, 199));
//		sleep(1);
//		$foo->invokeArgs(null, array(299, $enemyID, 299));
//		sleep(1);
//		$foo->invokeArgs(null, array(399, $enemyID, 399));
//		sleep(1);
//		$foo->invokeArgs(null, array(499, $enemyID, 499));
//		sleep(1);
//		$foo->invokeArgs(null, array(599, $enemyID, 599));
//		sleep(1);
//		
//		echo "== "."CopyLogic::checkSaveReplay_3 End ============"."\n";
//	}


	// 
	// afterAttack
	// 
	// isCopyOver
	// 
	// 
	// getEnemyRefreshPointID


	// calculateSuccess
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */