<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Boss.class.php 40444 2013-03-11 02:01:07Z wuqilin $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/boss/Boss.class.php $
 * @author $Author: wuqilin $(jhd@babeltime.com)
 * @date $Date: 2013-03-11 10:01:07 +0800 (一, 2013-03-11) $
 * @version $Revision: 40444 $
 * @brief
 *
 **/





class Boss implements IBoss
{
	/**
	 *
	 * boss attack info
	 *
	 * @var array
	 */
	private $m_boss_attack = array();

	/**
	 *
	 * 当前用户uid
	 *
	 * @var int
	 */
	private $m_uid;

	/**
	 *
	 * 当前boss id
	 *
	 * @var int
	 */
	private $m_boss_id;

	private function bossUserAttackInfo($boss_id)
	{
		$this->m_uid = RPCContext::getInstance()->getUid();
		$this->m_boss_id = $boss_id;
		$this->m_boss_attack = $this->getUserBossAttackInfo($boss_id);
	}

	/**
	 *
	 * 当前是否可以进入boss副本
	 *
	 * @param int $boss_id
	 *
	 * @throws Exception
	 *
	 * @return boolean				TRUE表示可以进入
	 *
	 */
	public function canEnter($boss_id)
	{
		//检查boss id 是否合法
		if ( BossUtil::isBossTown($boss_id) == FALSE )
		{
			Logger::FATAL('invalid boss id:%d', $boss_id);
			throw new Exception('fake');
		}

		//检查boss是否开放
		if ( BossUtil::isBossTime($boss_id) == FALSE )
		{
			Logger::DEBUG('not in boss time!boss id:%d', $boss_id);
			return FALSE;
		}

		$bossInfo = BossDAO::getBoss($boss_id);

		$boss_hp = $bossInfo[BossDef::BOSS_HP];

		//boss已经死亡
		if ( $boss_hp <= 0 )
		{
			return FALSE;
		}

		return TRUE;
	}

	/* (non-PHPdoc)
	 * @see IBoss::getBossOffset()
	 */
	public function getBossOffset()
	{
		return GameConf::BOSS_OFFSET;
	}

	/* (non-PHPdoc)
	 * @see IBoss::enterBossCopy()
	 */
	public function enterBossCopy($boss_id, $x, $y) {
		//格式化数据
		$boss_id = intval($boss_id);
		$x = intval($x);
		$y = intval($y);

		//检查是否开启功能节点
		if ( EnSwitch::isOpen(SwitchDef::ACTIVE) == FALSE )
		{
			Logger::WARNING('not open active!can not enter boss copy');
			throw new Exception('fake');
		}

		//检查boss id 是否合法
		if ( BossUtil::isBossTown($boss_id) == FALSE )
		{
			Logger::FATAL('invalid boss id:%d', $boss_id);
			throw new Exception('fake');
		}

		//检查boss是否开放
		if ( BossUtil::isBossTime($boss_id) == FALSE )
		{
			Logger::DEBUG('not in boss time!boss id:%d', $boss_id);
			return array();
		}

		$boss_start_time = BossUtil::getBossStartTime($boss_id);
		$boss_end_time = BossUtil::getBossEndTime($boss_id);

		//得到当前boss的情况
		$bossInfo = BossDAO::getBoss($boss_id);
		$boss_hp = $bossInfo[BossDef::BOSS_HP];
		//boss已经死亡
		if ( $boss_hp <= 0 )
		{
			Logger::DEBUG("boss has died!");
			return array();
		}
		$boss_level = $bossInfo[BossDef::BOSS_LEVEL];

		//得到自己的信息
		$this->bossUserAttackInfo($boss_id);
		$attack_time = $this->m_boss_attack[BossDef::LAST_ATTACK_TIME];
		$attack_hp = $this->m_boss_attack[BossDef::ATTACK_HP];
		$inspire = $this->m_boss_attack[BossDef::INSPIRE];
		$revive = $this->m_boss_attack[BossDef::REVIVE];
		$flags = $this->m_boss_attack[BossDef::FLAGS];

		if ( $this->isSetBossBotFlags($flags) == TRUE )
		{
			Logger::WARNING('already set boss bot');
			return array();
		}

		//检测当前是否在城镇,客户端应当先调用leaveTown然后再调用该方法
		$townId = RPCContext::getInstance()->getTownId();
		if (! empty ( $townId ))
		{
			Logger::fatal ( "user:%d already in town:%d", $this->m_uid, $townId );
			throw new Exception ( "fake" );
		}

		$arr = City::userInfoForEnterTown();
		RPCContext::getInstance ()->enterTown ( $boss_id, $x, $y, $arr );
		RPCContext::getInstance()->setSession('global.townId', $boss_id);

		//通知前端当前所在人数
		$user_count = City::getTownUserCount($boss_id)+1;
		RPCContext::getInstance()->sendFilterMessage('town', $boss_id, 'boss.updateUserCount', array($user_count));

		return array(
			BossDef::ATTACK_LIST => BossUtil::getBossAttackHpTop($boss_id,
				 $boss_start_time, $boss_end_time, BossConfig::BOSS_ATTACK_LIST_MAX_NUM),
			BossDef::ATTACK_GROUP => BossUtil::getBossAttackHpGroup($boss_id,
				$boss_start_time, $boss_end_time),
			BossDef::BOSS_HP => $boss_hp,
			BossDef::BOSS_MAX_HP => BossUtil::getBossMaxHp($boss_id, $boss_level),
			BossDef::BOSS_LEVEL => $boss_level,
			BossDef::BOSS_TOWN_USER_COUNT => $user_count,
			BossDef::ATTACK_HP => $attack_hp,
			BossDef::INSPIRE => $inspire,
			BossDef::REVIVE	=> $revive,
			BossDef::LAST_ATTACK_TIME => $attack_time,
			BossDef::FLAGS => $flags,
		);
	}

	/* (non-PHPdoc)
	 * @see IBoss::leaveBossCopy()
	 */
	public function leaveBossCopy() {

		$boss_id = RPCContext::getInstance()->getTownId();

		if ( BossUtil::isBossTown($boss_id) == FALSE )
		{
			Logger::WARNING('invalid boss id:%d', $boss_id);
			throw new Exception('fake');
		}

		RPCContext::getInstance()->setSession('global.townId', 0);

		RPCContext::getInstance ()->leaveTown ();
	}

	/* (non-PHPdoc)
	 * @see IBoss::inspire()
	 */
	public function inspire()
	{
		$boss_id = RPCContext::getInstance()->getTownId();

		if ( BossUtil::isBossTown($boss_id) == FALSE )
		{
			Logger::FATAL('invalid boss id:%d', $boss_id);
			throw new Exception('fake');
		}

		$return = array();

		//检查boss是否开放
		if ( BossUtil::isBossTime($boss_id) == FALSE )
		{
			Logger::DEBUG('not in boss time!boss id:%d', $boss_id);
			return $return;
		}

		$this->bossUserAttackInfo($boss_id);

		//是否设置boss傀儡
		$flags = $this->m_boss_attack[BossDef::FLAGS];
		if ( $this->isSetBossBotFlags($flags) == TRUE )
		{
			Logger::WARNING('already set boss bot');
			return $return;
		}

		$inspire = $this->m_boss_attack[BossDef::INSPIRE];
		//是否鼓舞到达上限
		if ( $inspire >= BossConfig::MAX_INSPIRE_NUM )
		{
			Logger::DEBUG('already max inspire!');
			return $return;
		}

 		//减少阅历
 		$user = EnUser::getUserObj();
 		if ( $user->subExperience(BossConfig::INSPIRE_REQ_EXPERIENCE*$user->getLevel()) == FALSE )
 		{
 			Logger::DEBUG('no enough $prestige');
 			return $return;
 		}

 		$max_rand = BossConfig::INSPIRE_EXPERIENCE_RAND - $inspire * BossConfig::INSPIRE_EXPERIENCE_DEC_RAND;
 		$rand = rand(0, BossConfig::INSPIRE_EXPERIENCE_MAX_RAND);
 		if ( $rand > $max_rand )
 		{
 			Logger::INFO('inspire failed!:rand:%d, need:%d', $rand, $max_rand);

 			//用户更新
 			$user->update();

 			return array ( 'inspire_success' => FALSE );
 		}

 		//用户更新
 		$user->update();

 		$this->m_boss_attack[BossDef::INSPIRE] += 1;
 		$this->m_boss_attack[BossDef::LAST_INSPIRE_TIME] = Util::getTime();
		BossDAO::setBossAttackInspire($boss_id, $this->m_uid, Util::getTime(), $inspire+1);

 		return array ( 'inspire_success' => TRUE );
	}

	/* (non-PHPdoc)
	 * @see IBoss::inspireByGold()
	 */
	public function inspireByGold() {

		$boss_id = RPCContext::getInstance()->getTownId();

		if ( BossUtil::isBossTown($boss_id) == FALSE )
		{
			Logger::FATAL('invalid boss id:%d', $boss_id);
			throw new Exception('fake');
		}

		//检查boss是否开放
		if ( BossUtil::isBossTime($boss_id) == FALSE )
		{
			Logger::DEBUG('not in boss time!boss id:%d', $boss_id);
			return FALSE;
		}

		$this->bossUserAttackInfo($boss_id);

		//是否设置boss傀儡
		$flags = $this->m_boss_attack[BossDef::FLAGS];
		if ( $this->isSetBossBotFlags($flags) == TRUE )
		{
			Logger::WARNING('already set boss bot');
			return FALSE;
		}

		$inspire = $this->m_boss_attack[BossDef::INSPIRE];
		//是否鼓舞到达上限
		if ( $inspire >= BossConfig::MAX_INSPIRE_NUM )
		{
			Logger::DEBUG('already max inspire!');
			return FALSE;
		}

		//减少金币
 		$user = EnUser::getUserObj();
 		if ( $user->subGold(BossConfig::INSPIRE_REQ_GOLD) == FALSE )
 		{
 			Logger::DEBUG('no enough gold');
 			return FALSE;
 		}

 		//更新用户
 		$user->update();

 		$this->m_boss_attack[BossDef::INSPIRE] += 1;
 		$this->m_boss_attack[BossDef::LAST_INSPIRE_TIME] = Util::getTime();
		BossDAO::setBossAttackInspire($boss_id, $this->m_uid, Util::getTime(), $inspire+1);

		 //统计
 		Statistics::gold(StatisticsDef::ST_FUNCKEY_BOSS_INSPIRE,
 				 		 BossConfig::INSPIRE_REQ_GOLD,
 				 		 Util::getTime());

 		return TRUE;
	}

	/* (non-PHPdoc)
	 * @see IBoss::revive()
	 */
	public function revive($boss_id) {

		$boss_id = intval($boss_id);

		$return = array ();
		//是否在boss战场景
		if ( $boss_id != RPCContext::getInstance()->getTownId()
			|| bossUtil::isBossTown($boss_id) == FALSE )
		{
			Logger::DEBUG('invalid boss id:%d', $boss_id);
			return $return;
		}

		//检查当前boss是否开启
		if ( BossUtil::isBossTime($boss_id) == FALSE )
		{
			Logger::DEBUG('not in boss time!');
			return $return;
		}

		$boss_start_time = BossUtil::getBossStartTime($boss_id);
		$boss_end_time = BossUtil::getBossEndTime($boss_id);

		$this->bossUserAttackInfo($boss_id);
		$attack_time = $this->m_boss_attack[BossDef::LAST_ATTACK_TIME];
		$revive = $this->m_boss_attack[BossDef::REVIVE];
		$flags = $this->m_boss_attack[BossDef::FLAGS];

		//是否设置boss傀儡
		if ( $this->isSetBossBotFlags($flags) == TRUE )
		{
			Logger::WARNING('already set boss bot');
			return $return;
		}

		//不需要检测攻击时间

		$user = EnUser::getUserObj();
		$vip = $user->getVip();
		//是否VIP等级足够
		if ( $vip < BossConfig::REVIVE_REQ_VIP_LEVEL )
		{
			Logger::DEBUG('vip level:%d too low!req:%d', $vip, BossConfig::REVIVE_REQ_VIP_LEVEL);
			return $return;
		}
		$gold = BossConfig::REVIVE_REQ_GOLD + $revive * BossConfig::REVIVE_REQ_INC_GOLD;
		//是否金币足够
		if ( $user->subGold($gold) == FALSE )
		{
			Logger::DEBUG('no enough gold!');
			return $return;
		}

		$return =  $this->__attack($boss_id, $boss_start_time, $boss_end_time, TRUE);

		if ( $return['success'] == TRUE )
		{
			//Statistics
			Statistics::gold(StatisticsDef::ST_FUNCKEY_BOSS_REVIVE, $gold, Util::getTime());
		}

		return $return;

	}

	/* (non-PHPdoc)
	 * @see IBoss::setBossBot()
	 */
	public function setBossBot($boss_id, $sub_cd)
	{
		$boss_id = intval($boss_id);
		$sub_cd = intval($sub_cd);
		$uid = RPCContext::getInstance()->getUid();

		$return = FALSE;

		if ( BossUtil::isBossTown($boss_id) == FALSE )
		{
			Logger::WARNING('invalid boss id:%d', $boss_id);
			return $return;
		}

		//check time
		$next_boss_start_time = BossUtil::getBossStartTime($boss_id);
		$before_boss_end_time = BossUtil::getBeforeBossEndTime($boss_id);
		if ( Util::getTime() < $before_boss_end_time + BossConfig::BOSS_BOT_SET_TIME_SUFFIX ||
			Util::getTime() > $next_boss_start_time - BossConfig::BOSS_BOT_SET_TIME_PRE )
		{
			Logger::WARNING('in invalid time!before_boss_end_time:%d next_boss_start_time:%d',
				$before_boss_end_time, $next_boss_start_time);
			return $return;
		}

		$this->bossUserAttackInfo($boss_id);
		$flags = $this->m_boss_attack[BossDef::FLAGS];
		if ( $this->isSetBossBotFlags($flags) == TRUE )
		{
			Logger::WARNING('already set boss bot in boss:%d', $boss_id);
			return $return;
		}

		$flags = $this->setBossBotFlags($flags, $sub_cd);

		$user = EnUser::getUserObj();
		$vip_level = $user->getVip();
		$gold = btstore_get()->VIP[$vip_level]['boss_atk_gold'];
		if ( $gold == 0 )
		{
			Logger::WARNING('vip level:%d not open boss bot!', $vip_level);
			return $return;
		}

		$boss_start_time = BossUtil::getBossStartTime($boss_id);

		BossDAO::setBossAttack($boss_id, $uid, $boss_start_time,
			 NULL, NULL, NULL, NULL, $flags, NULL, NULL);

		return TRUE;
	}

	public function unsetBossBot($boss_id)
	{
		$boss_id = intval($boss_id);
		$uid = RPCContext::getInstance()->getUid();

		$return = FALSE;

		if ( BossUtil::isBossTown($boss_id) == FALSE )
		{
			Logger::WARNING('invalid boss id:%d', $boss_id);
			return $return;
		}

		//check time
		$next_boss_start_time = BossUtil::getBossStartTime($boss_id);
		$before_boss_end_time = BossUtil::getBeforeBossEndTime($boss_id);
		if ( Util::getTime() < $before_boss_end_time + BossConfig::BOSS_BOT_SET_TIME_SUFFIX ||
			Util::getTime() > $next_boss_start_time - BossConfig::BOSS_BOT_SET_TIME_PRE )
		{
			Logger::WARNING('in invalid time!before_boss_end_time:%d next_boss_start_time:%d',
				$before_boss_end_time, $next_boss_start_time);
			return $return;
		}

		$this->bossUserAttackInfo($boss_id);
		$flags = $this->m_boss_attack[BossDef::FLAGS];
		if ( $this->isSetBossBotFlags($flags) == FALSE )
		{
			Logger::WARNING('not set boss bot in boss:%d', $boss_id);
			return $return;
		}

		$flags = $this->unsetBossBotFlags($flags);

		BossDAO::setBossAttack($boss_id, $uid, $before_boss_end_time,
			 NULL, NULL, NULL, NULL, $flags, NULL, NULL);

		return TRUE;
	}

	/* (non-PHPdoc)
	 * @see IBoss::getBossBot()
	 */
	public function getBossBot($boss_id)
	{
		//格式化输入
		$boss_id = intval($boss_id);

		$return = array(
			'set_status' => 1,
		);
		//check time
		$next_boss_start_time = BossUtil::getBossStartTime($boss_id);
		$before_boss_end_time = BossUtil::getBeforeBossEndTime($boss_id);
		if ( Util::getTime() < $before_boss_end_time + BossConfig::BOSS_BOT_SET_TIME_SUFFIX ||
			Util::getTime() > $next_boss_start_time - BossConfig::BOSS_BOT_SET_TIME_PRE )
		{
			$return = array(
				'set_status' => 2,
			);
		}
		$before_boss_end_time = BossUtil::getBeforeBossEndTime($boss_id);
		if ( Util::getTime() < $before_boss_end_time + BossConfig::BOSS_BOT_SET_TIME_SUFFIX &&
			Util::getTime() >= $before_boss_end_time )
		{
			$return = array(
				'set_status' => 3,
			);
			return $return;
		}

		$this->bossUserAttackInfo($boss_id);

		$flags = $this->m_boss_attack[BossDef::FLAGS];

		$return[BossDef::BOT] = self::isSetBossBotFlags($flags);
		$return[BossDef::BOT_SUB_CDTIME] = self::isSetBossBotSubTimeFlags($flags);
		return $return;
	}

	/* (non-PHPdoc)
	 * @see IBoss::subCdTime()
	 */
	public function subCdTime($boss_id) {

		$boss_id = intval($boss_id);
		$uid = RPCContext::getInstance()->getUid();

		if ( $boss_id != RPCContext::getInstance()->getTownId()
			|| BossUtil::isBossTown($boss_id) == FALSE )
		{
			Logger::DEBUG('invalid boss id:%d', $boss_id);
			return FALSE;
		}

		//检查boss是否开放
		if ( BossUtil::isBossTime($boss_id) == FALSE )
		{
			Logger::DEBUG('not in boss time!boss id:%d', $boss_id);
			return FALSE;
		}

		$this->bossUserAttackInfo($boss_id);
		$attack_time = $this->m_boss_attack[BossDef::LAST_ATTACK_TIME];
		$flags = $this->m_boss_attack[BossDef::FLAGS];

		//是否设置boss傀儡
		$flags = $this->m_boss_attack[BossDef::FLAGS];
		if ( $this->isSetBossBotFlags($flags) == TRUE )
		{
			Logger::WARNING('already set boss bot');
			return FALSE;
		}

		//检测是否已经没有冷却
		if ( self::isAttack($attack_time, $flags) == TRUE )
		{
			Logger::DEBUG('no need sub cd time!attack_time:%d, flags:%d',
				$attack_time, $flags);
			return FALSE;
		}

		//检测是否已经减少过cd
		if ( $flags & BossDef::FLAGS_SUB_CD_TIME )
		{
			Logger::DEBUG('has sub cdtime!');
			return FALSE;
		}

		//减少金币
		$user = EnUser::getUserObj();
		if ( $user->subGold(BossConfig::SUB_CDTIME_REQ_GOLD) == FALSE )
		{
			Logger::DEBUG('no enough gold!');
			return FALSE;
		}

		//用户更新
		$user->update();

		//设置为已经减少过cd
		$flags |= BossDef::FLAGS_SUB_CD_TIME;
		BossDAO::setBossAttackFlag($boss_id, $uid, $flags);
		$this->m_boss_attack[BossDef::FLAGS] = $flags;

		 //统计
 		Statistics::gold(StatisticsDef::ST_FUNCKEY_BOSS_SUBCDTIME,
 				 		 BossConfig::SUB_CDTIME_REQ_GOLD,
 				 		 Util::getTime());

		return TRUE;
	}

	/**
	 *
	 * boss战斗
	 *
	 * @param int $boss_id
	 *
	 * @throws Exception
	 *
	 * @return
	 */
	public function attack($boss_id)
	{
		$boss_id = intval($boss_id);

		$return = array();

		//检查当前是否在boss战斗的场景
		if ( $boss_id != RPCContext::getInstance()->getTownId()
			|| BossUtil::isBossTown($boss_id) == FALSE )
		{
			Logger::DEBUG('invalid boss id:%d', $boss_id);
			return $return;
		}

		//检查当前boss是否开启
		if ( BossUtil::isBossTime($boss_id) == FALSE )
		{
			Logger::DEBUG('not in boss time!');
			return $return;
		}

		$boss_start_time = BossUtil::getBossStartTime($boss_id);
		$boss_end_time = BossUtil::getBossEndTime($boss_id);

		$this->bossUserAttackInfo($boss_id);
		$attack_time = $this->m_boss_attack[BossDef::LAST_ATTACK_TIME];
		$flags = $this->m_boss_attack[BossDef::FLAGS];

		//是否设置boss傀儡
		if ( $this->isSetBossBotFlags($flags) == TRUE )
		{
			Logger::WARNING('already set boss bot');
			return $return;
		}

		$time = Util::getTime();
		//检查当前是否战斗在冷却
		if ( self::isAttack($attack_time, $flags) == FALSE )
		{
			Logger::DEBUG('in boss attack freeze!');
			return $return;
		}

		return $this->__attack($boss_id, $boss_start_time, $boss_end_time);
	}

	public function __attack($boss_id, $boss_start_time, $boss_end_time, $is_revive=FALSE)
	{
		$success_key = 'success';

		$user = EnUser::getUserObj();
		$userFormation = EnFormation::getFormationInfo();
		// 将阵型ID设置为用户当前默认阵型
		$formationID = $user->getCurFormation();
		$user->prepareItem4CurFormation();
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation, TRUE);
		$inspire = $this->m_boss_attack[BossDef::INSPIRE];
		//增加鼓舞所得到的攻击力
		foreach ( $userFormationArr as $key => $value )
		{
			$userFormationArr[$key]['physicalAttackRatio'] += $inspire * BossConfig::INSPIRE_INC_PHYSICAL_ATTACK_PRECENT;
			$userFormationArr[$key]['magicAttackRatio'] += $inspire * BossConfig::INSPIRE_INC_KILL_ATTACK_PRECENT;
			$userFormationArr[$key]['killAttackRatio'] += $inspire * BossConfig::INSPIRE_INC_MAGIC_ATTACK_PRECENT;
		}
		Logger::debug('The hero list is %s', $userFormationArr);
		$battle_user=array(
						'uid' => $this->m_uid,
						'name' => $user->getUname(),
			            'level' => $user->getLevel(),
			            'flag' => 0,
			            'formation' => $formationID,
			            'isPlayer' => 1,
			            'arrHero' => $userFormationArr
					);


		$armyID = BossUtil::getBossArmyId($boss_id);
		$teamID = btstore_get()->ARMY[$armyID]['monster_list_id'];

		$boss_info = BossDAO::getBoss($boss_id);

		//如果boss血量为0,则boss战斗结束
		if ( $boss_info[BossDef::BOSS_HP] == 0 )
		{
			return array($success_key => FALSE);
		}

		$boss_level = $boss_info[BossDef::BOSS_LEVEL];
		// 敌人信息
		$enemyFormation = BossUtil::getBossFormationInfo($boss_id, $boss_level);

		// 将对象转化为数组
		$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation);
		if ( count($enemyFormationArr) != 1  )
		{
			Logger::FATAL('invalid boss army! boss_id:%d', $boss_id);
			throw new Exception('config!');
		}

		//将boss血量设置成为当前血量
		foreach ( $enemyFormationArr as $key => $value )
		{
			$enemyFormationArr[$key]['currHp'] = $boss_info[BossDef::BOSS_HP];
		}

		// 调用战斗模块
		$bt = new Battle();
		$attack_ret = $bt->doHero($battle_user,
		                      array(
		                      		'uid' => $armyID,
		                      		'name' => btstore_get()->ARMY[$armyID]['name'],
		                            'level' => $boss_level,
		                            'flag' => 0,
		                            'formation' => btstore_get()->TEAM[$teamID]['fid'],
		                            'isPlayer' => 0,
		                            'arrHero' => $enemyFormationArr),
		                      0,
		                      array($this, 'attackCallback'),
		                      array('attackRound' => BossConfig::BATTLE_ROUND),
		                      array (
		                      	'bgid' => btstore_get()->ARMY[$armyID]['background_id'],
		                      	'musicId' => btstore_get()->ARMY[$armyID]['music_path'],
								'type' => BattleType::BOSS,
		                      	)
		                      );

		//更新boss的血量
		$boss_hp =  $attack_ret['server']['team2'][0]['hp'];
		$boss_cost_hp = $attack_ret['server']['team2'][0]['costHp'];
		$boss_affect_rows = BossDAO::subBossHP($boss_id, $boss_cost_hp);

		//玩家不需要减少血量

		//处理数据
		//如果boss血量为0
		if ( $boss_hp <= 0 || ( $boss_affect_rows == 0 && $boss_cost_hp > 0 ) )
		{
			//locker
			$lock = new Locker();
			$lock->lock(BossDef::LOCK_PREFIX . $boss_id);

			$_boss_info = BossDAO::getBoss($boss_id);
			if ( $_boss_info[BossDef::BOSS_HP] == 0 )
			{
				$lock->unlock(BossDef::LOCK_PREFIX . $boss_id);
				return array($success_key => FALSE);
			}

			//设置boss血量为0
			BossDAO::setBossHP($boss_id, 0);

			$lock->unlock(BossDef::LOCK_PREFIX . $boss_id);

			if ( $boss_affect_rows == 0 && $boss_hp > 0 )
			{
				//由于奖励已经加上去了,因此上需要将奖励回滚
				$user->rollback();

				$_boss_hp = intval($boss_cost_hp / 10);
				if ( $_boss_hp <= 0 )
				{
					$_boss_hp = 1;
				}

				//将boss血量设置成为当前血量
				foreach ( $enemyFormationArr as $key => $value )
				{
					$enemyFormationArr[$key]['currHp'] = $_boss_hp;
				}
				$attack_ret = $bt->doHero($battle_user,
                      array(
                      		'uid' => $armyID,
                      		'name' => btstore_get()->ARMY[$armyID]['name'],
                            'level' => $boss_level,
                            'flag' => 0,
                            'formation' => btstore_get()->TEAM[$teamID]['fid'],
                            'isPlayer' => 0,
                            'arrHero' => $enemyFormationArr),
                      0,
                      array($this, 'attackCallback'),
                      array('attackRound' => BossConfig::BATTLE_ROUND),
                      array (
                      	'bgid' => btstore_get()->ARMY[$armyID]['background_id'],
                      	'musicId' => btstore_get()->ARMY[$armyID]['music_path'],
						'type' => BattleType::BOSS,
                      	)
                      );

				if ( $attack_ret['server']['team2'][0]['hp'] != 0 )
				{
					Logger::FATAL("1/10 boss cost hp:%d, but boss not kill!", $boss_cost_hp);
				}

				$boss_cost_hp = $attack_ret['server']['team2'][0]['costHp'];

			}

			$boss_hp = 0;

			//通知成就系统
			//击杀世界boss
			EnAchievements::notify($this->m_uid, AchievementsDef::KILL_WORLD_BOSS, $boss_id);


			//通知前所有人boss死亡
			RPCContext::getInstance()->sendMsg(array(0), 'boss.kill', array($boss_id));

			RPCContext::getInstance()->setSession(BossDef::BOSS_SESSION_KILLER . $boss_id, 1);
			Util::asyncExecute('boss.reward', array($boss_id, $boss_level, BossUtil::getBossStartTime($boss_id),
				BossUtil::getBossEndTime($boss_id), $this->m_uid));
			Util::asyncExecute('boss.rewardForBotList', array($boss_id,
				$boss_level, BossUtil::getBossStartTime($boss_id),
				BossUtil::getBossEndTime($boss_id), Util::getTime()));
		}

		//增加冷却时间和更新攻击的血量
		$attack_hp = $this->m_boss_attack[BossDef::ATTACK_HP] + $boss_cost_hp;
		$attack_time = Util::getTime();
		if ( $is_revive == TRUE )
		{
			$this->m_boss_attack[BossDef::REVIVE] += 1;
			BossDAO::setBossAttack($boss_id, $this->m_uid, $attack_time,
				$attack_hp,	NULL, NULL, $this->m_boss_attack[BossDef::REVIVE], 0, NULL, NULL);
		}
		else
		{
			BossDAO::setBossAttack($boss_id, $this->m_uid, $attack_time,
				$attack_hp, NULL, NULL, NULL, 0, NULL, NULL);
		}

		//更新用户数据
		$user->update();

		//通知成就系统
		EnAchievements::notify($this->m_uid, AchievementsDef::ATTACK_WORLD_BOSS_TIMES, 1);

		//发送消息给boss场景中的其他人
		$return = array (
			BossDef::ATTACK_LIST => BossUtil::getBossAttackHpTop($boss_id,
				 $boss_start_time, $boss_end_time, BossConfig::BOSS_ATTACK_LIST_MAX_NUM),
			BossDef::ATTACK_GROUP => BossUtil::getBossAttackHpGroup($boss_id,
				$boss_start_time, $boss_end_time),
			BossDef::BOSS_HP => $boss_hp,
		);

		if ( $boss_hp <= 0 ||
			rand(0, BossConfig::BOSS_SEND_MAX_PROBABILITY) < BossConfig::BOSS_SEND_PROBABILITY )
		{
			RPCContext::getInstance()->sendFilterMessage('town', $boss_id, 'boss.update', $return);
		}

		//front call back
		$coordinate = City::getTownBirthCoordinate($boss_id);
		RPCContext::getInstance()->transport(0, $coordinate[TownDef::TOWN_BIRTH_COORDINATE_X], $coordinate[TownDef::TOWN_BIRTH_COORDINATE_Y]);

		//返回给当前用户消息
		$return[$success_key] = TRUE;
		$return[BossDef::LAST_ATTACK_TIME] = $attack_time;
		$return['attack_hp'] = $attack_hp;
		$return['fight_ret'] = $attack_ret['client'];
		$return['belly'] = $user->getBelly();
		$return['prestige'] = $user->getPrestige();
		$return['experience'] = $user->getExperience();

		return $return;

	}

	/* (non-PHPdoc)
	 * @see IBoss::over()
	 */
	public function over()
	{
		$boss_id = RPCContext::getInstance()->getTownId();
		$uid = RPCContext::getInstance()->getUid();

		//是否在boss城镇中
		if ( BossUtil::isBossTown($boss_id) == FALSE )
		{
			Logger::FATAL('not in boss town!');
			throw new Exception('fake');
		}

		$user = EnUser::getUserObj();
		$time = Util::getTime();

		$boss_info = BossDAO::getBoss($boss_id);
		$boss_level = $boss_info[BossDef::BOSS_LEVEL];
		$boss_hp = $boss_info[BossDef::BOSS_HP];
		$boss_start_time = $boss_info[BossDef::BOSS_START_TIME];
		$boss_end_time = BossUtil::getBossEndTime($boss_id, $boss_start_time);

		if ( $time < $boss_end_time && $boss_hp != 0 )
		{
			Logger::DEBUG('boss over request expired!');
			return array('is_expired' => TRUE);
		}

		$boss_max_hp = BossUtil::getBossMaxHp($boss_id, $boss_level);
		$is_killer = RPCContext::getInstance()->getSession(BossDef::BOSS_SESSION_KILLER . $boss_id);

		$attack_list = BossUtil::getBossAttackListSorted($boss_id, $boss_start_time, $boss_end_time);
		$order = 0;
		$attack_hp = 0;
		$is_attack = FALSE;
		foreach ( $attack_list as $key => $value )
		{
			$order++;
			if ( $uid == $key )
			{
				$attack_hp = $value;
				$is_attack = TRUE;
				break;
			}
		}

		//通知前端boss结束
		$return = array(
			'is_expired'			=> FALSE,
			'boss_id'				=> $boss_id,
			'is_killed'				=> $is_killer,
			'max_hp'				=> $boss_max_hp,
			'attack_hp'				=> $attack_hp,
		);

		if ( $is_attack == FALSE )
		{
			$return['order']		=	0;
			$return['reward']		=	array();
		}
		else
		{
			$return['order']		=	$order;
			$attack_group = BossUtil::getBossAttackHpGroup($boss_id, $boss_start_time, $boss_end_time);
			$group_id = $user->getGroupId();
			$reward_modulus = self::getRewardModulus($attack_group, $group_id);
			$return['reward']		=	$this->getUserReward($uid, $boss_id, $order, $reward_modulus);
		}

		if ( $is_killer == TRUE )
		{
			$return['killer_reward']	=	$this->getUserReward($uid, $boss_id, 0);
			RPCContext::getInstance()->unsetSession(BossDef::BOSS_SESSION_KILLER);
		}

		return $return;
	}

	/**
	 *
	 * 给battle端的callback
	 *
	 * @param array $attack_ret
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'belly':int
	 * 		'prestige':int
	 * 		'experience':int
	 * }
	 * </code>
	 */
	public function attackCallback($attack_ret)
	{
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('invalid uid:0');
			throw new Exception('fake');
		}

		$user = EnUser::getUserObj();
		$level = $user->getLevel();
		$belly = self::bellyRewardPreAttack($level, $this->m_boss_id);
		$experience = self::experienceRewardPerAttack($level, $this->m_boss_id);

		//获得声望
		$boss_info = BossDAO::getBoss($this->m_boss_id);
		$prestige = self::prestigeRewardPerAttack($level, $this->m_boss_id,
			 $boss_info[BossDef::BOSS_LEVEL], $attack_ret['team2'][0]['costHp']);

		//增加belly
		if ( $user->addBelly($belly) == FALSE )
		{
			Logger::FATAL('attack callback add belly failed!');
			return array();
		}

		//增加阅历
		if ( $user->addExperience($experience) == FALSE )
		{
			Logger::FATAL('attack callback add experience failed');
			return array();
		}

		//增加声望
		if ( $user->addPrestige($prestige) == FALSE )
		{
			Logger::FATAL('attack callback add prestige failed');
			return array();
		}

		return array (
			BossDef::REWARD_BELLY => $belly,
			BossDef::REWARD_PRESTIGE => $prestige,
			BossDef::REWARD_EXPERIENCE => $experience
		);

	}

	/**
	 *
	 * boss战斗即将开始
	 *
	 * @param int $boss_id
	 *
	 */
	public function bossComing($boss_id)
	{
		//发送系统消息和公告
		ChatTemplate::sendBossBeingStart($boss_id);

		$time = BossUtil::getBossStartTime($boss_id);
		TimerTask::addTask(2, $time, 'boss.bossStart', array($boss_id));

	}


	/**
	 *
	 * boss战斗开始
	 *
	 * @param int $boss_id
	 *
	 */
	public function bossStart($boss_id)
	{
		//发送系统消息和公告
		ChatTemplate::sendBossStart($boss_id);

		$start_time = BossUtil::getBossStartTime($boss_id);
		$end_time = BossUtil::getBossEndTime($boss_id);
		TimerTask::addTask(2,
						$end_time,
						'boss.rewardForTimer',
						array($boss_id, $start_time, $end_time));
		TimerTask::addTask(2,
						$end_time + BossConfig::BOSS_END_TIME_SHIFT,
						'boss.bossEnd',
						array($boss_id, $start_time, $end_time));

	}

	public function rewardForTimer($boss_id, $start_time, $end_time)
	{
		$boss_info = BossDAO::getBoss($boss_id);

		//已经由于被击杀而处理完毕
		if ( $boss_info[BossDef::BOSS_HP] == 0 )
		{
			Logger::INFO('boss has killed!boos_id:%d', $boss_id);
			return;
		}
		else
		{
			Util::asyncExecute('boss.reward', array($boss_id, $boss_info[BossDef::BOSS_LEVEL],
				$start_time, $end_time));
			Util::asyncExecute('boss.rewardForBotList', array($boss_id,
				$boss_info[BossDef::BOSS_LEVEL], $start_time, $end_time, $end_time));
		}
	}

	/**
	 *
	 * boss结束
	 *
	 * @param int $boss_id
	 * @param int $start_time
	 * @param int $end_time
	 *
	 * @return
	 */
	public function bossEnd($boss_id, $start_time, $end_time)
	{
		$boss_info = BossDAO::getBoss($boss_id);
		$level = $boss_info[BossDef::BOSS_LEVEL];

		//如果boss存活,则boss level降低
		if ( $boss_info[BossDef::BOSS_HP] > 0 )
		{
			$level -= 1;
			$min_level = BossUtil::getBossMinLevel($boss_id);
			if ( $level < $min_level )
			{
				$level = $min_level;
			}
		}
		//如果boss死亡,则boss level增加
		else
		{
			$level += 1;
			$max_level = BossUtil::getBossMaxLevel($boss_id);
			if ( $level > $max_level )
			{
				$level = $max_level;
			}
		}
		$boss_max_hp = BossUtil::getBossMaxHp($boss_id, $level);
		$time = BossUtil::getBossStartTime($boss_id, $end_time);

		//如果开始时间已经小于当前时间,则选取下一个时间
		if ( $time < Util::getTime() && $time != 0 )
		{
			$time = BossUtil::getBossEndTime($boss_id);
			//如果得到的时间为0,则表示boss的活动已经结束
			if ( $time == 0 )
			{
				Logger::INFO('boss:%d activity is over!', $boss_id);
				return;
			}
			$time = BossUtil::getBossStartTime($boss_id, $time);
		}

		//如果得到的时间为0,则表示boss的活动已经结束
		if ( $time == 0 )
		{
			Logger::INFO('boss:%d activity is over!', $boss_id);
			return;
		}
		else if ( $time == $boss_info[BossDef::BOSS_SQL_START_TIME] )
		{
			Logger::WARNING('boss:%d timer has execute!', $boss_id);
			return;
		}
		//否则开始下一次的活动
		else
		{
			BossDAO::setBoss($boss_id, $boss_max_hp, $level, $time);
			$time -= BossConfig::BOSS_COMING_TIME;
			TimerTask::addTask(0, $time, 'boss.bossComing', array($boss_id));
		}
	}

	/**
	 *
	 * 发送奖励
	 *
	 * @param int $boss_id				boss id
	 * @param int $start_time			boss开始时间点
	 * @param int $end_time				boss结束时间点
	 * @param int $killer				击杀者uid
	 *
	 * @return NULL
	 */
	public function reward($boss_id, $boss_level, $start_time, $end_time, $killer=NULL)
	{
		sleep(BossConfig::BOSS_REWARD_SLEEP_TIME);

		if ( $killer!==NULL )
		{
			$killer_reward = $this->rewardUser($killer, $boss_id, 0);

			//发送公告
			$user = EnUser::getUserObj($killer);
			$killer_userinfo = $user->getTemplateUserInfo();
			ChatTemplate::sendBossKill($killer_userinfo, $boss_id, self::makeMessage($killer_reward));

			//发送邮件
			MailTemplate::sendBossKill($killer, $boss_id, $killer_reward);
		}

		$boss_attack_list = BossUtil::getBossAttackListSorted($boss_id, $start_time, $end_time);
		$attack_group = BossUtil::getBossAttackHpGroup($boss_id, $start_time, $end_time);

		$boss_max_hp = BossUtil::getBossMaxHp($boss_id, $boss_level);

		$first_reward = array();
		$first_uid = 0;
		$first_user = array();
		$first_attack_hp = '';
		$second_reward = array();
		$second_uid = '';
		$second_user = array();
		$second_attack_hp = 0;
		$third_reward = array();
		$third_uid = '';
		$third_attack_hp = 0;
		$third_user = array();
		$order = 0;
		foreach ( $boss_attack_list as $uid => $attack_hp )
		{
			try {
				$order++;
				$user = EnUser::getUserObj($uid);
				$group_id = $user->getGroupId();
				$reward_modulus = self::getRewardModulus($attack_group, $group_id);
				$reward = $this->rewardUser($uid, $boss_id, $order, $reward_modulus);
				if ( $order == 1 )
				{
					$first_reward = $reward;
					$first_uid = $uid;
					$first_attack_hp = BossUtil::getBossAttackHPPrecent($attack_hp, $boss_max_hp);
					//发送攻击血量第一邮件
					MailTemplate::sendBossAttackHpFirst($first_uid, $boss_id, $reward);

					//通知成就系统
					EnAchievements::notify($first_uid, AchievementsDef::WORLD_BOSS_NO1, 1);
				}
				else if ( $order == 2 )
				{
					$second_reward = $reward;
					$second_uid = $uid;
					$second_attack_hp = BossUtil::getBossAttackHPPrecent($attack_hp, $boss_max_hp);
					//发送攻击血量第二邮件
					MailTemplate::sendBossAttackHpSecond($second_uid, $boss_id, $reward);
				}
				else if ( $order == 3 )
				{
					$third_reward = $reward;
					$third_uid = $uid;
					$third_attack_hp = BossUtil::getBossAttackHPPrecent($attack_hp, $boss_max_hp);
					//发送攻击血量第三邮件
					MailTemplate::sendBossAttackHpThird($third_uid, $boss_id, $reward);
				}
				else
				{
					//发送邮件
					MailTemplate::sendBossAttackHpOther($uid, $boss_id, $attack_hp,
						BossUtil::getBossAttackHPPrecent($attack_hp, $boss_max_hp), $order, $reward);
				}

				$user->update();
			}
			catch(Exception $e)
			{
				Logger::FATAL('send boss reward to user:%d failed!order:%d', $uid, $order);
			}
		}

		//发送系统公告
		if ( !empty($first_uid) )
		{
			$user = EnUser::getUserObj($first_uid);
			$first_user = $user->getTemplateUserInfo();
			ChatTemplate::sendBossAttackHPFirst($first_user, $boss_id);
		}

		if ( !empty($second_uid) )
		{
			$user = EnUser::getUserObj($second_uid);
			$second_user = $user->getTemplateUserInfo();
			ChatTemplate::sendBossAttackHPSecond($second_user, $boss_id);
		}

		if ( !empty($third_uid) )
		{
			$user = EnUser::getUserObj($third_uid);
			$third_user = $user->getTemplateUserInfo();
			ChatTemplate::sendBossAttackHPThird($third_user, $boss_id);
		}

		//broatcast
		ChatTemplate::sendBossAttackHP($boss_id, $first_user, $first_attack_hp,
			self::makeMessage($first_reward), $second_user,
			$second_attack_hp, self::makeMessage($second_reward),
			$third_user, $third_attack_hp, self::makeMessage($third_reward));

		//更新物品数据
		ItemManager::getInstance()->update();

	}

	public function rewardForBotList($boss_id, $boss_level,
		$boss_start_time, $boss_end_time, $kill_time)
	{
		sleep(BossConfig::BOSS_REWARD_SLEEP_TIME);
		$boss_bot_list = BossUtil::getBossBotList($boss_id, $boss_start_time, $boss_end_time);

		foreach ( $boss_bot_list as $value )
		{
			$uid = $value[BossDef::UID];
			$flags = $value[BossDef::FLAGS];
			RPCContext::getInstance()->executeTask($uid, 'boss.rewardForBot',
				 array($uid, $boss_id, $boss_level, $boss_start_time, $boss_end_time, $kill_time, $flags));
		}
	}


	public function rewardForBot($uid, $boss_id, $boss_level,
		$boss_start_time, $boss_end_time, $kill_time, $flags)
	{
		if ( RPCContext::getInstance()->getUid() == NULL )
		{
			RPCContext::getInstance()->setSession('global.uid', $uid);
		}
		$attack_hp_once = $this->attackBossForBot($boss_id);
		$user = EnUser::getUserObj();
		$user_level = $user->getLevel();
		$boss_max_hp = BossUtil::getBossMaxHp($boss_id, $boss_level);

		$gold = $user->getGold();

		if ( $kill_time > $boss_end_time )
		{
			$kill_time = $boss_end_time;
		}

		$duration_time = $kill_time - $boss_start_time;
		$sub_attack_time = 0;
		$sub_attack_time_gold = 0;
		if ( Boss::isSetBossBotSubTimeFlags($flags) == TRUE )
		{
			$sub_attack_time = min(intval($gold/BossConfig::SUB_CDTIME_REQ_GOLD),
				intval($duration_time/BossConfig::BOSS_BOT_SUB_ATTACK_TIME));

			$sub_attack_time_gold = $sub_attack_time * BossConfig::SUB_CDTIME_REQ_GOLD;
			$user->subGold($sub_attack_time_gold);
		}

		$normal_attack_time = intval(($duration_time - $sub_attack_time * BossConfig::BOSS_BOT_SUB_ATTACK_TIME)
			/ BossConfig::BOSS_BOT_ATTACK_TIME);

		$total_attack_time = $normal_attack_time + $sub_attack_time;

		$total_attack_hp = $total_attack_time * $attack_hp_once;

		$attack_list = BossUtil::getBossAttackListSorted($boss_id, $boss_start_time, $boss_end_time);
		$order = BossConfig::BOSS_BOT_ORDER_EXCURSION;
		foreach ( $attack_list as $key => $value )
		{
			if ( $total_attack_hp >= $value )
			{
				break;
			}
			$order++;
		}

		$attack_belly = self::bellyRewardPreAttack($user_level, $boss_id) * $total_attack_time;
		$attack_experience = self::experienceRewardPerAttack($user_level, $boss_id) * $total_attack_time;
		$attack_prestige = self::prestigeRewardPerAttack($user_level, $boss_id,
				 $boss_level, $attack_hp_once) * $total_attack_time;

		//增加belly
		if ( $user->addBelly($attack_belly) == FALSE )
		{
			Logger::FATAL('attack callback add belly failed!');
		}

		//增加阅历
		if ( $user->addExperience($attack_experience) == FALSE )
		{
			Logger::FATAL('attack callback add experience failed');
		}

		//增加声望
		if ( $user->addPrestige($attack_prestige) == FALSE )
		{
			Logger::FATAL('attack callback add prestige failed');
		}

		$attack_group = BossUtil::getBossAttackHpGroup($boss_id, $boss_start_time, $boss_end_time);
		$group_id = $user->getGroupId();
		$reward_modulus = self::getRewardModulus($attack_group, $group_id);
		$reward = self::rewardUser($user->getUid(), $boss_id, $order, $reward_modulus);

		//send mail
		if ( Boss::isSetBossBotSubTimeFlags($flags) == TRUE )
		{
			MailTemplate::sendBossBotSubTime($uid, $boss_id, $total_attack_hp,
				BossUtil::getBossAttackHPPrecent($total_attack_hp, $boss_max_hp),
				$order, $attack_experience, $attack_prestige, $reward,
				$sub_attack_time, $sub_attack_time_gold);
		}
		else
		{
			MailTemplate::sendBossBot($uid, $boss_id, $total_attack_hp,
				BossUtil::getBossAttackHPPrecent($total_attack_hp, $boss_max_hp),
				$order, $attack_experience, $attack_prestige, $reward);
		}

		//update Item
		if ( !empty($reward[BossDef::REWARD_ITEMS]) )
		{
			ItemManager::getInstance()->update();
		}

		//update user
		$user->update();

		//在线用户，推到前端
		if ($user->isOnline())
		{
			$array = array(
				'gold_num' => $user->getGold(),
				'belly_num' => $user->getBelly(),
				'experience_num' => $user->getExperience(),
				'prestige_num' => $user->getPrestige(),
			);

			RPCContext::getInstance()->sendMsg(array($uid), 're.user.updateUser', $array);
		}

		//统计
 		Statistics::gold(StatisticsDef::ST_FUNCKEY_BOSS_BOT_SUBCDTIME,
 				 		 $sub_attack_time_gold,
 				 		 Util::getTime());

		Logger::INFO('user:%d in boss:%d at time:%d boss bot end!', $uid, $boss_id, $boss_start_time);
		Logger::INFO('user:%d total_attack_hp:%d order:%d sub_attack_time:%d sub_attack_time_gold:%d',
			$uid, $total_attack_hp, $order, $sub_attack_time, $sub_attack_time_gold);
	}

	private function attackBossForBot($boss_id)
	{
		$user = EnUser::getUserObj();
		$userFormation = EnFormation::getFormationInfo();
		// 将阵型ID设置为用户当前默认阵型
		$formationID = $user->getCurFormation();
		$user->prepareItem4CurFormation();
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation, TRUE);

		Logger::debug('The hero list is %s', $userFormationArr);
		$battle_user=array(
						'uid' => $user->getUid(),
						'name' => $user->getUname(),
			            'level' => $user->getLevel(),
			            'flag' => 0,
			            'formation' => $formationID,
			            'isPlayer' => 1,
			            'arrHero' => $userFormationArr
					);


		$armyID = BossUtil::getBossArmyId($boss_id);
		$teamID = btstore_get()->ARMY[$armyID]['monster_list_id'];

		$boss_info = BossDAO::getBoss($boss_id);
		$boss_level = $boss_info[BossDef::BOSS_LEVEL];
		// 敌人信息
		$enemyFormation = BossUtil::getBossFormationInfo($boss_id, $boss_level);

		// 将对象转化为数组
		$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation);

		// 调用战斗模块
		$bt = new Battle();
		$maxTime = 2;
		$costHp = 0;
		for ( $i = 0; $i < $maxTime; $i++ )
		{
			$attack_ret = $bt->doHero($battle_user,
		                      array(
		                      		'uid' => $armyID,
		                      		'name' => btstore_get()->ARMY[$armyID]['name'],
		                            'level' => $boss_level,
		                            'flag' => 0,
		                            'formation' => btstore_get()->TEAM[$teamID]['fid'],
		                            'isPlayer' => 0,
		                            'arrHero' => $enemyFormationArr),
		                      0,
		                      NULL,
		                      array('attackRound' => BossConfig::BATTLE_ROUND),
		                      array (
		                      	'bgid' => btstore_get()->ARMY[$armyID]['background_id'],
		                      	'musicId' => btstore_get()->ARMY[$armyID]['music_path'],
								'type' => BattleType::BOSS,
		                      	)
		                      );
		     $costHp += $attack_ret['server']['team2'][0]['costHp'];
		}

		return $costHp / $maxTime;
	}

	/**
	 *
	 * 是否可以攻击
	 *
	 * @param int $last_attack_time
	 * @param int $flags
	 *
	 * @return boolean
	 */
	private function isAttack($last_attack_time, $flags)
	{
		$next_attack_time = $last_attack_time + BossConfig::FREEZE_TIME;
		if ( $flags & BossDef::FLAGS_SUB_CD_TIME )
		{
			$next_attack_time -= BossConfig::SUB_CDTIME;
		}

		if ( $next_attack_time <= Util::getTime() )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 *
	 * 初始化boss(为script/BossInit.class.php提供支持)
	 *
	 * @param int $boss_id
	 *
	 * @return NULL
	 */
	public function initBoss($boss_id)
	{
		$boss_init_level = BossUtil::getBossInitLevel($boss_id);
		//如果server start time被设置
		$server_start_time = strtotime(GameConf::SERVER_OPEN_YMD);
		if ( $server_start_time < Util::getTime() )
		{
			$server_start_time = Util::getTime();
		}

		$start_time = BossUtil::getBossStartTime($boss_id, $server_start_time);
		$end_time = BossUtil::getBossEndTime($boss_id, $server_start_time);

		//如果刚好处于一个boss战斗周期内
		if ( $start_time <= Util::getTime() && $end_time > Util::getTime() )
		{
			$start_time = BossUtil::getBossStartTime($boss_id, $end_time);
		}

		//如果boss已经结束
		if ( $start_time == 0 )
		{
			Logger::INFO('boss:%d activity is over!', $boss_id);
			return;
		}

		$coming_time = $start_time - BossConfig::BOSS_COMING_TIME;
		TimerTask::addTask(0, $coming_time, 'boss.bossComing', array($boss_id));
		Logger::INFO('init boss:%d, start_time:%s', $boss_id, date('Y-m-d H:i:s', $start_time));
		BossDAO::initBoss($boss_id, BossUtil::getBossMaxHp($boss_id, $boss_init_level),
				BossUtil::getBossInitLevel($boss_id), $start_time);
	}

	private function setBossBotFlags($flags, $sub_cd)
	{
		if ( $sub_cd )
		{
			$flags |= BossDef::FLAGS_BOT_SUB_CD_TIME;
		}

		$flags |= BossDef::FLAGS_BOT;

		return $flags;
	}

	private function unsetBossBotFlags($flags)
	{
		$flags = $flags & (~BossDef::FLAGS_BOT_SUB_CD_TIME);
		$flags = $flags & (~BossDef::FLAGS_BOT);

		return $flags;
	}

	private function isSetBossBotFlags($flags)
	{
		if ( $flags & BossDef::FLAGS_BOT )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	private function isSetBossBotSubTimeFlags($flags)
	{
		if ( $flags & BossDef::FLAGS_BOT_SUB_CD_TIME )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	private static function bellyRewardPreAttack($user_level, $boss_id)
	{
		return BossUtil::getBossBellyPerAttack($boss_id) * $user_level;
	}

	private static function experienceRewardPerAttack($user_level, $boss_id)
	{
		return BossUtil::getBossExperiencePerAttack($boss_id) * $user_level;
	}

	private static function prestigeRewardPerAttack($user_level, $boss_id, $boss_level, $attack_hp)
	{
		//获得声望
		//需求修改于2012-4-21 redmine question:#2418
		$prestige = intval($attack_hp /
			 BossUtil::getBossMaxHp($boss_id, $boss_level) *
			 BossConfig::PRE_ATTACK_PRESTIGE_MODULUS);
		$prestige = max(BossConfig::PRE_ATTACK_MIN_PRESTIGE, $prestige);
		$prestige = min(BossConfig::PRE_ATTACK_MAX_PRESTIGE, $prestige);

		return $prestige;
	}

	private function getUserBossAttackInfo($boss_id)
	{
		$user_attack_info = BossDAO::getBossAttack($boss_id, $this->m_uid);
		if ( empty($user_attack_info) )
		{
			$user = EnUser::getUserObj();
			$uname = $user->getUname();
			$group_id = $user->getGroupId();
			$user_attack_info = array(
				BossDef::BOSS_ID				=>	$boss_id,
				BossDef::LAST_ATTACK_TIME 		=>	0,
				BossDef::LAST_INSPIRE_TIME		=>	0,
				BossDef::ATTACK_HP				=>	0,
				BossDef::INSPIRE				=>	0,
				BossDef::REVIVE					=>	0,
				BossDef::FLAGS					=>	0,
			);
			BossDAO::initBossAttack($boss_id, $this->m_uid, 0, 0, 0, 0, 0, 0, $uname, $group_id);
		}
		else if ( ( $user_attack_info[BossDef::LAST_ATTACK_TIME] < BossUtil::getBossStartTime($boss_id)
			&& $user_attack_info[BossDef::LAST_ATTACK_TIME] != 0 ) || (
			$user_attack_info[BossDef::LAST_INSPIRE_TIME] < BossUtil::getBossStartTime($boss_id)
			&& $user_attack_info[BossDef::LAST_INSPIRE_TIME] != 0 ) )
		{
			$user = EnUser::getUserObj();
			$uname = $user->getUname();
			$group_id = $user->getGroupId();
			$user_attack_info[BossDef::LAST_ATTACK_TIME]	= 0;
			$user_attack_info[BossDef::LAST_INSPIRE_TIME]	= 0;
			$user_attack_info[BossDef::INSPIRE]				= 0;
			$user_attack_info[BossDef::REVIVE]				= 0;
			$user_attack_info[BossDef::ATTACK_HP]			= 0;
			$user_attack_info[BossDef::FLAGS]				= 0;
			BossDAO::setBossAttack($boss_id, $this->m_uid, 0, 0, 0, 0, 0, 0, $uname, $group_id);
		}
		return $user_attack_info;
	}

	private function getUserReward($uid, $boss_id, $order,
			$reward_modulus = BossConfig::GROUP_DEFAULT_REWARD_MODULUS)
	{
		$reward = BossUtil::getBossReward($boss_id, $order);

		$user = EnUser::getUserObj($uid);
		$level = $user->getLevel();

		$belly = intval($reward[BossDef::REWARD_BELLY] * $level * $reward_modulus);
		$experience = intval($reward[BossDef::REWARD_EXPERIENCE] * $level * $reward_modulus);
		$prestige = intval($reward[BossDef::REWARD_PRESTIGE] * $reward_modulus);

		return array(
			BossDef::REWARD_BELLY => $belly,
			BossDef::REWARD_PRESTIGE => $prestige,
			BossDef::REWARD_EXPERIENCE => $experience,
			BossDef::REWARD_GOLD => intval($reward[BossDef::REWARD_GOLD]),
			BossDef::REWARD_ITEMS => array(),
		);
	}

	private function rewardUser($uid, $boss_id, $order,
			$reward_modulus = BossConfig::GROUP_DEFAULT_REWARD_MODULUS)
	{
		$reward = BossUtil::getBossReward($boss_id, $order);

		$belly = $reward[BossDef::REWARD_BELLY];
		$prestige = $reward[BossDef::REWARD_PRESTIGE];
		$experience = $reward[BossDef::REWARD_EXPERIENCE];
		$gold = $reward[BossDef::REWARD_GOLD];
		$drop_template_id = $reward[BossDef::REWARD_DROP_TEMPLATE_ID];

		$user = EnUser::getUserObj($uid);
		$level = $user->getLevel();

		$belly = intval($belly * $level * $reward_modulus);
		$experience = intval($experience * $level * $reward_modulus);
		$prestige = intval($prestige * $reward_modulus);

		if ( !empty($belly) && $user->addBelly($belly) == FALSE )
		{
			Logger::FATAL('add belly failed');
			throw new Exception('fake');
		}

		if ( !empty($prestige) && $user->addPrestige($prestige) == FALSE )
		{
			Logger::FATAL('add prestige failed');
			throw new Exception('fake');
		}

		if ( !empty($experience) && $user->addExperience($experience) == FALSE )
		{
			Logger::FATAL('add experience failed');
			throw new Exception('fake');
		}

		if ( !empty($gold) )
		{
			if ( $user->addGold($gold) == FALSE )
			{
				Logger::FATAL('add gold failed');
				throw new Exception('fake');
			}

			Logger::INFO('user:%d get boss reward gold:%d', $uid, $gold);
			//Statictics
			Statistics::gold(StatisticsDef::ST_FUNCKEY_BOSS_REWARD,
					$gold, Util::getTime(), FALSE, $user->getPid() );
		}

		$item_ids = array();
		if ( !empty($drop_template_id) )
		{
			$item_ids = ItemManager::getInstance()->dropItem($drop_template_id);

			//发送获得物品公告
			ChatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(),
				ChatTemplate::prepareItem($item_ids));
		}

		return array(
			BossDef::REWARD_BELLY => intval($belly),
			BossDef::REWARD_PRESTIGE => intval($prestige),
			BossDef::REWARD_EXPERIENCE => intval($experience),
			BossDef::REWARD_GOLD => intval($gold),
			BossDef::REWARD_ITEMS => $item_ids,
		);
	}

	/**
	 *
	 * 得到某个阵营的奖励系数
	 *
	 * @param array $attack_group
	 * @param int $group_id
	 *
	 * @return float
	 */
	private static function getRewardModulus($attack_group, $group_id)
	{
		if ( !isset($attack_group[$group_id]) )
		{
			Logger::FATAL('invalid group id!', $group_id);
			return BossConfig::GROUP_DEFAULT_REWARD_MODULUS;
		}

		arsort($attack_group);

		$order = 0;
		foreach ( $attack_group as $key => $value )
		{
			$order++;
			if ( $key == $group_id )
			{
				break;
			}
		}

		if ( $order == 1 )
		{
			return BossConfig::GROUP_FIRST_REWARD_MODULUS;
		}
		else if ( $order == 2 )
		{
			return BossConfig::GROUP_SECOND_REWARD_MODULUS;
		}
		else
		{
			return BossConfig::GROUP_THIRD_REWARD_MODULUS;
		}

	}

	public static function makeMessage($reward)
	{
		if ( empty($reward) )
		{
			return array();
		}

		$_reward = array();
		if ( $reward[BossDef::REWARD_BELLY] != 0 )
		{
			$_reward[BossDef::REWARD_BELLY] = $reward[BossDef::REWARD_BELLY];
		}
		if ( $reward[BossDef::REWARD_PRESTIGE] != 0 )
		{
			$_reward[BossDef::REWARD_PRESTIGE] = $reward[BossDef::REWARD_PRESTIGE];
		}
		if ( $reward[BossDef::REWARD_EXPERIENCE] != 0 )
		{
			$_reward[BossDef::REWARD_EXPERIENCE] = $reward[BossDef::REWARD_EXPERIENCE];
		}
		if ( $reward[BossDef::REWARD_GOLD] != 0 )
		{
			$_reward[BossDef::REWARD_GOLD] = $reward[BossDef::REWARD_GOLD];
		}
		if ( !empty($reward[BossDef::REWARD_ITEMS]) )
		{
			$reward_items = array();
			foreach ( $reward[BossDef::REWARD_ITEMS] as $item_id)
			{
				$item = ItemManager::getInstance()->getItem($item_id);
				if ( $item !== NULL )
				{
					if ( $item->canStackable() == TRUE )
					{
						$reward_items[] = array(
							'item_id' => ItemDef::ITEM_ID_NO_ITEM,
							'item_template_id' => $item->getItemTemplateId(),
							'item_num' => $item->getItemNum(),
						);
					}
					else
					{
						$reward_items[] = $item->itemInfo();
					}
				}
			}
			$_reward[BossDef::REWARD_ITEMS] = $reward_items;
		}
		return $_reward;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
