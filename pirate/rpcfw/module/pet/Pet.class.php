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
 * Class       : Pet
 * Description : 宠物对外接口实现类
 * Inherit     : IPet
 **********************************************************************************************************************/
class Pet implements IPet
{
	/* (non-PHPdoc)
	 * @see IPet::getUserPetInfo()
	 */
	public function getUserPetInfo() 
	{
		Logger::debug('Pet::getUserPetInfo Start.');
		// 获取用户宠物信息
		$ret = PetLogic::getUserPetInfo();
		Logger::debug('Pet::getUserPetInfo End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::reborn()
	 */
	public function reborn($petID) 
	{
		Logger::debug('Pet::reborn Start.');
		// 宠物重生了,你说怎么会有这种劳民伤财的功能呢？
		$ret = PetLogic::reborn($petID);
		Logger::debug('Pet::reborn End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::resetByEgg()
	 */
	public function resetByEgg($petID) 
	{
		Logger::debug('Pet::resetByEgg Start.');
		// 蛋重置
		$ret = PetLogic::reset($petID, 'egg');
		Logger::debug('Pet::resetByEgg End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::resetByGold()
	 */
	public function resetByGold($petID) 
	{
		Logger::debug('Pet::resetByGold Start.');
		// 钱重置
		$ret = PetLogic::reset($petID, 'gold');
		Logger::debug('Pet::resetByGold End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::sell()
	 */
	public function sell($petID) 
	{
		Logger::debug('Pet::sell Start.');
		// 出售宠物
		$ret = PetLogic::sell($petID);
		Logger::debug('Pet::sell End.');
		
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::equip()
	 */
	public function equip($petID) 
	{
		Logger::debug('Pet::equip Start.');
		// 以防万一
		$petID = intval($petID);
		// 装备宠物
		$ret = PetLogic::equip($petID);
		Logger::debug('Pet::equip End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::unequip()
	 */
	public function unequip() 
	{
		Logger::debug('Pet::unequip Start.');
		// 卸下宠物
		PetLogic::unequip();
		Logger::debug('Pet::unequip End.');

		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IPet::openSlot()
	 */
	public function openSlot() 
	{
		Logger::debug('Pet::openSlot Start.');
		// 开启新的携带栏位
		$ret = PetLogic::openSlot();
		Logger::debug('Pet::openSlot End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::understand()
	 */
	public function understand($petID) 
	{
		Logger::debug('Pet::understand Start.');
		// 恩，开始领悟
		$ret = PetLogic::understand($petID);
		Logger::debug('Pet::understand End.');
		// 别问我，我也不知道悟出来啥了……
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::rapid()
	 */
	public function rapid($petID) 
	{
		Logger::debug('Pet::rapid Start.');
		// 突飞！
		$ret = PetLogic::rapid($petID);
		Logger::debug('Pet::rapid End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::rapidByGold()
	 */
	public function rapidByGold($petID) 
	{
		Logger::debug('Pet::rapidByGold Start.');
		// 金币突飞！真吓人啊！
		$ret = PetLogic::rapidByGold($petID);
		Logger::debug('Pet::rapidByGold End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::train()
	 */
	public function train($petID) 
	{
		Logger::debug('Pet::train Start.');
		// 开始训练
		$ret = PetLogic::train($petID);
		Logger::debug('Pet::train End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::stopTrain()
	 */
	public function stopTrain($petID) 
	{
		Logger::debug('Pet::stopTrain Start.');
		// 开始训练
		$ret = PetLogic::stopTrain($petID);
		Logger::debug('Pet::stopTrain End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::lockSkill()
	 */
	public function lockSkill($petID, $skillID) 
	{
		Logger::debug('Pet::lockSkill Start.');
		// 锁定技能
		$ret = PetLogic::lockSkill($petID, $skillID);
		Logger::debug('Pet::lockSkill End.');
		
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::unLockSkill()
	 */
	public function unLockSkill($petID, $skillID) 
	{
		Logger::debug('Pet::unLockSkill Start.');
		// 解锁技能
		$ret = PetLogic::unLockSkill($petID, $skillID);
		Logger::debug('Pet::unLockSkill End.');
		
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::getCDTime()
	 */
	public function getCDTime() 
	{
		Logger::debug('Pet::getCDTime Start.');
		// 获取CD截止时刻
		$cdEndTime = PetLogic::getCdEndTime();
		Logger::debug('Pet::getCDTime End.');

		return $cdEndTime;
	}

	/* (non-PHPdoc)
	 * @see IPet::clearCDByGold()
	 */
	public function clearCDByGold() 
	{
		Logger::debug('Pet::clearCDByGold Start.');
		// 使用RMB清除CD时刻
		$ret = PetLogic::clearCDByGold();
		Logger::debug('Pet::clearCDByGold End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::getAttr()
	 */
	public function getAttr($petID, $attrID)
	{
		Logger::debug('Pet::getAttr Start.');
		// 获取某项技能加成
		$ret = PetLogic::getAttr($petID, $attrID);
		Logger::debug('Pet::getAttr End.');

		return $ret;
	}


	/* (non-PHPdoc)
	 * @see IPet::getAllAttr()
	 */
	public function getAllAttr($petID)
	{
		Logger::debug('Pet::getAllAttr Start.');
		// 获取所有技能加成
		$ret = PetLogic::getAllAttr($petID);
		Logger::debug('Pet::getAllAttr End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::refreshQualifications()
	 */
	public function refreshQualifications($petID, $itemTID) 
	{
		Logger::debug('Pet::refreshQualifications Start.');
		// 洗练资质
		$ret = PetLogic::refreshQualifications($petID, $itemTID);
		Logger::debug('Pet::refreshQualifications End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::evolution()
	 */
	public function evolution($petID) 
	{
		Logger::debug('Pet::evolution Start.');
		// 进化
		$ret = PetLogic::evolution($petID);
		Logger::debug('Pet::evolution End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::commitRefresh()
	 */
	public function commitRefresh($petID) 
	{
		Logger::debug('Pet::commitRefresh Start.');
		// 提交洗练结果
		$ret = PetLogic::commitRefresh($petID);
		Logger::debug('Pet::commitRefresh End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::rollbackRefresh()
	 */
	public function rollbackRefresh($petID) 
	{
		Logger::debug('Pet::rollbackRefresh Start.');
		// 回滚洗练结果
		$ret = PetLogic::rollbackRefresh($petID);
		Logger::debug('Pet::rollbackRefresh End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::feedingOnce()
	 */
	public function feedingOnce($petID, $itemTID) 
	{
		Logger::debug('Pet::feedingOnce Start.');
		// 喂养一条鱼
		$ret = PetLogic::feedingOnce($petID, $itemTID);
		Logger::debug('Pet::feedingOnce End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::feedingAll()
	 */
	public function feedingAll($petID) 
	{
		Logger::debug('Pet::feedingAll Start.');
		// 一键喂养
		$ret = PetLogic::feedingAll($petID);
		Logger::debug('Pet::feedingAll End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::openWarehouseSlot()
	 */
	public function openWarehouseSlot() 
	{
		Logger::debug('Pet::openWarehouseSlot Start.');
		// 开启仓库格子
		$ret = PetLogic::openWarehouseSlot();
		Logger::debug('Pet::openWarehouseSlot End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::putInWarehouse()
	 */
	public function putInWarehouse($petID) 
	{
		Logger::debug('Pet::putInWarehouse Start.');
		// 将宠物放到仓库里
		$ret = PetLogic::putInWarehouse($petID);
		Logger::debug('Pet::putInWarehouse End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::getOutWarehouse()
	 */
	public function getOutWarehouse($petID) 
	{
		Logger::debug('Pet::getOutWarehouse Start.');
		// 将宠物从仓库里面拿出
		$ret = PetLogic::getOutWarehouse($petID);
		Logger::debug('Pet::getOutWarehouse End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPet::transfer()
	 */
	public function transfer($curPet, $objPet, $type) 
	{
		Logger::debug('Pet::transfer Start.');
		// 资质传承
		$ret = PetLogic::transfer($curPet, $objPet, $type);
		Logger::debug('Pet::transfer End.');

		return $ret;
	}

	public function advanceTransfer($curPet, $objPet) 
	{
		Logger::debug('Pet::advanceTransfer Start.');
		// 资质传承
		$ret = PetLogic::advanceTransfer($curPet, $objPet);
		Logger::debug('Pet::advanceTransfer End.');

		return $ret;
	}
	
	public function degenerateToEgg($petID)
	{
		return PetLogic::degenerateToEgg($petID);
	}

	public function addPetSkill($petID, $item_template_id, $num)
	{
		return PetLogic::addPetSkill($petID, $item_template_id, $num);
	}
	
	public function getUserPetCollectionInfo()
	{
		// return array('ids' => array(1,4,12,3,5,7,6), 'prized');
	}
	
	public function getPrize($prize_id)
	{
		// logger::warning($prize_id);
		// $ret['ids'] = array(1,4,12,3,5,7,6);
		// $ret['bag'] = array();
		// return $ret;
	}
	
	public function upTalentSkill($petID)
	{
		return PetLogic::upTalentSkill($petID);
		// return array('ret'=>'err');
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */