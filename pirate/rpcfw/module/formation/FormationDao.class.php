<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FormationDao.class.php 23982 2012-07-16 10:04:53Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/formation/FormationDao.class.php $
 * @author $Author: YangLiu $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-16 18:04:53 +0800 (一, 2012-07-16) $
 * @version $Revision: 23982 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : FormationDao
 * Description : 阵型数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class FormationDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 表名定义
	const tblHeroFor = 't_hero_formation';
	// 数据缓存区
	private static $buffer = array();

	/******************************************************************************************************************
     * t_hero_formation 表操作
     ******************************************************************************************************************/
	/**
	 * 获取用户所有阵型数据
	 * @param int $uid							用户ID
	 */
	public static function getAllFormation($uid)
	{
		// 优先检查缓冲区数据
		if (isset(self::$buffer[$uid]))
		{
			// 缓冲区如果有的话，那么就使用缓冲区的数据
			return self::$buffer[$uid];
		}
		// 使用 uid 检索数据
		$data = new CData();
		$arrRet = $data->select(FormationDef::$HERO_FORMATION_FIELDS)
		               ->from(self::tblHeroFor)
		               ->where(array('uid', '=', $uid))
		               ->query();
		// 检查返回值
		if (!empty($arrRet))
		{
			// 将检索的结果放到缓冲区里面, 以fid作为KEY返回
			self::$buffer[$uid] = Util::arrayIndex($arrRet, 'fid');
			return self::$buffer[$uid];
		}
		// 否则返回空
		return false;
	}

	/**
	 * 获取用户所有阵型数据
	 * @param int $uid							用户ID
	 * @param int $fid							阵型ID
	 */
	public static function getFormationByID($uid, $fid)
	{
		// 优先检查缓冲区数据
		if (isset(self::$buffer[$uid][$fid]))
		{
			// 缓冲区如果有的话，那么就使用缓冲区的数据
			return self::$buffer[$uid][$fid];
		}
		// 使用 uid 检索数据
		$data = new CData();
		$arrRet = $data->select(FormationDef::$HERO_FORMATION_FIELDS)
		               ->from(self::tblHeroFor)
		               ->where(array('uid', '=', $uid))
		     		   ->where(array('fid', '=', $fid))
		               ->query();
		// 检查返回值
		if (isset($arrRet[0]))
		{
			// 将检索的结果放到缓冲区里面
			self::$buffer[$uid][$fid] = $arrRet[0];
			return $arrRet[0];
		}
		// 返回当前数据库中该用户的所有阵型数据
		return false;
	}

	/**
	 * 更新阵型信息
	 * @param int $uid							用户ID
	 * @param int $fid							阵型ID
	 * @param array $arrField					更新数值和更新项目
	 */
	public static function updateFormationInfo($uid, $fid, $arrField)
	{
		Logger::debug("UpdCopyInfo called, buffer is %s, set is %s.", self::$buffer, $arrField);
		// 缓存不为空，则进行比较
		if (!empty(self::$buffer[$uid][$fid]))
		{
			// 判断，如果什么都没改动，那么直接返回
			if (self::$buffer[$uid][$fid] == $arrField)
			{
				Logger::debug("Upd formation array diff ret is same.");
				return $arrField;
			}
		}

		// 检查缓冲区数据, 更新缓冲区的数据
		self::$buffer[$uid][$fid] = $arrField;
		// 使用 uid 和  阵型ID 更新数据
		$data = new CData();
		$arrRet = $data->update(self::tblHeroFor)
		     		   ->set($arrField)
		     		   ->where(array('uid', '=', $uid))
		     		   ->where(array('fid', '=', $fid))
		     		   ->query();

		return $arrRet;
	}

	/**
	 * 在所有阵型里删除此英雄
	 * @param int $uid							用户ID
	 * @param int $hid							英雄ID
	 */
	public static function delHeroFromFormation($uid, $hid)
	{
		foreach (FormationDef::$HERO_FORMATION_KEYS as $hidpos)
		{
			$data = new CData();
			$data->update(self::tblHeroFor)
			     ->set(array($hidpos => 0))
			     ->where(array('uid', '=', $uid))
			     ->where(array($hidpos, '=', $hid))
			     ->query();	

			// 清除缓存数据
			if (!empty(self::$buffer[$uid]))
			{
				foreach (self::$buffer[$uid] as $fid => $formation)
				{
					self::$buffer[$uid][$fid][$hidpos] = 0;
				}
			}
		}
	}

	/**
	 * 给用户增加一个新阵型
	 * @param array $arrField					空阵型数据
	 */
	public static function addNewFormation($arrField)
	{
		// 检查缓冲区数据, 更新缓冲区的数据
		self::$buffer[$arrField['uid']][$arrField['fid']] = $arrField;
		// 插入数据库
		$data = new CData();
		$arrRet = $data->insertInto(self::tblHeroFor)
		     		   ->values($arrField)
		     		   ->query();

		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */