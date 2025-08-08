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
 * Class       : Activity
 * Description : 活动类，获取下次活动开始时刻
 * Inherit     :
 **********************************************************************************************************************/
class Activity
{
	/**
	 * 增加些日期
	 * @param int $curTime							当前时刻
	 * @param int $days								想增加的日数
	 */
	public static function getPlusDays($curTime, $days)
	{
		$curTime = $curTime + 86400 * $days;
		// 记录下当天 年份
		$curYear = date("Y", $curTime);
		// 记录下当天 月份
		$curMon = date("m", $curTime);
		// 记录下当天日期
		$curDate = date("d", $curTime);
		// 返回计算好的值
		return array('year' =>$curYear, 'mon' => $curMon, 'day' => $curDate);
	}

	/**
	 * 获取下一天的开始时刻
	 * @param int $curTime							当前时刻
	 */
	public static function getNextDayStart($curTime)
	{
		// 加一天
		$curTime = $curTime + 86400;
		// 记录下当天 年份
		$curYear = date("Y", $curTime);
		// 记录下当天 月份
		$curMon = date("m", $curTime);
		// 记录下当天日期
		$curDate = date("d", $curTime);
		// 返回时刻
		return mktime('00', '00', '00', $curMon, $curDate, $curYear);
	}

	/**
	 * 根据配置，获取下一天，如果得不到下一天，那么返回当日
	 * @param string $curTime						当前时刻
	 * @param array $dayList						设定好的日期数组
	 * @param array $weekList						设定好的星期数组
	 */
	private static function getNextDay($curTime, $dayList, $weekList)
	{
		// 每次往后看一天，进行计算
		for ($i = $curTime; ; $i += 86400)
		{
			// 记录下当天 年份
			$curYear = date("Y", $i);
			// 记录下当天 月份
			$curMon = date("m", $i);
			// 记录下当天日期
			$curDate = date("d", $i);
			// 记录下当天星期
			$curWeek = date("w", $i);

			// 如果两个都不为空，则需要重叠判断
			if (!empty($dayList) && !empty($weekList))
			{
				// 如果恰好合适，就返回计算好的日期
				if (in_array($curDate, $dayList) && in_array($curWeek, $weekList))
				{
					return array('year' =>$curYear, 'mon' => $curMon, 'day' => $curDate);
				}
				// 不然就加一天
			}
			// 如果只设置了日期
			else if (!empty($dayList))
			{
				foreach ($dayList as $day)
				{
					if ($day >= $curDate && $day <= date('t', $i))
					{
						// 需要加上天数啊！
						return self::getPlusDays($i, $day - $curDate);
					}
				}
				// 如果循环一遍都没找到，这时候就比较麻烦了，要到下个月看看了
			}
			// 如果只设置了星期
			else if (!empty($weekList))
			{
				foreach ($weekList as $week)
				{
					// 当前的星期大于配置星期，那么看下一个
					if ($week >= $curWeek)
					{
						// 需要加上天数啊！
						return self::getPlusDays($i, $week - $curWeek);
					}
				}
				// 如果循环一遍都没找到，这时候就比较麻烦了，要到下周看看了
			}
			else
			{
				// 如果都没设置，则返回当前日期够用了
				return array('year' =>$curYear, 'mon' => $curMon, 'day' => $curDate);
			}
		}
	}

	/**
	 * 获取下次活动开始的时刻
	 * @param int $actID						活动ID
	 */
	public static function getNextStartTime($actID)
	{
		$nextTime = '';
		$nextYear = '';
		$nextDay = '';

		/******************************************************************************************************************
	 	 * 获取系统当前时刻
	 	 ******************************************************************************************************************/
		// 记录下当前时间
		$curTime = Util::getTime();
		// 记录下当天 年份
		$curYear = date("Y", $curTime);
		// 记录下当天 月份
		$curMon = date("m", $curTime);
		// 记录下当天日期
		$curDate = date("d", $curTime);
		// 记录下当天星期
		$curWeek = date("w ", $curTime);

		/******************************************************************************************************************
		 * 获取刷新点的刷新时刻
	 	 ******************************************************************************************************************/
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
		if (!empty($dayList))
		{
			$dayList = $dayList->toArray();
		}
		// 获取每个星期有哪些日子需要刷新
		$weekList = btstore_get()->COPY_ACT[$actID]['week_list'];
		if (!empty($weekList))
		{
			$weekList = $weekList->toArray();
		}
		// 先检查下，万一没读到信息，就不太好了。
		if (empty($dStart) || empty($dEnd))
		{
			Logger::fatal("Can not read %d activity 's refresh time.", $actID);
			throw new Exception('fake');
		}
		Logger::debug('The start time of day is %s', $dStart['hour'].$dStart['min'].$dStart['sec']);
		Logger::debug('The end time of day is %s', $dEnd['hour'].$dEnd['min'].$dEnd['sec']);

		/******************************************************************************************************************
		 * 判断总刷新时间段
	 	 ******************************************************************************************************************/
		// 判断刷新总时间段是否为空
		if (!empty($yStart) && !empty($yEnd))
		{
			// 将开始时刻转化为时间戳
			$yearStart = mktime($yStart['hour'], $yStart['min'], $yStart['sec'], $yStart['mon'], $yStart['day'], $yStart['year']);
			// 将截止时刻转化为时间戳
			$yearEnd = mktime($yEnd['hour'], $yEnd['min'], $yEnd['sec'], $yEnd['mon'], $yEnd['day'], $yEnd['year']);
			// 如果过了总时间段, 那就直接返回，不用计算了
			if ($curTime > $yearEnd)
			{
				return 0;
			}
			else if ($curTime < $yearStart)// 离开始时间尚早呢
			{
				//使用开始年份
				$nextDay = self::getNextDay($yearStart, $dayList, $weekList);
				return mktime($dStart['hour'], $dStart['min'], $dStart['sec'], $nextDay['mon'], $nextDay['day'], $nextDay['year']);
			}
		}

		/******************************************************************************************************************
		 * 比较每日的时间段
	 	 ******************************************************************************************************************/
		// 将开始时刻转化为时间戳
		$dayStart = mktime($dStart['hour'], $dStart['min'], $dStart['sec']);
		// 将截止时刻转化为时间戳
		$dayEnd = mktime($dEnd['hour'], $dEnd['min'], $dEnd['sec']);
		// 在活动时间范围内, 且间隔大于0
		if ($curTime > $dayStart && $curTime < $dayEnd && $interval != 0)
		{
			// 初始化，从开始从头算
			$nextTime = $dayStart;
			// 换算下次刷新时刻
			while ($nextTime < $curTime)
			{
				$nextTime += $interval;
			}
			// 如果没超出
			if ($nextTime <= $dayEnd)
			{
				// 先换算回来
				$nextTime = array('hour' => date("H", $nextTime), 'min' => date("i", $nextTime), 'sec' => date("s", $nextTime));
			}
			// 超了就不一样了
			else
			{
				// 用一天活动开始的时刻
				$nextTime = array('hour' => $dStart['hour'], 'min' => $dStart['min'], 'sec' => $dStart['sec']);
				// 将日期往后挪移一天，反正今天是玩儿不了了
				$curTime = self::getNextDayStart($curTime);
			}
		}
		// 如果当前时间在时间段之外
		else
		{
			// 用一天活动开始的时刻. 下次的开始时间，就是这个了
			$nextTime = array('hour' => $dStart['hour'], 'min' => $dStart['min'], 'sec' => $dStart['sec']);
			// 将日期往后挪移一天，反正今天是玩儿不了了
			$curTime = self::getNextDayStart($curTime);
		}

		/******************************************************************************************************************
		 * 获取日期
	 	 ******************************************************************************************************************/
		$nextDay = self::getNextDay($curTime, $dayList, $weekList);
		// 返回计算结果
		return mktime($nextTime['hour'], $nextTime['min'], $nextTime['sec'], $nextDay['mon'], $nextDay['day'], $nextDay['year']);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */