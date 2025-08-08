<?php

class ExchangeShopLogic
{
	
	public static function exchangShopInfo()
	{
		logger::warning('test');
		$info = MyExchangeShop::getInstance()->getInfo();
		$tmp = $info ['va_exchange_item_info'];
		unset($info['va_exchange_item_info']);
		unset($info['status']);
		$info['exiteminfo'] = $tmp;
		return $info;
	}

	public static function exItem($uid, $type, $exItemId, $num)
	{
		if(EMPTY($exItemId))
		{
			Logger::fatal('Err para:exItemId is null.');
			throw new Exception('fake');
		}
		
		switch ($type)
		{
			case 'elementshop':
				$mShopAry = btstore_get()->ELEMENT_EXCHANGE->toArray();				
				$score = self::subElementScore($uid, $mShopAry[$exItemId]['needScore']);				
				break;
			case 'cardshop':
				break;
			case 'worldtree':
				break;
			case 'starshop':
				$mShopAry = btstore_get()->BLOOD_EXCHANGE->toArray();
				break;
			case 'worldboatshop':
				break;
			case 'bullfightarenashop':
				break;
			case 'scratchcardshop':
				break;
		}
		
		if(!isset($mShopAry[$exItemId]))
		{
			Logger::fatal('Err para:exItemId is not exist in conf.');
			throw new Exception('fake');
		}
		$exItemId = intval($exItemId);
		$num = intval($num);
		// 取得该用户荣誉信息
		// $info = MyExchangeShop::getInstance()->getInfo();
		// 减积分
		// self::subExPoint($info, $exItemId, $num);
		// 给装备
		// $ret = self::exItem($uid, $exItemId, $num);
		$bag = BagManager::getInstance()->getBag();
		$itemTempLateId = $mShopAry[$exItemId]['itemTempId'];
		$bag->addItembyTemplateID($mShopAry[$exItemId]['itemTempId'], $mShopAry[$exItemId]['getNum']*$num);
		$ret = array('bagInfo' => $bag->update(), 'resource' => $score);
		// logger::warning($ret);
		return $ret;
	}
	
	private static function subElementScore($uid, $needScore)
	{
		$info = ElementSysDao::get($uid, array('element_score'));
		$info['element_score'] -= $needScore;
		ElementSysDao::update($uid, $info);		
		return $info['element_score'];
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
	// private static function exItem($uid, $exItemId, $num)
	// {
		// $mHonourShopAry = btstore_get()->HONOURSHOP->toArray();
		// $itemTempLateId = $mHonourShopAry[$exItemId]['itemTempLateId'];
		// $itemTempLateNum = $mHonourShopAry[$exItemId]['itemTempLateNum'];
		// $itemIdAry = ItemManager::getInstance()->addItem($itemTempLateId, $itemTempLateNum*$num);
		// if(EMPTY($itemIdAry))
		// {
			// return;
		// }
		// $userObj = EnUser::getUserObj();
		// if($itemTempLateId != 60014)
		// {
			// ChatTemplate::sendHonourExItem($userObj->getTemplateUserInfo(), $itemIdAry[0]);
		// }
		
		// $bagObj = BagManager::getInstance()->getBag();
		// if($bagObj->addItem($itemIdAry[0], FALSE) == FALSE)
		// {
			// return 'noBag';
		// }
		// MyHonourShop::getInstance()->save();
		// $bagInfo = $bagObj->update();
		// Logger::debug('bagInfo %s.', $bagInfo);
		// return $bagInfo;
	// }

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