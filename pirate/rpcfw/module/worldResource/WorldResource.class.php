<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldResource.class.php 28372 2012-10-10 02:40:59Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldResource/WorldResource.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-10-10 10:40:59 +0800 (三, 2012-10-10) $
 * @version $Revision: 28372 $
 * @brief
 *
 **/




class WorldResource implements IWorldResource
{
	/* (non-PHPdoc)
	 * @see IWorldResource::cancelSignup()
	 */
	public function cancelSignup($world_resource_id) {

	}

	/* (non-PHPdoc)
	 * @see IWorldResource::signup()
	 */
	public function signup($world_resource_id) {
		//格式化输入
		$world_resource_id = intval($world_resource_id);

		$uid = RPCContext::getInstance()->getUid();

		$return = array(WorldResourceDef::WR_ERROR_CODE_NAME => WorldResourceDef::WR_ERROR_CODE_INVALID);

		//是否在报名时间内
		if ( self::inSignUpTime() == FALSE )
		{
			Logger::DEBUG('not in signup time!start:%s, end:%s',
				date('Y-m-d H:i:s', self::getSignupStartTime()),
				date('Y-m-d H:i:s', self::getSignupEndTime()));
			$return[WorldResourceDef::WR_ERROR_CODE_NAME] = WorldResourceDef::WR_ERROR_CODE_NOT_IN_SIGNUP_TIME;
			return $return;
		}

		//是否有权限进行操作
		if ( self::isGuildRight() == FALSE )
		{
			return $return;
		}

		//公会等级是否足够
		$guildInfo = GuildLogic::getRawGuildInfo($uid);
		if ( empty($guildInfo) )
		{
			Logger::DEBUG('no guild!');
			return $return;
		}

		$guild_level = $guildInfo['guild_level'];
		if ( self::getAttackReqGuildLevel($world_resource_id) > $guild_level )
		{
			Logger::DEBUG('guild level is not enough!');
			return $return;
		}

		//检测是否该工会是否当前占有该资源
		$guild_id = $guildInfo['guild_id'];
		if ( WorldResourceDAO::getOccupyGuildID($world_resource_id) == $guild_id )
		{
			$return[WorldResourceDef::WR_ERROR_CODE_NAME] = WorldResourceDef::WR_ERROR_CODE_HAS_RESOURCE;
			return $return;
		}

		//得到锁
		$locker = new Locker();
		$locker->lock(WorldResourceDef::WR_SIGNUP_LOCKER_NAME);

		//是否已经报名了该资源
		$list = WorldResourceDAO::getAttackWorldResourceListByGuildId($guild_id,
			self::getSignupStartTime(), self::getSignupEndTime());
		$list = Util::arrayExtract($list, WorldResourceDef::WR_SQL_RESOURCE_ID);
		if ( in_array($world_resource_id, $list) )
		{
			$return[WorldResourceDef::WR_ERROR_CODE_NAME] = WorldResourceDef::WR_ERROR_CODE_ALREADY_SIGNUP;
			//释放锁
			$locker->unlock(WorldResourceDef::WR_SIGNUP_LOCKER_NAME);
			return $return;
		}

		//是否已经报名了一个资源
		if ( count($list) >= 1 )
		{
			$return[WorldResourceDef::WR_ERROR_CODE_NAME] = WorldResourceDef::WR_ERROR_CODE_ALREADY_SIGNUP_OTHER_RESOURCE;
			//释放锁
			$locker->unlock(WorldResourceDef::WR_SIGNUP_LOCKER_NAME);
			return $return;
		}

		//添加进队列
		WorldResourceDAO::addAttackList($world_resource_id, $guild_id, Util::getTime());

		$return[WorldResourceDef::WR_ERROR_CODE_NAME] = WorldResourceDef::WR_ERROR_CODE_OK;

		//添加报名结算的timer
		//@see 考虑到报名操作并不多,为了减少初始化配置项，将signup的结算timer的添加放置在这里
		//需求的改变会使得此处需要做修改
		$signup_timer_id = WorldResourceDAO::getSignupEndTimer($world_resource_id);
		if ( empty($signup_timer_id) )
		{
			$timer_id = TimerTask::addTask(0,
					self::getSignupEndTime(),
					'worldResource.signupEnd',
					array($world_resource_id));
			WorldResourceDAO::setSignupEndTimer($world_resource_id, $timer_id);
		}

		//释放锁
		$locker->unlock(WorldResourceDef::WR_SIGNUP_LOCKER_NAME);

		$return[WorldResourceDef::WR_ERROR_CODE_NAME] = WorldResourceDef::WR_ERROR_CODE_OK;

		return $return;
	}

	/* (non-PHPdoc)
	 * @see IWorldResource::enter()
	 */
	public function enter($world_resource_id) {
		//格式化输入
		$world_resource_id = intval($world_resource_id);

		$time = Util::getTime();

		$return = FALSE;

		//根据时间查询当前的场次
		$battle_start_time = self::getBattleStartTime();
		$index = -1;
		foreach ( WorldResourceConfig::$BATTLE_TIME as $key => $battle_time_offset )
		{
			$start_time = $battle_start_time + $battle_time_offset;
			$end_time = $start_time + WorldResourceConfig::SINGLE_BATTLE_DURATION;

			if ( $time >= $start_time && $time <= $end_time )
			{
				$index = $key;
				break;
			}
		}

		//如果没有任何战斗场次匹配
		if ( $index == -1 )
		{
			Logger::DEBUG('not in battle time!');
			return $return;
		}

		$list = WorldResourceDAO::getAttackList($world_resource_id,
			self::getSignupStartTime(), self::getSignupEndTime() );

		if ( !isset($list[$index]) )
		{
			Logger::DEBUG('no battle!');
			return $return;
		}

		//攻击方公会ID
		$attack_guild_id = intval($list[$index][WorldResourceDef::WR_SQL_GUILD_ID]);

		//防守方公会ID
		$defeat_guild_id = intval($list[$index][WorldResourceDef::WR_SQL_DEFEND_GUILD_ID]);

		//得到当前的公会ID
		$uid = RPCContext::getInstance()->getUid();
		$guildInfo = GuildLogic::getMemberInfo($uid);
		$guild_id = $guildInfo['guild_id'];

		//检测用户是否属于攻击方或者防守方公会
		if ( $guild_id != $attack_guild_id && $guild_id != $defeat_guild_id )
		{
			Logger::DEBUG('you are not a guild member!attack guild id:%d, defeat guild id:%d, your guild id:%d',
				$attack_guild_id, $defeat_guild_id, $guild_id);
			return $return;
		}

		//call lcserver enter
		RPCContext::getInstance()->joinGuildBattle($world_resource_id);

		return TRUE;
	}

	/* (non-PHPdoc)
	 * @see IWorldResource::leave()
	 */
	public function leave($world_resource_id) {

		//call lcserver leave
		RPCContext::getInstance()->leaveGuildBattle();

	}

	/**
	 *
	 * 报名结束(由timer调用执行,timer在此次战斗报名的第一次被添加)
	 *
	 * @param int $world_resource_id
	 *
	 * @return NULL
	 */
	public function signupEnd($world_resource_id)
	{
		//得到所有的报名列表
		$list = WorldResourceDAO::getSignupList($world_resource_id, self::getSignupStartTime(), self::getSignupEndTime());

		//将公会的周贡献取出用于排序
		foreach ( $list as $key => $value )
		{
			$guild_id = $value[WorldResourceDef::WR_SQL_GUILD_ID];
			$guild_info = GuildLogic::getRawGuildInfoById($guild_id);
			$week_contribution = intval($guild_info['week_contribute_data']);
			$list[$key]['week_contribution'] = $week_contribution;
		}

		//计算报名结果
		$signupSuccess = array();
		$list_ = $list;
		for ( $i = 0; $i < WorldResourceConfig::SIGNUP_QUEUE_MAX_LENGTH; $i++ )
		{
			$max_week_contribution = -1;
			$index = -1;
			foreach ( $list_ as $key => $value )
			{
				if ( $max_week_contribution < $value['week_contribution'] )
				{
					$max_week_contribution = $value['week_contribution'];
					$index = $key;
				}
			}
			if ( $index != -1 )
			{
				$signupSuccess[] = $index;
				unset($list_[$index]);
			}
			else
			{
				break;
			}
		}

		Logger::DEBUG('signSuccess:%s', $signupSuccess);

		//增加battle timer
		foreach ( $signupSuccess as $i => $value )
		{
			if ( empty($list[$value][WorldResourceDef::WR_SQL_BATTLE_TIMER]) )
			{
				//为每场战斗增加battle timer
				$battle_start_time = self::getBattleStartTime() + WorldResourceConfig::$BATTLE_TIME[$i];
				$battle_end_time = $battle_start_time + WorldResourceConfig::SINGLE_BATTLE_DURATION;

				$timer_id = TimerTask::addTask(0,
						$battle_start_time - WorldResourceConfig::TIMER_SHIFT,
						'worldResource.createBattle',
						array($world_resource_id,
						$list[$value][WorldResourceDef::WR_SQL_SIGNUP_ID],
						$list[$value][WorldResourceDef::WR_SQL_GUILD_ID], $battle_end_time));

				//当前的资源占有者为第一场的防守方
				$is_know_defend = FALSE;
				$defend_id = WorldResourceDef::WR_NO_OCCUPY_GUILD;
				if ( $i == 0 )
				{
					$defend_id = WorldResourceDAO::getCurOccupyGuildID($world_resource_id);
					$is_know_defend = TRUE;
				}

				WorldResourceDAO::setAttack(
					$list[$value][WorldResourceDef::WR_SQL_SIGNUP_ID],
					$is_know_defend,
					$defend_id,
					$timer_id
				);
			}
		}

		//增加战斗结束timer
		$timer_id = TimerTask::addTask(WorldResourceConfig::TIMER_USER,
					self::getBattleEndTime() - WorldResourceConfig::TIMER_SHIFT,
					'worldResource.battleEnd',
					array($world_resource_id));

		//设置battle end timer,会重置signup timer
		WorldResourceDAO::setBattleEndTimer($world_resource_id, $timer_id);

		//输出INFO日志，以方便查询和修复
		Logger::INFO('world resource:%d signup end!signup start time:%s',
			$world_resource_id, date('Y-m-d H:i:s', self::getSignupStartTime()));

	}

	/**
	 *
	 * 攻击结算
	 *
	 * @param array $args							callback指定的参数
	 * <code>
	 * {
	 * 		'signup_id':int							报名ID
	 * }
	 * </code>
	 * @param int $guild_id							占领公会ID
	 * @param int $replay_id						战斗录像ID
	 *
	 * @throws Exception
	 *
	 * @return NULL
	 */
	public function attackEnd($args, $guild_id, $replay_id)
	{
		//格式化输入
		$battle_id = intval($args['signup_id']);
		$guild_id = intval($guild_id);
		$replay_id = intval($replay_id);

		$info = WorldResourceDAO::getInfoBySignupId($battle_id);

		if ( empty($info)  )
		{
			Logger::FATAL('world resource battle! signup id:%d is not exist!', $battle_id);
			return;
		}

		$world_resource_id = intval($info[WorldResourceDef::WR_SQL_RESOURCE_ID]);
		$attack_guild_id = intval($info[WorldResourceDef::WR_SQL_GUILD_ID]);
		$battle_timer_id = intval($info[WorldResourceDef::WR_SQL_BATTLE_TIMER]);

		//占领该资源
		WorldResourceDAO::setCurOccupyGuildID($world_resource_id, $guild_id);

		//写入战斗录像
		$win = $attack_guild_id == $guild_id;
		WorldResourceDAO::setBattleReplay($battle_id, $replay_id, $win);

		//写入下个资源战的防守者
		$next_signup_id = WorldResourceDAO::getNextSignupIdByBattleId($world_resource_id, $battle_timer_id);
		if ( !empty($next_signup_id) )
		{
			WorldResourceDAO::setDefend($next_signup_id, $guild_id);
		}
	}

	/**
	 *
	 * 资源战役结算
	 *
	 * @param int $world_resource_id
	 *
	 * @throws Exception
	 */
	public function battleEnd($world_resource_id)
	{
		//如果一个公会报名前占领着一个资源,而报名另外一个资源,战斗结束后该公会成功占领了报名的资源
		//并且守住了原来的资源,则该公会将占领他报名的资源,而自动放弃守住的资源

		//得到当前的 资源信息
		$world_resource_info = WorldResourceDAO::getWorldResourceInfo($world_resource_id);

		//得到当前的胜利者(即将成为占领者)
		$cur_occupy_guild_id = $world_resource_info[WorldResourceDef::WR_SQL_CUR_GUILD_ID];
		//原先的占领者
		$occupy_guild_id = $world_resource_info[WorldResourceDef::WR_SQL_GUILD_ID];

		if ( $cur_occupy_guild_id != WorldResourceDef::WR_NO_OCCUPY_GUILD )
		{
			//得到此公会当前已经占领的世界资源
			$occupy_data = WorldResourceDAO::getWorldResourcesByGuildID($cur_occupy_guild_id);
			if ( count($occupy_data) > WorldResourceConfig::MAX_OCCUPY_RESOURCE )
			{
				Logger::FATAL("fixed me!guild:%d occupy resources:%s", $cur_occupy_guild_id, $occupy_data);
				throw new Exception('fake');
			}

			//放弃所占领的资源
			foreach ( $occupy_data as $value )
			{
				$resource_id = $value[WorldResourceDef::WR_SQL_RESOURCE_ID];

				//跳过当前资源的计算
				if ( $resource_id == $world_resource_id )
				{
					continue;
				}

				//如果放弃成功,则发送系统广播
				if ( WorldResourceDAO::resetOccupyGuildID($resource_id, $cur_occupy_guild_id,
					$value[WorldResourceDef::WR_SQL_CUR_GUILD_ID] == $cur_occupy_guild_id ) == TRUE )
				{
					//向所有人广播资源更新
					RPCContext::getInstance()->sendMsg(array(0), 're.worldResource.updateRes',
						array($resource_id, $this->worldResourceInfo($resource_id)));
				}
			}
		}

		//占领该资源
		WorldResourceDAO::setOccupyGuildID($world_resource_id, $cur_occupy_guild_id);

		if ( $occupy_guild_id != $cur_occupy_guild_id )
		{
			//向所有人广播资源更新
			RPCContext::getInstance()->sendMsg(array(0), 're.worldResource.updateRes',
				array($world_resource_id, $this->worldResourceInfo($world_resource_id)));
		}

		//发送成就相关消息
		if ( !empty($cur_occupy_guild_id) )
		{
			EnAchievements::guildNotify($cur_occupy_guild_id, AchievementsDef::GET_WORLD_RES, 1);
		}
	}

	/**
	 *
	 * 发放奖励(由crontab调用,用timer的话计算量过大,在指定时间内无法完成)
	 *
	 * @param int $world_resource_id
	 *
	 * @return NULL
	 */
	public function reward($world_resource_id)
	{
		//格式化输入
		$world_resource_id = intval($world_resource_id);

		$guild_id = WorldResourceDAO::getOccupyGuildID($world_resource_id);

		if ( $guild_id == WorldResourceDef::WR_NO_OCCUPY_GUILD )
		{
			Logger::INFO('wolrd_resource_id:%d, date:%s reward end!no occupy guild!', $world_resource_id, date('Y-m-d'));
			return;
		}

		$offset = 0;
		while ( TRUE )
		{
			//分批次拉取数据
			//@see getMemberListByGuild可能会导致拉取的数据有错.
			$members = GuildLogic::getMemberListByGuildId($guild_id, $offset, WorldResourceDef::WR_GUILD_MEMBER_MAX_NUMBER);

			$buffer = GuildLogic::getBufferByGuildId($guild_id);
			$resource_buffer = floatval($buffer['resourceAddition']) / WorldResourceDef::WR_MODULUS;

			foreach ( $members as $member )
			{
				try
				{
					$uid = $member['uid'];
					$official = $member['official'];
					$role_type = $member['role_type'];

					$user = EnUser::getUserObj($uid);
					$userInfo = $user->getUserInfo();
					$level = $userInfo['level'];

					//世界资源收益=资源点游戏币基础值*收成者等级*公会职位等级世界资源收成系数
					// *（1+公会资源矿科技百分比*科技等级）
					$belly = self::getWorldResourceOutput($world_resource_id) * $level *
						GuildUtil::officialToResourceCoef($official, $role_type) *
						( 1 + $resource_buffer);
					$belly = intval( round( $belly ) );
					$user->addBelly($belly);

					$user->update();

					//sendmail
					MailTemplate::sendWorldResourceAward($uid, $world_resource_id, $belly);
				}
				catch(Exception $e)
				{
					Logger::FATAL('send worldresource:%d reward to user:%d failed!', $world_resource_id, $uid);
					Logger::FATAL($e->getTraceAsString ());
				}
			}

			if ( count($members) < WorldResourceDef::WR_GUILD_MEMBER_MAX_NUMBER )
			{
				break;
			}
			else
			{
				$offset += WorldResourceDef::WR_GUILD_MEMBER_MAX_NUMBER;
			}
		}
		Logger::INFO('wolrd_resource_id:%d, date:%s reward end!', $world_resource_id, date('Y-m-d'));

	}

	/* (non-PHPdoc)
	 * @see IWorldResource::giveup()
	 */
	public function giveup($world_resource_id) {
		//格式化输入
		$world_resource_id = intval($world_resource_id);

		//只有在报名时间内允许放弃资源
		if ( self::inSignUpTime() == FALSE )
		{
			return FALSE;
		}

		//检测当前用户是否有权限
		if ( self::isGuildRight() == FALSE )
		{
			return FALSE;
		}

		//检测该世界资源是否属于当前用户所在的公会
		$occupy_guild_id = WorldResourceDAO::getOccupyGuildID($world_resource_id);

		$uid = RPCContext::getInstance()->getUid();
		$guildInfo = GuildLogic::getGuildInfo($uid);
		if ( empty($guildInfo) )
		{
			return FALSE;
		}

		$guild_id = $guildInfo['guild_id'];

		if ( $guild_id != $occupy_guild_id )
		{
			Logger::FATAL('world resource:%d belong to:%d not belong to guild:%d!',
				$world_resource_id, $occupy_guild_id, $guild_id);
			throw new Exception('fake');
		}

		//设置资源占领状态
		WorldResourceDAO::setOccupyGuildID($world_resource_id, WorldResourceDef::WR_NO_OCCUPY_GUILD);

		//向所有人广播资源更新
		RPCContext::getInstance()->sendMsg(array(0), 're.worldResource.updateRes',
			array($world_resource_id, $this->worldResourceInfo($world_resource_id)));

		return TRUE;
	}

	/* (non-PHPdoc)
	 * @see IWorldResource::worldResourceAttackList()
	 */
	public function worldResourceAttackList()
	{
		$start_time = self::getSignupStartTime();
		$end_time = self::getSignupEndTime();

		//如果在报名期间,则返回的时上次战斗的列表
		if ( self::inSignUpTime() == TRUE )
		{
			$start_time -= WorldResourceConfig::BATTLE_INTERVAL;
			$end_time -= WorldResourceConfig::BATTLE_INTERVAL;
		}

		$list = WorldResourceDAO::getAllAttackList($start_time, $end_time);

		$return = array();
		foreach ( $list as $data )
		{
			$world_resource_id = $data[WorldResourceDef::WR_SQL_RESOURCE_ID];
			$attack_guild_id = $data[WorldResourceDef::WR_SQL_GUILD_ID];
			$is_know_defend = $data[WorldResourceDef::WR_SQL_IS_KNOW_DEFEND];
			$defend_guild_ud = $data[WorldResourceDef::WR_SQL_DEFEND_GUILD_ID];
			$replay_id = $data[WorldResourceDef::WR_SQL_REPLAY];
			$win = $data[WorldResourceDef::WR_SQL_WIN];

			if ( !isset($return[$world_resource_id]) )
			{
				$return[$world_resource_id] = array();
			}

			$array = array();
			$guild_info = GuildLogic::getRawGuildInfoById($attack_guild_id);
			$guild_name = $guild_info['name'];
			$occupy_guild_emblem = $guild_info['current_emblem_id'];
			$guild_level = $guild_info['guild_level'];
			$array['attack'] =  array (
				'guild_id' => intval($attack_guild_id),
				'guild_name' => $guild_name,
				'guild_emblem' => intval($occupy_guild_emblem),
				'guild_level' => intval($guild_level),
			);

			//如果不知道防守者是谁,则没有defend子项(用于防守者是上一场的胜利者的情况)
			if ( $is_know_defend != 0 )
			{
				//如果防守者为0,则防守者是NPC
				if ( $defend_guild_ud == WorldResourceDef::WR_NO_OCCUPY_GUILD )
				{
					$array['defend'] = array();
				}
				else
				{
					$guild_info = GuildLogic::getRawGuildInfoById($defend_guild_ud);
					$guild_name = $guild_info['name'];
					$occupy_guild_emblem = $guild_info['current_emblem_id'];
					$guild_level = $guild_info['guild_level'];
					$array['defend'] =  array (
						'guild_id' => intval($defend_guild_ud),
						'guild_name' => $guild_name,
						'guild_emblem' => intval($occupy_guild_emblem),
						'guild_level' => intval($guild_level),
					);
				}
			}

			$array['replay'] = $replay_id;
			$array['win'] = $win;
			$return[$world_resource_id][] = $array;
		}

		return $return;
	}

	/* (non-PHPdoc)
	 * @see IWorldResource::guildworldResourceInfos()
	 */
	public function guildworldResourceInfos()
	{
		$return['world_resources'] = $this->worldResourceInfos();
		$userInfo = EnUser::getUser();
		$guild_id = $userInfo['guild_id'];
		$return['signup_list'] = WorldResourceDAO::getAttackWorldResourceListByGuildId(
			$guild_id, self::getSignupStartTime(), self::getSignupEndTime() );

		return $return;
	}

	/* (non-PHPdoc)
	 * @see IWorldResource::worldResourceSignupList()
	 */
	public function worldResourceSignupList($world_resource_id) {
		//格式化输入
		$world_resource_id = intval($world_resource_id);

		$return = array();

		//报名时间以外不允许拉去报名列表
		if ( self::inSignUpTime() == FALSE )
		{
			return $return;
		}

		$list = WorldResourceDAO::getSignupList($world_resource_id, self::getSignupStartTime(), self::getSignupEndTime());

		foreach ( $list as $value )
		{
			$guild_id = $value[WorldResourceDef::WR_SQL_GUILD_ID];
			$guild_info = GuildLogic::getRawGuildInfoById($guild_id);
			$guild_name = $guild_info['name'];
			$occupy_guild_emblem = $guild_info['current_emblem_id'];
			$guild_level = $guild_info['guild_level'];
			$week_contribution = $guild_info['week_contribute_data'];
			$return[] =  array (
				'guild_id' => intval($guild_id),
				'guild_name' => $guild_name,
				'guild_emblem' => intval($occupy_guild_emblem),
				'guild_level' => intval($guild_level),
				'guild_week_contribution' => intval($week_contribution),
			);
		}
		return $return;
	}

	/* (non-PHPdoc)
	 * @see IWorldResource::worldResourceInfo()
	 */
	public function worldResourceInfo($world_resource_id) {
		//格式化输入
		$world_resource_id = intval($world_resource_id);

		$occupy_guild_id = WorldResourceDAO::getOccupyGuildID($world_resource_id);
		if ( $occupy_guild_id == WorldResourceDef::WR_NO_OCCUPY_GUILD )
		{
			return array();
		}
		else
		{
			$guild_info = GuildLogic::getRawGuildInfoById($occupy_guild_id);
			$guild_name = $guild_info['name'];
			$occupy_guild_emblem = $guild_info['current_emblem_id'];
			$guild_level = $guild_info['guild_level'];
			return array (
				'guild_id' => intval($occupy_guild_id),
				'guild_name' => $guild_name,
				'guild_emblem' => intval($occupy_guild_emblem),
				'guild_level' => intval($guild_level),
			);
		}
	}

	/* (non-PHPdoc)
	 * @see IWorldResource::worldResourceInfos()
	 */
	public function worldResourceInfos() {
		$world_resource_infos = WorldResourceDAO::getWorldResourceInfos();
		$array = array();
		foreach ( $world_resource_infos as $world_resource_info )
		{
			$world_resource_id = $world_resource_info[WorldResourceDef::WR_SQL_RESOURCE_ID];
			$occupy_guild_id = $world_resource_info[WorldResourceDef::WR_SQL_GUILD_ID];
			$guild_info = GuildLogic::getRawGuildInfoById($occupy_guild_id);
			$guild_name = $guild_info['name'];
			$guild_level = $guild_info['guild_level'];
			$occupy_guild_emblem = $guild_info['current_emblem_id'];
			$array[$world_resource_id] = array (
				'guild_id' => intval($occupy_guild_id),
				'guild_name' => $guild_name,
				'guild_emblem' => intval($occupy_guild_emblem),
				'guild_level' => intval($guild_level),
			);
		}
		return $array;
	}

	/**
	 *
	 * 建立资源团战
	 *
	 * @param int $world_resource_id					资源ID
	 * @param int $signup_id							报名ID
	 * @param int $$attack_guild_id						攻击方公会ID
	 * @param int $time									资源战斗开始时间
	 *
	 * @return NULL
	 */
	public static function createBattle($world_resource_id, $signup_id, $attack_guild_id, $time)
	{
		//得到资源的实际占领者
		$occupy_guild_id = WorldResourceDAO::getOccupyGuildID($world_resource_id);
		$occupy_guild_id = intval($occupy_guild_id);

		//得到资源的当前占领者
		$cur_occupy_guild_id = WorldResourceDAO::getCurOccupyGuildID($world_resource_id);
		$cur_occupy_guild_id = intval($cur_occupy_guild_id);

		$defend_guild_id = 0;
		//如果当前占领者为空,则防御者为实际占领者,否则防御者为实际占领者
		if ( $cur_occupy_guild_id == 0 )
		{
			$defend_guild_id = $occupy_guild_id;
		}
		else
		{
			$defend_guild_id = $cur_occupy_guild_id;
		}

		$guild_info = GuildLogic::getRawGuildInfoById($attack_guild_id);
		$guild_name = $guild_info['name'];
		$guild_level = $guild_info['guild_level'];
		$guild_emblem = $guild_info['current_emblem_id'];
		$attack_guild_info = array (
				'guild_id' => intval($attack_guild_id),
				'guild_name' => $guild_name,
				'guild_emblem' => intval($guild_emblem),
				'guild_level' => intval($guild_level),
		);

		if ( empty($defend_guild_id) )
		{
			$defend_guild_info = array(
				'guild_id' => WorldResourceDef::WR_NO_OCCUPY_GUILD,
				'guild_name' => self::getWorldResourceGroupName($world_resource_id),
				'guild_emblem' => 0,
				'guild_level' => self::getWorldResourceLevel($world_resource_id),
			);
		}
		else
		{
			$guild_info = GuildLogic::getRawGuildInfoById($defend_guild_id);
			$guild_name = $guild_info['name'];
			$guild_level = $guild_info['guild_level'];
			$guild_emblem = $guild_info['current_emblem_id'];
			$defend_guild_info = array (
					'guild_id' => intval($defend_guild_id),
					'guild_name' => $guild_name,
					'guild_emblem' => intval($guild_emblem),
					'guild_level' => intval($guild_level),
			);
		}

		$array = array(
			'attacker' => $attack_guild_info,
			'defender' => $defend_guild_info,
		);

		if ( empty($defend_guild_id) )
		{
			$array['defendNpc'] = self::getWorldResourceArmys($world_resource_id);
			//TODO擂台NPC
			$array['chanllengeNpc'] = array();
		}

		//添加callback需要的参数
		$array['arrExtra'] = array('signup_id' => $signup_id);
		//@see 需要让策划配置的世界资源ID不同于港口ID
		RPCContext::getInstance()->createGuildBattle($world_resource_id, $time,
			'worldResource.attackEnd', $array);

		Logger::INFO('World Resource:%d battle create!start battle at:%d',
			$world_resource_id, $time);

		//增加战斗开始前的timer
		TimerTask::addTask(0, $time - WorldResourceConfig::SINGLE_BATTLE_DURATION, 'worldResource.chatBattleStart',
				array(
					$world_resource_id,
					$attack_guild_id,
					$defend_guild_id,
				));
	}

	/**
	 *
	 * 战斗开始
	 *
	 * @param int $world_resource_id
	 * @param int $attack_guild_id
	 * @param int $defend_guild_id
	 *
	 * @return NULL
	 */
	public function chatBattleStart($world_resource_id, $attack_guild_id, $defend_guild_id)
	{
		$guild_info = GuildLogic::getRawGuildInfoById($attack_guild_id);
		$guild_name = $guild_info['name'];
		$attack_guild_info = array (
				'guild_id' => intval($attack_guild_id),
				'guild_name' => $guild_name,
		);

		if ( $defend_guild_id == 0 )
		{
			ChatTemplate::sendWorldResourceBattleNPC($world_resource_id, $attack_guild_info);
		}
		else
		{
			$guild_info = GuildLogic::getRawGuildInfoById($defend_guild_id);
			$guild_name = $guild_info['name'];
			$defend_guild_info = array (
					'guild_id' => intval($defend_guild_id),
					'guild_name' => $guild_name,
			);
			ChatTemplate::sendWorldResourceBattle($world_resource_id,
				$attack_guild_info, $defend_guild_info);
		}
	}

	/**
	 *
	 * 每次登录发送的消息
	 *
	 * @param int $uid
	 *
	 * @return NULL
	 */
	public static function chatBattle4UserLogin($uid)
	{

		$user = EnUser::getUserObj($uid);
		$guild_id = $user->getGuildId();
		//如果没有加入公会则退出
		if ( empty($guild_id) )
		{
			return;
		}

		$attack_list = WorldResourceDAO::getAllAttackList(self::getSignupStartTime(), self::getSignupEndTime());
		for ( $i = 0; $i < count($attack_list); $i++ )
		{
			//是否属于攻击方公会或者防守方公会
			if ( $attack_list[$i][WorldResourceDef::WR_SQL_GUILD_ID] != $guild_id &&
				$attack_list[$i][WorldResourceDef::WR_SQL_DEFEND_GUILD_ID] != $guild_id )
			{
				continue;
			}
			else
			{
				//是否已经完毕
				if ( !empty($attack_list[$i][WorldResourceDef::WR_SQL_REPLAY]) )
				{
					continue;
				}
				else
				{
					$battle_order = 0;
					for ( $k = $i - 1; $k>=0; $k-- )
					{
						if ( $attack_list[$i][WorldResourceDef::WR_SQL_RESOURCE_ID] ==
							$attack_list[$k][WorldResourceDef::WR_SQL_RESOURCE_ID] )
						{
							$battle_order++;
						}
					}
					if ( !isset(WorldResourceConfig::$BATTLE_TIME[$battle_order]))
					{
						Logger::FATAL('fixed me!invalid battle order!');
					}
					else
					{
						$battle_start_time = self::getBattleStartTime() + WorldResourceConfig::$BATTLE_TIME[$battle_order];
						$battle_end_time = self::getBattleStartTime() + WorldResourceConfig::$BATTLE_TIME[$battle_order]
								+ WorldResourceConfig::SINGLE_BATTLE_DURATION;
						if ( Util::getTime() > $battle_start_time
							&& Util::getTime() < $battle_end_time )
						{
							$attack_guild_id = $attack_list[$i][WorldResourceDef::WR_SQL_GUILD_ID];
							$defend_guild_id = $attack_list[$i][WorldResourceDef::WR_SQL_DEFEND_GUILD_ID];
							$guild_info = GuildLogic::getRawGuildInfoById($attack_guild_id);
							$guild_name = $guild_info['name'];
							$attack_guild_info = array (
									'guild_id' => intval($attack_guild_id),
									'guild_name' => $guild_name,
							);
							if ( empty($defend_guild_id) )
							{
								ChatTemplate::sendWorldResourceBattleNPCToMe(array($uid),
									 $attack_list[$i][WorldResourceDef::WR_SQL_RESOURCE_ID],
									 $attack_guild_info);
							}
							else
							{
								$guild_info = GuildLogic::getRawGuildInfoById($defend_guild_id);
								$guild_name = $guild_info['name'];
								$defend_guild_info = array (
										'guild_id' => intval($defend_guild_id),
										'guild_name' => $guild_name,
								);
								ChatTemplate::sendWorldResourceBattleToMe(array($uid),
									$attack_list[$i][WorldResourceDef::WR_SQL_RESOURCE_ID],
									$attack_guild_info, $defend_guild_info);
							}
						}
					}
				}
			}
		}
	}

	/**
	 *
	 * 发送报名开始消息
	 *
	 * @param int $time
	 *
	 * @return NULL
	 *
	 */
	public function chatSignupStart($time)
	{
		ChatTemplate::sendWorldResourceSignup();
		TimerTask::addTask(0,
				$time+WorldResourceConfig::BATTLE_INTERVAL,
				'worldResource.chatSignupStart',
				array($time+WorldResourceConfig::BATTLE_INTERVAL)
		);
	}

	/**
	 *
	 * 初始化报名开始消息
	 *
	 * @param NULL
	 *
	 * @return NULL
	 */
	public function initWorldResourceSignupTimer()
	{
		TimerTask::addTask(0,
				self::getSignupStartTime()+WorldResourceConfig::BATTLE_INTERVAL,
				'worldResource.chatSignupStart',
				array(self::getSignupStartTime()+WorldResourceConfig::BATTLE_INTERVAL)
		);
	}

 	private static function isGuildRight()
	{
		$uid = RPCContext::getInstance()->getUid();
		$memberinfo = GuildLogic::getMemberInfo($uid);

		//必须属于某个公会
		if ( empty($memberinfo) )
		{
			Logger::DEBUG('you must be a guild member!');
			return FALSE;
		}

		//如果不是会长和副会长则不能进行此操作
		if ( $memberinfo['role_type'] != GuildRoleType::PRESIDENT &&
			$memberinfo['role_type'] != GuildRoleType::VICE_PRESIDENT )
		{
			Logger::DEBUG('limit to rights!you must be a (vice_)persident!');
			return FALSE;
		}

		return TRUE;
	}

	private static function getSignupStartTime()
	{
		$time = Util::getTime();
		$first_battle_end_time = strtotime(GameConf::SERVER_OPEN_YMD . ' ' . WorldResourceConfig::FIREST_BATTLE_END_DATE);
		$battle_count = floor(($time - $first_battle_end_time) / WorldResourceConfig::BATTLE_INTERVAL);

		$signup_start_time = $first_battle_end_time + $battle_count * WorldResourceConfig::BATTLE_INTERVAL;
		return $signup_start_time;
	}

	private static function inSignUpTime()
	{
		$time = Util::getTime();

		if ( $time > self::getSignupStartTime() && $time < self::getSignupEndTime() )
			return TRUE;
		else
			return FALSE;
	}

	private static function inBattleTime()
	{
		$time = Util::getTime();

		if ( $time > self::getBattleStartTime() && $time < self::getBattleEndTime() )
			return TRUE;
		else
			return FALSE;
	}

	private static function getSignupEndTime()
	{
		return self::getSignupStartTime() + WorldResourceConfig::SIGNUP_DURATION;
	}

	private static function getBattleStartTime()
	{
		return self::getBattleEndTime() - WorldResourceConfig::BATTLE_DURATION;
	}

	private static function getBattleEndTime()
	{
		return self::getSignupStartTime() + WorldResourceConfig::BATTLE_INTERVAL;
	}

	private static function getAttackReqGuildLevel($world_resource_id)
	{
		$resourceInfo = self::getWorldResourceInfo($world_resource_id);
		if ( !isset($resourceInfo[WorldResourceDef::WR_ATTAK_REQ_GUILD_LEVEL]) )
		{
			Logger::FATAL('invalid world resource id:%d!', $world_resource_id);
			throw new Exception('config');
		}
		return $resourceInfo[WorldResourceDef::WR_ATTAK_REQ_GUILD_LEVEL];
	}

	private static function getWorldResourceLevel($world_resource_id)
	{
		$resourceInfo = self::getWorldResourceInfo($world_resource_id);
		if ( !isset($resourceInfo[WorldResourceDef::WR_LEVEL]) )
		{
			Logger::FATAL('invalid world resource id:%d!', $world_resource_id);
			throw new Exception('config');
		}
		return $resourceInfo[WorldResourceDef::WR_LEVEL];
	}

	private static function getWorldResourceName($world_resource_id)
	{
		$resourceInfo = self::getWorldResourceInfo($world_resource_id);
		if ( !isset($resourceInfo[WorldResourceDef::WR_NAME]) )
		{
			Logger::FATAL('invalid world resource id:%d!', $world_resource_id);
			throw new Exception('config');
		}
		return $resourceInfo[WorldResourceDef::WR_NAME];
	}

	private static function getWorldResourceGroupName($world_resource_id)
	{
		$resourceInfo = self::getWorldResourceInfo($world_resource_id);
		if ( !isset($resourceInfo[WorldResourceDef::WR_GROUP_NAME]) )
		{
			Logger::FATAL('invalid world resource id:%d!', $world_resource_id);
			throw new Exception('config');
		}
		return $resourceInfo[WorldResourceDef::WR_GROUP_NAME];
	}

	private static function getWorldResourceInfo($world_resource_id)
	{
		if ( !isset(btstore_get()->WORLDRESOURCE[$world_resource_id]) )
		{
			Logger::FATAL('invalid world resource id:%d!', $world_resource_id);
			throw new Exception('config');
		}
		return btstore_get()->WORLDRESOURCE[$world_resource_id];
	}

	private static function getWorldResourceOutput($world_resource_id)
	{
		$resourceInfo = self::getWorldResourceInfo($world_resource_id);
		if ( !isset($resourceInfo[WorldResourceDef::WR_OUTPUT]) )
		{
			Logger::FATAL('invalid world resource id:%d!', $world_resource_id);
			throw new Exception('config');
		}
		return $resourceInfo[WorldResourceDef::WR_OUTPUT];
	}

	private static function getWorldResourceArmys($world_resource_id)
	{
		$resourceInfo = self::getWorldResourceInfo($world_resource_id);
		if ( !isset($resourceInfo[WorldResourceDef::WR_ARMY_IDS]) )
		{
			Logger::FATAL('invalid world resource id:%d!', $world_resource_id);
			throw new Exception('config');
		}
		return $resourceInfo[WorldResourceDef::WR_ARMY_IDS]->toArray();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */