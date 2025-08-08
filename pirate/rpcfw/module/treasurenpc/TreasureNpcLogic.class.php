<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TreasureNpcLogic.class.php 36405 2013-01-18 10:09:55Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasurenpc/TreasureNpcLogic.class.php $
 * @author $Author$(lijinfeng@babeltime.com)
 * @date $Date: 2013-01-18 18:09:55 +0800 (五, 2013-01-18) $
 * @version $Revision: 36405 $
 * @brief 
 *  
 **/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */


class TreasureNpcLogic
{
	/**
	 * 获取寻宝npc信息
	 * @return unknown_type
	 */
	/*static function getTreasureNpc()
	{
		$ret = array('ret' => 'ok','npc_boat' => array());
		// 当前status标示有效的npc船
		$res = TreasureNpcDao::getTreasureNpcInfo();
		// 过滤下无效的
		$res = self::checkTreasureNpcs($res);
		// 没有有效数据
		if(empty($res))
		{
			$act_time = self::getTreasureNpcTime();
			if(!empty($act_time))	// 目前在活动中
			{
				$locker = new Locker();
				$lockKey = 'treasurenpc#gennpc';
				try
				{
					$locker->lock($lockKey);
				}
				catch (Exception $e) 
				{
					Logger::warning('lock exception. exception msg:%s', $e->getMessage());
					$ret['ret'] = 'lock';
					return $ret;
				}

				try 
				{
					// 初始化随机npc
					$res = self::genTreasureNpc($act_time);
					// 解锁
					$locker->unlock($lockKey);
				}
				catch (Exception $e) 
				{
					$locker->unlock($lockKey);
					throw $e;
				}
			}
		}
		
		// 过滤下历史遗留数据
		$ret['npc_boat'] = self::checkTreasureNpcs($res,true);
		return $ret;
	}*/
	
	static function getTreasureNpc()
	{
		$ret = array('ret' => 'ok','npc_boat' => array());
		$npc_return_time = self::getTreasureNpcTime(TreasureNpcDef::TREASURE_NPC_RETURN_TIME);
		// 不在活动区间，当前也不在npc船返航时间
		if(empty($npc_return_time))
		{
			return $ret;
		}
		
		$res = TreasureNpcDao::getTreasureNpcInfoInActivityTime($npc_return_time);
		//Logger::warning("getTreasureNpcInfoInActivityTime res:%s",$res);
		if(empty($res))
		{
			$act_time = self::getTreasureNpcTime();
			if(!empty($act_time))	// 目前在活动中
			{
				$locker = new Locker();
				$lockKey = 'treasurenpc#gennpc';
				try
				{
					$locker->lock($lockKey);
				}
				catch (Exception $e) 
				{
					Logger::warning('lock exception. exception msg:%s', $e->getMessage());
					$ret['ret'] = 'lock';
					return $ret;
				}
				
				try 
				{
					// 初始化随机npc
					$res = self::genTreasureNpc($act_time);
					
				}
				catch (Exception $e) 
				{
					$locker->unlock($lockKey);
					throw $e;
				}
				
				// 解锁
				$locker->unlock($lockKey);
			}
		}
		
		//Logger::warning("getTreasureNpc %s",$res);
		// 过滤下历史遗留数据
		$ret['npc_boat'] = self::checkTreasureNpcs($res,true);
		return $ret;
	}

	
	/**
	 * 检查npc船合法性
	 * @param $boats	npc船集合
	 * @param $isStrict 严格检查，就是时间没到也不能出现在结果集里
	 * @return array 有效的npc船信息
	 */
	static function checkTreasureNpcs($boats,$isStrict = false)
	{
		$ret = array();
		foreach($boats as $bt)
		{
			$res = self::checkBoatNpc($bt,$isStrict);
			if($res != 'ok')
				continue;
				
			$ret[] = $bt;
		}
		
		return $ret;
	}
	

	
	/**
	 * 获取npc船活动时间区间
	 * @param $offset_time 是否考虑npc船的返航时间
	 * @return array
	 * 		!empty()，在活动时间区间
	 * 		empty()，不在活动时间区间	
	 * mark $offset_time 有值时，返回的是当前是否有npc船的时间，为0时，返回的是当前是否npc船活动时间	
	 */
	static function getTreasureNpcTime($offset_time = 0)
	{
		$time = Util::getTime();
		
		$tn_refresh_times = btstore_get()->NPC_TREASURE[TreasureNpcDef::TREASURE_NPC_DATA_INDEX]['npc_boat_refresh_times'];	
		if(empty($tn_refresh_times['begin_time']) || empty($tn_refresh_times['end_time']))
		{
			return array();
		}
		
		$bgtime = $tn_refresh_times['begin_time']->toArray();
		$edtime = $tn_refresh_times['end_time']->toArray();
		sort($bgtime);
		sort($edtime);
		
		//Logger::debug("begin time:%s,end time:%s",$bgtime,$edtime);
		
		// 当天最后一刻
		$interval = self::getMatchTime($time,$bgtime,$edtime,$offset_time);	
		Logger::debug("current time interval:%s",$interval);
			
		return $interval;
	}
	
	
	static function getMatchTime($curTime,$beginTimes,$endTimes,$offset_time)
	{
		$cur_day_time = mktime(0, 0, 0, date('m', $curTime), date('d', $curTime), date('Y', $curTime));
		
		//比较每日的时间段
		for ( $i = 0; $i < count($beginTimes); $i++)
		{
			//如果在当前时间段内，或者还没有到开始时间，则match
			if ($curTime >= $cur_day_time + $beginTimes[$i] && $curTime < $cur_day_time + $endTimes[$i] + $offset_time)
			{
				return array($cur_day_time + $beginTimes[$i],$cur_day_time + $endTimes[$i]);
			}
		}
		
		return array();
	}
	
	
	
	/**
	 * 产生寻宝npc船数据
	 * @param $act_time		活动时间
	 * @return unknown_type
	 */
	static function genTreasureNpc($act_time)
	{
		$ret = array();
		$ids = self::genTreasureNpcIDs();
		if(empty($ids) || empty($act_time))
		{
			return $ret;
		}
		
		$ret = TreasureNpcDao::activateTreasureNpc($ids,$act_time);	

		return $ret;
	}
	
	
	/**
	 * 获取当前服务器对应的npc船ID组
	 * @return array
	 */
	static function genTreasureNpcIDs()
	{
		$svr_lvl = self::getServerTopNLvl(TreasureNpcDef::TREASURE_NPC_LVL_DEPENDS_SEC);
		if($svr_lvl > 0) // 
		{
			$lvls_array = btstore_get()->NPC_TREASURE[TreasureNpcDef::TREASURE_NPC_DATA_INDEX]['npc_boat_refresh_lvls'];
			$cur_ids_index = -1;
			foreach($lvls_array as $lvl_pair)
			{
				$cur_ids_index++;
				if($svr_lvl >= $lvl_pair[0] && $svr_lvl < $lvl_pair[1])
				{
					break;
				}
			}
		}
		
		$ids = isset(btstore_get()->NPC_TREASURE[TreasureNpcDef::TREASURE_NPC_DATA_INDEX]['npc_boat_ids'][$cur_ids_index])?btstore_get()->NPC_TREASURE[TreasureNpcDef::TREASURE_NPC_DATA_INDEX]['npc_boat_ids'][$cur_ids_index]:array();
		
		$max_boats_cnt = btstore_get()->NPC_TREASURE[TreasureNpcDef::TREASURE_NPC_DATA_INDEX]['npc_boat_rand_cnt'];
		$ids_res = array();
		
		// 随机出npc船ID
		$rand_pool = array();
		$rand_base = 0;
		foreach($ids as $boat_id)
		{
			$rand_pool[$boat_id] = $rand_base + btstore_get()->NPC_BOAT[$boat_id]['npc_boat_army_weights'];
			$rand_base = $rand_pool[$boat_id];
		}
		
		while($max_boats_cnt > 0)
		{
			$rd = rand(0,$rand_base);
			foreach($rand_pool as $bt_id => $bt_wt)
			{
				if($rd < $bt_wt)
				{
					if(!in_array($bt_id,$ids_res))
					{
						array_push($ids_res,$bt_id);
						$max_boats_cnt--;
						break;
					}
				}	
			}
		}
		
		return $ids_res;
	}
	
	
	
	/**
	 * 取服务器前$player_cnt的平均级别
	 * @param $player_cnt 	样本区间
	 * @return unknown_type
	 */
	static function getServerTopNLvl($player_cnt)
	{
		$lvl_info = TreasureNpcDao::getServerTopNLvl($player_cnt);	
		if(empty($lvl_info))
		{
			return 0;
		}
		
		$lvl_cnt = count($lvl_info);
		return array_sum($lvl_info)/$lvl_cnt; 
	}
	
	
	/**
	 * 打劫NPC船
	 * @param $bt_npc_id	npc船ID
	 * @return unknown_type
	 */
	static function huntTreasureNpc($bt_npc_id)
	{
		$ret = array('ret' => 'err');
		if(empty($bt_npc_id))
		{
			Logger::warning("huntTreasureNpc boat id invalid");
			return $ret;
		}
		
		$boat_info = TreasureNpcDao::getNpcBoatInfo($bt_npc_id);
		$res = self::checkBoatNpc($boat_info);
		if($res != 'ok')
		{
			Logger::warning("checkBoatNpc failed %s",$boat_info);
			return $ret;
		}		

		// 打劫，劫财
		$ret = TreasureNpcLogic::rob($boat_info);

		return $ret;
	}
	
	
	// npc船ID
	private static $boat_npc_id;
	// 打劫奖励
	private static $robGoods;
	
	/**
	 * 打劫NPC船
	 * @param $boat_info	被劫npc船信息
	 * @return unknown_type
	 */
	static function rob($boat_info)
	{
		$arrRet = array('ret'=>'ok', 'res'=>0, 'belly'=>0, 
			'prestige'=>0, 'grid' => array());
		
		if(empty($boat_info))
		{
			$arrRet['ret'] = 'err';
			return $arrRet;
		}
		
		// 检查玩家
		$uid = RPCContext::getInstance()->getSession('global.uid');
		$res = self::checkSelfTreasureNpcInfo($uid);
		if($res['npc_rob_cnt'] <= 0)
		{
			Logger::warning("checkSelfTreasureNpcInfo(%d) failed no rob cnt",$uid);
			$arrRet['ret'] = 'err';
			return $arrRet;
		}
		
		// 检查被打劫的， 这里用lock
		$locker = new Locker();
		$lockKey = 'treasurenpc#robbed#' . $boat_info[TreasureNpcDef::TREASURE_NPC_BOAT_ID] ;
		try
		{
			$locker->lock($lockKey);
		}
		catch (Exception $e) 
		{
			Logger::warning('lock exception. exception msg:%s', $e->getMessage());
			$arrRet['ret'] = 'lock';
			return $arrRet;
		}
		
		// 战斗
		try 
		{
			$atkRet = self::attack($boat_info[TreasureNpcDef::TREASURE_NPC_BOAT_ID]);
			
			if(is_string($atkRet) && $atkRet == 'hp')
			{
				$arrRet['ret'] = $atkRet;
				$locker->unlock($lockKey);
				return $arrRet;
			}
			
			// 刷新该船被劫次数
			self::decreaseRobCnt($boat_info);
		}
		catch (Exception $e) 
		{
			$locker->unlock($lockKey);
			throw $e;
		}
		$locker->unlock($lockKey);
		
		$cur_rob_cnt = $res['npc_rob_cnt']-1;
		EnTreasure::decreaseNpcBoatCnt($uid,$cur_rob_cnt);
		
			// 将战斗结果返回给前端
		return array('ret' => 'ok',
		             'cd' => $atkRet['cd'],
					 'belly' => self::$robGoods['belly'],
					 'prestige' => self::$robGoods['prestige'],
					 'grid' => self::$robGoods['grid'],
					 'reward' => $atkRet['server']['reward'],
		             'appraisal' => BattleDef::$APPRAISAL[$atkRet['server']['appraisal']],
					 'fightRet' => $atkRet['client']);
	}

	/**
	 * 开打
	 * @param $uid	玩家ID
	 * @param $bt_npc_id npc船ID
	 * @return unknown_type
	 */
	static function attack($bt_npc_id)
	{
		
		$enemyID = self::getTreasureEnemyID($bt_npc_id);
		if(empty($enemyID))
		{
			Logger::fatal('The boat_npc_id %d has no amry',$bt_npc_id);
			throw new Exception('fake');
		}
		
		if (!isset(btstore_get()->ARMY[$enemyID]))
		{
			Logger::fatal('The %d enemy has no data!', $enemyID);
			throw new Exception('fake');
		}
		
		
		self::$boat_npc_id = $bt_npc_id;
		
		// 获取怪物小队ID
		$teamID = btstore_get()->ARMY[$enemyID]['monster_list_id'];
		// 检查部队类型
		$armyType = btstore_get()->ARMY[$enemyID]['army_type'];
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 获取用户ID
		$uid = $user->getUid();
		
		
		if ($armyType == CopyConf::ARMY_TYPE_NML)
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
		$formationRet = EnFormation::checkUserFormation($uid, $userFormation, $armyType);
		if ($formationRet != 'ok')
		{
			if ($formationRet == 'not_enough_hp')
			{
				return 'hp';
			}
			Logger::fatal('Can not attack, checkUserFormation error, uid is %d,enemyid is %d.', 
			              $uid, $enemyID);
			throw new Exception('fake');
		}
		
		// 将对象转化为数组
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation);
		Logger::debug('The hero list is %s', $userFormationArr);
		$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation);
		Logger::debug('The boss list is %s', $enemyFormationArr);
		
		
		// 查看是否是强制回合
		$battleType = intval(btstore_get()->ARMY[$enemyID]['battle_type']) == CopyDef::FORCE_ROUND ? 
		                                               CopyDef::FORCE_ROUND : CopyDef::NORMAL_ROUND;
		                                               

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
		                      array("TreasureNpcLogic", "calculateFightRet"),
		                      null, 
		                      array('bgid' => intval(btstore_get()->ARMY[$enemyID]['background_id']),
		                            'musicId' => btstore_get()->ARMY[$enemyID]['music_path'],
		                            'type' => BattleType::TREASURE));
		// 战斗系统返回值
		Logger::debug('Ret from battle is %s.', $atkRet);                                               

	
		// 获胜
		if (BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'])
		{
			// 奖励的数据可以从$atkRet ['server'] ['reward']得到，这里
			RPCContext::getInstance()->sendFilterMessage('treasure', TreasureDef::TREASURE_ID, 'reRobNpcBoatMsg',
														 array($uid, $user->getUname(),
																$bt_npc_id, self::$robGoods['belly'], 
															   self::$robGoods['prestige']));
			
		}
		
		
		
		$atkRet['cd'] = $user->getFightCDTime();
		return $atkRet;
	
	}
	
	
	/**
	 * 处理战斗结果
	 * @param $atkRet
	 * @return unknown_type
	 */
	static function calculateFightRet($atkRet)
	{
		$arrRet = array('belly'=>0, 'prestige'=>0);
		$isSuc = BattleDef::$APPRAISAL[$atkRet['appraisal']] <= BattleDef::$APPRAISAL['D'];
		
		$user = EnUser::getUserObj();
		
		// 打赢了
		if ($isSuc)
		{	
			$boatInfo = btstore_get()->NPC_BOAT[self::$boat_npc_id];
		
			//得到收益
			self::$robGoods['belly'] = $boatInfo[TreasureNpcDef::TREASURE_NPC_REWARDS_SUCC][TreasureNpcDef::TREASURE_NPC_REWARDS_BELLY];
			self::$robGoods['prestige'] = $boatInfo[TreasureNpcDef::TREASURE_NPC_REWARDS_SUCC][TreasureNpcDef::TREASURE_NPC_REWARDS_PRESTIGE];

			$arrRet['belly'] = self::$robGoods['belly'];
			$arrRet['prestige'] = self::$robGoods['prestige'];
			
			self::$robGoods['grid'] = array();
			$dropId = isset($boatInfo[TreasureNpcDef::TREASURE_NPC_REWARDS_SUCC][TreasureNpcDef::TREASURE_NPC_REWARDS_DROP])?$boatInfo[TreasureNpcDef::TREASURE_NPC_REWARDS_SUCC][TreasureNpcDef::TREASURE_NPC_REWARDS_DROP]:0;
			if($dropId > 0)
			{
				$itemMgr = ItemManager::getInstance();
				$arrItems = $itemMgr->dropItem($dropId);
				if (empty($arrItems))
				{
					return $arrRet;
				}
				$tmpItem = ChatTemplate::prepareItem($arrItems);		
				
				$bag = BagManager::getInstance()->getBag();
				$bag->addItems($arrItems, true);
				ChatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $tmpItem);
				
				self::$robGoods['grid'] = $bag->update();
				//$arrRet['grid'] = self::$robGoods['grid'];
			}
			
		}else
		{
			
			// todo  由于没有配置失败所得表
			
			$user = EnUser::getUserObj();
			$boatInfo = btstore_get()->NPC_BOAT[self::$boat_npc_id];
		
			//得到收益
			self::$robGoods['belly'] = $boatInfo[TreasureNpcDef::TREASURE_NPC_REWARDS_FAIL][TreasureNpcDef::TREASURE_NPC_REWARDS_BELLY];
			self::$robGoods['prestige'] = $boatInfo[TreasureNpcDef::TREASURE_NPC_REWARDS_FAIL][TreasureNpcDef::TREASURE_NPC_REWARDS_PRESTIGE];

			$arrRet['belly'] = self::$robGoods['belly'];
			$arrRet['prestige'] = self::$robGoods['prestige'];

			self::$robGoods['grid'] = array();
			$dropId = isset($boatInfo[TreasureNpcDef::TREASURE_NPC_REWARDS_FAIL][TreasureNpcDef::TREASURE_NPC_REWARDS_DROP])?$boatInfo[TreasureNpcDef::TREASURE_NPC_REWARDS_FAIL][TreasureNpcDef::TREASURE_NPC_REWARDS_DROP]:0;
			if($dropId > 0)
			{
				$itemMgr = ItemManager::getInstance();
				$arrItems = $itemMgr->dropItem($dropId);
				if (empty($arrItems))
				{
					return $arrRet;
				}
				$tmpItem = ChatTemplate::prepareItem($arrItems);		
				
				$bag = BagManager::getInstance()->getBag();
				$bag->addItems($arrItems, true);
				ChatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $tmpItem);
				
				self::$robGoods['grid'] = $bag->update();
				//$arrRet['grid'] = self::$robGoods['grid'];
			}
		}
		
		if(self::$robGoods['belly'] > 0)
			$user->addBelly(self::$robGoods['belly']);
		if(self::$robGoods['prestige'] > 0)
			$user->addPrestige(self::$robGoods['prestige']);
		
		$user->update();	
		return $arrRet;
	}
	
	
	/**
	 * 减少该npc船的被劫次数
	 * @param $bt_id  被劫的npc船信息
	 * @return unknown_type
	 */
	static function decreaseRobCnt($bt_info)
	{
		$left_cnt = ($bt_info[TreasureNpcDef::TREASURE_NPC_ROB_LEFT_CNT] - 1) >= 0 ?($bt_info[TreasureNpcDef::TREASURE_NPC_ROB_LEFT_CNT] - 1):0 ;
		$arrField = array();
		if($left_cnt > 0)
		{
			$arrField = array(TreasureNpcDef::TREASURE_NPC_ROB_LEFT_CNT => $left_cnt);
		}else
		{
			$arrField = array(	TreasureNpcDef::TREASURE_NPC_ROB_LEFT_CNT => $left_cnt,
								TreasureNpcDef::TREASURE_NPC_STATUS => TreasureNpcDef::TREASURE_NPC_BOAT_STATUS_FAIL);
		}
		TreasureNpcDao::updateNpcBoatInfo($bt_info[TreasureNpcDef::TREASURE_NPC_BOAT_ID],$arrField);
		
		// 客户端没有保存以前的时间数据，要我们推
		$new_boat_info = $bt_info;
		$new_boat_info[TreasureNpcDef::TREASURE_NPC_ROB_LEFT_CNT] = $left_cnt;
		
		Logger::debug("decreaseRobCnt invoke %s",$new_boat_info);
		// 通知城镇的强盗们
		$arrRet['npc_boat'] = array();
		$arrRet['npc_boat'][] = $new_boat_info;
		RPCContext::getInstance()->sendFilterMessage('treasure',
						TreasureNpcDef::TREASURE_NPC_SENDTEMPLATEID,'updateNpcBoat',
						$arrRet);
		
	}
	

	/**
	 * 随机部队ID
	 * @param $boat_npc_id	船ID
	 * @return unknown_type
	 */
	static function getTreasureEnemyID($boat_npc_id)
	{
		$enemyIDs = btstore_get()->NPC_BOAT[$boat_npc_id]['npc_boat_army_ids'];
		
		if(empty($enemyIDs))
		{
			return 0;
		}
		
		Logger::warning("enemyIDs:%s",$enemyIDs);
		
		$rd_index = rand(0,count($enemyIDs)-1);
		$eneID = $enemyIDs[$rd_index];
		
		return $eneID;
	}
	
	
	
	/**
	 * 检查自己的npc船打劫次数
	 * @return unknown_type
	 */
	static function checkSelfTreasureNpcInfo($uid)
	{
		if(empty($uid))
		{
			return array();
		}
		
		$ret = EnTreasure::getTreasureInfo($uid);
		if(empty($ret))
		{
			Logger::warning("user %d can't get treasure info",$uid);
			return $ret;
		}
		
		return $ret;
	}
	
	/**
	 * 检查npc船的状态
	 * @param $boat_info
	 * @return unknown_type
	 */
	static function checkBoatNpc($boat_info,$strictlyCheck = false)
	{
		$ret = 'err';
		if(empty($boat_info))
		{
			return $ret;
		}
		
		// 状态检查
		if($boat_info[TreasureNpcDef::TREASURE_NPC_STATUS] == 0)
		{
			Logger::warning("boat id %d status is not active",$boat_info[TreasureNpcDef::TREASURE_NPC_BOAT_ID]);
			return $ret;
		}
		
		// 时间检查，是不是有效船只
		$cur_time = Util::getTime();
		if($boat_info[TreasureNpcDef::TREASURE_NPC_BOAT_END_TIME] < $cur_time )
		{
			Logger::debug("boat id %d is expired",$boat_info[TreasureNpcDef::TREASURE_NPC_BOAT_ID]);
			return $ret;
		}
		
		// 是不是还没开始，没开始不要给客户端了
		if($strictlyCheck)
		{
			if($boat_info[TreasureNpcDef::TREASURE_NPC_BOAT_BEGIN_TIME] > $cur_time)
			{
				Logger::warning("boat id %d return time is not coming",$boat_info[TreasureNpcDef::TREASURE_NPC_BOAT_ID]);
				return $ret;
			}
		}
	
		
		// 次数检查
		if($boat_info[TreasureNpcDef::TREASURE_NPC_ROB_LEFT_CNT] <= 0)
		{
			Logger::warning("boat id %d has no rob cnt",$boat_info[TreasureNpcDef::TREASURE_NPC_BOAT_ID]);
			return $ret;
		}
		
		$ret = 'ok';
		return $ret;
	}

}