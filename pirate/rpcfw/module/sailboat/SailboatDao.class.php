<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $$Id: SailboatDao.class.php 18414 2012-04-10 04:00:42Z YangLiu $$
 * 
 **********************************************************************************************************************/

/**
 * @file $$HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sailboat/SailboatDao.class.php $$
 * @author $$Author: YangLiu $$(liuyang@babeltime.com)
 * @date $$Date: 2012-04-10 12:00:42 +0800 (二, 2012-04-10) $$
 * @version $$Revision: 18414 $$
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : SailboatDao
 * Description : 主船数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class SailboatDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblBoat = 't_sailboat';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);

	/**
	 * 造一条新船
	 * 
	 * @param array $boatInfo					所需创建的主船信息
	 */
	public static function makeNewBoat($boatInfo)
	{
		$data = new CData();
		$arrRet = $data->insertInto(self::$tblBoat)
		               ->values($boatInfo)->query();
		return $arrRet;
	}

	/**
	 * 用玩家ID获取主船信息
	 * 
	 * @param string $uid						玩家ID
	 * @param array  $arrField					需要返回的数据库项目名称
	 */
	public static function getBoatInfoByUid($uid, $arrField)
	{
		// 使用 uid 作为条件
		$data = new CData();
		$arrRet = $data->select($arrField)
		               ->from(self::$tblBoat)
					   ->where(array("uid", "=", $uid))->where(self::$status)->query();

		return empty($arrRet) ? $arrRet : $arrRet[0];
	}

	/**
	 * 更新主船信息
	 * 
	 * @param string $uid						玩家ID
	 * @param array  $set						需要更新的内容
	 */
	public static function updateBoatInfo($uid, $set)
	{
		$data = new CData();
		$arrRet = $data->update(self::$tblBoat)
		               ->set($set)
		               ->where(array("uid", "=", $uid))->query();
		return $arrRet;
	}

	/**
	 * 通过一组uid，查询主船类型
	 */
	public static function getMultiUserBoatType($userList)
	{
		// 如果参数不对，则返回空数组
		if (empty($userList))
		{
			return array();
		}
		// 使用 uid组 作为条件
		$data = new CData();
		$arrRet = $data->select(array('uid', 'boat_type'))
		               ->from(self::$tblBoat)
					   ->where(array("uid", "IN", $userList))->where(self::$status)->query();

		// 如果不为空，则使用uid作为主键
		if (!empty($arrRet))
		{
			$arrRet = Util::arrayIndex($arrRet, 'uid');
		}
		return $arrRet;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */