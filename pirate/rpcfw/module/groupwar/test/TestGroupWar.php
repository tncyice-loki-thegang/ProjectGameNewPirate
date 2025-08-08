<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestGroupWar.php 36986 2013-01-24 11:14:28Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/groupwar/test/TestGroupWar.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-01-24 19:14:28 +0800 (四, 2013-01-24) $
 * @version $Revision: 36986 $
 * @brief 
 *  
 **/

require_once (LIB_ROOT . '/RPCProxy.class.php');
require_once('UserClient.php');




class TestGroupWar extends BaseScript
{
	
	public static $battleId = 0;
	public static $roadNum = 0;
	
	public static $pid = 20012;
	public static $uid = 20100;

	public static $pid2 = 20020;		//这个人要比上面那个厉害
	public static $uid2 = 21719;
	
	const CALL_BACK_REFRESH = 'sc.groupwar.refresh';
	const CALL_BACK_BATTLE_END = 'sc.groupwar.battleEnd';
	const CALL_BACK_WIN = 'sc.groupwar.fightWin';
	const CALL_BACK_LOSE = 'sc.groupwar.fightLose';
 	const CALL_BACK_FIGHT_RESULT = 'sc.groupwar.fightResult';
	const CALL_BACK_TOUCH_DOWN = 'sc.groupwar.touchDown';
	
	function __construct()
	{
		$dayOfWeek = date('w');
		!$dayOfWeek && $dayOfWeek = 7;
		self::$battleId = $dayOfWeek*2-1;
		self::$roadNum = GroupWarConfig::$FIELD_CONF['roadNum'];
	}
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	*/
	protected function executeScript($arrOption)
	{
		exec("> /home/pirate/lcserver/log/lcserver.log");
		exec("> /home/pirate/lcserver/log/lcserver.log.wf");
		exec("> /home/pirate/rpcfw/log/rpc.log");
		exec("> /home/pirate/rpcfw/log/rpc.log.wf");
		exec("> /home/pirate/rpcfw/log/script.log");
		exec("> /home/pirate/rpcfw/log/script.log.wf");
		
 		$allMethods = get_class_methods(get_class($this));
 		
 		echo "init btstore\n";
 		//$btConf = btstore_get()->GROUP_BATTLE;
 		echo "init btstore done\n";
 		
 		/*
 		$user = new UserClient (self::$pid, self::$uid );
 		$user->setClass ( 'groupwar' );
 		$ret = $user->groupBattleInfo();
 		var_dump($ret);
 		*/
 		$tagetMethod = array();
		$tagetMethod = array('testRewardOnEnd');
 		foreach($allMethods as $methodName)
 		{
 			if(preg_match( '/^test[A-Z][a-zA-Z]*$/' ,$methodName ))
 			{
 				if(  !empty($tagetMethod) && !in_array($methodName, $tagetMethod ))
 				{
 					continue;
 				}
 				$this->runTest($methodName);

 			}
 		}
		
	}
	
	public function runTest($methodName)
	{
		try
		{
			echo "-------------------------------------test:$methodName\n";
			Logger::trace("runTest:$methodName");
			$this->$methodName();
			echo "done\n\n";
		}
		catch ( Exception $e )
		{
			echo "$methodName failed:".$e->getMessage()."\n";
			Logger::fatal ( $e->getTraceAsString () );
			exit();
		}
	}
	
	/*
	public function testCreateBattle()
	{
		$groupWar = new GroupWar();
		
		$ret = $groupWar->createTodayBattle( 3 );
		ASSERT_EQUAL(false, $ret);
		
		$ret = $groupWar->createTodayBattle( 0 );
		ASSERT_EQUAL(true, $ret);
		endBattle(1);
	}
	*/
	
	public function testRewardOnEnd()
	{
		$proxy = new ServerProxy ();
		//$proxy->asyncExecuteRequest ( 0,  'groupwar.rewardOnEnd', array(8) );
		//return;
		
		$groupWar = new GroupWar();
		$groupWar->rewardOnEnd(8);
		
		//Util::asyncExecute('boss.reward', array(16, 1, 1358090400, 1358091300));
		
		$callback = RPCContext::getInstance()->getCallback();
		$callback = $callback[0];
		var_dump($callback);
	
		$proxy = new PHPProxy ( 'lcserver' );
		$proxy->setDummyReturn ( true );
	
		return $proxy->asyncExecuteLong ( $callback['args'][0],$callback['args'][1],$callback['args'][2] );
	}
	
	public function singleBattle($battleId)
	{		
		createBattle($battleId);
		
		$btConf = btstore_get()->GROUP_BATTLE;
		$joinScore = intval($btConf['joinScore']);
		$joinHonour = intval($btConf['joinHonour']);
		$plunderScore = intval($btConf['plunderScore']);
		$plunderHonour= intval($btConf['plunderHonour']);
		$killBelly = intval($btConf['killBelly']);
		$killExperience = intval($btConf['killExperience']);
		$killPrestige = intval($btConf['killPrestige']);
		$killHonour = intval($btConf['killHonour']);
		$killScoreArr = GroupWar::intvalArray($btConf['killScoreArr']->toArray());
		$streakCoefArr = GroupWar::intvalArray($btConf['streakCoefArr']->toArray());
		$streakHonourArr = GroupWar::intvalArray($btConf['streakHonourArr']->toArray());
		
		$attackGroupId = 2;
		if(GroupWarConfig::DIVIDE_GROUP_METHOD && GroupWar::isFirstHalf($battleId))
		{
			$attackGroupId = 1;
			//创建上半场时，需要分组，然后重置分组，分数等数据
			$select = array('uid', 'battle_id', 'uname', 'group_id', 'score', 'honour', 'belly', 'experience', 'prestige', 'soul');
			$where = array('uid', '>=', 0);
			$data = new CData();
			$offset = 0; 
			$limit = CData::MAX_FETCH_SIZE;
			$userList = array();
			while(true)
			{
				$ret = $data->select($select)->from('t_group_war_user')->where($where)
					->limit($offset, $limit)->query();
				if(empty($ret))
				{
					break;
				}
				$userList = array_merge($userList, $ret);
				$offset += $limit;
			}
			$groupNumList = array(0=>0, 1=>0, 2=>0);			
			foreach($userList as $user)
			{
				ASSERT_TRUE( $user['uid'] != 0);
				ASSERT_TRUE( $user['uname'] != '');
				ASSERT_EQUAL(0, $user['battle_id']);
				ASSERT_EQUAL(0, $user['score']);
				ASSERT_EQUAL(0, $user['honour']);
				ASSERT_EQUAL(0, $user['belly']);
				ASSERT_EQUAL(0, $user['experience']);
				ASSERT_EQUAL(0, $user['prestige']);
				ASSERT_EQUAL(0, $user['soul']);
				$groupNumList[$user['group_id']]++;
			}
			ASSERT_EQUAL(GroupWarConfig::ARENA_FRONT_NUM, $groupNumList[1]+$groupNumList[2]);
			ASSERT_EQUAL($groupNumList[1], $groupNumList[2]);
		}
	
		//进去
		$user_1 = new UserClient ( self::$pid, self::$uid);
		$user_2 = new UserClient ( self::$pid2, self::$uid2);
		$user_1->battleId = $battleId;
		$user_2->battleId = $battleId;
		$ret1 = $user_1->tryEnter($attackGroupId);	
		$ret2 = $user_2->tryEnter(3-$attackGroupId);
		
		ASSERT_TRUE(isset($ret1['res']['user']['extra']));
		ASSERT_TRUE(isset($ret2['res']['user']['extra']));
		if(GroupWar::isFirstHalf($battleId))
		{
			ASSERT_EQUAL(0, $ret2['res']['user']['extra']['reward']['score']);
		}
		else
		{
			ASSERT_TRUE($ret2['res']['user']['extra']['reward']['score'] > 0);
			ASSERT_TRUE($ret2['res']['user']['extra']['reward']['honour'] > 0);
		}
		
		//参战
		$roadId = 1;
		$ret = $user_1->tryJoin($roadId);
		ASSERT_EQUAL('ok', $ret['ret']);
		ASSERT_TRUE(isset($ret['reward']));
		ASSERT_TRUE(isset($ret['topN']));		
		$user_1->score = $ret['reward']['score'];
		$user_1->honour = $ret['reward']['honour'];
		
		$ret = $user_2->tryJoin($roadId);
		ASSERT_EQUAL('ok', $ret['ret']);
		$user_2->score = $ret['reward']['score'];
		$user_2->honour = $ret['reward']['honour'];		
		if(GroupWar::isFirstHalf($battleId))
		{
			ASSERT_EQUAL($joinScore, $user_2->score);
		}
		
		//等待战斗开始
		while(true)
		{
			$ret = $user_2->receiveData(self::CALL_BACK_REFRESH);
			$roadInfo = $ret['field']['road'];
			if(count($roadInfo) > 0)
			{
				break;
			}
		}
		ASSERT_EQUAL(2, count($roadInfo));
		$userInfo_1 = $roadInfo[0];
		$userInfo_2 = $roadInfo[1];
	
		//等待相遇，开打
		$ret = $user_1->receiveData(self::CALL_BACK_FIGHT_RESULT);
		
		if( $ret['winnerId'] == $user_1->uid )
		{
			$userWin = &$user_1;
			$userLose = &$user_2;
			$userInfoWin = &$userInfo_1;
			$userInfoLose = &$userInfo_2;
		}
		else
		{
			$userWin = &$user_2;
			$userLose = &$user_1;
			$userInfoWin = &$userInfo_2;
			$userInfoLose = &$userInfo_1;
		}
		var_dump($ret);
		printf("user:%d win user:%d\n", $userWin->uid, $userLose->uid);

		$ret = $userWin->receiveData(self::CALL_BACK_WIN);
		ASSERT_TRUE(isset($ret['reward']));
		ASSERT_TRUE(isset($ret['topN']));
		
		$ret = $userLose->receiveData(self::CALL_BACK_LOSE);
		ASSERT_TRUE(isset($ret['reward']));
		ASSERT_TRUE(isset($ret['topN']));
		
		$userLose->setUserGold( 10000);	
		$ret = $userLose->removeJoinCd();
		ASSERT_EQUAL('ok', $ret['ret']);
		$ret = $userLose->tryJoin($roadId);
		ASSERT_EQUAL('ok', $ret['ret']);
		
		$ret = $user_1->receiveData(self::CALL_BACK_FIGHT_RESULT);
		var_dump($ret);
		
		//等待达阵
		while(true)
		{
			printf("wait for touch down\n");
			$ret = $userWin->receiveData(self::CALL_BACK_REFRESH);
			$roadInfo = $ret['field']['road'];
			if(count($roadInfo) < 1)
			{
				break;
			}
		}
		ASSERT_TRUE(isset($ret['field']['touchdown']));

		printf("wait for battleEnd\n");
		$ret = $userWin->receiveData(self::CALL_BACK_BATTLE_END);	
	}
	
	public function testRound()
	{
		$dayOfWeek = date('w');
		!$dayOfWeek && $dayOfWeek = 7;
		
		$battleId = $dayOfWeek*2 - 1;
		
		//上半场
		self::singleBattle($battleId);
		sleep(2);
		
		//下半场
		self::singleBattle($battleId+1);
	}
	
	
	public function testRefreshData()
	{
		//endBattle(self::$battleId);
		createBattle(self::$battleId);
		
		
		$userList = getUserList(array(
				array('uid','!=',self::$uid),
				array('pid','>', 1000)  ),
				GroupWarConfig::$FIELD_CONF['maxGroupSize']*2);
		$index = 0;
		$user_1 = addUser($userList[$index]['pid'],  $userList[$index]['uid'], true, self::$battleId);
		$index++;
		
		$roadId = 1;
		$ret = $user_1->tryJoin($roadId);
		ASSERT_EQUAL('ok', $ret['ret']);
		
		while(true)
		{
			$ret = $user_1->receiveData(self::CALL_BACK_REFRESH);
			$roadInfo = $ret['field']['road'];
			if( count($roadInfo) > 0 )
			{
				break;
			}
		}		
		$ret = $user_1->receiveData(self::CALL_BACK_REFRESH);
		
		//之后都为空
		$roadInfo = $ret['field']['road'];
		ASSERT_EQUAL(0, count($roadInfo));
		
		//我进去后，所有信息都是全的
		$user = new UserClient ( self::$pid, self::$uid);
		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);
		$roadInfo = $ret['res']['field']['road'];
		$userInfo_1 = $roadInfo[0];
		ASSERT_EQUAL(12, count($userInfo_1) );
		ASSERT_TRUE( isset( $userInfo_1['id'] ) );
		ASSERT_TRUE( isset( $userInfo_1['type'] ) );
		ASSERT_TRUE( isset( $userInfo_1['name'] ) );
		ASSERT_TRUE( isset( $userInfo_1['tid'] ) );
		ASSERT_TRUE( isset( $userInfo_1['transferId'] ) );
		ASSERT_TRUE( isset( $userInfo_1['roadIds'] ) );
		ASSERT_TRUE( isset( $userInfo_1['speed'] ) );
		ASSERT_TRUE( isset( $userInfo_1['curHp'] ) );
		ASSERT_TRUE( isset( $userInfo_1['maxHp'] ) );
		ASSERT_TRUE( isset( $userInfo_1['winStreak'] ) );
		ASSERT_TRUE( isset( $userInfo_1['roadX'] ) );
		ASSERT_TRUE( isset( $userInfo_1['stopX'] ) );
		
		//之后都为空
		$ret = $user->receiveData(self::CALL_BACK_REFRESH);
		$roadInfo = $ret['field']['road'];
		ASSERT_EQUAL(0, count($roadInfo));
		
		
		//新人进来之后，要全部信息
		$user_2 = addUser($userList[$index]['pid'],  $userList[$index]['uid'], false, self::$battleId);
		$index++;
		$ret = $user_2->tryJoin($roadId);
		ASSERT_EQUAL('ok', $ret['ret']);
		
		while(true)
		{
			$ret = $user->receiveData(self::CALL_BACK_REFRESH);
			$roadInfo = $ret['field']['road'];
			if( count($roadInfo) > 1 )
			{
				break;
			}			
		}

		$roadInfo = $ret['field']['road'];
		ASSERT_EQUAL(2, count($roadInfo) );
		$userInfo_1 = $roadInfo[0];
		$userInfo_2 = $roadInfo[1];
		
		ASSERT_EQUAL(12, count($userInfo_2) );
		ASSERT_TRUE( isset( $userInfo_2['id'] ) );
		ASSERT_TRUE( isset( $userInfo_2['type'] ) );
		ASSERT_TRUE( isset( $userInfo_2['name'] ) );
		ASSERT_TRUE( isset( $userInfo_2['tid'] ) );
		ASSERT_TRUE( isset( $userInfo_2['transferId'] ) );
		ASSERT_TRUE( isset( $userInfo_2['roadIds'] ) );
		ASSERT_TRUE( isset( $userInfo_2['speed'] ) );
		ASSERT_TRUE( isset( $userInfo_2['curHp'] ) );
		ASSERT_TRUE( isset( $userInfo_2['maxHp'] ) );
		ASSERT_TRUE( isset( $userInfo_2['winStreak'] ) );
		ASSERT_TRUE( isset( $userInfo_2['roadX'] ) );
		ASSERT_TRUE( isset( $userInfo_2['stopX'] ) );
		
		
		while(true)
		{
			$ret = $user->receiveData(self::CALL_BACK_REFRESH);
			$roadInfo = $ret['field']['road'];
			
			
			if(count($roadInfo) == 1)
			{
				var_dump($ret);
				break;
			}
			else if(count($roadInfo) == 2)
			{
				echo "meet and wait for fight result\n";
				$ret = $user->receiveAnyData();
				foreach ($ret as $value)
				{
					$callback = $value['callback']['callbackName'];
					if($callback == self::CALL_BACK_FIGHT_RESULT)
					{
						$ret = $user->receiveData(self::CALL_BACK_REFRESH);
						$roadInfo = $ret['field']['road'];
						ASSERT_EQUAL(1, count($roadInfo) );
						break 2;
					}					
				}
				throw "erro:".var_export($ret,true);
			}
			else
			{
				ASSERT_EQUAL(0, count($roadInfo) );
			}
		}
		
		$ret = $user->receiveData(self::CALL_BACK_REFRESH);
		$roadInfo = $ret['field']['road'];
		ASSERT_EQUAL(0, count($roadInfo) );
	
	
	}
	
	
	public function testRefreshDataOld()
	{
		//endBattle(self::$battleId);
		createBattle(self::$battleId);
	
	
		$userList = getUserList(array(
				array('uid','!=',self::$uid),
				array('pid','>', 1000)  ),
				GroupWarConfig::$FIELD_CONF['maxGroupSize']*2);
		$index = 0;
		$user_1 = addUser($userList[$index]['pid'],  $userList[$index]['uid'], true);
		$index++;
	
		$roadId = 1;
		$ret = $user_1->tryJoin($roadId);
		ASSERT_EQUAL('ok', $ret['ret']);
	
		while(true)
		{
			$ret = $user_1->receiveData(self::CALL_BACK_REFRESH);
			$roadInfo = $ret['field']['road'];
			if( count($roadInfo) > 0 )
			{
				break;
			}
		}
		$ret = $user_1->receiveData(self::CALL_BACK_REFRESH);
	
		//这个时候应该只有id,type等字段
		$roadInfo = $ret['field']['road'];
		$userInfo_1 = $roadInfo[0];
		ASSERT_EQUAL(4, count($userInfo_1) );
		ASSERT_TRUE( isset( $userInfo_1['id'] ) );
		ASSERT_TRUE( isset( $userInfo_1['type'] ) );
		ASSERT_TRUE( isset( $userInfo_1['roadX'] ) );
		ASSERT_TRUE( isset( $userInfo_1['stopX'] ) );
	
		//我进去后，所有信息都是全的
		$user = new UserClient ( self::$pid, self::$uid);
		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);
		$roadInfo = $ret['res']['field']['road'];
		$userInfo_1 = $roadInfo[0];
		ASSERT_EQUAL(12, count($userInfo_1) );
		ASSERT_TRUE( isset( $userInfo_1['id'] ) );
		ASSERT_TRUE( isset( $userInfo_1['type'] ) );
		ASSERT_TRUE( isset( $userInfo_1['name'] ) );
		ASSERT_TRUE( isset( $userInfo_1['tid'] ) );
		ASSERT_TRUE( isset( $userInfo_1['transferId'] ) );
		ASSERT_TRUE( isset( $userInfo_1['roadIds'] ) );
		ASSERT_TRUE( isset( $userInfo_1['speed'] ) );
		ASSERT_TRUE( isset( $userInfo_1['curHp'] ) );
		ASSERT_TRUE( isset( $userInfo_1['maxHp'] ) );
		ASSERT_TRUE( isset( $userInfo_1['winStreak'] ) );
		ASSERT_TRUE( isset( $userInfo_1['roadX'] ) );
		ASSERT_TRUE( isset( $userInfo_1['stopX'] ) );
	
		//之后name,tid,transferId,roadIds,speed一直都没有
		$ret = $user->receiveData(self::CALL_BACK_REFRESH);
		$roadInfo = $ret['field']['road'];
		$userInfo_1 = $roadInfo[0];
		ASSERT_EQUAL(4, count($userInfo_1) );
		ASSERT_TRUE( isset( $userInfo_1['id'] ) );
		ASSERT_TRUE( isset( $userInfo_1['type'] ) );
		ASSERT_TRUE( isset( $userInfo_1['roadX'] ) );
		ASSERT_TRUE( isset( $userInfo_1['stopX'] ) );
	
	
		//新人进来之后，要全部信息
		$user_2 = addUser($userList[$index]['pid'],  $userList[$index]['uid'], false);
		$index++;
		$ret = $user_2->tryJoin($roadId);
		ASSERT_EQUAL('ok', $ret['ret']);
	
		while(true)
		{
			$ret = $user->receiveData(self::CALL_BACK_REFRESH);
			$roadInfo = $ret['field']['road'];
			if( count($roadInfo) > 1 )
			{
				break;
			}
		}
	
		$roadInfo = $ret['field']['road'];
		ASSERT_EQUAL(2, count($roadInfo) );
		$userInfo_1 = $roadInfo[0];
		$userInfo_2 = $roadInfo[1];
		ASSERT_EQUAL(4, count($userInfo_1) );
		ASSERT_TRUE( isset( $userInfo_1['id'] ) );
		ASSERT_TRUE( isset( $userInfo_1['type'] ) );
		ASSERT_TRUE( isset( $userInfo_1['roadX'] ) );
		ASSERT_TRUE( isset( $userInfo_1['stopX'] ) );
	
		ASSERT_EQUAL(12, count($userInfo_2) );
		ASSERT_TRUE( isset( $userInfo_2['id'] ) );
		ASSERT_TRUE( isset( $userInfo_2['type'] ) );
		ASSERT_TRUE( isset( $userInfo_2['name'] ) );
		ASSERT_TRUE( isset( $userInfo_2['tid'] ) );
		ASSERT_TRUE( isset( $userInfo_2['transferId'] ) );
		ASSERT_TRUE( isset( $userInfo_2['roadIds'] ) );
		ASSERT_TRUE( isset( $userInfo_2['speed'] ) );
		ASSERT_TRUE( isset( $userInfo_2['curHp'] ) );
		ASSERT_TRUE( isset( $userInfo_2['maxHp'] ) );
		ASSERT_TRUE( isset( $userInfo_2['winStreak'] ) );
		ASSERT_TRUE( isset( $userInfo_2['roadX'] ) );
		ASSERT_TRUE( isset( $userInfo_2['stopX'] ) );
	
	
		while(true)
		{
			$ret = $user->receiveData(self::CALL_BACK_REFRESH);
			$roadInfo = $ret['field']['road'];
			if(count($roadInfo) == 2)
			{
				//单位如果没有发生战斗，HP是没有的
				$userInfo_1 = $roadInfo[0];
				$userInfo_2 = $roadInfo[1];
				ASSERT_TRUE(4 == count($userInfo_1) || 2== count($userInfo_1));
				ASSERT_TRUE(4 == count($userInfo_2) || 2== count($userInfo_2));
	
			}
			else
			{
				ASSERT_EQUAL(1, count($roadInfo) );
				//战斗了就的给我HP
				$userInfo_1 = $roadInfo[0];
				ASSERT_EQUAL(7, count($userInfo_1) );
				ASSERT_TRUE( isset( $userInfo_1['id'] ) );
				ASSERT_TRUE( isset( $userInfo_1['type'] ) );
				ASSERT_TRUE( isset( $userInfo_1['roadX'] ) );
				ASSERT_TRUE( isset( $userInfo_1['stopX'] ) );
				ASSERT_TRUE( isset( $userInfo_1['curHp'] ) );
				ASSERT_TRUE( isset( $userInfo_1['maxHp'] ) );
				ASSERT_TRUE( isset( $userInfo_1['winStreak'] ) );
				break;
			}
		}
	
		$ret = $user->receiveData(self::CALL_BACK_REFRESH);
		$roadInfo = $ret['field']['road'];
		$userInfo_1 = $roadInfo[0];
		ASSERT_EQUAL(4, count($userInfo_1) );
		ASSERT_TRUE( isset( $userInfo_1['id'] ) );
		ASSERT_TRUE( isset( $userInfo_1['type'] ) );
		ASSERT_TRUE( isset( $userInfo_1['roadX'] ) );
		ASSERT_TRUE( isset( $userInfo_1['stopX'] ) );
	
	
		//单位如果没有移动roadX，stopX是没有的 TODO：测不到
	}
	

	public function testEnter()
	{
		//失败：not_found
		endBattle(self::$battleId);
		$user = new UserClient ( self::$pid, self::$uid);
		$ret = $user->tryEnter();
		ASSERT_EQUAL('not_found', $ret['ret']);

		createBattle(self::$battleId);
		
		$user->battleId = 20;
		$ret = $user->tryEnter();
		ASSERT_EQUAL('fake', $ret['ret']);
		
		$user->battleId = 1;
		$ret = $user->tryEnter();
		ASSERT_EQUAL('fake', $ret['ret']);
		
		$user->battleId = 0;
		
		//成功：
		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);

		//失败：reenter
		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);

		//失败：full
		$userList = getUserList(array(  
							array('uid','!=',$user->uid), 
							array('pid','>', 1000)  ),
		   GroupWarConfig::$FIELD_CONF['maxGroupSize']*2);

		for($i = 0; $i < GroupWarConfig::$FIELD_CONF['maxGroupSize']*2; $i++)
		{
			$newUser = new UserClient ( $userList[$i]['pid'],  $userList[$i]['uid'] );
		
			$ret = $newUser->tryEnter();
	
			echo "$i {$ret['ret']}\n";
			ASSERT_TRUE( ($ret['ret']=='ok')|| ($ret['ret']=='full'));
			if($ret['ret']=='full')
			{
				break;
			}
		}		
		ASSERT_EQUAL('full', $ret['ret']);
	}
	
	public function testAddRoad()
	{
		endBattle(self::$battleId);		
		createBattle(self::$battleId);
	
		$user = new UserClient ( self::$pid, self::$uid);
		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);
		ASSERT_EQUAL(1, $ret['res']['field']['roadState'] );
		
		$ret = $user->receiveData(self::CALL_BACK_REFRESH);
		ASSERT_EQUAL(1, $ret['field']['roadState'] );
		
		$userList = getUserList(array(
				array('uid','!=',$user->uid),
				array('pid','>', 1000)  ),
				GroupWarConfig::$FIELD_CONF['addRoadThr']);
		
		for($i = 0; $i < GroupWarConfig::$FIELD_CONF['addRoadThr']; $i++)
		{
			$newUser = new UserClient ( $userList[$i]['pid'],  $userList[$i]['uid'] );
			
			$ret = $newUser->tryEnter();
			
			echo "$i {$ret['ret']}\n";
			ASSERT_TRUE( ($ret['ret']=='ok') );
			$user->receiveData(self::CALL_BACK_REFRESH);
		}
		ASSERT_EQUAL(2, $ret['res']['field']['roadState'] );
		
		$ret = $user->receiveData(self::CALL_BACK_REFRESH);
		ASSERT_EQUAL(2, $ret['field']['roadState'] );
	}

	/**
	 * 参战
	 */
	public function testJoin()
	{
		endBattle(self::$battleId);			
		createBattle(self::$battleId);
		
		$user = new UserClient ( self::$pid, self::$uid);
		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);
	
		RPCContext::getInstance()->setSession('global.uid', $user->uid);
		//失败：lack_hp
		$user->setClass ( 'console' );
		$user->execute( 'blood 0'  );
		$user->execute( 'hero allHeroHp 0' );

		$user->setClass ( 'groupwar' );
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('lack_hp', $ret['ret']);
			
		//成功：
		$user->setClass ( 'console' );
		$user->execute( 'blood 10000'  );
		$user->execute( 'hero allHeroHp 1' );
			
		$user->setClass ( 'groupwar' );
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);
		
		//参战失败:full
		$userInfoList = getUserList(array(
				array('pid','>',1000),
				array('uid', '!=', $user->uid)),
				btstore_get()->GROUP_BATTLE['maxWaitQueue']*10);
		$userList = array();
		Logger::debug($userInfoList);
		foreach ($userInfoList as $userInfo)
		{
			$tmpUser = new UserClient ( $userInfo['pid'], $userInfo['uid']);
			$userList[] = $tmpUser;
			$ret = $tmpUser->tryEnter($user->groupId);

			ASSERT_EQUAL('ok', $ret['ret']);

			$ret = $tmpUser->tryJoin(1);
			
			echo "user[{$userInfo['pid']}, {$userInfo['uid']}] join. ret={$ret['ret']}\n";
			ASSERT_TRUE( ($ret['ret']=='ok')|| ($ret['ret']=='full'));	
			if($ret['ret']=='full')		
			{
				break;
			}
		}		
		unset($userList);
		ASSERT_EQUAL('full', $ret['ret']);
			
		//失败：battling
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('battling', $ret['ret']);
						
		//!!!这个人要比上面的人强		
		$user2 = new UserClient ( self::$pid2,  self::$uid2 );		
		$ret = $user2->tryEnter(3-$user->groupId);
		ASSERT_EQUAL('ok', $ret['ret']);
		$ret = $user2->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);			
		
		
		$ret = $user->receiveData(self::CALL_BACK_FIGHT_RESULT);

		if($ret['loserId'] !=  $user->uid )
		{
			exit("find a stronger opponent\n");
		}
	
		//参战失败：cdtime
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('cdtime', $ret['ret']);
	}

	
	/**
	 * 鼓舞
	 */
	public function testInspire()
	{
		endBattle(self::$battleId);
		createBattle(self::$battleId);
		
		$user1 = new UserClient ( self::$pid,  self::$uid );
		$user2 = new UserClient ( self::$pid2,  self::$uid2 );				
			
		$ret = $user1->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);
			
		$ret = $user2->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);

		//鼓舞成功
		$preGoldNum = $user1->getUserGold();
		$ret = $user1->inspire(true);
		ASSERT_EQUAL( 'ok', $ret['ret']);		
		ASSERT_EQUAL( 1, $ret['res']['attackLevel'] + $ret['res']['defendLevel'] );
		
		$curGoldNum = $user1->getUserGold();
		$btConf = btstore_get()->GROUP_BATTLE;
		ASSERT_EQUAL( intval($btConf['inspireGoldNum']), $preGoldNum-$curGoldNum);
		
		//失败：cdtime
		$ret = $user1->inspire(true);
		ASSERT_EQUAL('cdtime', $ret['ret']);
		
		//失败：lack_gold
		$preGoldNum = 1;
		$user2->setUserGold($preGoldNum);
		$ret = $user2->inspire(true);
		$curGoldNum = $user2->getUserGold();
		ASSERT_EQUAL('fake', $ret['ret']);
		ASSERT_EQUAL( $preGoldNum, $curGoldNum);
		
		//失败：lack_exp
		$user2->setUserExp(0);
		$ret = $user2->inspire(false);
		ASSERT_EQUAL('fake', $ret['ret']);
		
		$user2->setUserExp(10000);
		
		//失败：no_inspire			
		$hasOk = false;
		$hasNo = false;
		for($i = 0; $i < 10; $i++)
		{
			$preExp = $user2->getUserExp();
			$ret = $user2->inspire(false);		
			$curExp = $user2->getUserExp();
			
			echo "inspire[{$i}]:{$ret['ret']}\n";
			ASSERT_EQUAL($btConf['inspireExperienceNum']*$user2->getUserLevel(), $preExp-$curExp  );
								
			
			if($ret['ret'] == 'full' )
			{
				echo "!!!!!!cant't test no_inspire\n";
				break;
			}
			ASSERT_TRUE( $ret['ret'] == 'ok' || $ret['ret'] == 'no_inspire' );				
			if($ret['ret'] == 'ok')
			{
				$hasOk = true;
			}
			else if($ret['ret'] == 'no_inspire')
			{
				$hasNo = true;
			}
			if($hasOk && $hasNo)
			{
				break;
			}
			echo 'sleep:'.$btConf['inspireCdTime']."\n";
			sleep(intval($btConf['inspireCdTime']));	
		}
		if($ret['ret'] != 'full' )
		{
			ASSERT_EQUAL(true, $hasOk);
			ASSERT_EQUAL(true, $hasNo);
		}
		
		//失败：full
		RPCContext::getInstance()->setSession('global.uid', $user1->uid);
		$userObj1 = EnUser::getUserObj ( $user1->uid );
		$ret = $userObj1->addGold(9999999);
		for($i = 0; $i < 2*$btConf['attackDefendMaxLevel']; $i++)
		{
			$ret = $user1->inspire(true);
			echo "inspire[{$i}]:{$ret['ret']}\n";
			ASSERT_TRUE( $ret['ret'] == 'ok' || $ret['ret'] == 'full' );
			if( $ret['ret'] == 'full')
			{
				break;
			}
			
			echo 'sleep:'.$btConf['inspireCdTime']."\n";
			sleep($btConf['inspireCdTime']);	
		}
		ASSERT_EQUAL('full', $ret['ret']);

	}
	
	public function testRemoveJoinCd()
	{
		endBattle(self::$battleId);
		createBattle(self::$battleId);
		
		$user = new UserClient ( self::$pid,  self::$uid );
		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);

		//RPCContext::getInstance()->setSession('global.uid', $user->uid);

		$preGoldNum = $user->getUserGold();
		//可参战时：秒除参战冷却时间，不能消耗金币
		$ret = $user->removeJoinCd();
		ASSERT_EQUAL('ok', $ret['ret']);
		
		$goldNum =  $user->getUserGold();
		ASSERT_EQUAL($preGoldNum, $goldNum);		
		
		//战斗中时，不能消耗金币
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);				
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('battling', $ret['ret']);
		
		$ret = $user->removeJoinCd();
		ASSERT_EQUAL('ok', $ret['ret']);
		
		$goldNum = $user->getUserGold();
		ASSERT_EQUAL($preGoldNum, $goldNum);		
		
		//!!!这个人要比上面的人强		
		$user2 = new UserClient ( self::$pid2,  self::$uid2 );		
		$ret = $user2->tryEnter(3-$user->groupId);
		ASSERT_EQUAL('ok', $ret['ret']);
		$ret = $user2->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);	
		
		$ret = $user->receiveData(self::CALL_BACK_FIGHT_RESULT);

		if($ret['loserId'] !=  $user->uid )
		{
			exit("find a stronger opponent\n");
		}
		//参战失败：cdtime
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('cdtime', $ret['ret']);
		
		//秒除参战冷却时间失败：lack_cost
		$user->setUserGold(0);
		$ret = $user->removeJoinCd();
		ASSERT_EQUAL('fake', $ret['ret']);
		
		$preGoldNum = 10000;
		$user->setUserGold( $preGoldNum);
		
		
		$ret = $user->removeJoinCd();
		ASSERT_EQUAL('ok', $ret['ret']);
		
		$btConf = btstore_get()->GROUP_BATTLE;
		$joinCdTime = $btConf['joinCdTime'];
		$joinCdPerGold = $btConf['joinCdPerGold'];
		$needGold1 = ceil($joinCdTime/$joinCdPerGold);
		$needGold2 = ceil(($joinCdTime-1)/$joinCdPerGold);
		$goldNum = $user->getUserGold();
		
		ASSERT_TRUE( ($preGoldNum - $needGold1 <= $goldNum) && ($preGoldNum - $needGold2 >= $goldNum));
		
		
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);		
	}
	
	/**
	 * 离开
	 */
	public function testLeave()
	{
		endBattle(self::$battleId);
		createBattle(self::$battleId);
		$user = new UserClient ( self::$pid,  self::$uid );
		
		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);

		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);
		
		$ret = $user->inspire(true);
		ASSERT_EQUAL( 'ok', $ret['ret']);
		ASSERT_EQUAL( 1, $ret['res']['attackLevel'] + $ret['res']['defendLevel'] );
		$attackLevel =  $ret['res']['attackLevel'];
		$defendLevel = $ret['res']['defendLevel'] ;
		
		$ret = $user->inspire(true);
		ASSERT_EQUAL('cdtime', $ret['ret']);
		
		//离开了，重新回来，鼓舞冷却时间不变，攻方等级不变
		$ret = $user->leave(self::$battleId);
		ASSERT_EQUAL('ok', $ret);
		
		$ret = $user->enter(self::$battleId);
		ASSERT_EQUAL('ok', $ret['ret']);

		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);
		
		$ret = $user->inspire(true);
		ASSERT_EQUAL('cdtime', $ret['ret']);

		//离开，再回来，参战冷却时间保持
		//!!!这个人要比上面的人强		
		$user2 = new UserClient ( self::$pid2,  self::$uid2 );		
		$ret = $user2->tryEnter(3-$user->groupId);
		ASSERT_EQUAL('ok', $ret['ret']);
		$ret = $user2->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);	

		$ret = $user->receiveData(self::CALL_BACK_FIGHT_RESULT);

		if($ret['loserId'] !=  $user->uid )
		{
			exit("find a stronger opponent\n");
		}
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('cdtime', $ret['ret']);
		
		$ret = $user->leave(self::$battleId);
		ASSERT_EQUAL('ok', $ret);
		
		$ret = $user->enter(self::$battleId);
		ASSERT_EQUAL('ok', $ret['ret']);
		$cdTime = $ret['res']['user']['canJoinTime'] - time();
		
		$btConf = btstore_get()->GROUP_BATTLE;
		$joinCdTime = $btConf['joinCdTime'];
		ASSERT_TRUE( ($cdTime> $joinCdTime-2) && $cdTime<=$joinCdTime);
		
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('cdtime', $ret['ret']);
	}
	
	/**
	 * 用于已经在通道上来，然后离开
	 */
	public function testLeaveRoad()
	{
		endBattle(self::$battleId);
		createBattle(self::$battleId);
		$user = new UserClient ( self::$pid,  self::$uid );

		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);
		
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);
		
		$ret = $user->inspire(true);
		ASSERT_EQUAL( 'ok', $ret['ret']);
		ASSERT_EQUAL( 1, $ret['res']['attackLevel'] + $ret['res']['defendLevel'] );
		$attackLevel =  $ret['res']['attackLevel'];
		$defendLevel = $ret['res']['defendLevel'] ;
		
		$ret = $user->inspire(true);
		ASSERT_EQUAL('cdtime', $ret['ret']);
		
		//等到上了通道再离开了，重新回来， 攻方等级不变
		while(true)
		{
			$ret = $user->receiveData(self::CALL_BACK_REFRESH);
			if(count($ret['field']['road'])>0 )
			{
				break;
			}
		}
		
		$ret = $user->leave(self::$battleId);
		ASSERT_EQUAL('ok', $ret);
		
		$user2 = new UserClient ( self::$pid2, self::$uid2 );

		$ret = $user2->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);
		$ret = $user2->receiveData(self::CALL_BACK_REFRESH);
		ASSERT_EQUAL(0, count($ret['field']['road']) );
				
		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);
		ASSERT_EQUAL( $attackLevel, $ret['res']['user']['attackLevel']);
		ASSERT_EQUAL( $defendLevel, $ret['res']['user']['defendLevel']);
				
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);
	}
	
	/**
	 * 用户下线
	 */
	public function testLogoff()
	{
		endBattle(self::$battleId);
		createBattle(self::$battleId);
		$user = new UserClient ( self::$pid,  self::$uid );

		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);
		
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);
	
		$ret = $user->inspire(true);
		ASSERT_EQUAL( 'ok', $ret['ret']);
		ASSERT_EQUAL( 1, $ret['res']['attackLevel'] + $ret['res']['defendLevel'] );
		$attackLevel =  $ret['res']['attackLevel'];
		$defendLevel = $ret['res']['defendLevel'] ;
	
		$ret = $user->inspire(true);
		ASSERT_EQUAL('cdtime', $ret['ret']);
	
		//下线，重新回来，鼓舞冷却时间不变，攻方等级不变
		unset($user);
		$user = new UserClient ( self::$pid,  self::$uid );
		$user->setClass ( 'groupwar' );
		
		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);
		ASSERT_EQUAL( $attackLevel, $ret['res']['user']['attackLevel']);
		ASSERT_EQUAL( $defendLevel, $ret['res']['user']['defendLevel']);			
	
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);
	
		$ret = $user->inspire(true);
		ASSERT_EQUAL('cdtime', $ret['ret']);

		//离开，再回来，参战冷却时间保持
		//!!!这个人要比上面的人强		
		$user2 = new UserClient ( self::$pid2,  self::$uid2 );		
		$ret = $user2->tryEnter(3-$user->groupId);
		ASSERT_EQUAL('ok', $ret['ret']);
		$ret = $user2->tryJoin(1);
		ASSERT_EQUAL('ok', $ret['ret']);	
		
		$ret = $user->receiveData(self::CALL_BACK_FIGHT_RESULT);

		if($ret['loserId'] !=  $user->uid )
		{
			exit("find a stronger opponent\n");
		}
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('cdtime', $ret['ret']);
	
		unset($user);
		$user = new UserClient ( self::$pid,  self::$uid );
		$user->setClass ( 'groupwar' );
	
		$ret = $user->tryEnter();
		ASSERT_EQUAL('ok', $ret['ret']);
		$cdTime = $ret['res']['user']['canJoinTime'] - time();


		$btConf = btstore_get()->GROUP_BATTLE;
		$joinCdTime = $btConf['joinCdTime'];
		ASSERT_TRUE( ($cdTime> $joinCdTime-3) && $cdTime<= $joinCdTime);
	
		$ret = $user->tryJoin(1);
		ASSERT_EQUAL('cdtime', $ret['ret']);
	}
	 

	

	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
