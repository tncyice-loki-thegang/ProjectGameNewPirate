<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Reward.class.php 36940 2013-01-24 07:54:43Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/Reward.class.php $
 * @author $Author: lijinfeng $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-24 15:54:43 +0800 (四, 2013-01-24) $
 * @version $Revision: 36940 $
 * @brief 
 *  
 **/

class Reward implements IReward
{
	
	private $uid;
	
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	/* (non-PHPdoc)
	 * @see IReward::getSign()
	 */
	public function getSignInfo ()
	{
		$arrRet = RewardSignLogic::get($this->uid);
		if ($arrRet['ret']=='ok')
		{
			unset($arrRet['res']['uid']);
		}
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IReward::sign()
	 */
	public function sign ($step)
	{		
		return RewardSignLogic::sign($this->uid, $step);
	}
	
	/* (non-PHPdoc)
	 * @see IReward::getOnlineGift()
	 */
	public function getGiftInfo ()
	{
		$arrRet = RewardGiftLogic::getArr();
		$arrRet = array_values($arrRet);
		
		foreach ($arrRet as &$ret)
		{
			unset($ret['uid']);
			unset($ret['end_time']);
			unset($ret['status']);
		}
		return $arrRet;
	}
	
	/* (non-PHPdoc)
	 * @see IReward::getGift()
	 */
	public function getGift ($id, $step)
	{
		return RewardGiftLogic::getRewardGift($id, $step);		
	}

	/* (non-PHPdoc)
	 * @see IReward::getGiftByCode()
	 */
	public function getGiftByCode ($code)
	{
		// return GiftCodeLogic::getGiftByCode($this->uid, $code);
		// $ret = array('ret'=>'ok', 'reward'=>array('belly' => 999999, 'gold' => 10000, ), 'grid', 'info'=>'新手礼包码');
		// $arr = explode('*', $code);		
		// $ids = ItemManager::getInstance()->addItem($arr[0], $arr[1]);
		// MailTemplate::sendGiftItem($this->uid, '', $ids, true);
		// ItemManager::getInstance()->update();
		// $ret = array('ret'=>'ok', 'reward'=>array('belly' => 0, 'gold' => 0), 'grid', 'info'=>'');
		// return $ret;

		
		 if ($code == 'halloween')
		{
			$arrField = array('count', 'time');
			$info = GiftCodeDao::get($this->uid, $arrField);
			if (empty($info))
			{
				$field = array('uid'=>$this->uid, 
				'count' => 0, 
				'time' => Util::getTime());
				GiftCodeDao::insert($this->uid, $field);
				$info = GiftCodeDao::get($this->uid, $arrField);
			}
			// logger::warning($info);
			if ($info['count']==0)
			{
				$info['count']++;
				$info['time'] = Util::getTime();
				GiftCodeDao::update($this->uid, $info);
				// 万圣节时装，1万金币，500x2万贝利
				$ids = ItemManager::getInstance()->addItems(array(70753=>1, 70014=>5, 70307=>2));
				MailTemplate::sendGiftItem($this->uid, '疯狂万圣节礼物', $ids, true);
				ItemManager::getInstance()->update();
				$ret = array('ret'=>'ok', 'reward'=>array('belly' => 0, 'gold' => 0), 'grid', 'info'=>'');
				return $ret;
			}
		}
	}

	/* (non-PHPdoc)
	 * @see IReward::getRewardGoldInfo()
	 */
	public function getRewardGoldInfo ()
	{
		if (isset(GameConf::$CLOSE_ADD_GOLD))
		{
			if (GameConf::$CLOSE_ADD_GOLD)
			{
				$arrRet['ret'] = 'over';
				$arrRet['res'] = array();
				return $arrRet;
			}
		}
		
		$arrRet = RewardGoldLogic::get($this->uid);
		if ($arrRet['ret']=='ok')
		{
			unset($arrRet['res']['uid']);
		}
		return $arrRet;		
	}

	/* (non-PHPdoc)
	 * @see IReward::getRewardGold()
	 */
	public function getRewardGold ()
	{
		if (isset(GameConf::$CLOSE_ADD_GOLD))
		{
			if (GameConf::$CLOSE_ADD_GOLD)
			{
				throw new Exception('fake');
			}
		}
		return RewardGoldLogic::rewardGold($this->uid);	
	}

	/* (non-PHPdoc)
	 * @see IReward::signUpgrade()
	 */
	public function signUpgrade ()
	{
		return RewardSignLogic::signUpgrade($this->uid);
	}
	
	public function getLastSignTime($uid)
	{
		$ret = RewardSignDao::get($uid, array('sign_time'));
		if (empty($ret))
		{
			return 0;
		}
		return $ret['sign_time'];
	}
	
	public function isSignToday($uid)
	{
		$ret = RewardSignDao::get($uid, array('sign_time'));
		if (empty($ret))
		{
			return false;
		}
		
		if (Util::isSameDay($ret['sign_time']))
		{
			return true;
		}
		return false;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see IReward#getSprFestWelfareInfo()
	 */
	public function getSprFestWelfareInfo()
	{
		$ret = SpringFestivalWelfareLogic::getInfo($this->uid);
		
		unset($ret['uid']);
		unset($ret['day_time']);
		
		return $ret;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see IReward#recieveSprFestWelfare($gift_index)
	 */
	public function recieveSprFestWelfare($gift_index)
	{
		$ret = SpringFestivalWelfareLogic::recieveSprFestWelfare($this->uid,$gift_index);
		return $ret;
	}
		
	public function getDailySignInfo()
	{
		return RewardDailySignLogic::get($this->uid);
	}

	public function dailySign()
	{
		RewardDailySignLogic::dailySign($this->uid);
		return'ok';
	}
	
	public function dailyFillSign($date)
	{
		RewardDailySignLogic::dailyFillSign($this->uid, $date);
		return 'ok';
	}
	
	public function dailySignReward($event, $step)
	{
		$arrRet = array_merge(array('ret'=>'ok'), RewardDailySignLogic::dailySignReward($this->uid, $event, $step));
		return $arrRet;
	}
	
	public function getHolidaysInfo()
	{
		$arrRet = RewardHolidaysLogic::getHolidaysInfo();
		unset($arrRet['uid']);
		unset($arrRet['id']);
		unset($arrRet['end_time']);			
		return $arrRet;
	}
	
	public function holidaysReward($step)
	{
		return RewardHolidaysLogic::holidaysReward($this->uid, $step);
	}
	
	public function allHolidaysReward()
	{
		return RewardHolidaysLogic::allHolidaysReward($this->uid);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */