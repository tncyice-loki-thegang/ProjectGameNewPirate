<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldwarLogic.class.php 40907 2013-03-18 09:31:27Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldwar/WorldwarLogic.class.php $
 * @author $Author: YangLiu $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-03-18 17:31:27 +0800 (一, 2013-03-18) $
 * @version $Revision: 40907 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : WorldwarLogic
 * Description : 跨服赛逻辑类
 * Inherit     :
 **********************************************************************************************************************/
class WorldwarLogic
{

	/**
	 * 进入跨服赛
	 */
	public static function enterWorldWar() 
	{
		// 检查是否可以进入
		if (!WorldwarUtil::canEnter())
		{
			return 'err';
		}
		// 设置ID, 需要加上偏移量
		RPCContext::getInstance()->setSession('global.arenaId', WorldwarDef::WORLD_WAR_OFF_SET);
		// 返回
		return 'ok';
	}


	/**
	 * 离开跨服赛
	 */
	public static function leaveWorldWar()
	{
		// 离开赛场，不再发送消息
		RPCContext::getInstance()->unsetSession('global.arenaId');
		// 返回
		return 'ok';
	}


	/**
	 * 获取用户的跨服战信息
	 */
	public static function getUserWorldWarInfo()
	{
		// 获取用户的跨服战信息
		$userInfo = MyWorldwar::getInstance()->getUserWorldWarInfo();
		// 删除掉前端不用的字段
		unset($userInfo['va_world_war']);
		// 前端需要现在是第几届 —— 人家就是不要自己算嘛~~~ 而且就算你查不到人家也要嘛~~~~~
		$userInfo['session'] = WorldwarUtil::getSession(TRUE);
		// 返回
		return $userInfo;
	}


	/**
	 * 更新战斗信息
	 * 
	 * 本方法只适用于用户本身所在的服务器
	 */
	public static function updateFormation()
	{
		/**************************************************************************************************************
     	 * 可换阵时间检查
     	 **************************************************************************************************************/
		// 获取现在是第几届跨服战 —— 用来查询策划的配置表
		$setting = WorldwarUtil::getSetting();
		// 如果现在根本就没有配置跨服赛，则直接返回
		$session = $setting['session'];
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return 'err';
		}
		// 获取当前时刻
		$curTime = Util::getTime();
		// 如果现在根本根本没有比赛，那么就直接返回
		$round = $setting['round'];
		if ($round == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return 'err';
		}
		// 报名时间的可换阵阶段是报名开始时间到下一个阶段开始前的限制时间
		else if($round == WorldwarDef::SIGNUP || $round == WorldwarDef::WORLD_REST)
		{
			// 先获取海选报名开始时间或者是休息开始时间
			$canUpBeginTime = btstore_get()->WORLDWAR[$session]['time'][$round]['start'];
			// 获取海选开始时间，再用这个时间减去限制时间
			$canUpEndTime = btstore_get()->WORLDWAR[$session]['time'][$round + 1]['start'] - 
							btstore_get()->WORLDWAR[$session]['cd_time'][$round]['limit'];
			// 如果当前时刻不在时间范围内，则不可以进行换阵操作 (NOTICE:我发现这个 if 的前半部分是没用的，不过姑且就留着吧)
			if($canUpBeginTime >= $curTime || $curTime >= $canUpEndTime)
			{
				Logger::debug('Can not updateFormation, not in time. round is %d.', $round);
				return 'err';
			}
		}
		// 服内海选时，到海选结束，需要对每一轮比赛进行检查
		else if($round == WorldwarDef::GROUP_AUDITION &&
				btstore_get()->WORLDWAR[$session]['time'][$round]['end'] > $curTime &&
				!self::__isUpFormationTime($session, 
										   $round, 
										   $curTime,
										   btstore_get()->WORLDWAR[$session]['audition_time'], 
										   btstore_get()->WORLDWAR[$session]['group_fight_time'], 
										   btstore_get()->WORLDWAR[$session]['cd_time'][$round]['limit']))
		{
			return 'err';
		}
		// 跨服海选，到海选结束，也需要对每一轮比赛进行检查
		else if($round == WorldwarDef::WORLD_AUDITION &&
				btstore_get()->WORLDWAR[$session]['time'][$round]['end'] > $curTime &&
				!self::__isUpFormationTime($session, 
										   $round, 
										   $curTime,
										   btstore_get()->WORLDWAR[$session]['advanced_time'], 
										   btstore_get()->WORLDWAR[$session]['worldwar_fight_time'],
										   btstore_get()->WORLDWAR[$session]['cd_time'][$round]['limit']))
		{
			return 'err';
		}
		
		// 其他时间段暂时没有要求

		/**************************************************************************************************************
     	 * 进行其他的换阵型检查
     	 **************************************************************************************************************/
		// 获取用户的跨服战信息
		$userInfo = MyWorldwar::getInstance()->getUserWorldWarInfo();
		// 是否被淘汰, 淘汰了的话, 不能更新
		if(self::__isUserEliminated($session, $round, $userInfo))
		{
			Logger::debug('The user has been eliminated.');
			return 'eliminated';
		}
		// 看看是否冷却
		if ($curTime < $userInfo['update_fmt_time'] + 
					   btstore_get()->WORLDWAR[$session]['cd_time'][$round]['cool'])
		{
			Logger::warning('updateFormation not cd yet.');
			return 'err';
		}

		/**************************************************************************************************************
     	 * 通过检查，进行阵型信息保存
     	 **************************************************************************************************************/
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 获取用户的战斗信息参数
		$battlePara = WorldwarUtil::getUserForBattle($user);
		// 保存用户的战斗信息
		MyWorldwar::getInstance()->updateBattleInfo($battlePara);
		return 'ok';
	}


	/**
	 * 使用金币清除更新CD时间 —— 只有在晋级赛可以秒CD
	 * 
	 * @return int								实际花费金币
	 * @return err:string						清除CD时间失败
	 */
	public static function clearUpdFmtCdByGold()
	{
		// 获取现在是第几届跨服战 —— 用来查询策划的配置表
		$setting = WorldwarUtil::getSetting();
		$session = $setting['session'];
		// 如果现在根本就没有配置跨服赛，则直接返回
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return 'err';
		}
		// 当前的比赛详细阶段
		$round = $setting['round'];
		// 报名阶段和海选阶段不允许进行秒CD
		if($round == WorldwarDef::SIGNUP || 
		   $round == WorldwarDef::GROUP_AUDITION || $round == WorldwarDef::WORLD_AUDITION)
		{
			Logger::warning("Can not clear cd when not finals.");
			throw new Exception('fake');
		}
		// 需要多少个金币
		$num = intval(btstore_get()->WORLDWAR[$session]['clear_cd_cost']);
		// 如果不需要清除CD时刻，那么就直接返回
		if ($num <= 0)
		{
			return 0;
		}

		// 获取用户信息
		$user = EnUser::getUserObj();
		// 因为要扣钱，所以记录下日志，省的你不认账
		Logger::trace('The gold of cur user is %s, need gold is %s.', $user->getGold(), $num);
		if ($num > $user->getGold())
		{
			// 钱不够，直接返回
			return 'err';
		}
		// 扣钱
		$user->subGold($num);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_WORLDWAR_CLEARCDTIME, $num, Util::getTime());
		// 清空CD时刻
		MyWorldwar::getInstance()->resetCdTime();
		// 返回给前端，用来矫正
		return $num;
	}


	/**
	 * 海选报名， 用户在报名的时候会保存战斗信息
	 * 之后也可以自己选择随时保存战斗信息。所以在真正战斗的时候，不再处理战斗信息为空的情况。
	 * 
	 * 本方法只适用于用户本身所在的服务器
	 */
	public static function signUp()
	{
		// 获取现在是第几届跨服战 —— 用来查询策划的配置表
		$setting = WorldwarUtil::getSetting();
		$session = $setting['session'];
		// 如果现在根本就没有配置跨服赛，则直接返回
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return 'err';
		}
		// 需开服小于等于策划配置的天数（配置）才可开启服内争霸赛
		if(WorldwarUtil::getServerOpenTime() > btstore_get()->WORLDWAR[$session]['activity_basetime'])
		{
			Logger::warning('Failed to hold the groupwar， server open time is not enough.'.
							'serverOpenTime = %s. activiTyBaseTime = %s', 
							WorldwarUtil::getServerOpenTime(), btstore_get()->WORLDWAR[$session]['activity_basetime']);
			return 'err';
		}
		// 需要检查是否在报名时间段内
		$round = $setting['round'];
		if ($round != WorldwarDef::SIGNUP)
		{
			Logger::warning('Now is not signUp time.');
			return 'err';
		}
		if(Util::getTime() < btstore_get()->WORLDWAR[$session]['time'][$round]['start'] || 
		   Util::getTime() > btstore_get()->WORLDWAR[$session]['time'][$round]['end'])
		{
			Logger::debug('The signUp time is over.');
			return 'err';
		}
		// 查看用户是否已经报过名了
		$userWorldwarInfo = MyWorldwar::getInstance()->getUserWorldWarInfo();
		// 如果报名时间是这届开始以后的时间，就不行了
		if ($userWorldwarInfo['sign_time'] >= btstore_get()->WORLDWAR[$session]['time'][$round]['start'])
		{
			Logger::debug('Already signed.');
			return 'err';
		}

		// 获取用户信息
		$user = EnUser::getUserObj();
		// 等级大于等于策划配置的等级（配置），报名的玩家都能参赛
		if($user->getLevel() < btstore_get()->WORLDWAR[$session]['need_level'])
		{
			Logger::warning('The level of user is not enough.');
			return 'err';
		}
		// 获取用户的战斗信息参数
		$battlePara = WorldwarUtil::getUserForBattle($user);
		// 保存用户的战斗信息
		MyWorldwar::getInstance()->updateBattleInfo($battlePara, false);
		// 报名并保存到数据库
		MyWorldwar::getInstance()->signUp();

		return 'ok';
	}


	/**
     * 查看战绩(获取自己跨服或者海选的所有战斗信息)
	 * 
	 * 本方法只适用于用户本身所在的服务器
     */
	public static function getHistoryFightInfo()
	{
		// 获取用户的战报信息
		$userInfo = MyWorldwar::getInstance()->getUserWorldWarInfo();
		// 返回
		return $userInfo['va_world_war']['replay'];
	}


	/**
	 * 获取助威历史
	 */
	public static function getHistoryCheerInfo()
	{
		// 获取用户的助威信息
		$userInfo = MyWorldwar::getInstance()->getUserWorldWarInfo();
		// 返回
		return $userInfo['va_world_war']['cheer'];
	}


	/**
	 * 获取跨服战的最新信息
	 * 
	 */
	public static function getWorldWarInfo()
	{
		// 获取当前的届数
		$setting = WorldwarUtil::getSetting(TRUE, TRUE);
		$session = $setting['session'];
		// 如果现在根本就没有配置跨服赛，则直接返回
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return 'err';
		}
    	// 判断当前是在服内赛阶段还是跨服赛阶段，两个阶段拉取数据的地方不一样
    	$now = $setting['now'];
		// 如果是服内赛，就简单了
		if ($now == WorldwarDef::TYPE_GROUP)
		{
			// 服内赛不需要拉取别人的数据
			$teamID = 0;
			$db = 0;
		}
		// 其他情况都需要拉取跨服赛数据
		else 
		{
			// 获取服务器ID
			$serverID = WorldwarUtil::getServerId();
			// 获取用户所在的大区ID
			$teamID = WorldwarUtil::getTeamIDByServerID($serverID, $session);
			// 获取跨服的DB
			$db = WorldwarConfig::KFZ_DB_NAME;
		}
		// 不使用日期或者轮次当做参数，一次性全拉去出来，然后再手动遍历出最大的
		$arrRet = WorldwarDao::getWorldWarInfo($db, $teamID, 0, 0, $session);
		// 设置一个最小的回合数
		$round = 0;
		// 遍历并查询出最大的
		foreach ($arrRet as $info)
		{
			// 如果这个回合比设置的大，就记录下
			if ($round <= $info['round'])
			{
				// 因为胜者组和负者组的round是相等的，所以使用这一个变量变可以取出两条最大的数据
				$ret[$info['team']] = $info;
				$round = $info['round'];
			}
		}
		// 返回, 如果没拉取出合适数据的话，就返回空，否则返回数据库数据
		return $round == 0 ? array() : $ret;
	}


    /**
     * 助威
	 * 
	 * 本方法只适用于用户本身所在的服务器
     * 
	 * @param $objUid:int						助威对象的ID
	 * @param $objUname:string					助威对象的名字
	 * @param $type:int							伟大航路 (1)  新世界(2)
	 * @param $serverId:int						助威对象的服务器ID (服内助威的时候传0即可)
     */
	public static function cheer($objUid, $objUname, $type, $serverId = 0)
	{
		$uid = RPCContext::getInstance()->getUid();
		if(empty($objUid))
		{
			return 'err';
		}
		// 获取现在是第几届跨服战
		$setting = WorldwarUtil::getSetting();
		$session = $setting['session'];
		// 如果现在根本就没有配置跨服赛，则直接返回
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return 'err';
		}
		// 判断当前是在服内赛阶段还是跨服赛阶段，两个阶段拉取数据的地方不一样
    	$now = $setting['now'];
    	// 如果没有赛事，则直接返回
    	if ($now == WorldwarDef::OUT_RANGE)
    	{
    		Logger::warning("Need not to draw block, out range now.");
    		return 'err';
    	}

		// 当前的比赛详细阶段
		$round = $setting['round'];
		// 晋级赛才可以助威 
		if((WorldwarDef::GROUP_ADVANCED_32 >= $round && $round >= WorldwarDef::GROUP_ADVANCED_2) ||  
		   (WorldwarDef::WORLD_ADVANCED_32 >= $round && $round >= WorldwarDef::WORLD_ADVANCED_2))
		{
			Logger::warning('Now round can not cheer.');
			return 'err';
		}
		// 当前阶段的结束时间
		$nowRoundEndTime = btstore_get()->WORLDWAR[$session]['time'][$round]['end'];
		// 下一阶段的开始时间
		$nextRoundStart = btstore_get()->WORLDWAR[$session]['time'][$round + 1]['start'];
		
		// 助威时间限制
		$limitTime = btstore_get()->WORLDWAR[$session]['cheer_limit_time'];
		// 看看助威时间过没
		if(Util::getTime() <= $nowRoundEndTime || 
		   Util::getTime() + $limitTime >= $nextRoundStart)
		{
			Logger::warning('Cheer time is over, can not cheer.');
			return 'err';
		}

		// 看看他助威过没
		// 当前阶段的结束时间
		$nowRoundStartTime = btstore_get()->WORLDWAR[$session]['time'][$round]['start'];
		$userWorldwarInfo = MyWorldwar::getInstance()->getUserWorldWarInfo();
		if($nowRoundStartTime < $userWorldwarInfo['cheer_time'])
		{
			Logger::warning('The uid %d had cheered yet.', $userWorldwarInfo['uid']);
			return 'err';
		}

		// 看看他助威的人是否是轮空
		$vaHeroInfo = self::__getWorldwarInfoByServerid($session, $round, $now, $serverId, $type);
		if(empty($vaHeroInfo[0]))
		{
			Logger::warning('The round %s info is not exist.', $round);
			return 'err';
		}
		$vaHeroInfo = $vaHeroInfo[0];
		// 获取step值, 来判断需要跨越多少个人获取一对对手
		if($round == WorldwarDef::GROUP_AUDITION)
		{
			$index = WorldwarDef::GROUP_ADVANCED_32;
		}
		else if($round == WorldwarDef::WORLD_AUDITION)
		{
			$index = WorldwarDef::WORLD_ADVANCED_32;
		}
		else 
		{
			$index = $round;
		}
    	$step = WorldwarDef::$step[$index];
    	// 获取当前阶段的用户应该有的最大排名信息
    	$rank = WorldwarDef::$round_rank[$index];
    	for ($i = 0; $i < WorldwarDef::MAX_JOIN_NUM; $i += $step)
    	{
    		// 看看他是否已经进入32强
			for ($j = $i; $j < $i + $step; ++$j)
    		{
    			if($now == WorldwarDef::TYPE_GROUP)
				{
					if(!empty($vaHeroInfo['va_world_war'][$j]['uid']) && 
						$uid == $vaHeroInfo['va_world_war'][$j]['uid'])
					{
						Logger::warning("User already in 32 finals.");
						throw new Exception('fake');
					}
					// 如果是服内的时候，需要保存一下serverID
					if (!empty($vaHeroInfo['va_world_war'][$j]['uid']) && 
						$objUid == $vaHeroInfo['va_world_war'][$j]['uid'])
					{
						$serverId = $vaHeroInfo['va_world_war'][$j]['server_id'];
					}
				}
				else if($now == WorldwarDef::TYPE_WORLD && !empty($serverId))
				{
					if(!empty($vaHeroInfo['va_world_war'][$j]['uid']) && 
						$uid.WorldwarUtil::getServerId() == $vaHeroInfo['va_world_war'][$j]['uid'].$serverId)
					{
						Logger::warning("User already in 32 finals.");
						throw new Exception('fake');
					}
				}
    		}
    		
    		// 获取两个战斗的人
			$tmp = WorldwarUtil::getEnemy($vaHeroInfo['va_world_war'], $i, $step, $rank, WorldwarDef::$next_rank[$rank], $now);
			if($now == WorldwarDef::TYPE_GROUP)
			{
				// 服内， 看看他的对手是不是轮空了
				if((!empty($tmp[0]['uid']) && empty($tmp[1]['uid']) && $objUid == $tmp[0]['uid']) || 
				   (!empty($tmp[1]['uid']) && empty($tmp[0]['uid']) && $objUid == $tmp[1]['uid']))
				{
					Logger::warning("Bye obj can not cheer.");
					throw new Exception('fake');
				}
			}
			else if($now == WorldwarDef::TYPE_WORLD && !empty($serverId))
			{
				// 跨服， 看看他的对手是不是轮空了
				if((!empty($tmp[0]['uid']) && empty($tmp[1]['uid']) && 
						$objUid.$serverId == $tmp[0]['uid'].$tmp[0]['server_id']) || 
				   (!empty($tmp[1]['uid']) && empty($tmp[0]['uid']) && 
				   		$objUid.$serverId == $tmp[1]['uid'].$tmp[0]['server_id']))
				{
					Logger::warning("Bye obj can not cheer.");
					throw new Exception('fake');
				}
			}
			else 
			{
				Logger::warning('Find cheer obj error.');
				throw new Exception('fake');
			}
    	}

    	Logger::debug('You can cheer.');
		// 扣费用
		$cheerCostBelly = btstore_get()->WORLDWAR[$session]['cheer_belly'];
		$user = EnUser::getUserObj();
		if(!$user->subBelly($cheerCostBelly))
		{
			Logger::warning('The belly value %s is error.', $cheerCostBelly);
			return 'err';
		}
		$user->update();

		// 助威
		MyWorldwar::getInstance()->updateCheerInfo($objUid, $objUname, $round, $type, $serverId);
		return 'ok';
	}


	/**
	 * 开始一轮淘汰赛
	 * 
	 * 本方法适用于所有服务器
	 */
	public static function startFinals()
	{
		// 获取现在是第几届跨服战
		$setting = WorldwarUtil::getSetting();
		// 如果现在根本就没有配置跨服赛，则直接返回
		$session = $setting['session'];
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return ;
		}
    	// 判断当前是在服内赛阶段还是跨服赛阶段，两个阶段拉取数据的地方不一样
    	$now = $setting['now'];
    	// 判断当前阶段，如果没有比赛，则直接退出即可
    	if ($now == WorldwarDef::OUT_RANGE)
    	{
			Logger::warning('Neither group nor world.');
    		return ;
    	}
    	// 获取今天的轮次 
    	$curRound = $setting['round'];
    	// 判断别轮空
    	if ($curRound == WorldwarDef::OUT_RANGE)
    	{
			Logger::warning('Round err, 0.');
    		return ;
    	}
		// 根据今天的轮次获取昨天进行的轮次
		$yesterdayRound = $curRound - 1;
		// 获取今天的年月日
		$ymd = WorldwarUtil::getCurYmd();

		// 查询表，看看都需要进行哪些胜者组的淘汰赛， 如果是跨服赛阶段，这个返回值是多个组
		$arrWins = WorldwarDao::getWorldWarInfo(0, 0, 0, $yesterdayRound, $session, WorldwarDef::TEAM_WIN);
		// 胜者组完了是负者组
		$arrLoses = WorldwarDao::getWorldWarInfo(0, 0, 0, $yesterdayRound, $session, WorldwarDef::TEAM_LOSE);
		// 创建两个tmp数组，用来缓存数据，这样就不需要反复查询数据库
		$tmpArrWins = $arrWins;
		$tmpArrLose = $arrLoses;

		// 执行五轮就足够了
		for ($i = 0; $i < WorldwarConfig::FINALS_GAME_TIMES; ++$i)
		{
			// 记录这一轮的开始时间
			$startTime = time();

			// 更新最新数据
			$arrWins = $tmpArrWins;
			// 清空缓存区
			$tmpArrWins = array();

			// 对查询出来的胜者组结果，执行一轮淘汰赛
			foreach ($arrWins as $winTeam)
			{
				// 执行一轮淘汰赛
				$arrHero = self::__doOnceFinals($winTeam['va_world_war'], $setting, WorldwarDef::TEAM_WIN);
		    	// 将辛苦获取的结果入库
		    	WorldwarDao::updWorldWar($now, array('date_ymd' => $ymd, 
				                               		 'team_id' => $winTeam['team_id'], 
				                               		 'session' => $session,
		    										 'round' => $curRound,
		    										 'team' => WorldwarDef::TEAM_WIN,
		    										 'va_world_war' => $arrHero));
		    	$arrHero['round'] = $i;
		    	$arrHero['team'] = WorldwarDef::TEAM_WIN;
		    	$arrHero['now'] = $setting['round'];
		    	// 推送数据
		    	self::__sendWorldWarMsg($arrHero, $winTeam['team_id'], $session, $curRound);
		    	// 保存缓存
		    	$tmpArrWins[] = array('va_world_war' => $arrHero, 'team_id' => $winTeam['team_id']);
			}

			// 更新最新数据
			$arrLoses = $tmpArrLose;
			// 清空缓存区
			$tmpArrLose = array();

			// 对查询出来的负者组结果，执行一轮淘汰赛
			foreach ($arrLoses as $loseTeam)
			{
				// 执行一轮淘汰赛
				$arrHero = self::__doOnceFinals($loseTeam['va_world_war'], $setting, WorldwarDef::TEAM_LOSE);
		    	// 入库
		    	WorldwarDao::updWorldWar($now, array('date_ymd' => $ymd, 
				                               		 'team_id' => $loseTeam['team_id'], 
				                               		 'session' => $session,
		    										 'round' => $curRound,
		    										 'team' => WorldwarDef::TEAM_LOSE,
		    										 'va_world_war' => $arrHero));
		    	$arrHero['round'] = $i;
		    	$arrHero['team'] = WorldwarDef::TEAM_LOSE;
		    	$arrHero['now'] = $setting['round'];
		    	// 推送数据
		    	self::__sendWorldWarMsg($arrHero, $loseTeam['team_id'], $session, $curRound);
		    	// 保存缓存
		    	$tmpArrLose[] = array('va_world_war' => $arrHero, 'team_id' => $loseTeam['team_id']);
			}
			// 计算实际执行时间
			$needTime = time() - $startTime;
			Logger::info("startFinals once need %d time.", $needTime);
			Logger::debug("sleep time is %s.", btstore_get()->WORLDWAR[$session]['advanced_time']);
			// 等三十分钟 —— 需要减去实际消耗时间
			sleep(btstore_get()->WORLDWAR[$session]['advanced_time'] - $needTime);
		}
		// 潇洒的结束
    	return ;
	}


    /**
     * 开始海选战斗
	 * 
	 * 本方法适用于所有服务器
     */
	public static function startOpenAudition()
	{
		/**************************************************************************************************************
     	 * 海选赛前检查
     	 **************************************************************************************************************/
		// 获取现在是第几届跨服战
		$setting = WorldwarUtil::getSetting();
		$session = $setting['session'];
		// 如果现在根本就没有配置跨服赛，则直接返回
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return 'err';
		}
    	// 判断当前是在服内赛阶段还是跨服赛阶段，两个阶段拉取数据的地方不一样
    	$now = $setting['now'];
    	// 判断当前阶段，如果没有比赛，则直接退出即可
    	if ($now == WorldwarDef::OUT_RANGE)
    	{
			Logger::warning('Neither group nor world.');
    		return ;
    	}

		/**************************************************************************************************************
     	 * 进行胜者组海选比赛
     	 **************************************************************************************************************/
		Logger::trace("Start once team win audition.");
		// 取得所有报名用户的数据, 并将此数据维护到内存中
		$teamArr = self::__getAllSignUser($now,
										  btstore_get()->WORLDWAR[$session]['time'][WorldwarDef::SIGNUP]['start'],
									  	  btstore_get()->WORLDWAR[$session]['group_fail_num']);
		// 对所有大区依次进行海选
		self::__doOpenAudition($setting, $teamArr, WorldwarDef::TEAM_WIN);

		// 发送海选结束消息
		self::sendAuditiondOverMsg($setting);
		
		/**************************************************************************************************************
     	 * 进行败者组组海选比赛
     	 **************************************************************************************************************/
		Logger::trace("Start once team lose audition.");
		// 取得所有不是胜者组的报名者数据
		$teamArr = self::__getAllSignUser($now,
										  btstore_get()->WORLDWAR[$session]['time'][WorldwarDef::SIGNUP]['start'],
									  	  btstore_get()->WORLDWAR[$session]['group_fail_num'],
									  	  WorldwarDef::TEAM_WIN);
		// 对所有大区依次进行海选
		self::__doOpenAudition($setting, $teamArr, WorldwarDef::TEAM_LOSE);
		
		Logger::trace("Audition over.");
	}


	/**
	 * 从所有服拉取需要参与跨服赛的数据
	 * 
	 * 本方法适用于跨服战服务器 
	 * 可以在服内海选分组之后 到 跨服赛海选之前的任意时间调用
	 * 建议在某日的服内淘汰赛之后就立刻调用，节省时间
	 * 
     * @param int $ymd							需要拉取数据的年月日
     * @param int $session						第几届跨服赛
	 */
	public static function getAllHerosAroundWorld($session)
	{
		// 声明平台接口
		$platform = ApiManager::getApi(true);
		// 拉取所有参赛的大组和组下的所有服务器
		$allServers = $platform->users('getServerGroupAll', array('pid' => 1, 
																  'spanid' => $session,
																  'action' => 'getServerGroupAll'));
		// 遍历所有大组
		foreach ($allServers as $teamID => $servers)
		{
			// 记录一共有多少人参加
			$count = 0;
			// 记录和上次的DB是否一样，用来处理合服
			$tmpDB = '';
			// 一个大组应该有20组左右的服务器
			foreach ($servers as $serverID => $db)
			{
				// 如果DB和上次一样，那么就直接不拉取了
				if ($tmpDB == $db)
				{
					continue;
				}
				// 否则，保存这次的DB名称
				else 
				{
					$tmpDB = $db;
				}

				try 
				{
					// 查询表，从各个服务器获取他们的胜者组和负者组数据  肯定是跨服阶段，需要往各个库上拉取 usedb
					$arrheros = WorldwarDao::getWorldWarInfo($db, 0, 0, WorldwarDef::GROUP_AUDITION, $session);
				}
				catch (Exception $e)
				{
					Logger::warning("Can not get info from  getWorldWarInfo.");
					continue;
				}

				// 循环所有数据 —— 一个库应该有胜者组和负者组两条数据
				foreach ($arrheros as $heros)
				{
					foreach ($heros['va_world_war'] as $hero)
					{
						// 处理轮空
						if (empty($hero['uid']))
						{
							continue;
						}
						// 将这些数据插入到 t_user_world_sign_up 表里面
						WorldwarDao::insertUserWorldSignUp($teamID, $hero['uid'], 
														   $hero['server_id'], 
														   WorldwarDao::getServerNameByID($hero['server_id']));
						++$count;
					}
				}
			}
			Logger::info("This time %d team all fight user count is %d.", $teamID, $count);
		}
	}


	/**
	 * 获取奖励
	 * 
	 * @param int $type							服内 (1)  跨服(2)
	 */
	public static function getPrize($prizeID)
	{
		// 查看用户的跨服战数据，看看是否可以领奖
		$worldwarInfo = MyWorldwar::getInstance()->getUserWorldWarInfo();
		// 查看是否已经领过了
		if ($prizeID == $worldwarInfo['group_prize_id'] && $worldwarInfo['group_prize_time'] != 0)
		{
			return 'err';
		}
		else if ($prizeID == $worldwarInfo['group_prize_id'] && $worldwarInfo['group_prize_time'] == 0)
		{
			$type = WorldwarDef::TYPE_GROUP;
		}
		// 没领取过的话，获取奖励ID
		else if ($prizeID == $worldwarInfo['world_prize_id'] && $worldwarInfo['world_prize_time'] != 0)
		{
			return 'err';
		}
		else if ($prizeID == $worldwarInfo['world_prize_id'] && $worldwarInfo['world_prize_time'] == 0)
		{
			$type = WorldwarDef::TYPE_WORLD;
		}
		else 
		{
			return 'err';
		}

		if(empty(btstore_get()->WORLDWAR_PRIZE[$prizeID]))
		{
			return 'err';
		}
		// 如果奖励ID不为空，则进行发奖
		$bagInfo = array();
		if (!empty($prizeID))
		{
			$bagInfo = self::__getAllPrize(btstore_get()->WORLDWAR_PRIZE[$prizeID]);
			if ($bagInfo === false)
			{
				return 'err';
			}
		}

		// 领取奖励
		MyWorldwar::getInstance()->getPrize($type);
		return $bagInfo;
	}


	/**
	 * 发送所有助威的奖励
	 */
	public static function sendAllCheerAward()
	{
		Logger::debug('send cheer reward begin.');
		// 获取现在是第几届跨服战
		$setting = WorldwarUtil::getSetting();
		Logger::debug('The setting is %s.', $setting);
		$session = $setting['session'];
		// 如果现在根本就没有配置跨服赛，则直接返回
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return;
		}
		// 看看这个服有这个活动没有
		if(!WorldwarUtil::checkWorldwarIsOpen($setting))
		{
			Logger::debug('There is not activity in the server.');
			return;
		}
		// 获取轮次
		$round = $setting['round'];
		// 报名、海选、跨服海选没有助威
		if($round == WorldwarDef::SIGNUP || $round == WorldwarDef::GROUP_AUDITION || $round == WorldwarDef::WORLD_AUDITION)
		{
			return;
		}
		// 根据服内还是跨服获取奖励ID
		if ($setting['now'] == WorldwarDef::TYPE_GROUP)
		{
			$prizeID = btstore_get()->WORLDWAR[$session]['group_cheer_reward_id'];
		}
		else 
		{
			$prizeID = btstore_get()->WORLDWAR[$session]['world_cheer_reward_id'];
		}

		// 获取进入下一轮的人名单
		$time = rand(1, 60);
		sleep($time);
		$serverId = 0;
		if($setting['now'] == WorldwarDef::TYPE_WORLD)
		{
			$serverId = WorldwarUtil::getServerID();
		}
		$vaHeroInfoAry = self::__getWorldwarInfoByServerid($session, $round, $setting['now'], $serverId);
		if(empty($vaHeroInfoAry))
		{
			Logger::debug('The winner result is empty.');
			return ;
		}

		// 查看谁是晋级者
		foreach ($vaHeroInfoAry as $vaHeroInfo)
		{
			// 取得所有进阶者的UID
			$winnerUserInfo = array();
			// 遍历所有参赛用户
			foreach ($vaHeroInfo['va_world_war'] as $vaHero)
			{
				// 查看名次用于辨别晋级者
				if(!empty($vaHero['rank']) && $vaHero['rank'] == WorldwarDef::$all_rank[$round])
				{
					// 记录晋级者的 uid 和 serverID
					$uidAry[] = $vaHero['uid'];
					$winnerUserInfo[] = $vaHero['uid'].intval($vaHero['server_id']);
				}
			}
			Logger::debug('The winnerUserInfo is %s.', $winnerUserInfo);
			// 错误检查，如果没有的话直接退出
			if(empty($winnerUserInfo))
			{
				Logger::debug('The winnerUserInfo is empty.');
				continue;
			}
			// 获取所有成功助威的人
			$rewardUserInfo = WorldwarDao::getAllCheerUserInfo($uidAry);
			Logger::debug('The rewardUserInfo is %s.', $rewardUserInfo);
			// 看看都谁获得助威奖励
			foreach ($rewardUserInfo as $rewardUser)
			{
				// 看看上面那些助威的人，谁助威到了晋级选手
				foreach ($winnerUserInfo as $info)
				{
					// 如果正好助威到了晋级选手，那么就进行发奖动作
					if($rewardUser['cheer_uid'].intval($rewardUser['cheer_uid_server_id']) == $info &&
						$rewardUser['cheer_time'] < btstore_get()->WORLDWAR[$session]['time'][$round]['start'])
					{
						self::__executeCheerAward($rewardUser['uid'], $prizeID);
						Logger::info('send allcheer reward, recieverUid is %s.', $rewardUser['uid']);
						break;
					}
				}
			}
		}
		// 初始化助威信息
		WorldwarDao::initCheerInfo(btstore_get()->WORLDWAR[$session]['time'][$round]['start']);
		Logger::info('send cheer reward success');
	}


	/**
	 * 发放所有奖励
	 */
	public static function sendFightAward($session, $now, $round, $machine = 0)
	{
		if(empty($session) || empty($now) || empty($round)){
			// 获取现在是第几届跨服战
			$setting = WorldwarUtil::getSetting();
			$session = $setting['session'];
			// 如果现在根本就没有配置跨服赛，则直接返回
			if ($session == WorldwarDef::OUT_RANGE)
			{
				Logger::warning('Now has no world war.');
				return ;
			}
			// 先设定一个默认参数
			$round = WorldwarDef::WORLD_ADVANCED_2;
			// 判断当前是在服内赛阶段还是跨服赛阶段，两个阶段拉取数据的地方不一样
	    	$now = $setting['now'];
	    	// 如果没有赛事，则直接返回
	    	if ($now == WorldwarDef::OUT_RANGE)
	    	{
		    	// 获取现在的轮次
				$round = $setting['round'];
				// 如果是服内的，就发服内的
				if ($round < WorldwarDef::WORLD_AUDITION)
				{
					$now = WorldwarDef::TYPE_GROUP;
					$round = WorldwarDef::GROUP_ADVANCED_2;
				}
	    		// 不是发服内奖的时候，就是发跨服奖
				else 
				{
		    		$now = WorldwarDef::TYPE_WORLD;
				}
	    	}
	    	// 如果是服内的，就发服内的
	    	else if ($now == WorldwarDef::TYPE_GROUP)
	    	{
				$round = WorldwarDef::GROUP_ADVANCED_2;
	    	}
		}
		// 看看这个服有这个活动没有
		if($machine == 0 && !WorldwarUtil::checkWorldwarIsOpen($setting))
		{
			Logger::debug('There is not activity in the server.');
			return;
		}
		// 取得所有获奖的记录(所有大组)
		$vaHeroInfoAry = self::__getWorldwarInfoByServerid($session, $round, $now, 0);
		foreach ($vaHeroInfoAry as $vaHeroInfo)
		{
			if($vaHeroInfo['team'] == WorldwarDef::TEAM_WIN)
			{
				// 胜者组奖励
				$reward = btstore_get()->WORLDWAR[$session]['reward'][$now]['newworld'];
			}
			else if($vaHeroInfo['team'] == WorldwarDef::TEAM_LOSE)
			{
				// 败者组奖励
				$reward = btstore_get()->WORLDWAR[$session]['reward'][$now]['greatland'];
			}
			else 
			{
				Logger::warning('The worldwar record is wrong. YMD = %s, TEAMID = %s, TEAM = %s',
								$vaHeroInfo['date_ymd'], $vaHeroInfo['team_id'], $vaHeroInfo['team']);
				return;
			}
			foreach ($vaHeroInfo['va_world_war'] as $vaHero)
			{
				try {
					// 更新用户排名奖励
					if(!empty($vaHero['uid']))
					{
						Logger::debug('The fighter is %s, rank is %s, serverId is %s. now is %s.', 
									  $vaHero['uid'],	$vaHero['rank'], $vaHero['server_id'], $now);
						// 发放奖励
						self::__saveReward($vaHero['uid'], $reward[$vaHero['rank']], $vaHero['server_id'], $now);
						// 发放阳光普照奖
						if ($vaHero['rank'] == WorldwarDef::RANK_1 && $now == WorldwarDef::TYPE_WORLD &&
							$vaHeroInfo['team'] == WorldwarDef::TEAM_WIN)
						{
							self::__sendAllWorldPrize($session, $vaHero['server_id']);
						}
					}
				}
				catch (Exception $e)
				{
					// 防止发奖励出错
					Logger::warning('sendFightAward:%s', $e->getMessage());
				}
			}
		}
	}


	/**
	 * 获取膜拜神殿信息
	 */
	public static function getTempleInfo()
	{
		// 获取当前时刻
		$curTime = Util::getTime();
		// 第一届结束之前不允许调用
		if ($curTime < btstore_get()->WORLDWAR[1]['time'][WorldwarDef::WORLD_ADVANCED_2]['end'])
		{
			Logger::warning('no permission.');
			return array();
		}
		// 获取当前是第几届跨服战
		$oldSession  = WorldwarUtil::getSession(TRUE);
		$newSession  = WorldwarUtil::getSession(FALSE);
		// 获取神庙信息
		$lostTemple = WorldwarDao::getTempleInfo();
		// 查看是否需要更新数据了 (本届已经完成，并且可以拉取到上届数据)
		if (empty($lostTemple[0]) || 
			($oldSession != $lostTemple[0]['session'] && 
			 $newSession == WorldwarDef::OUT_RANGE && $oldSession != WorldwarDef::OUT_RANGE))
		{
			// 获取服务器ID
			$serverID = WorldwarUtil::getServerId();
			// 获取用户所在的大区ID
			$teamID = WorldwarUtil::getTeamIDByServerID($serverID, $oldSession);
			// 拉取最新最后一条数据
			$arrheros = WorldwarDao::getWorldWarInfo(WorldwarConfig::KFZ_DB_NAME, $teamID, 0, 
													 WorldwarDef::WORLD_ADVANCED_2, $oldSession, WorldwarDef::TEAM_WIN);
			// 防止胜者组没有人
			if (!empty($arrheros[0]))
			{
				// 找出前两名
				foreach ($arrheros[0]['va_world_war'] as $hero)
				{
					// 如果是前两名，就入库
					if (!empty($hero['rank']) && $hero['rank'] < WorldwarDef::RANK_3)
					{
						// 设置更新参数
						$set = array('session' => $oldSession,
									 'rank' => $hero['rank'], 
									 'uid' => $hero['uid'], 
									 'uname' => $hero['uname'], 
									 'server_id' => $hero['server_id'], 
									 'server_name' => WorldwarDao::getServerNameByID($hero['server_id']), 
									 'htid' => $hero['htid'], 
									 'msg' => '');
						// 保存返回值
						$lostTemple[$hero['rank']] = $set;
						// 更新到数据库
						WorldwarDao::updTempleInfo($set);
					}
				}
			}
			// 获取第三名
			// 拉取最新最后一条数据
			$arrheros = WorldwarDao::getWorldWarInfo(WorldwarConfig::KFZ_DB_NAME, $teamID, 0, 
													 WorldwarDef::WORLD_ADVANCED_2, $oldSession, WorldwarDef::TEAM_LOSE);
			// 防止败者组没有人
			if (!empty($arrheros[0]))
			{
				// 找出前三名
				foreach ($arrheros[0]['va_world_war'] as $hero)
				{
					// 如果是前三名，就入库
					if (!empty($hero['rank']) && $hero['rank'] == WorldwarDef::RANK_1)
					{
						// 设置更新参数
						$set = array('session' => $oldSession,
									 'rank' => WorldwarDef::RANK_3, 
									 'uid' => $hero['uid'], 
									 'uname' => $hero['uname'], 
									 'server_id' => $hero['server_id'], 
									 'server_name' => WorldwarDao::getServerNameByID($hero['server_id']), 
									 'htid' => $hero['htid'], 
									 'msg' => '');
						// 保存返回值
						$lostTemple[WorldwarDef::RANK_3] = $set;
						// 更新到数据库
						WorldwarDao::updTempleInfo($set);
					}
				}
			}
		}
		return $lostTemple;
	}


	/**
	 * 获取最近膜拜的人物
	 */
	public static function getWorshipUsers()
	{
		// 获取最新的列表信息
		return WorldwarDao::getWorshipUserInfo();
	}


	/**
	 * 膜拜
	 * 
	 * @param $type      						膜拜种类
	 */
	public static function worship($type)
	{
		// 获取当前时刻
		$curTime = Util::getTime();
		// 第一届结束之前不允许调用
		if ($curTime < btstore_get()->WORLDWAR[1]['time'][WorldwarDef::WORLD_ADVANCED_2]['end'])
		{
			Logger::warning('no permission.');
			return array();
		}

		// 查表, 检查参数
		if (empty(btstore_get()->WORSHIP[$type]))
		{
			Logger::warning("Error type, %d.", $type);
        	throw new Exception('fake');
		}
		// 获取现在是第几届跨服战
		$session = WorldwarUtil::getSession(TRUE);

		// 获取用户跨服战信息
		$worldwarInfo = MyWorldwar::getInstance()->getUserWorldWarInfo();
		// 获取当前是第几届跨服战
		$oldSession  = WorldwarUtil::getSession(TRUE);
		$newSession  = WorldwarUtil::getSession(FALSE);
		// 查看获取的两次是否相等
		if ($oldSession == $newSession)
		{
			// 表明正处于一次跨服赛内, 那么获取这一届的截止时间
			$endTime = btstore_get()->WORLDWAR[$oldSession]['time'][WorldwarDef::WORLD_ADVANCED_2]['end'];
			// 如果是第一届，那么判断起来就简单了
			if ($oldSession == 1 && $worldwarInfo['worship_time'] >= $endTime)
			{
				Logger::warning("Already worship.");
	        	throw new Exception('fake');
			}
			// 如果不是第一届，那么需要获取上一届的结束时间
			else if ($oldSession != 1)
			{
				$endTime = btstore_get()->WORLDWAR[$oldSession - 1]['time'][WorldwarDef::WORLD_ADVANCED_2]['end'];
				// 如果膜拜时间已经膜拜过，还是不行
				if ($worldwarInfo['worship_time'] >= $endTime)
				{
					Logger::warning("Already worship.");
		        	throw new Exception('fake');
				}
			}
		}
		else 
		{
			// 不是同一届的话，取上一届的时间就可以
			if ($worldwarInfo['worship_time'] >= 
				btstore_get()->WORLDWAR[$oldSession]['time'][WorldwarDef::WORLD_ADVANCED_2]['end'])
			{
				Logger::warning("Already worship.");
	        	throw new Exception('fake');
			}
		}
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 检查用户数据是否足够
		if ($user->getBelly() < btstore_get()->WORSHIP[$type]['need_belly'] ||
			$user->getGold() < btstore_get()->WORSHIP[$type]['need_gold'])
		{
			Logger::warning("Not enough money, user is belly: %d, gold: %d.",
							$user->getBelly(), $user->getGold());
        	throw new Exception('fake');
		}

		// 领奖
		$bagInfo = self::__getAllPrize(btstore_get()->WORSHIP[$type]);
		if ($bagInfo === false)
		{
			return 'err';
		}

		// 扣除膜拜所需的金币和游戏币
		$user->subBelly(btstore_get()->WORSHIP[$type]['need_belly']);
		$user->subGold(btstore_get()->WORSHIP[$type]['need_gold']);
		$user->update();
		// 记录膜拜信息
		MyWorldwar::getInstance()->worship();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_WORLDWAR_WORSHIP, 
						 btstore_get()->WORSHIP[$type]['need_gold'], Util::getTime());

		// 更新最新膜拜信息
		self::__checkSaveWorshipList($user->getUid(), $user->getUname(), $user->getLevel(), $type);
		// 返回
		return $bagInfo;
	}


	/**
	 * 留言
	 * 
	 * @param $msg      						留言
	 */
	public static function leaveMsg($msg)
	{
		/**************************************************************************************************************
     	 * 留言检查
     	 **************************************************************************************************************/
		// 获取uid 
		$uid = RPCContext::getInstance()->getUid();
		// 获取神庙信息
		$templeUser = WorldwarDao::getTempleInfo($uid);
		// 获取服务器ID
		$serverID = WorldwarUtil::getServerId();
		// 如果根本没取出来uid用户，则直接报错
		if (empty($templeUser[0]) || $templeUser[0]['server_id'] != $serverID)
		{
			Logger::warning("Can not send msg when not a god.");
        	throw new Exception('fake');
		}
		// 检查留言，包括长度和过滤OOXX词
		$ret = TrieFilter::search($msg);
		if (!empty($ret) || strlen($msg) >= 128 || strlen($msg) == 0)
		{
			Logger::warning("OOXX word.");
			return 'err';
		}
		// 检查通过，请改留言吧，大爷
		$templeUser[0]['msg'] = $msg;

		/**************************************************************************************************************
     	 * 需要更新到所有同组的服务器上
     	 **************************************************************************************************************/
		// 获取用户所在的大区ID
		$teamID = WorldwarUtil::getTeamIDByServerID($serverID, $templeUser[0]['session']);
		// 声明平台接口
		$platform = ApiManager::getApi(true);
		// 拉取所有参赛的大组和组下的所有服务器
		$serverIDs = $platform->users('getServerGroupBySpanid', array('pid' => 1, 
														  			  'team' => $teamID,
														  			  'spanid' => $templeUser[0]['session'],
														  			  'action' => 'getServerGroupBySpanid'));
		Logger::debug("The leaveMsg allserverID is %s.", $serverIDs);
		// 根据teamID 获取这个team里面所有的服务器ID 对所有服务器发送消息
		foreach ($serverIDs[$teamID] as $serverId => $db)
		{
			try {
				// 更新数据库
				WorldwarDao::updateTempleInfo($templeUser[0], $db);
			}
			catch (Exception $e)
			{
				// 防止更新出错
				Logger::warning("Can not save msg.");
			}
		}

		// 返回
		return 'ok';
	}


	/******************************************************************************************************************
     * 以下内容供内部调用
     ******************************************************************************************************************/
	/**
	 * 执行海选
	 * 
     * @param array $setting	
     * <code>				
     * 		int:session							现在是第几届跨服赛
     * 		int:now								跨服赛阶段
	 *		int:round							现在是第几轮
	 * </code>
	 * @param array $teamArr					参赛者数组
	 * @param int $team							胜者组海选，还是负者组海选
	 */
	public static function __doOpenAudition($setting, $teamArr, $team)
	{
		// 标志全部是否完成
		$count = array();
		// 进行循环比赛
		while (1)
		{
			// 记录这一轮的开始时间
			$startTime = time();
			// 对所有大区依次进行海选
			foreach ($teamArr as $teamID => $heroArr)
			{
				// 进行一次海选比赛
				$ret = self::__doOnceAudition($setting,
											  $heroArr, 
											  $team,
											  btstore_get()->WORLDWAR[$setting['session']]['group_fail_num']);
				// 设置返回值
				$teamArr[$teamID] = $ret['heros'];
				// 查看比赛结果, 如果已经决出32强则结束比赛
				if ($ret['ret'] === 'end')
				{
					if (!isset($count[$teamID]))
					{
						// 进行入库操作
						self::__drawBlock($setting, $team, $teamID, $ret['heros']);
						Logger::info("openAudition team %d done.", $teamID);
					}
					$count[$teamID] = 1;
				}
			}

			if  (count($count) >= count($teamArr))
			{
				// 退出循环，完成海选
				Logger::info("openAudition all team done.");
				break;
			}
			
			Logger::debug("Team %d open audition over.", $teamID);
			// 计算实际执行时间
			$needTime = time() - $startTime;
			Logger::info("openAudition once need %d time.", $needTime);
			Logger::debug("sleep time is %s.", btstore_get()->WORLDWAR[$setting['session']]['audition_time']);

			// 等10分钟 —— 需要减去实际消耗时间
			sleep(btstore_get()->WORLDWAR[$setting['session']]['audition_time'] - $needTime);
		}
	}


	/**
	 * 执行一轮海选
	 * 
	 * 本方法适用于所有服务器
	 * 
     * @param array $setting	
     * <code>				
     * 		int:session							现在是第几届跨服赛
     * 		int:now								跨服赛阶段
	 *		int:round							现在是第几轮
	 * </code>
	 * @param array $heroArr					参赛者数组
	 * @param int $team							胜者组海选，还是负者组海选
	 * @param int $maxLoseTime					最大可失败次数
	 */
	private static function __doOnceAudition($setting, $heroArr, $team, $maxLoseTime)
    {
    	// 统计一下现在有多少人想要进行海选
    	$userNum = count($heroArr);
    	// 如果参数为空，则直接返回
    	if ($userNum == 0)
    	{
    		Logger::debug("__doOnceAudition empty heroArr.");
    		return array('ret' => 'end', 'heros' => $heroArr);
    	}
    	// 从第一个人开始打，一直打到最后算是一轮
    	$curIndex = 0;
    	// 获取所有参赛队员的「serverID|uid」列表, 其实是遍历这个列表，进行比赛
    	$arrUids = array_keys($heroArr);
  	  	// 乱序
		shuffle($arrUids);
		Logger::debug("__doOnceAudition after shuffle is %s.", $arrUids);

    	// 如果剩下的用户不到32位，则继续执行 2v2 比赛
    	while ($userNum > WorldwarDef::MAX_JOIN_NUM)
    	{
    		// 查看二个人是否存在，不存在就表示结束了, 全部列表遍历完毕
    		if (empty($arrUids[$curIndex]) || empty($arrUids[$curIndex + 1]))
    		{
    			return array('ret' => 'ok', 'heros' => $heroArr);
    		}

			// 获取两个对手的信息
    		$curUser = $heroArr[$arrUids[$curIndex]];
    		$objUser = $heroArr[$arrUids[$curIndex + 1]];
    		Logger::debug('The fighter one information is %s.', $curUser);
    		Logger::debug('The fighter two information is %s.', $objUser);
    		// 获取两人的战斗信息, 如果战斗信息不存在, 则直接到数据库里面拉取
			$curUser['va_world_war']['fight_para'] = 
						WorldwarDao::getUserFightPara($curUser['uid'], $curUser['uid_server_id'], $setting['now']);
			$objUser['va_world_war']['fight_para'] = 
						WorldwarDao::getUserFightPara($objUser['uid'], $objUser['uid_server_id'], $setting['now']);

			// 开始战斗
			$battleRet = WorldwarUtil::doFight($setting['now'],
											   $curUser['va_world_war']['fight_para'], 
											   $objUser['va_world_war']['fight_para']);
			Logger::debug('The battle result is %s.', $battleRet);
			// 获取失败者所在数组中的下标 
			if ($battleRet['loser']['uid'] == $curUser['uid'])
			{
				$index = $arrUids[$curIndex];
			}
			else 
			{
				$index = $arrUids[$curIndex + 1];
			}
			// 增加一个key，告诉前端这个战报是什么时候的
			$battleRet['round'] = $setting['now'] == WorldwarDef::TYPE_GROUP ? 
													 WorldwarDef::GROUP_AUDITION : WorldwarDef::WORLD_AUDITION;
			$battleRet['team'] = $team;
			// 更新战斗结果信息
			self::__saveReplay($curUser, $objUser, $battleRet, $setting);
			// 失败者次数加1
			self::__addLoseTimes($setting['now'], $heroArr[$index]['uid'], $heroArr[$index]['uid_server_id'], $team);
			// 查看执行完这一轮之后是否可以截止了
    		if(++$heroArr[$index]['lose_times'] >= $maxLoseTime)
			{
				// 删掉此人， 用来查看是否已经算出了32个人
				--$userNum;
				unset($heroArr[$index]);
			}
			// 计数
			$curIndex += 2;
    	}
    	// 用户数小于32, 更新并结束
    	if($userNum <= WorldwarDef::MAX_JOIN_NUM)
    	{
    		// 更新所有人的组别数据
    		self::__modifyTeam($setting['now'], $heroArr, $team);
    	}
    	// 如果正常决出32个人，也可以结束一轮战斗了
    	return array('ret' => 'end', 'heros' => $heroArr);
    }


    /**
     * 执行一轮淘汰赛
	 * 
	 * 本方法适用于所有服务器
     * 
	 * @param array $heroArr					参赛者数组
	 * @param array $setting					当前是第几轮比赛, 跨服 or 服内, 当前是第几界
	 * @param int   $team						胜者组 or 败者组
	 * 
	 * @return array 							比赛后的参赛者数组结果 (其实只是修改了rank 和 记录了战报)，可以直接插入数据库
     */
    private static function __doOnceFinals($arrHero, $setting, $team)
    {
    	// 获取step值, 来判断需要跨越多少个人获取一对对手
    	$step = WorldwarDef::$step[$setting['round']];
    	// 获取当前阶段的用户应该有的最大排名信息
    	$rank = WorldwarDef::$round_rank[$setting['round']];
    	// 循环进行比赛 
    	for ($i = 0; $i < WorldwarDef::MAX_JOIN_NUM; $i += $step)
    	{
			// 获取两个战斗的人
			$tmp = WorldwarUtil::getEnemy($arrHero, $i, $step, $rank, WorldwarDef::$next_rank[$rank], $setting['now']);
			// 如果两个人都轮空了，则什么都不做了，浪费感情
			if (empty($tmp[0]['uid']) && empty($tmp[1]['uid']))
			{
				continue;
			}
    		// 如果已经决出来了，就不需要再执行下面的语句了
			if ((!empty($tmp[0]['rank']) && $tmp[0]['rank'] == WorldwarDef::$next_rank[$rank]) || 
				(!empty($tmp[1]['rank']) && $tmp[1]['rank'] == WorldwarDef::$next_rank[$rank]))
			{
				continue;
			}
			// 如果两方都是轮空，则进行下一轮
			if (empty($tmp[0]['va_world_war']['fight_para']['uid']) && 
				empty($tmp[1]['va_world_war']['fight_para']['uid']))
			{
				Logger::debug('Can not fight two empty fighters.');
				continue;
			}
			// 如果有一方轮空，也不需要进行比赛
			if (empty($tmp[0]['va_world_war']['fight_para']['uid']) && 
			   !empty($tmp[1]['va_world_war']['fight_para']['uid']))
			{
				$arrHero[$tmp[1]['index']]['rank'] = WorldwarDef::$next_rank[$rank];
				continue;
			}
			else if (!empty($tmp[0]['va_world_war']['fight_para']['uid']) && 
			   		  empty($tmp[1]['va_world_war']['fight_para']['uid']))
			{
				$arrHero[$tmp[0]['index']]['rank'] = WorldwarDef::$next_rank[$rank];
				continue;  	
			}

			// 开始战斗
			$battleRet = WorldwarUtil::doFight($setting['now'],
											   $tmp[0]['va_world_war']['fight_para'],
											   $tmp[1]['va_world_war']['fight_para']);
			// 异常处理
			if (empty($battleRet))
			{
				Logger::warning("doFight returns null, jzc pls check it out!");
				continue;
			}
			Logger::debug('BattleRet：%s', $battleRet);
			// 从返回值里面获取胜负者
			if (!empty($tmp[0]['uid']) && $battleRet['winer']['uid'] == $tmp[0]['uid'] && 
				$battleRet['winer']['server_id'] == $tmp[0]['va_world_war']['fight_para']['server_id'])
			{
				$winUserIndex = $tmp[0]['index'];
				$loseUserIndex = $tmp[1]['index'];
			}
			// 好吧，出现其他情况就是闹鬼了
			else if (!empty($tmp[1]['uid']) && $battleRet['winer']['uid'] == $tmp[1]['uid'] && 
					 $battleRet['winer']['server_id'] == $tmp[1]['va_world_war']['fight_para']['server_id'])
			{
				$winUserIndex = $tmp[1]['index'];
				$loseUserIndex = $tmp[0]['index'];
			}
			else 
			{
				Logger::warning("I found a ghost here.");
	        	throw new Exception('fake');
			}
			// 如果真的进行过战斗，则需要记录战报 
			if (!empty($battleRet['replay']))
			{
				// 增加一个key，告诉前端这个战报是什么时候的
				$battleRet['round'] = $setting['round'];
				$battleRet['team'] = $team;
				try 
				{
					// 更新战斗结果信息
					self::__saveReplay($tmp[0], $tmp[1], $battleRet, $setting);
				}
				catch (Exception $e)
				{
					// 防止发战报出错
					Logger::warning("Can not save replay.");
				}

				// 两者战斗，规定负的人负责记录战报信息
				if($setting['now'] == WorldwarDef::TYPE_WORLD)
				{
					$battleRet['replay'] = RecordType::KFZ_PREFIX.$battleRet['replay'];
				}
				$arrHero[$loseUserIndex]['replay'][$battleRet['replay']] = $battleRet;
			}
			Logger::debug('The winner index is %s, loser index is %s.', $winUserIndex, $loseUserIndex);
			// 记录失败次数
			if (++$arrHero[$loseUserIndex]['lose_times'] >= WorldwarConfig::FINALS_LOSE_TIMES)
			{
				// 修改胜利者的排名信息
				$arrHero[$winUserIndex]['rank'] = WorldwarDef::$next_rank[$rank];
				$arrHero[$winUserIndex]['lose_times'] = 0;
			}
    	}
		Logger::debug("__doOnceFinals ret is %s.", $arrHero);
    	// 返回
    	return $arrHero;
    }


    /**
     * 在当前服务器查看用户是否已经被淘汰了
     * 
     * @param int $session						现在是第几届
     * @param int $round						现在是第几轮
     * @param array $userInfo					用户的跨服赛信息
     */
    private static function __isUserEliminated($session, $round, $userWorldwarInfo)
    {
    	// 获取用户uid和serverID
    	$uid = $userWorldwarInfo['uid'];
    	$serverID = $userWorldwarInfo['va_world_war']['fight_para']['server_id'];
    	Logger::debug('The isEliminated va_world_war serverid is %s.', $serverID);
    	// 为跨服赛做个准备
    	$worldwarInfo = array();
    	// 查看当前回合, 如果是海选阶段，则非常简单，直接查看即可
    	if ($round <= WorldwarDef::GROUP_AUDITION)
    	{
    		// 判断用户的失败次数
    		if ($userWorldwarInfo['lose_team_lose_times'] < btstore_get()->WORLDWAR[$session]['group_fail_num'])
    		{
    			return false;
    		} 
    	}
    	// 如果是跨服海选阶段
    	else if ($round == WorldwarDef::WORLD_REST || $round == WorldwarDef::WORLD_AUDITION)
    	{
			// 获取用户的战斗信息
    		$userInfo = WorldwarDao::getUserWorldSignUpByID($uid, $serverID);
    		// 如果丫根本都没晋级，则直接滚蛋
    		if (empty($userInfo))
    		{
    			return true;
    		}
    		// 判断用户的失败次数
    		if ($userInfo['lose_team_lose_times'] < btstore_get()->WORLDWAR[$session]['wrold_fail_num'])
    		{
    			return false;
    		}
    	}
    	// 如果是服内淘汰赛阶段
    	else if ($round >= WorldwarDef::GROUP_ADVANCED_32 && $round <= WorldwarDef::GROUP_ADVANCED_2)
    	{
    		// 获取淘汰赛数据
    		$worldwarInfo = WorldwarDao::getWorldWarInfo(0, 0, 0, $round, $session);
    	}
    	// 如果是跨服淘汰赛阶段
    	else if ($round >= WorldwarDef::WORLD_ADVANCED_32 && $round <= WorldwarDef::WORLD_ADVANCED_2)
    	{
    		// 获取淘汰赛数据
    		$worldwarInfo = WorldwarDao::getWorldWarInfo(WorldwarConfig::KFZ_DB_NAME, 0, 0, $round, $session);
    	}
    	// 循环，里面应该有胜者组和败者组，当然，如果不是跨服赛的话，这里应该是是0
    	foreach ($worldwarInfo as $worldwar)
    	{
	    	// 获取当前阶段的用户应该有的最大排名信息
	    	$rank = WorldwarDef::$round_rank[$round];
	    	// 查看参赛的所有用户
	    	Logger::debug('The isEliminated va_world_war is %s.', $worldwar);
	    	foreach ($worldwar['va_world_war'] as $hero)
	    	{
	    		if(empty($hero['server_id']))
	    		{
	    			continue;
	    		}
    			Logger::debug('The isEliminated serverid is %s %s %s.', $rank, $hero['server_id'], $serverID);
	    		// 如果确切查出来这个人还在的话，就表明没有淘汰呢
	    		if ($hero['rank'] <= $rank && $hero['uid'] == $uid && $hero['server_id'] == $serverID)
	    		{
	    			return false;
	    		}
	    	}
    	}
    	// 其他情况直接返回失败，啥也不让干了
    	return true;
    }


    /**
     * 更新组别
     * 
     * @param int $now							现在是服内赛还是跨服赛阶段
	 * @param array $heroArr					参赛者结果数组
	 * @param int $team							组别信息，胜者组还是败者组
     */
    private static function __modifyTeam($now, $heroArr, $team)
    {
    	// 遍历所有的用户，更新其组别信息
    	foreach ($heroArr as $value)
    	{
    		self::__saveUserTeam($now, $value['uid'], $value['uid_server_id'], $team);
    	}
    }


    /**
     * 更新用户失败次数
     * 
     * @param int $now							现在是服内赛还是跨服赛阶段
     * @param int $uid							用户ID
     * @param int $serverID						用户所在服务器ID
     * @param int $team							胜者组比赛 or 败者组比赛
     */
    private static function __addLoseTimes($now, $uid, $serverID, $team)
    {
    	Logger::debug('The loser is %s.', $uid);
    	self::__saveLoseTimes($now, $uid, $serverID, $team);
    }


    /**
     * 分组，并插入数据
	 * 
	 * 本方法适用于所有服务器
     * 
     * @param array $setting	
     * <code>				
     * 		int:session							现在是第几届跨服赛
     * 		int:now								跨服赛阶段
	 *		int:round							现在是第几轮
	 * </code>
	 * @param int $team							组别信息，胜者组还是败者组
	 * @param int $teamID						大组ID
	 * @param array $arrHeros					参赛者数组
     */
    private static function __drawBlock($setting, $team, $teamID, $arrHeros)
    {
    	// 获取参赛人的数量
    	$num = count($arrHeros);
    	// 判断是否需要入库的人为空，如果空的话直接返回
    	if ($num == 0)
    	{
    		return ;
    	}
    	Logger::debug("__drawBlock para arrHeros is %s.", $arrHeros);
    	// 获取所有参赛队员的「serverID|uid」列表, 其实是遍历这个列表，进行比赛
    	$arrUids = array_keys($arrHeros);
		// 可能会不足32个，需要补足32个
		if ($num < WorldwarDef::MAX_JOIN_NUM)
		{
			$arrUids = array_merge($arrUids, array_fill($num, WorldwarDef::MAX_JOIN_NUM - $num, "0|0"));
		}
		// 乱序
		shuffle($arrUids);
		Logger::debug("Drawblock after sort is %s.", $arrUids);
		// 声明一个返回值
		$heros = array();
		// 添加一个key
		foreach ($arrUids as $index => $uid_serverID)
		{
			// 记录位置信息
			$heros[$index]['index'] = $index;
			$heros[$index]['rank'] = WorldwarDef::RANK_32;
			// 查看数组该项是否为空
			if (!empty($arrHeros[$uid_serverID]['uid']))
			{
				// 先设置一些基础的值
				$heros[$index]['uid'] = $arrHeros[$uid_serverID]['uid'];
				$heros[$index]['server_id'] = 0;
				$heros[$index]['lose_times'] = 0;
				// 跨服阶段， 需要额外记录一些信息，用来跨表查询
				if ($setting['now'] == WorldwarDef::TYPE_WORLD)
				{
					// 跨服的时候，需要额外记录用户的服务器ID和服务器名
					$heros[$index]['server_id'] = $arrHeros[$uid_serverID]['uid_server_id'];
					$heros[$index]['server_name'] = $arrHeros[$uid_serverID]['uid_server_name'];
				}
				else if ($setting['now'] == WorldwarDef::TYPE_GROUP)
				{
					// 这个地方设置为0, 用来往下面的函数中传递参数，其实下面也是用不到的
					$heros[$index]['server_id'] = 0;
				}

				// 需要获取前端需要展示的信息
				$fightPara = WorldwarDao::getUserFightPara($heros[$index]['uid'], $heros[$index]['server_id'], $setting['now']);
				$heros[$index]['uname'] = $fightPara['name'];
				$heros[$index]['htid'] = $fightPara['htid'];
				$heros[$index]['level'] = $fightPara['level'];
				// 在这里获取真正的id 
				$heros[$index]['server_id'] = $fightPara['server_id'];
			}
		}
    	// 如果是服内赛阶段，则直接插入表，否则需要跨表强行插入
    	WorldwarDao::updWorldWar($setting['now'], array('date_ymd' => WorldwarUtil::getCurYmd(), 
		                         'team_id' => $teamID, 
		                         'session' => $setting['session'],
    							 'round' => $setting['round'],
    							 'team' => $team,
    							 'va_world_war' => $heros));
    	// 返回
    	return ;
    }


    /**
     * 保存战报， 跨服赛阶段，需要放入到两个人的库里面
     * 
     * @param array $curUser					战斗者A
     * @param array $objUuser					战斗者B
     * @param array $replay						战报详情
     * @param int $session						当前是第几届
     */
    private static function __saveReplay($curUser, $objUuser, $replay, $setting)
    {
		// 判断当前是在服内赛阶段还是跨服赛阶段，两个阶段拉取数据的地方不一样, 如果没有赛事，则直接返回
    	if ($setting['now'] == WorldwarDef::OUT_RANGE)
    	{
    		Logger::warning("Need not to saveReplay, out range now.");
    		return ;
    	}
    	// 分两种情况
    	// 海选的时候，两个参数都不为空，需要保存到两个用户的 t_user_world_war va 字段里面
    	// 海选的时候，前两个参数里面有用的key是uid
    	$round = $setting['round'];
    	// 组内海选、组内晋级
    	if(WorldwarDef::GROUP_AUDITION <= $round && $round <= WorldwarDef::GROUP_ADVANCED_2)
    	{   
    		self::__saveBattleReplay($curUser['uid'], $replay);
			self::__saveBattleReplay($objUuser['uid'], $replay);
    	}
		// 跨服海选、跨服晋级
    	else if(WorldwarDef::WORLD_AUDITION <= $round && $round <= WorldwarDef::WORLD_ADVANCED_2)
		{
			try {
				// 获取serverID 
				$serverID = intval($curUser['va_world_war']['fight_para']['server_id']);
				// 特殊情况需要进行处理
				if ($serverID < 100)
				{
					$serverID = sprintf ("%03.0f", $serverID);
				}
				// 对战报进行特殊处理
				$replay['replay'] = RecordType::KFZ_PREFIX.$replay['replay'];

				// 发送消息
				$lcProxyCur = new ServerProxy();
				$lcProxyCur->init('game'.$serverID, Util::genLogId());
				$lcProxyCur->asyncExecuteRequest($curUser['uid'], 
											     'worldwar.__saveBattleReplay', array($curUser['uid'], $replay));
				
				// 获取serverID 
				$serverID = intval($objUuser['va_world_war']['fight_para']['server_id']);
				// 特殊情况需要进行处理
				if ($serverID < 100)
				{
					$serverID = sprintf ("%03.0f", $serverID);
				}
				$lcProxyObj = new ServerProxy();
				$lcProxyObj->init('game'.$serverID, Util::genLogId());
				$lcProxyObj->asyncExecuteRequest($objUuser['uid'], 
											     'worldwar.__saveBattleReplay', array($objUuser['uid'], $replay));
			}
			catch (Exception $e)
			{
				// 防止保存战报出错
				Logger::warning('__saveReplay:%s', $e->getMessage());
			}
		}
		return;
    }


    /**
     * 发放战斗奖励
     * 
     * @param int $uid							用户ID
     * @param int $reward						奖励ID
     * @param int $serverId						服务器ID
     * @param int $now							当前比赛阶段
     */
    private static function __saveReward($uid, $reward, $serverID, $now)
    {
    	// 如果奖励是空的, 则什么也不做
    	if(!empty($reward))
		{
			// 服内
			if($now == WorldwarDef::TYPE_GROUP)
			{
				self::__saveBattleReward($uid, $reward, $now);
			}
			else if($now == WorldwarDef::TYPE_WORLD)
			{
				// 获取serverID 
				$serverID = intval($serverID);
				// 特殊情况需要进行处理
				if ($serverID < 100)
				{
					$serverID = sprintf ("%03.0f", $serverID);
				}

				$lcProxyObj = new ServerProxy();
				$lcProxyObj->init('game'.$serverID, Util::genLogId());
				$lcProxyObj->asyncExecuteRequest($uid, 
											 	'worldwar.__saveBattleReward', array($uid, $reward, $now));
			}
		}
    }


	/**
	 * 领取奖励
	 * 
	 * @param array $prizes						策划们配置的奖励
	 * @throws Exception
	 */
	private static function __getAllPrize($prizes)
	{
		Logger::debug('The prize info is %s.', $prizes);
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 声明一个空数组
		$itemTemplateAry = array();

		// 获取用户背包信息
		$bag = BagManager::getInstance()->getBag();
		// 如果有物品，那么才传过去，不然就是空数组
		if (!empty($prizes['items']))
		{
			Logger::debug('The prize item is %s.', $prizes['items']);
			// 生成物品
			$items = array();
			for ($i = 0; $i < count($prizes['items']); ++$i)
			{
				// 合并所有产生的物品
				$items = array_merge($items,
									 ItemManager::getInstance()->addItem($prizes['items'][$i][0], 
																		 $prizes['items'][$i][1]));
				$itemTemplateAry[$prizes['items'][$i][0]] = $prizes['items'][$i][1];
				Logger::debug('The prize item id is %s.', $itemTemplateAry);
			}
			// 记录发送的信息
			$msg = chatTemplate::prepareItem($items);
			// 直接增加到背包里，不使用临时背包
			if ($bag->addItems($items, FALSE) == FALSE)
			{
				Logger::warning('Bag full.');
				return false;
			}
			// 发送信息
			chatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
		}
		// 增加蓝魂
		if (!empty($prizes['soul']))
		{
			SoulObj::getInstance()->addBlue($prizes['soul']);
			SoulObj::getInstance()->save();
		}

		// 获取用户等级
		$lv = $user->getLevel();
		// 增加游戏币
		$user->addBelly($prizes['belly'] * $lv);
		// 增加阅历
		$user->addExperience($prizes['experience'] * $lv);
		// 增加行动力
		$user->addExecution($prizes['execution']);
		// 增加声望
		$user->addPrestige($prizes['prestige']);
		// 增加荣誉
		if (!empty($prizes['honourpoint']))
		{
			EnHonourShop::addHonourPoint($user->getUid(), $prizes['honourpoint']);
		}
		// 如果有金币需要发金币
		if (!empty($prizes['gold']))
		{
			$user->addGold($prizes['gold']);
		}

		// 更新数据库
		$user->update();
		$bagInfo = $bag->update();

		// 发送战斗奖励邮件
		if (!empty($prizes['rank']))
		{
			self::sendWorldwarPrizeMail($prizes['rank'], $user->getUid(), 
										$prizes['belly'] * $lv, 
										$prizes['gold'], 
										$prizes['experience'] * $lv, 
										$prizes['prestige'], 
										$prizes['soul'], 
										$prizes['honourpoint'], 
										$itemTemplateAry);
		}
		// 返回背包信息
		return $bagInfo;
	}


	/**
	 * 在淘汰赛阶段, 查询现在是否可以更新阵型
	 * 
	 * @param int $session						当前是第几届跨服战
	 * @param int $round						当前是第几个阶段
	 * @param int $curTime						当前时刻
	 * @param int $roundTime					一回合需要多长时间
	 * @param int $fightTime					计算战斗需要的时间
	 * @param int $limitTime					这个回合开始之前多长时间不允许进行操作
	 */
	private static function __isUpFormationTime($session, $round, $curTime, $roundTime, $fightTime, $limitTime)
	{
		// 查看已经打了几回合
		$fightRound = intval(($curTime - 
							  btstore_get()->WORLDWAR[$session]['time'][$round]['start']) / $roundTime);
		// 可以更新的开始时间 = 当前阶段的开始时间 + 打了几回的时间 + 战斗计算时间 
		$canUpBeginTime = btstore_get()->WORLDWAR[$session]['time'][$round]['start'] + 
						  $fightRound * $roundTime + 
						  $fightTime;

		//  可以更新的结束时间 = 当前阶段的开始时间 + (打了几回合+1)的时间 - 更新限制时间
		$canUpEndTime = btstore_get()->WORLDWAR[$session]['time'][$round]['start'] + 
						($fightRound + 1) * $roundTime - 
						$limitTime;

		// 看看更新时间
		if($canUpBeginTime >= $curTime || $curTime >= $canUpEndTime)
		{
			Logger::debug('can not updateFormation, not in time, round is %d.', $round);
			return false;
		}
		return true;
	}


    /**
     * 推送一轮决赛信息
     * 
     * @param int $msg							需要推送给前端的决赛信息
     * @param int $teamId						分组ID
     * @param int $session						第几届
     * @param int $round						第几轮
     */
    public static function __sendWorldWarMsg($msg, $teamId, $session, $round)
    {
    	Logger::debug("__sendWorldWarMsg para is %s, %d, %d", $msg, $teamId, $session);

		try {
	    	// 如果信息是空的, 则什么也不做
	    	if(!empty($msg))
			{
				// 减少一些不需要传输的数据，用来减少传输压力
				foreach ($msg as $key => $hero)
				{
					if (isset($hero['rank']) && $hero['rank'] > WorldwarDef::$round_rank[$round])
					{
						unset($msg[$key]);
					}
				}
				// 服内
				if ($teamId == 0)
				{
					// 声明平台接口
					$lcProxyObj = new ServerProxy();
					// 广播给前端并返回
					$lcProxyObj->sendFilterMessage('arena', WorldwarDef::WORLD_WAR_OFF_SET, 
												   array ('callback' => array ('callbackName' => 're.worldwar.sendWorldWarResult' ), 
												   							   'err' => 'ok',
																			   'ret' => $msg));
				}
				// 如果是跨服的话，需要转到各个服务器上，采用上面的方法推送到前端去
				else 
				{
					// 声明平台接口
					$platform = ApiManager::getApi(true);
					// 拉取所有参赛的大组和组下的所有服务器
					$serverIDs = $platform->users('getServerGroupBySpanid', array('pid' => 1, 
																	  			  'team' => $teamId,
																	  			  'spanid' => $session,
																	  			  'action' => 'getServerGroupBySpanid'));
	
					Logger::debug("__sendWorldWarMsg allserverID is %s.", $serverIDs);
					// 根据teamID 获取这个team里面所有的服务器ID 对所有服务器发送消息
					foreach ($serverIDs[$teamId] as $serverId => $db)
					{
						try {
							// 获取serverID 
							$serverId = intval($serverId);
							// 特殊情况需要进行处理
							if ($serverId < 100)
							{
								$serverId = sprintf ("%03.0f", $serverId);
							}

							$lcProxyObj = new ServerProxy();
							$lcProxyObj->init('game'.$serverId, Util::genLogId());
							$lcProxyObj->asyncExecuteRequest(4, 
														 	'worldwar.__sendWorldWarMsg', array($msg, 0, $session, $round));
						}
						catch (Exception $e)
						{
							// 防止发消息出错
							Logger::warning('__sendWorldWarMsg foreach :%s', $e->getMessage());
						}
					}
				}
			}
		}
		catch (Exception $e)
		{
			// 防止发邮件出错
			Logger::warning('__sendWorldWarMsg:%s', $e->getMessage());
		}
    }


	/**
	 * 获取所有报名用户
	 * 
	 * 本方法适用于所有服务器
	 * NOTICE： 获取当前服务器数据时，用户有战斗信息，跨服战的时候，获取的数据是没有战斗信息的
	 * 
     * @param int $now							现在是服内赛还是跨服赛阶段
     * @param int $startTime					本次的报名开始时间
     * @param int $failTime						策划允许的最大失败次数
     * @param int $team							胜者组还是败者组
	 */
    private static function __getAllSignUser($now, $startTime, $failTime, $team = 0)
    {
    	// 声明返回值
    	$tmp = array();
    	// 如果是服内赛阶段，则直接进表拉取
    	if ($now == WorldwarDef::TYPE_GROUP)
    	{
			// 取得报名用户的数据
			$heroArr = WorldwarDao::getSignUpUserInfo($startTime, $failTime, $team);
			// 为了做法一致，我这里采取了非常蛋疼的方法，我要把每个key变成「serverID|uid」这样的格式
			foreach ($heroArr as $hero)
			{
				// 因为服内赛大家的serverID都一样，所以设置为0即可
				$hero['uid_server_id'] = WorldwarUtil::getServerId();
				$tmp[$hero['uid_server_id']."|".$hero['uid']] = $hero;
			}
			Logger::debug("__getAllSignUser return is %s.", $tmp);
			// 返回 —— 因为服内赛没有分组，所以设置teamID为0即可。擦擦擦，真蛋疼
			return array(0 => $tmp);
    	}
    	// 如果是跨服战，则需要进行跨表拉取
    	else if ($now == WorldwarDef::TYPE_WORLD)
    	{
    		// 获取所有报名的人
    		$heroArr = WorldwarDao::getUserWorldSignUp($startTime, $failTime, $team);
			// 修改key
			foreach ($heroArr as $hero)
			{
				// 因为服内赛大家的serverID都一样，所以设置为0即可
				$tmp[$hero['team_id']][$hero['uid_server_id']."|".$hero['uid']] = $hero;
			}
			Logger::debug("__getAllSignUser return is %s.", $tmp);
			// 返回一个2层数组，第一层的key是分组ID，第二层是「serverID|uid」
			return $tmp;
    	}
    	// 如果当前不属于任何阶段，则返回空即可
   		return array();
    }

	/**
	 * 通过server id获取该服务器对应进阶记录
	 * 
	 * 本方法适用于所有服务器
	 * 
	 * 
	 * @param int $session						第几届跨服赛
	 * @param int $round						当前比赛是第几回合
	 * @param int $now							当前比赛阶段
	 * @param int $serverId						服务器ID
	 * 
	 * @return array $worldwarInfo				当前比赛阶段的记录
	 */
	private static function __getWorldwarInfoByServerid($session, $round, $now, $serverId, $type = 0)
	{
    	// 分组ID
    	$teamID = 0;
    	if(!empty($serverId))
    	{
    		// 分组ID 
    		$teamID = WorldwarUtil::getTeamIDByServerID($serverId, $session);
    		// 如果这个值很奇怪，就直接fake
    		if ($teamID === false)
    		{
				Logger::warning("getTeamIDByServerID ret error, serverid is %d.", $serverId);
				throw new Exception('fake');
    		}
    	}
		Logger::debug('The team id is %s.', $teamID);
    	// 服内
		if($now == WorldwarDef::TYPE_GROUP)
		{
			return WorldwarDao::getWorldWarInfo(0, $teamID, 0, $round, $session, $type);
		}
		// 跨服
		else if($now == WorldwarDef::TYPE_WORLD)
		{
			return WorldwarDao::getWorldWarInfo(WorldwarConfig::KFZ_DB_NAME, $teamID, 0, $round, $session, $type);
		}
		return ;
	}


	/**
	 * 发放奖励(可异步执行)
	 * 
	 * @param int $uid							用户UID
	 * @param int $prizeID						奖励ID
	 */
	public static function __executeCheerAward($uid, $prizeID)
	{
		$setting = WorldwarUtil::getSetting();
		// 获取实际的奖励
		$prizes = btstore_get()->WORLDWAR_PRIZE[$prizeID]->toArray();
		// 查看是否有物品
		$item = array();
		// 如果有物品，那么才传过去，不然就是空数组
		if (!empty($prizes['items']))
		{
			// 生成物品
			for ($i = 0; $i < count($prizes['items']); ++$i)
			{
				// 合并所有产生的物品
				$item = array_merge($item,
									ItemManager::getInstance()->addItem($prizes['items'][$i][0], 
																		$prizes['items'][$i][1]));
			}
			// 更新到数据库
			ItemManager::getInstance()->update();
		}

		// 获取用户信息
		$user = EnUser::getUserObj($uid);
		// 获取用户等级
		$lv = $user->getLevel();
		// 增加游戏币
		$user->addBelly($prizes['belly'] * $lv);
		// 增加阅历
		$user->addExperience($prizes['experience'] * $lv);
		// 增加金币
		$user->addGold($prizes['gold']);
		// 增加声望
		$user->addPrestige($prizes['prestige']);
		// 增加荣誉
		if (!empty($prizes['honourpoint']))
		{
			EnHonourShop::addFinallyHonourPoint($uid, $prizes['honourpoint']);
		}
		// 更新数据库
		$user->update();
		// 手动推送数据
		RPCContext::getInstance()->sendMsg(array($uid), 're.user.updateUser', 
										   array('belly_num' => $user->getBelly(), 
										   		 'experience_num' => $user->getExperience(), 
										   		 'gold_num' => $user->getGold(), 
										   		 'prestige_num' => $user->getPrestige()));
		try
		{
			// 发送奖励邮件 ,有数值才发邮件 
			if (!empty($prizes['belly']) ||
			    !empty($prizes['experience']) ||
			    !empty($prizes['gold']) ||
			    !empty($prizes['prestige']) ||
			    !empty($prizes['honourpoint']) ||
			    !empty($item))
		    {
		    	// 发送助威奖邮件
				self::sendWorldwarCheerMail($setting['now'], 
											$user->getUid(), 
											$prizes['belly'] * $lv, 
											$prizes['gold'], 
											$prizes['experience'] * $lv, 
											$prizes['prestige'], 
											0, 
											$prizes['honourpoint'],
											$item);
		    }
		}
		catch (Exception $e)
		{
			// 防止发邮件出错
			Logger::warning('Mail exeception:%s', $e->getMessage());
		}
	}


	/**
	 * 更新膜拜信息
	 * 
	 * @param int $uid							用户ID
	 * @param string $uname						用户名
	 * @param int $lv							用户等级
	 * @param int $type							膜拜类型
	 */
	private static function __checkSaveWorshipList($uid, $uname, $lv, $type)
	{
		// 获取最新的列表信息
		$worshipUserInfo = WorldwarDao::getWorshipUserInfo();
		// 查询重复，如果来过就无视
		foreach ($worshipUserInfo as $userInfo) 
		{
			// 如果有重复，那么直接返回
			if ($userInfo['uid'] == $uid)
			{
				return ;
			}
		}
		// 如果小于5个
		if (count($worshipUserInfo) < WorldwarConfig::WORSHIP_LIST ||
		    empty($worshipUserInfo))
		{
			WorldwarDao::insertWorshipUserInfo($uid, $uname, $lv, $type);
		}
		// 大于五个了，那么更新最老的
		else 
		{
			// 更新最后一个
			WorldwarDao::updateWorshipUserInfo($worshipUserInfo[WorldwarConfig::WORSHIP_LIST - 1]['uid'], 
											   $uid, $uname, $lv, $type);
		}
	}


	/**
	 * 发放全服奖励
	 * 
	 * @param int $session						第几届跨服赛
	 * @param int $serverID						需要发放的服务器ID
	 */
	private static function __sendAllWorldPrize($session, $serverID)
	{
		// 获取可以领取的开始时刻
		$startTime = btstore_get()->WORLDWAR[$session]['time'][WorldwarDef::WORLD_ADVANCED_2]['end'];
		// 24小时就截止了
		$endTime = $startTime + WorldwarConfig::HOURS_24;
		// 获取全服礼包
		$prizes = btstore_get()->WORLDWAR_PRIZE[btstore_get()->WORLDWAR[$session]['server_reward_id']];
		// 准备一下道具
		$arrItem = array();
		foreach ($prizes['items'] as $item)
		{
			$arrItem[$item[0]] = $item[1];
		}

		// 准备参数
		$_compensate = array('type' => 1,							// 类型，0系统补偿 1跨服战奖励
							 'message' => '',						// 给前端显示的文字
		    				 'belly' => $prizes['belly'],			// 贝里
		    				 'experience' => $prizes['experience'],	// 阅历
		    				 'prestige' => $prizes['prestige'],		// 声望
		    				 'gold' => $prizes['gold'],				// 金币
		    				 'execution' => $prizes['execution'],	// 行动力
		    				 'item_ids' => $arrItem);

		// 获取serverID 
		$serverID = intval($serverID);
		// 特殊情况需要进行处理
		if ($serverID < 100)
		{
			$serverID = sprintf ("%03.0f", $serverID);
		}
		// 声明一个对象
		$proxy = new ServerProxy();
		$proxy->init('game'.$serverID, Util::genLogId());
		//添加补偿信息
		try 
		{
		    $returnInfo = $proxy->addPayBackInfo($startTime, $endTime, $_compensate);
		} catch (Exception $e) 
		{
		    Logger::warning('set PayBack error serverGroup:%s', $serverID);
		    return false;
		}

		//开启该补偿信息
		$compensateInfo = $proxy->getPayBackInfoByTime($startTime, $endTime);
		if(isset($compensateInfo[0]['payback_id']))
		{
		    try
		    {
		        $proxy->openPayBackInfo($compensateInfo[0]['payback_id']);
		    } catch (Exception $e) 
		    {
		        Logger::warning('open PayBack error serverGroup:'. $serverID . 
		        				'payback_id:'. $compensateInfo[0]['payback_id']);
		    	return false;
		    }
		}
		else
		{
		    Logger::warning('not set PayBack serverGroup:%s', $serverID);
		    return false;
		}
		return true;
	}


    /******************************************************************************************************************
     * 以下内容供异步调用
     ******************************************************************************************************************/
    /**
     * 供异步调用 (当然，同步也可以), 更新用户的战斗数据
     * 
	 * 本方法只适用于用户本身所在的服务器
     * 
     * @param int $uid							用户ID
     * @param array $battlePara					战斗模块的参数
     */
	public static function __saveBattlePara($uid, $battlePara)
	{		
		// 判断session的uid，如果是空的，表明这个人已经不在线了，需要手动设置
		if (RPCContext::getInstance()->getUid() == 0)
		{
			RPCContext::getInstance()->setSession('global.uid', $uid);
		}
		// 设置用户的战斗信息
		MyWorldwar::getInstance()->updateBattleInfo($battlePara);
	}


    /**
     * 供异步调用 (当然，同步也可以), 更新战报信息
     * 
     * @param int $now							现在是服内赛还是跨服赛阶段
     * @param int $uid							用户ID
     * @param int $serverID						用户所在服务器ID
     * @param int $team							组别
     */
	public static function __saveUserTeam($now, $uid, $serverID, $team)
	{
	   	// 服内赛的时候和跨服赛的表不是一个
		if ($now == WorldwarDef::TYPE_GROUP)
		{
			RPCContext::getInstance()->setSession('global.uid', $uid);
			// 更新组别信息
			MyWorldwar::getInstance()->saveTeam($team);
		}
		// 跨服赛的时候直接更新数据库
		else 
		{
			WorldwarDao::saveUserWoldTeam($uid, $serverID, $team);
		}
	}


    /**
     * 供异步调用 (当然，同步也可以), 更新用户的失败次数
     * 
     * @param int $now							现在是服内赛还是跨服赛阶段
     * @param int $uid							用户ID
     * @param int $serverID						用户所在服务器ID
     * @param int $team							胜者组比赛 or 败者组比赛
     */
	public static function __saveLoseTimes($now, $uid, $serverID, $team)
	{
		Logger::debug("__saveLoseTimes para is %d, %d, %d.", $now, $uid, $serverID);
	   	// 服内赛的时候和跨服赛的表不是一个
		if ($now == WorldwarDef::TYPE_GROUP)
		{
			// 判断session的uid，如果是空的，表明这个人已经不在线了，需要手动设置
			RPCContext::getInstance()->setSession('global.uid', $uid);
			// 增加一次用户的失败次数
			MyWorldwar::getInstance()->addLoseTime($team);
		}
		// 跨服赛的时候直接更新数据库
		else 
		{
			WorldwarDao::addUserWoldLoseTimes($uid, $serverID, $team);
		}
	}


    /**
     * 供异步调用 (当然，同步也可以), 更新用户的战斗记录
     * 
     * @param int $uid							用户ID
     * @param int $team							组别
     */
	public static function __saveBattleReplay($uid, $replay)
	{
		// 判断session的uid，如果是空的，表明这个人已经不在线了，需要手动设置
		RPCContext::getInstance()->setSession('global.uid', $uid);
		// 增加一次用户的失败次数
		MyWorldwar::getInstance()->updateReplay($replay);
	}


    /**
     * 供异步调用 (当然，同步也可以), 更新用户的奖励记录
     * 
     * @param int $uid							用户ID
     * @param int $reward						奖励组
     * @param int $now							当前所处的阶段
     */
	public static function __saveBattleReward($uid, $reward, $now)
	{
    	// 如果没有赛事，则直接返回
    	if ($now == WorldwarDef::OUT_RANGE)
    	{
    		Logger::warning("Need not to send reward, out range now.");
    		return ;
    	}
    	Logger::debug("Send battle reward %d to user %d. now is %d.", $reward, $uid, $now);
    	
		// 判断session的uid，如果是空的，表明这个人已经不在线了，需要手动设置
		RPCContext::getInstance()->setSession('global.uid', $uid);
		// 发放奖励		
		MyWorldwar::getInstance()->sendGroupReward($reward, $now);
	}


	/**
	 * 每日从杨老师那里拉取一次服务器ID对应的服务器名
	 */
	public static function __getAllServerInfo()
	{
		// 声明平台接口
		$platform = ApiManager::getApi(true);
		// 获取所有服的名字
		$allServers = $platform->users('getNameAll', array('pid' => 1, 'action' => 'getNameAll'));

		// 更新到数据库
		foreach ($allServers as $serverID => $v)
		{
  			WorldwarDao::updServerInfo(intval($serverID), $v[0], $v[1]);
		}
		return $allServers;
	}


	/**
	 * 系统消息
	 * 
	 */
	public static function sendWorldwarMsg()
	{
		$setting = WorldwarUtil::getSetting();
		$session = $setting['session'];
		// 如果现在根本就没有配置跨服赛，则直接返回
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return 'err';
		}
		$round = $setting['round'];
		if ($round == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return 'err';
		}
		// 看看这个服有这个活动没有
		if(!WorldwarUtil::checkWorldwarIsOpen($setting))
		{
    		Logger::debug('There is not activity in the server.');
			return 'err';
		}
		// 取得进入决赛的2个人
		$result = array();
		$arrWins = array();
		$arrLose = array();
		$teamID = WorldwarUtil::getTeamIDByServerID(WorldwarUtil::getServerID(), $session);
		Logger::debug('The team id is %s.', $teamID);
		if ($round == WorldwarDef::GROUP_ADVANCED_4 || $round == WorldwarDef::GROUP_ADVANCED_2)
		{

			$result = WorldwarDao::getWorldWarInfo(0, $teamID, 0, $round, $session);
			if(empty($result))
			{
				Logger::debug('The send msg use info is not exist. round = %s.', $round);
				return;
			}
		}
		else if ($round == WorldwarDef::WORLD_ADVANCED_4 || $round == WorldwarDef::WORLD_ADVANCED_2 )
		{
			$result = WorldwarDao::getWorldWarInfo(WorldwarConfig::KFZ_DB_NAME, $teamID, 0, $round, $session);
			if(empty($result))
			{
				Logger::debug('The send msg use info is not exist. round = %s.', $round);
				return;
			}
		}
		if(!empty($result[0]) && !empty($result[1]))
		{
			if($result[0]['team'] == WorldwarDef::TEAM_WIN)
			{
				$arrWins = $result[0]['va_world_war'];
				$arrLose = $result[1]['va_world_war'];
			}
			else
			{
				$arrWins = $result[1]['va_world_war'];
				$arrLose = $result[0]['va_world_war'];
			}
		}
		else if(!empty($result[0]) && empty($result[1]))
		{			
			if($result[0]['team'] == WorldwarDef::TEAM_WIN)
			{
				$arrWins = $result[0]['va_world_war'];
			}
			else
			{
				$arrLose = $result[0]['va_world_war'];
			}
		}
		else if(empty($result[0]) && !empty($result[1]))
		{
			if($result[1]['team'] == WorldwarDef::TEAM_WIN)
			{
				$arrWins = $result[1]['va_world_war'];
			}
			else
			{
				$arrLose = $result[1]['va_world_war'];
			}
		}

		$winGropuUserInfo = array();
		$loserGropuUserInfo = array();
		if ($round == WorldwarDef::WORLD_ADVANCED_4 || $round == WorldwarDef::GROUP_ADVANCED_4)
		{
			foreach ($arrWins as $hero)
			{
				if (!empty($hero['rank']) && $hero['rank'] == WorldwarDef::$all_rank[$round])
				{
					$winGropuUserInfo[] = empty($hero['uid']) ? 
											array() : 
											array('uid'=>$hero['uid'],
												  'utid'=>$hero['htid'],
												  'uname'=>$hero['uname']);
				}
			}
			
			foreach ($arrLose as $hero)
			{
				if (!empty($hero['rank']) && $hero['rank'] == WorldwarDef::$all_rank[$round])
				{
					$loserGropuUserInfo[] = empty($hero['uid']) ? 
											array() : 
											array('uid'=>$hero['uid'],
												  'utid'=>$hero['htid'],
												  'uname'=>$hero['uname']);
				}
			}
		}
		// 取得冠军赛 胜者和败者
		else if ($round == WorldwarDef::WORLD_ADVANCED_2 || $round == WorldwarDef::GROUP_ADVANCED_2)
		{
			foreach ($arrWins as $hero)
			{
				if (!empty($hero['rank']) && $hero['rank'] == WorldwarDef::$all_rank[$round])
				{
					$winner = empty($hero['uid']) ? 
											array() : 
											array('uid'=>$hero['uid'],
												  'utid'=>$hero['htid'],
												  'uname'=>$hero['uname']);
					$winGropuUserInfo['win'] = $winner;
				}
				if (!empty($hero['rank']) && $hero['rank'] == WorldwarDef::$all_rank[$round - 1])
				{
					$loser = empty($hero['uid']) ? 
											array() : 
											array('uid'=>$hero['uid'],
												  'utid'=>$hero['htid'],
												  'uname'=>$hero['uname']);
					$winGropuUserInfo['lose'] = $loser;
				}
			}
			
			foreach ($arrLose as $hero)
			{
				if (!empty($hero['rank']) && $hero['rank'] == WorldwarDef::$all_rank[$round])
				{
					$winner = empty($hero['uid']) ? 
											array() : 
											array('uid'=>$hero['uid'],
												  'utid'=>$hero['htid'],
												  'uname'=>$hero['uname']);
					$loserGropuUserInfo['win'] = $winner;
				}
				if (!empty($hero['rank']) && $hero['rank'] == WorldwarDef::$all_rank[$round - 1])
				{
					$loser = empty($hero['uid']) ? 
											array() : 
											array('uid'=>$hero['uid'],
												  'utid'=>$hero['htid'],
												  'uname'=>$hero['uname']);
					$loserGropuUserInfo['lose'] = $loser;
				}
			}
		}
		Logger::debug('The winGropuUserInfo is %s.', $winGropuUserInfo);
		Logger::debug('The loserGropuUserInfo is %s.', $loserGropuUserInfo);
		
		switch ($round) {
			// 争霸赛海选结束产生两个组别16强
			case WorldwarDef::GROUP_ADVANCED_32: 
				ChatTemplate::sendGroupWarTop16();
			break;
			// 争霸赛海选结束产生两个组别8强
			case WorldwarDef::GROUP_ADVANCED_16:
				ChatTemplate::sendGroupWarTop8();
			break;
			// 争霸赛海选结束产生两个组别4强
			case WorldwarDef::GROUP_ADVANCED_8:
				ChatTemplate::sendGroupWarTop4();
			break;
			// 争霸赛海选结束产生两个组别2强
			case WorldwarDef::GROUP_ADVANCED_4:
				ChatTemplate::sendGroupWarTop2($winGropuUserInfo, $loserGropuUserInfo);
			break;
			// 争霸赛海选结束产生两个组别冠军
			case WorldwarDef::GROUP_ADVANCED_2:
				ChatTemplate::sendGroupWarFinal($winGropuUserInfo, $loserGropuUserInfo);
			break;
			// 跨服争霸赛海选结束产生两个组别16强
			case WorldwarDef::WORLD_ADVANCED_32:
				ChatTemplate::sendWorldWarTop16();
			break;
			// 跨服争霸赛海选结束产生两个组别8强
			case WorldwarDef::WORLD_ADVANCED_16:
				ChatTemplate::sendWorldWarTop8();
			break;
			// 跨服争霸赛海选结束产生两个组别4强
			case WorldwarDef::WORLD_ADVANCED_8:
				ChatTemplate::sendWorldWarTop4();
			break;
			// 跨服争霸赛海选结束产生两个组别2强
			case WorldwarDef::WORLD_ADVANCED_4:
				ChatTemplate::sendWorldWarTop2($winGropuUserInfo, $loserGropuUserInfo);
			break;
			// 跨服争霸赛海选结束产生两个组别冠军
			case WorldwarDef::WORLD_ADVANCED_2:
				ChatTemplate::sendWorldWarFinal($winGropuUserInfo, $loserGropuUserInfo);
			break;
			default:
			break;
		}
	}


	/**
	 * 系统消息(crontab调用)
	 * 
	 */
	public static function sendWorldwarPrepareMsg($round)
	{
		Logger::debug('The system message send start.The round is %s.', $round);
		// 获取现在是第几届跨服战 —— 用来查询策划的配置表
		$setting = WorldwarUtil::getSetting();
		$session = $setting['session'];
		// 如果现在根本就没有配置跨服赛，则直接返回
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::warning('Now has no world war.');
			return 'err';
		}
		// 看看这个服有这个活动没有
		if(!WorldwarUtil::checkWorldwarIsOpen($setting))
		{
			Logger::debug('There is not activity in the server.');
			return 'err';
		}
		$limit = intval(btstore_get()->WORLDWAR[$session]['cd_time'][$round]['limit'] / 60);
		$cheerLimit = intval(btstore_get()->WORLDWAR[$session]['cheer_limit_time'] / 60);
		switch ($round) {
			// 争霸赛报名阶段开始
			case WorldwarDef::SIGNUP:
				if(Util::isSameDay(btstore_get()->WORLDWAR[$session]['time'][$round]['start']))
				{
					ChatTemplate::sendGroupWarSignUpStart();
				}
			break;
			// 争霸赛海选阶段开始15分钟前
			case WorldwarDef::GROUP_AUDITION:
				$limit = intval(btstore_get()->WORLDWAR[$session]['cd_time'][$round - 1]['limit'] / 60);
				ChatTemplate::sendGroupWarStartPrepare($limit);
			break;
			// 争霸赛32进16比赛第一局比赛开始前15分钟
			case WorldwarDef::GROUP_ADVANCED_32:
				ChatTemplate::sendGroupWarTop16Prepare($cheerLimit, $limit);
			break;
			// 争霸赛16进8比赛第一局比赛开始前15分钟
			case WorldwarDef::GROUP_ADVANCED_16:
				ChatTemplate::sendGroupWarTop8Prepare($cheerLimit, $limit);
			break;
			// 争霸赛8进4比赛第一局比赛开始前15分钟
			case WorldwarDef::GROUP_ADVANCED_8:
				ChatTemplate::sendGroupWarTop4Prepare($cheerLimit, $limit);
			break;
			// 争霸赛4进2比赛第一局比赛开始前15分钟
			case WorldwarDef::GROUP_ADVANCED_4:
				ChatTemplate::sendGroupWarTop2Prepare($cheerLimit, $limit);
			break;
			// 争霸赛2进1比赛第一局比赛开始前15分钟
			case WorldwarDef::GROUP_ADVANCED_2:
				ChatTemplate::sendGroupWarFinalPrepare($cheerLimit, $limit);
			break;
			// 跨服争霸赛海选阶段开始15分钟前
			case WorldwarDef::WORLD_AUDITION:
				$limit = intval(btstore_get()->WORLDWAR[$session]['cd_time'][$round - 1]['limit'] / 60);
				ChatTemplate::sendWorldWarStartPrepare($limit);
			break;
			// 跨服争霸赛32进16比赛第一局比赛开始前15分钟
			case WorldwarDef::WORLD_ADVANCED_32:
				ChatTemplate::sendWorldWarTop16Prepare($cheerLimit, $limit);
			break;
			// 跨服争霸赛16进8比赛第一局比赛开始前15分钟
			case WorldwarDef::WORLD_ADVANCED_16:
				ChatTemplate::sendWorldWarTop8Prepare($cheerLimit, $limit);
			break;
			// 跨服争霸赛8进4比赛第一局比赛开始前15分钟
			case WorldwarDef::WORLD_ADVANCED_8:
				ChatTemplate::sendWorldWarTop4Prepare($cheerLimit, $limit);
			break;
			// 跨服争霸赛4进2比赛第一局比赛开始前15分钟
			case WorldwarDef::WORLD_ADVANCED_4:
				ChatTemplate::sendWorldWarTop2Prepare($cheerLimit, $limit);
			break;
			// 跨服争霸赛2进1比赛第一局比赛开始前15分钟
			case WorldwarDef::WORLD_ADVANCED_2:
				ChatTemplate::sendWorldWarFinalPrepare($cheerLimit, $limit);
			break;
			default:
			break;
		}
		Logger::debug('The system message send end.');
	}


	/**
	 * 邮件
	 * 
	 */
	private static function sendWorldwarPrizeMail($round, $recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		Logger::debug('The mail use round is %s.', $round);
		switch ($round) {
			// 争霸赛32强奖励
			case WorldwarDef::GROUP_AUDITION:
				MailTemplate::sendGroupWarTop32($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			break;
			// 争霸赛16强奖励
			case WorldwarDef::GROUP_ADVANCED_32:
				MailTemplate::sendGroupWarTop16($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			break;
			// 争霸赛8强奖励
			case WorldwarDef::GROUP_ADVANCED_16:
				MailTemplate::sendGroupWarTop8($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			break;
			// 争霸赛4强奖励
			case WorldwarDef::GROUP_ADVANCED_8:
				MailTemplate::sendGroupWarTop4($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			break;
			// 争霸赛亚军奖励
			case WorldwarDef::GROUP_ADVANCED_4:
				MailTemplate::sendGroupWarTop2($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			break;
			// 争霸赛冠军奖励
			case WorldwarDef::GROUP_ADVANCED_2:
				MailTemplate::sendGroupWarTop1($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			break;
			// 跨服争霸赛32强奖励
			case WorldwarDef::WORLD_AUDITION:
				MailTemplate::sendWorldWarTop32($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			break;
			// 跨服争霸赛16强奖励
			case WorldwarDef::WORLD_ADVANCED_32:
				MailTemplate::sendWorldWarTop16($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			break;
			// 跨服争霸赛8强奖励
			case WorldwarDef::WORLD_ADVANCED_16:
				MailTemplate::sendWorldWarTop8($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			break;
			// 跨服争霸赛4强奖励
			case WorldwarDef::WORLD_ADVANCED_8:
				MailTemplate::sendWorldWarTop4($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			break;
			// 跨服争霸赛亚军奖励
			case WorldwarDef::WORLD_ADVANCED_4:
				MailTemplate::sendWorldWarTop2($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			break;
			// 跨服争霸赛冠军奖励
			case WorldwarDef::WORLD_ADVANCED_2:
				MailTemplate::sendWorldWarTop1($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
			default:
			break;
		}
	}


	private static function sendWorldwarCheerMail($type, $recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		if($type == WorldwarDef::TYPE_GROUP)
		{
			MailTemplate::sendGroupWarCheer($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
		}
		else if($type == WorldwarDef::TYPE_WORLD)
		{
			MailTemplate::sendWorldWarCheer($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
		}
		else
		{
			return;
		}
	}
	
	private static function sendAuditiondOverMsg($setting)
	{
		// 看看这个服有这个活动没有
		if(!WorldwarUtil::checkWorldwarIsOpen($setting))
		{
			Logger::debug('There is not activity in the server.');
			return;
		}
		if ($setting['now'] == WorldwarDef::TYPE_GROUP)
		{
			try
			{
				$lcProxyObj = new ServerProxy();
				$lcProxyObj->asyncExecuteRequest(0, 
											 	'worldwar.__sendAuditiondOverMsg', array($setting['now']));
			}
			catch (Exception $e)
			{
				// 防止发消息出错
				Logger::warning('__sendAuditiondOverMsg:%s', $e->getMessage());
			}
		}
		else if ($setting['now'] == WorldwarDef::TYPE_WORLD)
		{	
			// 声明平台接口
			$platform = ApiManager::getApi(true);
			// 拉取所有参赛的大组和组下的所有服务器
			$allServers = $platform->users('getServerGroupAll', array('pid' => 1, 
																	  'spanid' => $setting['session'],
																	  'action' => 'getServerGroupAll'));
			// 遍历所有大组
			foreach ($allServers as $teamID => $servers)
			{
				foreach ($servers as $serverID => $db)
				{
					try
					{
						// 获取serverID 
						$serverID = intval($serverID);
						// 特殊情况需要进行处理
						if ($serverID < 100)
						{
							$serverID = sprintf ("%03.0f", $serverID);
						}

						$lcProxyObj = new ServerProxy();
						$lcProxyObj->init('game'.$serverID, Util::genLogId());
						$lcProxyObj->asyncExecuteRequest(0, 
													 	'worldwar.__sendAuditiondOverMsg', array($setting['now']));			
					}
					catch (Exception $e)
					{
						// 防止发消息出错
						Logger::warning('__sendAuditiondOverMsg:%s', $e->getMessage());
					}
				}
			}
		}
	}
	
	public static function __sendAuditiondOverMsg($now)
	{
		if ($now == WorldwarDef::TYPE_GROUP)
		{
			ChatTemplate::sendGroupAuditionOver();
		}
		else if ($now == WorldwarDef::TYPE_WORLD)
		{
			ChatTemplate::sendWorldAuditionOver();
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */