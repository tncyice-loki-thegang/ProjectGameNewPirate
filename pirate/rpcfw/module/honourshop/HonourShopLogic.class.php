<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: HonourShopLogic.class.php 36559 2013-01-21 12:28:10Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/honourshop/HonourShopLogic.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-21 20:28:10 +0800 (一, 2013-01-21) $
 * @version $Revision: 36559 $
 * @brief 
 *  
 **/
class HonourShopLogic
{
	/**
	 * 
	 * 获取用户信息
	 * @param int $uid						用户UID
	 * @return array 'honourpoint'			荣誉积分
	 */
	public static function honourInfo($uid)
	{
		Logger::debug('HonourShop::honourInfo start.');
		// 取得该用户荣誉信息
		$honourInfo = MyHonourShop::getInstance()->getHonourInfo();
		
		Logger::debug('HonourShop::honourInfo end.');
		return array('honourpoint' => $honourInfo['honour_point'],
					 'exiteminfo' => $honourInfo['va_exchange_item_info']['iteminfo']);
	}

	/**
	 * 
	 * 兑换荣誉装备
	 * @param int $uid						用户UID
	 * @param int $exItemId					兑换物品模板id
	 * @return array 'ret'					结果信息
	 * 				 'items'				背包信息
	 */
	public static function exItemByHonour($uid, $exItemId, $num)
	{
		Logger::debug('HonourShop::exItemByHonour start.');
		if(EMPTY($exItemId))
		{
			Logger::fatal('Err para:exItemId is null.');
			throw new Exception('fake');
		}
		$mHonourShopAry = btstore_get()->HONOURSHOP->toArray();
		if(!isset($mHonourShopAry[$exItemId]))
		{
			Logger::fatal('Err para:exItemId is not exist in conf.');
			throw new Exception('fake');
		}
		$exItemId = intval($exItemId);
		$num = intval($num);
		// 取得该用户荣誉信息
		$honourInfo = MyHonourShop::getInstance()->getHonourInfo();
		// 减积分
		$ret = self::subExPoint($honourInfo, $exItemId, $num);
		if($ret != 'ok')
		{
			return array('ret' => $ret);
		}
		// 给装备
		$ret = self::exItem($uid, $exItemId, $num);
		Logger::debug('HonourShop::exItemByHonour end.');
		return array('ret' => 'ok',
					 'items' => $ret);
	}
	private static function subExPoint($honourInfo, $exItemId, $num)
	{
		$mHonourShopAry = btstore_get()->HONOURSHOP->toArray();
		$honourItemInfo = $mHonourShopAry[$exItemId];
		Logger::debug('the honourItemInfo is %s.', $honourItemInfo);
		
		// 用户荣誉积分检查
		$userHonourPoint = $honourInfo['honour_point'];
		Logger::debug('the honour point of user is %s.', $userHonourPoint);
		if($userHonourPoint < $honourItemInfo['consumeHonour']*$num)
		{
			Logger::debug('the point of user is not enough. point %s.', $userHonourPoint);
			return 'noPoint';
		}
		// 用户声望检查
		$userObj = EnUser::getUserObj();
		$userPrestige = $userObj->getPrestige();
		if($userPrestige < $honourItemInfo['needPrestige']*$num)
		{
			return 'noPrestige';
		}
		// 用户等级检查
		$userLevel = $userObj->getLevel();
		if($userLevel < $honourItemInfo['needLevel'])
		{
			return 'noLevel';
		}
		// 用户物品兑换次数检查
		if(!EMPTY($honourInfo['va_exchange_item_info']['iteminfo'][$exItemId]))
		{
			$exTimes = $honourInfo['va_exchange_item_info']['iteminfo'][$exItemId];
			if($exTimes >= $honourItemInfo['exItemTimes'] && $honourItemInfo['exItemTimes'] != 0)
			{
				return 'noExTimes';
			}
		}
		// 减积分
		MyHonourShop::getInstance()->mondifyHonourPoint($honourItemInfo['consumeHonour']*$num, FALSE);
		// 更新装备信息
		MyHonourShop::getInstance()->mondifyItemInfo($exItemId, $num);
		return 'ok';
	}
	private static function exItem($uid, $exItemId, $num)
	{
		$mHonourShopAry = btstore_get()->HONOURSHOP->toArray();
		$itemTempLateId = $mHonourShopAry[$exItemId]['itemTempLateId'];
		$itemTempLateNum = $mHonourShopAry[$exItemId]['itemTempLateNum'];
		$itemIdAry = ItemManager::getInstance()->addItem($itemTempLateId, $itemTempLateNum*$num);
		if(EMPTY($itemIdAry))
		{
			return;
		}
		$userObj = EnUser::getUserObj();
		if($itemTempLateId != 60014)
		{
			ChatTemplate::sendHonourExItem($userObj->getTemplateUserInfo(), $itemIdAry[0]);
		}
		
		$bagObj = BagManager::getInstance()->getBag();
		if($bagObj->addItem($itemIdAry[0], FALSE) == FALSE)
		{
			return 'noBag';
		}
		MyHonourShop::getInstance()->save();
		$bagInfo = $bagObj->update();
		Logger::debug('bagInfo %s.', $bagInfo);
		return $bagInfo;
	}

	/**
	 * 
	 * 增加荣誉积分
	 * @param int $uid						用户UID
	 * @return int $honourPoint				荣誉积分
	 */
	public static function addHonourPoint($uid, $honourPoint)
	{
		Logger::debug('HonourShop::addHonourPoint start.');
		$point = intval($honourPoint);
		if($point <= 0)
		{
			return 'pointiszero';
		}
		if(EMPTY($uid))
		{
			return 'noUser';
		}
	    Logger::debug('HonourShop::addHonourPoint end.');
		return self::modifyHonourPoint($uid, $honourPoint);
	}
	public static function modifyHonourPoint($uid, $honourPoint)
	{
		// 增加积分
		$honourInfo = HonourShopDao::getHonourInfo($uid);
	    if ($honourInfo === false)
	    {
	        // 初始化人信息
	        $honourInfo = HonourShopDao::addNewHonourInfo($uid, $honourPoint);
	    }
	    else 
	    {
	    	$honourInfo['honour_point'] = $honourInfo['honour_point'] + $honourPoint;
	        HonourShopDao::updateHonourInfo($uid, $honourInfo);
	    }
	    // 荣誉积分检测
	    self::chectPoint($uid, $honourInfo, $honourPoint);
	    return 'ok';
	}
	private static function chectPoint($uid, $honourInfo, $honourPoint)
	{
	    if (!Util::isSameDay($honourInfo['add_honour_point_time']))
	    {
	        $honourInfo['daily_honour_point'] = $honourPoint;
	    }
	    else 
	    {
	        $honourInfo['daily_honour_point'] += $honourPoint;
	    }
	    $honourInfo['add_honour_point_time'] = Util::getTime();
	    HonourShopDao::updateHonourInfo($uid, $honourInfo);
	    if($honourInfo['daily_honour_point'] >= 1500)
	    {
	        Logger::warning('the honour point of user is too much, please check the user. uid:[%s]', $uid);
	    }
	    RPCContext::getInstance()->setSession('honourshop.list', $honourInfo);
	}
        
	/**
	 * 
	 * 获取当前用户荣誉积分
	 * @param int $uid						用户UID
	 * @return int $honourPoint				荣誉积分
	 */
	public static function getUserHonourPoint()
	{
		Logger::debug('HonourShop::getUserHonourPoint start.');
		$userInfo = MyHonourShop::getInstance()->getHonourInfo();
		Logger::debug('HonourShop::getUserHonourPoint end.');
		return $userInfo['honour_point'];
	}
	
	/**
	 * 
	 * 增加荣誉积分(异步执行,扔到用户线程上执行)
	 * @param int $uid						用户UID
	 * @return int $honourPoint				荣誉积分
	 */
	public static function addFinallyHonourPoint($uid, $honourPoint)
	{
		Logger::debug('HonourShop::addHonourPoint start.');
		$point = intval($honourPoint);
		if($point <= 0)
		{
			return 'pointiszero';
		}
		if(EMPTY($uid))
		{
			return 'noUser';
		}
		Logger::info('the user %s get honour point is %d.', $uid, $honourPoint);
		RPCContext::getInstance()->executeTask($uid, 
		                                       'honourshop.modifyHonourPoint',
		                                       array($uid, $honourPoint));	
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */