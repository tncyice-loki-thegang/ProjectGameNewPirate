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

class RewardSignLogic
{
	private static $arrField = array('uid', 'sign_time', 'step', 'id', 'upgrade_time');
	
	const SECONDS_DAY = 86400;	
	
	private static function signTimeValidate($id)
	{
		$begin = btstore_get()->REWARD_SIGN[$id]['begin'];
		$end = btstore_get()->REWARD_SIGN[$id]['end'];
		$curTime = Util::getTime();
		if ($curTime >= $begin && $curTime <= $end)
		{
			return true;
		}
		return false;
	}
	
	public static function get($uid)
	{
		$arrRet = array('ret'=>'ok', 'res'=>array());
		$arrRet['res'] = self::get_($uid);		
		
		//先判断一下是不是在活动的时间范围
		if (!self::signTimeValidate($arrRet['res']['id']))
		{
			Logger::warning('fail to get sign info, out of date');
			//throw new Exception('fake');
			$arrRet['ret'] = 'out_of_date';
			return $arrRet;	
		}
		
		return $arrRet;		
	}
	
	public static function get_($uid)
	{				
		$ret = RewardSignDao::get($uid, self::$arrField);
		if (empty($ret))
		{
			self::insertDefault($uid);
			$ret = RewardSignDao::get($uid, self::$arrField);
		}		
		
		return $ret;
	}
	
	private static function insertDefault($uid)
	{
		//这里设置默认值为最后一个
		$totalStep = self::getTotalStep();
		$arrField = array('uid'=>$uid, 
			'sign_time'=>Util::getTime()-self::SECONDS_DAY, 
			'step' => $totalStep,
			'id' => key(btstore_get()->REWARD_SIGN->toArray()),
			'upgrade_time'=>0);
		
		RewardSignDao::insert($uid, $arrField);
	}
	
	public static function sign($uid, $step)
	{
		$arrRet = array('ret'=>'ok', 'reward'=>array());
		
		$info = self::get_($uid);
		//先判断一下是不是在活动的时间范围
		if (!self::signTimeValidate($info['id']))
		{
			Logger::warning('fail to sign, out of date');
			//throw new Exception('fake');
			$arrRet['ret'] = 'out_of_date';
			return $arrRet;	
		}
		
		//已经签过了		
		if (Util::isSameDay($info['sign_time']))
		{
			Logger::warning('fail to sign, user have singed already.');
			$arrRet['ret'] = 'signed';
			return $arrRet;			
		}
		
		//昨天签名的么
		if (Util::isSameDay($info['sign_time']+self::SECONDS_DAY))
		{
			$info['step'] += 1;
			//到最大了重新开始
			if ($info['step']==self::getTotalStep()+1)
			{
				$info['step'] = 1;
			}
		}
		else //重置为1
		{
			$info['step'] = 1;
		}
		
		if ($step != $info['step'])
		{
			Logger::warning('fail to sign, step err.');
			$arrRet['ret'] = 'step';			
			return $arrRet;
		}	
			
		$info['sign_time'] = Util::getTime();		
		
		//reward
		$rewardId = btstore_get()->REWARD_SIGN[$info['id']]['reward_id'][$step];
		$arrRet['reward'] = RewardUtil::rewardById($uid, $rewardId);
		
		//update
		RewardSignDao::update($uid, $info);
		
		if (isset($arrRet['reward']['gold']))
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_REWARD_SIGN, 
				$arrRet['reward']['gold'], 
				Util::getTime(), false);
		}
		
		return $arrRet;
	}
	
	private static function getTotalStep()
	{
		$first = current(btstore_get()->REWARD_SIGN->toArray());		
		return count($first['reward_id']);
	}
	
	public static function signUpgrade($uid)
	{
		$info = self::get_($uid);
		$id = $info['id']+1;
		if (!isset(btstore_get()->REWARD_SIGN[$id]))
		{
			Logger::warning('fail to upgrade, the next id is not exist');
			throw new Exception('fake');
		}
				
		$level = btstore_get()->REWARD_SIGN[$id]['level'];
		if (EnUser::getUserObj($uid)->getMasterHeroLevel() < $level)
		{
			Logger::warning('fail to upgrade, level is not enough');
			throw new Exception('fake');
		}
		
		//升级当天必须先签到
		if (!Util::isSameDay($info['sign_time']))
		{
			Logger::warning('fail to upgrade, sign first');
			throw new Exception('fake');
		}
		
		
		$arrUpdate = array('id'=>$id, 'upgrade_time'=>Util::getTime(), 
			'sign_time'=>Util::getTime()-self::SECONDS_DAY, 'step'=>self::getTotalStep());
		RewardSignDao::update($uid, $arrUpdate);		
		$arrRet = array('ret'=>'ok', 'res'=>$arrUpdate);
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */