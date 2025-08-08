<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UserDao.class.php 31205 2012-11-19 08:33:40Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/mergeServer/module/MergeServer/UserDao.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-11-19 16:33:40 +0800 (一, 2012-11-19) $
 * @version $Revision: 31205 $
 * @brief
 *
 **/

class UserDao
{
	public static function getUser($game_id, $start_id, $limit)
	{
		$mysql = MysqlManager::getMysql($game_id);
		$return = $mysql->query("select * from t_user where uid > $start_id order by uid asc limit 0, $limit;");
		return $return;
	}

	public static function setRetainUser($target_game_id, $game_id, $uid, $pid, $name)
	{
		$mysql = MysqlManager::getMysql($target_game_id);
		$return = $mysql->query("insert into t_tmp_user (uid, game_id, pid, name, deal) values ($uid, '$game_id', $pid, '$name', 0);");
	}

	public static function setDealUser($target_game_id, $game_id, $uid, $new_uid)
	{
		$mysql = MysqlManager::getMysql($target_game_id);
		$return = $mysql->query("update t_tmp_user set deal = 1, new_uid = $new_uid where uid = $uid and game_id = '$game_id'");
	}

	public static function getRetainUser($target_game_id, $game_id, $start_id, $limit)
	{
		$mysql = MysqlManager::getMysql($target_game_id);
		$return = $mysql->query("select * from t_tmp_user where uid > $start_id and game_id = '$game_id' order by uid asc limit 0, $limit;");
		return $return;
	}

	public static function getHero($game_id , $hid)
	{
		$mysql = MysqlManager::getMysql($game_id);
		$return = $mysql->query("select * from t_hero where hid = $hid");
		if (!empty($return))
		{
			return $return[0];
		}
		return $return;
	}

	public static function getChargeUid($game_id)
	{
		//select distinct(uid) from t_bbpay_gold where order_id not like 'test%'
		$mysql = MysqlManager::getMysql($game_id);
		$return = $mysql->query("select distinct(uid) from t_bbpay_gold where order_id not like 'TEST%'");

		$ret = array();
		foreach ($return as $tmp)
		{
			$ret[] = $tmp['uid'];
		}

		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */