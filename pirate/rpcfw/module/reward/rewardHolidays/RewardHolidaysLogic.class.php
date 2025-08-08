<?php

class RewardHolidaysLogic
{
	private static $arrField = array(
	'uid',
	'id',
	'begin_time',
	'end_time',
	'accumulate_time',
	'va_rewardInfo',
	);
	
	/**
	 * 得到有效的id。 根据时间检查
	 * Enter description here ...
	 */
	public static function getValidGiftId()
	{
		$curTime = Util::getTime();
		$curDayOfWeek = Util::getTodayWeek();
		foreach (btstore_get()->REWARD_SUMMER as $id => $arrGift)
		{
			if ( $curTime > $arrGift['begin'] && $curTime < $arrGift['end'] && in_array($curDayOfWeek, $arrGift['weekdays']->toArray()))
			{
				return $id;
			}
		}
	}
	
	public static function getHolidaysInfo()
	{
		$uid = RPCContext::getInstance()->getUid();
		$id = self::getValidGiftId();		
		$arrGift = RPCContext::getInstance()->getSession('reward.holidays');
		
		if ($arrGift!==null)
		{
			return $arrGift;
		}

		$arrGift = RewardHolidaysDao::get($uid, $id, self::$arrField);
		
		if (empty($arrGift))
		{
			$arrGift = self::insertDefault($uid, $id);
		}

		//都不等于0,数据有问题。可能是服务器挂掉后， 直接修改数据库end_time数据。这里修复数据
		if ($arrGift['begin_time']!=0 && $arrGift['end_time']!=0)
		{
			$arrGift['accumulate_time'] += ($arrGift['end_time'] - $arrGift['begin_time']);
			//重新开始计时
			$arrGift['begin_time'] = Util::getTime();
			$arrGift['end_time'] = 0;
			RewardHolidaysDao::update($uid, $id, $arrGift);
		}
		//都为0, 用户正常下线
		else if ($arrGift['begin_time']==0 && $arrGift['end_time']==0)
		{
			//重新开始计时
			$arrGift['begin_time'] = Util::getTime();
			RewardHolidaysDao::update($uid, $id, $arrGift);
		}
		else
		{
			//nothing
			//begin_time !=0, end_time==0, 可能为session被清空了,或者是刚刚插入数据库的
			//begin_time ==0, end_time!=0， 没有这情况。如果发生了，也没有悲剧。用户领取第一个礼包，或者下线都能修复数据
		}
		
		RPCContext::getInstance()->setSession('reward.holidays', $arrGift);
		return $arrGift;
	}

	public static function holidaysReward($uid, $step)
	{
		$arrGift = self::getHolidaysInfo();

		//检查位置
		if (in_array($step, $arrGift['va_rewardInfo']))
		{
			Logger::warning('fail to get gift, rewarded');
			throw new Exception('fake');
		}
		
		array_push($arrGift['va_rewardInfo'], $step);
	
		//发奖
		$rewardId = btstore_get()->REWARD_SUMMER[$arrGift['id']]['prize'][$step];
		$reward  = RewardUtil::rewardById($uid, $rewardId, true);
		
		//update session
		RPCContext::getInstance()->setSession('reward.holidays', $arrGift);		
		//update db				
		RewardHolidaysDao::update($uid, $arrGift['id'], $arrGift);				
		
		// if (isset($arrRet['reward']['gold']))
		// {
			// Statistics::gold(StatisticsDef::ST_FUNCKEY_REWARD_HOLIDAYS,
				// $arrRet['reward']['gold'],
				// Util::getTime(), false);
		// }
		
		return $reward;			
	}
	
	public static function allHolidaysReward($uid)
	{
		$arrGift = self::getHolidaysInfo();
		$cfg = btstore_get()->REWARD_SUMMER[$arrGift['id']];
		$count = count($arrGift['va_rewardInfo']);
		$user = EnUser::getInstance();
		$user->subGold($cfg['allGold'][$count]);
		$user->update();
		
		$reward = array();
		foreach ($cfg['prize'] as $key => $rewardId)
		{
			if (in_array($key, $arrGift['va_rewardInfo']))
			{
				continue;
			}
			array_push($reward, RewardUtil::rewardById($uid, $rewardId, true));			
		}
		$arrGift['va_rewardInfo'] = array(0,1,2,3,4);
		RPCContext::getInstance()->setSession('reward.holidays', $arrGift);
		RewardHolidaysDao::update($uid, $arrGift['id'], $arrGift);
		return $reward;
	}

	
	//用户下线
	public static function logoff()
	{
		$arrGift = self::getHolidaysInfo();
		$arrGift['end_time'] = Util::getTime();
		$arrGift['accumulate_time'] += ($arrGift['end_time'] - $arrGift['begin_time']);
		$arrGift['end_time'] = $arrGift['begin_time'] = 0;
		RewardHolidaysDao::update($arrGift['uid'], $arrGift['id'], $arrGift);
	}
	
	//用户登录
	public static function login()
	{
		self::getHolidaysInfo();
	}

	private static function insertDefault($uid, $id)
	{
		$arrField = array(
		'uid'=>$uid,
		'id'=>$id,
		'begin_time'=>Util::getTime(),
		'end_time'=>0,
		'accumulate_time'=>0,
		'va_rewardInfo'=>array(),
		);
		RewardHolidaysDao::insert($uid, $id, $arrField);
		return $arrField;
	}
		
}