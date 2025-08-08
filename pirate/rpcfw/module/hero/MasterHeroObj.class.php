<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MasterHeroObj.class.php 39920 2013-03-05 06:57:28Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/MasterHeroObj.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-05 14:57:28 +0800 (二, 2013-03-05) $
 * @version $Revision: 39920 $
 * @brief 
 *  
 **/




/**
 * Enter description here ...
 * @author idyll
 *
 */
class MasterHeroObj extends HeroObj
{
	public function __construct ($attr)
	{
        parent::__construct($attr);
	}
	
	public function rebirth()
	{
		Logger::warning('cannot rebirth for master hero');
		throw new Exception('fake');
	}

	public function recruit()
	{
		Logger::warning('cannot recruit for master hero');
		throw new Exception('fake');
	}

	public function fire()
	{
		Logger::warning('cannot fire for master hero');
		throw new Exception('fake');
	}	
	
	//---------------以下新的接口 
	
	public function learnSkill ($skillId)
	{		
		$tsfNum = $this->getTransferNum();
		$htid = $this->getHtid();
		$cfg = btstore_get()->MASTER_HEROES_TRANSFER[$htid][$tsfNum];
		$needBelly = 0;
		$indexStr = '';
		//普通技能
		if (isset($cfg['transfer_normalSkills'][$skillId]))
		{
			$indexStr = 'learned_normal_skills';
			$needBelly = $cfg['transfer_normalSkills'][$skillId];
		}
		else if (isset($cfg['transfer_rageSkills'][$skillId]))
		{
			$indexStr = 'learned_rage_skills';
			$needBelly = $cfg['transfer_rageSkills'][$skillId];
		}
		else
		{
			Logger::warning('the skill %d can not been learned.', $skillId);
			throw new Exception('fake');
		}
		
		//已经学过了
		if (in_array($skillId, $this->attrModify['va_hero']['master'][$indexStr]))
		{
			Logger::warning('the skill %d has been learn for hero htid %d', $skillId, $this->attrModify['htid']);
			throw new Exception('fake');
		}

		//sub belly
		$user = EnUser::getUserObj($this->attrModify['uid']);
		$user->subBelly($needBelly);		
		$this->attrModify['va_hero']['master'][$indexStr][] = $skillId;	
	}
	
	public function resetUsingSkill()
	{
		$master = $this->attrModify['va_hero']['master'];
		if (!Util::isSameDay($master['using_skill_time']))
		{
			$master['using_skill_time'] = 0;
			$master['using_skill_num'] = 0;
		}
		$this->attrModify['va_hero']['master'] = $master;
	}
		
	//装备技能
	public function usingSkill($skillId, $type)
	{
		$master = $this->attrModify['va_hero']['master'];
		if ($master['using_skill']==$skillId || $master['using_normal_skill']==$skillId)
		{
			return;
		}
		
		$this->resetUsingSkill();
		
		//装备费用为0, 不累计
		$master['using_skill_num'] += 1;
		$master['using_skill_time'] = Util::getTime();
		
		//判断是否是已学习
		switch ($type)
		{
			case 0:
				if (in_array($skillId, $master['learned_rage_skills']))
				{
					$master['using_skill'] = $skillId;
				}
				else
				{
					Logger::warning('the skill %d has not learned', $skillId);
					throw new Exception('fake');
				}		
				break;
			case 1:
				if (in_array($skillId, $master['learned_normal_skills']))
				{
					$master['using_normal_skill'] = $skillId;
				}
				else
				{
					Logger::warning('the skill %d has not learned', $skillId);
					throw new Exception('fake');
				}		
				break;			
		}
		
		$this->attrModify['va_hero']['master'] = $master;
	}
	
	private static function throwFake($is, $str)
	{
		if ($is)
		{
			Logger::warning('fail to transfer, bacause of %s', $str);
			throw new Exception('fake');
		}
	}
	
	//转职
	public function transfer()
	{
		$master = $this->attrModify['va_hero']['master'];
		$htid = $this->getHtid();		
		if (HeroConf::MAX_TRANSFER_NUM == $this->getTransferNum())
		{
			Logger::warning('fail to transfer, transfer num is max %d', HeroConf::MAX_TRANSFER_NUM);
			throw new Exception('fake');			
		}
		
		$cfg = btstore_get()->MASTER_HEROES_TRANSFER[$htid][$master['transfer_num']+1];
		
		$user = EnUser::getUserObj($this->attrModify['uid']);
		//level
		self::throwFake($cfg['need_lv'] > $user->getLevel(), 'level');
		self::throwFake($cfg['need_prestige'] > $user->getPrestige(), 'prestige');
		
		$costItem = $cfg['cost_item']->toArray();
		if (!empty($costItem))
		{
			$bag = BagManager::getInstance()->getBag();
			$ret = $bag->deleteItemsByTemplateID($cfg['cost_item']);
			self::throwFake($ret, 'item');
			//$bag->update();
		}		
		
		self::throwFake(!$user->subBelly($cfg['cost_belly']), 'belly');
		self::throwFake(!$user->subExperience($cfg['cost_experience']), 'experience');
		
		$master['transfer_num'] += 1;
		// 开启栏位
		// $this->openDaimonPos($master['transfer_num']);
		$this->attrModify['va_hero']['master'] = $master;
	}	
	
	public function getMasterHeroProperty()
	{
		return $this->attrModify['va_hero']['master'];
	}
	
	public function learnSkillFromOther ($hid, $skillId)
	{
		if ($hid==$this->attrModify['hid'])
		{
			Logger::warning('learn skill from himself');
			throw new Exception('fake');
		}
		
		//好感度等级
		$heroObj = EnUser::getUserObj()->getHeroObj($hid);		
		$arrSkill = $heroObj->canLearnedGoodwillSkill($this->getHtid());
		if (!in_array($skillId, $arrSkill))
		{
			Logger::warning('fail to learn skill %d, all skill:%s', $skillId, $arrSkill);
			throw new Exception('fake');
		}
		
		//已经学过了
		if (in_array($skillId, $this->attrModify['va_hero']['master']['learned_rage_skills']))
		{
			Logger::warning('the skill %d has been learn for hero htid %d', $skillId, $this->attrModify['htid']);
			throw new Exception('fake');
		}
				
		$this->attrModify['va_hero']['master']['learned_rage_skills'][] = $skillId;	
	}
	
	//是否需要重新计算总经验
	protected function needSumAllExp()
	{
		if ($this->attrModify['exp']==0 && $this->attrModify['level']==1)
		{
			return false;
		}
		 
		if ($this->attrModify['all_exp']==0)
		{
			return true;
		}
		return false;
	}
	
	//计算总经验
	protected function sumAllExp()
	{
		$expTblId = btstore_get()->CREATURES[$this->attrModify['htid']][CreatureInfoKey::expId];
		//读取旧配置，计算出来经验是多少
		foreach (btstore_get()->EXP_TBL_OLD[$expTblId] as $cfgLevel => $cfgExp)
		{
			if ($this->attrModify['level'] < $cfgLevel)
			{
				break;
			}
			 
			$this->attrModify['all_exp'] += $cfgExp;
		}
		$this->attrModify['all_exp'] += $this->attrModify['exp'];
	}
	
	public function fixLevel()
	{
		$modify = false;
		if ($this->needSumAllExp())
		{
			$this->sumAllExp();
			$modify = true;
		}
		
		$expTblId = btstore_get()->CREATURES[$this->attrModify['htid']][CreatureInfoKey::expId];
		$allExp = $this->attrModify['all_exp'];
		$oldLevel = $this->attrModify['level'];
		$oldExp = $this->attrModify['exp'];
		
		foreach (btstore_get()->EXP_TBL[$expTblId] as $cfgLevel => $cfgExp)
		{
			//if ($allExp = )
			$allExp -= $cfgExp;
			if ($allExp < 0)
			{
				break;
			}
			
			if ($cfgLevel > UserConf::MAX_LEVEL)
			{
				break;
			}
			
			$this->attrModify['level'] = $cfgLevel;
			$this->attrModify['exp'] = $allExp;
		}
		
		if ($this->attrModify['level'] < $oldLevel)
		{
			Logger::fatal('level degrade');
			
			if(!FrameworkConfig::DEBUG)
			{
				throw new Exception('close');
			}
		}
		
		if ($this->attrModify['level'] != $oldLevel)
		{
			$this->attrModify['upgrade_time'] = Util::getTime();
		}
		
		if (($this->attrModify['level'] != $oldLevel) || ($this->attrModify['exp']!=$oldExp))
		{
			Logger::info('fix level from %d to %d, fix exp from %d to %d', 
				$oldLevel, $this->attrModify['level'],
				$oldExp, $this->attrModify['exp']);
			$modify = true;
		}
		$this->openDaimonPos($this->attrModify['level']);
		return $modify;
	}
	
	public function getMaxLevel()
	{
		return UserConf::MAX_LEVEL;
	}
	
	protected function levelChange()
	{
		if ($this->isLevelChange)
		{
			$user = EnUser::getUserObj();
			$user->fullBloodPackage();
			
			if ($this->getLevel() >= $this->getMaxLevel())
			{
				$this->attrModify['all_exp'] -= $this->attrModify['exp'];	
				$this->attrModify['exp'] = 0;
			}
			
			parent::levelChange();
		}
	}	
	
	protected function icsExp($num)
	{
		parent::icsExp($num);
		$this->attrModify['all_exp'] += $num;
	}
	
	public function getAllExp()
	{
		return $this->attrModify['all_exp'] ;
	}
	
	/**
	 * @deprecated
	 * @param unknown_type $num
	 */
	public function setAllExp($num)
	{
		$this->attrModify['all_exp'] = 0;
	}
	
	public function addExp ($num)
	{
		if ($this->needSumAllExp())
		{
			$this->sumAllExp();
			$this->fixLevel();			
		}
		
		return parent::addExp($num);
	}
	
	public function learnNormalSkill($skillId)
	{
		// 已经学过了
		if (in_array($skillId, $this->attrModify['va_hero']['master']['learned_normal_skills']))
		{
			Logger::warning('the skill %d has been learn for hero htid %d', $skillId, $this->attrModify['htid']);
			throw new Exception('fake');
		}
				
		$this->attrModify['va_hero']['master']['learned_normal_skills'][] = $skillId;	
	}
	public function learnRageSkill($skillId)
	{
		// 已经学过了
		if (in_array($skillId, $this->attrModify['va_hero']['master']['learned_rage_skills']))
		{
			Logger::warning('the skill %d has been learn for hero htid %d', $skillId, $this->attrModify['htid']);
			throw new Exception('fake');
		}
				
		$this->attrModify['va_hero']['master']['learned_rage_skills'][] = $skillId;	
	}	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */