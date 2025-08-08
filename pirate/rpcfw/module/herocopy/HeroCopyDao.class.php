<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroCopyDao.class.php 25458 2012-08-10 07:16:26Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/herocopy/HeroCopyDao.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-08-10 15:16:26 +0800 (五, 2012-08-10) $
 * @version $Revision: 25458 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : HeroCopyDao
 * Description : 英雄副本数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class HeroCopyDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblHeroCopy = 't_hero_copy';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);
	// 数据缓存区
	private static $buffer = array();

	/******************************************************************************************************************
     * t_hero_copy 表操作
     ******************************************************************************************************************/
	/**
	 * 用用户ID获取玩家Happy过的副本
	 * @param int $uid							用户ID
	 * @return 返回相应信息
	 */
	public static function getUserCopies($uid)
	{
		Logger::debug("GetUserCopies called, buffer is %s.", self::$buffer);
		// 优先检查缓冲区数据
		if (isset(self::$buffer[$uid]))
		{
			// 缓冲区如果有的话，那么就使用缓冲区的数据
			return self::$buffer[$uid];
		}
		// 使用 uid 作为条件
		$data = new CData();
		$arrRet = $data->select(array('uid', 
		                              'copy_id', 
		                              'is_over', 
									  'coins', 
									  'buy_coin_times',
		                              'va_copy_info'))
		               ->from(self::$tblHeroCopy)
					   ->where(array("uid", "=", $uid))
					   ->where(self::$status)
					   ->query();
		// 检查返回值
		if (!empty($arrRet))
		{
			// 将检索的结果放到缓冲区里面, 以copyID作为KEY返回
			self::$buffer[$uid] = Util::arrayIndex($arrRet, 'copy_id');
			return self::$buffer[$uid];
		}
		// 没检索结果的时候，直接返回false
		return $arrRet;
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
	 * 更新副本信息
	 * @param array $set						需要更新的内容
	 */
	public static function updateCopyInfo($value)
	{
		Logger::debug("UpdCopyInfo called, buffer is %s, set is %s.", self::$buffer, $value);
		// 缓存不为空，则进行比较
		if (!empty(self::$buffer[$value['uid']][$value['copy_id']]))
		{
			// 判断，如果什么都没改动，那么直接返回
			if (self::$buffer[$value['uid']][$value['copy_id']] == $value)
			{
				Logger::debug("Upd copy array diff ret is same.");
				return $value;
			}
		}

		// 检查缓冲区数据, 更新缓冲区的数据
		self::$buffer[$value['uid']][$value['copy_id']] = $value;

		$data = new CData();
		$arrRet = $data->insertOrUpdate(self::$tblHeroCopy)
		               ->values($value)
		               ->query();
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */