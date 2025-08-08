<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: OlympicLogic.class.php 33628 2012-12-24 03:12:08Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/olympic/OlympicLogic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-24 11:12:08 +0800 (一, 2012-12-24) $
 * @version $Revision: 33628 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : OlympicLogic
 * Description : 擂台赛实际逻辑实现类， 对内接口实现类
 * Inherit     :
 **********************************************************************************************************************/
class OlympicLogic
{

	/**
	 * 进入擂台赛
	 */
	public static function enterArena() 
	{
		// 获取用户阵营ID
		$groupID = EnUser::getUserObj()->getGroupId();
		// 检查是否可以进入
		if (!OlympicUtil::canEnter() || $groupID == 0)
		{
			return 'err';
		}
		// 设置阵营ID, 需要加上偏移量， 便于在 sendFilterMessage 区分
		RPCContext::getInstance()->setSession('global.arenaId', $groupID + OlympicDef::OLYMPIC_OFF_SET);
		// 返回
		return 'ok';
	}

	/**
	 * 离开擂台赛
	 */
	public static function levelArena()
	{
		// 离开赛场，不再发送消息
		RPCContext::getInstance()->unsetSession('global.arenaId');
	}

	/**
	 * 获取擂台信息
	 */
	public static function getFightInfo() 
	{
		// 获取CD时间
		$cd = MyOlympic::getInstance()->getCdEndTime();
		// 判断当前时刻
		$now = OlympicUtil::getNow();
		// 如果是报名的时候拉取，就简单了。
		if ($now == OlympicDef::SIGN_UP)
		{
			// 获取报名信息
			$info = OlympicDao::getOlympicInfo();
			// 返回
			return array('now' => $now, 'info' => $info, 'cd' => $cd, 'replay' => array());
		}
		else if ($now != OlympicDef::OUT_RANGE)
		{
			// 其他时刻，需要加上uname和htid, 还有order信息返回前端
			$ret = array();
			// 返回前端战报信息
			$replay = array();
			// 获取报名信息
			$info = OlympicDao::getOlympicInfo();
			// 获取当前最新信息
			$dayInfo = OlympicDao::getMaxOlympicLog();
			// 如果有值，则进行判断
			if ($dayInfo !== false)
			{
				// 获取比赛顺序
				$order = $dayInfo[OlympicDef::SIGN_UP]['va_olympic'];
				// 循环查看
				foreach ($order as $index => $uid)
				{
					// 如果这个位置上有人的话
					if ($uid != 0)
					{
						// 获取用户信息
						$user = EnUser::getUserObj($uid);
						// 设置用户信息
						$ret[$uid]['uid'] = $uid;
						$ret[$uid]['order'] = $index;
						$ret[$uid]['htid'] = $user->getMasterHeroObj()->getHtid();
						$ret[$uid]['uname'] = $user->getUname();
	
						// 获取用户其他信息
						$curUserInfo = OlympicUtil::getInfoByUid($info, $uid);
						$ret[$uid]['final_rank'] = $curUserInfo['final_rank'];
						$ret[$uid]['sign_up_index'] = $curUserInfo['sign_up_index'];
						$ret[$uid]['group_id'] = $curUserInfo['group_id'];
					}
				}
				// 返回战报信息
				$replay = empty($dayInfo[OlympicDef::REPLAY]['va_olympic']) ? 
								array() : $dayInfo[OlympicDef::REPLAY]['va_olympic'];
			}
			// 返回前端
			return array('now' => $now, 'info' => $ret, 'cd' => $cd, 'replay' => $replay);
		}
		else if ($now == OlympicDef::OUT_RANGE)
		{
			// 其他时刻，需要加上uname和htid, 还有order信息返回前端
			$ret = array();
			// 返回前端战报信息
			$replay = array();
			// 获取比赛结果
			$dayInfo = OlympicDao::getMaxOlympicLog();
			Logger::debug("getMaxOlympicLog ret is %s.", $dayInfo);
			// 如果有值，则进行判断
			if ($dayInfo !== false)
			{
				// 获取对战顺序
				$order = $dayInfo[OlympicDef::SIGN_UP]['va_olympic'];
				// 获取对战结果
				$award = $dayInfo[OlympicDef::AWARDS]['va_olympic'];
				// 循环查看
				foreach ($order as $index => $uid)
				{
					// 如果这个位置上有人的话
					if ($uid != 0)
					{
						// 获取用户信息
						$user = EnUser::getUserObj($uid);
						// 设置用户信息
						$ret[$uid]['uid'] = $uid;
						$ret[$uid]['order'] = $index;
						$ret[$uid]['htid'] = $user->getMasterHeroObj()->getHtid();
						$ret[$uid]['uname'] = $user->getUname();
						$ret[$uid]['final_rank'] = $award[$uid];
					}
				}
				// 返回战报信息
				$replay = empty($dayInfo[OlympicDef::REPLAY]['va_olympic']) ? 
								array() : $dayInfo[OlympicDef::REPLAY]['va_olympic'];
			}
			// 返回前端
			return array('now' => $now, 'info' => $ret, 'cd' => $cd, 'replay' => $replay);
		}
	}

	/**
	 * 使用金币清除CD时间
	 */
	public static function clearCdByGold() 
	{
		// 获取CD时刻, 看看一共需要多少个金币
		$num = ceil((MyOlympic::getInstance()->getCdTime() / OlympicDef::OLYMPIC_10_SEC) / 
		             btstore_get()->OLYMPIC['cd_gold']);
		// 如果不需要清除CD时刻，那么就直接返回
		if ($num <= 0)
		{
			return 0;
		}

		// 获取用户信息
		$userInfo = EnUser::getUser();
		// 因为要扣钱，所以记录下日志，省的你不认账
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $num);
		if ($num > $userInfo['gold_num'])
		{
			// 钱不够，直接返回
			return 'err';
		}
		// 清空CD时刻
		MyOlympic::getInstance()->resetCdTime();

		// 扣钱
		$user = EnUser::getInstance();
		$user->subGold($num);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $num);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_OLYMPIC_CD, $num, Util::getTime());

		// 保存至数据库
		MyOlympic::getInstance()->save();
		// 返回给前端，用来校准数据
		return $num;
	}

	/**
	 * 挑战
	 * 
	 * @param int $groupID						阵营ID
	 * @param int $index						报名位置
	 * @throws Exception
	 */
	public static function challenge($groupID, $index)
	{
		// 判断是否可以报名，如果报名时刻已过，或者不能进入，那么直接返回
		if (OlympicUtil::getNow() != OlympicDef::SIGN_UP || 
		   !OlympicUtil::canEnter())
	    {
	    	return 'err';
	    }
	    // 检查CD时刻
	    if (MyOlympic::getInstance()->getCdTime() > 0)
	    {
	    	return 'cd';
	    }

	    // 获取用户信息
	    $user = EnUser::getUserObj();
	    // 检查游戏币数量
	    if ($user->getBelly() < $user->getLevel() * btstore_get()->OLYMPIC['join_belly'])
	    {
	    	Logger::warning("Belly not enough. need is %d, now have %d.", 
	    	                $user->getLevel() * btstore_get()->OLYMPIC['join_belly'], $user->getBelly());
	    	return 'err';
	    }

		// 检查是否可以报名
		if ($user->getGroupId() != $groupID && $groupID != OlympicDef::NEUTRAL)
		{
			Logger::warning('What group you choose? Para : %d, user is %d.', $groupID, $user->getGroupId());
        	throw new Exception('fake');
		}
		// 获取当时的最新信息
		$info = OlympicDao::getOlympicInfo();
		// 获取当前用户信息
		$infoCur = OlympicUtil::getInfoByUid($info, $user->getUid());
		if ($infoCur !== false)
		{
			Logger::warning('Can not sign up ag and ag!');
			return 'err';
		}

		// 执行一场战斗操作
		$ret = self::doFight($user->getUid(), $groupID, $index);
		// 检查是否获胜了
		if (is_array($ret) && $ret['winer']['uid'] == $user->getUid())
		{
			// 中立阵营报名的时候，发广播给所有人，否则只发消息给本阵营的
			foreach (OlympicDef::$sea as $gID)
			{
				// 如果报名阵营是中立阵营，都发消息； 如果不是中立阵营，只发本阵营消息
				if (($groupID != OlympicDef::NEUTRAL && $groupID == $gID) || 
				     $groupID == OlympicDef::NEUTRAL)
			    {
					// 广播给前端并返回
					RPCContext::getInstance()->sendFilterMessage('arena', 
					                                             $gID + OlympicDef::OLYMPIC_OFF_SET, 
					                                             're.olympic.fightResultInfo', 
					                                             $ret);
			    }
			}
			// 获取对方的uid
			$objUid = $ret['loser']['uid'];
			// 报名成功，通知成就系统
			EnAchievements::notify($user->getUid(), AchievementsDef::OLYMPIC_SIGN_TIMES, 1);
		}
		// 如果输了的时候，需要告诉挑战者，你狠雄伟高大，又弄死了个人！
		else if (is_array($ret) && $ret['winer']['uid'] != $user->getUid())
		{
			// 虚荣心害死人啊！年轻人！
			RPCContext::getInstance()->sendMsg(array($ret['winer']['uid']), 
											   're.olympic.fightResultInfo', $ret);
			// 获取对方的uid
			$objUid = $ret['winer']['uid'];
		}
		// 正常攻打，都需要计入CD时间
		if ($ret !== 'lock')
		{
			// 扣除游戏币
			$user->subBelly($user->getLevel() * btstore_get()->OLYMPIC['join_belly']);
			// 返回CD时间
			$ret['cd'] = MyOlympic::getInstance()->setCdTime();
			// 保存到数据库
			$user->update();
			// 需要保存的战报信息
			$replay = array('replay' => $ret['replay'], 'winer' => $ret['winer'], 
			                'loser' => $ret['loser'], 'offensive' => $ret['offensive']);

			// 保存自己的战报信息
			MyOlympic::getInstance()->saveReplay($replay);
			MyOlympic::getInstance()->save();
			// 让对方也保存一份战报信息
			RPCContext::getInstance()->executeTask($objUid, 
												   'olympic.__saveSignUpReplay', array($replay, $objUid));			
		}
		// 没有获胜就不需要广播数据了，直接返回就可以了
		return $ret;
	}

	/**
	 * 报名
	 * 
	 * @param int $groupID						阵营ID
	 * @param int $index						报名位置
	 * @throws Exception
	 */
	public static function signUp($groupID, $index)
	{
		// 判断是否可以报名，如果报名时刻已过，或者不能进入，那么直接返回
		if (OlympicUtil::getNow() != OlympicDef::SIGN_UP || 
		   !OlympicUtil::canEnter())
	    {
	    	return 'err';
	    }

	    // 获取用户信息
	    $user = EnUser::getUserObj();
	    // 检查游戏币数量
	    if ($user->getBelly() < $user->getLevel() * btstore_get()->OLYMPIC['join_belly'])
	    {
	    	Logger::warning("Belly not enough. need is %d, now have %d.", 
	    	                $user->getLevel() * btstore_get()->OLYMPIC['join_belly'], $user->getBelly());
	    	return 'err';
	    }

		// 检查是否可以报名
		if ($user->getGroupId() != $groupID && $groupID != OlympicDef::NEUTRAL)
		{
			Logger::warning('What group you choose? Para : %d, user is %d.', $groupID, $user->getGroupId());
        	throw new Exception('fake');
		}

		// 对这个位置加锁
	    $tLocker = new Locker();
	    // 加锁，必须在获取数据之前进行加锁
	    if (!$tLocker->lock(OlympicDef::LOCKER. $groupID. $index))
	    {
	    	// 正忙，请稍后……
	    	Logger::debug('Lock fail, try ag later.');
	    	return 'lock';
	    }
	    Logger::debug("Start lock for sigh up.");

	    // 防止有些人无聊吧唧的随便抛异常，我就没办法解锁了。
	    try 
	    {
			// 获取当时的最新信息
			$info = OlympicDao::getOlympicInfo();
			// 获取当前用户信息
			$infoCur = OlympicUtil::getInfoByUid($info, $user->getUid());
			// 如果已经报名参过赛了
			if ($infoCur !== false)
			{
				// 解锁 返回
				Logger::warning('Can not sign up ag and ag!');
				$tLocker->unlock(OlympicDef::LOCKER. $groupID. $index);
				return 'err';
			}
			// 获取需要的位置信息
			$infoObj = OlympicUtil::getInfoByIndex($info, $groupID, $index);
			Logger::debug("GetInfoByIndex returns %s.", $infoObj);
	    	// 判断下到底是不是没有人，有没有人我说了算啊！
			if (empty($infoObj['uid']))
			{
				// 真没有人啊！赶紧占位
				$infoCur = array();
				$infoCur['uid'] = $user->getUid();
				$infoCur['final_rank'] = OlympicUtil::getNextLevel();
				// 更新数据库
				OlympicDao::updOlympicInfo($infoCur, $index, $groupID);
				// 解锁
				$tLocker->unlock(OlympicDef::LOCKER. $groupID. $index);
				// 扣除游戏币
				$user->subBelly($user->getLevel() * btstore_get()->OLYMPIC['join_belly']);
				$user->update();

				// 中立阵营报名的时候，发广播给所有人，否则只发消息给本阵营的
				foreach (OlympicDef::$sea as $gID)
				{
					// 如果报名阵营是中立阵营，都发消息； 如果不是中立阵营，只发本阵营消息
					if (($groupID != OlympicDef::NEUTRAL && $groupID == $gID) || 
					     $groupID == OlympicDef::NEUTRAL)
				    {
						// 广播给前端并返回
						RPCContext::getInstance()->sendFilterMessage('arena', 
						                                             $gID + OlympicDef::OLYMPIC_OFF_SET, 
						                                             're.olympic.fightResultInfo', 
						                                             array('winer' => OlympicUtil::getUserInfo($user),
						             									   'index' => array('index' => $index, 
						             									                    'groupID' => $groupID)));
				    }
				}
				// 报名成功，通知成就系统
				EnAchievements::notify($user->getUid(), AchievementsDef::OLYMPIC_SIGN_TIMES, 1);
				// 返回
				return 'ok';
			}
			// 都已经被抢了，还报什么名，挑战吧！
			return 'full';
	    }
	    // 出错的时候执行解锁操作
		catch (Exception $e)
		{
			// 解锁，然后该干啥依旧干啥
			Logger::warning('Fight exeception:%s', $e->getMessage());
			$tLocker->unlock(OlympicDef::LOCKER. $groupID. $index);
			throw $e;
		}
	}

	/**
	 * 分组
	 */
	public static function drawBlock()
	{
		// 获取32强名单
		$info = OlympicDao::getOlympicInfo();
		// 获取所有的uid
		$arrUids = Util::arrayExtract($info, 'uid');
		// 获取用户名称
		$mapUid2Uname = Util::getUnameByUid($arrUids);
		// 乱序
		shuffle($arrUids);
		Logger::debug("Shuffled uids is %s.", $arrUids);
		// 记录空白人的个数
		$count = 0;
		// 获取详细信息
		foreach ($arrUids as $index => $uid)
		{
			// 不为空的时候才加上名字啥的
			if (empty($uid))
			{
				continue;
			}
			// 有活人，进行计数
			++$count;
			// 给用户附上名字和次序
	    	foreach ($info as $key => $v)
	    	{
	    		if ($info[$key]['uid'] == $uid)
	    		{
					// 获取姓名
					$info[$key]['uname'] = $mapUid2Uname[$uid];
					$info[$key]['order'] = $index;
	    		}
	    	}
    		// 给一个32强的成就
			EnAchievements::notify($uid, AchievementsDef::OLYMPIC_NO_TIMES, 32, 1);
		}

		// 记录抽签结果
		OlympicDao::insertOlympicLog(array('date_ymd' => OlympicUtil::getCurYmd(), 
		                                   'status' => OlympicDef::SIGN_UP, 
		                                   'va_olympic' => $arrUids));
		// 如果一个人都没有，就不再发送消息了
		if ($count > 0)
		{
			// 发广播给所有人
			foreach (OlympicDef::$sea as $groupID)
			{
				// 按照阵营来，一个一个依次发送给所有人
				RPCContext::getInstance()->sendFilterMessage('arena', 
				                                             $groupID + OlympicDef::OLYMPIC_OFF_SET, 
				                                             're.olympic.getFightInfo', 
				                                             $info);
			}
		}
	}

	/**
	 * 执行一场 PvP
	 * 
	 * @param int $curUserID					当前人的用户ID
	 * @param int $groupID						阵营ID
	 * @param int $index						报名位置
	 * @param bool $isFinal						是否是决赛 (失败的时候，对方是否需要更新数据库)
	 * @throws Exception
	 */
	public static function doFight($curUserID, $groupID, $index, $isFinal = false)
	{
		// 对这个位置加锁
	    $tLocker = new Locker();
	    // 加锁，必须在获取数据之前进行加锁
	    if (!$isFinal && !$tLocker->lock(OlympicDef::LOCKER. $groupID. $index))
	    {
	    	// 正忙，请稍后……
	    	Logger::debug('Lock fail, try ag later.');
	    	return 'lock';
	    }
	    Logger::debug("Start lock.");

	    // 防止有些人无聊吧唧的随便抛异常，我就没办法解锁了。
	    try 
	    {
			// 获取当时的最新信息
			$info = OlympicDao::getOlympicInfo();
			// 获取需要的位置信息
			$infoObj = OlympicUtil::getInfoByIndex($info, $groupID, $index);
			Logger::debug("GetInfoByIndex returns %s.", $infoObj);
			// 获取当前用户信息
			$infoCur = OlympicUtil::getInfoByUid($info, $curUserID);
			// 如果这个人都没参过赛呢, (报名时候)
			if ($infoCur === false)
			{
				// 那么就朝着目标努力去吧
				$infoCur['uid'] = $curUserID;
				$infoCur['sign_up_index'] = $index;
				$infoCur['group_id'] = $groupID;
			}
			Logger::debug("GetInfoByUid returns %s.", $infoCur);
			// 获取想要争取的名次是啥
			$nextRank = OlympicUtil::getNextLevel();
			// 获取当前用户
			$curUser = EnUser::getUserObj($curUserID);

			// 古人云：不战而屈人之兵，上之上者也
			if (empty($infoObj['uid']))
			{
				// 判断是否是决赛, 决赛更新自己，报名抢占别人
				$tmp = $isFinal ? $infoCur : $infoObj;
				// 赶紧占位
				$tmp['uid'] = $curUserID;
				$tmp['final_rank'] = $nextRank;
				// 获取战斗力  —— 轮空时候显示这个有鸡毛用，不当家不知柴米贵
				$fightForce = $curUser->getFightForce();
				// 更新数据库
				OlympicDao::updOlympicInfo($tmp, $tmp['sign_up_index'], $tmp['group_id']);
				// 解锁 —— 只有报名的时候需要加锁
				if (!$isFinal)
				{
					$tLocker->unlock(OlympicDef::LOCKER. $groupID. $index);
				}
				// 决赛的时候需要记录战报 —— 蛋疼，轮空的也得记录
				else 
				{
					// 决赛的时候，使用一个特殊的uid来串行的执行记录战报的行径, 这样即便是异步执行的战斗，也不怕会记录错
					RPCContext::getInstance()->executeTask(4, 'olympic.__executeSaveReplay', 
				                                       	   array(array('winer' => OlympicUtil::getUserInfo($curUser, $fightForce), 
				                                       	   			   'final_rank' => $nextRank)),
				                                       	   true);
					// 决赛需要进行成就记录操作
					EnAchievements::notify($tmp['uid'], AchievementsDef::OLYMPIC_NO_TIMES, $nextRank, 1);
				}
				Logger::debug("After updOlympicInfo.");
				// 播放消息 —— 如果需要的话
				OlympicUtil::sendChatMsg($curUser->getTemplateUserInfo(), $nextRank);
				// 准备好推送的值
				return array('winer' => OlympicUtil::getUserInfo($curUser, $fightForce),
				             'index' => array('index' => $index, 'groupID' => $groupID),
							 'final_rank' => $nextRank);
			}

			// 不是每次都有那么好的美事儿啊，这时候需要和人干一架了
			$objUser = EnUser::getUserObj($infoObj['uid']);
			// 获取两个人的战斗信息
			$arrCurUser = OlympicUtil::getUserForBattle($curUser);
			$arrObjUser = OlympicUtil::getUserForBattle($objUser);
			// 如果是决赛阶段，需要判断谁先先手
			if ($isFinal)
			{
				// 谁战斗力靠前谁先手
				if ($arrCurUser['fightForce'] >= $arrObjUser['fightForce'])
				{
					$offensiveUser = $arrCurUser;
					$defensiveUser = $arrObjUser;
				}
				else 
				{
					$offensiveUser = $arrObjUser;
					$defensiveUser = $arrCurUser;
				}
			}
			// 否则，就攻击的人先手
			else 
			{
				$offensiveUser = $arrCurUser;
				$defensiveUser = $arrObjUser;
			}

			// 开打
			$bt = new Battle();
			$atkRet = $bt->doHero($offensiveUser, 
			                      $defensiveUser, 
			                      0, 
			                      null,
								  null, 
								  array('bgid' => ArenaConf::BATTLE_BJID,
								        'musicId' => ArenaConf::BATTLE_MUSIC_ID, 
								        'type' => BattleType::OLYMPIC));
			// 战斗系统返回值
			Logger::debug('Ret from battle is %s.', $atkRet);
			// 胜负判定, 如果赢了的话
			// 现在有两种情况算是获胜 1. 本人先手，且获胜; 2. 对方先手， 失败了
			if (((BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D']) && 
				  $offensiveUser['uid'] == $arrCurUser['uid']) || 
				((BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] >= BattleDef::$APPRAISAL['E']) && 
				  $offensiveUser['uid'] == $arrObjUser['uid']))
			{
				// 判断是否是决赛, 决赛更新自己，报名抢占别人
				$tmp = $isFinal ? $infoCur : $infoObj;
				// 赶紧占位
				$tmp['uid'] = $curUser->getUid();
				$tmp['final_rank'] = $nextRank;
				// 更新数据库
				OlympicDao::updOlympicInfo($tmp, $tmp['sign_up_index'], $tmp['group_id']);
				// 准备好推送的值
				$ret = array('winer' => OlympicUtil::getUserInfo($curUser, $arrCurUser['fightForce']),
	                  		 'loser' => OlympicUtil::getUserInfo($objUser, $arrObjUser['fightForce']),
				             'replay' => $atkRet['server']['brid'],
				             'index' => array('index' => $index, 'groupID' => $groupID),
							 'final_rank' => $nextRank,
							 'offensive' => $offensiveUser['uid']);
				// 播放消息
				OlympicUtil::sendChatMsg($curUser->getTemplateUserInfo(), $nextRank, 
				                         false, $objUser->getTemplateUserInfo(), $atkRet['server']['brid']);
			}
			// 本来输了啥都不想给你的，但是还是得给个证据
			else 
			{
				// 如果是淘汰赛的时候，输了就意味着对方赢了
				if ($isFinal)
				{
					// 进入下一轮
					$infoObj['uid'] = $objUser->getUid();
					$infoObj['final_rank'] = $nextRank;
					// 占位
					OlympicDao::updOlympicInfo($infoObj,
					                           $infoObj['sign_up_index'], $infoObj['group_id']);
					// 播放消息 —— 给人家……
					OlympicUtil::sendChatMsg($objUser->getTemplateUserInfo(), $nextRank, 
					                         false, $curUser->getTemplateUserInfo(), $atkRet['server']['brid']);
				}
				// 失败的时候，就不广播了，也没什么用
				$ret = array('winer' => OlympicUtil::getUserInfo($objUser, $arrObjUser['fightForce']),
	                  		 'loser' => OlympicUtil::getUserInfo($curUser, $arrCurUser['fightForce']),
				             'replay' => $atkRet['server']['brid'],
				             'index' => array('index' => $index, 'groupID' => $groupID),
							 'final_rank' => $nextRank,
							 'offensive' => $offensiveUser['uid']);
			}
			// 解锁 —— 只有报名的时候需要加锁
			if (!$isFinal)
			{
				$tLocker->unlock(OlympicDef::LOCKER. $groupID. $index);
			}
			// 记录战报信息
			else 
			{
				// 决赛的时候，使用一个特殊的uid来串行的执行记录战报的行径, 这样即便是异步执行的战斗，也不怕会记录错
				RPCContext::getInstance()->executeTask(4, 'olympic.__executeSaveReplay', 
			                                       	   array(array('replay' => $ret['replay'], 
			                                       	   			   'winer' => $ret['winer'], 
			                                       	   		 	   'loser' => $ret['loser'],
			                                       	   			   'final_rank' => $nextRank,
			                                       	   		 	   'offensive' => $ret['offensive'])),
			                                       	   true);
				// 决赛需要进行成就记录操作
				EnAchievements::notify($ret['winer']['uid'], AchievementsDef::OLYMPIC_NO_TIMES, $nextRank, 1);
			}
			// 返回，估计只有失败的时候会用这个值
			Logger::debug("Dofight ret is %s.", $ret);
			return $ret;
	    }
	    // 出错的时候执行解锁操作
		catch (Exception $e)
		{
			// 解锁 —— 只有报名的时候需要加锁
			if (!$isFinal)
			{
				$tLocker->unlock(OlympicDef::LOCKER. $groupID. $index);
			}
			Logger::warning('Fight exeception:%s', $e->getMessage());
			throw $e;
		}
	}

	/**
	 * 助威
	 * 
	 * @param int $objUid						助威对象ID
	 */
	public static function cheer($objUid)
	{
		/**************************************************************************************************************
 		 * 助威前资格检查
 		 **************************************************************************************************************/
		// 获取当前时刻
		$now = OlympicUtil::getNow();
		// 当前必须在八强时间段内
		if ($now < OlympicDef::FINAL_16_PER || $now > OlympicDef::FINAL_SEMI)
		{
			Logger::warning('Time is wrong!');
			return 'err';
		}

		// 获取32强所有人
		$info = OlympicDao::getOlympicInfo();
		// 获取所有的uid
		$arrUids = Util::arrayExtract($info, 'uid');
		// 所有成功进入决赛的玩家不可点击助威
		if (!in_array($objUid, $arrUids))
		{
			Logger::warning('The user passed %d is not in 32 list.', $objUid);
        	throw new Exception('fake');
		}

		// 对方需要进入八强
		foreach ($info as $user)
		{
			// 不到八强或者已经夺冠时候不允许助威
			if (($user['uid'] == $objUid && $user['final_rank'] < 4) ||
				($user['uid'] == $objUid && $user['final_rank'] == 1))
			{
				Logger::warning('The user final rank is %d.', $objUid);
	        	throw new Exception('fake');
			}
		}

		// 已经助威过的话，不可以再助威
		if (MyOlympic::getInstance()->getTodayCheerTimes() != 0)
		{
			Logger::warning('Already cherr yet.');
			return 'err';
		}

		// 获取人物信息
		$user = EnUser::getUserObj();
		// 获取助威所需游戏币
		$needBelly = $user->getLevel() * btstore_get()->OLYMPIC['cheer_belly'];
		// 游戏币不足的话，不可以助威
		if ($user->getBelly() < $needBelly)
		{
			Logger::warning('Not enough belly, user is %d.', $user->getBelly());
			return 'err';
		}

		/**************************************************************************************************************
 		 * 助威
 		 **************************************************************************************************************/
		// 给某位英雄助威
		MyOlympic::getInstance()->cheer($objUid);
		// 增加奖池内容
		OlympicDao::updJackPot($needBelly * OlympicConf::CHEER_NEED_BELLY_RATE);

		// 扣除游戏币
		$user->subBelly($needBelly);
		// 更新数据库
		$user->update();
		MyOlympic::getInstance()->save();

		// 给前端推送数据 —— 隔几个人推送一次
		if ($user->getUid() % OlympicConf::PERCENT_NUM == 0)
		{
			// 发广播给所有人
			foreach (OlympicDef::$sea as $groupID)
			{
				// 按照阵营来，一个一个依次发送给所有人
				RPCContext::getInstance()->sendFilterMessage('arena', 
				                                             $groupID + OlympicDef::OLYMPIC_OFF_SET, 
				                                             're.olympic.cheerInfo', 
				                                             self::getAllCheerObj());
			}
		}
		// 发送奖池数据
		if ($user->getUid() % OlympicConf::PERCENT_NUM * 3 == 0)
		{
			// 发广播给所有人
			foreach (OlympicDef::$sea as $groupID)
			{
				// 按照阵营来，一个一个依次发送给所有人
				RPCContext::getInstance()->sendFilterMessage('arena', 
				                                             $groupID + OlympicDef::OLYMPIC_OFF_SET, 
				                                             're.challenge.freshPrizePool', 
				                                             array(self::getJackPot()));
			}
		}
		// 助威成功，通知成就系统
		EnAchievements::notify($user->getUid(), AchievementsDef::OLYMPIC_CHEER_TIMES, 1);
		// 返回
		return 'ok';
	}

	/**
	 * 获取八强的所有助威人数
	 */
	public static function getAllCheerObj()
	{
		// 声明返回值
		$ret = array();
		// 获取决赛开始时间
		$startTime = strtotime(OlympicUtil::getCurYmd(). OlympicConf::START_TIME) + GameConf::BOSS_OFFSET;
		$finalTime = OlympicUtil::getEndTime($startTime, 0);
		Logger::debug("Final time is %d.", $finalTime);
		// 获取所有助威对象
		$objUsers = OlympicDao::getAllCheerObj($finalTime);
		Logger::debug("Dao getAllCheerObj ret is %s.", $objUsers);
		// 循环查看个数
		foreach ($objUsers as $user)
		{
			// 如果还没记录过这个对象，那么就计入一个次数
			if (empty($ret[$user['cheer_uid']]))
			{
				$ret[$user['cheer_uid']] = 1;
				continue;
			}
			// 否则加算次数
			++$ret[$user['cheer_uid']];
		}
		// 返回所有助威人数
		return $ret;
	}

	/**
	 * 获取助威冠军的所有人
	 * 
	 * @param int $uid							冠军的uid
	 */
	public static function sendCheerAward($uid)
	{
		// 获取决赛开始时间
		$startTime = strtotime(OlympicUtil::getCurYmd(). OlympicConf::START_TIME) + GameConf::BOSS_OFFSET;
		$finalTime = OlympicUtil::getEndTime($startTime, 0);
		// 获取所有冠军助威的对象
		$uids = OlympicDao::getChampionCheerObj($finalTime, $uid);
		// 异步执行发奖请求
		foreach ($uids as $objUid)
		{
			RPCContext::getInstance()->executeTask($objUid['uid'], 
												   'olympic.__executeAward', array(0, $objUid['uid'], OlympicDef::TYPE_CHEER));
		}	
	}

	/**
	 * 发放奖池中的大奖
	 */
	public static function distribute500wBelly()
	{
//		// 获取决赛开始时间
//		$startTime = strtotime(OlympicUtil::getCurYmd(). OlympicConf::START_TIME) + GameConf::BOSS_OFFSET;
//		$finalTime = OlympicUtil::getEndTime($startTime, 0);
		// 获取所有有积分的用户
//		$uids = OlympicDao::getAllLotteryUids($finalTime);
// 20120922 被刘洋删掉了，因为有排行榜上有遗漏的用户
		$uids = OlympicDao::getAllLotteryUids(0);
		// 获取奖池奖金数量
		$jackPot = OlympicDao::getJackPot();
		// 计算封顶值
		$tmpBelly = intval($jackPot[OlympicDef::MAX_LEVEL]) * intval(btstore_get()->OLYMPIC['Jackpot_max']);
		// 获取奖池封顶奖金数 —— 如果等级算出的比较大，那么就用奖池的奖金，否则就使用封顶奖金
		if (intval($jackPot[OlympicDef::JACKPOT_AMOUNT]) > $tmpBelly)
		{
			$maxBelly = $tmpBelly;
		}
		else 
		{
			$maxBelly = intval($jackPot[OlympicDef::JACKPOT_AMOUNT]);
		}
		Logger::debug("Max jack pot is %d.", $maxBelly);
		// 计算下每个积分对应的奖金
		$integralBelly = intval($maxBelly * 
								btstore_get()->OLYMPIC['prize_percent'] / OlympicDef::LITTLE_WHITE_PERCENT);
		// 循环发奖
		foreach ($uids as $user)
		{
			// 发送奖金
			$userObj = EnUser::getUserObj($user['uid']);
			$userObj->addBelly($user['integral'] * $integralBelly);
			$userObj->update();

			// 防止邮件发送失败导致整个过程失败
			try {
				// 发送邮件通知
				MailTemplate::sendChanlledgePrizePool($user['uid'], 
													  $user['integral'], $user['integral'] * $integralBelly);
			}
			catch (Exception $e)
			{
				Logger::warning("Send mail exception, uid is %d.", $user['uid']);
			}

			// 清空用户积分
			RPCContext::getInstance()->executeTask($user['uid'], 'olympic.__updOtherUserIntegral', 
			                                       array($user['uid'], 0, true));
		}

		// 发奖完毕，清空奖池
		OlympicDao::updJackPot(0, true);
	}

	/**
	 * 返回奖池内容
	 */
	public static function getJackPot()
	{
		// 获取奖池奖金数量
		$jackPot = OlympicDao::getJackPot();
		// 计算封顶值
		$tmpBelly = intval($jackPot[OlympicDef::MAX_LEVEL]) * intval(btstore_get()->OLYMPIC['Jackpot_max']);
		// 返回数量 —— 如果奖池数量大于封顶值，就返回封顶值，否则返回奖池实际数量
		if (intval($jackPot[OlympicDef::JACKPOT_AMOUNT]) > $tmpBelly)
		{
			$maxBelly = $tmpBelly;
		}
		else 
		{
			$maxBelly = intval($jackPot[OlympicDef::JACKPOT_AMOUNT]);
		}
		Logger::debug("Max jack pot is %d.", $maxBelly);
		return $maxBelly;
	}

	/**
	 * 返回用户的擂台赛信息
	 */
	public static function getUserOlympicInfo()
	{
		return MyOlympic::getInstance()->getUserOlympicInfo();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
