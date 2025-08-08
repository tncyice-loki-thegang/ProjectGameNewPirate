<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArenaLogic.class.php 35118 2013-01-09 10:50:15Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/arena/ArenaLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-09 18:50:15 +0800 (三, 2013-01-09) $
 * @version $Revision: 35118 $
 * @brief
 *
 **/


/**
 * warning:
 * challenge会修改其它用户的数据。也就是当前用户和被挑战的用户都能被修改。
 * 所以update的时候只update修改的数据，不能省事update所有。
 * 字段						 修改人   是否加锁
 * uid
 * position					  all	 lock
 * last_challenge_time		  self	 safe
 * challenge_num			  self	 safe
 * last_buy_challenge_time	  self	 safe
 * added_challenge_num		  self	 safe
 * cur_suc					  all	 lock
 * history_max_suc			  all	 lock
 * history_min_position		  all	 safe
 * upgrade_continue			  all	 lock
 * fight_cdtime				  self	 safe
 * va_opponents				  all	 lock
 * reward_time				  self	 safe
 * va_reward				  self	 safe
 *
 * 拉对手信息的时候只使用$arrAtkedField.
 * update被攻击用户的时候只update这几个数据。
 *
 * 在challenge （这个命令加锁了）以外的命令不要这几个数据
 * @author idyll
 *
 */


class ArenaLogic
{
	static $arrAtkedField = array('uid',
                               'position',
                               'cur_suc',
                               'history_max_suc',
                               'va_opponents');

	static $arrField = array('uid',
                             'position',
                             'challenge_num',
							 'added_challenge_num',
                             'last_challenge_time',
                             'last_buy_challenge_time',
                             'cur_suc',
                             'history_max_suc',
                             'history_min_position',
                             'upgrade_continue',
                             'fight_cdtime',
                             'va_opponents');

	static $arrMsgField = array('attack_uid',
                                'attack_name',
                                'defend_uid',
                                'defend_name',
                                'attack_time',
                                'attack_position',
                                'defend_position',
                                'attack_res',
								'attack_replay');

	public static function getAtkedByPos($pos)
	{
		return ArenaDao::getByPos($pos, self::$arrAtkedField);
	}

	public static function getInfo($uid)
	{
		$arrRet = array('ret'=>'ok', 'res'=>array());
		$res = self::get($uid);
		if (empty($res))
		{
			////从来没有进入过竞技场
			$ret = self::joinArena($uid);
            if ('ok'!=$ret)
            {
            	$arrRet['ret'] = $ret;
            	return $arrRet;
            }
            $res = self::get($uid);
		}

		$oppts = self::getOpponents($res['va_opponents']);
		if ($oppts === false)
		{
			Logger::fatal('fail to get opponents in arena');
			throw new Exception('sys');
		}
		$res['opponents'] = $oppts;

		//返回给前端发奖时间
		$res['reward_time'] = ArenaRound::getRewardTime();
		$res['last_day'] = ArenaDateConf::LAST_DAYS;

		$arrRet['res'] = $res;
		return $arrRet;
	}

	public static function joinArena ($uid)
	{
		$alock = new ArenaLock();

		if (false==$alock->lock('insert'))
		{
			Logger::fatal('fail to lock t_arena for insert data');
			return 'lock';
		}

		$maxPosition = ArenaDao::getMaxPostion();
		$pos = $maxPosition + 1;
		$opptPos = self::getOpponentPosition($pos);
		$arrField = array('position' => $pos,
						  'last_challenge_time' => 0,
						  'challenge_num' => 0,
						  'last_buy_challenge_time' => 0,
						  'added_challenge_num' =>0,
						  'cur_suc' => 0,
						  'history_min_position'=>$pos,
						  'upgrade_continue' => 0,
						  'fight_cdtime' => 0,
						  'va_opponents' => $opptPos,
						  'reward_time' => 0,
						  'va_reward'=>array());
		ArenaDao::insert($uid, $arrField);
		
		if ($pos<11)
		{
			//产生幸运奖的排名, 竞技场发奖日期
			ArenaLuckyLogic::generatePosition();
		}

		$alock->unlock();
		return 'ok';
	}

	public static function get($uid, $arrField=null)
	{
		if ($arrField==null)
		{
			$arrField = self::$arrField;
		}
		$info = ArenaDao::get($uid, $arrField);

		if (empty($info))
		{
			return $info;
		}

		//不是同一天
		if (isset($info['last_challenge_time'])
			&& !Util::isSameDay($info['last_challenge_time']))
		{
			$info['challenge_num'] = 0;
		}
        //不是今天购买的补充次数需要清空
        if (isset($info['last_buy_challenge_time'])
        	&& !Util::isSameDay($info['last_buy_challenge_time']))
        {
            $info['last_buy_challenge_time'] = 0;
            $info['added_challenge_num'] = 0;
        }
        return $info;
	}

	public static function hasReward($uid)
	{
		$info = ArenaDao::get($uid, array('va_reward', 'reward_time'));
		if (empty($info))
		{
			return false;
		}

		//没有奖励， 可能已经领了
		if (empty($info['va_reward']))
		{
			return false;
		}
		
		//过期
		//当前时间-ArenaDateConf::LAST_DAY   大于发奖时间
		$strRewardTime ="-1" . ArenaDateConf::LAST_DAYS . "day";
		$rewardTime = strtotime($strRewardTime, Util::getTime());
		if ($rewardTime > $info['reward_time'])
		{
			return false;			
		}
			 
		return true;
	}

    public static function getPositionReward($uid)
    {
        $arrRet = array('ret'=>'ok', 'res'=>array());

        $info = ArenaDao::get($uid, array('va_reward'));
        if (empty($info))
        {
            Logger::warning('fail to getPositionReward, no info in arena.');
            throw new Exception('fake');
        }

        if (empty($info['va_reward']))
        {
            $arrRet['ret'] = 'fail';
            return $arrRet;
        }

        if (!ArenaDao::updateReward($uid))
        {
        	$arrRet['ret'] = 'out_of_date';
        	return $arrRet;
        }

        $arrRes = array();
        $arrRes['belly'] = $info['va_reward']['belly'];
        $arrRes['prestige'] = $info['va_reward']['prestige'];
        $arrRes['experience'] = $info['va_reward']['experience'];
        $arrRes['position'] = $info['va_reward']['position'];

        $user = EnUser::getUserObj($uid);
        $user->addBelly($arrRes['belly']);
        $user->addPrestige($arrRes['prestige']);
        $user->addExperience($arrRes['experience']);
        $user->update();

        $arrRet['res'] = $arrRes;
        return $arrRet;
    }

    //战斗模块回调会用到
    private static $g_level = 0;
    private static $g_arrReward = array();

    /**
     * 为战斗模块构造输入数组
     * Enter description here ...
     * @param UserObj $user
     * @throws Exception
     * @return array()
     */
    private static function getUserForBattle($user)
    {
    	// 将阵型ID设置为用户当前默认阵型
		$userFormation = EnFormation::getFormationInfo($user->getUid());
		$hasHero = false;
		foreach ($userFormation as $heroTmp)
		{
			if ($heroTmp instanceof OtherHeroObj)
			{
				$hasHero = true;
				break;
			}
		}

		if (!$hasHero)
		{
			Logger::warning('the cur formation has no hero');
			throw new Exception('fake');
		}

		$formationID = $user->getCurFormation();
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation, true);
		Logger::debug('The hero list is %s', $userFormationArr);
		return array('name' => $user->getUname(),
                             'level' => $user->getLevel(),
							 'isPlayer' => true,
                             'flag' => 0,
                             'formation' => $formationID,
                             'uid' => $user->getUid(),
                             'arrHero' => $userFormationArr);
    }

    /**
     * 挑战
     * Enter description here ...
     * @param UserObj $user
     * @param UserObj $atkedUser
     * @param 挑战位置
     * @throws Exception
     * @return array($atkRet, $arrBroadcast) 战斗结果， 广播信息
     */
    private static function challenge__($user, $atkedUser, &$info, &$atkedInfo)
    {
    	$oldPositoin = $info['position'];
    	$oldAtkPosition = $atkedInfo['position'];
    	
    	$user->prepareItem4CurFormation();
    	$atkedUser->prepareItem4CurFormation();
		$battleUser= self::getUserForBattle($user);
		$atkedBattleUser = self::getUserForBattle($atkedUser);
		$bt = new Battle();
		$atkRet = $bt->doHero($battleUser, $atkedBattleUser, 0, array('ArenaLogic', 'battleCallback'),
			null, array('bgid'=>ArenaConf::BATTLE_BJID, 'musicId'=>ArenaConf::BATTLE_MUSIC_ID, 'type'=>BattleType::ARENA));

		// 战斗系统返回值
		Logger::debug('Ret from battle is %s.', $atkRet);
		//modify		
		$info['challenge_num'] += 1;
		$info['last_challenge_time'] = Util::getTime();

        $diffPos = $info['position'] - $atkedInfo['position'];
        $isSuc = BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'];
        $arrBroadcast = array();
		// 打败对方
		if ($isSuc)
		{
			$info['fight_cdtime'] = Util::getTime() + ArenaConf::FIGHT_SUC_CDTIME;
			
            //打败比我名次高的
            if ($diffPos > 0)
            {
            	$atkedInfo['position'] += $diffPos;
                $info['position'] -= $diffPos;
                 //第一名广播
                if ($info['position']==1)
                {
                	$arrBroadcast[ArenaBroadcast::PRI_TOP1] = 1;
                	//成就
                	EnAchievements::notify($user->getUid(), AchievementsDef::ARENA_NO_1, 1);
                	
                	ChatTemplate::sendArenaTopFailed($user->getTemplateUserInfo(), 
                		$atkedUser->getTemplateUserInfo(), 
                		$atkRet['server']['brid']);
                }
                $atkedInfo['va_opponents'] = self::getOpponentPosition($atkedInfo['position']);

                //连续上升名次广播
                $broadcastUpgrade = self::isBroadcastUpgradeContinue($info['upgrade_continue'],
                	$info['upgrade_continue'] + $diffPos);
                if (-1!=$broadcastUpgrade)
                {
                	$arrBroadcast[ArenaBroadcast::PRI_UPGRADE_CONTINUE] = $broadcastUpgrade;
                }
                $info['upgrade_continue'] += $diffPos;
                if ($info['history_min_position'] > $info['position'])
                {
                	$info['history_min_position'] = $info['position'];
                }

                //成就
                EnAchievements::notify($user->getUid(), AchievementsDef::ARENA_POSITION_UP, $atkedInfo['position'], $info['position']);
            }

            //连胜终止广播
            if ($atkedInfo['cur_suc'] >= ArenaBroadcast::MIN_CONTINUE_END)
            {
            	$arrBroadcast[ArenaBroadcast::PRI_CONTINUE_END] = $atkedInfo['cur_suc'];
            }
            $atkedInfo['cur_suc'] = 0;
            $atkedInfo['upgrade_continue'] = 0;

			$info['va_opponents'] = self::getOpponentPosition($info['position']);

            $info['cur_suc'] += 1;
            //连胜广播
            if (isset(ArenaBroadcast::$ARR_CONTINUE_SUC[$info['cur_suc']]))
            {
            	$arrBroadcast[ArenaBroadcast::PRI_CONTINUE_SUC] = $info['cur_suc'];
            }

            if ($info['cur_suc'] > $info['history_max_suc'])
            {
                $info['history_max_suc'] = $info['cur_suc'];
                //连胜成就
            	EnAchievements::notify($user->getUid(), AchievementsDef::ARENA_KEEP_WIN_NUM, $info['cur_suc']);
            }
		}
		else
		{
			$info['fight_cdtime'] = Util::getTime() + ArenaConf::FIGHT_FAIL_CDTIME;
			
            //连胜终止
            $info['cur_suc'] = 0;
            $info['upgrade_continue'] = 0;

            //被攻击连胜加1
            $atkedInfo['cur_suc'] += 1;
            if ($atkedInfo['cur_suc'] > $atkedInfo['history_max_suc'])
            {
                $atkedInfo['history_max_suc'] = $atkedInfo['cur_suc'];
                //连胜成就
            	EnAchievements::notify($atkedUser->getUid(), AchievementsDef::ARENA_KEEP_WIN_NUM, $atkedInfo['cur_suc']);
            }
            //防守方胜利的广播， 只有这一条
            if (isset(ArenaBroadcast::$ARR_CONTINUE_SUC[$atkedInfo['cur_suc']]))
            {
            	$templateId = self::getContinueSucTempId(ArenaBroadcast::$ARR_CONTINUE_SUC[$atkedInfo['cur_suc']]);
            	$message = ChatTemplate::makeMessage($templateId, array($atkedUser->getTemplateUserInfo()));
            	self::arenaBroadcast($message);
            }
		}

		Logger::debug("updateChallenge atk:%s, atked:%s", $info, $atkedInfo);
		$user->update();
						
        ArenaDao::updateChallenge($info, $atkedInfo, $oldPositoin, $oldAtkPosition);
        return array($atkRet, $arrBroadcast);
    }

    public static function arenaBroadcast($message)
    {
    	$message_text = array (
    		'message_text' => $message,
    	);
    	RPCContext::getInstance()->sendFilterMessage('arena', ArenaDef::ARENA_ID, 'arena.refreshBroadcast', $message_text);
    	ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
		self::setBroadcast($message_text);
    }

    //进攻方胜利广播
	private static function arenaAtkBroadcast($arrBroadcast, $user, $atkedUser)
	{
		if (empty($arrBroadcast))
		{
			return;
		}
		//根据广播的优先级别，发优先级最高的广播
		ksort($arrBroadcast);
		$broadcastType = key($arrBroadcast);
		switch ($broadcastType)
		{
		case ArenaBroadcast::PRI_TOP1:
			$message = ChatTemplate::makeMessage(ChatTemplateID::MSG_ARENA_TOP_CHANGE,
			array (
				$user->getTemplateUserInfo(),
				$atkedUser->getTemplateUserInfo(),
			)
			);
			break;
		case ArenaBroadcast::PRI_CONTINUE_END:
			$message = ChatTemplate::makeMessage(ChatTemplateID::MSG_ARENA_CONSECUTIVE_END,
			array (
				$user->getTemplateUserInfo(),
				 $atkedUser->getTemplateUserInfo(),
				current($arrBroadcast),
			)
		);
			break;
		case ArenaBroadcast::PRI_CONTINUE_SUC:
			$templateId = self::getContinueSucTempId(ArenaBroadcast::$ARR_CONTINUE_SUC[current($arrBroadcast)]);
			$message = ChatTemplate::makeMessage($templateId,
			array (
				$user->getTemplateUserInfo(),
			)
			);
			break;
		case ArenaBroadcast::PRI_UPGRADE_CONTINUE:
			$templateId = self::getUpgradeTempldateId(ArenaBroadcast::$ARR_UPGRADE_CONTINUE[current($arrBroadcast)]);
			$message = ChatTemplate::makeMessage($templateId,
			array (
				$user->getTemplateUserInfo(),
			)
		);
			break;
		default:
			Logger::fatal('sys err. unknown arena broadcast type:%d', $broadcastType);
			return;
		}

		self::arenaBroadcast($message);
	}

	private static function getUpgradeTempldateId($type)
	{
		$templateId = ChatTemplateID::MSG_ARENA_LEVELUP_0;
		switch ( $type )
		{
			case 0:
				$templateId = ChatTemplateID::MSG_ARENA_LEVELUP_0;
				break;
			case 1:
				$templateId = ChatTemplateID::MSG_ARENA_LEVELUP_1;
				break;
			case 2:
				$templateId = ChatTemplateID::MSG_ARENA_LEVELUP_2;
				break;
			case 3:
				$templateId = ChatTemplateID::MSG_ARENA_LEVELUP_3;
				break;
			default:
				Logger::FATAL('invalid arena levelup type:%d', $type);
				throw new Exception('fake');
				break;
		}
		return $templateId;
	}

	private static function getContinueSucTempId($type)
	{
		$templateId = ChatTemplateID::MSG_ARENA_CONSECUTIVE_0;
		switch ( $type )
		{
			case 0:
				$templateId = ChatTemplateID::MSG_ARENA_CONSECUTIVE_0;
				break;
			case 1:
				$templateId = ChatTemplateID::MSG_ARENA_CONSECUTIVE_1;
				break;
			case 2:
				$templateId = ChatTemplateID::MSG_ARENA_CONSECUTIVE_2;
				break;
			case 3:
				$templateId = ChatTemplateID::MSG_ARENA_CONSECUTIVE_3;
				break;
			default:
				Logger::FATAL('invalid arena consecutive type:%d', $type);
				throw new Exception('fake');
				break;
		}
		return $templateId;
	}

	//arena广播消息的key
	const BROADCAST_MSG_KEY = 'arena.broadcastMsg';

	public static function setBroadcast($message)
	{
		McClient::set(self::BROADCAST_MSG_KEY, $message);
	}

	public static function getBroadcast()
	{
		$ret =  McClient::get(self::BROADCAST_MSG_KEY);
		if ($ret==null)
		{
			return array();
		}
		return $ret;
	}

	//0 for cd time
	//1 for buy added times
	static $GoldNum = array('cdtime'=>0, 'added_times'=>0);
	public static function subGoldRes()
	{
		if (self::$GoldNum['cdtime']!=0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_ARENA_CDTIME, self::$GoldNum['cdtime'], Util::getTime());
		}

		if (self::$GoldNum['added_times']!=0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_ARENA_ADDED_TIMES, self::$GoldNum['added_times'], Util::getTime());
		}
	}

	private static function subGold($gold, $type)
	{
		if ($type=='cdtime')
		{
			self::$GoldNum[$type] += $gold;
		}
		else if ($type=='added_times')
		{
			self::$GoldNum[$type] += $gold;
		}
		else
		{
			Logger::fatal('sys err. sub gold %s type err in arena.', $type);
		}
	}

	//警告：此函数的出口记得释放lock
	public static function challenge ($uid, $pos, $atkedUid, $buyAddedChallengeNum, $isClearCdtime)
	{
		if (ArenaRound::isLock())
		{
			Logger::warning('fail to challenge in arena, the arena is lock for rewarding.');
			throw new Exception('fake');
		}

		$arrRet = array('ret' => 'ok', 'atk' => array(), 'cost'=>0, 'opponents'=>array());

		//这里的lock不能往下移动了，因为拉出来$info数据可能会被其它用户修改
		$alock = new ArenaLock();
		if (false==$alock->lock($uid, $atkedUid))
		{
			$arrRet['ret'] = 'lock';
			return $arrRet;
		}

		try
		{
			$info = self::get($uid, self::$arrField);
			if (empty($info))
			{
				Logger::fatal('in challenge, fail to query from db by uid %d', $uid);
				throw new Exception('sys');
			}

			$user = EnUser::getUserObj($uid);
			self::$g_level = $user->getLevel();
			self::$g_arrReward = array();

			//是否购买补充挑战次数
			if ($buyAddedChallengeNum != 0)
			{
				$costGold = self::buyAddedChallenge__($uid, $info, $buyAddedChallengeNum, $user->getVip());
				if (!$user->subGold($costGold))
				{
					Logger::warning('fail to buy added challenge, the gold is not enough');
					throw new Exception('fake');
				}
				self::subGold($costGold, 'added_times');
				$arrRet['cost'] += $costGold;
			}
			//check 挑战次数
			//过了免费挑战次数
			if ($info['challenge_num'] >= ArenaConf::FREE_CHALLENGE_NUM)
			{
				if ($info['added_challenge_num'] <= 0)
				{
					Logger::warning('fail to challenge, the num is not enough');
					throw new Exception('fake');
				}
				$info['added_challenge_num'] -= 1;
			}

			//check cd time
			if ($info['fight_cdtime'] > Util::getTime())
			{
				if ($isClearCdtime == 0)
				{
					Logger::warning('fail to challenge in arena, the fight_cdtime isnot arrival.');
					throw new Exception('fake');
				}
				else
				{
					$costGold = self::fightcdtimeCost($info['fight_cdtime']);
					if (!$user->subGold($costGold))
					{
						Logger::warning('fail to buy added challenge, the gold is not enough');
						throw new Exception('fake');
					}
					self::subGold($costGold, 'cdtime');
					$arrRet['cost'] += $costGold;
				}
			}

			//check position
			if (!in_array($pos, $info['va_opponents']))
			{
				Logger::warning('fail to challenge in arena, the postion isnot one of opponents');
				//throw new Exception('fake');
				$arrRet['ret'] = 'position_err';
				$alock->unlock();
				return $arrRet;
			}

			//根据排名得到的用户，这个排名的用户可能已经改变了
			$atkedInfo = self::getAtkedByPos($pos, self::$arrAtkedField);
			if ($atkedUid != 0)
			{
				if ($atkedUid != $atkedInfo['uid'])
				{
					$arrRet['ret'] = 'opponents_err';
					$alock->unlock();
					return $arrRet;
				}
			}
			else
			{
				$atkedUid = $atkedInfo['uid'];
			}

			$atkedUser = EnUser::getUserObj($atkedUid);
			//竞技场消息
			$arrMsgField = array(
            'attack_uid'  => $user->getUid(),
            'attack_name' => $user->getUname(),
            'defend_uid'  => $atkedUser->getUid(),
            'defend_name' => $atkedUser->getUname(),
            'attack_time' => Util::getTime(),
            'attack_position'    => $info['position'],
        	'defend_position' => $atkedInfo['position'],
            'attack_res'  => 0,
        	'attack_replay' => 0,
            );
			list($atkRet, $arrBroadcast) = self::challenge__($user, $atkedUser, $info, $atkedInfo);
			//早点释放， 后面不会修改info, atkedInfo 的数据了
			$alock->unlock();
		}
		catch ( Exception $e )
		{
			Logger::warning('challenge exeception:%s', $e->getMessage());
			$alock->unlock();
			throw $e;
		}
		$arrMsgField['attack_replay'] = $atkRet['server']['brid'];
		$isSuc =  $isSuc = BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'];

        //发送信息给被攻击者
        $sendAtkedInfo = array();
        $sendAtkedInfo['va_opponents'] = array();
        //挑战成功，对手有变动
        if ($isSuc)
        {
        	$arrMsgField['attack_res'] = 1;
        	$sendAtkedInfo['va_opponents'] = $atkedInfo['va_opponents'];
        	$arrRet['opponents'] = self::getOpponents($info['va_opponents']);
        }
        $sendAtkedInfo['position'] = $atkedInfo['position'];
        $sendAtkedInfo['arena_msg'] = $arrMsgField;
        Logger::debug('sync to client:%s', $sendAtkedInfo);
        RPCContext::getInstance()->executeTask($atkedUid, 'arena.arenaDataRefresh', array($sendAtkedInfo));

        //无论输赢都能得到威望	阅历
        list($addPrestige, $addExperience ) = self::$g_arrReward;
        $user->addPrestige($addPrestige);
        $user->addExperience($addExperience);
		$user->update();

		// 将战斗结果返回给前端
		$arrRet['atk'] = array('fightRet' => $atkRet['client'], 'appraisal' => $atkRet['server']['appraisal']);
		$arrRet['experience_num'] = $user->getExperience();
		$arrRet['prestige_num'] = $user->getPrestige();
		$arrRet['fight_cdtime'] = $info['fight_cdtime'];

		//广播
		self::arenaAtkBroadcast($arrBroadcast, $user, $atkedUser);

        //insert msg
        ArenaDao::insertMsg($arrMsgField);
        $arrRet['arena_msg'] = $arrMsgField;
		return $arrRet;
	}

	private static function computeReward($isSuccess, $level)
	{
		if ($isSuccess)
		{
			self::$g_arrReward =  array(ArenaConf::REWARD_SUC_CHALLENGE_PRESTIGE, ArenaConf::REWARD_SUC_CHALLENGE_EXPERIENCE * $level);
		}
		else
		{
			self::$g_arrReward =  array(ArenaConf::REWARD_FAIL_CHALLENGE_PRESTIGE, ArenaConf::REWARD_FAIL_CHALLENGE_EXPERIENCE * $level);
		}
	}

	public static function battleCallback($atkRet)
	{
		$arrRet = array();
		$isSuc = BattleDef::$APPRAISAL[$atkRet['appraisal']] <= BattleDef::$APPRAISAL['D'];
		self::computeReward($isSuc, self::$g_level);
		list($arrRet['prestige'], $arrRet['experience']) = self::$g_arrReward;
		return $arrRet;
	}

	//是否广播连续上升名次
	//我二了， 这个变量名不对。
	public static function isBroadcastUpgradeContinue($oldPos, $newPos)
	{
		$arrPos = array_keys(ArenaBroadcast::$ARR_UPGRADE_CONTINUE);
		$i = 0;
		for (; $i < count($arrPos); $i++)
		{
			if ($newPos < $arrPos[$i])
			{
				break;
			}
		}

		if ($i>0 && $arrPos[$i-1] > $oldPos)
		{
			return $arrPos[$i-1];
		}
		return -1;
	}

	public static function clearCdtime ($uid)
	{
		$info = ArenaDao::get($uid, array('fight_cdtime'));
		$costGold = self::fightcdtimeCost($info['fight_cdtime']);

		$user = EnUser::getUserObj($uid);
		if (!$user->subGold($costGold))
		{
			Logger::warning('fail to clear cdtime for arena, the gold isnot enough');
			throw new Exception('fake');
		}
		$user->update();
		ArenaDao::update($uid, array('fight_cdtime'=>Util::getTime()));
		self::subGold($costGold, 'cdtime');
		return $costGold;
	}

	private static function fightcdtimeCost ($cdtime)
	{
		$diff = $cdtime - Util::getTime();
        if ($diff<=0)
        {
            return 0;
        }
		$costGold = ceil($diff/ArenaConf::CDTIME_PER_GOLD) * 2;
		return $costGold;
	}

	public static function buyAddedChallenge($uid, $num)
    {
        $info = self::get($uid, array('added_challenge_num', 'last_buy_challenge_time'));
		Logger::debug("buy num %d info:%s, ", $num, $info);
        $user = EnUser::getUserObj($uid);
		$costGold = self::buyAddedChallenge__($info, $num, $user->getVip());
    	if (!$user->subGold($costGold))
        {
            Logger::warning('fail to buy added challenge, the gold is not enough');
            throw new Exception('fake');
        }
		Logger::debug("buy info for update:%s", $info);
        ArenaDao::update($uid, $info);
        $user->update();
        self::subGold($costGold, 'added_times');
        return $costGold;
    }

 	private static function buyAddedChallenge__(&$info, $num, $vip)
    {
        if (!isset(btstore_get()->VIP[$vip]['arena_times_gold']))
        {
        	Logger::fatal('no info in VIP for vip %d', $vip);
        	throw new Exception('sys');
        }

        $buyConfig = btstore_get()->VIP[$vip]['arena_times_gold'];

        $max_added_num = $buyConfig['num'];
        if ($info['added_challenge_num'] + $num > $max_added_num )
        {
            Logger::warning("fail to buy added chanllenge, over max num if buying %d. ", $num);
            throw new Exception('fake');
        }

        $costGold = $num * $buyConfig['gold'];
		$info['added_challenge_num'] += $num;
        $info['last_buy_challenge_time'] = Util::getTime();
		Logger::debug("buy info for update:%s", $info);
        return $costGold;
    }

    public static function getPositionList($num)
    {
        $arrList = ArenaDao::getPositionList($num, array('uid', 'position'));
        $arrUid = array_keys($arrList);
        $arrUserInfo = Util::getArrUser($arrUid, array('level', 'uname', 'utid'));
        foreach ($arrList as &$list)
        {
            if (isset($arrUserInfo[$list['uid']]))
            {
                $list = array_merge($list, $arrUserInfo[$list['uid']]);
                $list['master_htid'] = UserConf::$USER_INFO[$list['utid']][1];
            }
        }
        return $arrList;
    }

	public static function getMsg ($uid)
	{
		return ArenaDao::getMsg($uid, self::$arrMsgField, ArenaConf::MSG_NUM);
	}

	public static function getOpponents ($arrOpptPos)
	{
		$arrUidPos = ArenaDao::getArrByPos($arrOpptPos, array('uid', 'position'));
		$arrUid = Util::arrayExtract($arrUidPos, 'uid');
		$arrUser = Util::getArrUser($arrUid, array('uname', 'level', 'utid'));

		$arrUidPos = Util::arrayIndex($arrUidPos, 'uid');
		$arrRet = array();
		foreach($arrUser as $user)
		{
			$user['master_htid'] = UserConf::$USER_INFO[$user['utid']][1];
			$user['position'] = $arrUidPos[$user['uid']]['position'];
			$arrRet[] = $user;
		}
		return $arrRet;
	}

	//竞技场总人数，每次请求查询一次即可，保存总数
	//执行脚本的时候手动设置为0,每次从数据库拉
	public static $s_count = 0;
	private static function getCount()
	{
		if (self::$s_count==0)
		{
			self::$s_count = ArenaDao::getCount();
		}
		return self::$s_count;
	}

	//得到对手的排名
	public static function getOpponentPosition ($pos)
	{
		$oppNum = ArenaConf::OPPONENT_AFTER + ArenaConf::OPPONENT_BEFOR;

		//总数
		$count = self::getCount();
		if ($count < $oppNum)
		{
			Logger::fatal('the arena has %d npc, the number of user must be greater than %d', $oppNum, $oppNum);
			throw new Exception('sys');
		}

        if ($pos>$count+1)
        {
            Logger::fatal('fail to get opponents position, the pos %d must be <= 1+ the total of arena %d', $pos, $count);
            throw new Exception('sys');
        }

		//小于100的从前后10名中取
		if ($pos <= 100)
		{
			$min = $pos - 10;
			if ($min <= 0)
			{
				$min = 1;
			}
			$max = $pos + 10;
		}
		//大于100的从前后10%里面取
		else if ($pos >= 100)
		{
			$min = intval($pos * 0.9);
			$max = intval($pos * 1.1);
		}
		//不超过总数
		if ($max > $count)
		{
			$max = $count;
		}

		// 当前位置前取3个
		$beforNum = ArenaConf::OPPONENT_BEFOR;

		// 当前位置后取2个
		$afterNum = ArenaConf::OPPONENT_AFTER;

        //前段区间小于需要的数量
		if ($pos <= ArenaConf::OPPONENT_BEFOR)
		{
			$beforNum = $pos - 1;
			$afterNum = $oppNum - $beforNum;
		}
		//后段区间小于需要的数量
		if ($count - $pos < $afterNum)
		{
            //新用户是最后一个，小于0.
			$afterNum = $count - $pos;
            if ($afterNum<0)
            {
                $afterNum = 0;
            }
			$beforNum = $oppNum - $afterNum;
		}

		$arrRet = array();
		if ($beforNum != 0)
		{
			$beforArr = self::getRandSeq($min, $pos - 1, $beforNum);
			if ($beforArr === false)
			{
				Logger::fatal('getOpponent error');
				throw new Exception('sys');
			}
			$arrRet = $beforArr;
		}
		if ($afterNum != 0)
		{
			$afterArr = self::getRandSeq($pos + 1, $max, $afterNum);
			if ($afterArr === false)
			{
				Logger::fatal('getOpponent error');
				throw new Exception('sys');
			}
			$arrRet = array_merge($arrRet, $afterArr);
		}
		sort($arrRet);
		return $arrRet;
	}

	private static function getRandSeq ($min, $max, $num)
	{
		if ($min > $max || $num <= 0 || $max - $min + 1 < $num)
		{
			return false;
		}

		$arrRet = array();
		for($i = 0; $i < $num; $i++)
		{
			$x = mt_rand($min, $max);
			while ( in_array($x, $arrRet) )
			{
				if (++$x > $max)
				{
					$x = $min;
				}
			}
			$arrRet[] = $x;
		}
		return $arrRet;
	}

	public static function getTop($offset, $limit, $arrField)
	{
		$arrPos = range($offset+1, $offset+$limit);
		return ArenaDao::getArrByPos($arrPos, $arrField);
	}
	
	public static function refreshOpponents($uid)
	{
		$info = self::get($uid, array('position', 'va_opponents'));
		if (empty($info))
		{
			Logger::warning('fail to get info for refresh opponents');
			throw new Exception('sys');
		}
		
		$newOppts = self::getOpponentPosition($info['position']);
		$arrField = array('va_opponents' => $newOppts);		
		
		$user = EnUser::getUserObj($uid);
		if (!$user->subGold(ArenaConf::REFRESH_OPPONENTS_GOLD))
		{
			Logger::warning('fail to refresh opponents in arena, gold isnot enough');
			throw new Exception('fake');
		}		
		
		ArenaDao::update($uid, $arrField);
		$user->update();
		
		Statistics::gold(StatisticsDef::ST_FUNCKEY_ARENA_REFRESH_OPPTS, ArenaConf::REFRESH_OPPONENTS_GOLD, Util::getTime());
		
		$arrRet = array('ret'=>'ok', 'opponents'=>array());
		$arrRet['opponents'] = self::getOpponents($newOppts);
		return $arrRet;
		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */