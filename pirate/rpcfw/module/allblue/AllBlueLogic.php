<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AllBlueLogic.php 40299 2013-03-08 04:32:12Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/allblue/AllBlueLogic.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-03-08 12:32:12 +0800 (五, 2013-03-08) $
 * @version $Revision: 40299 $
 * @brief 
 *  
 **/
class AllBlueLogic
{
	
	/** 
	 * 检查allblue功能是否开启
	 */
    private static function checkEnter()
    {
        if (!EnSwitch::isOpen(SwitchDef::ALLBLUE))
        {
        	Logger::warning('fail to enter allblue, switch return false');
        	throw new Exception('fake');
        }	
    }

    private static function checkEnterFish($uid=0)
    {
    	if (EnSwitch::isOpen(SwitchDef::ALLBLUE, $uid=0))
        {
        	return;
        }
        else if (!EnSwitch::isOpen(SwitchDef::FISH, $uid=0))
        {
        	Logger::warning('fail to enter fish, switch return false');
        	throw new Exception('fake');
        }	
    }

	/** 
	 * 取得采集厂信息
	 */
	public static function getAllBlueInfo($uid)
	{
		Logger::debug('AllBlueLogic::getAllBlueInfo start.');
		self::checkEnter();
		// 通过 uid 获取用户信息
		$allBlueInfo = self::getUserInfo($uid);

		// 剩余次数计算
		$times = self::getCollectTimes($allBlueInfo);
		// 攻击海怪剩余次数
		$aliveTimes = self::getAtkMonsterTimes($uid);
		Logger::debug('AllBlueLogic::getAllBlueInfo end.');
		return array('ret' => 'ok',
					 'bellyTimes' => $times['bellyTimes'],
					 'aliveTimes' => $aliveTimes,
					 'monster_ary' => array(array(11007=>1000,11008=>1000)),
					 //$allBlueInfo['monster_id'],
					 'level' => 1,
					 'point' => 100,);
	}

	/** 
	 * 开始采集
	 */
	public static function collectAllBule($uid, $type, $isGold, $collectLevel)
	{
		Logger::debug('AllBlueLogic::collectAllBule start.');
		self::checkEnter();
		$i_type = intval($type);
		if($i_type <= 0 || $i_type > 5)
		{
			Logger::fatal('Err para:i_type, %d!', $type);
			throw new Exception('fake');
		}
		$i_cLevel = intval($collectLevel);
		if($isGold && ($i_cLevel <= 0 || $i_cLevel > AllBlueDef::ALLBLUE_COLLECT_GOLD_LEVEL_NUM))
		{
			Logger::fatal('Err para:i_cLevel, %d!', $i_cLevel);
			throw new Exception('fake');
		}
		// 取得用户信息
		$allBlueInfo = self::getUserInfo($uid);
		// 采集次数判断
		if(!self::checkCollectCount($allBlueInfo, $i_type))
		{
			Logger::debug('no collection times.');
			return array('ret' => 'noTimes',
						 'res' => array());
		}
		// 采集
		$ret = self::collect($allBlueInfo, $type, $isGold, $i_cLevel);
		
		Logger::debug('ret = %s', $ret);
		Logger::debug('AllBlueLogic::collectAllBule end.');
		
		return $ret;
	}

	/** 
	 * 攻打海王类
	 */
	public static function atkSeaMonster($uid, $monsterId)
	{
		Logger::debug('AllBlueLogic::atkSeaMonster start.');
		self::checkEnter();
		$monsterId = intval($monsterId);
		// 检查参数
		if ($monsterId <= 0)
		{
			Logger::fatal('Err para, %d!', $monsterId);
			throw new Exception('fake');
		}
		// 攻击还怪次数判断
		$times = self::getAtkMonsterTimes($uid);
		if($times <= 0)
		{
			Logger::debug('the times of attack monster is noenough.');
			MyAllBlue::getInstance()->initAtkMonsterFailTimes();
			MyAllBlue::getInstance()->save();
			return 'noTimes';
		}
		
		$monster = btstore_get()->ARMY[$monsterId];
		if (!isset($monster))
		{
			Logger::fatal('The %d is not exist in the enemy!', $monsterId);
			throw new Exception('fake');
		}
		
		// 获取怪物小队ID
		$teamID = $monster['monster_list_id'];
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 获取用户ID
		$uid = $user->getUid();
		
		// 获取用户选择的NPC阵型信息
		$userFormation = EnFormation::getFormationInfo($uid);
		// 将阵型ID设置为用户当前默认阵型
		$formationID = $user->getCurFormation();
		// 这时候拉取所有缓存信息
		$user->prepareItem4CurFormation();

		// 敌人信息
		$enemyFormation = EnFormation::getBossFormationInfo($teamID);
		// 将对象转化为数组
		Logger::debug('userFormation = %s', $userFormation);
		Logger::debug('enemyFormation = %s', $enemyFormation);
		
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation, TRUE);
		$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation);

		Logger::debug('userFormationArr = %s', $userFormationArr);
		Logger::debug('userFormationArr = %s', $enemyFormationArr);
		
		// 调用战斗模块
		$bt = new Battle();
		$atkRet = $bt->doHero(array('name' => $user->getUname(), 
		                            'level' => $user->getLevel(),
		                            'isPlayer' => true,
		                            'flag' => 0,
		                            'formation' => $formationID,
		                            'uid' => $uid,
		                            'arrHero' => $userFormationArr),
		                      array('name' => $monster['name'], 
		                            'level' => $monster['lv'],
		                            'isPlayer' => false,
		                            'flag' => 0,
		                            'formation' => btstore_get()->TEAM[$teamID]['fid'],
		                            'uid' => $monsterId,
		                            'arrHero' => $enemyFormationArr),
		                      0,
		                      array('AllBlueLogic', 'atkSeaMonsterCallback'),
		                      NULL, 
		                      array('bgid' => intval($monster['background_id']),
		                            'musicId' => $monster['music_path'],
		                            'type' => BattleType::COPY));

		                      
		// 战斗系统返回值
		Logger::debug('Ret from battle is %s.', $atkRet);
		// 将战斗结果返回给前端
		Logger::debug('AllBlueLogic::atkSeaMonster end.');
		return array('ret' => 'ok',
					 'fightRet' => $atkRet['client'], 
		             'cd' => $user->getFightCDTime(), 
		             'reward' => $atkRet['server']['reward'],
		             'appraisal' => BattleDef::$APPRAISAL[$atkRet['server']['appraisal']]);
	}
	
	private static function getUserInfo($uid)
	{
		// 通过 uid 获取用户活跃度信息
		// 不是当前用户的话
		if ($uid != RPCContext::getInstance()->getUid())
		{
			$allBlueInfo = self::getOtherUserInfo($uid);
		}
		// 当前用户
		else 
		{
			$allBlueInfo = MyAllBlue::getInstance()->getAllBlueInfo();
			Logger::debug('allBlueInfo = [%s]', $allBlueInfo);
			// 看看采集次数的va有没有times这个key,没有的话就修复一次
			// 修复成array('times' => array(), 'maxtimes' => array()) 
			$allBlueInfo = MyAllBlue::getInstance()->changeCollectTimesVa();
			
			// 检查养鱼的va字段看看是否是空的,空的就初始化一下
			if (EMPTY($allBlueInfo['va_farmfish_queueInfo']))
			{
				$allBlueInfo = MyAllBlue::getInstance()->initAllBlueFarmFishVa();
			}
			// 看看有没有这个before_vip字段 
			if (EMPTY($allBlueInfo['before_vip']))
			{
				Logger::debug('allBlueInfo before_vip is %s.', $allBlueInfo['before_vip']);
				$allBlueInfo = MyAllBlue::getInstance()->initAllBlueFarmFishUserVip();
			}
			// 看看是否过了当天的凌晨4点
			if (!Util::isSameDay($allBlueInfo['collect_time'], AllBlueConf::REFRESH_TIME) &&
					EnSwitch::isOpen(SwitchDef::ALLBLUE))
			{
				// 累计次数计算
				MyAllBlue::getInstance()->updateMaxCollectTimes();
				// 初始化前一天的采集使用次数
				$allBlueInfo = MyAllBlue::getInstance()->initAllBlueInfo($uid);
			}
			
			// 表意义变更判断flg   养鱼已使用次数 -> 养鱼可以使用次数
			if (EMPTY($allBlueInfo['farmfish_times_changeflg']) || 
				$allBlueInfo['farmfish_times_changeflg'] == 0)
			{
				MyAllBlue::getInstance()->updateFarmFishTimesFlg();
				$allBlueInfo = MyAllBlue::getInstance()->updateFarmFishTimesColMean();
			}
			// 看看是否过了当天的凌晨4点
			if (!Util::isSameDay($allBlueInfo['farmfish_time'], AllBlueConf::REFRESH_TIME))
			{
				MyAllBlue::getInstance()->initAllBlueFarmFishInfo($uid);
				$allBlueInfo = MyAllBlue::getInstance()->updateMaxFarmFishTimes();
			}
			// 判定下vip有没有变化,有变化就更新下养鱼次数 
			$allBlueInfo = MyAllBlue::getInstance()->updateMaxFarmFishTimesByVip($allBlueInfo);

			// 保存用户信息
			MyAllBlue::getInstance()->save();
		}
		return $allBlueInfo;
	}
	private static function getOtherUserInfo($uid)
	{
		// 通过 uid 获取用户信息
		$allBlueInfo = AllBlueDao::getAllBlueInfo($uid);
		Logger::debug('getAllBlueInfo allBlueInfo = [%s]', $allBlueInfo);
//		if ($allBlueInfo === false)
//		{
//			// 初始化人信息
//			$allBlueInfo = AllBlueDao::addNewAllBlueInfo($uid);
//			Logger::debug('addNewAllBlueInfo allBlueInfo = [%s]', $allBlueInfo);
//		}
//		else if(EMPTY($allBlueInfo['va_farmfish_queueInfo'])) 
//		{
//			$arr = array('va_farmfish_queueInfo' => self::initFishQueue());
//			AllBlueDao::updateAllBlueInfo($uid, $arr);
//		}
		return $allBlueInfo;
	}
	private static function checkCollectCount($allBlueInfo, $type)
	{
		// 最大采集次数
		$mAllBlue = btstore_get()->ALLBLUE->toArray();
		$maxTimes = intval($mAllBlue[AllBlueDef::ALLBLUE_COLLECT_BELLYCOUNT]);
		// 最大累计次数
		$mTopLimit = btstore_get()->TOP_LIMIT->toArray();
		// 已经累计的次数
		$accTimes = $allBlueInfo['va_belly_times']['maxtimes'][$type];
		// 可以使用次数 
		$canUseTimes = $accTimes + $maxTimes;
		if($canUseTimes >= $mTopLimit)
		{
			$canUseTimes = $mTopLimit;
		}
		// 已经使用采集次数
		$usedTimes = $allBlueInfo['va_belly_times']['times'][$type];
		
		
		Logger::debug('the times is maxTimes = [%s], usedTimes = [%s], accTimes = [%s].', 
						$maxTimes, $usedTimes, $accTimes);
		if ($maxTimes <= $usedTimes && $usedTimes >= $canUseTimes)
		{
			return FALSE;
		}
		return TRUE;
	}
	
	private static function collect($allBlueInfo, $type, $isGold, $cLevel)
	{
		$mAllBlue = btstore_get()->ALLBLUE;
		// 取得已经使用次数
		$usedTimes = intval($allBlueInfo['va_belly_times']['times'][$type]);
		if(!self::checkCollectCount($allBlueInfo, $type))
		{
			Logger::debug('no collection times.');
			return array('ret' => 'noTimes',
					 	 'res' => array('monster_ary' => array(),
									'bag' => ""));
		}
		
		// 当前采集次数 = 最大次数 - 剩余采集次数 + 1(当前采集了一次)
		$usedTimes = $usedTimes + 1;
		
		$userObj = EnUser::getUserObj($allBlueInfo['uid']);
		// 用户等级
		$userLevel = $userObj->getLevel();
		// 用户金币
		$userGold = $userObj->getGold();
		// 用户贝利
		$userBelly = $userObj->getBelly();
		Logger::info('the gold of user is  = [%d].', $userGold);
		Logger::info('the belly of user is = [%d].', $userBelly);
		$costGold = 0;
		$costBelly = 0;
		$collectGoodsId = array();
		// 费用计算
		if ($isGold)
		{
			
			// 金币采集基础金币
			$collectGold = $mAllBlue[AllBlueDef::ALLBLUE_COLLECT_GOLD_LEVEL.$cLevel];
			// 金币采集每次递增金币
			$collectAddGold = $mAllBlue[AllBlueDef::ALLBLUE_COLLECT_ADDGOLD_LEVEL.$cLevel];
			// 金币采集场掉落表ID
			$collectGoodsId = $mAllBlue[AllBlueDef::ALLBLUE_COLLECT_GOODS_LEVEL.$cLevel][$type];
			Logger::debug('collectGold is %s', $collectGold);
			Logger::debug('collectAddGold is %s', $collectAddGold);
			// 金币采集花费金币=金币采集基础金币+（当前采集次数 -1）*金币采集每次递增金币
			$costGold = $collectGold + ($usedTimes - 1) * $collectAddGold;
			Logger::info('the cost gold of collect is = [%d].', $costGold);
			if($userGold < $costGold)
			{
				return array('ret' => 'noGold',
							 'res' => array());
			}
			$userObj->subGold($costGold);
		}
		else
		{
			// 贝里采集花费贝里
			$collectBelly = $mAllBlue[AllBlueDef::ALLBLUE_COLLECT_BASEBELLY];
			// 贝利采集场掉落表ID
			$collectGoodsId = $mAllBlue[AllBlueDef::ALLBLUE_COLLECT_GOODS][$type];
			Logger::debug('collectBelly is %s', $collectBelly);
			// 贝里采集花费贝里 = 奖励游戏币基础值*玩家等级
			$costBelly = $collectBelly * $userLevel;
			Logger::info('the cost belly of collect is = [%d].', $costBelly);
			if($userBelly < $costBelly)
			{
				return array('ret' => 'noBelly',
							 'res' => array());
			}
			$userObj->subBelly($costBelly);
		}

		Logger::debug('collectGoodsId is %s', $collectGoodsId);
		$itemArray = array();
		$itemId = array();
		if(!EMPTY($collectGoodsId))
		{
			$itemArray = self::dropItem($collectGoodsId);
			Logger::debug('itemArray is %s', $itemArray);
			if (!Empty($itemArray))
			{
				foreach ($itemArray as $item)
				{
					$itemId = array_merge($itemId, 
										  ItemManager::getInstance()->
												addItem($item['item_template_id'], 
														$item['item_num']));
				}
			}
		}
		// 取得物品
		$bagObj = BagManager::getInstance()->getBag();
		if (!Empty($itemId))
		{
			$bagObj->addItems($itemId, TRUE);
		}
		// 更新用户采集次数
		MyAllBlue::getInstance()->addBellyTimes($type);

		MyAllBlue::getInstance()->save();
		$userObj->update();
		$bagInfo = $bagObj->update();

		if ($costGold > 0)
		{
			Logger::debug('the statistics gold is %d.', $costGold);
			Statistics::gold(StatisticsDef::ST_FUNCKEY_ALLBLUE_COLLECT, 
									$costGold, Util::getTime(), TRUE);
		}
		
		// 采集两个操作的任务完成条件
		TaskNotify::operate(TaskOperateType::ALLBLUE_HARVEST);

		// 海王类
		// 是否遇到海王类怪物
		$monster = 0;
		$monsterWeight = $mAllBlue[AllBlueDef::ALLBLUE_COLLECT_GETMONSTERWEIGHT];
		$randValue = rand(0, 10000);

		Logger::debug('monsterWeight = [%d].', $monsterWeight);
		Logger::debug('randValue = [%d].', $randValue);
		
		if($randValue < $monsterWeight)
		{
			// 海怪部队组
			$monsters = $mAllBlue[AllBlueDef::ALLBLUE_COLLECT_MONSTERS];
			Logger::debug('monsters = [%s].', $monsters);
			// 随即出怪物ID
			$monsterId = Util::backSample($monsters,
										1, 
										AllBlueDef::ALLBLUE_COLLECT_MONSTERIDWEIGHT);
			Logger::debug('monsterId = [%s].', $monsterId);
			$monster = $monsters[$monsterId[0]][AllBlueDef::ALLBLUE_COLLECT_MONSTERID];

			MyAllBlue::getInstance()->initAtkMonsterFailTimes();
		}
		if($monster != 0)
		{
			MyAllBlue::getInstance()->updateMonsterId($monster);
		}
		MyAllBlue::getInstance()->save();
		Logger::debug('monster = [%s].', $monster);
		return array('ret' => 'ok',
					 'res' => array('items' => $itemArray,
									'monster_ary' => array(),
									//$monster,
									'bag' => $bagInfo));
	}
	
	private static function dropItem($collectGoodsId)
	{
		$itemArray = array();
		foreach ($collectGoodsId as $itmeId)
		{
			// 奖励掉落表ID组
			if (!Empty($itmeId))
			{
				$itemTemplate = Drop::dropItem($itmeId);
				Logger::debug('itemTemplate is %s', $itemTemplate);
				// 牌ID和装备模板ID
				if(!EMPTY($itemTemplate))
				{
					foreach ($itemTemplate as $value)
					{
						$itemArray[] = array(
										'item_template_id' => $value[DropDef::DROP_ITEM_TEMPLATE_ID],
										'item_num' => $value[DropDef::DROP_ITEM_NUM]);
					}
				}
			}
		}
		return $itemArray;
	}
	
	public static function atkSeaMonsterCallback($atkRet)
	{
		// 没有获得敌人小队ID
		if (!isset($atkRet['uid2']))
		{
			Logger::fatal('Can not get monster team id.');
			throw new Exception('fake');
		}
		
		$itemId = array();
		$itemArray = array();
		$itemTmp = array();
		$belly = 0;
		$exp = 0;
		$experience = 0;
		$prestige = 0;
		$gold = 0;
		// 获取用户类实例
		$user = EnUser::getUserObj();
		$enemyID = $atkRet['uid2'];
		if (BattleDef::$APPRAISAL[$atkRet['appraisal']] <= BattleDef::$APPRAISAL['D'])
		{			
			$dropIDs = btstore_get()->ARMY[$enemyID]['drop_ids']->toArray();
			Logger::debug('dropIDs is %s', $dropIDs);
			if (!Empty($dropIDs))
			{
				$itemArray = self::dropItem($dropIDs);
			}
			// 打败怪物 海怪消失
			MyAllBlue::getInstance()->updateMonsterId(0);
			
			// 获胜奖励游戏币
			$belly = btstore_get()->ARMY[$enemyID]['init_belly'];
			// 获胜奖励威望
			$prestige = btstore_get()->ARMY[$enemyID]['init_prestige'];
			// 获取公会科技
			$guild = GuildLogic::getBuffer($user->getUid());
			// 获胜奖励阅历
			$experience = floor(btstore_get()->ARMY[$enemyID]['init_experience'] * 
			                    (1 + $guild['battleExperienceAddition'] / 10000) *
			                    EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_COPY));
			// 获胜奖励经验
			$exp = floor(btstore_get()->ARMY[$enemyID]['init_exp'] * 
			                    (1 + $guild['battleExpAddition'] / 10000) *
			                    EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_BATTLE));                    
		}
		else
		{
			// 失败次数计算
			MyAllBlue::getInstance()->addAtkMonsterFailTimes();
			$exp = intval(btstore_get()->ARMY[$enemyID]['lose_exp']);
		}
		// 攻打海怪剩余次数
		$aliveTimes = self::getAtkMonsterTimes($user->getUid());
		if($aliveTimes <= 0)
		{
			// 复活次数没有了,海怪消失
			MyAllBlue::getInstance()->updateMonsterId(0);
		}
		MyAllBlue::getInstance()->save();
			
		if (!Empty($itemArray))
		{
			foreach ($itemArray as $item)
			{
				$itemId = array_merge($itemId, 
									  ItemManager::getInstance()->
											addItem($item['item_template_id'], 
													$item['item_num']));
			}
		}
		$bagObj = BagManager::getInstance()->getBag();
		if (!Empty($itemId))
		{
			foreach ($itemId as $value)
			{
				$itemTmp[] = ItemManager::getInstance()->itemInfo($value);
			}
			$bagObj->addItems($itemId, TRUE);
		}
		$bagInfo = $bagObj->update();
		$equip = array('item' => $itemTmp, 'bag' => $bagInfo, 'heroID' => 0);

		// 获胜奖励游戏币
		if(!EMPTY($belly))
		{
			$user->addBelly($belly);
		}
		// 获胜奖励阅历
		if(!EMPTY($experience))
		{
			$user->addExperience($experience);
		}
		// 获胜奖励威望
		if(!EMPTY($prestige))
		{
			$user->addPrestige($prestige);
		}
		
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
		$user->update();
		return array('arrHero' => $heroList, 'belly' => 0, 'exp' => 0, 'experience' => 0, 'prestige' => 0,
					 'equip' => $equip, 'bag' => $bagInfo, 'aliveTimes' => $aliveTimes);
	}
	
	private static function getCollectTimes($allBlueInfo)
	{
		$times = array();
		// 最大采集次数
		$mAllBlue = btstore_get()->ALLBLUE;
		$maxBellyTimes = intval($mAllBlue[AllBlueDef::ALLBLUE_COLLECT_BELLYCOUNT]);
		// 最大累计次数
		$mTopLimit = btstore_get()->TOP_LIMIT->toArray();
		$topLimit = $mTopLimit[TopLimitDef::ALLBULE_COLLECT_MAX_TIME];
		Logger::debug('the maxBellyTimes is %d, topLimit is %d.', $maxBellyTimes, $topLimit);

		// 剩余使用次数
		$bellyTimes = array();
		foreach ($allBlueInfo['va_belly_times']['times'] as $key => $value)
		{
			if($allBlueInfo['va_belly_times']['maxtimes'][$key] + $maxBellyTimes <= intval($value))
			{
				$bellyTimes[$key] = 0;
			}
			else 
			{
				if($allBlueInfo['va_belly_times']['maxtimes'][$key] + $maxBellyTimes >= $topLimit)
				{
					$bellyTimes[$key] = $topLimit - intval($value);
				}
				else 
				{
					$bellyTimes[$key] = $maxBellyTimes - intval($value) + 
									$allBlueInfo['va_belly_times']['maxtimes'][$key];
				}
			}
		}
		$times['bellyTimes'] = $bellyTimes;
		Logger::debug('the collect times is %s.', $times);
		return $times;
	}
	
	private static function getAtkMonsterTimes($uid)
	{
		$allBlueInfo = self::getUserInfo($uid);
		$failTimes = $allBlueInfo['atkmonster_fail_times'];
		$maxFailTimes = btstore_get()->ALLBLUE[AllBlueDef::ALLBLUE_MONSTER_FAIL_TIMES];
		Logger::debug('the failTimes is %d.', $failTimes);
		Logger::debug('the maxFailTimes is %d.', $maxFailTimes);
		$times = 0;
		if($failTimes < $maxFailTimes)
		{
			$times = $maxFailTimes - $failTimes;
		}
		Logger::debug('the aliveTimes is %d.', $times);
		return $times;
	}
	
	/** 
	 * 获得养鱼的信息
	 */
	public static function farmFishInfo($uid)
	{
		Logger::debug('AllBlueLogic::farmFishInfo start.');
		self::checkEnterFish();
		// 通过 uid 获取用户信息
		$allBlueInfo = self::getUserInfo($uid);
		
		$mAllBlue = btstore_get()->ALLBLUE;
		// 每天可偷鱼次数
		$thiefFishTimes =  $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_DAILYTHIEFCOUNT];
		// 每日可祝福次数
		$wishFishTimes =  $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_DAILYWISHCOUNT];
		// 每序列可被祝福次数
		$queueWishFishTimes =  $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_QUEUEWISHCOUNT];

		// 剩余养鱼次数
		$ffTimes = $allBlueInfo['farmfish_times'];
		// 剩余偷鱼次数
		$tfTimes = self::subTowValue($thiefFishTimes, $allBlueInfo['farmfish_tftimes']);
		// 剩余祝福次数
		$wfTimes = self::subTowValue($wishFishTimes, $allBlueInfo['farmfish_wftimes']);

		// 剩余被祝福次数 = 各个养鱼序列祝福次数和 - 各个养鱼序列已经祝福的次数
    	$wfdTimes = $allBlueInfo['farmfish_wdftimes'];
    	$rwfdTimes = self::subTowValue($queueWishFishTimes * count($allBlueInfo['va_farmfish_queueInfo']), $wfdTimes);
    	
    	// 更新养鱼状态
    	$fishQueue = self::updateFishStatus($allBlueInfo['va_farmfish_queueInfo']);
		MyAllBlue::getInstance()->updateFishQueue($fishQueue);
		MyAllBlue::getInstance()->save();
		
		// 剩余鱼的数量
		foreach ($fishQueue as $key => $value)
		{
			if($value['qstatus'] == 1 && $value['fstatus'] != 0 && $value['fishid'] != 0)
			{
				$fishNum = self::fishingCount($value);
				$fishQueue[$key]['fishnum'] = $fishNum;
			};
		}
		Logger::debug('the fishQueue of farmFishInfo is %s.', $fishQueue);
		$mTopLimit = btstore_get()->TOP_LIMIT->toArray();
		Logger::debug('AllBlueLogic::farmFishInfo end.');
		return array('ret' => 'ok',
					 'fftimes' => $ffTimes,
					 'maxfftimes' => $mTopLimit[TopLimitDef::ALLBLUE_FEED_MAX_TIME],
					 'tftimes' => $tfTimes,
					 'wftimes' => $wfTimes,
					 'wfdtimes' => $rwfdTimes,
					 'fishqueue' => $fishQueue);
	}
	
	/** 
	 * 开启保护罩
	 */
	public static function openBoot($uid, $queueId)
	{
		Logger::debug('AllBlueLogic::openBoot start.');
		self::checkEnterFish();
		if($queueId < 0)
		{
			Logger::fatal('Err para:queueId, %d!', $queueId);
			throw new Exception('fake');
		}
		
		// 保护罩开启判断
		$mVip = btstore_get()->VIP;
		$userObj = EnUser::getUserObj();
		if($mVip[$userObj->getVip()]['is_open_fish_boot'] != 1)
		{
			Logger::debug('the user is not permission for openboot.');
			return 'noPermission';
		}
		// 通过 uid 获取用户信息
		$allBlueInfo = self::getUserInfo($uid);
		// 队列信息
		$fishQueue = $allBlueInfo['va_farmfish_queueInfo'];
		if(!isset($fishQueue[$queueId]))
		{
			Logger::debug('the fish is not exist in the queue. queueId = %s', $queueId);
			return 'noQueue';
		}
		// 看看玩家是否已经开启了保护罩
		if($fishQueue[$queueId]['isboot'] == 1)
		{
			Logger::debug('the fish boot has been openned.');
			return 'ok';
		}
		// 看看有没有鱼
		if(EMPTY($fishQueue[$queueId]['fishid']))
		{
			Logger::debug('there is not fish in the queue.');
			return 'ok';
		}
		$mAllBlue = btstore_get()->ALLBLUE;
		// 保护罩开启花费金币
		$openBootGold = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_OPENBOOTGOLD];
		// 用户现在有的金币数
		$userGold = $userObj->getGold();
		Logger::info('the gold of user is %s in the farmfish.', $userGold);
		Logger::info('the openBootGold is %s.', $openBootGold);
		if($openBootGold > $userGold)
		{
			Logger::debug('the gold of user is not enough for openboot.');
			return 'noGold';
		}
		// 扣金币
		$userObj->subGold($openBootGold);
		
		$fishQueue[$queueId]['isboot'] = 1;
		MyAllBlue::getInstance()->updateFishQueue($fishQueue);
		
		$userObj->update();
		MyAllBlue::getInstance()->save();
		// 金币统计
		self::totalGold($openBootGold, StatisticsDef::ST_FUNCKEY_ALLBLUE_FISH_OPENBOOT);
		Logger::debug('AllBlueLogic::openBoot end.');
		return 'ok';
	}
	
	/** 
	 * 捕捞已经成熟的鱼
	 */
	public static function fishing($uid, $queueId)
	{
		Logger::debug('AllBlueLogic::fishing start.');
		self::checkEnterFish();
		if($queueId < 0)
		{
			Logger::fatal('Err para:queueId, %d!', $queueId);
			throw new Exception('fake');
		}

		// 通过 uid 获取用户信息
		$allBlueInfo = self::getUserInfo($uid);
		// 养鱼队列信息
		$fishQueue = $allBlueInfo['va_farmfish_queueInfo'];
		
		// 看看队列中有没有鱼
		if(!isset($fishQueue[$queueId]) || 
		   EMPTY($fishQueue[$queueId]) ||
		   !isset($fishQueue[$queueId]['fishid']) ||
		   EMPTY($fishQueue[$queueId]['fishid']))
		{
			Logger::debug('the fish is not exist in the queue. queueId = %s', $queueId);
			return array('ret' => 'noFish');
		}
		// 看看鱼是否已经成熟
		$fishInfo = $fishQueue[$queueId];
		$fishId = $fishInfo['fishid'];
		if(Util::getTime() < $fishInfo['etime'])
		{
			Logger::debug('the fish is not ripe in the queue. queueId = %s', $queueId);
			return array('ret' => 'noRipe');
		}
		
		// 有鱼,成熟  ok可以收啦
		// 配置表
		$mAllBlue = btstore_get()->ALLBLUE;
		$mFish = btstore_get()->FISH;
		if(!isset($mFish[$fishId]))
		{
			Logger::debug('sorry, the fish is not exist. fishid = %s', $fishId);
			return array('ret' => 'err');
		}
		// 计算可以收获的数量(需要减去被偷的鱼的数量)
		// 收获的数量 - 被偷次数*每次偷的数量
		$count = self::fishingCount($fishInfo);
		Logger::debug('the fishing count is %s.', $count);
		$bagObj = BagManager::getInstance()->getBag();
		if (!Empty($fishId))
		{
			if($bagObj->addItemByTemplateID($fishId, $count) == FALSE)
			{
				Logger::debug('the bag of user is full.');
				return array('ret' => 'noBag');
			}
		}
		// 收获成功,清空改队列中的信息
		$initFishInfo = AllBlueDao::initFishQueue();
		$fishQueue[$queueId] = $initFishInfo[$queueId];
		MyAllBlue::getInstance()->updateFishQueue($fishQueue);
		MyAllBlue::getInstance()->save();
		$bagInfo = $bagObj->update();

		Logger::debug('AllBlueLogic::fishing end.');
		return array('ret' => 'ok',
					 'item' => array('item_template_id' => $fishId,
									 'item_num' => $count),
					 'bag' => $bagInfo);
	}
	
	/** 
	 * 开通养鱼序列
	 */
	public static function openFishQueue($uid, $queueId)
	{
		Logger::debug('AllBlueLogic::openFishQueue start.');
		self::checkEnterFish();
		if($queueId < 0)
		{
			Logger::fatal('Err para:queueId, %d!', $queueId);
			throw new Exception('fake');
		}
		// 通过 uid 获取用户信息
		$allBlueInfo = self::getUserInfo($uid);
		// 养鱼队列信息
		$fishQueue = $allBlueInfo['va_farmfish_queueInfo'];
		
		// 看看有没有这个队列
		if(!isset($fishQueue[$queueId]))
		{
			Logger::debug('the queue is not exist. queueId = %s', $queueId);
			return 'noQueue';
		}
		// 看看改队列是否已经开通了
		if($fishQueue[$queueId]['qstatus'] == 1)
		{
			Logger::debug('the queue has been openned. queueId = %s', $queueId);
			return 'opended';
		}
		
		$mAllBlue = btstore_get()->ALLBLUE;
		// 开队列的花费金币
		$openQueueGold = 0;
		if($queueId == 1)
		{
			$mVip = btstore_get()->VIP;
			$userObj = EnUser::getUserObj();
			$freeQueue = $mVip[$userObj->getVip()]['free_open_fishqueie'];
			if((EMPTY($fishQueue[$queueId]['qopentimes']) || $fishQueue[$queueId]['qopentimes'] == 0)
					 && $freeQueue == 1)
			{
				$openQueueGold = 0;
			}
			else
			{
				$openQueueGold = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_QUEUE1GOLD];
			}
		}
		else if($queueId == 2)
		{
			$openQueueGold = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_QUEUE2GOLD];
		}
		else 
		{
			return 'err';
		}

		// 是否有鱼精灵,有的话就免费开
		$isElves = EnElves::hasFishElf($uid);
		// 用户现在有的金币数
		if(!$isElves)
		{
			$userObj = EnUser::getUserObj($uid);
			$userGold = $userObj->getGold();
			Logger::info('the gold of user is %s in the farmfish.', $userGold);
			Logger::info('the openFishQueue is %s, cost gold is %s.', $queueId, $openQueueGold);
			if($openQueueGold > $userGold)
			{
				Logger::debug('the gold of user is not enough for openqueue.');
				return 'noGold';
			}
			// 扣金币
			$userObj->subGold($openQueueGold);
		}
		// 开通队列
		$fishQueue[$queueId]['qstatus'] = 1;
		if(!isset($fishQueue[$queueId]['qopentimes']) ||
			EMPTY($fishQueue[$queueId]['qopentimes']))
		{
			$fishQueue[$queueId]['qopentimes'] = 1;
		}
		else 
		{
			$fishQueue[$queueId]['qopentimes']++;
		}
		MyAllBlue::getInstance()->updateFishQueue($fishQueue);
		if(!$isElves)
		{
			$userObj->update();
		}
		MyAllBlue::getInstance()->save();
		// 金币统计
		self::totalGold($openQueueGold, StatisticsDef::ST_FUNCKEY_ALLBLUE_FISH_OPENQUEUE);
		Logger::debug('AllBlueLogic::openFishQueue end.');
		return 'ok';
	}
	
	
	/**
     * 获得鱼缸信息
     */
    public static function krillInfo($uid, $queueId)
    {
    	Logger::debug('AllBlueLogic::krillInfo start.');
		self::checkEnterFish();
		// check队列,取得队列信息
    	$result = self::checkQueue($uid, $queueId);
    	if($result['ret'] != 'ok')
    	{
    		return $result;
    	}

    	// 该队列的鱼苗信息
    	$krillInfo = $result['res']['va_farmfish_queueInfo'][$queueId]['krillinfo'];
    	// 准备养殖的鱼苗
    	$krillId = $result['res']['va_farmfish_queueInfo'][$queueId]['krillid'];
    	Logger::debug('the krillInfo is %s.', $krillInfo);

    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	
    	// 已经捞了几次
    	$reTimes = 0;
    	if(count($krillInfo) == 0 && $krillId == 0)
    	{
    		$reTimes = 0;
    	}
    	else if(count($krillInfo) == 0 && $krillId != 0)
    	{
    		$reTimes = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_KRILLCOUNT];
    	}
    	else 
    	{
    		$reTimes = self::subTowValue($mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_KRILLCOUNT], count($krillInfo));
    	}
    	Logger::debug('AllBlueLogic::krillInfo end.');
    	return array('ret' => 'ok',
    				 'krillinfo' => array('reTimes' => $reTimes,
    				 					  'fish' => $result['res']['va_farmfish_queueInfo'][$queueId]['krillid'],
    									  'fishinfo' => $krillInfo));
    }
    
	/**
     * 捞鱼苗(5条)
     */
    public static function catchKrills($uid, $queueId)
    {
       	Logger::debug('AllBlueLogic::catchKrills start.');
		self::checkEnterFish();
    	// check队列,取得队列信息
    	$result = self::checkQueue($uid, $queueId);
    	if($result['ret'] != 'ok')
    	{
    		return $result;
    	}

    	// 看看是否有上一次的鱼苗,有的话就显示
    	$krillInfo = $result['res']['va_farmfish_queueInfo'][$queueId]['krillinfo'];
    	if(!EMPTY($krillInfo))
    	{
    		return array('ret' => 'ok',
    				 'fish' => $krillInfo);
    	}
    	// 随即鱼苗
 		$krillId = self::randFiveKrill();
    	// 保存随即出来的鱼苗
    	$fishQueue = $result['res']['va_farmfish_queueInfo'];
    	$fishQueue[$queueId]['krillinfo'] = $krillId;
		MyAllBlue::getInstance()->updateFishQueue($fishQueue);
		MyAllBlue::getInstance()->save();
		
		Logger::debug('AllBlueLogic::catchKrills end.');
    	return array('ret' => 'ok',
    				 'fish' => $krillId);
    }
    
	/**
     * 捕获鱼苗(随即1条)
     */
    public static function catchKrill($uid, $queueId)
    {
    	Logger::debug('AllBlueLogic::catchKrill start.');
		self::checkEnterFish();
		// 配置表信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	$mFish = btstore_get()->FISH->toArray();
		// 捞鱼苗初始金币	
		$baseGold = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_KRILLGOLD];
    	// 捞鱼苗递增金币
    	$addGold = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_KRILLADDGOLD];

    	// check队列,取得队列信息
    	$result = self::checkQueue($uid, $queueId);
    	if($result['ret'] != 'ok')
    	{
    		return $result;
    	}
    	// 鱼苗信息
		$krillInfo = $result['res']['va_farmfish_queueInfo'][$queueId]['krillinfo'];
		
		// 看看还有没有鱼苗
		if(count($krillInfo) <= 0)
		{
			Logger::debug('there is not krill in the fishpond.');
			return array('ret' => 'noFish');
		}

		// vip可以捞几次鱼苗
		$mVip = btstore_get()->VIP;
		$userObj = EnUser::getUserObj();
		$vipCatchKrillCount = $mVip[$userObj->getVip()]['free_fishing_times'];
		// 次数计算
		if($vipCatchKrillCount + count($krillInfo) >= $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_KRILLCOUNT])
		{
			$count = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_KRILLCOUNT];
		}
		else 
		{
			$count = $vipCatchKrillCount + count($krillInfo);
		}
		// 计算金币
		$userObj = EnUser::getUserObj($uid);
		$catchGold = $baseGold + $addGold *
						($mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_KRILLCOUNT] - $count);
		// 用户金币
		$userGold = $userObj->getGold();
		if($userGold < $catchGold)
		{
			Logger::debug('the gold of user is not enough.');
			return array('ret' => 'noGold');
		}

		Logger::info('the gold of user is %d.', $userGold);
		Logger::info('the gold of catchkrill is %d.', $catchGold);

		// 鱼苗的随即数组
		$randKrillArray = array();
		foreach ($krillInfo as $value)
		{
			$randKrillArray[$value] = $mFish[$value];
		}
		$krillId = Util::backSample($randKrillArray,
							1, 
							AllBlueDef::ALLBLUE_FARMFISH_FISHINGWEIGHT);
		Logger::debug('the rand krillId is %s.', $krillId[0]);
		
		// 从5条鱼中,去除这条鱼苗
		$fishQueue = $result['res']['va_farmfish_queueInfo'];
		Logger::debug('the fishQueue info is %s.', $fishQueue);
		foreach ($fishQueue[$queueId]['krillinfo'] as $key => $value)
		{
			if($fishQueue[$queueId]['krillinfo'][$key] == $krillId[0])
			{
				unset($fishQueue[$queueId]['krillinfo'][$key]);
				Logger::debug('the fishQueue info is %s after removing.', $fishQueue);
				break;
			}
		}
		// 保存准备养殖的鱼
    	$fishQueue[$queueId]['krillid'] = $krillId[0];
		MyAllBlue::getInstance()->updateFishQueue($fishQueue);

		// 扣金币
		$userObj->subGold($catchGold);

		$userObj->update();
		MyAllBlue::getInstance()->save();
		// 金币统计
		self::totalGold($catchGold, StatisticsDef::ST_FUNCKEY_ALLBLUE_FISH_GETKRILL);
    	Logger::debug('AllBlueLogic::catchKrill end.');
    	return array('ret' => 'ok',
    				 'fish' => $krillId[0],
    				 'fishinfo' => $fishQueue[$queueId]['krillinfo']);
    }
    
	/**
     * 刷新鱼苗
     */
    public static function refreshKrill($uid, $queueId)
    {
    	Logger::debug('AllBlueLogic::refreshKrill start.');
		self::checkEnterFish();
    	// check队列,取得队列信息
    	$result = self::checkQueue($uid, $queueId);
    	if($result['ret'] != 'ok')
    	{
    		return $result;
    	}
    	// 随即鱼苗
		$krillId = self::randFiveKrill();
		
    	// 保存随即出来的鱼苗
    	$fishQueue = $result['res']['va_farmfish_queueInfo'];
    	$fishQueue[$queueId]['krillinfo'] = $krillId;
		MyAllBlue::getInstance()->updateFishQueue($fishQueue);
		
		// 配置表信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
		// 计算金币
    	$userObj = EnUser::getUserObj($uid);
		$refreshGold = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_KRILLINITGOLD];

		// 用户金币
		$userGold = $userObj->getGold();
		if($userGold < $refreshGold)
		{
			Logger::debug('the gold of user is not enough.');
			return array('ret' => 'noGold');
		}
		Logger::info('the gold of user is %d.', $userGold);
		Logger::info('the gold of catchkrill is %d.', $refreshGold);
		// 扣金币
		$userObj->subGold($refreshGold);
		$userObj->update();
		MyAllBlue::getInstance()->save();
		// 金币统计
		self::totalGold($refreshGold, StatisticsDef::ST_FUNCKEY_ALLBLUE_FISH_REKRILL );
    	Logger::debug('AllBlueLogic::refreshKrill end.');
    	return array('ret' => 'ok', 'fish' => $krillId); 
    }
    
	/**
     * 养殖鱼苗
     */
    public static function farmFish($uid, $queueId)
    {
    	Logger::debug('AllBlueLogic::farmFish start.');
		self::checkEnterFish();
		// check队列,取得队列信息
    	$result = self::checkQueue($uid, $queueId);
    	if($result['ret'] != 'ok')
    	{
    		return $result['ret'];
    	}
    	$fishQueue = $result['res']['va_farmfish_queueInfo'];

		// 配置表信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	$mFish = btstore_get()->FISH->toArray();
		$ffTimes = $result['res']['farmfish_times'];
    	// 看看有没有养殖次数了
        if ($ffTimes <= 0)
    	{
    		Logger::debug('there is not farmfish times.');
    		return array('ret' => 'noTimes');
    	}
    	// 看看有没有准备要养殖的鱼苗
    	if (EMPTY($fishQueue[$queueId]['krillid']))
    	{
    		Logger::debug('there is not krill.');
    		return array('ret' => 'noKrill');
    	}
    	// 要养殖的鱼苗ID
    	$krillId = $fishQueue[$queueId]['krillid'];
    	// 清空鱼苗ID
    	$fishQueue[$queueId]['krillid'] = 0;
    	// 清空鱼苗信息组
    	$fishQueue[$queueId]['krillinfo'] = array();
    	// 养鱼状态更新
    	$fishQueue[$queueId]['fstatus'] = AllBlueDef::ALLBLUE_FISH_STATUS_FARM;
		// 该队列所养的鱼的ID
		$fishQueue[$queueId]['fishid'] = $krillId;
		// 更新鱼苗养殖开始时间
		$fishQueue[$queueId]['btime'] = Util::getTime();
    	// 更新鱼苗成熟时间
		// 鱼苗成熟时间(单位秒)
		Logger::debug('the farm fish is is %d.', $mFish[$krillId]);
    	$time = $mFish[$krillId][AllBlueDef::ALLBLUE_FARMFISH_RIPETIME];
    	$fishQueue[$queueId]['etime'] = Util::getTime() + $time;

		MyAllBlue::getInstance()->updateFishQueue($fishQueue, TRUE);
		MyAllBlue::getInstance()->subFarmFishTimes();
		MyAllBlue::getInstance()->save();
		
		// 鱼剩余的数量
		foreach ($fishQueue as $key => $value)
		{
			if($value['qstatus'] == 1 && $value['fstatus'] != 0 && $value['fishid'] != 0)
			{
				$fishNum = self::fishingCount($value);
				$fishQueue[$key]['fishnum'] = $fishNum;
			};
		}
		
		// 把自己养鱼的信息推给自己的主人
		$masterUid = EnVassal::getMstUid($uid);
		if(!empty($masterUid))
		{
	    	if (EnSwitch::isOpen(SwitchDef::ALLBLUE, $masterUid) ||
	    		EnSwitch::isOpen(SwitchDef::FISH, $masterUid))
	    	{
				RPCContext::getInstance()->sendMsg(array($masterUid), 'fish.subordinateBreed', 
													array('isfarm' => TRUE));
	    	}
		}

    	Logger::debug('AllBlueLogic::farmFish end.');
    	return array('ret' => 'ok',
    				 'fishqueue' => $fishQueue);
    }
    
	/**
     * 获得好友列表
     */
    public static function getFriendList($uid, $offset, $limit)
    {
    	Logger::debug('AllBlueLogic::getFriendList start.');
		self::checkEnterFish();
    	$friendList = EnFriend::getFriendList($uid, $offset, $limit);
    	Logger::debug('the friend list is %s.', $friendList);
    	
    	$friendArray = $friendList['userinfo'];
    	if(EMPTY($friendArray))
    	{
    		Logger::debug('the user have not friend.');
    		return $friendList;
    	}
    	$uidArray = array_keys($friendArray);
    	
    	$userInfo = AllBlueDao::getUserInfoByIn($uidArray);
		Logger::debug('the friend user info is %s.', $userInfo);
		
   		// 配置表信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	$mFish = btstore_get()->FISH->toArray();
    	// 每天祝福鱼次数
    	$baseWfcount = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_DAILYWISHCOUNT];
    	// 每天偷鱼次数
    	$baseTfcount = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_DAILYTHIEFCOUNT];
    	// 每个队列可以被祝福几次
    	$queueWishFishTimes = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_QUEUEWISHCOUNT];
		// 看看自己有偷鱼 和 祝福的次数没有
		$allBlueInfo = self::getUserInfo($uid);
    	$wfCount = $allBlueInfo['farmfish_wftimes'];
    	$tfCount = $allBlueInfo['farmfish_tftimes'];
//    	$isWf = true;
//    	$isTf = true;
//    	if($wfCount >= $baseWfcount)
//    	{
//    		$isWf = false;
//    	}
//    	if($tfCount >= $baseTfcount)
//    	{
//    		$isTf = false;
//    	}
		$htidTempLate = UserConf::$USER_INFO;
    	$resultUserInfo = array();
    	if(EMPTY($userInfo))
    	{
    		foreach ($friendArray as $key => $value)
    		{
				
				$friendList['userinfo'][$key]['htid'] = $htidTempLate[$friendList['userinfo'][$key]['utid']][1];
				unset($friendList['userinfo'][$key]['utid']);
		    	// 是否可以偷鱼
		    	$friendList['userinfo'][$key]['isthief'] = 0;
		    	// 是否可以祝福
		    	$friendList['userinfo'][$key]['iswish'] = 0;
		    	// 是否可以被征服
		    	$friendList['userinfo'][$key]['isconquer'] = self::isConquer($uid, $key);
    		}
    	}
    	else 
    	{
			foreach ($friendArray as $fuid => $myFriendInfo)
			{
				$isthief = 0;
				$iswish = 0;
				if(isset($userInfo[$fuid]))
				{
					// 养鱼队列信息
					$fishQueue = $userInfo[$fuid]['va_farmfish_queueInfo'];
					// 因为此列是此次新加的,所以要是没有值的话,得初始化一下
					if(EMPTY($fishQueue))
					{
						$fishQueue = AllBlueDao::initFishQueue();
					}
					
//					// 该用户剩余被祝福了几次
//					$rwfdTimes = self::subTowValue($queueWishFishTimes * count($fishQueue), $myFriendInfo['farmfish_wdftimes']);
//					if($rwfdTimes <= 0)
//					{
//						$isWf = false;
//					}
			    	foreach ($fishQueue as $value)
			    	{
			    		// 检查是否可以被偷
			    		$isThief = self::isThief($uid, $value);
			    		if($isThief['ret'] == 'ok' && $isthief != 1)
			    		{
			    			$isthief = 1;
			    		}
			    		// 检查是否可以被祝福
						$isWish = self::isWish($uid, $value);
			    		if($isWish['ret'] == 'ok' && $iswish != 1)
			    		{
			    			$iswish = 1;
			    		}
			    	}
				}
				// tid -> htid
				$friendList['userinfo'][$fuid]['htid'] = $htidTempLate[$friendList['userinfo'][$fuid]['utid']][1];
				unset($friendList['userinfo'][$fuid]['utid']);
		    	// 是否可以偷鱼
		    	$friendList['userinfo'][$fuid]['isthief'] = $isthief;
		    	// 是否可以祝福
		    	$friendList['userinfo'][$fuid]['iswish'] = $iswish;
		    	// 是否可以被征服
		    	$friendList['userinfo'][$fuid]['isconquer'] = self::isConquer($uid, $fuid);
			}
    	}
    	Logger::debug('the friend farmFish info is %s.', $friendList);
    	Logger::debug('AllBlueLogic::getFriendList end.');
    	return $friendList;
    }
    
	/**
     * 去偷窥好友
     */
    public static function goFriendFishpond($uid, $fuid)
    {
    	Logger::debug('AllBlueLogic::goFriendFishpond start.');
		self::checkEnterFish();
        if($uid == $fuid)
    	{
    		Logger::debug('can not go myself home. %s, %s', $uid, $fuid);
    		return array('ret' => 'err');
    	}
    	// 表配置信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	$mFish = btstore_get()->FISH->toArray();

    	// 通过 uid 获取用户信息
		$allBlueInfo = self::getUserInfo($uid);
		// 养鱼队列信息
		$fishQueue = $allBlueInfo['va_farmfish_queueInfo'];
		
		// 今日剩余偷鱼次数
		$tftimes = self::subTowValue($mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_DAILYTHIEFCOUNT], $allBlueInfo['farmfish_tftimes']);
		// 今日祝福次数
		$wftimes = self::subTowValue($mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_DAILYWISHCOUNT], $allBlueInfo['farmfish_wftimes']);
		// 今日剩余被祝福次数
    	$wfdTimes = $allBlueInfo['farmfish_wdftimes'];
    	$rwfdTimes = self::subTowValue($mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_QUEUEWISHCOUNT] * count($allBlueInfo['va_farmfish_queueInfo']), $wfdTimes);
		
		// 计算好友的各种次数
    	// 通过 uid 获取用户信息
    	$fFishQueue = self::getFriendInfo($fuid);
		// 更新各个队列中的养鱼状态
		$fQueueInfo = self::updateFishStatus($fFishQueue);
		// 他是否已经偷过该好友的
		$fQueueInfo = self::isStolenWish($uid, $fQueueInfo);
		$result = array('ret' => 'ok',
					 	'myself' => array('tftimes' => $tftimes,
									      'wftimes' => $wftimes,
									  	  'wdftimes' => $rwfdTimes),
					 	'myfrend' => $fQueueInfo,
						'issubord' => self::isMyVassal($uid, $fuid));
		Logger::debug('the result is %s.', $result);
		Logger::debug('AllBlueLogic::goFriendFishpond end.');
		return $result;
    }
    
	/**
     * 去好友家干坏事(偷鱼)
     */
    public static function thiefFish($uid, $fuid, $queueId)
    {
    	Logger::debug('AllBlueLogic::thiefFish start.');
		self::checkEnterFish();
		if($uid == $fuid)
    	{
    		Logger::debug('can not steal myself. %s, %s', $uid, $fuid);
    		return array('ret' => 'err');
    	}
    	// 看看是不是自己的好友或下属
    	if(!EnFriend::isMyFriend($uid, $fuid) && !self::isMyVassal($uid, $fuid))
    	{
    		Logger::debug('it is not my friend, not my subordinate too. %s, %s', $uid, $fuid);
    		return array('ret' => 'err');
    	}
    	else 
    	{
    		Logger::debug('it is my friend or my subordinate.');
    	}
    	
    	// 表配置信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	$mFish = btstore_get()->FISH->toArray();

    	// 通过 uid 获取用户信息
		$allBlueInfo = self::getUserInfo($uid);
		// 养鱼队列信息
		$fishQueue = $allBlueInfo['va_farmfish_queueInfo'];
		// 通过 fuid 获取用户信息
		$fOldFishQueue = self::getFriendInfo($fuid);
		// 更新队列信息
		$fNewFishQueue = self::updateFishStatus($fOldFishQueue);
		$fFishQueue = $fNewFishQueue[$queueId];
		// 看自己是否还有偷鱼次数
		$tftimes = self::subTowValue($mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_DAILYTHIEFCOUNT], $allBlueInfo['farmfish_tftimes']);
		if($tftimes == 0)
		{
			Logger::debug('there is not noTfTimes.');
			return array('ret' => 'noTfTimes', 'queueInfo' => $fNewFishQueue);
		}

		// 看看是不是自己的下属
		$isVassal = self::isMyVassal($uid, $fuid);
		
		// 看看可不可以偷鱼
		$isThief = self::isThief($uid, $fFishQueue, $isVassal);
		if($isThief['ret'] != 'ok')
    	{
    		$fQueueInfo = self::isStolenWish($uid, $fNewFishQueue);
    		$isThief['queueInfo'] = $fQueueInfo;
    		return $isThief;
    	}
    	// 开始偷鱼
		RPCContext::getInstance()->executeTask($fuid, 
		                                       'allblue.modifyStealFishInfoByOther',
		                                       array($uid, $fuid, $queueId));
		Logger::debug('AllBlueLogic::thiefFish end.');
		return array('ret' => 'ok');
    }
    
	/**
     * 去好友家做好事
     */
    public static function wishFish($uid, $fuid, $queueId)
    {
    	Logger::debug('AllBlueLogic::wishFish start.');
		self::checkEnterFish();
    	if($uid == $fuid)
    	{
    		Logger::debug('can not wish myself. %s, %s', $uid, $fuid);
    		return array('ret' => 'err');
    	}
        // 看看是不是自己的好友或下属
    	if(!EnFriend::isMyFriend($uid, $fuid) && 
    		!self::isMyVassal($uid, $fuid))
    	{
    		Logger::debug('it is not my friend, not my subordinate too. %s, %s', $uid, $fuid);
    		return array('ret' => 'err');
    	}
    	// 表配置信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	$mFish = btstore_get()->FISH->toArray();
    	
    	// 通过 uid 获取用户信息
		$allBlueInfo = self::getUserInfo($uid);
		// 养鱼队列信息
		$fishQueue = $allBlueInfo['va_farmfish_queueInfo'];
		// 通过 fuid 获取用户信息
		$fOldFishQueue = self::getFriendInfo($fuid);
		// 更新队列信息
		$fNewFishQueue = self::updateFishStatus($fOldFishQueue);
		$fFishQueue = $fNewFishQueue[$queueId];
    	// 自己今日祝福次数是否已用完
		$wftimes = self::subTowValue($mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_DAILYWISHCOUNT], $allBlueInfo['farmfish_wftimes']);
		if($wftimes == 0)
		{
			$fQueueInfo = self::isStolenWish($uid, $fNewFishQueue);
			Logger::debug('there is not wish times.');
			return array('ret' => 'noWfTimes', 'queueInfo' => $fQueueInfo);
		}
		// 看看可不可以祝福
		$isWish = self::isWish($uid, $fFishQueue);
 		if($isWish['ret'] != 'ok')
    	{
    		Logger::debug('the result isWish is %s.', $isWish);
    		$fQueueInfo = self::isStolenWish($uid, $fNewFishQueue);
    		$isWish['queueInfo'] = $fQueueInfo;
    		return $isWish;
    	}
		// 开始祝福
		RPCContext::getInstance()->executeTask($fuid, 
		                                       'allblue.modifyWishFishInfoByOther',
		                                       array($uid, $fuid, $queueId));	
    	Logger::debug('AllBlueLogic::wishFish end.');
    	return array('ret' => 'ok');
    }
    
    private static function checkQueue($uid, $queueId)
    {
		if($queueId < 0)
		{
			Logger::fatal('Err para:queueId, %d!', $queueId);
			throw new Exception('fake');
		}
		// 通过 uid 获取用户信息
		$allBlueInfo = self::getUserInfo($uid);
		// 养鱼队列信息
		$fishQueue = $allBlueInfo['va_farmfish_queueInfo'];

		// 看看有没有这个队列
		if(!isset($fishQueue[$queueId]))
		{
			Logger::debug('the queue is not exist. queueId = %s', $queueId);
			return array('ret' => 'noQueue');
		}
		// 检查下看看该队列是否开通
    	if($fishQueue[$queueId]['qstatus'] != 1)
    	{
    		// 该队列没有开通或该队列有鱼
			Logger::debug('the queue is not exist. queueId = %s', $queueId);
			return array('ret' => 'noPermission');
    	}
   		// 检查下看看该队列是否有鱼
    	if(!EMPTY($fishQueue[$queueId]['fishid']))
    	{
    		// 该队列没有开通或该队列有鱼
			Logger::debug('the queue have fish. queueId = %s', $queueId);
			return array('ret' => 'noPermission');
    	}
    	return array('ret' => 'ok', 'res' => $allBlueInfo);
    }
    private static function randFiveKrill()
    {
		// 配置表信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	$mFish = btstore_get()->FISH->toArray();
    	
    	// 可以随即的鱼
    	$fishArray = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_GROUPSEAFISH];
    	
    	// 做成鱼的随即数组(鱼ID和权重)
    	$randFishArray = array();
    	for ($i = 0; $i < count($fishArray); $i++)
    	{
    		if(!isset($mFish[$fishArray[$i]]))
    		{
    			continue;
    		}
    		$randFishArray[$mFish[$fishArray[$i]][AllBlueDef::ALLBLUE_FARMFISH_ID]] = 
    					array(AllBlueDef::ALLBLUE_FARMFISH_REFISHINGWEIGHT =>
    						  $mFish[$fishArray[$i]][AllBlueDef::ALLBLUE_FARMFISH_REFISHINGWEIGHT]);
    	}
    	Logger::debug('the fish random array info is %s.', $randFishArray);
    	
    	$krillId = array();
    	if(!EMPTY($randFishArray))
    	{
			$krillId = Util::noBackSample($randFishArray,
									$mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_KRILLCOUNT], 
									AllBlueDef::ALLBLUE_FARMFISH_REFISHINGWEIGHT);
    	}
		return $krillId;
    }
	private static function subTowValue($baseValue, $objValue)
	{
		$times = 0;
		if($baseValue < $objValue)
		{
			$times = 0;
		}
		else 
		{
			$times = $baseValue - $objValue;
		}
		return $times;
	}

	private static function isThief($uid, $fishQueue, $isMyVassal = false)
	{
		Logger::debug('the fish queue is %s.', $fishQueue);
    	// 表配置信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
		// 看看鱼是否有鱼
		if(EMPTY($fishQueue['fishid']))
		{
			Logger::debug('there is not fish.');
			return array('ret' => 'noFish');
		}
		// 好友家是否了开启泡泡保护罩
		if($fishQueue['isboot'] == 1)
    	{
    		Logger::debug('the fish is booting.');
    		return array('ret' => 'booting');
    	}
		// 看看鱼是否已经成熟了
		if(!$isMyVassal && $fishQueue['etime'] > Util::getTime())
		{
			Logger::debug('the fish is not ripe.');
			return array('ret' => 'noRipe');
		}
		// 好友家鱼苗养殖队列被偷次数是否已用完
    	if($fishQueue['tfcount'] >= $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_QUEUETHIEFCOUNT])
    	{
    		Logger::debug('there is not Stolen times.');
    		return array('ret' => 'fNoTfTimes');
    	}
		// 看看自己是否已经偷过该好友该队列的鱼了
		if (isset($fishQueue['thief']))
		{
			for ($i = 0; $i < count($fishQueue['thief']); $i++)
			{
				if($fishQueue['thief'][$i] == $uid)
				{
		    		Logger::debug('there is not Stolen times in the queue.');
		    		return array('ret' => 'qStolen');
				}
			}
		}
		return array('ret' => 'ok');
	}
	private static function isWish($uid, $fishQueue)
	{
		Logger::debug('the fish queue is %s.', $fishQueue);
    	// 表配置信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
		// 好友家鱼苗养殖队列被祝福数是否已用完
    	if($fishQueue['wfcount'] >= $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_QUEUEWISHCOUNT])
    	{
    		Logger::debug('your friend do not has wish times.');
    		return array('ret' => 'fNoWfTimes');
    	}
		// 看看鱼是否有鱼
		if(EMPTY($fishQueue['fishid']))
		{
			Logger::debug('there is not fish.');
			return array('ret' => 'noFish');
		}
		// 看看鱼是否已经成熟了,已经成熟不能被祝福
		if($fishQueue['etime'] <= Util::getTime())
		{
			Logger::debug('the fish has been riped.');
			return array('ret' => 'ripe');
		}
		// 看看自己是否已经祝福过该好友该队列的鱼了
		if (isset($fishQueue['wisher']))
		{
			for ($i = 0; $i < count($fishQueue['wisher']); $i++)
			{
				if($fishQueue['wisher'][$i] == $uid)
				{
		    		Logger::debug('there is not wish times in the queue.');
		    		return array('ret' => 'qWished');
				}
			}
		}
		return array('ret' => 'ok');
	}
	private static function updateFishStatus($fishQueue)
	{
		$fishArray = array();
		foreach ($fishQueue as $key => $value)
		{
			// 队列是否开通
			if($value['qstatus'] != AllBlueDef::ALLBLUE_FISHQUEUE_STATUS_OPEN)
			{
			}
			// 是否有鱼
			else if(EMPTY($value['fishid']))
			{
			}
			// 更新养鱼状态
			else if($value['etime'] <= Util::getTime())
			{
				// 成熟
				$value['fstatus'] = AllBlueDef::ALLBLUE_FISH_STATUS_RIPE;
				$value['btime'] = 0;
				$value['etime'] = 0;
			}
			else if($value['etime'] > Util::getTime() && $value['btime'] <= Util::getTime())
			{
				// 养殖中
				$value['fstatus'] = AllBlueDef::ALLBLUE_FISH_STATUS_FARM;
			} 
			else 
			{
				// 空闲
				$value['fstatus'] = AllBlueDef::ALLBLUE_FISH_STATUS_FREE;
				$value['btime'] = 0;
				$value['etime'] = 0;
			}
			$fishArray[$key] = $value;
		}
		return $fishArray;
	}
	private static function totalGold($costGold, $type)
	{
		if ($costGold > 0)
		{
			Logger::debug('the statistics gold is %d.', $costGold);
			Statistics::gold($type, $costGold, Util::getTime(), TRUE);
		}
	}
	private static function isStolenWish($uid, $fishQueueInfo)
	{
		$ret = array();
		foreach ($fishQueueInfo as $key => $value)
		{
			$isStolen = 0;
			$isWith = 0;
			if (isset($value['wisher']))
			{
				for ($i = 0; $i < count($value['wisher']); $i++)
				{
					if($value['wisher'][$i] == $uid)
					{
			    		Logger::debug('there is not wish times in the queue.');
			    		$isWith = 1;
			    		break;
					}
				}
			}
			if (isset($value['thief']))
			{
				for ($i = 0; $i < count($value['thief']); $i++)
				{
					if($value['thief'][$i] == $uid)
					{
			    		Logger::debug('there is not Stolen times in the queue.');
			    		$isStolen = 1;
			    		break;
					}
				}
			}
			$value['wisher'] = $isWith;
			$value['thief'] = $isStolen;
			$ret[$key] = $value;
		}
		return $ret;
	}
	private static function fishingCount($fishQueue)
	{
		$mAllBlue = btstore_get()->ALLBLUE->toArray();
		$mFish = btstore_get()->FISH->toArray();
		// 鱼ID
		$fishId = $fishQueue['fishid'];
		// 被偷次数
		$tfCount = $fishQueue['tfcount'];
		// 收获的数量 - 被偷次数*每次偷的数量
		$count = $mFish[$fishId][AllBlueDef::ALLBLUE_FARMFISH_GETFISHCOUNT] -
						ceil($mFish[$fishId][AllBlueDef::ALLBLUE_FARMFISH_GETFISHCOUNT] * 
							$mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_THIEFFISHCOUNT]/10000) * $tfCount;
		return $count;
	}

	private static function getFriendInfo($fuid)
	{
		$fAllBlueInfo = self::getUserInfo($fuid);
		// 养鱼队列信息
		if($fAllBlueInfo == false)
		{
			$fFishQueue = AllBlueDao::initFishQueue();
		}
		else 
		{
			if(EMPTY($fAllBlueInfo['va_farmfish_queueInfo']))
			{
				$fFishQueue = AllBlueDao::initFishQueue();
			}
			else 
			{
				$fFishQueue = $fAllBlueInfo['va_farmfish_queueInfo'];	
			}
		}
		return $fFishQueue;
	}

	/**
	 * 
	 * 更新其他用户信息(被偷)
	 * 
	 * @param $uid 给物品用的uid
	 * @param $fuid 被偷者
	 * @param $queueId 队列id
	 */
	public static function modifyStealFishInfoByOther($uid, $fuid, $queueId)
	{
		Logger::debug('AllBlueLogic::modifyStealFishInfoByOther start.');
		// 如果此人恰巧不在线
		$sessionUid = RPCContext::getInstance()->getSession('global.uid');
		if (empty($sessionUid))
		{
			RPCContext::getInstance()->setSession('global.uid', $fuid);
		}

		// 看看是不是自己的下属
		$isVassal = self::isMyVassal($uid, $fuid);
		// 看看是不是自己的好友
    	if(!EnFriend::isMyFriend($uid, $fuid) && !$isVassal)
    	{
    		Logger::debug('it is not my friend, not my subordinate too. %s, %s', $uid, $fuid);
    		return array('ret' => 'err');
    	}

		// 表配置信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	$mFish = btstore_get()->FISH->toArray();

    	// 好友用户信息
		$allBlueInfo = self::getUserInfo($fuid);
		$fOldFishQueue = $allBlueInfo['va_farmfish_queueInfo'];
		$fNewFishQueue = self::updateFishStatus($fOldFishQueue);
		$fFishQueue = $fNewFishQueue[$queueId];
		$isThief = self::isThief($uid, $fFishQueue, $isVassal);

		// 结果
   		$resStolen = array();
   		$resThief = array();
		if($isThief['ret'] != 'ok')
    	{
    		$fQueueInfo = self::isStolenWish($uid, $fNewFishQueue);
    		$ret = $isThief['ret'];
			$resStolen = array('uid' => $fuid, 'queueId' => $queueId);
			$resThief = array('uid' => $fuid, 'queueId' => $queueId, 'queueInfo' => $fQueueInfo);
    	}
    	else 
    	{
    		// 鱼id
    		$fishId = $fFishQueue['fishid'];
    		// 每次偷取获得鱼数量
    		$tfcount = ceil($mFish[$fishId][AllBlueDef::ALLBLUE_FARMFISH_GETFISHCOUNT] * 
								$mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_THIEFFISHCOUNT]/10000);
								
			// 更新自己偷的次数
			RPCContext::getInstance()->executeTask($uid, 
			                                       'allblue.modifyStealFishTimes',
			                                       array());
//			$arr = array('farmfish_tftimes' => new IncOperator(1));
//			AllBlueDao::updateAllBlueInfo($uid, $arr);
			
			// 更新被偷者的数据
			// 该队列被偷的次数
			$fNewFishQueue[$queueId]['tfcount']++; 
			// 小偷信息uid
			$fNewFishQueue[$queueId]['thief'][] = $uid;
			
			Logger::debug('the fNewFishQueue is %s.', $fNewFishQueue);
			MyAllBlue::getInstance()->updateFishQueue($fNewFishQueue);
			
			// 给东西
			$itemTmp = ItemManager::getInstance()->addItem($fishId, $tfcount);
			Logger::debug('the itemid is %s.', $itemTmp);
			Logger::debug('the uid of bag is %s.', $uid);
			$bag = BagManager::getInstance()->getBag($uid);
			$bagRet = $bag->addItems($itemTmp, TRUE);
			Logger::debug('the result of bag is %d.', $bagRet);
			if($bagRet == FALSE)
			{
				Logger::debug('the bag of user is full.');
				$ret = 'noBag';
			}
			else 
			{	
				MyAllBlue::getInstance()->save();
				// 给下属钱,安慰下
				if($isVassal && 
					$fFishQueue['fstatus'] == AllBlueDef::ALLBLUE_FISH_STATUS_FARM)
				{
					$userObj = EnUser::getUserObj($fuid);
					$belly = $mFish[$fishId][AllBlueDef::ALLBLUE_FARMFISH_STEALCOMPEN] * $userObj->getLevel();
					if(!empty($belly) && $userObj->addBelly($belly))
					{
						Logger::debug('the belly is %s give to Subordinate.', $belly);
						$userObj->update();
						// 手动推送数据
						RPCContext::getInstance()->sendMsg(array($fuid), 're.user.updateUser', 
										   array('belly_num' => $userObj->getBelly()));
					}
				}
				$bagInfo = $bag->update();
				$item = array('item_template_id' => $fishId, 'item_num' => $tfcount);

				// 发邮件
				// 被偷者
				$stolenUser = EnUser::getUserObj($fuid);
				// 小偷
				$thiefUser = EnUser::getUserObj($uid);
				try
				{
					if($isVassal && 
						$fFishQueue['fstatus'] == AllBlueDef::ALLBLUE_FISH_STATUS_FARM)
					{
						MailTemplate::sendStealSubordinateFishMsg($uid, $stolenUser->getTemplateUserInfo(), $item);
						MailTemplate::sendStolenSubordinateFishMsg($fuid, $thiefUser->getTemplateUserInfo(), $item, $belly);
					}
					else 
					{
						MailTemplate::sendStealFishMsg($uid, $stolenUser->getTemplateUserInfo(), $item);
						MailTemplate::sendStolenFishMsg($fuid, $thiefUser->getTemplateUserInfo(), $item);
					}
				}
				catch (Exception $e)
				{
					Logger::warning('send mail is exception.');
				}

				$fQueueInfo = self::isStolenWish($uid, $fNewFishQueue);
				$ret = 'ok';
				$resStolen = array('uid' => $fuid, 'queueId' => $queueId, 'tfcount' => $tfcount, 
								   'queueInfo' => $fQueueInfo);
				$resThief = array('uid' => $fuid, 'queueId' => $queueId, 'queueInfo' => $fQueueInfo,
								  'item_template_id' => $fishId, 
								  'item_num' => $tfcount);
			}
    	}
		// 发送推送消息
		RPCContext::getInstance()->sendMsg(array($uid, $fuid), 'fish.steal', array('ret' => $ret, 'res' => $resStolen));
		RPCContext::getInstance()->sendMsg(array($uid), 'fish.add', array('ret' => $ret, 'res' => $resThief));
		Logger::debug('AllBlueLogic::modifyStealFishInfoByOther end.');
	}
	
	/**
	 * 
	 * 更新其他用户信息(祝福)
	 * 
	 * @param $uid 祝福者uid
	 * @param $fuid 被祝福者
	 * @param $queueId 队列id
	 */
	public static function modifyWishFishInfoByOther($uid, $fuid, $queueId)
	{
		Logger::debug('AllBlueLogic::modifyWishFishInfoByOther start.');
		// 如果此人恰巧不在线
		$sessionUid = RPCContext::getInstance()->getSession('global.uid');
		if (empty($sessionUid))
		{
			RPCContext::getInstance()->setSession('global.uid', $fuid);
		}
	    // 看看是不是自己的好友或下属
    	if(!EnFriend::isMyFriend($uid, $fuid) && 
    		!self::isMyVassal($uid, $fuid))
    	{
    		Logger::debug('it is not my friend, not my subordinate too. %s, %s', $uid, $fuid);
    		return array('ret' => 'err');
    	}

		// 表配置信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	$mFish = btstore_get()->FISH->toArray();

		// 好友用户信息
		$allBlueInfo = self::getUserInfo($fuid);
		$fOldFishQueue = $allBlueInfo['va_farmfish_queueInfo'];
		$fNewFishQueue = self::updateFishStatus($fOldFishQueue);
		$fFishQueue = $fNewFishQueue[$queueId];

		$beWill = array('uid' => $fuid, 'queueId' => $queueId);
		$will = array('uid' => $fuid, 'queueId' => $queueId);
		// 看看可不可以祝福
		$isWish = self::isWish($uid, $fFishQueue);
 		if($isWish['ret'] != 'ok')
    	{
    		$fQueueInfo = self::isStolenWish($uid, $fNewFishQueue);
			$ret = $isWish['ret'];
			$beWill['queueInfo'] = $fQueueInfo;
			$will['queueInfo'] = $fQueueInfo;
    	}
    	else 
    	{
			// 更新好友该队列祝福次数和成熟时间
			$fFishQueue['wfcount']++;
			$fFishQueue['etime'] -= $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_WISHSUBTIME];
			$fFishQueue['wisher'][] = $uid;
			$fNewFishQueue[$queueId] = $fFishQueue;
			$fNewFishQueue = self::updateFishStatus($fNewFishQueue);
			// 更新被祝福次数
			MyAllBlue::getInstance()->addBeWishFishTimes();
			// 更新队列信息
			MyAllBlue::getInstance()->updateFishQueue($fNewFishQueue);
			MyAllBlue::getInstance()->save();

			// 祝福获得的belly
			$userObj = EnUser::getUserObj($uid);
			$belly = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_WISHREWARD] * $userObj->getLevel();
			if(!empty($belly) && $userObj->addBelly($belly))
			{
				$userObj->update();
				// 手动推送数据
				RPCContext::getInstance()->sendMsg(array($uid), 're.user.updateUser', 
										   			array('belly_num' => $userObj->getBelly()));
			}
			// 更新自己的祝福次数
			RPCContext::getInstance()->executeTask($uid, 
			                                       'allblue.modifyBeWishFishTimes',
			                                       array());
			// 发邮件
			try
			{
				// 被祝福者
				$wishedUser = EnUser::getUserObj($fuid);
				MailTemplate::sendWishFishMsg($uid, $wishedUser->getTemplateUserInfo(), $fFishQueue['fishid'], 
												$mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_WISHSUBTIME]/60, $belly);
				// 祝福者
				$wishUser = EnUser::getUserObj($uid);
				MailTemplate::sendWishedFishMsg($fuid, $wishUser->getTemplateUserInfo(), $fFishQueue['fishid'], 
												$mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_WISHSUBTIME]/60);
			}
			catch (Exception $e)
			{
				Logger::warning('send mail is exception.');
			}
											
			$fQueueInfo = self::isStolenWish($uid, $fNewFishQueue);
			// 发送推送消息
			$ret = 'ok';
			$beWill['queueInfo'] = $fQueueInfo;
			$will['queueInfo'] = $fQueueInfo;
    	}
    	// 我祝福好友
		RPCContext::getInstance()->sendMsg(array($fuid), 'fish.will', array('ret' => $ret, 'res' => $beWill));
		// 被好友祝福
		RPCContext::getInstance()->sendMsg(array($uid), 'fish.willFriend', array('ret' => $ret, 'res' => $will));
		Logger::debug('AllBlueLogic::modifyWishFishInfoByOther end.');
	}

	/**
	 * 
	 * 更新自己的偷鱼次数
	 */
	public static function modifyStealFishTimes()
	{
		Logger::debug('AllBlueLogic::modifyStealFishTimes start.');
		MyAllBlue::getInstance()->addThiefFishTimes();
		MyAllBlue::getInstance()->save();
		Logger::debug('AllBlueLogic::modifyStealFishTimes end.');
	}
	
	/**
	 * 
	 * 更新自己的祝福次数
	 */
	public static function modifyBeWishFishTimes()
	{
		Logger::debug('AllBlueLogic::modifyBeWishFishTimes start.');
		MyAllBlue::getInstance()->addWishFishTimes();
		MyAllBlue::getInstance()->save();
		Logger::debug('AllBlueLogic::modifyBeWishFishTimes end.');
	}
	
	/**
	 * 
	 * 修改数据(首次开启allblue采集,把采集时间修改成当前系统时间)
	 */
	public static function initAllBlueCollectTime()
	{
		MyAllBlue::getInstance()->initAllBlueCollectTime();
		MyAllBlue::getInstance()->save();
	}
	
	/**
	 * 
	 * 获得自己的下属
	 */
	public static function getSubordinateList($uid)
	{
		Logger::debug('AllBlueLogic::getSubordinateList start.');
		self::checkEnterFish();
		// 取得自己的下属
		$vassalUid = EnVassal::getArrVsl($uid);
		if(EMPTY($vassalUid))
		{
			Logger::debug('There is not subordinate.');
			return;
		}
		$mapUidUser = Util::getArrUser($vassalUid,
						array ('uid', 'uname', 'status', 'utid', 'level'));
		$vassalRet = array();
		foreach ($vassalUid as $vUid)
		{
			if (!isset($mapUidUser[$vUid]))
			{
				Logger::fatal ( "user:%d not found in db", $vUid );
				continue;
			}
			$arrUser = $mapUidUser[$vUid];
			$arrVassal ['uname'] = $arrUser ['uname'];
			$arrVassal ['utid'] = $arrUser ['utid'];
			$arrVassal ['level'] = $arrUser ['level'];
			$vassalRet [$vUid] = $arrVassal;
		}
		$uidArray = array_keys($vassalRet);
		$userInfo = AllBlueDao::getUserInfoByIn($uidArray);
		Logger::debug('the vassal user info is %s.', $userInfo);
		
   		// 配置表信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	$mFish = btstore_get()->FISH->toArray();
    	// 每天祝福鱼次数
    	$baseWfcount = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_DAILYWISHCOUNT];
    	// 每天偷鱼次数
    	$baseTfcount = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_DAILYTHIEFCOUNT];
    	// 每个队列可以被祝福几次
    	$queueWishFishTimes = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_QUEUEWISHCOUNT];
		$htidTempLate = UserConf::$USER_INFO;
    	$resultUserInfo = array();
    	if(EMPTY($userInfo))
    	{
    		foreach ($vassalRet as $vUid => $vassalInfo)
    		{
				$vassalInfo['htid'] = $htidTempLate[$vassalInfo['utid']][1];
				unset($vassalInfo['utid']);
		    	// 是否可以偷鱼
		    	$vassalInfo['isthief'] = 0;
		    	// 是否可以祝福
		    	$vassalInfo['iswish'] = 0;
		    	$vassalInfo['uid'] = $vUid;
		    	$resultUserInfo[$vUid] = $vassalInfo;
    		}
    	}
    	else 
    	{
			foreach ($vassalRet as $vUid => $vassalInfo)
			{
				$isthief = 0;
				$iswish = 0;
				if(isset($userInfo[$vUid]))
				{
					// 养鱼队列信息
					$fishQueue = $userInfo[$vUid]['va_farmfish_queueInfo'];
					// 因为此列是此次新加的,所以要是没有值的话,得初始化一下
					if(EMPTY($fishQueue))
					{
						$fishQueue = AllBlueDao::initFishQueue();
					}
					
			    	foreach ($fishQueue as $value)
			    	{
			    		// 检查是否可以被偷
			    		$isThief = self::isThief($uid, $value, true);
			    		if($isThief['ret'] == 'ok' && $isthief != 1)
			    		{
			    			$isthief = 1;
			    		}
			    		// 检查是否可以被祝福
						$isWish = self::isWish($uid, $value);
			    		if($isWish['ret'] == 'ok' && $iswish != 1)
			    		{
			    			$iswish = 1;
			    		}
			    	}
				}
				// tid -> htid
				$vassalInfo['htid'] = $htidTempLate[$vassalInfo['utid']][1];
				unset($vassalRet[$vUid]['utid']);
		    	// 是否可以偷鱼
		    	$vassalInfo['isthief'] = $isthief;
		    	// 是否可以祝福
		    	$vassalInfo['iswish'] = $iswish;
		    	$vassalInfo['uid'] = $vUid;
		    	$resultUserInfo[$vUid] = $vassalInfo;
			}
    	}
		Logger::debug('AllBlueLogic::getSubordinateList end.');
		return $resultUserInfo;
	}
	
	private static function isMyVassal($uid, $vUid)
	{
		// 取得自己的下属
		$vassalUid = EnVassal::getArrVsl($uid);
		Logger::debug('the my vassal is %s, %s.', $vassalUid, $vUid);
		return in_array($vUid, $vassalUid);
	}
	
	/**
	 * 跟但前用户是否为同一个港口
	 *
	 * @param $uid 用户uid
	 * @param $otherUid 用户uid
	 */
	private static function isSamePort($uid, $otherUid)
	{
		$port = new PortBerth($uid);
		$pid1 = $port->getPort();
		$port = new PortBerth($otherUid);
		$pid2 = $port->getPort();
		Logger::debug('Port info is uid1:%s port1:%s, uid2:%s port2:%s.', $uid, $pid1, $otherUid, $pid2);
		return $pid1==$pid2;
	}
	
	private static function isConquer($uid, $vUid)
	{
		$isConquer = 2;
		// 是否可以被征服
    	if(self::isMyVassal($uid, $vUid))
    	{
    		// 是自己的下属的话, 显示'放'
    		$isConquer = 0;
    	}
    	else if(self::isSamePort($uid, $vUid))
    	{
    		// 不是自己的下属并且同一港口, 显示'征'
    		$isConquer = 1;
    	}
    	return $isConquer;
	}
	
	/**
	 * 获取下属养殖列表
	 * 
	 */
	public static function getSubordinateFishList($uid)
	{
		Logger::debug('AllBlueLogic::getSubordinateList start.');
		self::checkEnterFish();
		// 取得自己的下属
		$vassalUid = EnVassal::getArrVsl($uid);
		if(EMPTY($vassalUid))
		{
			Logger::debug('There is not subordinate.');
			return;
		}
		$mapUidUser = Util::getArrUser($vassalUid,
						array ('uid', 'uname'));
		$vassalRet = array();
		foreach ($vassalUid as $vUid)
		{
			if (!isset($mapUidUser[$vUid]))
			{
				Logger::fatal ( "user:%d not found in db", $vUid );
				continue;
			}
			$arrUser = $mapUidUser[$vUid];
			$arrVassal ['uname'] = $arrUser ['uname'];
			$vassalRet [$vUid] = $arrVassal;
		}
		$uidArray = array_keys($vassalRet);
		$userInfo = AllBlueDao::getUserInfoByIn($uidArray);
		Logger::debug('the vassal user info is %s.', $userInfo);
    	$resultUserInfo = array();
    	if(EMPTY($userInfo))
    	{
    		return;
    	}
    	else 
    	{
			foreach ($vassalRet as $vUid => $vassalInfo)
			{
				$fishInfo = array();
				if(isset($userInfo[$vUid]))
				{
					// 养鱼队列信息
					$fishQueue = $userInfo[$vUid]['va_farmfish_queueInfo'];
					// 空队列就直接返回
					if(EMPTY($fishQueue))
					{
						continue;
					}
					Logger::debug('the vassal user fishQueue is %s.', $fishQueue);
			    	foreach ($fishQueue as $value)
			    	{
			    		// 该队列没有鱼
			    		if(EMPTY($value['fishid']) || 
			    		   EMPTY($value['btime']) ||
			    		   $value['etime'] <= Util::getTime())
						{
							continue;
						}
						else
						{
							$fishInfo[] = array('fishid' => $value['fishid'],
												'beginTime' => $value['btime']);
						}
			    	}
				}
				if(!empty($fishInfo))
				{
					$resultUserInfo[] =  array('uid' => $vUid,
					                           'uname' => $vassalInfo['uname'], 
					                           'fishinfo' => $fishInfo);
				}
			}
    	}
		Logger::debug('AllBlueLogic::getSubordinateList end.');
		return $resultUserInfo;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
