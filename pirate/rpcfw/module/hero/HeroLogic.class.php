<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroLogic.class.php 38814 2013-02-20 10:15:08Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/HeroLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-20 18:15:08 +0800 (三, 2013-02-20) $
 * @version $Revision: 38814 $
 * @brief
 *
 **/

class HeroLogic
{
	public static function getHeroes ($uid)
	{
		$arrRet = HeroDao::getHeroesByUid($uid, HeroDef::$HERO_FIELDS);
		//$heroes = Util::arrayIndex($arrRet, 'htid');
		//Logger::debug("get heros by %d(uid):%s", $uid, $heroes);
		return $arrRet;
	}
	
	public static function getHeroByUidHtid($uid, $htid, $arrField=null)
	{
		if ($arrField==null)
		{
			$arrField = HeroDef::$HERO_FIELDS;
		}
		return HeroDao::getHeroByUidHtid($uid, $htid, $arrField);
	}
	
	public static function getArrHeroByHtid($uid, $htid, $arrField=null)
	{
		if ($arrField==null)
		{
			$arrField = HeroDef::$HERO_FIELDS;
		}
		return HeroDao::getArrHeroByHtid($uid, $htid, $arrField);
	}
	
	public static function getRecruitHero($uid)
	{
		return HeroDao::getHeroesByUidStatus($uid, HeroDef::STATUS_RECRUIT, HeroDef::$HERO_FIELDS);
	}
	
	public static function getHero($hid)
	{
		return HeroDao::getByHid($hid, HeroDef::$HERO_FIELDS);
	}
	
	public static function getArrHero($arrHid, $arrField=null, $noCache=false)
	{
		if ($arrField==null)
		{
			$arrField = HeroDef::$HERO_FIELDS;
		}
		return HeroDao::getByArrHid($arrHid, $arrField, $noCache);
	}
	
	public static function recruitForInit($uid, $htid, $arrField)
	{
		if ($arrField==null)
		{
			$arrField = array();
		}
		$arrField['status'] =  HeroDef::STATUS_RECRUIT;
		return self::insertFromTemplate($uid, $htid, $arrField);	
	}	
	
	public static function getInitGoodwill()
	{
		return array('exp'=>0, 'level'=>0, 'upgrade_time'=>0, 'heritage'=>0);
	}
	
	public static function insertFromTemplate ($uid, $htid, $arrField = null)
	{
		if ($arrField == null)
		{
			$arrField = array();
		}
		$arrField['htid'] = $htid;
		$arrField['uid'] = $uid;
		
		if (!isset($arrField['curHp']))
		{
			$arrField['curHp'] = 0;
		}

		if (!isset($arrField['level']))
		{
			$arrField['level'] = 1;
		}

		if (!isset($arrField['rebirthNum']))
		{
			$arrField['rebirthNum'] = 0;
		}

		if (!isset($arrField['exp']))
		{
			$arrField['exp'] = 0;
		}
		
		if (!isset($arrField['all_exp']))
		{
			$arrField['all_exp'] = 0;
		}		

		if (!isset($arrField['stateId']))
		{
			$arrField['stateId'] = 0;
		}
		
		if (!isset($arrField['upgrade_time']))
		{
			$arrField['upgrade_time'] = Util::getTime();
		}

		if (!isset($arrField['va_hero']))
		{
			$arrField['va_hero'] = array();
			$arrField['va_hero']['daimonApple'] = array();
		}
		else
		{
			if (!isset($arrField['va_hero']['daimonApple']))
			{
				$arrField['va_hero']['daimonApple'] = array();
			}
		}

		//主角英雄特有的字段
		if (HeroUtil::isMasterHero($htid))
		{
			$arrField['va_hero']['master'] = array('transfer_num'=>0,
												   // 'learned_normal_skills'=>array(),
												   'using_skill_time'=>0,
												   'using_skill_num'=>0,);
			$transNum = $arrField['va_hero']['master']['transfer_num'];
			$arrDefaultRageSkill = btstore_get()->MASTER_HEROES_TRANSFER[$htid][$transNum]['transfer_rageSkills']->toArray();
			$defaultNormalSkill = btstore_get()->CREATURES[$htid][CreatureInfoKey::normalAtk];
			$rageSkill = intval(key($arrDefaultRageSkill));
			$normalSkill = intval($defaultNormalSkill[0]);
			$arrField['va_hero']['master']['learned_rage_skills'] = array($rageSkill);
			$arrField['va_hero']['master']['using_skill'] = $rageSkill;
			$arrField['va_hero']['master']['learned_normal_skills'] = array($normalSkill);
			$arrField['va_hero']['master']['using_normal_skill'] = $normalSkill;
			$arrField['va_hero']['daimonApple'] = array(0);			
		}
		
		$itemMgr = null;
		//0转开启位置		
		foreach (btstore_get()->CREATURES[$htid][CreatureInfoKey::devilFruitSkill] as $pos=>$rebirthDmTid)
		{
			list($rebirthNum, $DmTid) = $rebirthDmTid;
			if ($rebirthNum==0)
			{
				$itemId = 0;
				//有默认的恶魔果实
				if ($DmTid!=0)
				{
					$itemMgr = ItemManager::getInstance();
					$itemId = $itemMgr->addItem($DmTid);
					$itemId = $itemId[0];
				}
				$arrField['va_hero']['daimonApple'][$pos] = $itemId;
			}
		}
		if ($itemMgr!=null)
		{
			$itemMgr->update();
		}
		
		$arrField['va_hero']['arming'] = ArmingDef::$ARMING_NO_ARMING;
		$arrField['va_hero']['dress'] = self::getDefaultDress();
		$arrField['va_hero']['jewelry'] = self::getDefaultJewelry();
		$arrField['va_hero']['element'] = self::getDefaultElement();
		$arrField['va_hero']['goodwill'] = self::getInitGoodwill();
		
		$arrField['va_hero']['haki'] = array(
				'hp' => array('level'=>0, 'expe'=>0, 'property'=>array()),
				'master' => array('level'=>0, 'expe'=>0, 'property'=>array()),
				'xiuluo' => array('level'=>0, 'expe'=>0, 'property'=>array()),
				'defense' => array('level'=>0, 'expe'=>0, 'property'=>array()),
				'attack' => array('level'=>0, 'expe'=>0, 'property'=>array()),
		);
		$arrField['va_hero']['master_haki_id'] = 0;
		$arrField['va_hero']['talnet_skill_level'] = 0;
		return HeroDao::save($arrField);
	}
	
	public static function getDefaultDress()
	{
		return FashionDressDef::$FASHION_NO_DRESS;
	}
	
	public static function getDefaultJewelry()
	{
		return JewelryDef::$JEWELRY_NO_JEWELRY;
	}

	public static function getDefaultElement()
	{
		return ElementDef::$ELEMENT_NO_ELEMENT;
	}
	
	public static function getByHid ($hid)
	{
		return HeroDao::getByHid($hid, HeroDef::$HERO_FIELDS);
	}
	
	public static function getAttrByHid ($hid)
	{
		return Herodao::getByHid($hid, HeroDef::$HERO_FIELDS);
	}
	
	public static function getMasterTopLevel($offset, $limit, $arrField)
	{
		return HeroDao::getMasterTopLevel($offset, $limit, $arrField);
	}	
	
	public static function getMasterTopLevelUnstable($offset, $limit, $arrField)
	{
		return HeroDao::getMasterTopLevelUnstable($offset, $limit, $arrField);
	}	
	
	public static function getMasterByLevel($level, $arrField, $num)
	{
		return HeroDao::getMasterByLevel($level, $arrField, $num);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */