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
 * Class       : EnPet
 * Description : 宠物内部接口类
 * Inherit     : 
 **********************************************************************************************************************/
class EnPet
{
	/**
	 * 开启新的宠物训练栏位
	 */
	public static function openTrainSlot($cabinLv)
	{
		// 如果尚未开启宠物室
		if (!EnSwitch::isOpen(SwitchDef::PET))
		{
			return 'ok';
		}
		// 获取宠物信息
		$petInfo = MyPet::getInstance()->getUserPetInfo();
		// 空值判断
		if ($petInfo === false)
		{
			Logger::debug('Init pet info.');
			// 初始化人物宠物信息
			$petInfo = MyPet::getInstance()->addNewPetInfo();
		}

		// 算出开启下个宠物训练栏位所需要的等级, 如果所需等级未到
		if (empty(btstore_get()->PET_ROOM['next_slot_lvs'][$petInfo['train_slots']]) || 
		    intval(btstore_get()->PET_ROOM['next_slot_lvs'][$petInfo['train_slots']]) > $cabinLv)
		{
			Logger::debug('Currant pet cabin level is %d, need level is %s.', $cabinLv, 
			               btstore_get()->PET_ROOM['next_slot_lvs']->toArray());
			return 'err';
		}
		// 否则，增加一个宠物栏位
		MyPet::getInstance()->openTrainSlot();
		// 保存到数据库
		MyPet::getInstance()->save();
		return 'ok';
	}

	/**
	 * 孵化一个蛋，恩，蛋
	 * 
	 * @param $petTID	 						宠物模板ID
	 */
	public static function hatch($petTID)
	{
		// 宠物室等级检查, 获取最新舱室信息
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
		// 获取宠物室等级
		$cabinLv = $cabinInfo[SailboatDef::PET_ID]['level'];
		if (empty($cabinLv) || $cabinLv <= 0)
		{
			Logger::fatal('Can not get pet room level!');
			throw new Exception('fake');
		}
		// 当然，如果要孵化宠物蛋，需要先有宠物室，如果有宠物室，必然会通过这个宠物室节点
		if (!EnSwitch::isOpen(SwitchDef::PET))
		{
			Logger::fatal('Can not get pet info before task!');
			throw new Exception('fake');
		}
		// 获取宠物信息
		$petInfo = MyPet::getInstance()->getUserPetInfo();
		// 控制判断
		if ($petInfo === false)
		{
			Logger::debug('Init pet info.');
			// 初始化人物宠物信息
			$petInfo = MyPet::getInstance()->addNewPetInfo();
		}
		// 个数检查
		// 如果宠物栏数目小于当前宠物个数
		if ($petInfo['pet_slots'] <= MyPet::getInstance()->countOutWareHouse())
		{
			// 不能再孵化了
			Logger::trace('The count of pets can equip is %d, now equip num is %d.', 
			              $petInfo['pet_slots'], MyPet::getInstance()->countOutWareHouse());
			return false;
		}
		// 否则，增加一个宠物
		$newPet = MyPet::getInstance()->addNewPet($petTID);
		// 保存到数据库
		MyPet::getInstance()->save();
		// 通知成就系统
		EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::OWN_PET, $petTID);
		EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::PET_TYPE_NUM, 1);

		return $newPet;
	}

	/**
	 * 通过用户ID获取当前装备的宠物ID
	 * @param int $uid							用户ID
	 */
	public static function getUserCurPetID($uid)
	{
		// 查看输入的用户ID
		if (RPCContext::getInstance()->getUid() == $uid)
		{
			// 如果是当前用户，那么从内存中获取宠物信息
			$petInfo = MyPet::getInstance()->getUserPetInfo();
		}
		else 
		{
			// 如果不是当前用户，那么从数据库中获取宠物信息
			$petInfo = PetDao::getPetInfo($uid);
			// 修复旧有宠物数据
			$petInfo = self::fixUserPetInfo($petInfo);
		}
		// 返回
		return isset($petInfo['cur_pet']) ? $petInfo['cur_pet'] : 0;
	}

	/**
	 * 通过用户ID获取当前装备的宠物信息
	 * @param int $uid							用户ID
	 */
	public static function getUserCurPet($uid)
	{
		// 设置返回值
		$ret = false;
		// 查看输入的用户ID
		if (RPCContext::getInstance()->getUid() == $uid)
		{
			// 如果是当前用户，那么从内存中获取宠物信息
			$petInfo = MyPet::getInstance()->getUserPetInfo();
		}
		else 
		{
			// 如果不是当前用户，那么从数据库中获取宠物信息
			$petInfo = PetDao::getPetInfo($uid);
			// 修复旧有宠物数据
			$petInfo = self::fixUserPetInfo($petInfo);
		}
		// 如果装备的有宠物
		if (!empty($petInfo['cur_pet']))
		{
			// 获取宠物详细信息
			$ret = $petInfo['va_pet_info'][$petInfo['cur_pet']];
			$ret['name'] = btstore_get()->PET[$ret['tid']]['name'];
		}
		// 返回
		return $ret;
	}

	/**
	 * 宠物数据更新，需要在这里修复一下数据格式，加一些新项目
	 */
	public static function fixUserPetInfo($petInfo)
	{
		// 空值判断
		if ($petInfo === false)
		{
			return $petInfo;
		}
		// 修复用户数据, 查看仓库个数
		if (empty($petInfo['warehouse_slots']))
		{
			$petInfo['warehouse_slots'] = btstore_get()->PET_ROOM['init_warehouse_slot'];
		}
		// 修复用户数据, 循环查看所有宠物信息
		foreach ($petInfo['va_pet_info'] as $petID => $pet)
		{
			// 如果宠物没有仓库数据，则添加仓库数据
			if (!isset($petInfo['va_pet_info'][$petID]['in_warehouse']))
			{
				$petInfo['va_pet_info'][$petID]['in_warehouse'] = PetDef::OUT_WARE_HOUSE;
			}
			// 如果宠物没有成长数据，则加入成长经验和等级
			if (!isset($petInfo['va_pet_info'][$petID]['grow_up_exp']) || 
				!isset($petInfo['va_pet_info'][$petID]['grow_up_lv'])) 
			{
				$petInfo['va_pet_info'][$petID]['grow_up_exp'] = 0;
				$petInfo['va_pet_info'][$petID]['grow_up_lv'] = 0;
			}
			// 如果没有宠物属性加成，则加入属性加成
			if (!isset($petInfo['va_pet_info'][$petID]['qualifications']['pow']))
			{
				$petInfo['va_pet_info'][$petID]['qualifications'] = array('pow' => array('cur' => 0, 'add' => 0), 
																		  'sen' => array('cur' => 0, 'add' => 0), 
																		  'int' => array('cur' => 0, 'add' => 0),
																		  'phy' => array('cur' => 0, 'add' => 0));
			}
		}
		// 返回最新数据
		return $petInfo;
	}

	/**
	 * 计算用户当前宠物资质加成属性
	 *
	 * @param int $uid							用户ID
	 */
	private static function __getUserCurPetQualifications($petInfo, $buf)
	{
		// 如果装备的有宠物
		if (!empty($petInfo['cur_pet']))
		{
			// 获取宠物模板ID
			$tid = $petInfo['va_pet_info'][$petInfo['cur_pet']]['tid'];
			// 查看是否已经记录的有值了
			for ($index = PetDef::PET_HP; $index <= PetDef::DEF_MGC; ++$index)
			{
				// 如果还没记录过这个值，那么就赋值为0， 以便下面直接进行加算
				if (!isset($buf[$index]))
				{
					$buf[$index] = 0;
				}
			}
			/**********************************************************************************************************
 		 	 * 宠物资质增加的最终物理攻击=宠物蛮力资质*宠物资质攻击系数/10000
 		 	 * 宠物资质增加的最终物理防御=宠物蛮力资质*宠物资质防御系数/10000
 		 	 * 宠物资质增加的最终必杀攻击=宠物灵敏资质*宠物资质攻击系数/10000
 		 	 * 宠物资质增加的最终必杀防御=宠物灵敏资质*宠物资质防御系数/10000
 		 	 * 宠物资质增加的最终魔法攻击=宠物智慧资质*宠物资质攻击系数/10000
 		 	 * 宠物资质增加的最终魔法防御=宠物智慧资质*宠物资质防御系数/10000
 		 	 * 宠物资质增加的最终生命=宠物体质资质*宠物资质生命系数/10000
 		 	 **********************************************************************************************************/
			$buf[PetDef::ATK_PHY] += $petInfo['va_pet_info'][$petInfo['cur_pet']]['qualifications']['pow']["cur"] *
									 intval(btstore_get()->PET[$tid]['atk']) / PetDef::LITTLE_WHITE_PERCENT;
			$buf[PetDef::DEF_PHY] += $petInfo['va_pet_info'][$petInfo['cur_pet']]['qualifications']['pow']["cur"] *
							  	     intval(btstore_get()->PET[$tid]['def']) / PetDef::LITTLE_WHITE_PERCENT;
			$buf[PetDef::ATK_SPE] += $petInfo['va_pet_info'][$petInfo['cur_pet']]['qualifications']['sen']["cur"] *
							  	     intval(btstore_get()->PET[$tid]['atk']) / PetDef::LITTLE_WHITE_PERCENT;
			$buf[PetDef::DEF_SPE] += $petInfo['va_pet_info'][$petInfo['cur_pet']]['qualifications']['sen']["cur"] *
							  	     intval(btstore_get()->PET[$tid]['def']) / PetDef::LITTLE_WHITE_PERCENT;
			$buf[PetDef::ATK_MGC] += $petInfo['va_pet_info'][$petInfo['cur_pet']]['qualifications']['int']["cur"] *
							  	     intval(btstore_get()->PET[$tid]['atk']) / PetDef::LITTLE_WHITE_PERCENT;
			$buf[PetDef::DEF_MGC] += $petInfo['va_pet_info'][$petInfo['cur_pet']]['qualifications']['int']["cur"] *
							  	     intval(btstore_get()->PET[$tid]['def']) / PetDef::LITTLE_WHITE_PERCENT;
			$buf[PetDef::PET_HP] += $petInfo['va_pet_info'][$petInfo['cur_pet']]['qualifications']['phy']["cur"] *
							   	     intval(btstore_get()->PET[$tid]['hp']) / PetDef::LITTLE_WHITE_PERCENT;
		}
		Logger::debug("__getUserCurPetQualifications ret is %s.", $buf);
		// 如果错的了话返回空
		return $buf;
	}

	/**
	 * 计算宠物所有技能属性加成
	 * @param int $uid							用户ID
	 */
	public static function getUserCurPetAllAttr($uid)
	{
		// 设置返回值
		$ret = false;
		$buf = false;
		// 查看输入的用户ID
		if (RPCContext::getInstance()->getUid() == $uid)
		{
			// 如果是当前用户，那么从内存中获取宠物信息
			$petInfo = MyPet::getInstance()->getUserPetInfo();
		}
		else 
		{
			// 如果不是当前用户，那么从数据库中获取宠物信息
			$petInfo = PetDao::getPetInfo($uid);
			// 修复下数据
			$petInfo = self::fixUserPetInfo($petInfo);
		}
		// 如果装备的有宠物
		if (!empty($petInfo['cur_pet']))
		{
			// 获取宠物详细信息
			$ret = $petInfo['va_pet_info'][$petInfo['cur_pet']];
			$ret['name'] = btstore_get()->PET[$ret['tid']]['name'];
		}
		// 如果有宠物信息
		if ($ret === false)
		{
			return $buf;
		}
		// 返回计算结果
		$attr = MyPet::calculateAllAttr($ret['tid'], $ret['skill_info']);
		// 获取资质加成属性
		return self::__getUserCurPetQualifications($petInfo, $attr);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */