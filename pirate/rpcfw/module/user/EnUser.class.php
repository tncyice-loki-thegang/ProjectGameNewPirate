<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: EnUser.class.php 6559 2011-10-18 13:47:03Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL:
 * svn://192.168.1.80:3698/C/trunk/pirate/rpcfw/module/user/EnUser.class.php $
 * 
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 *         @date $Date: 2011-10-18 21:47:03 +0800 (二, 2011-10-18) $
 * @version $Revision: 6559 $
 *          @brief
 *         
 *         
 */
class EnUser
{
	/**
	 *
	 * @deprecated
	 *
	 * @param
	 *        	uint uid 当前登录用户，uid设置为null
	 *        	当前登录用户，
	 * @return array
	 * @see UserDef::$USER_FIELDS
	 */
	public static function getUser ($uid = null)
	{
		$userObj = EnUser::getUserObj($uid);
		return $userObj->getUserInfo();
	}
	
	/**
	 *
	 *
	 * 判断某个物品是否属于某个用户
	 * 暂时不支持其他用户， uid 必须传null
	 * 
	 * @param int $item_id
	 *        	物品ID
	 * @param int $uid=NULL
	 *        	用户ID
	 *        	
	 * @return boolean TRUE表示item_id属于用户$uid
	 */
	public static function itemBelongTo ($item_id, $uid = NULL)
	{
		if ($uid == NULL)
		{
			// item in bag?
			$bag = BagManager::getInstance()->getBag();
			if ($bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID)
			{
				$heroes = EnUser::getUserObj()->getRecruitHeroes();
				if (empty($heroes))
				{
					return FALSE;
				}
				// item on hero
				foreach ($heroes as $hero)
				{
					// item in arming
					foreach ($hero['va_hero']['arming'] as $arm_position=>$arm_item_id)
					{
						if ($arm_item_id == $item_id)
						{
							return TRUE;
						}
					}
					// item in dress
					if (isset($hero['va_hero']['dress']))
					{
						foreach ($hero['va_hero']['dress'] as $position=>$itemId)
						{
							if ($itemId == $item_id)
							{
								return TRUE;
							}
						}
					}
					// item in jewelry
					if (isset($hero['va_hero']['jewelry']))
					{
						foreach ($hero['va_hero']['jewelry'] as $position=>$itemId)
						{
							if ($itemId == $item_id)
							{
								return TRUE;
							}
						}
					}
					// item in daimonapple
					foreach ($hero['va_hero']['daimonApple'] as $position_id=>$daimonapple_item_id)
					{
						if ($daimonapple_item_id == $item_id)
						{
							return TRUE;
						}
					}
				}
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * 保存UserObj
	 * uid => UserObj
	 * 
	 * @var unknown_type
	 */
	private static $arrUser = array();
	
	/**
	 *
	 * @return UserObj
	 */
	public static function getUserObj ($uid = 0)
	{
		if ($uid == 0)
		{
			$uid = RPCContext::getInstance()->getUid();
			if ($uid == null)
			{
				Logger::fatal('uid and global.uid are 0');
				throw new Exception('fake');
			}
		}
		
		if (!isset(self::$arrUser[$uid]))
		{
			if ($uid == RPCContext::getInstance()->getUid())
			{
				self::$arrUser[$uid] = new UserObj($uid);
			}
			else
			{
				self::$arrUser[$uid] = new OtherUserObj($uid);
			}
		}
		return self::$arrUser[$uid];
	}
	
	/**
	 *
	 * @deprecated
	 *
	 */
	public static function getInstance ($uid = 0)
	{
		return self::getUserObj($uid);
	}
	public static function release ($uid = 0)
	{
		if ($uid == 0)
		{
			self::$arrUser = array();
		}
		else if (isset(self::$arrUser[$uid]))
		{
			unset(self::$arrUser[$uid]);
		}
	}
	
	/**
	 * 返回历史充值过的所有金币数
	 */
	public static function getSumGold ()
	{
		return User4BBpayDao::getSumGoldByUid(RPCContext::getInstance()->getUid());
		;
	}
	
	/**
	 * 返回 >=time1 <=time2 的充值总数
	 * 
	 * @param unknown_type $time1        	
	 * @param unknown_type $time2        	
	 * @param unknown_type $uid        	
	 * @return number
	 */
	public static function getSumGoldByTime ($time1, $time2, $uid = 0)
	{
		if ($uid == 0)
		{
			$uid = RPCContext::getInstance()->getUid();
		}
		return User4BBpayDao::getSumGoldByTime($time1, $time2, $uid);
	}
	
	public static function getBattleInfoKey($uid)
	{
		return 'battle_info#' . $uid;
	}
	
	const MODIFY_BATTLE_INFO_KEY = 'global.modify_battle_info';
	
	public static function modifyBattleInfo()
	{
		RPCContext::getInstance()->setSession(self::MODIFY_BATTLE_INFO_KEY, 1);
	}
	
	public static function getSpendRewardKey($beginTime, $endTime)
	{
		return  $beginTime . '-' . $endTime;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */