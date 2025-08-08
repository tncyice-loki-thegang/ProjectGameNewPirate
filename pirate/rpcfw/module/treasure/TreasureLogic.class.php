<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TreasureLogic.class.php 40236 2013-03-07 08:01:32Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasure/TreasureLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-07 16:01:32 +0800 (四, 2013-03-07) $
 * @version $Revision: 40236 $
 * @brief 
 *  
 **/






class TreasureLogic
{
	private static $AllField = array(
		'uid',
		'cur_treasure_level',
		'gold_refresh_num',
		'gold_refresh_time',
		'refresh_time',
		'experience_refresh_num',
		'hunt_num',
		'hunt_aviable_num',
		'return_begin_time',
		'return_end_time',
		'return_tid',
		'using_map_id',
		'be_robbed_num',
		'sub_profit',
		'rob_cdtime',
		'rob_time',
		'rob_num',
		'npc_rob_cnt',
		'npc_rob_time',
		'treasure_auto_type',
		'treasure_auto_begin_time',
		'va_treasure');	
	
	/**
	 * tid 等于0 表示已经返航成功，并且发过奖励
	 * Enter description here ...
	 * @param unknown_type $info
	 */
	public static function isReturn($info)
	{
		return $info['return_tid'] == 0; 
	}
	
	public static function getInfo($uid)
	{
		$user = EnUser::getUserObj($uid);
		if(empty($user))
		{
			Logger::warning('fail to get user info by id %d', $uid);
			return array();
		}
		
		$max_cnt = self::getTreasureCntByLvl($user->getLevel());
		
		$info = TreasureDao::getByUid($uid, self::$AllField);
		if (empty($info))
		{
			self::insertDefault($uid,$max_cnt);
			$info = TreasureDao::getByUid($uid, self::$AllField);
		}
		
		// 每天四点重置为第一张图，只有刷新能出下一张图，所以可以认为刷新时间不是今天，目前就是第一张图
		if (!Util::isSameDay($info['refresh_time']))
		{
			foreach($info['va_treasure']['line'] as &$line)
			{
				$line['cur_pos'] = 0;
			}
			unset($line);
			$info['experience_refresh_num'] = 0;
		}
	
		if (!Util::isSameDay($info['rob_time']))
		{
			$info['rob_num'] = 0;
		}
		
		if(!Util::isSameDay($info['npc_rob_time']))
		{
			$info['npc_rob_cnt'] = isset(btstore_get()->NPC_TREASURE[TreasureNpcDef::TREASURE_NPC_DATA_INDEX]['npc_boat_rob_cnt_max'])?
				btstore_get()->NPC_TREASURE[TreasureNpcDef::TREASURE_NPC_DATA_INDEX]['npc_boat_rob_cnt_max']:TreasureConf::NPC_BOAT_ROB_CNT;
		}

		//返航开始时间不是今天，把寻宝次数重置为0
//		if (!Util::isSameDay($info['return_begin_time']))
//		{
//			$info['hunt_num'] = 0;
//		}
		
		$info['hunt_aviable_num'] = self::calcAccumulatedTreasureCnt($info,$max_cnt);
		
		/**
		 * 返航成功会重置为0
		if (Util::getTime() > $info['return_end_time'])
		{
			$info['be_robbed_num'] = 0;
		}
		*/

		if (!Util::isSameDay($info['gold_refresh_time']))
		{
			$info['gold_refresh_num'] = 0;
		}

		$info['line'] = $info['va_treasure']['line'];
		
		if (!isset($info['va_treasure']['red_score']))
		{
			$info['va_treasure']['red_score'] = 0;
			$info['va_treasure']['purple_score'] = 0;	
		}
		return $info;		
	}
	
	

	/**
	 * 获取累计寻宝次数
	 * @param $info 寻宝信息
	 * @param $huntNum 该等级下寻宝上限
	 * @return 返回有效次数
	 */
	protected static function calcAccumulatedTreasureCnt($info,$huntNum)
	{
		// 可用次数
		$old_avi_cnt = 0;
		
		if($info['hunt_num'] > 0)
		{
			$old_avi_cnt = $huntNum > $info['hunt_num']?($huntNum - $info['hunt_num']) : 0;
			//TreasureDao::update($info['uid'], array('hunt_num'=>0));
		}
		
		if(empty($info['hunt_aviable_num']))
		{
			$info['hunt_aviable_num'] = 0;
		}
		$avi_cnt = $info['hunt_aviable_num'] + $old_avi_cnt;
		
		// 距离上次寻宝的天数
		$days = Util::getDaysBetween($info['return_begin_time']);
		
		$maxcnt = btstore_get()->TOP_LIMIT[TopLimitDef::TREASURE_MAX_TIME];
		
		// 累计次数吧
		for($i = 1; $i <= $days ; $i++)
		{
			$avi_cnt += $huntNum;
			
			if($avi_cnt > $maxcnt)
			{
				$avi_cnt = $maxcnt;
				break;
			}
		}
		
		return $avi_cnt;
	} 
	
	
	/**
	 * 获取寻宝次数
	 * @param $lvl 等级
	 * @return 该等级对应的寻宝次数
	 */
	static function getTreasureCntByLvl($lvl)
	{
		$canHuntNum = 0;
		foreach (TreasureConf::$LV_REFRESH_TREASURE as $needLevel => $arr)
		{
			$canHuntNum = $arr[1];
			if ($needLevel > $lvl)
			{
				break;
			}
		}
		
		return $canHuntNum;
	}
	
	

	private static function insertDefault($uid,$init_cnt)
	{
		$va_treasure = array(
			'red_score' => 0,
			'purple_score' => 0,
			'line' => array(
				1 => array(
					'cur_pos' => 0,
					),
				),
			);
		$va_treasure['line'][2] = $va_treasure['line'][1];
		
		
		$arrField = array(
			'cur_treasure_level' => 1,
			'hunt_num' => 0,
			'hunt_aviable_num' => $init_cnt,
			'gold_refresh_num'=>0,
			'gold_refresh_time'=>Util::getTime(),
			'experience_refresh_num' =>0,
			'refresh_time' => Util::getTime(),
			'return_begin_time' => Util::getTime(),
			'return_end_time' => 0,
			'using_map_id' => 0,
			'return_tid' => 0,
			'be_robbed_num' => 0,
			'sub_profit' => 0,
			'rob_cdtime' => 0,
			'rob_time' => 0,
			'rob_num' => 0,
			'npc_rob_cnt' => 0,
			'npc_rob_time' => 0,
			'va_treasure' => $va_treasure);
		TreasureDao::insert($uid, $arrField);
	}
	
	public static function refresh($uid, $line)
	{
		$needGold = -1;
		$openNext = 0;
		if ($line!=1 && $line!=2)
		{
			Logger::warning('fail to refresh, the argv line %d error ', $line);
			throw new Exception('fake');			
		}
		
		$info = self::getInfo($uid);
		//返航的时候不能刷新
		if (!self::isReturn($info))
		{
			Logger::warning('fail to refresh, return is not end');
			throw new Exception('fake');
		}

		$user = EnUser::getUserObj($uid);
		$level = $user->getLevel();

		$infoLine = $info['line'][$line];		
		$curPos = $infoLine['cur_pos'];
		$tsuLevel = btstore_get()->TREASURE_LEVEL[$info['cur_treasure_level']];

		// 下一张图不存在
		if (!isset($tsuLevel['line'][$line][$curPos + 1]))
		{
			Logger::warning('fail to refresh, next map is not exist');
			throw new Exception('fake');
		}
		$nextMapId = $tsuLevel['line'][$line][$curPos + 1];
		
		// 开下一张图等级不够
		if (btstore_get()->TREASURE[$nextMapId]['need_level'] > $level)
		{
			Logger::warning('fail to refresh, level is not enough');
			throw new Exception('fake');
		}

		$arrUpdateField = array('refresh_time'=>Util::getTime());
		$experieceRefreshNum = self::getExperienceRefresh($level);
		//阅历刷新
		if ($info['experience_refresh_num'] < $experieceRefreshNum)
		{
			if (!$user->subExperience(btstore_get()->TREASURE[$nextMapId]['refreshCostExperience']))
			{
				Logger::warning('fail to refresh, fail to sub experience');
				throw new Exception('fake');
			}
			$arrUpdateField['experience_refresh_num'] = $info['experience_refresh_num'] + 1;
		}
		//金币刷新
		else
		{
			$goldRefreshNum = btstore_get()->VIP[$user->getVip()]['treasure_refresh_gold']['times'];
			if ($goldRefreshNum <= $info['gold_refresh_num'])
			{
				Logger::warning('fail to refresh, the num is to max');
				throw new Exception('fake');
			}

			$needGold = btstore_get()->VIP[$user->getVip()]['treasure_refresh_gold']['gold'];
			if (!$user->subGold($needGold))
			{
				Logger::warning('fail to refresh, the gold is not enough');
				throw new Exception('fake');
			}
			$arrUpdateField['gold_refresh_time'] = Util::getTime();
			$arrUpdateField['gold_refresh_num'] = $info['gold_refresh_num'] + 1;
		}		

		$newInfoLine = array(
			'cur_pos' => $curPos,
			);

		$rand = rand(1,10000);
		//刷出新图
		if ($rand <= btstore_get()->TREASURE[$nextMapId]['rate'])
		{
			$newInfoLine['cur_pos'] = $curPos+1;			
			$openNext = 1;			
		}
		$arrUpdateField['va_treasure'] = $info['va_treasure'];
		foreach ($arrUpdateField['va_treasure']['line'] as &$tmpLine)
		{
			$tmpLine['cur_pos'] = -1;
		}
		unset($tmpLine);
		$arrUpdateField['va_treasure']['line'][$line] = $newInfoLine;
		TreasureDao::update($uid, $arrUpdateField);
		$user->update();
		
		//成就
		if ($openNext == 1)
		{
			EnAchievements::notify($uid, 
				AchievementsDef::TREASURE_QUALITY, 
				btstore_get()->TREASURE[$nextMapId]['quality']);
		}
		
		if ($needGold!=-1)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_TREASURE_REFRESH, $needGold, Util::getTime());
		}
		return $openNext;
	}

	// 根据等级返回阅历刷新次数
	public static function getExperienceRefresh($level)
	{
		$num = 0;
		// 等级 刷新次数
		foreach (TreasureConf::$LV_REFRESH_TREASURE as $needLevel => $arr)
		{
			$num = $arr[0];
			if ($needLevel > $level)
			{
				break;
			}
		}
		return $num;
	}

	public static function hunt($uid, $line, $pos)
	{
		$info = self::getInfo($uid);
		if ($pos !=$info['line'][$line]['cur_pos'] )
		{
			Logger::warning('fail to hunt, pos value %d err.', $pos);
			throw new Exception('fake');
		}

		// 上次返航没有结束
		if (!self::isReturn($info))
		{
			Logger::warning('fail to hunt, last hunt is not end.');
			throw new Exception('fake');
		}
		
		//$level = EnUser::getUserObj($uid)->getLevel();
		//$max_cnt = self::getTreasureCntByLvl($level);		

		//$canHuntNum = self::calcAccumulatedTreasureCnt($info,$max_cnt);
		
		// 寻宝次数不够了
		if ($info['hunt_aviable_num'] <= 0)
		{
			Logger::warning('uid:%d hunt num is not enough', $uid);
			throw new Exception('fake');
		}

		// 做下记录
		if($info['hunt_num'] > 0)
		{
			Logger::trace('hunt_num will be fixed %d', $info['hunt_num']);
		}

		$mapId = btstore_get()->TREASURE_LEVEL[$info['cur_treasure_level']]['line'][$line][$pos];
		$returnTime = Util::getTime() + TreasureConf::RETURN_COST_TIME;
		$tid = TimerTask::addTask($uid, $returnTime, 'treasure.huntReturnTimeout', array($uid, $mapId));
				
		
		$updateField = array('return_begin_time' => Util::getTime(),
							 'return_end_time' => $returnTime,							 
							 'return_tid' => $tid,
							 'refresh_time' => $info['refresh_time'],
							 'hunt_num' => 0,
							 'hunt_aviable_num' => $info['hunt_aviable_num'] - 1,
							 'using_map_id' => $mapId,);
		
		//加积分
		$quality = btstore_get()->TREASURE[$mapId]['quality'];
		if (isset(btstore_get()->SCORE_EXCHANGE["treasure_score"][$quality]))
		{
			$cfg = btstore_get()->SCORE_EXCHANGE["treasure_score"][$quality];
			$va_treasure = $info['va_treasure'];
			$va_treasure['purple_score'] += 
				floor(EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_TREASURE_PURPLESTAR) * $cfg['purple_score']);
			$va_treasure['red_score'] += 
				floor(EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_TREASURE_REDSTAR) * $cfg['red_score']);			
			
			$updateField['va_treasure'] = $va_treasure;
		}
		
		TreasureDao::update($uid, $updateField);
		
		//广播
		if ($pos >= TreasureConf::MIN_BROADCAST_POS)
		{
			$user = EnUser::getUserObj($uid);
			ChatTemplate::sendTreasureMap($user->getTemplateUserInfo(), $mapId);
		}
		
		//成就
		EnAchievements::notify($uid, AchievementsDef::TREASURE_TIMES, 1);
	}

	// 返航。给timer调用
	public static function huntReturnTimeout ($uid, $mapId)
	{
		$arrRet = array('ret'=>'fail');
		$info = self::getInfo($uid);
		//时间没到或者已经返航了
		if ($info['return_end_time'] > Util::getTime() || self::isReturn($info))
		{
			Logger::fatal('fail to huntReturn, return_end_time is no arrive or tid==0');
			return $arrRet;
		}

		if ($info['using_map_id']!=$mapId)
		{
			Logger::fatal('fail to huntReturn, using_map_id is not equal to the num from db');
			return $arrRet;
		}
		
		$arrRet = self::huntReturn($uid, $info);
		
		// 对自动寻宝的处理
		if($arrRet['ret'] == 'ok')
		{
			if($info['treasure_auto_begin_time'] != 0)
			{
				$arrRet['autoHunt'] = true;
				$arrRet['line'] = $info['treasure_auto_type'];
				// 不能继续了
				if(!TreasureAutoLogic::isAutoContious($uid,$info))
				{
					$arrRet['autoHunt'] = false;
					TreasureAutoLogic::stopAutoHunt($uid);
				}
			}
		}
		
		return $arrRet;
	}
	
	public static function huntReturnByGold($uid)
	{
		$info = self::getInfo($uid);
		$diffTime = $info['return_end_time'] - Util::getTime();
		$needGold = 0;
		if ($diffTime > 0)
		{
			$needGold = ceil($diffTime / TreasureConf::HUNT_CDTIME_PER_GOLD);
			$user = EnUser::getUserObj($uid);
			if (!$user->subGold($needGold))
			{
				Logger::warning('fail to huntReturnByGold, the gold is not enough');
				throw new Exception('fake');
			}
		}		
		
		$arrRet = self::huntReturn($uid, $info);		
		$arrRet['gold'] = 0;		
		if ($arrRet['ret'] == 'ok')
		{
			$arrRet['gold'] = $needGold;
			//取消timer
			if ($info['return_tid'] != 0)
			{
				TimerTask::cancelTask($info['return_tid']);
			}			
			Statistics::gold(StatisticsDef::ST_FUNCKEY_TREASURE_GOLD_RETURN, $needGold, Util::getTime());
			
			// 对自动寻宝的处理
			if($info['treasure_auto_begin_time'] != 0)
			{
				
				if(TreasureAutoLogic::isAutoContious($uid,$info))
				{
					$arrRet['isAutoHunt'] = true;
					TreasureAutoLogic::autoHunt($uid,$info['treasure_auto_type']);
					
				}else
				{
					TreasureAutoLogic::stopAutoHunt($uid);
				}
			}
		}		
		return $arrRet;		
	}

	private static function huntReturn($uid, $info)
	{
		$arrRet = array('ret'=>'ok', 'belly'=>0, 'prestige'=>0, 'grid'=>array());
		if ($info['using_map_id']==0)
		{
			$arrRet['ret'] = 'returned';
			return $arrRet;
		}
		
		$mapId = $info['using_map_id'];
		$belly = btstore_get()->TREASURE[$mapId]['reward_belly'];
		$prestige = btstore_get()->TREASURE[$mapId]['reward_prestige'];
		$dropId = btstore_get()->TREASURE[$mapId]['reward_droptable_id'];

		//奖励 belly presitge
		$subProfit = $info['sub_profit'];		
		$user = EnUser::getUserObj($uid);
		$arrRet['belly'] = ceil($belly * (1-$subProfit/100));
		$arrRet['prestige'] = ceil($prestige * (1-$subProfit/100));
		$user->addBelly($arrRet['belly']);
		$user->addPrestige($arrRet['prestige']);
		$user->update();

		//返航后把所有图设置为第一张
		$va_treasure = $info['va_treasure'];
		foreach ($va_treasure['line'] as &$line)
		{
			$line['cur_pos'] = 0;
		}
		unset($line);				
		
		//阅历刷新重置为0,
		$arrField = array('using_map_id' => 0,
						  'return_tid' => 0,
						 // 'hunt_num' => $info['hunt_num']+1,
						  'experience_refresh_num' => 0,
						  'refresh_time' => Util::getTime(),
						  'be_robbed_num' => '0',
						  'return_end_time' => Util::getTime(), //这里把时间更新为当前值，可能为金币返航，所以需要更新
						  'sub_profit' =>0, 'va_treasure' => $va_treasure);
		TreasureDao::update($uid, $arrField);
		
		$arrItem = ItemManager::getInstance()->dropItem($dropId);
		Logger::debug('drop item:%s', $arrItem);				

		//mail
		MailTemplate::sendTreasureReward($uid, $arrRet['belly'], $arrRet['prestige'], $arrItem, false);
		if (!empty($arrItem))
		{
			$tmpItem = ChatTemplate::prepareItem($arrItem);
			ChatTemplate::sendTreasureItem($user->getTemplateUserInfo(), $user->getGroupId(), $tmpItem);		
			$bag = BagManager::getInstance()->getBag($uid);
			$bag->addItems($arrItem, true);
			$arrRet['grid'] = $bag->update();
		}
		return $arrRet;
	}

	
	private static $uid;
	private static $robbedUid;
	private static $robGoods;
	private static $mapId;
	private static $subProfit;

	//记得unlock
	public static function rob($uid, $robbedUid)
	{
		self::$uid = $uid;
		self::$robbedUid = $robbedUid;
		self::$robGoods = array();
		self::$subProfit = 0;
		
		
		$arrRet = array('ret'=>'ok', 'res'=>0, 'belly'=>0, 'mapId'=>0, 
			'prestige'=>0, 'rob_cdtime'=>0, 'sub_profit'=>0);
		
		$info = self::getInfo($uid);
		//检查打劫者次数，
		if ($info['rob_num'] >= TreasureConf::ROB_NUM )
		{
			Logger::warning('fail to rob, the num is max');
			throw new Exception('fake');
		}

		//check cdtime
		if ($info['rob_cdtime'] > Util::getTime())
		{
			Logger::warning('fail to rob, cdtime is not reach');
			throw new Exception('fake');
		}		

		// 检查被打劫的， 这里用lock
		$locker = new Locker();
		$lockKey = 'treasure#robbed#' . $robbedUid ;
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

		//被打劫者到了最大值
		$robbedInfo = self::getInfo($robbedUid);
		if ($robbedInfo['be_robbed_num'] >= TreasureConf::RETURN_ROBBED_NUM) 
		{
			Logger::debug('fail to rob, the robbed uid is max num');
			$arrRet['ret'] = 'max';
			$locker->unlock($lockKey);
			return $arrRet;
		}
		
		//被打劫已经返航
		if (Util::getTime() >= $robbedInfo['return_end_time'] )
		{
			$arrRet['ret'] = 'nothing';			
			return $arrRet;			
		}

		$mapId = $robbedInfo['using_map_id'];
		self::$mapId = $mapId;

		// 战斗
		try 
		{
			$atkRet = self::attack($uid, $robbedUid);
		}
		catch (Exception $e) 
		{
			$locker->unlock($lockKey);
			throw $e;
		}
		
		$arrRet['fightRet'] = $atkRet['client'];		
		$arrRet['mapId'] = $mapId;
		$user = EnUser::getUserObj($uid);
		$robbedUser = EnUser::getUserObj($robbedUid);
		$robNum = $info['rob_num'];

		$isSuccess = BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'];
		// 打赢了
		if ($isSuccess)
		{
			$arrRet['res'] = 1;
			
			$updateRobbed = array('be_robbed_num' => $robbedInfo['be_robbed_num'] + 1,
				'sub_profit' => $robbedInfo['sub_profit'] + self::$subProfit,				
				);
				
			TreasureDao::updateRobbed($robbedUid, $updateRobbed);			
			$locker->unlock($lockKey);
			
			$arrRet['sub_profit'] = $updateRobbed['sub_profit'];

			//得到收益
			$arrRet['belly'] = self::$robGoods['belly'];
			$arrRet['prestige'] = self::$robGoods['prestige'];
			$user->addBelly($arrRet['belly']);
			$user->addPrestige($arrRet['prestige']);
			$user->update();
			$robNum +=1;						
		}
		else
		{
			$locker->unlock($lockKey);
		}
		
		//增加cdtime, 打劫次数
		$updateRob = array('rob_num'=>$robNum, 
				'rob_time'=>Util::getTime(), 
				'rob_cdtime'=>Util::getTime() + TreasureConf::ROB_CDTIME_ADD);
		TreasureDao::update($uid, $updateRob);
		$arrRet['rob_cdtime'] = $updateRob['rob_cdtime'];
		
		$robbedForMail = array('uid'=>$robbedUid,
							   'uname'=>$robbedUser->getUname(),
							   'utid'=>$robbedUser->getUtid());
		//发送邮件
		MailTemplate::sendTreasureAttack($uid,
										 $mapId,
										 $robbedForMail,
										 $arrRet['belly'],
										 $arrRet['prestige'],
										 $atkRet['server']['brid'],
										 $isSuccess);
			
		$robForMail = array('uid'=>$uid,
							'uname'=>$user->getUname(),
							'utid'=>$user->getUtid());
		MailTemplate::sendTreasureDefend($robbedUid,
										 $robForMail,
										 $arrRet['belly'],
										 $arrRet['prestige'],
										 $atkRet['server']['brid'],
										 !$isSuccess);
				
		return $arrRet;
	}

	private static function attack($uid, $atkedUid)
	{
		
		$user = EnUser::getUserObj($uid);
		$battleInfo = $user->getBattleInfo(true);
		
		$atkedUser = EnUser::getUserObj($atkedUid);
		$atkedBattleInfo = $atkedUser->getBattleInfo();
				
		$battleUser = array('name' => $user->getUname(),
                             'level' => $user->getLevel(),
                             'flag' => 0,
							'isPlayer' => true,
                             'formation' =>  $user->getCurFormation(),
                             'uid' => $uid,
                             'arrHero' => $battleInfo['info']);
	
		$battleAtkedUser = array('name' => $atkedUser->getUname(), 
			'level' => $atkedUser->getLevel(), 
			'flag' => 0, 
			'isPlayer' => true,
			'formation' => $atkedUser->getCurFormation(), 
			'uid' => $atkedUid, 
			'arrHero' => $atkedBattleInfo['info']);
		
		$bt = new Battle();
		$atkRet = $bt->doHero($battleUser, $battleAtkedUser, 0, array('TreasureLogic', 'battleCallback'),
			null, array('bgid'=>TreasureConf::BATTLE_BJID, 'musicId'=>ArenaConf::BATTLE_MUSIC_ID, 'type'=>BattleType::TREASURE));
		// 战斗系统返回值
		Logger::debug('Ret from battle is %s.', $atkRet);

		return $atkRet;
	}
	
	public static function battleCallback($atkServer)
	{
		$arrRet = array('belly'=>0, 'prestige'=>0);
		$isSuc = BattleDef::$APPRAISAL[$atkServer['appraisal']] <= BattleDef::$APPRAISAL['D'];
		// 打赢了
		if ($isSuc)
		{
			$user = EnUser::getUserObj(self::$uid);
			$robbedUser = EnUser::getUserObj(self::$robbedUid);
			$diffLevel = $user->getLevel() - $robbedUser->getLevel();
			if ($diffLevel <= 0)
			{
				self::$subProfit = TreasureConf::ROB_MAX_PROFIT;
			}
			else
			{
				self::$subProfit = TreasureConf::ROB_MAX_PROFIT - floor(($diffLevel - 1) / 5);
				if (self::$subProfit < TreasureConf::ROB_MIN_PROFIT)
				{
					self::$subProfit = TreasureConf::ROB_MIN_PROFIT;
				}
			}

			//得到收益
			self::$robGoods['belly'] = floor(btstore_get()->TREASURE[self::$mapId]['reward_belly'] /100 * self::$subProfit);
			self::$robGoods['prestige'] = floor(btstore_get()->TREASURE[self::$mapId]['reward_prestige'] / 100 * self::$subProfit);

			$arrRet['belly'] = self::$robGoods['belly'];
			$arrRet['prestige'] = self::$robGoods['prestige'];
		}
		return $arrRet;		
	}

	public static function clearRobCdtime($uid)
	{
		$arrRet = array('ret'=>'ok', 'gold'=>0);
		$info = self::getInfo($uid);
		$curTime = Util::getTime();
		$diff = $info['rob_cdtime'] - $curTime;
		$needGold = 0;		
		if ($diff > 0)
		{
			$needGold = ceil($diff / TreasureConf::ROB_CDTIME_PER_GOLD);
		}
		$user = EnUser::getUserObj($uid);
		if (!$user->subGold($needGold))
		{
			Logger::warning('fail to clear rob_cdtime, gold is not enough');
			throw new Exception('fake');
		}
		$user->update();
		
		$arrField = array('rob_cdtime' => $curTime);
		TreasureDao::update($uid, $arrField);
		$arrRet['gold'] = $needGold;
		
		Statistics::gold(StatisticsDef::ST_FUNCKEY_TREASURE_ROB_CDTIME, $needGold, Util::getTime());
		
		return $arrRet;
	}
	
	private static function getMultiGuildAll($arrGuild, $arrField)
	{
		if (empty($arrGuild))
		{
			return array();
		}
		
		$arrGuild =  array_unique($arrGuild);
		$arrArrGuild = array_chunk($arrGuild, CData::MAX_FETCH_SIZE);
		
		$arrRet = array();
		foreach ($arrArrGuild as $arrGuild)
		{
			$ret = GuildLogic::getMultiGuild($arrGuild, $arrField);	
			$arrRet += $ret;			
		}
		return $arrRet;			
	}
	
	public static function getArrUserAll($arrUid, $arrField)
	{
		if (empty($arrUid))
		{
			return array();
		}
		$arrArrUid = array_chunk($arrUid, CData::MAX_FETCH_SIZE);		
		
		$arrRet = array();
		foreach ($arrArrUid as $arrUid)
		{
			$ret = Util::getArrUser($arrUid, $arrField);
			$arrRet += $ret;			
		}
		return $arrRet;		
	}
	
	public static function getReturnScene()
	{
		$arrField = array('uid', 'using_map_id',
						  'return_begin_time', 'return_end_time',
						  'be_robbed_num', 'sub_profit' );
		$arrRet = TreasureDao::getNotReturn($arrField);
		$arrUid = Util::arrayExtract($arrRet, 'uid');		
		$arrUserInfo = self::getArrUserAll($arrUid, array('utid', 'uname', 'level', 'guild_id'));
		$arrGuild = Util::arrayExtract($arrUserInfo, 'guild_id');
		$arrGuildName = self::getMultiGuildAll($arrGuild, array('name'));		
		foreach ($arrRet as &$ret)
		{
			$ret += $arrUserInfo[$ret['uid']];
			$ret['guild_name'] = '';
			if (isset($arrGuildName[$ret['guild_id']]))
			{
				$ret['guild_name'] = $arrGuildName[$ret['guild_id']]['name'];	
			}
		}
		
		return $arrRet;
	}
	
	/**
	 * 在用户读Treasure前修复。
	 * 放getUser里面
	 * Enter description here ...
	 */
	public static function fixTreasure($uid)
	{
		$ret = TreasureDao::getByUid($uid, array('return_end_time', 'return_tid'));
		if (empty($ret))
		{
			return;
		}	

		if ($ret['return_tid'] != 0 && $ret['return_end_time'] < Util::getTime())
		{
			try
			{
				//huntReturnTimeout
				if (!EnTimer::checkTask($ret['return_tid'], 'treasure.huntReturnTimeout'))
				{
					EnTimer::resetTask($ret['return_tid']);
				}
			}
			catch (Exception $e)
			{
				//nothing
			}
		}
	}
	
	public static function openMapByGold ($line, $pos)
	{
		$user = EnUser::getUserObj();
		$vip = $user->getVip();		
		$needGold = btstore_get()->VIP[$vip]['treasure_open_gold'];
		if ($needGold==0)
		{
			Logger::warning('fail to open map, vip level is err');
			throw new Exception('fake');
		}
		
		if ($line!=1 && $line!=2)
		{
			Logger::warning('fail to open map by gold, the argv line %d error ', $line);
			throw new Exception('fake');			
		}
		
		if ($pos!=TreasureConf::OPEN_MAP_POS)
		{
			Logger::warning('fail to open map by gold, argv pos%d err', $pos);
			throw new Exception('fake');
		}
			
		$uid = RPCContext::getInstance()->getUid();
		
		$info = self::getInfo($uid);
		//返航的时候不能开藏宝图
		if (!self::isReturn($info))
		{
			Logger::warning('fail to open map, return is not end');
			throw new Exception('fake');
		}
		
		
		$infoLine = $info['line'][$line];		
		$curPos = $infoLine['cur_pos'];
		$tsuLevel = btstore_get()->TREASURE_LEVEL[$info['cur_treasure_level']];
		
		//已开地图，或者等级更高
		if ($curPos >= $pos)
		{
			Logger::warning('fail to open map, cur map level is more than pos:%d', $pos);
			throw new Exception('fake');
		}
		
		// 等级不够
		$nextMapId = $tsuLevel['line'][$line][TreasureConf::OPEN_MAP_POS];
		$level = $user->getLevel();
		if (btstore_get()->TREASURE[$nextMapId]['need_level'] > $level)
		{
			Logger::warning('fail to refresh, level is not enough');
			throw new Exception('fake');
		}
		
		if (!$user->subGold($needGold))
		{
			Logger::warning('fail to open map, gold is not enough');
			throw new Exception('fake');
		}
		
		$arrUpdateField = array('refresh_time'=>Util::getTime());
		$newInfoLine = array(
			'cur_pos' => TreasureConf::OPEN_MAP_POS,
			);
		$arrUpdateField['va_treasure'] = $info['va_treasure'];
		foreach ($arrUpdateField['va_treasure']['line'] as &$tmpLine)
		{
			$tmpLine['cur_pos'] = -1;
		}
		unset($tmpLine);
		$arrUpdateField['va_treasure']['line'][$line] = $newInfoLine;
		TreasureDao::update($uid, $arrUpdateField);
		$user->update();
		
		//成就		
		EnAchievements::notify($uid, 
			AchievementsDef::TREASURE_QUALITY, 
			btstore_get()->TREASURE[$nextMapId]['quality']);

		
		Statistics::gold(StatisticsDef::ST_FUNCKEY_TREASURE_OPEN_MAP, $needGold, Util::getTime());
		
	}
	
	public static function exchangeItemWithScore($itemTplId)
	{
		$arrRet = array('ret'=>'ok', 'grid'=>array());
		
		if (!isset(btstore_get()->SCORE_EXCHANGE['treasure_exchange'][$itemTplId]))
		{
			Logger::warning('fail to exchagne item witt score, item %d config is not found', $itemTplId);
			throw new Exception('fake');
		}
		
		$cfg = btstore_get()->SCORE_EXCHANGE['treasure_exchange'][$itemTplId];
		$type = $cfg['type'];
		$score = $cfg['score'];
		
		$uid = RPCContext::getInstance()->getUid();
		$info = self::getInfo($uid);
		$vaTreasure = $info['va_treasure'];
		if ($vaTreasure[$type . '_score'] < $score)
		{
			Logger::warning('fail to exchange item with score, score %d is not enough', $info['va_treasure'][$type . '_score']);
			throw new Exception('fake');
		}
				
		$vaTreasure[$type . '_score'] -= $score;
		
		$arrItem = ItemManager::getInstance()->addItem($itemTplId);			
		$user = EnUser::getUserObj();		
		$bag = BagManager::getInstance()->getBag($uid);
		
		$tmpItem = ChatTemplate::prepareItem($arrItem);			
		if (!$bag->addItems($arrItem))
		{
			$arrRet['ret'] = 'full';
			return $arrRet;
		}
				
		$arrRet['grid'] = $bag->update();		
		TreasureDao::update($uid, array('va_treasure'=>$vaTreasure));	

		ChatTemplate::sendTreasureExchangeItem($user->getTemplateUserInfo(), $user->getGroupId(), $tmpItem);		
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */