<?php

class MyExchangeShop
{
	private $m_exchangeShop;
	private static $instance = NULL;

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
		// $info = RPCContext::getInstance()->getSession('exchangeshop.list');
		// 阵营战结算的时候，不能用session
		$info = array();
		// 获取用户ID，使用用户ID获取活跃度信息
		$uid = RPCContext::getInstance()->getUid();
		// 如果没顺利取得活跃度信息
		if (empty($info['uid'])) 
		{
			if (empty($uid)) 
			{
				Logger::fatal('Can not get info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户信息
			$info = ExchangeShopDao::getInfo($uid);
			// 检查用户是否完成相应任务
			if ($info === false)
			{
				// 初始化人信息
				$info = ExchangeShopDao::addNewInfo($uid);
				Logger::debug('creat a new record in honour shop.');
			}
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			ExchangeShopDao::setBufferWithoutSelect($uid, $info);
		}
		// 赋值给自己
		$this->m_exchangeShop = $info;
		// 设置进session
		RPCContext::getInstance()->setSession('exchangeshop.list', $this->m_exchangeShop);
		Logger::debug('this->m_exchangeShop = [%s]', $this->m_exchangeShop);
	}

	/**
	 * 
	 */
	public function getInfo()
	{
		// 返回最新的数据
		return $this->m_exchangeShop;
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
			$this->m_exchangeShop['honour_point'] += $point;
		}
		else 
		{
			$this->m_exchangeShop['honour_point'] -= $point;
			$this->m_exchangeShop['exchange_item_time'] = Util::getTime();
		}
		// 设置进session
		RPCContext::getInstance()->setSession('exchangeshop.list', $this->m_exchangeShop);
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
		if(EMPTY($this->m_exchangeShop['va_exchange_item_info'][$itemId]))
		{
			$this->m_exchangeShop['va_exchange_item_info'][$itemId] = $num;
		}
		else 
		{
			$this->m_exchangeShop['va_exchange_item_info'][$itemId] += $num;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('exchangeshop.list', $this->m_exchangeShop);
	}

	/**
	 * 更新数据库
	 */
	public function save()
	{
		// 更新到数据库
		ExchangeShopDao::updateInfo($this->m_exchangeShop['uid'], $this->m_exchangeShop);
		// 返回更新信息
		return $this->m_exchangeShop;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */