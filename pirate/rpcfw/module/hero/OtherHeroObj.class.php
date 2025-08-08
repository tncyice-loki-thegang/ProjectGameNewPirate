<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: OtherHeroObj.class.php 39830 2013-03-04 09:23:00Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/OtherHeroObj.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-04 17:23:00 +0800 (一, 2013-03-04) $
 * @version $Revision: 39830 $
 * @brief 
 * 
 **/

/**
 * 没有登录用户的英雄
 * @author idyll
 *
 */

class OtherHeroObj extends Creature
{
	protected $attr;
	protected $attrModify;
	
	/**
	 * 好感度属性
	 * Enter description here ...
	 * @var unknown_type
	 */	 
	protected $gwAttr;
	
	/**
	 * 装备item对象数组
	 * pos => item
	 * Enter description here ...
	 * @var unknown_type
	 */
	protected $armingItem;
	
	/**
	 * 恶魔果实item对象
	 * Enter description here ...
	 * @var unknown_type
	 */
	protected $daItem;
	
	protected $dressItem=null;
	
	protected $jewelryItem = null;
	
	protected $elementItem = null;
	
	public function __construct ($attr)
	{
		$this->attr = $attr;
		$this->attrModify = $this->attr;
		$this->gwAttr = null;
		if (HeroUtil::isMasterHero($this->attr['htid']))
		{
			$this->btstoreConf = btstore_get()->MASTER_HEROES_TRANSFER[$this->attr['htid']];
		}
		parent::__construct($this->attr['htid']);
		
		$this->initGoodwill();
		
		$this->armingItem = null;
		$this->daItem = null;
		
		if (!isset($this->attrModify['va_hero']['dress']))
		{
			$this->attrModify['va_hero']['dress'] = $this->attr['va_hero']['dress'] = HeroLogic::getDefaultDress();
		}
		
		if (!isset($this->attrModify['va_hero']['jewelry']))
		{
			$this->attrModify['va_hero']['jewelry'] = $this->attr['va_hero']['jewelry'] = HeroLogic::getDefaultJewelry();
		}
		
		if (!isset($this->attrModify['va_hero']['element']))
		{
			$this->attrModify['va_hero']['element'] = $this->attr['va_hero']['element'] = HeroLogic::getDefaultElement();
		}		
	}
	
	/**
	 * 兼容老数据， 这里初始化goodwill
	 * Enter description here ...
	 */
	protected function initGoodwill()
	{
		if (!isset($this->attrModify['va_hero']['goodwill']))
		{
			//两个都赋值，避免OtherHeroObj更新va
			$this->attrModify['va_hero']['goodwill'] = HeroLogic::getInitGoodwill();
			$this->attr['va_hero']['goodwill'] = $this->attrModify['va_hero']['goodwill'];
		}
		else
		{
			//兼容检查
			if (!isset($this->attrModify['va_hero']['goodwill']['heritage']))
			{
				$this->attrModify['va_hero']['goodwill']['heritage'] = 
					$this->attr['va_hero']['goodwill']['heritage'] = 0;				
			}
		}		
	}
	
	public function getAllAttr ()
	{
		return $this->attrModify;
	}
	
	public function isPub ()
	{
		return $this->attrModify['status'] == HeroDef::STATUS_PUB;
	}
	
	public function isRecruit ()
	{
		return $this->attrModify['status'] == HeroDef::STATUS_RECRUIT;
	}
	
	public function getCurHp ()
	{
		return $this->attrModify['curHp'];
	}
	
	public function getHid ()
	{
		return $this->attrModify['hid'];
	}
	
	public function getHtid ()
	{
		return $this->attrModify['htid'];
	}
	
	public function getExp ()
	{
		return $this->attrModify['exp'];
	}
	
	public function getLevel ()
	{
		return $this->attrModify['level'];
	}
	
	public function getVocation ()
	{
		$heroConfig = btstore_get()->CREATURES[$this->attrModify['htid']];
		return $heroConfig[CreatureInfoKey::vocation];
	}
	
	public function addExp ($num)
	{
		$this->attrModify['exp'] += $num;
	}
	
	//得到科技和宠物的属性
	private function getSciAndPet ()
	{
		$sciInfo = array();
		if (EnSwitch::isOpen(SwitchDef::RESEARCH, $this->attrModify['uid']))
		{
			//科技
			$sciTech = new SciTech();
			$sciInfo = $sciTech->getAllSciTechAttrByUid($this->attrModify['uid']);
		}
		
		//阵型
		$formationInfo = false;
		if (EnSwitch::isOpen(SwitchDef::FORMATION, $this->attrModify['uid']))
		{
			$formationInfo = EnFormation::getUserCurFormationAttr($this->attrModify['uid']);		
		}		
		
		$petInfo = array();
		$copetInfo = array();
		if (EnSwitch::isOpen(SwitchDef::PET, $this->attrModify['uid']))
		{
			//宠物
			$petInfo = EnPet::getUserCurPetAllAttr($this->attrModify['uid']);
			$copetInfo = EnCoPet::getUserCurPetAllAttr($this->attrModify['uid']);
		}
		
		Logger::debug("sciInfo:%s, petInfo:%s, formationInfo:%S", $sciInfo, $petInfo, $formationInfo);
		if ($petInfo !== false)
		{
			foreach ($petInfo as $k=>$v)
			{
				if (isset($sciInfo[$k]))
				{
					$sciInfo[$k] += $v;
				}
				else
				{
					$sciInfo[$k] = $v;
				}
			}
		}

		if ($copetInfo !== false)
		{
			foreach ($copetInfo as $k=>$v)
			{
				if (isset($sciInfo[$k]))
				{
					$sciInfo[$k] += $v;
				}
				else
				{
					$sciInfo[$k] = $v;
				}
			}
		}

		if ($formationInfo!==false)
		{
			foreach ($formationInfo as $k=>$v)
			{
				if (isset($sciInfo[$k]))
				{
					$sciInfo[$k] += $v;
				}
				else
				{
					$sciInfo[$k] = $v;
				}
			}
		}
		
		return $sciInfo;
	}
	
	public function getCurrHp()
	{
		return $this->attrModify['curHp'];
	}
	
	protected function getInfo_ ()
	{
		$hero = $this->getNude();
		
		//天赋星盘
		if ($this->isMasterHero())
		{
			$talentAst = $this->getTalentAst();
			Logger::debug('talent ast:%s', $talentAst);
			foreach ($talentAst as $k=>$v)
			{
				if (!in_array($k, HeroDef::$AST_EXPT))
				{
					$hero[$k] += $talentAst[$k];
				}
			}
		}
		
		//主星盘
		$mainAst = $this->getMainAst();
		foreach ($mainAst as $k=>$v)
		{
			Logger::debug('main ast:%s', $mainAst);
			if (!in_array($k, HeroDef::$AST_EXPT))
			{
				$hero[$k] += $mainAst[$k];
			}
		}
		
		//好感度
		$gwAttr = $this->getGoodwillAttr();
		if (!empty($gwAttr))
		{
			$hero = $this->sumGoodwill($hero, $gwAttr);
		}
		
		//装备(时装)、恶魔果实
		$info = $this->getArmingAndDmApple();
		if (!empty($info))
		{
			$hero = $this->sumArming($hero, $info);
		}
		//装备信息
		$hero['equipInfo'] = $this->arrItemInfo($this->getArmingItem());
		//恶魔果实信息
		$hero['daimonApple'] = $this->arrItemInfo($this->getDaItem());
		//时装
		$hero['dress'] = $this->arrItemInfo($this->getDressItem());
		$dressInfo = DressLogic::getDressRommInfo($this->attrModify['uid']);
		$hero['imageDress'] = array(0 => array ('template_id' => $dressInfo['cur_dress']));
		$hero['show_dress'] = EnUser::getUserObj($this->attrModify['uid'])->isShowDress();		
		
		//恶魔果实技能
		$Eskills = $this->getElementSkills($this->getElementItem());
		$Dakills = $this->getDaSkills($this->getDaItem());
		$hero[CreatureInfoKey::arrSkill] = array_merge($Dakills, $Eskills);
		
		//科技、宠物
		$info = $this->getSciAndPet();
		if (!empty($info))
		{
			$hero = $this->sumSciAndPet($hero, $info);
		}
		
		$titleAttr = EnAchievements::getCurrentTitleAttr($this->attrModify['uid']);
		if (!empty($titleAttr))
		{
			//跟宠物的属性id一样
			$hero = $this->sumSciAndPet($hero, $titleAttr);
		}
		
		return $hero;
	}
	
	public function getSanWei ()
	{
		$arrRet = array();
		$hero = $this->getInfo_();
		list($arrRet['stg'], $arrRet['agile'], $arrRet['itg']) = $this->calculateSanWeiBase($hero);
		$arrRet['stgAdd'] = $hero[CreatureInfoKey::reBirthNum] * $hero[CreatureInfoKey::stgRebirth] / 100;
		$arrRet['agileAdd'] = $hero[CreatureInfoKey::aglRebirth] * $hero[CreatureInfoKey::reBirthNum] / 100;
		$arrRet['itgAdd'] = $hero[CreatureInfoKey::reBirthNum] * $hero[CreatureInfoKey::itgRebirth] / 100;
		return $arrRet;
	}
	
	//加上科技和宠物的属性
	protected function sumSciAndPet ($hero, $addInfo)
	{
		foreach ($addInfo as $k=>$v)
		{
			if (isset($hero[MapSciHero::$mapSciHero[$k]]))
			{
				$hero[MapSciHero::$mapSciHero[$k]] += $v;
			}
			else
			{
				$hero[MapSciHero::$mapSciHero[$k]] = $v;
			}
		}
		return $hero;
	}
	
	public function getRebirthNum ()
	{
		if ($this->rebirthNumTmp!=0)
		{
			return $this->rebirthNumTmp;
		}
		return $this->attrModify['rebirthNum'];
	}
	
	/**
	 * 生命总值=生命基础值*（1+生命值百分比）
	 * 生命基础值 = 英雄基础生命值 + 英雄生命成长*英雄等级 +
	 * 装备提供的生命固定值+宝石提供的生命固定值+卡牌提供的生命固定值+恶魔果实提供的生命固定值 +
	 * 科技提供的生命固定值+ 宠物提供的生命固定值
	 * Enter description here ...
	 */
	public function getMaxHp ()
	{
		$heroConfig = btstore_get()->CREATURES[$this->attrModify['htid']];
		$hpBase = $heroConfig[CreatureInfoKey::hp];
		$hpPercent = 0;
		$hp = 0;
		
		//天赋星盘
		if ($this->isMasterHero())
		{
			$talentAst = $this->getTalentAst();
			if (isset($talentAst[CreatureInfoKey::hp]))
			{
				$hpBase += $talentAst[CreatureInfoKey::hp];
			}
			if (isset($talentAst[CreatureInfoKey::hpFinal]))
			{
				$hp += intval($talentAst[CreatureInfoKey::hpFinal]);
			}
			if (isset($talentAst[CreatureInfoKey::hpRatio]))
			{
				$hpPercent += $talentAst[CreatureInfoKey::hpRatio];
			}
		}
		
		//主星盘
		$mainAst = $this->getMainAst();
		if (isset($mainAst[CreatureInfoKey::hp]))
		{
			$hpBase += $mainAst[CreatureInfoKey::hp];
		}
		if (isset($mainAst[CreatureInfoKey::hpFinal]))
		{
			$hp += intval($mainAst[CreatureInfoKey::hpFinal]);
		}
		if (isset($mainAst[CreatureInfoKey::hpRatio]))
		{
			$hpPercent += $mainAst[CreatureInfoKey::hpRatio];
		}
		
		//装备\恶魔果实 附加hp
		$info = $this->getArmingAndDmApple();
		if (isset($info[ItemDef::ITEM_ATTR_NAME_HP]))
		{
			$hpBase += $info[ItemDef::ITEM_ATTR_NAME_HP];
		}
		if (isset($info[ItemDef::ITEM_ATTR_NAME_HP_PERCENT]))
		{
			$hpPercent += $info[ItemDef::ITEM_ATTR_NAME_HP_PERCENT];
		}
		
		//科技\宠物 附加hp， key取值看MapSciHero.def.php
		$petSci = $this->getSciAndPet();
		if (isset($petSci[1]))
		{
			$hpBase += $petSci[1];
		}
		if (isset($petSci[2]))
		{
			$hpPercent += $petSci[2];
		}
		
		$gwAttr = $this->getGoodwillAttr();
		if (isset($gwAttr[GoodwillAttr::HP_BASE]))
		{
			$hpBase += $gwAttr[GoodwillAttr::HP_BASE];
		}
		
		$titleAttr = EnAchievements::getCurrentTitleAttr($this->attrModify['uid']);
		if (isset($titleAttr[GoodwillAttr::HP_BASE]))
		{
			$hpBase += $titleAttr[GoodwillAttr::HP_BASE];
		}
		if (isset($titleAttr[2])) //todo
		{
			$hpPercent += $titleAttr[2];
		}
		
		$hpBase += $this->attrModify['level'] * $heroConfig[CreatureInfoKey::hpIcs];
		$hp +=  intval($hpBase * (1 + $hpPercent / 10000));
		
		if (isset($gwAttr[GoodwillAttr::HP_FINAL]))
		{
			$hp += intval($gwAttr[GoodwillAttr::HP_FINAL]);
		}
		
		//宠物加最终生命
		if (isset($petSci[GoodwillAttr::HP_FINAL]))
		{
			$hp += intval($petSci[GoodwillAttr::HP_FINAL]);
		}
		
		if (isset($titleAttr[GoodwillAttr::HP_FINAL]))
		{
			$hpPercent += $titleAttr[GoodwillAttr::HP_FINAL];
		}
		
		return intval($hp);		
	}
	
	public function getEquipByPosition($type, $position)
	{
		if (!isset($this->attrModify['va_hero'][$type][$position]))
		{
			$this->fixEquipPosition($type);
			
			if (!isset($this->attrModify['va_hero'][$type][$position]))
			{
				throw new Exception('invalid equip type ' . $type . 'or positon ' . $position);
			}			
		}		
		return $this->attrModify['va_hero'][$type][$position];
	}
	
	public function getArmingByPosition ($arm_position)
	{
		return $this->getEquipByPosition('arming', $arm_position);
	}
	
	protected function fixDressPosition()
	{
		$this->fixEquipPosition('dress');
	}
	
	protected function fixJewelryPosition()
	{
		$this->fixEquipPosition('jewelry');
	}

	protected function fixElementPosition()
	{
		$this->fixEquipPosition('element');
	}
	
	protected function fixEquipPosition($type)
	{
		$PosItem = HeroUtil::getEquipDef($type);
		foreach ($PosItem as $pos=> $noItem)
		{
			if (!isset($this->attrModify['va_hero'][$type][$pos]))
			{
				$this->attrModify['va_hero'][$type][$pos] = $noItem;
			}
		}
	}
	
	public function getDressByPosition($dress_position)
	{
		return $this->getEquipByPosition('dress', $dress_position);
	}
	
	public function getJewelryByPosition($position)
	{
		return $this->getEquipByPosition('jewelry', $position);
	}

	public function getElementByPosition($position)
	{
		return $this->getEquipByPosition('element', $position);
	}
	
	protected function getNude ()
	{
		$tplHero = parent::getNude();
		$retInfo = array();
		foreach ($tplHero as $k=>$v)
		{
			$retInfo[$k] = $v;
		}
		
		$retInfo['hid'] = $this->attrModify['hid'];
		$retInfo[CreatureInfoKey::curHp] = $this->attrModify['curHp'];
		$retInfo[CreatureInfoKey::level] = $this->attrModify['level'];
		$retInfo[CreatureInfoKey::reBirthNum] = $this->getRebirthNum();
		
		return $retInfo;
	}
	
	public function isHero ()
	{
		return true;
	}
	
	public function isMasterHero ()
	{
		return isset(UserConf::$MASTER_HEROES[$this->getHtid()]);
	}
	
	public function setHp ($hpNum)
	{
		$this->attrModify['curHp'] = $hpNum;
	}
	
	public function addHp ($hpNum)
	{
		$maxHp = $this->getMaxHp();
		if ($this->attrModify['curHp'] >= $maxHp)
		{
			return 0;
		}
		
		if ($this->attrModify['curHp'] + $hpNum >= $maxHp)
		{
			$hpNum = $maxHp - $this->attrModify['curHp'];
		}
		
		//从血池中减去相应的量
		$user = EnUser::getInstance($this->attrModify['uid']);
		$retNum = $user->subFitBloodPackage($hpNum);
		$this->attrModify['curHp'] += $retNum;
		return $retNum;
	}
	
	/**
	 * 设置为英雄血为最大值
	 * Enter description here ...
	 * @param unknown_type $useMedicalRoot 	 是否使用医疗室较少血库的消耗
	 */
	public function setToMaxHp ($useMedicalRoom=false)
	{
		$maxHp = $this->getMaxHp();
		$diff = $maxHp - $this->attrModify['curHp'];
		Logger::debug("addToMaxHp, maxHp:%d, curHp:%d", $maxHp, $this->attrModify['curHp']);
		if ($diff > 0)
		{
			$user = EnUser::getInstance($this->attrModify['uid']);
			$needBlood = $diff;
			if ($useMedicalRoom)
			{
				$boatInfo = EnSailboat::getUserBoat($this->attrModify['uid']);
				// 获取医务室等级
				$cabinLv = isset($boatInfo['va_boat_info']['cabin_id_lv'][SailboatDef::MEDICAL_ROOM_ID]['level']) ?
				$boatInfo['va_boat_info']['cabin_id_lv'][SailboatDef::MEDICAL_ROOM_ID]['level'] : 0;
				
				$needBlood = $needBlood * (1 - btstore_get()->MEDICAL_ROOM['after_battle_hp_percent'] * $cabinLv / CopyConf::LITTLE_WHITE_PERCENT);
				
			}
			
			$bloodPackage = $user->getBloodPackage();
			//血库不满，不给加成，就坑你一点
			if ($bloodPackage < $needBlood)
			{
				$retNum = $user->subFitBloodPackage($needBlood);
				$this->attrModify['curHp'] += $retNum;
				return false;
			}
			else
			{
				$user->subFitBloodPackage($needBlood);
				$this->attrModify['curHp'] += $diff;
				return true;
			}
		}
		//超过最大值，则直接改为最大值
		else if ($diff < 0)
		{
			$this->setHp($maxHp);
		}
		return true;
	}
	
	//加上装备提供的属性
	private function sumArming ($hero, $armingInfo)
	{
		Logger::debug('arming for getInfo :%s', $armingInfo);
		foreach ($armingInfo as $k=>$v)
		{
			if (isset($hero[MapItemHeroDef::$ItemHero[$k]]))
			{
				$hero[MapItemHeroDef::$ItemHero[$k]] += $v;
			}
			else
			{
				$hero[MapItemHeroDef::$ItemHero[$k]] = $v;
			}
			
		}
		
		$hero['physicalDamageIgnoreRatio'] = 0;
		$hero['killDamageIgnoreRatio'] = 0;
		$hero['magicDamageIgnoreRatio'] = 0;
		return $hero;
	}
	
	//加上好感度的基础属性， 不包括最终hp
	private function sumGoodwill ($hero, $gwAttr)
	{
		Logger::debug('goodwill for getInfo:%s, hero:%s', $gwAttr, $hero);
		foreach ($gwAttr as $k => $v)
		{
			if (isset(GoodwillAttr::$arrAttrGwMap[$k]))
			{
				$hero[GoodwillAttr::$arrAttrGwMap[$k]] += $v;
			}
		}
		Logger::debug('after goodwill, hero:%s', $hero);
		return $hero;
	}
	
	/**
	 * $type 'arming' or 'daimonApple'
	 * Enter description here ...
	 * @param unknown_type $type
	 * @return array(pos=>itemId)
	 */
	public function getPosItem ($type)
	{
		$return = array();
		foreach ($this->attrModify['va_hero'][$type] as $arm_position=>$item_id)
		{
			if ($item_id != BagDef::ITEM_ID_NO_ITEM)
			{
				$return[$arm_position] = $item_id;
			}
			else
			{
				$return[$arm_position] = 0;
			}
		}
		return $return;
	}
	
	private function getArrItem ($arrPosId, $arrItem)
	{
		$ret = array();
		foreach ($arrPosId as $pos=>$id)
		{
			if (isset($arrItem[$id]))
			{
				$ret[$pos] = $arrItem[$id];
			}
			else
			{
				$ret[$pos] = 0;
			}
		}
		return $ret;
	}	
	
	/**
	 * @deprecated
	 * Enter description here ...
	 */
	public function getArmingDmItems ()
	{		
		$arrRet = array();
		$arrRet['arming'] = $this->getArmingItem();
		$arrRet['daimonApple'] = $this->getDaItem();
		return $arrRet;
	}
	
	/**
	 * 初始话装备和恶魔果实物品对象
	 * Enter description here ...
	 */
	protected function initArmingDmItems_ ()
	{
		$arrArmingId = $this->getPosItem('arming');
		$arrDmId = $this->getPosItem('daimonApple');
		$arrDressId = $this->getPosItem('dress');
		$arrJewelryId = $this->getPosItem('jewelry');
		$arrElementId = $this->getPosItem('element');
		
		$allId = array_merge($arrArmingId, $arrDmId, $arrDressId, $arrJewelryId, $arrElementId);
		$allItems = ItemManager::getInstance()->getItems($allId);
		
		$this->armingItem = $this->getArrItem($arrArmingId, $allItems);
		$this->daItem = $this->getArrItem($arrDmId, $allItems);		
		$this->dressItem = $this->getArrItem($arrDressId, $allItems);
		$this->jewelryItem = $this->getArrItem($arrJewelryId, $allItems);
		$this->elementItem = $this->getArrItem($arrElementId, $allItems);
	}
	
	public function getArmingItem()
	{
		if ($this->armingItem===null)
		{
			$this->initArmingDmItems_();	
		}
		return $this->armingItem;
	}
	
	public function getDaItem()
	{
		if ($this->daItem===null)
		{
			$this->initArmingDmItems_();	
		}
		return $this->daItem;
	}
	
	/**
	 * 返回时装物品对象
	 * @return array(FashionDressItem)
	 */
	public function getDressItem()
	{
		if ($this->dressItem===null)
		{
			$this->initArmingDmItems_();
		}
		return $this->dressItem;
	}
	
	public function getJewelryItem()
	{
		if ($this->jewelryItem===null)
		{
			$this->initArmingDmItems_();
		}
		return $this->jewelryItem;
	}

	public function getElementItem()
	{
		if ($this->elementItem===null)
		{
			$this->initArmingDmItems_();
		}
		return $this->elementItem;
	}
	
	public function getDressTemplate()
	{
		$arrItem = $this->getDressItem();
		$arrRet = array();
		foreach ($arrItem as $pos=>$itemObj)
		{	
			if (!empty($itemObj))
			{		
				$arrRet[$pos] = array('template_id' => $itemObj->getItemTemplateID());
			}
		}
		
		return $arrRet;
	}
	
	public function arrItemInfo ($arrItem)
	{
		$arrRet = array();
		foreach ($arrItem as $key=>$item)
		{
			if ($item == null)
			{
				$arrRet[$key] = array();
			}
			else
			{
				$arrRet[$key] = $item->itemInfo();
			}
		}
		return $arrRet;
	}
	
	//得到恶魔果实技能
	protected function getDaSkills ($arrPosItem)
	{
		//恶魔果实技能
		$daSkill = array();
		foreach ($arrPosItem as $item)
		{
			if ($item != null)
			{
				$itemSkill = $item->getSkills();
				$daSkill = array_merge($daSkill, $itemSkill);
			}
		}
		return $daSkill;
	}

	protected function getElementSkills ($arrPosItem)
	{		
		$ESkill = array();
		foreach ($arrPosItem as $item)
		{
			if ($item != null)
			{
				$itemSkill = $item->getSkills();
				$ESkill = array_merge($ESkill, $itemSkill);
			}
		}
		return $ESkill;
	}

	// 得到装备（包括时装） 和 恶魔果实 的属性 
	public function getArmingAndDmApple ()
	{
		$ret = array();
		
		$allItems = array();
		$allItems['arming'] = $this->getArmingItem();
		$allItems['dress'] = $this->getDressItem();
		$allItems['daimonApple'] = $this->getDaItem();
		$allItems['jewelry'] = $this->getJewelryItem();
		$allItems['element'] = $this->getElementItem();
		
		foreach ($allItems as $posItem)
		{
			foreach ($posItem as $item)
			{
				if ($item == null)
				{
					continue;
				}
				
				$itemInfo = $item->info();
				foreach ($itemInfo as $key=>$value)
				{
					if (isset($ret[$key]))
					{
						$ret[$key] += $value;
					}
					else
					{
						$ret[$key] = $value;
					}
				}
			
			}
		}
		return $ret;
	}
	
	public function update ()
	{
		$arrField = array();
		foreach ($this->attr as $key=>$value)
		{
			if ($this->attrModify[$key] != $value)
			{
				$arrField[$key] = $this->attrModify[$key] - $value;
			}
		}
		if (!empty($arrField))
		{
			$this->attr = $this->attrModify;
			//给lcserver发消息
			RPCContext::getInstance()->executeTask(intval($this->attr['uid']), 'hero.modifyHeroByOther', array($this->attr['uid'], $this->attr['hid'], $arrField), false);
		}
	
	}
	
	public function rollback ()
	{
		$this->attrModify = $this->attr;
	}
	
	//主角英雄计算的方法不同
	public function getTransferNum ()
	{
		if ($this->isMasterHero())
		{
			return $this->attrModify['va_hero']['master']['transfer_num'];
		}
		Logger::warning('the hero htid %d is not master hero', $this->attrModify['htid']);
		throw new Exception('fake');
	}
	
	protected function  getTalentAst()
	{
		return EnUser::getUserObj($this->attr['uid'])->getTalentAst();
	}
	
	protected function  getMainAst()
	{
		return EnUser::getUserObj($this->attr['uid'])->getMainAst();
	}
	
	private $btstoreConf;
	
	protected function getStrengthBase ()
	{
		if ($this->isMasterHero())
		{
			$tsfNum = $this->getTransferNum();
			return $this->btstoreConf[$tsfNum]['transfer_baseStr'];
		}
		return parent::getStrengthBase();
	}
	
	protected function getAgileBase ()
	{
		if ($this->isMasterHero())
		{
			$tsfNum = $this->getTransferNum();
			return $this->btstoreConf[$tsfNum]['transfer_baseAgi'];
		}
		return parent::getAgileBase();
	}
	
	protected function getIntelligenceBase ()
	{
		if ($this->isMasterHero())
		{
			$tsfNum = $this->getTransferNum();
			return $this->btstoreConf[$tsfNum]['transfer_baseInt'];
		}
		return parent::getIntelligenceBase();
	}

	protected function getPhyFDmgRatio ()
	{
		if ($this->isMasterHero())
		{		
			//用天赋星盘替换原来的值
			$talentAst = $this->getTalentAst();
			if (isset($talentAst[CreatureInfoKey::phyFDmgRatio]))
			{
				return $talentAst[CreatureInfoKey::phyFDmgRatio];	
			}
				
			$tsfNum = $this->getTransferNum();
			return $this->btstoreConf[$tsfNum]['transfer_phyAtt'];
		}
		return parent::getPhyFDmgRatio();
	}
	
	protected function getPhyFEptRatio ()
	{
		if ($this->isMasterHero())
		{
			$talentAst = $this->getTalentAst();
			if (isset($talentAst[CreatureInfoKey::phyFEptRatio]))
			{
				return $talentAst[CreatureInfoKey::phyFEptRatio];	
			}
			
			$tsfNum = $this->getTransferNum();
			return $this->btstoreConf[$tsfNum]['transfer_phyDef'];
		}
		return parent::getPhyFEptRatio();
	}
	
	protected function getKillFDmgRatio ()
	{
		if ($this->isMasterHero())
		{
			$talentAst = $this->getTalentAst();
			if (isset($talentAst[CreatureInfoKey::killFDmgRatio]))
			{
				return $talentAst[CreatureInfoKey::killFDmgRatio];	
			}
			
			$tsfNum = $this->getTransferNum();
			return $this->btstoreConf[$tsfNum]['transfer_kilAtt'];
		}
		return parent::getKillFDmgRatio();
	}
	
	protected function getKillFEptRatio ()
	{
		if ($this->isMasterHero())
		{
			$talentAst = $this->getTalentAst();
			if (isset($talentAst[CreatureInfoKey::killFEptRatio]))
			{
				return $talentAst[CreatureInfoKey::killFEptRatio];	
			}
			
			$tsfNum = $this->getTransferNum();
			return $this->btstoreConf[$tsfNum]['transfer_kilDef'];
		}
		return parent::getKillFEptRatio();
	}
	
	protected function getMgcFDmgRatio ()
	{
		if ($this->isMasterHero())
		{
			$talentAst = $this->getTalentAst();
			if (isset($talentAst[CreatureInfoKey::mgcFDmgRatio]))
			{
				return $talentAst[CreatureInfoKey::mgcFDmgRatio];	
			}
			
			$tsfNum = $this->getTransferNum();
			return $this->btstoreConf[$tsfNum]['transfer_magAtt'];
		}
		return parent::getMgcFDmgRatio();
	}
	
	protected function getMgcFEptRatio ()
	{
		if ($this->isMasterHero())
		{
			$talentAst = $this->getTalentAst();
			if (isset($talentAst[CreatureInfoKey::mgcFEptRatio]))
			{
				return $talentAst[CreatureInfoKey::mgcFEptRatio];	
			}
			
			$tsfNum = $this->getTransferNum();
			return $this->btstoreConf[$tsfNum]['transfer_magDef'];
		}
		return parent::getMgcFEptRatio();
	}
	
	//得到普通攻击技能
	protected function getNormalAtkSkills ()
	{
		if ($this->isMasterHero())
		{
			$master = $this->attrModify['va_hero']['master'];
			$usingNormalSkill = $master['using_normal_skill'];
			//装备了普通技能， 替换原来的技能
			if (in_array($usingNormalSkill, $master['learned_normal_skills']))
			{
				return array($usingNormalSkill);
			}
			else
			{
				return parent::getNormalAtkSkills();
			}
		}
		return parent::getNormalAtkSkills();
	}
	
	//得到怒气技能
	protected function getRageSkill ()
	{
		if ($this->isMasterHero())
		{
			$master = $this->attrModify['va_hero']['master'];
			$usingSkill = $master['using_skill'];
			if (in_array($usingSkill, $master['learned_rage_skills']))
			{
				return $usingSkill;
			}
			else
			{
				return parent::getRageSkill();
			}
		}
		return parent::getRageSkill();
	}
	
	public function getOrderLevel ()
	{
		return HeroDao::getOrderLevel($this->attrModify['level'], $this->attrModify['upgrade_time']);
	}
	
	/**
	 * 临时设置的好感度等级, 不会保存到数据库
	 * @var int
	 */
	protected $gwLevelTmp = 0;
	
	/**
	 * 临时设置的转生次数
	 * @var int
	 */
	protected $rebirthNumTmp = 0;
	
	/**
	 * 临时设置好感度等级， 不会保存到数据库
	 */
	public function setGoodwillLevelTmp($level)
	{
		$this->gwLevelTmp = $level;
	}
	
	public function setRebirthNumTmp($rebirthNum)
	{
		$this->rebirthNumTmp = $rebirthNum;
	}
	
	public function getGoodwillLevel()
	{
		if ($this->gwLevelTmp!=0)
		{
			return $this->gwLevelTmp;
		}
		
		return $this->attrModify['va_hero']['goodwill']['level'];
	}
	
	/**
	 * 能被主角英雄学习的好感度技能
	 * Enter description here ...
	 * @param unknown_type $htid
	 */
	public function canLearnedGoodwillSkill($htid)
	{		
		$gwSkill = btstore_get()->CREATURES[$this->attrModify['htid']][CreatureInfoKey::good_will_skill]->toArray();
		$gwLevel = $this->getGoodwillLevel();	
		$arrSkill = array();
		foreach ($gwSkill as $needLevel => $skill)
		{
			if ($gwLevel < $needLevel )
			{
				break;
			}
			$arrSkill[] = $skill[$htid];
		}
		return $arrSkill;
	}	
	
	/**
	 * 得到好感度增加的属性
	 * Enter description here ...
	 */
	public function getGoodwillAttr()
	{
		if ($this->gwAttr !==null)
		{
			return $this->gwAttr;
		}
		
		$cfg =  btstore_get()->GOODWILL;
		$gwLevel = $this->getGoodwillLevel();
		if (!isset($cfg[$gwLevel]))
		{
			return array();
		}
		
		$this->gwAttr = btstore_get()->GOODWILL[$this->getGoodwillLevel()]['info'];
		return $this->gwAttr;		
	}
	
	/**
	 * 得到装备上的宝石id id
	 * Enter description here ...
	 */
	public function getArmingGenId()
	{
		$ret = array();
		$armingItem = $this->getArmingItem();
		foreach ($armingItem  as $item)
		{
			if ($item==null)
			{
				continue;
			}
			$ret = array_merge($ret, array_values($item->getGemItems()));
		}
		return $ret;
	}
	
	public function getArmingDmItemId()
	{
		$arrArmingId = $this->getPosItem('arming');
		$arrDmId = $this->getPosItem('daimonApple');
		$allId = array_merge($arrArmingId, $arrDmId);
		return $allId;
	}
	
	public function getGoodwillExp()
	{
		return $this->attrModify['va_hero']['goodwill']['exp'];
	}
	
	public function getGoodwillAllExp()
	{
		$exp = $this->getGoodwillExp();
		$level = $this->getGoodwillLevel();
		
		foreach (btstore_get()->GOODWILL_EXP as $needLv => $needExp)
		{
			if ($level < $needLv)
			{
				break;
			}
			$exp += $needExp;
		}
		return $exp;
	}
	
	public function getMaxRebirthNum()
	{
		$ret = 0;
		$level = $this->getLevel();
		foreach (btstore_get()->HERO_REBIRTH as $num=>$cfg)
		{
			if ($level < $cfg['need_level'])
			{
				break;
			}
			$ret = $num;
		}
		return $ret;
	}
	
	public function getMaxGoodwillLevel()
	{
		$arrCfg = btstore_get()->GOODWILL->toArray();
		$maxLevel = 0;
		
		
		//主角需要等级， 非主角需要转生次数
		if ($this->isMasterHero())
		{
			$num = $this->getLevel();
			$key = 'master_need_level';
		}
		else
		{
			$num = $this->getRebirthNum();
			$key = 'need_rebirth';
		}
		
		
		foreach ($arrCfg as $level => $cfg)		 
		{
			if ($num < $cfg[$key])
			{
				return $maxLevel;
			}
			$maxLevel = $level;
		}
		return $maxLevel;
	}
	
	public function getBaseHtid()
	{
		if (empty($this->attrModify['va_hero']['convert_from']))
		{
			return $this->getHtid();
		}
		
		return end($this->attrModify['va_hero']['convert_from']);
	}
	
	public function getMasterHakiId()
	{
		if (isset($this->attrModify['va_hero']['master_haki_id']))
		{
			return $this->attrModify['va_hero']['master_haki_id'];
		} else return 0;
	}	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */