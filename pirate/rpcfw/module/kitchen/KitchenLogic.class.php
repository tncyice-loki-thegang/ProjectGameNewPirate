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
 * Class       : KitchenLogic
 * Description : 厨房实现类, 本类没有数据缓冲
 * Inherit     : 
 **********************************************************************************************************************/
class KitchenLogic
{

	/**
	 * 获取用户订单信息
	 * 
	 * @param int $uid							用户ID
	 */
	public static function getUserOrderInfo($uid)
	{
		// 完全不用告诉前端，哈哈，其实两个方法时用的同一种实现方式 —— 这就是传说中的偷懒么？
		$orderInfo = self::getUserKitchenInfo($uid);
		if ($orderInfo == 'err')
		{
			return $orderInfo;
		}
		// 这个方法，前端不需要VA字段，删掉
		unset($orderInfo['va_kitchen_info']);
		// 获取目标用户的厨房等级
		$boatObj = EnSailboat::getUserBoat($uid);
		// 厨房等级
		$kitchenLv = $boatObj['va_boat_info']['cabin_id_lv'][SailboatDef::KITCHEN_ID]['level'];
		// 给前端加入厨房等级
		$orderInfo['kitchen_Lv'] = $kitchenLv;
		// 返回给前端
		return $orderInfo;
	}

	/**
	 * 获取用户厨房信息
	 * 
	 * @param int $uid							用户ID
	 */
	public static function getUserKitchenInfo($uid = 0)
	{
		// 判断uid 
		if ($uid === 0)
		{
			// 如果传入的uid 为0，则需从session里面获取uid
			$uid = RPCContext::getInstance()->getSession('global.uid');
		}
		// 获取厨房信息
		$kitchenInfo = KitchenDao::getKitchenInfo($uid);
		// 判断是否为空
		if ($kitchenInfo === false)
		{
			// 如果不是当前人物的厨房，那么还没开启就更不应该取这个数据了
			if ($uid != RPCContext::getInstance()->getUid())
			{
// 新加好友订单查询，如果没开启，返回空  2012/07/16
//				Logger::fatal('Can not get user kitchen info, user id is %d!', $uid);
				return 'err';
			}
			// 检查用户是否完成相应任务
			if (EnSwitch::isOpen(SwitchDef::KITCHEN))
			{
				Logger::debug('Open kitchen cabin.');
				// 初始化人物厨房信息
				$kitchenInfo = KitchenDao::addNewKitchenInfo($uid);
				// 删掉不用内容
				unset($kitchenInfo['status']);
			}
			// 没完成的时候不能获取厨房数据
			else 
			{
				Logger::fatal('Can not get user kitchen info before task!');
				throw new Exception('fake');
			}
		}
		// 保存一个中间变量
		$tmp = $kitchenInfo;
		// 调整订单和被订单次数
		$kitchenInfo = self::adjustOrderTimes($kitchenInfo);
		// 始终坚持一个原则， 不改自己干的事情不要干 —— 现在不需要拉取别人的制作次数，只需要订单次数
		if ($uid == RPCContext::getInstance()->getUid())
		{
			// 调整时刻，如果过了一天，需要卖出所有的菜
			$kitchenInfo = self::getTodayCookTimes($kitchenInfo);
			// 如果厨房数据已经遭到过修改，那么需要统一更新一次数据库
			if ($tmp !== $kitchenInfo)
			{
				KitchenDao::updKitchenInfo($uid, $kitchenInfo);
			}
		}
		// 返回给前端
		return $kitchenInfo;
	}

	/**
	 * 获取今天的订单次数
	 * 
	 * @param array $orderInfo					用户厨房数据
	 */
	public static function adjustOrderTimes($kitchenInfo)
	{
		// 记录一下原始数据,当做 where 条件
		$be_order_times = $kitchenInfo['be_order_times'];
		$order_times = $kitchenInfo['order_times'];
		// 计算上次订单到现在经过了几次刷新
		$times = self::__checkOrderUpdateTime($kitchenInfo['order_date']);
		Logger::debug("__checkOrderUpdateTime ret is %d.", $times);

		// 如果大于一次，就进行更新
		if ($times > 0)
		{
			// 计算累积次数
			$kitchenInfo['order_accumulate'] += btstore_get()->KITCHEN['orders_per_day'] * ($times - 1);
			// 如果今天还有剩余次数，需要加上
			if (btstore_get()->KITCHEN['orders_per_day'] - $order_times > 0)
			{
				$kitchenInfo['order_accumulate'] += btstore_get()->KITCHEN['orders_per_day'] - $order_times;
			}
			// 如果加超了，那么取最大值
			if ($kitchenInfo['order_accumulate'] > 
				btstore_get()->TOP_LIMIT[TopLimitDef::ORDER_MAX_TIME] - btstore_get()->KITCHEN['orders_per_day'])
			{
				$kitchenInfo['order_accumulate'] = 
				btstore_get()->TOP_LIMIT[TopLimitDef::ORDER_MAX_TIME] - btstore_get()->KITCHEN['orders_per_day'];
			}

			// 更新数据库
			$ret = KitchenDao::updOrderTimes($kitchenInfo['uid'], 0, 
																  0, 
																  $kitchenInfo['order_accumulate'],
																  $order_times, 
																  $be_order_times);
			// 如果更新失败(可能被竞争了)，那么需要重新取一下最新数据
			if ($ret['affected_rows'] === 0)
			{
				$kitchenInfo = KitchenDao::getKitchenInfo($kitchenInfo['uid']);
			}
			// 更新成功的话，需要修改一下数据
			else 
			{
				$kitchenInfo['order_date'] = Util::getTime();
				$kitchenInfo['be_order_times'] = 0;
				$kitchenInfo['order_times'] = 0;
			}
		}
		// 返回最新数据
		return $kitchenInfo;
	}

	/**
	 * 计算上次订单到现在经过了多少次刷新
	 * 
	 * @param int $orderTime					上次订单时间
	 */
	public static function __checkOrderUpdateTime($orderTime)
	{
		// 获取当前时刻
		$curTime = Util::getTime();
		// 获取当日日期
		$curYmd = date("Y-m-d ", $curTime);
		// 获取今天第一次刷新的时间 
		$curFirstTime = strtotime($curYmd.KitchenConf::$ORDER_RESET[0]);
		// 获取上次订单的第一次刷新时间
		$orderYmd = date("Y-m-d ", $orderTime);
		// 获取今天第一次刷新的时间 
		$orderFirstTime = strtotime($orderYmd.KitchenConf::$ORDER_RESET[0]);
		// 计算间隔的累加次数
		$times = ($curFirstTime - $orderFirstTime) / 86400 * count(KitchenConf::$ORDER_RESET);
		Logger::debug("__checkOrderUpdateTime curFirstTime is %d, orderFirstTime is %d.", $curFirstTime, $orderFirstTime);

		// 对次数进行调整
		foreach (KitchenConf::$ORDER_RESET as $rtime)
		{
			// 获取这次活动开始时刻
			$startTime = strtotime($orderYmd.$rtime);
			// 如果订单次数大于这个时间，那么就减一
			if ($orderTime > $startTime)
			{
				--$times;
			}
			// 获取这次活动开始时刻
			$startTime = strtotime($curYmd.$rtime);
			if ($curTime > $startTime)
			{
				++$times;
			}
		}
		// 返回次数
		return $times;
	}

	/**
	 * 获取今天的生产次数
	 * 
	 * @param array $orderInfo					用户厨房数据
	 */
	public static function getTodayCookTimes($kitchenInfo)
	{
		// 如果两者都小于当日，那么需要进行刷新
		if (!Util::isSameDay($kitchenInfo['cook_date']) && !Util::isSameDay($kitchenInfo['gold_cook_date']))
		{
			// 出售所有的货
			$kitchenInfo = self::sellAll(Util::getTime(), $kitchenInfo);
		}
		// 如果上次生产的时间是今天之前
		else if (!Util::isSameDay($kitchenInfo['cook_date']))
		{
			// 获取相间隔的天数 —— 这里需要减一，因为有一天是需要根据最近一天的剩余次数算出来的
			$days = Util::getDaysBetween($kitchenInfo['cook_date'], KitchenConf::REFRESH_TIME) - 1;
			// 设置清空时刻
			$kitchenInfo['cook_date'] = Util::getTime();
			// 设置累积次数 —— 当日最大次数减去实际使用次数，累积起来 : modify by liuyang 12-12-05
			$kitchenInfo['cook_accumulate'] += (btstore_get()->KITCHEN['cook_times_per_day'] - 
												$kitchenInfo['cook_times'] + $days * btstore_get()->KITCHEN['cook_times_per_day']);
			// 判断是否累积超过了最大值
			if ($kitchenInfo['cook_accumulate'] > 
				btstore_get()->TOP_LIMIT[TopLimitDef::KITCHEN_MAX_TIME] - btstore_get()->KITCHEN['cook_times_per_day'])
			{
				// 防止意外出现的错误数据
				$tmp = btstore_get()->TOP_LIMIT[TopLimitDef::KITCHEN_MAX_TIME] > btstore_get()->KITCHEN['cook_times_per_day'] ?
					   btstore_get()->TOP_LIMIT[TopLimitDef::KITCHEN_MAX_TIME] - btstore_get()->KITCHEN['cook_times_per_day'] : 0;
				// 如果超过了，就给最大值，不能再多给次数了
				$kitchenInfo['cook_accumulate'] = $tmp;
			}
			// 清空当日实际生产次数，迎接下一天的到来
			$kitchenInfo['cook_times'] = 0;
		}
		// 如果上次金币生产的时间是今天之前
		else if (!Util::isSameDay($kitchenInfo['gold_cook_date']))
		{
			// 清空金币生产次数
			$kitchenInfo['gold_cook_date'] = Util::getTime();
			$kitchenInfo['gold_cook_times'] = 0;
		}
		// 返回次数
		return $kitchenInfo;
	}

	/**
	 * 增加经验值, 返回, 不更新数据库！
	 * 
	 * @param array $orderInfo					用户厨房数据
	 * @param int $value						增加的经验值
	 */
	private static function addExp($kitchenInfo, $value)
	{
		// 增加经验值
		$kitchenInfo['exp'] += $value;
		// 查看是否已经可以升级了
		if ($kitchenInfo['exp'] >= btstore_get()->COOK_LV[$kitchenInfo['lv']])
		{
			// 如果经验值到了扣除升级经验
			$kitchenInfo['exp'] -= btstore_get()->COOK_LV[$kitchenInfo['lv']];
			// 提升升级
			++$kitchenInfo['lv'];
			// 通知成就系统
			EnAchievements::notify(self::getUid(), AchievementsDef::COOK_LEVEL, $kitchenInfo['lv']);
		}
		Logger::debug('Kitchen level is %d, exp is %d.', $kitchenInfo['lv'], $kitchenInfo['exp']);
		// 返回最新的数据
		return $kitchenInfo;
	}

	/**
	 * 更新制作次数, 返回, 不更新数据库！
	 * 
	 * @param array $kitchenInfo				用户厨房数据
	 * @param boolean $isGold					是否是金币制作
	 */
	private static function addCookTimes($kitchenInfo, $isGold)
	{
		// 获取当前时刻
		$curTime = Util::getTime();
		// 调整时刻，如果过了一天，需要卖出所有的菜
		$kitchenInfo = self::getTodayCookTimes($kitchenInfo);
		// 增加次数
		if ($isGold)
		{
			// 如果是金币制作，那么修改金币制作时间
			$kitchenInfo['gold_cook_date'] = $curTime;
			++$kitchenInfo['gold_cook_times'];
		}
		else 
		{
			// 如果是普通制作，那么修改普通制作时间
			$kitchenInfo['cook_date'] = $curTime;
			// 先判断次数，如果没有次数了，需要从累积的部分进行处理
			if ($kitchenInfo['cook_times'] < btstore_get()->KITCHEN['cook_times_per_day'])
			{
				++$kitchenInfo['cook_times'];
			}
			// 如果次数不对，那么减去累积的次数
			else 
			{
				--$kitchenInfo['cook_accumulate'];
			}
		}
		// 返回次数
		return $kitchenInfo;
	}

	/**
	 * 制作实现
	 * 
	 * @param array $kitchenInfo				用户厨房数据
	 * @param int $dishID						菜肴ID
	 * @param boolean $isCritical				暴击不暴击
	 * @param boolean $isGold					是否是金币制作
	 */
	private static function _cook($kitchenInfo, $dishID, $isCritical, $isGold)
	{
		/**************************************************************************************************************
 		 * 制作检查
		 **************************************************************************************************************/
		// 获取用户信息
		$userInfo = EnUser::getUser();
		// 制作该菜肴所需的厨艺等级
		if ($kitchenInfo['lv'] < btstore_get()->DISH[$dishID]['need_lv'])
		{
			Logger::fatal('Can not cook this dish, level not enough! Need level is %d, user is %d.', 
			              btstore_get()->DISH[$dishID]['need_lv'], $kitchenInfo['lv']);
			throw new Exception('fake');
		}
		// 该菜肴生产的基础成本价格
		if (!$isGold && $userInfo['belly_num'] < btstore_get()->DISH[$dishID]['base_value'])
		{
			Logger::fatal('Can not cook this dish, belly not enough! Need belly is %d, user is %d.',
			              btstore_get()->DISH[$dishID]['base_value'], $userInfo['belly_num']);
			throw new Exception('fake');
		}
		// 检查暴击消费
		if ($isCritical && $userInfo['gold_num'] < 1)
		{
			Logger::fatal('Can not critical, gold not enough!.');
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 进行制作
		 **************************************************************************************************************/
		$needGold = 0;
		// 获取产生的个数
		if ($isCritical)
		{
			// 暴击需要一块钱……哦，不！一个金币……
			$needGold = KitchenConf::CRITICAL_GOLD;
			// 暴击的倍率要高一些 —— 只抽样一次
			$ret = Util::backSample(btstore_get()->DISH[$dishID]['critical_cook_num_weights'], 1);
			$num = $ret[0];
		}
		else 
		{
			// 只抽样一次
			$ret = Util::backSample(btstore_get()->DISH[$dishID]['cook_num_weights'], 1);
			$num = $ret[0];
		}
		Logger::debug('Cook num is %d.', $num);

		/**************************************************************************************************************
 		 * 获取增加的数值
		 **************************************************************************************************************/
		// 获取目标用户的厨房等级
		$boatInfo = EnSailboat::getUserBoat(self::getUid());
		// 厨房等级
		$kitchenLv = $boatInfo['va_boat_info']['cabin_id_lv'][SailboatDef::KITCHEN_ID]['level'];
		// 如果厨艺等级小于厨房等级的时候，才能提升经验
		if ($kitchenInfo['lv'] < $kitchenLv)
		{
			$kitchenInfo = self::addExp($kitchenInfo, intval(btstore_get()->DISH[$dishID]['cook_exp_up']) * $num);
		}
		// 厨房每日生产次数
		$kitchenInfo = self::addCookTimes($kitchenInfo, $isGold);
		// 日常任务
		EnDaytask::kitchenProduce();
		// 通知任务系统，厨房生产了
		TaskNotify::operate(TaskOperateType::KITCHEN_PRODUCE);
		// 通知活跃度系统
		EnActive::addCookTimes();
		// 通知节日系统
		EnFestival::addCookPoint();

		// 返回给上层，更新数据库
		return array('num' => $num, 'kitchenInfo' => $kitchenInfo, 
		             'needBelly' => btstore_get()->DISH[$dishID]['base_value'], 'needGold' => $needGold);
	}

	/**
	 * 制作
	 * 
	 * @param int $dishID						菜肴ID
	 * @param boolean $isCritical				暴击不暴击
	 */
	public static function cook($dishID, $isCritical)
	{
		// 获取用户厨房信息
		$kitchenInfo = self::getUserKitchenInfo(self::getUid());
		// 获取冷却时间
		$endTime = self::addCDTime(btstore_get()->KITCHEN['cook_cd_up'], 'cook');
		// 检查冷却时间
		if ($endTime === false)
		{
			Logger::warning('Can not cook, not cd yet!.');
			throw new Exception('fake');
		}
		// 检查制作次数, 足够了就不能再进行制作
		if ($kitchenInfo['cook_times'] >= btstore_get()->KITCHEN['cook_times_per_day'] && 
		    $kitchenInfo['cook_accumulate'] <= 0)
		{
			Logger::warning('Can not cook, cook times %d full!.', $kitchenInfo['cook_times']);
			throw new Exception('fake');
		}
		// 调用实现方法
		$ret = self::_cook($kitchenInfo, $dishID, $isCritical, false);
		// 更新各个项目数据
		$kitchenInfo = $ret['kitchenInfo'];
		$kitchenInfo['cook_cd_time'] = $endTime;
		// 加上产生的个数
		if (!isset($kitchenInfo['va_kitchen_info']['stock'][$dishID]))
		{
			// 如果是第一次做这种菜，那么需要新加一条数据 (给小姑尝尝？)
			$kitchenInfo['va_kitchen_info']['stock'][$dishID] = array('num' => $ret['num'], 'id' => $dishID);
		}
		else 
		{
			// 不是第一次的话，就随随便便的加一下就行了
			$kitchenInfo['va_kitchen_info']['stock'][$dishID]['num'] += $ret['num'];
		}

		// 暴击时需要扣一个金币
		EnUser::getInstance()->subGold($ret['needGold']);
		// 扣除所需游戏币
		EnUser::getInstance()->subBelly($ret['needBelly']);
		// 更新到数据库
		EnUser::getInstance()->update();
		// 发送金币通知
		if ($isCritical)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_KITCHEN_CRITICAL, $ret['needGold'], Util::getTime());
		}

		// 更新数据库
		KitchenDao::updKitchenInfo(self::getUid(), $kitchenInfo);
		// 返回给前端
		return $kitchenInfo;
	}

	/**
	 * 查看金币制作所需的金币数是否足够
	 * 
	 * @param int $nowGoldCookTimes				玩家已经进行过的金币制作次数
	 * @param bool $isCritical					是否暴击
	 * @param int $cookTimes					想要金币制作的次数
	 * @throws Exception
	 */
	private static function __checkCookGold($nowGoldCookTimes, $isCritical, $cookTimes)
	{
		// 计算制作消耗的金币
		$needGold = 0;
		// 对不起……我只会一次一次的算钱。sorry，客官……
		for ($i = 0; $i < $cookTimes; ++$i)
		{
			// 根据当前制作次数遍历所有金币需求
			foreach (KitchenConf::$cookCost as $times => $cost)
			{
				// 如果次数达到了，那么表明找到了所需金币数
				if ($nowGoldCookTimes < $times)
				{
					// 加算这一次所需要的金币数
					$needGold += $cost;
					// 修改次数
					++$nowGoldCookTimes;
					break;
				}
			}
			Logger::debug("Now gold cook times is %d, needgold is %d", $nowGoldCookTimes, $needGold);
		}
		// 暴击还需要加一个金币
		if ($isCritical)
		{
			$needGold += KitchenConf::CRITICAL_GOLD * $cookTimes;
		}
		// 获取用户信息
		$userGold = EnUser::getUserObj()->getGold();
		if ($userGold < $needGold)
		{
			Logger::fatal('Can not cook, gold not enough!. Need gold is %d, user is %d.', 
			              $needGold, $userGold);
			throw new Exception('fake');
		}
		// 返回实际需要的金币个数
		return $needGold;
	}

	/**
	 * 金币制作
	 * 
	 * @param int $dishID						菜肴ID
	 * @param boolean $isCritical				暴击不暴击
	 * @param int $cookTimes					进行的次数
	 */
	public static function goldCook($dishID, $isCritical, $cookTimes = 1)
	{
		// 获取用户厨房信息
		$kitchenInfo = self::getUserKitchenInfo(self::getUid());
		// 获取用户VIP等级
		$vipLv = EnUser::getUserObj()->getVip();
		// 获取金币出制作数
		$maxCookTimes = btstore_get()->VIP[$vipLv]['cook_max_time'];
		// 检查是否超过了最大出制作数
		if ($kitchenInfo['gold_cook_times'] + $cookTimes > $maxCookTimes)
		{
			Logger::warning('Can not cook any more, today gold cook times is %d, max cook times is %d.',
			                $kitchenInfo['gold_cook_times'], $maxCookTimes);
			throw new Exception('fake');
		}
		// 获取实际需要的金币数
		$needGold = self::__checkCookGold($kitchenInfo['gold_cook_times'], $isCritical, $cookTimes);

		// 使用传进来的总次数，一次一次的进行制作。当然了，船上其实只有一个灶……
		for ($i = 0; $i < $cookTimes; ++$i)
		{
			// 调用实现方法 —— 在里面进行实际制作并加算次数
			$ret = self::_cook($kitchenInfo, $dishID, $isCritical, true);
			// 更新各个项目数据
			$kitchenInfo = $ret['kitchenInfo'];
			// 加上产生的个数
			if (!isset($kitchenInfo['va_kitchen_info']['stock'][$dishID]))
			{
				// 如果是第一次做这种菜，那么需要新加一条数据 (我又不是新媳妇儿！！)
				$kitchenInfo['va_kitchen_info']['stock'][$dishID] = array('num' => $ret['num'], 'id' => $dishID);
			}
			else 
			{
				// 不是第一次的话，就随随便便的加一下就行了
				$kitchenInfo['va_kitchen_info']['stock'][$dishID]['num'] += $ret['num'];
			}
		}

		// 扣除金币
		EnUser::getInstance()->subGold($needGold);
		Logger::trace('The gold of cur user is %s, need gold is %s.', EnUser::getUserObj()->getGold(), $needGold);
		// 更新到数据库
		EnUser::getInstance()->update();
		// 发送金币通知
		if ($isCritical)
		{
			// 暴击时，去掉暴击所需金币
			Statistics::gold(StatisticsDef::ST_FUNCKEY_KITCHEN_GOLDCOOK, $needGold - KitchenConf::CRITICAL_GOLD * $cookTimes, Util::getTime());
			Statistics::gold(StatisticsDef::ST_FUNCKEY_KITCHEN_CRITICAL, KitchenConf::CRITICAL_GOLD * $cookTimes, Util::getTime());
		}
		else 
		{
			// 其他时刻就不用减去了
			Statistics::gold(StatisticsDef::ST_FUNCKEY_KITCHEN_GOLDCOOK, $needGold, Util::getTime());
		}

		// 更新数据库
		KitchenDao::updKitchenInfo(self::getUid(), $kitchenInfo);
		// 返回给前端
		return array('gold' => $needGold, 'info' => $kitchenInfo);
	}

	/**
	 * 向其他用户下订单
	 * 
	 * @param int $uid							对方的用户ID
	 * @param int $dishID						菜肴ID
	 */
	public static function placeOrder($uid, $dishID)
	{
		/**************************************************************************************************************
 		 * 订单检查
		 **************************************************************************************************************/
		$curUid = self::getUid();
		// 不能向自己下订单
		if ($curUid === $uid)
		{
			Logger::fatal('Can not order self, user is %d.', $uid);
			throw new Exception('fake');
		}
		// 没完成任务的话，不允许下订单
		if (!EnSwitch::isOpen(SwitchDef::ORDER_LIST))
		{
			Logger::fatal('Can not place order before get task!');
			throw new Exception('fake');
		}
		// 玩家只可在同阵营玩家那下订单
		// 获取两个玩家的数据
		$userObj = EnUser::getUserObj($uid);
		$userCur = EnUser::getUserObj($curUid);
		// 如果阵营不一致
		if ($userObj->getGroupId() !== $userCur->getGroupId())
		{
			Logger::fatal('Can not order, group not equal, user is %d, target user is %d.',
			              $userCur->getGroupId(), $userObj->getGroupId());
			throw new Exception('fake');
		}
		// 获取用户厨房信息
		$kitchenObj = self::getUserKitchenInfo($uid);
		$kitchenCur = self::getUserKitchenInfo($curUid);
		// 调整本人和对方的订单次数
		$kitchenObj = self::adjustOrderTimes($kitchenObj);
		$kitchenCur = self::adjustOrderTimes($kitchenCur);
		Logger::debug('The current user info is %s.', $kitchenCur);
		Logger::debug('The target user info is %s.', $kitchenObj);
		// 检查制作该菜肴所需的厨艺等级
		if ($kitchenObj['lv'] < btstore_get()->DISH[$dishID]['need_lv'])
		{
			Logger::fatal('Can not order this dish, level not enough! Need level is %d, target user is %d.', 
			              btstore_get()->DISH[$dishID]['need_lv'], $kitchenObj['lv']);
			throw new Exception('fake');
		}
		// 检查两者的订单次数是否足够
		if ($kitchenObj['be_order_times'] >= btstore_get()->KITCHEN['be_orders_per_day'])
		{
			// 如果对方被订单次数已经满了，那么不能预订，直接返回
			Logger::debug('Target user be order times is full now.');
			return 'err';
		}
		if ($kitchenCur['order_times'] >= btstore_get()->KITCHEN['orders_per_day'] && 
			$kitchenCur['order_accumulate'] <= 0)
		{
			// 如果自己订单次数已经满了，那么不能预订
			Logger::fatal('Can not order, order num is full.');
			throw new Exception('fake');
		}
		// 获取冷却时间
		$endTime = self::addCDTime(btstore_get()->KITCHEN['order_cd_up'], 'order');
		// 检查订单CD时间
		if ($endTime === false)
		{
			Logger::warning('Can not order, not cd yet!.');
			throw new Exception('fake');
		}
		// 获取目标用户的厨房等级和贸易室等级
		$boatObj = EnSailboat::getUserBoat($uid);
		// 贸易室等级
		$tradeLv = isset($boatObj['va_boat_info']['cabin_id_lv'][SailboatDef::TRADE_ROOM_ID]['level']) ? 
		                 $boatObj['va_boat_info']['cabin_id_lv'][SailboatDef::TRADE_ROOM_ID]['level'] : 0;
		// 厨房等级
		$kitchenLv = $boatObj['va_boat_info']['cabin_id_lv'][SailboatDef::KITCHEN_ID]['level'];
		// 检查一下等级
		if ($kitchenLv <= 0 || $kitchenLv < btstore_get()->DISH[$dishID]['need_lv'])
		{
			Logger::fatal('Can not order, level not enough, user is %d, trade level is %d,
			               kitchen level is %d.', $uid, $tradeLv, $kitchenLv);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 开始执行订单
		 **************************************************************************************************************/
		// 获取订单产生的菜肴个数 —— 只抽样一次
		$ret = Util::backSample(btstore_get()->DISH[$dishID]['critical_cook_num_weights'], 1);
		$num = $ret[0];

		Logger::debug('Order cook dish num is %s.', $num);

		// 获取菜肴卖出价格
		$sellBelly = self::_sellBelly($kitchenLv, $tradeLv, array(array('id' => $dishID, 'num' => $num)));
		Logger::debug('Sell belly is %d.', $sellBelly);
		// 订单接受者获得游戏币 = 菜肴卖出价格*订单接受者系数
		$objUserBelly = floor($sellBelly * 
		                      btstore_get()->KITCHEN['be_order_coefficient'] / KitchenConf::LITTLE_WHITE_PERCENT);
		// 下定单者获得游戏币 = 菜肴卖出价格*订单下单者系数
		$curUserBelly = floor($sellBelly * 
		                      btstore_get()->KITCHEN['order_coefficient'] / KitchenConf::LITTLE_WHITE_PERCENT);

		/**************************************************************************************************************
 		 * 更新两者的数据库
		 **************************************************************************************************************/
		// 更新被订单次数
		$ret = KitchenDao::updOrderTimes($uid, $kitchenObj['order_times'], 
		                                       $kitchenObj['be_order_times'] + 1, 
		                                       $kitchenObj['order_accumulate'],
		                                       $kitchenObj['order_times'], 
		                                       $kitchenObj['be_order_times']);
		// 更新失败
		if ($ret['affected_rows'] === 0)
		{
			return 'err';
		}

		// 设置更新时刻
		$set = array('order_date' => Util::getTime(), 'order_cd_time' => $endTime);
		// 如果还有订单次数，那么优先减订单次数
		if ($kitchenCur['order_times'] < btstore_get()->KITCHEN['orders_per_day'])
		{
			$set['order_times'] = ++$kitchenCur['order_times'];
		}
		// 否则减去累积的次数
		else 
		{
			$set['order_accumulate'] = --$kitchenCur['order_accumulate'];
		}
		// 更新订单次数, 这个更新自己的数据，不应该出错
		KitchenDao::updKitchenInfo(self::getUid(), $set);

		/**************************************************************************************************************
 		 * 分赃
		 **************************************************************************************************************/
		// 给被订单者加游戏币
		$userObj->addBelly($objUserBelly);
		$userObj->update();
		// 给自己加游戏币
		$userCur->addBelly($curUserBelly);
		$userCur->update();
		// 通知任务系统，下订单了
		TaskNotify::operate(TaskOperateType::OREDER);
		// 通知活跃度系统
		EnActive::addOrderTimes();
		// 通知节日系统
		EnFestival::addOrderPoint();

		// 每成功被其他玩家下订单，都需以邮件的方式通知该玩家
		MailTemplate::sendBoatOrder($uid, 
		                            EnUser::getUserObj()->getTemplateUserInfo(),
		                            $objUserBelly);

		// 更新成功后需要推送
		RPCContext::getInstance()->sendMsg(array($uid), 
		                                  'kitchen.getBeorder',
		                                   array('beOrderTimes' => $kitchenObj['be_order_times'] + 1,
		                                         'getBelly' => $objUserBelly));

		// 将结果返回给前端
		return array('cdTime' => $endTime, 'targetUserBeOrderTimes' => $kitchenObj['be_order_times'] + 1,
		             'userBelly' => $curUserBelly, 'targetUserBelly' => $objUserBelly, 'num' => $num,
					 'ordertimes' => $kitchenCur['order_times'], 'orderAcc' => $kitchenCur['order_accumulate']);
	}

	/**
	 * 得到出售全部的价格
	 * 
	 * @param int $kitchenLv					厨房等级
	 * @param int $tradeLv						贸易室等级
	 * @param int $stock						厨房的仓库信息
	 */
	private static function _sellBelly($kitchenLv, $tradeLv, $stock)
	{
		// 获取港口信息
		$port = new Port();
		// 通过用户ID获取当前所在的港口ID
		$portID = $port->getPort(self::getUid());
		// 获取港口属性
		$portAttr = Port::getPortExtendAttr($portID);
		// 获取港口系数
		$portModulus = Port::getPortModulus($portID);
		// 记录总价
		$allBelly = 0;
		// 获取所有菜肴出售价格
		foreach ($stock as $dish)
		{
			/**************************************************************************************************************
	 		 * 获取菜肴信息 
			 * 菜肴卖出价格  = (菜肴游戏币基础值 + 
			 *             厨房游戏币基础值 * 厨房等级) * 
			 *            港口系数 * 
			 *             (1 + 贸易室菜肴游戏币百分比值 * 贸易室等级 + 
			 *              港口菜肴游戏币百分比值) *
			 *              节日加成 *
			 *              合服活动
			 **************************************************************************************************************/
			$belly = (btstore_get()->DISH[$dish['id']]['dish_belly_base'] + 
				 	  btstore_get()->KITCHEN['cook_belly_base'] * $kitchenLv +
				 	  $portAttr[PortDef::PORT_ATTR_ID_VOYAGE_MODIFY] ) *
					 ($portModulus / KitchenConf::LITTLE_WHITE_PERCENT) *
					 (1 + btstore_get()->TRADE_ROOM['dish_belly_percent'] * $tradeLv / KitchenConf::LITTLE_WHITE_PERCENT +
				 	  $portAttr[PortDef::PORT_ATTR_ID_SELL_BELLY_PERCENT] / KitchenConf::LITTLE_WHITE_PERCENT) *
				 	  EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_KITCHEN) *
			 	  	  EnMergeServer::theKitchenSail();;

			// 计算全部的价格
		    $belly *= $dish['num'];
		    // 记录总价
		    $allBelly += $belly;
		}
		// 返回获得的游戏币值
		return floor($allBelly);
	}

	/**
	 * 卖出所有菜肴
	 * 
	 * @param int $curTime						当前时刻
	 * @param int $kitchenInfo					厨房信息
	 */
	public static function sellAll($curTime, $kitchenInfo)
	{
		/**************************************************************************************************************
 		 * 获取厨房信息
 		 **************************************************************************************************************/
		// 初始化售出款项 
		$belly = 0;
		// 当前厨艺等级
		$level = $kitchenInfo['lv'];

		/**************************************************************************************************************
	 	 * 出售并更新用户数据
	 	 **************************************************************************************************************/
		// 如果有剩饭剩菜，才可以出售赚外快，否则不需要那么做…… 
		if (!empty($kitchenInfo['va_kitchen_info']['stock']))
		{
			// 获取最新舱室信息
			$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
			// 获取厨房等级
			$kitchenLv = $cabinInfo[SailboatDef::KITCHEN_ID]['level'];
			// 获取贸易室室等级
			$tradeLv = empty($cabinInfo[SailboatDef::TRADE_ROOM_ID]['level']) ? 0 : $cabinInfo[SailboatDef::TRADE_ROOM_ID]['level'];
	
			// 获取卖的的游戏币
			$belly = self::_sellBelly($kitchenLv, $tradeLv, $kitchenInfo['va_kitchen_info']['stock']);
			Logger::debug('All dish sell belly is %d.', $belly);
			// 循环卖出所有存货
			foreach ($kitchenInfo['va_kitchen_info']['stock'] as $dish)
			{
				// 清空菜肴个数
				$kitchenInfo['va_kitchen_info']['stock'][$dish['id']]['num'] = 0;
			}
			// 如果增加的贝里不为零的话， 才进行更新动作
			if ($belly != 0)
			{
				// 添加游戏币
				EnUser::getInstance()->addBelly($belly);
				// 更新到数据库
				EnUser::getInstance()->update();
			}
		}

		/**************************************************************************************************************
	 	 * 更新累积次数
	 	 **************************************************************************************************************/
		// 获取相间隔的天数 —— 这里需要减一，因为有一天是需要根据最近一天的剩余次数算出来的
		$days = Util::getDaysBetween($kitchenInfo['cook_date'], KitchenConf::REFRESH_TIME) - 1;
		// 修改时刻
		$kitchenInfo['cook_date'] = $curTime;
		// 设置累积次数 —— 当日最大次数减去实际使用次数，累积起来 : modify by liuyang 12-12-05
		$kitchenInfo['cook_accumulate'] += (btstore_get()->KITCHEN['cook_times_per_day'] - 
											$kitchenInfo['cook_times'] + $days * btstore_get()->KITCHEN['cook_times_per_day']);
		// 判断是否累积超过了最大值
		if ($kitchenInfo['cook_accumulate'] > 
			btstore_get()->TOP_LIMIT[TopLimitDef::KITCHEN_MAX_TIME] - btstore_get()->KITCHEN['cook_times_per_day'])
		{
			// 防止意外出现的错误数据
			$tmp = btstore_get()->TOP_LIMIT[TopLimitDef::KITCHEN_MAX_TIME] > btstore_get()->KITCHEN['cook_times_per_day'] ?
				   btstore_get()->TOP_LIMIT[TopLimitDef::KITCHEN_MAX_TIME] - btstore_get()->KITCHEN['cook_times_per_day'] : 0;
			// 如果超过了，就给最大值，不能再多给次数了
			$kitchenInfo['cook_accumulate'] = $tmp;
		}
		$kitchenInfo['cook_times'] = 0;
		// 修改金币时刻
		$kitchenInfo['gold_cook_date'] = $curTime;
		$kitchenInfo['gold_cook_times'] = 0;
		// 所有的工作都只为了这个地方, 放心吧，我们会计算好玩家们的每一分钱
		$kitchenInfo['belly'] = $belly;
		// 返回
		return $kitchenInfo;
	}

	/**
	 * 卖出一种菜肴
	 * 
	 * @param int $dishID						菜肴ID
	 */
	public static function sell($dishID)
	{
		/**************************************************************************************************************
 		 * 获取厨房信息
 		 **************************************************************************************************************/
		// 获取最新舱室信息
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
		// 获取厨房等级
		$kitchenLv = $cabinInfo[SailboatDef::KITCHEN_ID]['level'];
		// 获取贸易室室等级
		$tradeLv = empty($cabinInfo[SailboatDef::TRADE_ROOM_ID]['level']) ? 
		                 0 : $cabinInfo[SailboatDef::TRADE_ROOM_ID]['level'];

		// 获取用户厨房信息
		$kitchenInfo = KitchenDao::getKitchenInfo(self::getUid());
		// 获取菜肴库存
		$dishStock = $kitchenInfo['va_kitchen_info'];
		// 当前厨艺等级
		$level = $kitchenInfo['lv'];

		/**************************************************************************************************************
 		 * 出售并更新用户数据
 		 **************************************************************************************************************/
		// 获取卖的的游戏币
		$belly = self::_sellBelly($kitchenLv, $tradeLv, array(array('id' => $dishID, 'num' => $dishStock['stock'][$dishID]['num'])));
		Logger::debug('The %d dish sell belly is %d.', $dishID, $belly);

		// 添加游戏币
		EnUser::getInstance()->addBelly($belly);
		// 更新到数据库
		EnUser::getInstance()->update();
		// 清空菜肴个数
		$dishStock['stock'][$dishID]['num'] = 0;
		// 更新数据库
		KitchenDao::updKitchenInfo(self::getUid(), array('va_kitchen_info' => $dishStock));

		// 通知成就系统
		EnAchievements::notify(self::getUid(), AchievementsDef::SELL_DISH, $belly);
		// 返回
		return $belly;
	}

	/**
	 * 获取当前CD截止时刻
	 */
	public static function getCdEndTime()
	{
		$kitchenInfo = KitchenDao::getKitchenInfo(self::getUid());
		// 获取CD截止时刻
		return array('cook' => $kitchenInfo['cook_cd_time'], 'order' => $kitchenInfo['order_cd_time']);
	}

	/**
	 * 获取当前CD时刻
	 * 
	 * @param string $type						CD类型
	 */
	public static function getCDTime($type) 
	{
		// 获取CD截止时刻
		$ret = self::getCdEndTime();
		$endTime = $ret[$type];
		// 获取当前CD时刻
		$cd = $endTime - Util::getTime();
		return $cd < 0 ? 0 : $cd;
	}

	/**
	 * 添加CD时间
	 * 
	 * @param int $addTime						需要增加的时刻
	 * @param string $type						CD类型
	 */
	private static function addCDTime($addTime, $type)
	{
		// 记录下当前时间
		$curTime = Util::getTime();
		// 获取CD截止时刻
		$ret = self::getCdEndTime();
		$endTime = $ret[$type];
		// 如果时间已经大于当前时刻
		if ($endTime > $curTime) 
		{
			// 不能增加CD时间，直接返回
			return false;
		}
		// 否则，记录CD时刻
		$endTime = $curTime + $addTime;
		Logger::debug("The Kitchen %s endTime is %s, current time is %d.", $type, $endTime, $curTime);
		// 成功添加时刻, 返回截止时间
		return $endTime;
	}

	/**
	 * 使用人民币清空CD时间
	 * 
	 * @param string $type						CD类型
	 */
	public static function clearCDByGold($type) 
	{
		// 查询每金币秒的CD时间
		$goldPerCd = $type === 'cook' ? intval(btstore_get()->KITCHEN['gold_per_cook_cd']) :
		                                intval(btstore_get()->KITCHEN['gold_per_order_cd']);
		// 获取CD时刻, 看看一共需要多少个金币
		$num = ceil(self::getCDTime($type) / $goldPerCd);
		// 如果不需要清除CD时刻，那么就直接返回
		if ($num <= 0)
		{
			return 0;
		}

		// 获取用户信息
		$userInfo = EnUser::getUser();
		// 因为要扣钱，所以记录下日志，省的你不认账
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $num);
		if ($num > $userInfo['gold_num'])
		{
			// 钱不够，直接返回
			return 'err';
		}
		// 清空CD时刻
		KitchenDao::updKitchenInfo(self::getUid(), array($type.'_cd_time' => Util::getTime()));

		// 扣钱
		EnUser::getInstance()->subGold($num);
		EnUser::getInstance()->update();
		// 发送金币通知
		$cdType = $type === 'cook' ? StatisticsDef::ST_FUNCKEY_KITCHEN_COOKCDTIME :
		                             StatisticsDef::ST_FUNCKEY_KITCHEN_ORDERCDTIME;
		Statistics::gold($cdType, $num, Util::getTime());

		// 返回给前端，实际使用的金币数量
		return $num;
	}

	/**
	 * 获取登陆用户的uid
	 */
	public static function getUid()
	{
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		// 如果没有获取到
		if (empty($uid)) 
		{
			Logger::fatal('Can not get Captain info from session!');
			throw new Exception('fake');
		}
		return $uid;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */