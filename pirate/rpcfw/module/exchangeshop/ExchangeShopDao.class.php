<?php

class ExchangeShopDao
{
	// 定义表的名称
	private static $tblName = 't_exchange_shop';
	// 表项目的名称
	private static $tabField = array('uid', 'starpoint', 'bindPoint', 'unbindPoint', 
										'contribute', 'heroPoint', 'resource', 'va_exchange_item_info', 'status');
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);

	// 数据缓存区
	private static $buffer = array();

	/**
	 * 获取用户的信息
	 * 
	 * @param string $uid						用户ID
	 * @return 返回相应信息
	 */
	public static function getInfo($uid)
	{
		// 优先检查缓冲区数据
		if (isset(self::$buffer[$uid]))
		{
			// 缓冲区如果有的话，那么就使用缓冲区的数据
			return self::$buffer[$uid];
		}
		
		// 使用 uid 作为条件
		$arrCond = array(array('uid', '=', $uid));
		$arrField = self::$tabField;
		$arrRet = self::selectExchangeShop($arrCond, $arrField);

		// 检查返回值
		if (!empty($arrRet))
		{
			// 将检索的结果放到缓冲区里面
			self::$buffer[$uid] = $arrRet;
			return $arrRet;
		}
		// 没检索结果的时候，直接返回false
		return false;
	}

	/**
	 * 有些时刻，没有从数据库进行检索，而是从session里面获取数据，那么数据库类的buffer就是空的
	 * 这时候需要调用这个方法，对缓存进行初始化
	 * 
	 * @param array $set						缓存数据
	 */
	public static function setBufferWithoutSelect($uid, $set)
	{
		Logger::debug("SetBufferWithoutSelect called, buffer is %s, set is %s.", self::$buffer, $set);
		// 只有没有缓冲区数据的时候，才保存缓冲区数据
		if (empty(self::$buffer[$uid]))
		{
			self::$buffer[$uid] = $set;
		}
	}

	/**
	 * 更新用户信息
	 * 
	 * @param string $uid						用户ID
	 * @param array $set						更新项目
	 */
	public static function updateInfo($uid, $set)
	{
		Logger::debug("updateInfo is called, buffer is %s, set is %s.", self::$buffer, $set);
		// 缓存不为空，则进行比较
		if (!empty(self::$buffer[$uid]))
		{
			// 判断，如果什么都没改动，那么直接返回
			if (self::$buffer[$uid] == $set)
			{
				Logger::debug("Upd Info array diff ret is same.");
				return $set;
			}
		}

		// 检查缓冲区数据, 更新缓冲区的数据
		self::$buffer[$uid] = $set;
		// 将用户ID作为条件
		$arrCond = array(array('uid', '=', $uid));
		self::updateExchangeShop($arrCond, $set);
		return;
	}

	/**
	 * 添加用户信息
	 * 
	 * @param string $uid						
	 */
	public static function addNewInfo($uid)
	{
		// 设置属性
		$arr = array('uid' => $uid,
					/** BLOOD_STAR_SHOP **/
					'starpoint' => 0,			
			
					/** CARD_SHOP **/
					'bindPoint' => 0,
					'unbindPoint' => 0,
					
					/** WORLD_TREE_SHOP **/
					'contribute' => 0,
					'heroPoint' => 0,
					
					/** BOAT_BATTLE_SHOP || PIRATE_ARENA_SHOP**/ /** SWEEPSTAKE_SHOP **/
					'resource' => 0,
			
					'va_exchange_item_info' => array(), 
					'status' => DataDef::NORMAL);
		// 数据插入
		self::insertExchangeShop($arr);
		// 给缓冲区也插入一条数据
		self::$buffer[$uid] = $arr;
		// 缓冲区好像不需要状态字段
		unset(self::$buffer[$uid]['status']);
		return $arr;
	}

	private static function selectExchangeShop($arrCond, $arrField)
	{
		$data = new CData();
		$data->select($arrField)->from(self::$tblName);
		foreach ( $arrCond as $cond )
		{
			$data->where($cond);
		}	
		$arrRet = $data->query();
		if (!empty($arrRet))
		{
			$arrRet = $arrRet[0];
		}
		return $arrRet;
	}
	
	private static function updateExchangeShop($arrCond, $arrField)
	{
		$data = new CData();
		$data->update(self::$tblName)->set($arrField);
		foreach ( $arrCond as $cond )
		{
			$data->where($cond);
		}	
		$data->query();
	}
	
	private static function insertExchangeShop($arrField)
	{	
		$data = new CData();
		$data->insertInto(self::$tblName)->values($arrField)->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */