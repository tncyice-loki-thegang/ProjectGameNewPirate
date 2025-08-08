<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 *
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief
 *
 **/




class Creature
{
	public static function getAttr($id, $arrField=null)
	{
		if (empty($arrField))
		{
			return btstore_get()->CREATURES[$id]->toArray(); 
		}
		
		$ret = array();
		
		$cfg = btstore_get()->CREATURES[$id];
		foreach ($arrField as $field)
		{
			$ret[$field] = $cfg[$field];
		}
		return $ret;
	}
	
	private $id;
	private $level;
	private $extAttr;
	private $rageSkill = 0;
	
	public function __construct($id, $level=0, $extAttr=null, $rageSkill=0)
	{
		$this->id = $id;
		if (!isset(btstore_get()->CREATURES[$this->id]))
		{
			throw new Exception("fail to get creature $id from btstore");
		}
		$this->level = $level;
		$this->extAttr = $extAttr;
		$this->rageSkill = $rageSkill;
	}
	
	public function setRageSkill($rageSkill)
	{
		$this->rageSkill = $rageSkill;
	}
	
	public function getLevel()
	{
		if ($this->level!=0)
		{
			return $this->level;
		}
		return btstore_get()->CREATURES[$this->id][CreatureInfoKey::level];
	}
	
	public function getHtid()
	{
		return $this->id;
	}
	
	public function getBaseHtid()
	{
		return $this->id;
	}
	
	public function getHid()
	{
		return $this->id;
	}
	
	public function getCurrHp()
	{
		return $this->getMaxHp();
	}

	/**
	 * 战斗使用的信息
	 * Enter description here ...
	 * @param unknown_type $countfightForce 为true的时候多一个值fight_force
	 */
	public function getInfo ($countfightForce=false)
	{
		Logger::debug("get creature %d info", $this->id);
		$creature = $this->getInfo_();		
		$creature = $this->calculateProperty($creature, $countfightForce);
		$creature['hid'] = $this->getHid();
		$creature['htid'] = $this->getHtid();		
		$creature['currHp'] = $this->getMaxHp();		
		return $creature;
	}
	
	protected function getInfo_ ()
	{
		$creature = $this->getNude();
		
		//附加属性
		if ($this->extAttr!=null)
		{
			foreach ($this->extAttr as $k=>$v)
			{
				$creature[MapSciHero::$mapSciHero[$k]] += $v;
			}
		}
		
		return $creature;
	}

	public function getMaxHp()
	{		
		$creature =  btstore_get()->CREATURES[$this->id];
		return ($this->getHpBase() +	$this->getLevel() * $creature[CreatureInfoKey::hpIcs])
			* (1+ 0);
	}
	
	public function getHpBase()
	{
		if (isset($this->extAttr[1]))
		{
			return $this->extAttr[1];
		}
		return btstore_get()->CREATURES[$this->id][CreatureInfoKey::hp];
	}

	public function isHero()
	{
		return false;
	}

	public function isMasterHero()
	{
		return false;
	}

	public function update()
	{
		//不需要实现
	}

	public function setHp($hpNum)
	{
		//不需要实现
	}

	public function addHp($hpNum)
	{
		//不需要实现
		return $hpNum;
	}

	//得到普通攻击技能
	protected function getNormalAtkSkills()
	{
		$creature = btstore_get()->CREATURES[$this->id];
		return $creature[CreatureInfoKey::normalAtk]->toArray();
	}

	//得到怒气技能
	protected function getRageSkill()
	{
		if ($this->rageSkill!=0)
		{
			return $this->rageSkill;
		}
		
		$creature = btstore_get()->CREATURES[$this->id];
		//怒气技能只有一个
		if (isset($creature[CreatureInfoKey::rageAtkSkill][0]))
		{
			return $creature[CreatureInfoKey::rageAtkSkill][0];
		}
		else
		{
			return 0;
		}
	}

	protected function getNude()
	{
		$creature = btstore_get()->CREATURES[$this->id];
		$retInfo = array();
		foreach ($creature as $k=>$v)
		{
			$retInfo[$k] = $v;
		}

		$retInfo['htid'] = $creature[CreatureInfoKey::htid];		
		$retInfo[CreatureInfoKey::hp] = $creature[CreatureInfoKey::hp];

		$retInfo[CreatureInfoKey::normalAtk] = $this->getNormalAtkSkills();
		//添加恶魔果实技能到arrSkill
		$retInfo[CreatureInfoKey::arrSkill] = array();
		$retInfo[CreatureInfoKey::level] = $this->getLevel();
		$retInfo[CreatureInfoKey::reBirthNum] = 0;

		$retInfo[CreatureInfoKey::immuneBufferID] = $retInfo[CreatureInfoKey::immuneBufferID]->toArray();

		$retInfo[CreatureInfoKey::rageAtkSkill] = $this->getRageSkill();

		//主角英雄的计算方法不同
		$retInfo[CreatureInfoKey::strength] = $this->getStrengthBase();
		$retInfo[CreatureInfoKey::agile] = $this->getAgileBase();
		$retInfo[CreatureInfoKey::intelligence] = $this->getIntelligenceBase();
		$retInfo[CreatureInfoKey::phyFDmgRatio] = $this->getPhyFDmgRatio();
		$retInfo[CreatureInfoKey::phyFEptRatio] = $this->getPhyFEptRatio();
		$retInfo[CreatureInfoKey::killFDmgRatio] = $this->getKillFDmgRatio();
		$retInfo[CreatureInfoKey::killFEptRatio] = $this->getKillFEptRatio();
		$retInfo[CreatureInfoKey::mgcFDmgRatio] = $this->getMgcFDmgRatio();
		$retInfo[CreatureInfoKey::mgcFEptRatio] = $this->getMgcFEptRatio();

		$retInfo[CreatureInfoKey::hpRatio] = 0;
		$retInfo[CreatureInfoKey::stgRatio] = 0;
		$retInfo[CreatureInfoKey::itgRatio] = 0;
		$retInfo[CreatureInfoKey::aglRatio] = 0;

		//装备信息
		$retInfo['equipInfo'] = array();
		//恶魔果实
		$retInfo['daimonApple'] = array();

		return $retInfo;
	}

	protected function getStrengthBase()
	{
		return btstore_get()->CREATURES[$this->id][CreatureInfoKey::strength];
	}

	protected function getAgileBase ()
	{
		return btstore_get()->CREATURES[$this->id][CreatureInfoKey::agile];
	}

	protected function getIntelligenceBase ()
	{
		return btstore_get()->CREATURES[$this->id][CreatureInfoKey::intelligence];
	}

	protected function getPhyFDmgRatio ()
	{
		return btstore_get()->CREATURES[$this->id][CreatureInfoKey::phyFDmgRatio];
	}

	protected function getPhyFEptRatio ()
	{
		return btstore_get()->CREATURES[$this->id][CreatureInfoKey::phyFEptRatio];
	}

	protected function getKillFDmgRatio ()
	{
		return btstore_get()->CREATURES[$this->id][CreatureInfoKey::killFDmgRatio];
	}

	protected function getKillFEptRatio ()
	{
		return btstore_get()->CREATURES[$this->id][CreatureInfoKey::killFEptRatio];
	}

	protected function getMgcFDmgRatio ()
	{
		return btstore_get()->CREATURES[$this->id][CreatureInfoKey::mgcFDmgRatio];
	}

	protected function getMgcFEptRatio ()
	{
		return btstore_get()->CREATURES[$this->id][CreatureInfoKey::mgcFEptRatio];
	}
	
	/**
	 * 返回战斗力值
	 * Enter description here ...
	 * @return
	 */
	public function getFightForce()
	{
		$creature = $this->getInfo_();
		$sanWei = $this->calculateSanWeiBase($creature);
		
		$fightForceBase = $this->calculateFightForceAtkDefBase($sanWei, $this->getLevel(), $creature);
		$fightForceRatio = $this->calculateFightForceAtkDefRatio($this->getLevel(), $creature);
		$finalAtkDef= $this->getFightForceAtkFinal($creature);
		
		Logger::debug('hero htid %d fight force base %s, fight force ratio %s', 
			$this->getHtid(), $fightForceBase, $fightForceRatio);
		
		return $this->getFightForce_($fightForceBase, $fightForceRatio, $this->getMaxHp(), $finalAtkDef, $creature, $sanWei);
	}
	
	protected function getFightForce_($fightForceBase, $fightForceRatio, $maxHp, $finalAtkDef, $creature, $sanWei)
	{
		$fightForce = 0;
		foreach ($fightForceBase as $key=>$value)
		{
			$fightForce += intval($value * (1 + $fightForceRatio[$key]/10000) );
		}
		
		$fightForce += intval($maxHp/10);		
		
		$finalAtkDefInt = array_map('intval', $finalAtkDef);
		$fightForce += intval(array_sum($finalAtkDefInt));
	
		$fightForce += $this->getTalentFightForce($creature);
		$fightForce += $this->getSanweiFightForce($sanWei);
		
		return intval($fightForce);
	}
	
	protected function getSanweiFightForce($sanWei)
	{
		return array_sum($sanWei) +  FightforceConf::SAN_WEI_FIX;
	}
	
	protected function getTalentFightForce($creature)
	{
		$createTalentKey = array(CreatureInfoKey::phyFDmgRatio, CreatureInfoKey::phyFEptRatio,
				CreatureInfoKey::mgcFDmgRatio, CreatureInfoKey::mgcFEptRatio,
				CreatureInfoKey::killFDmgRatio, CreatureInfoKey::killFEptRatio);
		$talent = 0;
		foreach ($createTalentKey as $key)
		{
			$talent += $creature[$key];
		}
		return intval(($talent+FightforceConf::TALENT_FIX)/4);		
	}
	
	//计算三围基础值， 查看其他用户的英雄和战斗都需要
	public function calculateSanWeiBase($creature)
	{
		$level = $creature[CreatureInfoKey::level];
		
		//20121224 修改为不除100， 计算战斗力的时候需要单独除100 传给前端也要除100
		$stg = $creature[CreatureInfoKey::strength]
			+ $creature[CreatureInfoKey::reBirthNum] * $creature[CreatureInfoKey::stgRebirth]
			+ $level * $creature[CreatureInfoKey::stgIcs];			
		$stg += $stg * ($creature[CreatureInfoKey::stgRatio]/10000);
		//$stg /= 100;
		
		
		$agile = $creature[CreatureInfoKey::agile]
			+ $creature[CreatureInfoKey::aglRebirth] * $creature[CreatureInfoKey::reBirthNum]
			+ $level * $creature[CreatureInfoKey::aglIcs];
		$agile += $agile * ($creature[CreatureInfoKey::aglRatio]/10000);
		//$agile /= 100;
		
		$itg = $creature[CreatureInfoKey::intelligence]
			+ $creature[CreatureInfoKey::reBirthNum] * $creature[CreatureInfoKey::itgRebirth]
			+ $level * $creature[CreatureInfoKey::itgIcs];	
		$itg += $itg * ($creature[CreatureInfoKey::itgRatio]/10000);
		//$itg /= 100;		
		return array($stg, $agile, $itg);		
	}
	
	
	protected function getFightForceAtkFinal($creature)
	{		
		list($phyAtk, $killAtk, $mgcAtk, $phyDef, $killDef, $mgcDef) = array(0,0,0,0,0,0);
		
		if (isset($creature[CreatureInfoKey::phyAtkFinal]))
		{
			$phyAtk +=  $creature[CreatureInfoKey::phyAtkFinal];
		}

		if (isset($creature[CreatureInfoKey::killAtkFinal]))
		{
			$killAtk +=  $creature[CreatureInfoKey::killAtkFinal];
		}
		
		if (isset($creature[CreatureInfoKey::mgcAtkFinal]))
		{
			$mgcAtk +=  $creature[CreatureInfoKey::mgcAtkFinal];
		}
		
		if (isset($creature[CreatureInfoKey::phyDfsFinal]))
		{
			$phyDef +=  $creature[CreatureInfoKey::phyDfsFinal];
		}
		
		if (isset($creature[CreatureInfoKey::killDfsFinal]))
		{
			$killDef +=  $creature[CreatureInfoKey::killDfsFinal];
		}
		
		if (isset($creature[CreatureInfoKey::mgcDfsFinal]))
		{
			$mgcDef +=  $creature[CreatureInfoKey::mgcDfsFinal];
		}
		
		return array($phyAtk, $killAtk, $mgcAtk, $phyDef, $killDef, $mgcDef);
	}
	
	protected function calculateFightForceAtkDefBase($sanWei, $level, $creature)
	{
		list($stg, $agile, $itg) = $sanWei;
		//计算战斗力的时候需要除100
		$stg /= 100;
		$agile /= 100;
		$itg /= 100;
		
		$phyAtk = $stg * $creature[CreatureInfoKey::stgPhyAtkRatio]
			+ $creature[CreatureInfoKey::phyAttack]
			+ $level * $creature[CreatureInfoKey::phyAtkIcs];
		
		
		$killAtk = $agile * $creature[CreatureInfoKey::aglKillAtkRatio]
			+ $creature[CreatureInfoKey::killAttack]
			+ $level * $creature[CreatureInfoKey::killAtkIcs];		
				

		$mgcAtk = $itg * $creature[CreatureInfoKey::itgMgcAtkRatio]
			+ $creature[CreatureInfoKey::mgcAttack]
			+ $level * $creature[CreatureInfoKey::mgcAtkIcs];
		
		
		$phyDef = $stg * $creature[CreatureInfoKey::stgPhyDfsRatio]
			+ $creature[CreatureInfoKey::phyDefend]
			+ $level * $creature[CreatureInfoKey::phyDfsIcs];
		

		$killDef = $agile * $creature[CreatureInfoKey::aglKillDfsRatio]
			+ $creature[CreatureInfoKey::killDefend]
			+ $level * $creature[CreatureInfoKey::killDfsIcs];
		

		$mgcDef = $itg * $creature[CreatureInfoKey::itgMgcDfsRatio]
			+ $creature[CreatureInfoKey::mgcDefend]
			+ $level * $creature[CreatureInfoKey::mgcDfsIcs];				
			
		return array($phyAtk, $killAtk, $mgcAtk, $phyDef, $killDef, $mgcDef);
	}
	
	protected function calculateFightForceAtkDefRatio($level, $creature)
	{
		$phyAtkRatio = $creature[CreatureInfoKey::phyAtkRatio]
			+ $level * $creature[CreatureInfoKey::phyAtkRatioIcs];		

		$killAtkRatio = $creature[CreatureInfoKey::killAtkRatio]
			+ $level * $creature[CreatureInfoKey::killAtkRatioIcs];		

		$mgcAtkRatio = $creature[CreatureInfoKey::mgcAtkRatio]
			+ $level * $creature[CreatureInfoKey::mgcAtkRatioIcs];
			
		$phyDfsRatio = $creature[CreatureInfoKey::phyDfsRatio]
			+ $level * $creature[CreatureInfoKey::phyDfsRatioIcs];
			
		$killDfsRatio = $creature[CreatureInfoKey::killDfsRatio]
			+ $level * $creature[CreatureInfoKey::killDfsRatioIcs];
			
		$mgcDfsRatio = $creature[CreatureInfoKey::mgcDfsRatio]
			+ $level * $creature[CreatureInfoKey::mgcDfsRatioIcs];
			
		return array($phyAtkRatio, $killAtkRatio, $mgcAtkRatio, $phyDfsRatio, $killDfsRatio, $mgcDfsRatio);
	}

	protected function calculateProperty ($creature, $countfightForce)
	{
		$retCreature = array();
		//装备信息
		$retCreature['equipInfo'] = $creature['equipInfo'];
		$retCreature['daimonApple'] = $creature['daimonApple'];
		if (isset($creature['dress']))
		{
			$retCreature['dress'] = $creature['dress'];
			$retCreature['imageDress'] = $creature['imageDress'];
			$retCreature['show_dress']  = $creature['show_dress'];
		}

		$retCreature['arrSkill'] = $creature[CreatureInfoKey::arrSkill];
		$retCreature['rageSkill'] = $creature[CreatureInfoKey::rageAtkSkill];
		if (empty($creature[CreatureInfoKey::normalAtk]))
		{
			Logger::fatal('creature %d attack skill is empty', $this->id);
			throw new Exception('sys');
		}
		$retCreature['attackSkill'] = $creature[CreatureInfoKey::normalAtk][0];
		
		//这里应该单独一列比较合理, 临时搞成这样
		$arrSkill = array_slice($creature[CreatureInfoKey::normalAtk], 1);
		$retCreature['arrSkill'] = array_unique(array_merge($arrSkill, $retCreature['arrSkill']));		

		$level = $creature[CreatureInfoKey::level];
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::HP_BASE]] = $this->getMaxHp();

		//$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::HP_ADDITION]] = 0;

		list($stg, $agile, $itg) = $this->calculateSanWeiBase($creature);
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::STRENGHT_BASE]] = $stg;						
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::AGILE_BASE]] = $agile;
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::INTELLIGENCE_BASE]] = $itg;

		$fightForceBase = $this->calculateFightForceAtkDefBase(array($stg, $agile, $itg), $level, $creature);
		list ($retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::PHYSICAL_ATTACK_BASE]],
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::KILL_ATTACK_BASE]],
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::MAGIC_ATTACK_BASE]],
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::PHYSICAL_DEFEND_BASE]],
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::KILL_DEFEND_BASE]],
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::MAGIC_DEFEND_BASE]])
		 = $fightForceBase;
		 
		$fightForceRatio = $this->calculateFightForceAtkDefRatio($level, $creature);
		list ($retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::PHYSICAL_ATTACK_ADDITION]],		
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::KILL_ATTACK_ADDITION]],		
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::MAGIC_ATTACK_ADDITION]],		
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::PHYSICAL_DEFEND_ADDITION]],
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::KILL_DEFEND_ADDITION]],
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::MAGIC_DEFEND_ADDITION]])
		 = $fightForceRatio;
				
		$finalAtkDef = $finalAtkDef = $this->getFightForceAtkFinal($creature);
		list($retCreature[propertydef::$ARR_INDEX2KEY[PropertyDef::ABSOLUTE_PHYSICAL_ATTACK]],
				$retCreature[propertydef::$ARR_INDEX2KEY[PropertyDef::ABSOLUTE_KILL_ATTACK]],
				$retCreature[propertydef::$ARR_INDEX2KEY[PropertyDef::ABSOLUTE_MAGIC_ATTACK]],
				$retCreature[propertydef::$ARR_INDEX2KEY[PropertyDef::ABSOLUTE_PHYSICAL_DEFEND]],
				$retCreature[propertydef::$ARR_INDEX2KEY[PropertyDef::ABSOLUTE_KILL_DEFEND]],
				$retCreature[propertydef::$ARR_INDEX2KEY[PropertyDef::ABSOLUTE_MAGIC_DEFEND]],
				) = $finalAtkDef; 
		 
		if ($countfightForce)
		{
			
			$retCreature['fight_force'] = 
				$this->getFightForce_($fightForceBase, 
					$fightForceRatio, 
					$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::HP_BASE]],
					$finalAtkDef,
					$creature, 
					array($stg, $agile, $itg));

		}

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::RAGE_BASE]] =
			$creature[CreatureInfoKey::rage];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::HIT_BASE]] =
			$creature[CreatureInfoKey::hitRatingRatio]
			+ $level * $creature[CreatureInfoKey::hitrIncs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::FATAL_BASE]] =
			$creature[CreatureInfoKey::ftlAtkRatio]
			+ $level * $creature[CreatureInfoKey::ftlIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::PARRY_BASE]] =
			$creature[CreatureInfoKey::pryRatio]
			+ $level * $creature[CreatureInfoKey::pryIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::DODGE_BASE]] =
			$creature[CreatureInfoKey::dgeRatio]
			+ $level * $creature[CreatureInfoKey::dgeIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::WIND_ATTACK_BASE]] =
			$creature[CreatureInfoKey::windAttack]
			+ $level * $creature[CreatureInfoKey::windAtkIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::THUNDER_ATTACK_BASE]] =
			$creature[CreatureInfoKey::thdAttack]
			+ $level * $creature[CreatureInfoKey::thdAtkIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::WATER_ATTACK_BASE]] =
			$creature[CreatureInfoKey::wtrAttack]
			+ $level * $creature[CreatureInfoKey::wtrAtkIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::FIRE_ATTACK_BASE]] =
			$creature[CreatureInfoKey::fireAttack]
			+ $level * $creature[CreatureInfoKey::fireAtkIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::WIND_DEFEND_BASE]] =
			$creature[CreatureInfoKey::windResistance]
			+ $level * $creature[CreatureInfoKey::windRstIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::THUNDER_DEFEND_BASE]] =
			$creature[CreatureInfoKey::thdResistance]
			+ $level * $creature[CreatureInfoKey::thdRstIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::WATER_DEFEND_BASE]] =
			$creature[CreatureInfoKey::wtrResistance]
			+ $level * $creature[CreatureInfoKey::wtrRstIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::FIRE_DEFEND_BASE]] =
			$creature[CreatureInfoKey::fireResistance]
			+ $level * $creature[CreatureInfoKey::fireRstIcs];		

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::WIND_ATTACK_ADDITION]] =
			$creature[CreatureInfoKey::windAtkRatio]
			+ $level * $creature[CreatureInfoKey::windAtkRatioIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::THUNDER_ATTACK_ADDITION]] =
			$creature[CreatureInfoKey::thdAtkRatio]
			+ $level * $creature[CreatureInfoKey::thdAtkRatioIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::WATER_ATTACK_ADDITION]] =
			$creature[CreatureInfoKey::wtrAtkRatio]
			+ $level * $creature[CreatureInfoKey::wtrAtkRatioIcs];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::FIRE_ATTACK_ADDITION]] =
			$creature[CreatureInfoKey::fireAtkRatio]
			+ $level * $creature[CreatureInfoKey::fireAtkRatioIcs];

		/*
		 * 物理伤害倍率=英雄固定物理伤害倍率+英雄当前等级*英雄物理伤害倍率成长+
		 * 阵型提供的物理伤害倍率+
		 * 科技提供的物理伤害倍率+
		 * 英雄最终力量*力量物理伤害倍率系数
		 */
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::PHYSICAL_ATTACK_RATIO]] =
			$creature[CreatureInfoKey::phyFDmgRatio]
			+ $level * $creature[CreatureInfoKey::phyDmgRatioIcs]
			+ $stg * $creature[CreatureInfoKey::stgPhyDmgRatio];

		//PHYSICAL_DEFEND_RATIO del

		/*
		 * 必杀伤害倍率=英雄固定必杀伤害倍率+
		 * 英雄当前等级*英雄必杀伤害倍率成长+
		 * 阵型提供的必杀伤害倍率+科技提供的必杀伤害倍率+
		 * 英雄最终敏捷*敏捷必杀伤害倍率系数
		 */
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::KILL_ATTACK_RATIO]] =
			$creature[CreatureInfoKey::killFDmgRatio]
			+ $level * $creature[CreatureInfoKey::killDmgRatioIcs]
			+ $agile * $creature[CreatureInfoKey::aglKillDmgRatio];

		//KILL_DEFEND_RATIO del

		/*
		 * 魔法伤害倍率=英雄固定魔法伤害倍率+
		 * 英雄当前等级*英雄魔法伤害倍率成长+
		 * 阵型提供的魔法伤害倍率+科技提供的魔法伤害倍率+
		 * 英雄最终智慧*智慧魔法伤害倍率系数
		 */
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::MAGIC_ATTACK_RATIO]] =
			$creature[CreatureInfoKey::mgcFDmgRatio]
			+ $level * $creature[CreatureInfoKey::mgcDmgRatioIcs]
			+ $itg * $creature[CreatureInfoKey::itgMgcEptDmgRatio];

		//MAGIC_DEFEND_RATIO

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::ABSOLUTE_ATTACK]] =
			$creature[CreatureInfoKey::absoluteDamage];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::ABSOLUTE_DEFEND]] =
			$creature[CreatureInfoKey::absoluteDefend];

//		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::STRENGTH_ADDITION]] = 0;
//
//		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::AGILE_ADDITON]] = 0;
//
//		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::INTELLIGENCE_ADDITION]] = 0;

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::CHARM_SKILL]] = $creature[CreatureInfoKey::charmID];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::PARRY_SKILL]] = $creature[CreatureInfoKey::parryID];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::CHAOS_SKILL]] = $creature[CreatureInfoKey::choasID];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::IMMUNED_BUFFER_LIST]] = $creature[CreatureInfoKey::immuneBufferID];

		/*
		 * 必杀免伤倍率=英雄固定必杀免伤倍率+
		 * 英雄当前等级*英雄必杀免伤倍率成长+
		 * 阵型提供的必杀免伤倍率+科技提供的必杀免伤倍率+
		 * 英雄最终敏捷*敏捷必杀免伤倍率系数
		 */
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::KILL_DAMAGE_IGNORE_RATIO]] =
			$creature[CreatureInfoKey::killFEptRatio]
			+ $level * $creature[CreatureInfoKey::killEptRatioIcs]
			+ $agile * $creature[CreatureInfoKey::aglKillEptDmgRatio];

		/*
		 * 魔法免伤倍率=英雄固定魔法免伤倍率+
		 * 英雄当前等级*英雄魔法免伤倍率成长+
		 * 阵型提供的魔法免伤倍率+
		 * 科技提供的魔法免伤倍率+
		 * 英雄最终智慧*智慧魔法免伤倍率系数
		 */
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::MAGIC_DAMAGE_IGNORE_RATIO]] =
			$creature[CreatureInfoKey::mgcFEptRatio]
			+ $level * $creature[CreatureInfoKey::mgcDmgRatioIcs]
			+ $itg * $creature[CreatureInfoKey::itgMgcEptDmgRatio];

		/*
		 * 物理免伤倍率=英雄固定物理免伤倍率+英雄当前等级*英雄物理免伤倍率成长+
		 * 阵型提供的物理免伤倍率+
		 * 科技提供的物理免伤倍率+
		 * 英雄最终力量*力量物理免伤倍率系数
		 */
		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::PHYSICAL_DAMAGE_IGNORE_RATIO]] =
			$creature[CreatureInfoKey::phyFEptRatio]
			+ $level * $creature[CreatureInfoKey::phyEptRatioIcs]
			+ $stg * $creature[CreatureInfoKey::stgPhyEptRatio];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::RAGE_GET_BASE]] = $creature[CreatureInfoKey::rageGetBase];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::RAGE_GET_RATIO]] = $creature[CreatureInfoKey::rageGetRatio];

		$retCreature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::RAGE_GET_AMEND]] = $creature[CreatureInfoKey::rageGetAmend];

		$retCreature['level'] = $level;
		
		$modifyArr = array('modifyPhysicalAttack' => CreatureInfoKey::normalAttRatio, 
				'modifyPhysicalDefend' => CreatureInfoKey::normalDefRatio, 
				'modifyRageAttack' => CreatureInfoKey::ragerAttRatio,
				'modifyRageDefend' => CreatureInfoKey::ragerDefRatio, 
				'modifyCureRatio' => CreatureInfoKey::treatRatio, 
				'modifyBeCuredRatio' => CreatureInfoKey::treatedRatio);
		
		foreach ($modifyArr as $k=>$v)
		{
			if (isset($creature[$v]))
			{
				$retCreature[$k] = $creature[$v];
			}
			else
			{
				$retCreature[$k] = 0;
			}
		}
		
		$retCreature['absoluteAttackRatio'] = 0;
		$retCreature['absoluteDefendRatio'] = 0;
		$retCreature['baseHtid'] = $this->getBaseHtid();
		
		return $retCreature;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */