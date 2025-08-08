<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldwarTest.class.php 40910 2013-03-18 09:51:41Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldwar/test/WorldwarTest.class.php $
 * @author $Author: YangLiu $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-03-18 17:51:41 +0800 (一, 2013-03-18) $
 * @version $Revision: 40910 $
 * @brief 
 *  
 **/
class WorldwarTest extends PHPUnit_Framework_TestCase
{
	
	private $uid = 21300;
	private $worldWar;

	protected static function getMethod($name) 
	{
		$class = new ReflectionClass('ActiveLogic');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		RPCContext::getInstance()->unsetSession('user.worldwar');
//		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		$this->worldWar = new Worldwar();
		parent::setUp ();
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('user.worldwar');
		RPCContext::getInstance()->unsetSession('global.uid');
		MyWorldwar::release();
	}

	protected function signUp()
	{
		$data = new CData();
		$arrRet = $data->select(array('uid'))
		               ->from("t_user")
		               ->where(array("vip", ">", 7))
					   ->query();

		foreach ($arrRet as $uid)
		{
			$user = EnUser::getUserObj($uid['uid']);
			echo "uid:".$user->getUid()."\n";
			$set = array('uid' => $user->getUid(),
						 'win_team_lose_times' => 0, 
						 'lose_team_lose_times' => 0, 
			             'team' => 0,
			             'cheer_uid' => 0,
			             'cheer_uid_server_id' => '0',
			             'cheer_time' => 0,
			             'worship_times' => 0,
			             'worship_time' => 0,
						 'update_fmt_time' => Util::getTime(),
			             'sign_time' => Util::getTime(),
						 'sign_session' => 1,
						 'group_prize_id' => 0,
						 'group_prize_time' => 0,
						 'world_prize_id' => 0,
						 'world_prize_time' => 0,
			             'va_world_war' => array('replay' => array(), 
			             						 'cheer_obj' => array(), 
			             						 'fight_para' => WorldwarUtil::getUserForBattle($user)));
			try {
					$data = new CData();
					$arrRet = $data->insertInto("t_user_world_war")
					               ->values($set)->query();
			}
			catch (Exception $e)
			{
				echo $e."\n";
			}
		}
	}

	private function testCheer()
	{
		
		for ($index = 3; $index <= 14; ++$index)
		{
			echo $index." ======================================\n";
			if ($index == 8 || $index == 9)
			{
				continue;
			}
			echo "========================================\n";

    		$step = WorldwarDef::$step[$index];
	    	for ($i = 0; $i < WorldwarDef::MAX_JOIN_NUM; $i += $step)
	    	{
	    		// 看看他是否已经进入32强
				for ($j = $i; $j < $i + $step; ++$j)
	    		{
	    			echo $j." ";
	    		}
	    	}
	    	echo "\n";
		}
	}
	
	/**
	 * @group test_Worldwar 
	 */
	public function test_Worldwar()
	{
//		$ret = $this->worldWar->getWorldWarInfo();

//		WorldwarLogic::startOpenAudition();
//		WorldwarLogic::startFinals();
//		WorldwarLogic::sendFightAward();
//		$ret = WorldwarLogic::getTempleInfo();
//		$ret = WorldwarLogic::getWorshipUsers();
//		$ret = WorldwarLogic::worship(1);

//		$userObj = EnUser::getUserObj(21300);
//		$ret = WorldwarUtil::getUserForBattle($userObj);
//		var_dump($ret);

//		WorldwarLogic::getAllHerosAroundWorld(20130108, 1);
	
		// 系统喊话用
//		WorldwarLogic::sendWorldwarMsgByCrontab(1);
		
// 		修数据用
//		$uidAry = array(20103,20108,20127,20156,20163,20169,20178,20226,20271,20315,20452,20494,20566,21321,21483,21948,23967,32507,48895,74226,75111);
//		foreach ($uidAry as $uid)
//		{
//			$ary = WorldwarDao::getUserWorldWarInfo($uid);
//			if(!empty($ary['va_world_war']['fight_para']['server_id']))
//			{
//				if($ary['va_world_war']['fight_para']['server_id'] == 2)
//				{
//					$ary['va_world_war']['fight_para']['server_id'] = '002';
//				}
//				else if($ary['va_world_war']['fight_para']['server_id'] == 8)
//				{
//					$ary['va_world_war']['fight_para']['server_id'] = '008';
//				}
//				$set = array('va_world_war' => $ary['va_world_war']);
//				WorldwarDao::updUserWorldWarInfo($set, $uid);
//			}
//		}
//		self::signUp();
//		$setting = WorldwarUtil::getSetting();
//		$ret = WorldwarUtil::checkWorldwarIsOpen($setting);
//		$ret = WorldwarLogic::cheer(20103, '小白', 1);
//		$ret = WorldwarLogic::getUserWorldWarInfo();
//		var_dump($ret);

		
		self::testCheer();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */