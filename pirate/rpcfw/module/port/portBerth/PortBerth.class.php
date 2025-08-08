<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: PortBerth.class.php 28289 2012-10-09 04:10:05Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/portBerth/PortBerth.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-10-09 12:10:05 +0800 (二, 2012-10-09) $
 * @version $Revision: 28289 $
 * @brief
 *
 **/

class PortBerth
{
	/**
	 *
	 * 用户uid
	 * @var int
	 */
	private $m_uid;

	/**
	 *
	 * 用户主船所在的港口ID
	 * @var int
	 */
	private $m_port_id;

	/**
	 *
	 * 搬迁冷却CD
	 * @var int
	 */
	private $m_move_cd;

	/**
	 *
	 * 上次掠夺的时间
	 * @var int
	 */
	private $m_last_plunder_time;

	/**
	 *
	 * 掠夺次数
	 * @var int
	 */
	private $m_plunder_time;

	/**
	 *
	 * 掠夺cd
	 * @var int
	 */
	private $m_plunder_cd;

	public function PortBerth($uid = 0)
	{
		if ( $uid == 0 )
		{
			$this->m_uid = RPCContext::getInstance()->getSession(PortDef::PORT_SESSION_UID);
		}
		else
		{
			$this->m_uid = $uid;
		}
		$this->getBerthInfo();
	}

	/**
	 *
	 * 得到用户主船所在的港口ID
	 *
	 * @return int
	 */
	public function getPort()
	{
		return $this->m_port_id;
	}

	/**
	 *
	 * 得到用户主船所在的页ID
	 *
	 * @return int
	 */
	public function getPortPageId()
	{
		return self::getPageId($this->m_port_id, $this->m_uid);
	}

	/**
	 *
	 * 得到用户主船的搬迁CD
	 *
	 * @return int
	 */
	public function getMoveCD()
	{
		return $this->m_move_cd;
	}

	/**
	 *
	 * 得到用户掠夺的cd
	 *
	 * @return int
	 */
	public function getPlunderCd()
	{
		return $this->m_plunder_cd;
	}

	/**
	 *
	 * 得到用户的掠夺次数
	 *
	 * @return int
	 */
	public function getPlunderTime()
	{
		if ( Util::isSameDay($this->m_last_plunder_time) == FALSE )
		{
			$this->m_plunder_time = 0;
		}
		return $this->m_plunder_time;
	}

	/**
	 *
	 * 得到用户的上次掠夺时间
	 *
	 * @return int
	 */
	public function getLastPlunderTime()
	{
		return $this->m_last_plunder_time;
	}

	/**
	 *
	 * 是否可以掠夺
	 *
	 * @return boolean
	 */
	public function canPlunder()
	{
		if ( Util::getTime() < $this->m_plunder_cd )
		{
			return FALSE;
		}

		if ( $this->m_plunder_time >= ExcavateUtil::getMaxPlunderTimePreDay() &&
			Util::isSameDay($this->m_last_plunder_time) )
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 *
	 * 增加掠夺CD时间
	 *
	 * @param int $cd_time
	 *
	 * @return NULL
	 */
	public function addPlunderCd($cd_time)
	{
		$this->m_plunder_cd = Util::getTime() + $cd_time;
		PortBerthDAO::updatePlunder($this->m_uid, $this->m_plunder_cd, NULL, NULL);
	}

	/**
	 *
	 * 金币清除掠夺cd
	 *
	 * @param NULL
	 *
	 * @return array
	 * <code>
	 *	[
	 *		'reset_success':boolean			是否清除成功,如果失败,则gold项不存在
	 *		'gold':int						消耗的金币
	 *	]
	 * </code>
	 */
	public function resetPlunderCdByGold()
	{
		$return = array ( 'reset_success' => FALSE );
		$user = EnUser::getUserObj();
		$cd_time = $this->m_plunder_cd - Util::getTime();
		if ( $cd_time <= 0 )
		{
			return $return;
		}

		$need_gold = ceil($cd_time / ExcavateUtil::getPlunderTimeResetSecond());

		if ( $user->subGold($need_gold) == FALSE )
		{
			Logger::DEBUG('no enough gold!');
			return $return;
		}

		$this->m_plunder_cd = 0;
		PortBerthDAO::updatePlunder($this->m_uid, $this->m_plunder_cd, NULL, NULL);

		$user->update();

		return array(
			'reset_success' => TRUE,
			'gold' => $need_gold,
		);
	}

	/**
	 *
	 * 增加掠夺次数
	 *
	 * @param int $plunder_time
	 *
	 * @return NULL
	 */
	public function addPlunderTime($plunder_time)
	{
		if ( Util::isSameDay($this->m_last_plunder_time) == FALSE )
		{
			$this->m_plunder_time = 0;
		}
		$this->m_last_plunder_time = Util::getTime();
		$this->m_plunder_time += $plunder_time;
		PortBerthDAO::updatePlunder($this->m_uid, NULL, $this->m_plunder_time, $this->m_last_plunder_time);
	}

	/**
	 *
	 * 设置当前主船位置
	 *
	 * @param int $port_id
	 * @param int $time
	 *
	 * @return NULL
	 */
	public function setBerthInfo($port_id, $time = NULL)
	{
		if ( $this->m_port_id == 0 )
		{
			PortBerthDAO::insertBerthID($port_id, $this->m_uid, $time);
		}
		else
		{
			PortBerthDAO::updateBerthID($port_id, $this->m_uid, $time);
		}
		if ( $time !== NULL )
		{
			$this->m_move_cd = $time;
		}
		$this->m_port_id = $port_id;
	}

	private function getBerthInfo()
	{
		$berthInfo = self::getUserBoatBerthInfo($this->m_uid);
		if ( empty($berthInfo) )
		{
			$this->m_port_id = 0;
			$this->m_move_cd = 0;
			$this->m_plunder_cd = 0;
			$this->m_plunder_time = 0;
			$this->m_last_plunder_time = 0;
		}
		else
		{
			$this->m_port_id = intval($berthInfo[PortDef::PORT_SQL_PORT_ID]);
			$this->m_move_cd = intval($berthInfo[PortDef::PORT_SQL_MOVE_CD]);
			$this->m_plunder_cd = intval($berthInfo[PortDef::PORT_SQL_PLUNDER_CD]);
			$this->m_plunder_time = intval($berthInfo[PortDef::PORT_SQL_PLUNDER_TIME]);
			$this->m_last_plunder_time = intval($berthInfo[PortDef::PORT_SQL_LAST_PLUNDER_TIME]);
		}
	}

	/**
	 *
	 * 得到用户的主船所在的港口
	 *
	 * @param int $uid						用户UID
	 *
	 * @return int							港口ID
	 */
	public static function getUserBoatBerth($uid)
	{
		$berthInfo = PortBerthDAO::getBerthID($uid);
		if ( empty($berthInfo) )
		{
			return 0;
		}
		else
		{
			return intval($berthInfo[PortDef::PORT_SQL_PORT_ID]);
		}
	}

	/**
	 *
	 * 得到用户的主船所在的港口信息
	 *
	 * @param int $uid						用户UID
	 *
	 * @return array						港口ID和迁移CD
	 */
	public static function getUserBoatBerthInfo($uid)
	{
		$berthInfo = PortBerthDAO::getBerthID($uid);
		return $berthInfo;
	}

	/**
	 *
	 * 得到港口中主船的总数
	 *
	 * @param int $port_id					港口ID
	 *
	 * @return int
	 */
	public static function getBoatCount($port_id)
	{
		return PortBerthDAO::getBerthBoatCount($port_id);
	}

	/**
	 *
	 * 得到港口中在当前用户前的主船的总数
	 *
	 * @param int $port_id
	 * @param int $uid
	 *
	 * @return int
	 */
	public static function getBoatCountBefore($port_id, $uid)
	{
		return PortBerthDAO::getBerthBoatCountBefore($port_id, $uid);
	}

	/**
	 *
	 * 得到所在港口页数ID
	 *
	 * @param int $port_id
	 * @param int $uid
	 *
	 * @return int
	 */
	public static function getPageId($port_id, $uid)
	{
		return ceil(PortBerth::getBoatCountBefore($port_id, $uid) / PortConfig::PORT_BERTH_NUM_PER_PAGE);
	}

	/**
	 *
	 * 得到港口船舶停放信息
	 *
	 * @param int $port_id
	 * @param int $page_id
	 *
	 * @return array
	 * <code>
	 * [
	 * 		place_id:array
	 * 		{
	 * 			uid:int
	 * 			uname:int
	 * 			level:int
	 * 			group_id:int
	 * 			guild_id:int
	 * 			protect_cdtime:int
	 * 			atk_value:int
	 * 		}
	 * ]
	 * </code>
	 */
	public static function getBoatBerthList($port_id, $page_id)
	{
		$berthInfos = PortBerthDAO::getBerthInfos($port_id, $page_id);
		$return = array();
		$arrField = array(
			'uid',
			'uname',
			'level',
			'group_id',
			'guild_id',
			'protect_cdtime',
			'atk_value',
		);

		$uids = Util::arrayExtract($berthInfos, PortDef::PORT_SQL_UID);

		$boatTypes = EnSailBoat::getMultiUserBoatType($uids);

		$userInfos = Util::getArrUser($uids, $arrField);

		$guildIds = Util::arrayExtract($userInfos, 'guild_id');

		$guildEmblems = GuildLogic::getMultiEmblemByIds($guildIds);

		$place_id = 0;
		foreach ( $berthInfos  as $berthInfo )
		{
			$place_id ++;
			$uid = $berthInfo[PortDef::PORT_SQL_UID];
			if ( !isset($userInfos[$uid]) )
			{
				Logger::FATAL('user:%d in port:%d, but not exist!', $uid, $port_id);
				throw new Exception('fake');
			}
			$return[$place_id] = $userInfos[$uid];
			//公会会徽
			if ( empty($userInfos[$uid]['guild_id']) )
			{
				$return[$place_id]['guild_emblem'] = 0;
			}
			else
			{
				$return[$place_id]['guild_emblem'] = $guildEmblems[$userInfos[$uid]['guild_id']];
			}

			//得到港口中主船的类型
			if ( isset($boatTypes[$uid]) && isset($boatTypes[$uid]['boat_type']) )
			{
				$return[$place_id]['boat_type'] = $boatTypes[$uid]['boat_type'];
			}
			else
			{
				$return[$place_id]['boat_type'] = SailboatConf::REFIT_ID_01;
			}


			$return[$place_id]['place_id'] = $place_id;
		}
		return $return;
	}

	/**
	 *
	 * 标记用户删除
	 *
	 * @param int $uid
	 */
	public static function deleteUserBerth($uid)
	{
		PortBerthDAO::setDelete($uid);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */