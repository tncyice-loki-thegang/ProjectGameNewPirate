<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GroupBattleBase.class.php 37720 2013-01-31 05:38:14Z YangLiu $
 *
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/GroupBattleBase.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-31 13:38:14 +0800 (四, 2013-01-31) $
 * @version $Revision: 37720 $
 * @brief
 *
 **/

/**********************************************************************************************************************
 * Class       : GroupBattleBase
 * Description : 活动多人战基类
 * Inherit     : CopyLogic
 **********************************************************************************************************************/
class GroupBattleBase extends CopyLogic
{
	/**
	 * 获取活动军团战部队的剩余次数
	 */
	public static function getActivityGroupArmyDefeatNum()
	{
		// 获取用户数据
		$groupBattleInfo = self::__getCurUserGroupBattleInfo();
		// 返回最新次数
		return self::__getActivityBattleTimes($groupBattleInfo);
	}

	/**
	 * 获取普通军团战部队的剩余次数
	 */
	public static function getCommonGroupArmyDefeatNum()
	{
		// 获取用户数据
		$groupBattleInfo = self::__getCurUserGroupBattleInfo();
		// 返回最新次数
		return self::__getCommonBattleTimes($groupBattleInfo);
	}

	/**
	 * 开战 —— 开战的实现，需要在子类具体具现出真正的战斗操作
	 *
	 * @param int $enemyID						敌人ID
	 * @param array $teamList					玩家组队uid的数组
	 * @throws Exception
	 */
	public static function doAttack($enemyID, $teamList, $cache = false)
	{
		/**************************************************************************************************************
 		 * 获取组队所有人的阵型信息
 		 **************************************************************************************************************/
		// 获取队长信息
		$userInfo = EnUser::getUser($teamList[0]);
		// 设定返回值
		$userFormations = array();
		$userFormations['name'] = $userInfo['uname'].CopyDef::DE_XIAO_DUI;
		$userFormations['level'] = $userInfo['level'];
		// 获取最大最小人数限制
		$maxNum = btstore_get()->GROUP_ARMY[$enemyID]['max_join_num'];
		$minNum = btstore_get()->GROUP_ARMY[$enemyID]['least_join_num'];
		$teamNum = count($teamList);
		// 检查人数
		if ($teamNum > $maxNum || $teamNum < $minNum)
		{
			Logger::fatal('Team num err, now is %d, Need max is %d, min is %d.', $teamNum, $maxNum, $minNum);
			throw new Exception('fake');
		}
		// 循环查看小队情况
		foreach ($teamList as $uid)
		{
			// 获取用户信息
			$user = EnUser::getUserObj($uid);
			// 默认需要从DB中间拉取战斗模块需要的参数
			$fetchDb = true;
			// 如果需要缓存（亲友团模式） ，那么需要访问memcache
			if($cache)
			{
				// 生成一个在memcache保存所需要的key
				$key = "group.battle.$uid";
				// 先使用这个key去获取一下，如果能获取到就使用这个值进行战斗
				$battleInfo = McClient::get($key);
				// 如果已经拉取出来了，那么就不必再访问数据库了
				if(!empty($battleInfo))
				{
					$fetchDb = false;
				}
			}

			// 如果没有获取到，则需要临时拉取
			if($fetchDb)
			{
				// 准备缓存数据
				$user->prepareItem4CurFormation();
				// 获取名称,等级和默认阵型信息
				$battleInfo = array('name' => $user->getUname(),
		                            			 'level' => $user->getLevel(),
		                            			 'isPlayer' => true,
		                            			 'flag' => 0,
		                            			 'formation' => $user->getCurFormation(),
		                            			 'uid' => $uid,
		                            			 'arrHero' => EnFormation::changeForObjToInfo(EnFormation::getFormationInfo($uid)));
				// 如果是亲友团模式，需要把获取到的东西设置到memcache里面
				if($cache)
				{
					McClient::set($key, $battleInfo, CopyConf::GROUP_BATTLE_EXPIRE_TIME);
				}
			}
			// 这只战斗模块的参数
			$userFormations['members'][] = $battleInfo;
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

		Logger::debug("doMultiHero return %s.", $atkRet);
		// 返回战斗结果 —— 这里不执行任何操作，仅仅在子类执行战斗后的操作
		return $atkRet;
	}

	/******************************************************************************************************************
 	 * 以下为需要子类进行实现的函数
 	 ******************************************************************************************************************/
	/**
	 * 计算战斗结果
	 * 有些工作需要在这里做 —— 譬如掉落道具，增加经验之类
	 *
	 * @param int $enemyID						敌人军团ID
	 * @param array $atkRet						战斗模块返回值
	 * @param array $teamList					uid列表
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
	 *
	 * @throws Exception
	 */
	protected static function calculateGroupFightRet($enemyID, $atkRet, $teamList)
	{
		// 留给子类实现
		throw new Exception('sys');
	}

	// 单例实例 —— static 方法是不应该被继承的。而且没办法被用来实现多态
	// php在这里提供了解决方法，可以使用static:: 这种方法来解决这个问题
	// 但是这种解决方法很畸形，而且编辑器会认为这里有个错误。
	/**
	 * 其他子类特有的检查
	 *
	 * @throws Exception
	 */
	protected static function otherCheck($list)
	{
		// 留给子类实现
		throw new Exception('sys');
	}

	/******************************************************************************************************************
 	 * 以下为各种内部函数
 	 ******************************************************************************************************************/
	/**
	 * 返回当前人物的活动组队战信息
	 */
	protected static function __getCurUserGroupBattleInfo()
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
		// 返回
		return $groupBattleInfo;
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
			Logger::warning('Can not get copy info, enemy ID is %d', $enemyID);
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
	 * 战斗之前需要进行的检查
	 *
	 * @param int $enemyID						部队ID
	 */
	protected static function beforeAttackCheck($enemyID)
	{
		/**************************************************************************************************************
 		 * 创建/加入 队伍检查
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
		$groupBattleInfo = self::__getCurUserGroupBattleInfo();
		// 根据种类查看次数
		$times = self::getCurBattleTime($groupBattleInfo, $enemyID);
		// 如果没有次数了，那么就不能创建
		if ($times <= 0)
		{
			// 记录日志, 返回不能攻击
			Logger::warning('Can not create/join team, defeat count is 0, now defeat time is %d.', $times);
			return 'err';
		}

		/**************************************************************************************************************
 		 * 创建/加入 队伍时还需检测创建者的血量是否已满（不满先自动补满），是否空阵（必须要有一名英雄）。如不满足情况，需进行相应提示。
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
		// 返回ok
		return 'ok';
	}

	/**
	 * 增加一次服务器攻击计数
	 *
	 * @param int $enemyID						敌人军团ID
	 * @param int $uid							队长的uid
	 */
	protected static function __addGroupBattleServerKillNum($enemyID, $uid)
	{
		/**************************************************************************************************************
 		 * 增加服务器击败次数
 		 **************************************************************************************************************/
		// 查看队长的信息，是否需要加一次部队次数
		// 获取怪物所在的副本ID
		$copyID = btstore_get()->GROUP_ARMY[$enemyID]['copy_id'];
		// 获取副本信息
		$LeaderCopyInfo = CopyDao::getUserCopy($uid, $copyID);
		// 如果已经打过这个敌人，那么就需要增加服务器次数
		if (isset($LeaderCopyInfo['va_copy_info']['defeat_id_times'][$enemyID]))
		{
			// 获取用户阵营
			$leaderInfo = EnUser::getUser($uid);
			// 增加一次服务器攻击次数
		 	parent::addServerKillNum($enemyID, $leaderInfo['group_id']);
		}
	}

	/**
	 * 战斗结束后掉落道具
	 */
	protected static function __dropOneManItem($enemyID)
	{
		// 掉落普通道具
		$dropIDs = btstore_get()->GROUP_ARMY[$enemyID]['drop_ids'];

		// 声明背包信息返回值
		$bagInfo = array();
		if (!empty($dropIDs) && !empty($dropIDs[0]))
		{
			// 获取用户信息
			$user = EnUser::getUserObj();
			// 掉落道具, 放到背包里
			$bag = BagManager::getInstance()->getBag();
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
			$bagInfo['bag'] = $bag->update();
			$bagInfo['item'] = $itemArr;
		}
		// 返回已经掉落的各种IDs
		return $bagInfo;
	}

	/**
	 * 阅历加成
	 *
	 * @throws Exception
	 */
	protected static function __experienceAddition($enemyID)
	{
		// 只有活动的时候，才需要阅历加成
		if (btstore_get()->GROUP_ARMY[$enemyID]['type'] == CopyConf::REFRESH_ENEMY)
		{
			// 获取当前时刻
			$curTime = Util::getTime();
			// 获取当日日期
			$curYmd = date("Y-m-d ", $curTime);
			// 查看策划们配置的一天中所有的活动
			foreach (CopyConf::$GROUP_ADD_SCALE as $scale)
			{
				// 获取这次活动开始时刻
				$startTime = $curYmd.$scale['start'];
				$startTime = strtotime($startTime);
				// 获取这次截止时刻
				$endTime = $curYmd.$scale['end'];
				$endTime = strtotime($endTime);
				Logger::debug('Cur time is %d, start time is %d, end time is %d.', $curTime, $startTime, $endTime);

				// 判断当前时刻是否在活动中
				if ($curTime >= $startTime && $curTime <= $endTime)
			    {
			    	// 有加成
					return CopyConf::EXPERIENCE_ADDITION;
			    }
			}
		}
		// 不在配置时间范围内, 或者普通组队的场合, 都是没有加成的
		return 1;
	}

	/******************************************************************************************************************
 	 * 以下内容为了节省子类代码，这里实现了两份。分别对应活动组队和普通组队
 	 ******************************************************************************************************************/
	/**
	 * 获取某个活动军团怪的剩余次数
	 *
	 * @param array $groupBattleInfo			用户军团战信息
	 * @param int $enemyID						军团ID
	 */
	private static function getCurBattleTime($groupBattleInfo, $enemyID)
	{
		// 其实这个方法只是用于活动副本获取次数使用，这里为了通用，进行了特殊处理
		if (btstore_get()->GROUP_ARMY[$enemyID]['type'] != CopyConf::NORMAL_ENEMY)
		{
			// 如果没设置过这个值，那么在这里修复数据
			if (!isset($groupBattleInfo['va_copy_info']['copy_times'][$enemyID]) &&
				self::canAttackGroupEnemy($enemyID))
			{
				// 一切顺利，给初始化次数
				$groupBattleInfo['va_copy_info']['copy_times'][$enemyID] = CopyConf::DAY_ACTIVITY_TIMES;
			}
			// 获取次数
			$times = self::__getActivityBattleTimes($groupBattleInfo);
			// 返回这个怪对应的次数
			return $times[$enemyID];
		}
		// 普通副本组队只有一个次数限制
		return self::__getCommonBattleTimes($groupBattleInfo);
	}

	/**
	 * 获取活动副本的次数
	 *
	 * @param array $groupBattleInfo			用户军团战信息
	 */
	private static function __getActivityBattleTimes($groupBattleInfo)
	{
		// 如果是今天以前，那么刷新次数
		if (!Util::isSameDay($groupBattleInfo['activity_last_time'], CopyConf::REFRESH_TIME))
		{
			// 整理次数
			$groupBattleInfo['activity_last_time'] = Util::getTime();
			// 遍历所有部队
			foreach ($groupBattleInfo['va_copy_info']['copy_times'] as $groupEnemyID => $times)
			{
				// 重置次数
				$groupBattleInfo['va_copy_info']['copy_times'][$groupEnemyID] = CopyConf::DAY_ACTIVITY_TIMES;
			}
			// 更新数据库
			CopyDao::updateGroupBattle($groupBattleInfo['uid'], $groupBattleInfo);
		}
		// 返回所有次数
		return $groupBattleInfo['va_copy_info']['copy_times'];
	}

	/**
	 * 获取普通副本的次数
	 *
	 * @param array $groupBattleInfo			用户军团战信息
	 */
	private static function __getCommonBattleTimes($groupBattleInfo)
	{
		// 如果是今天以前，那么刷新次数
		if (!Util::isSameDay($groupBattleInfo['normal_last_time'], CopyConf::REFRESH_TIME))
		{
			// 获取间隔的天数
			$days = Util::getDaysBetween($groupBattleInfo['normal_last_time'], CopyConf::REFRESH_TIME);
			// 整理次数
			$groupBattleInfo['normal_last_time'] = Util::getTime();
			$groupBattleInfo['normal_times'] += CopyConf::DAY_GROUP_TIMES * $days;
			// 判断是否累积超过了最大值
			if ($groupBattleInfo['normal_times'] > btstore_get()->TOP_LIMIT[TopLimitDef::GROUP_BATTLE_MAX_TIME])
			{
				// 如果超过了，就给最大值，不能再多给次数了
				$groupBattleInfo['normal_times'] = btstore_get()->TOP_LIMIT[TopLimitDef::GROUP_BATTLE_MAX_TIME];
			}
			// 更新数据库
			CopyDao::updateGroupBattle($groupBattleInfo['uid'], $groupBattleInfo);
		}
		// 返回所有次数
		return $groupBattleInfo['normal_times'];
	}

	/**
	 * 记录军团怪的杀敌时刻和次数
	 *
	 * @param int $uid							用户ID
	 * @param int $enemyID						敌人ID
	 */
	protected static function addGroupBattleTimes($uid, $enemyID)
	{
		// 判断敌人类型, 如果是普通怪, 那么减去普通怪次数
		if (btstore_get()->GROUP_ARMY[$enemyID]['type'] == CopyConf::NORMAL_ENEMY)
		{
			return self::__addCommonBattleTimes($uid);
		}
		// 如果是活动怪, 减去活动怪次数
		else
		{
			return self::__addActivityBattleTimes($uid, $enemyID);
		}
	}

	/**
	 * 增加活动副本的次数
	 *
	 * @param array $uid						uid
	 */
	private static function __addActivityBattleTimes($uid, $enemyID)
	{
		// 获取用户数据
		$groupBattleInfo = CopyDao::getGroupBattleInfo($uid);
		// 减去活动次数
		$groupBattleInfo['activity_last_time'] = Util::getTime();
		// 小于0 就不减了吧
		if ($groupBattleInfo['va_copy_info']['copy_times'][$enemyID] > 0)
		{
			--$groupBattleInfo['va_copy_info']['copy_times'][$enemyID];
		}
		else
		{
			Logger::warning('Warning, Act times less then 0! now is %d.',
			                $groupBattleInfo['va_copy_info']['copy_times'][$enemyID]);
			return false;
		}
		// 达成成就
		EnAchievements::notify($uid, AchievementsDef::ACT_GROUP_ATK_TIMES, 1);
		// 更新数据库
		CopyDao::updateGroupBattle($uid, $groupBattleInfo);
		// 返回
		return true;
	}

	/**
	 * 增加普通副本的次数
	 *
	 * @param array $uid						uid
	 */
	private static function __addCommonBattleTimes($uid)
	{
		// 获取用户数据
		$groupBattleInfo = CopyDao::getGroupBattleInfo($uid);
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
			return false;
		}
		// 达成成就
		EnAchievements::notify($uid, AchievementsDef::TEAM_BATTLE_TIMES, 1);
		// 更新数据库
		CopyDao::updateGroupBattle($uid, $groupBattleInfo);
		// 返回
		return true;
	}

	/**
	 * 做所有战斗后的处理
	 *
	 * @param int $uid							用户ID
	 * @param int $enemyID						敌人ID
	 * @param array $atkRet						战斗模块的返回值
	 * @param bool $isLeader					是否是对战
	 */
	public static function addGroupEnemyDefeatInfo($uid, $enemyID, $atkRet, $isLeader = false)
	{
		// 如果此人恰巧不在线，那么就按照在线进行处理
		$sessionUid = RPCContext::getInstance()->getSession('global.uid');
		if (empty($sessionUid))
		{
			RPCContext::getInstance()->setSession('global.uid', $uid);
		}
		// 获取怪物所在的副本ID
		$copyID = btstore_get()->GROUP_ARMY[$enemyID]['copy_id'];

		// 需要判断胜负
		if ($atkRet['server']['result'])
		{
			/**********************************************************************************************************
 		 	 * 增加服务器击败次数 —— 一直没有用，被刘洋在 2012/10/12 重构的时候暂时删掉了
 		 	 **********************************************************************************************************/
//			parent::__addGroupBattleServerKillNum($enemyID, $uids[0]);

			/**********************************************************************************************************
	 		 * 更新军团怪数据库中的杀敌次数和时刻
	 		 **********************************************************************************************************/
			if (!self::addGroupBattleTimes($uid, $enemyID))
			{
				// 没有次数混进来的人
				return ;
			}

			/**********************************************************************************************************
	 		 * 更新用户副本数据中的杀敌信息
	 		 **********************************************************************************************************/
			// 生成副本数据
			$copyInst = new MyCopy();
			// 加算
			$copyInst->addUserDefeatNum($copyID, $enemyID);
			// 依次处理副本奖励
			self::__checkPrize($copyID, $enemyID, $copyInst);
			// 更新数据库
			$copyInst->save($copyID);

			/**********************************************************************************************************
	 		 * 更新人物信息
	 		 **********************************************************************************************************/
			$reward = self::__executeWin($enemyID, $atkRet['server']['battleInfo'], $isLeader);

			/**********************************************************************************************************
	 		 * 通知其他系统
	 		 **********************************************************************************************************/
			// 通知任务系统， 怪物小队和战斗结果
			TaskNotify::beatArmy($enemyID, BattleDef::$APPRAISAL['S']);
			// 通知节日系统
			EnFestival::addCopyPoint();
			// 在这里通知每日任务系统
			EnDaytask::beatSuccess();
		}
		else
		{
			// 失败的时候，仅需要更新人物信息
			$reward = self::__executeLose($enemyID, $atkRet['server']['battleInfo']);
		}

		// 插入用户获得好处的信息
		$atkRet['reward'] = $reward;
		// 给前端发布消息
		RPCContext::getInstance()->sendMsg(array($uid), 'team.reBattleResult', $atkRet);
		// 如果有需要，返回前端
		return $atkRet;
	}

	/**
	 * 处理副本奖励
	 *
	 * @param int $uid							用户ID
	 * @param int $enemyID						敌人ID
	 * @param object $copyInst					用户副本信息实例
	 */
	private static function __checkPrize($copyID, $enemyID, $copyInst)
	{
		// 获取uid
		$uid = RPCContext::getInstance()->getUid();
		// 设置了奖励，遍历奖励ID组
		if (!empty(btstore_get()->PRIZE['enemy'][$enemyID]))
		{
			// 遍历所有奖励数组
			foreach (btstore_get()->PRIZE['enemy'][$enemyID] as $prizeID)
			{
				// 先检查这个奖励是否已经实现过了
				if ($copyInst->isPrized($copyID, $prizeID))
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
					if ($prize['defeat_times'] <= $copyInst->getCopyEnemyDefeatNum($copyID, $enemyID))
					{
						// 记录奖励
						$copyInst->addPrize($copyID, $prizeID);
						// 给增加奖励分数并 检查是否达成成就
						if ($copyInst->addCopyScore($copyID, $prize['score']))
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
	}

	/**
	 * 胜利的时候给用户的所有好处
	 *
	 * @param int $enemyID						敌人ID
	 * @param array $afterFightList				战斗模块的返回值
	 * @param bool $isLeader					是否是队长
	 */
	private static function __executeWin($enemyID, $afterFightList, $isLeader)
	{
		/**************************************************************************************************************
 		 * 奖励各种货
 		 **************************************************************************************************************/
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 掉落物品
		$items = self::__dropOneManItem($enemyID);

		/**************************************************************************************************************
 		 *  减去所有人的血量和增加战斗CD —— 无论输赢都需要做的
 		 **************************************************************************************************************/
		$heroArr = self::__subHpAndAddCd($user, $enemyID, $afterFightList);

		/**************************************************************************************************************
 		 * 给用户奖励
 		 **************************************************************************************************************/
		// 获胜奖励阅历
		$experience_tmp = btstore_get()->GROUP_ARMY[$enemyID]['init_experience'] *
		                  						EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_COPY);
		// 获胜奖励经验
		$exp_tmp = btstore_get()->GROUP_ARMY[$enemyID]['init_exp'] *
		                  						EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_BATTLE);
		// 如果是队长，那么给点儿额外奖励
		if ($isLeader)
		{
			// 获取公会科技
			$guild = GuildLogic::getBuffer($user->getUid());
			// 额外奖励经验 = 初始经验*（1+公会经验科技百分比*科技等级）*（1+队长经验加成）
			$exp = floor($exp_tmp *
			             (1 + $guild['battleExpAddition'] / CopyConf::LITTLE_WHITE_PERCENT) *
			             (1 + btstore_get()->GROUP_ARMY[$enemyID]['captain_exp'] / CopyConf::LITTLE_WHITE_PERCENT));
			// 额外奖励阅历 = 战斗获得阅历=初始阅历*（1+公会阅历科技百分比*科技等级）*（1+队长阅历加成）
			$experience = floor($experience_tmp *
			        (1 + $guild['battleExperienceAddition'] / CopyConf::LITTLE_WHITE_PERCENT) *
			        (1 + btstore_get()->GROUP_ARMY[$enemyID]['captain_experience'] / CopyConf::LITTLE_WHITE_PERCENT));
		}
		// 不是队长就没那么美的事儿了
		else
		{
			// 获取公会科技
			$guild = GuildLogic::getBuffer($user->getUid());
			// 只能获取基础值
			$exp = floor($exp_tmp  * (1 + $guild['battleExpAddition'] / CopyConf::LITTLE_WHITE_PERCENT));
			$experience = floor($experience_tmp * (1 + $guild['battleExperienceAddition'] / CopyConf::LITTLE_WHITE_PERCENT));
		}
		Logger::debug('Uid is %d, BattleExpAddition is %d, BattleExperienceAddition is %d.',
		              $user->getUid(), $guild['battleExpAddition'], $guild['battleExperienceAddition']);

		/**************************************************************************************************************
 		 * 实际操作用户数据
 		 **************************************************************************************************************/
		// 扣除行动力
		$user->subExecution(intval(btstore_get()->GROUP_ARMY[$enemyID]['need_execution']));
		// 获胜奖励游戏币
		$belly = btstore_get()->GROUP_ARMY[$enemyID]['init_belly'];
		$user->addBelly($belly);
		// 获胜奖励阅历
		$experience *= self::__experienceAddition($enemyID);
		Logger::debug("After attack, __executeWin add experience is %d.", $experience);
		$user->addExperience($experience);
		// 获胜奖励威望
		$prestige = btstore_get()->GROUP_ARMY[$enemyID]['init_prestige'];
		$user->addPrestige($prestige);
		// 增加英雄经验
		$heroList = self::addHeroExp($user, $exp, $afterFightList);
		// 更新数据库
		$user->update();

		// 准备掉落物品返回值
		$equip = empty($items) ? array('item' => array(), 'bag' => array()) : $items;
		// 返回给前端
		return array('arrHero' => $heroList, 'belly' => $belly, 'exp' => $exp, 'curHp' => $heroArr,
 	                 'bloodPackage' => $user->getBloodPackage(), 'experience' => $experience,
 	                 'prestige' => $prestige, 'equip' => $equip, 'execution' => $user->getCurExecution(),
					 'cd' => $user->getFightCDTime());
	}

	/**
	 * 失败的时候扣除血量和增加战斗CD
	 *
	 * @param array $afterFightList				战斗模块的返回值
	 */
	private static function __executeLose($enemyID, $afterFightList)
	{
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 减去所有人的血量和增加战斗CD
		$heroArr = self::__subHpAndAddCd($user, $enemyID, $afterFightList);
		// 增加英雄经验
		$heroList = self::addHeroExp($user, 0, $afterFightList);
		// 更新数据库
		$user->update();
		// 返回给前端
		return array('arrHero' => $heroList, 'belly' => 0, 'exp' => 0, 'curHp' => $heroArr,
 	                 'bloodPackage' => $user->getBloodPackage(), 'experience' => 0,
 	                 'prestige' => 0, 'equip' => array('item' => array(), 'bag' => array()),
					 'execution' => $user->getCurExecution(), 'cd' => $user->getFightCDTime());
	}

	/**
	 * 减血和增加CD
	 *
	 * @param object $user						用户实例
	 * @param array $afterFightList				战斗系统的返回值
	 */
	private static function __subHpAndAddCd($user, $enemyID, $afterFightList)
	{
		// 获取用户ID
		$uid = $user->getUid();
		// 战斗后英雄的血量
		$heroArr = array();
		if (isset($afterFightList[$uid]))
		{
			// 如果设置过血量
			$heroArr = EnFormation::subUserHeroHp($afterFightList[$uid], $uid);
		}
		// 增加杀敌CD时间
		$user->addFightCDTime(btstore_get()->GROUP_ARMY[$enemyID]['cd_time']);
		// 返回实际减血数量
		return $heroArr;
	}

	/**
	 * 增加英雄经验
	 *
	 * @param object $user						用户数据实例
	 * @param int $exp							需要增加的经验
	 * @param array $afterFightList				战斗结果
	 */
	private static function addHeroExp($user, $exp, $afterFightList)
	{
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
		if (isset($afterFightList[$user->getUid()]))
		{
			foreach ($afterFightList[$user->getUid()] as $hero)
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
		// 返回加算后的英雄列表
		return $heroList;
	}

	/******************************************************************************************************************
 	 * 海战, 无用代码
 	 ******************************************************************************************************************/
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