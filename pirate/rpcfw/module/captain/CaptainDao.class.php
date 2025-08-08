<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: CaptainDao.class.php 36305 2013-01-17 10:19:27Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/captain/CaptainDao.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-17 18:19:27 +0800 (四, 2013-01-17) $
 * @version $Revision: 36305 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : CaptainDao
 * Description : 船长室数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class CaptainDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblCaptain = 't_captain';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);


	/**
	 * 获取用户的船长室信息
	 * @param string $uid						用户ID
	 * @return 返回相应信息
	 */
	public static function getCaptainInfo($uid)
	{
		// 使用 uid 作为条件
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->select(array('uid', 
		                              'sail_times', 
		                              'sail_date', 
		                              'cd_time', 
									  'fatigue',
									  'gold_sail_times',
									  'gold_sail_date',
		                              'va_sail_info'))
		               ->from(self::$tblCaptain)
					   ->where($where)->where(self::$status)->query();

		// 修复旧有数据, 如果之前没有这一项，那么需要凭空弄出来一份
		if (isset($arrRet[0]))
		{
			if (!isset($arrRet[0]['va_sail_info']['question_ids']))
			{
				$arrRet[0]['va_sail_info']['question_ids'] = array();
			}
			// 返回数据
			return $arrRet[0];
		}
		return false;
	}

	/**
	 * 更新用户船长室信息
	 * @param string $uid						用户ID
	 * @param array $set						更新项目
	 */
	public static function updCaptainInfo($uid, $set)
	{
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->update(self::$tblCaptain)
		               ->set($set)
		               ->where($where)->query();
		return $arrRet;
	}

	/**
	 * 添加新船长室信息，需要在创建主船时候调用
	 * @param string $uid						
	 */
	public static function addNewCaptainInfo($uid)
	{
		// 设置属性
		$arr = array('uid' => $uid,
					 'sail_times' => btstore_get()->CAPTAIN_ROOM['sail_times_max'],
		             'sail_date' => Util::getTime(),
		             'cd_time' => 0, 
					 'fatigue' => 0,
					 'gold_sail_times' => 0,
					 'gold_sail_date' => 0,
					 'question_id' => 0,
					 'va_sail_info' => array('question_ids' => array()),
					 'status' => DataDef::NORMAL);

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblCaptain)
		               ->values($arr)->query();
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */