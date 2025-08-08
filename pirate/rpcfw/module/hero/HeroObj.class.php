<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroObj.class.php 39943 2013-03-05 07:58:02Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/HeroObj.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-05 15:58:02 +0800 (二, 2013-03-05) $
 * @version $Revision: 39943 $
 * @brief
 *
 **/

class HeroObj extends OtherHeroObj
{
	//for notify task
	protected $isLevelChange = false;
	
	//goodwill level change
	protected $isGWLevelChange = false;
	
	protected static $affectedBattleField = array('htid', 'level', 'rebirthNum', 'va_hero');

	public function __construct ($attr)
	{
        parent::__construct($attr);
	}

    public function modifyHeroByOther($arrField)
    {
        foreach ($arrField as $attrName=>$num)
		{
            if($attrName=='exp')
            {
                $this->addExp($num);
            }
            else if ($attrName=='curHp')
            {
                $this->subHp(-$num);
            }
            else
            {
                Logger::fatal('can not modify other user attribute %s', $attrName);
              	throw new Exception('fake');
            }
		}

    }
    
    public function subHp($num)
    {
    	$this->attrModify['curHp'] -= $num;
    }
    
    
    public function getMaxLevel()
    {
	   	return min(array(EnUser::getUserObj()->getMasterHeroLevel(), HeroConf::MAX_LEVEL));
    }
    
    protected function levelChange()
    {
    	if ($this->isLevelChange)
    	{
    		$this->attrModify['curHp'] = $this->getMaxHp();
    		//最大等级后把经验设置为0    	
    		if ($this->attrModify['level'] >= $this->getMaxLevel())
    		{
    			$this->attrModify['exp'] = 0;
    		}	
    	}
    }
    
    protected function icsExp($num)
    {
    	$this->attrModify['exp'] += $num;
    }
    

    public function addExp ($num)
	{
		$num = intval($num);
		$maxLevel = $this->getMaxLevel();
		
		//最大等级后经验不再增加
		if ($this->attrModify['level'] >= $maxLevel)
		{
			return true;
		}

		$this->icsExp($num);

		$expTblId = btstore_get()->CREATURES[$this->attrModify['htid']][CreatureInfoKey::expId];
		$expTbl = btstore_get()->EXP_TBL[$expTblId];
		$newLevel = $this->attrModify['level'];
		while (true)
		{
			//可以升级
			$upgradeExp = $expTbl[$newLevel+1];
			if ($upgradeExp <= $this->attrModify['exp'])
			{
				$this->attrModify['exp'] -= $upgradeExp;
				$newLevel += 1;
				$this->attrModify['level'] = $newLevel;
				$this->isLevelChange = true;
				$this->attrModify['upgrade_time'] = Util::getTime();				
			}
			else
			{
				break;
			}

			//到最大等级后不能再升级
			if ($newLevel >= $maxLevel)
			{
				break;
			}
		}
		
		$this->levelChange();
		
		return true;
	}


	/**
	 * 转生
	 * @param unknown_type $htid
	 * @throws Exception
	 */
	public function rebirth ()
	{
		//已经招募的英雄才能转生
		if ($this->attrModify['status']!=HeroDef::STATUS_RECRUIT)
		{
			Logger::warning('fail to rebirth, the hero(htid:%d) is not recruited', $this->attrModify['htid']);
			throw new Exception('fake');
		}
		
		if ($this->attrModify['rebirthNum'] >= HeroConf::MAX_REBIRTH_NUM)
		{
			Logger::warning('fail to rebirth, the rebirth num is max %d', HeroConf::MAX_REBIRTH_NUM);
			throw new Exception('fake');
		}
		
		$cfg = btstore_get()->HERO_REBIRTH[$this->attrModify['rebirthNum']+1];
		$needLevel = $cfg['need_level'];
		$needItem = $cfg['need_item'];
		if ($this->getLevel() < $needLevel)
		{
			Logger::warning('fail to rebirth, the hero(htid:%d) level is not enough', $this->attrModify['htid']);
			throw new Exception('fake');
		}
		
		if ($needItem!=0)
		{
			$bag = BagManager::getInstance()->getBag();
			$ret = $bag->deleteItembyTemplateID($needItem, 1);
			if (!$ret)
			{
				Logger::warning('fail to rebirth, lack item %d', $needItem);
				throw new Exception('fake');
			}
		}
		
		$HeroConfig = btstore_get()->CREATURES[$this->attrModify['htid']];
		$dfLevel = $this->attrModify['level'] - $needLevel;

		$this->attrModify['rebirthNum'] += 1;
		$this->isLevelChange = true;
		// 开启栏位
		$this->openDaimonPos($this->attrModify['rebirthNum']);

		//超出转生所需要的等级，返还多出的经验
		$expTblId = $HeroConfig[CreatureInfoKey::expId];
		//多出的经验
		$expMore = 0;
		$expTbl = btstore_get()->EXP_TBL[$expTblId];
		for ($i=$needLevel+1; $i<=$this->attrModify['level']; ++$i)
		{
			$expMore += $expTbl[$i];
		}
		$this->attrModify['level'] = 1;
		$this->attrModify['upgrade_time'] = Util::getTime();
		$this->addExp($expMore);

		return $this->attrModify['rebirthNum'];
	}
	
	//普通英雄用rebirthNum开启恶魔果实位置， 主角英雄用transferNum开启
	protected function openDaimonPos($rebirthNumOrTransferNum)
	{
		$HeroConfig = btstore_get()->CREATURES[$this->attrModify['htid']];
		// 开启栏位
		foreach ($HeroConfig[CreatureInfoKey::devilFruitSkill] as $pos => $rebirthDmTid)
		{
			$needRebirth = $rebirthDmTid[0];
			if ($needRebirth==$rebirthNumOrTransferNum)
			{
				if (!isset($this->attrModify['va_hero']['daimonApple'][$pos]))
				{
					$this->attrModify['va_hero']['daimonApple'][$pos] = 0;
				}
			}
		}
	}
	
	public function openDaimonAppleByItem($position_id)
	{
		if (!isset($this->attrModify['va_hero']['daimonApple'][$position_id]))
		{
			$this->attrModify['va_hero']['daimonApple'][$position_id] = 0;
		}
	}

	/* (non-PHPdoc)
	 * @see IHero::recruit()
	 */
	public function recruit ()
	{
        $userObj = EnUser::getUserObj();

		//check recuit num
		$curRctNum = $userObj->getRecruitHeroesNum();
		$canRctNum = $userObj->getCanRecruitHeroNum();
		if ($curRctNum >= $canRctNum)
		{
			Logger::warning('fail to recuit hero, the number of recruit hero is max:%d', $canRctNum);
			throw new Exception('fake');
		}

		//检查是否能招募
		if ($this->isRecruit())
		{
			 Logger::warning('the hero(htid:%d) has been recruited', $this->attrModify['htid']);
             throw new Exception('fake');			
		}
		
		if ($userObj->isHeroConvert($this->getHtid()))
		{
			Logger::warning('fail to recruit, the hero is converted');
			throw new Exception('fake');
		}

		//根据招募价格扣钱
		$baseInfo = btstore_get()->CREATURES[$this->attrModify['htid']];
		$price = $baseInfo[CreatureInfoKey::price];
		$userObj = EnUser::getUserObj();
		if (!$userObj->subBelly($price))
		{
			Logger::warning('belly isnot enough for recruiting');
			throw new Exception('fake');
		}

		//保存到数据库
		$arrField = array('status' => HeroDef::STATUS_RECRUIT);
		$this->attrModify['status'] = HeroDef::STATUS_RECRUIT;
		if ($this->attrModify['hid']!=0)
		{
			$this->attrModify['status'] = HeroDef::STATUS_RECRUIT;
		}
		else
		{
			//hid=0 表示这个英雄重来没有招募过								
			$uid = RPCContext::getInstance()->getSession('global.uid');
			$hid = HeroLogic::insertFromTemplate($uid, $this->attrModify['htid'], $arrField);
			$this->attr = HeroLogic::getHero($hid);
			$this->attrModify = $this->attr;
			//此时把它的血量设置为最大值，而不减血库
			$this->setHp($this->getMaxHp());
		}

		//保存到招募英雄顺序表里面
		//可能数据出错，已在招募英雄表里面
		if (!in_array($this->attrModify['hid'],$userObj->getRctHeroOrder()))
		{
			$userObj->addHeroToRctHeroOrder($this->attrModify['hid']);
		}
		
	}


	/* (non-PHPdoc)
	 * @see IHero::fire()
	 */
	public function fire ()
	{
		$arrRet = array('ret'=>'ok', 'grid'=>array());		
		
		$userObj = EnUser::getUserObj();		
		//check
		if (!$this->isRecruit())
		{
			//可能为数据出错， 没有从user的招募表里面删除
			
			//不在招募表里面			
			if (!in_array($this->attrModify['hid'],$userObj->getRctHeroOrder()))
			{
				Logger::warning('hero(htid:%d) isnot recruited, cannot be fired.', $this->attrModify['htid']);
				throw new Exception('fake');
			}			
			//从招募英雄顺序表 里面删除		
			$userObj->delHeroFromRctHeroOrder($this->attrModify['hid']);
			Logger::fatal('fixed data. fire hero hid from recruit hero order list:%d', $this->getHid());
			return $arrRet;
		}
		
		//检查是否训练
		if (EnTrain::isTraining($this->getHid()))
		{
			Logger::warning('fail to fire, the hero %d is training', $this->getHid());
			throw new Exception('fake');
		}

		//扒装备, 需要在外面update bag
		if (!$this->removeAllArming()
				||!$this->removeAllDress()
				||!$this->removeAllJewelry()
				||!$this->removeAllElement())
		{
			$arrRet['ret'] = 'bag_full';
			return $arrRet;
		}

		$hid = $this->attrModify['hid'];
		//从阵型表里面删除此英雄
		EnFormation::delHeroFromFormation($hid);

		//update db
		$this->attrModify['status'] = HeroDef::STATUS_PUB;

		//从招募英雄顺序表 里面删除		
		//可能数据出错，不在招募表里面
		if (in_array($this->attrModify['hid'],$userObj->getRctHeroOrder()))
		{
			$userObj->delHeroFromRctHeroOrder($this->attrModify['hid']);
		}		
		
		return $arrRet;
	}

	//更新
	public function update ()
	{
		//hid=0 表示这个英雄重来没有招募过
		if ($this->attrModify['hid']==0)
		{
			unset($this->attrModify['hid']);
			$hid = HeroLogic::insertFromTemplate($this->attrModify['uid'], $this->attrModify['htid'], $this->attrModify);
			$this->attrModify = HeroLogic::getHero($hid);
			$this->attr = $this->attrModify;       
		}		
		else
		{
			$arrField = $this->getModifyAttr();
			if (!empty($arrField))
			{				
				$this->attr = $this->attrModify;		
				HeroDao::update($this->attrModify['hid'], $arrField);
				
				$affectedField = array_intersect(array_keys($arrField), self::$affectedBattleField);
				//更新了影响战斗的字段
				if (!empty($affectedField) 
						&& EnFormation::isInCurFormation($this->attrModify['hid']))
				{
					EnUser::modifyBattleInfo();
				}
			} 
		}		

        $userObj = EnUser::getInstance();
		//用户在线，通知任务系统
		if ($userObj->isOnline())
		{
			if ($this->isLevelChange)
			{
				TaskNotify::heroUpgrade();				
				//主英雄
				if ($this->isMasterHero())
				{
					RPCContext::getInstance()->setSession('global.level', $this->getLevel());
					TaskNotify::userLevelChange();
					EnPractice::changePracticeEfficiency($this->getLevel());
       				EnTalks::openTalksWindow($userObj->getUid());
       				FormationLogic::openNewFormation($this->getLevel());
					SciTechLogic::openNewSciTech($this->getLevel());
					
					// 通知成就系统
					EnAchievements::notify($this->attrModify['uid'], 
						AchievementsDef::LEVEL, $this->getLevel());					
				}
				
				//成就系统
				EnAchievements::notify($this->attrModify['uid'], AchievementsDef::HEROS_LEVEL, $this->attrModify['level']);				
				EnAchievements::notify($this->attrModify['uid'], AchievementsDef::HERO_LEVEL, $this->attrModify['htid'], $this->attrModify['level']);
				$this->isLevelChange = false;
			}
					

			if ($this->isGWLevelChange)
			{
				$gwLevel = &$this->attrModify['va_hero']['goodwill']['level'];
				//成就
				$srcHtid = btstore_get()->CREATURES[$this->getHtid()][CreatureInfoKey::modelId]; 
				EnAchievements::notify($this->attrModify['uid'], 
					AchievementsDef::HERO_GOOD_WILL_LV, 
					$srcHtid,
					$gwLevel);
				EnAchievements::notify($this->attrModify['uid'], AchievementsDef::GOOD_WILL_LEVEL, $gwLevel);
				$this->isGWLevelChange = false;				
			}
		}
	}

	public function addDaimonApple ($item_id, $position_id)
	{
		$item_manager = ItemManager::getInstance();
		$item = $item_manager->getItem($item_id);
		//使用的物品类型是否为恶魔果实
		if ($item === NULL || $item->getItemType() != ItemDef::ITEM_DAIMONAPPLE)
		{
			Logger::warning('item:%d is not exist in system or is not a daimonapple!', $item_id);
			return FALSE;
		}

		$bag = BagManager::getInstance()->getBag();
		//物品是否存在于背包
		if ($bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID)
		{
			Logger::warning('item_id:%d is not in bag!', $item_id);
			return FALSE;
		}

		Logger::DEBUG('daimonapple:%s', $this->attrModify['va_hero']['daimonApple']);

		//当前的恶魔果实栏位是否开启
		if (!isset($this->attrModify['va_hero']['daimonApple'][$position_id]))
		{
			Logger::warning('invalid daimonapple position!');
			return FALSE;
		}
		else
		{
			$old_item_id = $this->attrModify['va_hero']['daimonApple'][$position_id];
			if ($old_item_id != BagDef::ITEM_ID_NO_ITEM)
			{
				Logger::warning("daimonapple position:%d has item:%d", $position_id, $old_item_id);
				return FALSE;
			}

			//将已经装备的恶魔果实从背包中移除
			$bag->removeItem($item_id);

			//背包数据
			$bag_modify = $bag->update();

			//更新恶魔果实栏位数据
			$this->attrModify['va_hero']['daimonApple'][$position_id] = $item_id;	
			
			return TRUE;
		}
	}

	public function removeDaimonApple ($position_id, $type)
	{
		$return = array('remove_success' => FALSE, 'gold'=>0);

		//当前的恶魔果实栏位是否开启
		if (!isset($this->attrModify['va_hero']['daimonApple'][$position_id]))
		{
			Logger::warning('invalid daimonapple position!');
			return $return;
		}

		$item_id = $this->attrModify['va_hero']['daimonApple'][$position_id];

		if ($item_id == BagDef::ITEM_ID_NO_ITEM)
		{
			Logger::warning('no item in daimonapple position:%d', $position_id);
			return $return;
		}

		$item_manager = ItemManager::getInstance();
		$item = $item_manager->getItem($item_id);
		if ($item->canErasure() == FALSE)
		{
			Logger::warning("daimon apple item_id:%d can not erasure!", $item_id);
			return $return;
		}

		$item_req = $item->getDaimonAppleReq();
		$bag = BagManager::getInstance()->getBag();
		//扣除daimon apple fee
		$user = EnUser::getInstance();
		switch ($type)
		{
			case 1: //Item
				if ($bag->deleteItemsByTemplateID($item_req[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_ITEMS]->toArray()) == FALSE )
				{
					Logger::warning('no enough item!:%s', $item_req[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_ITEMS]->toArray());
					return $return;
				}
				break;
			case 2: //Belly
				if ($user->subBelly($item_req[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_BELLY]) == FALSE )
				{
					Logger::warning('no enough belly!%d', $item_req[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_BELLY]);
					return $return;
				}
				break;
			case 3: //Gold
				if ($user->subGold($item_req[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_GOLD]) == FALSE )
				{
					Logger::warning('no enough Gold!%d', $item_req[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_GOLD]);
					return $return;
				}		
				$return['gold'] = $item_req[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_GOLD];
				break;
		}


		//将旧的恶魔果实从物品系统中移除
		$bag->addItem($item_id);

		$this->attrModify['va_hero']['daimonApple'][$position_id] = ItemDef::ITEM_ID_NO_ITEM;
		$return['remove_success'] = TRUE;
		$return['bag_modify'] = $bag->update();
		
		return $return;
	}

	public function setArmingByPostionNoCheck($arm_position, $item_id)
	{
		if (!isset($this->attrModify['va_hero']['arming'][$arm_position]))
		{
			throw new Exception('invalid arm position!');
		}
		else
		{							
			$this->attrModify['va_hero']['arming'][$arm_position] = $item_id;
			return TRUE;
		}
	}

	/**
	 *
	 * 装备物品
	 *
	 * @param int $arm_position
	 * @param int $item_id
	 *
	 * @return boolean
	 */
	public function setArmingByPosition ($arm_position, $item_id)
	{
		if (!isset($this->attrModify['va_hero']['arming'][$arm_position]))
		{
			throw new Exception('invalid arm position!');
		}
		else
		{
			if ( $item_id != ItemDef::ITEM_ID_NO_ITEM )
			{
				$item_manager = ItemManager::getInstance();
				$item = $item_manager->getItem($item_id);
				//英雄等级需求
				$equipReq = $item->equipReq();
				$hero_level_req = $equipReq[ItemDef::ITEM_ATTR_NAME_HERO_LEVEL];
				//对于主角，则只需要判断等级
				if ($this->isMasterHero()&& $this->getLevel() < $hero_level_req )
				{
					Logger::DEBUG('hero level is low!req:%d!', $hero_level_req);
					return FALSE;
				}
				//对于其他英雄则等级或转生满足其一即可
				$hero_rebirth_num = $equipReq[ItemDef::ITEM_ATTR_NAME_REBIRTH_NUM];
				if (!$this->isMasterHero())
				{
					if( !($this->getLevel() >= $hero_level_req||$this->getRebirthNum()>=$hero_rebirth_num))
					{
						Logger::DEBUG('hero level or rebirth is low!req:%d! rebirthnum:%d!', $hero_level_req,$hero_rebirth_num);
						return FALSE;
					}
				}

				//英雄职业需求
				$hero_vocation_req = $equipReq[ItemDef::ITEM_ATTR_NAME_HERO_VOCATION];
				if ( $hero_vocation_req != 0 && $this->getVocation() != $hero_vocation_req )
				{
					Logger::DEBUG('hero vocation miss match!req:%d', $hero_vocation_req);
					return FALSE;
				}
			}
			

			$this->attrModify['va_hero']['arming'][$arm_position] = $item_id;
			return TRUE;
		}
	}
	
	public function setDressByPosition ($dress_position, $item_id)
	{
		if (!isset($this->attrModify['va_hero']['dress'][$dress_position]))
		{
			$this->fixDressPosition();
			if (!isset($this->attrModify['va_hero']['dress'][$dress_position]))
			{
				throw new Exception('invalid dress position!');
			}
		}
		
		if ($item_id != ItemDef::ITEM_ID_NO_ITEM)
		{
			$item_manager = ItemManager::getInstance();
			$item = $item_manager->getItem($item_id);

			$equipReq = $item->equipReq();
			$hero_level_req = $equipReq[ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_LVLIMIT];
			if ($this->getLevel() < $hero_level_req)
			{
				Logger::warning('hero level is low!req:%d!', $hero_level_req);
				return FALSE;
			}
			
			$arrHtidReq = $equipReq[ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_HERO_IDS]->toArray();
			$arrHtidReq = array_keys($arrHtidReq);			
			if (!in_array($this->getHtid(), $arrHtidReq))
			{
				Logger::warning('htid is not err for the dress %d', $item_id);
				return FALSE;
			}
		}
		
		
		$this->attrModify['va_hero']['dress'][$dress_position] = $item_id;
		return TRUE;

	}
	
	public function setJewelryByPosition ($position, $item_id)
	{
		if (!isset($this->attrModify['va_hero']['jewelry'][$position]))
		{
			$this->fixJewelryPosition();
			if (!isset($this->attrModify['va_hero']['jewelry'][$position]))
			{
				throw new Exception('invalid jewelry position!');
			}
		}
	
		if ($item_id != ItemDef::ITEM_ID_NO_ITEM)
		{
			$item_manager = ItemManager::getInstance();
			$item = $item_manager->getItem($item_id);

			$equipReq = $item->equipReq();
			$hero_level_req = $equipReq[ItemDef::ITEM_ATTR_NAME_HERO_LEVEL];
			// 对于主角，则只需要判断等级
			if ($this->isMasterHero() && $this->getLevel() < $hero_level_req)
			{
				Logger::warning('hero level is low!req:%d!', $hero_level_req);
				return FALSE;
			}
			
			// 对于其他英雄则等级或转生满足其一即可
			$hero_rebirth_num = $equipReq[ItemDef::ITEM_ATTR_NAME_REBIRTH_NUM];
			if (!$this->isMasterHero())
			{
				if (!($this->getLevel() >= $hero_level_req || $this->getRebirthNum() >= $hero_rebirth_num))
				{
					Logger::warning('hero level or rebirth is low!req:%d! rebirthnum:%d!', $hero_level_req, $hero_rebirth_num);
					return FALSE;
				}
			}

		}
	
		$this->attrModify['va_hero']['jewelry'][$position] = $item_id;
		return TRUE;
	
	}

	public function setElementByPosition ($position, $item_id)
	{
		if (!isset($this->attrModify['va_hero']['element'][$position]))
		{
			$this->fixJewelryPosition();
			if (!isset($this->attrModify['va_hero']['element'][$position]))
			{
				throw new Exception('invalid element position!');
			}
		}
	
		if ($item_id != ItemDef::ITEM_ID_NO_ITEM)
		{
			$item_manager = ItemManager::getInstance();
			$item = $item_manager->getItem($item_id);

			$equipReq = $item->equipReq();
			$hero_level_req = $equipReq[ItemDef::ITEM_ATTR_NAME_HERO_LEVEL];
			// 对于主角，则只需要判断等级
			if ($this->isMasterHero() && $this->getLevel() < $hero_level_req)
			{
				Logger::warning('hero level is low!req:%d!', $hero_level_req);
				return FALSE;
			}
			
			// 对于其他英雄则等级或转生满足其一即可
			$hero_rebirth_num = $equipReq[ItemDef::ITEM_ATTR_NAME_REBIRTH_NUM];
			if (!$this->isMasterHero())
			{
				if (!($this->getLevel() >= $hero_level_req || $this->getRebirthNum() >= $hero_rebirth_num))
				{
					Logger::warning('hero level or rebirth is low!req:%d! rebirthnum:%d!', $hero_level_req, $hero_rebirth_num);
					return FALSE;
				}
			}

		}
	
		$this->attrModify['va_hero']['element'][$position] = $item_id;
		return TRUE;
	
	}
	
	/**
	 *
	 * 移除英雄所有点装备，并放置到背包中
	 *
	 * @return boolean
	 *
	 * @see 需要在外边调用$bag->update();
	 */
	public function removeAllArming()
	{
		return $this->removeAllEquip('arming');
	}
	
	public function removeAllDress()
	{
		return $this->removeAllEquip('dress');
	}
	
	public function removeAllJewelry()
	{
		return $this->removeAllEquip('jewelry');
	}

	public function removeAllElement()
	{
		return $this->removeAllEquip('element');
	}
	
	protected function removeAllEquip($type)
	{
		$equipPos = $this->attrModify['va_hero'][$type];
		$bag = BagManager::getInstance()->getBag();
		foreach ($equipPos as $pos=>$itemId)
		{
			if ($itemId == BagDef::ITEM_ID_NO_ITEM)
			{
				continue;
			}
			
			if (!$bag->addItem($itemId))
			{
				return false;
			}
			
			$equipPos[$pos] = BagDef::ITEM_ID_NO_ITEM;
		}
		$this->attrModify['va_hero'][$type] = $equipPos;
		
		return TRUE;
	}	
	
	/**
	 * 0:成功 ， 1 经验溢出
	 * Enter description here ...
	 * @param unknown_type $goodwill
	 */
	public function addGoodwill($goodwill)
	{
		$gw = &$this->attrModify['va_hero']['goodwill'];
		$maxLevel = $this->getMaxGoodwillLevel();
		$ret = 0;
		
		//升级
		$expTbl = btstore_get()->GOODWILL_EXP;
		$oldLevel = $gw['level'];
		$gw['exp'] += $goodwill;
		while (true)
		{
			//可以升级
			$upgradeExp = $expTbl[$gw['level']+1];
			if ($upgradeExp <= $gw['exp'])
			{
				$gw['exp'] -= $upgradeExp;
				$gw['level'] += 1;
				if ($gw['level'] > $maxLevel)
				{
					$gw['level']-=1;
					$gw['exp'] = ($upgradeExp - 1);
					$ret = 1;
				}								
			}
			else
			{
				break;
			}						
		}
		
		if ($oldLevel != $gw['level'])
		{
			$gw['upgrade_time'] = Util::getTime();
			$this->isGWLevelChange = true;		
		}
		
		Logger::debug('after addGoodwill %s %s', $gw, $this->attrModify['va_hero']['goodwill']);
		
		return $ret;
	}
	
	public function addGoodwillByItem($gid, $itemId, $itemNum, $goldNum)
	{				
		$bag = BagManager::getInstance()->getBag();

		//物品是否存在
		if ( $bag->getGridID($itemId) != $gid )
		{
			Logger::WARNING('invalid item_id:%d not in gid:%d', $itemId, $gid);
			throw new Exception('fake');
		}

		//物品数量是否合法
		if ( $itemNum <= 0 )
		{
			Logger::WARNING('invalid item_num:%d', $itemNum);
			throw new Exception('fake');
		}

		$itemMgr = ItemManager::getInstance();
		$item = $itemMgr->getItem($itemId);
		//物品是否存在于系统中,如果不存在于系统中，则是一个致命bug!
		if ( $item === NULL )
		{
			Logger::FATAL('fixed me!invalid item_id:%d in bag!', $itemId);
			throw new Exception('fake');
		}
		
		//不是好感度物品
		$itemType = $item->getItemType();
		if ($itemType !== ItemDef::ITEM_GOODWILL)
		{
			Logger::warning('fail to add goodwill, the item %d type %d err', $itemId, $itemType);
			throw new Exception('fake');
		}		

		//物品是否足够
		if ( $bag->decreaseItem($itemId, $itemNum) == FALSE )
		{
			Logger::WARNING('add gold by item invalid!item_id:%d, item_num:%d', $itemId, $itemNum);
			throw new Exception('fake');
		}		
		
		//计算能增加的好感值
		$gwItemType = $item->getGoodWillType();
		$itemRate = 0;
		$cfg = btstore_get()->CREATURES[$this->getHtid()]; 
		if ($cfg[CreatureInfoKey::good_will_like]['item_type']==$gwItemType)
		{
			$itemRate = $cfg[CreatureInfoKey::good_will_like]['goodwill_rate']; 
		}
		else if ($cfg[CreatureInfoKey::good_will_mislike]['item_type']==$gwItemType)
		{
			$itemRate = - $cfg[CreatureInfoKey::good_will_mislike]['goodwill_rate']; 
		}
		
		//礼包
		$goldNumType = $goldNum / $itemNum;
		if (!isset(HeroConf::$GOODWILL_GIFT_RATE[$goldNumType]))
		{
			Logger::warning('gold num %d err for gift goodwill', $goldNumType);
			throw new Exception('fake');
		}
		$giftRate = HeroConf::$GOODWILL_GIFT_RATE[$goldNumType];
		$user = EnUser::getUserObj();
		if (!$user->subGold($goldNum))
		{
			Logger::warning('fail to add goodwill, gold is not enough');
			throw new Exception('fake');
		}
		
		//每次给伙伴赠送礼物时最终增加好感度=该礼物好感度基础值*（1+礼品盒好感度倍率/10000）*(1+喜好增减好感度倍率/10000）
		$goodwill = floor($itemNum * $item->getGoodWill() * ( 1+ $giftRate/10000 ) * (1 + $itemRate/10000));
		Logger::debug('goodwill:%d, item:%d,  itemId:%d giftRate:%d, itemRate:%d', 
			$goodwill, $item->getGoodWill(), $itemId, $giftRate, $itemRate);
		
		if (0!=$this->addGoodwill($goodwill))
		{			
			Logger::warning('goodwill level err, rebirth num or level is not enough');
			throw new Exception('fake');
		}
	}
	
	/**
	 * 金币加好感度
	 */
	public function addGoodwillByGold ()
	{		
		$user = EnUser::getUserObj();		
		//修改次数
		$user->addGoodwillNum();
		
		//计算能增加的好感度
		$goodwill = HeroConf::GOODWILL_BY_GOLD ;
		
		if (0!=$this->addGoodwill($goodwill))
		{
			Logger::warning('goodwill level err, rebirth num or level is not enough');
			throw new Exception('fake');
		}		
	}
	
	public function convert()
	{
		$hid = $this->getHid();
		//不能在当前阵型
		if (in_array($hid, EnFormation::getFormationHids()))
		{
			Logger::warning('fail to convert, the hero is in cur formation');
			throw new Exception('fake');
		}
		
		if (!$this->isRecruit())
		{
			Logger::warning('fail to convert,  the hero is not recruit');
			throw new Exception('fake');
		}
		
		$htid =$this->getHtid();
		$cfg = btstore_get()->HERO_CONVERT[$htid];
		$toHtid = $cfg['htid'];
		
		if ($this->getRebirthNum() < $cfg['rebirth'])
		{
			Logger::warning('fail to convert, rebirth num not enough');
			throw new Exception('fake');
		}		
		if ($this->getLevel() < $cfg['level'])
		{
			Logger::warning('fail to convert, level not enough');
			throw new Exception('fake');
		}		
		if ($this->getGoodwillLevel() < $cfg['goodwill_level'])
		{
			Logger::warning('fail to convert, goodwill level not enough');
			throw new Exception('fake');
		}
		
		
		$user = EnUser::getUserObj();
		if (!$user->subBelly($cfg['belly']))
		{
			Logger::warning('fail to convert, lack belly');
			throw new Exception('fake');
		}
		
		if (!$user->subExperience($cfg['experience']))
		{
			Logger::warning('fail to convert, lack experience');
			throw new Exception('fake');
		}
		
		if ($cfg['copy']!=0)
		{
			//没有通过某个副本
			if (HeroCopyLogic::isCopyOver($cfg['copy'])=='no')
			{
				Logger::warning('fail to convert, need pass copy:%d', $cfg['copy']);
				throw new Exception('fake');
			}
		}
		
		
		$preHtid = $cfg['pre_htid'];
		if ($preHtid > 0)
		{
			if (!$user->isHeroConvert($preHtid))
			{
				Logger::warning('fail to convert, first convert hero %d', $preHtid);
				throw new Exception('fake');
			}
		}
		
		$soulObj = SoulObj::getInstance();
		foreach ($cfg['soul'] as $soulType => $soul)
		{
			if ($soulType==1)
			{
				$soulObj->subBlue($soul);
			}
			else
			{
				$soulObj->subPurple($soul);
			}
		}		
		$this->attrModify['htid'] = $toHtid;	
		$this->attrModify['va_hero']['convert_from'][] = $htid;	

		//恶魔果实修改
		$heroCfg = $HeroConfig = btstore_get()->CREATURES;
		//foreach ($HeroConfig[CreatureInfoKey::devilFruitSkill] as $pos => $rebirthDmTid)
		$dmArr = $heroCfg[$htid][CreatureInfoKey::devilFruitSkill];
		$toDmArr = $heroCfg[$toHtid][CreatureInfoKey::devilFruitSkill];
		foreach ($dmArr as $pos=>$rebirthDmTid)
		{
			list($rebirthNum, $DmTid) = $rebirthDmTid;
			//0表示没有物品
			if ($DmTid == 0)
			{
				continue;
			}
			
			//位置没开 
			if (!isset($this->attrModify['va_hero']['daimonApple'][$pos]))
			{
				continue;
			}

			$itemMgr = ItemManager::getInstance();
			$itemObj = $itemMgr->getItem($this->attrModify['va_hero']['daimonApple'][$pos]);				
			$tplId = $itemObj->getItemTemplateID();
			//并且是进阶前配置中的物品id, 并且进阶后的配置有修改
			if ($tplId == $DmTid
					&& $tplId != $toDmArr[$pos][1])
			{
				//换一个恶魔果实
				$itemMgr->deleteItem($itemObj->getItemID());				
				$itemId = $itemMgr->addItem($toDmArr[$pos][1]);				
				$itemId = $itemId[0];
				$this->attrModify['va_hero']['daimonApple'][$pos] = $itemId;				
			}				
		}
		
		$user->covertHero($htid, $cfg['htid']);
	}
	
	//得到修改的属性
	public function getModifyAttr ()
	{
		$arrField = array();
		if ($this->attrModify['curHp'] <= 0)
		{
			$this->attrModify['curHp'] = 1;
		}
		
		foreach ($this->attr as $key=>$value)
		{
			if ($this->attrModify[$key] != $value)
			{
				$arrField[$key] = $this->attrModify[$key];
			}
		}
		
		return $arrField;
	}
	
	/**
	 * 一般不要使用，这里把英雄的属性变化忽略了。
	 * 例如，交换装备的时候使用直接使用dao update， 调用此函数忽略英雄属性， 
	 * 调用 HeroManager::saveRctHero 修改HeroManager里面的值
	 * 调用 RPCContext::getInstance()->setSession('hero.arrHeroAttr', $this->heroManager->getArrRctAttr())
	 * 设置session
	 * @param unknown_type $arrAttr
	 */
	public function setAttrNoModify()
	{
		$this->attr = $this->attrModify;
	}
	
	public function heritageGoodwill($desHid, $type)
	{
		$userObj = EnUser::getUserObj();
		$desHero = $userObj->getHeroObj($desHid);
		if ($this->isMasterHero() || $desHero->isMasterHero())
		{
			Logger::warning('master hero cannot heritage goodwill');
			throw new Exception('fake');
		}
		
		if (!$this->isRecruit() || !$desHero->isRecruit())
		{
			Logger::warning('hero must be recruit for heritage goodwill');
			throw new Exception('fake');
		}
		
		if (($this->getGoodwillLevel() < $desHero->getGoodwillLevel())
			|| ($this->getGoodwillLevel() == $desHero->getGoodwillLevel() 
				&& $this->getGoodwillExp() < $desHero->getGoodwillExp()))
		{
			Logger::warning('fail to heritage goodwill, des hero goodwill all exp >= this hero');
			throw new Exception('fake');
		}		
		
		if ($type == 0)
		{
			if (!$userObj->subGold(HeroConf::GOODWILL_HERITAGE_GOLD))
			{
				Logger::warning('lack gold for heritage goodwill');
				throw new Exception('fake');
			}
			Statistics::gold(StatisticsDef::ST_FUNCKEY_GOODWILL_HERITAGE, HeroConf::GOODWILL_HERITAGE_GOLD, Util::getTime());
		}
		else
		{
			$bag = BagManager::getInstance()->getBag();
			if (!$bag->deleteItemsByTemplateID(HeroConf::$GOODWILL_HERITAGE_ITEM))
			{
				Logger::warning('lack item for heritage goodwill');
				throw new Exception('fake');
			}
		}
		
		$exp = $this->getGoodwillAllExp();

		//忽略溢出经验
		$desHero->addGoodwill(ceil($exp * HeroConf::GOODWILL_HERITAGE_RATE));

		//不能超过当前level
		$gwLevel = $this->getGoodwillLevel();		
		if ($desHero->getGoodwillLevel() > $gwLevel)
		{
			$desHero->setGoodwillLevel($gwLevel);
			$desHero->setGoodwillExp($this->getGoodwillExp());			
		}
		else if (($desHero->getGoodwillLevel() == $this->getGoodwillLevel()) 
			&& ($desHero->getGoodwillExp() > $this->getGoodwillExp()))
		{
			$desHero->setGoodwillExp($this->getGoodwillExp());
		}
	}
	
	public function setGoodwillLevel($level)
	{
		$this->attrModify['va_hero']['goodwill']['level'] = $level;
	}
	
	public function setGoodwillExp($exp)
	{
		$this->attrModify['va_hero']['goodwill']['exp'] = $exp;
	}
	
	public function addProperty($propertys)
	{
		// haki 1  floor(forceModulus * upgradeNeed * 0.0001); 
		// haki 2  floor(knowledgeModulus * upgradeNeed * 0.0001); 
		// haki 3  floor(DomineerModulus * upgradeNeed * 0.0001); 
		// haki 4  floor(AshuraModulus * AshuraUpgrade * 0.0001); 

		$affix = array('attack'=>61, 'defense'=>1, 'hp'=>60, 'xiuluo'=>56);
		foreach ($propertys as $key => $value)
		{
			$cfg = btstore_get()->DOMINEER_PROPERTY[$this->attrModify['va_hero']['haki'][$key]['level']];
			$this->attrModify['va_hero']['haki'][$key]['expe'] += $value;
			
			switch ($key)
			{
				case 'attack':
					$property = floor($cfg['forceModulus']*$value*0.0001);
					break;
				case 'defense':
					$property = floor($cfg['knowledgeModulus']*$value*0.0001);
					break;
				case 'hp':
					$property = floor($cfg['DomineerModulus']*$value*0.0001);
					break;
				case 'xiuluo':
					$property = floor($cfg['AshuraModulus']*$value*0.0001);
					break;
			}			
			
			if ($key == 'master')
			{
				if ($this->attrModify['va_hero']['haki'][$key]['expe'] >= $cfg['DomineerUpgrade'])
				{
					++$this->attrModify['va_hero']['haki'][$key]['level'];
				}
			} else
			{
				if ($this->attrModify['va_hero']['haki'][$key]['expe'] >= $cfg['upgradeNeed'])
				{
					++$this->attrModify['va_hero']['haki'][$key]['level'];
				}
				if (isset($this->attrModify['va_hero']['haki'][$key]['property'][$affix[$key]])) {
				$this->attrModify['va_hero']['haki'][$key]['property'][$affix[$key]] += $property;
				} else $this->attrModify['va_hero']['haki'][$key]['property'] = array($affix[$key]=>$property);
			}			
		}
	}
		
	public function getHakiInfo()
	{
		return $this->attrModify['va_hero']['haki'];
	}
	
	public function getDomineerLevel()
	{
		$lv = 30;
		foreach ($this->attrModify['va_hero']['haki'] as $key => $value)
		{
			if ($lv < $value['level'])
			{
				$lv = $value['level'];
			}
		}
		return $lv;
	}
	
	public function convertHaki()
	{
		$hid = $this->getHid();
		//不能在当前阵型
		if (in_array($hid, EnFormation::getFormationHids()))
		{
			Logger::warning('fail to convert, the hero is in cur formation');
			throw new Exception('fake');
		}
		
		if (!$this->isRecruit())
		{
			Logger::warning('fail to convert,  the hero is not recruit');
			throw new Exception('fake');
		}
		
		$htid =$this->getHtid();
		$cfg = btstore_get()->DOMINEER_HERO[$htid];
		$toHtid = $cfg['htid'];
		
		if ($this->getLevel() < $cfg['level'])
		{
			Logger::warning('fail to convert, level not enough');
			throw new Exception('fake');
		}		
		if ($this->getDomineerLevel() < $cfg['domineer_level'])
		{
			Logger::warning('fail to convert, domineer level not enough');
			throw new Exception('fake');
		}		
						
		$this->attrModify['htid'] = $toHtid;	
		$this->attrModify['va_hero']['convert_from'][] = $htid;	

		//恶魔果实修改
		$heroCfg = $HeroConfig = btstore_get()->CREATURES;
		//foreach ($HeroConfig[CreatureInfoKey::devilFruitSkill] as $pos => $rebirthDmTid)
		$dmArr = $heroCfg[$htid][CreatureInfoKey::devilFruitSkill];
		$toDmArr = $heroCfg[$toHtid][CreatureInfoKey::devilFruitSkill];
		$itemMgr = ItemManager::getInstance();
		foreach ($dmArr as $pos=>$rebirthDmTid)
		{
			list($rebirthNum, $DmTid) = $rebirthDmTid;
			//0表示没有物品
			if ($DmTid == 0)
			{
				continue;
			}
			
			//位置没开 
			if (!isset($this->attrModify['va_hero']['daimonApple'][$pos]))
			{
				continue;
			}
			
			$itemObj = $itemMgr->getItem($this->attrModify['va_hero']['daimonApple'][$pos]);				
			$tplId = $itemObj->getItemTemplateID();
			//并且是进阶前配置中的物品id, 并且进阶后的配置有修改
			if ($tplId == $DmTid
					&& $tplId != $toDmArr[$pos][1])
			{
				//换一个恶魔果实
				$itemObj->returnExpKernel();
				$itemMgr->deleteItem($itemObj->getItemID());				
				$itemId = $itemMgr->addItem($toDmArr[$pos][1]);				
				$itemId = $itemId[0];
				$itemMgr->update();				
				$this->attrModify['va_hero']['daimonApple'][$pos] = $itemId;
			}				
		}

		$user = EnUser::getUserObj();
		$user->covertHero($htid, $cfg['htid']);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */