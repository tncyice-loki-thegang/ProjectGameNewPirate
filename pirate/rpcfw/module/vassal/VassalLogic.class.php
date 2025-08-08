<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: VassalLogic.class.php 38381 2013-02-18 04:06:04Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/vassal/VassalLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-18 12:06:04 +0800 (一, 2013-02-18) $
 * @version $Revision: 38381 $
 * @brief
 *
 **/










/**
 * 这个不用session，中途数据可能有修改
 * @author idyll
 *
 */
class VassalLogic
{
	public static $arrField = array('master_id', 'vassal_id', 'train_date', 'train_num', 'status');

	private static function getArrNumLvs()
	{
		return btstore_get()->CAPTAIN_ROOM['subordinate_num_lvs'];
	}

	private static function getMaxTrainNumPerVsl()
	{
		return btstore_get()->CAPTAIN_ROOM['teach_times_max'];
	}

	/**
	 * * 给框架调用
	 * 主公调教下属后，下属得到的belly
	 * @param uint $vassalId 下属uid
	 * @param uint $belly 得到的belly数量
	 */
	public static function getTrainBelly($vassalId, $belly)
	{
		$uid = RPCContext::getInstance()->getSession('global.uid');
		//设置uid，避免user修改数据再次发送数据到lcserver
		if ($uid==null)
		{
			RPCContext::getInstance()->setSession('global.uid', $vassalId);
		}

		$userObj = EnUser::getUserObj();
		$userObj->addBelly($belly);
		$userObj->update();

		//推送到前端
		if ($userObj->isOnline())
		{
            RPCContext::getInstance()->sendMsg(array($vassalId),
                                               're.user.updateUser',
                                               array('belly_num'=>$userObj->getBelly()));
		}
	}

	private static function getVslInfoByVslId($vslId)
	{
		$vslInfo = VassalDao::getVslByVslId($vslId, self::$arrField);
		$user = EnUser::getUserObj($vslId);

		$arrRet['uid'] = $vslId;
		$arrRet['uname'] = $user->getUname();
		$arrRet['vip'] = $user->getVip();
		$arrRet['guild_id'] = $user->getGuildId();
		$arrRet['utid'] = $user->getUtid();
		$arrRet['master_htid'] = $user->getMasterHeroObj()->getHtid();
		$arrRet['guild_name'] = '';

		if ($arrRet['guild_id'] != 0)
		{
			$guildInfo = GuildLogic::getRawGuildInfoById($arrRet['guild_id']);
			$arrRet['guild_name'] = $guildInfo['name'];
		}

		$portBerth = new PortBerth($vslId);
		$arrRet['port_id'] = $portBerth->getPort();

		//悬赏值 悬赏等级
		$ptLv = EnAchievements::getUserBounty($vslId);
		$arrRet['offer_reward'] = $ptLv['point'];
		$arrRet['offer_reward_level'] = $ptLv['lv'];

		$today = Util::todayDate();
		if ($today == $vslInfo['train_date'])
		{
			$arrRet['train_num'] = $vslInfo['train_num'];
		}
		else
		{
			$arrRet['train_num'] = 0;
		}
		$arrRet['train_date'] = $today;
		return $arrRet;
	}

	public static function getArrVsl($masterId)
	{
		$arrRet = VassalDao::getVslByMstId($masterId, self::$arrField);

		$arrVslInfo = array();
		foreach ($arrRet as $ret)
		{
			$vslInfo = array();
			$vslInfo['uid'] = $ret['vassal_id'];
			//今天调教了多少次
			$today = Util::todayDate();
			if ($ret['train_date']!=$today)
			{
				$vslInfo['train_num'] = 0;
			}
			else
			{
				$vslInfo['train_num'] = $ret['train_num'];
			}
			$vslInfo['train_date'] = $today;
			$arrVslInfo[] = $vslInfo;
		}

		$arrVslId = array_keys($arrRet);
        $arrVslTmp = Util::getArrUser($arrVslId, array('uid', 'utid', 'uname', 'guild_id', 'vip' ));
        
        //得到竞技场排名
        $arrVslArena = EnArena::getArrArena($arrVslId, array('position'));
        
        $arrGuildId = Util::arrayExtract($arrVslTmp, 'guild_id');
        $arrGuildIdName = GuildLogic::getMultiGuild($arrGuildId, array('name'));

		foreach ($arrVslInfo as &$vslInfo)
		{
			$vslId = $vslInfo['uid'];
			if (!isset($arrVslTmp[$vslId]))
			{
				Logger::fatal('fail to get vassal (id:%d) info ', $vslId);
				throw new Exception('sys');
			}
			$vslInfo['uname'] = $arrVslTmp[$vslId]['uname'];
			$vslInfo['vip'] = $arrVslTmp[$vslId]['vip'];
			$vslInfo['utid'] = $arrVslTmp[$vslId]['utid'];
			$vslInfo['master_htid'] = UserConf::$USER_INFO[$vslInfo['utid']][1];
			$vslInfo['guild_id'] = $arrVslTmp[$vslId]['guild_id'];			
			$vslInfo['guild_name'] = '';
			if ($vslInfo['guild_id'] != 0)
			{
				$vslInfo['guild_name'] = $arrGuildIdName[$vslInfo['guild_id']]['name'];
			}
			
			if (isset($arrVslArena[$vslId]))
			{
				$vslInfo['arena_position'] = $arrVslArena[$vslId]['position'];	
			}
			else 
			{
				$vslInfo['arena_position'] = 0;
			}			

            $portBerth = new PortBerth($vslId);
			$vslInfo['port_id'] = $portBerth->getPort();
            // 悬赏值 等级
            $ptLv = EnAchievements::getUserBounty($vslId);
			$vslInfo['offer_reward'] = $ptLv['point'];
			$vslInfo['offer_reward_level'] = $ptLv['lv'];
		}
		unset($vslInfo);

		return $arrVslInfo;
	}

	/**
	 * 得到vassal信息,
	 * 给已登录用户用
	 */
	public static function getVslAll()
	{
		$uid = RPCContext::getInstance()->getSession('global.uid');
		$arrRet = array();
		$arrRet['train_num_per_vassal'] = self::getMaxTrainNumPerVsl();
		$arrVslInfo = self::getArrVsl($uid);
		$arrRet['vassal'] = $arrVslInfo;

        //for master
        $arrRet['master'] = self::getMstInfo($uid);
		return $arrRet;
	}

    public static function getMstInfo($uid)
    {
        $retMst = array();
        $mstInfo = VassalDao::getVslByVslId($uid, self::$arrField);
        if (!empty($mstInfo))
        {
            $today = Util::todayDate();
			if ($mstInfo['train_date']!=$today)
			{
				$retMst['train_num'] = 0;
			}
			else
			{
				$retMst['train_num'] = $mstInfo['train_num'];
			}
			$retMst['train_date'] = $today;
            $retMst['uid'] = $mstInfo['master_id'];

            //get mst info
            $arrMstTmp = Util::getArrUser(array($retMst['uid']), array('uid', 'utid', 'uname', 'guild_id', 'vip' ));
            if (empty($arrMstTmp))
            {
                Logger::fatal('fail to get user info by uid %d', $retMst['uid']);
                return array();
            }
            else
            {
                $mstTmp = $arrMstTmp[$retMst['uid']];
                $retMst['uname'] = $mstTmp['uname'];
                $retMst['guild_id'] = $mstTmp['guild_id'];
                $retMst['utid'] = $mstTmp['utid'];
                $retMst['master_htid'] = UserConf::$USER_INFO[ $retMst['utid']][1];
                $retMst['vip'] = $mstTmp['vip'];

                $retMst['guild_name'] = '';
				if ($retMst['guild_id'] != 0)
				{
					$guildInfo = GuildLogic::getRawGuildInfoById($retMst['guild_id']);
					$retMst['guild_name'] = $guildInfo['name'];	
				}
				
				$ptLv = EnAchievements::getUserBounty($mstTmp['uid']);
				$retMst['offer_reward'] = $ptLv['point'];
				$retMst['offer_reward_level'] = $ptLv['lv'];
				$retMst['arena_position'] = EnArena::getPosition($mstTmp['uid']);
				
				$portBerth = new PortBerth($mstTmp['uid']);
				$retMst['port_id'] = $portBerth->getPort();
            }
        }
        return $retMst;
    }

    private static $courseMail = array(
    	1 => array('sendTrainBrushToilet', 'sendBeingTrainBrushToilet'),
    	2 => array('sendTrainPacify', 'sendBeingTrainPacify'),
    	3 => array('sendTrainItch', 'sendBeingTrainItch'),
    	4 => array('sendTrainPlayGame', 'sendBeingTrainPlayGame'),
    	5 => array('sendTrainBeat', 'sendBeingTrainBeat'), 
    	6 => array('sendTrainPraise', 'sendBeingTrainPraise'),
    	7 => array('sendTrainRide', 'sendBeingTrainRide'),
    	8 => array('sendTrainPlayBall', 'sendBeingTrainRide'), 
    	9 => array('sendTrainShowtime', 'sendBeingTrainShowtime'),
    ); 
    
	public static function train($courseId, $vslId)
	{
		$arrRet = array('ret'=>'ok', 'master_belly'=>0, 'vassal_belly'=>0);

		$uid = RPCContext::getInstance()->getSession('global.uid');
		$vslInfo = VassalDao::getVsl($uid, $vslId, self::$arrField);
		//0 判断是否为主奴关系
		if (empty($vslInfo) || $vslInfo['status']!=VassalDef::STATUS_OK)
		{
			Logger::warning('The user %d is not one of vassal %d',$vslId, $uid );
			//throw new Exception('fake');
			$arrRet['ret'] = 'invalid';
			return $arrRet;
		}

		//1 判断是否到最大值了
		//今天调教了多少次
		$today = Util::todayDate();
		if ($vslInfo['train_date'] != $today)
		{
			$vslInfo['train_num'] = 0;
		}
		$vslInfo['train_date'] = $today;
		if (self::getMaxTrainNumPerVsl() <= $vslInfo['train_num'])
		{
			Logger::warning('train num is max for vassal %d.', $vslId);
			throw new Exception('fake');
		}

		//2 check level for course
		$trainCourse = btstore_get()->TRAIN_VASSAL[$courseId];
		$needLevel = $trainCourse['need_level'];
		$user = EnUser::getUser();
		if ($user['level']<$needLevel)
		{
			Logger::warning('level of user isnot enough for train courseid %d ', $courseId);
			throw new Exception('fake');
		}

		//更新数据
		$vslInfo['train_num'] += 1;
		//这里update的时候可能已经不是主公了
		$arrUpdate = array('train_date'=>$vslInfo['train_date'], 'train_num'=>$vslInfo['train_num']);
		if (!VassalDao::update( $vslInfo['master_id'], $vslInfo['vassal_id'], $arrUpdate))
		{
			Logger::warning('fail to update train_date and train_num for train, the master is %d, vassal is %d', $vslInfo['master_id'], $vslInfo['vassal_id']);
			$arrRet['ret'] = 'fail';
			return $arrRet;
		}

		$mstUser = EnUser::getUserObj();
		$vslUser = EnUser::getUserObj($vslId);
		$baseBelly = $trainCourse['reward_belly'];
		// 主公的belly = 基础值 × 下属等级
		$mstBelly = $baseBelly * $vslUser->getLevel();
		$arrRet['master_belly'] = $mstBelly;
		$mstUser->addBelly($mstBelly);
		$mstUser->update();

		//发钱给下属 基础值 × 主公等级
		$vslBelly = $mstUser->getLevel() * $baseBelly;
		$arrRet['vassal_belly'] = $vslBelly;

		RPCContext::getInstance()->executeTask(
            intval($vslId),
            'vassal.getTrainBelly',
			array('vassalId'=>$vslId, 'belly'=>$vslBelly),
            false);
                   
		//发邮件给主人
       call_user_func_array(array('MailTemplate', self::$courseMail[$courseId][0]), 
       		array($uid, $vslUser->getTemplateUserInfo(), $mstBelly));
       //发邮件给下属
       call_user_func_array(array('MailTemplate', self::$courseMail[$courseId][1]), 
       		array($vslId, $mstUser->getTemplateUserInfo(), $vslBelly));

		return $arrRet;
	}

	/**
	 * 主公的等级计算可以拥有的下属数量
	 * @param unknown_type $level 主公等级
	 */
	private static function getMaxVslNum($level)
	{
		$num = 0;
		foreach (self::getArrNumLvs() as $numLvs)
		{
			$num = $numLvs['max'];
			if ($level <= $numLvs['lv'])
			{
				break;				
			}
		}
		return $num;
	}

	/**
	 * 警告：此函数的出口记得释放lock
	 * 主公踢掉vassal
	 * @param unit $masterId
	 * @param unit $vassalId
	 */
	public static function relieve($mstId, $vslId)
	{
		$vlock = new VassalLock();
		if (false==$vlock->lock($mstId, $vslId))
		{
			return 'lock';
		}

		$info = VassalDao::getVsl($mstId, $vslId, array('status'));
		if (empty($info))
		{
			//这里可能是其他用户修改了数据，不做任何处理
			$vlock->unlock();
			return 'ok';
		}
		VassalDao::update($mstId, $vslId, array('status' => VassalDef::STATUS_RELIEVE));
		$mstUser = EnUser::getUserObj($mstId);
		MailTemplate::sendupSubordinateGivenup($vslId, $mstUser->getTemplateUserInfo());
		VassalMsg::relieveMsgToVsl($mstId, $vslId);
        $vlock->unlock();
        return 'ok';
	}

	/**
	 * 这个函数搬迁港口的时候用，跟relieve差不多，
	 * 出错处理不同，邮件不同，能有名字，就几行代码，copy一份吧。
	 * Enter description here ...
	 * @param unknown_type $mstId
	 * @param unknown_type $vslId
	 */
    public static function relievedByMstMovePort($mstId, $vslId)
    {
		$vlock = new VassalLock();
		if (false==$vlock->lock($mstId, $vslId))
		{
			Logger::fatal('in vassal modual, fail to relieve user %d by user %d because of lock, the user %d may be moving into another port.', $vslId, $mstId );
			return;
		}

		//判断是否为主公
		$info = VassalDao::getVsl($mstId, $vslId, array('status'));
		if (empty($info))
		{
			//这里可能是其他用户修改了数据，不做任何处理
			$vlock->unlock();
			return;
		}

		VassalDao::update($mstId, $vslId, array('status' => VassalDef::STATUS_RELIEVE));
		$mstUser = EnUser::getUserObj($mstId);
		MailTemplate::sendMasterMovePort($vslId, $mstUser->getTemplateUserInfo());
		VassalMsg::relieveMsgToVsl($mstId, $vslId);
		$vlock->unlock();
    }


	/**
	 * 跟但前用户是否为同一个港口
	 * Enter description here ...
	 * @param unknown_type $otherUid
	 */
	public static function isSamePort($uid, $otherUid)
	{
		$portBerth = new PortBerth($uid);
		$pid1 = $portBerth->getPort();
		
		$portBerth = new PortBerth($otherUid);
		$pid2 = $portBerth->getPort();
		
		return $pid1==$pid2;
	}
	
	/**
	 * 返回给战斗模块使用用户信息
	 * Enter description here ...
	 * @param UserObj $user
	 * @param unknown_type $isCheckFormation 不检查阵型上的英雄， 被攻击用户不检查
	 */
	private static function getUserForBattle($user, $isCheckFormation = false)
	{
		$arrRet = array('ok', false);
		$userFormation = EnFormation::getFormationInfo($user->getUid());
		//缓存item
		$user->prepareItem4CurFormation();
		
		//都把血设置为最大
		$ret = EnFormation::checkUserFormation($user->getUid(), $userFormation);
		// 检查英雄血量是否足够
		if ($ret!='ok' && $isCheckFormation)
		{
			Logger::warning('fail to conquer, checkUserFormation return %s, is not ok.', $ret);
			//throw new Exception('fake');
			$arrRet[0] = $ret;	
			if ($arrRet[0]=='not_enough_hp')
			{
				$arrRet[0] = 'hp_err';
			}
			return $arrRet;
		}

		// 将阵型ID设置为用户当前默认阵型		
		$formationID = $user->getCurFormation();
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation);
		Logger::debug('The hero list is %s', $userFormationArr);
		$arrRet[1] = array('name' => $user->getUname(),
                           'level' => $user->getLevel(),
							'isPlayer' => true,
                           'flag' => 0,
                           'formation' => $formationID,
                           'uid' => $user->getUid(),
                           'arrHero' => $userFormationArr);
		return $arrRet;
	}
	
	/**
	 * 战斗类型， 征服还是反抗
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param unknown_type $otherUid
	 * @return array(Type, $atkedUid)
	 * type: BattleType::REVOLT  BattleType::VASSAL err
	 */
	private static function battleType($uid, $otherUid)
	{
		// 0=>战斗类型， 1=> 被攻击的uid
		$arrRet = array(BattleType::VASSAL, $otherUid, 'ok');
		
		$arrMst = VassalDao::getVslData($otherUid, $uid, self::$arrField);
		$battleType = BattleType::VASSAL;
		//征服
		if (empty($arrMst) || $arrMst['status']!=VassalDef::STATUS_OK)
		{
			//判断vsl数量是否已经到了最大值
			$arrVsl = VassalDao::getVslByMstId($uid, self::$arrField);	
			$curNum = count($arrVsl);
			$userInfo = EnUser::getUser();
			$maxNum = self::getMaxVslNum($userInfo['level']);
			if ($curNum >= $maxNum)
			{
				//Logger::warning('vassal is to max num:%d', $maxNum);
				//throw new Exception('fake');
				$arrRet[2] = 'max_vassal';
				return $arrRet; 
			}

			//判断用户是否已经是下属了
			if (isset($arrVsl[$otherUid]))
			{
				//Logger::warning('the uid %d is one of vassals', $otherUid);
				//throw new Exception('fake');
				$arrRet[2] = 'is_vassal';
				return $arrRet;
			}

			//otherUid 是否已经有主公
			$otherUidMstInfo = VassalDao::getVslByVslId($otherUid, self::$arrField);
			//有主公，跟主公打架
			if (!empty($otherUidMstInfo))
			{
				$arrRet[1] = $otherUidMstInfo['master_id'];
			}
			else
			{
				$arrRet[1] = $otherUid;
			}
		}
		//反抗
		else
		{
			$arrRet[0] = BattleType::REVOLT;
			$arrRet[1] = $otherUid;
		}
		return $arrRet;
	}

	//警告：此函数的出口记得释放lock
	//这里只需要锁住当前用户和被征服或者反抗的人即可.
	//锁当前用户用处只有防止两人互相征服。
	//锁定otherUid 防止多人抢一个的情况出现。
	public static function conquer($otherUid)
	{
		$arrRet = array('ret'=>'ok', 'atk'=> array(), 'del_mst'=>0, 
			'add_vassal'=>array(), 'costExecution'=>0);

		//得到用户所有的vsl信息
		$uid = RPCContext::getInstance()->getSession('global.uid');
		
		//相同港口才能征服
		if (!self::isSamePort($uid, $otherUid))
		{
			Logger::warning('fail to conquer, uid %d and %d is not in the same port', $uid, $otherUid);
			//throw new Exception('fake');
			$arrRet['ret'] = 'port_err';
			return $arrRet;
		}
		
		//拉数据前就必须加锁
		$vlock = new VassalLock();
		if (false==$vlock->lock($otherUid, $uid))
		{
			$arrRet['ret'] = 'lock';
			return $arrRet;
		}
		
		try
		{
			list($battleType, $atkedUid, $arrRet['ret']) = self::battleType($uid, $otherUid);
			if ($arrRet['ret'] != 'ok')
			{
				return $arrRet;
			}

			//跟attackedUid打一架
			//1 检查行动力 . 
			//反抗失败不减行动力，这里改为检查
			$user = EnUser::getUserObj($uid);
			if (($user->getVassalExecution() + $user->getCurExecution()) 
					< VassalConf::CONQUER_CONSUME_EXECUTION)
			{
				Logger::warning('execution is not enough for conquer %d', VassalConf::CONQUER_CONSUME_EXECUTION);
				throw new Exception('fake');
			}

			//2 检查cd时间
			if ($user->getFightCDTime() > Util::getTime())
			{
				Logger::warning('fight cdtime is not arrival');
				throw new Exception('fake');
			}

			//当前用户信息
			list($arrRet['ret'], $battle_user) = self::getUserForBattle($user, true);
			if ($arrRet['ret']!='ok')
			{
				$vlock->unlock();
				Logger::warning('fail to conquer, getUserForBattle return %s, is not ok.', $arrRet['ret']);
				//throw new Exception('fake');
				return $arrRet;
			}
		
		
			$atkedUser = EnUser::getUserObj($atkedUid);
			//被攻击不检查血量
			list($arrRet['ret'], $battle_atked_user) = self::getUserForBattle($atkedUser);
			if ($arrRet['ret']!='ok')
			{
				$vlock->unlock();
				Logger::warning('fail to conquer, getUserForBattle return %s, is not ok.', $arrRet['ret']);
				//throw new Exception('fake');
				return $arrRet;
			}

			$bt = new Battle();
			$atkRet = $bt->doHero($battle_user, $battle_atked_user, 0, null,
								  null, array('bgid'=>VassalConf::BATTLE_BJID, 'musicId'=>VassalConf::BATTLE_MUSIC_ID, 'type'=>$battleType));

			// 战斗系统返回值
			Logger::debug('Ret from battle is %s.', $atkRet);

			$otherUser = EnUser::getUserObj($otherUid);
			list($heroArr, $arrRet['costExecution']) = self::conquerRes($user, $otherUser, $atkedUser, $battleType, $atkRet, $arrRet);
			$vlock->unlock();
		}
		catch (Exception $e)
		{
			$vlock->unlock();
			Logger::warning('fail to conquer, the exception msg:%s', $e->getMessage());
			throw $e;
		}
		
		if ($arrRet['costExecution'] != 0)
		{
			$extracExecution = $user->getVassalExecution();
			$diff = $arrRet['costExecution'] - $extracExecution;
			//免费行动力不够
			if ($diff >= 0)
			{
				$user->subVassalExecution($extracExecution);
				$user->subExecution($diff);
			}
			else
			{
				$user->subVassalExecution($arrRet['costExecution']);
			}
		}
		
		//user 更新， $atkedUser会减血（不同阵营）， otherUser没有改变
		$user->update();
		$atkedUser->update();

		// 前端不需要道具信息，unset掉
		//unset($atkRet['server']['reward']['equip']['item']);
		// 将战斗结果返回给前端
        $arrRet['atk'] = array('fightRet' => $atkRet['client'], 'bloodPackage' => $user->getBloodPackage(),
        						'curHp'=> $heroArr,
                               'appraisal' => $atkRet['server']['appraisal']);
        return $arrRet;
	}
	
	/**
	 * 征服战斗后处理,
	 * Enter description here ...
	 * @param UserObj $user
	 * @param OtherUserObj $otherUser
	 * @param OtherUserObj $atkedUser
	 * @param unknown_type $atkRet
	 * @return 战斗后英雄血量 消耗行动力
	 */
	private static function conquerRes($user, $otherUser, $atkedUser, $battleType, $atkRet, &$arrRet)
	{
		$uid = $user->getUid();
		$otherUid = $otherUser->getUid();
		$atkedUid = $atkedUser->getUid();
		
		//消耗行动力
		$costExecution = 0;
		
		// 增加杀敌CD时间
		$user->addFightCDTime(VassalConf::CONQUER_CDTIME);

		// 战斗后英雄的血量
		$heroArr = array();
		//不同阵营玩家才减血
		if ($user->getGroupId() != $atkedUser->getGroupId())
		{
			Logger::debug('group is not equal, sub hp');
			// 减自己的血
			$heroArr = EnFormation::subUserHeroHp($atkRet['server']['team1']);
			//被攻击方血消耗量
			EnFormation::subUserHeroHp($atkRet['server']['team2'], $atkedUid);
		}

		$replayId = $atkRet['server']['brid'];
		$isSuccess = BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'];
		//反抗
		if ($battleType==BattleType::REVOLT)
		{
			// 发邮件给两个
			MailTemplate::sendRevoltMaster($uid,
										   $atkedUser->getTemplateUserInfo(),
										   $replayId,
										   $isSuccess);
			MailTemplate::sendSubordinateRevolt($atkedUid,
												$user->getTemplateUserInfo(),
												$replayId,
												!$isSuccess);

			// 打败对方
			if ($isSuccess)
			{
				//反抗成功才减行动力
				$costExecution = 1;
				$arrRet['del_mst'] = $otherUid;
				VassalDao::update($otherUid, $uid,  array('status'=>VassalDef::STATUS_RELIEVE));
				//更新到前端
				VassalMsg::revoltMsgToMst($otherUid, $uid);
			}
		}
		else  //征服
		{
			//被征服者邮件
			MailTemplate::sendBeingConquer($otherUid,
										   $user->getTemplateUserInfo(),
										   $replayId,
										   !$isSuccess);

			//征服不管成功失败，都减行动力
			$costExecution = 1;
			
			//抢别人的奴隶
			if ($atkedUid!=$otherUid)
			{
				//被抢下属的邮件
				MailTemplate::sendBeingPillage($atkedUid, $user->getTemplateUserInfo(),
					$otherUser->getTemplateUserInfo(), $replayId, !$isSuccess);
				//抢下属的邮件
				MailTemplate::sendPillage($uid, $atkedUser->getTemplateUserInfo(),
					$otherUser->getTemplateUserInfo(), $replayId, $isSuccess);

				if ($isSuccess)
				{
					$arrRet['add_vassal'] = self::getVslInfoByVslId($otherUid);
					VassalDao::update($atkedUid, $otherUid, array('status'=>VassalDef::STATUS_RELIEVE));
					VassalDao::updateOrInsert($uid, $otherUid, array('status'=>VassalDef::STATUS_OK));
					//给前主公
					VassalMsg::plunderMsgToMst($atkedUid, $otherUid);
					//给被征服的人
					VassalMsg::conquerMsgToVsl($uid, $otherUid);
				}

			}
			else
			{
				//征服者的邮件
				MailTemplate::sendConquer($uid, $atkedUser->getTemplateUserInfo(), 
					$replayId, $isSuccess);

				if ($isSuccess)
				{					
					VassalDao::updateOrInsert($uid, $otherUid, array('status'=>VassalDef::STATUS_OK));
					$arrRet['add_vassal'] = self::getVslInfoByVslId($otherUid);
					//给被征服的人
					VassalMsg::conquerMsgToVsl($uid, $otherUid);
				}
			}
		}
		return array($heroArr, $costExecution);
	}

    public static function getInfoByUid($uid)
    {
        $arrRet = array('user'=>array(), 'master'=>array(), 'vassal'=>array());

        $userInfo = EnUser::getUser($uid);
        $arrRet['user']['uid'] = $uid;
        $arrRet['user']['uname'] = $userInfo['uname'];
        $arrRet['user']['level'] = $userInfo['level'];
        $arrRet['user']['akt_value'] = $userInfo['atk_value'];
        $arrRet['user']['protect_cdtime'] = $userInfo['protect_cdtime'];
        $arrRet['user']['msg'] = $userInfo['msg'];
        $arrRet['user']['group_id'] = $userInfo['group_id'];
        $arrRet['user']['guild_id'] = $userInfo['guild_id'];
        $arrRet['user']['guild_name'] = '';
        
        $arrRet['user']['order_list'] = 0;
        if (EnSwitch::isOpen(SwitchDef::ORDER_LIST, $uid))
        {
        	$arrRet['user']['order_list'] = 1;
        }
        
        if ($userInfo['guild_id']!=0)
        {
        	$guildInfo = GuildLogic::getRawGuildInfoById($userInfo['guild_id']);
       		$arrRet['user']['guild_name'] = $guildInfo['name'];
        }

        //for master
        $mstInfo = VassalDao::getVslByVslId($uid, array('master_id'));
        if (!empty($mstInfo))
        {
            $arrIdName = Util::getArrUser(array($mstInfo['master_id']), array('uname'));
            if (!empty($arrIdName))
            {
                $idName = current($arrIdName);
                $arrRet['master']['uid'] = $idName['uid'];
                $arrRet['master']['uname'] = $idName['uname'];
            }
            else
            {
                Logger::fatal("fail to get uname by uid %d for vassal.", $mstInfo['master_id']);
            }
        }

        //for vassal
        $arrVsl = VassalDao::getVslByMstId($uid, array('vassal_id'));
        if (!empty($arrVsl))
        {
            $arrVslId = array_keys($arrVsl);
            $arrVslTmp = Util::getArrUser($arrVslId, array('uid', 'uname'));
            foreach ($arrVslTmp as $vsl)
            {
                $arrRet['vassal'][] = $vsl;
            }
        }
        return $arrRet;
    }

    //用户摆脱奴隶关系
    public static function breakoutMovePort($uid)
    {
    	$vlock = new VassalLock();
    	if (false==$vlock->lock($uid))
    	{
    		Logger::fatal('in vassal modual, user %d fail to breakout vassal becase of lock. the user may be moving into another port.', $uid);
    		return;
    	}

		$arrRet = VassalDao::getVslByVslId($uid, array('master_id', 'vassal_id'));
		if (!empty($arrRet))
		{
			$vslUser = EnUser::getUserObj($arrRet['vassal_id']); 
			MailTemplate::sendSubordinateMovePort($arrRet['master_id'], array('uid'=>$arrRet['vassal_id'], 'uname'=>$vslUser->getUname(), 'utid'=>$vslUser->getUtid()));
			VassalDao::update($arrRet['master_id'], $arrRet['vassal_id'], array('status'=>VassalDef::STATUS_RELIEVE));
			VassalMsg::breakoutToMst($arrRet['master_id'], $arrRet['vassal_id']);
			return $arrRet['master_id'];
		}
		$vlock->unlock();
		return 0;
    }
    
    public static function getVslUserInfo($uid)
    {
    	$arrVslId = VassalDao::getVslByMstId($uid, array('vassal_id'));
    	if (empty($arrVslId))
    	{
    		return array(); 
    	}
    	
    	$arrVslId = Util::arrayExtract($arrVslId, 'vassal_id');
    	$arrRet = Util::getArrUser($arrVslId, array('uid','uname', 'level', 'utid'));
    	return $arrRet;    	
    }
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */