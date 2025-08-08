<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GroupWarDAO.class.php 37349 2013-01-28 12:03:47Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/groupwar/GroupWarDAO.class.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-01-28 20:03:47 +0800 (一, 2013-01-28) $
 * @version $Revision: 37349 $
 * @brief 
 *  
 **/
class GroupWarDAO
{
	public static $USER_KEY_2_DB = array(
			'uid' => GroupWarDef::GW_SQL_UID,
			'uname' => GroupWarDef::GW_SQL_UNAME,
			'battleId' =>GroupWarDef::GW_SQL_BATTLE_ID,
			'groupId' => GroupWarDef::GW_SQL_GROUP_ID,
			'killNum' => GroupWarDef::GW_SQL_KILL_NUM,
			'winStreak' => GroupWarDef::GW_SQL_WIN_STREAK,
			'score' => GroupWarDef::GW_SQL_SCORE,
			'honour' => GroupWarDef::GW_SQL_HONOUR,
			'belly' => GroupWarDef::GW_SQL_BELLY,
			'experience' => GroupWarDef::GW_SQL_EXP,
			'prestige' => GroupWarDef::GW_SQL_PRESTIGE,
			'resource' => GroupWarDef::GW_SQL_RESOURCE,
			'soul' => GroupWarDef::GW_SQL_SOUL,
			'scoreTime' => GroupWarDef::GW_SQL_SCORE_TIME,	
			'removeJoinCd' => GroupWarDef::GW_SQL_REMOVE_JOIN_CD,
			'maxFightForce' => GroupWarDef::GW_SQL_MAX_FIGHT_FORCE,
			);
	public static $DB_2_USER_KEY = array(
			GroupWarDef::GW_SQL_UID => 'uid',
			GroupWarDef::GW_SQL_UNAME => 'uname',
			GroupWarDef::GW_SQL_BATTLE_ID => 'battleId',
			GroupWarDef::GW_SQL_GROUP_ID => 'groupId',
			GroupWarDef::GW_SQL_KILL_NUM => 'killNum',
			GroupWarDef::GW_SQL_WIN_STREAK => 'winStreak',
			GroupWarDef::GW_SQL_SCORE => 'score',
			GroupWarDef::GW_SQL_HONOUR => 'honour',
			GroupWarDef::GW_SQL_BELLY => 'belly',
			GroupWarDef::GW_SQL_EXP => 'experience',
			GroupWarDef::GW_SQL_PRESTIGE => 'prestige',
			GroupWarDef::GW_SQL_RESOURCE => 'resource',
			GroupWarDef::GW_SQL_SOUL => 'soul',
			GroupWarDef::GW_SQL_SCORE_TIME => 'scoreTime',
			GroupWarDef::GW_SQL_REMOVE_JOIN_CD => 'removeJoinCd',
			GroupWarDef::GW_SQL_MAX_FIGHT_FORCE => 'maxFightForce',
	);
	
	/**
	 * 获取阵营数据
	 */
	public static function getAllGroupInfo()
	{
		$select = array(
				GroupWarDef::GW_SQL_GROUP_ID, 
				GroupWarDef::GW_SQL_RESOURCE,
				GroupWarDef::GW_SQL_ENTER_NUM);
		
		$where = array(GroupWarDef::GW_SQL_GROUP_ID, '>', 0);
				
		$data = new CData();
		$ret = $data->select($select)->from(GroupWarDef::GW_SQL_RESOURCE_TABLE)
					->where($where)->query();
			
		$returnData = array();
		foreach($ret as $value )
		{
			$returnData[] = array(
					'groupId' => $value[GroupWarDef::GW_SQL_GROUP_ID],
					'resource' => $value[GroupWarDef::GW_SQL_RESOURCE],
					'enterNum' => $value[GroupWarDef::GW_SQL_ENTER_NUM]);
		}
		return $returnData;
	}
	

	/**
	 * 更新阵营数据
	 * @param array $grouInfo
	 * 
	 */
	public static function updateGroupInfo($grouInfo)
	{
		$values = array();
		
		if(isset($grouInfo['resource']))
		{
			$values[GroupWarDef::GW_SQL_RESOURCE] = $grouInfo['resource'];
		}
		if(isset($grouInfo['enterNum']))
		{
			$values[GroupWarDef::GW_SQL_ENTER_NUM] = $grouInfo['enterNum'];
		}
		
	
		$where = array(GroupWarDef::GW_SQL_GROUP_ID, '=', $grouInfo['groupId']);
		$data = new CData();
	
		try
		{
			$ret= $data->update(GroupWarDef::GW_SQL_RESOURCE_TABLE)->set($values)
			->where($where)->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('GroupWarDAO.updateGroupInfo failed!  err:%s ', $e->getMessage ());
			return false;
		}
		return $ret;
	}
	
	/**
	 * 重置阵营数据
	 * @param array $value
	 *
	 */
	public static function resetGroupInfo($value)
	{
		$values = array();
		$values[GroupWarDef::GW_SQL_RESOURCE] = $value['resource'];
	
		$where = array(GroupWarDef::GW_SQL_GROUP_ID, '>', 0);
		$data = new CData();
	
		try
		{
			$ret= $data->update(GroupWarDef::GW_SQL_RESOURCE_TABLE)->set($values)
			->where($where)->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('GroupWarDAO.updateGroupInfo failed!  err:%s ', $e->getMessage ());
			return false;
		}
		return $ret;
	}
	
	
	/**
	 * 获取用户数据
	 * @param unknown_type $uid
	 */
	public static function getUserInfo($uid)
	{
		$select = array( 
						GroupWarDef::GW_SQL_UID,
						GroupWarDef::GW_SQL_BATTLE_ID,
						GroupWarDef::GW_SQL_GROUP_ID,
						GroupWarDef::GW_SQL_KILL_NUM,
						GroupWarDef::GW_SQL_WIN_STREAK,
				 		GroupWarDef::GW_SQL_SCORE,
						GroupWarDef::GW_SQL_HONOUR,
						GroupWarDef::GW_SQL_BELLY,
						GroupWarDef::GW_SQL_EXP,
						GroupWarDef::GW_SQL_PRESTIGE,
						GroupWarDef::GW_SQL_RESOURCE,
						GroupWarDef::GW_SQL_SOUL,
						GroupWarDef::GW_SQL_SCORE_TIME,
						GroupWarDef::GW_SQL_REMOVE_JOIN_CD,
						GroupWarDef::GW_SQL_MAX_FIGHT_FORCE
						);
		
		$where = array ( GroupWarDef::GW_SQL_UID, '=', $uid );
		
		$data = new CData();
		$ret = $data->select($select)->from(GroupWarDef::GW_SQL_USER_TABLE)
					->where($where)->query();
		
		if ( empty($ret) )
		{
			return array();
		}
		else
		{
			$returnData = array();
			foreach($select as $key)
			{
				$returnData[self::$DB_2_USER_KEY[$key]] = $ret[0][$key];
			}
			return $returnData;
		}	
	}
	
	/**
	 * 获取所有参战用户
	 * @param array $battleIdList,指定是参见那场战斗
	 * @param bool $join 是否只要参过战的人
	 */
	public static function getAllEnterUser($battleIdList = array(), $join = false)
	{
		$select = array(
				GroupWarDef::GW_SQL_UID,
				GroupWarDef::GW_SQL_BATTLE_ID,
				GroupWarDef::GW_SQL_GROUP_ID,
				GroupWarDef::GW_SQL_KILL_NUM,
				GroupWarDef::GW_SQL_WIN_STREAK,
		 		GroupWarDef::GW_SQL_SCORE,
				GroupWarDef::GW_SQL_HONOUR,
				GroupWarDef::GW_SQL_BELLY,
				GroupWarDef::GW_SQL_EXP,
				GroupWarDef::GW_SQL_PRESTIGE,
				GroupWarDef::GW_SQL_RESOURCE,
				GroupWarDef::GW_SQL_SOUL,
				GroupWarDef::GW_SQL_SCORE_TIME,
				GroupWarDef::GW_SQL_MAX_FIGHT_FORCE
		);
		
		$wheres = array();
		if(empty($battleIdList))
		{
			$wheres[] = array ( GroupWarDef::GW_SQL_BATTLE_ID, '>', 0 );
		}
		else
		{
			$wheres[] = array ( GroupWarDef::GW_SQL_BATTLE_ID, 'IN', $battleIdList );
		}
		if($join)
		{
			$wheres[] = array ( GroupWarDef::GW_SQL_SCORE, '>', 0 );
		}
		
		$data = new CData();
		$data->select($select)->from(GroupWarDef::GW_SQL_USER_TABLE);
		foreach ( $wheres as $where )
		{
			$data->where($where);
		}
		$ret = $data->query();
		
		$returnData = array();
		foreach ($ret as $value)
		{
			$data = array();
			foreach($select as $key )
			{
				$data[self::$DB_2_USER_KEY[$key]] = $value[$key];
			}
			$returnData[] = $data;
		}			
		return $returnData;
	}
	
	/**
	 * 获取参战用户的数目
	 * @param array $battleIdList,指定是参见那场战斗
	 * @param bool $join 是否只要参过战的人
	 */
	public static function getLastRoundEnterNum()
	{	
		$ret = self::getAllGroupInfo();
		
		$num = 0;
		foreach( $ret as $value)
		{
			$num += $value['enterNum'];
		}
		return $num;
	}
	
	/**
	 * 获取积分的前N名
	 */
	public static function getScoreTopN($topN)
	{
		$select = array(
				GroupWarDef::GW_SQL_UID,
				GroupWarDef::GW_SQL_UNAME,
				GroupWarDef::GW_SQL_SCORE
		);
		
		$where = array ( GroupWarDef::GW_SQL_BATTLE_ID, '>', 0 );
		
		$data = new CData();
		$ret = $data->select($select)->from(GroupWarDef::GW_SQL_USER_TABLE)->where($where);
		
		$data->orderBy(GroupWarDef::GW_SQL_SCORE, FALSE);
		$data->orderBy(GroupWarDef::GW_SQL_KILL_NUM, FALSE);
		$data->orderBy(GroupWarDef::GW_SQL_SCORE_TIME, TRUE);
		$data->orderBy(GroupWarDef::GW_SQL_UID, TRUE);
		$data->limit(0, $topN);
		$ret = $data->query();
		
		$returnData = array();
		foreach ($ret as $value)
		{
			$returnData[] =  array(
						'uid' => $value[GroupWarDef::GW_SQL_UID],
						'uname' => $value[GroupWarDef::GW_SQL_UNAME],
						'score' => $value[GroupWarDef::GW_SQL_SCORE]					
						);
		}
		return $returnData;
	}
	
	/**
	 * 设置用户信息
	 * @param array $userInfo
	 * 
	 */
	public static function setUserInfo($userInfo)
	{		
		$values = array(
				GroupWarDef::GW_SQL_UID => $userInfo['uid'],
				GroupWarDef::GW_SQL_UNAME => $userInfo['uname'],
				GroupWarDef::GW_SQL_BATTLE_ID => 0,
				GroupWarDef::GW_SQL_GROUP_ID => 0,
				GroupWarDef::GW_SQL_KILL_NUM => 0,
				GroupWarDef::GW_SQL_WIN_STREAK => 0,
				GroupWarDef::GW_SQL_SCORE => 0,
				GroupWarDef::GW_SQL_HONOUR => 0,
				GroupWarDef::GW_SQL_BELLY => 0,
				GroupWarDef::GW_SQL_EXP => 0,
				GroupWarDef::GW_SQL_PRESTIGE => 0,
				GroupWarDef::GW_SQL_RESOURCE => 0,
				GroupWarDef::GW_SQL_SOUL => 0,
				GroupWarDef::GW_SQL_SCORE_TIME => 0,
				GroupWarDef::GW_SQL_REMOVE_JOIN_CD => 0,
				GroupWarDef::GW_SQL_MAX_FIGHT_FORCE => 0
				);
		foreach($userInfo as $key => $value)
		{
			$values[ self::$USER_KEY_2_DB[$key] ] = $value;
		}		
		
		$arrKey = array ( 
				GroupWarDef::GW_SQL_BATTLE_ID,
				GroupWarDef::GW_SQL_GROUP_ID,
				GroupWarDef::GW_SQL_KILL_NUM,
				GroupWarDef::GW_SQL_WIN_STREAK,
				GroupWarDef::GW_SQL_SCORE,
				GroupWarDef::GW_SQL_HONOUR,
				GroupWarDef::GW_SQL_BELLY,
				GroupWarDef::GW_SQL_EXP,
				GroupWarDef::GW_SQL_PRESTIGE,
				GroupWarDef::GW_SQL_RESOURCE,
				GroupWarDef::GW_SQL_SOUL,
				GroupWarDef::GW_SQL_SCORE_TIME,
				GroupWarDef::GW_SQL_REMOVE_JOIN_CD
				);
		//千万别更新：GroupWarDef::GW_SQL_MAX_FIGHT_FORCE
	
		$data = new CData();
		try
		{
			$ret = $data->insertOrUpdate ( GroupWarDef::GW_SQL_USER_TABLE )
			->values ( $values )->onDuplicateUpdateKey ($arrKey )->query ();
		}
		catch (Exception $e)
		{
			Logger::FATAL('GroupWarDAO.setUserInfo failed!  err:%s ', $e->getMessage ());
			return false;
		}
		return $ret;
	}
	
	public static function updateUserInfo($userInfo)
	{	
		$values = array();
		
		foreach($userInfo as $key => $value)
		{
			$values[ self::$USER_KEY_2_DB[$key] ] = $value;
		}
		
		$where = array(GroupWarDef::GW_SQL_UID, '=', $userInfo['uid']);
		
		$data = new CData();
		
		try
		{
			$ret= $data->update(GroupWarDef::GW_SQL_USER_TABLE)->set($values)
			->where($where)->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('GroupWarDAO.updateUserInfo failed!  err:%s ', $e->getMessage ());
			return false;
		}
		return $ret;
	}

	public static function resetUserInfo($data = NULL)
	{
		$values = array();
		if(empty($data))
		{
			$values = array(
					GroupWarDef::GW_SQL_BATTLE_ID => 0,
					GroupWarDef::GW_SQL_GROUP_ID => 0,
					GroupWarDef::GW_SQL_KILL_NUM => 0,
					GroupWarDef::GW_SQL_WIN_STREAK => 0,
					GroupWarDef::GW_SQL_SCORE => 0,
					GroupWarDef::GW_SQL_HONOUR => 0,
					GroupWarDef::GW_SQL_BELLY => 0,
					GroupWarDef::GW_SQL_EXP => 0,
					GroupWarDef::GW_SQL_PRESTIGE => 0,
					GroupWarDef::GW_SQL_RESOURCE => 0,
					GroupWarDef::GW_SQL_SOUL => 0,
					GroupWarDef::GW_SQL_SCORE_TIME => 0,
					GroupWarDef::GW_SQL_REMOVE_JOIN_CD => 0
					);
		}
		else
		{
			foreach($data as $key => $value)
			{
				$values[ self::$USER_KEY_2_DB[$key] ] = $value;
			}
		}
		$where = array(GroupWarDef::GW_SQL_UID, '>', 0);
		$data = new CData();
		try
		{
			$ret= $data->update(GroupWarDef::GW_SQL_USER_TABLE)->set($values)
					->where($where)->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('GroupWarDAO.resetUserInfo failed!  err:%s ', $e->getMessage ());
			return false;
		}
		return $ret;
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */