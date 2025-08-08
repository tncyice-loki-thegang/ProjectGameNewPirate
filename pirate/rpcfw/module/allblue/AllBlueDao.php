<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AllBlueDao.php 33192 2012-12-15 08:48:29Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/allblue/AllBlueDao.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-15 16:48:29 +0800 (六, 2012-12-15) $
 * @version $Revision: 33192 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : AllBlueDao
 * Description : 伟大的航道数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class AllBlueDao
{
	// 定义表的名称
	private static $tblName = 't_allblue';
	// 表项目的名称
	private static $tabField = array('uid', 
									 'va_belly_times',
									 'gold_times', 
									 'collect_time', 
									 'atkmonster_fail_times', 
									 'monster_id', 
									 'status',
									 'va_farmfish_queueInfo', 
									 'farmfish_times', 
									 'farmfish_tftimes', 
									 'farmfish_wftimes',
									 'farmfish_wdftimes', 
									 'farmfish_time',
									 'farmfish_times_changeflg',
									 'before_vip');
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);

	// 数据缓存区
	private static $buffer = array();

	/**
	 * 贝利采集次数
	 * 
	 * @param NULL
	 * @return 返回相应信息
	 */
	public static function initBellyCount()
	{
		return array('times' => array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0),
					 'maxtimes' => array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0));
	}

	/**
	 * 初始化养鱼队列信息
	 * 
	 * @param NULL
	 * @return 返回相应信息
	 */
	public static function initFishQueue()
	{
		// 队列一
		$queie1 = array('qstatus' => 1,			// 队列的状态 0:未开通,1:已开通
						'qopentimes' => 0,		// 当天队列开通次数
						'fstatus' => 0,			// 养鱼的状态 0:空闲,1:养殖中,2:成熟
						'btime' => 0,			// 养鱼开始时间
						'etime' => 0,			// 养鱼成熟时间
						'fishid' => 0,			// 该队列所养的鱼的ID
						'isboot' => 0,			// 保护罩状态 0:未开启 1:开启
						'tfcount' => 0,			// 该队列被偷的次数
						'wfcount' => 0,			// 该队列被祝福的次数
						'krillid' => 0,			// 准备养殖的鱼苗 0:没有鱼
						'krillinfo' => array(), // 鱼苗信息
						'thief' => array(),		// 小偷信息uid
						'wisher' => array());	// 祝福信息uid
		// 队列二
		$queie2 = array('qstatus' => 0,			// 队列的状态 0:未开通,1:已开通
						'qopentimes' => 0,		// 当天队列开通次数
						'fstatus' => 0,			// 养鱼的状态 0:空闲,1:养殖中,2:成熟
						'btime' => 0,			// 养鱼开始时间
						'etime' => 0,			// 养鱼成熟时间
						'fishid' => 0,			// 该队列所养的鱼的ID
						'isboot' => 0,			// 保护罩状态 0:未开启 1:开启
						'tfcount' => 0,			// 该队列被偷的次数
						'wfcount' => 0,			// 该队列被祝福的次数
						'krillid' => 0,			// 准备养殖的鱼苗 0:没有鱼
						'krillinfo' => array(), // 鱼苗信息
						'thief' => array(),		// 小偷信息uid
						'wisher' => array());	// 祝福信息uid
		// 队列三
		$queie3 = array('qstatus' => 0,			// 队列的状态 0:未开通,1:已开通
						'qopentimes' => 0,		// 当天队列开通次数
						'fstatus' => 0,			// 养鱼的状态 0:空闲,1:养殖中,2:成熟
						'btime' => 0,			// 养鱼开始时间
						'etime' => 0,			// 养鱼成熟时间
						'fishid' => 0,			// 该队列所养的鱼的ID
						'isboot' => 0,			// 保护罩状态 0:未开启 1:开启
						'tfcount' => 0,			// 该队列被偷的次数
						'wfcount' => 0,			// 该队列被祝福的次数
						'krillid' => 0,			// 准备养殖的鱼苗 0:没有鱼
						'krillinfo' => array(), // 鱼苗信息
						'thief' => array(),		// 小偷信息uid
						'wisher' => array());	// 祝福信息uid
		return array('0' => $queie1, '1' => $queie2, '2' => $queie3);
	}
	
	/**
	 * 获取用户的采集信息
	 * 
	 * @param string $uid						用户ID
	 * @return 返回相应信息
	 */
	public static function getAllBlueInfo($uid)
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
		$arrRet = self::selectAllBlue($arrCond, $arrField);

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
	public static function updateAllBlueInfo($uid, $set)
	{
		Logger::debug("updateAllBlueInfo called, buffer is %s, set is %s.", self::$buffer, $set);
		// 缓存不为空，则进行比较
		if (!empty(self::$buffer[$uid]))
		{
			// 判断，如果什么都没改动，那么直接返回
			if (self::$buffer[$uid] == $set)
			{
				Logger::debug("Upd allbuleinfo array diff ret is same.");
				return $set;
			}
		}

		// 检查缓冲区数据, 更新缓冲区的数据
		self::$buffer[$uid] = $set;
		// 将用户ID作为条件
		$arrCond = array(array('uid', '=', $uid));
		self::updateAllBlue($arrCond, $set);
		return;
	}

	/**
	 * 添加用户信息
	 */
	public static function addNewAllBlueInfo($uid)
	{
		$useObj = EnUser::getUserObj();
		$vip = $useObj->getVip();
		
		// 设置属性
		$arr = array('uid' => $uid, 
					'va_belly_times' => self::initBellyCount(), 
					'gold_times' => 0, 
					'collect_time' => 0, 
					'monster_id' => 0,
					'atkmonster_fail_times' => 0,
					'status' => DataDef::NORMAL,
					'farmfish_times' => 0,
					'farmfish_tftimes' => 0,
					'farmfish_wftimes' => 0,
					'farmfish_wdftimes' => 0,
					'va_farmfish_queueInfo' => self::initFishQueue(),
					'farmfish_time' => Util::getTime(),
					'farmfish_times_changeflg' => 0,
					'before_vip' => $vip);

		// 数据插入
		self::insertAllBlue($arr);
		// 给缓冲区也插入一条数据
		self::$buffer[$uid] = $arr;
		// 缓冲区好像不需要状态字段
				logger::warning($arr);
		unset(self::$buffer[$uid]['status']);
		return $arr;
	}

	/**
	 * 添加用户信息
	 * 
	 * @param array $arrUid						uid数组集合
	 * @return 返回相应信息
	 */
	public static function getUserInfoByIn($arrUid)
	{
		$data = new CData();
		$arrRet = $data->select(self::$tabField)->from(self::$tblName)->where('uid', 'IN', $arrUid)->query();
		return Util::arrayIndex($arrRet, 'uid');
	}
	
	
	private static function selectAllBlue($arrCond, $arrField)
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
	
	private static function updateAllBlue($arrCond, $arrField)
	{
		$data = new CData();
		$data->update(self::$tblName)->set($arrField);
		foreach ( $arrCond as $cond )
		{
			$data->where($cond);
		}	
		$data->query();
	}
	
	private static function insertAllBlue($arrField)
	{	
		$data = new CData();
		$data->insertInto(self::$tblName)->values($arrField)->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */