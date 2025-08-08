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
 * Class       : PetLogic
 * Description : 宠物实现类
 * Inherit     : 
 **********************************************************************************************************************/
class PetLogic
{

	/**
	 * 获取用户的宠物信息
	 */
	public static function getUserPetInfo() 
	{
		// 调整训练时刻和经验
		self::adjustTrainTime();
		// 获取宠物信息
		return MyPet::getInstance()->getUserPetInfo();
	}

	/**
	 * 获取当前CD时刻
	 */
	public static function getCDTime()
	{
		// 获取CD截止时刻
		$endTime = MyPet::getInstance()->getCdEndTime();
		// 获取当前CD时刻
		$cd = $endTime['cd_time'] - Util::getTime();
		return $cd < 0 ? 0 : $cd;
	}

	/**
	 * 获取当前CD截止时刻
	 */
	public static function getCdEndTime()
	{
		// 获取CD截止时刻
		return MyPet::getInstance()->getCdEndTime();
	}

	/**
	 * 使用人民币清空CD时间
	 */
	public static function clearCDByGold()
	{
		// 获取CD时刻, 看看一共需要多少个金币
		$num = ceil(self::getCDTime() / btstore_get()->PET_ROOM['gold_per_cd']);
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
		MyPet::getInstance()->resetCdTime();

		// 扣钱
		$user = EnUser::getInstance();
		$user->subGold($num);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $num);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_PET_CLEARCDTIME, $num, Util::getTime());

		// 保存至数据库
		MyPet::getInstance()->save();
		// 返回给前端，用来校准数据
		return $num;
	}

	/**
	 * 添加CD时间
	 * @param int $addTime						需要增加的时刻
	 */
	public static function addCDTime($addTime)
	{
		// 获取最新的CD截止时刻和状态
		$petInfo = self::getUserPetInfo();
		// 如果CD时间为空闲
		if ($petInfo['cd_status'] == PetConf::RAPID_FREE)
		{
			// 加上时间
			$petInfo = MyPet::getInstance()->addCdTime($addTime);
			// 成功返回
			return true;
		}
		// 如果CD时间为忙
		else if ($petInfo['cd_status'] == PetConf::RAPID_BUSY)
		{
			// 如果CD时间还没有走完，那么就侯着吧
			return false;
		}
	}

	/**
	 * 开启新的宠物位置
	 */
	public static function openSlot()
	{
		/**************************************************************************************************************
 		 * 获取宠物信息
 		 **************************************************************************************************************/
		$petInfo = self::getUserPetInfo();
		// 获取当前携带栏位个数
		$petCount = $petInfo['pet_slots'];

		/**************************************************************************************************************
 		 * 获取用户信息
 		 **************************************************************************************************************/
		$user = EnUser::getUserObj();
		// 得到用户vip等级
		$vipLv = $user->getVip();
		// 当前拥有的金币数量
		$gold = $user->getGold();
		// 如果当前宠物栏个数超出了最大值
		if (empty(btstore_get()->VIP[$vipLv]['pet_slots'][$petCount + 1]))
		{
			Logger::trace('Pet slot num max.');
			return 'err';
		}
		// 根据VIP等级计算所需金币数量
		$needGold = btstore_get()->VIP[$vipLv]['pet_slots'][$petCount + 1]['gold'];

		/**************************************************************************************************************
 		 * 判断VIP等级和金币数量
 		 **************************************************************************************************************/
		// 如果金币数量不到
		if ($gold < $needGold)
		{
			Logger::trace('New pet slot need gold is %d. The user now vip is %d, gold is %d.', 
			              $needGold, $vipLv, $gold);
			return 'err';
		}

		/**************************************************************************************************************
 		 * 开启新的宠物训练栏
 		 **************************************************************************************************************/
		// 增加一个新的宠物栏
		MyPet::getInstance()->openSlot();
		// 保存到数据库
		MyPet::getInstance()->save();

		/**************************************************************************************************************
 		 * 扣除金币数
 		 **************************************************************************************************************/
		$user->subGold($needGold);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $user->getGold(), $needGold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_PET_OPENSLOT, $needGold, Util::getTime());

		return 'ok';
	}

	/**
	 * 锁定技能
	 * @param int $petID						宠物ID
	 * @param int $skillID						宠物技能ID
	 */
	public static function lockSkill($petID, $skillID)
	{
		/**************************************************************************************************************
 		 * 获取宠物信息
 		 **************************************************************************************************************/
		$petInfo = self::getUserPetInfo();
		// 如果没有这个技能
		if (empty($petInfo['va_pet_info'][$petID]['skill_info'][$skillID]))
		{
			Logger::fatal('The pet %d, did not have the %d skill now.', $petID, $skillID);
			throw new Exception('fake');
		}
		// 查看是否已经被锁定
		if ($petInfo['va_pet_info'][$petID]['skill_info'][$skillID]['lock'] == PetDef::LOCK)
		{
			Logger::debug('Already locked.');
			return 'ok';
		}
		// 查看一共有多少个宠物技能被锁定了
		$lockCount = 0;
		// 循环查看此宠物的所有技能
		foreach ($petInfo['va_pet_info'][$petID]['skill_info'] as $skill)
		{
			// 如果已经被锁定
			if ($skill['lock'] == PetDef::LOCK)
			{
				++$lockCount;
			}
		}

		/**************************************************************************************************************
 		 * 获取用户信息
 		 **************************************************************************************************************/
		$userInfo = EnUser::getUser();
		// 得到用户vip等级
		$vipLv = intval($userInfo['vip']);
		// 当前拥有的金币数量
		$gold = $userInfo['gold_num'];
		// 如果当前锁定技能个数超出了最大值
		if (empty(btstore_get()->VIP[$vipLv]['pet_skill_lock'][$lockCount + 1]))
		{
			Logger::trace('Lock num max.');
			return 'err';
		}
		// 查看多锁定一个，所需要的金币数量
		$needGold = btstore_get()->VIP[$vipLv]['pet_skill_lock'][$lockCount + 1]['gold'];

		/**************************************************************************************************************
 		 * 判断VIP等级和金币数量
 		 **************************************************************************************************************/
		// 如果金币数量不到
		if ($gold < $needGold)
		{
			Logger::trace('Lock gold is %d. The user now vip is %d, gold %d.', $needGold, $vipLv, $gold);
			return 'err';
		}

		/**************************************************************************************************************
 		 * 设置锁定状态
 		 **************************************************************************************************************/
		// 设置锁定状态
		MyPet::getInstance()->setLockState($petID, $skillID, PetDef::LOCK);
		// 保存到数据库
		MyPet::getInstance()->save();

		/**************************************************************************************************************
 		 * 扣除金币数
 		 **************************************************************************************************************/
		$user = EnUser::getInstance();
		$user->subGold($needGold);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $needGold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_PET_LOCKSKILL, $needGold, Util::getTime());

		return 'ok';
	}

	/**
	 * 解锁技能
	 * @param int $petID						宠物ID
	 * @param int $skillID						宠物技能ID
	 */
	public static function unLockSkill($petID, $skillID)
	{
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 如果没有这个技能
		if (empty($petInfo['va_pet_info'][$petID]['skill_info'][$skillID]))
		{
			Logger::fatal('The pet %d, did not have the %d skill now.', $petID, $skillID);
			throw new Exception('fake');
		}
		// 设置锁定状态
		MyPet::getInstance()->setLockState($petID, $skillID, PetDef::UNLOCK);
		// 保存到数据库
		MyPet::getInstance()->save();
		return 'ok';
	}

	/**
	 * 装备宠物
	 * @param int $petID						宠物ID
	 */
	public static function equip($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 设置当前宠物
		MyPet::getInstance()->changeCurPet($petID);
		// 保存到数据库
		MyPet::getInstance()->save();
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 告诉前端，显示个宠物出来
		RPCContext::getInstance()->addPet(intval($petInfo['cur_pet']), 
		                                  intval($petInfo['va_pet_info'][$petInfo['cur_pet']]['tid']),
		                                  btstore_get()->PET[$petInfo['va_pet_info'][$petInfo['cur_pet']]['tid']]['name']);
		return 'ok';
	}

	/**
	 * 卸下宠物
	 */
	public static function unequip()
	{
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 告诉前端，要卸载宠物，别在界面上显示了
		if (intval($petInfo['cur_pet']) != 0)
		{
			RPCContext::getInstance()->delPet(intval($petInfo['cur_pet']));
		}
		// 卸下当前宠物
		MyPet::getInstance()->changeCurPet(0);
		// 保存到数据库
		MyPet::getInstance()->save();
	}

	/**
	 * 出售宠物
	 * @param int $petID						宠物ID
	 */
	public static function sell($petID)
	{
		// 如果宠物ID不存在
		if (!MyPet::getInstance()->checkPetExist($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 查看此宠物是否正在装备中
		if ($petInfo['cur_pet'] == $petID)
		{
			// 卸下当前宠物
			MyPet::getInstance()->changeCurPet(0);
		}

		// 查看宠物能卖几个钱
		$backBelly = btstore_get()->PET[$petInfo['va_pet_info'][$petID]['tid']]['sell_belly'];
		Logger::debug('The %d pet is kind of %d pet, worth %d.', 
		              $petID, $petInfo['va_pet_info'][$petID]['tid'], $backBelly);
		// 返还用户游戏币
		$user = EnUser::getInstance();
		$user->addBelly($backBelly);
		$user->update();

		// 删除宠物信息
		MyPet::getInstance()->delPet($petID);
		// 保存到数据库
		MyPet::getInstance()->save();
		return 'ok';
	}

	/**
	 * 重生
	 * @param int $petID						宠物ID
	 */
	public static function reborn($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}

		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 获取宠物模板ID
		$tid = $petInfo['va_pet_info'][$petID]['tid'];
		// 判断是否达到重生等级 
		if ($petInfo['va_pet_info'][$petID]['lv'] < btstore_get()->PET_ROOM['reborn_lv'])
		{
			Logger::trace('Can not reborn. Need pet level is %d, %d pet is %d now.',
			              btstore_get()->PET_ROOM['reborn_lv'], $petID, $petInfo['va_pet_info'][$petID]['lv']);
			return 'err';
		}
		// 查看该宠物是否正在训练
		$trainTime = $petInfo['va_pet_info'][$petID]['train_start_time'];
		// 重置宠物信息
		MyPet::getInstance()->clearPetInfo($petID, $tid);
		// 返回值初始化
		$startTime = 0;
		// 如果该宠物正在训练呢，那么需要让他继续训练
		if ($trainTime != 0)
		{
			// 记录开始时刻
			$startTime = Util::getTime();
			// 从这个时刻进行训练
			MyPet::getInstance()->setTrainTime($petID, $startTime);
		}
		// 保存到数据库
		MyPet::getInstance()->save();
		// 返回训练开始时刻
		return $startTime;
	}

	/**
	 * 开始宠物训练
	 * @param int $petID						宠物ID
	 * @throws Exception
	 */
	public static function train($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 查看一共有多少个宠物正在训练
		$trainCount = 0;
		// 查看所有宠物的训练时刻
		foreach ($petInfo['va_pet_info'] as $pet)
		{
			// 如果正在训练中
			if ($pet['train_start_time'] != 0)
			{
				++$trainCount;
			}
		}
// 删除 2012-11-23
//		// 判断是否已经满了
//		if ($trainCount >= $petInfo['train_slots'])
//		{
//			Logger::trace('Train slots if full now, num is %d.', $petInfo['train_slots']);
//			return 'err';
//		}

		// 调整训练时刻和经验
		self::adjustTrainTime();
		// 开始训练
		$startTime = Util::getTime();
		// 记录开始时刻
		MyPet::getInstance()->setTrainTime($petID, $startTime);
		// 保存到数据库
		MyPet::getInstance()->save();
		// 通知任务系统，宠物训练了
		TaskNotify::operate(TaskOperateType::PET_TRAIN);
		// 通知前端，开始时间是什么
		return $startTime;
	}

	/**
	 * 停止宠物训练
	 * @param int $petID						宠物ID
	 * @throws Exception
	 */
	public static function stopTrain($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 调整训练时刻和经验
		self::adjustTrainTime();
		// 结束训练
		MyPet::getInstance()->setTrainTime($petID, 0);
		// 保存到数据库
		MyPet::getInstance()->save();
		return 'ok';
	}

	/**
	 * 使用领悟点进行领悟
	 * 
	 * @param int $petID						宠物ID
	 * @throws Exception
	 */
	public static function understand($petID)
	{
		/**************************************************************************************************************
 		 * 检查领悟点，获取基本技能信息
 		 **************************************************************************************************************/
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物技能个数
		$count = 0;
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 检查领悟点是否够用
		if ($petInfo['va_pet_info'][$petID]['know_points'] <= 0)
		{
			Logger::trace('Know points not enough, the %d pet have %d now.',
			              $petID, $petInfo['va_pet_info'][$petID]['know_points']);
			return 'err';
		}
		// 检查是否等级已经满了
		if (MyPet::getInstance()->isSkillLevelFull($petID))
		{
			Logger::trace('Pet sill level is full, all of it.');
			return 'full';
		}
		// 获取技能列表
		$skillList = $petInfo['va_pet_info'][$petID]['skill_info'];
		// 技能个数
		$skillNum = count($skillList);
		// 获取宠物模板ID
		$tid = $petInfo['va_pet_info'][$petID]['tid'];

		/**************************************************************************************************************
 		 * 根据技能，获取权重信息
 		 **************************************************************************************************************/
		// 权重数组
		$wightArr = array();
		// 检查技能栏是否已经开满了
		// 1. 获取新宠物技能开启权重
		if ($skillNum < btstore_get()->PET[$tid]['skill_limit'])
		{
			$wightArr[] = array('key' => 'open', 'weight' => btstore_get()->PET[$tid]['skill_num_plus_weight'][$skillNum]);
		}
		// 2. 循环所有技能，查看技能的升级权重
		// 3. 查看新技能开启的权重
		foreach ($skillList as $skill)
		{
			// 技能尚未开启
			if ($skill['lv'] == 0)
			{
				$wightArr[] = array('key' => 'skill', 'weight' => btstore_get()->PET[$tid]['skill_lv_up_weight'][0]);
				// 获取旧的随机数无用技能ID
				$oldID = $skill['id'];
			}
			// 技能升级
			else
			{
				// 计算旧有技能个数
				++$count;
				// 计算权重
				$wightArr[] = array('key' => 'lv', 'id' =>  $skill['id'], 'weight' => btstore_get()->PET[$tid]['skill_lv_up_weight'][$skill['lv']]);
			}
		}

		/**************************************************************************************************************
 		 * 根据权重信息进行随机抽样
 		 **************************************************************************************************************/
		$randKey = Util::noBackSample($wightArr, 1);
		Logger::debug('The rand ret is %s.', $wightArr[$randKey[0]]);
		// 给用户的提示
		$info = 0;
		// 开启新的宠物技能栏
		if ($wightArr[$randKey[0]]['key'] == 'open')
		{
			// 开启一个新技能栏，随机一个值，用来当做暂时的技能ID, 等级为0
			MyPet::getInstance()->openNewSkillSlot($petID);
			// 告诉前端打开新技能栏
			$info = 0;
		}
		// 开启新技能
		else if ($wightArr[$randKey[0]]['key'] == 'skill')
		{
			do 
			{
				// 随机获取一种技能
				$key = array_rand(btstore_get()->PET[$tid]['can_acquire_skills']->toArray());
				// 获取技能ID
				$skillID = intval(btstore_get()->PET[$tid]['can_acquire_skills'][$key]);
			}
			// 如果这个技能尚未学会
			while (!empty($skillList[$skillID]));
			// 开启这个新技能, 同时干掉占位的旧技能
			MyPet::getInstance()->levelUpSkill($petID, $skillID, $oldID);
			// 告诉前端技能ID
			$info = $skillID;
			// 加算新技能个数
			++$count;
		}
		// 技能升级
		else if ($wightArr[$randKey[0]]['key'] == 'lv')
		{
			// 提升该宠物该技能的等级
			if (MyPet::getInstance()->levelUpSkill($petID, $wightArr[$randKey[0]]['id']))
			{
				// 告诉前端技能升级
				$info = $wightArr[$randKey[0]]['id'];
			}
			else 
			{
				// 升级失败，告诉前端
				$info = -1;
			}
		}

		/**************************************************************************************************************
 		 * 减去领悟点
 		 **************************************************************************************************************/
		MyPet::getInstance()->subKnowPoint($petID);
		// 保存到数据库
		MyPet::getInstance()->save();
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();

		// 通知成就系统
		EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::PET_SKILL_TYPE_NUM, $count);

		// 获取最新的宠物信息，返回前端
		return array('petInfo' => $petInfo['va_pet_info'][$petID], 'stat' => $info);
	}

	/**
	 * 突飞啊
	 * @param int $petID						宠物ID
	 * @throws Exception
	 */
	public static function rapid($petID)
	{
		/**************************************************************************************************************
 		 * 获取宠物室和宠物的信息
 		 **************************************************************************************************************/
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物信息 (包含最新的CD时间)
		$petInfo = self::getUserPetInfo();
		// 查看是否正处于训练状态
		if ($petInfo['va_pet_info'][$petID]['train_start_time'] == 0)
		{
			// 如果尚未处于训练状态，则不准进行突飞
			Logger::fatal('Can not rapid, the %d pet is not training now.', $petID);
			throw new Exception('fake');
		}

		// 宠物室等级检查, 获取最新舱室信息
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
		// 获取宠物室等级
		$cabinLv = $cabinInfo[SailboatDef::PET_ID]['level'];
		if (empty($cabinLv) || $cabinLv <= 0)
		{
			Logger::fatal('Can not get pet room level!');
			throw new Exception('fake');
		}
		// 如果宠物等级已经达到宠物室等级了
		if ($petInfo['va_pet_info'][$petID]['lv'] >= EnUser::getUserObj()->getLevel())
		{
			Logger::trace('Can not rapid, pet level is %d, User level is %d.', $petInfo['va_pet_info'][$petID]['lv'], EnUser::getUserObj()->getLevel());
			return 'err';
		}

		/**************************************************************************************************************
 		 * 升级费用检查
 		 **************************************************************************************************************/
		// 获取用户信息
		$userInfo = EnUser::getUser();
		// 游戏币/阅历/金币检查
		if ((!isset(btstore_get()->PET_ROOM['rapid_res_base'][0]) || $userInfo['belly_num'] < btstore_get()->PET_ROOM['rapid_res_base'][0] * $cabinLv) ||
		    (!isset(btstore_get()->PET_ROOM['rapid_res_base'][1]) || $userInfo['experience_num'] < btstore_get()->PET_ROOM['rapid_res_base'][1] * $cabinLv) ||
		    (!isset(btstore_get()->PET_ROOM['rapid_res_base'][2]) || $userInfo['gold_num'] < btstore_get()->PET_ROOM['rapid_res_base'][2] * $cabinLv))
		{
			Logger::trace('Res not enough.');
			return 'err';
		}
		// 检查,增加CD时间
		if (!self::addCdTime(btstore_get()->PET_ROOM['rapid_time_up']))
		{
			Logger::trace('Not cool down yet.');
			return 'err';
		}

		/**************************************************************************************************************
 		 * 宠物突飞
 		 **************************************************************************************************************/
		// 计算需要增加的经验值
		$exp = $cabinLv * btstore_get()->PET_ROOM['rapid_exp_base'];
		// 增加经验
		MyPet::getInstance()->addPetExp($petID, $exp);
		Logger::debug('Rapid exp is %d.', $exp);

		/**************************************************************************************************************
 		 * 扣除成本
 		 **************************************************************************************************************/
		// 扣游戏币/阅历/金币
		$user = EnUser::getInstance();
		$user->subBelly(btstore_get()->PET_ROOM['rapid_res_base'][0] * $cabinLv);
		$user->subExperience(btstore_get()->PET_ROOM['rapid_res_base'][1] * $cabinLv);
		$user->subGold(btstore_get()->PET_ROOM['rapid_res_base'][2] * $cabinLv);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], btstore_get()->PET_ROOM['rapid_res_base'][2] * $cabinLv);
		$user->update();
		// 发送金币通知
		if (btstore_get()->PET_ROOM['rapid_res_base'][2] > 0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_PET_RAPID, btstore_get()->PET_ROOM['rapid_res_base'][2] * $cabinLv, Util::getTime());
		}

		// 调整等级
		self::adjustTrainTime();
		// 保存到数据库
		MyPet::getInstance()->save();
		return 'ok';
	}

	/**
	 * 有钱人专用的金币突飞啊
	 * @param int $petID						宠物ID
	 * @throws Exception
	 */
	public static function rapidByGold($petID)
	{
		/**************************************************************************************************************
 		 * 获取宠物室和宠物的信息
 		 **************************************************************************************************************/
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 查看是否正处于训练状态
		if ($petInfo['va_pet_info'][$petID]['train_start_time'] == 0)
		{
			// 如果尚未处于训练状态，则不准进行突飞
			Logger::fatal('Can not rapid, the %d pet is not training now.', $petID);
			throw new Exception('fake');
		}

		// 宠物室等级检查 获取最新舱室信息
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
		// 获取宠物室等级
		$cabinLv = $cabinInfo[SailboatDef::PET_ID]['level'];
		if (empty($cabinLv) || $cabinLv <= 0)
		{
			Logger::fatal('Can not get pet room level!');
			throw new Exception('fake');
		}
		// 如果宠物等级已经达到宠物室等级了
		if ($petInfo['va_pet_info'][$petID]['lv'] >= EnUser::getUserObj()->getLevel())
		{
			Logger::trace('Can not rapid, pet level is %d, User level is %d.', $petInfo['va_pet_info'][$petID]['lv'], EnUser::getUserObj()->getLevel());
			return 'err';
		}

		/**************************************************************************************************************
 		 * 升级费用检查
 		 **************************************************************************************************************/
		// 查看现在已经金币突飞的次数
		$rapidTimes = MyPet::getInstance()->getTodayRapidTimes();
		$gold = ($rapidTimes * btstore_get()->PET_ROOM['rapid_gold_up'] + 
		         btstore_get()->PET_ROOM['rapid_gold_base']) * PetConf::RAPID_GOLD_RATIO;
		// 获取用户信息
		$userInfo = EnUser::getUser();
		// 金币检查
		if ($userInfo['gold_num'] < $gold)
		{
			Logger::trace('Gold not enough, rapid needs %d, user have now %d, today rapid times is %d.',
			              $gold, $userInfo['gold_num'], $rapidTimes);
			return 'err';	
		}
		// VIP检查
		if (empty(btstore_get()->VIP[$userInfo['vip']]['pet_rapid_open_lv']))
		{
			Logger::trace('Vip level not enough, user now is %d.', $userInfo['vip']);
			return 'err';	
		}

		/**************************************************************************************************************
 		 * 宠物突飞
 		 **************************************************************************************************************/
		// 计算需要增加的经验值
		$exp = $cabinLv * btstore_get()->PET_ROOM['rapid_exp_base'] * PetConf::RAPID_GOLD_RATIO;
		Logger::debug('The pet room level is %d, add exp is %d.', $cabinLv, $exp);
		// 增加经验
		MyPet::getInstance()->addPetExp($petID, $exp);
		// 增加今日突飞次数
		MyPet::getInstance()->addRapidTimes();

		/**************************************************************************************************************
 		 * 扣除成本
 		 **************************************************************************************************************/
		// 扣金币
		$user = EnUser::getInstance();
		$user->subGold($gold);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $gold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_PET_GOLDRAPID, $gold, Util::getTime());

		// 调整等级
		self::adjustTrainTime();
		// 保存到数据库
		MyPet::getInstance()->save();
		return 'ok';
	}

	/**
	 * 根据宠物ID计算宠物所有技能属性加成
	 * @param int $petID						宠物ID
	 * @param int $attrID						属性ID
	 */
	public static function getAttr($petID, $attrID)
	{
		// 获取宠物所有技能
		$buf = self::getAllAttr($petID);
		// 返回某项属性加成
		return isset($buf[$attrID]) ? $buf[$attrID] : 0;
	}

	/**
	 * 根据宠物ID计算宠物所有技能属性加成
	 * @param int $petID						宠物ID
	 */
	public static function getAllAttr($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 返回计算结果
		return MyPet::calculateAllAttr($petInfo['va_pet_info'][$petID]['tid'], 
		                               $petInfo['va_pet_info'][$petID]['skill_info']);
	}

	/**
	 * 重置
	 * @param int $petID						重置的宠物ID
	 * @param string $resetType					重置类型(钱还是蛋)
	 */
	public static function reset($petID, $resetType)
	{
		/**************************************************************************************************************
 		 * 获取基本信息
 		 **************************************************************************************************************/
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}

		// 查看背包……不会被人叫做偷窥吧……
		$bag = BagManager::getInstance()->getBag();
		// 获取用户信息
		$userInfo = EnUser::getUser();
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 获取宠物模板ID
		$tid = $petInfo['va_pet_info'][$petID]['tid'];
		// 获取宠物等级
		$level = $petInfo['va_pet_info'][$petID]['lv'];
		// 获取该宠物现在应该有的领悟点个数
		$tmp = floor($level / btstore_get()->PET_ROOM['kown_point_per_lv']) * btstore_get()->PET[$tid]['understand_grow'];
		$kownPoint =  $tmp + btstore_get()->PET[$tid]['understand_init'];
		Logger::debug('Understand point num is $d.', $kownPoint);
		// 所需金币数 (和领悟点个数不太一样)
		$gold = ($tmp + PetConf::RESET_GOLD_INIT) * PetConf::RESET_GOLD_PER;

		/**************************************************************************************************************
 		 * 检查突飞需求
 		 **************************************************************************************************************/
		// 分辨是宠物蛋重置还是金币重置, 然后查蛋查钱……
		if ($resetType == 'egg')
		{
			// 如果使用宠物蛋进行重置, 就到背包里面扣一个蛋
			if (!$bag->deleteItembyTemplateID(btstore_get()->PET[$tid]['reset_t_id'], 1))
			{
				Logger::trace('Not enough egg…… oh! egg like this O.');
				return 'err';
			}
		}
		else if ($resetType == 'gold')
		{
			// 如果是金币重置，那么需要检查金币数量
			if ($userInfo['gold_num'] < $gold)
			{
				Logger::trace('Not enough gold. Need %d, now have %d.', $gold, $userInfo['gold_num']);
				return 'err';
			}
		}

		/**************************************************************************************************************
 		 * 重置所有技能
 		 **************************************************************************************************************/
		$retPoints = MyPet::getInstance()->resetSkill($kownPoint, $petID, $tid);

		/**************************************************************************************************************
 		 * 扣除突飞成本
 		 **************************************************************************************************************/
		$bagInfo = array();
		if ($resetType == 'egg')
		{
			// 扣蛋
			$bagInfo = $bag->update();
		}
		else if ($resetType == 'gold')
		{
			// 扣钱
			$user = EnUser::getInstance();
			$user->subGold($gold);
			$user->update();
			// 发送金币通知
			Statistics::gold(StatisticsDef::ST_FUNCKEY_PET_RESET, $gold, Util::getTime());
		}

		// 保存到数据库
		$petInfo = MyPet::getInstance()->save();
		// 返回该宠物所拥有的领悟点个数
		return array('points' => $retPoints, 'bag' => $bagInfo, 'pet' => $petInfo['va_pet_info'][$petID]);
	}

	/**
	 * 调整训练的经验和等级
	 * @throws Exception
	 */
	private static function adjustTrainTime()
	{
		// 宠物室等级检查  获取最新舱室信息
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
		// 获取宠物室等级
		if (empty($cabinInfo[SailboatDef::PET_ID]))
		{
			// 初始化等级
			$cabinInfo[SailboatDef::PET_ID]['level'] = 1;
		}
		// 你说多不容易，可算获取了
		$cabinLv = $cabinInfo[SailboatDef::PET_ID]['level'];

		// 获取宠物信息
		$petInfo = MyPet::getInstance()->getUserPetInfo();
		// 如果没有获取到宠物信息，那么就查看任务，是否创建一条新的
		if ($petInfo === false)
		{
			// 如果完成任务还尚未开启宠物室
			if (EnSwitch::isOpen(SwitchDef::PET))
			{
				Logger::debug('Init pet info.');
				// 初始化人物宠物信息
				$petInfo = MyPet::getInstance()->addNewPetInfo();
			}
			// 如果尚未完成任务，那么就不应该获取这个数据
			else 
			{
				Logger::fatal('Can not get pet cabin level!');
				throw new Exception('fake');
			}
		}
		// 获取这个玩家的所有宠物
		foreach ($petInfo['va_pet_info'] as $pet)
		{
			// 获取下最新的时刻
			$curTime = Util::getTime();
			// 如果正在训练中
			if ($pet['train_start_time'] != 0)
			{
				// 如果宠物等级还小于人物等级
				if ($pet['lv'] < EnUser::getUserObj()->getLevel())
				{
					// 计算分钟
					$min = floor(($curTime - $pet['train_start_time']) / 60);
					// 获取剩余的秒数
					$sec = ($curTime - $pet['train_start_time']) % 60;
					Logger::debug('Now time is %d, train start time is %d, min is %d, left second is %d.', 
					              $curTime, $pet['train_start_time'], $min, $sec);
					// 计算一共获取经验值
					$exp = $min * $cabinLv * btstore_get()->PET_ROOM['exp_coefficient'];
					Logger::debug('Pet train min is %d, exp is %d.', $min, $exp);
					// 需要加上上次剩下的经验值
					$exp += $pet['exp'];
					// 调整等级和训练重置时刻
					MyPet::getInstance()->resetExpLv($pet['id'], $exp, $curTime - $sec);
				}
				// 超过了，不能长经验的话，则重置训练开始时刻
				else 
				{
					MyPet::getInstance()->setTrainTime($pet['id'], $curTime);
				}
			}
			else
			{
				// 调整等级和训练重置时刻
				MyPet::getInstance()->resetExpLv($pet['id'], 
				                                 $petInfo['va_pet_info'][$pet['id']]['exp'], 0);
			}
		}

		// 保存到数据库
		MyPet::getInstance()->save();
		return 'ok';
	}

	/**
	 * 开启仓库格子
	 */
	public static function openWarehouseSlot()
	{
		/**************************************************************************************************************
 		 * 宠物仓库开启所需金币 = 宠物仓库开启初始金币+（开启次数-1）*宠物仓库开启递增金币
 		 * 例如开启初始金币为10金币，递增金币为20金币
 		 * 则开启第1个需要10金币，第2个需要30金币，第3个50金币，以此类推
 		 **************************************************************************************************************/
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 获取所需金币
		$needGold = btstore_get()->PET_ROOM['warehouse_slot_gold_base'] + 
					btstore_get()->PET_ROOM['warehouse_slot_gold_up'] * 
					($petInfo['warehouse_slots'] - btstore_get()->PET_ROOM['init_warehouse_slot']);
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 当前拥有的金币数量
		$gold = $user->getGold();
		// 如果金币数量不到
		if ($gold < $needGold)
		{
			Logger::warning('New pet warehouse slot need gold is %d. The user now gold is %d.', $needGold, $gold);
			return 'err';
		}
		// 判断VIP可否开启
		if (btstore_get()->VIP[$user->getVip()]['max_warehouse_num'] <= $petInfo['warehouse_slots'])
		{
			Logger::warning('VIP not enough, can not open any more.');
			return 'err';
		}

		/**************************************************************************************************************
 		 * 开启新的宠物仓库铺位
 		 **************************************************************************************************************/
		// 增加一个新的仓库铺位
		MyPet::getInstance()->openWareHouseSlot();
		// 保存到数据库
		MyPet::getInstance()->save();

		/**************************************************************************************************************
 		 * 扣除金币数
 		 **************************************************************************************************************/
		$user->subGold($needGold);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $gold, $needGold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_PET_WAREHOUSE_OPENSLOT, $needGold, Util::getTime());

		return 'ok';
	}

	/**
	 * 将宠物放入仓库，靠！用完了就放到养老院了！
	 * 
	 * @param int $petID						宠物ID
	 */
	public static function putInWarehouse($petID)
	{
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 如果在训练中或者在装备中，是不允许放入仓库的
		if ($petInfo['va_pet_info'][$petID]['train_start_time'] != 0 || $petInfo['cur_pet'] == $petID)
		{
			Logger::warning('The pet is now training or is cur pet, ps: cur_pet is %d.', $petInfo['cur_pet']);
			throw new Exception('fake');
		}
		// 现获取仓库已经有的宠物数量
		$num = MyPet::getInstance()->countInWareHouse();
		// 查看仓库还住得下不住得下
		if ($petInfo['warehouse_slots'] <= $num)
		{
			Logger::warning('Not enough slot in warehouse, now have %d, live pets num is %d.', 
							$petInfo['warehouse_slots'], $num);
			throw new Exception('fake');
		}
		// 住得下就住进去吧
		MyPet::getInstance()->putInWarehouse($petID);
		// 保存到数据库
		MyPet::getInstance()->save();

		return 'ok';
	}

	/**
	 * 从仓库搬出来，别高兴，搞不好接着是要卖掉……
	 * 
	 * @param int $petID						宠物ID
	 */
	public static function getOutWarehouse($petID)
	{
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 现获取不在仓库中的宠物数量
		$num = MyPet::getInstance()->countOutWareHouse();
		// 查看携带位还够不够
		if ($petInfo['pet_slots'] <= $num)
		{
			Logger::warning('Not enough pet slot, now have %d, live pets num is %d.', 
							$petInfo['warehouse_slots'], $num);
			throw new Exception('fake');
		}
		// 携带位如果足够，那么就带在身上吧
		MyPet::getInstance()->getOutWarehouse($petID);
		// 保存到数据库
		MyPet::getInstance()->save();

		return 'ok';
	}

	/**
	 * 提交洗练结果
	 * 
	 * @param int $petID						宠物ID
	 */
	public static function commitRefresh($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 提交洗练结果
		MyPet::getInstance()->commitQualifications($petID);
		// 保存到数据库
		MyPet::getInstance()->save();

		return 'ok';
	}

	/**
	 * 回滚洗练结果
	 * 
	 * @param int $petID						宠物ID
	 */
	public static function rollbackRefresh($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 提交洗练结果
		MyPet::getInstance()->rollbackQualifications($petID);
		// 保存到数据库
		MyPet::getInstance()->save();

		return 'ok';
	}

	/**
	 * 饲养，使其成长
	 * 
	 * @param int $petID						宠物ID
	 * @throws Exception
	 */
	public static function feedingAll($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 都把手举起来，包拿出来！搜身了！
		$bag = BagManager::getInstance()->getBag();
		// 取出所有鱼类，擦，真暴力
		$allFish = $bag->getItemIdsByItemType(ItemDef::ITEM_FISH);
		// 通过物品信息获取物品模板信息 —— 获取改鱼到底是啥鱼，恩，吃鲨鱼和吃王八的营养价值是不一样的
		$allFish = ItemManager::getInstance()->getTemplateInfoByItemIds($allFish);
		// 吃吧，撑死丫的！
		foreach ($allFish as $fishID => $num)
		{
			// 如果是强化鱼，则不进行喂养 
			if (btstore_get()->FISH[$fishID]['is_qualifications'] == 1)
			{
				continue;
			}
			// 先从背包里面拿出来再喂养，这也是合乎逻辑的做法嘛
			if ($bag->deleteItembyTemplateID($fishID, $num) != TRUE)
			{
				continue;
			}
			// 然后你就张嘴吧
			MyPet::getInstance()->addGrowUpExp($petID, btstore_get()->FISH[$fishID]['feed_base'] * $num);
		}
		// 顺便返回背包的最新数据
		$ret = $bag->update();
		// 加完经验然后更新到数据库
		$petInfo = MyPet::getInstance()->save();

		// 成功返回背包最新的样子
		return array('bagInfo' => $ret, 'exp' => $petInfo['va_pet_info'][$petID]['grow_up_exp']);
	}

	/**
	 * 饲养，使其成长
	 *
	 * @param int $petID						宠物ID
	 * @param int $itemTID						物品模板ID
	 * @throws Exception
	 */
	public static function feedingOnce($petID, $itemTID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 检查是否有这个小鱼存在, 扣除小鱼
		$bag = BagManager::getInstance()->getBag();
		// 扣除失败就不给好处 —— 吃过亏的人敬上
		if ($bag->deleteItembyTemplateID($itemTID, 1) != TRUE)
		{
			return 'err';
		}
		// 顺便返回背包的最新数据
		$ret = $bag->update();

		// 喂养，虔诚的
		MyPet::getInstance()->addGrowUpExp($petID, btstore_get()->FISH[$itemTID]['feed_base']);
		// 加完经验然后更新到数据库
		MyPet::getInstance()->save();

		// 成功返回背包最新的样子
		return $ret;
	}

	/**
	 * 洗练资质
	 * 
	 * @param int $petID						宠物ID
	 * @param int $itemTID						物品模板ID
	 * @throws Exception
	 */
	public static function refreshQualifications($petID, $itemTID)
	{
		/**************************************************************************************************************
 		 * 验证强化鱼的数量
 		 **************************************************************************************************************/
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 获取宠物模板ID
		$tid = $petInfo['va_pet_info'][$petID]['tid'];
		// 获取现有资质总和
		$quaSum = MyPet::getInstance()->sumAllQualifications($petID);
		// 根据资质总和获取所需强化鱼的数量
		// 初始化所需强化鱼数量
		$needFishes = 0;
		// 查看该宠物现阶段洗练所需的强化鱼的数量
		foreach (btstore_get()->PET[$tid]['need_fishes'] as $fishNum => $quaNum)
		{
			// "以宠物资质总和为标准，表示当资质总和小于等于4000的时候，需要消耗1条强化鱼来进行洗炼。小于等于8000的时候需要消耗2条，小于等于999999时，消耗3条"
			if ($quaSum <= $quaNum)
			{
				$needFishes = $fishNum;
				break;
			}
		}
		// 以防万一，验证一下
		if ($needFishes == 0)
		{
			Logger::warning('Need fish num is 0, kao! liuyang! whats up!');
			throw new Exception('fake');
		}
		// 检查是否有这个小鱼存在, 扣除小鱼
		$bag = BagManager::getInstance()->getBag();
		// 把鱼都从背包里拿出来
		if ($bag->deleteItembyTemplateID($itemTID, $needFishes) != TRUE)
		{
			return 'err';
		}
		// 直接扣除
		$bagInfo = $bag->update();

		/**************************************************************************************************************
 		 * 获取强化项目
 		 **************************************************************************************************************/
		// 查看是随机进行洗练，还是定向洗练，屌丝们，你们以为是随机吗？其实都是郑琛定的啊！
		$type = array();
		// 定向洗练，需要查看具体是改变哪几项
		if (btstore_get()->FISH[$itemTID]['rand_qualification_num'] == 0)
		{
			// 如果需要随机哪一项就取哪一项
			foreach (PetDef::$ATTR_INDEX as $attr)
			{
				// 如果已经满了，就不再随机
				if (!MyPet::getInstance()->isTopAlready($attr, $petID) &&
					btstore_get()->FISH[$itemTID]['qualification_'.$attr] == 1)
				{
					$type[] = $attr;
				}
			}
		}
		// 随机洗练，需要进行随机选择了
		else 
		{
			// 记录所有项目
			$allAttr = PetDef::$ATTR_INDEX;
			// 循环查看都哪些满了
			foreach ($allAttr as $key => $attr)
			{
				// 如果已经满了，就不再随机
				if (MyPet::getInstance()->isTopAlready($attr, $petID))
				{
					unset($allAttr[$key]);
				}
			}
			// 计算还剩余多少项目
			$allNotTopAttrCount = count($allAttr);
			// 查看循环截止条件，到底是董宇鑫配置的项目数少，还是用户残存的少。谁小用谁
			$end = $allNotTopAttrCount < btstore_get()->FISH[$itemTID]['rand_qualification_num'] ? 
				   $allNotTopAttrCount : btstore_get()->FISH[$itemTID]['rand_qualification_num'];
			// 根据随机项目的个数进行随机
			for ($i = 0; $i < $end; )
			{
				// 随机获取一个项目
				$index = rand(0, 3);
				// 如果这个项目还没被随机出来
				if (isset($allAttr[$index]) && !in_array($allAttr[$index], $type))
				{
					// 表示这次随机很成功，记录并进行下一次随机
					$type[] = $allAttr[$index];
					++$i;
				}
			}
		}
		Logger::debug("Need refresh item is %s.", $type);

		/**************************************************************************************************************
 		 * 加上强化数值
 		 **************************************************************************************************************/
		// 下限：（洗炼宠物资质增加值 - 洗炼宠物资质修正值）
		$min = btstore_get()->FISH[$itemTID]['qualification_up'] - btstore_get()->FISH[$itemTID]['qualification_fix'];
		// 上限：（洗炼宠物资质增加值 + 洗炼宠物资质修正值）
		$max = btstore_get()->FISH[$itemTID]['qualification_up'] + btstore_get()->FISH[$itemTID]['qualification_fix'];
		// 根据上面选出来的项目进行随机
		foreach ($type as $attr)
		{
			// 先随机出具体需要强化多少数值
			$add = rand($min, $max);
			// 检查是否超过上限
			$top = MyPet::__getPetQualificationTop($attr, 
												   $petInfo['va_pet_info'][$petID]['grow_up_lv'], 
												   $tid);
			// 判断是否超过，并记录到底加算了多少
			Logger::debug("Refresh add is %d, top is %d.", $add, $top);
			// 加上数值
			MyPet::getInstance()->addQualifications($petID, $attr, $add, $top);
		}
		// 更新到数据库
		$petInfo = MyPet::getInstance()->save();
		// 返回
		return array('bagInfo' => $bagInfo, 'qualifications' => $petInfo['va_pet_info'][$petID]['qualifications']);
	}


	/**
	 * 进化
	 * 
	 * @param int $petID						宠物ID
	 * @throws Exception
	 */
	public static function evolution($petID)
	{
		/**************************************************************************************************************
 		 * 检查是否可以进化
 		 **************************************************************************************************************/
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 获取宠物模板ID
		$tid = $petInfo['va_pet_info'][$petID]['tid'];
		// 只有有进化表ID的宠物可以进化, 进化到顶级的宠物也不可以进化
		if (empty(btstore_get()->PET[$tid]['evolution_id']) || 
			empty(btstore_get()->PET_EVOLUTION[btstore_get()->PET[$tid]['evolution_id']]['after_evolution_id']))
		{
			Logger::warning('The pet %d evolution is max.', $petID);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 进化
 		 **************************************************************************************************************/
		if (!MyPet::getInstance()->evolution($petID))
		{
			Logger::warning('The pet grow up exp or level is not enough, now is %s.', $petInfo['va_pet_info'][$petID]);
			throw new Exception('fake');
		}
		// 更新到数据库
		MyPet::getInstance()->save();

		return 'ok';
	}


	/**
	 * 宠物资质传承
	 * 
	 * @param int $curPet						想要废弃的宠物
	 * @param int $objPet						传承对象
	 * @param int $type							金币传承还是道具传承
	 */
	public static function transfer($curPet, $objPet, $type)
	{
		/**************************************************************************************************************
 		 * 检查是否可以传承
 		 **************************************************************************************************************/
		// 如果两个宠物是同一个，那么就奇怪了
		if ($curPet == $objPet)
		{
			Logger::fatal('Same pet para %d.', $curPet);
			throw new Exception('fake');
		}
		// 如果宠物ID不存在 或者在仓库里面, 那么是绝对不可以传承的
		if (!MyPet::getInstance()->checkPetExist($curPet) || MyPet::getInstance()->inWareHouse($curPet) || 
			!MyPet::getInstance()->checkPetExist($objPet) || MyPet::getInstance()->inWareHouse($objPet))
		{
			Logger::fatal('Do not have the pet %d, %d. Or in warehouse.', $curPet, $objPet);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 检查传承和被传承次数
		if (!MyPet::getInstance()->checkTransferTimes($curPet, $objPet))
		{
			Logger::warning('Transfer time not enough.');
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 检查物品或者金币是否足够
 		 **************************************************************************************************************/
		if ($type == PetDef::TRANSFER_GOLD)
		{
			// 获取所需金币
			$needGold = MyPet::getInstance()->getTransferGold($curPet);
			// 获取用户信息
			$user = EnUser::getUserObj();
			// 当前拥有的金币数量
			if ($user->getGold() < $needGold)
			{
				Logger::warning('Not enough gold, user has %d, need is %d.', $user->getGold(), $needGold);
				return 'err';
			}
		}
		else if ($type == PetDef::TRANSFER_ITEM)
		{
			// 获取传承所需道具
			$needItems = MyPet::getInstance()->getTransferItems($curPet);
			// 获取用户背包信息 
			$bag = BagManager::getInstance()->getBag();
			// 直接扣除，看是否足够
			if (!$bag->deleteItembyTemplateID($needItems['id'], $needItems['num']))
			{
				Logger::warning('Not enough item for transfer.');
				return 'err';
			}
		}
		else
		{
			Logger::warning('Err para, type is %d.', $type);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 扣除传承所需
 		 **************************************************************************************************************/
		$bagInfo = array();
		// 判断是金币传承还是道具传承
		if ($type == PetDef::TRANSFER_GOLD)
		{
			// 扣除金币
			$user->subGold($needGold);
			$user->update();
			// 发送金币通知
			Statistics::gold(StatisticsDef::ST_FUNCKEY_PET_TRANSFER_GOLD, $needGold, Util::getTime());
		}
		else if ($type == PetDef::TRANSFER_ITEM)
		{
			// 扣除道具
			$bagInfo = $bag->update();
		}
		// 加算传承次数
		MyPet::getInstance()->addTransferTimes($curPet, $objPet);

		/**************************************************************************************************************
 		 * 传承并计算等级
 		 **************************************************************************************************************/
		// 传承经验
		MyPet::getInstance()->transferExp($curPet, $objPet);
		// 调整等级
		self::adjustTrainTime();
		// 计算进化等级
		while (MyPet::getInstance()->evolution($objPet));
		// 传承资质
		MyPet::getInstance()->transferQualifications($curPet, $objPet);

		// 更新到数据库
		$petInfo = MyPet::getInstance()->save();
		// 返回最新信息
		return array('ret' => 'ok', 'bag' => $bagInfo, 'pet' => $petInfo['va_pet_info'][$objPet]);
	}

	public static function advanceTransfer($curPet, $objPet)
	{
		/**************************************************************************************************************
 		 * 检查是否可以传承
 		 **************************************************************************************************************/
		// 如果两个宠物是同一个，那么就奇怪了
		if ($curPet == $objPet)
		{
			Logger::fatal('Same pet para %d.', $curPet);
			throw new Exception('fake');
		}
		// 如果宠物ID不存在 或者在仓库里面, 那么是绝对不可以传承的
		if (!MyPet::getInstance()->checkPetExist($curPet) || MyPet::getInstance()->inWareHouse($curPet) || 
			!MyPet::getInstance()->checkPetExist($objPet) || MyPet::getInstance()->inWareHouse($objPet))
		{
			Logger::fatal('Do not have the pet %d, %d. Or in warehouse.', $curPet, $objPet);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 检查传承和被传承次数
		if (!MyPet::getInstance()->checkTransferTimes($curPet, $objPet))
		{
			Logger::warning('Transfer time not enough.');
			throw new Exception('fake');
		}

		// 获取用户背包信息 
		$bag = BagManager::getInstance()->getBag();
		// 直接扣除，看是否足够
		if (!$bag->deleteItembyTemplateID(120038, 1))
		{
			Logger::warning('Not enough item for transfer.');
			return 'err';
		}
		/**************************************************************************************************************
 		 * 扣除传承所需
 		 **************************************************************************************************************/
		// 扣除道具
			$bagInfo = $bag->update();

		// 加算传承次数
		MyPet::getInstance()->addTransferTimes($curPet, $objPet);

		/**************************************************************************************************************
 		 * 传承并计算等级
 		 **************************************************************************************************************/
		// 传承经验
		MyPet::getInstance()->transferExp($curPet, $objPet);
		// 调整等级
		self::adjustTrainTime();
		
		// 传承资质
		MyPet::getInstance()->transferQualifications($curPet, $objPet);
		
		MyPet::getInstance()->transferTalentLv($curPet, $objPet);

		// 更新到数据库
		$petInfo = MyPet::getInstance()->save();
		// 返回最新信息
		return array('ret' => 'ok', 'bag' => $bagInfo, 'petsource' => $petInfo['va_pet_info'][$curPet], 'pettarget' => $petInfo['va_pet_info'][$objPet]);
	}
	
	public static function degenerateToEgg($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID))
		{
			Logger::fatal('Do not have the pet %d.', $petID);
			throw new Exception('fake');
		}

		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 获取宠物模板ID
		$tid = $petInfo['va_pet_info'][$petID]['tid'];
		// 判断
		if ($petInfo['cur_pet'] == $petID)
		{			
			MyPet::getInstance()->changeCurPet(0);
		}

		$user = EnUser::getInstance();
		if ($petInfo['va_pet_info'][$petID]['grow_up_lv'] >= 5 || $user->subGold(10)==false)
		{	
			return 'err';
		}
		MyPet::getInstance()->delPet($petID);
		MyPet::getInstance()->save();
		$user->update();
		$bag = BagManager::getInstance()->getBag();
		$bag->addItembyTemplateID(btstore_get()->PET[$tid]['reset_t_id'], 1);
		return $bag->update();
	}

	public static function addPetSkill($petID, $item_template_id, $num)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}		
		
		$bag = BagManager::getInstance()->getBag();
		$bag->deleteItembyTemplateID($item_template_id, $num);
		MyPet::getInstance()->addKnowPoint($petID, $num);
		MyPet::getInstance()->save();
		$petInfo = self::getUserPetInfo();
		return array('baginfo' => $bag->update(), 'petInfo' => array('id' => $petID, 'know_points' => $petInfo['va_pet_info'][$petID]['know_points']+$num));
	}
	
	public static function upTalentSkill($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyPet::getInstance()->checkPetExist($petID) || MyPet::getInstance()->inWareHouse($petID))
		{
			Logger::fatal('Do not have the pet %d. Or in warehouse.', $petID);
			throw new Exception('fake');
		}
		
		$petInfo = self::getUserPetInfo();
		$tid = $petInfo['va_pet_info'][$petID]['tid'];
		$info = btstore_get()->PET_SKILL_UP[$tid][$petInfo['va_pet_info'][$petID]['talent_lv']+1];
		$bag = BagManager::getInstance()->getBag();
		if ($bag->deleteItembyTemplateID(key($info['item_need']), $info['item_need'][key($info['item_need'])])==true);
		{
			$rate = floor($info['success'] + $info['fail'] * $petInfo['va_pet_info'][$petID]['talent_lose_times']) / 100;
			if ($rate > 100)
			{
				$rate = 100;
			}
			$rand = rand(0,100);
			if ($rate >= $rand)
			{
				MyPet::getInstance()->setTalentLostTime($petID,0);
				MyPet::getInstance()->addTalentLv($petID);
				$ret['ret'] = 'ok';
			} else 
			{
				MyPet::getInstance()->setTalentLostTime($petID, $petInfo['va_pet_info'][$petID]['talent_lose_times'] +1);			
				$ret['ret'] = 'err';
			}
			MyPet::getInstance()->save();
			$ret['bag'] = $bag->update();
			return $ret;
		}
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */