<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ActiveLogic.class.php 25582 2012-08-14 03:45:14Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/active/ActiveLogic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-08-14 11:45:14 +0800 (二, 2012-08-14) $
 * @version $Revision: 25582 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : ActiveLogic
 * Description : 活跃度实际逻辑实现类， 对内接口实现类
 * Inherit     :
 **********************************************************************************************************************/
class ActiveLogic
{

	/**
	 * 获取用户的活跃度信息
	 */
	public static function getActiveInfo()
	{
		// 获取活跃度信息
		$actInfo = MyActive::getInstance()->getActiveInfo();
		// 计算分数
		$actInfo['point'] = MyActive::calculateActivePoint($actInfo);
		// 返回
		return $actInfo;
	}

	/**
	 * 领取奖励
	 * 
	 * @param int $caseID						宝箱ID
	 */
	public static function fetchPrize($caseID)
	{
		// 背包信息，返回值
		$bagInfo = array();
		// 获取活跃度信息
		$actInfo = self::getActiveInfo();
		// 如果没有这个档位或者已经领取已有奖励或者所需分数大于已有分数
		if (!isset(ActiveDef::$CASE_INDEX[$caseID]) || 
		    empty(btstore_get()->ACTIVE_PRIZE[$caseID]) || 
		    btstore_get()->ACTIVE_PRIZE[$caseID]['point'] > $actInfo['point'] ||
		    ($actInfo['prized_num'] & ActiveDef::$CASE_INDEX[$caseID]))
		{
			// 防止连点，降低错误级别
			Logger::debug('Fetch prize case ID is %d, score is %d, prized_num is %d, can not fetch anymore.', 
			              $caseID, $actInfo['point'], $actInfo['prized_num']);
			return 'err';
		}

		// 获取用户背包信息
		$bag = BagManager::getInstance()->getBag();
		// 获取用户信息
		$user = EnUser::getUserObj();

		// 分发所有奖励
		// 先增加道具，如果背包满了，不做其他操作
		if (!empty(btstore_get()->ACTIVE_PRIZE[$caseID]['item_num']))
		{
			// 生成物品
			$itemIDs = ItemManager::getInstance()->addItem(btstore_get()->ACTIVE_PRIZE[$caseID]['item_id'], 
			                                               btstore_get()->ACTIVE_PRIZE[$caseID]['item_num']);
			// 记录发送的信息
			$msg = chatTemplate::prepareItem($itemIDs);
			// 直接增加到背包里，不使用临时背包
			if ($bag->addItems($itemIDs, FALSE) == FALSE)
			{
				Logger::warning('Bag full.');
				return 'err';
			}
			// 发送信息
			chatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
		}

		// 增加游戏币
		$user->addBelly(btstore_get()->ACTIVE_PRIZE[$caseID]['lv_belly'] * $user->getLevel());
		// 增加金币
		$user->addGold(btstore_get()->ACTIVE_PRIZE[$caseID]['gold']);
		// 增加阅历
		$user->addExperience(btstore_get()->ACTIVE_PRIZE[$caseID]['lv_experience'] * $user->getLevel());
		// 增加声望
		$user->addPrestige(btstore_get()->ACTIVE_PRIZE[$caseID]['prestige']);
		// 更新数据库
		$bagInfo = $bag->update();
		EnUser::getInstance()->update();

		// 发送金币通知
		if (btstore_get()->ACTIVE_PRIZE[$caseID]['gold'] != 0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_COPY_GETPRIZE,
			                 btstore_get()->ACTIVE_PRIZE[$caseID]['gold'], Util::getTime(), FALSE);
		}
		// 增加领取次数
		MyActive::getInstance()->addPirzedTimes($caseID);
		// 更新数据库
		MyActive::getInstance()->save();
		// 返回背包信息
		return $bagInfo;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */