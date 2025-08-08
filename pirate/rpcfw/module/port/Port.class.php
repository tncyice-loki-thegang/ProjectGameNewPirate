<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Port.class.php 39706 2013-03-01 03:15:04Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/Port.class.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-03-01 11:15:04 +0800 (五, 2013-03-01) $
 * @version $Revision: 39706 $
 * @brief
 *
 **/



class Port implements Iport
{
	/* (non-PHPdoc)
	 * @see IPort::attackResource()
	 */
	public function attackResource($port_id, $page_id, $resource_id) {
		//格式化输入
		$port_id = intval($port_id);
		$page_id = intval($page_id);
		$resource_id = intval($resource_id);

		$user_port_id = $this->getPort();
		if ( $port_id != $user_port_id )
		{
			Logger::FATAL('uid:%d in port:%d, not in port:%d',
				RPCContext::getInstance()->getUid(), $user_port_id, $port_id );
		}

		$resource = new PortResource($port_id, $page_id, $resource_id);
		return $resource->attackResource();
	}

	/* (non-PHPdoc)
	 * @see IPort::givenupResource()
	 */
	public function givenupResource($port_id, $page_id, $resource_id)
	{
		//格式化输入
		$port_id = intval($port_id);
		$page_id = intval($page_id);
		$resource_id = intval($resource_id);

		$resource = new PortResource($port_id, $page_id, $resource_id);
		return $resource->givenupResource();
	}

	/* (non-PHPdoc)
	 * @see IPort::plunderResource()
	 */
	public function plunderResource($port_id, $page_id, $resource_id)
	{
		//格式化输入
		$port_id = intval($port_id);
		$page_id = intval($page_id);
		$resource_id = intval($resource_id);
		$resource = new PortResource($port_id, $page_id, $resource_id);
		return $resource->plunder();
	}

	public function dueResource($uid, $port_id, $page_id, $resource_id,$occupy_time=0)
	{
		//格式化输入
		$uid = intval($uid);
		$port_id = intval($port_id);
		$page_id = intval($page_id);
		$resource_id = intval($resource_id);

		$resource = new PortResource($port_id, $page_id, $resource_id);
		return $resource->dueResource($uid,$occupy_time);
	}

	/* (non-PHPdoc)
	 * @see IPort::excavateResource()
	 */
	public function excavateResource($port_id, $page_id, $resource_id)
	{
		//格式化输入
		$uid = RPCContext::getInstance()->getUid();
		$port_id = intval($port_id);
		$page_id = intval($page_id);
		$resource_id = intval($resource_id);

		$resource = new PortResource($port_id, $page_id, $resource_id);
		return $resource->excavate($uid);
	}

	/* (non-PHPdoc)
	 * @see IPort::resourceInfo()
	 */
	public function resourceInfo($port_id, $page_id) {
		//格式化输入
		$port_id = intval($port_id);
		$page_id = intval($page_id);

		$return = array();

		if ( !isset(btstore_get()->PORT[$port_id]) ||
			!isset(btstore_get()->PORT[$port_id][PortDef::PORT_RESOURCE_GROUPS]) ||
			!isset(btstore_get()->PORT[$port_id][PortDef::PORT_RESOURCE_GROUPS][$page_id]) )
		{
			Logger::WARNING('invalid port id:%d, page_id:%d', $port_id, $page_id);
			throw new Exception('fake');
		}

		$resource_group_id = btstore_get()->PORT[$port_id][PortDef::PORT_RESOURCE_GROUPS][$page_id];

		if ( !isset(btstore_get()->PORTRESOURCE[$resource_group_id]) )
		{
			Logger::WARNING('invalid port resource group id:%d', $resource_group_id);
			throw new Exception('fake');
		}

		$resource_group = btstore_get()->PORTRESOURCE[$resource_group_id];

		foreach ( $resource_group[PortDef::PORT_RESOURCE_LIST] as $resource_id => $value )
		{
			$resource = new PortResource($port_id, $page_id, $resource_id);
			$return[$resource_id] = $resource->resourceInfo();
		}
		return $return;
	}

	/* (non-PHPdoc)
	 * @see IPort::selfResourceInfo()
	 */
	public function selfResourceInfo()
	{
		$uid = RPCContext::getInstance()->getUid();

		$info = PortResourceDAO::getAllPortResource($uid);

		$return = array();
		foreach ( $info as $data )
		{
			//过期时间需要加上金币延长时间
			$extendtime=0;
			$grade_id=$data[PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE];
			if ($grade_id>0 && isset(btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]))
			{
				$extendtime=btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]['time'];
			}

			$resourceInfo = PortResource::getResouceInfo($data[PortDef::PORT_SQL_PORT_ID],
					$data[PortDef::PORT_SQL_PAGE_ID], $data[PortDef::PORT_SQL_RESOURCE_ID]);
			$array = array();
			$array['port_id'] = $data[PortDef::PORT_SQL_PORT_ID];
			$array['page_id'] = $data[PortDef::PORT_SQL_PAGE_ID];
			$array['resource_id'] = $data[PortDef::PORT_SQL_RESOURCE_ID];
			$array['due_time'] = intval($data[PortDef::PORT_SQL_OCCUPY_TIME]
					+ $resourceInfo[PortDef::PORT_RESOURCE_TIME])
					+ $extendtime //加上金币延长时间
					- $data[PortDef::PORT_SQL_PLUNDER_TIME] *
					ExcavateUtil::getPlunderSubOccpuyTime();
			$array['protect_time'] = intval($data[PortDef::PORT_SQL_OCCUPY_TIME]
					+ $resourceInfo[PortDef::PORT_RESOURCE_PROTECTED_TIME]);
			$array['plunder_protect_time'] = $data[PortDef::PORT_SQL_PLUNDER_PROTECTED_TIME];
			$array['is_excavate'] = $data[PortDef::PORT_SQL_IS_EXCAVATE];
			$array['plunder_time'] = $data[PortDef::PORT_SQL_PLUNDER_TIME];
			$array['occpuy_time'] = $data[PortDef::PORT_SQL_OCCUPY_TIME];
			$array['gold_extend_count'] = ($data[PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE]>0)?1:0;
			$return[] = $array;
		}

		return $return;
	}

	/* (non-PHPdoc)
	 * @see IPort::resetPlunderCdByGold()
	 */
	public function resetPlunderCdByGold()
	{
		$portBerth = new PortBerth();
		return $portBerth->resetPlunderCdByGold();
	}

	/* (non-PHPdoc)
	 * @see IPort::getPlunderInfo()
	 */
	public function getPlunderInfo()
	{
		$portBerth = new PortBerth();
		return array(
			'plunder_time' => $portBerth->getPlunderTime(),
			'plunder_cd' => $portBerth->getPlunderCd(),
		);
	}

	/* (non-PHPdoc)
	 * @see IPort::enterPort()
	 */
	public function enterPort($port_id) {
		//格式化输入
		$port_id = intval($port_id);

		//检查是否进入过港口所在城镇,2012-3-5按照需求不再检测
		/*
		$town_id = self::getTownByPort($port_id);
		$city = new City();

		//如果港口所在的城镇从未到达过,则请求非法
		if (  $city->isEnterTown($town_id) == FALSE )
		{
			Logger::FATAL('port_id:%d is belong to town:%d,but town:%d not enter !',
				$port_id, $town_id, $town_id);
			throw new Exception('fake');
		}
		*/

		//检查功能是否开启2012-02-29按照小白的需求后端不在检查港口相关的开关
		/*
		if ( EnSwitch::isOpen(SwitchDef::PORT) == FALSE )
		{
			Logger::FATAL('port is not open!');
			throw new Exception('fake');
		}
		*/

		//调用任务系统
		TaskNotify::operate(TaskOperateType::ENTER_PORT);

		RPCContext::getInstance()->setSession(PortDef::PORT_SESSION_PORT_ID, $port_id);
	}

	/* (non-PHPdoc)
	 * @see IPort::leavePort()
	 */
	public function leavePort() {
		RPCContext::getInstance()->unsetSession(PortDef::PORT_SESSION_PORT_ID);
	}

	/* (non-PHPdoc)
	 * @see IPort::enterPortResource()
	 */
	public function enterPortResource($port_id) {

		//调用任务系统
		TaskNotify::operate(TaskOperateType::ENTER_RESOURCE_SCENE);

		RPCContext::getInstance()->setSession(PortDef::PORT_SESSION_RESOURCE_ID, $port_id);

		return $this->selfResourceInfo();
	}

	/* (non-PHPdoc)
	 * @see IPort::leavePortResource()
	 */
	public function leavePortResource() {
		RPCContext::getInstance()->unsetSession(PortDef::PORT_SESSION_RESOURCE_ID);
	}

	/* (non-PHPdoc)
	 * @see IPort::portBerthInfo()
	 */
	public function portBerthInfo($port_id, $page_id) {
		//格式化输入
		$port_id = intval($port_id);
		$page_id = intval($page_id);

		//检查功能是否开启
		/*
		if ( EnSwitch::isOpen(SwitchDef::PORT) == FALSE )
		{
			Logger::FATAL('port is not open!');
			throw new Exception('fake');
		}
		*/

		$return = array (
			'data' => PortBerth::getBoatBerthList($port_id, $page_id),
			'page_count' => ceil(PortBerth::getBoatCount($port_id)/PortConfig::PORT_BERTH_NUM_PER_PAGE),
		);
		return $return;
	}

	/* (non-PHPdoc)
	 * @see IPort::selfBerthInfo()
	 */
	public function selfBerthInfo() {
		$port_id = self::getPort();

		//检查功能是否开启
		/*
		if ( EnSwitch::isOpen(SwitchDef::PORT) == FALSE )
		{
			Logger::FATAL('port is not open!');
			throw new Exception('fake');
		}
		*/

		//检测是否存在港口
		if ( empty($port_id) )
		{
			return array();
		}

		$uid = RPCContext::getInstance()->getUid();
		$page_id = PortBerth::getPageId($port_id, $uid);

		$return = array (
			'data' => PortBerth::getBoatBerthList($port_id, $page_id),
			'page_count' => ceil(PortBerth::getBoatCount($port_id)/PortConfig::PORT_BERTH_NUM_PER_PAGE),
			'page_id' => $page_id,
		);

		return $return;
	}

	/* (non-PHPdoc)
	 * @see IPort::moveInPort()
	 */
	public function moveInPort($port_id) {
		//格式化输入
		$port_id = intval($port_id);

		$return = array( 'move_success' => FALSE );

		//检查功能是否开启
		/*
		if ( EnSwitch::isOpen(SwitchDef::PORT) == FALSE )
		{
			Logger::FATAL('port is not open!');
			throw new Exception('fake');
		}
		*/

		$uid = RPCContext::getInstance()->getSession(PortDef::PORT_SESSION_UID);

		$portBerth = new PortBerth();
		$old_port_id = $portBerth->getPort();
		$time = $portBerth->getMoveCD();

		//如果已经在当前港口
		if ( $old_port_id == $port_id )
		{
			Logger::DEBUG('already in port:%d', $port_id);
			return $return;
		}

		//检查是否在搬迁CD中
		if ( $time >= Util::getTime() )
		{
			Logger::DEBUG('in move cd!');
			return $return;
		}

		//港口的搬迁只能在同城镇进行
		if ( self::getTownByPort($port_id) != self::getTownByPort($old_port_id) )
		{
			Logger::DEBUG('invalid port_id:%d, not in town_id:%d', $port_id, self::getTownByPort($old_port_id));
			return $return;
		}

		//检测是否阵营符合
		$user = EnUser::getUserObj();
		if ( self::getTownType($port_id) != PortDef::PORT_TYPE_NEUTRAL &&
			self::getTownType($port_id) != $user->getGroupId() )
		{
			Logger::WARNING('invalid port id:%d group is not match!', $port_id);
			return $return;
		}

		//增加迁移时间
		$time = Util::getTime() + PortConfig::PORT_MOVE_TIME;

		$this->moveIntoPort($port_id, $time);

		$return = array (
			'move_success' => TRUE,
			'belly' => $user->getBelly(),
		);

		return $return;
	}

	/* (non-PHPdoc)
	 * @see IPort::getMoveCD()
	 */
	public function getMoveCD()
	{
		$portBerth = new PortBerth();
		return $portBerth->getMoveCD();
	}

	public function moveIntoPort($port_id, $time = NULL)
	{
		$uid = RPCContext::getInstance()->getSession(PortDef::PORT_SESSION_UID);

		$portBerth = new PortBerth();
		$portBerth->setBerthInfo($port_id, $time);

		//重置所有的奴隶信息
		EnVassal::resetAll($uid);

		//重置所有的资源信息
		$this->giveupAllResource();
	}

	/* (non-PHPdoc)
	 * @see IPort::getPort()
	 */
	public function getPort()
	{
		$portBerth = new PortBerth();
		return $portBerth->getPort();
	}

	/**
	 *
	 * 放弃所有资源
	 *
	 * @return boolean
	 */
	public function giveupAllResource()
	{
		$uid = RPCContext::getInstance()->getSession(PortDef::PORT_SESSION_UID);

		$resources = PortResourceDAO::getAllPortResource($uid);

		foreach ( $resources as $value )
		{
			$port_id = $value[PortDef::PORT_SQL_PORT_ID];
			$page_id = $value[PortDef::PORT_SQL_PAGE_ID];
			$resource_id = $value[PortDef::PORT_SQL_RESOURCE_ID];

			$resource = new PortResource($port_id, $page_id, $resource_id);
			$resource->givenupResource();
		}

		return TRUE;
	}

	/**
	 *
	 * 得到港口类型
	 *
	 * @param int $port_id
	 * @throws Exception							如果港口id不存在于配置中,则throw exception
	 *
	 * @return int
	 */
	public static function getTownType($port_id)
	{
		if ( !isset(btstore_get()->PORT[$port_id]) ||
			!isset(btstore_get()->PORT[$port_id][PortDef::PORT_TYPE]) )
		{
			Logger::FATAL('invalid port_id:%d', $port_id);
			throw new Exception('config');
		}
		return btstore_get()->PORT[$port_id][PortDef::PORT_TYPE];
	}

	/**
	 *
	 * 得到港口所在的城镇ID
	 *
	 * @param int $port_id
	 *
	 * @throws Exception							如果港口id不存在于配置中,则throw exception
	 *
	 * @return int
	 */
	public static function getTownByPort($port_id)
	{
		if ( !isset(btstore_get()->PORT[$port_id]) ||
			!isset(btstore_get()->PORT[$port_id][PortDef::PORT_TOWN_ID]) )
		{
			Logger::FATAL('invalid port_id:%d', $port_id);
			throw new Exception('config');
		}
		return btstore_get()->PORT[$port_id][PortDef::PORT_TOWN_ID];
	}

	/**
	 *
	 * 得到港口系数
	 *
	 * @param int $port_id							港口ID
	 *
	 * @throws Exception							如果港口id不存在于配置中,则throw exception
	 *
	 * @return int
	 */
	public static function getPortModulus($port_id)
	{
		if ( !isset(btstore_get()->PORT[$port_id]) ||
			!isset(btstore_get()->PORT[$port_id][PortDef::PORT_MODULUS]) )
		{
			Logger::FATAL('invalid port_id:%d', $port_id);
			throw new Exception('config');
		}
		return btstore_get()->PORT[$port_id][PortDef::PORT_MODULUS];
	}

	/**
	 *
	 * 得到港口资源用户等级上限(为了向下兼容,则配置不存在时返回NULL,调用者需要处理)
	 *
	 * @param int $port_id							港口ID
	 *
	 * @throws Exception							如果港口id不存在于配置中,则throw exception
	 *
	 * @return int/NULL
	 */
	public static function getPortResourceUserLevelUp($port_id)
	{
		if ( !isset(btstore_get()->PORT[$port_id]) )
		{
			Logger::FATAL('invalid port_id:%d', $port_id);
			throw new Exception('config');
		}

		if ( isset(btstore_get()->PORT[$port_id][PortDef::PORT_RESOURCE_USER_LEVEL_UP]) )
		{
			return btstore_get()->PORT[$port_id][PortDef::PORT_RESOURCE_USER_LEVEL_UP];
		}
		else
		{
			return NULL;
		}
	}

	/**
	 *
	 * 得到港口资源用户等级下限(为了向下兼容,则配置不存在时返回NULL,调用者需要处理)
	 *
	 * @param int $port_id							港口ID
	 *
	 * @throws Exception							如果港口id不存在于配置中,则throw exception
	 *
	 * @return int/NULL
	 */
	public static function getPortResourceUserLevelLow($port_id)
	{
		if ( !isset(btstore_get()->PORT[$port_id]) )
		{
			Logger::FATAL('invalid port_id:%d', $port_id);
			throw new Exception('config');
		}

		if ( isset(btstore_get()->PORT[$port_id][PortDef::PORT_RESOURCE_USER_LEVEL_LOW]) )
		{
			return btstore_get()->PORT[$port_id][PortDef::PORT_RESOURCE_USER_LEVEL_LOW];
		}
		else
		{
			return NULL;
		}
	}

	/**
	 *
	 * 得到港口的扩展属性
	 *
	 * @param int $port_id							港口ID
	 *
	 * @throws Exception							如果港口id不存在于配置中,则throw exception
	 *
	 * @return array
	 * <code>
	 * {
	 *		PortDef::PORT_ATTR_ID_VOYAGE_BELLY_PERCENT	:int
	 *		PortDef::PORT_ATTR_ID_VOYAGE_MODIFY			:int
	 *		PortDef::PORT_ATTR_ID_SELL_BELLY_PERCENT	:int
	 *		PortDef::PORT_ATTR_ID_BATTLE_INJURE_MODIFY	:int
	 * }
	 * </code>
	 */
	public static function getPortExtendAttr($port_id)
	{
		if ( !isset(btstore_get()->PORT[$port_id]) ||
			!isset(btstore_get()->PORT[$port_id][PortDef::PORT_ATTRS]) )
		{
			Logger::FATAL('invalid port_id:%d', $port_id);
			throw new Exception('config');
		}

		$array = PortDef::$PORT_ATTRS_DEFAULT;
		foreach ( btstore_get()->PORT[$port_id][PortDef::PORT_ATTRS] as $attr_id => $value )
		{
			$array[$attr_id] = $value;
		}
		return $array;
	}

	public function extendResourceTimeByGold($port_id, $page_id, $resource_id,$grade_id)
	{
		//格式化输入
		$uid = RPCContext::getInstance()->getUid();
		$port_id = intval($port_id);
		$page_id = intval($page_id);
		$resource_id = intval($resource_id);

		$resource = new PortResource($port_id, $page_id, $resource_id);
		return $resource->extendTimeByGold($uid,$grade_id);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */