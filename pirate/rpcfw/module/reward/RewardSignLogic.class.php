<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: RewardSignLogic.class.php 17254 2012-03-24 06:23:49Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-26/module/reward/RewardSignLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-24 14:23:49 +0800 (Sat, 24 Mar 2012) $
 * @version $Revision: 17254 $
 * @brief 
 *  
 **/

class RewardSignLogic
{
	private static $arrField = array('uid', 'sign_time', 'step');
	
	const SECONDS_DAY = 86400;	
	
	private static function signTimeValidate()
	{
		$begin = btstore_get()->REWARD_SIGN['begin'];
		$end = btstore_get()->REWARD_SIGN['end'];
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
		//先判断一下是不是在活动的时间范围
		if (!self::signTimeValidate())
		{
			Logger::warning('fail to get sign info, out of date');
			//throw new Exception('fake');
			$arrRet['ret'] = 'out_of_date';
			return $arrRet;	
		}
		$arrRet['res'] = self::get_($uid);
		return $arrRet;		
	}
	
	private static function get_($uid)
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
		$arrField = array('uid'=>$uid, 'sign_time'=>Util::getTime()-86400, 'step' => RewardConf::SIGN_ACCUM);
		RewardSignDao::insert($uid, $arrField);
	}
	
	public static function sign($uid, $step)
	{
		$arrRet = array('ret'=>'ok', 'reward'=>array());
		
		//先判断一下是不是在活动的时间范围
		if (!self::signTimeValidate())
		{
			Logger::warning('fail to sign, out of date');
			//throw new Exception('fake');
			$arrRet['ret'] = 'out_of_date';
			return $arrRet;	
		}
		
		$info = self::get_($uid);
		//已经签过了		
		if (Util::isSameDay($info['sign_time']))
		{
			Logger::warning('fail to sign, user have singed already.');
			$arrRet['ret'] = 'signed';
			return $arrRet;			
		}
		
		//昨天签名的么
		if (Util::isSameDay($info['sign_time']-self::SECONDS_DAY))
		{
			$info['step'] += 1;
			//到最大了重新开始
			if ($info['step']==RewardConf::SIGN_ACCUM)
			{
				$info['step'] = 1;
			}
		}
		else //重置为0
		{
			$info['step'] = 0;
		}
		
		if ($step != $info['step'])
		{
			Logger::warning('fail to sign, step err.');
			$arrRet['ret'] = 'step';			
			return $arrRet;
		}	
			
		$info['sign_time'] = Util::getTime();
		//update
		RewardSignDao::update($uid, $info);
		
		//reward
		$rewardId = btstore_get()->REWARD_SIGN['reward_id'][$step];
		$arrRet['reward'] = RewardUtil::reward($uid, $rewardId);
		
		if (isset($arrRet['reward']['gold']))
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_REWARD_SIGN, 
				$arrRet['reward']['gold'], 
				Util::getTime(), false);
		}
		
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */