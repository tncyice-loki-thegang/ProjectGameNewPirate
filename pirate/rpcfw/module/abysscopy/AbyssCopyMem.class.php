<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AbyssCopyMem.class.php 40511 2013-03-11 07:28:17Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/AbyssCopyMem.class.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-11 15:28:17 +0800 (一, 2013-03-11) $
 * @version $Revision: 40511 $
 * @brief 
 *  
 **/

class AbyssCopyMem
{
	private $uuid = 0;
	
	private $copyData = array();
	
	
	/**
	 * 发送modify×××数据时，告诉前端是谁(uid)，点了什么产生了这些数据变更
	 * 	 cause => array(
	 * 					'uid' => 1,
	 * 					'objId' => 1
	 * 					'objType'=> 1 点击什么东西
	 * 				)
	*/
	
	/**
	 * 发生改变的东西
	 * array(
	 * 		array(op, roomId, type, id, data)
	 * )
	 * op = add/del/modify
	
	 *
	*/
	private $modifyObjList = array();
	
	/**
	 * 玩家信息改变
	 * array(
	 * 		array(
	 * 			'uidList' => array(1)
	 * 			'set' => array(
	 * 						'canBtTime' => 13426000
	 * 					)
	 * 			'delt' => array(
	 * 						fightNum => 10,
	 * 						energy => -10,
	 * 						atkAdd => 100
	 * 						defAdd => 100
	 * 					)
	 * 		)
	 * )
	*/
	private $modifyUserList = array();
	
	/**
	 * 
	 * @var AbyssCopyMem
	 */
	private static $instance = null;
	
	private function __construct($uuid)
	{
		if($uuid <= 0)
		{
			return;
		}
		$this->uuid = $uuid;
	
		$key = self::getMemKey($this->uuid);
	
		$this->copyData = McClient::get($key);
		if( empty($this->copyData) )
		{
			Logger::warning('failed to get copy data');
			throw new Exception('fake');
		}
	}
	
	/**
	 * 得到RPCContext的实例
	 * @return AbyssCopyMem
	 */
	public static function getInstance()
	{	
		if (empty ( self::$instance ))
		{
			self::$instance = new AbyssCopyMem (MyAbyssCopy::getCopyUUID());
		}
		return self::$instance;
	}
	
	public static function initCopyData($copyId, $ids, $uidList)
	{
		$copyConf = AbyssCopy::getCopyConf($copyId);
		$uidList = array_values($uidList);
		
		self::$instance = new AbyssCopyMem(0);
		
		self::$instance->uuid = current($ids);
		if(self::isCopyExist(self::$instance->uuid ))
		{
			Logger::fatal('copy:%d exist', self::$instance->uuid);
			throw new Exception('inter');
		}
		
		self::$instance->copyData = array(
				'uuid' => self::$instance->uuid,
				'copyId' => $copyId,
				'startTime' => Util::getTime(),
				'creator' => $uidList[0],
				'score' => 0,
				'userList' => array(),
				'rooms' => array(),
				//'cards' => array() //通关后这个字段才有
		);
		
		//先把每个房间对应的townId存着
		foreach($copyConf['roomIdArr'] as $roomId)
		{
			self::$instance->copyData['rooms'][$roomId] = next($ids);
			
			$roomConf = AbyssCopy::getRoomConf($roomId);
			if( $roomConf['preRoomId'] == 0 )
			{
				self::$instance->addRoom($roomId);
			}
		}
		
		$userNum = count($uidList);
		foreach($uidList as $id)
		{
			self::$instance->addUser($id, $userNum);	
		}
		
		return self::$instance->uuid;
	}
	
	public function getUUID()
	{
		return $this->uuid;
	}
	public function getUserModify()
	{
		return $this->modifyUserList;
	}
	public function getObjModify()
	{
		return $this->modifyObjList;
	}
	
	public function getOnlineNum()
	{
		$onlineNum = 0;
		foreach ($this->copyData['userList'] as $userInfo)
		{
			if( $userInfo['online'] )
			{
				$onlineNum++;
			}
		}
		return $onlineNum;
	}
	public function getUserNum()
	{
		return count($this->copyData['userList']);
	}
		
	
	

	/**
	 * 根据用户当前所在城镇ID，得到用户所在房间ID
	 * @return int roomId
	 */
	public function getCurRoom()
	{
		$townId = RPCContext::getInstance()->getTownId();
		foreach($this->copyData['rooms'] as $roomId => $room)
		{
			if($room['townId'] == $townId)
			{
				return $roomId;
			}
		}
	
		Logger::warning('uid:%d not in abyss', RPCContext::getInstance()->getUid());
		throw new Exception('fake');
	}
	


	public function getRoom($roomId)
	{
		if(!isset($this->copyData['rooms'][$roomId]) )
		{
			Logger::info('not found room:%d', $roomId);
			throw new Exception('fake');
		}
		if(!is_array($this->copyData['rooms'][$roomId]) )
		{
			Logger::info('room:%d not opened', $roomId);
			throw new Exception('fake');
		}
	
		return $this->copyData['rooms'][$roomId];
	}
	
	public function getAllUser()
	{
		return $this->copyData['userList'];
	}
	public function getCreator()
	{
		return $this->copyData['creator'];
	}
	public function getAllOnlineUser()
	{
		$uidList = array();
		foreach($this->copyData['userList'] as $id => $value)
		{
			if($value['online'])
			{
				$uidList[] = $id;
			}
		}
		return $uidList;
	}
	public function getUser($uid)
	{
		if( !isset($this->copyData['userList'][$uid]) )
		{
			Logger::info('not found uid:%d', $uid);
			throw new Exception('fake');
		}
		return $this->copyData['userList'][$uid];
	}
	public function getEnemyAnchor($roomId, $anchorId)
	{
		if(!isset($this->copyData['rooms'][$roomId]['enemyAnchors'][$anchorId]))
		{
			Logger::info('not found anchor:%d, room:%d', $anchorId, $roomId);
			throw new Exception('fake');
		}
		return $this->copyData['rooms'][$roomId]['enemyAnchors'][$anchorId];
	}
	
	public function getCopyId()
	{
		return $this->copyData['copyId'];
	}
	
	public function getCopyScore()
	{
		return $this->copyData['score'];
	}
	
	public function getTrigger($roomId, $id)
	{
		$conf = AbyssCopy::getTriggerConf($id);
		if(!isset($conf))
		{
			Logger::fatal('no trigger:%d in room:%d', $id, $roomId);
			throw new Exception('config');
		}
		switch($conf['type'])
		{
			case AbyssCopyDef::$TRIGGER_TYPE['BOX']:
				if( !isset( $this->copyData['rooms'][$roomId]['boxes'][$id] ) ) 
				{
					Logger::info('no box:%d in room:%d', $id, $roomId);
					throw new Exception('fake');
				}
				return $this->copyData['rooms'][$roomId]['boxes'][$id];
					
			case AbyssCopyDef::$TRIGGER_TYPE['VIAL']:
				if( !isset( $this->copyData['rooms'][$roomId]['vials'][$id] ) )
				{
					Logger::info('no vial:%d in room:%d', $id, $roomId);
					throw new Exception('fake');
				}
				return $this->copyData['rooms'][$roomId]['vials'][$id];
			default:
				Logger::warning('invalid type:%d, trigger:%d', $conf['type'], $id);
				break;
		}
	}
	public function getQuestion($roomId, $triggerId)
	{		
		if(!isset($this->copyData['rooms'][$roomId]['quests'][$triggerId]))
		{
			Logger::info('not found quest:%d, room:%d', $triggerId, $roomId);
			throw new Exception('fake');
		}
		return $this->copyData['rooms'][$roomId]['quests'][$triggerId];
	}
	

	public function isRoomOpen($roomId)
	{
		if(!isset($this->copyData['rooms'][$roomId]))
		{
			Logger::warning('no room:%d in copy:%d', $roomId, $this->copyData['copyId']);
			throw new Exception('fake');
		}
		return is_array($this->copyData['rooms'][$roomId]);
	}
	
	public function isRoomClean($roomId)
	{
		foreach($this->copyData['rooms'][$roomId]['enemyAnchors'] as $anchorId => $value)
		{
			if($value['beatenNum'] == 0)
			{
				return false;
			}
		}
		return true;
	}
	
	public function isEnemyAnchorBeaten( $anchorId, $notExistRet = false)
	{
		foreach($this->copyData['rooms'] as $roomInfo)
		{
			if(!is_array($roomInfo['enemyAnchors']))
			{
				continue;
			}
			foreach($roomInfo['enemyAnchors'] as $id => $value)
			{
				if($id == $anchorId)
				{
					return $value['beatenNum'] > 0;
				}
			}
		}
		//不存在时
		return $notExistRet;
	}
	
	public function isCopyPassed()
	{
		return isset( $this->copyData['cards'] );
	}
	
	public function setCopyPassed()
	{
		$this->copyData['cards'] = array();
	}
	
	public function addCopyScore($score)
	{
		$this->copyData['score'] += $score;
		return $this->copyData['score'];
	}
	
	/**
	 * 新增一个玩家
	 * @param int $uid
	 */
	public function addUser($uid, $totalNum)
	{
		if(isset($this->copyData['userList'][$uid]))
		{
			Logger::warning('user:%d already in', $uid);
			return;
		}
		$copyId = $this->getCopyId();
		$copyConf = AbyssCopy::getCopyConf($copyId)->toArray();
	
		if(!isset($copyConf['baseFightNumArr'][$totalNum]) ||
				!isset($copyConf['baseEnergyArr'][$totalNum]))
		{
			Logger::warning('no config for user num:%d', $totalNum);
			throw new Exception('fake');
		}
		$this->copyData['userList'][$uid] = array(
				'online' => false,	//等玩家enterRoom时，改成true
				'isExe' => EnAbyssCopy::getMyAbyss($uid)->isExercise(),
				'visiCount'=> 0, //用户在进入副本之前，设置的显示多少人。用户进入副本时记下这个值，离开时恢复
				'canBtTime' => 0,
				'fightNum' => $copyConf['baseFightNumArr'][$totalNum],
				'energy' => $copyConf['baseEnergyArr'][$totalNum],
				'atkAdd' => 0,
				'defAdd' => 0,				
		);
	}
	
	public function delUser($uid)
	{
		if(isset($this->copyData['userList'][$uid]))
		{
			Logger::warning('user:%d not in copy uuid:%d', $uid, $this->uuid);
			return false;
		}
		unset($this->copyData['userList'][$uid]);
	
		Logger::info('del uid:%d, copy uuid:%d', $uid, $this->uuid);
	
		return true;
	}
	
	
	public function modifyAllUser($setData, $deltData, $excludeId = 0)
	{
		$uidList = array_keys($this->copyData['userList']);
		if($excludeId)
		{
			$uidList = array_diff($uidList, array($excludeId));
		}
	
		$this->modifyUser($uidList, $setData, $deltData);
	}
	
	public function modifyUser($uidList, $setData, $deltData, $record = true)
	{
		if(empty($deltData) && empty($setData))
		{
			Logger::debug('empty data');
			return;
		}
		//过滤掉deltData中为0的值
		if($deltData)
		{
			foreach($deltData as $key => $value )
			{
				if($value == 0)
				{
					unset($deltData[$key]);
				}
			}
		}
		foreach($uidList as $uid )
		{
			if( !isset( $this->copyData['userList'][$uid] ) )
			{
				Logger::warning('not found uid:%d, uuid:%d', $uid, $this->uuid);
				throw new Exception('fake');
			}
			$userInfo = $this->copyData['userList'][$uid];
				
			if($setData)
			{
				foreach($setData as $key => $value)
				{
					$userInfo[$key] = $value;
				}
			}
			if($deltData)
			{
				foreach($deltData as $key => $value)
				{
					$userInfo[$key] += $value;
				}
			}
			$this->copyData['userList'][$uid] = $userInfo;
		}
	
		if($record)
		{
			$modify = array(
					'uidList' => $uidList,
			);
			if( $setData)
			{
				$modify['set'] = $setData;
			}
			if( $deltData )
			{
				$modify['delt'] = $deltData;
			}
		
			$this->modifyUserList[] = $modify;
		}
	
		Logger::debug('modifyUser uidList:%s set:%s, delt:%s', $uidList, $setData, $deltData);
	
	}
	
	public function getUserCardInfo($uid)
	{
		if($uid == 0)
		{
			return $this->copyData['cards'];
		}
	
		if(!isset($this->copyData['cards'][$uid]))
		{
			return NULL;
		}
		return $this->copyData['cards'][$uid];
	}
	
	public function setUserCardInfo($uid, $data)
	{
		$this->copyData['cards'][$uid] = $data;
	}
	
	


	
	
	/**
	 * 新增一个房间
	 * @param int $roomId
	 * @throws Exception
	 */
	public function addRoom($roomId)
	{
		if( $this->isRoomOpen($roomId) )
		{
			Logger::fatal('add exist room:%d', $roomId);
			throw new Exception('inter');
		}
	
		//在初始时，此房间对应的数据是其townId
		$townId = $this->copyData['rooms'][$roomId];
	
		$roomConf = AbyssCopy::getRoomConf($roomId);
		if(!isset($roomConf))
		{
			Logger::fatal('no config room:%d', $roomId);
			throw new Exception('fake');
		}
		$this->copyData['rooms'][$roomId] = array(
				'townId' => $townId,
				'teleports' => array(),
				'boxes' => array(),
				'vials' => array(),
				'enemyAnchors' => array(),
				'quests' => array(),				
		);
		
		//把没有开启条件的传送阵开启了
		foreach($roomConf['teleportArr'] as $id)
		{
			$teleConf = AbyssCopy::getTeleportConf($id);
			if(!isset($teleConf))
			{
				Logger::fatal('no teleport:%d in room:%d', $id, $roomId);
				throw new Exception('config');
			}
			if( $teleConf['byEnemyAnchorId'] == 0 &&
					$teleConf['byTriggerID'] == 0 &&
					$teleConf['byCleanRoomId'] == 0 )
			{
				$this->addTeleport($roomId, $id, false);
			}
		}
	
		//把没有开启条件的箱子激活
		foreach($roomConf['triggerArr'] as $id)
		{
			$triggerConf = AbyssCopy::getTriggerConf($id);
			if(!isset($triggerConf))
			{
				Logger::fatal('no trigger:%d in room:%d', $id, $roomId);
				throw new Exception('config');
			}
			if($triggerConf['type'] == AbyssCopyDef::$TRIGGER_TYPE['BOX'] )
			{
				$this->addBox($roomId, $id, false);
			}
		}
	
		//把该出怪物的地方的怪物都弄出来吧
		foreach($roomConf['enemyAnchorArr'] as $id)
		{
			$anchorConf = AbyssCopy::getEnemyAnchorConf($id);
			if(!isset($anchorConf))
			{
				Logger::fatal('no enemyanchor:%d in room:%d', $id, $roomId);
				throw new Exception('config');
			}
			if($anchorConf['preShowId'] == 0)
			{
				$this->addEnemy($roomId, $id, false);
			}
		}
	
		Logger::debug('add room:%d', $roomId);
	}
	
	/**
	 * 新加一个传送阵
	 */
	public function addTeleport($roomId, $teleportId, $record = true)
	{
		$teleportConf = AbyssCopy::getTeleportConf($teleportId);
		if(!isset($teleportConf))
		{
			Logger::fatal('no teleport:%d, room:%d', $teleportId, $roomId);
			throw new Exception('config');
		}
	
		if($roomId != $teleportConf['roomId'])
		{
			Logger::debug('add teleport in other room:%d, curRoom:%d', $teleportConf['roomId'], $roomId);			
			return $this->addTeleport($teleportConf['roomId'], $teleportId, $record);
		}
		
		if(!$this->isRoomOpen($roomId))
		{
			Logger::info('addTeleport failed. room:%d not open', $roomId);
			throw new Exception('fake');
		}
	
		if ( isset($this->copyData['rooms'][$roomId]['teleports'][$teleportId] ) )
		{
			Logger::warning('add a exist teleport:%d, room:%d', $teleportId, $roomId);
			return false;
		}
	
		Logger::debug('add teleport:%d in room:%d', $teleportId, $roomId);
		$this->copyData['rooms'][$roomId]['teleports'][$teleportId] = 0;
	
		if($record)
		{
			$this->modifyObjList[] = array(
					'op' => AbyssCopyDef::$OP_TYPE['ADD'],
					'roomId' => $roomId,
					'type' =>  AbyssCopyDef::$OBJ_TYPE['TELEPORT'],
					'id' => $teleportId,
					'data' => 0 );
		}
	
		$toRoomId = $teleportConf['toRoomId'];
		if( !$this->isRoomOpen($toRoomId) )
		{
			Logger::debug('open room:%d', $toRoomId);
			$this->addRoom($toRoomId);
		}
		
		return true;
	}
	
	public function addTrigger($roomId, $id, $record = true)
	{
		$conf = AbyssCopy::getTriggerConf($id);
		if(!isset($conf))
		{
			Logger::fatal('no trigger:%d, room:%d', $id, $roomId);
			throw new Exception('config');
		}
		switch($conf['type'])
		{
			case AbyssCopyDef::$TRIGGER_TYPE['BOX']:
				$this->addBox($roomId, $id, $record);
				break;
			case AbyssCopyDef::$TRIGGER_TYPE['VIAL']:
				$this->addVial($roomId, $id, $record);
				break;
			default:
				Logger::warning('invalid type:%d, trigger:%d', $conf['type'], $id);
				break;
		}
	}
	
	/**
	 * 新加一个箱子
	 * @param int $roomId
	 * @param int $boxId
	 */
	public function addBox($roomId, $boxId, $record = true)
	{
		$boxConf = AbyssCopy::getTriggerConf($boxId);
		if(!isset($boxConf))
		{
			Logger::fatal('no box:%d in room:%d', $boxId, $roomId);
			throw new Exception('config');
		}
	
		if($roomId != $boxConf['roomId'])
		{
			Logger::debug('add box in other room:%d, curRoom:%d', $boxConf['roomId'], $roomId);
			return $this->addBox($boxConf['roomId'], $boxId, $record);
		}
	
		if(!$this->isRoomOpen($roomId))
		{
			Logger::info('addBox failed. room:%d not open', $roomId);
			throw new Exception('fake');
		}
				
		if ( isset($this->copyData['rooms'][$roomId]['boxes'][$boxId] ) )
		{
			Logger::warning('add a exist box:%d, room:%d', $boxId, $roomId);
			return false;
		}
	
		if( $boxConf['type'] != AbyssCopyDef::$TRIGGER_TYPE['BOX'])
		{
			Logger::fatal('trigger:%d in room:%d not a box', $boxId, $roomId);
			throw new Exception('config');
		}
	
		Logger::debug('add box:%d in room:%d', $boxId, $roomId);
		$boxInfo =  array(
				'state' => AbyssCopyDef::$BOX_STATE['SHOW']
		);
	
		if( $boxConf['byEnemyAnchorId'] == 0  && $boxConf['byCleanRoomId'] == 0 )
		{
			$boxInfo['state'] = AbyssCopyDef::$BOX_STATE['CAN_OPEN'];
		}
		$this->copyData['rooms'][$roomId]['boxes'][$boxId] = $boxInfo;
	
		if($record)
		{
			$this->modifyObjList[] = array(
					'op' => AbyssCopyDef::$OP_TYPE['ADD'],
					'roomId' => $roomId,
					'type' =>  AbyssCopyDef::$OBJ_TYPE['BOX'],
					'id' => $boxId,
					'data' => $boxInfo['state'] );
		}
		return true;
	}
	
	public function addVial($roomId, $vialId, $record = true)
	{
		$vialConf = AbyssCopy::getTriggerConf($vialId);
		if(!isset($vialConf))
		{
			Logger::fatal('no vial:%d, room:%d', $vialId, $roomId);
			throw new Exception('config');
		}
	
		if($roomId != $vialConf['roomId'])
		{
			Logger::debug('add vial in other room:%d, curRoom:%d', $vialConf['roomId'], $roomId);
			return $this->addBox($vialConf['roomId'], $vialId);
		}
	
		if(!$this->isRoomOpen($roomId))
		{
			Logger::info('addVial failed. room:%d not open', $roomId);
			throw new Exception('fake');
		}
		
		if ( isset($this->copyData['rooms'][$roomId]['vials'][$vialId] ) )
		{
			Logger::warning('add a exist vial:%d, room:%d', $vialId, $roomId);
			return false;
		}
	
		if( $vialConf['type'] != AbyssCopyDef::$TRIGGER_TYPE['VIAL'])
		{
			Logger::fatal('trigger:%d in room:%d not a box', $vialId, $roomId);
			throw new Exception('config');
		}
	
		Logger::debug('add vial:%d in room:%d', $vialId, $roomId);
		$vialInfo = 0;
		$this->copyData['rooms'][$roomId]['vials'][$vialId] = $vialInfo;
	
		if($record)
		{
			$this->modifyObjList[] = array(
					'op' => AbyssCopyDef::$OP_TYPE['ADD'],
					'roomId' => $roomId,
					'type' =>  AbyssCopyDef::$OBJ_TYPE['VIAL'],
					'id' => $vialId,
					'data' => $vialInfo );
		}
		return true;
	}
	
	/**
	 * 新加一个怪物
	 * @param int $anchorId
	 */
	public function addEnemy($roomId, $anchorId, $record = true)
	{
		$anchorConf = AbyssCopy::getEnemyAnchorConf($anchorId);
		if(!isset($anchorConf))
		{
			Logger::fatal('no enemyanchor:%d in room:%d', $anchorId, $roomId);
			throw new Exception('config');
		}
	
		if($roomId != $anchorConf['roomId'])
		{
			Logger::debug('add enemy in other room:%d, curRoom:%d', $anchorConf['roomId'], $roomId);
			return $this->addEnemy($anchorConf['roomId'], $anchorId, $record);
		}
		
		if(!$this->isRoomOpen($roomId))
		{
			Logger::info('addEnemy failed. room:%d not open', $roomId);
			throw new Exception('fake');
		}
	
		if ( isset($this->copyData['rooms'][$roomId]['enemyAnchors'][$anchorId] ) )
		{
			Logger::warning('add a exist enemyanchor:%d, room:%d', $anchorId, $roomId);
			return false;
		}
	
		$anchorInfo = self::genArmy($anchorConf);
		if( $anchorConf['preAtkId'] == 0 )
		{
			$anchorInfo['state'] = AbyssCopyDef::$ENEMY_STATE['CAN_ATK'];
		}
	
		Logger::debug('add enemy:%d in room:%d, army:%d', $anchorId, $roomId, $anchorInfo['armyId']);
		$this->copyData['rooms'][$roomId]['enemyAnchors'][$anchorId] = $anchorInfo;
	
		if( $record )
		{
			$this->modifyObjList[] = array(
					'op' => AbyssCopyDef::$OP_TYPE['ADD'],
					'roomId' => $roomId,
					'type' =>  AbyssCopyDef::$OBJ_TYPE['ENEMY'],
					'id' => $anchorId,
					'data' => $anchorInfo );
		}
		return true;
	}
	
	public function addQuestion($roomId, $triggerId, $questId)
	{
		$this->copyData['rooms'][$roomId]['quests'][$triggerId] = $questId;		
	}
	
	
	public function modifyEnemy($roomId, $anchorId, $modifyData, $record = true)
	{
		if ( !isset($this->copyData['rooms'][$roomId]['enemyAnchors'][$anchorId] ) )
		{
			Logger::warning('not found enemyanchor:%d, room:%d', $anchorId, $roomId);
			throw new Exception('fake');
		}
	
		$anchorInfo = $this->copyData['rooms'][$roomId]['enemyAnchors'][$anchorId];
	
		foreach($modifyData as $key => $value)
		{
			$anchorInfo[$key] = $value;
		}
			
		Logger::debug('change enemyanchor:%d in room:%d, changed:%s', $anchorId, $roomId, $modifyData);
	
		$this->copyData['rooms'][$roomId]['enemyAnchors'][$anchorId] = $anchorInfo;
	
		if($record)
		{
			$this->modifyObjList[] = array(
					'op' => AbyssCopyDef::$OP_TYPE['MODIFY'],
					'roomId' => $roomId,
					'type' =>  AbyssCopyDef::$OBJ_TYPE['ENEMY'],
					'id' => $anchorId,
					'data' => $modifyData );
		}
		return true;
	}
	
	public function modifyTrigger($roomId, $id,  $modifyData)
	{
		$conf = AbyssCopy::getTriggerConf($id);
		if(!isset($conf))
		{
			Logger::fatal('no trigger:%d in room:%d', $id, $roomId);
			throw new Exception('config');
		}
		switch($conf['type'])
		{
			case AbyssCopyDef::$TRIGGER_TYPE['BOX']:
				$this->modifyBox($roomId, $id, $modifyData);
				break;
			default:
				Logger::warning('invalid type:%d, to modify trigger:%d', $conf['type'], $id);
				break;
		}
	}
	
	public function modifyBox($roomId, $boxId, $modifyData)
	{
		if(!isset($this->copyData['rooms'][$roomId]['boxes'][$boxId]))
		{
			Logger::warning('no box:%d in room:%d', $boxId, $roomId );
			throw new Exception('fake');
		}
	
		$boxInfo = $this->copyData['rooms'][$roomId]['boxes'][$boxId];
	
		foreach($modifyData as $key => $value)
		{
			$boxInfo[$key] = $value;
		}
		$this->modifyObjList[] = array(
				'op' => AbyssCopyDef::$OP_TYPE['MODIFY'],
				'roomId' => $roomId,
				'type' =>  AbyssCopyDef::$OBJ_TYPE['BOX'],
				'id' => $boxId,
				'data' => $modifyData );

			
		Logger::debug('change box:%d in room:%d, changed:%s',
		$boxId, $roomId, $modifyData);
	
		$this->copyData['rooms'][$roomId]['boxes'][$boxId] = $boxInfo;
	
		return true;
	
	}
	
	
	public function delTrigger($roomId, $id)
	{
		$conf = AbyssCopy::getTriggerConf($id);
		if(!isset($conf))
		{
			Logger::fatal('no trigger:%d in room:%d', $id, $roomId);
			throw new Exception('config');
		}
		switch($conf['type'])
		{
			case AbyssCopyDef::$TRIGGER_TYPE['BOX']:
				$this->delBox($roomId, $id);
				break;
			case AbyssCopyDef::$TRIGGER_TYPE['VIAL']:
				$this->delVial($roomId, $id);
				break;
			default:
				Logger::warning('invalid type:%d, trigger:%d', $conf['type'], $id);
				break;
		}
	
	
	}
	public function delBox($roomId, $boxId)
	{
		if ( !isset($this->copyData['rooms'][$roomId]['boxes'][$boxId] ) )
		{
			Logger::warning('not found box:%d, room:%d', $boxId, $roomId);
			return false;
		}
		unset($this->copyData['rooms'][$roomId]['boxes'][$boxId]);
		Logger::debug('del box:%d in room:%d', $boxId, $roomId);
	
		$this->modifyObjList[] = array(
				'op' => AbyssCopyDef::$OP_TYPE['DEL'],
				'roomId' => $roomId,
				'type' =>  AbyssCopyDef::$OBJ_TYPE['BOX'],
				'id' => $boxId );
	
		return true;
	}
	
	public function delVial($roomId, $vialId)
	{
		if ( !isset($this->copyData['rooms'][$roomId]['vials'][$vialId] ) )
		{
			Logger::warning('not found vial:%d, room:%d', $vialId, $roomId);
			return false;
		}
		unset($this->copyData['rooms'][$roomId]['vials'][$vialId]);
		Logger::debug('del vial:%d in room:%d', $vialId, $roomId);
	
		$this->modifyObjList[] = array(
				'op' => AbyssCopyDef::$OP_TYPE['DEL'],
				'roomId' => $roomId,
				'type' =>  AbyssCopyDef::$OBJ_TYPE['VIAL'],
				'id' => $vialId );
	
		return true;
	}
	
	public function delEnemy($roomId, $anchorId)
	{
		if ( !isset($this->copyData['rooms'][$roomId]['enemyAnchors'][$anchorId] ) )
		{
			Logger::warning('not found enemyanchor:%d, room:%d', $anchorId, $roomId);
			return false;
		}
		Logger::debug('del enemyanchor:%d in room:%d', $anchorId, $roomId);
		unset($this->copyData['rooms'][$roomId]['enemyAnchors'][$anchorId]);
	
		$this->modifyObjList[] = array(
				'op' => AbyssCopyDef::$OP_TYPE['DEL'],
				'roomId' => $roomId,
				'type' =>  AbyssCopyDef::$OBJ_TYPE['ENEMY'],
				'id' => $anchorId );
		return true;
	}
	

	public function delQuestion($roomId, $triggerId)
	{
		if ( !isset($this->copyData['rooms'][$roomId]['quests'][$triggerId] ) )
		{
			Logger::warning('not found quest:%d, room:%d', $triggerId, $roomId);
			return false;
		}
		unset($this->copyData['rooms'][$roomId]['quests'][$triggerId]);
	}
	
	public function saveCopyData()
	{
		$key = self::getMemKey($this->uuid);
	
		//TODO:给个副本最大完成时间，安全一点
		$ret = McClient::set($key, $this->copyData);
		if ('STORED' != $ret)
		{
			Logger::fatal('failed to set copy data');
			throw new Exception('inter');
		}
	}
	
	public function setCopyInvalid()
	{
		$this->copyData['copyId'] = -$this->copyData['copyId'];
		$this->saveCopyData();
	}
	
	public static function isCopyExist($uuid)
	{
		$key = self::getMemKey($uuid);
	
		$ret = McClient::get($key);
		return !empty($ret);
	}
	
	public static function delCopy($uuid)
	{
		$key = self::getMemKey($uuid);
	
		$ret = McClient::del($key);
		if ('DELETED' != $ret)
		{
			Logger::fatal('failed to del copy:%d', $uuid);
			throw new Exception('inter');
		}
	}

	
	public static function getMemKey($uuid)
	{
		if ($uuid == 0)
		{
			throw new Exception('fake');
			Logger::fatal('getMemKey uuid=0');
		}
		return 'abyss_'.$uuid;
	}
	
	

	public static function genArmy($enemyAnchorConf)
	{
		$index = rand(0, count($enemyAnchorConf['armyIdArr'])-1);
		$armyId = $enemyAnchorConf['armyIdArr'][$index];
	
		$data =  array(
				'armyId' => $armyId,
				'fightNum' => 0,
				'beatenNum' => 0,
				'state' => AbyssCopyDef::$ENEMY_STATE['SHOW'],
		);
	
		return $data;
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */