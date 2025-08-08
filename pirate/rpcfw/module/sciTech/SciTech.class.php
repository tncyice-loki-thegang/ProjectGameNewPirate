<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SciTech.class.php 21457 2012-05-28 04:00:25Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sciTech/SciTech.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-05-28 12:00:25 +0800 (一, 2012-05-28) $
 * @version $Revision: 21457 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : SciTech
 * Description : 科技对外接口实现类
 * Inherit     : ISciTech
 **********************************************************************************************************************/
class SciTech implements ISciTech
{
	/* (non-PHPdoc)
	 * @see ISciTech::getCdEndTime()
	 */
	public function getCdEndTime()
	{
		Logger::debug('SciTech::getCdEndTime Start.');
		// 获取最新的CD时刻
		$ret = SciTechLogic::getCdEndTime();
		Logger::debug('SciTech::getCdEndTime End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISciTech::getAllSciTechLv()
	 */
	public function getAllSciTechLv() 
	{
		Logger::debug('SciTech::getAllSciTechLv Start.');
		// 获取所有科技等级
		$ret = SciTechLogic::getAllSciTechLv();
		Logger::debug('SciTech::getAllSciTechLv End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISciTech::getSciTechAttr()
	 */
	public function getSciTechAttr($attrID) 
	{
		Logger::debug('SciTech::getSciTechAttr Start.');
		// 获取此属性的所有加成
		$ret = SciTechLogic::getSciTechAttr($attrID); 
		Logger::debug('SciTech::getSciTechAttr End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISciTech::getAllSciTechAttr()
	 */
	public function getAllSciTechAttr() 
	{
		Logger::debug('SciTech::getAllSciTechAttr Start.');
		// 获取所有属性的所有加成
		$ret = SciTechLogic::getAllSciTechAttr(); 
		Logger::debug('SciTech::getAllSciTechAttr End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISciTech::plusSciTechLv()
	 */
	public function plusSciTechLv($stID) 
	{
		Logger::debug('SciTech::plusSciTechLv Start.');
		// 以防万一，转换下
		$stID = intval($stID);
		// 提升科技等级
		$ret = SciTechLogic::plusSciTechLv($stID); 
		Logger::debug('SciTech::plusSciTechLv End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ISciTech::clearCdTimeByGold()
	 */
	public function clearCdTimeByGold() 
	{
		Logger::debug('SciTech::clearCdTimeByGold Start.');
		// 扣钱，清空CD
		$ret = SciTechLogic::clearCdTimeByGold(); 

		Logger::debug('SciTech::clearCdTimeByGold End.');
		// 将结果返回
		return $ret;
	}

	/**
	 * 通过用户ID获取科技信息
	 * @param int $uid							用户ID
	 */
	public function getAllSciTechLvByUid($uid) 
	{
		Logger::debug('SciTech::getAllSciTechLvByUid Start.');
		// 以防万一一下
		$uid = intval($uid);
		// 获取科技信息
		$ret = SciTechLogic::getAllSciTechLvByUid($uid);

		Logger::debug('SciTech::getAllSciTechLvByUid End.');
		// 返回查询结果
		return $ret;
	}

	/**
	 * 获取某用户的该属性的技能加成
	 * @param int $uid							用户ID
	 * @param int $attrID						科技增长属性ID
	 */
	public function getSciTechAttrByUid($uid, $attrID) 
	{
		Logger::debug('SciTech::getSciTechAttrByUid Start.');
		// 以防万一一下
		$uid = intval($uid);
		$attrID = intval($attrID);
		// 返回加成结果
		$ret = SciTechLogic::getSciTechAttrByUid($uid, $attrID);

		Logger::debug('SciTech::getSciTechAttrByUid End.');
		// 返回加成结果
		return $ret;
	}

	/**
	 * 获取某用户的所有属性的技能加成
	 * @param int $uid							用户ID
	 */
	public function getAllSciTechAttrByUid($uid) 
	{
		Logger::debug('SciTech::getAllSciTechAttrByUid Start.');
		// 以防万一一下
		$uid = intval($uid);
		// 返回加成结果
		$ret = SciTechLogic::getAllSciTechAttrByUid($uid);

		Logger::debug('SciTech::getAllSciTechAttrByUid End.');
		// 返回加成结果
		return $ret;
	}

	/**
	 * 主船等级提升后， 开启新科技
	 * @param int $cabinLv						科技室等级
	 */
	public function openNewSciTech($cabinLv)
	{
		Logger::debug('SciTech::openNewSciTech Start.');
		// 开启新科技
		SciTechLogic::openNewSciTech($cabinLv);
		Logger::debug('SciTech::openNewSciTech End.');
	}

	/**
	 * (non-PHPdoc)
	 * @see ISciTech::openCreditMode()
	 */
	public function openCreditMode()
	{
		Logger::debug('SciTech::openCreditMode Start.');
		// 开启新科技
		$ret = SciTechLogic::openCreditMode();
		Logger::debug('SciTech::openCreditMode End.');
		
		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */