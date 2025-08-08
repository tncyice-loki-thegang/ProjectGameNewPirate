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



/**********************************************************************************************************************
 * Class       : CopyManager
 * Description : 副本控制类，在用户进行副本活动时，需要和此类的数据进行同步
 * Inherit     : 
 **********************************************************************************************************************/
class CopyManager
{
	/**
	 * 获取某个副本中所有活动出现的所有队伍,  注意！！ 活动随机出现的部队，不一定都会出现在副本里，需要进行过滤
	 * 
	 * 返回值会包括刷新点信息和部队信息，  部队ID有可能会重复
	 * @param int $copyID						副本ID
	 */
	public static function getLatestEnemies($copyID)
	{
		// 活动的敌人们， 函数返回值
		$enemies = array();
		// 使用副本ID进行过滤, 获取此副本举行的所有活动ID
		if (!isset(btstore_get()->COPY_ACT['copy_act'][$copyID]))
		{
			// 没有活动的话，直接返回空数组
			return $enemies;
		}
		$actList = btstore_get()->COPY_ACT['copy_act'][$copyID];
		Logger::debug('All the act in No. %d copy is %s.', $copyID, $actList->toArray());

		// 循环处理所有活动
		foreach ($actList as $actID)
		{
			// 调整时刻，如果需要进行刷新则执行刷新动作
			$ret = self::adjustRefreshTime($actID);
			// 有变化的时候
			if ($ret === 'IN' || $ret === 'OUT')
			{
				// 将设置保存到数据库
				AllActivities::getInstance()->save($actID);
			}
			// 在活动外的时候，就别计算部队了，都没活动还能计算啥啊
			if ($ret === 'OUT' || $ret === 'QUIET')
			{
				// 如果活动已经结束了，那么赶紧看下个吧，别耗了。
				continue;
			}
			// 获取活动信息
			$actInfo = AllActivities::getInstance()->getActivityInfo($actID);
			Logger::debug('The rp info of activity is %s.', $actInfo);
			// 获取此活动的部队ID
			foreach ($actInfo['va_activity_info'] as $key => $act)
			{
				// 根据刷新点位置进行过滤
				if ($act['refreshPoint'] != 0 &&
				    btstore_get()->REFRESH_POINT[$act['refreshPoint']]['copy_id'] == $copyID)
				{
					$enemy[$key] = $act;
				}
			}
			// 返回剩余部队信息
			// 如果随出来东西了，那么返回部队ID，啥都没随出来的话，返回空就行了
			if (isset($enemy))
			{
				$enemies = Util::arrayIndex($enemy, 'enemyID');
			}
		}
		// 这个结果需要保存设置进session
		RPCContext::getInstance()->setSession('copy.rpEnemies', $enemies);
		Logger::debug('All rp enemies is %s.', $enemies);
		// 如果没有结果，那么就返回空数组, 如果有数据，返回刷新点ID和部队ID
		return $enemies;
	}

	/**
	 * 随机抽选刷新点，随机出部队
	 * @param int $actID						活动ID
	 */
	private static function refreshEnemy($actID)
	{
		/**************************************************************************************************************
 		 * 先随机出一共有几个刷新点
 		 **************************************************************************************************************/
		// 先看看刷新个数的权重
		$arrNumWeight = btstore_get()->COPY_ACT[$actID]['rp_num']->toArray();
		// 给爷刷一个, 因为数组下标是 0 开始的，所以个数需要加 1
		$num = Util::randWeight($arrNumWeight) + 1;
		Logger::debug('Refresh start.');
		Logger::debug('The count of refresh point is %d.', $num);

		/**************************************************************************************************************
 		 * 随机出刷新点
 		 **************************************************************************************************************/
		$rpArray = btstore_get()->COPY_ACT[$actID]['rp_array'];
		$rpWeight = btstore_get()->COPY_ACT[$actID]['rp_array_weight'];
		// 权重数组
		$rpArrayWeight = array();
		// 设置权重
		for ($index = 0; $index < count($rpArray); ++$index)
		{
			$rpArrayWeight[$rpArray[$index]] = array('weight' => $rpWeight[$index]);
		}
		// 随机出结果
		$rpIDs = Util::noBackSample($rpArrayWeight, $num);

		/**************************************************************************************************************
 		 * 随机出部队
 		 **************************************************************************************************************/
		$armyArray = btstore_get()->COPY_ACT[$actID]['army_array'];
		$armyWeight = btstore_get()->COPY_ACT[$actID]['army_array_weight'];
		// 权重数组
		$armyArrayWeight = array();
		// 设置权重
		for ($index = 0; $index < count($armyArray); ++$index)
		{
			$armyArrayWeight[$armyArray[$index]] = array('weight' => $armyWeight[$index]);
		}
		// 随机出结果 (放回抽样，有可能重复)
		$armyIDs = Util::BackSample($armyArrayWeight, $num);

		/**************************************************************************************************************
 		 * 拼凑下
 		 **************************************************************************************************************/
		$armyInfo = array();
		for ($index = 0; $index < $num; ++$index)
		{
			$armyInfo[] = array('refreshPoint' => $rpIDs[$index], 'enemyID' => $armyIDs[$index]);
			Logger::trace('The refresh resault is refreshPoint %d, enemyID %d.', $rpIDs[$index], $armyIDs[$index]);
		}
		Logger::debug('Refresh end.');
		// 返回结果
		return $armyInfo;
	}

	/**
	 * 服务器刷新时，清空服务器攻击部队记录表
	 * @param array $armyInfo					需要清空这些部队的被A记录
	 */
	private static function resetServerDefeat($armyInfo)
	{
		// 循环查看所有部队
		foreach ($armyInfo as $key => $enemy)
		{
			// 获取部队攻击次数所属
			$belong = btstore_get()->ARMY[$enemy['enemyID']]['belong'];
			Logger::debug('The army %d belongs to %s', $enemy['enemyID'], $belong);
			// 如果属于阵营级
			if ($belong == CopyConf::BELONG_GROUP)
			{
				// 清空所有阵营的数据
				for ($groupID = 1; $groupID <= CopyConf::ALL_GROUPS; ++$groupID)
				{
					// 把所有部队清空 —— 如果没有部队信息，那么就插一条空的
					CopyDao::clearServerDefeatNum($enemy['enemyID'], $enemy['refreshPoint'], $groupID);
				}
			}
			// 如果属于所有
			else
			{
				// 把所有部队清空 —— 如果没有部队信息，那么就插一条空的
				CopyDao::clearServerDefeatNum($enemy['enemyID'], $enemy['refreshPoint'], 0);
			}
		}
	}

	/**
	 * 开始活动
	 * @param int $actID						活动ID
	 * @param int $nexTime						下次活动开始时刻
	 */
	private static function startAct($actID, $nexTime)
	{
		Logger::trace('Next start time is %d.', $nexTime);
		// 更新状态
		AllActivities::getInstance()->updActStatus($actID, CopyDef::BEGIN);
		// 更新时刻
		AllActivities::getInstance()->updActRefreshTime($actID, $nexTime);
		// 刷新点儿部队啥的
		$armyInfo = self::refreshEnemy($actID);
		// 将刷新结果保存
		AllActivities::getInstance()->updActRefreshPoints($actID, $armyInfo);
		// 将服务器攻击次数清零
		self::resetServerDefeat($armyInfo);
		// 返回活动开始的消息， 像不像烽火台啊？
		return 'IN';
	}

	/**
	 * 调整刷新时刻，如果到了刷新时刻，则进行刷新动作
	 * @param int $actID						活动Id
	 */
	private static function adjustRefreshTime($actID)
	{
		// 获取活动信息
		$actInfo = AllActivities::getInstance()->getActivityInfo($actID);
		// 如果数据库没有取到这个活动信息，则需要插入一条新数据
		if ($actInfo === false)
		{
			// 把时间设置为下次开始的时刻
			$nextTime = Activity::getNextStartTime($actID);
			Logger::trace('New Activity, start time is %d.', $nextTime);
			// 如果活动已经结束了
			if (empty($nextTime))
			{
				return 'QUIET';
			}
			// 插入一条空数据,添加个新活动
			AllActivities::getInstance()->addNewActivity($actID, $nextTime);
			// 这种场合不存在刷新时刻
			$actInfo['next_refresh_time'] = $nextTime;
			// 此场合，状态值为 1
			$actInfo['status'] = CopyDef::INIT;
		}
		Logger::debug('The next_refresh_time of No. %d activity is %d. means %s', 
		              $actID, $actInfo['next_refresh_time'], date("Ymd-His-w", $actInfo['next_refresh_time']));


		// 调整刷新怪物的时间, 如果当前活动并未举行, 则需要清空数组，并置状态
		if (!self::inTimeScale($actID))
		{
			// 检查状态, 如果不为静止状态，需要设置为静止状态了
			if ($actInfo['status'] != CopyDef::QUIET)
			{
				// 把时间设置为下次开始的时刻
				$nextTime = Activity::getNextStartTime($actID);
				Logger::trace('Next start time is %d.', $nextTime);
				// 更新时刻
				AllActivities::getInstance()->updActRefreshTime($actID, $nextTime);
				// 更新状态
				AllActivities::getInstance()->updActStatus($actID, CopyDef::QUIET);
				$ret = 'OUT';
			}
			// 最近一直都没进活动啊，别紧张
			else
			{
				// 别再执行下面的动作了，给个标识，告诉上层，活动不执行了
				$ret = 'QUIET';
			}
		}
		// 如果此活动正在进行，那么需要调整时间
		else
		{
			// 记录下当前时间
			$curTime = Util::getTime();
			// 获取刷新间隔（秒）
			$interval = btstore_get()->COPY_ACT[$actID]['interval'];
			Logger::debug('Currant time is %d, Refresh time is %d.', $curTime, $actInfo['next_refresh_time']);
			// 记录的刷新时间小于现在的时刻，需要重新刷新
			if ($interval != 0 && $actInfo['next_refresh_time'] < $curTime)
			{
				// 换算下次刷新时刻
				while ($actInfo['next_refresh_time'] < $curTime)
				{
					$actInfo['next_refresh_time'] += $interval;
				}
				// 进入活动了啊，运气不错
				$ret = self::startAct($actID, $actInfo['next_refresh_time']);
			}
			// 一次性启动的活动, 如果还没启动的话
			else if ($interval == 0 && $actInfo['status'] != CopyDef::BEGIN)
			{
				// 把时间设置为下次开始的时刻
				$nextTime = Activity::getNextStartTime($actID);
				// 进入活动, 虽然可能迟了些
				$ret = self::startAct($actID, $nextTime);
			}
			// 1. 刷新时间还没到，表明正常的活动着呢。好像啥都不用干？？
			// 2. 时间没到或者是一次性活动已经启动了
			else
			{
				// 活动中，请勿打扰
				$ret = 'ING';
			}
		}
		Logger::debug('The ret of adjustRefreshTime is %s.', $ret);
		// 返回函数执行结果
		return $ret;
	}

	/**
	 * 根据刷新点ID和当前时间，判断活动是否应该正在举行
	 * @param int $actID						活动ID
	 */
	private static function inTimeScale($actID)
	{
		/**************************************************************************************************************
 		 * 获取系统当前时刻
 		 **************************************************************************************************************/
		// 记录下当前时间
		$curTime = Util::getTime();
		// 记录下当天日期
		$curDate = date("d", $curTime);
		// 记录下当天星期
		$curWeek = date("w", $curTime);

		/**************************************************************************************************************
 		 * 获取刷新点的刷新时刻
 		 **************************************************************************************************************/
		// 获取当日刷新的开始
		$dStart = btstore_get()->COPY_ACT[$actID]['day_start'];
		// 获取当日刷新的截止时刻
		$dEnd = btstore_get()->COPY_ACT[$actID]['day_end'];
		// 获取刷新间隔（秒）
		$interval = btstore_get()->COPY_ACT[$actID]['interval'];
		// 获取刷新总时间段的开始
		$yStart = btstore_get()->COPY_ACT[$actID]['year_start'];
		// 获取刷新总时间段的截止
		$yEnd = btstore_get()->COPY_ACT[$actID]['year_end'];
		// 获取每个月有哪些日子需要刷新
		$dayList = btstore_get()->COPY_ACT[$actID]['day_list'];
		// 获取每个星期有哪些日子需要刷新
		$weekList = btstore_get()->COPY_ACT[$actID]['week_list'];

		/**************************************************************************************************************
 		 * 比较每日的时间段
 		 **************************************************************************************************************/
		// 先进行每天时刻的比较，因为这个是必填的
		// 先检查下，万一没读到信息，就不太好了。
		if (empty($dStart) || empty($dEnd))
		{
			Logger::warning("Can not read %d activity 's refresh time.", $actID);
			return false;
		}
		// 将开始时刻转化为时间戳
		$dayStart = mktime($dStart['hour'], $dStart['min'], $dStart['sec']);
		// 将截止时刻转化为时间戳
		$dayEnd = mktime($dEnd['hour'], $dEnd['min'], $dEnd['sec']);
		// 如果当前时间在时间段之外
		if ($curTime < $dayStart || $curTime > $dayEnd)
		{
			Logger::debug('out of refresh range.');
			return false;
		}

		/**************************************************************************************************************
 		 * 判断总刷新时间段
 		 **************************************************************************************************************/
		// 判断刷新总时间段是否为空
		if (!empty($yStart) && !empty($yEnd))
		{
			// 将开始时刻转化为时间戳
			$yearStart = mktime($yStart['hour'], $yStart['min'], $yStart['sec'], $yStart['mon'], $yStart['day'], $yStart['year']);
			// 将截止时刻转化为时间戳
			$yearEnd = mktime($yEnd['hour'], $yEnd['min'], $yEnd['sec'], $yEnd['mon'], $yEnd['day'], $yEnd['year']);
			// 如果当前时间在时间段之外
			if ($curTime < $yearStart || $curTime > $yearEnd)
			{
				Logger::debug('out of refresh range.');
				return false;
			}
		}

		/**************************************************************************************************************
 		 * 判断日期
 		 **************************************************************************************************************/
		if (!empty($dayList))
		{
			// 转化为数组先
			$dayList = $dayList->toArray();
			// 如果当前日期不在这个时间段内
			if (!in_array($curDate, $dayList))
			{
				Logger::debug('out of refresh range.');
				return false;
			}
		}

		/**************************************************************************************************************
 		 * 判断星期
 		 **************************************************************************************************************/
		if (!empty($weekList))
		{
			// 转化为数组先
			$weekList = $weekList->toArray();
			// 如果当前日期不在这个时间段内
			if (!in_array($curWeek, $weekList))
			{
				Logger::debug('out of refresh range.');
				return false;
			}
		}

		Logger::debug('OK, in refresh range.');
		// 如果在活动时间范围内，则需要重新随机
		return true;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */