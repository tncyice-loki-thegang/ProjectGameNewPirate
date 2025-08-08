<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GroupBattle.class.php 24200 2012-07-19 04:12:23Z YangLiu $
 *
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-99/module/copy/GroupBattle.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-07-19 12:12:23 +0800 (Thu, 19 Jul 2012) $
 * @version $Revision: 24200 $
 * @brief
 *
 **/

/**********************************************************************************************************************
 * Class       : GroupBattle
 * Description : 多人战实现类
 * Inherit     : CopyLogic
 **********************************************************************************************************************/
class GroupBattle extends CopyLogic
{
	private static $groupEnemyID;
	private static $uidList;

	/**
	 * 获取军团战部队的剩余次数
	 * 
	 * @param int $enemyType					军团类型
	 */
	public static function getGroupArmyDefeatNum($enemyType)
	{
		// 获取用户ID
		$uid = RPCContext::getInstance()->getUid();
		// 获取用户数据
		$groupBattleInfo = CopyDao::getGroupBattleInfo($uid);
		// 如果没有数据，那么初始化一条
		if ($groupBattleInfo === false)
		{
			// 得到一条空数据
			$groupBattleInfo = CopyDao::initGroupBattle($uid);
		}
		// 根据种类查看次数, 普通怪采取普通怪的方法获取次数
		if ($enemyType == CopyConf::NORMAL_ENEMY)
		{
			$times = self::getCurBattleTime($groupBattleInfo, $enemyType, 0);
		}
		// 活动怪调用活动怪的方法获取次数
		else 
		{
			$times = self::__getActivityBattleTimes($groupBattleInfo);
		}
		return $times;
	}

	/**
	 * 获取活动副本的次数
	 * 
	 * @param array $groupBattleInfo			用户军团战信息	
	 */
	private static function __getActivityBattleTimes($groupBattleInfo)
	{
		// 获取当前时刻
		$curTime = Util::getTime();
		// 次数返回值
		$times = 0;
		// 设置标志位
		$inTime = false;
		
		// 初始化次数
		$times = $groupBattleInfo['va_copy_info']['copy_times'];
		// 获取当日日期
		$curYmd = date("Y-m-d ", $curTime);
		// 获取当日活动时刻
		foreach (CopyConf::$GROUP_RESET as $rtime)
		{
			// 获取这次活动开始时刻
			$startTime = $curYmd.$rtime;
			$startTime = strtotime($startTime);
			// 获取这次活动结束时刻
			$endTime = $startTime + CopyConf::RESET_LAST_TIME;
			Logger::debug('Cur time is %d, start time is %d, end time is %d.', $curTime, $startTime, $endTime);
			// 判断当前时刻是否在活动中 
			if ($curTime >= $startTime && $curTime <= $endTime)
		    {
				// 标志在活动中
				$inTime = true;
		    	// 如果当前正在活动中，并且上次攻打时刻是活动之前，那么刷新一下
		    	if ($groupBattleInfo['activity_last_time'] < $startTime)
		    	{
					// 整理次数
					$groupBattleInfo['activity_last_time'] = $curTime;
					// 遍历所有部队
					foreach ($groupBattleInfo['va_copy_info']['copy_times'] as $groupEnemyID => $times)
					{
						// 重置次数
						$groupBattleInfo['va_copy_info']['copy_times'][$groupEnemyID] = CopyConf::DAY_ACTIVITY_TIMES;
					}
					// 更新数据库
					CopyDao::updateGroupBattle($groupBattleInfo['uid'], $groupBattleInfo);
					// 直接返回
					$times = $groupBattleInfo['va_copy_info']['copy_times'];
					break;
		    	}
		    }
		}
		// 返回负数，不在活动中的时刻不能进行攻击行为
		if (!$inTime)
		{
			Logger::debug('Not in act time.');
			return -1;
		}

		return $times;
	}

	/**
	 * 获取军团战次数
	 * 
	 * @param array $groupBattleInfo			用户军团战信息
	 * @param int $enemyType					军团类型
	 */
	private static function getCurBattleTime($groupBattleInfo, $enemyType, $enemyID)
	{
		// 获取当前时刻
		$curTime = Util::getTime();
		// 次数返回值
		$times = 0;
		// 设置标志位
		$inTime = false;
		// 判断敌人类型, 如果是普通怪，那么看天刷新就行
		if ($enemyType == CopyConf::NORMAL_ENEMY)
		{
			// 初始化次数
			$times = $groupBattleInfo['normal_times'];
			// 如果是今天以前，那么刷新次数
			if (!Util::isSameDay($groupBattleInfo['normal_last_time'], CopyConf::REFRESH_TIME))
			{
				// 整理次数
				$groupBattleInfo['normal_last_time'] = $curTime;
				$groupBattleInfo['normal_times'] = CopyConf::DAY_GROUP_TIMES;
				// 更新数据库
				CopyDao::updateGroupBattle($groupBattleInfo['uid'], $groupBattleInfo);
				// 直接返回
				$times = CopyConf::DAY_GROUP_TIMES;
			}
		}
		// 如果是活动怪
		else 
		{
			// 如果参数不正确，则直接返回
			if (empty($enemyID))
			{
				Logger::debug('Wrong para, enemy id is %d.', $enemyID);
				return -1;
			}
			// 如果没设置过这个值，那么在这里修复数据
			if (!isset($groupBattleInfo['va_copy_info']['copy_times'][$enemyID]) && self::canAttackGroupEnemy($enemyID))
			{
				// 一切顺利，给初始化次数
				$groupBattleInfo['va_copy_info']['copy_times'][$enemyID] = CopyConf::DAY_ACTIVITY_TIMES;
			}
			// 获取次数
			$times = self::__getActivityBattleTimes($groupBattleInfo);
		}
		Logger::debug('Battle times is %d, enemy type is %d.', $times, $enemyType);
		return $times;
	}

	/**
	 * 判断是否可以攻击某军团
	 * 
	 * @param int $enemyID						军团ID
	 * @throws Exception
	 */
	private static function canAttackGroupEnemy($enemyID)
	{
		// 获取怪物所在的副本ID
		$copyID = btstore_get()->GROUP_ARMY[$enemyID]['copy_id'];
		// 根据副本ID获取副本信息
		$copyInst = new MyCopy();
		$copy = $copyInst->getCopyInfo($copyID);
		// 如果没获取到副本信息
		if ($copy === false)
		{
			Logger::fatal('Can not get copy info, enemy ID is %d', $enemyID);
			throw new Exception('fake');
		}
		// 获取此人的此副本进度
		$defeat = $copy['va_copy_info']['defeat_id_times'];
		// 是否能攻击这个怪
		if (!parent::alreadyCanAtt($enemyID, true, $defeat))
		{
			// 记录日志
			Logger::fatal('Still have enemy not defeat yet. enemyID is %d.', $enemyID);
			// 尚不能攻击此怪物，则直接返回错误
			throw new Exception('fake');
		}
		return true;
	}

	/**
	 * 创建队伍
	 * @param int $enemyID						部队ID
	 * @param bool $isAutoStart					是否自动开战
	 * @param int $joinLimit					组队限制 （公会还是阵营）
	 */
	public static function createTeam($enemyID, $isAutoStart, $joinLimit)
	{
		/**************************************************************************************************************
 		 * 创建队伍检查
 		 **************************************************************************************************************/
		// 获取用户ID
		$uid = RPCContext::getInstance()->getUid();
		if (empty($uid))
		{
			Logger::fatal('Can not get uid from session!');
			throw new Exception('fake');
		}
		// 进行行动力和CD时间判断
		if (!parent::checkExecution($enemyID, $uid, true) || !parent::checkCdTime($uid))
		{
			// 没通过检查，则直接返回错误
			return 'err';
		}
		// 检查是否可以攻击该军团怪
		self::canAttackGroupEnemy($enemyID);

		/**************************************************************************************************************
 		 * 检查玩家攻击次数
 		 **************************************************************************************************************/
		// 获取用户数据
		$groupBattleInfo = CopyDao::getGroupBattleInfo($uid);
		// 如果没有数据，那么初始化一条
		if ($groupBattleInfo === false)
		{
			// 得到一条空数据
			$groupBattleInfo = CopyDao::initGroupBattle($uid);
		}
		// 获取军团怪种类
		$enemyType = btstore_get()->GROUP_ARMY[$enemyID]['type'];
		// 根据种类查看次数
		$times = self::getCurBattleTime($groupBattleInfo, $enemyType, $enemyID);
		// 如果没有次数了，那么就不能创建
		if ($times <= 0)
		{
			// 记录日志, 返回不能攻击
			Logger::warning('Defeat count is 0, now defeat time is %d.', $times);
			return 'err';
		}

		/**************************************************************************************************************
 		 * 创建队伍时还需检测创建者的血量是否已满（不满先自动补满），是否空阵（必须要有一名英雄）。如不满足情况，需进行相应提示。
 		 **************************************************************************************************************/
		// 获取此用户的默认阵型
		$userFormation = EnFormation::getFormationInfo($uid);
		// 检查此阵型
		$ret = EnFormation::checkUserFormation($uid, $userFormation);
		if ($ret != 'ok')
		{
			if ($ret == 'not_enough_hp')
			{
				Logger::warning('Hp not enough.');
				return 'hp';
			}
			// 血不满的时候，不能加入队伍
			return 'err';
		}
		// 将补血的事情告知数据库
		EnUser::getUserObj()->update();
		// 完成所有检查，可以创建队伍
		RPCContext::getInstance()->createTeam($isAutoStart, $joinLimit);
		RPCContext::getInstance()->getFramework()->resetCallback();

		// 返回当前血包数量
		RPCContext::getInstance()->sendMsg(array($uid), 
		                                   're.copy.getCurrentBloodbag', 
		                                   array(EnUser::getUserObj()->getBloodPackage()));
		return 'ok';
	}

	/**
	 * 加入队伍
	 * @param int $enemyID						部队ID
	 * @param bool $teamId						创建好的小队ID
	 */
	public static function joinTeam($enemyID, $teamId)
	{
		/**************************************************************************************************************
 		 * 加入队伍检查
 		 **************************************************************************************************************/
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		if (empty($uid))
		{
			Logger::fatal('Can not get uid from session!');
			throw new Exception('fake');
		}
		// 进行行动力和CD时间判断
		if (!parent::checkExecution($enemyID, $uid, true) || !parent::checkCdTime($uid))
		{
			// 没通过检查，则直接返回错误
			return 'err';
		}
		// 检查是否可以攻击该军团怪
		self::canAttackGroupEnemy($enemyID);

		/**************************************************************************************************************
 		 * 检查玩家攻击次数
 		 **************************************************************************************************************/
		// 获取用户数据
		$groupBattleInfo = CopyDao::getGroupBattleInfo($uid);
		// 如果没有数据，那么初始化一条
		if ($groupBattleInfo === false)
		{
			// 得到一条空数据
			$groupBattleInfo = CopyDao::initGroupBattle($uid);
		}
		// 获取军团怪种类
		$enemyType = btstore_get()->GROUP_ARMY[$enemyID]['type'];
		// 根据种类查看次数
		$times = self::getCurBattleTime($groupBattleInfo, $enemyType, $enemyID);
		// 如果没有次数了，那么就不能创建
		if ($times <= 0)
		{
			// 记录日志, 返回不能攻击
			Logger::warning('Defeat count is 0, now defeat time is %d.', $times);
			return 'err';
		}

		/**************************************************************************************************************
 		 * 该玩家此刻阵型上的英雄血量必须全满且不能空阵必须要有一名英雄，否则先用血池补血，不满不能加入。
 		 **************************************************************************************************************/
		// 获取此用户的默认阵型
		$userFormation = EnFormation::getFormationInfo($uid);
		// 检查此阵型
		$ret = EnFormation::checkUserFormation($uid, $userFormation);
		if ($ret != 'ok')
		{
			if ($ret == 'not_enough_hp')
			{
				Logger::warning('Hp not enough.');
				return 'hp';
			}
			// 血不满的时候，不能加入队伍
			return 'err';
		}
		// 将补血的事情告知数据库
		EnUser::getUserObj()->update();
		// 完成所有检查，可以加入队伍
		RPCContext::getInstance()->joinTeam($teamId);
		RPCContext::getInstance()->getFramework()->resetCallback();

		// 返回当前血包数量
		RPCContext::getInstance()->sendMsg(array($uid), 
		                                   're.copy.getCurrentBloodbag', 
		                                   array(EnUser::getUserObj()->getBloodPackage()));
		return 'ok';
	}

	/**
	 * 攻击一个部队
	 * 
	 * @param int $enemyID						敌人ID
	 * @param array $teamList					玩家组队uid的数组
	 * @throws Exception
	 */
	public static function groupAttack($enemyID, $teamList)
	{
//		// 异步执行   2012/07/16 异步不好用 —— 反而占用了更多服务器资源，改为同步
//		Util::asyncExecute('copy.doGroupAttack', array($enemyID, $teamList));
		self::doGroupAttack($enemyID, $teamList);
	}
	public static function doGroupAttack($enemyID, $teamList)
	{
		/**************************************************************************************************************
 		 * 获取组队所有人的阵型信息
 		 **************************************************************************************************************/
		// 获取队长信息
		$userInfo = EnUser::getUser($teamList[0]);
		// 设定返回值
		$userFormations = array();
		$userFormations['name'] = $userInfo['uname'].'的小队';
		$userFormations['level'] = $userInfo['level'];
		// 循环查看小队情况
		foreach ($teamList as $uid)
		{
			// 获取用户信息
			$user = EnUser::getUserObj($uid);
			// 准备缓存数据
			$user->prepareItem4CurFormation();
			// 获取名称,等级和默认阵型信息
			$userFormations['members'][] = array('name' => $user->getUname(), 
		                            			 'level' => $user->getLevel(),
		                            			 'isPlayer' => true,
		                            			 'flag' => 0,
		                            			 'formation' => $user->getCurFormation(),
		                            			 'uid' => $uid,
		                            			 'arrHero' => EnFormation::changeForObjToInfo(EnFormation::getFormationInfo($uid)));
		}

		/**************************************************************************************************************
 		 * 获取军团的怪物小队组
 		 **************************************************************************************************************/
		$enemyFormations = array();
		$enemyFormations['name'] = btstore_get()->GROUP_ARMY[$enemyID]['name'];
		$enemyFormations['level'] = btstore_get()->GROUP_ARMY[$enemyID]['lv'];
		// 获取怪物小队信息
		$enemyTeamIDs = btstore_get()->GROUP_ARMY[$enemyID]['monster_list_ids'];
		foreach ($enemyTeamIDs as $teamID)
		{
			// 敌人信息
			$enemyFormations['members'][] = array('name' => btstore_get()->TEAM[$teamID]['display_name'], 
		                            			  'level' => btstore_get()->TEAM[$teamID]['display_lv'],
		                            			  'isPlayer' => false,
		                            			  'flag' => 0,
		                            			  'formation' => btstore_get()->TEAM[$teamID]['fid'],
		                            			  'uid' => $teamID,
		                            			  'arrHero' => EnFormation::changeForObjToInfo(EnFormation::getBossFormationInfo($teamID)));
		}
		// 记录参数
		self::$groupEnemyID = $enemyID;
		self::$uidList = $teamList;

		/**************************************************************************************************************
 		 * 调用战斗模块
 		 **************************************************************************************************************/
		$bt = new Battle();
		$atkRet = $bt->doMultiHero($userFormations,
		                           $enemyFormations, 
		                           btstore_get()->GROUP_ARMY[$enemyID]['max_win_times'],
		                           BattleConf::MAX_ARENA_COUNT,
		                           array('arrEndCondition' => 0,
		                                 'mainBgid' => CopyConf::BACK_GROUND_M,
		                                 'subBgid' => CopyConf::BACK_GROUND_S,
		                                 'mainMusicId' => CopyConf::MUSIC_ID_M,
		                                 'subMusicId' => CopyConf::MUSIC_ID_S,
		                                 'mainCallback' => null,
		                                 'subCallback' => null,
		                                 'mainType' => BattleType::GUILD_SINGLE,
		                                 'subType' => BattleType::TEAM));
		// 计算战斗结果
		$rewards = self::calculateGroupFightRet($atkRet['server']['result'], $atkRet['server']['battleInfo']);
		// 分别给每个人送消息
		foreach ($rewards as $uid => $reward)
		{
			// 插入用户获得好处的信息
			$atkRet['reward'] = $reward;
			// 给前端发布消息
			RPCContext::getInstance()->sendMsg(array($uid), 'team.reBattleResult', $atkRet);
		}
	}

	/**
	 * 计算战斗结果 
	 * 有些工作需要在这里做 —— 譬如掉落道具，增加经验之类
	 * 
	 * @param string $isWin						战斗胜负
	 * 
	 * @return 
	 * <code>
	 * {[
	 * uid:{
	 * arrHero:[{
	 * hid:英雄id
	 * htid:形象id
	 * initial_level:等级初量
	 * current_level:等级终量
	 * current_exp:经验终量
	 * add_exp:经验增量
	 * }]
	 * prestige:获得的威望
	 * exp:经验
	 * expericne:阅历
	 * belly:游戏币
	 * curHp:[
	 * 英雄的当前血量
	 * ]
	 * bloodPackage：剩余血包数
	 * equip:{
	 * item:[全部掉落的itemID]
	 * bag：{背包信息}
	 * }
	 * }
	 * ]}
	 * </code>
	 */
	public static function calculateGroupFightRet($isWin, $afterFightList)
	{

		/**************************************************************************************************************
 		 * 增加服务器击败次数
 		 **************************************************************************************************************/
		// 查看队长的信息，是否需要加一次部队次数
		// 获取怪物所在的副本ID
		$copyID = btstore_get()->GROUP_ARMY[self::$groupEnemyID]['copy_id'];
		// 获取副本信息
		$LeaderCopyInfo = CopyDao::getUserCopy(self::$uidList[0], $copyID);
		// 如果已经打过这个敌人，那么就需要增加服务器次数
		if (isset($LeaderCopyInfo['va_copy_info']['defeat_id_times'][self::$groupEnemyID]))
		{
			// 获取用户阵营
			$leaderInfo = EnUser::getUser(self::$uidList[0]);
			// 增加一次服务器攻击次数
		 	parent::addServerKillNum(self::$groupEnemyID, $leaderInfo['group_id']);
		}

		/**************************************************************************************************************
 		 * 修改用户数据
 		 **************************************************************************************************************/
		// 详细的奖励值
		$itemIDs = array();
		$belly = 0;
		$exp_tmp = 0;
		$experience_tmp = 0;
		$prestige = 0;
		// 壮烈殉国的场合， 稍微给点经验意思一下就得了
		if (!$isWin)
		{
			$exp_tmp = btstore_get()->GROUP_ARMY[self::$groupEnemyID]['lose_exp'];
		}
		// 哟~不容易啊，还赢了
		else 
		{
			// 在这里通知任务系统
			EnDaytask::beatSuccess();
			// 在这里掉落物品
			$itemIDs = self::dropGroupItems();
			// 获胜奖励游戏币
			$belly = btstore_get()->GROUP_ARMY[self::$groupEnemyID]['init_belly'];
			// 获胜奖励阅历
			$experience_tmp = btstore_get()->GROUP_ARMY[self::$groupEnemyID]['init_experience'];
			// 获胜奖励威望
			$prestige = btstore_get()->GROUP_ARMY[self::$groupEnemyID]['init_prestige'];
			// 获胜奖励经验
			$exp_tmp = btstore_get()->GROUP_ARMY[self::$groupEnemyID]['init_exp'];
		}

		/**************************************************************************************************************
 		 * 奖励各种货
 		 **************************************************************************************************************/
		$reward = array();
		// 给每个人奖励
		for ($index = 0; $index < count(self::$uidList); ++$index)
		{
			/**********************************************************************************************************
	 		 * 获取用户信息，增加副本击败记录, 并添加副本奖励
	 		 **********************************************************************************************************/
			// 获取用户ID
			$uid = self::$uidList[$index];
			// 获取用户类实例
			$user = EnUser::getUserObj($uid);
			// 只有在胜利的时候才在 副本中加入击败记录，并计算 副本奖励
			if ($isWin)
			{
				RPCContext::getInstance()->executeTask($uid, 
				                                       'copy.addGroupEnemyDefeatInfo',
				                                       array($uid, $copyID, self::$groupEnemyID));
			}

			/**********************************************************************************************************
	 		 *  减去所有人的血量
	 		 **********************************************************************************************************/
			// 战斗后英雄的血量 
			$heroArr = array();
			if (isset($afterFightList[$uid]))
			{
				// 如果设置过血量
				$heroArr = EnFormation::subUserHeroHp($afterFightList[$uid], $uid);
			}

			/**********************************************************************************************************
	 		 * 战斗结束后，扣除行动力 和 增加杀敌CD时间
	 		 **********************************************************************************************************/
			// 增加杀敌CD时间
			$user->addFightCDTime(btstore_get()->GROUP_ARMY[self::$groupEnemyID]['cd_time']);
			// 只有在胜利的时候才扣除行动力
			if ($isWin)
			{
				// 扣除行动力
				$user->subExecution(intval(btstore_get()->GROUP_ARMY[self::$groupEnemyID]['need_execution']));
			}

			/**********************************************************************************************************
	 		 * 给用户奖励
	 		 **********************************************************************************************************/
			// 如果是队长，那么给点儿额外奖励
			if ($index == 0)
			{
				// 获取公会科技
				$guild = GuildLogic::getBuffer($uid);
				// 额外奖励经验 = 初始经验*（1+公会经验科技百分比*科技等级）*（1+队长经验加成）
				$exp = floor($exp_tmp * 
				             (1 + $guild['battleExpAddition'] / CopyConf::LITTLE_WHITE_PERCENT) *
				             (1 + btstore_get()->GROUP_ARMY[self::$groupEnemyID]['captain_exp'] / CopyConf::LITTLE_WHITE_PERCENT));
				// 额外奖励阅历 = 战斗获得阅历=初始阅历*（1+公会阅历科技百分比*科技等级）*（1+队长阅历加成）
				$experience = floor($experience_tmp *
				        (1 + $guild['battleExperienceAddition'] / CopyConf::LITTLE_WHITE_PERCENT) *
				        (1 + btstore_get()->GROUP_ARMY[self::$groupEnemyID]['captain_experience'] / CopyConf::LITTLE_WHITE_PERCENT));
			}
			// 不是队长就没那么美的事儿了
			else 
			{
				// 获取公会科技
				$guild = GuildLogic::getBuffer($uid);
				// 只能获取基础值
				$exp = floor($exp_tmp  * (1 + $guild['battleExpAddition'] / CopyConf::LITTLE_WHITE_PERCENT));
				$experience = floor($experience_tmp * (1 + $guild['battleExperienceAddition'] / CopyConf::LITTLE_WHITE_PERCENT));
			}
			Logger::debug('Uid is %d, BattleExpAddition is %d, BattleExperienceAddition is %d.', 
			              $uid, $guild['battleExpAddition'], $guild['battleExperienceAddition']);
			// 获胜奖励游戏币
			$user->addBelly($belly);
			// 获胜奖励阅历
			$user->addExperience($experience);
			// 获胜奖励威望
			$user->addPrestige($prestige);

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
			// 添加所有英雄的经验
			if (isset($afterFightList[$uid]))
			{
				foreach ($afterFightList[$uid] as $hero)
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
			}
			// 保存用户数据至数据库
			$user->update();

			// 准备掉落物品返回值
			$equip = isset($itemIDs[self::$uidList[$index]]) ? $itemIDs[self::$uidList[$index]] : array('item' => array(), 'bag' => array());
			// 返回给前端
			$reward[self::$uidList[$index]] = array('arrHero' => $heroList, 'belly' => $belly, 'exp' => $exp,
	 	                                            'curHp' => $heroArr, 'bloodPackage' => $user->getBloodPackage(),
	 	                                            'experience' => $experience, 'prestige' => $prestige,
			                                        'equip' => $equip);
		}

		// 返回奖励内容
	 	return $reward;
	}

	/**
	 * 达成副本奖励
	 * 
	 * @param int $uid							用户ID
	 * @param int $copyID						副本ID
	 * @param int $enemyID						敌人ID
	 */
	public static function addGroupEnemyDefeatInfo($uid, $copyID, $enemyID)
	{
		/**************************************************************************************************************
 		 * 更新用户杀敌信息, 并得到副本信息
 		 **************************************************************************************************************/
		// 增加次数，并获取副本信息
		$copyInfo = self::addOtherUserKillNum($uid, $copyID, $enemyID);

		/**************************************************************************************************************
 		 * 依次处理奖励
 		 **************************************************************************************************************/
		// 设置了奖励，遍历奖励ID组
		if (!empty(btstore_get()->PRIZE['enemy'][$enemyID]))
		{
			foreach (btstore_get()->PRIZE['enemy'][$enemyID] as $prizeID)
			{
				// 先检查这个奖励是否已经实现过了
				if (isset($copyInfo['va_copy_info']['prize_ids'][$prizeID]) && 
				    $copyInfo['va_copy_info']['prize_ids'][$prizeID] === true)
				{
					// 给过奖励了，就不再次给了，直接看下一条奖励是否给过了
					continue;
				}
				// 获取奖励信息
				$prize = btstore_get()->PRIZE[$prizeID];
				/******************************************************************************************************
	 		 	 * 查看奖励种类, 如果是攻击次数奖励
	 		 	 ******************************************************************************************************/
				if ($prize['type'] == CopyDef::DEFEATE_TIMES)
				{
					// 查看现在的击败次数 和 配置的次数对比，过了次数就可以奖励
					if ($prize['defeat_times'] <= $copyInfo['va_copy_info']['defeat_id_times'][$enemyID])
					{
						// 记录奖励
						$copyInfo['va_copy_info']['prize_ids'][$prizeID] = true;
						// 给增加奖励分数
						$copyInfo['score'] += $prize['score'];
						// 检查是否达成成就
						if (!empty(btstore_get()->COPY[$copyID]['prize_scores']) &&
			    			$copyInfo['score'] >= btstore_get()->COPY[$copyID]['prize_scores'][count(btstore_get()->COPY[$copyID]['prize_scores']) - 1])
						{
							// 达成成就
							EnAchievements::notify($uid, AchievementsDef::GET_ALL_COPY_PRIZE, $copyID);
						}
						// 通知前端，获取了副本奖励
						RPCContext::getInstance()->sendMsg(array($uid), 
						                                  'copy.copyTeamPrize',
						                                   array('copyID' => $copyID, 'prizeID' => $prizeID));
					}
				}
			}
		}
		// 更新数据库
		CopyDao::updateCopyInfo($copyInfo);

		// 通知任务系统， 怪物小队和战斗结果
		TaskNotify::beatArmy($enemyID, BattleDef::$APPRAISAL['S']);

		/**************************************************************************************************************
 		 * 查看用户是否在线,如果在线，需要更新 session 信息
 		 **************************************************************************************************************/
		if (RPCContext::getInstance()->getSession('global.uid') != null)
		{
			// 先清掉，然后取新的
			RPCContext::getInstance()->unsetSession('copy.copyList');
			// 异步请求之前，需要先清空缓存
			CopyDao::clearBuffer();
			// 生成对象的时候，会取最新数据并更新session的
			$copyInst = new MyCopy();
		}
	}

	/**
	 * 更新用户的杀敌信息
	 * 
	 * @param int $uid							用户ID
	 * @param int $copyID						副本ID
	 * @param int $enemyID						敌人ID
	 * @throws Exception
	 */
	private static function addOtherUserKillNum($uid, $copyID, $enemyID)
	{
		/**************************************************************************************************************
 		 * 更新军团怪数据库中的杀敌次数和时刻
 		 **************************************************************************************************************/
		self::addGroupBattleTimes($uid, $enemyID);

		/**************************************************************************************************************
 		 * 更新用户数据库信息
 		 **************************************************************************************************************/
		// 获取副本信息
		$copyInfo = CopyDao::getUserCopy($uid, $copyID);
		// 如果副本信息为空 —— 一般不会出现这种清空，因为组队前已经检查过了
		if ($copyInfo === false)
		{
			Logger::fatal('Can not get copy info, uid is %d, enemy ID is %d', $uid, $enemyID);
			throw new Exception('fake');
		}
		// 加算杀怪信息
		if (isset($copyInfo['va_copy_info']['defeat_id_times'][$enemyID]))
		{
			++$copyInfo['va_copy_info']['defeat_id_times'][$enemyID];
		}
		else 
		{
			$copyInfo['va_copy_info']['defeat_id_times'][$enemyID] = 1;
		}
		// 记录日志
		Logger::debug('The total number defeat No.%d army is %d.', 
		              $enemyID, $copyInfo['va_copy_info']['defeat_id_times'][$enemyID]);

		// 返回上层继续处理
		return $copyInfo;
	}

	/**
	 * 记录军团怪的杀敌时刻和次数
	 * 
	 * @param int $uid							用户ID
	 * @param int $enemyID						敌人ID
	 */
	private static function addGroupBattleTimes($uid, $enemyID)
	{
		// 获取军团怪种类
		$enemyType = btstore_get()->GROUP_ARMY[$enemyID]['type'];
		// 获取用户数据
		$groupBattleInfo = CopyDao::getGroupBattleInfo($uid);

		// 判断敌人类型, 如果是普通怪, 那么减去普通怪次数
		if ($enemyType == CopyConf::NORMAL_ENEMY)
		{
			// 减去普通次数
			$groupBattleInfo['normal_last_time'] = Util::getTime();
			// 小于0 就不减了吧
			if ($groupBattleInfo['normal_times'] > 0)
			{
				--$groupBattleInfo['normal_times'];
			}
			else 
			{
				Logger::warning('Warning, Normal times less then 0! %d.', 
				                $groupBattleInfo['normal_times']);
			}
			// 达成成就
			EnAchievements::notify($uid, AchievementsDef::TEAM_BATTLE_TIMES, 1);
		}
		// 如果是活动怪, 减去活动怪次数
		else 
		{
			// 减去活动次数
			$groupBattleInfo['activity_last_time'] = Util::getTime();
			// 小于0 就不减了吧
			if ($groupBattleInfo['va_copy_info']['copy_times'][$enemyID] > 0)
			{
				--$groupBattleInfo['va_copy_info']['copy_times'][$enemyID];
			}
			else 
			{
				Logger::warning('Warning, Act times less then 0! %d.', 
				                $groupBattleInfo['va_copy_info']['copy_times'][$enemyID]);
			}
			// 达成成就
			EnAchievements::notify($uid, AchievementsDef::ACT_GROUP_ATK_TIMES, 1);
		}
		// 更新数据库
		CopyDao::updateGroupBattle($uid, $groupBattleInfo);
	}

	/**
	 * 在回调函数中，战斗结束后掉落道具
	 */
	private static function dropGroupItems()
	{
		/**************************************************************************************************************
 		 * 掉落道具
 		 **************************************************************************************************************/
		// 掉落普通道具
		$dropIDs = btstore_get()->GROUP_ARMY[self::$groupEnemyID]['drop_ids'];

		// 声明背包信息返回值
		$bagInfo = array();
		if (!empty($dropIDs) && !empty($dropIDs[0]))
		{
			// 人人为我，我为人人。恩，见者有份啊！
			foreach (self::$uidList as $uid)
			{
				// 获取用户信息
				$user = EnUser::getUserObj($uid);
				// 掉落道具, 放到背包里
				$bag = BagManager::getInstance()->getBag($uid);
				// 需要返回给前端的所有掉落物品详细信息
				$itemArr = array();
				// 循环处理所有掉落表ID
				foreach ($dropIDs as $dropID)
				{
					// 掉落物品
					$itemIDs = ItemManager::getInstance()->dropItem($dropID);
					// 记录发送的信息
					$msg = chatTemplate::prepareItem($itemIDs);
					// 循环处理所有的掉落物品
					foreach ($itemIDs as $itemID)
					{
						// 先获取数据信息，保存。
						$itemTmp = ItemManager::getInstance()->itemInfo($itemID);
						// 塞一个货到背包里，可以使用临时背包
						if ($bag->addItem($itemID, TRUE) == FALSE)
						{
							// 如果连临时背包都满了的话， 删除该物品
							ItemManager::getInstance()->deleteItem($itemID);
						}
						else
						{
							// 保留物品详细信息，传给前端
							$itemArr[] = $itemTmp;
						}
					}
					// 发送信息
					chatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
				}
				// 获取调用的结果，背包里面掉落出来了什么东西
				$bagInfo[$uid]['bag'] = $bag->update();
				$bagInfo[$uid]['item'] = $itemArr;
			}
		}
		// 返回已经掉落的各种IDs
		return $bagInfo;
	}

	

	// 海战 TODO
	public static function navalAttack($enemyID)
	{
		/**************************************************************************************************************
 		 * 查看是否可以攻击
 		 **************************************************************************************************************/
		// 检查部队类型
		$armyType = btstore_get()->ARMY[$enemyID]['army_type'];
		// 如果不是普通类型的战斗，则出错退出
		if ($armyType != CopyConf::ARMY_TYPE_SEA)
		{
			Logger::fatal('Error interface used! The enemy type is %d.', $armyType);
			throw new Exception('fake');
		}
		//
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */