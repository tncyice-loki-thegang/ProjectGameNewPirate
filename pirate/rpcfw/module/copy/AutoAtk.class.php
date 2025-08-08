<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AutoAtk.class.php 30506 2012-10-29 10:18:38Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/AutoAtk.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-29 18:18:38 +0800 (一, 2012-10-29) $
 * @version $Revision: 30506 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : AutoAtk
 * Description : 挂机逻辑实现类
 * Inherit     :
 **********************************************************************************************************************/
class AutoAtk
{
	/**
	 * 开始挂机
	 * 
	 * @param int $copyID						副本ID
	 * @param int $enemyID						部队ID
	 * @param int $times						次数
	 */
	static public function startAutoAtk($copyID, $enemyID, $times)
	{
		// 如果挂机次数大于100，则修改为100, 再大会超时的
		if ($times > CopyConf::MAX_AUTO_ATK_TIMES)
		{
			Logger::fatal('Can not auto attack over 100 times!');
			throw new Exception('fake');
		}
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		if (empty($uid)) 
		{
			Logger::fatal('Can not get auto attack info from session!');
			throw new Exception('fake');
		}
		// 没完成任务的话，不允许进行副本挂机
		if (!EnSwitch::isOpen(SwitchDef::ATTACK_CONTINOUS))
		{
			Logger::fatal('Can not auto attack before get task!');
			throw new Exception('fake');
		}

		// 获取人物信息
		$user = EnUser::getUserObj();
		// 获取人物副本信息
		$copyInst = new MyCopy();
		$copyInfo = $copyInst->getCopyInfo($copyID);
		// 检查是否可以挂机
		// 检查已经打过了怪物 和 行动力是否足够
		if (!isset(btstore_get()->AUTO_ATK[$enemyID]) || 
		    $copyInfo === false ||
		    !isset($copyInfo['va_copy_info']['defeat_id_times'][$enemyID]) ||
		    $user->getCurExecution() < btstore_get()->AUTO_ATK[$enemyID]['execution'] * $times)
	    {
			Logger::warning('Can not auto attack, enemy not defeat or execution not enough!');
			throw new Exception('fake');
	    }
		// 更新数据库
		return MyAutoAtk::startAutoAtk($uid, $copyID, $enemyID, $times);
	}

	/**
	 * 获取自动攻击信息
	 */
	static public function getAutoAtkInfo()
	{
		return MyAutoAtk::getInstance()->getAutoAtkInfo();
	}

	/**
	 * 取消挂机
	 * 
	 * @throws Exception
	 */
	static public function cancelAutoAtk()
	{
		/**************************************************************************************************************
 		 * 获取用户挂机信息并检查
		 **************************************************************************************************************/
		// 获取用户挂机信息
		$atkInfo = MyAutoAtk::getInstance()->getAutoAtkInfo();
		// 判断返回值
		if (empty($atkInfo) || $atkInfo['start_time'] == 0)
		{
			Logger::fatal('Can not get auto attack info from session!');
			throw new Exception('fake');
		}
		// 检查一下是否结束了
		else if ($atkInfo['annihilate'] >= $atkInfo['times'])
		{
			// 如果已经攻击次数大于等于想要攻击次数了,那么停止挂机
			$allGetInfo = MyAutoAtk::getInstance()->stopAutoAtking();
			MyAutoAtk::getInstance()->save();
			return array('va_auto_atk_info' => $allGetInfo);
		}
		// 获取剩余次数
		$lastTime = $atkInfo['times'] - $atkInfo['annihilate'];
		// 获取当前时刻
		$curTime = Util::getTime();
		// 获取所需攻击次数
		$atkTimes = floor(($curTime - $atkInfo['last_atk_time']) / CopyConf::AUTO_ATTACK_TIME);
		// 检查次数
		if ($atkTimes <= 0)
		{
			// 没到时候，停止返回就行
			$allGetInfo = MyAutoAtk::getInstance()->stopAutoAtking();
			MyAutoAtk::getInstance()->save();
			return array('va_auto_atk_info' => $allGetInfo);
		}
		// 判断是否剩余次数已经小于时间差所间隔的次数了
		$atkTimes = $lastTime < $atkTimes ? $lastTime : $atkTimes;
		Logger::debug('Need attack %d times!', $atkTimes);

		/**************************************************************************************************************
 		 * 自动攻击并给予奖励
		 **************************************************************************************************************/
		$ret = self::attackExecute($atkInfo, $atkTimes);
		// 停止挂机了
		$allGetInfo = MyAutoAtk::getInstance()->stopAutoAtking();
		// 查看是否已经设置了 va_auto_atk_info 这个字段
		if (!isset($ret['va_auto_atk_info']))
		{
			// 如果没设置，就已现在这个为准，如果设置过了，以设置的内容为准
			$ret['va_auto_atk_info'] = $allGetInfo;
		}
		// 存入数据库
		MyAutoAtk::getInstance()->save();
		// 将结果返回给前端
		return $ret;
	}

	/**
	 * 进行一次自动攻击操作
	 * 
	 * @throws Exception
	 */
	static public function attackOnce($isLogin = false)
	{
		/**************************************************************************************************************
 		 * 获取用户挂机信息并检查
		 **************************************************************************************************************/
		// 获取用户挂机信息
		$atkInfo = MyAutoAtk::getInstance()->getAutoAtkInfo();
		// 判断返回值
		if (empty($atkInfo) || $atkInfo['start_time'] == 0)
		{
			// 登陆检查的时候，不需要抛异常，直接返回就可以了
			if ($isLogin)
			{
				return 'ok';
			}
			// 其他情况下，因为已经是在挂机了，所以抛异常。恩，有种被前端哄骗的感觉啊……
			Logger::warning('Can not get auto attack info from session!');
			throw new Exception('fake');
		}
		// 检查一下是否结束了
		else if ($atkInfo['annihilate'] >= $atkInfo['times'])
		{
			// 如果已经攻击次数大于等于想要攻击次数了,那么停止挂机
			$allGetInfo = MyAutoAtk::getInstance()->stopAutoAtking();
			MyAutoAtk::getInstance()->save();
			return array('va_auto_atk_info' => $allGetInfo);
		}
		// 获取剩余次数
		$lastTime = $atkInfo['times'] - $atkInfo['annihilate'];
		// 获取当前时刻
		$curTime = Util::getTime();
		// 获取所需攻击次数
		$atkTimes = floor(($curTime - $atkInfo['last_atk_time']) / CopyConf::AUTO_ATTACK_TIME);
		// 检查次数
		if ($atkTimes <= 0)
		{
			// 登陆检查也可能不到五分钟, 那种情况不需要打这种危险的日志
			if (!$isLogin)
			{
				Logger::warning('Can not auto attack, not cd yet!');
			}
			return 'err';
		}
		// 判断是否剩余次数已经小于时间差所间隔的次数了
		$atkTimes = $lastTime < $atkTimes ? $lastTime : $atkTimes;
		Logger::debug('Need attack %d times!', $atkTimes);

		/**************************************************************************************************************
 		 * 自动攻击并给予奖励
		 **************************************************************************************************************/
		$ret = self::attackExecute($atkInfo, $atkTimes, 0, $isLogin);

		// 存入数据库
		MyAutoAtk::getInstance()->save();
		// 将结果返回给前端
		return $ret;
	}

	/**
	 * 执行自动攻击
	 * 
	 * @param string $atkInfo					自动攻击信息
	 * @param int $times						攻击次数
	 * @param int $needGold						每次所需金币
	 * @throws Exception
	 */
	static private function attackExecute($atkInfo, $times, $needGold = 0, $isLogin = false)
	{
		// 返回值初始化
		$atkTime = 0;
		$i = 0;
		$allGetInfo = false;
		// 获取人物信息
		$user = EnUser::getUserObj();
		// 获取公会科技
		$guild = GuildLogic::getBuffer($user->getUid());
		Logger::debug("Auto atk, get guild is %s.", $guild);
		// 获胜奖励经验
		$exp = floor(btstore_get()->AUTO_ATK[$atkInfo['army_id']]['exp'] * 
		                      (1 + $guild['battleExpAddition'] / CopyConf::LITTLE_WHITE_PERCENT));
		// 获胜奖励阅历
		$experience = floor(btstore_get()->AUTO_ATK[$atkInfo['army_id']]['experience'] * 
		                             (1 + $guild['battleExperienceAddition'] / CopyConf::LITTLE_WHITE_PERCENT) *
		                             EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_COPY));
		// 记录一个通知使用的金币数量
		$allGold = 0;
		// 策划们又改了，你说说，真是伤不起啊……  获取用户当前阵型上的所有英雄
		$heroList = EnFormation::getFormationInfo();
		// 根据次数进行攻击
		while ($i < $times)
		{
			// 行动力返回值
			$ret = true;
			// 扣除行动力
			if ($isLogin)
			{
				// 登陆的情况需要进行特殊处理
				$ret = $user->preSubExecution(btstore_get()->AUTO_ATK[$atkInfo['army_id']]['execution']);
			}
			else 
			{
				$ret = $user->subExecution(btstore_get()->AUTO_ATK[$atkInfo['army_id']]['execution']);
			}
			// 检查行动力, 如果这个人行动力不足
		    if (!$ret)
		    {
				Logger::debug('Not enough execution, already attack times %d, still need %d!', 
				              $i, btstore_get()->AUTO_ATK[$atkInfo['army_id']]['execution']);
		    	break;
		    }
			// 进行实际攻击的计数
			++$i;

			// 增加游戏币
			$user->addBelly(btstore_get()->AUTO_ATK[$atkInfo['army_id']]['belly']);
			// 增加所有主英雄经验, 否则卡等级时，用户其他英雄有可能会损失一部分经验
			$masterHeroObj = $user->getMasterHeroObj();

			// 增加经验值
			$masterHeroObj->addExp($exp);
			// 增加所有其他英雄经验
			foreach ($heroList as $hero)
			{
				if (!empty($hero) && !$hero->isMasterHero())
				{
					$hero->addExp($exp);
				}
			}
			// 增加阅历
			$user->addExperience($experience);
			// 扣除金币
			$user->subGold($needGold);
			// 记录一共花了多少金币
			$allGold += $needGold;
			// 进行攻击
			$atkTime = MyAutoAtk::getInstance()->attackOnce(btstore_get()->AUTO_ATK[$atkInfo['army_id']]['belly'],
			                                                $exp,
			                                                $experience);
			// 通知节日活动
			EnFestival::addAutoAtkPoint();
			// 检查攻击后的次数
			if (++$atkInfo['annihilate'] >= $atkInfo['times'])
			{
				// 如果已经攻击次数大于等于想要攻击次数了,那么停止挂机
				$allGetInfo = MyAutoAtk::getInstance()->stopAutoAtking();
				break;
			}
		}
		// 更新到数据库
		$user->update();
		// 发送金币通知
		if ($allGold > 0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_COPY_AUTOATTACK, $allGold, Util::getTime());
		}

		// 掉落物品
		$items = self::dropItems($atkInfo['army_id'], $i);
		// 如果还没结束挂机呢
		if ($allGetInfo === false)
		{
			// 那么需要保存这次挂机获取到的道具们
			MyAutoAtk::getInstance()->keepingItems($items['item']);
			// 获取所有已经获得的收益信息
			$allGetInfo = MyAutoAtk::getInstance()->getAllGetInfo();
		}
		// 如果已经结束挂机了，那么需要把最后一次掉落物品的信息加入进去
		else 
		{
			// 如果已经结束挂机了，那么需要带上所有挂机信息一并返回
			$allGetInfo['items'][] = $items['item'];
		}
		// 通知任务系统
		EnAchievements::notify($user->getUid(), AchievementsDef::AUTO_ATK_TIMES, $i);
		// 返回给前端
		return array('items' => $items, 'last_atk_time' => $atkTime, 
					 'add_exp' => $exp, 'experience' => $experience,
		             'exp' => $user->getMasterHeroObj()->getExp(), 
		             'lv' => $user->getMasterHeroObj()->getLevel(),
		             'once_times' => $i, 'va_auto_atk_info' => $allGetInfo);
	}

	/**
	 * 掉落物品
	 * 
	 * @param int $enemyID						挂机部队ID
	 * @param int $times						掉落次数
	 */
	static private function dropItems($enemyID, $times)
	{
		// 声明背包信息返回值
		$bagInfo = array();
		$itemArr = array();
		// 掉落表ID
		$dropIDs = btstore_get()->AUTO_ATK[$enemyID]['drop_id'];
		Logger::debug("Drop id is %s in auto atk.", $dropIDs);
		// 如果配置的有掉落表
		if (!empty($dropIDs[0]) && $times != 0)
		{
			// 掉落道具, 放到背包里
			$bag = BagManager::getInstance()->getBag();
			// 根据次数进行掉落物品
			$itemIDs = array();
			foreach ($dropIDs as $dropID)
			{
				// 根据掉落表，依次掉落物品
				$itemIDs = array_merge($itemIDs, 
									   ItemManager::getInstance()->dropItems(array_fill(0, $times, $dropID)));
			}
			// 记录发送的信息
			$msg = chatTemplate::prepareItem($itemIDs);
			// 标志是否背包已经满了
			$deleted = FALSE;
			// 循环处理所有的掉落物品
			foreach ($itemIDs as $itemID)
			{
				// 背包还没满的时候，就往背包里面塞吧……
				if ($deleted == FALSE)
				{
					// 先获取数据信息，保存。
					$itemTmp = ItemManager::getInstance()->itemInfo($itemID);
					// 塞一个货到背包里，可以使用临时背包
					if ($bag->addItem($itemID, TRUE) == FALSE)
					{
						// 如果连临时背包都满了的话， 删除该物品
						ItemManager::getInstance()->deleteItem($itemID);
						// 修改标志量
						$deleted = TRUE;
					}
					else
					{
						// 保留物品详细信息，传给前端
						$itemArr[] = $itemTmp;
					}
				}
				// 背包满了，不行了
				else 
				{
					// 全部删除，多可惜啊……换成钱不行么？或者送给刘洋的号……
					ItemManager::getInstance()->deleteItem($itemID);
				}
			}
			// 保存用户背包数据，并获取改变的内容
			$bagInfo = $bag->update();
			// 发送信息
			$user = EnUser::getUserObj();
			chatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
		}
		// 返回已经掉落的各种IDs
		return array('item' => $itemArr, 'bag' => $bagInfo);
	}

	/**
	 * 使用金币立刻进行一次挂机
	 */
	static public function attackOnceByGold()
	{
		/**************************************************************************************************************
 		 * 获取用户挂机信息并检查
		 **************************************************************************************************************/
		// 获取用户挂机信息
		$atkInfo = MyAutoAtk::getInstance()->getAutoAtkInfo();
		// 判断返回值
		if (empty($atkInfo) || $atkInfo['start_time'] == 0)
		{
			Logger::fatal('Can not get auto attack info from session!');
			throw new Exception('fake');
		}
		// 检查一下是否结束了
		else if ($atkInfo['annihilate'] >= $atkInfo['times'])
		{
			// 如果已经攻击次数大于等于想要攻击次数了,那么停止挂机
			$allGetInfo = MyAutoAtk::getInstance()->stopAutoAtking();
			MyAutoAtk::getInstance()->save();
			return array('va_auto_atk_info' => $allGetInfo);
		}

		// 获取人物信息
		$user = EnUser::getUserObj();
		// 检查金币
		if ($user->getGold() < CopyConf::AUTO_ATTACK_COIN)
		{
			Logger::fatal('Can not rapid not enough gold, user have %d!', $user->getGold());
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 自动攻击一次并给予奖励
		 **************************************************************************************************************/
		$ret = self::attackExecute($atkInfo, 1, CopyConf::AUTO_ATTACK_COIN);

		// 存入数据库
		MyAutoAtk::getInstance()->save();
		// 将结果返回给前端
		return $ret;
	}

	/**
	 * 登陆时检查并处理挂机
	 */
	static public function checkWhenLogin()
	{
		// 从session里面获取信息
		$ret = RPCContext::getInstance()->getSession('tmp.auto_atk');
		// 毁尸灭迹
		RPCContext::getInstance()->unsetSession('tmp.auto_atk');
		// 如果前端反复调用，那么就出错了
		if (empty($ret))
		{
			$ret = 'ok';
		}
		// 返回
		return $ret;
	}

	/**
	 * 使用金币，结束全部挂机
	 */
	static public function endAttackByGold()
	{
		/**************************************************************************************************************
 		 * 获取用户挂机信息并检查
		 **************************************************************************************************************/
		// 获取用户挂机信息
		$atkInfo = MyAutoAtk::getInstance()->getAutoAtkInfo();
		// 判断返回值
		if (empty($atkInfo) || $atkInfo['start_time'] == 0)
		{
			// 其他情况下，因为已经是在挂机了，所以抛异常。恩，有种被前端哄骗的感觉啊……
			Logger::fatal('Can not get auto attack info from session!');
			throw new Exception('fake');
		}
		// 检查一下是否结束了
		else if ($atkInfo['annihilate'] >= $atkInfo['times'])
		{
			// 如果已经攻击次数大于等于想要攻击次数了,那么停止挂机
			$allGetInfo = MyAutoAtk::getInstance()->stopAutoAtking();
			MyAutoAtk::getInstance()->save();
			return array('va_auto_atk_info' => $allGetInfo);
		}
		// 获取剩余次数
		$atkTimes = $atkInfo['times'] - $atkInfo['annihilate'];

		// 获取人物信息
		$user = EnUser::getUserObj();
		// 检查金币
		if ($user->getGold() < CopyConf::AUTO_ATTACK_COIN * $atkTimes)
		{
			Logger::fatal('Can not rapid not enough gold, user have %d, need %d!', 
			              $user->getGold(), CopyConf::AUTO_ATTACK_COIN * $atkTimes);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 自动攻击一次并给予奖励
		 **************************************************************************************************************/
		$ret = self::attackExecute($atkInfo, $atkTimes, CopyConf::AUTO_ATTACK_COIN);

		// 存入数据库
		MyAutoAtk::getInstance()->save();
		// 将结果返回给前端
		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
