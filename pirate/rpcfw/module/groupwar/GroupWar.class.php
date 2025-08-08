<?php
/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GroupWar.class.php 40421 2013-03-10 07:09:39Z wuqilin $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/groupwar/GroupWar.class.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-10 15:09:39 +0800 (日, 2013-03-10) $
 * @version $Revision: 40421 $
 * @brief
 *
 **/

//本来想用GroupBattle的名字的，但是在copy模块下已经存在一个GroupBattle类，所以这里用GroupWar表示阵营战

class GroupWar implements IGroupWar
{

	/* (non-PHPdoc)
	 * @see IGroupWar::groupBattleInfo()
	*/
	public function groupBattleInfo()
	{
		//根据配置中：指定的战斗场次和战斗间隔计算一轮战斗有几场
		$battleList = self::getCurBattleList();

		$initResource = intval(btstore_get()->GROUP_BATTLE['initResource']);

		//取每个阵营的信息
		$groupList = array();
		$ret = GroupWarDAO::getAllGroupInfo();
		foreach ($ret as $key => $value)
		{
			$groupList[$value['groupId']] = array
							(
								'initResource'=> $initResource,
								'curResource'=>$value['resource']
							);
		}

		//获取当前用户的信息
		$uid = RPCContext::getInstance()->getUid();
		$userInfo = GroupWarDAO::getUserInfo($uid);

		$returnData = array
					(
							'battleList' => array_values($battleList),
							'groupList'=> $groupList,
							'userInfo' => $userInfo
					);
		return $returnData;
	}


	/* (non-PHPdoc)
	 * @see IGroupWar::enter()
	 */
	public function enter($battleId = 0)
	{
		$SEC_OF_DAY = 86400;
		$nowTime = Util::getTime ();

		//开服一定天数之后才能开启阵营战
		$serverStartTime = strtotime(GameConf::SERVER_OPEN_YMD);
		$startDays = intval(btstore_get()->GROUP_BATTLE['startDays']);
		if( $nowTime < $serverStartTime + $startDays*$SEC_OF_DAY  )
		{
			Logger::warning ( 'group battle not open. server open:%s', GameConf::SERVER_OPEN_YMD);
			throw new Exception('fake');
		}
		$battleId = intval($battleId);
		$uid = RPCContext::getInstance()->getUid();

		//获取当前正在进行的战斗，如果指定了战斗ID，检查该战斗是否在进行中
		$battleList = self::getCurBattleList();
		if($battleId == 0)
		{
			foreach($battleList as $battle)
			{
				if ( $nowTime >= $battle['startTime'] && $nowTime <= $battle['endTime'])
				{
					$battleId = $battle['id'];
					break;
				}
			}
			if($battleId == 0)
			{
				Logger::warning ( 'uid:%d enter failed: no battle', $uid);
				throw new Exception('fake');
			}
			$battle = $battleList[$battleId];
		}
		else
		{
			if( !isset($battleList[$battleId]))
			{
				Logger::warning ( 'uid:%d enter failed: cant find battle:%d', $uid, $battleId);
				throw new Exception('fake');
			}
			$battle = $battleList[$battleId];
			//战斗是否在进行中
			if ( $nowTime < $battle['startTime'] || $nowTime > $battle['endTime'])
			{
				Logger::warning ( 'uid:%d enter failed: battle:%d, startTime:%d, endTime:%d, now:%d',
				$uid, $battleId, $battle['startTime'], $battle['endTime'], $nowTime);
				throw new Exception('fake');
			}
		}

		//检查用户等级
		$minLevel = intval(btstore_get()->GROUP_BATTLE['joinMinLevel']);
		$user = EnUser::getUserObj($uid);

		$level = $user->getLevel();
		if( $level < $minLevel)
		{
			Logger::warning ( 'uid:%d enter failed: level:%d < minLevel:%d', $uid, $level, $minLevel);
			throw new Exception('fake');
		}

		$userInfo = GroupWarDAO::getUserInfo($uid);

		if( empty($userInfo) || $userInfo['groupId'] == 0 ) // 无此用户数据，或此用户还未分组
		{
			$userInfo = array(
					'uid' => $uid,
					'uname' => $user->getUname(),
					'score' => 0,
					'resource' => 0,
					'removeJoinCd' => 0,
					'groupId' => self::getRandGroup()
			);
			Logger::debug('uid:%d enter group battle, set groupId:%d', $uid, $userInfo['groupId'] );
			GroupWarDAO::setUserInfo($userInfo);
		}

		$userInfo['honour'] = EnHonourShop::getUserHonourPoint();
		$userData = array(
				'uid' => $uid,
				'uname' => strval ( RPCContext::getInstance ()->getSession( 'global.uname' ) ),
				'master_htid' => intval($user->getMasterHeroObj()->getHtid()),
				'groupId' => $userInfo['groupId'],
				);

		//调用lcserver
		RPCContext::getInstance()->enterGroupBattle($battleId, $userData);

		Logger::debug('uid:%d enter group:%d', $uid, $userInfo['groupId'] );
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IGroupWar::getEnterInfo()
	*/
	public function getEnterInfo($battleId)
	{		
		$uid = RPCContext::getInstance()->getUid();
		$battleId = intval($battleId);
		if( $battleId <= 0 )
		{
			Logger::warning ( 'uid:%d getEnterInfo failed: invalid battleId:%d', $uid, $battleId);
			throw new Exception('fake');
		}
		$battleIdInSession = RPCContext::getInstance ()->getSession( GroupWarDef::SESSION_GROUP_BATTLE_ID);
		if($battleId != $battleIdInSession )
		{
			Logger::warning ( 'uid:%d getEnterInfo failed: invalid battleId:%d != %d', $uid, $battleId, $battleIdInSession);
			throw new Exception('fake');
		}
			
		$isFirstEnter = 0;//这个人是否是第一次参加这轮战斗
		$userInfo = GroupWarDAO::getUserInfo($uid);
	
		if( empty($userInfo)  )
		{
			Logger::warning ( 'uid:%d getEnterInfo failed: not found userinfo', $uid);
			throw new Exception('fake');
		}
		if( $userInfo['battleId'] == 0 )
		{
			$isFirstEnter = 1;
		}
		
		$values = array(
					'uid' => $uid,
					'battleId' => $battleId
			);
		GroupWarDAO::updateUserInfo($values);
		
		$userInfo['honour'] = EnHonourShop::getUserHonourPoint();
		$userData = array(
			'info' => array(
					'score' => $userInfo['score'],
					'honour' => $userInfo['honour'],
					'resource' => $userInfo['resource'],
					'removeJoinCd' => $userInfo['removeJoinCd'],
					//'winStreak' => $userInfo['winStreak'],
					'isFirst' => $isFirstEnter
			),
			'topN' => GroupWarDAO::getScoreTopN(GroupWarConfig::SCORE_TOP_N),			
		);
	
		//调用lcserver
		RPCContext::getInstance()->getGroupBattleEnterInfo($battleId, $userData);
	
		Logger::debug('uid:%d getEnterInfo battleId:%d', $uid, $battleId );
		return true;
	}
	

	/* (non-PHPdoc)
	 * @see IGroupWar::leave()
	 */
 	public function leave($battleId)
	{
		$battleId = intval($battleId);
		$uid = RPCContext::getInstance()->getUid();

		//调用lcserver
		RPCContext::getInstance()->leaveGroupBattle($battleId);
		Logger::debug('uid:%d leave battle', $uid);
		return true;
	}

	/* (non-PHPdoc)
	 * @see IGroupWar::join()
	 */
 	public function join($battleId, $transferId)
	{
		$battleId = intval($battleId);
		$transferId = intval($transferId);
		$uid = RPCContext::getInstance()->getUid();

		if($battleId <= 0)
		{
			Logger::warning ( 'uid:%d join failed: invalid battleId', $uid, $battleId);
			throw new Exception('fake');
		}
		if( $transferId < 0 )
		{
			Logger::warning ( 'uid:%d enter failed: invalid transferId', $uid, $transferId);
			throw new Exception('fake');
		}

		//检查等待时间
		$nowTime = Util::getTime ();
		$quitBattleTime = RPCContext::getInstance ()->getSession( GroupWarDef::SESSION_QUIT_BATTLE_TIME );
		$joinReadyTime = intval(btstore_get()->GROUP_BATTLE['joinReadyTime']);
		$waitTime = $quitBattleTime + $joinReadyTime - $nowTime;
		if ( $waitTime > 0)
		{
			Logger::info ( 'uid:%d join failed: waitTime', $uid);
			return array ('ret' => 'waitTime', 'waitTime'=>$waitTime);
		}

		//检查参战cd
		$leaveBattleTime = RPCContext::getInstance ()->getSession( GroupWarDef::SESSION_LEAVE_BATTLE_TIME );

		$joinCdTime = intval(btstore_get()->GROUP_BATTLE['joinCdTime']);
		$remainCd = $leaveBattleTime + $joinCdTime - $nowTime;
		if ( $remainCd > 0)
		{
			Logger::info ( 'uid:%d join failed: cdtime', $uid);
			return array ('ret' => 'cdtime', 'cdtime'=>$remainCd);
		}

		//获取战斗数据
		$uid = RPCContext::getInstance()->getUid();
		$battleData = self::getBattleData($uid);

		//检查英雄血是否满
		if ( false === $battleData )
		{
			Logger::info ( 'uid:%d join failed: lack_hp', $uid);
			return array ('ret' => 'lack_hp');
		}

		//通知lcserver用户参战
		$proxy = new PHPProxy ( 'lcserver' );
		$ret = $proxy->joinGroupBattle( $uid, $battleId, $transferId, $battleData);

		if( $ret['ret'] != 'ok' )
		{
			Logger::info ( 'uid:%d join failed: %s', $uid, $ret);
			return array ('ret' => $ret['ret']);
		}
		$outTime = $ret['outTime'];

		//计算奖励
		$reward = self::rewardWhenJoin($uid);

		$scoreTopN = self::broadcastScoreTopN($battleId);

		Logger::debug ( 'uid:%d join at transfer:%d', $uid, $transferId);

		return array (
				'ret' => 'ok', 
				'outTime'=> $outTime, 
				'reward'=> $reward, 
				'topN' => $scoreTopN 
				);
	}

	/* (non-PHPdoc)
	 * @see IGroupWar::inspire()
	 */
 	public function inspire($isGold)
	{
		$isGold = !empty($isGold);
		$uid = RPCContext::getInstance()->getUid();
		$nowTime = Util::getTime ();

		$btConf = btstore_get()->GROUP_BATTLE;

		$goldNum = intval($btConf['inspireGoldNum']);
		$experienceNum = intval($btConf['inspireExperienceNum']);

		//扣金币或者阅历
		$cost = 0;//扣除的金币或者阅历值
		$userObj = EnUser::getUserObj ( $uid );
		if ($isGold)
		{
			$cost = $goldNum;
			$ret = $userObj->subGold ( $goldNum );
			if (! $ret)
			{
				Logger::warning ('uid:%d inspire failed: lack_gold', $uid);
				throw new Exception('fake');
			}
		}
		else
		{
			$lastInspireTime = RPCContext::getInstance ()->getSession( GroupWarDef::SESSION_LAST_INSPIRE_TIME );
			$inspireCdTime = intval($btConf['inspireCdTime']);
			$remainCd = $lastInspireTime + $inspireCdTime - $nowTime;
			if (  $remainCd > 0 )
			{
				Logger::info ('uid:%d inspire failed: cdtime', $uid);
				return array ('ret' => 'cdtime', 'res'=>$remainCd);
			}


			$cost = $userObj->getLevel () * $experienceNum;
			$ret = $userObj->subExperience ($cost);
			if (! $ret)
			{
				Logger::warning ('uid:%d inspire failed: lack_exp', $uid);
				throw new Exception('fake');
			}
		}
		$userObj->update ();

		//调用lcserver
		RPCContext::getInstance()->inspireGroupBattle($isGold, $cost);

		if ($isGold)
		{
			Statistics::gold ( StatisticsDef::ST_FUNCKEY_GROUP_WAR_INSPIRE,
								$goldNum, $nowTime );
		}

		//EnAchievements::notify ( $uid, AchievementsDef::INSPIRE_TIMES, 1 );

		Logger::info ('uid:%d inspire success. isGold:%d', $uid, $isGold);
		return true;
	}

	/* (non-PHPdoc)
	 * @see IGroupWar::removeJoinCd()
	 */
 	public function removeJoinCd()
	{
		$uid = RPCContext::getInstance()->getUid();
		$nowTime = Util::getTime ();
		$leaveBattleTime = RPCContext::getInstance ()->getSession( GroupWarDef::SESSION_LEAVE_BATTLE_TIME );

		//检查cd
		$btConf = btstore_get()->GROUP_BATTLE;
		$joinCdTime = intval($btConf['joinCdTime']);
		$joinCdBaseGold= intval($btConf['joinCdBaseGold']);
		$joinCdIncGold= intval($btConf['joinCdIncGold']);

		$remainCd = $leaveBattleTime + $joinCdTime - $nowTime;
		if ($remainCd <=0 )
		{
			Logger::info ('uid:%d removeJoinCd failed: no cd', $uid);
			return array ('ret' => 'ok', 'res'=>0);
		}

		//这个人以前秒过几次，然后算他现在需要多少钱
		$userInfo = GroupWarDAO::getUserInfo($uid);
		if(empty($userInfo))
		{
			Logger::warning ('uid:%d removeJoinCd failed: no user info', $uid);
			throw new Exception('fake');
		}
		$needGold = $joinCdBaseGold + $userInfo['removeJoinCd']*$joinCdIncGold;
		$userObj = EnUser::getUserObj ( $uid );
		$ret = $userObj->subGold ($needGold);
		if (! $ret)
		{
			Logger::warning ('uid:%d removeJoinCd failed: lack_cost', $uid);
			throw new Exception('fake');
		}
		$userObj->update ();

		RPCContext::getInstance ()->setSession( GroupWarDef::SESSION_LEAVE_BATTLE_TIME, 0 );

		//这个人秒除参战冷却的次数加1
		$userInfo['removeJoinCd'] += 1;
		$values = array(
				'uid' => $uid,
				'removeJoinCd' => $userInfo['removeJoinCd']
		);
		GroupWarDAO::updateUserInfo($values);
		//统计金币花费
		Statistics::gold ( StatisticsDef::ST_FUNCKEY_GROUP_WAR_REMOVE_JOIN_CD, $needGold, $nowTime );

		//调用lcserver
		RPCContext::getInstance()->removeGroupBattleJoinCd();

		Logger::info ('uid:%d removeJoinCd success, gold:%d', $uid, $needGold);
		return array ('ret' => 'ok', 'res'=>$needGold);
	}

	/**
	 * 创建今天的阵营战
	 */
	public function createTodayBattle()
	{

		$dayOfWeek = date('w');
		!$dayOfWeek && $dayOfWeek = 7;

		$btConf = btstore_get()->GROUP_BATTLE;
		$index = array_search($dayOfWeek, $btConf['battleDayArr']->toArray());
		if($index === false)
		{
			Logger::info('no battle today');
			return false;
		}

		$battleId = $dayOfWeek*2 - 1;

		Logger::info('today battle:%d, %d', $battleId, $battleId+1);
		
		$battleList = self::getCurBattleList();
		if( !isset($battleList[$battleId]) || !isset($battleList[$battleId+1]))
		{
			Logger::warning('cant find battle:%d or %d', $battleId, $battleId + 1);
			return false;
		}
		//上半场直接创建，下半场用timer
		self::createBattle($battleId);
		
		$secondCreateTime = $battleList[$battleId]['endTime'] + 60;
		TimerTask::addTask(0, $secondCreateTime, 'groupwar.createBattle', array($battleId + 1));
		Logger::info('second round will be created at :%s', date('Y-m-d H:i:s', $secondCreateTime));
		
		return true;
	}

	/**
	 * 建立阵营战
	 *
	 */
	public function createBattle($battleId = -1)
	{
		$battleId = intval($battleId);

		//参数检查
		$battleList = self::getCurBattleList();
		if( !isset($battleList[$battleId]))
		{
			Logger::warning('cant find battle:%d', $battleId);
			return false;
		}
		$battle = $battleList[$battleId];

		$now = time();
		if($battle['endTime'] < $now )
		{
			Logger::warning('it is too late to create battle:%d. exit', $battleId);
			return false;
		}		

	
		//取阵营数据，统计上一轮参加人数，根据上一轮参加人数获取配置
		$groupInfoList = GroupWarDAO::getAllGroupInfo();
		$groupInfoList = Util::arrayIndex($groupInfoList, 'groupId');
		$lastUserNum = $groupInfoList[1]['enterNum'] + $groupInfoList[2]['enterNum'];
		$btConf = self::getBattleConf($lastUserNum);
		
		//重置阵营、用户数据
		if(self::isFirstHalf($battleId))
		{			
			$lastUserData = GroupWarDAO::getAllEnterUser();
			//重置用户数据。分组的时候还想使用一下上一轮的数据			
			GroupWarDAO::resetUserInfo();		
			self::divideToGroup($lastUserData);
			
			//重置阵营数据
			$groupInfoList[1]['resource'] = intval($btConf['initResource']);
			$groupInfoList[2]['resource'] = intval($btConf['initResource']);
			
			//由于t_group_war_resource中的enter_num是后加字段。所以第一次使用时，其值为0，需要做一下初始化，给下半场使用
			if($lastUserNum==0 && count($lastUserData) > 0)
			{
				$lastUserNum = count($lastUserData);
				$btConf = self::getBattleConf($lastUserNum);
			
				$groupInfoList[1]['enterNum'] = 0;
				$groupInfoList[2]['enterNum'] = 0;
				foreach($lastUserData as $user)
				{
					$groupInfoList[$user['groupId']]['enterNum'] ++;
				}
				
				$ret = GroupWarDAO::updateGroupInfo($groupInfoList[1]);
				$ret = GroupWarDAO::updateGroupInfo($groupInfoList[2]);
			}									
		}
		else
		{
			//如果是下半场，只需要重置连胜次数
			GroupWarDAO::resetUserInfo(array('winStreak'=>0));
		}					
		
		//战场相关配置。部分配置在php的配置中，部分配置来自策划配置表
		$fieldConf = GroupWarConfig::$FIELD_CONF;
		
		$fieldConf['battleDuration'] = intval($btConf['battleDuration']);
		$fieldConf['prepareTime'] = intval($btConf['prepareTime']);
		$fieldConf['presenceIntervalMs'] = intval($btConf['presenceIntervalMs']);
		$fieldConf['joinCdTime'] = intval($btConf['joinCdTime']);
		$fieldConf['joinReadyTime'] = intval($btConf['joinReadyTime']);
		$fieldConf['maxWaitQueue'] = intval($btConf['maxWaitQueue']);
		$fieldConf['joinMinLevel'] = intval($btConf['joinMinLevel']);
		$fieldConf['speed'] = intval($btConf['speed']);
		$fieldConf['roadLength'] = intval($btConf['roadLength']);
		$fieldConf['collisionRange'] = intval($btConf['collisionRange']);
		$fieldConf['maxGroupOnlineSize'] = intval($btConf['maxGroupOnlineSize']);

		//前面检查了endTime，这里检查startTime。之所以把startTime检查放到这里， 是因为如果需要修改startTime，前面有分组的活，无法准确估计其所需时间
		$now = time();
		if($battle['startTime'] < $now )
		{
			Logger::warning('it is too late to create battle:%d. change startTime', $battleId);
			$startTime = $now + 3;
						
			$fieldConf['battleDuration'] = $battle['endTime'] - $startTime;
			$fieldConf['prepareTime'] = max(0, $fieldConf['prepareTime'] - ($startTime - $battle['startTime']) );
			$battle['startTime'] = $startTime;
		}
		
		Logger::info('last:%d, presenceInter:%d, roadLength:%d', 
						$lastUserNum, $btConf['presenceIntervalMs'], $btConf['roadLength']);
		
		//鼓舞相关配置
		$inspireConf = array(
				'baseProp' => intval($btConf['inspireBaseProp']),
				'probCoef'	=> intval($btConf['inspireProbCoef']),
				'maxAttackLevel'	=>	intval($btConf['attackDefendMaxLevel']),
				'maxDefendLevel'	=>	intval($btConf['attackDefendMaxLevel']),
				'attackDefendArr' => self::intvalArray($btConf['attackDefendArr']),
				'cdTime' => intval($btConf['inspireCdTime'])
				);

		$battleInfo = array
		(
				'attacker' => array(
								'id'=>$battle['attacker'],
								'resource'=>$groupInfoList[$battle['attacker']]['resource'],
								),
				'defender' => array(
								'id'=>$battle['defender'],
								'resource'=>$groupInfoList[$battle['defender']]['resource'],
								),
				'fieldConf' => $fieldConf,
				'inspireConf' => $inspireConf,
				'callMethods' => GroupWarConfig::$CALL_PHP_METHODS,
				'frontCallbacks' => GroupWarConfig::$FRONT_CALLBACKS,
		);

		$proxy = new PHPProxy ( 'lcserver' );
		$proxy->setDummyReturn ( true );

		$startTime = $battle['startTime'];
		if(FrameworkConfig::DEBUG == TRUE && GroupWarConfig::DEBUG_GROUP_WAR > 0)
		{
			$startTime = time() + 2;
		}
		$proxy->createGroupBattle( $battleId, $startTime, $battleInfo);

		//发一下广播
		if(self::isFirstHalf($battleId))
		{
			TimerTask::addTask(0, $battle['startTime']-GroupWarConfig::SYSTEM_MSG_FIRST_COMING,
						'groupwar.systemMsg', array($battleId, GroupWarDef::$SYSTEM_MSG['FIRST_COMING']));
		}
		else
		{
			TimerTask::addTask(0, $battle['startTime'],
						'groupwar.systemMsg', array($battleId, GroupWarDef::$SYSTEM_MSG['SECOND_START']));
		}
		
		Logger::info('create battle:%d, startTime:%s', $battleId, date('Y-m-d H:i:s', $startTime));
		return true;
	}

	/**
	 * 一场战斗结束后
	 * 这个函数需要通过loser的通道执行，里面会修改loser的session
	 * @param int $winnerId
	 * @param int $loserId
	 * @param int $fightEndTime
	 * @param array $LoserHpArr
	 * @param int $brid
	 * @param string $replayData
	 */
	public function fightWin($battleId, $attackerId, $winnerId, $loserId, $winStreak, $terminalStreak, $brid, $replayData)
	{
		Logger::info ('uid:%d win uid:%d. battleId:%d, brid:%d', $winnerId, $loserId, $battleId, $brid);

		//1. 记录战报数据,  发一下系统消息
		if($brid > 0)
		{
			EnBattle::addRecord( $brid, $replayData );

			if($attackerId == $winnerId)
			{
				$defenderId = $loserId;
			}
			else
			{
				$defenderId = $winnerId;
			}
			$atkUser = EnUser::getUserObj($attackerId);
			$defUser = EnUser::getUserObj($defenderId);
			ChatTemplate::sendGroupWarAtkMeg($atkUser->getTemplateUserInfo(),
											$defUser->getTemplateUserInfo(), $brid);
		}

		//2.发奖励
		$reward = self::rewardWhenKill($winnerId, $loserId, $winStreak, $terminalStreak);
		$winReward = $reward['winner'];
		$loserReward = $reward['loser'];

		$scoreTopN = self::broadcastScoreTopN($battleId);

		//3.发送一下奖励信息
		$winner = EnUser::getUserObj($winnerId);
		$loser = EnUser::getUserObj($loserId);
		$msg = array(
				'reward' => $loserReward,
				'extra' => array(
						'winnerName' => $winner->getUname()
						//'winStreak' => $winStreak
						),
				'topN' => $scoreTopN
		);
		RPCContext::getInstance()->sendMsg(array($loserId),
					GroupWarConfig::$FRONT_CALLBACKS['fightLose'], $msg);

		$msg = array(
				'reward' => $winReward,
				'extra' => array(
						'loserName' => $loser->getUname()
						//'winStreak' => $winStreak
						),
				'topN' => $scoreTopN
		);

		//当前是在winner的connection中，且lcserver已经设置了callback，所以直接return就可以
		return $msg;
	}

	/**
	 * 一场战斗结失败后
	 * 这个函数需要通过loser的通道执行，里面会修改loser的session
	 * @param int $uid
	 * @param int $fightEndTime
	 * @param array $curHpArr
	 */
	public function fightLose($uid, $fightEndTime/*, $curHpArr*/)
	{
		Logger::info ('uid:%d lose fight at time:%d', $uid, $fightEndTime);

		//1. 记录失败者从战斗状态退出的时间。之所以在php记session，是因为在lcserver中fightLose和touchdown时，拿到用户的connection太麻烦
		RPCContext::getInstance()->setSession(GroupWarDef::SESSION_LEAVE_BATTLE_TIME, $fightEndTime);

		//2. 给失败者扣血，自动补血（现在不扣血）
		//EnFormation::subUserHeroHp($curHpArr, $uid);
	}


	/**
	 * 当一个用户达阵后，需要计算相关的奖励，然后扣血
	 * @param int $uid
	 * @param int $touchDownTime 达阵个时间
	 * @param array $heroHpArr  各个英雄当前血量
	 */
	public function touchDown($battleId, $uid, $touchDownTime, /*$heroHpArr,*/ $group1Resource, $group2Resource )
	{
		Logger::info('uid:%d touch down at time:%d in battle:%d', $uid, $touchDownTime, $battleId);

		//1. 记录这个用户从战斗状态退出的时间
		RPCContext::getInstance()->setSession(GroupWarDef::SESSION_LEAVE_BATTLE_TIME, $touchDownTime);

		//2. 扣血（现在不扣血）
		//EnFormation::subUserHeroHp($heroHpArr, $uid);

		//3. 更新一下阵营资源
		$groupInfo = array(
				'groupId' => 1,
				'resource' => $group1Resource,
		);
		$ret = GroupWarDAO::updateGroupInfo($groupInfo);

		$groupInfo = array(
				'groupId' => 2,
				'resource' => $group2Resource,
		);
		$ret = GroupWarDAO::updateGroupInfo($groupInfo);

		$reward = self::rewardWhenTouchDown($uid);

		$scoreTopN = self::broadcastScoreTopN($battleId);

		return array( 'reward' => $reward, 'topN' => $scoreTopN);
	}

	/**
	 * 一场战役结束
	 * @param int battleId
	 * @param array attackGroup
	 * @param array defendGroup
	 */
	public function battleEnd($battleId, $attackGroup, $defendGroup)
	{
		Logger::info('battleEnd:%d', $battleId);
	
		$roundEnd = 0;
		if(self::isFirstHalf($battleId))
		{
			$userList = GroupWarDAO::getAllEnterUser(array($battleId));			
		}	
		else
		{
			$userList = GroupWarDAO::getAllEnterUser(array($battleId, $battleId-1));
			$roundEnd = 1;
		}	
		
		//1. 更新阵营的资源数
		$attackGroupInfo = array(
				'groupId' => $attackGroup['groupId'],
				'resource' => $attackGroup['resource']
		);
		$defendGroupInfo = array(
				'groupId' => $defendGroup['groupId'],
				'resource' => $defendGroup['resource']
		);
		
		if( $roundEnd )
		{
			//算一下这一轮有多少人参加。 为了方便，只统计下半场的人数
			$groupNums = array( 1=>0, 2=>0);
			foreach($userList as $userInfo)
			{
				$groupNums[$userInfo['groupId']]++;
			}
			$attackGroupInfo['enterNum'] = $groupNums[$attackGroup['groupId']];
			$defendGroupInfo['enterNum'] = $groupNums[$defendGroup['groupId']];
		}
		$ret = GroupWarDAO::updateGroupInfo($attackGroupInfo);
		$ret = GroupWarDAO::updateGroupInfo($defendGroupInfo);
		

		//2. 发送结算信息
		//阵营数据
		if($attackGroup['groupId'] == 1)
		{
			$group1 = $attackGroup;
			$group2 = $defendGroup;
		}
		else
		{
			$group1 = $defendGroup;
			$group2 = $attackGroup;
		}
		$groupRewardCoefs = self::calcGroupRewardCoef($group1, $group2);

		$winGroupId = 0;
		if($group1['resource'] > $group2['resource'])
		{
			$winGroupId = 1;
		}
		else if($group1['resource'] < $group2['resource'])
		{
			$winGroupId = 2;
		}
		
		//给所有用户发送结算数据
		usort($userList, array('GroupWar', 'compUserScore') );
		$rank = 0;
		foreach($userList as $userInfo )
		{
			$rank++;
			$uid = $userInfo['uid'];

			//参加了本半场的才发通知
			if($userInfo['battleId'] == $battleId)
			{
				$msg = array(
						'roundEnd' => $roundEnd,
						'resource1' => $group1['resource'],
						'resource2' => $group2['resource'],
						'winGroup' => $winGroupId,
						'rank' => $rank,
						'score' => $userInfo['score'],
						'killNum' => $userInfo['killNum'],
				);
				RPCContext::getInstance()->sendMsg(array($uid),
				GroupWarConfig::$FRONT_CALLBACKS['reckon'], $msg);
			}			
		}

		//3.释放战斗
		RPCContext::getInstance()->freeGroupBattle($battleId);

		//下半场结束时，发奖
		if($roundEnd)
		{
			$now = Util::getTime();
			$reckonTime = $now + rand(GroupWarConfig::RECKON_START_OFFSET_MIN, GroupWarConfig::RECKON_START_OFFSET_MAX);
			Logger::info('rewardOnEnd will do in %d', $reckonTime);
			TimerTask::addTask(0, $reckonTime, 'groupwar.rewardOnEnd', array($battleId));
		}
	}


	/**
	 * 起一个进程发奖
	 * @param int $battleId
	 */
	public function rewardOnEnd($battleId)
	{
		Util::asyncExecute('groupwar.doRewardOnEnd', array($battleId));
	}

	/**
	 * 战斗结束时，发排名等奖励
	 * @param int $battleId
	 */
	public function doRewardOnEnd($battleId)
	{
		$battleId = intval($battleId);
		if($battleId ==0)
		{
			Logger::warning('invalid battleId');
			return;
		}

		if(self::isFirstHalf($battleId))
		{
			Logger::warning('only second half battle can get here');
			return;
		}

		//取出所有参战的用户
		$userList = GroupWarDAO::getAllEnterUser(array($battleId, $battleId-1), true);
		usort($userList, array('GroupWar', 'compUserScore') );

		//阵营数据
		$groupInfoList = GroupWarDAO::getAllGroupInfo();
		$groupInfoList = Util::arrayIndex($groupInfoList, 'groupId');
		$groupRewardCoefs = self::calcGroupRewardCoef($groupInfoList[1], $groupInfoList[2]);

		$winGroupId = 0;
		if($groupInfoList[1]['resource'] > $groupInfoList[2]['resource'])
		{
			$winGroupId = 1;
		}
		else if($groupInfoList[1]['resource'] < $groupInfoList[2]['resource'])
		{
			$winGroupId = 2;
		}
		
		$rank = 0;		
		foreach($userList as $userInfo )
		{
			$rank++;
			$uid = $userInfo['uid'];
			if($userInfo['groupId'] > 2)
			{				
				Logger::info('ignor reward for uid:%d, rank:%d, group:%d', $uid, $rank, $userInfo['groupId']);
				continue;
			}
			Logger::info('to reward for uid:%d, rank:%d, group:%d', $uid, $rank, $userInfo['groupId']);
			try
			{
				$reward = array(
						'rank' => $rank,
						'score' => $userInfo['score'],
						'honour' => 0,//$userInfo['honour'],
						'belly' => 0,//$userInfo['belly'],
						'experience' => 0,//$userInfo['experience'],
						'prestige' => 0,//$userInfo['prestige'],
						'items' => array()
				);
				if($userInfo['score'] > 0)
				{
					$userInfo['rank'] = $rank;
					$ret = self::rewardForUser($userInfo, $winGroupId, $groupRewardCoefs[$userInfo['groupId']]);

					$reward['belly'] += $ret['belly'];
					$reward['experience'] += $ret['experience'];
					$reward['prestige'] += $ret['prestige'];
					$reward['honour'] += $ret['honour'];
					$reward['items'] = $ret['items'];
				}
				//Logger::info('reward for uid:%d done', $uid);
				Logger::debug('send reward to uid:%d, reward:%s', $uid, $reward);

				MailTemplate::sendGroupWarReward($userInfo['uid'], $reward);
				
				if($rank == 1)
				{
					EnAchievements::notify($userList[0]['uid'], AchievementsDef::PIRATE_BATTLE_JIFEN_FIRST, 1);//积分第一
				}
				
				//标记一下这个人已经领完奖励了
				$values = array(
						'uid' => $uid,
						'groupId' => $userInfo['groupId'] + 10
				);
				GroupWarDAO::updateUserInfo($values);
			}
			catch(Exception $e)
			{
				Logger::FATAL('send gourpbattle reward to user:%d failed!order:%d', $uid, $rank);
			}

		}
	}



	/**
	 * 发送系统消息
	 * @param int $battleId
	 * @param int $type
	 */
	public function systemMsg($battleId, $type)
	{
		$battleList = self::getCurBattleList();
		if( !isset($battleList[$battleId]))
		{
			Logger::warning('invalid battle id:%d', $battleId);
			return;
		}
		$battle = $battleList[$battleId];

		switch($type)
		{
			case GroupWarDef::$SYSTEM_MSG['FIRST_COMING']:
				ChatTemplate::sendGroupWarFirstHarfBeingStart();
				TimerTask::addTask(0, $battle['startTime'], 'groupwar.systemMsg', array($battleId, GroupWarDef::$SYSTEM_MSG['FIRST_START']));
				break;

			case GroupWarDef::$SYSTEM_MSG['FIRST_START']:
				ChatTemplate::sendGroupWarFirstHarfStart();
				TimerTask::addTask(0, $battle['endTime'], 'groupwar.systemMsg', array($battleId, GroupWarDef::$SYSTEM_MSG['FIRST_END']));
				break;

			case GroupWarDef::$SYSTEM_MSG['FIRST_END']:
				ChatTemplate::sendGroupWarFirstHarfEnd();
				//$secondHalf = $battleList[$battleId+1];
				//TimerTask::addTask(0, $secondHalf['startTime'], 'groupwar.systemMsg', array($battleId, GroupWarDef::$SYSTEM_MSG['SECOND_START']));
				break;

			case GroupWarDef::$SYSTEM_MSG['SECOND_START']:
				ChatTemplate::sendGroupWarSecondHarfStart();
				break;
		}

	}

	/**
	 * 计算阵营奖励系数
	 * @param array $group1
	 * @param array $group2
	 * @return multitype:number
	 */
	private static function calcGroupRewardCoef($group1, $group2)
	{
		$btConf = btstore_get()->GROUP_BATTLE;

		//计算一下阵营奖励系数
		$winGroupRewardCoef = 1;
		$resourceRatio = 1;	//其实这个最大有意义的值是2
		$winGroupId = 0;
		if($group1['resource'] > $group2['resource'])
		{
			$winGroupId = 1;
			$resourceRatio = $group2['resource']==0 ? 10000 : ($group1['resource'] / $group2['resource']);
		}
		else if($group1['resource'] < $group2['resource'])
		{
			$winGroupId = 2;
			$resourceRatio = $group1['resource']==0 ? 10000 : ($group2['resource'] / $group1['resource']);
		}
		if( $winGroupId != 0)
		{
			$maxV = intval($btConf['resourceRewardMax']);
			$minV = intval($btConf['resourceRewardMin']);
			//1+min(资源最终奖励系数上限,max( (胜利方资源数/初始资源数-1)* 资源最终奖励系数上限, 资源最终奖励系数下限) )/10000
			$winGroupRewardCoef = 1 + min($maxV,  max( ($resourceRatio-1)*$maxV, $minV) )/GroupWarConfig::COEF_BASE;
			$returnData = array(
					$winGroupId => $winGroupRewardCoef,
					3-$winGroupId => 1
			);
		}
		else
		{
			$returnData = array(
					1 => 1,
					2 => 1
			);
		}

		return $returnData;
	}


	/**
	 * 获取用户的结算奖励
	 * @param int $battleId
	 * @param int $uid
	 */
	private static function getReckonReward( $userInfo, $winGroupId, $groupRewardCoef)
	{
		$uid = $userInfo['uid'];
		$rank = $userInfo['rank'];

		$btConf = btstore_get()->GROUP_BATTLE;
		$rankRewardList = btstore_get()->GROUP_BATTLE_RANK->toArray();

		$rewardInfo = array(
				'rankReward' => array(),
				'winReward' => array(),
		);

		$rewardInfo['rankReward'] = self::getRankReward($uid, $rank, $groupRewardCoef);

		//如果属于胜利阵营，还有额外的胜利奖励
		if($userInfo['groupId'] == $winGroupId)
		{
			$user = EnUser::getUserObj($uid);
			$level = $user->getLevel();
			$rewardInfo['winReward'] = array(
					'belly' => $btConf['winBelly']*$level,
					'honour' => $btConf['winHonour'],
			);
		}

		return $rewardInfo;
	}

	/**
	 * 一个用户的排名奖励
	 * @param int $uid
	 * @param int $rank
	 * @param array $rankRewardList
	 * @param int $groupRewardCoef
	 * @return array
	 */
	private static function getRankReward($uid, $rank, $groupRewardCoef)
	{
		$rankRewardList = btstore_get()->GROUP_BATTLE_RANK->toArray();

		if(isset($rankRewardList[$rank]))
		{
			$reward = $rankRewardList[$rank];
		}
		else//没有配置的排名，都用最后一个名的奖励配置
		{
			$reward = end($rankRewardList);
		}

		$user = EnUser::getUserObj($uid);
		$level = $user->getLevel();

		$addBelly = floor($reward['belly']*$level*$groupRewardCoef);
		$addExp = floor($reward['experience']*$level*$groupRewardCoef);
		$addPrestige = floor($reward['prestige']*$groupRewardCoef);
		$addExecution = floor($reward['execution']*$groupRewardCoef);
		$addHonour = floor($reward['honour']*$groupRewardCoef);
		$addGold = $reward['gold'];
		$addItems = $reward['itemArr'];

		$returnData = array(
				'belly' => $addBelly,
				'experience' => $addExp,
				'prestige' => $addPrestige,
				'execution' => $addExecution,
				'gold' => $addGold,
				'honour' => $addHonour,
				'items' => $reward['itemArr']
				);
		return $returnData;
	}


	/**
	 * 发放某个用户的排名奖励
	 * @param array $userInfo
	 * @param int $winGroupId
	 * @param array $btConf
	 * @param array $rankRewardList
	 * @param int $groupRewardCoef
	 */
	private static function rewardForUser($userInfo, $winGroupId, $groupRewardCoef)
	{
		$btConf = btstore_get()->GROUP_BATTLE;
		$rankRewardList = btstore_get()->GROUP_BATTLE_RANK;

		$uid = intval($userInfo['uid']);
		$rank = intval($userInfo['rank']);
		$groupId = intval($userInfo['groupId']);

		$user = EnUser::getUserObj($uid);

		$rewardInfo = self::getReckonReward($userInfo, $winGroupId, $groupRewardCoef);

		$belly = 0;
		$experience = 0;
		$prestige = 0;
		$execution= 0;
		$honour = 0;

		//如果属于胜利阵营，发胜利奖励
		if( !empty($rewardInfo['winReward']) )
		{
			$winReward = $rewardInfo['winReward'];
			$belly += $winReward['belly'];
			$honour += $winReward['honour'];
		}

		//排名奖励
		$rankReward = $rewardInfo['rankReward'];

		$honour += $rankReward['honour'];
		$belly += $rankReward['belly'];
		$experience += $rankReward['experience'];
		$prestige += $rankReward['prestige'];
		$execution += $rankReward['execution'];

		EnHonourShop::addFinallyHonourPoint($uid, $honour);

		if ( !empty($belly) && $user->addBelly($belly) == FALSE )
		{
			Logger::FATAL('add belly failed');
			throw new Exception('fake');
		}
		if ( !empty($experience) && $user->addExperience($experience) == FALSE )
		{
			Logger::FATAL('add experience failed');
			throw new Exception('fake');
		}
		if ( !empty($prestige) && $user->addPrestige($prestige) == FALSE )
		{
			Logger::FATAL('add prestige failed');
			throw new Exception('fake');
		}
		if ( !empty($execution) && $user->addExecution($execution) == FALSE )
		{
			Logger::FATAL('add execution failed');
			throw new Exception('fake');
		}

		$gold = $rankReward['gold'];
		if($gold > 0)
		{
			if( $user->addGold($gold) == FALSE)
			{
				Logger::FATAL('add gold failed');
				throw new Exception('fake');
			}
			Statistics::gold(StatisticsDef::ST_FUNCKEY_GROUP_WAR_RAND_REWARD,
					$gold, Util::getTime(), FALSE, $user->getPid() );
		}

		$items = array();
		if (!empty($rankReward['items']))
		{
			$itemTemplates = Util::arrayIndexCol($rankReward['items'], 0, 1);

			$bag = BagManager::getInstance()->getBag($uid);
			$ret = $bag->addItemsByTemplateID($itemTemplates, true);
			$grid = $bag->update();
			//因为从来没有配过物品，此处代码没有运行过
		}

		$user->update();

		$returnData = array(
				'honour' => $honour,
				'belly' => $belly,
				'experience' => $experience,
				'prestige' => $prestige,
				'execution' => $execution,
				'gold' => $gold,
				'items' => $items
				);

		return $returnData;
	}

	/**
	 * 计算参战奖励，返回奖励值
	 * @param int $uid
	 */
	private static function rewardWhenJoin($uid)
	{
		$btConf = btstore_get()->GROUP_BATTLE;
		$joinScore = intval($btConf['joinScore']);
		$joinHonour = intval($btConf['joinHonour']);

		Logger::info('uid:%d join get score:%d, honour:%d', $uid, $joinScore, $joinHonour);

		$userInfo = GroupWarDAO::getUserInfo($uid);
		
		$userInfo['score'] += $joinScore;
		$userInfo['honour'] += $joinHonour;
		
		$values = array(
				'uid' => $uid,
				'score' => $userInfo['score'],
				'honour' => $userInfo['honour'],
				'scoreTime' => Util::getTime ()
				);
		
		//TODO：参战时更新一下战斗力，这个操作放在这里不好，忍忍先。要不然需要对代码结构进行调整
		$user = EnUser::getUserObj($uid);
		$fightForce = $user->getFightForce();
		if($fightForce > $userInfo['maxFightForce'])
		{
			$values['maxFightForce'] = $fightForce;
		}
		
		GroupWarDAO::updateUserInfo($values);

		EnHonourShop::addHonourPoint($uid, $joinHonour);
	
		
		EnAchievements::notify($uid, AchievementsDef::PIRATE_BATTLE_JOIN_CNT, 1);//参与次数

		return array('score' => $joinScore, 'honour' => $joinHonour);
	}

	private static function rewardWhenTouchDown($uid)
	{
		$btConf = btstore_get()->GROUP_BATTLE;
		$plunderScore = intval($btConf['plunderScore']);
		$plunderHonour= intval($btConf['plunderHonour']);

		Logger::info('uid:%d touch down get score:%d, honour:%d', $uid, $plunderScore, $plunderHonour);

		$userInfo = GroupWarDAO::getUserInfo($uid);
		if($plunderScore > 0 || $plunderHonour > 0)
		{
			$userInfo['score'] += $plunderScore;
			$userInfo['honour'] += $plunderHonour;
			$userInfo['resource'] += 1;
			$values = array(
					'uid' => $uid,
					'score' => $userInfo['score'],
					'honour' => $userInfo['honour'],
					'resource' => $userInfo['resource'],
					'scoreTime' => Util::getTime ()
			);
			GroupWarDAO::updateUserInfo($values);

			EnHonourShop::addHonourPoint($uid, $plunderHonour);
		}

		EnAchievements::notify($uid, AchievementsDef::PIRATE_BATTLE_RESOURCE_CNT, 1);//占领资源数
		return array('score' => $plunderScore, 'honour' => $plunderHonour);
	}

	/**
	 * 计算战斗后奖励变化
	 * 这个地方需要在同时计算winner和loser的积分变化
	 * @param int $winnerId
	 * @param int $loserId
	 * @param int $winStreak
	 */
	private static function rewardWhenKill($winnerId, $loserId, $winStreak, $terminalStreak)
	{
		$btConf = btstore_get()->GROUP_BATTLE;

		$killBelly = intval($btConf['killBelly']);
		$killExperience = intval($btConf['killExperience']);
		$killPrestige = intval($btConf['killPrestige']);
		//$killSoul = intval($btConf['killSoul']);
		$killHonour = intval($btConf['killHonour']);
		$killScoreArr = self::intvalArray($btConf['killScoreArr']->toArray());
		$streakCoefArr = self::intvalArray($btConf['streakCoefArr']->toArray());
		$streakHonourArr = self::intvalArray($btConf['streakHonourArr']->toArray());

		$killScore = self::getMatchLevel($killScoreArr, $winStreak);
		$streakCoef = self::getMatchLevel($streakCoefArr, $terminalStreak);
		$streakHonour = self::getMatchLevel($streakHonourArr, $winStreak);

		$loserInfo = GroupWarDAO::getUserInfo($loserId);
		$winnerInfo = GroupWarDAO::getUserInfo($winnerId);

		$loserSocre = $loserInfo['score'];

		$exScore = ceil($streakCoef * $loserSocre / GroupWarConfig::COEF_BASE);

		//积分变化
		$winScore = $killScore + $exScore;
		$loseScore = -$exScore;

		//胜利者的其他奖励
		$winHonour = $killHonour + $streakHonour;

		$winner = EnUser::getUserObj($winnerId);
		$winnerLevel = $winner->getLevel();

		$winBelly = $winnerLevel * $killBelly;
		$winExp =  $winnerLevel * $killExperience;
		$winPrestige = $killPrestige;

		//本来有奖影魂的，后来不需要了。奖励影魂时需要考虑阵营战的参战最低等级和影魂开启等级的问题

		Logger::info('winner:%d, winStreak:%d, loser:%d, terminalStreak:%d, killScore:%d, exScore:%d, honour:%d, belly:%d, exp:%d, prestige:%d',
			$winnerId, $winStreak, $loserId, $terminalStreak, $killScore, $exScore,
			$winHonour, $winBelly, $winExp, $winPrestige);

		$returnData = array(
				'winner' => array(),
				'loser' => array()
				);
		if($winScore > 0 || $winHonour > 0 || $winBelly > 0 || $winExp > 0 || $winPrestige > 0 )
		{
			$winnerInfo['killNum'] += 1;
			$winnerInfo['score'] += $winScore;
			$winnerInfo['honour'] += $winHonour;
			$winnerInfo['belly'] += $winBelly;
			$winnerInfo['experience'] += $winExp;
			$winnerInfo['prestige'] += $winPrestige;
			$values = array(
					'uid' => $winnerId,
					'killNum' => $winnerInfo['killNum'],
					'winStreak' => $winStreak,
					'score' => $winnerInfo['score'],
					'honour' => $winnerInfo['honour'],
					'belly' => $winnerInfo['belly'],
					'experience' => $winnerInfo['experience'],
					'prestige' => $winnerInfo['prestige'],
					'scoreTime' => Util::getTime ()
			);
			GroupWarDAO::updateUserInfo($values);

			$user = EnUser::getUserObj($winnerId);

			$user->addBelly($winBelly);
			$user->addExperience($winExp);
			$user->addPrestige($winPrestige);

			$user->update();

			EnHonourShop::addHonourPoint($winnerId, $winHonour);
		}
		$returnData['winner'] = array(
				'score' => $winScore,
				'plunderScore' => $exScore,
				'honour' => $winHonour,
				'belly' => $winBelly,
				'experience' => $winExp,
				'prestige' => $winPrestige,
		);

		//失败者
		if($loseScore != 0)
		{
			$loserInfo['score'] += $loseScore;
			$values = array(
					'uid' => $loserId,
					'score' => $loserInfo['score'],
					'winStreak' => 0,
			);
			GroupWarDAO::updateUserInfo($values);
		}
		$returnData['loser'] = array(
				'score' => $loseScore,
				'plunderScore' => $exScore,
		);
		
		//成就
		EnAchievements::notify($winnerId, AchievementsDef::PIRATE_BATTLE_ENEMIES_KILL, 1);//海贼战场中杀敌数目
		EnAchievements::notify($winnerId, AchievementsDef::PIRATE_BATTLE_ROB_JIFEN_CNT, $exScore);//掠夺积分数
		EnAchievements::notify($winnerId, AchievementsDef::PIRATE_BATTLE_CONTIOUS_WIN, $winStreak);//连胜数

		EnAchievements::notify($loserId, AchievementsDef::PIRATE_BATTLE_LOSE_CNT, 1);//失败次数
		return $returnData;
	}

	/**
	 * 向前端广播积分topN的数据
	 */
	private static function broadcastScoreTopN($battleId)
	{
		$scoreTopN = GroupWarDAO::getScoreTopN(GroupWarConfig::SCORE_TOP_N);
		$prob = rand(0, GroupWarConfig::COEF_BASE);
		Logger::debug('broadcastScoreTopN, prob:%d, send_prob:%d', $prob, GroupWarConfig::SEND_SCORE_TOP_PROB );
		if(  $prob <= GroupWarConfig::SEND_SCORE_TOP_PROB )
		{
			RPCContext::getInstance()->broadcastGroupBattle($battleId, $scoreTopN, GroupWarConfig::$FRONT_CALLBACKS['scoreTopN']);
		}
		return $scoreTopN;
	}

	/**
	 * 获取用户的战斗数据
	 * @param int $uid
	 */
	private static function getBattleData($uid)
	{
		$user = EnUser::getUserObj($uid);
		$user->prepareItem4CurFormation();
		$formationID = $user->getCurFormation();

		$userFormation = EnFormation::getFormationInfo($uid);
		if ( EnFormation::checkUserFormation($uid, $userFormation) != 'ok' )
		{
			return false;
		}
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation, false);
		$userFormationArr = BattleUtil::unsetEmpty ( $userFormationArr );

		$formation = array('name' => $user->getUname(),
		                            'level' => $user->getLevel(),
		                            'isPlayer' => true,
		                            'flag' => 0,
		                            'formation' => $formationID,
		                            'uid' => $uid,
		                            'arrHero' => $userFormationArr);

		$formation = BattleUtil::prepareClientFormation ( $formation, array());

		$arrHero = BattleUtil::prepareBattleFormation ( $userFormationArr );


		//formation用于生成战报，arrHero用于调用battle模块 $formation['arrHero'] 和 arrHero中有几个重复字段，为了方便没有优化
		return array(
					'formation' => $formation,
					'arrHero' => $arrHero);
	}


	/**
	 * 获取当前这一轮(周)的战斗列表
	 *
	 */
	private static function getCurBattleList()
	{
		$SEC_OF_DAY = 86400;
		$now = time();
		$dayOfWeek = date('w');
		!$dayOfWeek && $dayOfWeek = 7;

		$btConf = btstore_get()->GROUP_BATTLE;
		$battleDuration = intval($btConf['battleDuration']);
		$battleDayArr = $btConf['battleDayArr'];
		$firstStartTime = $btConf['startTime'];
		$halfRoundInterval = intval($btConf['halfRoundInterval']);		

		$battleList = array();
		foreach( $battleDayArr as $day)
		{
			$battleId = $day*2-1;
			$startDay = date('Y-m-d',($now + ($day - $dayOfWeek)*$SEC_OF_DAY ));

			$startTime = strtotime($startDay. ' ' . $firstStartTime);
			
			//根据boss战偏移，算我的偏移
			if(GameConf::BOSS_OFFSET >= 1800)
			{
				$startTime += $battleDuration*2 + $halfRoundInterval;//偏移一轮战斗的时间
			}
			
			$battleList[$battleId] = array('id' => $battleId,
											'startTime' => $startTime,
											'endTime' => $startTime + $battleDuration,
											'attacker' => 1,
											'defender' => 2); //上半场A打B
			$battleId++;

			$startTime += $halfRoundInterval + $battleDuration;
			$battleList[$battleId] = array('id' => $battleId,
											'startTime' => $startTime,
											'endTime' => $startTime + $battleDuration,
											'attacker' => 2,
											'defender' => 1); //上半场B打A
		}

		//开启调试模式时，直接修改指定测试的战斗和其后的一场战斗的起始，结束时间
		if(FrameworkConfig::DEBUG == TRUE && GroupWarConfig::DEBUG_GROUP_WAR > 0)
		{
			$battleId = $dayOfWeek*2 - 1;
			$battleList[$battleId]['startTime'] = $now-1;
			$battleList[$battleId]['endTime'] = time()+$battleDuration;

			$battleList[$battleId+1]['startTime'] = $now-1;
			$battleList[$battleId+1]['endTime'] = time()+$battleDuration;
			Logger::info('debug model:%d', GroupWarConfig::DEBUG_GROUP_WAR );
		}

		return $battleList;
	}


	/**
	 * 用户分组
	 */
	private static function divideToGroup($lastUserData)
	{
		$method = 1;
		if(GroupWarConfig::DIVIDE_GROUP_METHOD == 3)
		{
			$method = rand(1,2);
		}
		else
		{
			$method = GroupWarConfig::DIVIDE_GROUP_METHOD;
		}
		Logger::info('divideToGroup, top:%d, method:%d', GroupWarConfig::ARENA_FRONT_NUM, $method);
		
		try 
		{
			self::divideToGroupFromArena(GroupWarConfig::ARENA_FRONT_NUM, $method, $lastUserData);
		}
		catch (Exception $e)
		{
			Logger::FATAL('divideToGroup failed!  err:%s ', $e->getMessage ());
		}
	}

	/**
	 * 取竞技场的前N名来分组
	 * @param int $topN
	 */
	private static function divideToGroupFromArena($topN, $method, $lastUserData)
	{
		//取竞技场前N名
		$arenaTopN = self::getArenaTopN($topN); 
	
		$lastUserData = Util::arrayIndex($lastUserData, 'uid');
		$now = Util::getTime();
		$userListLong = array();
		$userList = array();
		foreach($arenaTopN as $value)
		{
			$uid = $value['uid'];
			$userObj = EnUser::getUserObj($uid);
			
			$user = array(
					'uid' => $uid,
					'pid' => $userObj->getPid(),
					'position' => $value['position'],
					'uname' => $userObj->getUname(),
					'level' => $userObj->getLevel(),
					'vip' => $userObj->getVip(),
					'lastLoginTime' => $userObj->getLastLoginTime(),
					'score' => 0,
					);			
			
			if(isset($lastUserData[$uid]))
			{
				$user['score'] = $lastUserData[$uid]['score'];
			}	
			if(isset($lastUserData[$uid]))
			{
				$user['maxFightForce'] = $lastUserData[$uid]['maxFightForce'];
			}	
			else
			{
				//如果这个人上一次没有参战，lastUserData中就没有他的战斗力数据
				$ret = GroupWarDAO::getUserInfo($uid);
				$user['maxFightForce'] = isset($ret['maxFightForce'])?$ret['maxFightForce']:0;
			}
			if($user['lastLoginTime'] < $now - GroupWarConfig::DIVIDE_GROUP_LAST_LOGIN)
			{
				$userListLong[] = $user;
			}
			else
			{
				$userList[] = $user;
			}
		}
		usort($userList, array('GroupWar', 'compUserVip') );
		$speciUserList = array_splice($userList, 0, GroupWarConfig::DIVIDE_GROUP_VIP_TOP_N);
		
		$userList = array_merge($userList, $userListLong);
		usort($userList, array('GroupWar', 'compUserArena') );
		$userList =  array_merge($speciUserList, $userList);
		
		$groupIdA = rand(1,2);
		$groupIdB = 3-$groupIdA;
		$pos = 0;
		foreach( $userList as $user)
		{
			$uid = $user['uid'];
			$pos++;

			$groupId = 0;
			if($method == 2 || $pos <= GroupWarConfig::DIVIDE_GROUP_VIP_TOP_N)
			{
				$groupId = (floor($pos/2) % 2) ? $groupIdB : $groupIdA;
			}
			else
			{
				$groupId = ($pos % 2) ? $groupIdA : $groupIdB;
			}

			//前面几个人打info日志，方便线上出现势力不均时查找原因
			if($pos <= GroupWarConfig::DIVIDE_GROUP_VIP_TOP_N)
			{
				Logger::info('divide group. index:%d, pid:%d, vip:%d, level:%d, score:%d, pos:%d, fightForce:%d, lastlogin:%d, group:%d',
				$pos, $user['pid'], $user['vip'], $user['level'], $user['score'], $user['position'], $user['maxFightForce'], $user['lastLoginTime'], $groupId);
			}
			else
			{
				Logger::debug('divide group. index:%d, pid:%d, vip:%d, level:%d, score:%d, pos:%d, fightForce:%d, lastlogin:%d, group:%d', 
					$pos, $user['pid'], $user['vip'], $user['level'], $user['score'], $user['position'], $user['maxFightForce'], $user['lastLoginTime'], $groupId);
			}

			$userInfo = array(
					'uid' => $uid,
					'uname' => $user['uname'],
					'groupId' => $groupId,
			);

			GroupWarDAO::setUserInfo($userInfo);
		};
	}
	
	
	/**
	 * 取竞技场前N名。如果有上一轮发奖时候的数据，就用这个数据；如果没有就用当前排名
	 * @param int $topN
	 */
	protected static function getArenaTopN($topN)
	{
		$arenaTopN = array();
		
		//默认取上一轮发奖时的排名
		try 
		{
			$arenaTopN = EnArena::getArenaLastRank($topN);			
		}
		catch (Exception $e)
		{
			Logger::FATAL('EnArena::getArenaLastRank failed!  err:%s ', $e->getMessage ());
		}
		
		if(!empty($arenaTopN))
		{			
			return $arenaTopN;
		}
				
		Logger::info('no data of arena last rank, get now rank ');
		//没有就取当前排名
		$pos = 1;
		$arrField = array('uid', 'position');
		while(true)
		{
			$arrPos = array();
			for($i = 0; $i < CData::MAX_FETCH_SIZE && $pos <= $topN; $i++)
			{
				$arrPos[] = $pos;
				$pos++;
			}
			$ret = EnArena::getArrPostion($arrPos, $arrField);
			if(empty($ret))
			{
				break;
			}
			$arenaTopN = array_merge($arenaTopN, $ret);			
		}
		return $arenaTopN;
	}

	/**
	 * 根据战斗ID判断是否是上半场
	 */
	public static function isFirstHalf($battleId)
	{
		return ($battleId % 2 ==1);
	}


	/**
	 * 获取一个随机分组
	 */
	private static function getRandGroup()
	{
		return rand(1,2);
	}

	/**
	 * 按照积分和杀敌数对用户排序
	 */
	public static function compUserScore($user1, $user2)
	{
		if( $user1['score'] == $user2['score'])
		{
			if( $user1['killNum'] == $user2['killNum']  )
			{
				if( $user1['scoreTime'] == $user2['scoreTime']  )
				{
					return $user1['uid'] < $user2['uid'] ? -1 : 1;
				}
				else
				{
					return $user1['scoreTime'] < $user2['scoreTime'] ? -1 : 1;
				}
			}
			else
			{
				return $user1['killNum'] > $user2['killNum'] ? -1 : 1;
			}
		}
		else
		{
			return $user1['score'] > $user2['score'] ? -1 : 1;
		}
	}
	
	/**
	 * 按vip和上一轮积分排序
	 */
	public static function compUserVip($user1, $user2)
	{
		$sortKeys = array('vip', 'maxFightForce', 'position', 'score', 'level', 'uid');
		
		foreach( $sortKeys as $key)
		{
			if($user1[$key] == $user2[$key])
			{
				continue;
			}
			if($key =='position' )
			{
				return $user1[$key] < $user2[$key] ? -1 : 1;
			}				
			else
			{
				return $user1[$key] > $user2[$key] ? -1 : 1;
			}
		}
		return 0;
	}
	

	/**
	 * 按竞技场排名排序
	 */
	public static function compUserArena($user1, $user2)
	{
		if( $user1['maxFightForce'] == $user2['maxFightForce'])
		{
			return $user1['position'] < $user2['position'] ? -1 : 1;
		}
		else
		{
			return $user1['maxFightForce'] > $user2['maxFightForce'] ? -1 : 1;
		}
	}
	
	/**
	 * 将数组中的值都变成int
	 */
	public static function intvalArray($arr)
	{
		if(!is_array($arr))
		{
			return intval($arr);
		}
		$newArr = array();
		foreach($arr as $key => $value)
		{
			$newArr[$key] = self::intvalArray($value);
		}
		return $newArr;
	}

	/**
	 * @param array $levelArr  array{ 0=> array(0=>, 1=>)  }
	 * @param int $key
	 */
	private static function getMatchLevel($levelArr, $key)
	{
		foreach( $levelArr as $value)
		{
			if($key <= $value[0])
			{
				break;
			}
		}
		return $value[1];
	}
	
	private static function getBattleConf($lastUserNum)
	{
		$btConf = btstore_get()->GROUP_BATTLE->toArray();
		$diff = array();
		foreach($btConf['diff'] as $value)
		{
			if($lastUserNum >= $value['userNum'])
			{
				$diff = $value;
			}
			else
			{
				break;
			}
		}
		if(empty($diff))
		{
			Logger::warning('no config, lastUserNum:%d', $lastUserNum);
			throw new Exception('config');
		}
		foreach($diff as $key => $value)
		{
			$btConf[$key] = $value;
		}
		return $btConf;
	}
	
	public function getAutoJoin()
	{
		$ret = array('isAuto'=>0);
		return $ret;
	}
	
	public function setAutoJoin()
	{
		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
