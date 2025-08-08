<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AbyssCopy.class.php 40633 2013-03-12 07:40:54Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/AbyssCopy.class.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-12 15:40:54 +0800 (二, 2013-03-12) $
 * @version $Revision: 40633 $
 * @brief 
 *  
 **/


/**
 * 深渊副本
 *
 */
class AbyssCopy implements IAbyssCopy
{
	
	/* 副本数据 
	 	uuid:21
	 	copyId: 10001
	 	userList:
	 		{
	 			1:
	 			{	 				
	 				'online' => true
	 				'visiCount'=>100 //用户在进入副本之前，设置的显示多少人
					'fightNum' => 10,
					'canBtTime' => 0,
					'energy' => 10,
					'atkAdd' => 0,
					'defAdd' => 0,			
	 			}
	 			...
	 		}
	 	rooms:
	 		{
	 			1:	//房间1中有哪些东西
	 			{	
	 				teleports://传送阵
	 				{
	 					10001:0	 					
	 				}
	 				boxes:
	 				{
	 					20001:
	 					{
	 						state:1/2 // 未打开/打开
	 					}
	 				}
	 				vials:	//小药品
	 				{
	 					30001:0
	 				}
	 				enemyAnchors:		//出怪物的坑
	 				{
	 					40001:
	 					{
	 						armyId:1	//出的是啥怪物
	 						state:1		//1:可见； 2：可打
	 						fightNum:1	//被攻击次数
	 						beatenNum:0 //被打败的次数
	 						hpArr:
	 						{
	 							10027251:100
	 						}
	 					}	 					
	 				}
	 				quests	//奇遇问题
	 				{
	 					40001:
	 				}
	 				
	 			}
	 		}
 		card:	//通关后，翻牌时才会有
 			{
 				uid:
 				[	
 					cardId：
 					{
 						index:0 //0表示此牌还没有被翻开，>0表示对应第几张牌
 						drop:0
 					}
 				]
 			}

	 */
	
	//战斗回调函数，传递参数
	private static $gCurArmyId = 0;



	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::getUserInfo()
	 */
	public function getUserInfo()
	{
		$uid = RPCContext::getInstance()->getUid();
		
		$myAbyssCopy = EnAbyssCopy::getMyAbyss($uid);
		$userInfo = $myAbyssCopy->getInfo();
		
		$returnData = array(
				'copyList' => array(),
				'weekBuyNum' => $userInfo['weekBuyNum'],
				'weekClgNum' => $userInfo['weekClgNum'],
				'weekExeNum' => $userInfo['weekExeNum'],
				);

		$allCopyConf = self::getCopyConf(0);
		foreach($allCopyConf as $copyId => $copyConf)
		{
			
			if( $copyConf['preArmyId'] == 0 
					|| CopyLogic::isEnemyDefeated($copyConf['preArmyId']) )
			{
				$copystate = AbyssCopyDef::$COPY_STATE['CANT_ENTER'];
			}
			else
			{
				//如果连需要打败的部队都没打败，就告诉前端不用显示这个副本了
				continue;
			}
			
			if( $copyConf['preAbyssCopyId'] == 0 ||
				in_array( $copyConf['preAbyssCopyId'], $userInfo['passed']) )
			{
				$copystate = AbyssCopyDef::$COPY_STATE['OPEN_ENTER'];
			}
			
			$returnData['copyList'][$copyId] = $copystate;
		}		
		
		return $returnData;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::buyChallengeNum()
	 */
	public function buyChallengeNum($num)
	{
		$uid = RPCContext::getInstance()->getUid();
	
		$userConf = AbyssCopy::getUserConf();
		
		$userObj = EnUser::getUserObj($uid);
		$vipConf = btstore_get()->VIP;
		$weekMaxBuy = $vipConf[$userObj->getVip()]['abyss_max_buy'];
		
		$myAbyssCopy = EnAbyssCopy::getMyAbyss($uid);
		$userCopyInfo = $myAbyssCopy->getInfo();
		
		if( $num + $userCopyInfo['weekBuyNum'] > $weekMaxBuy )
		{
			Logger::debug('exceed week buy num:%d', $weekMaxBuy);
			throw new Exception('fake');
		}
		if( $num + $userCopyInfo['weekClgNum'] > $userConf['maxChallengeNum'])
		{
			Logger::debug('exceed week challenge num:%d',$userConf['maxChallengeNum']);
			throw new Exception('fake');
		}
		
		$needGold = $num * $userConf['buyCostGold'];
		if (!$userObj->subGold($needGold))
		{
			Logger::debug('fail to buy num, the gold is not enough');
			throw new Exception('fake');
		}
		$userObj->update();
		Statistics::gold(StatisticsDef::ST_FUNCKEY_ABYSS_BUY_CHALLENGE, $needGold, Util::getTime());
		
		$curClgNum = $myAbyssCopy->buyChallengeNum($num);
		$myAbyssCopy->update();
		
		Logger::info('uid:%d buy:%d cur:%d', $uid, $num, $curClgNum);
		return array( 'ret' => 'ok', 'needGold' => $needGold, 'curClgNum' => $curClgNum);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::create()
	 */
	public function create($copyId, $isAutoStart, $joinLimit)
	{		
		$uid = RPCContext::getInstance()->getUid();
		
		//1. 检查创建者的数据
		$myAbyssCopy = EnAbyssCopy::getMyAbyss($uid);
		$ret = $myAbyssCopy->canEnterCopy($copyId);
		if( false == $ret )
		{
			Logger::debug('uid:%d cant create copy:%d', $uid, $copyId);
			throw new Exception('fake');
		}

		$isAutoStart = false;//深渊本暂时不能自动开始，因为目前如果设置自动开始lcserver会直接把请求发到copy模块
		
		//2. 取一下战斗力
		$userObj = EnUser::getUserObj($uid);
		RPCContext::getInstance()->setSession("global.fightForce", $userObj->getFightForce());		
		
		//3. 通知lcserver
		RPCContext::getInstance()->createTeam($isAutoStart, $joinLimit, 'abysscopy.start');
			
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::join()
	 */
	public function join($copyId, $teamId)
	{
		//1. 检查用户数据
		$uid = RPCContext::getInstance()->getUid();
		$myAbyssCopy = EnAbyssCopy::getMyAbyss($uid);
		
		$ret = $myAbyssCopy->canEnterCopy($copyId);
		if( false == $ret )
		{
			Logger::debug('uid:%d cant enter copy:%d', $uid, $copyId);
			throw new Exception('fake');
		}

		//2. 取一下战斗力
		$userObj = EnUser::getUserObj($uid);
		RPCContext::getInstance()->setSession("global.fightForce", $userObj->getFightForce());

		//3. 通知lcserver
		RPCContext::getInstance()->joinTeam($teamId);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::start()
	 */
	public function start($uidList, $copyId)
	{
		$copyConf = self::getCopyConf($copyId);
		if( !isset($copyConf))
		{
			Logger::info('no copy:%d', $copyId);
			throw new Exception('fake');
		}
		
		//1. 申请id
		$idNum = count($copyConf['roomIdArr']) + 1;
		$ids = IdGenerator::nextMultiId("abyss_id", $idNum);	
		if(count($ids) != $idNum)
		{
			Logger::fatal('apply id failed. return:%s', $ids);
			throw new Exception('inter');
		}
		
		//2. 生成副本数据，包含第一个房间中的数据
		$uuid = AbyssCopyMem::initCopyData($copyId, $ids, $uidList);
				
		AbyssCopyMem::getInstance()->saveCopyData();
				
		Logger::info('create abyss copy in mem. copyId:%d, uuid:%d, uids:%s', $copyId, $uuid, $uidList);
		
		//更新一下每个玩家的数据
		foreach($uidList as $id)
		{
			$myAbyssCopy = EnAbyssCopy::getMyAbyss($id);
			$myAbyssCopy->joinCopy($uuid);
			$myAbyssCopy->update();
		}
		
		//3. 通知一下大家进场吧
		$msg = array();
		RPCContext::getInstance()->sendMsg( $uidList, AbyssCopyConf::$FRONT_CALLBACKS['start'], $msg);
		
		return 'ok';
	}
	
	
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::enterRoom()
	 */
	public function enterRoom($roomId, $x, $y)
	{
		$uid = RPCContext::getInstance()->getUid();
		
		$dataChanged = $this->userInCopy();			
		
		$roomInfo = AbyssCopyMem::getInstance()->getRoom($roomId);
		
		$townId = $roomInfo['townId'];
		$roomConf = self::getRoomConf($roomId);
		$townTemplateId = $roomConf['townId'];	
		
		Logger::debug("curTownId:%d", RPCContext::getInstance()->getSession('global.townId') );
		
		$arr = City::userInfoForEnterTown();
		RPCContext::getInstance()->enterTown ( $townId, $x, $y, $arr, NULL, $townTemplateId);
		RPCContext::getInstance()->setSession('global.townId', $townId);
		
		if($dataChanged)
		{
			AbyssCopyMem::getInstance()->saveCopyData();
		}
		
		foreach($roomInfo['enemyAnchors'] as $key => $value)
		{
			unset($roomInfo['enemyAnchors'][$key]['hpArr']);
		}
		
		Logger::debug('uid:%d enter room:%d', $uid, $roomId);
		return $roomInfo;
	}
	
	public function leaveRoom()
	{
		RPCContext::getInstance()->leaveTown ();
		RPCContext::getInstance()->setSession('global.townId', 0);
	}
	
	public function leave()
	{
		
		$this->userLeaveCopy();		
		
		RPCContext::getInstance()->setSession('global.townId', 0);
		RPCContext::getInstance()->leaveTown();	

		RPCContext::getInstance()->setSession(AbyssCopyDef::SESSION_COPY_UUID, 0);
		
		//恢复其显示人数的设置
		$uid = RPCContext::getInstance()->getUid();
		$userInfo = AbyssCopyMem::getInstance()->getUser($uid);
		RPCContext::getInstance ()->setSession ( 'global.visibleCount', $userInfo['visiCount'] );			

	}
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::getAllUser()
	 */
	public function getAllUser()
	{
		$allUserInfo = AbyssCopyMem::getInstance()->getAllUser();
		$creator = AbyssCopyMem::getInstance()->getCreator();
		
		$returnData = array();
		foreach($allUserInfo as $uid => $value)
		{
			$userObj = EnUser::getUserObj($uid);
			$returnData[$uid] = array(
					'uname' => $userObj->getUname(),
					'level' => $userObj->getMasterHeroLevel(),
					'htid' => $userObj->getMasterHeroObj()->getHtid(),
					'fightNum' => $value['fightNum'],
					'energy' => $value['energy'],
					'canBtTime' => $value['canBtTime'],
					'isExe' => $value['isExe'],
					);
			if($uid == $creator)
			{
				$returnData[$uid]['captain'] = true;
			}
		}
		return $returnData;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::onTrigger()
	 */
	public function onTrigger($triggerId)
	{
		if($triggerId <= 0)
		{
			Logger::debug('invalid param');
			throw new Exception('fake');
		}
		$triggerConf = self::getTriggerConf($triggerId);
		if( !isset($triggerConf) )
		{
			Logger::info('not found trigger:%d', $triggerId);
			throw new Exception('fake');
		}
		
		$uid = RPCContext::getInstance()->getUid();
				
		$memInst = AbyssCopyMem::getInstance();	
		if($memInst->isCopyPassed())
		{
			Logger::debug('copy passed');
			throw new Exception('fake');
		}
		$roomId = $memInst->getCurRoom();
		
		//检查这个机关在不在
		$triggerInfo = $memInst->getTrigger($roomId, $triggerId);
		
		$modifyData = array();
		//检查这个机关能不能被点
		$objType = AbyssCopyDef::$OBJ_TYPE['TRIGGER'];
		switch($triggerConf['type'])
		{
			case AbyssCopyDef::$TRIGGER_TYPE['BOX']:
				$objType = AbyssCopyDef::$OBJ_TYPE['BOX'];
				if($triggerInfo['state'] != AbyssCopyDef::$BOX_STATE['CAN_OPEN'])
				{
					Logger::info('cant open box:%d, state:%d', $triggerId, $triggerInfo['state']);
					throw new Exception('fake');
				}
				//如果不准备删掉这个东西，就修改一下状态
				if (!$triggerConf['delAfter'] )
				{
					$modifyData['state'] = AbyssCopyDef::$BOX_STATE['OPENED'];
				}
				break;
			case AbyssCopyDef::$TRIGGER_TYPE['VIAL']:
				$objType = AbyssCopyDef::$OBJ_TYPE['VIAL'];
				break;
			default:
				Logger::fatal('invalid type:%d, trigger:%d', $triggerConf['type'], $triggerId);
				break;
		}
		
		//点完会有很多不同类型的事情发生		
		$returnData = array('ret' => 'ok');		
		switch($triggerConf['useType'])
		{
			//新机关
			case AbyssCopyDef::$TRIGGER_USE_TYPE['ADD_NEW']:
				$memInst->addTrigger($roomId, $triggerConf['newTriggerId']);
				break;
				
			//恢复
			case AbyssCopyDef::$TRIGGER_USE_TYPE['RECOVER']:
				$deltData = array(
					'energy' => $triggerConf['addEnergy'],
					'fightNum' => $triggerConf['addFightNum'],
				);
				
				if($triggerConf['addAll'])
				{					
					$memInst->modifyAllUser(NULL, $deltData);
				}
				else
				{
					$memInst->modifyUser($uid, NULL, $deltData);
				}
				break;
				
			//攻防增益
			case AbyssCopyDef::$TRIGGER_USE_TYPE['GAIN']:
				$deltData = array(
					'atkAdd' => $triggerConf['addAtkRatio'],
					'defAdd' => $triggerConf['addDefRatio'],
				);
				if($triggerConf['addAll'])
				{
					$memInst->modifyAllUser(NULL, $deltData);
				}
				else
				{
					$memInst->modifyUser($uid, NULL, $deltData);
				}
				break;
			
			//奇遇问题
			case AbyssCopyDef::$TRIGGER_USE_TYPE['QUESTION']:
				$prob = rand(1, AbyssCopyDef::COEF_BASE);
				if( $prob <= $triggerConf['questionProb'] )
				{
					$questId = self::getRandQuestion();
					$memInst->addQuestion($roomId, $triggerId, $questId);
					$returnData['questId'] = $questId;
				}
				else
				{
					Logger::debug('rand question failed');
				}
				break;
				
			//触发战斗
			case AbyssCopyDef::$TRIGGER_USE_TYPE['BATTLE']:
				$prob = rand(0, AbyssCopyDef::COEF_BASE);
				if( $prob <= $triggerConf['battleProb'] )
				{
					Logger::debug('trigger battle succeed');
					$armyId = $triggerConf['armyId'];
					$ret = $this->doAttackArmy($roomId, 0, $armyId, NULL, NULL);
					if($ret['ret'] != 'ok')
					{
						//战斗失败就直接返回，啥也没干
						Logger::info('cant attack, trigger:%d, ret=%s', $triggerId, $ret['ret']);
						$returnData = array(
								'ret' => 'btf',
								'bt' => $ret['ret']
								);
						return $returnData;
					}				
					$returnData['btStr'] = $ret['btStr'];
				}
				else
				{
					Logger::debug('trigger battle failed');
				}
				break;
			
			//解密
			case AbyssCopyDef::$TRIGGER_USE_TYPE['PUZZLE']:
				Logger::fatal('not support PUZZLE');
				throw new Exception('inter');
				break;
			default:
				Logger::fatal('invalid use type:%d, trigger:%d', $triggerConf['useType'], $triggerId);
				break;
		}
		if( !empty($modifyData))
		{
			$memInst->modifyTrigger($roomId, $triggerId, $modifyData);
		}
				
		if ( $triggerConf['delAfter'] )
		{
			$memInst->delTrigger($roomId, $triggerId);
		}
		if( $triggerConf['openTeleport'] > 0)
		{
			$memInst->addTeleport($roomId, $triggerConf['openTeleport']);
		}
		
		$this->checkAddScore($roomId, 0, $triggerId);
		
		//广播一下数据
		$cause = array(
				'uid' => $uid,
				'objId' => $triggerId,
				'objType' => $objType,
		);
		
		$this->broadcast(AbyssCopyConf::$FRONT_CALLBACKS['modifyObj'], 
							array('data'=>$memInst->getObjModify(), 'cause' => $cause ) );
		$this->broadcast(AbyssCopyConf::$FRONT_CALLBACKS['modifyUser'], 
							array('data'=>$memInst->getUserModify(), 'cause' => $cause ) );
				
		$memInst->saveCopyData();
		return $returnData;
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::onQuestion()
	 */
	public function onQuestion($triggerId, $answer)
	{
		if($triggerId <= 0 || $answer <= 0 )
		{
			Logger::debug('invalid param');
			throw new Exception('fake');
		}
		//检查参数
		$triggerConf = self::getTriggerConf($triggerId);
		if( !isset($triggerConf) )
		{
			Logger::info('not found trigger:%d', $triggerId);
			throw new Exception('fake');
		}
		if($triggerConf['useType'] != AbyssCopyDef::$TRIGGER_USE_TYPE['QUESTION'])
		{
			Logger::info('not a question trigger:%d', $triggerId);
			throw new Exception('fake');
		}
		
		//获取副本数据
		$uid = RPCContext::getInstance()->getUid();	
		$memInst = AbyssCopyMem::getInstance();
		if($memInst->isCopyPassed())
		{
			Logger::debug('copy passed');
			throw new Exception('fake');
		}

		$roomId = $memInst->getCurRoom();
		$roomInfo = $memInst->getRoom($roomId);
				
		//检查这个问题在不在
		$questId = $memInst->getQuestion($roomId, $triggerId);
				
		$questConf = self::getQuestionConf($questId);
		if(!isset($questConf))
		{
			Logger::fatal('not found question:%d', $questId);
			throw new Exception('config');
		}
		
		$getReward = true;
		$returnData = array('ret' => 'ok');
		//碰到战斗型问题就倒霉了，打赢了才能拿东西
		if($questConf['type'] == AbyssCopyDef::$QUESTION_TYPE['BATTLE'] &&
				$questConf['armyIdArr'][$answer] > 0)
		{
			$armyId = $questConf['armyIdArr'][$answer];
			$ret = $this->doAttackArmy($roomId, 0, $armyId, NULL, NULL);
			if($ret['ret'] != 'ok')
			{
				//战斗失败就直接返回，啥也没干
				Logger::info('cant attack when question, trigger:%d, ret=%s', $triggerId, $ret['ret']);
				$returnData = array(
						'ret' => 'btf',
						'bt' => $ret['ret']
				);
				return $returnData;
			}
			$returnData['btStr'] = $ret['btStr'];
			$getReward = ! $ret['isWin'];
			Logger::debug('question battle win:%d',$ret['isWin']);		
		}
		
		if($getReward)
		{
			$rewardArr = $questConf['rewardArr'][$answer];
			$deltData = array();
			foreach($rewardArr as $reward)
			{
				switch($reward[0])
				{
					case AbyssCopyDef::$REWARD_TYPE['FIGHT_NUM']:
						$deltData['fightNum'] = $reward[1];
						break;
					case AbyssCopyDef::$REWARD_TYPE['ENERGY']:
						$deltData['energy'] = $reward[1];
						break;
					case AbyssCopyDef::$REWARD_TYPE['BATTLE_GAIN']:
						$deltData['atkAdd'] = $reward[1];
						$deltData['defAdd'] = $reward[1];
						break;
				}
			}
			if(!empty($deltData))
			{				
				$memInst->modifyAllUser(NULL, $deltData);
			}			
		}
		
		$memInst->delQuestion($roomId, $triggerId);
				
		$memInst->saveCopyData();
		
		$cause = array(
				'uid' => $uid,
				'objId' => $triggerId,
				'objType' => AbyssCopyDef::$OBJ_TYPE['BOX'],	//这个地方默认是宝箱，如果以后还有其他玩意也能出奇遇，就得改了
		);
		$this->broadcast(AbyssCopyConf::$FRONT_CALLBACKS['modifyUser'],
				array('data'=>$memInst->getUserModify(), 'cause' => $cause) );			
		
		return $returnData;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::onArmy()
	 */
	public function onArmy($anchorId, $npcTeamID = null, $heroList = null)
	{
		
		$uid = RPCContext::getInstance()->getUid();

		$memInst = AbyssCopyMem::getInstance();
		if($memInst->isCopyPassed())
		{
			Logger::debug('copy passed');
			throw new Exception('fake');
		}
		//检查参数
		$roomId = $memInst->getCurRoom();
		
		//检查一下这个怪物是不是这个房间里的
		$anchorInfo = $memInst->getEnemyAnchor($roomId, $anchorId);
	
		if($anchorInfo['state'] != AbyssCopyDef::$ENEMY_STATE['CAN_ATK'])
		{
			Logger::info('cant attack anchor:%d, room:%d', $anchorId, $roomId);
			throw new Exception('fake');
		}
		$armyId = $anchorInfo['armyId'];
		$armyConf = self::getArmyConf($armyId);
		if (!isset($armyConf) )
		{
			Logger::info('army:%d not found. anchorId:%d', $armyId, $anchorId);
			throw new Exception('config');
		}					
		

		$skipBattle = false;
		//打了多次以后，就可以直接胜利了
		if( $armyConf['winByManyAtk'] > 0 && $anchorInfo['fightNum'] >= $armyConf['winByManyAtk']  )
		{
			$skipBattle = true;
			Logger::info('win by many atk');
		}
		
		$ret = $this->doAttackArmy($roomId, $anchorId, $armyId, $npcTeamID, $heroList, true, $skipBattle);
		if($ret['ret'] != 'ok')
		{
			return $ret;			
		}
		$isWin = $ret['isWin'];			
		$battleStr = $ret['btStr'];			
	
		if($isWin)
		{
			//胜利后会有积分奖励
			//加积分的操作需要在refreshRoomAfterFight之前做，因为有可能refreshRoomAfterFight时，把anchor删掉
			$this->checkAddScore($roomId, $anchorId, 0);
		}
		
		//处理一下战斗之后对副本数据的影响 
		$this->refreshRoomAfterFight($roomId, $anchorId, $isWin);			
		
		if($isWin)
		{
			//检查是否干掉当前房间所有怪物
			$this->checkCleanRoom($roomId);
			//检查是否通关
			$this->checkPassCopy($anchorId);
		}
		
		//广播一下数据
		$cause = array(
				'uid' => $uid,
				'objId' => $anchorId,
				'objType' => AbyssCopyDef::$OBJ_TYPE['ENEMY'],
		);
		$this->broadcast(AbyssCopyConf::$FRONT_CALLBACKS['modifyObj'], 
							array('data'=>$memInst->getObjModify(), 'cause' => $cause ) );
		$this->broadcast(AbyssCopyConf::$FRONT_CALLBACKS['modifyUser'], 
							array('data'=>$memInst->getUserModify(), 'cause' => $cause ) );						
		
		$memInst->saveCopyData();
		
		return array('ret' => 'ok', 'btStr' => $battleStr);
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::getCardInfo()
	 */
	public function dealCard()
	{
		//1. 获取数据
		$uid = RPCContext::getInstance()->getUid();
		
		$memInst = AbyssCopyMem::getInstance();		
		if( !$memInst->isCopyPassed() )
		{
			Logger::debug('copy not passed');			
			throw new Exception('fake');
		}
		
		$userInfo = $memInst->getUser($uid);
		if($userInfo['isExe'])
		{
			Logger::debug('exe copy');
			throw new Exception('fake');
		}		
		
		$copyId = $memInst->getCopyId();
		$copyConf = self::getCopyConf($copyId);
		$cardConfList = self::getCardConf(0);
		
		//2. 检查
		$userCardInfo = $memInst->getUserCardInfo($uid);
		if(  is_array($userCardInfo) )
		{
			Logger::debug('already deal card');
			throw new Exception('fake');
		}
		if(  empty($userCardInfo) &&  $userCardInfo != NULL)
		{
			Logger::debug('already reward card');
			throw new Exception('fake');
		}
		
		//3. 根据用户的积分，选择牌
		$cardIdList = array();
		//rand一下，有没有宝物牌
		$userNum = $memInst->getUserNum();
		$copyScore = $memInst->getCopyScore();
		$jewelryProb = self::getMatchLevel($copyConf['dropJewelryWeightArr'][$userNum], $copyScore);
		$diced = rand(1, AbyssCopyDef::COEF_BASE);
		
		Logger::debug('diced:%d prob:%d', $diced, $jewelryProb);
		if($diced <= $jewelryProb)
		{			
			$arr = self::getValues($cardConfList, $copyConf['dropJewelryArr']);
			$ret =  Util::noBackSample($arr, 1, 'chooseWeight');
			
			$cardIdList[] = $ret[0];
		}

		//随机取几张普通牌
		$arr = self::getValues($cardConfList, $copyConf['dropNormArr']);
		$ret =  Util::noBackSample($arr, AbyssCopyConf::CARD_NUM - count($cardIdList), 'chooseWeight');
		$cardIdList = array_merge($cardIdList, $ret);
		
		Logger::debug('card list:%s', $cardIdList);
		
		//4. 生成当前用户的卡牌信息
		$userCardInfo = array();
		foreach( $cardIdList  as $id)
		{
			$userCardInfo[$id] = array('index' => 0);
			
			$card = $cardConfList[$id];
			//如果要掉东西，就掉吧。最后需要展示所有牌上掉落的物品
			if($card['dropId'] > 0 )
			{
				$dropItem = Drop::dropItem($card['dropId']);				
				if(!empty($dropItem))
				{									
					$dropItem = Util::arrayIndexCol($dropItem, DropDef::DROP_ITEM_TEMPLATE_ID, DropDef::DROP_ITEM_NUM);
					$userCardInfo[$id]['drop'] = $dropItem;
					Logger::debug('card:%d, drop:%s', $card['dropId'], $dropItem);										
				}
			}	
		}
		
		$memInst->setUserCardInfo($uid, $userCardInfo);		
		$memInst->saveCopyData();
		
		//5. 返回所有人的卡牌信息
		$allCardInfo = $memInst->getUserCardInfo(0);
		$returnData = array(
				$uid => array(),
				);
		//自己的所有牌
		$index = 1;
		foreach($userCardInfo as $cardId => $value)
		{
			$info = array(
					'cardId' => $cardId,
			);
			if(isset($value['drop']))
			{
				$info['drop'] = $value['drop'];
			}	
			$returnData[$uid][$index++] = $info;
		}
		//别人已经翻开的牌
		foreach($allCardInfo as $id => $userInfo)
		{
			if($id == $uid)
			{
				continue;
			}
			$info = array();
			foreach($userInfo as $cardId => $value)
			{			
				$index = $value['index'];
				if($index > 0 )
				{
					$info[$index] = array(
							'cardId' => $cardId,
							);
					if(isset($value['drop']))
					{
						$info[$index]['drop'] = $value['drop'];
					}
				}
			}
			$returnData[$id] = $info;
		}

		return $returnData;		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::flopCard()
	 */
	public function flopCard($index)
	{
		//1. 检查
		if($index <= 0 || $index > AbyssCopyConf::CARD_NUM)
		{
			Logger::info('invalid index:%d', $index);
			throw new Exception('fake');
		}		
		
		$uid = RPCContext::getInstance()->getUid();		
		$memInst = AbyssCopyMem::getInstance();
		if( !$memInst->isCopyPassed() )
		{
			Logger::debug('copy not passed');
			throw new Exception('fake');
		}
		
		$userCardInfo = $memInst->getUserCardInfo($uid);		
		if (empty($userCardInfo))
		{
			Logger::info('already rewad or not deal');
			throw new Exception('fake');
		}
		
		//2. 根据翻牌权重，翻出一张牌
		$leftCardList = array();		
		foreach($userCardInfo as $cardId => $value)
		{
			if($value['index'] == $index)
			{
				Logger::info('index:%d already flop', $index);
				throw new Exception('fake');
			}
			if($value['index'] == 0)
			{
				$leftCardList[] = $cardId;
			}
		}
		
		if( count($leftCardList) <= 0)
		{
			Logger::fatal('all card floped');
			throw new Exception('inter');
		}
		$allCardConfList = self::getCardConf(0);
		$cardConfList = self::getValues($allCardConfList, $leftCardList);
		$ret = Util::noBackSample($cardConfList, 1, 'flopWeight' );

		$cardId = $ret[0];
		$card = $cardConfList[$cardId];
						
		//3. 第二张开始就需要花钱了
		$returnData = array(
				'ret'=>'ok', 
				'gold'=>0, 
				'cardId'=>$cardId
				);
		$flopedNum = count($userCardInfo) - count($leftCardList);
		if($flopedNum > 0)
		{
			$userConf = self::getUserConf();
			$userObj = EnUser::getUserObj($uid);
			$needGold = $userConf['cardCostGoldArr'][$flopedNum-1];
			if (!$userObj->subGold($needGold))
			{
				Logger::debug('fail to flop, the gold is not enough');
				throw new Exception('fake');
			}
			$userObj->update();
			$returnData['gold'] = $needGold;
			Statistics::gold(StatisticsDef::ST_FUNCKEY_ABYSS_CARD, $needGold, Util::getTime());
			Logger::info('flop card:%d gold:%d', $flopedNum+1, $needGold);					
		}			
		
		//4. 拿到牌后，改一下数据，掉落一下物品
		$userCardInfo[$cardId]['index'] = $index;
		
		$msg = array(
				'uid' => $uid,
				'index' => $index,
				'cardId' => $cardId, 
		);
		if( !empty($userCardInfo[$cardId]['drop']) )
		{
			$msg['drop'] = $userCardInfo[$cardId]['drop'];
			$returnData['drop'] = $userCardInfo[$cardId]['drop'];
		}
		
		$memInst->setUserCardInfo($uid, $userCardInfo);		
		$memInst->saveCopyData();
		
		$this->broadcast(AbyssCopyConf::$FRONT_CALLBACKS['flopCard'], $msg, $uid );
		
		return $returnData;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IAbyssCopy::rewardCard()
	 */
	public function rewardCard($index)
	{
		//1. 检查
		if($index <= 0 || $index > AbyssCopyConf::CARD_NUM)
		{
			Logger::info('invalid index:%d', $index);
			throw new Exception('fake');
		}
		
		$uid = RPCContext::getInstance()->getUid();
		
		$memInst = AbyssCopyMem::getInstance();
		if( !$memInst->isCopyPassed() )
		{
			Logger::debug('copy not passed');
			throw new Exception('fake');
		}
		
		$userCardInfo = $memInst->getUserCardInfo($uid);
		if (empty($userCardInfo))
		{
			Logger::info('already rewad or not deal');
			throw new Exception('fake');
		}

		//2. 找到是哪张牌
		$cardId = 0;
		foreach($userCardInfo as $id => $value)
		{
			if($value['index'] == $index)
			{
				$cardId = $id;
			}
		}
		
		if($cardId == 0)
		{
			Logger::info('not floped, index:%d', $index);
			throw new Exception('fake');
		}
		
		$cardConf = self::getCardConf($cardId);
		
		//3. 发奖吧
		$userObj = EnUser::getUserObj($uid);
		if ( $cardConf['belly'] > 0 && $userObj->addBelly($cardConf['belly']) == FALSE )
		{
			Logger::FATAL('add belly failed');
			throw new Exception('fake');
		}
		if ( $cardConf['experience'] > 0 && $userObj->addExperience($cardConf['experience']) == FALSE )
		{
			Logger::FATAL('add experience failed');
			throw new Exception('fake');
		}
		if ( $cardConf['elementStone'] > 0 )
		{
			Jewelry::addEnergyElement($uid,0,$cardConf['elementStone']);
		}
		if ( $cardConf['energyStone'] > 0 )
		{
			Jewelry::addEnergyElement($uid, $cardConf['energyStone'], 0);
		}
		
		$itemTmpArr = array();
		if(!empty( $cardConf['normItemArr']) )
		{
			$itemTmpArr = Util::arrayIndexCol($cardConf['normItemArr'], 0, 1);
		}
		if( isset($userCardInfo[$cardId]['drop'])  )
		{		
			$itemTmpArr = self::mergeWithKey($itemTmpArr, $userCardInfo[$cardId]['drop'] );
		}
		
		$returnData = array();
		if (!empty($itemTmpArr))
		{			
			$itemArr = ItemManager::getInstance()->addItems($itemTmpArr);
			$bag = BagManager::getInstance()->getBag($uid);
			$ret = $bag->addItems($itemArr, true);
			$grid = $bag->update();
			$returnData['grid'] = $grid;
			
			//发个系统消息
			chatTemplate::sendAbyGetItemMsg($userObj->getTemplateUserInfo(), chatTemplate::prepareItem($itemArr));							
		}
		
		$userObj->update();
		
		Logger::info('uid:%d rewardcard:%d items:%s', $uid, $cardId, $itemTmpArr);
		
		//领完奖励后，把用户数据置成空array，表示这个人领完奖励了
		$memInst->setUserCardInfo($uid, array());
		$memInst->saveCopyData();
		
		$msg = array(
					'uid' => $uid,
					'index'=> $index
				);
		$this->broadcast(AbyssCopyConf::$FRONT_CALLBACKS['rewadCard'], $msg, $uid );
		
		return $returnData; 
	}
	
	public function chat( $msg )
	{
		$uid = RPCContext::getInstance()->getUid();

		$chatMsg = array(
				'sender' => $uid,
				'msg' => EnChat::filterMessage($msg),
				'time'=>Util::getTime()
				);
		$this->broadcast(AbyssCopyConf::$FRONT_CALLBACKS['chat'], $chatMsg, $uid);
	}
	
	public function userLogoff()
	{
		$uid = RPCContext::getInstance()->getUid();
		
		$copyUUID = RPCContext::getInstance()->getSession(AbyssCopyDef::SESSION_COPY_UUID);
		
		if(empty($copyUUID))
		{
			Logger::debug('uid:%d logoff but not in abyss copy ', $uid);
			return;
		}
		
		$this->userLeaveCopy();	
				
		//如果是在翻牌时掉的线，还要帮他领个奖
		$memInst = AbyssCopyMem::getInstance();
		$userCardInfo = $memInst->getUserCardInfo($uid);
		if(  is_array($userCardInfo) &&  !empty($userCardInfo))
		{
			Logger::info('uid:%d logoff reward card', $uid);
			$idList = array();
			foreach($userCardInfo as $id => $value)
			{
				if($value['index'] > 0)
				{
					$idList[] = $id;
				}
			}
			$cardId = 0;
			if(empty($idList))
			{
				$idList = array_keys($userCardInfo);
				$index = rand(1, count($idList));
				$cardId = $idList[$index-1];
			}
			else 
			{
				$index = rand(1, count($idList));
				$cardId = $idList[$index-1];
				$index = $userCardInfo[$cardId]['index'];
			}
			$userCardInfo[$cardId]['index'] = $index;
			
			$memInst->setUserCardInfo($uid, $userCardInfo);
			
			$this->rewardCard($index);
		}		
		
		Logger::info('uid:%d logoff leave copy:%d', $uid, $copyUUID);	
	}
	
	public function userInCopy()
	{
		$uid = RPCContext::getInstance()->getUid();
	
		$memInst = AbyssCopyMem::getInstance();
		$userInfo = $memInst->getUser($uid);
	
		if ( !$userInfo['online'] )
		{
			$copyUUID = $memInst->getUUID();
			Logger::info('uid:%d in copy:%d', $uid, $copyUUID);
	
			$setData = array(
					'online' => true,
					'visiCount' => RPCContext::getInstance()->getSession ( 'global.visibleCount' ),
					);
			$memInst->modifyUser(array($uid), $setData, NULL, false);
	
			//有人掉线后，通知一下我  
			$uuid = RPCContext::getInstance()->getSession(AbyssCopyDef::SESSION_COPY_UUID);
			if(!isset($uuid))
			{
				RPCContext::getInstance ()->addListener ( 'team.excute.abysscopy.userLogoff' );
			}
			
			RPCContext::getInstance()->setSession(AbyssCopyDef::SESSION_COPY_UUID, $copyUUID);
			
			//显示所有人
			RPCContext::getInstance ()->setSession ( 'global.visibleCount', 10000 );
			
			return true;
		}
		return false;
	}
	
	public function userLeaveCopy()
	{
		$uid = RPCContext::getInstance()->getUid();
	
		//1. 在此人数据中标记他已离开
		$myAbyssCopy = EnAbyssCopy::getMyAbyss($uid);
		$myAbyssCopy->leaveCopy();
		$myAbyssCopy->update();
	
		//2. 在副本数据中标记此人离开
		$memInst = AbyssCopyMem::getInstance();
		$userInfo = $memInst->getUser($uid);
	
		$memInst->modifyUser(array($uid), array('online' => false), NULL, true);
	
		//3. 人都不在了，就需要把副本数据删掉
		$onlineNum = $memInst->getOnlineNum();
		if( $onlineNum == 0)
		{
			Logger::info('all user leave, del copy:%d', $memInst->getUUID());
			//$memInst->delCopy($memInst->getUUID());
			$memInst->setCopyInvalid();	//TODO: 没有删除，而是标记无效
		}
		else
		{
			$memInst->saveCopyData();
		}
	
		//4. 通知一下其他人
		$this->broadcast(AbyssCopyConf::$FRONT_CALLBACKS['modifyUser'],
				array('data'=>$memInst->getUserModify() ) );
	
		Logger::debug('uid:%d leave, online:%d', $uid, $onlineNum);
	}
	
	/**
	 * 
	 * @param int $armyId
	 * @param int $npcTeamID
	 * @param array $heroList
	 * @throws Exception
	 * @return 
	 * 		
	 */
	public function doAttackArmy($roomId, $anchorId, $armyId, $npcTeamID, $heroList, $sendResult = true, $skipBattle = false)
	{
		$uid = RPCContext::getInstance()->getUid();
		$memInst = AbyssCopyMem::getInstance();
		
		//检查用户能不能打：cd，攻击次数，精力		
		$userInfo = $memInst->getUser($uid);
		$armyConf = self::getArmyConf($armyId);
		if(   Util::getTime() < $userInfo['canBtTime'] )
		{
			return array( 'ret' => 'cd');
		}
		if($userInfo['fightNum'] < $armyConf['costFightNum'])
		{			
			return array( 'ret' => 'fightNum');
		}
		/*
		if($userInfo['energy'] < $armyConf['costEnergy'])
		{			
			return array( 'ret' => 'energy');
		}
		*/
		
		$isWin = true;
		$btStr = '';
		$brid = 0;
		//准备战斗数据
		if(!$skipBattle)
		{
			$ret = $this->doBattle($roomId, $anchorId, $armyId, $npcTeamID, $heroList);
			$isWin = $ret['isWin'];
			$btStr = $ret['btStr'];
			$brid = $ret['brid'];
			Logger::debug($ret);
		}
			
		//胜利后可能会给其他人发些东西
		if ($isWin )
		{
			if(  $armyConf['defeatEnergy'] > 0 || $armyConf['defeatFightNum'] > 0  )
			{
				$deltData = array(
						'fightNum' => $armyConf['defeatFightNum'],
						'energy' => $armyConf['defeatEnergy']);
				if($armyConf['addAll'])
				{
					$memInst->modifyAllUser(NULL, $deltData);
				}
				else
				{
					$memInst->modifyAllUser(NULL, $deltData, $uid);
				}
			}
		}
		
		//处理一下战斗后用户数据的变化
		$memInst->modifyUser(array($uid),
				array('canBtTime'=>Util::getTime() + $armyConf['fightCd'] ),
				array(
						'fightNum' => -$armyConf['costFightNum'],
						'energy' => -min($armyConf['costEnergy'], $userInfo['energy'] ) ) );
				
		if($sendResult)
		{
			$battleResult = array(
					'uid' => $uid,
					'army' => $armyId,
					'brid' => $brid,
					'isWin' => $isWin,
			);
			if( $anchorId > 0)
			{
				$battleResult['anchor'] = $anchorId;
			}
			$this->broadcast(AbyssCopyConf::$FRONT_CALLBACKS['battleResult'], $battleResult);
		}
		
		$returnData = array(
				'ret' => 'ok',
				'isWin' => $isWin,
				'brid' => $brid,
				'btStr' => $btStr,
		);
		return $returnData;
	}
	
	public function doBattle($roomId, $anchorId, $armyId, $npcTeamID, $heroList)
	{
		$memInst = AbyssCopyMem::getInstance();
		
		$armyConf = self::getArmyConf($armyId);
		
		$monsterTeamId = $armyConf['monsterTeamId'];
		$monsterTeamConf = self::getMonsterTeamConf($monsterTeamId);
		if ( !isset($monsterTeamConf) )
		{
			Logger::info('no monsterTeam:%d for army:%d', $monsterTeamId, $armyId);
			throw new Exception('config');
		}
		
		$uid = RPCContext::getInstance()->getUid();
		$userObj = EnUser::getUserObj($uid);
		$armyType = $armyConf['armyType'];
		if( $armyType == CopyConf::ARMY_TYPE_NML)
		{
			$userFormation = EnFormation::getFormationInfo($uid);
			$formationID = $userObj->getCurFormation();
			$userObj->prepareItem4CurFormation();
		}
		else if($armyType == CopyConf::ARMY_TYPE_NPC)
		{
			if( empty($npcTeamID) || empty($heroList) )
			{
				Logger::info('invalid param');
				throw new Exception('fake');
			}
			$userFormation = EnFormation::getNpcFormation($npcTeamID, $heroList);
			$formationID = $monsterTeamConf['fid'];
		}
		else
		{
			Logger::fatal('not support army type:%d', $armyType);
			throw new Exception('config');
		}
		
		$enemyFormation = EnFormation::getBossFormationInfo($monsterTeamId);
		
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation, true);
		$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation, true);
		
		//调整难度		
		$copyId = $memInst->getCopyId();
		$copyConf = self::getCopyConf($copyId);
		$userInfo = $memInst->getUser($uid);
		
		$energyForce = self::getMatchLevel($copyConf['energBattleForceArr'], $userInfo['energy'], AbyssCopyDef::COEF_BASE);		
		foreach($userFormationArr as &$hero)
		{
			$hero['absoluteAttackRatio'] = $userInfo['atkAdd'] - $energyForce;
			$hero['absoluteDefendRatio'] = $userInfo['defAdd'];						
			unset($hero);
		}
		
		$userNum = $memInst->getUserNum();
		$userConf = self::getUserConf();
		if(!isset($userConf['difficultyArr'][$userNum]))
		{
			Logger::fatal('no config for user num:%d', $userNum);
			throw new Exception('config');
		}
		$enemyStrength = $userConf['difficultyArr'][$userNum] - AbyssCopyDef::COEF_BASE;		
		foreach($enemyFormationArr as &$hero)
		{
			$hero['absoluteAttackRatio'] = $enemyStrength;
			$hero['absoluteDefendRatio'] = $enemyStrength;
			unset($hero);
		}
		
		//如果打的是怪物模型点上的怪物，就需要考虑一下血量继承，和恢复的问题
		if($roomId > 0 && $anchorId > 0)
		{
			$anchorInfo = $memInst->getEnemyAnchor($roomId, $anchorId);
			if( isset($anchorInfo['hpArr']) )
			{
				$sumHp = 0;
				foreach($enemyFormationArr as  $key => $hero)
				{
					if( isset($anchorInfo['hpArr'][$hero['hid']] ) )
					{
						$enemyFormationArr[$key]['currHp'] = $anchorInfo['hpArr'][$hero['htid']];
						$sumHp += $enemyFormationArr[$key]['currHp'];
					}
					else
					{
						unset($enemyFormationArr[$key]);
					}
				}
				if($sumHp <=0 )
				{
					Logger::fatal('all enemy down');
					throw new Exception('inter');
				}
			}
		}
		Logger::debug('userFormationArr:%s', $userFormationArr);
		Logger::debug('enemyFormationArr:%s', $enemyFormationArr);
		
		//战斗之前记录一下，当前打的是哪个怪，战斗回调函数中需要用到
		self::$gCurArmyId = $armyId;
		
		$bt = new Battle();
		$ret = $bt->doHero(array('name' => $userObj->getUname(),
				'level' => $userObj->getLevel(),
				'isPlayer' => true,
				'flag' => 0,
				'formation' => $formationID,
				'uid' => $uid,
				'arrHero' => $userFormationArr),
				array('name' => $armyConf['name'],
						'level' => $armyConf['level'],
						'isPlayer' => false,
						'flag' => 0,
						'formation' => $monsterTeamConf['fid'],
						'uid' => $armyId,
						'arrHero' => $enemyFormationArr),
				$armyConf['battleType'],
				array('AbyssCopy', 'battleCallback'),
				self::getBattleEndCond( $armyConf ),
				array('bgid' => $armyConf['battleBgId'],
						'musicId' => $armyConf['battleMusicId'],
						'type' => BattleType::ABYSS_COPY));
		
		
		//如果打的是怪物模型点上的怪物，就需要考虑一下血量继承，和恢复的问题
		if($roomId > 0 && $anchorId > 0)
		{
			$hpArr = array();
			foreach($ret['server']['team2'] as $hero)
			{
				if($hero['hp'] > 0)
				{
					$hpArr[$hero['hid']] = $hero['hp'];
				}
			}
			$memInst->modifyEnemy($roomId, $anchorId, array('hpArr' => $hpArr), false );
		}
		
		Logger::debug('attack army:%d, brid:%d, result:%s', $armyId, $ret['server']['brid'], $ret['server']['appraisal']);
		
		$returnData = array(
				'brid' => $ret['server']['brid'],
				'btStr' => $ret['client'],
				'isWin' => BattleDef::$APPRAISAL[$ret['server']['appraisal']] <= BattleDef::$APPRAISAL['D'],
				);
		return $returnData;
	}
	
	public static function battleCallback($atkRet)
	{
		$armyConf = self::getArmyConf(self::$gCurArmyId);
		
		$isWin = BattleDef::$APPRAISAL[$atkRet['appraisal']] <= BattleDef::$APPRAISAL['D'];

		$returnData = array(
				'fightNum' => $armyConf['costFightNum'],
				'energy' => $armyConf['costEnergy']
				);
		
		$userNum = AbyssCopyMem::getInstance()->getUserNum();
		if ($isWin && $userNum > 1)
		{
			if( $armyConf['defeatFightNum'] > 0  )
			{
				$returnData['defeatFightNum'] = $armyConf['defeatFightNum'];
			}
			if(  $armyConf['defeatEnergy'] > 0 || $armyConf['defeatFightNum'] > 0  )
			{
				$returnData['defeatEnergy'] = $armyConf['defeatEnergy'];
			}
		}
		
		return $returnData;		
	}
	
	public function refreshRoomAfterFight($roomId, $anchorId, $isWin)
	{
		$anchorConf = self::getEnemyAnchorConf($anchorId);
				
		$memInst = AbyssCopyMem::getInstance();
		$roomInfo = $memInst->getRoom($roomId);
		$copyId = $memInst->getCopyId();
		$anchorInfo = $memInst->getEnemyAnchor($roomId, $anchorId);
		
		$copyConf = self::getCopyConf($copyId);
		
		//打了好多次后，会有些事情发生
		foreach($anchorConf['newEnemyAftManyAtkArr'] as $value)
		{
			$num = $value[0];
			$newAnchorId = $value[1];
			if($roomInfo['enemyAnchors'][$anchorId]['fightNum'] == $num  )
			{
				$memInst->addEnemy($roomId, $newAnchorId);
			}
		}
		if($isWin)
		{
			Logger::debug('fight win refresh room:%d, anchor:%d', $roomId, $anchorId);
			//一定先激活传送阵，激活传送阵可能会打开新的房间。这样后面才能在新房间中添加东西
			foreach($anchorConf['actTeleportIdArr'] as $id)
			{
				$memInst->addTeleport($roomId, $id);
			}
			//变身
			if($anchorConf['metamorphosis'] > 0)
			{
				$memInst->addEnemy($roomId, $anchorConf['metamorphosis']);
			}
			//出现新的怪物
			foreach($anchorConf['nexShowIdArr'] as $id)
			{
				$memInst->addEnemy($roomId, $id);
			}

			//之前数据会有改变，更新一下
			$roomInfo = $memInst->getRoom($roomId);
			
			//有新的怪物可以打了		
			foreach($anchorConf['nexAtkIdArr'] as $id)
			{
				if(isset($roomInfo['enemyAnchors'][$id]))
				{					
					$memInst->modifyEnemy($roomId, $id, 
							array(
									'state' => AbyssCopyDef::$ENEMY_STATE['CAN_ATK']
									));
				}
				else
				{
					Logger::warning('failed set attack enemyAnchor:%d by anchor:%d', $id, $anchorId);
					throw new Exception('config');
				}				
			}
			if($anchorConf['delAfterBeaten'])
			{
				$memInst->delEnemy($roomId, $anchorId);
			}
			else 
			{
				$modifyData = array(
						'fightNum' => $anchorInfo['fightNum'] + 1,
						'beatenNum' => $anchorInfo['beatenNum'] + 1,
						);
				$memInst->modifyEnemy($roomId, $anchorId, $modifyData);
			}			
			foreach($anchorConf['actTriggerIdArr'] as $id)
			{
				$memInst->modifyTrigger($roomId, $id, array('state' => AbyssCopyDef::$BOX_STATE['CAN_OPEN']));
			}									
		}
		else
		{
			Logger::debug('fight lose refresh room:%d, anchor:%d', $roomId, $anchorId);
			$modifyData = array(
					'fightNum' => $anchorInfo['fightNum'] + 1,
			);
			$memInst->modifyEnemy($roomId, $anchorId, $modifyData);
		}
				
	}
	
	public function checkAddScore($roomId, $anchorId = 0, $triggerId = 0)
	{
		$uid = RPCContext::getInstance()->getUid();
		$memInst = AbyssCopyMem::getInstance();
		$score = 0;
		$copyId = $memInst->getCopyId();
		$copyConf = self::getCopyConf($copyId);
		
		if($anchorId > 0)
		{			
			$anchorInfo = $memInst->getEnemyAnchor($roomId, $anchorId);
			if( isset($copyConf['scoreEventEnemy'][$anchorId]))
			{
				$score += $copyConf['scoreEventEnemy'][$anchorId];
			}
			if( isset($copyConf['scoreEventEnemyMany'][$anchorId]) &&
					$anchorInfo['fightNum'] < $copyConf['scoreEventEnemyMany'][$anchorId]['num'] )
			{
				$score += $copyConf['scoreEventEnemyMany'][$anchorId];
			}			
		}
		if( $triggerId > 0)
		{
			if( isset($copyConf['scoreEventBox'][$triggerId]))
			{
				$score += $copyConf['scoreEventBox'][$triggerId];
			}
		}
		if($score>0)
		{
			Logger::debug('add score anchor:%d, trigger:%d, score:%d', $anchorId, $triggerId, $score);			
			$memInst->addCopyScore($score);
		}
	}
	
	public function broadcast($method, $msg, $excludeId = 0, $emptyMsg = false)
	{		
		$uidList = AbyssCopyMem::getInstance()->getAllOnlineUser();
		if($excludeId)
		{
			$uidList = array_diff($uidList, array($excludeId));
			$uidList = array_values($uidList);
		}

		if( !empty($uidList) && (!empty($msg) || $emptyMsg) )
		{
			RPCContext::getInstance()->sendMsg( $uidList, $method, $msg);
		}
	}
	

	public function checkCleanRoom($roomId)
	{
		$memInst = AbyssCopyMem::getInstance();
		$clean = $memInst->isRoomClean($roomId);
		
		if($clean)
		{
			Logger::info('clean room:%d', $roomId);
			$roomConf = self::getRoomConf($roomId);
			foreach($roomConf['actTeleportIdArr'] as $id)
			{
				$memInst->addTeleport($roomId, $id);
			}
			foreach($roomConf['actTriggerIdArr'] as $id)
			{
				$memInst->modifyTrigger($roomId, $id, array('state' => AbyssCopyDef::$BOX_STATE['CAN_OPEN']));
			}
		}
	}
	
	public function checkPassCopy($enemyAnchorId = 0, $puzzleId = 0)
	{
		$memInst = AbyssCopyMem::getInstance();
		if( $memInst->isCopyPassed() )
		{
			Logger::warning('copy passed');
			return true;
		}
		
		$copyId = $memInst->getCopyId();
		$copyConf = self::getCopyConf($copyId);
		
		if( $enemyAnchorId > 0)
		{
			if($enemyAnchorId != $copyConf['pByEnemyAcId'] && 
					!in_array($enemyAnchorId, $copyConf['pByHideEnemyAcIdArr']->toArray() ) )
			{
				return false;
			}
		}
		else
		{
			//现在只支持通关打怪通关，解密通关不支持
			Logger::fatal('invalid param');
			throw new Exception('inter');
		}
		
		$isAnd = $copyConf['passType'] == AbyssCopyDef::$COPY_PASS_TYPE['AND'];
		$passCopy = false;
		//击败通关boss
		if($copyConf['pByEnemyAcId'] > 0 )
		{
			$pass = ($enemyAnchorId == $copyConf['pByEnemyAcId']) || 
					$memInst->isEnemyAnchorBeaten($copyConf['pByEnemyAcId']);
			
			if(!$pass && $isAnd)
			{
				return false;
			}
			if( $pass && !$isAnd)
			{
				$passCopy = true;
			}
		}
		
		//击败所有出现的隐藏boss 
		if(!$passCopy && !empty($copyConf['pByHideEnemyAcIdArr']))
		{
			$pass = true;
			foreach($copyConf['pByHideEnemyAcIdArr'] as $hideBossId)
			{
				if( !$memInst->isEnemyAnchorBeaten($hideBossId, true))
				{
					$pass = false;
				}
			} 
			if(!$pass && $isAnd)
			{
				return false;
			}
			if( $pass && !$isAnd)
			{
				$passCopy = true;
			}
		}
		
		if($passCopy)
		{
			$this->passCopy();
		}
		return $passCopy;
	}
	
	public function passCopy()
	{
		$memInst = AbyssCopyMem::getInstance();
		$copyId = $memInst->getCopyId();
		$copyConf = self::getCopyConf($copyId);
		
		$userList = $memInst->getAllUser();
		$uidList = array_keys($userList);		
		
		$totalScore = $copyConf['baseScore'];
		$leftFightNum = 0;
		$leftEnergy = 0;
		foreach ($userList as $uid => $info)
		{
			if(!$info['online'])
			{
				continue;
			}
			$leftFightNum += $info['fightNum'];
			$leftEnergy += $info['energy'];
		}
		$totalScore += self::getMatchLevel($copyConf['scoreFightNumArr'], $leftFightNum);
		$totalScore += self::getMatchLevel($copyConf['scoreEnergyArr'], $leftEnergy);

		$totalScore = $memInst->addCopyScore($totalScore);
		
		Logger::info('users:%s pass copy:%d, fightNum:%d, energy:%d, score:%d', 
					$uidList, $copyId, $leftFightNum, $leftEnergy, $totalScore);
		
		//结算一下score，奖励异步发
		foreach($userList as $uid => $info)
		{
			if(!$info['online'])
			{
				Logger::info('uid:%d leave copy no reward', $uid);
				continue;
			}
			if($info['isExe'])
			{
				Logger::info('uid:%d exe no reward', $uid);
				continue;
			}
			$score = $copyConf['baseScore'];
			
			//发奖涉及的DB操作太多，拿出去发
			RPCContext::getInstance()->executeTask($uid, 'abysscopy.passCopyReward',
                                                   array($copyId, $uid, $totalScore),
                                                   false);			
		}
		
		$memInst->setCopyPassed();
		
		//发送一下数据
		$msg = array('score' => $totalScore);
		
		$this->broadcast(AbyssCopyConf::$FRONT_CALLBACKS['copyPassed'], $msg);
	}

	public function passCopyReward($copyId, $uid, $score)
	{
		$copyConf = self::getCopyConf($copyId);
		
		//奖励等
		$userObj = EnUser::getUserObj($uid);
		
		$addRatio = 1 + self::getMatchLevel($copyConf['scoreAddArr'], $score) / AbyssCopyDef::COEF_BASE;
			
		$belly = intval($copyConf['rewardBelly']*$addRatio);
		$experience = intval($copyConf['rewardExperience']*$addRatio);
		
		
		if ( $belly > 0 && $userObj->addBelly( $belly ) == FALSE )
		{
			Logger::FATAL('add belly failed');
			throw new Exception('fake');
		}
		if ( $experience > 0 && $userObj->addExperience($experience  ) == FALSE )
		{
			Logger::FATAL('add experience failed');
			throw new Exception('fake');
		}
			
		if(!empty( $copyConf['rewardItemArr']) )
		{
			$itemArr = Util::arrayIndexCol($copyConf['rewardItemArr'], 0, 1);
			
			$bag = BagManager::getInstance()->getBag($uid);
			$ret = $bag->addItemsByTemplateID($itemArr, true);
			$grid = $bag->update();
			RPCContext::getInstance()->sendMsg(array($uid), 're.bag.bagInfo', array($grid) );
		}
		$userObj->update();
		
		$msg = array(
				'belly_num' => $userObj->getBelly(),
				'experience_num' => $userObj->getExperience(),
		);		
		RPCContext::getInstance()->sendMsg(array($uid), 're.user.updateUser', $msg);
		
		Logger::info('pass copy reward. copy:%d, uid:%d, score:%d, ratio:%d, belly:%d, exp:%d',
		$copyId, $uid, $score, $addRatio, $belly, $experience);
		

		//扣这个人的挑战次数
		$myAbyssCopy = EnAbyssCopy::getMyAbyss($uid);
		$myAbyssCopy->passCopy($copyId);
		$myAbyssCopy->update();
	}
	
	public static function getBattleEndCond($armyConf)
	{
		$endCond = array();
		if($armyConf['battleRound'] > 0)
		{
			$endCond['attackRound'] = $armyConf['battleRound'];
		}
		if($armyConf['defendRound'] > 0)
		{
			$endCond['defendRound'] = $armyConf['defendRound'];
		}
	
		if($armyConf['armyType'] == CopyConf::ARMY_TYPE_NPC &&
				$armyConf['npcId'] > 0)
		{
			$endCond['team1'] = array(array($armyConf['npcId'], $armyConf['npcHp']));
		}
	
		if($armyConf['monsterId'] > 0)
		{
			$endCond['team2'] = array(array($armyConf['monsterId'], $armyConf['monsterHp']));
		}
		return $endCond;
	}
	
	public static function getCopyConf($id)
	{
		$btConf = btstore_get()->ABYSS_COPY;
		if($id == 0)
		{
			return $btConf;
		}
		return isset($btConf[$id]) ? $btConf[$id] : NULL;
	}
	public static function getRoomConf($id)
	{
		$btConf = btstore_get()->ABYSS_ROOM;
		if($id == 0)
		{
			return $btConf;
		}
		return isset($btConf[$id]) ? $btConf[$id] : NULL;
	}
	public static function getTeleportConf($id)
	{
		$btConf = btstore_get()->ABYSS_TELEPORT;
		if($id == 0)
		{
			return $btConf;
		}
		return isset($btConf[$id]) ? $btConf[$id] : NULL;
	}
	public static function getArmyConf($id)
	{
		$btConf = btstore_get()->ABYSS_ARMY;
		if($id == 0)
		{
			return $btConf;
		}
		return isset($btConf[$id]) ? $btConf[$id] : NULL;
	}
	public static function getEnemyAnchorConf($id)
	{
		$btConf = btstore_get()->ABYSS_ENEMY_ANCHOR;
		if($id == 0)
		{
			return $btConf;
		}
		return isset($btConf[$id]) ? $btConf[$id] : NULL;
	}
	public static function getTriggerConf($id)
	{
		$btConf = btstore_get()->ABYSS_TRIGGER;
		if($id == 0)
		{
			return $btConf;
		}
		return isset($btConf[$id]) ? $btConf[$id] : NULL;
	}
	public static function getMonsterTeamConf($id)
	{
		$btConf = btstore_get()->TEAM;
		if($id == 0)
		{
			return $btConf;
		}
		return isset($btConf[$id]) ? $btConf[$id] : NULL;
	}
	public static function getCardConf($id)
	{
		$btConf = btstore_get()->ABYSS_CARD;
		if($id == 0)
		{
			return $btConf;
		}
		return isset($btConf[$id]) ? $btConf[$id] : NULL;
	}
	public static function getUserConf()
	{
		return btstore_get()->ABYSS_USER;
	}
	public static function getQuestionConf($id)
	{
		$btConf = btstore_get()->ABYSS_QUESTION;
		if($id == 0)
		{
			return $btConf;
		}
		return isset($btConf[$id]) ? $btConf[$id] : NULL;
	}
	public static function getRandQuestion()
	{
		$allQuest = btstore_get()->ABYSS_QUESTION->toArray();
		$allQids = array_keys($allQuest);
		$index = rand(0, count($allQids)-1);
		return $allQids[$index];
	}
	

	public static function getMatchLevel($levelArr, $key, $default = 0)
	{
		$returnValue = $default;
		foreach( $levelArr as $value)
		{
			if($key < $value[0])
			{
				break;
			}
			$returnValue = $value[1];
		}
		return $returnValue;
	}
	
	public static function getValues($arr, $keys)
	{
		$returnData = array();
		foreach($keys as $key)
		{
			$returnData[$key] = $arr[$key];
		}
		return $returnData;
	}
	public static function mergeWithKey($arr1, $arr2)
	{
		$arr = $arr1; 
		foreach($arr2 as $k => $v)
		{
			if(isset( $arr[$k] ))
			{
				$arr[$k] += $v;
			}
			else
			{
				$arr[$k] = $v;
			}
		}
		return $arr;
	}
	public function getDirectlyPassInfo()
	{
		return 'ok';
	}
}


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
