<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GiftCodeLogic.class.php 31658 2012-11-22 11:51:19Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/giftCode/GiftCodeLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-11-22 19:51:19 +0800 (四, 2012-11-22) $
 * @version $Revision: 31658 $
 * @brief 
 *  
 **/




class GiftCodeLogic
{	
	private static function isSignRight4LY($qid, $code)
	{
		//没有合服配置
		if (!isset(GameConf::$MERGE_LY_SERVER_ID))
		{
			$md5str = $qid . GameConf::LY_SERVER_ID . GameConf::LY_KEY;
			$sign = strtoupper(md5($md5str));
			if ($sign==$code)
			{
				return true;
			}
			return false;
		}
		
		//遍历合服server_id
		foreach (GameConf::$MERGE_LY_SERVER_ID as $serverId)
		{
			$md5str = $qid . $serverId . GameConf::LY_KEY;
			$sign = strtoupper(md5($md5str));
			if ($sign == $code)
			{
				return true;
			}
		}
		return false;
	}
	
	public static function getGiftByCode($uid, $code)
	{
		$arrRet = array('ret'=>'ok', 'reward'=>array(), 'gifts'=>'');
		$arrGift = array();
		
		//给联运平台计算是不是新手卡
		$qid = RPCContext::getInstance()->getSession('global.qid'); 
		if ($qid!=null)
		{
			//是新手卡
			if (self::isSignRight4LY($qid, $code))
			{
				$user = EnUser::getUserObj($uid);
				if ($user->isUseBeginnerCard())
				{
					Logger::warning('the beginner card is used');
					$arrRet['ret'] = 1;
					return $arrRet;
				}
				$user->setBeginnerCard();
				$arrGift['ret'] = GameConf::$LY_BEGINNER_REWARD;
				$arrGift['info'] = '新手卡';
			}
		}
		
		//不是新手卡
		if (empty($arrGift))
		{
			$platform = ApiManager::getApi();		
			$user = EnUser::getUserObj($uid);
			$argv = array('pid'=>$user->getPid(), 
				'group'=>RPCContext::getInstance ()->getFramework ()->getGroup (),
				'serverKey'=>Util::getServerId(), 
				'uid'=>$uid, 
				'code'=>$code );
			$arrGift = $platform->users('getGiftByCard', $argv);
		}		
				
		if (isset($arrGift['error']))
		{
			$arrRet['ret'] = $arrGift['error'];
			Logger::warning('fail to getGiftByCode, platform return error %s, code number:%s', $arrGift['error'], $code);
			return $arrRet;	
		}
		Logger::debug('platform return gift:%s', $arrGift);
		
		$arrReward = array();
		$arrItemIds = array();
		foreach ($arrGift['ret'] as $gift)
		{
			if ($gift['item_type']==RewardType::ITEM)
			{				
				$ids = ItemManager::getInstance()->addItem($gift['item_id'], $gift['item_num']);
				$arrItemIds = array_merge($arrItemIds, $ids);
			}
			else
			{
				$arrReward[] = array('type'=>$gift['item_type'], 'value'=>$gift['item_num']);
			}
		}				
				
		$arrRet['reward'] = RewardUtil::reward($uid, $arrReward);
		$arrRet['info'] = $arrGift['info'];
		
		//有物品
		if (!empty($arrItemIds))
		{
			ItemManager::getInstance()->update();
			Logger::debug('send item %s by mail', $arrItemIds);
			// 用邮件发物品
			MailTemplate::sendGiftItem($uid, $arrRet['info'], $arrItemIds, true);
			$arrRet['reward']['grid'] = 1;
		}
		
		if (isset($arrRet['reward']['gold']))
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_REWARD_GIFT_CODE,
				$arrRet['reward']['gold'],
				Util::getTime(), false);
		}
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */