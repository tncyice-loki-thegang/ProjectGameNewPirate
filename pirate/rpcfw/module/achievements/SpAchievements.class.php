<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SpAchievements.class.php 24014 2012-07-17 07:45:29Z HongyuLan $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/SpAchievements.class.php $
 * @author $Author: HongyuLan $(liuyang@babeltime.com)
 * @date $Date: 2012-07-17 15:45:29 +0800 (二, 2012-07-17) $
 * @version $Revision: 24014 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : SpAchievements
 * Description : 特殊成就内部实现类
 * Inherit     :
 **********************************************************************************************************************/
class SpAchievements
{

	/**
	 * 查看英雄一身装备
	 * 
	 * @param int $hid							英雄ID
	 */
	static public function checkHeroEquipColor($hid)
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[AchievementsDef::HERO_ITEM_COLOR]))
		{
			return $achieveArr;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[AchievementsDef::HERO_ITEM_COLOR];
		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieveID)
		{
			// 检查是否已经获得了这个成就
			if (!MyAchievements::getInstance()->checkAlreadyGet($achieveID))
			{
				// 如果尚未获取这个成就的话, 检查是否可以获取这个成就
				$num = btstore_get()->ACHIEVE[$achieveID]['condition'][0];
				$qua = btstore_get()->ACHIEVE[$achieveID]['condition'][1];
				// 获取所有武器
				$armings = EnUser::getUserObj()->getHeroObj($hid)->getArmingItem();
				Logger::debug('Hero arming is %s.', $armings);
				// 重置计数器
				$count = 0;
				// 循环查看一身装备
				foreach ($armings as $arming)
				{
					// 偷懒，先将计数器加算
					++$count;
					// 一旦身上装备不满或者一件装备不行，那么直接退出循环，啥都不用看了
					if ($arming == null || $arming->getItemQuality() != $qua)
					{
						// 遇到不符合要求的，再减一下
						--$count;
					}
				}

				// 循环完了，查看到底有多少件
				if ($count >= $num)
				{
					// 将新成就加入到数据库
					$achievePoint = MyAchievements::getInstance()->addAchieve($achieveID);
					// 加到差不多了就试试得没得到新成就
					EnAchievements::__notify(AchievementsDef::OFFER_BELLY, $achievePoint);
					// 记录成就ID
					$achieveArr[] = $achieveID;
				}
			}
		}
		// 返回所获取的成就
		return $achieveArr;
	}

	/**
	 * 竞技场名次提升
	 * 
	 * @param int $startRank					提升前的名次
	 * @param int $endRank						提升后的名次
	 */
	static public function checkArenaRankUp($startRank, $endRank)
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[AchievementsDef::ARENA_POSITION_UP]))
		{
			return $achieveArr;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[AchievementsDef::ARENA_POSITION_UP];
		// 获取当前时刻
		$curTime = Util::getTime();

		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieveID)
		{
			// 都已然到手了，那么就不再计算了
			if (MyAchievements::getInstance()->checkAlreadyGet($achieveID))
			{
				continue;
			}
			// 检查是否已经保存了这个成就
			else if (!MyAchievements::getInstance()->checkAlreadyHave($achieveID))
			{
				// 记录初始化的值
				$va = array('s_rank' => $startRank, 'e_rank' => $endRank, 'time' => $curTime);
				// 如果尚未保存这个成就的话，那么先保存下这个成就
				MyAchievements::getInstance()->addRecordAchieve($achieveID, $va);
			}
			// 如果已经保存过了，那么取出保存的值
			else 
			{
				$va = array();
				// 获取记录
				$achieveInfo = MyAchievements::getInstance()->getAchieveByID($achieveID);
				// 根据记录进行计算
				if (Util::isSameDay($achieveInfo['va_a_info']['time']))
				{
					// 这个值要大的
					$va['s_rank'] = $achieveInfo['va_a_info']['s_rank'] < $startRank ? $startRank : $achieveInfo['va_a_info']['s_rank'];
					// 这个值要小的
					$va['e_rank'] = $achieveInfo['va_a_info']['s_rank'] < $endRank ? $achieveInfo['va_a_info']['e_rank'] : $endRank;
					// 时间用现在
					$va['time'] = $curTime;
				}
				else 
				{
					$va = array('s_rank' => $startRank, 'e_rank' => $endRank, 'time' => $curTime);
				}
			}
			// 如果尚未获取这个成就的话, 检查是否可以获取这个成就
			$con = btstore_get()->ACHIEVE[$achieveID]['condition'][0];
			// 算到现在，va,里面应该都是今天的数据了
			$rank = $va['s_rank'] - $va['e_rank'];
			Logger::debug('rank is %d, s_rank is %d, e_rank is %d.', $rank, $va['s_rank'], $va['e_rank']);

			// 如果达成了这个成就的话，记录下成就ID
			if ($rank >= $con)
			{
				// 修改成就状态为：已经到手
				MyAchievements::getInstance()->changeAchieveStat($achieveID);
				// 记录成就ID
				$achieveArr[] = $achieveID;
			}
			// 如果还没有得到，那么记录这次的va
			else 
			{
				// 保存VA字段
				MyAchievements::getInstance()->updRecordVa($achieveID, $va);
			}
			// 将修改的值更新到数据库
			MyAchievements::getInstance()->save($achieveID);
		}
		// 返回所获取的成就
		return $achieveArr;
	}

	/**
	 * 查看前N个英雄的等级是否符合要求
	 * 
	 * @param int $lv							这次改变的英雄等级
	 */
	static public function checkHeroesLv($level)
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[AchievementsDef::HEROS_LEVEL]))
		{
			return $achieveArr;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[AchievementsDef::HEROS_LEVEL];
		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieveID)
		{
			// 检查是否已经获得了这个成就
			if (!MyAchievements::getInstance()->checkAlreadyGet($achieveID))
			{
				// 如果尚未获取这个成就的话, 检查是否可以获取这个成就
				$num = btstore_get()->ACHIEVE[$achieveID]['condition'][0];
				$lv = btstore_get()->ACHIEVE[$achieveID]['condition'][1];
				// 如果传入等级小于这一档策划配置等级，那么直接返回，不再做其他事情了
				if ($level < $lv)
				{
					return $achieveArr;
				}
				// 如果等级达标，那么需要进行一次数据库查询操作
				$heroLvNum = HeroUtil::getNumByLevel(RPCContext::getInstance()->getUid(), $lv);
				Logger::debug('Level is %d, hero num is %d.', $lv, $heroLvNum);
				// 如果达成了这个成就的话，记录下成就ID
				if ($heroLvNum >= $num)
				{
					// 将新成就加入到数据库
					$achievePoint = MyAchievements::getInstance()->addAchieve($achieveID);
					// 加到差不多了就试试得没得到新成就
					EnAchievements::__notify(AchievementsDef::OFFER_BELLY, $achievePoint);
					// 记录成就ID
					$achieveArr[] = $achieveID;
				}
			}
		}
		// 返回所获取的成就
		return $achieveArr;
	}

	/**
	 * 查询宠物种类
	 */
	static public function checkPetTypeNum()
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[AchievementsDef::PET_TYPE_NUM]))
		{
			return $achieveArr;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[AchievementsDef::PET_TYPE_NUM];
		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieveID)
		{
			// 检查是否已经获得了这个成就
			if (!MyAchievements::getInstance()->checkAlreadyGet($achieveID))
			{
				// 记录宠物种类
				$petType = array();
				// 获取宠物信息
				$petInfo = PetLogic::getUserPetInfo();
				// 循环查看所有宠物
				foreach ($petInfo['va_pet_info'] as $pet)
				{
					// 记录宠物种类
					$petType[$pet['tid']] = 1; 
				}

				// 如果尚未获取这个成就的话, 检查是否可以获取这个成就
				$con = btstore_get()->ACHIEVE[$achieveID]['condition'][0];
				// 如果达成了这个成就的话，记录下成就ID
				if (count($petType) >= $con)
				{
					// 将新成就加入到数据库
					$achievePoint = MyAchievements::getInstance()->addAchieve($achieveID);
					// 加到差不多了就试试得没得到新成就
					EnAchievements::__notify(AchievementsDef::OFFER_BELLY, $achievePoint);
					// 记录成就ID
					$achieveArr[] = $achieveID;
				}
			}
		}
		// 返回所获取的成就
		return $achieveArr;
	}

	/**
	 * 查询舱室等级是否达成
	 * 
	 * @param int $lv							这次改变的舱室等级
	 */
	static public function checkCabinsLv($level)
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[AchievementsDef::CABIN_LEVEL]))
		{
			return $achieveArr;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[AchievementsDef::CABIN_LEVEL];
		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieveID)
		{
			// 检查是否已经获得了这个成就
			if (!MyAchievements::getInstance()->checkAlreadyGet($achieveID))
			{
				// 记录等级
				$lv = btstore_get()->ACHIEVE[$achieveID]['condition'][0];
				// 这次修改的小于策划配置的这一档，直接返回
				if ($level < $lv)
				{
					return $achieveArr;
				}

				// 尚未获取这个成就，那么需要计算所有舱室等级
				$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
				// 循环计算所有舱室
				foreach ($cabinInfo as $cabin) 
				{
					// 如果有一个实际舱室的等级小于要求，那么直接返回
					if ($cabin['level'] < $lv)
					{
						return $achieveArr;
					}
				}

				// 如果尚未获取这个成就的话, 检查是否可以获取这个成就
				// 如果达成了这个成就的话，记录下成就ID
				// 将新成就加入到数据库
				$achievePoint = MyAchievements::getInstance()->addAchieve($achieveID);
				// 加到差不多了就试试得没得到新成就
				EnAchievements::__notify(AchievementsDef::OFFER_BELLY, $achievePoint);
				// 记录成就ID
				$achieveArr[] = $achieveID;
			}
		}
		// 返回所获取的成就
		return $achieveArr;
	}

	/**
	 * 计算总时长成就
	 */
	static public function checkTotalOnlineTime()
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[AchievementsDef::TOTAL_ONLINE_TIME]))
		{
			return $achieveArr;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[AchievementsDef::TOTAL_ONLINE_TIME];
		// 获取当前时刻
		$curTime = Util::getTime();
		// 记录下一档的差值
		$needTime = 0;
		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieveID)
		{
			// 检查是否已经获得了这个成就
			if (!MyAchievements::getInstance()->checkAlreadyGet($achieveID))
			{
				// 获取总在线时间
				$accTime = EnUser::getUserObj()->getOnlineAccumTime();

				// 如果尚未获取这个成就的话, 检查是否可以获取这个成就
				$con = btstore_get()->ACHIEVE[$achieveID]['condition'][0];
				// 如果达成了这个成就的话，记录下成就ID
				if ($accTime >= $con)
				{
					// 将新成就加入到数据库
					$achievePoint = MyAchievements::getInstance()->addAchieve($achieveID);
					// 加到差不多了就试试得没得到新成就
					EnAchievements::__notify(AchievementsDef::OFFER_BELLY, $achievePoint);
					// 记录成就ID
					$achieveArr[] = $achieveID;
				}
				// 不能达成成就，截止循环
				else 
				{
					$needTime = $con - $accTime;
					break;
				}
			}
		}
		// 返回所获取的成就
		return empty($achieveArr) ? $needTime : $achieveArr;
	}

	/**
	 * 计算持续在线成就
	 */
	static public function checkKeepOnlineTime()
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[AchievementsDef::KEEP_ONLINE_TIME]))
		{
			return $achieveArr;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[AchievementsDef::KEEP_ONLINE_TIME];
		// 获取当前时刻
		$curTime = Util::getTime();
		// 记录下一档的差值
		$needTime = 0;
		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieveID)
		{
			// 检查是否已经获得了这个成就
			if (!MyAchievements::getInstance()->checkAlreadyGet($achieveID))
			{
				// 登陆到现在的时间
				$accTime = $curTime - RPCContext::getInstance()->getSession('global.login_time');

				// 如果尚未获取这个成就的话, 检查是否可以获取这个成就
				$con = btstore_get()->ACHIEVE[$achieveID]['condition'][0];
				// 如果达成了这个成就的话，记录下成就ID
				if ($accTime >= $con)
				{
					// 将新成就加入到数据库
					$achievePoint = MyAchievements::getInstance()->addAchieve($achieveID);
					// 加到差不多了就试试得没得到新成就
					EnAchievements::__notify(AchievementsDef::OFFER_BELLY, $achievePoint);
					// 记录成就ID
					$achieveArr[] = $achieveID;
				}
				// 不能达成成就，截止循环
				else 
				{
					$needTime = $con - $accTime;
					break;
				}
			}
		}
		// 返回所获取的成就
		return empty($achieveArr) ? $needTime : $achieveArr;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */