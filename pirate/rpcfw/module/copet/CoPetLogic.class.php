<?php

class CoPetLogic
{

	/**
	 * 获取用户的宠物信息
	 */
	public static function getUserPetInfo() 
	{
		// 获取宠物信息
		return MyCoPet::getInstance()->getUserPetInfo();
	}
	
	public static function born($petID_left, $petID_right)
	{
		$user = EnUser::getInstance();
		$user->subBelly(50000);
		$user->update();
		$petInfo = PetLogic::getUserPetInfo();
		$sun = btstore_get()->PET[$petInfo['va_pet_info'][$petID_left]['tid']]['quality'] + 
				btstore_get()->PET[$petInfo['va_pet_info'][$petID_right]['tid']]['quality'];
		$data = btstore_get()->PET_REPRODUCE[$sun];
		foreach ($data['output'] as $pid => $weight)
		{
			$output[] = array('pid' => $pid, 'weight' => $weight);
		}
		$randKey = Util::noBackSample($output, 1);
		MyCoPet::getInstance()->addNewPet($output[$randKey[0]]['pid']);
		self::addCDTime(43200);
		$copetInfo = MyCoPet::getInstance()->getUserPetInfo();
		return $copetInfo;
	}

	public static function bornTwins($petID_left, $petID_right)
	{
		$copetInfo = MyCoPet::getInstance()->getUserPetInfo();
		$send = self::getCDTime();
		switch ($copetInfo['cd_status'])
		{
			case 'B':
				$costGold = ceil($send/864);
				// $num = ceil(72000/43200);
				// $gold = ceil(43200*$num/864);
				// $bornNum = floor(9/$num);
				$costGold += 400;
				break;
			case 'F':
				$nextTime = 72000-$send;
				$num = ceil($nextTime/43200);
				$costGold = ceil(($num*43200+$send)/864);
				// $num1 = ceil(72000/43200);
				// $gold = ceil(43200*$num1/864);
				// $bornNum = floor((9-$num)/$num1);
				// $costGold += $bornNum*$gold;
				$bornNum = floor((9-$num)/2);
				$costGold += $bornNum*100;
				break;
		}
		$user = EnUser::getInstance();
		$user->subBelly(500000);
		$user->subGold($costGold);
		$user->update();
		$petInfo = PetLogic::getUserPetInfo();
		$sun = btstore_get()->PET[$petInfo['va_pet_info'][$petID_left]['tid']]['quality'] + 
				btstore_get()->PET[$petInfo['va_pet_info'][$petID_right]['tid']]['quality'];
		$data = btstore_get()->PET_REPRODUCE[$sun];
		foreach ($data['output'] as $pid => $weight)
		{
			$output[] = array('pid' => $pid, 'weight' => $weight);
		}
		$randKey = Util::backSample($output, 10);		
		foreach ($randKey as $val)
		{
			MyCoPet::getInstance()->addNewPet($output[$val]['pid']);
		}
		self::addCDTime(43200);
		MyCoPet::getInstance()->save();		
		return array('pet' => MyCoPet::getInstance()->getUserPetInfo());
	}

	/**
	 * 获取当前CD时刻
	 */
	public static function getCDTime()
	{
		// 获取CD截止时刻
		$endTime = MyCoPet::getInstance()->getCdEndTime();
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
		return MyCoPet::getInstance()->getCdEndTime();
	}

	/**
	 * 使用人民币清空CD时间
	 */
	public static function clearCDByGold()
	{
		// 获取CD时刻, 看看一共需要多少个金币
		$num = ceil(self::getCDTime() / 864);
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
		MyCoPet::getInstance()->resetCdTime();

		// 扣钱
		$user = EnUser::getInstance();
		$user->subGold($num);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $num);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_COPET_CLEARCDTIME, $num, Util::getTime());

		// 保存至数据库
		MyCoPet::getInstance()->save();
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
			$petInfo = MyCoPet::getInstance()->addCdTime($addTime);
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
		// 当前拥有的金币数量
		$gold = $user->getGold();
		// 计算所需金币数量
		$needGold = 20+($petCount-15)*5;
		// 如果金币数量不到
		if ($gold < $needGold)
		{
			Logger::trace('New copet slot need gold is %d. The user gold is %d.', 
			              $needGold, $gold);
			return 'err';
		}
		/**************************************************************************************************************
 		 * 开启新的宠物训练栏
 		 **************************************************************************************************************/
		// 增加一个新的宠物栏
		MyCoPet::getInstance()->openSlot();
		// 保存到数据库
		MyCoPet::getInstance()->save();
		/**************************************************************************************************************
 		 * 扣除金币数
 		 **************************************************************************************************************/
		$user->subGold($needGold);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $user->getGold(), $needGold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_COPET_OPENSLOT, $needGold, Util::getTime());

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
			Logger::fatal('The copet %d, did not have the %d skill now.', $petID, $skillID);
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
		MyCoPet::getInstance()->setLockState($petID, $skillID, PetDef::LOCK);
		// 保存到数据库
		MyCoPet::getInstance()->save();

		/**************************************************************************************************************
 		 * 扣除金币数
 		 **************************************************************************************************************/
		$user = EnUser::getInstance();
		$user->subGold($needGold);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $needGold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_COPET_LOCKSKILL, $needGold, Util::getTime());

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
			Logger::fatal('The copet %d, did not have the %d skill now.', $petID, $skillID);
			throw new Exception('fake');
		}
		// 设置锁定状态
		MyCoPet::getInstance()->setLockState($petID, $skillID, PetDef::UNLOCK);
		// 保存到数据库
		MyCoPet::getInstance()->save();
		return 'ok';
	}

	/**
	 * 装备宠物
	 * @param int $petID						宠物ID
	 */
	public static function equip($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyCoPet::getInstance()->checkPetExist($petID))
		{
			Logger::fatal('Do not have the copet %d.', $petID);
			throw new Exception('fake');
		}
		// 设置当前宠物
		MyCoPet::getInstance()->changeCurPet($petID);
		// 保存到数据库
		MyCoPet::getInstance()->save();
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();	
		
		return 'ok';
	}

	/**
	 * 卸下宠物
	 */
	public static function unequip()
	{
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 卸下当前宠物
		MyCoPet::getInstance()->changeCurPet(0);
		// 保存到数据库
		MyCoPet::getInstance()->save();
	}

	/**
	 * 出售宠物
	 * @param int $petID						宠物ID
	 */
	public static function sell($petID)
	{
		// 如果宠物ID不存在
		if (!MyCoPet::getInstance()->checkPetExist($petID))
		{
			Logger::fatal('Do not have the copet %d.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 查看此宠物是否正在装备中
		if ($petInfo['cur_pet'] == $petID)
		{
			// 卸下当前宠物
			MyCoPet::getInstance()->changeCurPet(0);
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
		MyCoPet::getInstance()->delPet($petID);
		// 保存到数据库
		MyCoPet::getInstance()->save();
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
		if (!MyCoPet::getInstance()->checkPetExist($petID))
		{
			Logger::fatal('Do not have the copet %d.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物技能个数
		$count = 0;
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 检查领悟点是否够用
		if ($petInfo['va_pet_info'][$petID]['know_points'] <= 0)
		{
			Logger::trace('Know points not enough, the %d copet have %d now.',
			              $petID, $petInfo['va_pet_info'][$petID]['know_points']);
			return 'err';
		}
		// 检查是否等级已经满了
		if (MyCoPet::getInstance()->isSkillLevelFull($petID))
		{
			Logger::trace('copet sill level is full, all of it.');
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
			MyCoPet::getInstance()->openNewSkillSlot($petID);
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
			MyCoPet::getInstance()->levelUpSkill($petID, $skillID, $oldID);
			// 告诉前端技能ID
			$info = $skillID;
			// 加算新技能个数
			++$count;
		}
		// 技能升级
		else if ($wightArr[$randKey[0]]['key'] == 'lv')
		{
			// 提升该宠物该技能的等级
			if (MyCoPet::getInstance()->levelUpSkill($petID, $wightArr[$randKey[0]]['id']))
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
		MyCoPet::getInstance()->subKnowPoint($petID);
		// 保存到数据库
		MyCoPet::getInstance()->save();
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();

		// 获取最新的宠物信息，返回前端
		return array('pet' => $petInfo['va_pet_info'][$petID], 'stat' => $info);
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
		if (!MyCoPet::getInstance()->checkPetExist($petID))
		{
			Logger::fatal('Do not have the copet %d.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 返回计算结果
		return MyCoPet::calculateAllAttr($petInfo['va_pet_info'][$petID]['tid'], 
		                               $petInfo['va_pet_info'][$petID]['skill_info']);
	}

	/**
	 * 重置
	 * @param int $petID						重置的宠物ID
	 */
	public static function reset($petID)
	{
		/**************************************************************************************************************
 		 * 获取基本信息
 		 **************************************************************************************************************/
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyCoPet::getInstance()->checkPetExist($petID))
		{
			Logger::fatal('Do not have the pet %d.', $petID);
			throw new Exception('fake');
		}

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

		// 如果是金币重置，那么需要检查金币数量
		if ($userInfo['gold_num'] < $gold)
		{
			Logger::trace('Not enough gold. Need %d, now have %d.', $gold, $userInfo['gold_num']);
			return 'err';
		}
		/**************************************************************************************************************
 		 * 重置所有技能
 		 **************************************************************************************************************/
		$retPoints = MyCoPet::getInstance()->resetSkill($kownPoint, $petID, $tid);

		// 扣钱
		$user = EnUser::getInstance();
		$user->subGold($gold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_COPET_RESET, $gold, Util::getTime());

		// 保存到数据库
		$petInfo = MyCoPet::getInstance()->save();
		// 返回该宠物所拥有的领悟点个数
		return $petInfo['va_pet_info'][$petID];
	}

	/**
	 * 提交洗练结果
	 * 
	 * @param int $petID						宠物ID
	 */
	public static function commitRefresh($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyCoPet::getInstance()->checkPetExist($petID))
		{
			Logger::fatal('Do not have the copet %d.', $petID);
			throw new Exception('fake');
		}
		// 提交洗练结果
		MyCoPet::getInstance()->commitQualifications($petID);
		// 保存到数据库
		MyCoPet::getInstance()->save();

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
		if (!MyCoPet::getInstance()->checkPetExist($petID))
		{
			Logger::fatal('Do not have the copet %d.', $petID);
			throw new Exception('fake');
		}
		// 提交洗练结果
		MyCoPet::getInstance()->rollbackQualifications($petID);
		// 保存到数据库
		MyCoPet::getInstance()->save();

		return 'ok';
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
		if (!MyCoPet::getInstance()->checkPetExist($petID))
		{
			Logger::fatal('Do not have the copet %d.', $petID);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 获取宠物模板ID
		$tid = $petInfo['va_pet_info'][$petID]['tid'];
		// 获取现有资质总和
		$quaSum = MyCoPet::getInstance()->sumAllQualifications($petID);
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
				if (!MyCoPet::getInstance()->isTopAlready($attr, $petID) &&
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
				if (MyCoPet::getInstance()->isTopAlready($attr, $petID))
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
			$top = MyCoPet::__getPetQualificationTop($attr, 
												   $petInfo['va_pet_info'][$petID]['lv'], 
												   $tid);
			// 判断是否超过，并记录到底加算了多少
			Logger::debug("Refresh add is %d, top is %d.", $add, $top);
			// 加上数值
			MyCoPet::getInstance()->addQualifications($petID, $attr, $add, $top);
		}
		// 更新到数据库
		$petInfo = MyCoPet::getInstance()->save();
		// 返回
		return array('bagInfo' => $bagInfo, 'qualifications' => $petInfo['va_pet_info'][$petID]['qualifications']);
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
		if (!MyCoPet::getInstance()->checkPetExist($curPet)||!MyCoPet::getInstance()->checkPetExist($objPet))
		{
			Logger::fatal('Do not have the pet %d, %d.', $curPet, $objPet);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 检查传承和被传承次数
		// if (!MyCoPet::getInstance()->checkTransferTimes($curPet, $objPet))
		// {
			// Logger::warning('Transfer time not enough.');
			// throw new Exception('fake');
		// }

		/**************************************************************************************************************
 		 * 检查金币是否足够
 		 **************************************************************************************************************/
		// 获取所需金币
		switch ($type)
		{
			case 60:
				$needGold = 10;
				break;
			case 100:
				$needGold = 1000;
				break;
		}
		
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 当前拥有的金币数量
		if ($user->getGold() < $needGold)
		{
			Logger::warning('Not enough gold, user has %d, need is %d.', $user->getGold(), $needGold);
			return 'err';
		}

		/**************************************************************************************************************
 		 * 扣除传承所需
 		 **************************************************************************************************************/
		// 扣除金币
		$user->subGold($needGold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_COPET_TRANSFER_GOLD, $needGold, Util::getTime());
		// 加算传承次数
		// MyCoPet::getInstance()->addTransferTimes($curPet, $objPet);
		/**************************************************************************************************************
 		 * 传承并计算等级
 		 **************************************************************************************************************/
		// 传承经验
		MyCoPet::getInstance()->transferExp($curPet, $objPet, $type);
		// 传承资质
		MyCoPet::getInstance()->transferQualifications($curPet, $objPet, $type);

		// 更新到数据库
		MyCoPet::getInstance()->save();
		// 返回最新信息
		return self::getUserPetInfo();
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
		if (!MyCoPet::getInstance()->checkPetExist($curPet)||!MyCoPet::getInstance()->checkPetExist($objPet))
		{
			Logger::fatal('Do not have the pet %d, %d.', $curPet, $objPet);
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = self::getUserPetInfo();
		// 检查传承和被传承次数
		// if (!MyCoPet::getInstance()->checkTransferTimes($curPet, $objPet))
		// {
			// Logger::warning('Transfer time not enough.');
			// throw new Exception('fake');
		// }

		/**************************************************************************************************************
 		 * 检查金币是否足够
 		 **************************************************************************************************************/
		// 获取所需金币
		$needGold = 2000;
		
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 当前拥有的金币数量
		if ($user->getGold() < $needGold)
		{
			Logger::warning('Not enough gold, user has %d, need is %d.', $user->getGold(), $needGold);
			return 'err';
		}

		/**************************************************************************************************************
 		 * 扣除传承所需
 		 **************************************************************************************************************/
		// 扣除金币
		$user->subGold($needGold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_COPET_TRANSFER_GOLD, $needGold, Util::getTime());
		// 加算传承次数
		// MyCoPet::getInstance()->addTransferTimes($curPet, $objPet);
		/**************************************************************************************************************
 		 * 传承并计算等级
 		 **************************************************************************************************************/
		// 传承经验
		MyCoPet::getInstance()->transferExp($curPet, $objPet, 100);
		// 传承资质
		MyCoPet::getInstance()->transferQualifications($curPet, $objPet, 100);
		
		MyCoPet::getInstance()->transferTalentLv($curPet, $objPet);

		// 更新到数据库
		MyCoPet::getInstance()->save();
		// 返回最新信息
		return self::getUserPetInfo();
	}
	
	public static function changeToEgg($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyCoPet::getInstance()->checkPetExist($petID))
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
			MyCoPet::getInstance()->changeCurPet(0);
		}
		$user = EnUser::getInstance();
		if ($petInfo['va_pet_info'][$petID]['lv'] >= 10 && $user->subGold(10)==false)
		{	
			return 'err';
		}
		MyCoPet::getInstance()->delPet($petID);
		MyCOPet::getInstance()->save();
		$user->update();
		$bag = BagManager::getInstance()->getBag();
		$bag->addItembyTemplateID(btstore_get()->PET[$tid]['reset_t_id'], 1);		
		return array('gold' => $user->getGold(), 'bagInfo' => $bag->update());
	}

	public static function addPetSkill($petID, $item_template_id, $num)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyCoPet::getInstance()->checkPetExist($petID))
		{
			Logger::fatal('Do not have the pet %d.', $petID);
			throw new Exception('fake');
		}
		
		$petInfo = self::getUserPetInfo();
		$bag = BagManager::getInstance()->getBag();
		$bag->deleteItembyTemplateID($item_template_id, $num);
		MyCoPet::getInstance()->addKnowPoint($petID, $num);
		MyCoPet::getInstance()->save();
		return array('baginfo' => $bag->update(), 'petInfo' => array('id' => $petID, 'know_points' => $petInfo['va_pet_info'][$petID]['know_points']+$num));
	}

	public static function upTalentSkill($petID)
	{
		// 如果宠物ID不存在 或者在仓库里面
		if (!MyCoPet::getInstance()->checkPetExist($petID))
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
				MyCoPet::getInstance()->setTalentLostTime($petID,0);
				MyCoPet::getInstance()->addTalentLv($petID);
				$ret['ret'] = 'ok';
			} else 
			{
				MyCoPet::getInstance()->setTalentLostTime($petID, $petInfo['va_pet_info'][$petID]['talent_lose_times'] +1);			
				$ret['ret'] = 'err';
			}
			MyCoPet::getInstance()->save();
			$ret['bag'] = $bag->update();
			return $ret;
		}
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */