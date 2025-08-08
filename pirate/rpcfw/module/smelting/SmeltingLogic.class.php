<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SmeltingLogic.class.php 40340 2013-03-08 09:31:27Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/SmeltingLogic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-03-08 17:31:27 +0800 (五, 2013-03-08) $
 * @version $Revision: 40340 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : SmeltingLogic
 * Description : 装备制作逻辑类
 * Inherit     : 
 **********************************************************************************************************************/
class SmeltingLogic
{
	/**
	 * 获取用户的装备制作信息
	 */
	public static function getSmeltingInfo() 
	{
		// 获取工匠离开时刻
		$ret = SmeltingDao::getArtificerLeaveTime();
		// 获取用户装备制作信息
		$smeltingInfo = MySmelting::getInstance()->getUserSmeltingInfo();
		$smeltingInfo['artificer_leave_time'] = $ret[SmeltingConf::ARTIFICER_REFRESH_TIME];
		// 前端不要这个，前端要算好的……
		unset($smeltingInfo['smelt_times']);
		// 都弄好了，您瞧着吧
		return $smeltingInfo;
	}

	/**
	 * 一次性进行全部熔炼
	 * 
	 * @param int $type							准备多大开销
	 * @param int $itemType						哪种装备
	 * 
	 * @throws Exception
	 */
	public static function smeltingAll($type, $itemType)
	{
		/**************************************************************************************************************
		 * 获取已熔炼信息
 		 **************************************************************************************************************/
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 检查VIP和用户等级需求
		if (btstore_get()->VIP[$user->getVip()]['smelting_all']['can'] == 0 && 
			$user->getLevel() < btstore_get()->VIP[$user->getVip()]['smelting_all']['lv'])
		{
			Logger::warning('Vip or lv is not enough!');
			throw new Exception('fake');
		}

		// 记录返回值
		$quality = 0;
		$luckyTimes = 0;
		$bag = array();
		$artIDs = array();
		$integral = array();
		$itemInfo = array();
		// 获取用户装备制作信息
		$smelting = MySmelting::getInstance()->getUserSmeltingInfo();
		// 获取用户当日制作次数
		$times = MySmelting::getInstance()->getTodaySmeltTimes();
		// 有次数的话，进行熔炼
		while ($times['smelt'] > 0)
		{
			/**********************************************************************************************************
			 * 对次数进行检查
	 		 **********************************************************************************************************/
			// 获取已经执行过的熔炼次数
			$smeltTimes = 0;
			// 判断参数种类
			if ($itemType == SmeltingConf::TYPE_RING)
			{
				// 获取戒指的熔炼次数
				$smeltTimes = $smelting['smelt_times_1'];
			}
			else if ($itemType == SmeltingConf::TYPE_CLOAK)
			{
				// 获取披风的熔炼次数
				$smeltTimes = $smelting['smelt_times_2'];
			}
			else 
			{
				Logger::fatal('The type is %d!', $itemType);
				throw new Exception('fake');
			}
			// 熔炼次数满了，需要进行制作
			if ($smeltTimes >= SmeltingConf::MAX_SMELTING_TIMES)
			{
				// 获取装备
				$ret = self::getSmeltingItem($itemType);
				// 背包满了，你懂的
				if ($ret['itemInfo'] == 'err')
				{
					return array('itemInfo' => array('item' => $itemInfo, 'bag' => $bag), 
								 'integral' => $integral, 'artIDs' => $artIDs, 
								 'luckyTimes' => $luckyTimes, 'quality_'.$itemType => $quality);
				}
				// 正常获取到值了，需要进行保存
				$integral = $ret['integral'];
				$itemInfo[$ret['itemInfo']['item']['item_id']] = $ret['itemInfo']['item'];
				$itemInfo[$ret['itemInfo']['item']['item_id']]['quality'] = $quality;
				// 整理一下背包信息
				$tmp = $ret['itemInfo']['bag'];
				$grid = key($tmp);
				$bag[$ret['itemInfo']['bag'][$grid]['item_id']] = $ret['itemInfo']['bag'];
				$quality = 0;
			}
			// 次数不满的话, 需要进行一次熔炼
			else 
			{
				// 熔炼
				$ret = self::smeltingOnce($type, $itemType);
				// 如果返回值很奇怪，则表示不能再进行下去了，需要直接返回
				if ($ret == 'err')
				{
					return array('itemInfo' => array('item' => $itemInfo, 'bag' => $bag), 
								 'integral' => $integral, 'artIDs' => $artIDs, 
								 'luckyTimes' => $luckyTimes, 'quality_'.$itemType => $quality);
				}
				// 正常熔炼了，需要记录一下熔炼结果
				$quality += $ret['quality_'.$itemType];
				$luckyTimes += $ret['isLucky'];
				if (!empty($ret['artID']))
				{
					$artIDs[] = $ret['artID'];
				}
			}

			/**********************************************************************************************************
			 * 重新获取次数
	 		 **********************************************************************************************************/
			// 获取用户装备制作信息
			$smelting = MySmelting::getInstance()->getUserSmeltingInfo();
			// 获取用户当日制作次数
			$times = MySmelting::getInstance()->getTodaySmeltTimes();
		}
		// 返回结果
		// logger::warning($itemInfo);
		return array('itemInfo' => array('item' => $itemInfo, 'bag' => $bag), 
					 'integral' => $integral, 'artIDs' => $artIDs, 
					 'luckyTimes' => $luckyTimes, 'quality_'.$itemType => $quality);
	}

	/**
	 * 进行一次熔炼
	 * 
	 * @param int $type							准备多大开销
	 * @param int $itemType						哪种装备
	 */
	public static function smeltingOnce($type, $itemType) 
	{
		/**************************************************************************************************************
		 * 参数检查
 		 **************************************************************************************************************/
		// 获取人物等级
		$lv = EnUser::getUserObj()->getLevel();
		// 获取用户VIP等级
		$vipLv = EnUser::getUserObj()->getVip();
		// 准备好获取钱数和基础值
		$money = 0;
		$quality = 0;

		/**************************************************************************************************************
		 * 获取当前等级所需的参数
 		 **************************************************************************************************************/
		$tmp = array();
		// 循环查看素有的熔炼参数
		foreach (btstore_get()->SMELTING['belly_smelt_bases'] as $base)
		{
			// 如果等级小于当前等级，那么记录下
			if ($base['lv'] > $lv)
			{
				$tmp = $base;
				break;
			}
		}
		// 如果策划配错了，哇哈哈，小俞，这就是证据啊……
		if (empty($tmp))
		{
			Logger::warning('Can not smelt, user level is %d, go to find xiaoYu!', $lv);
			throw new Exception('fake');
		}
		// 保存获取到的基础值
		$quality = $tmp['base'];
		Logger::debug('SmeltingOnce::base quality is %d, user level is %d.', $quality, $lv);

		// 如果是游戏币制作
		if ($type == SmeltingConf::TYPE_BELLY)
		{
			// 保存获取到的游戏币数量
			$money = $tmp['belly'];
			// 顺便检查一下游戏币是否足够
			if ($money > EnUser::getUserObj()->getBelly())
			{
				Logger::trace('Can not smelt, money not enough, need %d!', $money);
				return 'err';
			}
		}
		// 金币制作
		else 
		{
			/**********************************************************************************************************
		     * 根据VIP等级，获取此人可以执行制作种类
 		     **********************************************************************************************************/
			$tmp = btstore_get()->VIP[$vipLv]['gold_smelt_open'][$type];
			// 如果没获取到制作参数的话，就异常掉算了
			if (empty($tmp))
			{
				Logger::fatal('The type is %d!', $type);
				throw new Exception('fake');
			}
			// 保存获取到的金币数量
			$money = $tmp['gold'];
			// 这个值需要进行加算
			$quality += $tmp['base'];
			// 顺便检查一下金币是否足够
			if ($money > EnUser::getUserObj()->getGold())
			{
				Logger::trace('Can not smelt, money not enough, need %d!', $money);
				return 'err';
			}
		}
		Logger::debug('SmeltingOnce::vip quality is %d, type is %d.', $quality, $type);

		/**************************************************************************************************************
		 * 获取已熔炼信息
 		 **************************************************************************************************************/
		// 获取用户装备制作信息
		$smelting = MySmelting::getInstance()->getUserSmeltingInfo();
		// 判断是否达到CD时间
		if ($smelting['cd_time'] > Util::getTime())
		{
			Logger::warning('Can not smelt, not cd yet!');
			throw new Exception('fake');
		}
		// 获取用户当日制作次数
		$times = MySmelting::getInstance()->getTodaySmeltTimes();
		// 对制作次数进行判断，查看是否超出了最大次数
		if ($times['smelt'] <= 0)
		{
			Logger::warning('Can not smelt, out of today max range, now is %d!', $times['smelt']);
			throw new Exception('fake');
		}
		// 获取已经执行过的熔炼次数
		$smeltTimes = 0;
		// 判断参数种类
		if ($itemType == SmeltingConf::TYPE_RING)
		{
			// 获取戒指的熔炼次数
			$smeltTimes = $smelting['smelt_times_1'];
		}
		else if ($itemType == SmeltingConf::TYPE_CLOAK)
		{
			// 获取披风的熔炼次数
			$smeltTimes = $smelting['smelt_times_2'];
		}
		else 
		{
			Logger::fatal('The type is %d!', $itemType);
			throw new Exception('fake');
		}
		// 你看看，都熔炼成啥样了，还熔炼呢！
		if ($smeltTimes >= SmeltingConf::MAX_SMELTING_TIMES)
		{
			Logger::warning('Can not smelting item, smelting times is %d!', $smeltTimes);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
		 * 进行装备制作
 		 **************************************************************************************************************/
		// 然后，欢迎我们亲爱的师傅们！
		$artificers = $smelting['va_smelt_info']['artificers'];
		// 掉落工匠的概率
		$newArt = btstore_get()->SMELTING['drop_art_weight'];
		// 幸运熔炼
		$luckSmelt = btstore_get()->SMELTING['lucky'];
		// 初始化暴击值
		$critical = btstore_get()->SMELTING['critical_base'];
		// 初始化暴击倍率
		$criticalRio = btstore_get()->SMELTING['critical_ratio_base'];
		// 计算各种权重
		foreach ($artificers as $artificer)
		{
			// 计算所有增加的概率
			// 计算品质增加值
			$quality += btstore_get()->ARTIFICER[$artificer['id']]['quality_low'];
			$quality += btstore_get()->ARTIFICER[$artificer['id']]['quality_high'];
			// 计算暴击
			$critical += btstore_get()->ARTIFICER[$artificer['id']]['critical_low'];
			$critical += btstore_get()->ARTIFICER[$artificer['id']]['critical_high'];
			// 计算掉落工匠的概率
			$newArt += btstore_get()->ARTIFICER[$artificer['id']]['new_artificer'];
			// 计算暴击倍率
			$criticalRio += btstore_get()->ARTIFICER[$artificer['id']]['critical_ratio'];
			// 计算幸运熔炼概率
			$luckSmelt += btstore_get()->ARTIFICER[$artificer['id']]['lucky'];
		}
		Logger::debug('SmeltingOnce::artificer quality is %d.', $quality);

		// 随机出结果 , 判断是否暴击
		$randRet = rand(0, SmeltingConf::LITTLE_WHITE_PERCENT);
		// 返回给前端用的值
		$isCritical = 0;
		// 查看是否暴击，暴击了以后，乘以暴击倍率神马的
		if ($randRet <= $critical)
		{
			$quality *= (1 + $criticalRio / SmeltingConf::LITTLE_WHITE_PERCENT);
			$isCritical = 1;
		}
		Logger::debug('Quality is %d.', $quality);

		/**************************************************************************************************************
		 * 扣除成本并给予好处
 		 **************************************************************************************************************/
		// 扣除游戏币
		if ($type == SmeltingConf::TYPE_BELLY)
		{
			EnUser::getUserObj()->subBelly($money);
		}
		// 扣除金币
		else 
		{
			EnUser::getUserObj()->subGold($money);
			// 发送金币通知
			Statistics::gold(StatisticsDef::ST_FUNCKEY_SMELTING_SMELTING, $money, Util::getTime());
		}
		// 记录熔炼次数和熔炼品质
		$ret = MySmelting::getInstance()->smelt($itemType, $quality);

		// 查看是否幸运熔炼了
		$randRet = rand(0, SmeltingConf::LITTLE_WHITE_PERCENT);
		// 返回给前端用的值
		$isLucky = 0;
		// 运气不错，加上一次幸运熔炼次数
		if ($randRet <= $luckSmelt)
		{
			$isLucky = 1;
		}
		// 没有幸运熔炼的时候，才计算熔炼次数
		else 
		{
			// 记录一次熔炼次数
			MySmelting::getInstance()->addSmeltingTimes();
		}

		// 检查是否掉落工匠
		$artID = self::dropArtificer($newArt);

		// 通知任务系统，熔炼一次了
		TaskNotify::operate(TaskOperateType::ARMING_PRODUCE);
		// 更新数据库 
		MySmelting::getInstance()->save();
		EnUser::getUserObj()->update();

		// 通知成就系统
		EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::SMELTING_QUALITY, $ret);

		// 返回给前端结果
		return array('quality_'.$itemType => $quality, 'isLucky' => $isLucky, 
		             'artID' => $artID, 'isCritical' => $isCritical);
	}

	/**
	 * 获取好东西
	 * 
	 * @param int $itemType						哪种装备
	 */
	public static function getSmeltingItem($itemType)
	{
		// 获取用户装备制作信息
		$smelting = MySmelting::getInstance()->getUserSmeltingInfo();
		// 判断是否达到CD时间
		if ($smelting['cd_time'] > Util::getTime())
		{
			Logger::warning('Can not get item, not cd yet!');
			throw new Exception('fake');
		}
		// 获取已经执行过的熔炼次数 和熔炼品质
		$smeltTimes = 0;
		$quality = 0;
		// 初始化CD时刻
		$cdTime = 0;
		// 初始化积分信息
		$integral = array();
		// 判断参数种类
		if ($itemType == SmeltingConf::TYPE_RING)
		{
			// 获取戒指的熔炼次数
			$smeltTimes = $smelting['smelt_times_1'];
			// 获取戒指的熔炼品质
			$quality = $smelting['quality_1'];
		}
		else if ($itemType == SmeltingConf::TYPE_CLOAK)
		{
			// 获取披风的熔炼次数
			$smeltTimes = $smelting['smelt_times_2'];
			// 获取披风的熔炼品质
			$quality = $smelting['quality_2'];
		}
		else 
		{
			Logger::fatal('The type is %d!', $itemType);
			throw new Exception('fake');
		}
		// 检查熔炼次数是否已经满了
		if ($smeltTimes < SmeltingConf::MAX_SMELTING_TIMES)
		{
			Logger::warning('Can not fetch item, smelt times is %d!', $smeltTimes);
			throw new Exception('fake');
		}
		// 通过检查了，可以获取物品
		$itemInfo = self::dropItem($itemType, $quality);
		// 如果背包满了的话，就别清空了，给人一次机会
		if ($itemInfo !== 'err')
		{
			// 增加积分
			$integral = self::getIntegral($quality);
			// 更新CD时刻
			$cdTime = MySmelting::getInstance()->setCdTime();
			// 清空该项的熔炼品质和熔炼次数
			MySmelting::getInstance()->resetSmeltTimes($itemType);
			MySmelting::getInstance()->save();

			// 通知活跃度系统
			EnActive::addSmeltingTimes();
			// 通知节日系统
			EnFestival::addSmeltingPoint();
		}
		// 返回给前端
		return array('itemInfo' => $itemInfo, 'cd_time' => $cdTime, 'integral' => $integral);
	}

	/**
	 * 获取积分
	 * 
	 * @param int $qualityValue					最终品质
	 */
	private static function getIntegral($qualityValue)
	{
		// 记录实际的档次
		$lv = array('type' => 1, 'integral' => 0);
		// 遍历所有档次
		foreach (btstore_get()->SCORE_EXCHANGE['grade_integral'] as $grade)
		{
			// 如果没有达到这个档次，那么直接退出，使用上一档即可
			if ($grade['value'] > $qualityValue)
			{
				break;
			}
			// 记录遍历到哪里了
			$lv = $grade;
		}
		Logger::debug("Quality is %d, get grade is %s.", $qualityValue, $lv);
		// 加上积分
		return MySmelting::getInstance()->addIntegral($lv['type'], $lv['integral']);
	}

	/**
	 * 掉落物品
	 * 
	 * @param int $type							哪种物品
	 * @param int $qualityValue					最终品质
	 */
	private static function dropItem($type, $qualityValue)
	{
		// 将品质取整
		$qualityValue = intval($qualityValue);
		// 获取掉落表
		$dropArr = array();
		// 根据参数判断哪个掉落表
		if ($type == SmeltingConf::TYPE_RING)
		{
			// 把戒指的掉落表拿出来
			$dropArr = btstore_get()->RING->toArray();
		}
		else if ($type == SmeltingConf::TYPE_CLOAK)
		{
			// 把披风的掉落表拿出来
			$dropArr = btstore_get()->CLOAK->toArray();
		}
		// 掉落表ID
		$dropID = 0;
		// 根据品质，获取掉落表ID
		foreach ($dropArr as $dropper)
		{
			// 以防万一，先赋值后推出，这样保证能有一个值不会为0 
			$dropID = $dropper['drop_id'];
			// 如果恰巧在区间内
			if ($qualityValue <= $dropper['quality_max'] && $qualityValue >= $dropper['quality_min'])
			{
				// 得到掉落表ID，退出
				break;
			}
		}
		Logger::debug('Drop item, quality is %d, drop id is %d.', $qualityValue, $dropID);
		// 掉落道具, 放到背包里
		$bag = BagManager::getInstance()->getBag();
		// 掉落物品吧
		$itemIDs = ItemManager::getInstance()->dropItem($dropID);
		// 如果没有掉落东西，那么就抛异常了
		if (empty($itemIDs[0]))
		{
			Logger::fatal('Not drop anything! Go to find little white!');
			throw new Exception('fake');
		}
		// 记录发送的信息
		$msg = chatTemplate::prepareItem($itemIDs);
		// 听说只掉落那么一件东西
		$itemID = $itemIDs[0];
		// 获取道具信息
		$itemInfo = ItemManager::getInstance()->itemInfo($itemID);
		// 塞物品货到背包里，可以使用临时背包
		if ($bag->addItem($itemID, TRUE) == FALSE)
		{
			// 如果连临时背包都满了的话， 删除该物品
			ItemManager::getInstance()->deleteItem($itemID);
			// 背包满了，你还想怎样？
			return 'err';
		}
		// 保存用户背包数据，并获取改变的内容
		$bagInfo = $bag->update();
		// 发送信息
		$user = EnUser::getUserObj();
		chatTemplate::sendSmeltingItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
		// 返回已经掉落的各种IDs
		return array('item' => $itemInfo, 'bag' => $bagInfo);
	}

	/**
	 * 查看是否掉落工匠
	 */
	private static function dropArtificer($newArt)
	{
		// 随机出结果 , 判断是否掉落工匠
		$randRet = rand(0, SmeltingConf::LITTLE_WHITE_PERCENT);
		Logger::debug('Drop Artificer rand ret is %d.', $randRet);
		// 哟~不错，没随机出来，那么……
		if ($randRet >= $newArt)
		{
			// 直接返回，没有掉落任何东西呀！
			return 0;
		}
		// 随机出来了，掉了个工匠，小子运气不错
		return self::__checkGetArtificer();
	}

	/**
	 * 聘请一位工匠
	 */
	public static function inviteArtificer() 
	{
		// 获取用户今日的金币开启次数
		$goldInviteTimes = MySmelting::getInstance()->getTodaySmeltTimes();
		// 获取用户VIP等级
		$vipLv = EnUser::getUserObj()->getVip();
		// 获取可开启的次数
		$times = btstore_get()->VIP[$vipLv]['artificer_times_gold']['times'];
		// 如果可开启次数为0 或者 超过当日最大次数
		if ($times === 0 || $times <= $goldInviteTimes['artificer'])
		{
			Logger::warning('Can not invite Artificer by gold, vip level not enough!');
			throw new Exception('fake');
		}
		// 检查金子吧
		$gold = btstore_get()->VIP[$vipLv]['artificer_times_gold']['gold'];
		// 获取用户信息
		$userInfo = EnUser::getUser();
		// 因为要扣钱，所以记录下日志，省的你不认账
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $gold);
		if ($gold > $userInfo['gold_num'])
		{
			// 钱不够，直接返回
			return 'err';
		}
		// 获取一个工匠
		$artID = self::__checkGetArtificer();
		// 真的获取到工匠了，才扣钱
		if ($artID != 0)
		{
			// 增加一次金币邀请的次数
			MySmelting::getInstance()->addArtficerInviteTimes();
			MySmelting::getInstance()->save();

			// 扣钱
			EnUser::getInstance()->subGold($gold);
			EnUser::getInstance()->update();
			// 发送金币通知
			Statistics::gold(StatisticsDef::ST_FUNCKEY_SMELTING_INVITE, $gold, Util::getTime());
		}
		// 返回前端这次获取的工匠ID
		return $artID;
	}

	/**
	 * 获取一名工匠
	 */
	private static function __checkGetArtificer()
	{
		/**************************************************************************************************************
		 * 计算各个等级工匠出现的权重
 		 **************************************************************************************************************/
		// 获取用户装备制作信息
		$smelting = MySmelting::getInstance()->getUserSmeltingInfo();
		// 看看亲爱的师傅们来了几位
		$artificers = $smelting['va_smelt_info']['artificers'];
		// 高级工匠获取几率增加权重数组
		$hiWeight = btstore_get()->SMELTING['art_lv_weight']->toArray();
		// 计算高级工匠获取几率的权重
		foreach ($artificers as $artificer)
		{
			// 查看所有等级提升权重
			foreach (btstore_get()->ARTIFICER[$artificer['id']]['new_art_wights'] as $newArt)
			{
				// 如果不为空的时候
				if ($newArt['lv'] != 0)
				{
					// 如果没设置过，那么先进行初始化工作
					if (!isset($hiWeight[$newArt['lv']]))
					{
						$hiWeight[$newArt['lv']]['weight'] = 0;
					}
					// 计算所有增加的概率
					$hiWeight[$newArt['lv']]['weight'] += $newArt['weight'];
				}
			}
		}

		/**************************************************************************************************************
		 * 先统计出可能出现的所有等级所有工匠
 		 **************************************************************************************************************/
		// 获取用户等级
		$userLv = EnUser::getUserObj()->getLevel();
		// 权重数组
		$arrWeight = array();
		// 可以获取到的最大工匠等级
		$maxLv = 0;
		// 循环查看所有的工匠，看看都谁愿意来
		foreach (btstore_get()->ARTIFICER as $artificer)
		{
			// 先判断一个工匠的出现等级
			if (btstore_get()->ARTIFICER[$artificer['id']]['need_lv'] > $userLv)
			{
				// 还不能邀请这个工匠呢，需要升级人物等级
				continue;
			}
			// 查看所有已有的工匠，过滤那些不会来的
			foreach ($artificers as $alreadyArt)
			{
				// 如果本尊已经驾到了 或者 师傅之类的已经到了
				if ($artificer['id'] == $alreadyArt['id'] || 
				   ($artificer['type'] == $alreadyArt['type'] && $artificer['lv'] <= $alreadyArt['lv']))
				{
					// 就不屈尊了，拜拜
					continue 2;
				}
			}
			// 选个头头
			if ($maxLv < $artificer['lv'])
			{
				$maxLv = $artificer['lv'];
			}
			// 呀，我发现自己有hr的潜质啊！
			$arrWeight[] = array('id' => $artificer['id'], 'type' => $artificer['type'], 
			                     'lv' => $artificer['lv'], 'weight' => $artificer['weight']);
		}
		// 如果一个都没有，表明已经获取了所有的高级工匠
		if (empty($arrWeight))
		{
			return 0;
		}

		/**************************************************************************************************************
		 * 根据工匠等级进行过滤
 		 **************************************************************************************************************/
		Logger::debug('Level weight array before sub is %s. ', $hiWeight);
		Logger::debug('All artificer array before sub is %s.', $arrWeight);
		// 裁剪掉一些不能出现的等级范围
		foreach ($hiWeight as $lv => $weight)
		{
			// 如果超出了最大可来工匠等级，那么直接裁剪掉 : 这段代码写的很不爽啊，明明都是随机数，谁知道随到哪了。真讨厌。
			if ($lv > $maxLv)
			{
				unset($hiWeight[$lv]);
			}
			// 12/03/03  还需要判断小于的时候，工匠列表里面有没有这个等级了
			else 
			{
				// 设立一个标志位
				$flg = false;
				// 循环查看所有工匠
				foreach ($arrWeight as $weight)
				{
					// 如果有这个等级，那么退出
					if ($lv == $weight['lv'])
					{
						$flg = true;
						break;
					}
				}
				// 查看是否真没有这个等级了
				if (!$flg)
				{
					// 没有的话一样删 
					unset($hiWeight[$lv]);
				}
			}
		}
		Logger::debug('Level weight array after sub is %s. ', $hiWeight);
		// 裁剪后了, 表明所有能随机出来的等级都是对的，不然就弄死你
		$index = Util::noBackSample($hiWeight, 1);
		$index = intval($index[0]);
		Logger::debug('Random level is %d. ', $hiWeight[$index]['lv']);
		// 根据随机出来的等级，对所有工匠进行裁剪，删除掉其他不相同的等级
		foreach ($arrWeight as $key => $weight)
		{
			// 等级不相同，就直接删掉
			if ($weight['lv'] != $hiWeight[$index]['lv'])
			{
				unset($arrWeight[$key]);
			}
		}
		Logger::debug('All artificer array after sub is %s.', $arrWeight);

		/**************************************************************************************************************
		 * 行了，随机吧
 		 **************************************************************************************************************/
		// 好了，开始随机
		$index = Util::noBackSample($arrWeight, 1);
		$index = $index[0];
		Logger::debug('Artificer list is %s, index is %d.', $arrWeight, $index);
		// 记录随机结果
		$ret = MySmelting::getInstance()->addNewArtificer($arrWeight[$index]['id'], 
		                                                  $arrWeight[$index]['type'], $arrWeight[$index]['lv']);

		// 获取工匠个数
		$count = count($ret);
		// 通知成就系统
		EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::ARTIFICER_NUM, $count);

		// 返回获取的工匠ID
		return $arrWeight[$index]['id'];
	}

	/**
	 * 使用金币清除CD时刻
	 */
	public static function clearCDByGold() 
	{
		// 获取用户装备制作信息
		$smelting = MySmelting::getInstance()->getUserSmeltingInfo();
		// 获取CD时刻, 看看一共需要多少个金币
		$num = ceil(($smelting['cd_time'] - Util::getTime()) / 60) * btstore_get()->SMELTING['gold_per_time'];
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
		MySmelting::getInstance()->resetCdTime();
		MySmelting::getInstance()->save();

		// 扣钱
		EnUser::getInstance()->subGold($num);
		EnUser::getInstance()->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_SMELTING_CLEARCDTIME, $num, Util::getTime());
		// 返回给前端，实际使用的金币数量
		return $num;
	}

	/**
	 * 积分兑换物品
	 * 
	 * @param int $itemTID						物品模板ID
	 */
	public static function integralExchange($itemTID)
	{
		// 获取物品所需积分信息
		if (empty(btstore_get()->SCORE_EXCHANGE['exchange_ring_magic'][$itemTID]))
		{
			Logger::fatal('Wrong item template id, id is %d!', $itemTID);
			throw new Exception('fake');
		}
		// 掉落道具, 放到背包里
		$bag = BagManager::getInstance()->getBag();
		// 生成物品
		$itemIDs = ItemManager::getInstance()->addItem($itemTID, 1);
		// 记录发送的信息
		$msg = chatTemplate::prepareItem($itemIDs);
		// 听说只掉落那么一件东西
		$itemID = $itemIDs[0];
		// 获取道具信息
		$itemInfo = ItemManager::getInstance()->itemInfo($itemID);
		// 塞物品货到背包里，可以使用临时背包
		if ($bag->addItem($itemID, FALSE) == FALSE)
		{
			// 如果连临时背包都满了的话， 删除该物品
			ItemManager::getInstance()->deleteItem($itemID);
			// 返回背包满了
			return 'err';
		}
		// 直接减去积分看看效果
		if (!MySmelting::getInstance()->subIntegral(btstore_get()->SCORE_EXCHANGE['exchange_ring_magic'][$itemTID]['type'],
		                                            btstore_get()->SCORE_EXCHANGE['exchange_ring_magic'][$itemTID]['integral']))
		{
			Logger::fatal('Sub integral error, not enough!');
			throw new Exception('fake');
		}
		// 发送信息
		$user = EnUser::getUserObj();
		chatTemplate::sendSmeltingExchangeItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
		// 保存分数信息
		MySmelting::getInstance()->save();
		// 保存用户背包数据，并获取改变的内容
		return array('bag' => $bag->update(),
		             'type' => btstore_get()->SCORE_EXCHANGE['exchange_ring_magic'][$itemTID]['type']);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */