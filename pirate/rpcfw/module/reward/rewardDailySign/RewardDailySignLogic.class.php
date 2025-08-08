<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: RewardSignLogic.class.php 27052 2012-09-12 08:52:01Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/rewardSign/RewardSignLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-12 16:52:01 +0800 (三, 2012-09-12) $
 * @version $Revision: 27052 $
 * @brief 
 *  
 **/

class RewardDailySignLogic
{
	private static $arrField = array('uid', 'acc_sign_time', 'acc_fillsign_time', 'used_fill_sign_times', 'va_signInfo');
	
	public static function get($uid)
	{
		$info = self::get_($uid);
		$arrayDailySignGainInfo = btstore_get()->REWARD_SIGNDAY;
		$curTime = Util::getTime();
		//Lay moc su kien
		foreach ($arrayDailySignGainInfo as $key => $value)
		{
			if ($curTime >= $value['begin'] && $curTime <= $value['end']) {
				$eventId=$key;
				$eventData=$value;
				break;
			}			
		}
		//kiem tra diem danh lan cuoi so voi su kien hien tai
			
		if ($info['acc_fillsign_time']!=0 && $info['acc_sign_time']!=0)
		{			
			
			if ($info['acc_sign_time'] < $eventData['begin'] || $info['acc_fillsign_time'] < $eventData['begin'])
			{
				$newInfo = array();
				$newInfo['va_signInfo']['oldsign']=$info['va_signInfo']['sign'];
				$newInfo['va_signInfo']['sign']=array();
				$newInfo['va_signInfo']['oldreward']=$info['va_signInfo']['reward'];
				$newInfo['va_signInfo']['reward']=array();
				$newInfo['used_fill_sign_times']=0;
				$newInfo['acc_sign_time']=0;
				$newInfo['acc_fillsign_time']=0;
				RewardDailySignDao::update($uid, $newInfo);
			}
		}
		if (!empty($newInfo)) {
			$info = $newInfo;
		}
		$arrRet['used_fill_sign_times'] = $info['used_fill_sign_times'];
		$arrRet['va_signInfo'] = $info['va_signInfo'];
		return $arrRet;		
	}
	
	public static function get_($uid)
	{				
		$ret = RewardDailySignDao::get($uid, self::$arrField);
		if (empty($ret))
		{
			self::insertDefault($uid);
			$ret = RewardDailySignDao::get($uid, self::$arrField);
		}		
		
		return $ret;
	}
	
	private static function insertDefault($uid)
	{
		//这里设置默认值为最后一个
		$arrField = array('uid'=>$uid, 
			'acc_sign_time' => 0, 
			'acc_fillsign_time' => 0,
			'used_fill_sign_times' => 0,
			'va_signInfo'=> array('sign'=>array(), 'reward'=>array(), 'oldsign'=>array(), 'oldreward'=>array()));
		
		RewardDailySignDao::insert($uid, $arrField);
	}
	
	public static function dailySign($uid)
	{
		$info = self::get_($uid);
		if (!in_array(Util::todayDate(), $info['va_signInfo']['sign'])) {
			array_push($info['va_signInfo']['sign'], Util::todayDate());
			$info['acc_sign_time'] = Util::getTime();
			RewardDailySignDao::update($uid, $info);
		}
	}
	
	public static function dailyFillSign($uid, $date)
	{
		$info = self::get_($uid);
		if (!in_array($date, $info['va_signInfo']['sign'])) {
			array_push($info['va_signInfo']['sign'], $date);
			$info['acc_fillsign_time'] = Util::getTime();
			$info['used_fill_sign_times']++;
			$user = EnUser::getUserObj();
			$user->subGold(50);
			$user->update();
			RewardDailySignDao::update($uid, $info);
		}
	}
	
	public static function dailySignReward($uid, $event, $step)
	{
		$arrayDailySignGainInfo = btstore_get()->REWARD_SIGNDAY;
		$curTime = Util::getTime();
		foreach ($arrayDailySignGainInfo as $key => $value)
		{
			if ($curTime >= $value['begin'] && $curTime <= $value['end']) {
				$eventId=$key;
				break;
			}			
		}		
		
		$info = self::get_($uid);
		switch ($event)
		{
			case 1:
				$info['va_signInfo']['oldreward'][$step]=$step;
				foreach ($arrayDailySignGainInfo[$eventId-1]['signmenuprize'] as $key => $val)
				{
					if ($step==$key)
					{
						$reward=$val;
						break;
					}
				}
				break;
			case 2:
				$info['va_signInfo']['reward'][$step]=$step;
				foreach ($arrayDailySignGainInfo[$eventId]['signmenuprize'] as $key => $val)
				{
					if ($step==$key)
					{
						$reward=$val;
						break;
					}
				}
				break;
		}
		$arrRet = RewardUtil::rewardById($uid, $reward);
		RewardDailySignDao::update($uid, $info);
		return $arrRet;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */