<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: RewardGiftLogic.class.php 17402 2012-03-27 03:45:30Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/rewardGift/RewardGiftLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-27 11:45:30 +0800 (二, 2012-03-27) $
 * @version $Revision: 17402 $
 * @brief 
 *  
 **/

class RewardGiftLogic
{
	private static $arrField = array(
	'uid',
	'id',
	'step',
	'status',	
	'begin_time',
	'end_time',
	'accumulate_time',
	);
	
	/**
	 * 得到有效的礼包id。 根据时间检查
	 * Enter description here ...
	 */
	public static function getValidGiftId()
	{
		$arr = array();
		foreach (btstore_get()->REWARD_GIFT as $id=>$arrGift)
		{
			$curTime = Util::getTime();
			if ( $curTime > $arrGift['begin'] && $curTime < $arrGift['end'])
			{
				$arr[] = $id;
			}
		}
		return $arr;
	}
	
	public static function getArr()
	{
		$uid = RPCContext::getInstance()->getUid();
		$arrId = self::getValidGiftId();
		//都过期了
		if (empty($arrId))
		{
			return array();
		}
		
		$arrGift = RPCContext::getInstance()->getSession('reward.gift');
		
		if ($arrGift!==null)
		{
			//把过期的过滤掉
			foreach($arrGift as $id => $gift)
			{
				if (!in_array($id, $arrId))
				{
					unset($arrGift[$id]);
					//极少数的情况会进这个if分支
					RPCContext::getInstance()->setSession('reward.gift', $arrGift);
				}
			}
			return $arrGift;
		}

		$arrGift = RewardGiftDao::getArr($uid, $arrId, self::$arrField);
		foreach ($arrId as $id)
		{
			if (!isset($arrGift[$id]))
			{
				$arrGift[$id] = self::insertDefault($uid, $id);
			}

			if ($arrGift[$id]['status']==RewardGiftStatus::FINISH)
			{
				unset($arrGift[$id]);
				continue;
			}

			//都不等于0,数据有问题。可能是服务器挂掉后， 直接修改数据库end_time数据。这里修复数据
			if ($arrGift[$id]['begin_time']!=0 && $arrGift[$id]['end_time']!=0)
			{
				$arrGift[$id]['accumulate_time'] += ($arrGift[$id]['end_time'] - $arrGift[$id]['begin_time']);
				//重新开始计时
				$arrGift[$id]['begin_time'] = Util::getTime();
				$arrGift[$id]['end_time'] = 0;
				RewardGiftDao::update($uid, $id, $arrGift[$id]);
			}
			//都为0, 用户正常下线
			else if ($arrGift[$id]['begin_time']==0 && $arrGift[$id]['end_time']==0)
			{
				//重新开始计时
				$arrGift[$id]['begin_time'] = Util::getTime();
				RewardGiftDao::update($uid, $id, $arrGift[$id]);
			}
			else
			{
				//nothing
				//begin_time !=0, end_time==0, 可能为session被清空了,或者是刚刚插入数据库的
				//begin_time ==0, end_time!=0， 没有这情况。如果发生了，也没有悲剧。用户领取第一个礼包，或者下线都能修复数据
			}
		}
		RPCContext::getInstance()->setSession('reward.gift', $arrGift);
		return $arrGift;
	}

	public static function getRewardGift($id, $step)
	{
		$arrRet = array('ret'=>'ok', 'reward'=>array());
		
		$arrGift = self::getArr();
		if (!isset($arrGift[$id]))
		{
			Logger::warning('fail to get gift %d info', $id);
			$arrRet['ret'] = 'id_err';
			return $arrRet;
		}

		$gift = $arrGift[$id];
		//检查位置
		if ($gift['step']!=$step)
		{
			Logger::warning('fail to get gift, step err.');
			//throw new Exception('fake');
			$arrRet['ret'] = 'step';
			return $arrRet;
		}

		$curTime = Util::getTime();
		//计算时间
		$gift['accumulate_time'] += ($curTime - $gift['begin_time']);		

		//检查累计时间
		if (!isset(btstore_get()->REWARD_GIFT[$id]['gift'][$step]))
		{
			Logger::warning('fail to get gift %d step %d, config is not exist.', $id, $step);
			throw new Exception('fake');
		}
		
		$cfg = btstore_get()->REWARD_GIFT[$id]['gift'][$step];
		if ($gift['accumulate_time'] < $cfg['time'])
		{
			Logger::warning('fail to get gift %d step %d, accumulate_time %d is not enough.',
							$id, $step, $gift['accumulate_time']);
			throw new Exception('fake');
		}
		
		//更新数据
		$gift['accumulate_time'] = 0;
		$gift['end_time'] = 0;
		$gift['begin_time'] = Util::getTime();				
		//如果已经是最大值了设置为已经完成
		if (!isset(btstore_get()->REWARD_GIFT[$id]['gift'][$step+1]))
		{
			$gift['status'] = RewardGiftStatus::FINISH;
			unset($arrGift[$id]); 
		}
		else
		{
			$gift['step'] += 1;
			$arrGift[$id] = $gift;
		}
		
		//发奖
		$rewardId = $cfg['reward_id'];
		$arrRet['reward']  = RewardUtil::rewardById($gift['uid'], $rewardId);		
		
		//update session
		RPCContext::getInstance()->setSession('reward.gift', $arrGift);		
		//update db				
		RewardGiftDao::update($gift['uid'], $id, $gift);				
		
		if (isset($arrRet['reward']['gold']))
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_REWARD_ONLINE_GIFT,
				$arrRet['reward']['gold'],
				Util::getTime(), false);
		}
		
		return $arrRet;			
	}
	
	//用户下线
	public static function logoff()
	{
		$arrGift = self::getArr();
		foreach ($arrGift as $id=>$gift)
		{
			$gift['end_time'] = Util::getTime();
			$gift['accumulate_time'] += ($gift['end_time'] - $gift['begin_time']);
			$gift['end_time'] = $gift['begin_time'] = 0;
			RewardGiftDao::update($gift['uid'], $gift['id'], $gift);
		}
	}
	
	//用户登录
	public static function login()
	{
		self::getArr();
	}

	private static function insertDefault($uid, $id)
	{
		$arrField = array(
		'uid'=>$uid,
		'id'=>$id,
		'step'=>1,
		'status'=>RewardGiftStatus::UNFINISH,
		'begin_time'=>Util::getTime(),
		'end_time'=>0,
		'accumulate_time'=>0,
		);
		RewardGiftDao::insert($uid, $id, $arrField);
		return $arrField;
	}
	
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */