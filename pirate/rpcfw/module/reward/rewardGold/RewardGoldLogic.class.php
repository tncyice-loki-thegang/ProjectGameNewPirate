<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: RewardGoldLogic.class.php 30714 2012-10-31 12:14:52Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/rewardGold/RewardGoldLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-10-31 20:14:52 +0800 (三, 2012-10-31) $
 * @version $Revision: 30714 $
 * @brief 
 *  
 **/

class RewardGoldLogic
{
	private static $arrField = array('uid', 'reward_time', 'step');
	
	const SECONDS_DAY = 86400;
	
	public static function get($uid)
	{
		$arrRet = array('ret'=>'ok', 'res'=>array());
		$arrRet['res'] = self::get_($uid);
		if ($arrRet['res']['step'] >= count(RewardGoldConf::$REWARD_GOLD_NUM))
		{
			$arrRet['ret'] = 'over';
		}
		return $arrRet;		
	}
	
	private static function get_($uid)
	{				
		$ret = RewardGoldDao::get($uid, self::$arrField);
		if (empty($ret))
		{
			self::insertDefault($uid);
			$ret = RewardGoldDao::get($uid, self::$arrField);
		}		
		
		return $ret;
	}
	
	private static function insertDefault($uid)
	{
		$arrField = array('uid'=>$uid, 'reward_time'=>0, 'step' => 0);
		RewardGoldDao::insert($uid, $arrField);
	}
	
	private static function createOrderId()
	{
		return 'TEST_01_' . strftime("%Y%m%d%H%M%S", Util::getTime()) .rand(100000000, 999999999);	
	}
	
	public static function rewardGold($uid)
	{
		$arrRet = array('ret'=>'ok', 'res'=>array());
		
		$info = self::get_($uid);
		
		if ($info['step'] >= count(RewardGoldConf::$REWARD_GOLD_NUM))
		{
			Logger::warning('fail to reward, step err.');
			$arrRet['ret'] = 'step';			
			return $arrRet;
		}
		
		//已经领过了		
		if (Util::isSameDay($info['reward_time']))
		{
			Logger::warning('fail to reward, user have rewarded already.');
			$arrRet['ret'] = 'rewarded';
			return $arrRet;			
		}		
		
		$addGold = RewardGoldConf::$REWARD_GOLD_NUM[$info['step']];		
		$orderId = self::createOrderId();
		$qid = RPCContext::getInstance()->getSession('global.qid');
		$userObj = EnUser::getUserObj($uid);
		$level = $userObj->getLevel();
		$newVip = User4BBpayDao::update($uid, $orderId, $addGold, 0, $qid, OrderType::ONLINE_REWARD_GOLD, $level);

		$oldVip = $userObj->getVip();
		//修改对象中的值			
		$userObj->modifyFields(array('gold_num'=>$addGold, 'vip'=>$newVip - $oldVip));
		//修改缓存中的值
		UserSession::saveSession('user.user', $userObj->getUserInfo());		
		$chargeGold = UserLogic::getSumGoldByUid($uid);
		
		$arrRet['res'] = array('gold_num'=>$userObj->getGold(), 
			'vip'=>$userObj->getVip(), 
			'charge_gold'=>$chargeGold);				
		
		$info['reward_time'] = Util::getTime();
		$info['step'] += 1;
		//update,最后更新送金币信息
		RewardGoldDao::update($uid, $info);
		
		//发公告
		if (($newVip -$oldVip) > 0 )
		{
			ChatTemplate::sendSysVipLevelUp1($userObj->getTemplateUserInfo(), $newVip);
			ChatTemplate::sendBroadcastVipLevelUp2($userObj->getTemplateUserInfo(), $newVip);	
			MailTemplate::sendVipperUpMsg(intval($uid), $newVip);
		}		
		
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */