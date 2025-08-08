<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: CopyLogic.class.php 39510 2013-02-27 08:29:44Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/CopyLogic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-27 16:29:44 +0800 (三, 2013-02-27) $
 * @version $Revision: 39510 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : CopyLogic
 * Description : 副本实际逻辑实现类， 对内接口实现类
 * Inherit     :
 **********************************************************************************************************************/
class CopyLogic
{
	// 回调中使用的参数
	private static $copyID;
	private static $defeatRet;
	private static $isFirstTime;
	private static $heroHPs;

	/**
	 * 进入副本
	 * @param int $copyID
	 * @throws Exception
	 */
	public static function enterCopy($copyID)
	{
		// 获取副本信息
		$copyInst = new MyCopy();
		$copyInfo = $copyInst->getCopyInfo($copyID);
		// 如果没获取到副本信息
		if ($copyInfo === false)
		{
			Logger::fatal('Can not enter copy! The copy id is %d.', $copyID);
			throw new Exception('fake');
		}
		// 可以进入副本
		RPCContext::getInstance()->setSession('global.copyId', $copyID);
	}

	/**
	 * 获取用户打到的，最大的副本ID
	 */
	public static function getUserLatestCopyInfo()
	{
		// 获取用户的所有副本
		$copyInst = new MyCopy();
		$copies = $copyInst->getUserCopies();
		// 判断空
		if (empty($copies))
		{
			return array('copyInfo' => array(), 'rpEnemies' => array());
		}
		// 设置默认值
		$copyID = 0;
		// 循环查看所有副本信息
		foreach ($copies as $copy)
		{
			// 如果这个副本ID比较大，而且还不是隐藏副本
			if ($copy['copy_id'] > $copyID && 
			    btstore_get()->COPY[$copy['copy_id']]['copy_type'] == CopyDef::NORMAL_COPY)
			{
				$copyID = $copy['copy_id'];
			}
		}

		// 将获取到的最大副本ID对应的副本信息和最新的刷新点部队信息
		return array('copyInfo' => $copies[$copyID], 
		             'rpEnemies' => CopyManager::getLatestEnemies($copyID));
	}

	/**
	 * 根据副本选择表ID返回玩家的副本信息
	 *
	 * @param int $ccID							副本选择表ID
	 */
	public static function getCopiesInfoByCopyChooseID($ccID)
	{
		// 返回值
		$isAlready = false;
		$ret = array();
		// 获取用户的所有副本
		$copyInst = new MyCopy();
		$userCopies = $copyInst->getUserCopies();
		// 查看副本选择表对应的副本
		if (!isset(btstore_get()->COPY_CHOOSE[$ccID]))
		{
			Logger::fatal('Can not find this copy choose id, %d.', $ccID);
			throw new Exception('fake');
		}
		$copies = btstore_get()->COPY_CHOOSE[$ccID]['copy_ids'];
		// 循环查看所有副本
		foreach ($copies as $copyID)
		{
			// 如果已经拥有了这个副本
			if (isset($userCopies[$copyID]))
			{
				// 设置副本信息，返回给前端
				$ret[$copyID] = $userCopies[$copyID];
				$isAlready = true;
			}
		}
		// 如果有数据，那么就返回数组，否则返回false
		return $isAlready ? $ret : $isAlready;
	}

	/**
	 * 获取用户的所有副本
	 */
	public static function getUserCopies()
	{
		$copyInst = new MyCopy();
		return $copyInst->getUserCopies();
	}

	/**
	 * 获取所有的副本ID
	 */
	public static function getAllCopiesID()
	{
		// 返回值
		$ret = array();
		// 获取用户的所有副本
		$copyInst = new MyCopy();
		$userCopies = $copyInst->getUserCopies();
		// 循环查看所有副本
		foreach ($userCopies as $copy)
		{
			$ret[] = $copy['copy_id'];
		}
		// 返回
		return $ret;
	}

	/**
	 * 通过副本ID获取副本信息
	 * @param int $copyID						副本ID
	 * @param bool $flg							是否需要推送
	 */
	public static function getCopyInfo($copyID, $flg = true)
	{
		$copyInst = new MyCopy();
		// 返回副本信息和最新的刷新点部队信息
		$ret = array('copyInfo' => $copyInst->getCopyInfo($copyID), 
		             'rpEnemies' => CopyManager::getLatestEnemies($copyID));
		// 前端直接调用，返回就行了
		if ($flg)
		{
			return $ret;
		}
		// timer 调用
		else 
		{
			$timer = new Timer();
			// 使用副本ID进行过滤, 获取此副本举行的所有活动ID
			$actList = btstore_get()->COPY_ACT['copy_act'][$copyID];
			// 循环处理所有活动
			foreach ($actList as $actID)
			{
				// 获取活动最新消息
				$actInfo = AllActivities::getInstance()->getActivityInfo($actID);
				// 获取下次活动时刻
				$nexTime = $actInfo['next_refresh_time'];
				// 添下次任务
				$tid = $timer->addTask(0, $nexTime, 'CopyLogic.getCopyInfo', array($copyID, false));
			}
			// 留个证据，我真发给何老师了
			Logger::trace('addTimer after %d excute. tid is %d, copyID is %d', $nexTime, $tid, $copyID);
			// 私房话，需要偷偷的传 TODO
//			RPCContext::getInstance()->sendMsg($arrTargetUid, $callback, $arrArg) => $ret;
		}
	}

	/**
	 * 返回该副本最新的刷新点信息
	 * @param int $copyID						副本ID
	 */
	public static function getLatestRefreshEnemies($copyID)
	{
		return CopyManager::getLatestEnemies($copyID);
	}

	/**
	 * 检查行动力
	 * @param int $enemyID						敌人ID
	 * @param int $uid							用户ID
	 * @param int $isGroup						是否是军团战
	 */
	protected static function checkExecution($enemyID, $uid, $isGroup)
	{
		// 获取用户信息
		$user = EnUser::getUserObj($uid);
		// 查看是否是军团怪
		if ($isGroup)
		{
			// 军团怪的值不一样
			$needExecution = intval(btstore_get()->GROUP_ARMY[$enemyID]['need_execution']);
			// 将获取的用户行动力和所需行动力进行比较
			if ($user->getCurExecution() < $needExecution)
			{
				// 记录日志
				Logger::warning('Execution not enough, enemy is %d, the %d user have %d, need %d.',
				                $enemyID, $uid, $user->getCurExecution(), $needExecution);
				// 锻炼锻炼肌肉再来吧，都没有行动力的人……
				return false;
			}
		}
		else 
		{
			// 获取实际需求的行动力
			$needExecution = intval(btstore_get()->ARMY[$enemyID]['need_execution']);
			// 普通怪的时候，需要先查看是否有免费令
			if ($user->getCopyExecution() + $user->getCurExecution() >= $needExecution)
			{
				return true;
			}
			else 
			{
				// 记录日志
				Logger::warning('Execution not enough, enemy is %d, the %d user have %d, now free execution is %d, need %d.',
				                $enemyID, $uid, $user->getCurExecution(), $user->getCopyExecution(), $needExecution);
				// 行动力和免费令都没了，你想干啥？
				return false;
			}
		}
		// 检查通过，一切OK
		return true;
	}

	/**
	 * 使用RMB清空打架CD时间
	 */
	public static function clearFightCdByGold()
	{
		// 获取用户实例
		$user = EnUser::getInstance();
		// 获取用户信息
		$userInfo = EnUser::getUser();
		// 获取CD时刻
		$cdTime = $userInfo['fight_cdtime'] - Util::getTime();
		// 如果不需要清除CD时刻，那么就直接返回
		if ($cdTime <= 0)
		{
			return 0;
		}
		// 看看一共需要多少个金币
		$num = ceil($cdTime / CopyConf::COIN_TIME);

		// 因为要扣钱，所以记录下日志，省的你不认账
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $num);
		if ($num > $userInfo['gold_num'])
		{
			// 钱不够，直接返回
			return 'err';
		}
		// 清空CD时刻
		$user->resetFightCDTime();

		// 扣钱
		$user->subGold($num);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_COPY_CLEARCDTIME, $num, Util::getTime());
		// 返回实际花费的金币值
		return $num;
	}

	/**
	 * 检查战斗冷却时间
	 * @param int $uid							用户ID
	 */
	protected static function checkCdTime($uid)
	{
		// 获取用户信息
		$userInfo = EnUser::getUser($uid);
		// 将CD时间和当前时间比较
		if (Util::getTime() < $userInfo['fight_cdtime'])
		{
			// 记录日志 (温柔对待, 不再fatal)
			Logger::debug('Not cool down yet, the %d user CD time is %d.', $uid, $userInfo['fight_cdtime']);
			// 别猴急啊，时间还没到呢
			return false;
		}
		// 检查通过，一切OK
		return true;
	}

	/**
	 * 检查敌人的攻击条件是否达成
	 * @param int $enemyID						敌人ID
	 * @param int $isGroup						是否是军团战
	 * @param int $defeat						副本中击败过的所有队伍
	 */
	protected static function alreadyCanAtt($enemyID, $isGroup, $defeat)
	{
		// 查看是否是军团怪
		if ($isGroup)
		{
			// 军团怪的值不一样
			$conEnemies = btstore_get()->GROUP_ARMY[$enemyID]['con_enemies'];
		}
		else 
		{
			// 需要击败以下部队们才能攻击  —— con 是 conditions 的意思哈。
			$conEnemies = btstore_get()->ARMY[$enemyID]['con_enemies'];
		}
		// 我就随便看看…… 看看该干的事儿你都忙完没……
		foreach ($conEnemies as $armyID)
		{
			// 如果有部队还没打到
			if ($armyID != 0 && !isset($defeat[$armyID]))
			{
				// 不行啊，总得把条件部队都干死吧？
				return false;
			}
		}
		// 检查通过，一切OK
		return true;
	}

	/**
	 * 检查判断是否可以攻击
	 * @param int $enemyID						攻击对象ID
	 * @param int $uid							用户ID
	 * @param int $copyID						副本ID
	 */
	private static function canAttack($enemyID, $uid, $copyID)
	{
		/**************************************************************************************************************
 		 * 进行行动力和CD时间判断
 		 **************************************************************************************************************/
		if (!self::checkExecution($enemyID, $uid, false))
		{
			return 'execution';
		}
		if (!self::checkCdTime($uid))
		{
			return 'cd';
		}

		/**************************************************************************************************************
 		 * 获取副本用户进度信息
 		 **************************************************************************************************************/
		// 获取副本信息
		$copyInst = new MyCopy();
		$copyInfo = $copyInst->getCopyInfo($copyID);
		// 查看这个贪得无厌的人玩儿了的部队信息
		$defeat = $copyInfo['va_copy_info']['defeat_id_times'];

		/**************************************************************************************************************
 		 * 查看是否是无敌怪
 		 **************************************************************************************************************/
		if (isset($defeat[$enemyID]) && 
		    isset(btstore_get()->ARMY[$enemyID]['can_not_refight']) &&
		    btstore_get()->ARMY[$enemyID]['can_not_refight'] == CopyConf::CAN_NOT_REFIGHT)
	    {
			// 记录日志
			Logger::fatal('Can not fight with unbeatable enemy.');
			// 如果这个怪已经打过，而且他是无敌怪，就不能再打了
			throw new Exception('fake');
	    }

		/**************************************************************************************************************
 		 * 活动的怪，需要检查现在是否在显示状态
 		 **************************************************************************************************************/
		// 判断此部队是普通部队还是刷新部队
		$refreshArmy = btstore_get()->ARMY[$enemyID]['refresh_army'];
		// 如果是刷新怪，那么得先看眼他刷出来没
		if ($refreshArmy == CopyConf::REFRESH_ENEMY)
		{
			// 查看最新刷新点信息
			$enemies = CopyManager::getLatestEnemies($copyID);
			// 肿么了？刷新点没刷出来这个部队啊？
			if (!in_array($enemyID, $enemies))
			{
				// 记录日志
				Logger::fatal('No this enemy in this copy now.');
				// 那拜拜吧，地图上都没这个部队么！下次再来哈……
				throw new Exception('fake');
			}
		}

		/**************************************************************************************************************
 		 * 任务的怪，需要判定任务是否接收
 		 **************************************************************************************************************/
		// 获取使得怪物出现的任务ID
		$taskID = btstore_get()->ARMY[$enemyID]['task_id'];
		// 如果不为空或者这个任务没有被接受 (前提是一次都没有打过)
		if (!isset($defeat[$enemyID]) && !empty($taskID) && !EnTask::isAccept($taskID))
		{
			// 记录日志, 不能攻击
			Logger::fatal('Can not attack this enemy, task %d is not accept.', $taskID);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 判断距离，是否可以进行攻击
 		 **************************************************************************************************************/
		if (!self::alreadyCanAtt($enemyID, false, $defeat))
		{
			// 记录日志
			Logger::fatal('Still have enemy not defeat yet. enemyID is %d.', $enemyID);
			// 没通过检查，则直接返回错误
			return 'enemy';
		}

		return 'ok';
	}

	/**
	 * 战斗前的工作
	 * @param int $enemyID						攻击对象ID
	 * @param int $uid							用户ID
	 * @return array							次数和检查结果
	 */
	private static function checkServerDefeat($enemyID, $uid)
	{
		/**************************************************************************************************************
 		 * 获取用户阵营
 		 **************************************************************************************************************/
		// 将阵营设置为所有
		$groupID = 0;
		// 获取部队攻击次数所属
		$belong = btstore_get()->ARMY[$enemyID]['belong'];
		// 如果属于阵营级
		if ($belong == CopyConf::BELONG_GROUP)
		{
			// 获取用户阵营
			$userInfo = EnUser::getUser();
			$groupID = $userInfo['group_id'];
		}

		/**************************************************************************************************************
 		 * 判断攻击次数是否已经满了
 		 **************************************************************************************************************/
		// 获取该部队免费攻击的次数
		$freeTime = intval(btstore_get()->ARMY[$enemyID]['free_time']);
		// 获取当日服务器最大攻击次数
		$maxDefeat = intval(btstore_get()->ARMY[$enemyID]['max_defeat']);
		// 刷新点ID，普通怪刷新点ID为0
		$rpID = self::getEnemyRefreshPointID($enemyID);
		// 获取用户实际攻击次数
		$userDefeat = CopyDao::getUserDefeatNum($uid, $enemyID, $rpID);

		// 判断日期，存入时需要存入一天的末尾时刻
		// 如果当前日期的开始时刻都大于数据库里面存入的日期，那么表示肯定已经换了一天了，就有免费次数; 否则如果小于数据库的时间，表示还在一天里面呢
		// 同时，如果当日的攻击次数大于等于免费攻击次数，表明需要和服务器的所有人抢了！都别拦着我！！！
		if (!Util::isSameDay($userDefeat['current_day'], CopyConf::REFRESH_TIME) && $userDefeat['annihilate'] >= $freeTime)
		{
			// 自己免费的消耗了，再判断服务器次数
			// 获取服务器实际攻击次数
			$serverDefeat = CopyDao::getServerDefeatNum($enemyID, $groupID, $rpID);
			// 要是不幸服务器的攻击次数已经大于当前的最大值了 (有可能没有配置最大值，那就是随便打的意思)
			if ($maxDefeat != 0 && $serverDefeat >= $maxDefeat)
			{
				// 记录日志
				Logger::debug('Defeat count is full, now defeat time is %d.', $serverDefeat);
				// 没办法了，别玩儿了，真要有诚心，拿银子吧！哇哈哈哈哈哈哈
				// 如果是强制攻击，则需要判定金币是否足够
				$userInfo = EnUser::getUser();
				// 先查看看多打了几次
				$times = $userDefeat['annihilate'] - $freeTime;
				// 如果想玩儿钱还不够……
				if ($userInfo['gold_num'] < CopyConf::FORCE_FIGHT_COINS + $times * intval(btstore_get()->ARMY[$enemyID]['need_gold']))
				{
					// 啊，亲，好像钱不太够啊，赶紧充值去吧
					return array('ret' => 'no');
				}
				// 强制攻击，还有钱
				return array('annihilate' => $userDefeat['annihilate'], 'ret' => 'force_ok');
			}
			// 此人的免费次数已经耗尽，不过服务器次数还有，还有不要钱的机会，需要给上层一个值，不能增加个人的战斗次数
			else 
			{
				return array('annihilate' => $serverDefeat, 'ret' => 'server_ok');
			}
		}
		// 还有免费机会，需要给上层一个值，战斗后不能增加服务器战斗次数
		else 
		{
			return array('annihilate' => $userDefeat['annihilate'], 'ret' => 'user_ok');
		}
	}

	/**
	 * 计算战斗结果 
	 * 有些工作需要在这里做 —— 譬如掉落道具，增加经验之类，因为需要通过战斗录像记录
	 * 
	 * @param array $atkRet						战斗结果
	 */
	public static function calculateFightRet($atkRet)
	{
		/**************************************************************************************************************
 		 * 获取敌人小队信息，掉落物品和英雄
 		 **************************************************************************************************************/
		// 没有获得敌人小队ID
		if (!isset($atkRet['uid2']))
		{
			Logger::fatal('Can not get monster team id.');
			throw new Exception('fake');
		}
		// 敌人小队ID
		$enemyID = $atkRet['uid2'];
		// 判断任务完成了才减血
		if (EnSwitch::isOpen(SwitchDef::FUNCTION_CHANGE))
		{
			// 战斗后英雄的血量
			self::$heroHPs = EnFormation::subUserHeroHp($atkRet['team1']);
		}

		/**************************************************************************************************************
 		 * 战斗结束后，根据胜负不同，进行不一样的操作
 		 **************************************************************************************************************/
		// 详细的奖励值
		$itemIDs = array();
		$prizeIDs = array();
		$isPassed = false;
		$belly = 0;
		$exp = 0;
		$experience = 0;
		$prestige = 0;
		$gold = 0;
		// 获取用户类实例
		$user = EnUser::getUserObj();
		// 增加杀敌CD时间
		$user->addFightCDTime(btstore_get()->ARMY[$enemyID]['cd_time']);
		// 壮烈殉国的场合， 稍微给点经验意思一下就得了
		if (BattleDef::$APPRAISAL[$atkRet['appraisal']] > BattleDef::$APPRAISAL['D'])
		{
			$exp = intval(btstore_get()->ARMY[$enemyID]['lose_exp']);
		}
		// 哟~不容易啊，还赢了
		else
		{
			// 查看需要减几个
			$needExecution = intval(btstore_get()->ARMY[$enemyID]['need_execution']); 
			// 获取此人现在拥有的免费令个数
			$copyExecution = $user->getCopyExecution();
			// 查看是否有免费令
			if ($needExecution - $copyExecution > 0)
			{
				// 扣除免费令
				$user->subCopyExecution($copyExecution);
				// 再扣除行动力
				$user->subExecution($needExecution - $copyExecution);
			}
			// 没有免费令，才扣除行动力
			else 
			{
				// 扣除行动力 —— 只有在胜利的时候才扣除行动力
				$user->subCopyExecution($needExecution);
			}
			// 获胜奖励游戏币
			$belly = btstore_get()->ARMY[$enemyID]['init_belly'];
			// 获胜奖励威望
			$prestige = btstore_get()->ARMY[$enemyID]['init_prestige'];

			// 获取公会科技
			$guild = GuildLogic::getBuffer($user->getUid());
			// 获胜奖励阅历
			$experience = floor(btstore_get()->ARMY[$enemyID]['init_experience'] * 
			                    (1 + $guild['battleExperienceAddition'] / CopyConf::LITTLE_WHITE_PERCENT) *
			                    EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_COPY));
			// 获胜奖励经验
			$exp = floor(btstore_get()->ARMY[$enemyID]['init_exp'] * 
			                    (1 + $guild['battleExpAddition'] / CopyConf::LITTLE_WHITE_PERCENT) *
			                    EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_BATTLE));

			/**********************************************************************************************************
 		 	 * 告诉任务系统
 		     **********************************************************************************************************/
			// 怪物小队和战斗结果
			TaskNotify::beatArmy($enemyID, $atkRet['appraisal']);
			// 每日任务
			EnDaytask::beatSuccess();
			// 节日系统
			EnFestival::addEliteCopyAtkPoint();

			/**********************************************************************************************************
 		 	 * 查看成就系统
 		     **********************************************************************************************************/
			// 评价成就
			if (BattleDef::$APPRAISAL[$atkRet['appraisal']] === BattleDef::$APPRAISAL['SSS'])
			{
				EnAchievements::notify($user->getUid(), AchievementsDef::DEFEATE_ENEMY_SSS, $enemyID);
			}
			// 评价次数成就
			if (BattleDef::$APPRAISAL[$atkRet['appraisal']] <= BattleDef::$APPRAISAL['S'])
			{
				EnAchievements::notify($user->getUid(), AchievementsDef::DEFEATE_ENEMY_S_TIMES, $enemyID, 1);
			}

			/**********************************************************************************************************
 		 	 * 记录攻击次数
 		     **********************************************************************************************************/
			// 获取用户阵营
			$groupID = $user->getGroupId();
			// 记录攻击次数, 如果是用户的免费次数，那么只需要记录次数就可以了
			if (self::$defeatRet['ret'] == 'user_ok')
			{
				// 增加一次用户攻击的次数
				self::addUserKillNum($enemyID);
			}
			// 服务器的话，不能和用户的嬲在一起
			else if (self::$defeatRet['ret'] == 'server_ok')
			{
				// 增加一次服务器攻击次数
				self::addServerKillNum($enemyID, $groupID);
			}
			// 强制攻击, 需要扣钱
			else if (self::$defeatRet['ret'] == 'force_ok')
			{
				// 增加一次用户攻击的次数
				self::addUserKillNum($enemyID);
				// 先查看看多打了几次
				$times = self::$defeatRet['annihilate'] - intval(btstore_get()->ARMY[$enemyID]['free_time']);
				// 再查看需要扣多少钱
				$gold = CopyConf::FORCE_FIGHT_COINS + $times * intval(btstore_get()->ARMY[$enemyID]['need_gold']);
				// 扣除金币
				$user->subGold($gold);
			}

			/**********************************************************************************************************
	 		 * 如果是普通怪，做这些事情，活动怪就不记了
	 		 **********************************************************************************************************/
			if (btstore_get()->ARMY[$enemyID]['refresh_army'] == CopyConf::NORMAL_ENEMY)
			{
				/******************************************************************************************************
	 		 	 * 是否需要开启新的副本, 如果需要开启新的军团怪，则给军团怪增加次数
	 		 	 ******************************************************************************************************/
				self::needOpenNewCopies($enemyID);
				// 如果是第一次打这个部队，才需要进行检查
				if (self::$isFirstTime)
				{
					self::needOpenNewActivityGroupEnemy($enemyID);
				}

				/******************************************************************************************************
	 		 	 * 保存进度、重新计算活动信息
	 		 	 ******************************************************************************************************/
				// 增加此部队的击败次数
				self::addKillNum(self::$copyID, $enemyID);
				// 保存进度
				self::saveProgress(self::$copyID, $enemyID);
				// 记录最好成绩
				self::addDefeatAppraisal(self::$copyID, $enemyID, BattleDef::$APPRAISAL[$atkRet['appraisal']]);
	
				/******************************************************************************************************
	 		 	 * 判断本副本是否已经结束
	 		 	 ******************************************************************************************************/
				// 如果已经结束了此副本
				if (self::isCopyOver(self::$copyID) === 'yes')
				{
					/**************************************************************************************************
	 		 	 	 * 玩儿完了，或许需要给任务系统一点儿货……
	 		 	 	 **************************************************************************************************/
					TaskNotify::passCopy();
					// 发送炫耀性质的聊天信息
					if (btstore_get()->COPY[self::$copyID]['need_msg'])
					{
						ChatTemplate::sendCopyEnd($user->getTemplateUserInfo(), self::$copyID);
					}

					/**************************************************************************************************
	 		 	 	 * 给玩家过关奖励的时刻到了！哎，其实玩家们最应该感谢的人还应该是我啊……
	 		 	 	 **************************************************************************************************/
					// 通关奖励阅历
					$experience += btstore_get()->COPY[self::$copyID]['experience'];
					// 通关奖励经验
					$belly += intval(btstore_get()->COPY[self::$copyID]['belly']);
					// 通关道具奖励
					$isPassed = true;
				}
			}
			// 在这里掉落物品 和 英雄
			$itemIDs = self::dropItems($enemyID, $isPassed);
			// 计算副本奖励信息
			$prizeIDs = self::getDefeatScore(self::$copyID, $enemyID, $atkRet);
		}

		/**************************************************************************************************************
 		 * 先添加人物奖励
 		 **************************************************************************************************************/
		// 获胜奖励游戏币
		$user->addBelly($belly);
		// 获胜奖励阅历
		$user->addExperience($experience);
		// 获胜奖励威望
		$user->addPrestige($prestige);

		/**************************************************************************************************************
 		 * 再添加所有英雄的经验
 		 **************************************************************************************************************/
		// 返回时候使用的英雄数据
		$heroList = array();
		// 先处理主英雄数据, 否则卡等级时，用户其他英雄有可能会损失一部分经验
		$masterHeroObj = $user->getMasterHeroObj();
		// 获取主英雄id
		$heroList[$masterHeroObj->getHid()]['hid'] = $masterHeroObj->getHid();
		// 获取主形象id
		$heroList[$masterHeroObj->getHid()]['htid'] = $masterHeroObj->getHtid();
		// 获取原等级
		$heroList[$masterHeroObj->getHid()]['initial_level'] = $masterHeroObj->getLevel();
		// 增加经验值
		$masterHeroObj->addExp($exp);
		// 获取提升等级
		$heroList[$masterHeroObj->getHid()]['current_level'] = $masterHeroObj->getLevel();
		// 获取当前经验
		$heroList[$masterHeroObj->getHid()]['current_exp'] = $masterHeroObj->getExp();
		// 获取获得经验
		$heroList[$masterHeroObj->getHid()]['add_exp'] = $exp;
		// 循环处理所有其他英雄数据
		foreach ($atkRet['team1'] as $hero)
		{
			// 不为NPC的英雄 并且不为主英雄
			if (HeroUtil::isHero($hero['hid']) && $hero['hid'] != $masterHeroObj->getHid())
			{
				// 获取英雄对象
				$heroObj = $user->getHeroObj($hero['hid']);
				// 获取英雄id
				$heroList[$hero['hid']]['hid'] = $hero['hid'];
				// 获取形象id
				$heroList[$hero['hid']]['htid'] = $heroObj->getHtid();
				// 获取原等级
				$heroList[$hero['hid']]['initial_level'] = $heroObj->getLevel();
				// 增加经验值
				$heroObj->addExp($exp);
				// 获取提升等级
				$heroList[$hero['hid']]['current_level'] = $heroObj->getLevel();
				// 获取当前经验
				$heroList[$hero['hid']]['current_exp'] = $heroObj->getExp();
				// 获取获得经验
				$heroList[$hero['hid']]['add_exp'] = $exp;
			}
		}
		// 将最后结果更新到数据库
		$user->update();
		// 发送金币通知
		if ($gold > 0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_COPY_FORCEFIGHT, $gold, Util::getTime());
		}

		/**************************************************************************************************************
 		 * 奖励各种货
 		 **************************************************************************************************************/
		// 将乱七八糟的东西全扔到数据库
		$copyInst = new MyCopy();
		$copyInst->save(self::$copyID);
		// 返回奖励内容
	 	return array('arrHero' => $heroList, 'belly' => $belly, 'exp' => $exp, 'prizes' => $prizeIDs,
	 	             'experience' => $experience, 'prestige' => $prestige, 'equip' => $itemIDs);
	}

	/**
	 * 攻击一个部队
	 * @param int $copyID						副本ID
	 * @param int $enemyID						敌人ID
	 * @param int $npcTeamID					NPC小队ID
	 * @param string $heroList					英雄序列
	 * @throws Exception
	 */
	public static function attack($copyID, $enemyID, $npcTeamID = null, $heroList = null)
	{
		/**************************************************************************************************************
 		 * 查看是否可以攻击
 		 **************************************************************************************************************/
		// 检查参数
		if (!isset(btstore_get()->ARMY[$enemyID]) || btstore_get()->ARMY[$enemyID]['copy_id'] != $copyID)
		{
			Logger::fatal('The %d enemy not in this %d copy!', $enemyID, $copyID);
			throw new Exception('fake');
		}
		// 获取怪物小队ID
		$teamID = btstore_get()->ARMY[$enemyID]['monster_list_id'];
		// 检查部队类型
		$armyType = btstore_get()->ARMY[$enemyID]['army_type'];
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 获取用户ID
		$uid = $user->getUid();
		// 获取副本信息
		$copyInst = new MyCopy();
		$copyInfo = $copyInst->getCopyInfo($copyID);
		// 如果没获取到副本信息
		if ($copyInfo === false)
		{
			Logger::fatal('Can not get copy info! The copy id is %d.', $copyID);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 获取当前阵型详情, 并检查是否可以攻击
 		 **************************************************************************************************************/
		// 获取用户选择的NPC阵型信息
		if (!empty($npcTeamID) && !empty($heroList) && $armyType == CopyConf::ARMY_TYPE_NPC)
		{
			$userFormation = EnFormation::getNpcFormation($npcTeamID, $heroList);
			// 获取NPC阵型
			$formationID = btstore_get()->TEAM[$teamID]['fid'];
		}
		// 用户当前阵型
		else if ($armyType == CopyConf::ARMY_TYPE_NML)
		{
			$userFormation = EnFormation::getFormationInfo($uid);
			// 将阵型ID设置为用户当前默认阵型
			$formationID = $user->getCurFormation();
			// 这时候拉取所有缓存信息
			$user->prepareItem4CurFormation();
		}
		// 参数不全的时候，出错了
		else 
		{
			Logger::fatal('Para not enough! The enemy type is %d.', $armyType);
			throw new Exception('fake');
		}
		// 敌人信息
		$enemyFormation = EnFormation::getBossFormationInfo($teamID);
		// 如果因为阵型不能攻击的话
		$formationRet = EnFormation::checkUserFormation($uid, $userFormation, $armyType, $npcTeamID);
		if ($formationRet != 'ok')
		{
			if ($formationRet == 'not_enough_hp')
			{
				return 'hp';
			}
			Logger::fatal('Can not attack, checkUserFormation error, uid is %d, copyid is %d, enemyid is %d.', 
			              $uid, $copyID, $enemyID);
			throw new Exception('fake');
		}
		// 如果因为其他原因
		$attackRet = self::canAttack($enemyID, $uid, $copyID);
		// 因为其他原因不能攻击的话
		if ($attackRet != 'ok')
		{
			if ($attackRet == 'cd')
			{
				return 'cd';
			}
			Logger::warning('Can not attack, canAttack error, uid is %d, copyid is %d, enemyid is %d.', 
			                $uid, $copyID, $enemyID);
			throw new Exception('fake');
		}
		
		// 判断攻击次数
		$defeatRet = self::checkServerDefeat($enemyID, $uid);
		// 不让打，我也没办法了
		if ($defeatRet['ret'] == 'no')
		{
			Logger::fatal('Not enough money! Try show me the money?', $uid, $enemyID);
			throw new Exception('fake');
		}
		// 将对象转化为数组
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation);
		Logger::debug('The hero list is %s', $userFormationArr);
		$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation);
		Logger::debug('The boss list is %s', $enemyFormationArr);

		/**************************************************************************************************************
 		 * 调用战斗模块
 		 **************************************************************************************************************/
		// 获取对话信息
		// 查看这个贪得无厌的人玩儿了的部队信息
		$defeat = $copyInfo['va_copy_info']['defeat_id_times'];
		// 查看是否是强制回合
		$battleType = intval(btstore_get()->ARMY[$enemyID]['battle_type']) == CopyDef::FORCE_ROUND ? 
		                                               CopyDef::FORCE_ROUND : CopyDef::NORMAL_ROUND;
		                                               
		// 记录参数 —— 回调函数中使用
		self::$copyID = $copyID;
		self::$defeatRet = $defeatRet;
		self::$isFirstTime = isset($defeat[$enemyID]) ? false : true;

		// 调用战斗模块
		$bt = new Battle();
		$atkRet = $bt->doHero(array('name' => $user->getUname(), 
		                            'level' => $user->getLevel(),
		                            'isPlayer' => true,
		                            'flag' => 0,
		                            'formation' => $formationID,
		                            'uid' => $uid,
		                            'arrHero' => $userFormationArr),
		                      array('name' => btstore_get()->ARMY[$enemyID]['name'], 
		                            'level' => btstore_get()->ARMY[$enemyID]['lv'],
		                            'isPlayer' => false,
		                            'flag' => 0,
		                            'formation' => btstore_get()->TEAM[$teamID]['fid'],
		                            'uid' => $enemyID,
		                            'arrHero' => $enemyFormationArr),
		                      $battleType,
		                      array("CopyLogic", "calculateFightRet"),
		                      self::getVictoryConditions($enemyID), 
		                      array('bgid' => intval(btstore_get()->ARMY[$enemyID]['background_id']),
		                            'musicId' => btstore_get()->ARMY[$enemyID]['music_path'],
		                            'type' => BattleType::COPY));
		// 战斗系统返回值
		Logger::debug('Ret from battle is %s.', $atkRet);

		/**************************************************************************************************************
		 * 记录首杀
 		 **************************************************************************************************************/
		// 必须先获胜
		if (BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'])
		{
			// 检查首杀
			$ret = self::checkFirstDown($atkRet['server']['uid1'], $enemyID, $atkRet['server']['brid']);
			// 如果首杀成功了
			if ($ret)
			{
				// 固化一下录像
				$bt->setPermanent($atkRet['server']['brid']);
				// 通知成就系统
				EnAchievements::notify($uid, AchievementsDef::DEFEAT_ARMY_NO_1, $enemyID);
			}
			// 如果是第一次攻击这个敌人并且还不是活动怪, 检查战斗录像
			if (self::$isFirstTime && btstore_get()->ARMY[$enemyID]['refresh_army'] == CopyConf::NORMAL_ENEMY)
			{
				// 那么需要保存战斗录像
				self::checkSaveReplay($uid, $enemyID, $user->getGroupId(), $atkRet['server']['brid']);
			}
		}
		// 如果失败了，可能会发一个可怜虫成就
		else 
		{
			// 通知成就系统
			EnAchievements::notify($uid, AchievementsDef::DEFEAT_ARMY_LOSE, $enemyID, 1);
			EnAchievements::notify($uid, AchievementsDef::LOSE_ENEMY_TIMES, 1);
		}

		/**************************************************************************************************************
 		 * 战斗后的各种处理
 		 **************************************************************************************************************/
		// 计算副本奖励
		$prizeIDs = $atkRet['server']['reward']['prizes'];

		// 前端不需要道具信息，unset掉
		unset($atkRet['server']['reward']['equip']['item']);
		unset($atkRet['server']['reward']['prizes']);
		// 将战斗结果返回给前端
		return array('fightRet' => $atkRet['client'], 'bloodPackage' => $user->getBloodPackage(),
		             'curHp' => self::$heroHPs, 'cd' => $user->getFightCDTime(), 'reward' => $atkRet['server']['reward'],
		             'appraisal' => BattleDef::$APPRAISAL[$atkRet['server']['appraisal']], 'prizeIDs' => $prizeIDs);
	}

	/**
	 * 获取用户玩儿此副本的次数
	 * @param int $copyID						副本ID
	 */
	public static function getUserPlayTimes($copyID)
	{
		// 获取玩此副本的次数
		$copyInst = new MyCopy();
		$copyInfo = $copyInst->getCopyInfo($copyID);
		return $copyInfo === false ? 0 : $copyInfo['raid_times'];
	}

	/**
	 * 开启新副本
	 * 
	 * @param int $enemyID						已经拿下的部队ID
	 */
	private static function needOpenNewCopies($enemyID)
	{
		// 判断是否需要开启新的 副本
		if (!empty(btstore_get()->COPY['enemy'][$enemyID]))
		{
			$nextCopies = btstore_get()->COPY['enemy'][$enemyID];
		}
		// 如果真要开启新的副本
		if (!empty($nextCopies))
		{
			$copyInst = new MyCopy();
			foreach ($nextCopies as $copyID)
			{
				// 不为空的时候，加入一个新副本
				if (!empty($copyID))
				{
					$copyInst->addNewCopy($copyID);
					$copyInst->save($copyID);
				}
			}
		}

		$nextCopies = array();
		if (!empty(btstore_get()->ELITE_COPY['enemy'][$enemyID]))
		{
			$nextCopies = btstore_get()->ELITE_COPY['enemy'][$enemyID];
		}
		if (!empty($nextCopies))
		{
			foreach ($nextCopies as $copyID)
			{
				EnEliteCopy::openNewEliteCopy($copyID);
			}
		}
	}

	/**
	 * 查看是否需要开启新的活动副本(添加次数用)
	 * 
	 * @param int $enemyID						已经拿下的部队ID
	 */
	private static function needOpenNewActivityGroupEnemy($enemyID)
	{
		// 如果没有配置军团，完事大吉，直接返回
		if (empty(btstore_get()->GROUP_ARMY['act_enemies'][$enemyID]))
		{
			return ;
		}
		// 记录下军团怪ID
		$groupEnemyID = btstore_get()->GROUP_ARMY['act_enemies'][$enemyID];
		// 获取军团怪种类, 查看是否是互动军团
		$groupEnemyType = btstore_get()->GROUP_ARMY[$groupEnemyID]['type'];
		// 如果不是活动怪，我也乐得清闲
		if ($groupEnemyType == CopyConf::NORMAL_ENEMY)
		{
			return ;
		}
		// 打这个怪，触发了活动怪，需要更新数据库，并给人家点儿次数
		$uid = RPCContext::getInstance()->getUid();
		// 获取用户数据
		$groupBattleInfo = CopyDao::getGroupBattleInfo($uid);
		// 如果没有数据，那么初始化一条
		if ($groupBattleInfo === false)
		{
			// 得到一条空数据
			$groupBattleInfo = CopyDao::initGroupBattle($uid);
		}
		// 如果没设置过这件事，才需要设置
		if (!isset($groupBattleInfo['va_copy_info']['copy_times'][$groupEnemyID]))
		{
			// 添加军团怪次数
			$groupBattleInfo['va_copy_info']['copy_times'][$groupEnemyID] = CopyConf::DAY_ACTIVITY_TIMES;
			// 更新到数据库
			CopyDao::updateGroupBattle($uid, $groupBattleInfo);
		}
	}

	/**
	 * 判断是否已经玩儿完了
	 * @param int $copyID						副本ID
	 * @return already：string					先前已经玩儿过这个副本
	 * 		   no:string						还没结束副本
	 * 		   over:string						副本已经结束
	 */
	public static function isCopyOver($copyID)
	{
		// 如果已经通关过，那么就直接告诉他玩儿过了
		if (self::getUserPlayTimes($copyID) > 0)
		{
			return 'already';
		}

		// 获取副本信息
		$copyInst = new MyCopy();
		$copyInfo = $copyInst->getCopyInfo($copyID);
		// 查看这个贪得无厌的人玩儿了的部队信息
		$defeat = $copyInfo['va_copy_info']['defeat_id_times'];
		// 获取此副本的部队总数
		$armyNum = btstore_get()->COPY[$copyID]['enemy_num'];
		// 比较
		if (count($defeat) < $armyNum)
		{
			// 如果还有部队没有打完
			return 'no';
		}
		// 达成成就
		EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::PASS_COPY, $copyID);
		// 获取通过该副本可以获取的奖励
		$fullScore = $copyInst->addCopyScore($copyID, intval(btstore_get()->COPY[$copyID]['over_score']));
		// 如果已经获取了所有奖励
		if ($fullScore)
		{
			// 达成成就
			EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::GET_ALL_COPY_PRIZE, $copyID);
		}
		// 增加一次副本完成次数
		$copyInst->addCopyRaid($copyID);
		return 'yes';
	}

	/**
	 * 在回调函数中，战斗结束后掉落道具和英雄
	 * @param array $enemyID					部队ID
	 */
	private static function dropItems($enemyID, $isPassed)
	{
		/**************************************************************************************************************
 		 * 掉落道具
 		 **************************************************************************************************************/
		// 掉落普通道具
		$dropIDs = btstore_get()->ARMY[$enemyID]['drop_ids']->toArray();
		// 查看是否在某些任务中, 掉落任务道具
		$dropIDs = array_merge($dropIDs, EnTask::getDroptableId($enemyID));
		// 获取当前用户
		$user = EnUser::getUserObj();

		// 声明背包信息返回值
		$bagInfo = array();
		// 需要返回给前端的所有掉落物品详细信息
		$itemArr = array();
		// 掉落道具, 放到背包里
		$bag = BagManager::getInstance()->getBag();
		// 如果配置的有掉落表
		if (!empty($dropIDs) && !empty($dropIDs[0]))
		{
			// 循环处理所有掉落表ID
			foreach ($dropIDs as $dropID)
			{
				// 掉落物品
				$itemIDs = ItemManager::getInstance()->dropItem($dropID);
				// 记录发送的信息
				$msg = chatTemplate::prepareItem($itemIDs);
				// 标志是否背包已经满了
				$deleted = FALSE;
				// 循环处理所有的掉落物品
				foreach ($itemIDs as $itemID)
				{
					// 背包还没满的时候，就往背包里面塞吧……
					if ($deleted == FALSE)
					{
						// 先获取数据信息，保存。
						$itemTmp = ItemManager::getInstance()->itemInfo($itemID);
						// 塞一个货到背包里，可以使用临时背包
						if ($bag->addItem($itemID, TRUE) == FALSE)
						{
							// 如果连临时背包都满了的话， 删除该物品
							ItemManager::getInstance()->deleteItem($itemID);
							// 修改标志量
							$deleted = TRUE;
						}
						else
						{
							// 保留物品详细信息，传给前端
							$itemArr[] = $itemTmp;
						}
					}
					// 背包满了，不行了
					else 
					{
						// 全部删除，多可惜啊……换成钱不行么？或者送给刘洋的号……
						ItemManager::getInstance()->deleteItem($itemID);
					}
				}
				// 发送信息
				chatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
			}
		}
		// 如果通关了副本
		if ($isPassed && !empty(btstore_get()->COPY[self::$copyID]['item_ids']))
		{
			// 生成物品
			$itemIDs = ItemManager::getInstance()->addItems(btstore_get()->COPY[self::$copyID]['item_ids']);
			// 记录发送的信息
			$msg = chatTemplate::prepareItem($itemIDs);
			// 压入背包
			$bag->addItems($itemIDs, TRUE);
			// 发送信息
			chatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
		}
		// 保存用户背包数据，并获取改变的内容
		$bagInfo = $bag->update();

		/**************************************************************************************************************
 		 * 掉落英雄
 		 **************************************************************************************************************/
		// 获取英雄ID和掉落权重
		$heroID = intval(btstore_get()->ARMY[$enemyID]['drop_hero_id']);
		// 如果会掉落英雄的话
		if (!empty($heroID))
		{
			$heroWeight = btstore_get()->ARMY[$enemyID]['drop_hero_weight'];
			// 随机出结果 
			$randRet = rand(0, CopyConf::LITTLE_WHITE_PERCENT);
			// 人品好啊，随机出来了
			if ($randRet < $heroWeight)
			{
				// 随机出来了，看看是否已经拥有这个英雄
				// 还没有这个英雄呢……
				if (!$user->hasHero($heroID))
				{
					// 放到酒馆里
					$user->addNewHeroToPub($heroID);
					Logger::debug('Add new hero to pub, hero id is %d', $heroID);
				}
				// 有的了话，就赋值为0
				else 
				{
					$heroID = 0;
				}
			}
			// 没随机出来也告诉前端
			else 
			{
				$heroID = 0;
			}
		}
		// 返回已经掉落的各种IDs
		return array('item' => $itemArr, 'bag' => $bagInfo, 'heroID' => $heroID);
	}

	/**
	 * 保存进度
	 * @param int $copyID						副本ID
	 * @param int $enemyID						部队ID
	 */
	private static function saveProgress($copyID, $enemyID)
	{
		/**************************************************************************************************************
 		 * 获取副本用户进度信息
 		 **************************************************************************************************************/
		// 获取副本信息
		$copyInst = new MyCopy();
		$copyInfo = $copyInst->getCopyInfo($copyID);
		// 如果玩家还没有这个副本信息  (很奇怪啊，都保存进度了，怎么能没有呢？)
		if ($copyInfo === false)
		{
			Logger::fatal('Can not find copy info from session, copyID is %d', $copyID);
			throw new Exception('fake');
		}
		// 获取用户进度信息
		$progress = $copyInfo['va_copy_info']['progress'];
		// 清理信息
		if (isset($progress[$enemyID]))
		{
			unset($progress[$enemyID]);
		}

		/**************************************************************************************************************
 		 * 判断分支，即查看开启的下个部队ID是不是多个
 		 **************************************************************************************************************/
		// 获取开启的下个部队ID数组
		$nextEnemies = btstore_get()->ARMY[$enemyID]['next_enemies'];
		Logger::debug('Next enemies is %s.', $nextEnemies->toArray());
		// 开启的部队信息放入数组
		foreach ($nextEnemies as $armyID)
		{
			$armyID = intval($armyID);
			$progress[$armyID] = $armyID;
		}
		Logger::debug('Ther user is beat %d enemy, progress now is %s.', $enemyID, $progress);

		/**************************************************************************************************************
 		 * 保存进度
 		 **************************************************************************************************************/
		$copyInst->updUserProgress($copyID, $progress);
	}

	/**
	 * 更新攻击某部队的最好成绩
	 * 
	 * @param int $copyID						副本ID
	 * @param int $enemyID						部队ID
	 * @param int $appraisal					评价
	 */
	private static function addDefeatAppraisal($copyID, $enemyID, $appraisal)
	{
		// 获取副本信息
		$copyInst = new MyCopy();
		$copyInfo = $copyInst->getCopyInfo($copyID);
		// 如果玩家还没有这个副本信息 
		if ($copyInfo === false)
		{
			Logger::fatal('Can not find copy info from session, copyID is %d', $copyID);
			throw new Exception('fake');
		}
		// 如果没设置过好成绩，或者成绩不够好
		if (!isset($copyInfo['va_copy_info']['id_appraisal'][$enemyID]) ||
		    $copyInfo['va_copy_info']['id_appraisal'][$enemyID] > $appraisal)
		{
			// 那么给戴个大红花
			$copyInst->setDefeatAppraisal($copyID, $enemyID, $appraisal);
		}
	}

	/**
	 * 加算攻击过的敌人, 副本结束的时候传给任务系统
	 * @param int $copyID						副本ID
	 * @param int $enemyID						部队ID
	 */
	private static function addKillNum($copyID, $enemyID)
	{
		/**************************************************************************************************************
 		 * 获取副本用户杀怪信息
 		 **************************************************************************************************************/
		// 获取副本信息
		$copyInst = new MyCopy();
		$copyInfo = $copyInst->getCopyInfo($copyID);
		// 如果玩家还没有这个副本信息 
		if ($copyInfo === false)
		{
			Logger::fatal('Can not find copy info from session, copyID is %d', $copyID);
			throw new Exception('fake');
		}
		// 获取用户杀怪信息
		$defeatList = $copyInfo['va_copy_info']['defeat_id_times'];

		/**************************************************************************************************************
 		 * 加算杀怪信息
 		 **************************************************************************************************************/
		if (isset($defeatList[$enemyID]))
		{
			++$defeatList[$enemyID];
		}
		else 
		{
			$defeatList[$enemyID] = 1;
		}
		// 记录日志
		Logger::debug('The total number defeat No.%d army is %d.', $enemyID, $defeatList[$enemyID]);
		// 普通的次数成就
		EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::DEFEATE_ENEMY_TIMES, $enemyID, $defeatList[$enemyID]);

		/**************************************************************************************************************
 		 * 保存副本杀怪信息
 		 **************************************************************************************************************/
		$copyInst->updUserDefeatNum($copyID, $defeatList);
	}

	/**
	 * 获取某部队的刷新点信息，如果不是活动怪则为0
	 * @param int $enemyID						部队ID
	 * @throws Exception
	 */
	private static function getEnemyRefreshPointID($enemyID)
	{
		// 刷新点ID，普通怪刷新点ID为0
		$rpID = 0;
		// 如果没设置，直接返回
		if (!isset(btstore_get()->ARMY[$enemyID]))
		{
			return $rpID;
		}
		Logger::debug('The type of enemy %d is %d.', $enemyID, btstore_get()->ARMY[$enemyID]['refresh_army']);
		// 查看是否为刷新怪
		if (btstore_get()->ARMY[$enemyID]['refresh_army'] == CopyConf::REFRESH_ENEMY)
		{
			// 如果是刷新怪，则需要获取刷新点信息
			$rpEnemies = RPCContext::getInstance()->getSession('copy.rpEnemies');
			// 如果是刷新怪，还没获取到刷新点信息
			if (!isset($rpEnemies[$enemyID]['refreshPoint']))
			{
				Logger::fatal('Can not get refreshPoint info from session! Enemy ID is %d.', $enemyID);
				throw new Exception('fake');
			}
			// 保存刷新点信息
			$rpID = $rpEnemies[$enemyID]['refreshPoint'];
		}
		Logger::debug('The enemy is %d, rpID is %d.', $enemyID, $rpID);
		return $rpID;
	}

	/**
	 * 增加一次用户攻击部队的次数
	 * @param int $enemyID						敌人ID
	 * @throws Exception
	 */
	private static function addUserKillNum($enemyID)
	{
		/**************************************************************************************************************
 		 * 获取用户杀敌信息
 		 **************************************************************************************************************/
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		if (empty($uid))
		{
			Logger::fatal('Can not get copy info from session!');
			throw new Exception('fake');
		}

		// 刷新点ID，普通怪刷新点ID为0
		$rpID = self::getEnemyRefreshPointID($enemyID);

		// 获取用户实际攻击次数
		$userDefeat = CopyDao::getUserDefeatNum($uid, $enemyID, $rpID);
		// 记录下当日开始刷新时间
		$curTime = date("Y-m-d ", Util::getTime());
		$curTime .= CopyConf::REFRESH_TIME;
		$todayBegin = strtotime($curTime);

		/**************************************************************************************************************
 		 * 计算杀敌信息并更新
 		 **************************************************************************************************************/
		// 如果上次攻击的计时还没到今天，表明今天还没打过, 次数为1,否则次数加1.
		$atkNum = $todayBegin > $userDefeat['current_day'] ? 1 : $userDefeat['annihilate'] + 1;
		// 更新到数据库
		CopyDao::addUserDefeatNum($uid, $enemyID, $rpID, $atkNum);
	}

	/**
	 * 增加一次服务器攻击次数
	 * 
	 * @param int $enemyID						敌人ID
	 * @param int $groupID						用户阵营
	 */
	protected static function addServerKillNum($enemyID, $groupID)
	{
		// 刷新点ID，普通怪刷新点ID为0
		$rpID = self::getEnemyRefreshPointID($enemyID);

		// 获取服务器实际攻击次数
		$serverDefeat = CopyDao::getServerDefeatNum($enemyID, $groupID, $rpID);
		// 如果数据库中压根就没用这个记录
		if ($serverDefeat === false)
		{
			// 插入一条为空的数据先
			$ret = CopyDao::clearServerDefeatNum($enemyID, $rpID, $groupID);
			// 还没人打过，次数清为 0 
			$serverDefeat = 0;
		}
		// 先抢一次攻击次数再说，哈哈，感激我吧！
		$ret = CopyDao::addServerDefeatNum($enemyID, $groupID, $rpID, $serverDefeat);
		// 如果没抢到，那么只有返回了，人品啊人品……
		if ($ret['affected_rows'] === 0)
		{
			return false;
		}
		// 成功抢到一次攻击机会
		return true;
	}

	/**
	 * 获取胜利条件
	 * @param int $enemyID						部队ID
	 */
	public static function getVictoryConditions($enemyID)
	{
		// 返回值
		$ret = array();
		// 获取NPC相关条件
		$npcCon = btstore_get()->ARMY[$enemyID]['npc_condition'];
		$npcID = intval($npcCon['id']);
		$npcHP = intval($npcCon['hp']);
		// 如果有NPC条件
		if (!empty($npcID))
		{
			$ret['team1'] = array(array($npcID, $npcHP));
		}
		// 获取怪物条件
		$monsterCon = btstore_get()->ARMY[$enemyID]['monster_condition'];
		$monsterID = intval($monsterCon['id']);
		$monsterHP = intval($monsterCon['hp']);
		// 如果有NPC条件
		if (!empty($monsterID))
		{
			$ret['team2'] = array(array($monsterID, $monsterHP));
		}
		// 取战斗总回合
		$attackRound = btstore_get()->ARMY[$enemyID]['fight_round'];
		// 如果有战斗回合条件
		if (!empty($attackRound))
		{
			$ret['attackRound'] = intval($attackRound);
		}
		// 取坚守回合
		$defendRound = btstore_get()->ARMY[$enemyID]['defend_round'];
		// 如果有坚守回合条件
		if (!empty($defendRound))
		{
			$ret['defendRound'] = intval($defendRound);
		}
		Logger::debug('End conditions is %s.', $ret);
		// 返回
		return $ret;
	}

	/**
	 * 查看是否打过这个部队
	 * @param int $enemyID						部队ID
	 */
	public static function isEnemyDefeated($enemyID)
	{
		// 获取副本信息
		$copyInst = new MyCopy();
		return $copyInst->isEnemyDefeated($enemyID);
	}

	/**
	 * 查看是否打过这组部队
	 * 
	 * @param array $enemyID					部队ID数组
	 */
	public static function getEnemiesDefeatNum($enemyIDs)
	{
		// 返回值
		$ret = array();
		// 获取副本信息
		$copyInst = new MyCopy();
		// 循环查看所有敌人ID
		foreach ($enemyIDs as $enemyID)
		{
			// 记录攻击次数
			$ret[$enemyID] = $copyInst->isEnemyDefeated($enemyID);
		}
		// 返回给前端
		return $ret;
	}

	/**
	 * 查看该部队当日被A的次数, 如果没有查到回数，则返回false
	 * 
	 * @param int $uid							用户ID
	 * @param int $enemyID						部队ID
	 */
	public static function getEnemyDefeatNum($uid, $enemyID)
	{
		// 获取用户阵营
		$userInfo = EnUser::getUser($uid);
		// 刷新点ID，普通怪刷新点ID为0
		$rpID = self::getEnemyRefreshPointID($enemyID);
		// 获取用户实际攻击次数
		$userDefeat = CopyDao::getUserDefeatNum($uid, $enemyID, $rpID);
		// 保存次数
		$userTimes = $userDefeat['annihilate'];
		// 检查是否翻日
		if (!Util::isSameDay($userDefeat['current_day'], CopyConf::REFRESH_TIME))
		{
			$userTimes = 0;
		}
		// 获取次数
		return array(
		'serverDefeat' => CopyDao::getServerDefeatNum($enemyID, $userInfo['group_id'], $rpID),
		'userDefeat' => $userTimes);
	}

	/**
	 * 冲击世界纪录
	 * 
	 * @param int $uid							用户ID
	 * @param int $enemyID						部队ID
	 * @param int $replayID						战报ID
	 */
	public static function checkFirstDown($uid, $enemyID, $replayID)
	{
		// 取服务器排名
		$rankList = CopyDao::getArmyFirstDownRank($enemyID);
		// 如果排名大于等于10，就不再记录了。下手太慢了啊，兄弟。下次充点儿人民币吧……
		if ($rankList[0]['rank'] >= CopyConf::FD_NUMBER)
		{
			return false;
		}
		// 已经有某人名号了，就别在冲击了啊，又不赢点儿啥好处……
		foreach ($rankList as $rank)
		{
			if ($rank['uid'] == $uid)
			{
				return false;
			}
		}

		// 抓紧时间冲击记录
		$ret = CopyDao::addFirstDownRank($uid, $enemyID, $rankList[0]['rank'], $replayID);
		// 不巧冲击失败，这个简直太遂了
		if ($ret['affected_rows'] == 0)
		{
			return false;
		}
		// 如果冲击成功, 返回true
		Logger::debug('The user %d, first down %d enemy.', $uid, $enemyID);
		return true;
	}
	
	/**
	 * 保存珍贵的第一次
	 * 
	 * @param int $uid							用户ID
	 * @param int $enemyID						部队ID
	 * @param int $groupID						阵营ID
	 * @param int $replayID						战报ID
	 */
	public static function checkSaveReplay($uid, $enemyID, $groupID, $replayID)
	{
		Logger::debug('checkSaveReplay start.');
		// 取所有战斗录像
		$replayList = CopyDao::getReplayList($enemyID, $groupID);
		// 检查数据库中没有自己的数据，以防万一，进行一次搜索
		foreach ($replayList as $replay)
		{
			// 如果数据库中已经有记录，并且是这个人的记录，那么就直接返回
			if ($replay['uid'] != 0 && $replay['uid'] == $uid)
			{
				return ;
			}
		}
		// 先取出现在录像的个数
		$rpCount = count($replayList);
		// 如果没有阵营且不到15个战斗录像  或  有阵营还不到5个战斗录像
		if (($rpCount < CopyConf::REPLAY_LIST_NUM && $groupID == CopyConf::GROUP_00) ||
		    ($rpCount < CopyConf::REPLAY_GROUP_NUM && $groupID != CopyConf::GROUP_00))
		{
			// 加入个新的战斗录像
			CopyDao::addNewReplay($uid, $enemyID, $groupID, $replayID);
		}
		// 超过的话
		else 
		{
			// 获取需要更改的战斗录像uid, 并更新战斗录像
			CopyDao::updateReplay($replayList[0]['uid'], $uid, $enemyID, $replayID);
			Logger::debug('Update replay, uid is %d, armyID is %d, replayID is %d.',
			              $replayList[0]['uid'], $enemyID, $replayID);
		}
		Logger::debug('checkSaveReplay end.');
	}

	/**
	 * 返回所有的攻略和战报信息
	 * 
	 * @param int $enemyID						部队ID
	 */
	public static function getReplayList($enemyID)
	{
		// 获取战斗录像
		$replayList = CopyDao::getAllReplayList($enemyID);
		// 空的时候什么都不做 
		if ($replayList[0]['uid'] !== 0)
		{
			// 循环获取用户名
			$arrUid = Util::arrayExtract($replayList, 'uid');
			$arrUser = Util::getArrUser($arrUid, array('uname'));
			foreach ($replayList as $key => $replay)
			{
				$replayList[$key]['uname'] = $arrUser[$replay['uid']]['uname'];
			}
		}
		// 取服务器排名
		$rankList = CopyDao::getArmyFirstDownRank($enemyID);
		// 空的时候什么都不做 
		if ($rankList[0]['uid'] !== 0)
		{
			// 循环获取用户名
			$arrUid = Util::arrayExtract($rankList, 'uid');
			$arrUser = Util::getArrUser($arrUid, array('uname'));
			foreach ($rankList as $key => $replay)
			{
				$rankList[$key]['uname'] = $arrUser[$replay['uid']]['uname'];
			}
		}
		// 返回
		return array('replayList' => $replayList, 'rankList' => $rankList);
	}

	/**
	 * 获取击败部队后的奖励
	 * 
	 * @param int $copyID						副本ID
	 * @param int $enemyID						攻击对象ID
	 * @param int $atkRet						战斗模块的返回值
	 */
	public static function getDefeatScore($copyID, $enemyID, $atkRet)
	{
		/**************************************************************************************************************
 		 * 检查并获取副本信息
 		 **************************************************************************************************************/
		// 记录返回值
		$prizeIDs = array();
		// 如果没有设置奖励
		if (empty(btstore_get()->PRIZE['enemy'][$enemyID]) || 
		    BattleDef::$APPRAISAL[$atkRet['appraisal']] > BattleDef::$APPRAISAL['D'])
		{
			return $prizeIDs;
		}
		// 获取副本信息
		$copyInst = new MyCopy();
		$copyInfo = $copyInst->getCopyInfo($copyID);
		// 如果尚未开启这个副本
		if ($copyInfo === false)
		{
			Logger::fatal('The copy is not open! The copy id is %d.', $copyID);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 依次处理奖励
 		 **************************************************************************************************************/
		// 设置了奖励，遍历奖励ID组
		foreach (btstore_get()->PRIZE['enemy'][$enemyID] as $prizeID)
		{
			// 先检查这个奖励是否已经实现过了
			if (isset($copyInfo['va_copy_info']['prize_ids'][$prizeID]) && 
			    $copyInfo['va_copy_info']['prize_ids'][$prizeID] === true)
			{
				// 给过奖励了，就不再次给了，直接看下一条奖励是否给过了
				continue;
			}
			// 标志是否给奖励
			$isPrize = false;
			// 获取奖励信息
			$prize = btstore_get()->PRIZE[$prizeID];
			/**********************************************************************************************************
 		 	 * 查看奖励种类, 如果是攻击次数奖励
 		 	 **********************************************************************************************************/
			if ($prize['type'] == CopyDef::DEFEATE_TIMES)
			{
				// 查看现在的击败次数 和 配置的次数对比，过了次数就可以奖励
				if ($prize['defeat_times'] <= $copyInfo['va_copy_info']['defeat_id_times'][$enemyID])
				{
					$isPrize = true;
				}
			}
			/**********************************************************************************************************
 		 	 * 如果是攻击评级奖励
 		 	 **********************************************************************************************************/
			else if ($prize['type'] == CopyDef::DEFEATE_APPRAISAL)
			{
				// 如果攻击评级 高于配置评级，那么就可以领取奖励
				if (BattleDef::$APPRAISAL[$atkRet['appraisal']] <= $prize['defeat_appraisal'])
				{
					$isPrize = true;
				}
			}
			/**********************************************************************************************************
 		 	 * 特殊奖励，算起来很麻烦
 		 	 **********************************************************************************************************/
			else if ($prize['type'] == CopyDef::DEFEATE_SPECIAL)
			{	
				// 如果需要查看战斗评价
				if (!empty($prize['sp_cons']['appraisal']) && $prize['sp_cons']['appraisal'] != 0)
				{
					// 不符合配置要求, 直接查看下一个奖励
					if (BattleDef::$APPRAISAL[$atkRet['appraisal']] > $prize['sp_cons']['appraisal'])
					{
						continue;
					}
				}
				// 如果设置了血量要求
				if (!empty($prize['sp_cons']['cost_hp']) && $prize['sp_cons']['cost_hp'] != 0)
				{
					// 初始计数为0
					$costHp = 0;
					$hp = 0;
					// 查看所有上阵英雄
					foreach ($atkRet['team1'] as $hero)
					{
						// 不为NPC的英雄
						if (HeroUtil::isHero($hero['hid']))
						{
							// 计算消耗血量和总血量
							$costHp += $hero['costHp'];
							$hp = $hp + $hero['costHp'] + $hero['hp'];
						}
					}
					// 消耗血量大于要求，那么直接查看下一条奖励
					if (($costHp / $hp) > ($prize['sp_cons']['cost_hp'] / CopyConf::LITTLE_WHITE_PERCENT))
					{
						continue;
					}
				}
				// 如果设置了上阵英雄数量要求
				if (!empty($prize['sp_cons']['fight_hero']) && $prize['sp_cons']['fight_hero'] != 0)
				{
					// 初始计数为0
					$heroNum = 0;
					// 获取上阵英雄数量
					foreach ($atkRet['team1'] as $hero)
					{
						// 不为NPC的英雄
						if (HeroUtil::isHero($hero['hid']))
						{
							++$heroNum;
						}
					}
					// 如果英雄数量大于要求，那么直接查看下一个奖励
					if ($heroNum > $prize['sp_cons']['fight_hero'])
					{
						continue;
					}
				}
				// 途经此地，就达到了所有的要求了
				$isPrize = true;
			}
			// 如果可以给奖励的话
			if ($isPrize)
			{
				// 记录返回值
				$prizeIDs[] = $prizeID;
				// 记录奖励
				$copyInst->addPrize($copyID, $prizeID);
				// 给增加奖励分数
				$fullScore = $copyInst->addCopyScore($copyID, $prize['score']);
				// 更新到数据库
				$copyInst->save($copyID);
				// 如果已经获取了所有奖励
				if ($fullScore)
				{
					// 达成成就
					EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::GET_ALL_COPY_PRIZE, $copyID);
				}
			}
		}
		// 返回奖励ID数组
		return $prizeIDs;
	}

	/**
	 * 领取奖励
	 * 
	 * @param int $copyID						副本ID
	 * @param int $caseID						宝箱ID
	 */
	public static function getPrize($copyID, $caseID)
	{
		// 背包信息，返回值
		$bagInfo = array();
		// 获取副本信息
		$copyInst = new MyCopy();
		$copyInfo = $copyInst->getCopyInfo($copyID);
		// 如果没有这个档位或者已经领取已有奖励或者所需分数大于已有分数
		if (!isset(CopyConf::$CASE_INDEX[$caseID]) || 
		    empty(btstore_get()->COPY[$copyID]['prize_scores'][$caseID]) ||
		    empty(btstore_get()->COPY[$copyID]['prize_ids'][$caseID]) ||
		    btstore_get()->COPY[$copyID]['prize_scores'][$caseID] > $copyInfo['score'] ||
		    ($copyInfo['prized_num'] & CopyConf::$CASE_INDEX[$caseID]))
		{
			// 防止连点，降低错误级别
			Logger::debug('Fetch prize case ID is %d, score is %d, prized_num is %d, can not fetch anymore.', 
			              $caseID, $copyInfo['score'], $copyInfo['prized_num']);
			return 'err';
		}

		// 获取用户背包信息
		$bag = BagManager::getInstance()->getBag();
		// 获取奖励信息
		$prizeIndex = 'prize_type_values_0'.($caseID + 1);
		$prizeList = btstore_get()->COPY[$copyID][$prizeIndex];
		// 分发所有奖励
		foreach ($prizeList as $prize)
		{
			// 先增加道具，如果背包满了，不做其他操作
			if ($prize[0] == 0)
			{
				// 获取道具信息
				$itemIndex = 'prize_items_0'.($caseID + 1);
				$itemList = btstore_get()->COPY[$copyID][$itemIndex];
                Logger::debug('Item list is %s.', $itemList);
				// 如果是空，就不给东西了 —— 防止表配错
				if (empty($itemList['id']))
				{
					continue;
				}
				// 生成物品
				$itemIDs = ItemManager::getInstance()->addItem($itemList['id'], $itemList['num']);
				// 记录发送的信息
				$msg = chatTemplate::prepareItem($itemIDs);
				// 直接增加到背包里，不使用临时背包
				if ($bag->addItems($itemIDs, FALSE) == FALSE)
				{
					Logger::warning('Bag full.');
					return 'err';
				}
				// 获取用户信息
				$user = EnUser::getUserObj();
				// 发送信息
				chatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
			}
		}
		// 统计发金币数量
		$gold = 0;
		// 分发其他所有奖励
		foreach ($prizeList as $prize)
		{
			// 根据类型进行不同操作
			switch ($prize[0])
			{
			case 1:									// 增加游戏币
				EnUser::getInstance()->addBelly($prize[1]);
				break;
			case 2:									// 增加金币
				EnUser::getInstance()->addGold($prize[1]);
				$gold += $prize[1];
				break;
			case 6:									// 增加阅历
				EnUser::getInstance()->addExperience($prize[1]);
				break;
			case 7:									// 增加声望
				EnUser::getInstance()->addPrestige($prize[1]);
				break;
			}
		}
		// 更新数据库
		$bagInfo = $bag->update();
		EnUser::getInstance()->update();
		// 发送金币通知
		if ($gold != 0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_COPY_GETPRIZE, $gold, Util::getTime(), FALSE);
		}
		// 增加领取次数
		$copyInst->addPirzedTimes($copyID, $caseID);
		// 更新数据库
		$copyInst->save($copyID);
		// 返回背包信息
		return $bagInfo;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
