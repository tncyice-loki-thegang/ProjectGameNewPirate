<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : Kitchen
 * Description : 厨房对外接口实现类
 * Inherit     : IKitchen
 **********************************************************************************************************************/
class Kitchen implements IKitchen
{

	/* (non-PHPdoc)
	 * @see IKitchen::getUserKitchenInfo()
	 */
	public function getUserKitchenInfo($uid = 0) 
	{
		Logger::debug('Kitchen::getUserKitchenInfo Start.');
		// 获取用户厨房信息
		$ret = KitchenLogic::getUserKitchenInfo($uid);

		Logger::debug('Kitchen::getUserKitchenInfo End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IKitchen::getUserOrderInfo()
	 */
	public function getUserOrderInfo($uid) 
	{
		Logger::debug('Kitchen::getUserOrderInfo Start.');
		// 获取用户订单信息
		$ret = KitchenLogic::getUserOrderInfo($uid);

		Logger::debug('Kitchen::getUserOrderInfo End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IKitchen::cook()
	 */
	public function cook($dishID, $isCritical) 
	{
		// 检查参数
		if ($dishID <= 0)
		{
			Logger::fatal('Err para, %d!', $dishID);
			throw new Exception('fake');
		}
		Logger::debug('Kitchen::cook Start.');
		// 做饭
		$ret = KitchenLogic::cook($dishID, $isCritical);

		Logger::debug('Kitchen::cook End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IKitchen::goldCook()
	 */
	public function goldCook($dishID, $isCritical) 
	{
		// 检查参数
		if ($dishID <= 0)
		{
			Logger::fatal('Err para, %d!', $dishID);
			throw new Exception('fake');
		}
		Logger::debug('Kitchen::goldCook Start.');
		// 花钱做饭
		$ret = KitchenLogic::goldCook($dishID, $isCritical);

		Logger::debug('Kitchen::goldCook End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IKitchen::goldCookByTimes()
	 */
	public function goldCookByTimes($dishID, $isCritical, $times) 
	{
		// 检查参数
		if ($dishID <= 0 || $times <= 0)
		{
			Logger::fatal('Err para, %d, %d!', $dishID, $times);
			throw new Exception('fake');
		}
		Logger::debug('Kitchen::goldCookByTimes Start.');
		// 花钱做饭
		$ret = KitchenLogic::goldCook($dishID, $isCritical, $times);

		Logger::debug('Kitchen::goldCookByTimes End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IKitchen::sell()
	 */
	public function sell($dishID) 
	{
		// 检查参数
		if ($dishID <= 0)
		{
			Logger::fatal('Err para, %d!', $dishID);
			throw new Exception('fake');
		}
		Logger::debug('Kitchen::sell Start.');
		// 就卖一样
		$ret = KitchenLogic::sell($dishID);

		Logger::debug('Kitchen::sell End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IKitchen::placeOrder()
	 */
	public function placeOrder($uid, $dishID) 
	{
		// 检查参数
		if ($dishID <= 0)
		{
			Logger::fatal('Err para, %d!', $dishID);
			throw new Exception('fake');
		}
		Logger::debug('Kitchen::placeOrder Start.');
		// 下单
		$ret = KitchenLogic::placeOrder($uid, $dishID);

		Logger::debug('Kitchen::placeOrder End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IKitchen::getCDTime()
	 */
	public function getCDTime($type) 
	{
		Logger::debug('Kitchen::getCDTime Start.');
		// 查询CD时间
		$ret = KitchenLogic::getCDTime($type);

		Logger::debug('Kitchen::getCDTime End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IKitchen::clearCDByGold()
	 */
	public function clearCDByGold($type) 
	{
		Logger::debug('Kitchen::clearCDByGold Start.');
		// 清除CD时间
		$ret = KitchenLogic::clearCDByGold($type);

		Logger::debug('Kitchen::clearCDByGold End.');
		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */