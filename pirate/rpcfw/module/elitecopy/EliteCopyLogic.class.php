<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EliteCopyLogic.class.php 36587 2013-01-22 03:24:18Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elitecopy/EliteCopyLogic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-22 11:24:18 +0800 (二, 2013-01-22) $
 * @version $Revision: 36587 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EliteCopyLogic
 * Description : 精英副本逻辑实现类
 * Inherit     : 
 **********************************************************************************************************************/
class EliteCopyLogic 
{

	/**
	 * 获取当前人物的精英副本信息
	 */
	public static function getEliteCopyInfo() 
	{
		// 获取此人精英副本信息
		$eliteCopy = MyEliteCopy::getInstance()->getUserEliteInfo();
		// 添加需要的信息
		$eliteCopy['max_coin'] = EliteCopyConf::COINS;
		// 删除前端不需要的信息
		unset($eliteCopy['coins']);
		unset($eliteCopy['buy_coin_times']);
		unset($eliteCopy['status']);
		// 返回给前端
		return $eliteCopy;
	}

	/**
	 * 进入精英副本
	 * 
	 * @param int $copyID						副本ID
	 */
	public static function enterEliteCopy($copyID) 
	{
		// 获取此人精英副本信息
		$eliteCopy = MyEliteCopy::getInstance()->getUserEliteInfo();
		// 是否可以进入这个副本
		if (!self::canEnterCopy($eliteCopy['progress'], $copyID, $eliteCopy['va_copy_info']))
		{
			Logger::warning('Can not enter elite copy! The copy id is %d.', $copyID);
			throw new Exception('fake');
		}
		// 检查挑战次数
		if (MyEliteCopy::getInstance()->getTodayChallengeTimes() <= 0)
		{
			Logger::warning('Today challenge times not enough.', 
			                 MyEliteCopy::getInstance()->getTodayChallengeTimes());
			throw new Exception('fake');
		}
		// 可以进入副本
		RPCContext::getInstance()->setSession('global.copyId', $copyID);
		// 更新精英副本进度
		MyEliteCopy::getInstance()->startFight($copyID);
		MyEliteCopy::getInstance()->save();
		// 正常返回
		return 'ok';
	}

	/**
	 * 离开精英副本
	 */
	public static function leaveEliteCopy() 
	{
		// 清空精英副本信息
		MyEliteCopy::getInstance()->resetCopyInfo();
		MyEliteCopy::getInstance()->save();
		// 离开副本，删掉信息
		RPCContext::getInstance()->unsetSession('global.copyId');
		// 正常返回
		return 'ok';
	}

	/**
	 * 重新进入精英副本
	 * 
	 * @param int $copyID						副本ID
	 */
	public static function restartEliteCopy($copyID) 
	{
		// 清空精英副本信息
		MyEliteCopy::getInstance()->resetCopyInfo();
		// 重新进入副本
		return self::enterEliteCopy($copyID);		
	}

	/**
	 * 攻击精英副本部队
	 * 
	 * @param int $enemyID						部队ID
	 */
	public static function attack($enemyID) 
	{
		/**************************************************************************************************************
 		 * 查看是否可以攻击
 		 **************************************************************************************************************/
		// 检查参数
		if (!isset(btstore_get()->ARMY[$enemyID]))
		{
			Logger::fatal('The %d enemy not found!', $enemyID);
			throw new Exception('fake');
		}
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 获取用户ID
		$uid = $user->getUid();
		// 如果因为CD时间
		if (Util::getTime() < $user->getFightCDTime())
		{
			return 'cd';
		}
		// 检查挑战次数
		if (MyEliteCopy::getInstance()->getTodayChallengeTimes() <= 0)
		{
			Logger::warning('Today challenge times not enough.', 
			                 MyEliteCopy::getInstance()->getTodayChallengeTimes());
			throw new Exception('fake');
		}
		// 检查失败次数
		if (MyEliteCopy::getInstance()->getCurCoinNum() < 0)
		{
			Logger::fatal('Coin not enough.', 
			               MyEliteCopy::getInstance()->getCurCoinNum());
			throw new Exception('fake');
		}

		// 获取对应副本地址 
		$copyID = btstore_get()->ARMY[$enemyID]['copy_id'];
		// 获取此人精英副本信息
		$eliteCopy = MyEliteCopy::getInstance()->getUserEliteInfo();
		// 是否可以进入这个副本
		if (!self::canEnterCopy($eliteCopy['progress'], $copyID, $eliteCopy['va_copy_info']))
		{
			Logger::warning('Can not enter elite copy! The copy id is %d.', $copyID);
			throw new Exception('fake');
		}
		// 检查是否可以攻击这个怪
		if ($eliteCopy['va_copy_info'][$copyID]['enemy_id'] != $enemyID &&
		    !isset($eliteCopy['va_copy_info'][$copyID]['defeat_id_times'][$enemyID]))
		{
			Logger::warning('Can not defeat this enemy! progress enemy id is %d. defeat_id_times is %s.', 
			                $eliteCopy['va_copy_info'][$copyID]['enemy_id'],
			                $eliteCopy['va_copy_info'][$copyID]['defeat_id_times']);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 获取当前阵型详情, 并检查是否可以攻击
 		 **************************************************************************************************************/
		// 获取怪物小队ID
		$teamID = btstore_get()->ARMY[$enemyID]['monster_list_id'];
		// 用户当前阵型
		$userFormation = EnFormation::getFormationInfo($uid);
		// 将阵型ID设置为用户当前默认阵型
		$formationID = $user->getCurFormation();
		// 敌人信息
		$enemyFormation = EnFormation::getBossFormationInfo($teamID);
		// 获取阵型信息，并加满血
		EnFormation::checkUserFormation($uid, $userFormation);
		// 将对象转化为数组
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation, true);
		Logger::debug('The hero list is %s', $userFormationArr);
		$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation);
		Logger::debug('The boss list is %s', $enemyFormationArr);

		/**************************************************************************************************************
 		 * 调用战斗模块
 		 **************************************************************************************************************/
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
		                      CopyDef::NORMAL_ROUND,
		                      array("EliteCopyLogic", "calculateFightRet"),
		                      CopyLogic::getVictoryConditions($enemyID), 
		                      array('bgid' => intval(btstore_get()->ARMY[$enemyID]['background_id']),
		                            'musicId' => btstore_get()->ARMY[$enemyID]['music_path'],
		                            'type' => BattleType::COPY));
		// 战斗系统返回值
		Logger::debug('Ret from battle is %s.', $atkRet);

		/**************************************************************************************************************
		 * 战斗后的各种处理
 		 **************************************************************************************************************/
		// 初始化返回值
		$reward = array();
		// 必须先获胜
		if (BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'])
		{
			// 如果是第一次攻击这个敌人, 检查战斗录像
			if ($copyID == $eliteCopy['progress'])
			{
				// 获取这个部队的所有攻略
				$replayList = CopyDao::getAllReplayList($enemyID);
				// 标识一下
				$need = true;
				// 查看是否有现存的此人战报
				foreach ($replayList as $replay)
				{
					// 如果已经有此人战报的话，则不需要在保存了
					if ($replay['uid'] == $uid)
					{
						$need = false;
					}
				}
				// 只有在需要的时候，才保存战报
				if ($need)
				{
					// 那么需要保存战斗录像
					CopyLogic::checkSaveReplay($uid, $enemyID, $user->getGroupId(), $atkRet['server']['brid']);
				}
			}
			// 如果通关了，那么就做特殊处理，进行奖励
			if (btstore_get()->ELITE_COPY[$copyID]['last_enemy_id'] == $enemyID)
			{
				// 扣除挑战次数
				MyEliteCopy::getInstance()->subChallengeTimes();
				// 只有首次通关才需要做这些事情
				if (MyEliteCopy::getInstance()->needPassCopy($copyID))
				{
					// 更新副本进度
					MyEliteCopy::getInstance()->upgradeProgress($copyID);
					// 更新炫耀信息
					self::checkSavePassList($copyID, $uid, $user->getLevel());
					// 达成成就
					EnAchievements::notify($uid, AchievementsDef::PASS_COPY, $copyID);
				}

				// 掉落各种物品
				$reward = self::getCopyPassItems($copyID);
				// 奖励阅历
				$user->addExperience(btstore_get()->ELITE_COPY[$copyID]['experience']);
				// 奖励游戏币
				$user->addBelly(btstore_get()->ELITE_COPY[$copyID]['belly']);
				// 奖励声望
				$user->addPrestige(btstore_get()->ELITE_COPY[$copyID]['prestige']);
				// 奖励星魂石
				Astrolabe::addStone($uid, btstore_get()->ELITE_COPY[$copyID]['star']);
				// 更新数据库
				$user->update();

				// 通知活跃度系统
				EnActive::addEliteCopyAtkTimes();
				// 通知节日系统
				EnFestival::addElCopyPoint();
			}
			// 没有通关，则需要更新进度
			else 
			{
				// 更新进度
				MyEliteCopy::getInstance()->saveEnemyID($copyID, $enemyID);
			}
		}
		// 如果失败了，可能会发一个可怜虫成就
		else 
		{
			// 扣除失败次数
			MyEliteCopy::getInstance()->subCoin();
		}
		// 保存所有的更新
		MyEliteCopy::getInstance()->save();

		// 将战斗结果返回给前端
		return array('fightRet' => $atkRet['client'], 
		             'appraisal' => BattleDef::$APPRAISAL[$atkRet['server']['appraisal']],
		             'cd' => $user->getFightCDTime(), 'reward' => $reward);
	}

	/**
	 * 计算战斗结果
	 * 
	 * @param unknown_type $atkRet
	 */
	public static function calculateFightRet($atkRet)
	{
		// 获取用户类实例
		$user = EnUser::getUserObj();
		// 返回值
		$heroList = array();
		// 先处理主英雄数据, 否则卡等级时，用户其他英雄有可能会损失一部分经验
		$masterHeroObj = $user->getMasterHeroObj();
		// 获取主英雄id
		$heroList[$masterHeroObj->getHid()]['hid'] = $masterHeroObj->getHid();
		// 获取主形象id
		$heroList[$masterHeroObj->getHid()]['htid'] = $masterHeroObj->getHtid();
		// 获取原等级
		$heroList[$masterHeroObj->getHid()]['initial_level'] = $masterHeroObj->getLevel();
		// 获取提升等级
		$heroList[$masterHeroObj->getHid()]['current_level'] = $masterHeroObj->getLevel();
		// 获取当前经验
		$heroList[$masterHeroObj->getHid()]['current_exp'] = $masterHeroObj->getExp();
		// 获取获得经验
		$heroList[$masterHeroObj->getHid()]['add_exp'] = 0;
		// 循环处理所有其他英雄数据
		foreach ($atkRet['team1'] as $hero)
		{
			// 不为NPC的英雄 并且不为主英雄
			if (HeroUtil::isHero($hero['hid']))
			{
				// 获取英雄对象
				$heroObj = $user->getHeroObj($hero['hid']);
				// 获取英雄id
				$heroList[$hero['hid']]['hid'] = $hero['hid'];
				// 获取形象id
				$heroList[$hero['hid']]['htid'] = $heroObj->getHtid();
				// 获取原等级
				$heroList[$hero['hid']]['initial_level'] = $heroObj->getLevel();
				// 获取提升等级
				$heroList[$hero['hid']]['current_level'] = $heroObj->getLevel();
				// 获取当前经验
				$heroList[$hero['hid']]['current_exp'] = $heroObj->getExp();
				// 获取获得经验
				$heroList[$hero['hid']]['add_exp'] = 0;
			}
		}
		// 返回奖励内容
	 	return array('arrHero' => $heroList, 'belly' => 0, 'exp' => 0, 'experience' => 0, 'prestige' => 0);
	}

	/**
	 * 查看是否可以进入副本
	 * 
	 * @param int $progress						用户当前副本进度
	 * @param int $copyID						副本ID
	 * @param array $userCopyInfo				用户当前拥有的精英副本
	 */
	private static function canEnterCopy($progress, $copyID, $userCopyInfo)
	{
		// 如果尚未开启活动功能
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE))
		{
			return false;
		}
		// 先检查是否已经可以攻打
		if (!isset($userCopyInfo[$copyID]))
		{
			// 返回不能进入这个副本
			Logger::warning('Copy id not set yet!');
			return false;
		}
		// 不是第一次进入的话
		if ($progress != 0 && $progress < $copyID)
		{
			// 检查挑战进度, 如果这个人压根不能挑战，或者根本没打到这里，那么就直接报错
			Logger::warning('Progress not right!');
			return false;
		}
		// 遍历之前的所有副本，如果没打到这里，也不能进入
		foreach ($userCopyInfo as $copyInfo)
		{
			// 尚有未通关副本，那么直接返回不能挑战
			if ($copyInfo['copy_id'] < $copyID && $copyInfo['is_end'] != 1)
			{
				Logger::warning('Not all copy end!');
				return false;
			}
		}
		return true;
	}

	/**
	 * 使用金币，直接通关副本
	 * 
	 * @param int $copyID						副本ID
	 */
	public static function passByGold($copyID) 
	{
		// 如果尚未开启活动功能
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE))
		{
			return 'err';
		}
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 获取实际需要的开销
		$gold = btstore_get()->VIP[$user->getVip()]['elitecopy_pass_gold'];
		// 因为要扣钱，所以记录下日志，省的你不认账
		Logger::trace('The gold of cur user is %s, need gold is %s.', $user->getGold(), $gold);
		if ($gold > $user->getGold())
		{
			// 钱不够，直接返回
			return 'err';
		}
		// 获取此人精英副本信息
		$eliteCopy = MyEliteCopy::getInstance()->getUserEliteInfo();
		// 查看是否已经通关
		if (!isset($eliteCopy['va_copy_info'][$copyID]) || $eliteCopy['va_copy_info'][$copyID]['is_end'] != 1)
		{
			Logger::warning('Can not pass elite copy, not passed yet! The copy id is %d.', $copyID);
			throw new Exception('fake');
		}
		// 查看剩余次数是否充足
		if (MyEliteCopy::getInstance()->getTodayChallengeTimes() <= 0)
		{
			Logger::warning('Today challenge times not enough.', 
			                 MyEliteCopy::getInstance()->getTodayChallengeTimes());
			throw new Exception('fake');
		}

		// 掉落各种物品
		$bagInfo = self::getCopyPassItems($copyID);
		// 奖励阅历
		$user->addExperience(btstore_get()->ELITE_COPY[$copyID]['experience']);
		// 奖励游戏币
		$user->addBelly(btstore_get()->ELITE_COPY[$copyID]['belly']);
		// 奖励声望
		$user->addPrestige(btstore_get()->ELITE_COPY[$copyID]['prestige']);
		// 奖励星魂石
		Astrolabe::addStone($user->getUid(), btstore_get()->ELITE_COPY[$copyID]['star']);

		// 减钱
		$user->subGold($gold);
		$user->update();	
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_PASS_BY_GOLD, $gold, Util::getTime());

		// 减去一次通关次数
		MyEliteCopy::getInstance()->subChallengeTimes();
		// 保存至数据库
		MyEliteCopy::getInstance()->save();
		// 通知活跃度系统
		EnActive::addEliteCopyAtkTimes();
		// 通知节日系统
		EnFestival::addElCopyPoint();

		// 返回前端背包信息
		return $bagInfo;
	}

	/**
	 * 掉落通关道具
	 * 
	 * @param int $copyID						副本ID
	 */
	private static function getCopyPassItems($copyID)
	{
		// 获取通关的掉落表
		$dropIDs = btstore_get()->ELITE_COPY[$copyID]['drop_ids'];
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
		// 保存用户背包数据，并获取改变的内容
		$bagInfo = $bag->update();
		// 返回已经掉落的各种IDs
		return array('item' => $itemArr, 'bag' => $bagInfo);
	}

	/**
	 * 购买失败挑战次数
	 * 
	 * @throws Exception
	 */
	public static function byCoin() 
	{
		// 获取此人精英副本信息
		$eliteCopy = MyEliteCopy::getInstance()->getUserEliteInfo();
		// 查看剩余次数, 如果剩余次数还满，则不需要再买了
		if ($eliteCopy['coins'] >= EliteCopyConf::COINS)
		{
			Logger::warning('Coins if full. Need not buy new one.');
			throw new Exception('fake');
		}
		// R要消费，检查金币个数
		$gold = $eliteCopy['buy_coin_times'] * EliteCopyConf::COIN_UP_GOLD + EliteCopyConf::COIN_INIT_GOLD;
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 因为要扣钱，所以记录下日志，省的你不认账
		Logger::trace('The gold of cur user is %s, need gold is %s.', $user->getGold(), $gold);
		if ($gold > $user->getGold())
		{
			// 钱不够，直接返回
			return 'err';
		}
		// 给个币子
		$coins = MyEliteCopy::getInstance()->addCoin();
		Logger::debug('Now have %d coins.', $coins);

		// 减钱
		$user->subGold($gold);
		$user->update();	
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_BUY_COINS, $gold, Util::getTime());

		// 保存至数据库
		MyEliteCopy::getInstance()->save();
		// 返回给前端
		return $gold;
	}

	/**
	 * 获取通过副本的列表
	 * 
	 * @param int $copyID						副本ID
	 */
	public static function getCopyPassList($copyID)
	{
		// 获取最新的列表信息
		$copyPassInfo = EliteCopyDao::getCopyPass($copyID);
		// 如果不为空，那么进行查询
		if (!empty($copyPassInfo))
		{
			// 现获取所有的uid
			$arrUid = Util::arrayExtract($copyPassInfo, 'uid');
			$arrUser =  Util::getArrUser($arrUid, array('uid', 'uname', 'group_id'));
			// 循环合并
			foreach ($copyPassInfo as $key => $copyPass)
			{
				// 获取uid
				$uid = $copyPass['uid'];
				// 循环查询数据库结果
				foreach ($arrUser as $user)
				{
					// 找到这个用户就直接合并
					if ($uid == $user['uid'])
					{
						$copyPassInfo[$key]['uname'] = $user['uname'];
						$copyPassInfo[$key]['group_id'] = $user['group_id'];
						break;
					}
				}
			}
		}
		// 返回合并结果，当然，如果查询出数据的话……
		return $copyPassInfo;
	}

	/**
	 * 更新通关信息
	 * 
	 * @param int $copyID						副本ID
	 * @param int $uid							用户ID
	 * @param int $lv							用户等级
	 */
	private static function checkSavePassList($copyID, $uid, $lv)
	{
		// 获取最新的列表信息
		$copyPassInfo = EliteCopyDao::getCopyPass($copyID);
		// 查询重复，如果来过就无视
		foreach ($copyPassInfo as $copy) 
		{
			// 如果有重复，那么直接返回
			if ($copy['uid'] == $uid)
			{
				return ;
			}
		}
		// 如果小于5个
		if (count($copyPassInfo) < EliteCopyConf::COPY_PASS_LIST ||
		    empty($copyPassInfo))
		{
			EliteCopyDao::addNewCopyPass($uid, $copyID, $lv);
		}
		// 大于五个了，那么更新最老的
		else 
		{
			// 更新最后一个
			EliteCopyDao::updateCopyPass($copyPassInfo[4]['uid'], $uid, $lv, $copyID);
		}
	} 
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */