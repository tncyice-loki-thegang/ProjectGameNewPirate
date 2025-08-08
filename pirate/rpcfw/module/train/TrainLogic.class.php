<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TrainLogic.class.php 37822 2013-02-01 05:21:27Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/train/TrainLogic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-01 13:21:27 +0800 (五, 2013-02-01) $
 * @version $Revision: 37822 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : TrainLogic
 * Description : 训练室实现类
 * Inherit     : 
 **********************************************************************************************************************/
class TrainLogic
{

	/**
	 * 获取用户的训练信息
	 */
	public static function getUserTrainInfo()
	{
		// 调整训练时刻和经验
		$addExp = self::adjustTrainTime();
		// 获取人物训练信息
		$userTrainInfo = MyTrain::getInstance()->getUserTrainInfo();
		// 在人物训练信息里面加上增加经验
		$userTrainInfo['addExp'] = $addExp;
		// 返回人物训练信息
		return $userTrainInfo;
	}

	/**
	 * 开启新的训练位
	 */
	public static function openTrainSlot() 
	{
		/**************************************************************************************************************
 		 * 获取训练信息
 		 **************************************************************************************************************/
		$trainInfo = self::getUserTrainInfo();
		// 获取当前训练栏位个数
		$trainCount = $trainInfo['train_slots'];

		/**************************************************************************************************************
 		 * 获取用户信息
 		 **************************************************************************************************************/
		$userInfo = EnUser::getUser();
		// 得到用户vip等级
		$vipLv = intval($userInfo['vip']);
		// 当前拥有的金币数量
		$gold = $userInfo['gold_num'];

		/**************************************************************************************************************
 		 * 判断VIP等级和金币数量
 		 **************************************************************************************************************/
		// 如果当前训练栏个数超出了最大值
		if (empty(btstore_get()->VIP[$vipLv]['train_slots'][$trainCount + 1]))
		{
			Logger::trace('Train slot num max.');
			return 'err';
		}
		$needGold = btstore_get()->VIP[$vipLv]['train_slots'][$trainCount + 1]['gold'];
		// 如果VIP等级不到或者金币数量不到
		if ($gold < $needGold)
		{
			Logger::fatal('New train slot need gold is %d. The user now is %d, %d.', 
			              $needGold, $vipLv, $gold);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 开启新的训练栏
 		 **************************************************************************************************************/
		// 增加一个新的训练栏
		MyTrain::getInstance()->openTrainSlot();
		// 保存到数据库
		MyTrain::getInstance()->save();

		/**************************************************************************************************************
 		 * 扣除金币数
 		 **************************************************************************************************************/
		$user = EnUser::getInstance();
		$user->subGold($needGold);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $needGold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_TRAIN_OPENTRAINSLOT, $needGold, Util::getTime());

		return 'ok';
	}

	/**
	 * 突飞前检查
	 * 
	 * @param int $heroID						// 英雄ID
	 * @throws Exception
	 */
	private static function rapidCheck($heroID)
	{
		// 主角英雄不能进行训练相关操作
		if ($heroID == EnUser::getUserObj()->getMasterHeroObj()->getHid())
		{
			Logger::fatal('Main hero can not rapid.');
			throw new Exception('fake');
		}
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 检查英雄ID, 查看其是否已经被招募
		if (!$user->getHeroObj($heroID)->isRecruit())
		{
			// 没有被招募，不能进行突飞
			Logger::fatal('Can not rapid, the %d hero is not been recruit.', $heroID);
			throw new Exception('fake');
		}
		// 英雄等级检查
		// 获取英雄对象
		$heroObj = $user->getHeroObj($heroID);
		// 英雄等级不能大于人物等级
		if ($heroObj->getLevel() >= $user->getLevel())
		{
			Logger::warning('Can not rapid, the %d hero level can not higher then user, user is %d, hero is %d.',
			              $heroID, $user->getLevel(), $heroObj->getLevel());
			throw new Exception('fake');
		}

		// 获取训练信息 (包含最新的CD时间)
		$trainInfo = self::getUserTrainInfo();
		// 检查该英雄是否正在训练
		if (!isset($trainInfo['va_train_info'][$heroID]))
		{
			// 如果尚未处于训练状态，则不准进行突飞
			Logger::fatal('Can not rapid, the %d hero is not training now.', $heroID);
			throw new Exception('fake');
		}

		// 训练室等级检查  获取最新舱室信息
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
		// 获取训练室等级
		$cabinLv = $cabinInfo[SailboatDef::TRAIN_ROOM_ID]['level'];
		if (empty($cabinLv) || $cabinLv <= 0)
		{
			Logger::fatal('Can not get train room level!');
			throw new Exception('fake');
		}
		// 婚检OK，返回
		return array('user' => $user, 'hero' => $heroObj, 'cabinLv' => $cabinLv);
	}

	/**
	 * 突飞啊
	 * 
	 * @param int $heroID						英雄ID
	 * @param int $times						想要突飞的次数
	 * 
	 * @throws Exception
	 */
	public static function rapid($heroID, $times = 1)
	{
		//  突飞检查并获取训练室和训练的信息
		$info = self::rapidCheck($heroID);
		// 检查OK，获取各个数据
		$user = $info['user'];
		$cabinLv = $info['cabinLv'];

		// 进行计数
		$i = 0;
		for (; $i < $times; ++$i)
		{
			// 实际突飞一次
			if (!self::__rapidOnce($user, $cabinLv, $info['hero']))
			{
				break;
			}
			// 日常任务
			EnDaytask::rapidHero();
			// 通知任务系统，突飞了
			TaskNotify::operate(TaskOperateType::HERO_RAPID);
			// 通知活跃度系统
			EnActive::addHeroRapidTimes();
			// 通知节日系统
			EnFestival::addRapidPoint();
		}

		// 如果真的进行过突飞，则需要进行一系列的动作
		if ($i > 0)
		{
			// 保存到数据库
			MyTrain::getInstance()->save();
		}
		// 调整等级
		self::adjustTrainTime();

		// 返回当前英雄的等级和经验
		return array('times' => $i, 'lv' => $info['hero']->getLevel(), 'exp' => $info['hero']->getExp());
	}

	/**
	 * 实际进行一次突飞
	 * 
	 * @param obj $user							用户实例
	 * @param int $cabinLv						舱室等级
	 * @param obj $hero							英雄实例
	 */
	private static function __rapidOnce($user, $cabinLv, $hero)
	{
		/**************************************************************************************************************
 		 * 升级费用检查
 		 **************************************************************************************************************/
		// 游戏币/阅历/金币检查
		if ((isset(btstore_get()->TRAIN_ROOM['rapid_res_base'][0]) && 
							$user->getBelly() < btstore_get()->TRAIN_ROOM['rapid_res_base'][0] * $cabinLv) ||
		    (isset(btstore_get()->TRAIN_ROOM['rapid_res_base'][1]) && 
		    				$user->getExperience() < btstore_get()->TRAIN_ROOM['rapid_res_base'][1] * $cabinLv) ||
		    (isset(btstore_get()->TRAIN_ROOM['rapid_res_base'][2]) && 
		    				$user->getGold() < btstore_get()->TRAIN_ROOM['rapid_res_base'][2] * $cabinLv))
		{
			Logger::trace('Res not enough, user belly is %d, experience is %d, gold is %d. need is %s.',
			              $user->getBelly(), $user->getExperience(), $user->getGold(),
			              btstore_get()->TRAIN_ROOM['rapid_res_base']->toArray());
			return false;
		}
		// 检查,增加CD时间
		if (!self::addCdTime(btstore_get()->TRAIN_ROOM['rapid_time_up']))
		{
			Logger::trace('Not cool down yet.');
			return false;	
		}

		/**************************************************************************************************************
 		 * 突飞
 		 **************************************************************************************************************/
		// 计算需要增加的经验值
		$exp = $cabinLv * btstore_get()->TRAIN_ROOM['rapid_exp_base'] * 
								EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_RAPID);
		// 增加经验  —— 不需要调用 update 在下面调用 user 的 update 即可
		$hero->addExp($exp);
		Logger::debug('The train room level is %d, add exp is %d.', $cabinLv, $exp);

		/**************************************************************************************************************
 		 * 扣除成本
 		 **************************************************************************************************************/
		// 统计用金币计数
		$gold = 0;
		// 扣游戏币/阅历/金币
		if (isset(btstore_get()->TRAIN_ROOM['rapid_res_base'][0]))
		{
			$user->subBelly(btstore_get()->TRAIN_ROOM['rapid_res_base'][0] * $cabinLv);
		}
		if (isset(btstore_get()->TRAIN_ROOM['rapid_res_base'][1]))
		{
			$user->subExperience(btstore_get()->TRAIN_ROOM['rapid_res_base'][1] * $cabinLv);
		}
		if (isset(btstore_get()->TRAIN_ROOM['rapid_res_base'][2]))
		{
			$gold = btstore_get()->TRAIN_ROOM['rapid_res_base'][2] * $cabinLv;
			$user->subGold($gold);
			Logger::trace('The gold of cur user is %s, need gold is %s.', $user->getGold(), $gold);
		}
		$user->update();

		// 发送金币通知
		if ($gold > 0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_TRAIN_RAPID, $gold, Util::getTime());
		}

		return true;
	}

	/**
	 * 富人的游戏，用金币进行突飞
	 * 
	 * @param int $heroID						英雄ID
	 * @throws Exception
	 */
	public static function rapidByGold($heroID) 
	{
		/**************************************************************************************************************
 		 * 突飞检查并获取训练室和训练的信息
 		 **************************************************************************************************************/
		$info = self::rapidCheck($heroID);

		/**************************************************************************************************************
 		 * 升级费用检查
 		 **************************************************************************************************************/
		// 查看现在已经金币突飞的次数
		$rapidTimes = MyTrain::getInstance()->getTodayRapidTimes();
		$gold = ($rapidTimes * btstore_get()->TRAIN_ROOM['rapid_gold_up'] + 
		         btstore_get()->TRAIN_ROOM['rapid_gold_base']) * TrainConf::RAPID_GOLD_RATIO;
		// 金币检查
		if ($info['user']->getGold() < $gold)
		{
			Logger::trace('Gold not enough, rapid needs %d, user have now %d, today rapid times is %d.',
			              $gold, $info['user']->getGold(), $rapidTimes);
			return 'err';	
		}
		// VIP检查
		if (empty(btstore_get()->VIP[$info['user']->getVip()]['rapid_open_lv']))
		{
			Logger::trace('Vip level not enough, user now is %d.', $info['user']->getVip());
			return 'err';	
		}

		/**************************************************************************************************************
 		 * 人物突飞
 		 **************************************************************************************************************/
		// 计算需要增加的经验值
		$exp = $info['cabinLv'] * btstore_get()->TRAIN_ROOM['rapid_exp_base'] * 
				TrainConf::RAPID_GOLD_RATIO * EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_RAPID);
		Logger::debug('The train room level is %d, add exp is %d.', $info['cabinLv'], $exp);
		// 增加经验 —— 不需要调用 update 在下面调用 user 的 update 即可
		$info['hero']->addExp($exp);
		// 增加今日突飞次数
		MyTrain::getInstance()->addRapidTimes();

		/**************************************************************************************************************
 		 * 扣除成本
 		 **************************************************************************************************************/
		// 扣金币
		$info['user']->subGold($gold);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $info['user']->getGold(), $gold);
		$info['user']->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_TRAIN_GOLDRAPID, $gold, Util::getTime());

		// 日常任务
		EnDaytask::rapidHero();
		// 通知任务系统，突飞了
		TaskNotify::operate(TaskOperateType::HERO_RAPID);
		// 通知活跃度系统
		EnActive::addHeroRapidTimes();
		// 通知节日系统
		EnFestival::addRapidPoint();

		// 调整等级
		self::adjustTrainTime();
		// 保存到数据库
		MyTrain::getInstance()->save();

		// 返回当前英雄的等级和经验
		return array('lv' => $info['hero']->getLevel(), 'exp' => $info['hero']->getExp());
	}

	/**
	 * 获取当前CD时刻
	 */
	public static function getCDTime() 
	{
		// 获取CD截止时刻
		$endTime = MyTrain::getInstance()->getCdEndTime();
		// 获取当前CD时刻
		$cd = $endTime['cd_time'] - Util::getTime();
		return $cd < 0 ? 0 : $cd;
	}

	/**
	 * 添加CD时间
	 * @param int $addTime						需要增加的时刻
	 */
	public static function addCDTime($addTime)
	{
		// 获取最新的CD截止时刻和状态
		$trainInfo = self::getUserTrainInfo();
		// 如果CD时间为空闲
		if ($trainInfo['cd_status'] == TrainConf::RAPID_FREE)
		{
			// 加上时间
			MyTrain::getInstance()->addCdTime($addTime);
			// 成功返回
			return true;
		}
		// 如果CD时间为忙
		else if ($trainInfo['cd_status'] == TrainConf::RAPID_BUSY)
		{
			// 如果CD时间还没有走完，那么就侯着吧
			return false;
		}
	}

	/**
	 * 使用人民币清空CD时间
	 */
	public static function clearCDByGold() 
	{
		// 获取CD时刻, 看看一共需要多少个金币
		$num = ceil(self::getCDTime() / btstore_get()->TRAIN_ROOM['gold_per_cd']);
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
		MyTrain::getInstance()->resetCdTime();

		// 扣钱
		$user = EnUser::getInstance();
		$user->subGold($num);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $num);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_TRAIN_CLEARCDTIME, $num, Util::getTime());

		// 保存至数据库
		MyTrain::getInstance()->save();
		// 返回实际使用的金币数量
		return $num;
	}

	/**
	 * 获取当前CD截止时刻
	 */
	public static function getCdEndTime()
	{
		// 获取CD截止时刻
		return MyTrain::getInstance()->getCdEndTime();
	}

	/**
	 * 调整训练的经验和等级
	 * @throws Exception
	 */
	public static function adjustTrainTime()
	{
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 训练室等级检查  获取最新舱室信息
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
		// 判断训练室是否存在
		if (empty($cabinInfo[SailboatDef::TRAIN_ROOM_ID]))
		{
			// 初始化等级
			$cabinInfo[SailboatDef::TRAIN_ROOM_ID]['level'] = 1;
		}
		// 获取训练室等级
		$cabinLv = $cabinInfo[SailboatDef::TRAIN_ROOM_ID]['level'];

		// 提升的经验
		$addExp = array();
		// 获取训练信息
		$trainInfo = MyTrain::getInstance()->getUserTrainInfo();
		// 空的时候初始化
		if ($trainInfo === false)
		{
			// 检查用户是否完成相应任务
			if (EnSwitch::isOpen(SwitchDef::TRAIN))
			{
				Logger::debug('Init train info.');
				// 初始化人物训练信息
				$trainInfo = MyTrain::getInstance()->addNewTrainInfo();
			}
			// 还没完成任务，不能弄这个啊
			else 
			{
				Logger::fatal('Can not get train cabin level!');
				throw new Exception('fake');
			}
		}
		// 初始化时刻，并用这个时刻当做标志位
		$min = 0;
		// 获取这个玩家的所有训练中的英雄
		foreach ($trainInfo['va_train_info'] as $hero)
		{
			// 获取英雄对象
			$heroObj = $user->getHeroObj($hero['id']);
			// 获取下最新的时刻
			$curTime = Util::getTime();
			// 计算是否超时
			if ($hero['train_start_time'] >= $hero['train_end_time'])
			{
				// 如果已经超时，那么不再计算经验，其实训练已经终止了
				continue;
			}
			// 这里有个历史悠久的bug, 如果$curTime 超过了 train_end_time 的话, 不幸的是这里没有进行处理, 所以让很多秃驴们占了便宜
			// 让玩家占便宜对于我来说到无所谓, 妈的丫还说我经验少给了。 现在弄的策划们也不好搞，所以这个地方和新项目不一样，做了一个比较特殊的处理方法
			// 先计算那些正常玩游戏玩家的请求
			$trainTime = $curTime - $hero['train_start_time'];
			// 如果发现不太对了的话, 则进行处理
			if ($trainTime > TrainConf::MAX_TRAIN_TIME)
			{
				// 如果超出截止时间超过了72小时，那么就给他72小时的便宜，丫真不要脸
				$trainTime = TrainConf::MAX_TRAIN_TIME;
			}
			// 计算十分钟
			$min = floor($trainTime / 60);
			// 获取剩余的秒数
			$sec = $trainTime % 60;
			// 真相只有一个
			Logger::debug('Now time is %d, train start time is %d, min is %d, left second is %d.', 
			              $curTime, $hero['train_start_time'], $min, $sec);
			// 只有不为零的时候才做这样的事儿
			if ($min != 0)
			{
				// 计算一共获取经验值
				$exp = $min * $cabinLv * btstore_get()->TRAIN_ROOM['exp_coefficient'] * $hero['train_mode'] * 
				 		EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_TRAIN);
				// 增加经验
				$heroObj->addExp($exp);
				Logger::debug('The train room level is %d, add exp is %d, min is %d, left second is %d.', $cabinLv, $exp, $min, $sec);
				// 调整训练重置时刻
				MyTrain::getInstance()->resetTrainStartTime($hero['id'], $curTime - $sec);
				// 保存经验值给前端
				$addExp[$hero['id']] = $exp;
			}
		}

		// 进行过实际经验改变的时候，保存到数据库
		if ($min != 0)
		{
			$user->update();
			MyTrain::getInstance()->save();
		}
		return $addExp;
	}

	/**
	 * 开始训练
	 * @param int $heroID						英雄ID
	 * @param int $mode							训练模式
	 * @param int $lastTime						持续时刻
	 */
	public static function train($heroID, $mode, $lastTime) 
	{
		// 主角英雄不能进行训练相关操作
		if ($heroID == EnUser::getUserObj()->getMasterHeroObj()->getHid())
		{
			Logger::fatal('Main hero can not train.');
			throw new Exception('fake');
		}
		// 没有被招募的英雄不能训练
		if (!EnUser::getUserObj()->getHeroObj($heroID)->isRecruit())
		{
			// 没有被招募，不能进行突飞
			Logger::fatal('Can not train, the %d hero is not been recruit.', $heroID);
			throw new Exception('fake');
		}
		// 获取训练信息
		$trainInfo = self::getUserTrainInfo();
		// 获取当前训练栏位个数
		$trainCount = $trainInfo['train_slots'];
		// 查看训练栏位是否已经满了
		if (count($trainInfo['va_train_info']) >= $trainCount)
		{
			Logger::trace('Train slots if full now, num is %d.', $trainInfo['train_slots']);
			return 'err';
		}
		// 返回训练截止时刻
		return self::startTrain($heroID, $mode, $lastTime);
	}

	/**
	 * 开始训练
	 * @param int $heroID						英雄ID
	 * @param int $mode							训练模式
	 * @param int $lastTime						持续时刻
	 */
	private static function startTrain($heroID, $mode, $lastTime)
	{
		// 校准前端的参数
		$mode -= 1;
		$lastTime -= 1;
		// 获取人物信息
		$userInfo = EnUser::getUser();
		// 得到用户vip等级
		$vipLv = intval($userInfo['vip']);
		// 当前拥有的钱数量
		$gold = $userInfo['gold_num'];
		$belly = $userInfo['belly_num'];
		// vip等级检查
		if (empty(btstore_get()->VIP[$vipLv]['train_mode'][$mode]) || 
		    empty(btstore_get()->VIP[$vipLv]['train_time'][$lastTime]) ||
		    !isset(btstore_get()->TRAIN_ROOM['train_mode_golds'][$mode]) ||
		    empty(btstore_get()->TRAIN_ROOM['train_time_golds'][$lastTime]) ||
		    empty(btstore_get()->TRAIN_ROOM['train_lv_ratio'][$mode]) ||
		    empty(btstore_get()->TRAIN_ROOM['train_time_sec'][$lastTime]))
		{
			// 如果vip等级过低，则不能使用这种训练模式
			Logger::fatal('Can not choose this train mode!');
			throw new Exception('fake');
		}
		// 该档训练所需花费
		$modeNeedGold = intval(btstore_get()->TRAIN_ROOM['train_mode_golds'][$mode]);
		// 该档时间所需花费
		$timeNeedGold = intval(btstore_get()->TRAIN_ROOM['train_time_golds'][$lastTime]['gold']);
		$timeNeedBelly = intval(btstore_get()->TRAIN_ROOM['train_time_golds'][$lastTime]['belly']);
		// 判断金币和游戏币数量
		if ($belly < $timeNeedBelly || $gold < $modeNeedGold + $timeNeedGold)
		{
			Logger::fatal('Can not train, need gold is %d, belly is %d. The user now is %d, %d.', 
			              $modeNeedGold + $timeNeedGold, $timeNeedBelly, $gold, $belly);
			throw new Exception('fake');
		}

		// 调整等级
		self::adjustTrainTime();
		// 记录开始时刻
		$endTime = MyTrain::getInstance()->startTrain($heroID, 
		                                              btstore_get()->TRAIN_ROOM['train_lv_ratio'][$mode] / TrainConf::LITTLE_WHITE_PERCENT, 
		                                              btstore_get()->TRAIN_ROOM['train_time_sec'][$lastTime]);
		// 保存到数据库
		MyTrain::getInstance()->save();

		// 扣除金币
		$user = EnUser::getInstance();
		$user->subGold($modeNeedGold + $timeNeedGold);
		$user->subBelly($timeNeedBelly);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $modeNeedGold + $timeNeedGold);
		$user->update();
		// 发送金币通知
		if ($modeNeedGold + $timeNeedGold > 0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_TRAIN_TRAIN, $modeNeedGold + $timeNeedGold, Util::getTime());
		}

		// 通知任务系统，训练了
		TaskNotify::operate(TaskOperateType::HERO_TRAIN);

		// 通知前端，截止时间是什么
		return $endTime;
	} 

	/**
	 * 调整训练模式
	 * @param int $heroID						英雄ID
	 * @param int $mode							训练模式
	 * @param int $lastTime						持续时刻
	 */
	public static function changeTrainMode($heroID, $mode, $lastTime) 
	{
		// 主角英雄不能进行训练相关操作
		if ($heroID == EnUser::getUserObj()->getMasterHeroObj()->getHid())
		{
			Logger::fatal('Main hero can not train.');
			throw new Exception('fake');
		}
		// 没有被招募的英雄不能训练
		if (!EnUser::getUserObj()->getHeroObj($heroID)->isRecruit())
		{
			// 没有被招募，不能进行突飞
			Logger::fatal('Can not train, the %d hero is not been recruit.', $heroID);
			throw new Exception('fake');
		}
		// 获取训练信息
		$trainInfo = self::getUserTrainInfo();
		// 查看该英雄的现在训练状态
		if (!isset($trainInfo['va_train_info'][$heroID]))
		{
			Logger::fatal('This hero %d is not train now!', $heroID);
			throw new Exception('fake');
		}
		// 返回训练截止时刻
		return self::startTrain($heroID, $mode, $lastTime);
	}

	/**
	 * 结束训练
	 * @param int $heroID						取消英雄的训练
	 */
	public static function stopTrain($heroID) 
	{
		// 主角英雄不能进行训练相关操作
		if ($heroID == EnUser::getUserObj()->getMasterHeroObj()->getHid())
		{
			Logger::fatal('Main hero can not train.');
			throw new Exception('fake');
		}
		// 没有被招募的英雄不能训练
		if (!EnUser::getUserObj()->getHeroObj($heroID)->isRecruit())
		{
			// 没有被招募，不能进行突飞
			Logger::fatal('Can not train, the %d hero is not been recruit.', $heroID);
			throw new Exception('fake');
		}
		// 调整训练时刻和经验
		self::adjustTrainTime();
		// 结束训练
		MyTrain::getInstance()->clearTrainInfo($heroID);
		// 保存到数据库
		MyTrain::getInstance()->save();
		return 'ok';
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */