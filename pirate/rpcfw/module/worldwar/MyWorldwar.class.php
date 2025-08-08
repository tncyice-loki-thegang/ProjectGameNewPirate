<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyWorldwar.class.php 37636 2013-01-30 08:21:07Z ZhichaoJiang $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldwar/MyWorldwar.class.php $
 * @author $Author: ZhichaoJiang $(liuyang@babeltime.com)
 * @date $Date: 2013-01-30 16:21:07 +0800 (三, 2013-01-30) $
 * @version $Revision: 37636 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyWorldwar
 * Description : 跨服赛个人数据持有类
 * Inherit     :
 **********************************************************************************************************************/
class MyWorldwar
{
	private $m_user_world_war;					// 跨服赛个人数据
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * 
	 * @return MyWorldwar
	 */
	public static function getInstance()
	{
   		self::$_instance = new self();
  		return self::$_instance;
	}

	/**
	 * 毁掉单例，单元测试对应
	 */
	public static function release() 
	{
		if (self::$_instance != null) 
		{
			self::$_instance = null;
		}
	}

	/**
	 * 构造函数，获取 session 信息
	 */
	private function __construct() 
	{
		// 从 session 中取得用户跨服赛信息
		$worldwarInfo = array();
		// 获取用户ID，使用用户ID获取用户跨服赛信息
		$uid = RPCContext::getInstance()->getUid();
		// 如果没顺利取得用户跨服赛信息
		if (empty($worldwarInfo))
		{
			if (empty($uid)) 
			{
				Logger::warning('Can not get user world war info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户跨服赛信息
			$worldwarInfo = WorldwarDao::getUserWorldWarInfo($uid);
			// 如果没有记录, 则创建一条没有报名的初始记录
			if(empty($worldwarInfo))
			{
				$worldwarInfo = WorldwarDao::insertUserWorldWar($uid);
			}
		}
		// 赋值给自己
		$this->m_user_world_war = $worldwarInfo;
	}

	/**
	 * 获取用户跨服赛信息
	 */
	public function getUserWorldWarInfo()
	{
		// 获取是第几届
		$session = WorldwarUtil::getSession();
		// 如果上届已经报名了，而且
		if (!empty($session) &&
			!empty(btstore_get()->WORLDWAR[$session]['time'][WorldwarDef::SIGNUP]['start']))
		{
			// 获取这一届报名开始时间
			$signStartTime = btstore_get()->WORLDWAR[$session]['time'][WorldwarDef::SIGNUP]['start'];
			// 如果现在时间已经大于报名时间，并且此人已经报过名的话
			if (Util::getTime() > $signStartTime && 
				(($this->m_user_world_war['sign_time'] != 0 && $this->m_user_world_war['sign_time'] <= $signStartTime) || 
				 ($this->m_user_world_war['cheer_time'] != 0 && $this->m_user_world_war['cheer_time'] <= $signStartTime)))
			{
				// 重置几项数据
				$this->m_user_world_war['va_world_war']['cheer'] = array();
				$this->m_user_world_war['va_world_war']['replay'] = array();
				self::save(array('team' => 0,
								 'win_team_lose_times' => 0,
								 'lose_team_lose_times' => 0,
								 'cheer_uid' => 0,
								 'cheer_uid_server_id' => 0,
								 'cheer_time' => 0,
								 'sign_time' => 0,
								 'va_world_war' => $this->m_user_world_war['va_world_war']));
			}
		}
		// 返回前端
		return $this->m_user_world_war;
	}

	/**
	 * 清除CD时间
	 */
	public function resetCdTime()
	{
		// 清除更新时间
		self::save(array('update_fmt_time' => 0));
	}

	/**
	 * 报名
	 */
	public function signUp()
	{
		// 设置报名时间、重置组别信息、重置失败次数、填写参与的届数、重置助威对象和战报信息
		$this->m_user_world_war['va_world_war']['cheer'] = array();
		$this->m_user_world_war['va_world_war']['replay'] = array();
		self::save(array('sign_time' => Util::getTime(),
						 'team' => 0,
						 'win_team_lose_times' => 0,
						 'lose_team_lose_times' => 0,
						 'cheer_uid' => 0,
						 'cheer_uid_server_id' => 0,
						 'cheer_time' => 0,
						 'sign_session' => WorldwarUtil::getSession(),
						 'va_world_war' => $this->m_user_world_war['va_world_war']));
	}

	/**
	 * 更新战斗信息
	 */
	public function updateBattleInfo($battlePara, $isUpTime = true)
	{
		// 设置更新时间、 设置战斗信息
		$this->m_user_world_war['va_world_war']['fight_para'] = $battlePara;
		if($isUpTime)
		{
			self::save(array('update_fmt_time' => Util::getTime(),
						 'va_world_war' => $this->m_user_world_war['va_world_war']));
		}
		else 
		{
			self::save(array('va_world_war' => $this->m_user_world_war['va_world_war']));
		}

	}
	
	/**
	 * 更新战斗结果
	 */
	public function saveTeam($team)
	{
		// 保存用户的战斗结果
		self::save(array('team' => $team));
	}
	
	/**
	 * 更新失败次数
	 */
	public function addLoseTime($team)
	{
		// 增加一次用户的失败次数
		if($team == WorldwarDef::TEAM_WIN)
		{
			Logger::debug('The team is %s.(winner)', WorldwarDef::TEAM_WIN);
			self::save(array('win_team_lose_times' => ++$this->m_user_world_war['win_team_lose_times']));
		}
		else if ($team == WorldwarDef::TEAM_LOSE) 
		{
			Logger::debug('The team is %s.(loser)', WorldwarDef::TEAM_WIN);
			self::save(array('lose_team_lose_times' => ++$this->m_user_world_war['lose_team_lose_times']));
		}
	}

	/**
	 * 更新助威信息
	 */
	public function updateCheerInfo($objUid, $objUname, $round, $type, $serverId = 0)
	{
		// 助威对象、助威对象的所在服务器ID、助威时刻、历史记录
		if (empty($serverId))
		{
			$serverId = WorldwarUtil::getServerID();
		}
		Logger::debug('The cheer info is %s %s %s %s.', $objUid, $objUname, $type, $round);
		Logger::debug('The update cheer info is %s.', $this->m_user_world_war);
		$this->m_user_world_war['va_world_war']['cheer'][$round] = array('uid' => $objUid,
																		 'uname' => $objUname,
																		 'type' => $type,
																		 'server_id' => $serverId);
		self::save(array('cheer_uid' => $objUid,
						 'cheer_uid_server_id' => $serverId,
						 'cheer_time' => Util::getTime(),
						 'va_world_war' => $this->m_user_world_war['va_world_war']));
	}

	/**
	 * 初始化助威信息
	 */
	public function initCheerInfo()
	{
		// 助威对象、助威对象的所在服务器ID、助威时刻、历史记录
		self::save(array('cheer_uid' => 0,
						 'cheer_uid_server_id' => 0,
						 'cheer_time' => 0));
	}
	
	/**
	 * 更新战报
	 */
	public function updateReplay($replay)
	{
		// 战报信息, 战报ID作为key
		$this->m_user_world_war['va_world_war']['replay'][$replay['replay']] = $replay;
		self::save(array('va_world_war' => $this->m_user_world_war['va_world_war']));
	}

	/**
	 * 发放奖励
	 */
	public function sendGroupReward($rewardID, $type)
	{
		// 判断是服内赛还是跨服赛
		if ($type == WorldwarDef::TYPE_GROUP)
		{
			// 服内赛奖励ID
			// 领取服内赛奖励时刻
			self::save(array('group_prize_id' => $rewardID, 'group_prize_time' => 0));
		}
		// 发放跨服赛奖励
		else if ($type == WorldwarDef::TYPE_WORLD)
		{
			// 跨服赛奖励ID
			// 领取跨服赛奖励时刻
			self::save(array('world_prize_id' => $rewardID, 'world_prize_time' => 0));
		}
	}

	/**
	 * 获取奖励
	 */
	public function getPrize($type)
	{
		// 领取奖励
		if ($type == WorldwarDef::TYPE_GROUP)
		{
			self::save(array('group_prize_time' => Util::getTime()));
		}
		// 没领取过的话，获取奖励ID
		else if ($type == WorldwarDef::TYPE_WORLD)
		{
			self::save(array('world_prize_time' => Util::getTime()));
		}
	}

	/**
	 * 膜拜
	 */
	public function worship()
	{
		// 因为现在只有一次，所以膜拜次数暂不设置
		// 设置膜拜时刻
		self::save(array('worship_time' => Util::getTime()));
	}

	/**
	 * 将数据保存到数据库
	 */
	public function save($set)
	{
		Logger::debug('Save uid is %s.', $this->m_user_world_war['uid']);
		// 更新到数据库
		WorldwarDao::updUserWorldWarInfo($set, $this->m_user_world_war['uid']);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */