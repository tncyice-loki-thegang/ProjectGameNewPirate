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

class GroupBattleTest extends PHPUnit_Framework_TestCase
{
	private $uid = 20103;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
	}

	/**
	 * @group groupAttack
	 */
	public function test_groupAttack_0()
	{
		echo "\n== "."GroupBattle::groupAttack_0 Start =========="."\n";

		$ret = GroupBattleBase::getCommonGroupArmyDefeatNum();
		var_dump($ret);
//		$ret = GroupBattleBase::getActivityGroupArmyDefeatNum();
//		var_dump($ret);

		$list = array('21509' => array('uid' => 21509, 'utid' => 1, 'uname' => 'sb1'), 
					  '21300' => array('uid' => 21300, 'utid' => 1, 'uname' => 'sb2'),
					  '20317' => array('uid' => 20317, 'utid' => 1, 'uname' => 'sb3'),
  					  '56956' => array('uid' => 56956, 'utid' => 1, 'uname' => 'sb4'));
//		$ret = AutoGroupBattle::saveInviteSetting();
//		var_dump($ret);

//		$groupBattleInfo = CopyDao::getGroupBattleInfo($this->uid);
//		$groupBattleInfo['va_copy_info']['invite_set'] = $list;
//		CopyDao::updateGroupBattle($groupBattleInfo['uid'], $groupBattleInfo);
//
//		$ret = AutoGroupBattle::getInviteSetting();
//		var_dump($ret);


//		$ret = CommonGroupBattle::createTeam(110001, true, 10);
//		var_dump($ret);
//		$ret = CommonGroupBattle::joinTeam(110001, 1);
//		var_dump($ret);

//		$inst = new AutoGroupBattle();
//		$inst->startAttack(110001, array(20103, 46167, 45302, 33222, 47080));

//		$inst = new CommonGroupBattle();
//		$inst->startAttack(110001, array(20103, 46167, 45302, 33222, 47080));


		echo "== "."GroupBattle::groupAttack_0 End ============"."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */