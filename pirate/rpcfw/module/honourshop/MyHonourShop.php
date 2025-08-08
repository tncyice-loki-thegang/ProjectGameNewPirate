<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyHonourShop.php 33371 2012-12-18 05:42:16Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/honourshop/MyHonourShop.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-18 13:42:16 +0800 (二, 2012-12-18) $
 * @version $Revision: 33371 $
 * @brief 
 *  
 **/
class MyHonourShop
{

	private $m_honourShop;						// 用户数据
	private static $instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MyHonourShop
	 */
	public static function getInstance()
	{
  		if (!self::$instance instanceof self)
  		{
     		self::$instance = new self();
  		}
  		return self::$instance;
	}

	/**
	 * 毁掉单例，单元测试对应
	 */
	public static function release() 
	{
		if (self::$instance != null) 
		{
			self::$instance = null;
		}
	}

	/**
	 * 构造函数，获取 session 信息
	 */
	private function __construct() 
	{
		// 从 session 中取得伟大的航道用户信息
		// $honourInfo = RPCContext::getInstance()->getSession('honourshop.list');
		// 阵营战结算的时候，不能用session
		$honourInfo = array();
		// 获取用户ID，使用用户ID获取活跃度信息
		$uid = RPCContext::getInstance()->getUid();
		// 如果没顺利取得活跃度信息
		if (empty($honourInfo['uid'])) 
		{
			if (empty($uid)) 
			{
				Logger::fatal('Can not get honourShop info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户信息
			$honourInfo = HonourShopDao::getHonourInfo($uid);
			// 检查用户是否完成相应任务
			if ($honourInfo === false)
			{
				// 初始化人信息
				$honourInfo = HonourShopDao::addNewHonourInfo($uid);
				Logger::debug('creat a new record in honour shop.');
			}
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			HonourShopDao::setBufferWithoutSelect($uid, $honourInfo);
		}
		// 赋值给自己
		$this->m_honourShop = $honourInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('honourshop.list', $this->m_honourShop);
		Logger::debug('this->m_honourShop = [%s]', $this->m_honourShop);
	}

	/**
	 * 
	 */
	public function getHonourInfo()
	{
		// 返回最新的数据
		return $this->m_honourShop;
	}

	/**
	 * 更新荣誉积分
	 * 
	 * @param int $point						荣誉积分
	 * @param boolean $isAdd					增加还是减少,default = TRUE:增加
	 */
	public function mondifyHonourPoint($point, $isAdd = TRUE)
	{
		if($isAdd == TRUE)
		{
			$this->m_honourShop['honour_point'] += $point;
		}
		else 
		{
			$this->m_honourShop['honour_point'] -= $point;
			$this->m_honourShop['exchange_item_time'] = Util::getTime();
		}
		// 设置进session
		RPCContext::getInstance()->setSession('honourshop.list', $this->m_honourShop);
	}

	/**
	 * 更新兑换过的物品信息
	 * 
	 * @param int $itemId						物品id
	 * @param int $exTimes						兑换次数
	 * @param boolean $isAdd					增加还是减少,default = TRUE:增加
	 */
	public function mondifyItemInfo($itemId, $num)
	{
		if(EMPTY($this->m_honourShop['va_exchange_item_info']['iteminfo'][$itemId]))
		{
			$this->m_honourShop['va_exchange_item_info']['iteminfo'][$itemId] = $num;
		}
		else 
		{
			$this->m_honourShop['va_exchange_item_info']['iteminfo'][$itemId] += $num;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('honourshop.list', $this->m_honourShop);
	}

	/**
	 * 更新数据库
	 */
	public function save()
	{
		// 更新到数据库
		HonourShopDao::updateHonourInfo($this->m_honourShop['uid'], $this->m_honourShop);
		// 返回更新信息
		return $this->m_honourShop;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */