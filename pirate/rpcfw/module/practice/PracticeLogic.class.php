<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PracticeLogic.class.php 29379 2012-10-15 06:46:35Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/practice/PracticeLogic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-15 14:46:35 +0800 (一, 2012-10-15) $
 * @version $Revision: 29379 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : PracticeLogic
 * Description : 人物挂机逻辑类
 * Inherit     : 
 **********************************************************************************************************************/
class PracticeLogic
{
	/**
	 * 获取当前用户的挂机信息
	 */
	public static function getUserPracticeInfo() 
	{
		// 获取用户挂机信息
		$practiceInfo = MyPractice::getInstance()->getUserPracticeInfo();
		// 如果不为空的话
		if ($practiceInfo !== false)
		{
			// 记录总经验值
			$practiceInfo['totalExp'] = MyPractice::getInstance()->calculateExp() + $practiceInfo['exp'];
			// 返回已经累计的经验值
			return $practiceInfo;
		}
		return 'err';
	}

	/**
	 * 获取挂机经验
	 */
	public static function fetchExp() 
	{
		// 获取挂机经验
		$exp = MyPractice::getInstance()->fetchExp();
		// 更新数据库
		MyPractice::getInstance()->save();

		// 给人物加经验吧
		if ($exp > 0)
		{
			EnUser::getUserObj()->addExp($exp);
			EnUser::getUserObj()->update();
		}
		// 通知成就系统
		EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::PRACTICE_TOTAL_EXP, $exp);

		// 返回给前端，人物当前的经验和等级
		return array('exp' => EnUser::getUserObj()->getMasterHeroObj()->getExp(), 
		             'lv' => EnUser::getUserObj()->getMasterHeroObj()->getLevel());
	}

	/**
	 * 加速挂机
	 * 
	 * @throws Exception
	 */
	public static function accelerate() 
	{
		// 获取用户挂机信息
		$practiceInfo = MyPractice::getInstance()->getUserPracticeInfo();
		// 获取用户VIP等级
		$vip = EnUser::getUserObj()->getVip();
		// 获取该用户每日可加速次数
		$accTimes = btstore_get()->VIP[$vip]['accelerate_times'];
		// 对次数进行检查
		if ($practiceInfo['acc_times'] >= $accTimes)
		{
			Logger::fatal('Can not accelerate, today acc time is %d, can acc time is %d.',
			              $practiceInfo['acc_times'], $accTimes);
			throw new Exception('fake');
		}
		// 查看所需金币数
		$gold = $practiceInfo['acc_times'];
		// 检查金币数是否足够
		if (EnUser::getUserObj()->getGold() < $gold)
		{
			Logger::fatal('Not enough gold, need %d, now have %d.', $gold, EnUser::getUserObj()->getGold());
			throw new Exception('fake');
		}
		// 加速！加速！！加速！！！
		if (!MyPractice::getInstance()->accelerateExp())
		{
			// 如果只剩下不到半个小时了，他们的意思就别扣人家钱了
			return 'err';
		}
		// 扣钱
		EnUser::getUserObj()->subGold($gold);
		Logger::trace('Accelerate, need gold is %d, today acc time is %d.', $gold, $practiceInfo['acc_times'] + 1);
		// 更新数据库
		MyPractice::getInstance()->save();
		EnUser::getUserObj()->update();
		// 发送金币通知
		if ($gold > 0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_PRACTICE_ACCELERATE, $gold, Util::getTime());
		}
		// 给前端返回值
		return 'ok';
	}

	/**
	 * 加速挂机
	 * 
	 * @param $times							期望加速的次数
	 * 
	 * @throws Exception
	 */
	public static function accelerateByTimes($times)
	{
		// 计数
		$count = 0;
		// 循环加速
		for ($i = 0; $i < $times; ++$i)
		{
			if (self::accelerate() == 'err')
			{
				break;
			}
			++$count;
		}
		// 返回实际次数
		return $count;
	}

	/**
	 * 开启24小时挂机模式
	 * 
	 * @throws Exception
	 */
	public static function openVipFullDayMode() 
	{
		// 获取用户挂机信息
		$practiceInfo = MyPractice::getInstance()->getUserPracticeInfo();
		// 检查是否已经开启，如果已经开启最高级模式，那么直接返回
		if ($practiceInfo['open_full_day'] >= 2)
		{
			return 'err';
		}
		// 如果尚未开启，检查开启条件
		// 获取用户VIP等级
		$vip = EnUser::getUserObj()->getVip();
		// 获取用户金币数
		$gold = EnUser::getUserObj()->getGold();
		// 如果VIP等级不够或者金币不够的话，就没法开启24小时挂机模式了
		if (empty(btstore_get()->VIP[$vip]['day_mode_open'][$practiceInfo['open_full_day']]) || 
		    $gold < btstore_get()->VIP[$vip]['day_mode_open'][$practiceInfo['open_full_day']])
		{
			Logger::fatal('Can not open 24 hours mode, user vip level is %d, gold is %d.', $vip, $gold);
			throw new Exception('fake');
		}
		// 在这时候需要重新计算一下开启时刻，调整一下经验计算时刻。把所有经验都结算掉，重新开始计时
		$exp = self::fetchExp();

		// 好吧，符合要求了
		MyPractice::getInstance()->openFullDayMode();
		// 自然还是要扣钱的
		EnUser::getUserObj()->subGold(btstore_get()->VIP[$vip]['day_mode_open'][$practiceInfo['open_full_day']]);
		Logger::trace('Open 24 hours mode, need gold is %d.', btstore_get()->VIP[$vip]['day_mode_open'][$practiceInfo['open_full_day']]);
		// 都完事儿了
		MyPractice::getInstance()->save();
		EnUser::getUserObj()->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_PRACTICE_OPEN24MODE, btstore_get()->VIP[$vip]['day_mode_open'][$practiceInfo['open_full_day']], Util::getTime());

		// 给前端返回值
		return $exp;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */