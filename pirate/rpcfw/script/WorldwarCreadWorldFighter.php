<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldwarCreadWorldFighter.php 37915 2013-02-02 07:05:39Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/WorldwarCreadWorldFighter.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-02-02 15:05:39 +0800 (六, 2013-02-02) $
 * @version $Revision: 37915 $
 * @brief 
 *  
 **/


class WorldwarCreadWorldFighter extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 * 
	 * @para $arrOption[0]          第几届
	 * @para $arrOption[1]      	 执行机器
	 */
	protected function executeScript ($arrOption)
	{
		// 获取想要干什么
		$session = 0;
		if(empty($arrOption[0]))
		{
			$ret = self::setting();	
			if($ret == 'err')
			{
				return 'err';
			}
			$session = $ret['session'];
		}
		else 
		{
			$session = $arrOption[0];
		}
		self::fixRecordWorldAudition();
	}

	private static function setting($offset = 0)
	{
		$session = WorldwarUtil::getSession();
		// 如果现在根本就没有配置跨服赛，则直接返回
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::debug('Now has no world war.');
			return 'err';
		}
		// 获取今天的轮次 
    	$curRound = WorldwarUtil::getRound($session, $offset);
    	if ($curRound == WorldwarDef::OUT_RANGE)
    	{
			Logger::debug('Round err, 0.');
    		return 'err';
    	}
		$now = WorldwarUtil::getNow($curRound);
    	if ($curRound != WorldwarDef::SIGNUP && $now == WorldwarDef::OUT_RANGE)
    	{
			Logger::debug('Now err, 0.');
    		return 'err';
    	}
    	return array('session' => $session,
    				 'round' => $curRound,
    				 'now' => $now);
	}
	
	
   /**
     * 修复数据用
	 * 
	 * 本方法适用于所有服务器
     */
	public static function fixRecordWorldAudition()
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

    	// 需要修复的组id
    	$fixTeamAry = array(1, 2, 3, 6, 36);
		/**************************************************************************************************************
     	 * 进行胜者组海选比赛
     	 **************************************************************************************************************/
		Logger::trace("Start once team win audition.");
		foreach ($fixTeamAry as $fixTeamId)
		{
			Logger::info("the fix win teamid is %s.", $fixTeamId);
			// 取得所有报名用户的数据, 并将此数据维护到内存中
			$teamArr = self::__getAllSignUser($now,
											  btstore_get()->WORLDWAR[$session]['time'][WorldwarDef::SIGNUP]['start'],
										  	  btstore_get()->WORLDWAR[$session]['group_fail_num'], 
										  	  0,
										  	  $fixTeamId);
			// 对所有大区依次进行海选
			WorldwarLogic::__doOpenAudition($setting, $teamArr, WorldwarDef::TEAM_WIN);
		}


		/**************************************************************************************************************
     	 * 进行败者组组海选比赛
     	 **************************************************************************************************************/
		$fixTeamAry = array(1, 3, 6, 11);
		Logger::trace("Start once team lose audition.");
		foreach ($fixTeamAry as $fixTeamId)
		{
			Logger::info("the fix lose teamid is %s.", $fixTeamId);
			// 取得所有不是胜者组的报名者数据
			$teamArr = self::__getAllSignUser($now,
											  btstore_get()->WORLDWAR[$session]['time'][WorldwarDef::SIGNUP]['start'],
										  	  btstore_get()->WORLDWAR[$session]['group_fail_num'],
										  	  WorldwarDef::TEAM_WIN,
										  	  $fixTeamId);
			// 对所有大区依次进行海选
			WorldwarLogic::__doOpenAudition($setting, $teamArr, WorldwarDef::TEAM_LOSE);	
		}
		
		Logger::trace("Audition over.");
	}
	
	private static function __getAllSignUser($now, $startTime, $failTime, $team = 0, $teamId)
    {
    	Logger::info("the now is %s.", $now);
    	
    	// 声明返回值
    	$tmp = array();
    	// 如果是服内赛阶段，则直接进表拉取
    	if ($now == WorldwarDef::TYPE_GROUP)
    	{
			// 取得报名用户的数据
			$heroArr = self::getSignUpUserInfo($startTime, $failTime, $team, $teamId);
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
    		$heroArr = self::getUserWorldSignUp($startTime, $failTime, $team, $teamId);
    		
			Logger::info("the hero info is %s.", $heroArr);

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
	
	public static function getUserWorldSignUp($signUpTime, $failNum, $team = 0, $teamId)
	{
		// 如果是胜者组
		if($team == 0)
		{
			$field = array('team_id',
						   'uid',
						   'uid_server_id',
						   'uid_server_name',
						   'win_team_lose_times as lose_times', 
                           'team');

			$cond = array('win_team_lose_times', '<', $failNum);
		}
		// 如果是败者组
		else 
		{
			$field = array('team_id',
						   'uid',
						   'uid_server_id',
						   'uid_server_name',
						   'lose_team_lose_times as lose_times', 
                           'team');

			$cond = array('lose_team_lose_times', '<', $failNum);
		}

		$data = new CData();
		$data->useDb(WorldwarConfig::KFZ_DB_NAME);
		// 查询
		$data->select($field)
             ->from('t_user_world_sign_up')
             ->where(array('sign_time', '>=', $signUpTime))
             ->where($cond);

		if (!empty($team))
		{
             $data->where(array('team', '!=', $team));
		}
		if (!empty($teamId))
		{
             $data->where(array('team_id', '=', $teamId));
		}
		
       	// 查询并返回结果
		return $data->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */