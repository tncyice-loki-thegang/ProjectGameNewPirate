<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : TalksLogic
 * Description : 会谈实现类
 * Inherit     : 
 **********************************************************************************************************************/
class TalksLogic
{
	/**
	 * 获取用户的会谈信息
	 */
	public static function getUserTalksInfo()
	{
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		// 根据用户ID获取会谈信息
		$talksInfo = TalksDao::getTalksInfo($uid);
		// 如果会谈信息为空
		if ($talksInfo === false)
		{
			Logger::debug('Open talks window.');
			// 初始化人物会谈信息
			$talksInfo = self::initUserTalksInfo($uid);
		}
		// 更新刷新信息
		$talksInfo = self::getTodayRefreshTimes($talksInfo, $uid);
		// 返回更新后信息
		return self::getTodayTalksTimes($talksInfo, $uid);
	}

	/**
	 * 初始化用户会谈数据
	 * 
	 * @param int $uid							用户ID
	 */
	protected static function initUserTalksInfo($uid)
	{
		// 初始化人物会谈信息
		$talksInfo = array('uid' => $uid,
					 	   'talk_times' => 0,
						   'talk_accumulate' => 0,
		             	   'talk_date' => Util::getTime(),
					 	   'refresh_times' => 0,
		             	   'refresh_date' => 0,
		             	   'open_free_mode' => 0,
					 	   'va_talks_info' => array('talk_win' => array(),
		                                      	    'out_heros' => array()),
					 	   'status' => DataDef::NORMAL);
		// 获取用户的等级
		$lv = EnUser::getUserObj()->getLevel();
		// 获取随机数组
		$randArr = self::getRandEvent($talksInfo, $lv);
		// 进行抽样
		$randID = self::getOnlyEvent($randArr, $talksInfo, $lv);
		// 修改数据库
		$talksInfo['va_talks_info']['talk_win'][1] = $randID;
		Logger::debug('Open new window, window id is 1, event id is %d.', $randID);
		// 插入数据库
		TalksDao::addNewTalksInfo($talksInfo);
		// 返回上层
		return $talksInfo;
	}

	/**
	 * 根据用户等级，获取可以会谈的最大次数
	 * 
	 * @param int $level						用户等级
	 */
	private static function __getUserTalksTimesPerDay($level)
	{		
		// 获取该用户可以进行的会谈次数
		foreach (TalksConf::$MAX_TALK_TIMES as $lv => $times)
		{
			// 查看等级,如果大于这个等级档，就直接退出
			if ($level > $lv)
			{
				break;
			}
			// 记录次数
			$num = $times;
		}
		// 返回次数
		return $num;
	}

	/**
	 * 会谈
	 * 
	 * @param int $winID						窗口ID
	 * @throws Exception
	 */
	public static function startTalks($winID) 
	{
		/**************************************************************************************************************
 		 * 检查会谈条件
 		 **************************************************************************************************************/
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		// 获取用户等级信息
		$user = EnUser::getUserObj();
		// 获取该用户可以进行的会谈次数
		$num = self::__getUserTalksTimesPerDay($user->getLevel());
		// 获取用户的当日会谈信息
		$talksInfo = TalksDao::getTalksInfo($uid);
		// 如果没获取到，就异常
		if ($talksInfo === false)
		{
			Logger::fatal('Can not find talks info of this user %d.', $uid);
			throw new Exception('fake');
		}
		// 获取最新的会谈次数
		$talksInfo = self::getTodayTalksTimes($talksInfo, $uid);
		// 进行判断
		if ($talksInfo['talk_times'] >= $num && $talksInfo['talk_accumulate'] <= 0)
		{
			Logger::warning('Can not start talks, today times is full %d, accumulate is %d.', 
							$talksInfo['talk_times'], $talksInfo['talk_accumulate']);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 执行上次事件，并给予好处
 		 **************************************************************************************************************/
		// 如果事件ID为空
		if (empty($talksInfo['va_talks_info']['talk_win'][$winID]))
		{
			Logger::fatal('Can not find eventID in this window %d, talks info is %s.', $$winID, $talksInfo);
			throw new Exception('fake');
		}		
		// 不为空的情况下, 获取当前事件ID
		$eventID = $talksInfo['va_talks_info']['talk_win'][$winID];
		// 获取事件
		$event = btstore_get()->TALKS_EVENT[$eventID];
		// 返回用的背包信息
		$bagInfo = array();
		// 如果是英雄事件
		if ($event['type'] == TalksConf::HERO_TYPE)
		{
			// 如果用户还没有这个英雄
			if (!$user->hasHero($event['hero_id']))
			{
				// 放到酒馆里
				$user->addNewHeroToPub($event['hero_id']);
				// 广播
				ChatTemplate::sendTalkHero($user->getTemplateUserInfo(), $event['hero_id']);
			}
			// 记录下英雄ID，以后不再出现了
			$talksInfo['va_talks_info']['out_heros'][] = $event['hero_id'];
		}
		// 普通事件
		else 
		{
			// 增加游戏币 —— 注意，注意，游戏币需要乘以等级
			$user->addBelly($event['belly'] * $user->getLevel());
			// 增加金币
			$user->addGold($event['gold']);
			// 增加阅历 —— 注意，注意，阅历需要乘以等级
			$user->addExperience($event['experience'] * $user->getLevel());
			// 增加声望
			$user->addPrestige($event['prestige']);
			// 声明背包信息，掉落到背包里
			if ($event['item_id'] != 0 && $event['item_num'] != 0)
			{
				// 生成物品
				$itemIDs = ItemManager::getInstance()->addItem($event['item_id'], $event['item_num']);
				// 记录发送的信息
				$msg = chatTemplate::prepareItem($itemIDs);
				// 压入背包
				$bag = BagManager::getInstance()->getBag();
				$bag->addItems($itemIDs, TRUE);
				$bagInfo = $bag->update();
				// 发送信息
				chatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
			}
		}

		/**************************************************************************************************************
 		 * 刷新并返回
 		 **************************************************************************************************************/
		// 获取随机数组
		$randArr = self::getRandEvent($talksInfo, $user->getLevel());
		// 进行抽样
		$randID = self::getOnlyEvent($randArr, $talksInfo, $user->getLevel());
		// 设置事件ID
		$talksInfo['va_talks_info']['talk_win'][$winID] = $randID;
		// 先判断次数，如果没有次数了，需要从累积的部分进行处理
		if ($talksInfo['talk_times'] < $num)
		{
			++$talksInfo['talk_times'];
		}
		// 如果次数不对，那么减去累积的次数
		else 
		{
			--$talksInfo['talk_accumulate'];
		}
		// 记录会谈时刻
		$talksInfo['talk_date'] = Util::getTime();
		// 修改数据库
		TalksDao::updTalksInfo($uid, $talksInfo);
		$user->update();
		// 发送金币通知
		if (!empty($event['gold']))
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_TALKS_TALK, $event['gold'], Util::getTime(), FALSE);
		}
		// 通知任务系统，会谈了
		TaskNotify::operate(TaskOperateType::CHAT);
		// 通知活跃度系统
		EnActive::addTalksTimes();
		// 通知节日系统
		EnFestival::addTalkPoint();

		// 将新的事件ID返回给前端
		return array('id' => $randID, 'bagInfo' => $bagInfo);
	}

	/**
	 * 刷新一个窗口
	 * 
	 * @param int $winID						窗口ID
	 */
	public static function refresh($winID) 
	{
		/**************************************************************************************************************
 		 * 检查刷新条件
 		 **************************************************************************************************************/
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		// 获取用户等级信息
		$user = EnUser::getUserObj();
		// 获取用户的会谈信息
		$talksInfo = TalksDao::getTalksInfo($uid);
		// 如果没获取到，就异常
		if ($talksInfo === false)
		{
			Logger::fatal('Can not find talks info of this user %d.', $uid);
			throw new Exception('fake');
		}
		// 检查金币是否足够
		if ($talksInfo['open_free_mode'] != TalksConf::FREE_MODE && $user->getGold() < TalksConf::REFRESH_GOLD)
		{
			Logger::fatal('Gold not enough, refresh needs %d, user have now %d.',
			              TalksConf::REFRESH_GOLD, $user->getGold());
			throw new Exception('fake');
		}
		// 检查参数
		if ($winID > count($talksInfo['va_talks_info']['talk_win']) || $winID <= 0)
		{
			Logger::fatal('Can not find this window id %d.', $winID);
			throw new Exception('fake');
		}
		// 获取最新的刷新次数
		$talksInfo = self::getTodayRefreshTimes($talksInfo, $uid);
		// 进行判断
		if ($talksInfo['open_free_mode'] != TalksConf::FREE_MODE && 
		    btstore_get()->VIP[$user->getVip()]['talks_refresh_times'] != 0 &&
			$talksInfo['refresh_times'] >= btstore_get()->VIP[$user->getVip()]['talks_refresh_times'])
		{
			Logger::fatal('Can not refresh, today times is full %d.', $talksInfo['refresh_times']);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 刷新并返回
 		 **************************************************************************************************************/
		// 获取随机数组
		$randArr = self::getRandEvent($talksInfo, $user->getLevel());
		// 进行抽样
		$randID = self::getOnlyEvent($randArr, $talksInfo, $user->getLevel());
		// 记录事件ID
		$talksInfo['va_talks_info']['talk_win'][$winID] = $randID;
		// 增加刷新次数
		++$talksInfo['refresh_times'];
		$talksInfo['refresh_date'] = Util::getTime();
		// 更新数据库
		TalksDao::updTalksInfo($uid, $talksInfo);

		// 富人不需要给钱啊……
		if ($talksInfo['open_free_mode'] != TalksConf::FREE_MODE)
		{
			// 减少消耗金币
			Logger::trace('The gold of cur user is %s, need gold is %s.', $user->getGold(), TalksConf::REFRESH_GOLD);
			EnUser::getInstance()->subGold(TalksConf::REFRESH_GOLD);
			EnUser::getInstance()->update();
			// 发送金币通知
			Statistics::gold(StatisticsDef::ST_FUNCKEY_TALKS_REFRESH, TalksConf::REFRESH_GOLD, Util::getTime());
		}

		// 将事件ID返回给前端
		return $randID;
	}

	/**
	 * 刷新所有窗口
	 */
	public static function refreshAll() 
	{
		/**************************************************************************************************************
 		 * 检查刷新条件
 		 **************************************************************************************************************/
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		// 获取用户等级信息
		$user = EnUser::getUserObj();
		// 检查用户等级 和 vip 需求
		if (empty(btstore_get()->VIP[$user->getVip()]['talks_refresh']))
		{
			Logger::fatal('Can not find user in session or user level (vip level) not enough.');
			throw new Exception('fake');
		}
		// 获取用户的会谈信息
		$talksInfo = TalksDao::getTalksInfo($uid);
		// 如果没获取到，就异常
		if ($talksInfo === false)
		{
			Logger::fatal('Can not find talks info of this user %d.', $uid);
			throw new Exception('fake');
		}
		// 获取窗口个数
		$winNum = count($talksInfo['va_talks_info']['talk_win']);
		// 检查金币是否足够
		if ($talksInfo['open_free_mode'] != TalksConf::FREE_MODE && $user->getGold() < TalksConf::REFRESH_GOLD * $winNum)
		{
			Logger::trace('Gold not enough, refresh needs %d, user have now %d.',
			              TalksConf::REFRESH_GOLD * $winNum, $user->getGold());
			throw new Exception('fake');
		}
		// 获取最新的刷新次数
		$talksInfo = self::getTodayRefreshTimes($talksInfo, $uid);
		// 进行判断 (次数需要加上窗口个数进行判断)
		if ($talksInfo['open_free_mode'] != TalksConf::FREE_MODE && 
		    btstore_get()->VIP[$user->getVip()]['talks_refresh_times'] != 0 &&
			$talksInfo['refresh_times'] + $winNum > btstore_get()->VIP[$user->getVip()]['talks_refresh_times'])
		{
			Logger::fatal('Can not refresh, today times is full %d.', $talksInfo['refresh_times']);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 刷新并返回
 		 **************************************************************************************************************/
		// 声明返回值
		$ret = array();
		// 循环所有的窗口
		for ($i = 1; $i <= $winNum; ++$i)
		{
			// 获取随机数组
			$randArr = self::getRandEvent($talksInfo, $user->getLevel());
			// 进行抽样
			$randID = self::getOnlyEvent($randArr, $talksInfo, $user->getLevel());
			// 修改数据库
			$talksInfo['va_talks_info']['talk_win'][$i] = $randID;
			// 记录下，返回给前端
			$ret[$i] = $randID;
		}
		// 增加刷新次数
		$talksInfo['refresh_times'] += $winNum;
		$talksInfo['refresh_date'] = Util::getTime();
		// 更新数据库
		TalksDao::updTalksInfo($uid, $talksInfo);

		// 有钱人真好
		if ($talksInfo['open_free_mode'] != TalksConf::FREE_MODE)
		{
			// 减少消耗金币
			Logger::trace('The gold of cur user is %s, need gold is %s.', $user->getGold(), TalksConf::REFRESH_GOLD * $winNum);
			EnUser::getInstance()->subGold(TalksConf::REFRESH_GOLD * $winNum);
			EnUser::getInstance()->update();
			// 发送金币通知
			Statistics::gold(StatisticsDef::ST_FUNCKEY_TALKS_REFRESHALL, TalksConf::REFRESH_GOLD * $winNum, Util::getTime());
		}

		// 将事件ID返回给前端
		return $ret;
	}

	/**
	 * 通过用户的会谈信息和已经求得的事件数组，随机出一个符合条件的(就是英雄事件是唯一的)事件
	 * 
	 * @param array $randArr					可以抽样的事件数组
	 * @param array $talksInfo					用户的会谈信息
	 * @param int $lv							用户等级
	 */
	protected static function getOnlyEvent($randArr, $talksInfo, $lv)
	{
		// 初始化下标值和重试次数
		$index = -1;
		$randTimes = 50;
		// 进行抽样, 并设置超时次数
		while (--$randTimes > 0)
		{
			// 如果数组空了的话
			if (count($randArr) < 1)
			{
				// 取普通事件进行随机，没有英雄事件了
				$randArr = self::getNormalTypeRandEvent($lv);
			}
			// 不空的话进行抽样
			$ret = Util::noBackSample($randArr, 1);
			$index = $ret[0];

			// 如果随机出的是英雄事件，那么需要查看是否重复
			if ($randArr[$index]['type'] == TalksConf::HERO_TYPE)
			{
				// 检查所有既存的窗口
				foreach ($talksInfo['va_talks_info']['talk_win'] as $eventID)
				{
					// 如果这两个事件重复了
					if ($eventID == $randArr[$index]['id'])
					{
						// 数组中删除一项
						unset($randArr[$index]);
						// 英雄事件不能重复，再随机一次
						continue 2;
					}
				}
			}
			// 检查完了，通过了，直接跳出循环
			break;
		}
		// 返回随机到事件的ID
		return $randArr[$index]['id'];
	}

	/**
	 * 获取今天的会谈次数
	 */
	protected static function getTodayTalksTimes($talksInfo, $uid)
	{
		// 如果上次会谈的时间是今天之前
		if (!Util::isSameDay($talksInfo['talk_date'], TalksConf::REFRESH_TIME))
		{
			// 获取用户当时最大的会谈次数
			$num = self::__getUserTalksTimesPerDay(EnUser::getUserObj($uid)->getLevel());
			// 获取相间隔的天数 —— 这里需要减一，因为有一天是需要根据最近一天的剩余次数算出来的
			$days = Util::getDaysBetween($talksInfo['talk_date'], TalksConf::REFRESH_TIME) - 1;
			// 重置次数
			$talksInfo['talk_date'] = Util::getTime();
			// 设置累积次数 —— 当日最大次数减去实际使用次数，累积起来 : modify by liuyang 12-12-05
			$talksInfo['talk_accumulate'] += ($num - $talksInfo['talk_times'] + $days * $num);
			// 判断是否累积超过了最大值
			if ($talksInfo['talk_accumulate'] > btstore_get()->TOP_LIMIT[TopLimitDef::TALKS_MAX_TIME] - $num)
			{
				// 如果超过了，就给最大值，不能再多给次数了
				$talksInfo['talk_accumulate'] = btstore_get()->TOP_LIMIT[TopLimitDef::TALKS_MAX_TIME] - $num;
			}
			$talksInfo['talk_times'] = 0;
			// 重置会谈次数
			TalksDao::updTalksInfo($uid, array('talk_date' => $talksInfo['talk_date'], 
											   'talk_accumulate' => $talksInfo['talk_accumulate'],
			                                   'talk_times' => $talksInfo['talk_times']));
		}
		// 返回次数
		return $talksInfo;
	}

	/**
	 * 获取今天的刷新次数
	 */
	protected static function getTodayRefreshTimes($talksInfo, $uid)
	{
		// 如果上次刷新的时间是今天之前
		if (!Util::isSameDay($talksInfo['refresh_date'], TalksConf::REFRESH_TIME))
		{
			// 重置次数
			$talksInfo['refresh_date'] = Util::getTime();
			$talksInfo['refresh_times'] = 0;
			// 重置刷新次数
			TalksDao::updTalksInfo($uid, array('refresh_date' => $talksInfo['refresh_date'], 
			                                   'refresh_times' => $talksInfo['refresh_times']));
		}
		// 返回次数
		return $talksInfo;
	}

	/**
	 * 获取可以随机的普通事件列表
	 * 
	 * @param int $lv							用户等级
	 */
	protected static function getNormalTypeRandEvent($lv)
	{
		// 随机抽选使用的返回值， 过滤后的
		$randArr = array();
		// 使用等级过滤普通条件
		foreach (btstore_get()->TALKS_EVENT as $event)
		{
			// 拿出普通事件，备用
			if ($event['open_lv'] <= $lv && $event['type'] == TalksConf::NORMAL_TYPE)
			{
				$randArr[] = $event->toArray();
			}
		}
		// 返回过滤结果
		return $randArr;
	}

	/**
	 * 获取可以随机的事件列表
	 * 
	 * @param array $talksInfo					用户会谈数据
	 * @param int $lv							用户等级
	 */
	protected static function getRandEvent($talksInfo, $lv)
	{
		// 初始化成普通事件
		$type = TalksConf::NORMAL_TYPE;
		// 先抽样，看是否是英雄事件
		$randRet = rand(0, TalksConf::LITTLE_WHITE_PERCENT);
		// 恩，英雄事件！
		if ($randRet < TalksConf::HERO_WEIGHT)
		{
			$type = TalksConf::HERO_TYPE;
		}
		Logger::debug('Rand event type ret is %d.', $type);

		// 保存普通事件
		$tmpArr = array();
		// 随机抽选使用的返回值， 过滤后的
		$randArr = array();
		// 使用等级过滤普通条件
		foreach (btstore_get()->TALKS_EVENT as $event)
		{
			// 只有等级允许的事件才可以被选中  而且，现在新加条件了，必须类型和前面抽中的类型相符才行
			if ($event['open_lv'] <= $lv && $event['type'] == $type)
			{
				if ($event['type'] == TalksConf::HERO_TYPE &&
			    	CopyLogic::isEnemyDefeated($event['army_id']) != 0 &&
			    	!in_array($event['hero_id'], $talksInfo['va_talks_info']['out_heros']))
				{
					$randArr[] = $event->toArray();
				}
				else if ($event['army_id'] == 0)
				{
					$randArr[] = $event->toArray();
				}
			}
			// 拿出普通事件，备用
			if ($event['open_lv'] <= $lv && $event['type'] == TalksConf::NORMAL_TYPE)
			{
				$tmpArr[] = $event->toArray();
			}
		}
		// 如果这个人不小心随到了英雄事件，而且还没有英雄的话，那么就只有用普通事件做备用了
		if (empty($randArr))
		{
			$randArr = $tmpArr;
		}
		Logger::debug('Rand array is %s.', $randArr);
		// 返回过滤结果
		return $randArr;
	}

	/**
	 * 获取展示用英雄列表
	 */
	public static function getHeroList()
	{
		// 获取用户等级信息
		$user = EnUser::getUserObj();
		// 获取用户的当日会谈信息
		$talksInfo = TalksDao::getTalksInfo($user->getUid());
		// 英雄数组
		$heroArr = array();
		// 使用等级过滤普通条件
		foreach (btstore_get()->TALKS_EVENT as $event)
		{
			// 只有等级允许的事件才可以被选中
			if ($event['open_lv'] <= $user->getLevel() && 
			    $event['type'] == TalksConf::HERO_TYPE &&
		    	CopyLogic::isEnemyDefeated($event['army_id']) != 0 &&
		    	(empty($talksInfo['va_talks_info']['out_heros']) ||
		    	 !in_array($event['hero_id'], $talksInfo['va_talks_info']['out_heros'])))
		    {
		    	// 返回英雄ID，用来展示用
				$heroArr[] = $event['hero_id'];
			}
		}
		// 返回英雄列表
		return $heroArr;	
	}

	/**
	 * 开启会谈免费模式
	 * 
	 * @throws Exception
	 */
	public static function openFreeMode()
	{
		/**************************************************************************************************************
 		 * 检查会谈信息
 		 **************************************************************************************************************/
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		// 获取用户等级信息
		$user = EnUser::getUserObj();
		// 检查用户等级
		if (empty(btstore_get()->VIP[$user->getVip()]['talks_free_mode']))
		{
			Logger::fatal('Can not find user in session or vip level not enough.');
			throw new Exception('fake');
		}
		// 获取用户的当日会谈信息
		$talksInfo = TalksDao::getTalksInfo($uid);
		// 如果没获取到，就异常
		if ($talksInfo === false)
		{
			Logger::fatal('Can not find talks info of this user %d.', $uid);
			throw new Exception('fake');
		}
		// 这么有钱就别添乱了啊，一次一次的。真要那么有钱，帮刘洋开启一下啊！
		if ($talksInfo['open_free_mode'] == TalksConf::FREE_MODE)
		{
			return 'ok';
		}

		/**************************************************************************************************************
 		 * 开启免费模式
 		 **************************************************************************************************************/
		// 获取开启所需的金币
		$gold = btstore_get()->VIP[$user->getVip()]['talks_free_mode'];
		// 金币检查
		if ($user->getGold() < $gold)
		{
			Logger::fatal('Can not open talks free mode, user vip level is %d, gold is %d.', $user->getVip(), $gold);
			throw new Exception('fake');
		}
		// 开启免费模式
		$talksInfo['open_free_mode'] = TalksConf::FREE_MODE;
		// 修改数据库
		TalksDao::updTalksInfo($uid, $talksInfo);
		// 扣款了 
		$user->subGold($gold);
		Logger::debug('Open free mode, sub gold %d.', $gold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_TALKS_FREEMODE, $gold, Util::getTime());

		return 'ok';
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */