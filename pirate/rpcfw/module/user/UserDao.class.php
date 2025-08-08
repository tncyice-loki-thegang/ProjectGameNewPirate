<?php
/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: UserDao.class.php 36483 2013-01-21 03:16:07Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/UserDao.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-21 11:16:07 +0800 (一, 2013-01-21) $
 * @version $Revision: 36483 $
 * @brief
 *
 **/



class UserDao
{
	const tblUser = 't_user';
	const tblRandomName = 't_random_name';

	private static $notDel = array('status', '!=', UserDef::STATUS_DELETED);

	const STATUS_SUSPEND = 1;
	const STATUS_DEL = 2;

	public static function getUsers($pid, $arrField)
	{
		$arrField = array_merge($arrField);
		$where = array("pid", "=", $pid);
		$whereDtime = array('dtime', '>', Util::getTime());
		$data = new CData();
		$data->select($arrField)->from(self::tblUser)
				->where($where)->where(self::$notDel)->where($whereDtime);

		if(defined('GameConf::MERGE_SERVER_OPEN_DATE'))
		{
			$serverId = RPCContext::getInstance()->getSession('global.serverId');
			$data->where('server_id', '=', $serverId);
		}
		$arrRet = $data->query();
		return $arrRet;
	}

	public static function getUserFieldsByUid($uid, $arrField, $ignoreDtime = false)
	{
		$arrField = array_merge($arrField);
		$data = new CData();
		$where = array('uid', '=', $uid);		
		$arrRet = $data->select($arrField)->from(self::tblUser)
				->where($where)->where(self::$notDel);				
		if (!$ignoreDtime)
		{
			$whereDtime = array('dtime', '>', Util::getTime());
			$data->where($whereDtime);
		}
		$arrRet = $data->query();
		if (empty($arrRet))
		{
			return $arrRet;
		}
		return $arrRet[0];
	}

	public static function getUserFieldsByUidPid($uid, $pid, $arrField, $noCache=false)
	{
		$arrField = array_merge($arrField);
		$data = new CData();
		$whereUid = array('uid', '=', $uid);
		$wherePid = array('pid', '=', $pid);
		$whereDtime = array('dtime', '>', Util::getTime());
		$data->select($arrField)->from(self::tblUser)->where($whereDtime)
				->where($whereUid)->where($wherePid)->where(self::$notDel);
		if ($noCache)
		{
			$data->noCache();
		}
		$arrRet = $data->query();
		if (empty($arrRet))
		{
			return $arrRet;
		}
		return $arrRet[0];
	}

	public static function getUsersNum($pid)
	{
		$data = new CData();
		$where = array('pid', '=', $pid);
		$whereDtime = array('dtime', '>', Util::getTime());
		$data->selectCount()->from(self::tblUser)->where($whereDtime)
					->where($where)->where(self::$notDel);
					
		if(defined('GameConf::MERGE_SERVER_OPEN_DATE'))
		{
			$serverId = RPCContext::getInstance()->getSession('global.serverId');
			$data->where('server_id', '=', $serverId);
		}
		$arrRet = $data->query();
		
		return $arrRet[0]['count'];
	}

	public static function updateUser($uid, $arrUserInfo)
	{
		$data = new CData();
		$where = array('uid', '=', $uid);		
		$arrRet = $data->update(self::tblUser)->set($arrUserInfo)
					->where($where)->where(self::$notDel)->query();
	}

	public static function updateDecIncUser($uid, $arrField)
	{
		$arrField = array_merge($arrField);
		$data = new CData();
		$where = array('uid', '=', $uid);
		$arrDecInc = array();
		foreach ($arrField as $key=>$value)
		{
			if ($value>0)
			{
				$arrDecInc[$key] = new IncOperator($value);
			}
			else
			{
				$arrDecInc[$key] = new DecOperator($value);
			}
		}
		$arrRet = $data->update(self::tblUser)->set($arrDecInc)
			->where($where)->where(self::$notDel)->query();
	}

	public static function createUser($arrUserInfo)
	{
		$data = new CData();
		if(defined('GameConf::MERGE_SERVER_OPEN_DATE'))
		{
			$arrUserInfo['server_id'] = RPCContext::getInstance()->getSession('global.serverId');
			$arrUserInfo['uname'] .= Util::getSuffixName();
		}		
		
		$arrRet = $data->insertIgnore(self::tblUser)->values($arrUserInfo)->query();
		return $arrRet;
	}

	public static function getRandomName($arrField, $gender, $limit, $offset)
	{
		$data = new CData();
		$where = array('status', '=', UserDef::RANDOM_NAME_STATUS_OK);
		$arrRet = $data->select($arrField)->from(self::tblRandomName)
				->where('gender', '=', $gender)
				->where($where)->limit($offset,$limit)->query();
		return $arrRet;
	}
	
	public static function countRandomName($gender)
	{
		$data = new CData();
		$arrRet = $data->selectCount()->from(self::tblRandomName)->where('gender', '=', $gender)
			->where('status', '=', UserDef::RANDOM_NAME_STATUS_OK)->query();
		return $arrRet[0]['count'];
	}

	public static function setRandomNameStatus($name, $status)
	{
		$data = new CData();
		$where = array('name', '==', $name);
		$arrRet = $data->update(self::tblRandomName)->set(array('status'=>$status))
					->where($where)->query();
		return $arrRet;
	}

	public static function unameToUid($uname)
	{
		$data = new CData();
		$where = array('uname', '==', $uname);
		$ret = $data->select(array('uid'))->from(self::tblUser)->where($where)->
			where(self::$notDel)->query();
		if (!empty($ret))
		{
			return $ret[0]['uid'];
		}
		return 0;
	}

	/**
	 * 返回用户最少的groupId
	 * Enter description here ...
	 */
	public static function getMinNumGroupId()
	{
		$retGroupId = key(GroupConf::$GROUP);
		$minNum = 999999999;
		foreach (GroupConf::$GROUP as $groupId => $tmp)
		{
			$data = new CData();
			$ret = $data->selectCount()->from(self::tblUser)->where('group_id', '=', $groupId)
				->where(self::$notDel)->query();
			$ret = $ret[0]['count'];
			if ($ret < $minNum)
			{
				$retGroupId = $groupId;
				$minNum = $ret;
			}
		}
		return $retGroupId;
	}
	
	public static function getTopPrestige($offset, $limit, $arrField)
	{
		$arrField = array_merge($arrField);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::tblUser)->where('uid', '>', '0')
			->orderBy('prestige_num', false)->orderBy('uid', true)
			->limit($offset, $limit)->query();
		return $arrRet;
	}
	
	public static function getTopPrestigeUnstable($offset, $limit, $arrField)
	{
		$arrField = array_merge($arrField);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::tblUser)->where('uid', '>', 0)
			->orderBy('prestige_num', false)->limit($offset, $limit)->query();
		return $arrRet;
	}
	
	public static function getByPrestige($prestige, $arrField, $num)
	{
		$arrField = array_merge($arrField);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::tblUser)->where('uid', '>', '0')
			->where('prestige_num', '=',  $prestige)->orderBy('uid', true)->query();
		return $arrRet;		
	}
	
	public static function getOrderPrestige($prestige, $uid)
	{
		$data = new CData();
		$arrRet = $data->selectCount()->from(self::tblUser)->where('prestige_num', '>', $prestige)->query();
		$count = $arrRet[0]['count'];

		$arrRet = $data->selectCount()->from(self::tblUser)
			->where('prestige_num', '=', $prestige)->where('uid', '<=', $uid)->query();
		$count += $arrRet[0]['count'];
		return $count;
	}
	
	public static function getArrUser($offset, $limit, $arrField)
	{
		$arrField = array_merge($arrField);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::tblUser)->orderBy('uid', true)
			->limit($offset, $limit)->where('uid', '>', FrameworkConfig::MIN_UID-1)->query();
		return $arrRet;
	}
	
	/**
	 * 根据uid升序返回limit个用户信息， 从$offsetUid（包括）开始
	 * Enter description here ...
	 * @param unknown_type $offsetUid
	 * @param unknown_type $limit
	 * @param unknown_type $arrField
	 */
	public static function getArrUserByOffsetUid($offsetUid, $limit, $arrField)
	{
		$arrField = array_merge($arrField);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::tblUser)->orderBy('uid', true)
			->limit(0, $limit)->where('uid', '>', $offsetUid-1)->query();
		return $arrRet;
	}
	
	public static function getArrUserByPid($arrPid, $arrField)
	{
		$arrField = array_merge($arrField);
		$data = new CData();		
		$data->select($arrField)->from(self::tblUser)->where('pid', 'in', $arrPid)
			->where('uid', '>', 0);
		
		if(defined('GameConf::MERGE_SERVER_OPEN_DATE'))
		{
			$serverId = Util::getServerId();
			$data->where('server_id', '=', $serverId);
		}
		
		$ret = $data->query();
		return $ret;
	}
	
	public static function getByUname($uname, $arrField)
	{
		$arrField = array_merge($arrField);
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblUser)
			->where('uname', '==', $uname)->where('uid', '>', '0')->query();
		if (!empty($ret))
		{
			return $ret[0];
		}
		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */