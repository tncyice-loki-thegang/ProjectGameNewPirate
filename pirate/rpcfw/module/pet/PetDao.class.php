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
 * Class       : PetDao
 * Description : 宠物数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class PetDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblPet = 't_pet';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);
	// 数据缓存区
	private static $buffer = array();

	/**
	 * 获取用户的全部宠物等级
	 * @param string $uid						用户ID
	 * @return 返回相应信息
	 */
	public static function getPetInfo($uid)
	{
		// 优先检查缓冲区数据
		if (isset(self::$buffer[$uid]))
		{
			// 缓冲区如果有的话，那么就使用缓冲区的数据
			return self::$buffer[$uid];
		}
		// 使用 uid 作为条件
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->select(array('uid', 
		                              'cd_time', 
		                              'cd_status', 
		                              'pet_slots', 
		                              'train_slots', 
									  'warehouse_slots',
									  'rapid_times',
									  'rapid_date',
		                              'cur_pet', 
		                              'va_pet_info'))
		               ->from(self::$tblPet)
					   ->where($where)->where(self::$status)->query();
		// 检查返回值
		if (isset($arrRet[0]))
		{
			// 将检索的结果放到缓冲区里面
			self::$buffer[$uid] = $arrRet[0];
			return $arrRet[0];
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
	 * 更新用户宠物信息
	 * @param string $uid						用户ID
	 * @param array $set						更新项目
	 */
	public static function updPetInfo($uid, $set)
	{
		Logger::debug("UpdPetInfo called, buffer is %s, set is %s.", self::$buffer, $set);
		// 缓存不为空，则进行比较
		if (!empty(self::$buffer[$uid]))
		{
			// 判断，如果什么都没改动，那么直接返回
			if (self::$buffer[$uid] == $set)
			{
				Logger::debug("Upd pet array diff ret is same.");
				return $set;
			}
		}

		// 检查缓冲区数据, 更新缓冲区的数据
		self::$buffer[$uid] = $set;
		// 将用户ID作为条件
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->update(self::$tblPet)
		               ->set($set)
		               ->where($where)->query();
		return $arrRet;
	}

	/**
	 * 添加新宠物信息，需要在创建主船时候调用
	 * @param string $uid						
	 */
	public static function addNewPetInfo($uid)
	{
		// 设置属性
		$arr = array('uid' => $uid,
					 'cd_time' => 0,
		             'cd_status' => PetConf::RAPID_FREE,
		             'pet_slots' => btstore_get()->PET_ROOM['init_slot_num'], 
		             'train_slots' => btstore_get()->PET_ROOM['init_train_slot'], 
					 'warehouse_slots' => btstore_get()->PET_ROOM['init_warehouse_slot'],
					 'rapid_times' => 0,
					 'rapid_date' => 0,
		             'cur_pet' => 0, 
		             'va_pet_info' => array(), 
					 'status' => DataDef::NORMAL);

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblPet)
		               ->values($arr)->query();

		// 给缓冲区也插入一条数据
		self::$buffer[$uid] = $arr;
		// 缓冲区好像不需要状态字段
		unset(self::$buffer[$uid]['status']);
		return $arr;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */