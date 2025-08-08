<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: City.class.php 36864 2013-01-24 02:47:23Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/city/City.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2013-01-24 10:47:23 +0800 (四, 2013-01-24) $
 * @version $Revision: 36864 $
 * @brief
 *
 **/

class City implements ICity
{
	/**
	 *
	 * 当前登录用户的uid
	 *
	 * @var int
	 *
	 */
	private $m_uid;

	/**
	 *
	 * 当前登录用户已经进入的城镇列表
	 *
	 * @var array(int)
	 *
	 */
	private $m_enter_town_list;

	public function City()
	{
		$this->m_uid = RPCContext::getInstance()->getUid();

		$this->m_enter_town_list = $this->getEnterTownList();
	}

	/* (non-PHPdoc)
	 * @see ICity::checkEnter()
	 */
	public function checkEnter($town_id)
	{
		//格式化数据
		$town_id = intval($town_id);

		//当前所在城镇
		$townId = RPCContext::getInstance()->getTownId();

		if(empty($townId))
		{
			return TRUE;
		}

		if($townId == $town_id)
		{
			Logger::warning("user already in town:%d", $town_id);
		}
		else
		{
			Logger::warning("user in town:%d now, trying to enter:%d", $townId, $town_id);
		}

		return false;
	}

	/* (non-PHPdoc)
	 * @see ITown::enterTown()
	 */
	public function enterTown($town_id, $x, $y) {
		//格式化输入
		$town_id = intval($town_id);
		$x = intval($x);
		$y = intval($y);

		//如果城镇不存在,则throw exception
		if ( !isset(btstore_get()->TOWN[$town_id]) )
		{
			Logger::FATAL('invalid town_id:%d', $town_id);
			throw new Exception('fake');
		}

		//如果没有迁入过该城镇,并且该城镇不为不可迁入,则出错
		if ( $this->isEnterTown($town_id) == FALSE )
		{
			if ( self::getTownType($town_id) == TownDef::TOWN_TYPE_NOT_MOVEIN )
			{
				$this->addEnterTownList($town_id);
			}
			else
			{
				Logger::FATAL('not movein town!%d', $town_id);
				throw new Exception('fake');
			}
		}

		$arr = self::userInfoForEnterTown();

		RPCContext::getInstance ()->enterTown ( $town_id, $x, $y, $arr );
		RPCContext::getInstance()->setSession(TownDef::TOWN_SESSION_TOWN_ID, $town_id);

		// 通知成就系统
		EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::ARRIVE_TOWN, $town_id);
	}

	public static function userInfoForEnterTown()
	{
		/**
		 * 其中userinfo部分的结构如下:
		 * [{
		 * name:用户名称
		 * tid:模板id
		 * uid:用户id
		 * title:称号id
		 * petInfo:宠物信息
		 * }]
		 */
		$uid = RPCContext::getInstance()->getUid();
		$user = EnUser::getUserObj();
		$utid = $user->getUtid();
		$arr = array (
			'name' => $user->getUname(),
			'tid' => $utid,
		);

		$title_id = EnAchievements::getUserTitle($uid);
		if ( $title_id !== 0 )
		{
			$arr['title'] = $title_id;
			if ( EnAchievements::isTitleMustShow($title_id) )
			{
				$arr['alwaysShow'] = TRUE;
			}
		}

		$arr += GuildLogic::getLoginInfo($uid);

		$petInfo = EnPet::getUserCurPet ( $uid );
		if ($petInfo !== false)
		{
			$petName = $petInfo ['name'];
			$petId = intval ( $petInfo ['id'] );
			if ($petId == 0)
			{
				Logger::fatal ( "invalid pet id:%d", $petId );
				throw new Exception ( "fake" );
			}
			$petTid = intval ( $petInfo ['tid'] );
			$arr ['petInfo'] = array ('pid' => $petId, 'name' => $petName, 'tid' => $petTid );
		}
		
		//时装
		if ($user->isShowDress())
		{
			$arr['dress'] = $user->getMasterHeroDressTemplate();
			$dressInfo = DressLogic::getDressRommInfo($uid);
			$arr['imageDress'] = array(0 => array ('template_id' => $dressInfo['cur_dress']));
		}
		$rideInfo = RideLogic::getInfo($uid);
		$arr['rideid'] = $rideInfo['cur_ride'];

		return $arr;
	}

	/* (non-PHPdoc)
	 * @see ITown::leaveTown()
	 */
	public function leaveTown() {

		RPCContext::getInstance ()->leaveTown ();
		$town_id = RPCContext::getInstance()->getSession(TownDef::TOWN_SESSION_TOWN_ID);
		if ( !in_array($town_id, $this->m_enter_town_list) )
		{
			Logger::WARNING('invalid town id in city.leaveTown!');
			throw new Exception('fake');
		}
		RPCContext::getInstance()->unsetSession(TownDef::TOWN_SESSION_TOWN_ID);
		if ( !empty($town_id) )
		{
			RPCContext::getInstance()->setSession(TownDef::TOWN_SESSION_LAST_TOWN_ID, $town_id);
		}
	}

	/* (non-PHPdoc)
	 * @see ITown::getEnterTownList()
	 */
	public function enterTownList() {

		return $this->m_enter_town_list;
	}

	/* (non-PHPdoc)
	 * @see ITown::townInfo()
	 */
	public function townInfo($town_id) {

	}

	/* (non-PHPdoc)
	 * @see ITown::moveInTown()
	 */
	public function moveInTown($town_id) {
		//格式化输入
		$town_id = intval($town_id);

		$return = array('movein_success' => FALSE );

		//如果已经进入过城镇,则出错
		if ( $this->isEnterTown($town_id) == TRUE )
		{
			Logger::DEBUG('alreay enter town:%d', $town_id);
			return $return;
		}

		//如果不满足迁入条件,则出错
		if ( $this->canMoveInTown($town_id) == FALSE )
		{
			return $return;
		}

		$user = EnUser::getUserObj();
		$group_id = $user->getGroupId();
		$port_id = self::getEnterPort($town_id, $group_id);
		//如果阵营港口是0,则迁入中立港口.
		if ( $port_id == 0 )
		{
			$port_id = self::getEnterPort($town_id);
			if ( $port_id == 0 )
			{
				Logger::FATAL('invalid city:%d port config', $town_id);
				throw new Exception('fake');
			}
		}

		//迁入港口
		$port = new Port();
		$port->moveIntoPort($port_id);

		//将当前城镇添加进已进入列表
		$this->addEnterTownList($town_id);

		$return['movein_success'] = TRUE;
		$return['belly'] = $user->getBelly();

		return $return;
	}

	/**
	 *
	 * 是否进入过城镇
	 *
	 * @param int $town_id						城镇ID
	 *
	 * @return boolean
	 */
	public function isEnterTown($town_id)
	{
		$enterTownList = $this->getEnterTownList();
		if ( in_array($town_id, $enterTownList) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 *
	 * 是否可以迁入城镇
	 *
	 * @param int $town_id
	 *
	 * @return boolean
	 */
	public function canMoveInTown($town_id)
	{
		$enter_req = self::getEnterReq($town_id);

		//该城镇是否可以迁入
		if ( self::getTownType($town_id) != TownDef::TOWN_TYPE_CAN_MOVEIN )
		{
			Logger::DEBUG('town:%d can not movein!', $town_id);
			return FALSE;
		}

		$user = EnUser::getUserObj($this->m_uid);

		//检验是否通过所需求的副本,目前不存在此校验
		if ( isset($enter_req[TownDef::TOWN_ENTER_REQ_ARMY_ID]) )
		{
			foreach ( $enter_req[TownDef::TOWN_ENTER_REQ_ARMY_ID] as $army_id )
			{
				if ( CopyLogic::isEnemyDefeated($army_id) == FALSE )
				{
					Logger::DEBUG('no pass copy id:%d', $army_id);
					return FALSE;
				}
			}
		}

		//检验是否接受了某任务
		if ( !empty($enter_req[TownDef::TOWN_ENTER_REQ_ACCEPT_TASK_ID]) )
		{
			$task_id = $enter_req[TownDef::TOWN_ENTER_REQ_ACCEPT_TASK_ID];
			if ( EnTask::isAccept($task_id) == FALSE )
			{
				Logger::DEBUG('no accept task id:%d', $task_id);
				return FALSE;
			}
		}

		//检验是否达到用户等级
		if ( isset($enter_req[TownDef::TOWN_ENTER_REQ_USER_LEVEL]) )
		{
			$level = $user->getLevel();

			if ( $level < $enter_req[TownDef::TOWN_ENTER_REQ_USER_LEVEL] )
			{
				Logger::DEBUG('use level is not satisfied!use_level:%d, need level:%d',
					$level, $enter_req[TownDef::TOWN_ENTER_REQ_USER_LEVEL]);
				return FALSE;
			}
		}

		//检测是否选择阵营
		if ( isset($enter_req[TownDef::TOWN_ENTER_REQ_GROUP]) &&
			!empty($enter_req[TownDef::TOWN_ENTER_REQ_GROUP]) )
		{
			if ( $user->getGroupId() == 0 )
			{
				Logger::DEBUG('not init group!');
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 *
	 * 得到已经进入的城镇列表
	 *
	 * @return array(int)
	 */
	public function getEnterTownList()
	{
		$list = RPCContext::getInstance()->getSession(TownDef::TOWN_SESSION_ENTER_TOWN_LIST);
		if ( !isset($list) )
		{
			$list = CityDAO::getEnterTownList($this->m_uid);
			RPCContext::getInstance()->setSession(TownDef::TOWN_SESSION_ENTER_TOWN_LIST, $list);
		}
		$list = array_merge($list, TownConfig::$DEFAULT_ENTER_TOWN_LIST);
		return $list;
	}

	/**
	 *
	 * 将城镇town_id添加到已进入城镇列表
	 *
	 * @param int $town_id
	 *
	 * @return NULL
	 */
	public function addEnterTownList($town_id)
	{
		if ( $this->isEnterTown($town_id) == TRUE )
		{
			Logger::FATAL('already enter town:%d', $town_id);
			throw new Exception('fake');
		}
		CityDAO::insertEnterTownList($this->m_uid, $town_id);
		$this->m_enter_town_list[] = $town_id;
		RPCContext::getInstance()->setSession(TownDef::TOWN_SESSION_ENTER_TOWN_LIST, $this->m_enter_town_list);
	}

	/**
	 *
	 * 该城镇是否有此服务
	 *
	 * @param int $town_id						城镇ID
	 * @param string $service					@see TownDef::TOWN_SERVICE_*
	 * @param int $service_id					服务ID
	 *
	 * @return boolean							如果存在则为TRUE
	 */
	public static function isTownService($town_id, $service, $service_id)
	{
		$town_services = self::getTownServices($town_id);
		if ( !isset($town_services[$service]) )
		{
			return FALSE;
		}
		else if ( !in_array($service_id, $town_services[$service]) )
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	public static function getTownType($town_id)
	{
		if ( !isset(btstore_get()->TOWN[$town_id]) ||
			!isset(btstore_get()->TOWN[$town_id][TownDef::TOWN_TYPE]) )
		{
			Logger::FATAL('invalid town_id:%d', $town_id);
			throw new Exception('fake');
		}

		return btstore_get()->TOWN[$town_id][TownDef::TOWN_TYPE];
	}

	/**
	 *
	 * 得到城镇的服务列表
	 *
	 * @param int $town_id				城镇ID
	 *
	 * @throws Exception				如果城镇id invaild,则throw exception
	 *
	 * @return array(int)
	 */
	public static function getTownServices($town_id)
	{
		if ( !isset(btstore_get()->TOWN[$town_id]) ||
			!isset(btstore_get()->TOWN[$town_id][TownDef::TOWN_SERVICES]) )
		{
			Logger::FATAL('invalid town_id:%d', $town_id);
			throw new Exception('fake');
		}

		return btstore_get()->TOWN[$town_id][TownDef::TOWN_SERVICES]->toArray();
	}

	/**
	 *
	 * 得到城镇进入需求
	 *
	 * @param int $town_id				城镇ID
	 *
	 * @throws Exception				如果城镇id invaild,则throw exception
	 *
	 * @return array
	 * <code>
	 * {
	 *		TownDef::TOWN_ENTER_REQ_COPY_ID:int
	 *		TownDef::TOWN_ENTER_REQ_USER_LEVEL:[levelmin, levelmax)
	 *		TownDef::TOWN_ENTER_REQ_GROUP:int
	 * }
	 * </code>
	 */
	public static function getEnterReq($town_id)
	{
		if ( !isset(btstore_get()->TOWN[$town_id]) ||
			!isset(btstore_get()->TOWN[$town_id][TownDef::TOWN_ENTER_REQ]) )
		{
			Logger::FATAL('invalid town_id:%d', $town_id);
			throw new Exception('fake');
		}

		return btstore_get()->TOWN[$town_id][TownDef::TOWN_ENTER_REQ];
	}

	/**
	 *
	 * 得到进入的城镇港口ID
	 *
	 * @param int $town_id				城镇ID
	 * @param int $group_id				阵营ID
	 *
	 * @throws Exception				如果城镇id invalid,则throw exception
	 *
	 * @return $port_id
	 */
	public static function getEnterPort($town_id, $group_id = 0)
	{
		if ( !isset(btstore_get()->TOWN[$town_id]) ||
			!isset(btstore_get()->TOWN[$town_id][TownDef::TOWN_ENTER_PORTS]) )
		{
			Logger::FATAL('invalid town_id:%d', $town_id);
			throw new Exception('fake');
		}
		return btstore_get()->TOWN[$town_id][TownDef::TOWN_ENTER_PORTS][$group_id];
	}

	/**
	 *
	 * 得到城镇出生点坐标
	 *
	 * @param int $town_id
	 *
	 * @return array
	 * <code>
	 * 	{
	 * 		'x':int
	 * 		'y':int
	 * 	}
	 * </code>
	 */
	public static function getTownBirthCoordinate($town_id)
	{
		if ( !isset(btstore_get()->TOWN[$town_id]) ||
			!isset(btstore_get()->TOWN[$town_id][TownDef::TOWN_BIRTH_COORDINATE]) )
		{
			Logger::FATAL('invalid town_id:%d', $town_id);
			throw new Exception('fake');
		}

		return array(
			TownDef::TOWN_BIRTH_COORDINATE_X => rand(
				btstore_get()->TOWN[$town_id][TownDef::TOWN_BIRTH_COORDINATE][TownDef::TOWN_BIRTH_COORDINATE_X][0],
				btstore_get()->TOWN[$town_id][TownDef::TOWN_BIRTH_COORDINATE][TownDef::TOWN_BIRTH_COORDINATE_X][1]
				),
			TownDef::TOWN_BIRTH_COORDINATE_Y => rand(
				btstore_get()->TOWN[$town_id][TownDef::TOWN_BIRTH_COORDINATE][TownDef::TOWN_BIRTH_COORDINATE_Y][0],
				btstore_get()->TOWN[$town_id][TownDef::TOWN_BIRTH_COORDINATE][TownDef::TOWN_BIRTH_COORDINATE_Y][1]
				),
		);
	}

	public static function isValidateCoordinate($town_id, $x, $y)
	{
		if ( !isset(btstore_get()->TOWN[$town_id]))
		{
			Logger::FATAL('invalid town_id:%d', $town_id);
			throw new Exception('fake');
		}

		if ($x >= btstore_get()->TOWN[$town_id][TownDef::TOWN_WIDTH] || $x <=0)
		{
			return false;
		}

		if ($y >= btstore_get()->TOWN[$town_id][TownDef::TOWN_HEIGHT] || $y <=0)
		{
			return false;
		}

		return true;
	}

	/**
	 *
	 * 得到当前的城镇的用户数量
	 *
	 * @param int $town_id
	 *
	 * @return int
	 */
	public static function getTownUserCount($town_id)
	{
		$town_id = intval($town_id);
		$server_proxy = new ServerProxy();
		return $server_proxy->getTownUserCount($town_id);
	}

	/**
	 * 返回npc信息
	 *
	 * @param uint $npcId
	 *
	 * @return array
	 * <code>
	 * 'townId': townId
	 * 'x': 坐标
	 * 'y': 坐标
	 * </code>
	 */
	public static function getNpcInfo ($npcId)
	{
		if (!isset(btstore_get()->NPC_TOWN[$npcId]))
		{
			return false;
		}
		return btstore_get()->NPC_TOWN[$npcId];
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */