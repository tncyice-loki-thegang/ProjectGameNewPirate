<?php

class CoPet implements ICoPet
{
	/* (non-PHPdoc)
	 * @see ICoPet::getUserPetInfo()
	 */
	public function getUserPetInfo() 
	{
		Logger::debug('CoPet::getUserPetInfo Start.');
		// 获取用户宠物信息
		$ret = CoPetLogic::getUserPetInfo();
		Logger::debug('CoPet::getUserPetInfo End.');
		return $ret;
	}
	
	function born($petID_left, $petID_right)
	{
		$ret = CoPetLogic::born($petID_left, $petID_right);
		return $ret;
	}

	function bornTwins($petID_left, $petID_right)
	{
		$ret = CoPetLogic::bornTwins($petID_left, $petID_right);
		return $ret;
	}
	
	function swallow($petID, $petID_swallowed)
	{
		MyCoPet::getInstance()->swallow($petID, $petID_swallowed);
		MyCoPet::getInstance()->save();
	}

	function swallowAll($petID)
	{
		MyCoPet::getInstance()->swallowAll($petID);
		MyCoPet::getInstance()->save();
		return 'ok';
	}
	
	function protect($petID)
	{
		MyCoPet::getInstance()->protect($petID);
		MyCoPet::getInstance()->save();
	}
	
	function unprotect($petID)
	{
		MyCoPet::getInstance()->unprotect($petID);
		MyCoPet::getInstance()->save();
	}
	
	/* (non-PHPdoc)
	 * @see ICoPet::reset()
	 */
	public function reset($petID) 
	{
		Logger::debug('CoPet::reset Start.');
		// 钱重置
		$ret = CoPetLogic::reset($petID);
		Logger::debug('CoPet::reset End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::sell()
	 */
	public function sell($petID) 
	{
		Logger::debug('CoPet::sell Start.');
		// 出售宠物
		$ret = CoPetLogic::sell($petID);
		Logger::debug('CoPet::sell End.');
		
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::equip()
	 */
	public function equip($petID) 
	{
		Logger::debug('CoPet::equip Start.');
		// 以防万一
		$petID = intval($petID);
		// 装备宠物
		$ret = CoPetLogic::equip($petID);
		Logger::debug('CoPet::equip End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::unequip()
	 */
	public function unequip() 
	{
		Logger::debug('CoPet::unequip Start.');
		// 卸下宠物
		CoPetLogic::unequip();
		Logger::debug('CoPet::unequip End.');

		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see ICoPet::openSlot()
	 */
	public function openSlot() 
	{
		Logger::debug('CoPet::openSlot Start.');
		// 开启新的携带栏位
		$ret = CoPetLogic::openSlot();
		Logger::debug('CoPet::openSlot End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::understand()
	 */
	public function understand($petID) 
	{
		Logger::debug('CoPet::understand Start.');
		// 恩，开始领悟
		$ret = CoPetLogic::understand($petID);
		Logger::debug('CoPet::understand End.');
		// 别问我，我也不知道悟出来啥了……
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::lockSkill()
	 */
	public function lockSkill($petID, $skillID) 
	{
		Logger::debug('CoPet::lockSkill Start.');
		// 锁定技能
		$ret = CoPetLogic::lockSkill($petID, $skillID);
		Logger::debug('CoPet::lockSkill End.');
		
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::unLockSkill()
	 */
	public function unLockSkill($petID, $skillID) 
	{
		Logger::debug('CoPet::unLockSkill Start.');
		// 解锁技能
		$ret = CoPetLogic::unLockSkill($petID, $skillID);
		Logger::debug('CoPet::unLockSkill End.');
		
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::clearCDByGold()
	 */
	public function clearCDByGold() 
	{
		Logger::debug('CoPet::clearCDByGold Start.');
		// 使用RMB清除CD时刻
		$ret = CoPetLogic::clearCDByGold();
		Logger::debug('CoPet::clearCDByGold End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::getAttr()
	 */
	public function getAttr($petID, $attrID)
	{
		Logger::debug('CoPet::getAttr Start.');
		// 获取某项技能加成
		$ret = CoPetLogic::getAttr($petID, $attrID);
		Logger::debug('CoPet::getAttr End.');

		return $ret;
	}


	/* (non-PHPdoc)
	 * @see ICoPet::getAllAttr()
	 */
	public function getAllAttr($petID)
	{
		Logger::debug('CoPet::getAllAttr Start.');
		// 获取所有技能加成
		$ret = CoPetLogic::getAllAttr($petID);
		Logger::debug('CoPet::getAllAttr End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::refreshQualifications()
	 */
	public function refreshQualifications($petID, $itemTID) 
	{
		Logger::debug('CoPet::refreshQualifications Start.');
		// 洗练资质
		$ret = CoPetLogic::refreshQualifications($petID, $itemTID);
		Logger::debug('CoPet::refreshQualifications End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::commitRefresh()
	 */
	public function commitRefresh($petID) 
	{
		Logger::debug('CoPet::commitRefresh Start.');
		// 提交洗练结果
		$ret = CoPetLogic::commitRefresh($petID);
		Logger::debug('CoPet::commitRefresh End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::rollbackRefresh()
	 */
	public function rollbackRefresh($petID) 
	{
		Logger::debug('CoPet::rollbackRefresh Start.');
		// 回滚洗练结果
		$ret = CoPetLogic::rollbackRefresh($petID);
		Logger::debug('CoPet::rollbackRefresh End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICoPet::transfer()
	 */
	public function transfer($curPet, $objPet, $type) 
	{
		Logger::debug('CoPet::transfer Start.');
		// 资质传承
		$ret = CoPetLogic::transfer($curPet, $objPet, $type);
		Logger::debug('CoPet::transfer End.');

		return $ret;
	}
	
	public function advanceTransfer($curPet, $objPet) 
	{
		Logger::debug('CoPet::advanceTransfer Start.');
		// 资质传承
		$ret = CoPetLogic::advanceTransfer($curPet, $objPet);
		Logger::debug('CoPet::advanceTransfer End.');

		return $ret;
	}	
	
	public function changeToEgg($petID)
	{		
		return CoPetLogic::changeToEgg($petID);
	}

	public function addPetSkill($petID, $item_template_id, $num)
	{
		return CoPetLogic::addPetSkill($petID, $item_template_id, $num);
	}


	public function getUserPetCollectionInfo()
	{
		// return array('ids' => array(1,4,12,3,5,7,6,2,8,9,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,46), 'prized' => array(1=>1201,2=>true));
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
		return CoPetLogic::upTalentSkill($petID);
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */