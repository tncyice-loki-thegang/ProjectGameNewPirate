<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $$Id: Sailboat.class.php 14993 2012-02-27 11:05:46Z YangLiu $$
 *
 **********************************************************************************************************************/

 /**
 * @file $$HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sailboat/Sailboat.class.php $$
 * @author $$Author: YangLiu $$(liuyang@babeltime.com)
 * @date $$Date: 2012-02-27 19:05:46 +0800 (一, 2012-02-27) $$
 * @version $$Revision: 14993 $$
 * @brief
 *
 **/

/**********************************************************************************************************************
 * Class       : Sailboat
 * Description : 主船对外接口实现类
 * Inherit     : ISailboat
 **********************************************************************************************************************/
class Sailboat implements ISailboat
{

	/* (non-PHPdoc)
	 * @see ISailboat::getBoatInfo()
	 */
	function getBoatInfo()
	{
		Logger::debug('Sailboat::getBoatInfo Start.');
		// 通过主船ID获取主船信息
		$boatInfo = SailboatLogic::getBoatInfo();

		Logger::debug('Sailboat::getBoatInfo End.');
		// 返回取得的内容
		return $boatInfo;
	}

	/* (non-PHPdoc)
	 * @see ISailboat::getBoatInfoByID()
	 */
	function getBoatInfoByID($uid)
	{
		Logger::debug('Sailboat::getBoatInfoByID Start.');
		// 通过主船ID获取主船信息
		$boatInfo = EnSailboat::getUserBoat($uid);
		// 获取道具信息
		$itemInfo = SailboatLogic::getAllItemInfo($boatInfo);

		Logger::debug('Sailboat::getBoatInfoByID End.');
		// 返回取得的内容
		return array('boat' => $boatInfo, 'item' => $itemInfo);
	}

	/* (non-PHPdoc)
	 * @see ISailboat::upgradeCabinLv()
	 */
	function upgradeCabinLv($roomID)
	{
		Logger::debug('Sailboat::upgradeCabinLv Start.');
		// 以防万一，转换一下
		$roomID = intval($roomID);
		Logger::debug('The Cabin id is : %d.', $roomID);
		$ret = SailboatLogic::upgradeCabinLv($roomID);

		Logger::debug('Sailboat::upgradeCabinLv End.');
		// 返回升级结果
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISailboat::getBuildListStatus()
	 */
	function getBuildListStatus()
	{
		Logger::debug('Sailboat::getBuildListStatus Start.');
		// 获取建筑队列信息
		$buildlist = SailboatInfo::getInstance()->getBuildListInfo();

		Logger::debug('Sailboat::getBuildListStatus End.');
		// 返回取得的内容
		return $buildlist;
	}

	/* (non-PHPdoc)
	 * @see ISailboat::clearCDByGold()
	 */
	function clearCDByGold($listID)
	{
		Logger::debug('Sailboat::clearCDByGold Start.');
		// 清除 建筑队列CD
		$ret = SailboatLogic::clearCDByGold($listID);

		Logger::debug('Sailboat::clearCDByGold End.');
		// 返回添加结果
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISailboat::addNewBuildList()
	 */
	function addNewBuildList()
	{
		Logger::debug('Sailboat::addNewBuildList Start.');
		// 添加一个新的 建筑队列
		$ret = SailboatLogic::addNewBuildList();

		Logger::debug('Sailboat::addNewBuildList End.');
		// 返回添加结果
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISailboat::refittingSailboat()
	 */
	function refittingSailboat($refitID)
	{
		Logger::debug('Sailboat::refittingSailboat Start.');
		// 以防万一，转换一下
		$refitID = intval($refitID);
		$ret = SailboatLogic::refittingSailboat($refitID);

		Logger::debug('Sailboat::refittingSailboat End.');
		// 返回添加结果
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISailboat::openRefitAbility()
	 */
	function openRefitAbility($refitID)
	{
		Logger::debug('Sailboat::openRefitAbility Start.');
		// 以防万一，转换一下
		$refitID = intval($refitID);
		$ret = SailboatLogic::openRefitAbility($refitID);

		Logger::debug('Sailboat::openRefitAbility End.');
		// 返回添加结果
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISailboat::equipItem()
	 */
	function equipItem($oldItem, $placeID)
	{
		Logger::debug('Sailboat::equipItem Start.');
		// 以防万一，转换一下
		$placeID = intval($placeID);
		$ret = SailboatLogic::equipItem($oldItem, $placeID);
		Logger::debug('Sailboat::equipItem End.');
		// 返回正常结束
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISailboat::removeItem()
	 */
	public function removeItem($placeID)
	{
		Logger::debug('Sailboat::removeItem Start.');
		// 以防万一，转换一下
		$placeID = intval($placeID);
		$ret = SailboatLogic::removeItem($placeID);
		Logger::debug('Sailboat::removeItem End.');
		// 返回正常结束
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISailboat::equipSkill()
	 */
	function equipSkill($skillIDs)
	{
		Logger::debug('Sailboat::equipSkill Start.');
		// 装备技能
		$ret = SailboatLogic::equipSkill($skillIDs);

		Logger::debug('Sailboat::equipSkill End.');
		// 返回正常结束
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISailboat::openNewCabin()
	 */
	function openNewCabin($cabinID)
	{
		Logger::debug('Sailboat::openNewCabin Start.');
		// 开启新舱室
		$ret = SailboatLogic::openNewCabin($cabinID);

		Logger::debug('Sailboat::openNewCabin End.');
		// 返回正常结束
		return $ret;
	}

	/**
	 * 根据舱室ID，获取舱室等级
	 * @param $cabinID	 						舱室ID
	 */
	public static function getCabinLv($cabinID)
	{
		Logger::debug('Sailboat::getCabinLv Start.');
		// 以防万一，转换一下
		$cabinID = intval($cabinID);
		// 获取主船信息
		$boatInfo = SailboatLogic::getBoatInfo();

		Logger::debug('Sailboat::getCabinLv End.');
		return $boatInfo['va_boat_info']['cabin_id_lv'][$cabinID]['level'];
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */