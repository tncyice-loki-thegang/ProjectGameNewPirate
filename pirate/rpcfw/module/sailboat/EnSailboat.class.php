<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnSailboat 
 * Description : 主船内部接口类
 * Inherit     : 
 **********************************************************************************************************************/
class EnSailboat
{

	/**
	 * 获取用户船信息
	 * 
	 * @param int $userID						用户ID
	 */
	static public function getUserBoat($userID)
	{
		// 如果是获取当前用户的主船信息, 那么可以直接从session里面获取
		if ($userID == RPCContext::getInstance()->getUid())
		{
			// 如果还没开启，那么直接返回空数据
			if (!EnSwitch::isOpen(SwitchDef::BOAT))
			{
				$boatInfo = array();
			}
			// 否则才需要去session里面获取信息
			else 
			{
				$boatInfo = SailboatInfo::getInstance()->getBoatInfo();
			}
		}
		// 否则，才需要从数据库中拉
		else 
		{
			$boatInfo = SailboatDao::getBoatInfoByUid($userID, SailboatConf::$SEL_ALL);
		}
		return $boatInfo;
	}

	/**
	 * 查看舱室是否开启
	 * 
	 * @param int $cabinID						舱室ID
	 */
	static public function isCabinOpen($cabinID)
	{
		// 获取舱室信息
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
		// 返回是否开启
		return isset($cabinInfo[$cabinID]);
	}

	/**
	 * 获取多人的主船类型
	 * 
	 * @param array $userList					uid数组
	 */
	static public function getMultiUserBoatType($userList)
	{
		return SailboatDao::getMultiUserBoatType($userList);
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */