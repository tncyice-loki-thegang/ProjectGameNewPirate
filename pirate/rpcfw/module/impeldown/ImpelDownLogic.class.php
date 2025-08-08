<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ImpelDownLogic.class.php 40357 2013-03-08 11:01:18Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/impeldown/ImpelDownLogic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-03-08 19:01:18 +0800 (五, 2013-03-08) $
 * @version $Revision: 40357 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : ImpelDownLogic
 * Description : 推进城实际逻辑实现类， 对内接口实现类
 * Inherit     :
 **********************************************************************************************************************/
class ImpelDownLogic
{
	// 回调中使用的参数
	private static $progressID;
	private static $sFloorID;

	/**
	 * 获取用户推进城信息
	 */
	static public function getImpelDownInfo()
	{
		return MyImpelDown::getInstance()->getImpelDownInfo();
	}


	/**
	 * 刷新NPC信息，在攻击一个NPC部队之前，需要调用此方法用来刷新最新的 NPC信息
	 */
	static public function refreshNpcList($floorID)
	{
		// 获取用户的推进城信息
		$impelInfo = MyImpelDown::getInstance()->getImpelDownInfo();
		// 获取npc信息
		$npcInfo = $impelInfo['va_impel_info']['npc_info'];
		// 如果有有NPC信息, 并且是今天更新的, 那么就不允许再更新了
		if (!empty($impelInfo['va_impel_info']['npc_info'][$floorID]['npc_list']) && 
			Util::isSameDay($impelInfo['va_impel_info']['npc_info'][$floorID]['npc_time'], ImpelConf::REFRESH_TIME))
		{
			Logger::warning('Today already refreshed, floor id is %d.', $floorID);
			throw new Exception('fake');
		}
		// 返回刷新后的NPC信息
		$ret = MyImpelDown::getInstance()->refreshNpcList($floorID);
		MyImpelDown::getInstance()->save();
		
		return $ret;
	}


	/**
	 * 金币刷新NPC
	 */
	static public function refreshNpcListByGold($floorID)
	{
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 需要检查用户的金币数
		$gold = btstore_get()->IMPEL['npc_cost'][0];
		// 需要检查用户的贝里
		$belly = btstore_get()->IMPEL['npc_cost'][1];

		// 因为要扣钱，所以记录下日志，省的你不认账
		Logger::trace('The gold of cur user is %s, need gold is %s.', $user->getGold(), $gold);
		if ($gold > $user->getGold() || $belly > $user->getBelly())
		{
			// 钱不够，直接返回
			return 'err';
		}

		// 刷新NPC
		$npcList = MyImpelDown::getInstance()->refreshNpcList($floorID);

		// 减钱
		$user->subGold($gold);
		$user->subBelly($belly);

		// 更新数据库
		$user->update();
		MyImpelDown::getInstance()->save();

		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_IMPEL_REFRESH_NPC, $gold, Util::getTime());
		// 返回列表
		return $npcList;
	}


	/**
	 * 是否可以攻击
	 * 
	 * @param int $floorID						小层ID
	 * @param array $impelInfo					推进城信息
	 * @throws Exception
	 */
	static private function __canAttack($floorID, $impelInfo)
	{
		// 检查挑战次数
		if (MyImpelDown::getInstance()->getTodayChallengeTimes() <= 0)
		{
			Logger::warning('Today challenge times not enough.', 
			                 MyImpelDown::getInstance()->getTodayChallengeTimes());
			throw new Exception('fake');
		}
		// 检查失败次数
		if (MyImpelDown::getInstance()->getTodayChallengeTimes() < 0)
		{
			Logger::warning('Coin not enough.', 
			                 MyImpelDown::getInstance()->getTodayChallengeTimes());
			throw new Exception('fake');
		}
		// 通过小层ID获取大层ID
		$lFloorID = btstore_get()->FLOOR_S[$floorID]['l_id'];
		// 检查等级是否达到需求
		if (btstore_get()->FLOOR_L[$lFloorID]['open_lv'] > EnUser::getUserObj()->getLevel())
		{
			Logger::warning('Can not enter %d floor! Lv not enough.', $floorID);
			throw new Exception('fake');
		}
		// 是否可以进入这一层
		if (!isset($impelInfo['va_impel_info']['progress'][$lFloorID]))
		{
			Logger::warning('Can not enter %d floor! The L floor id is %d.', $floorID, $lFloorID);
			throw new Exception('fake');
		}
		// 检查是否可以攻击这个怪
		if ($impelInfo['va_impel_info']['progress'][$lFloorID] != 0 && 
			$impelInfo['va_impel_info']['progress'][$lFloorID] < $floorID)
		{
			Logger::warning('Can not defeat this enemy! progress floor is %d, user want is %d.', 
							$impelInfo['va_impel_info']['progress'][$lFloorID], $floorID);
			throw new Exception('fake');
		}
	}


	/**
	 * 发放宝物
	 */
	static private function __sendJewelry($elementsStone, $energyStone)
	{		
		// 给石头
		//$obj= new Jewelry();
		// 增加元素石
		if(!empty($elementsStone))
		{
			Jewelry::addEnergyElement(RPCContext::getInstance()->getUid(), 0,$elementsStone);
		}
		// 增加能量石
		if(!empty($energyStone))
		{
			Jewelry::addEnergyElement(RPCContext::getInstance()->getUid(), $energyStone,0);
		}
		// 返回
		return array('elements' => $elementsStone, 'energy' => $energyStone);
	}
	
	/**
	 * 发放宝物
	 */
	static private function __sendKernel($kernel)
	{		
		if(!empty($kernel))
		{
			$uid = RPCContext::getInstance()->getUid();
			$info = AppleFactoryLogic::getInfo($uid);
			$curKernel = $info['demon_kernel'];
			AppleFactoryLogic::updateExpKernel($uid, NULL, $curKernel+$kernel);
		}
		return $kernel;
	}

	/**
	 * 在回调函数中，战斗结束后掉落道具
	 * 
	 * @param array $dropIDs					掉落表ID
	 */
	static private function __dropItems($dropIDs)
	{
		Logger::debug("__dropItems para is %s.", $dropIDs);
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
		// 返回已经掉落的各种IDs
		return array('item' => $itemArr, 'bag' => $bag->update());
	}


	/**
	 * 攻击一个部队
	 * 
	 * @param int $floorID						小层ID
	 * @param array $heros						用户选择的NPC
	 * @param int $fmtID						阵型ID	
	 * 
	 * @return int								
	 */
	static public function savingAce($floorID, $heros = array(), $fmtID = 0)
	{
		/**************************************************************************************************************
 		 * 查看是否可以攻击
 		 **************************************************************************************************************/
		// 获取敌人ID
		$enemyID = btstore_get()->FLOOR_S[$floorID]['army_id'];
		// 通过小层ID获取大层ID
		$lFloorID = btstore_get()->FLOOR_S[$floorID]['l_id'];
		// 检查参数
		if (!isset(btstore_get()->ARMY[$enemyID]))
		{
			Logger::warning('The %d enemy not found!', $enemyID);
			throw new Exception('config');
		}
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 获取用户ID
		$uid = $user->getUid();

		// 获取用户的推进城信息
		$impelInfo = MyImpelDown::getInstance()->getImpelDownInfo();
		// 参数检查
		if (!empty($fmtID))
		{
			// 如果有非法手段，直接fake
			if (empty($impelInfo['va_impel_info']['npc_info'][$floorID]))
			{
				Logger::warning('Npc list not found!');
				throw new Exception('fake');
			}
			// 先查看该用户准备了什么NPC
			$npcList = $impelInfo['va_impel_info']['npc_info'][$floorID]['npc_list'];
			Logger::debug("Npc now in %d floor is %s.", $floorID, $npcList);
			// 检查是否在已有的NPC列表里面
			if (!empty($heros))
			{
				// 声明一下给战斗的参数
				$herosPara = array();
				// 循环对传上来的内容进行过滤检查
				foreach ($heros as $index => $heroID)
				{
					if (!empty($heroID) && !HeroUtil::isHero($heroID) && 
						(!isset($npcList[$heroID]) || 
						 !in_array($heroID, btstore_get()->FLOOR_S[$floorID]['npc_list']->toArray())))
					{
						Logger::warning('Para err! user npc para is %s.', $heros);
						throw new Exception('fake');
					}
					else if (!empty($heroID) && !HeroUtil::isHero($heroID))
					{
						// 记录这个英雄的ID和技能
						$herosPara[$index] = $npcList[$heroID];
					}
					else 
					{
						$herosPara[$index]['id'] = $heros[$index];
					}
				}
			}
			else 
			{
				Logger::warning('Para err!');
				throw new Exception('fake');
			}
		}
		// 检查是否可以攻击
		self::__canAttack($floorID, $impelInfo);

		/**************************************************************************************************************
 		 * 获取当前阵型详情, 并检查是否可以攻击
 		 **************************************************************************************************************/
		// 获取怪物小队ID
		$teamID = btstore_get()->ARMY[$enemyID]['monster_list_id'];
		// 如果是NPC怪
		if (btstore_get()->FLOOR_S[$floorID]['need_npc'])
		{
			// 用户当前阵型
			$userFormation = EnFormation::getCreatureFormation($herosPara, $fmtID, 
															   $teamID, btstore_get()->IMPEL['power_radio']);
			// 将转生和好感设置为最大
			ImpelDownUtil::setMaxGwLevelRebirthByMaster($userFormation);
			// 将阵型ID设置参数传入的阵型
			$formationID = $fmtID;
		}
		// 如果是普通怪
		else 
		{
			// 用户当前阵型
			$userFormation = EnFormation::getFormationInfo($uid);
			// 将转生和好感设置为最大
			ImpelDownUtil::setMaxGwLevelRebirthByMaster($userFormation);
			// 将阵型ID设置为用户当前默认阵型
			$formationID = $user->getCurFormation();
		}
		// 敌人信息
		$enemyFormation = EnFormation::getBossFormationInfo($teamID);
		// 将对象转化为数组
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation, true);
		Logger::debug('The hero list is %s', $userFormationArr);
		$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation);
		Logger::debug('The boss list is %s', $enemyFormationArr);
		// 设置回调参数
		self::$progressID = $impelInfo['va_impel_info']['progress'][$lFloorID];
		self::$sFloorID = $floorID;

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
		                      array("ImpelDownLogic", "calculateFightRet"),
		                      CopyLogic::getVictoryConditions($enemyID), 
		                      array('bgid' => intval(btstore_get()->ARMY[$enemyID]['background_id']),
		                            'musicId' => btstore_get()->ARMY[$enemyID]['music_path'],
		                            'type' => BattleType::IMPEL_DOWN));
		// 战斗系统返回值
		Logger::debug('Ret from battle is %s.', $atkRet);

		/**************************************************************************************************************
		 * 战斗后的各种处理
 		 **************************************************************************************************************/
		// 必须先获胜
		if (BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'])
		{
			// 怪物小队和战斗结果
			TaskNotify::beatArmy($enemyID, $atkRet['server']['appraisal']);
			TaskNotify::operate(TaskOperateType::IMPEL_DEFEAT);
			// 查看这一层有多少小层
			$allSFloorNum = count(btstore_get()->FLOOR_L[$lFloorID]['s_floor_list']);
			// 获取这一层最后的一个小层ID
			$lastSFloorID = btstore_get()->FLOOR_L[$lFloorID]['s_floor_list'][$allSFloorNum - 1];
			Logger::debug("Big floor is %d, small is %d, last s floorid is %d.", $lFloorID, $floorID, $lastSFloorID);

			// 如果通关了，那么就做特殊处理，进行奖励
			if ($lastSFloorID == $floorID && btstore_get()->FLOOR_L[$lFloorID]['type'] != ImpelConf::HIDE_FLOOR)
			{
				// 只有首次通关才需要做这些事情
				if (!empty(btstore_get()->FLOOR_L[$lFloorID]['after_id']) && 
					MyImpelDown::getInstance()->needPassCopy($lFloorID) && 
					$user->getLevel() >= btstore_get()->FLOOR_L[btstore_get()->FLOOR_L[$lFloorID]['after_id']]['open_lv'])
				{
					// 更新副本进度
					MyImpelDown::getInstance()->upgradeCopyProgress(btstore_get()->FLOOR_L[$lFloorID]['after_id']);
				}
				// 更新排行 
				MyImpelDown::getInstance()->upgradeRank($floorID);
				// 将通关标识一下，方便前端使用
				MyImpelDown::getInstance()->upgradeArmyProgress($lFloorID, 0);
				// 设置通关
				MyImpelDown::getInstance()->setEnd($lFloorID);
			}
			// 隐藏关的话，通关之后需要删除数据
			else if ($lastSFloorID == $floorID)
			{
				MyImpelDown::getInstance()->clearHideCopyProgress($lFloorID);
			}
			// 没有通关，则需要更新进度
			else 
			{
				// 获取下一层的ID
				$nextFloorID = btstore_get()->FLOOR_S[$floorID]['next_id'];
				// 更新进度
				MyImpelDown::getInstance()->upgradeArmyProgress($lFloorID, $nextFloorID);
				// 更新排行, 隐藏关不计入排行
				if (btstore_get()->FLOOR_L[$lFloorID]['type'] != ImpelConf::HIDE_FLOOR)
				{
					MyImpelDown::getInstance()->upgradeRank($floorID);
				}
			}
		}
		// 如果失败了，扣除次数
		else 
		{
			// 扣除失败次数
			MyImpelDown::getInstance()->subCoin();
		}
		// 保存所有的更新
		MyImpelDown::getInstance()->save();

		// 将战斗结果返回给前端
		return array('fightRet' => $atkRet['client'], 'reward' => $atkRet['server']['reward'],
		             'appraisal' => BattleDef::$APPRAISAL[$atkRet['server']['appraisal']]);
	}


	/**
	 * 获取奖励
	 */
	static public function getPrize()
	{
		// 获取次数
		$times = MyImpelDown::getInstance()->getCurPrizeTimes();
		// 检查是否已经领取
		if ($times['free'] <= 0 && $times['gold'] <= 0) 
		{
			Logger::warning('Prize times not enough. free times is %d, gold times is %d', 
			                 $times['free'], $times['gold']);
			throw new Exception('fake');
		}
		// 记录奖励信息
		$experience = 0;
		$belly = 0;
		$dropIDs = array();
		$elementsStone = 0;
		$energyStone = 0;
		$kernel = 0;
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 获取此人推进城信息
		$impelInfo = MyImpelDown::getInstance()->getImpelDownInfo();
		// 遍历所有大关的进度
		foreach ($impelInfo['va_impel_info']['progress'] as $lFloorID => $maxSFloorID)
		{
			// 如果是隐藏关，则什么都不做
			if (btstore_get()->FLOOR_L[$lFloorID]['type'] == ImpelConf::HIDE_FLOOR)
			{
				continue;
			}

			// 查看这个大关下面有多少个小关
			foreach (btstore_get()->FLOOR_L[$lFloorID]['s_floor_list'] as $sFloorID)
			{
				// 如果合适的话，就发送奖励
				if ($maxSFloorID == 0 || $sFloorID < $maxSFloorID)
				{
					foreach (btstore_get()->FLOOR_S[$sFloorID]['drop_ids']->toArray() as $dropID)
					{
						if (!empty($dropID))
						{
							$dropIDs[] = $dropID;
						}
					}
					$experience += btstore_get()->FLOOR_S[$sFloorID]['experience'];
					$belly += btstore_get()->FLOOR_S[$sFloorID]['belly'];
					$elementsStone += btstore_get()->FLOOR_S[$sFloorID]['elements_stone'];
					$energyStone += btstore_get()->FLOOR_S[$sFloorID]['energy_stone'];
					$kernel += btstore_get()->FLOOR_S[$sFloorID]['demon_kernel'];
					Logger::debug("getPrize drop ids is %s.", $dropIDs);
				}
			}

			// 开启隐藏关
			if (MyImpelDown::getInstance()->checkHiddenFloor() && 
				!MyImpelDown::getInstance()->needPassCopy($lFloorID) &&
				rand(0, 10000) <= btstore_get()->IMPEL['hidden_floor_weight'] &&
				rand(0, 10000) <= btstore_get()->FLOOR_L[$lFloorID]['hiden_floor_wight'] &&
				$user->getLevel() >= btstore_get()->FLOOR_L[$lFloorID]['open_lv'])
			{
				MyImpelDown::getInstance()->setHiddenFloor(btstore_get()->FLOOR_L[$lFloorID]['hiden_floor_id']);
			}
		}
		// 计算所需金币  当免费次数没有了，则需要进行金币次数的消费
		if ($times['free'] <= 0)
		{
			// 领奖花费 = 当前可领取能量石数量*新增金币领奖能量石系数/10000+当前可领取元素石数量*新增金币领奖元素石系数/10000。最后结果向下取整最少为1
			$needGold = floor($energyStone * btstore_get()->IMPEL['prize_energy_stone_radio'] / ImpelConf::LITTLE_WHITE_PERCENT + 
							  $elementsStone * btstore_get()->IMPEL['prize_elements_stone_radio'] / ImpelConf::LITTLE_WHITE_PERCENT);
			// 判断金币是否足够
			if ($user->getGold() < $needGold)
			{
				Logger::warning('Not enough gold. need %d, now has %d', $needGold, $user->getGold());
				throw new Exception('fake');
			}
			// 如果够了我就先扣钱
			$user->subGold($needGold);
			// 发送金币通知
			Statistics::gold(StatisticsDef::ST_FUNCKEY_IMPEL_GOLD_PRIZE, $needGold, Util::getTime());
		}

		// 掉落各种物品
		$reward = self::__dropItems($dropIDs);
		// 发放宝物
		self::__sendJewelry($elementsStone, $energyStone);
		// 发放恶魔因子
		self::__sendKernel($kernel);
		
		// 奖励阅历
		$user->addExperience($experience);
		// 奖励游戏币
		$user->addBelly($belly);
		Logger::debug("getPrize add experience: %d, belly : %d, items : %s. ", $experience, $belly, $reward);

		// 记录次数
		MyImpelDown::getInstance()->subPrizeTimes();
		// 更新数据库
		MyImpelDown::getInstance()->save();
		$user->update();

		// 返回前端
		return array('reward' => $reward, 'info' => MyImpelDown::getInstance()->getImpelDownInfo());
	}


	/**
	 * 购买失败挑战次数
	 * 
	 * @throws Exception
	 */
	static public function buyChallengeTime() 
	{
		// 获取此人推进城信息
		$impelInfo = MyImpelDown::getInstance()->getImpelDownInfo();
		// 查看剩余次数, 如果剩余次数还满，则不需要再买了
		if ($impelInfo['buy_coin_times'] >= btstore_get()->IMPEL['coins'])
		{
			Logger::warning('Buy coin times if full. can not buy new one.');
			throw new Exception('fake');
		}
		// R要消费，检查金币个数
		$gold = ($impelInfo['buy_coin_times'] * btstore_get()->IMPEL['times_cost_up'][0]) + btstore_get()->IMPEL['times_cost'][0];
		// 如果超过了封顶值就使用封顶值
		$gold = $gold > btstore_get()->IMPEL['max_value'] ? btstore_get()->IMPEL['max_value'] : $gold;
		// 需要检查用户的贝里
		$belly = ($impelInfo['buy_coin_times'] * btstore_get()->IMPEL['times_cost_up'][1]) + btstore_get()->IMPEL['times_cost'][1];
		// 如果超过了封顶值就使用封顶值
		$belly = $belly > btstore_get()->IMPEL['max_value'] ? btstore_get()->IMPEL['max_value'] : $belly;
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 因为要扣钱，所以记录下日志，省的你不认账
		Logger::trace('The gold of cur user is %s, need gold is %s.', $user->getGold(), $gold);
		if ($gold > $user->getGold() || $belly > $user->getBelly())
		{
			// 钱不够，直接返回
			return 'err';
		}
		// 给个币子
		$coins = MyImpelDown::getInstance()->addCoin();
		Logger::debug('Now have %d coins.', $coins);

		// 减钱
		$user->subGold($gold);
		$user->subBelly($belly);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_IMPEL_BUY_COINS, $gold, Util::getTime());

		// 保存至数据库
		MyImpelDown::getInstance()->save();
		// 返回给前端
		return 'ok';
	}


	/**
	 * 获取当前用户排行
	 */
	static public function getSelfOrder()
	{
		// 通过 uid 获取推进城信息
		$impelInfo = ImpelDownDao::getImpelDownInfo(RPCContext::getInstance()->getUid());
		// 判断推进城信息是否为空
		if ($impelInfo === false)
		{
			Logger::warning('What do you want?');
			throw new Exception('fake');
		}
		// 获取本人推进城排名
		return ImpelDownDao::getUserImpelRank(RPCContext::getInstance()->getUid());
	}


	/**
	 * 获取Impel排行信息
	 * 
	 * @param $start							积分开始
	 * @param $offset							积分偏移
	 */
	static public function getTop($start, $offset)
	{
		// 获取服务器副本ID排行
		$list = ImpelDownDao::getServerImpelList($start, $offset);
		// 对空加判断
		if (!empty($list))
		{
			// 获取uid列表
	    	$arrUids = Util::arrayExtract($list, 'uid');
	    	// 使用uid列表, 获取用户信息
    		$arrUser = Util::getArrUser($arrUids, array('uname', 'utid', 'level'));
	    	// 将公会名称和等级插入数组
	    	foreach ($list as $key => $user)
	    	{
	    		// 合并用户等级
	    		$list[$key]['level'] = $arrUser[$user['uid']]['level'];
	    		// 合并用户名
	    		$list[$key]['uname'] = $arrUser[$user['uid']]['uname'];
	    		// 合并用户模板ID
	    		$list[$key]['utid'] = $arrUser[$user['uid']]['utid'];
	    	}
		}
    	// 返回给前端
    	return $list;
	}


	/**
	 * 计算战斗结果
	 * 
	 * @param array $atkRet						战斗结果
	 */
	public static function calculateFightRet($atkRet)
	{
		// 获取用户类实例
		$user = EnUser::getUserObj();
		$uid = $user->getUid();
		// 声明返回值
		$itemIDs = array();
		$experience = 0;
		$belly = 0;
		$heroList = array();
		$stone = array();
		$kernel = 0;

		// 获取敌人ID
		$enemyID = btstore_get()->FLOOR_S[self::$sFloorID]['army_id'];

		// 必须先获胜
		if (BattleDef::$APPRAISAL[$atkRet['appraisal']] <= BattleDef::$APPRAISAL['D'])
		{
			// 怪物小队和战斗结果
			TaskNotify::beatArmy($enemyID, $atkRet['appraisal']);
			TaskNotify::operate(TaskOperateType::IMPEL_DEFEAT);
			// 如果是第一次攻击这个敌人, 掉落物品
			if (self::$progressID == self::$sFloorID)
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
					CopyLogic::checkSaveReplay($uid, $enemyID, $user->getGroupId(), $atkRet['brid']);
				}

				// 检查首杀
				$ret = CopyLogic::checkFirstDown($uid, $enemyID, $atkRet['brid']);
				// 如果首杀成功了
				if ($ret)
				{
					// 固化一下录像
					$bt = new Battle();
					$bt->setPermanent($atkRet['brid']);
				}

				// 掉落各种物品
				$itemIDs = self::__dropItems(btstore_get()->FLOOR_S[self::$sFloorID]['drop_ids']);
				// 发放宝物
				$stone = self::__sendJewelry(btstore_get()->FLOOR_S[self::$sFloorID]['elements_stone'], 
											 btstore_get()->FLOOR_S[self::$sFloorID]['energy_stone']);
				// 发放恶魔因子
				$kernel = self::__sendKernel(btstore_get()->FLOOR_S[self::$sFloorID]['demon_kernel']); 
				
				
				 // 奖励阅历
				$user->addExperience(btstore_get()->FLOOR_S[self::$sFloorID]['experience']);
				$experience = btstore_get()->FLOOR_S[self::$sFloorID]['experience'];
				// 奖励游戏币
				$user->addBelly(btstore_get()->FLOOR_S[self::$sFloorID]['belly']);
				$belly = btstore_get()->FLOOR_S[self::$sFloorID]['belly'];
				// 更新数据库
				$user->update();
			}
		}
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
	 	return array('arrHero' => $heroList, 'belly' => $belly,
	 	             'experience' => $experience, 'equip' => $itemIDs, 'stone' => $stone, 'kernel' => $kernel);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */