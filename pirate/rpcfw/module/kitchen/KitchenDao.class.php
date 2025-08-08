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
 * Class       : KitchenDao
 * Description : 厨房数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class KitchenDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblKitchen = 't_kitchen';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);

	/**
	 * 获取用户的厨房信息
	 * @param string $uid						用户ID
	 * @return 返回相应信息
	 */
	public static function getKitchenInfo($uid)
	{
		// 使用 uid 作为条件
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->select(array('uid', 
		                              'cook_cd_time', 
		                              'order_cd_time', 
		                              'lv', 
		                              'exp', 
									  'gold_cook_times',
									  'gold_cook_date',
									  'cook_times',
									  'cook_date',
									  'cook_accumulate',
									  'be_order_times',
									  'order_times',
		                              'order_date',
									  'order_accumulate',
									  'va_kitchen_info'))
		               ->from(self::$tblKitchen)
					   ->where($where)->where(self::$status)->query();
		// 如果有数值时，需要插入一个belly, 用来给前端传递卖菜所得
		if (isset($arrRet[0]))
		{
			$arrRet[0]['belly'] = 0;
			return $arrRet[0];
		}
		else 
		{
			return false;
		}
	}

	/**
	 * 更新用户厨房信息
	 * @param string $uid						用户ID
	 * @param array $set						更新项目
	 */
	public static function updKitchenInfo($uid, $set)
	{
		// 一发牵全身啊，这个地方也得改了，如果更新项目里面有 belly 这个值，直接干掉
		if (isset($set['belly']))
		{
			unset($set['belly']);
		}
		// 接下来才能继续更新…… 多麻烦
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->update(self::$tblKitchen)
		               ->set($set)
		               ->where($where)->query();
		return $arrRet;
	}

	/**
	 * 更新用户订单信息
	 * @param string $uid						用户ID
	 * @param string $order						原始预订次数
	 * @param string $beOrder					原始被预订次数
	 */
	public static function updOrderTimes($uid, 
	                                     $order, $beOrder, $orderAcc,
	                                     $oldOrder, $oldBeOrder)
	{
		$whereUid = array("uid", "=", $uid);
		$whereOrder = array("order_times", "=", $oldOrder);
		$whereBeOrder = array("be_order_times", "=", $oldBeOrder);
		$data = new CData();
		$arrRet = $data->update(self::$tblKitchen)
		               ->set(array('be_order_times' => $beOrder, 
		                           'order_times' => $order, 'order_date' => Util::getTime(),
		               			   'order_accumulate' => $orderAcc))
		               ->where($whereUid)
		               ->where($whereOrder)
		               ->where($whereBeOrder)->query();
		return $arrRet;
	}

	/**
	 * 添加新厨房信息，需要在创建主船时候调用
	 * @param string $uid						
	 */
	public static function addNewKitchenInfo($uid)
	{
		// 设置属性
		$arr = array('uid' => $uid,
					 'cook_cd_time' => 0,
					 'order_cd_time' => 0,
		             'lv' => 1,
					 'exp' => 0,
		             'gold_cook_times' => 0, 
		             'gold_cook_date' => 0, 
					 'cook_times' => 0,
					 'cook_date' => Util::getTime(),
					 'cook_accumulate' => 0,
					 'be_order_times' => 0,
					 'order_times' => 0,
					 'order_date' => Util::getTime(),
					 'order_accumulate' => 0,
					 'va_kitchen_info' => array('stock' => array()),
					 'status' => DataDef::NORMAL);

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblKitchen)
		               ->values($arr)->query();
		return $arr;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */