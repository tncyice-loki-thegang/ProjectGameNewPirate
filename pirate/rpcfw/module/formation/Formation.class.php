<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Formation.class.php 19115 2012-04-23 08:10:38Z YangLiu $
 * 
 **********************************************************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/formation/Formation.class.php $
 * @author $Author: YangLiu $(lanhongyu@babeltime.com)
 * @date $Date: 2012-04-23 16:10:38 +0800 (一, 2012-04-23) $
 * @version $Revision: 19115 $
 * @brief 
 * 
 **/

/**********************************************************************************************************************
 * Class       : Formation
 * Description : 阵型接口实现类
 * Implements  : IFormation
 **********************************************************************************************************************/
class Formation implements IFormation
{

	/* (non-PHPdoc)
	 * @see IFormation::getAllFormation()
	 */
	public function getAllFormation()
	{
		Logger::debug('Formation::getAllFormation Start.');
		// 获取阵型信息, 返回查询结果
		$arrFor = FormationLogic::getAllFormation();
		$ret = array('bench_id' => $arrFor, 'info' => $arrFor, 'attr' => array('now' => array(), 'add' => array()));

		Logger::debug('Formation::getAllFormation End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IFormation::getFormationInfoByID()
	 */
	public function getFormationInfoByID($fid)
	{
		Logger::debug('Formation::getFormationInfoByID Start.');

		$fid = intval($fid);
		// 获取阵型信息, 返回查询结果
		$ret = FormationLogic::getFormationByID($fid);

		Logger::debug('Formation::getFormationInfoByID End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IFormation::setCurFormation()
	 */
	public function setCurFormation($fid, $formation)
	{
		Logger::debug('Formation::setCurFormation Start.');

		$fid = intval($fid);
		// 将此阵型设置到数据库
		$ret = FormationLogic::setCurFormation($fid, $formation);

		Logger::debug('Formation::setCurFormation End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IFormation::plusFormationLv()
	 */
	public function plusFormationLv($fid)
	{
		Logger::debug('Formation::plusFormationLv Start.');
		$fid = intval($fid);
		// 提升阵型等级
		$ret = FormationLogic::plusFormationLv($fid);

		// 通知成就系统
		EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::FORMATION_LEVEL, $ret);

		Logger::debug('Formation::plusFormationLv End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IFormation::changeCurFormation()
	 */
	public function changeCurFormation($fid, $formation)
	{
		Logger::debug('Formation::changeCurFormation Start.');
		$fid = intval($fid);
		// 更新到数据库
		FormationLogic::changeCurFormation($fid, $formation, false);

		Logger::debug('Formation::changeCurFormation End.');
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IFormation::getFormationAttr()
	 */
	public function getFormationAttr($fid) 
	{
		Logger::debug('Formation::getFormationAttr Start.');
		$fid = intval($fid);
		// 获取阵型属性
		$ret = FormationLogic::getFormationAttr($fid);

		Logger::debug('Formation::getFormationAttr End.');
	}
	
	public function evolution($fid)
	{
		return FormationLogic::evolution($fid);
	}
	
	public function refreshAttr($fid)
	{
		$ret = array('attr' => array('add'=>array(601,701,801)), 'bag');
		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */