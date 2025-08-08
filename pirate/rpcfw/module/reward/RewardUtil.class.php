<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: RewardUtil.class.php 40302 2013-03-08 05:16:54Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/RewardUtil.class.php $
 * @author $Author: yangwenhai $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-08 13:16:54 +0800 (五, 2013-03-08) $
 * @version $Revision: 40302 $
 * @brief 
 *  
 **/

class RewardUtil
{
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param unknown_type $rewardId
	 * @param unknown_type $tmpBag 是否放临时背包
	 */
	public static function rewardById($uid, $rewardId, $tmpBag=false)
	{
		$cfg = btstore_get()->REWARD_ONLINE_LIB[$rewardId];
		return self::reward($uid, $cfg, $tmpBag);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param unknown_type $arrReward
	 * @param unknown_type $tmpBag 是否放临时背包
	 */
	public static function reward($uid, $arrReward, $tmpBag=false)
	{
		$res = array();
		
		$user = EnUser::getUserObj($uid);
		$bag = null;
		foreach ($arrReward as $typeValue)
		{
			$type = $typeValue['type'];
			$value = $typeValue['value'];
			switch ($type)
			{
				case RewardType::BELLY:
					$user->addBelly($value);
					self::addKeyValue($res, 'belly', $value);
					break;
				case RewardType::EXPERIENCE:
					$user->addExperience($value);
					self::addKeyValue($res, 'experience', $value);
					break;
				case RewardType::GOLD:
					$user->addGold($value);
					self::addKeyValue($res, 'gold', $value);
					break;
				case RewardType::EXECUTION:
					$user->addExecution($value);
					self::addKeyValue($res, 'execution', $value);
					break;
				case RewardType::ITEM:
					$bag = BagManager::getInstance()->getBag($uid);
					if (!$bag->addItemByTemplateID($value, 1, $tmpBag))
					{
						Logger::warning('fail to reward, the bag is full');
						throw new Exception('fake');
					}
					break;
				case RewardType::BELLY_MUL_LEVEL:
					$belly = $value * $user->getMasterHeroLevel();
					$user->addBelly($belly);
					self::addKeyValue($res, 'belly', $belly);					
					break;
				case RewardType::EXPERIENCE_MUL_LEVEL:
					$experiece = $value * $user->getMasterHeroLevel();
					$user->addExperience($experiece);
					self::addKeyValue($res, 'experience', $experiece);	
					break;
				case RewardType::PRESTIGE:
					$user->addPrestige($value);
					self::addKeyValue($res, 'prestige', $value);
					break;
				case RewardType::ITEM_MULTI:
					$iteminfo = array_map("intval",explode('|',$value));
					if(count($iteminfo) != 2 || empty($iteminfo[0]) || empty($iteminfo[1]))
					{
						Logger::warning('ITEM_MULTI parameter not matched');
						throw new Exception('fake');
					}
					$bag = BagManager::getInstance()->getBag($uid);
					if (!$bag->addItemByTemplateID($iteminfo[0], $iteminfo[1], $tmpBag))
					{
						Logger::warning('fail to reward, the bag is full');
						throw new Exception('fake');
					}
					break;
				case RewardType::JEW_ENERGY:
					Jewelry::addEnergyElement($uid,$value,0);
					self::addKeyValue($res, 'jew_energy', $value);
					break;
				case RewardType::JEW_ELEMENT:
					Jewelry::addEnergyElement($uid,0,$value);
					self::addKeyValue($res, 'jew_element', $value);
					break;
				case RewardType::GEM_CARVED:					
					CruiseLogic::addCarvedStone($uid, $value);
					self::addKeyValue($res, 'gem_carved', $value);
					break;
				default:
					Logger::fatal("invalid reward type:%d",$type);
					throw new Exception("fake");
					break;
			}
		}
		
		if ($bag!=null)
		{
			$res['grid'] = $bag->update();
		}
		$user->update();

		return $res;
	}
	
	private static function addKeyValue(&$arr, $key, $value)
	{
		if (!isset($arr[$key]))
		{
			$arr[$key] = $value;
		}
		else
		{
			$arr[$key] += $value;
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */