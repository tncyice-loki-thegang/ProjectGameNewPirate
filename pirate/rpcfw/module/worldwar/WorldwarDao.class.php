<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldwarDao.class.php 38366 2013-02-18 02:17:38Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldwar/WorldwarDao.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-18 10:17:38 +0800 (一, 2013-02-18) $
 * @version $Revision: 38366 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : WorldwarDao
 * Description : 跨服赛数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class WorldwarDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblUserWorldwar = 't_user_world_war';
	private static $tblUserWorldSignUp = 't_user_world_sign_up';
	private static $tblWorldwar = 't_world_war';
	private static $tblServerInfo = 't_server_info';
	private static $tblWorshipTemple = 't_worship_temple';
	private static $tblWorshipUser = 't_worship_user';

	/******************************************************************************************************************
     * t_user_world_war 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取用户跨服赛的信息
	 * 
	 * @return 返回相应信息
	 */
	public static function getUserWorldWarInfo($uid)
	{
		$data = new CData();
		$arrRet = $data->select(array('uid',
									  'win_team_lose_times',
									  'lose_team_lose_times', 
		                              'team',
		                              'cheer_uid',
		                              'cheer_uid_server_id',
		                              'cheer_time',
		                              'worship_times',
		                              'worship_time',
									  'update_fmt_time',
		                              'sign_time',
									  'sign_session',
									  'group_prize_id',
									  'group_prize_time',
									  'world_prize_id',
									  'world_prize_time',
		                              'va_world_war'))
		               ->from(self::$tblUserWorldwar)
		               ->where(array("uid", "=", $uid))
					   ->query();
		// 检查返回值并返回
		return isset($arrRet[0]) ? $arrRet[0] : array();
	}

	/**
	 * 取得所有报名的用户信息 (服内)
	 * 
	 * @param int $signUpTime 					报名时间
	 * @param int $failNum 						失败次数
	 * @param int $team 						胜者组还是负者组 (这里是不等于)
	 */
	public static function getSignUpUserInfo($signUpTime, $failNum, $team = 0)
	{
		$data = new CData();

		// 如果是胜者组
		if($team == 0)
		{
			$field = array('uid', 'win_team_lose_times as lose_times', 'team');
			$cond = array('win_team_lose_times', '<', $failNum);
		}
		// 如果是败者组
		else 
		{
			$field = array('uid', 'lose_team_lose_times as lose_times', 'team');
			$cond = array('lose_team_lose_times', '<', $failNum);
		}

		$data->select($field)
             ->from(self::$tblUserWorldwar)
             ->where(array('sign_time', '>=', $signUpTime))
             ->where($cond);

		if (!empty($team))
		{             
             $data->where(array('team', '!=', $team));
		}

		// 查询并返回结果
		return $data->query();
	}

	/**
	 * 更新用户跨服赛信息
	 * 
	 * @param array $set						更新项目
	 * @param int $uid							用户ID
	 */
	public static function updUserWorldWarInfo($set, $uid)
	{
		$data = new CData();
		$arrRet = $data->update(self::$tblUserWorldwar)
		               ->set($set)
		               ->where(array("uid", "=", $uid))
		               ->query();
		return $arrRet;
	}

	/**
	 * 插入一条新的用户跨服赛数据
	 */
	public static function insertUserWorldWar($uid)
	{
		// 设置插入数据
		$set = array('uid' => $uid,
					 'win_team_lose_times' => 0, 
					 'lose_team_lose_times' => 0, 
		             'team' => 0,
		             'cheer_uid' => 0,
		             'cheer_uid_server_id' => '0',
		             'cheer_time' => 0,
		             'worship_times' => 0,
		             'worship_time' => 0,
					 'update_fmt_time' => 0,
		             'sign_time' => 0,
					 'sign_session' => 0,
					 'group_prize_id' => 0,
					 'group_prize_time' => 0,
					 'world_prize_id' => 0,
					 'world_prize_time' => 0,
		             'va_world_war' => array('replay' => array(), 
		             						 'cheer' => array(), 
		             						 'fight_para' => array()));

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblUserWorldwar)
		               ->values($set)->query();
		return $set;
	}

	/**
	 * 获取用户保存的战斗信息
	 * 
	 * @param int $uid							用户ID
	 * @param int $serverID						用户所在的服务器ID
	 * @param int $now							跨服 or 服内
	 */
	public static function getUserFightPara($uid, $serverID, $now)
	{
		$data = new CData();
		// 查询用户所在的数据库
		if (!empty($serverID) && $now == WorldwarDef::TYPE_WORLD)
		{
			$data->useDb(WorldwarConfig::KFZ_DB_NAME);
			// 设置查询语句
			$arrRet = $data->select(array('db_name'))
	             		   ->from(self::$tblServerInfo)
				 		   ->where(array('server_id', '=', $serverID))
				 		   ->query();

			Logger::debug("getUserFightPara , uid is %d, server id %d. DB is %s.", $uid, $serverID, $arrRet[0]);
			$data->useDb($arrRet[0]['db_name']);
		}
		// 查询表
		$arrRet = $data->select(array('va_world_war'))
		               ->from(self::$tblUserWorldwar)
		               ->where(array("uid", "=", $uid))
					   ->query();
		// 检查返回值并返回
		return isset($arrRet[0]) ? $arrRet[0]['va_world_war']['fight_para'] : array();
	}

	/**
	 * 获取所有成功助威人的用户信息，用于发送奖励
	 * 
	 * @param array $uidAry						用户UID组
	 * 
	 * @return array $rewardUidAry				获奖励的UID组 
	 */
	public static function getAllCheerUserInfo($uidAry)
	{
		$data = new CData();
		$arrRet = $data->select(array('uid',
						    		  'cheer_uid', 
						    		  'cheer_time', 
                            		  'cheer_uid_server_id'))
						->from(self::$tblUserWorldwar)
						->where('cheer_uid', 'IN', $uidAry)
						->query();
		return Util::arrayIndex($arrRet, 'uid');
	}
	
	/**
	 * 初始化用户助威信息
	 *  
	 */
	public static function initCheerInfo($time)
	{
		$data = new CData();
		$arrRet = $data->update(self::$tblUserWorldwar)
		               ->set(array('cheer_uid' => 0,
						 		   'cheer_uid_server_id' => 0,
						  		   'cheer_time' => 0))
		               ->where(array("cheer_time", "<", $time))
		               ->query();
		return $arrRet;
	}
	
	/******************************************************************************************************************
     * t_user_world_sign_up 表相关实现
     ******************************************************************************************************************/
	/**
	 * 插入一条新的用户跨服赛数据
	 * 
	 * @param int $teamID 						组别ID
	 * @param int $uid	 						用户ID
	 * @param int $serverID 					服务器ID
	 * @param string $serverName 				服务器名
	 */
	public static function insertUserWorldSignUp($teamID, $uid, $serverID, $serverName)
	{
		// 设置插入数据
		$set = array('team_id' => $teamID,
					 'uid' => $uid, 
		             'uid_server_id' => $serverID,
		             'uid_server_name' => $serverName,
					 'win_team_lose_times' => 0, 
					 'lose_team_lose_times' => 0, 
					 'team' => 0,
		             'sign_time' => Util::getTime());

		$data = new CData();
		$data->useDb(WorldwarConfig::KFZ_DB_NAME);
		$arrRet = $data->insertOrUpdate(self::$tblUserWorldSignUp)
		               ->values($set)
		               ->query();

		return $arrRet;
	}

	/**
	 * 取得所有报名的用户信息
	 * 
	 * @param int $signUpTime 					报名时间
	 * @param int $failNum 						失败次数
	 * @param int $team 						胜者组还是负者组 (这里是不等于)
	 */
	public static function getUserWorldSignUp($signUpTime, $failNum, $team = 0)
	{
		// 如果是胜者组
		if($team == 0)
		{
			$field = array('team_id',
						   'uid',
						   'uid_server_id',
						   'uid_server_name',
						   'win_team_lose_times as lose_times', 
                           'team');

			$cond = array('win_team_lose_times', '<', $failNum);
		}
		// 如果是败者组
		else 
		{
			$field = array('team_id',
						   'uid',
						   'uid_server_id',
						   'uid_server_name',
						   'lose_team_lose_times as lose_times', 
                           'team');

			$cond = array('lose_team_lose_times', '<', $failNum);
		}

		$data = new CData();
		$data->useDb(WorldwarConfig::KFZ_DB_NAME);
		// 查询
		$data->select($field)
             ->from(self::$tblUserWorldSignUp)
             ->where(array('sign_time', '>=', $signUpTime))
             ->where($cond);

		if (!empty($team))
		{             
             $data->where(array('team', '!=', $team));
		}

       	// 查询并返回结果
		return $data->query();
	}

	/**
	 * 取得某特定用户的报名信息
	 * 
	 * @param int $uid							用户ID
	 * @param int $serverID						用户所在服务器ID
	 */
	public static function getUserWorldSignUpByID($uid, $serverID)
	{
		$data = new CData();
		$data->useDb(WorldwarConfig::KFZ_DB_NAME);

		$arrRet = $data->select(array('team_id',
									  'uid',
									  'uid_server_id',
									  'uid_server_name',
									  'win_team_lose_times', 
									  'lose_team_lose_times',
	                            	  'team'))
		               ->from(self::$tblUserWorldSignUp)
		               ->where(array('uid', '=', $uid))
		               ->where(array('uid_server_id', '=', $serverID))
		               ->query();

		// 查询并返回结果
		return isset($arrRet[0]) ? $arrRet[0] : array();
	}

	/**
	 * 更新用户的失败次数
	 * 
	 * @param int $uid							用户ID
	 * @param int $serverID						用户所在服务器ID
	 * @param int $team							胜者组还是负者组
	 */
	public static function addUserWoldLoseTimes($uid, $serverID, $team = WorldwarDef::TEAM_WIN)
	{
		// 如果是胜者组
		if($team == WorldwarDef::TEAM_WIN)
		{
			$field = array('win_team_lose_times' => new IncOperator(1));
		}
		// 如果是败者组
		else 
		{
			$field = array('lose_team_lose_times' => new IncOperator(1));
		}

		$data = new CData();
		$data->useDb(WorldwarConfig::KFZ_DB_NAME);

		$arrRet = $data->update(self::$tblUserWorldSignUp)
		               ->set($field)
		               ->where(array('uid', '=', $uid))
		               ->where(array('uid_server_id', '=', $serverID))
		               ->query();
		return $arrRet;
	}

	/**
	 * 更新用户的组别信息
	 * 
	 * @param int $uid							用户ID
	 * @param int $serverID						用户所在服务器ID
	 * @param int $team							胜者组还是负者组
	 */
	public static function saveUserWoldTeam($uid, $serverID, $team)
	{
		$data = new CData();
		$data->useDb(WorldwarConfig::KFZ_DB_NAME);

		$arrRet = $data->update(self::$tblUserWorldSignUp)
		               ->set(array('team' => $team))
		               ->where(array('uid', '=', $uid))
		               ->where(array('uid_server_id', '=', $serverID))
		               ->query();
		return $arrRet;
	}

	/******************************************************************************************************************
     * t_world_war 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取用户跨服赛的信息
	 * 
	 * @param string $db						想要查询的数据库名
	 * @param int $teamID						大组ID
	 * @param string $date						年月日
	 * @param int $round						回合
	 * @param int $session						第几届
	 * @param int $team							胜者组还是负者组
	 */
	public static function getWorldWarInfo($db, $teamID, $date, $round, $session, $team = 0)
	{
		$data = new CData();
		// 如果设置了db，则表示需要跨表查询
		if (!empty($db))
		{
			$data->useDb($db);
		}
		// 设置查询语句
		$data->select(array('date_ymd',
							'team_id', 
		                    'session',
		                    'round',
		                    'team',
		                    'va_world_war'))
             ->from(self::$tblWorldwar)
			 ->where(array('session', '=', $session));

		// 如果设置了日期，则需要加为参数
		if (!empty($teamID))
		{
        	$data->where(array('team_id', '=', $teamID));
		}
		// 如果设置了日期，则需要加为参数
		if (!empty($date))
		{
        	$data->where(array('date_ymd', '=', $date));
		}
		// 如果设置了轮次，则需要加为参数
		if (!empty($round))
		{
        	$data->where(array('round', '=', $round));
		}
		// 如果设置了第几届，则需要加为参数
		if (!empty($team))
		{
        	$data->where(array('team', '=', $team));
		}
		// 查询并返回结果
		return $data->query();
	}

	/**
	 * 插入一条日志
	 */
	public static function updWorldWar($now, $set)
	{
		$data = new CData();
		// 如果设置了db，则表示需要跨表插入
		if ($now == WorldwarDef::TYPE_WORLD)
		{
			$data->useDb(WorldwarConfig::KFZ_DB_NAME);
		}
		$arrRet = $data->insertOrUpdate(self::$tblWorldwar)
		               ->values($set)
		               ->query();
		return $arrRet;
	}

	/******************************************************************************************************************
     * t_server_info 表相关实现
     ******************************************************************************************************************/
	/**
	 * 根据服务器ID获取服务器名
	 */
	public static function getServerNameByID($serverID)
	{
		$data = new CData();
		$data->useDb(WorldwarConfig::KFZ_DB_NAME);
		// 设置查询语句
		$arrRet = $data->select(array('server_name'))
             		   ->from(self::$tblServerInfo)
			 		   ->where(array('server_id', '=', $serverID))
		               ->query();

		return isset($arrRet[0]) ? $arrRet[0]['server_name'] : '';
	}

	/**
	 * 更新这个表
	 */
	public static function updServerInfo($serverID, $serverName, $dbName)
	{
		$data = new CData();
		$data->useDb(WorldwarConfig::KFZ_DB_NAME);
		// 设置查询语句
		$arrRet = $data->insertOrUpdate(self::$tblServerInfo)
		               ->values(array('server_id' => $serverID, 
		               				  'server_name' => $serverName,
		               				  'db_name' => $dbName))
		               ->query();

		return $arrRet;
	}


	/******************************************************************************************************************
     * t_worship_temple 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取神庙信息，恩，冠亚季军
	 */
	public static function getTempleInfo($uid = 0)
	{
		$data = new CData();
		$data->select(array('session',
							'rank',
							'uid',
						    'uname', 
						    'server_id', 
						    'server_name', 
						    'htid', 
                            'msg'))
			 ->from(self::$tblWorshipTemple);

		if (empty($uid))
		{
			$data->where('uid', '!=', 1);
		}
		else 
		{
			$data->where('uid', '=', $uid);
		}
		return $data->query();
	}

	/**
	 * 插入一条英雄信息
	 */
	public static function updTempleInfo($set, $db = 0)
	{
		$data = new CData();
		// 如果设置了db，则表示需要跨表查询
		if (!empty($db))
		{
			$data->useDb($db);
		}

		$arrRet = $data->insertOrUpdate(self::$tblWorshipTemple)
		               ->values($set)
		               ->query();
		return $arrRet;
	}


	/**
	 * 更新一条英雄信息
	 */
	public static function updateTempleInfo($set, $db = 0)
	{
		$data = new CData();
		// 如果设置了db，则表示需要跨表查询
		if (!empty($db))
		{
			$data->useDb($db);
		}

		$arrRet = $data->Update(self::$tblWorshipTemple)
		               ->set($set)
		               ->query();
		return $arrRet;
	}


	/******************************************************************************************************************
     * t_worship_user 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取最近的膜拜人员
	 */
	public static function getWorshipUserInfo()
	{
		$data = new CData();
		$arrRet = $data->select(array('uid',
						    		  'uname',
									  'lv', 
						    		  'worship_type', 
						    		  'worship_time'))
						->from(self::$tblWorshipUser)
						->where(array("uid", "!=", 0))
					    ->orderBy("worship_time", false)
					    ->query();

		return $arrRet;
	}

	/**
	 * 更新膜拜信息
	 * 
	 * @param int $oldUid					膜拜者uid
	 * @param int $newUid					新膜拜者uid
	 * @param string $uname					膜拜者姓名
	 * @param int $lv						膜拜者等级
	 * @param int $type						膜拜类型
	 */
	public static function updateWorshipUserInfo($oldUid, $newUid, $uname, $lv, $type)
	{
		// 设置空白数据段
		$value = array('uid' => $newUid,
					   'uname' => $uname,
					   'lv' => $lv,
		               'worship_type' => $type,
		               'worship_time' => Util::getTime());
		// 更新到数据库
		$data = new CData();
		$arrRet = $data->Update(self::$tblWorshipUser)
		               ->set($value)
		               ->where(array("uid", "=", $oldUid))
		               ->query();
		return $arrRet;
	}

	/**
	 * 插入膜拜信息
	 * 
	 * @param int $uid						膜拜者uid
	 * @param string $uname					膜拜者姓名
	 * @param int $lv						膜拜者等级
	 * @param int $type						膜拜类型
	 */
	public static function insertWorshipUserInfo($uid, $uname, $lv, $type)
	{		               
		// 设置属性
		$arr = array('uid' => $uid,
					 'uname' => $uname,
					 'lv' => $lv,
		             'worship_type' => $type,
		             'worship_time' => Util::getTime());

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblWorshipUser)
		               ->values($arr)->query();
		return $arrRet;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */