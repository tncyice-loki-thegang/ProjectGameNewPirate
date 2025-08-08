<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: OlympicUtil.class.php 34347 2013-01-06 08:15:31Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/olympic/OlympicUtil.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-06 16:15:31 +0800 (日, 2013-01-06) $
 * @version $Revision: 34347 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : OlympicUtil
 * Description : 擂台赛工具类
 * Inherit     :
 **********************************************************************************************************************/
class OlympicUtil
{
	/**
	 * 获取从开始到现在经过的时刻
	 * 
	 * @param int $startTime					开始时刻
	 * @param int $type							第几个阶段
	 */
	public static function getEndTime($startTime, $type, $needDelay = false)
	{
		for ($i = 0; $i <= $type; ++$i)
		{
			$startTime += OlympicConf::$last_times[$i];
		}
		// 需要延时的情况，延时一分钟
		if ($needDelay)
		{
			$startTime += OlympicDef::DELAY_TIME;
		}
		return $startTime;
	}

	/**
	 * 查看现在处于什么阶段
	 */
	public static function getNow()
	{
		// 获取当前时刻
		$curTime =  Util::getTime();
		// 获取擂台赛开始时刻 —— 需要进行偏移
		$startTime = strtotime(self::getCurYmd(). OlympicConf::START_TIME) + GameConf::BOSS_OFFSET;
		Logger::debug("OlympicUtil::getNow  Start time is %d, now is %d.", $startTime, $curTime);

		// 如果还没到时候，则直接返回
		if ($curTime < $startTime)
		{
			return OlympicDef::OUT_RANGE;		// 没到比赛时刻
		}
		// 返回相应的比赛时刻
		else if ($curTime < self::getEndTime($startTime, 0) - 5 && 
		         $curTime >= $startTime)
		{
			return OlympicDef::SIGN_UP;			// 报名时刻
		}
		else if ($curTime < self::getEndTime($startTime, 0, true) && 
		         $curTime >= self::getEndTime($startTime, 0))
        {
			return OlympicDef::DELAY;			// 什么都不干的等待时刻
        }
		else if ($curTime < self::getEndTime($startTime, 1, true) && 
		         $curTime >= self::getEndTime($startTime, 0, true))
		{
			return OlympicDef::FINAL_16_PER;	// 16/1 决赛
		}
		else if ($curTime < self::getEndTime($startTime, 2, true) && 
		         $curTime >= self::getEndTime($startTime, 1, true))
		{
			return OlympicDef::FINAL_8_PER;		// 8/1 决赛
		}
		else if ($curTime < self::getEndTime($startTime, 3, true) && 
		         $curTime >= self::getEndTime($startTime, 2, true))
		{
			return OlympicDef::FINAL_QUARTER;	// 4/1 决赛
		}
		else if ($curTime < self::getEndTime($startTime, 4, true) && 
		         $curTime >= self::getEndTime($startTime, 3, true))
		{
			return OlympicDef::FINAL_SEMI;		// 半决赛
		}
		else if ($curTime < self::getEndTime($startTime, 5, true) && 
		         $curTime >= self::getEndTime($startTime, 4, true))
		{
			return OlympicDef::FINALS;			// 决赛
		}
		else if ($curTime < self::getEndTime($startTime, 6, true) && 
		         $curTime >= self::getEndTime($startTime, 5, true))
		{
			return OlympicDef::AWARDS;			// 颁奖
		}
		// 其他时间都是空闲时间
		return OlympicDef::OUT_RANGE;
	}

	/**
	 * 是否可以参加擂台赛
	 */
    public static function canEnter()
    {
        if (!EnSwitch::isOpen(SwitchDef::ACTIVE))
        {
        	Logger::warning('Fail to enter olympic, switch return false!');
        	throw new Exception('fake');
        }	
    	// 活动节点
    	return true;
    }

    /**
     * 查看当前阶段，大家都在争取什么名次
     */
    public static function getNextLevel()
    {
    	// 获取当前情况
    	$now = self::getNow();
    	// 如果不在范围内调用了，直接出错 —— 我对自己还真是严格啊！
    	if ($now == OlympicDef::OUT_RANGE)
    	{
        	Logger::fatal('Coding error! liuyang, you ……');
        	throw new Exception('fake');
    	}
    	// 查看当前阶段，大家都在争取什么名次
    	return OlympicDef::$next[$now];
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

		// 返回
		return array('name' => $user->getUname(),
                     'level' => $user->getLevel(),
					 'isPlayer' => true,
                     'flag' => 0,
                     'formation' => $formationID,
                     'uid' => $user->getUid(),
                     'arrHero' => $userFormationArr,
					 'fightForce' => $fightForce);
    }

    /**
     * 获取前端需要知道的用户信息
     * 
     * @param obj $user							用户对象
     * @param int $fightForce					战斗力
     */
    public static function getUserInfo($user, $fightForce = 0)
    {
    	// 先保存基础信息
    	$ret = array('uid' => $user->getUid(),
                     'uname' => $user->getUname(),
                     'htid' => $user->getMasterHeroObj()->getHtid());
    	// 如果战斗力值不为空
    	if ($fightForce != 0)
    	{
    		// 记录下战斗力信息
    		$ret['fightForce'] = $fightForce;
    	}
    	return $ret;
    }

    /**
     * 查询某个位置的用户信息
     * 
     * @param array $info						数据库信息
	 * @param int $groupID						阵营ID
	 * @param int $index						报名位置
     */
    public static function getInfoByIndex($info, $groupID, $index)
    {
    	Logger::debug("GetInfoByIndex para is %d, %d.", $groupID, $index);
    	foreach ($info as $v)
    	{
    		if ($v['group_id'] == $groupID && $v['sign_up_index'] == $index)
    		{
    			return $v;
    		}
    	}
    }

    /**
     * 查询某个位置的用户信息
     * 
     * @param array $info						数据库信息
     * @param int $uid							用户ID
     * @throws Exception
     */
    public static function getInfoByUid($info, $uid)
    {
    	Logger::debug("GetInfoByUid para is %d.", $uid);
    	foreach ($info as $v)
    	{
    		if ($v['uid'] == $uid)
    		{
    			return $v;
    		}
    	}
    	// 出错了，没有找到这个用户
        return false;
    }

    /**
     * 根据顺序获取两个对战的人
     * 
     * @param array $info						数据库里面的所有人信息
     * @param array $order						数据库的32强对战顺序
     * @param int $start						对战数组里面需要查找的开始位置
     * @param int $offset						对战数组里面需要查找的跨度
     * @param int $curIndex						需要获取的名次
     * @throws Exception
     */
    public static function getEnemy($info, $order, $start, $offset, $curIndex)
    {
    	Logger::debug("GetEnemy para is order %s, start : %d, offset : %d.", $order, $start, $offset);
    	// 记录结果
    	$tmp = array();
    	// 循环这一小段
    	for ($i = 0; $i < $offset; ++$i)
    	{
//	    	Logger::debug("Tmp is %s, i is %d, order[i+start] is %d.", $tmp, $i, $order[$i + $start]);
    		// 如果uid是0，就不用干啥了
    		if (empty($order[$i + $start]))
    		{
    			continue;
    		}
    		// 根据获取出来的uid查询这个人的排行情况
	    	foreach ($info as $key => $v)
	    	{
	    		if ($v['uid'] == $order[$i + $start] && $v['final_rank'] == $curIndex)
	    		{
	    			// 记录跟最小值相同的人
	    			$tmp[] = $v;
	    		}
	    	}
    	}
    	// 如果对打的不是两个人，就出错了，人工介入吧……
    	if (count($tmp) > 2)
    	{
	        Logger::fatal("Can not 2 people fight info : %s, order : %s, start : %d, offset : %d!", 
	                      $info, $order, $start, $offset);
	        throw new Exception('fake');
    	}
    	// 只有一个人, 轮空, 需要特别对待, 赋一个特殊的值，这样进入比赛的时候，就会得不到数据，直接占位
    	else if (count($tmp) == 1)
    	{
    		$tmp[1]['group_id'] = 0; 
    		$tmp[1]['sign_up_index'] = 0;
    	}
    	Logger::debug("GetEnemy ret is %s.", $tmp);
    	return $tmp;
    }

    /**
     * 获取当年年月日
     */
    public static function getCurYmd()
    {
    	// 获取当前时刻
    	$curTime = Util::getTime();
    	// 获取当日日期
		$curYmd = date("Ymd ", $curTime);
		// 返回
		return $curYmd;
    }

    /**
     * 是否是发奖日
     */
    public static function isHappyDay()
    {
    	// 判断并返回
    	return Util::getTodayWeek() == OlympicConf::HAPPLY_WEEK;
    }

    /**
     * 获取最近一次的大奖时刻
     */
    public static function getLastestHappyTime()
    {
		// 一天的秒数
		$SECONDS_OF_DAY = 86400;
    	// 获取当前时刻
    	$curTime = Util::getTime();
    	// 获取开服时刻
    	$startTime = strtotime(GameConf::SERVER_OPEN_YMD . OlympicConf::START_TIME) + GameConf::BOSS_OFFSET;
    	// 获取开奖时刻 —— 以防万一，多加一些时间，这是没关系的。但是如果不加这个时间，会导致周六发奖的时候先被清空了
    	$startTime = self::getEndTime($startTime, 6, true) + 3600;
    	// 获取开服的星期
    	$sWeek = intval(date('w', $startTime));
    	// 算出开服后的第一个发奖时刻
    	$sAwardTime = $startTime + (OlympicConf::HAPPLY_WEEK - $sWeek) * $SECONDS_OF_DAY;
    	// 如果这个发奖时刻还没到呢
    	if ($curTime < $sAwardTime)
    	{
    		return 0;
    	}
    	// 计算
    	while ($sAwardTime <= $curTime)
    	{
    		// 先记录下日子
    		$ret = $sAwardTime;
    		// 加算
    		$sAwardTime += $SECONDS_OF_DAY * 7;
    	}

    	// 返回
    	return $ret;
    }

    /**
     * 发送决赛和半决赛的消息
     * 
     * @param array $winner						getTemplateUserInfo 的返回值 —— 胜利者
     * @param int $nextLv						下一轮比赛的目标名次
     * @param bool $isBye						是否轮空
     * @param array $loser						getTemplateUserInfo 的返回值 —— 失败者，轮空的话不需要传
     * @param array $rpID						战报ID，轮空的话不需要传
     */
    public static function sendChatMsg($winner, $nextLv, $isBye = true, $loser = null, $rpID = 0)
    {
    	// 先判断是决赛还是半决赛 —— 下一个目标名次是亚军的话，就是半决赛了
    	if ($nextLv == OlympicDef::RUNNER_UP)
    	{
    		// 判断是否轮空
    		if ($isBye)
    		{
    			ChatTemplate::sendChanlledgeSemifinalNull($winner);
    		}
    		else
    		{
    			ChatTemplate::sendChanlledgeSemifinal($winner, $loser, $rpID);
    		}
    	}
    	// 决赛的话发决赛的消息 —— 下一个目标名次是冠军！
    	else if ($nextLv == OlympicDef::CHAMPION)
    	{
    		// 其实轮空的人还是少数啊……
    		if ($isBye)
    		{
    			ChatTemplate::sendChanlledgeFinalNull($winner);
    		}
    		else
    		{
    			ChatTemplate::sendChanlledgeFinal($winner, $loser, $rpID);
    		}
    	}
    }
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
