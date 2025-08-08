<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldwarUtil.class.php 38046 2013-02-04 09:57:53Z ZhichaoJiang $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldwar/WorldwarUtil.class.php $
 * @author $Author: ZhichaoJiang $(liuyang@babeltime.com)
 * @date $Date: 2013-02-04 17:57:53 +0800 (一, 2013-02-04) $
 * @version $Revision: 38046 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : WorldwarUtil
 * Description : 跨服赛工具类
 * Inherit     :
 **********************************************************************************************************************/
class WorldwarUtil
{

	/**
	 * 判断当前是否有比赛，如果有比赛，返回当前是第几届
	 * 判断当前是否有比赛，如果有比赛，获得当前比赛阶段
	 * 判断当前是否有比赛，如果有比赛，返回是服内赛，还是跨服赛
	 * 
     * @param bool needLast						如果没有获取到当前届, 是否需要获取上一届是第几届
	 */
	public static function getSetting($needLast = FALSE, $needRest = FALSE)
	{
		// 获取当前时间
		$curTime = Util::getTime();
		// 记录上一届的ID
		$oldSessionID = WorldwarDef::OUT_RANGE;
		// 获取现在的ID 
		$session = WorldwarDef::OUT_RANGE;
		// 循环遍历策划们的配置表
		foreach (btstore_get()->WORLDWAR as $worldwar)
		{
			// 查看是否是这一届
			if ($curTime >= $worldwar['time'][WorldwarDef::SIGNUP]['start'] && 
				$curTime <= $worldwar['time'][WorldwarDef::WORLD_ADVANCED_2]['end'])
			{
				Logger::debug('The session is %s.', $worldwar['id']);
				// 遍历到了的话，返回当前届
				$session = $worldwar['id'];
				// 退出查找
				break;
			}
			// 记录一下上一届的ID值
			if ($curTime > $worldwar['time'][WorldwarDef::WORLD_ADVANCED_2]['end'])
			{
				$oldSessionID = $worldwar['id'];
			}
		}
		// 如果需要返回上一届的
		if ($needLast && $session == WorldwarDef::OUT_RANGE)
		{
			return array('session' => $oldSessionID, 
						 'round' => WorldwarDef::OUT_RANGE, 'now' => WorldwarDef::OUT_RANGE);
		}
		// 如果根本就没有，就没法进行下去了
		else if (!$needLast && $session == WorldwarDef::OUT_RANGE)
		{
			return array('session' => WorldwarDef::OUT_RANGE, 
						 'round' => WorldwarDef::OUT_RANGE, 'now' => WorldwarDef::OUT_RANGE);
		}

		// 查找轮次
		$round = WorldwarDef::OUT_RANGE;
		// 查看一共有多少轮次, 最后一个轮次不算
		$allRound = count(btstore_get()->WORLDWAR[$session]['time']);
		// 循环所有的轮次
		for ($index = 1; $index <= $allRound; ++$index)
		{
			// 最后一个需要特殊处理
			if ($index == WorldwarDef::WORLD_ADVANCED_2 && 
				$curTime <= btstore_get()->WORLDWAR[$session]['time'][$index]['end'])
			{
				$round = $index;
				Logger::debug('The round is %s.', $index);
				break;
			}
			else if ($index == WorldwarDef::WORLD_ADVANCED_2)
			{
				break;
			}
			// 找到了的话就返回
			if(btstore_get()->WORLDWAR[$session]['time'][$index]['start'] <= $curTime && 
			   btstore_get()->WORLDWAR[$session]['time'][$index + 1]['start'] >= $curTime)
			{
				Logger::debug('The round is %s. %s %s.', $index, 
									btstore_get()->WORLDWAR[$session]['time'][$index]['start'],
									btstore_get()->WORLDWAR[$session]['time'][$index + 1]['start']);
				$round = $index;
				break;
			}
		}

		// 获取现在的阶段
		$now = self::getNow($round, $needRest);
		// 返回
		return array('session' => $session, 'round' => $round, 'now' => $now);
	}


	/**
	 * 当前是否有比赛，如果有比赛，返回当前是第几届
	 * 
     * @param bool needLast						如果没有获取到当前届, 是否需要获取上一届是第几届
	 */
	public static function getSession($needLast = FALSE)
	{
		// 获取当前时间
		$curTime = Util::getTime();
		// 记录上一届的ID
		$oldSessionID = 0;
		// 循环遍历策划们的配置表
		foreach (btstore_get()->WORLDWAR as $worldwar)
		{
			// 查看是否是这一届
			if ($curTime >= $worldwar['time'][WorldwarDef::SIGNUP]['start'] && 
				$curTime <= $worldwar['time'][WorldwarDef::WORLD_ADVANCED_2]['end'])
			{
				Logger::debug('The session is %s.', $worldwar['id']);
				// 遍历到了的话，返回当前届
				return $worldwar['id'];
			}
			// 记录一下上一届的ID值
			if ($curTime > $worldwar['time'][WorldwarDef::WORLD_ADVANCED_2]['end'])
			{
				$oldSessionID = $worldwar['id'];
			}
		}
		// 否则返回空即可
		return $needLast ? $oldSessionID : WorldwarDef::OUT_RANGE;
	}


	/**
	 * 判断当前是否有比赛，如果有比赛，获得当前比赛阶段
	 * 
	 */
	public static function getRound($session, $offset)
	{
		// 获取当前时间
		$curTime = Util::getTime() + $offset;
		// 查看一共有多少轮次, 最后一个轮次不算
		$allRound = count(btstore_get()->WORLDWAR[$session]['time']);
		// 循环所有的轮次
		for ($index = 1; $index <= $allRound; ++$index)
		{
			// 最后一个需要特殊处理
			if ($index == WorldwarDef::WORLD_ADVANCED_2 && 
				$curTime <= btstore_get()->WORLDWAR[$session]['time'][$index]['end'])
			{
				Logger::debug('The round is %s.', $index);
				return $index;
			}
			else if ($index == WorldwarDef::WORLD_ADVANCED_2)
			{
				break;
			}
			// 找到了的话就返回
			if(btstore_get()->WORLDWAR[$session]['time'][$index]['start'] <= $curTime && 
			   btstore_get()->WORLDWAR[$session]['time'][$index + 1]['start'] >= $curTime)
			{
				Logger::debug('The round is %s.', $index);
				return $index;
			}
		}
		// 如果没有找到的话，就返回错误吧？
		return WorldwarDef::OUT_RANGE;
	}


	/**
	 * 判断当前是否有比赛，如果有比赛，返回是服内赛，还是跨服赛
	 * 
	 * @param $needRest 						是否需要休息一下
	 */
	public static function getNow($round, $needRest = FALSE)
	{
		// 服内赛的时候，返回服内赛阶段
		if ($round >= WorldwarDef::GROUP_AUDITION && $round <= WorldwarDef::GROUP_ADVANCED_2)
		{
			return WorldwarDef::TYPE_GROUP;
		}
		// 跨服赛的时候，返回跨服赛阶段
		else if ($round >= WorldwarDef::WORLD_AUDITION && $round <= WorldwarDef::WORLD_ADVANCED_2)
		{
			return WorldwarDef::TYPE_WORLD;
		}
		else if ($round == WorldwarDef::WORLD_REST && $needRest)
		{
			return WorldwarDef::TYPE_GROUP;
		}
		// 空的话，就表示没有比赛啊， 报名也是没有比赛啊
		return WorldwarDef::OUT_RANGE;
	}


    /**
     * 获取当年年月日
     */
    public static function getCurYmd($offset = 0)
    {
    	// 获取当前时刻
    	$curTime = Util::getTime() + $offset;
    	// 获取当日日期
		$curYmd = date("Ymd", $curTime);
		// 返回
		return $curYmd;
    }


    /**
     * 获取服务器开启时刻
     */
    public static function getServerOpenTime()
    {
    	// 获取开服更新时刻
		if (defined("GameConf::SERVER_OPEN_TIME"))
		{
			return strtotime(GameConf::SERVER_OPEN_YMD. GameConf::SERVER_OPEN_TIME);
		}
		// 没有配置开服时刻就返回凌晨四点
		return strtotime(GameConf::SERVER_OPEN_YMD. WorldwarConfig::REFRESH_HOUR);
    }


	/**
	 * 是否可以参加跨服赛
	 */
    public static function canEnter()
    {
        if (!EnSwitch::isOpen(SwitchDef::ACTIVE))
        {
        	Logger::warning('Fail to enter world war, switch return false!');
        	throw new Exception('fake');
        }	
    	// 活动节点
    	return true;
    }


    /**
     * 通过服务器ID，返回teamID
     * 
     * @param int $serverID						服务器ID
     * @param int $session						第几届
     */
    public static function getTeamIDByServerID($serverID, $session)
    {
    	// 声明平台接口
		$platform = ApiManager::getApi(true);
		// 获取所有服的名字
		$ret = $platform->users('getServerGroup', array('pid' => 1, 
														'servid' => $serverID, 
														'spanid' => $session,
														'action' => 'getServerGroup'));
		// 抽出key，返回
		$key = array_keys($ret);
		return isset($key[0]) ? $key[0] : false;
    }


    /**
     * 用户对象
     * 
     * @param obj $user							用户对象
     */
    public static function getUserForBattle($user)
    {
    	// 获取用户默认阵型信息
		$userFormation = EnFormation::getFormationInfo($user->getUid());
		// 查看是否有英雄在阵上
		$hasHero = false;
		foreach ($userFormation as $heroTmp)
		{
			if ($heroTmp instanceof OtherHeroObj)
			{
				$hasHero = true;
				break;
			}
		}
		// 如果没有英雄在阵上， 那么就出错了
		if (!$hasHero)
		{
			Logger::warning('Cur formation has no hero!');
			throw new Exception('fake');
		}
		// 组织战斗模块所需数据
		$formationID = $user->getCurFormation();
		// 这时候拉取所有缓存信息
		$user->prepareItem4CurFormation();
		// 转换格式
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation, true, true);
		Logger::debug('The hero list is %s', $userFormationArr);

		// 计算战斗力
		$fightForce = 0;
		// 战斗力是所有英雄战斗力的合
		foreach ($userFormationArr as $hero)
		{
			$fightForce += $hero['fight_force'];
		}
		// 获取服务器ID
		$serverID = self::getServerId();
		Logger::debug('The serverId of user is %s.', $serverID);
		// 根据服务器ID获取服务器名称
		$serverName = WorldwarDao::getServerNameByID($serverID);

		// 返回, 这些珍贵的信息需要保存到数据库里面。以后都不会再实时拉取了
		return array('uid' => $user->getUid(),
					 'name' => $user->getUname(),
					 'htid' => $user->getMasterHeroObj()->getHtid(),
					 'server_id' => $serverID,
					 'server_name' => $serverName,
                     'level' => $user->getLevel(),
					 'isPlayer' => true,
                     'flag' => 0,
                     'formation' => $formationID,
                     'arrHero' => $userFormationArr,
					 'fightForce' => $fightForce);
    }


    /**
     * 返回server id
     */
    public static function getServerID()
    {
    	return Util::getServerId();
    }


    /**
     * 前端需要的人物信息
     * 
     * @param array $userBattlePara				战斗前保存好的参数
     */
    private static function getUserInfo($userBattlePara)
    {
    	// 删掉一些前端不需要的内容，然后直接返回
    	unset($userBattlePara['isPlayer']);
    	unset($userBattlePara['flag']);
    	unset($userBattlePara['formation']);
    	unset($userBattlePara['arrHero']);
    	return $userBattlePara;
    }


	/**
	 * 执行一场 PvP
	 * 
	 * @param int $now							当前阶段
	 * @param array $infoCur					战斗者A的信息
 	 * @param array $infoObj					战斗者B的信息
	 */
	public static function doFight($now, $infoCur, $infoObj)
	{
		Logger::debug('Battle play1 is %s.', $infoCur);
		Logger::debug('Battle play2 is %s.', $infoObj);
		
		// 如果有一方轮空，则直接获胜
		if (empty($infoObj['uid']))
		{
			return array('winer' => self::getUserInfo($infoCur));
		}
		if (empty($infoCur['uid']))
		{
			return array('winer' => self::getUserInfo($infoObj));
		}
		// 两方都不为空，则需要进行真刀真枪的战斗
		// 谁战斗力靠前谁先手
		if ($infoCur['fightForce'] >= $infoObj['fightForce'])
		{
			$offensiveUser = $infoCur;
			$defensiveUser = $infoObj;
		}
		else 
		{
			$offensiveUser = $infoObj;
			$defensiveUser = $infoCur;
		}
	
		try 
		{
			// 开打
			$bt = new Battle();
			$atkRet = $bt->doHero($offensiveUser, 
			                      $defensiveUser, 
			                      0, 
			                      null,
								  null, 
								  array('bgid' => ArenaConf::BATTLE_BJID,
								        'musicId' => ArenaConf::BATTLE_MUSIC_ID, 
								        'isKFZ' => true, 
								        'type' => BattleType::OLYMPIC),
								  $now == WorldwarDef::TYPE_GROUP ? null : WorldwarConfig::KFZ_DB_NAME);
		}
		catch (Exception $e)
		{
			// 挂掉的时候，先手的人胜利
			$atkRet['server']['appraisal'] = 'S';
			$atkRet['server']['brid'] = '';
		}
		// 战斗系统返回值
		Logger::debug('Ret from battle is %s.', $atkRet);

		// 胜负判定, 有两种情况算是获胜 1. 本人先手，且获胜; 2. 对方先手， 失败了
		if (((BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D']) && 
			  $offensiveUser['uid'] == $infoCur['uid']) || 
			((BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] >= BattleDef::$APPRAISAL['E']) && 
			  $offensiveUser['uid'] == $infoObj['uid']))
		{
			return array('winer' => self::getUserInfo($infoCur), 'loser' => self::getUserInfo($infoObj),
			             'replay' => $atkRet['server']['brid'], 'offensive' => $offensiveUser['uid']);
		}
		// 输的时候，返回
		else 
		{
			return array('winer' => self::getUserInfo($infoObj), 'loser' => self::getUserInfo($infoCur),
			             'replay' => $atkRet['server']['brid'], 'offensive' => $offensiveUser['uid']);
		}
	}


	/**
	 * 获取两个战斗的对手 —— 有可能会有轮空情况
	 * 
	 * 本方法适用于所有服务器
	 * 
	 * 
	 * @param array $arrHeros					所有参赛人员名录
	 * @param int $start						开始选择的下标
	 * @param int $offset						在多少人内选择这两个人
	 * @param int $rank							这两个人现在应该有的名次
	 * @param int $nextRank						晋级的名次
	 * @param int $now							跨服 or 服内
	 * @throws Exception
	 */
	public static function getEnemy($arrHeros, $start, $offset, $rank, $nextRank, $now)
	{
		Logger::debug('getEnemy para:arrHeros = %s, start = %s, offset = %s, rank = %s', 
					   $arrHeros, $start, $offset, $rank);
    	// 记录结果
    	$tmp = array(0 => array(), 1 => array());
    	// 记录结果需要使用的下标
    	$index = 0;
    	// 循环这一小段
    	for ($i = 0; $i < $offset; ++$i)
    	{
    		Logger::debug('The arrHeros is %s', $arrHeros[$i + $start]);
    		// 如果uid是0，就不用干啥了
    		if (empty($arrHeros[$i + $start]['uid']))
    		{
    			$tmp[$index]['index'] = $arrHeros[$i + $start]['index'];
    			continue;
    		}
    		// 如果排行正好，则进行比赛
    		if ($arrHeros[$i + $start]['rank'] == $rank)
    		{
    			// 取出这个人，准备参赛
    			$tmp[$index]['uid'] = $arrHeros[$i + $start]['uid'];
    			$tmp[$index]['index'] = $arrHeros[$i + $start]['index'];
    			// 获取用户的战斗信息
    			$tmp[$index]['va_world_war']['fight_para'] = 
    						WorldwarDao::getUserFightPara($arrHeros[$i + $start]['uid'], 
    													  $arrHeros[$i + $start]['server_id'],
    													  $now);
    			// 拉到了一个壮丁，下标计数加一
    			++$index;
    		}
    		// 如果这个人已经晋级了, 直接返回原始数据
    		else if ($arrHeros[$i + $start]['rank'] == $nextRank)
    		{
    			// 取出这个人，准备参赛
    			$tmp[$index] = $arrHeros[$i + $start];
    			// 拉到了一个壮丁，下标计数加一
    			++$index;
    		}
    	}
    	// 如果对打的不是两个人，就出错了，人工介入吧……
    	if ($index > 2)
    	{
	        Logger::fatal("Can not more then 2 people fight, arrHeros : %s, start : %d, offset : %d, rank : %d!", 
	                      $arrHeros, $start, $offset, $rank);
	        throw new Exception('fake');
    	}
    	Logger::debug("GetEnemy ret is %s.", $tmp);
    	return $tmp;
	}
	
	public static function checkWorldwarIsOpen($setting)
	{
		// 需开服小于等于策划配置的天数（配置）才可开启服内争霸赛
		if($setting['now'] == WorldwarDef::TYPE_GROUP && 
			WorldwarUtil::getServerOpenTime() > btstore_get()->WORLDWAR[$setting['session']]['activity_basetime'])
		{
			return false;
		}
		
		$serverId = WorldwarUtil::getServerId();
		// 声明平台接口
		$platform = ApiManager::getApi(true);
		// 拉取所有参赛的大组和组下的所有服务器
		$allServers = $platform->users('getServerGroupAll', array('pid' => 1, 
																  'spanid' => $setting['session'],
																  'action' => 'getServerGroupAll'));
		// 遍历所有大组
		foreach ($allServers as $teamID => $servers)
		{
			// 一个大组应该有20组左右的服务器
			foreach ($servers as $serverID => $db)
			{
				if(intval($serverID) == intval($serverId))
				{
					return true;
				}
			}
		}
		return false;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */