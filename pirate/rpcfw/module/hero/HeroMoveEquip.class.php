<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroMoveEquip.class.php 39930 2013-03-05 07:05:48Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/HeroMoveEquip.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-05 15:05:48 +0800 (二, 2013-03-05) $
 * @version $Revision: 39930 $
 * @brief 
 *  
 **/

class HeroMoveEquip
{	
	/**
	 * @var HeroObj
	 */
	protected $srcHero = 0;
	
	/**
	 * @var HeroObj
	 */
	protected $desHero = 0;
	
	public function __construct($srcHid, $desHid)
	{
		$user = EnUser::getUserObj();
		$this->srcHero = $user->getHeroObj(intval($srcHid));
		$this->desHero = $user->getHeroObj(intval($desHid));
	}
	
	public function moveAllEquip($type)
	{
		$arrPosition = HeroUtil::getEquipDef($type);
		$func = HeroUtil::getSetEquipFunc($type);
		foreach ( $arrPosition as $position => $value)
		{
		
			$srcItemId = $this->srcHero->getEquipByPosition($type, $position);
			$desItemId = $this->desHero->getEquipByPosition($type, $position);
		
			if (!call_user_func_array(array($this->desHero, $func), array($position, $srcItemId)))
			{
				$this->srcHero->rollback();
				$this->desHero->rollback();
				Logger::warning('move all %s err, fail to set item:%d to hid:%d', $type, $srcItemId, $this->desHero->getHid());
				throw new Exception('fake');
			}
			else
			{
				if (!call_user_func_array(array($this->srcHero, $func), array($position, $desItemId)))
				{
					$this->srcHero->rollback();
					$this->desHero->rollback();
					Logger::warning('move all %s err, fail to set item:%d to hid:%d', $type, $desItemId, $this->srcHero->getHid());
					throw new Exception('fake');
				}
			}
		}
		return true;
	}
	
	public function moveEquip($type, $position)
	{
		$srcItemId = $this->srcHero->getEquipByPosition($type, $position);
		$desItemId = $this->desHero->getEquipByPosition($type, $position);
		if ( $srcItemId == BagDef::ITEM_ID_NO_ITEM )
		{
			Logger::DEBUG('srcItemId is null!');
			return false;
		}
		
		$setFunc = HeroUtil::getSetEquipFunc($type);
		if (!call_user_func_array(array($this->desHero, $setFunc), array($position, $srcItemId)))
		{
			Logger::DEBUG('item:%d can not equip in hero:%d', $srcItemId, $this->desHero->getHid());
			return false;
		}
		
		if (!call_user_func_array(array($this->srcHero, $setFunc), array($position, $desItemId)))
		{
			$this->desHero->rollback();
			Logger::DEBUG('item:%d can not equip in hero:%d', $srcItemId, $this->desHero->getHid());
			return false;
		}
		return true;
	}
	
	public function update()
	{
		$srcModify = $this->srcHero->getModifyAttr();
		$desModify = $this->desHero->getModifyAttr();
		
		if (empty($srcModify) || empty($desModify))
		{
			return;
		}
		
		//这里不调用userobj::update了
		$arrUpdate = array($this->srcHero->getHid()=>$srcModify, $this->desHero->getHid()=>$desModify);
		HeroDao::batchUpdate($arrUpdate);
		
		//影响战斗信息
		if (EnFormation::isInCurFormation($this->srcHero->getHid())
				|| EnFormation::isInCurFormation($this->desHero->getHid()))
		{
			EnUser::modifyBattleInfo();
		}
		
		//修改缓存信息
		$userObj = EnUser::getUserObj();
		$heroMgr = $userObj->getHeroManager();
		foreach (array($this->srcHero, $this->desHero) as $heroObj)
		{
			$heroObj->setAttrNoModify();
			$heroMgr->saveRctHero($heroObj->getAllAttr());
		}
		RPCContext::getInstance()->setSession('hero.arrHeroAttr', $heroMgr->getArrRctAttr());
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */