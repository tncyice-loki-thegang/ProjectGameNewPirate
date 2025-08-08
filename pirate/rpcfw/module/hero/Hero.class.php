<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Hero.class.php 39959 2013-03-05 10:12:05Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/Hero.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-05 18:12:05 +0800 (二, 2013-03-05) $
 * @version $Revision: 39959 $
 * @brief
 *
 **/

class Hero implements IHero
{
	/**
	 *
	 * Enter description here ...
	 * @var UserObj
	 */
	private $userObj = null;

	public function __construct()
	{
		if (RPCContext::getInstance()->getUid()!=null)
		{
			$this->userObj = EnUser::getUserObj();
		}
	}


	/* (non-PHPdoc)
	 * @see IHero::getPubHeroes()
	 */
	public function getPubHeroes ()
	{
		$arrRet = array();
	 	$arrPub = array_values($this->userObj->getPubHeroes());
	 	foreach ($arrPub as $hero)
	 	{
	 		unset($hero['uid']);
	 		unset($hero['upgrade_time']);
			//unset($hero['va_hero']);
			if (isset($hero['va_hero']))
			{				
				if (isset($hero['va_hero']['goodwill']))
				{
					$gw = $hero['va_hero']['goodwill'];
				}
				else
				{
					$gw = HeroLogic::getInitGoodwill();
				}				
				$hero['va_hero'] = array('goodwill'=>$gw);
				$hero['va_hero']['daimonApple'] = array(array('item_id' => 1, 'item_template_id' => 1, 'item_num' => 1, 'item_time' => time(), 'va_item_text' => array('exp' => 0, 'evo_level' => 0)));
			}


		$arrRet[] = $hero;
	 	}
	 	return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IHero::addPrestigeHero()
	 */
	public function addPrestigeHero ($htid)
	{
		$this->userObj->addPrestigeHero($htid);
		$this->userObj->update();

		TaskNotify::operate(TaskOperateType::ADD_PRESTIGE_HERO);

		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IHero::recruit()
	 */
	public function recruit ($htid)
	{
		try
		{
			$heroObj = $this->userObj->getPubHeroObj($htid);
		}
		catch (Exception $e)
		{
			//检查是否数据不一致， 并且修复数据
			$attr = HeroLogic::getHeroByUidHtid($this->userObj->getUid(), $htid);
			if (!empty($attr) && $attr['status']==HeroDef::STATUS_RECRUIT)
			{
				$hid = $attr['hid'];
				if (in_array($hid, $this->userObj->getRctHeroOrder()))
				{
					Logger::warning('the hero is recuit. htid:%d', $htid);
					throw new Exception('fake');
				}
				$this->userObj->addHeroToRctHeroOrder($hid);
				$this->userObj->update();
				
				Logger::fatal('fixed data. recruit hero hid:%d.', $hid);
				//修数据后踢下线， i'm sorry
				RPCContext::getInstance()->closeConnection($this->userObj->getUid());
				throw new Exception('fixing');;
			}
			else
			{				
				Logger::warning('fail to recruit, fail to get hero by htid:%d', $htid);
				throw new Exception('fake');
			}
		}
	
		$heroObj->recruit();
		$this->userObj->update();
		
		$hero = $heroObj->getAllAttr();
		$armingItem = $heroObj->getArmingItem();
		$daItem = $heroObj->getDaItem();		
		$hero['armingInfo'] = $heroObj->arrItemInfo($armingItem);
		$hero['daimonApple'] = $heroObj->arrItemInfo($daItem);
		foreach ($hero['daimonApple'] as $daimonPos => $daimonInfo) {
			$hero['daimonApple'][$daimonPos]['va_item_text'] = array('exp' => 0, 'evo_level' => 0);
		}
		unset($hero['uid']);
		unset($hero['upgrade_time']);
		//unset($hero['va_hero']);
		$va_hero = array('goodwill'=>$hero['va_hero']['goodwill']);
		$hero['va_hero'] = $va_hero;

		//for task
		TaskNotify::operate(TaskOperateType::RECRUIT);

		return $hero;
	}

	/* (non-PHPdoc)
	 * @see IHero::fire()
	 */
	public function fire ($htid)
	{
		//这个操作不要rollback 里面涉及扒装备， 在阵型中删除英雄
		//所以不要在这个请求里面添加其他的功能
		$heroObj = $this->userObj->getHeroObjByHtid($htid);
		$arrRet = $heroObj->fire();
		if ($arrRet['ret']!='ok')
		{
			return $arrRet;
		}
		$this->userObj->update();
		$bag = BagManager::getInstance()->getBag();			
		$arrRet['grid'] = $bag->update();
		return $arrRet;

	}

	/* (non-PHPdoc)
	 * @see IHero::getRecruitHeroes()
	 */
	public function getRecruitHeroes ($uid=0)
	{
		$userObj = EnUser::getUserObj($uid);
		$retRctHeroes = array();
		$rctHeroes = $userObj->getRecruitHeroes();
		$arrHid = $userObj->getRctHeroOrder();
		Logger::debug("order by htid %s", $arrHid);
		
		$modifyUser = false;
		$curUser = false;
		if ($uid==0 || RPCContext::getInstance()->getUid()==$uid)
		{
			$curUser = true;
		}

		foreach ($rctHeroes as $hero)
		{
			$hid = $hero['hid'];
			$pos = array_search($hid, $arrHid);
			if ($pos === false)
			{
				Logger::fatal("fail to find hero (htid:%d) in recruit_hero_order", $hid);
				throw new Exception("fail to find hero $hid in recruit_hero_order");
			}

			$heroObj = $userObj->getHeroObj($hero['hid']);
			
			$armingItem = $heroObj->getArmingItem();
			$daItem = $heroObj->getDaItem();
			$dressItem = $heroObj->getDressItem();
			$jewelryItem = $heroObj->getJewelryItem();
			$elementItem = $heroObj->getElementItem();
			
			$hero['armingInfo'] = $heroObj->arrItemInfo($armingItem);
			$hero['daimonApple'] = $heroObj->arrItemInfo($daItem);
			// foreach ($hero['daimonApple'] as $daimonPos => $daimonInfo) {
				// $hero['daimonApple'][$daimonPos]['va_item_text'] = array('exp' => 0, 'evo_level' => 0);
			// }


			$heroDress = $heroObj->arrItemInfo($dressItem);
			$heroJewelry = $heroObj->arrItemInfo($jewelryItem);
			$heroElement = $heroObj->arrItemInfo($elementItem);
			
			if ($curUser)
			{
				//检查英雄装备，自修复数据
				foreach ($hero['armingInfo'] as $armPos=>$armingInfo)
				{
					if (empty($armingInfo))
					{
						if ($hero['va_hero']['arming'][$armPos] != ItemDef::ITEM_ID_NO_ITEM)
						{
							//fix
							$heroObj->setArmingByPosition($armPos, ItemDef::ITEM_ID_NO_ITEM);
							$modifyUser = true;
							Logger::fatal('fix hero arming! pos %d item_id %d', $armPos, $hero['va_hero']['arming'][$armPos]);
						}
					}
				}
				
				//dress 自修复
				foreach ($heroDress as $dressPos=>$dressInfo)
				{
					if (empty($dressInfo))
					{
						if ($hero['va_hero']['dress'][$dressPos] != ItemDef::ITEM_ID_NO_ITEM)
						{
							//fix
							$heroObj->setDressByPosition($dressPos, ItemDef::ITEM_ID_NO_ITEM);
							$modifyUser = true;
							Logger::fatal('fix hero dress! pos %d item_id %d', $dressPos, $hero['va_hero']['dress'][$dressPos]);
						}
					}
				}
				
				//jewelry 自修复
				foreach ($heroJewelry as $jewelryPos=>$jewelryInfo)
				{
					if (empty($jewelryInfo))
					{
						if ($hero['va_hero']['jewelry'][$jewelryPos] != ItemDef::ITEM_ID_NO_ITEM)
						{
							//fix
							$heroObj->setJewelryByPosition($jewelryPos, ItemDef::ITEM_ID_NO_ITEM);
							$modifyUser = true;
							Logger::fatal('fix hero jewelry! pos %d item_id %d', $jewelryPos, $hero['va_hero']['jewelry'][$jewelryPos]);
						}
					}
				}

				//element 自修复
				foreach ($heroElement as $elementPos=>$elementInfo)
				{
					if (empty($elementInfo))
					{
						if ($hero['va_hero']['element'][$elementPos] != ItemDef::ITEM_ID_NO_ITEM)
						{
							//fix
							$heroObj->setJewelryByPosition($elementPos, ItemDef::ITEM_ID_NO_ITEM);
							$modifyUser = true;
							Logger::fatal('fix hero element! pos %d item_id %d', $elementPos, $hero['va_hero']['element'][$elementPos]);
						}
					}
				}
				
				//修复va_user convert_heroes
				if (isset($hero['va_hero']['convert']) && isset($hero['va_hero']['convert_from']))
				{
					foreach ($hero['va_hero']['convert_from'] as $conHtid)
					{
						if (!$userObj->isHeroConvert($conHtid))
						{
							Logger::fatal('fix convert hero htid %d', $hero['htid']);
							$userObj->covertHero($conHtid, $hero['htid']);
							$userObj->update();
							throw new Exception('fixing');
						}
							
					}
				}
			}
			else //查看其他用户的时候多返回几个字段
			{
				//这里计算了所有属性,多做了一些计算，但是应该没有增加数据库请求次数
				$sanWei = $heroObj->getSanWei();
				list($sanWei['stg'], $sanWei['agile'], $sanWei['itg']) = 
				 array($sanWei['stg']/100, $sanWei['agile']/100, $sanWei['itg']/100);
				
				
				$hero = array_merge($hero, $sanWei);
				$hero['maxHp'] = $heroObj->getMaxHp();				
			}

			//主角英雄属性
			if (HeroUtil::isMasterHero($hero['htid']))
			{
				$masterProp = $hero['va_hero']['master'];
				unset($masterProp['using_skill_time']);
				unset($masterProp['using_skill_num']);
				$hero = array_merge($hero, $masterProp);
			}
			
			unset($hero['uid']);
			unset($hero['upgrade_time']);
			//unset($hero['va_hero']);
			// logger::warning($hero);
			$va_hero = array('goodwill'=>$hero['va_hero']['goodwill'], 
					'dress'=>$heroDress, 
					'jewelry'=>$heroJewelry,
					'element'=>$heroElement,
					'master_haki_id'=>$hero['va_hero']['master_haki_id']
					);
			
			if (!$curUser && !$userObj->isShowDress())
			{
				unset($va_hero['dress']);
			}
			
			// $va_hero['master_haki_id'] = 0;
			$va_hero['talnet_skill_level'] = 0;
			$va_hero['haki'] = $hero['va_hero']['haki'];			
			
			$hero['va_hero'] = $va_hero;

			$retRctHeroes[$pos] = $hero;
		}
		
		if ($modifyUser)
		{
			$userObj->update();
			Logger::fatal('fixed data, convert hero');
		}

		$arrRet = array_merge($retRctHeroes);
		// logger::warning($arrRet);
		return $arrRet;
	}

	public function saveRecruitHeroOrder ($arrHtid)
	{
		//检查此列表都是已招募的英雄
		$arrHtid = array_merge($arrHtid);
		
		$arrRctAttr = EnUser::getUserObj()->getRecruitHeroes();
		$arrRctAttr = Util::arrayIndex($arrRctAttr, 'htid');
		if (count($arrHtid) != count($arrRctAttr))
		{
			Logger::warning('fail to save recruit hero order, the num of htid is not equal, argv htid:%s, all recruit hero htid:%s', 
				$arrHtid, array_keys($arrRctAttr));
			throw new Exception('fake');
		}

		$arrHid = array();
		foreach ($arrHtid as $htid)
		{
			if (!isset($arrRctAttr[$htid]))
			{
				Logger::warning('fail to save recruit hero order, hero(htid:%d) isnot recruited.', $htid);
				throw new Exception('fake');
			}
			$arrHid[] = $arrRctAttr[$htid]['hid'];
		}

		//检查主角英雄是第一个
		if (!HeroUtil::isMasterHero($arrHtid[0]))
		{
			Logger::warning('fail to save recruit hero order, the master hero is not first');
			throw new Exception('fake');
		}

		$this->userObj->saveRctHeroOrder($arrHid);
		$this->userObj->update();
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IHero::rebirth()
	 */
	public function rebirth ($htid)
	{
		$heroObj = $this->userObj->getHeroObjByHtid($htid);
		$rebirthNum = $heroObj->rebirth();
		$this->userObj->update();
		$arrRet = array();
		$arrRet['grid'] = BagManager::getInstance()->getBag()->update();
		$arrRet['ret'] = 'ok';

		// 通知成就系统
		EnAchievements::notify($this->userObj->getUid(), 
			AchievementsDef::HERO_REBIRTH, $rebirthNum);

		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IHero::addArming()
	 */
	public function addArming ($hid, $position, $itemId)
	{
		$itemId = intval($itemId);

		$item_manager = ItemManager::getInstance();
		$item = $item_manager->getItem($itemId);
		$return = array ('add_success' => FALSE, 'gid' => 0 );
		if ($item === NULL || $item->getItemType() != ItemDef::ITEM_ARM ||
			!isset(ArmingDef::$ARMING_POSITIONS[$position]) ||
			!in_array($item->getArmType(), ArmingDef::$ARMING_POSITIONS[$position]))
		{
			Logger::warning('item not exist!');
			return $return;
		}

		$return = $this->addEquip('arming', $hid, $position, $itemId);
		if (!$return['add_success'])
		{
			return $return;
		}
		
		//成就
		EnAchievements::notify($this->userObj->getUid(), AchievementsDef::HERO_ITEM_COLOR, $hid);

		//调用任务系统,装备交换不需要发送此通知
		TaskNotify::operate(TaskOperateType::ARMING);
		return $return;
	}
	
	protected function addEquip ($type, $hid, $position, $itemId)
	{
		$hid = intval($hid);
		$position = intval($position);
		$itemId = intval($itemId);
		
		$return = array ('add_success' => FALSE, 'gid' => 0 );
		$bag = BagManager::getInstance()->getBag();
		if ($bag->getGridID($itemId) == BagDef::BAG_INVALID_BAG_ID)
		{
			Logger::warning('item:%d not belong to!', $itemId);
			return $return;
		}
	
		$hero = $this->userObj->getHeroObj($hid);
	
		$setFunc = HeroUtil::getSetEquipFunc($type);
		$oldItemId = $hero->getEquipByPosition($type, $position);
		if (!call_user_func_array(array($hero, $setFunc), array($position, $itemId)))
		{
			Logger::warning('item:%d not equip in position:%d!', $itemId, $position);
			return $return;
		}
		
		$bag->removeItem($itemId);
		if ($oldItemId != BagDef::ITEM_ID_NO_ITEM && !$bag->addItem($oldItemId) )
		{
			return $return;
		}	
	
		//更新背包
		$bag_modify = $bag->update();
		ksort($bag_modify);
		$gids = array_keys($bag_modify);
		$return['add_success'] = TRUE;
		$return['gid'] = intval($gids[0]);
	
		$this->userObj->update();
	
		return $return;
	}


	/* (non-PHPdoc)
	 * @see IHero::moveArming()
	 */
	public function moveArming($srcHid, $desHid, $position)
	{
		$moveEquip = new HeroMoveEquip($srcHid, $desHid);		
		if (!$moveEquip->moveEquip('arming', $position))
		{
			return false;
		}		
		
		$moveEquip->update();
		
		//成就
		EnAchievements::notify($this->userObj->getUid(), AchievementsDef::HERO_ITEM_COLOR, $srcHid);
		EnAchievements::notify($this->userObj->getUid(), AchievementsDef::HERO_ITEM_COLOR, $desHid);
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IHero::moveJewelry()
	*/
	public function moveJewelry($srcHid, $desHid, $position)
	{
		$moveEquip = new HeroMoveEquip($srcHid, $desHid);		
		if (!$moveEquip->moveEquip('jewelry', $position))
		{
			return false;
		}		
		
		$moveEquip->update();
		return true;
	}

	/* (non-PHPdoc)
	 * @see IHero::addDress()
	*/
	public function addDress($hid, $position, $itemId)
	{
		$itemId = intval($itemId);
	
		$item_manager = ItemManager::getInstance();
		$item = $item_manager->getItem($itemId);
		$return = array ('add_success' => FALSE, 'gid' => 0 );
		
		if ($item === NULL || $item->getItemType() != ItemDef::ITEM_FASHION_DRESS || 
				!isset(FashionDressDef::$FASHION_DRESS_POSITIONS[$position]) 
				|| !in_array($item->getFashionType(), FashionDressDef::$FASHION_DRESS_POSITIONS[$position]))
		{
			Logger::warning('item not exist!');
			return $return;
		}		
	
		return $this->addEquip('dress', $hid, $position, $itemId);
	}
	
	/* (non-PHPdoc)
	 * @see IHero::addJewelry()
	*/
	public function addJewelry($hid, $position, $itemId)
	{
		if (!EnSwitch::isOpen(SwitchDef::JEWELRY_EQUIP))
		{
			Logger::warning('fail to addJewelry, switch is no open');
			throw new Exception('fake');
		}
		
		$itemId = intval($itemId);		
	
		$item_manager = ItemManager::getInstance();
		$item = $item_manager->getItem($itemId);
		$return = array ('add_success' => FALSE, 'gid' => 0 );
	
		if ($item === NULL || $item->getItemType() != ItemDef::ITEM_JEWELRY ||
				!isset(JewelryDef::$JEWELRY_POSITIONS[$position])
				|| !in_array($item->getJewelryType(), JewelryDef::$JEWELRY_POSITIONS[$position]))
		{
			Logger::warning('item not exist!');
			return $return;
		}
	
		return $this->addEquip('jewelry', $hid, $position, $itemId);
	}
	
	/* (non-PHPdoc)
	 * @see IHero::removeArming()
	*/
	public function removeArming ($hid, $position)
	{
		return $this->removeEquip('arming', $hid, $position);
	}
	
	protected function removeEquip($type, $hid, $position)
	{
		//格式化输入
		$hid = intval($hid);
		$position = intval($position);
		
		$hero = $this->userObj->getHeroObj($hid);
		
		$itemId = $hero->getEquipByPosition($type, $position);
		if ( $itemId == BagDef::ITEM_ID_NO_ITEM )
		{
			return BagDef::BAG_INVALID_BAG_ID;
		}
		
		$setFunc = HeroUtil::getSetEquipFunc($type);
		call_user_func_array(array($hero, $setFunc), array($position, BagDef::ITEM_ID_NO_ITEM));
		
		$bag = BagManager::getInstance()->getBag();
		if ( $bag->addItem($itemId) == FALSE )
		{
			Logger::DEBUG('full bag or item:%d is not exist!', $itemId);
			return BagDef::BAG_INVALID_BAG_ID;
		}
		
		$this->userObj->update();
		
		$bagModify = $bag->update();
		$gids = array_keys($bagModify);
		return intval($gids[0]);
	}
	/* (non-PHPdoc)
	 * @see IHero::removeDress()
	*/
	public function removeDress ($hid, $position)
	{
		return $this->removeEquip('dress', $hid, $position);
	}
	
	/* (non-PHPdoc)
	 * @see IHero::removeJewelry()
	*/
	public function removeJewelry($hid, $position)
	{
		return $this->removeEquip('jewelry', $hid, $position);
	}
	
	public function moveAllArming($srcHid, $desHid)
	{
		$moveEquip = new HeroMoveEquip($srcHid, $desHid);
		if ($moveEquip->moveAllEquip('arming'))
		{
			$moveEquip->update();
			return 'ok';
		}
		return 'fail';
	}
	
	public function moveAllJewelry($srcHid, $desHid)
	{
		$moveEquip = new HeroMoveEquip($srcHid, $desHid);
		if ($moveEquip->moveAllEquip('jewelry'))
		{
			$moveEquip->update();
			return 'ok';
		}
		return 'fail';
	}

	public function openDaimonAppleByItem($hid, $position_id)
	{		
		$hero = $this->userObj->getHeroObj($hid);		
		$HeroConfig = btstore_get()->CREATURES[$hero->getHtid()];
		$itemNeed = $HeroConfig[CreatureInfoKey::devilFruitPos][$position_id];
		$bag = BagManager::getInstance()->getBag();		
		if($bag->deleteItembyTemplateID(120017,$itemNeed[1]) == TRUE)
		{
			$hero->openDaimonAppleByItem($position_id);
			$this->userObj->update();
			return array('ret'=>'ok', 'grid'=>$bag->update());
		}
	}
	
	/* (non-PHPdoc)
	 * @see IHero::addDaimonApple()
	 */
	public function addDaimonApple($hid, $item_id, $position_id)
	{
		//格式化输入
		$hid = intval($hid);
		$item_id = intval($item_id);
		$position_id = intval($position_id);

		$hero = $this->userObj->getHeroObj($hid);
		$return = $hero->addDaimonApple($item_id, $position_id);
		if ($return)
		{
			$this->userObj->update();
		}
		return $return;
	}

	public function removeDaimonApple($hid, $position_id, $type)	
	{
		//格式化输入
		$hid = intval($hid);
		$position_id = intval($position_id);

		$hero = $this->userObj->getHeroObj($hid);
		$return = $hero->removeDaimonApple($position_id, $type);
		if ($return['remove_success'])
		{
			//更新数据
			$this->userObj->update();
			Statistics::gold(StatisticsDef::ST_FUNCKEY_REMOVE_DAIMON_APPLE, $return['gold'], Util::getTime());
			unset($return['gold']);
		}
		return $return;
	}

	/**
	 * 内部接口
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param unknown_type $hid
	 * @param unknown_type $arrAttr
	 */
    public function modifyHeroByOther($uid, $hid, $arrAttr)
	{
		$guid = RPCContext::getInstance()->getSession('global.uid');
		if ($guid == null)
		{
			RPCContext::getInstance()->setSession('global.uid', $uid);
		}
		else if($uid!=$guid)
        {
            Logger::fatal('modifyHeroByOther error, lcserver maybe error. uid modified is %d, global uid is %d', $uid, $guid);
            return;
        }

        $userObj = EnUser::getUserObj($uid);
        $heroObj = $userObj->getHeroObj($hid);
        $heroObj->modifyHeroByOther($arrAttr);
        $heroObj->setToMaxHp();
        $userObj->update();

        $userObj = EnUser::getInstance();
		//在线用户，推到前端
		if ($userObj->isOnline())
		{
		 	$arrRet = array();
		 	$curAttr = $heroObj->getAllAttr();
            foreach ($arrAttr as $key=>$tmp)
            {
            	$arrRet[$key] = $curAttr[$key];
            	if ($key=='exp')
            	{
            		$arrRet['level'] = $curAttr['level'];
            	}
            }

            if (!empty($curAttr))
            {
            	$arrRet['hid'] = $hid;
            	RPCContext::getInstance()->sendMsg(array($uid), 'sc.hero.updateProperty', array($arrRet));
            }
		}
	}

	/* (non-PHPdoc)
	 * @see IHero::masterTransfer()
	 */
	public function masterTransfer ()
	{
		if (!EnSwitch::isOpen(SwitchDef::TRANSFER))
		{
			Logger::warning('fail to transfer, switch return false');
			throw new Exception('fake');
		}
		$userObj = EnUser::getUserObj();
		$hero = $userObj->getMasterHeroObj();
		$hero->transfer();
		$userObj->update();
		//删除了物品
		$grid = BagManager::getInstance()->getBag()->update();
		$arrRet = array('ret'=>'ok', 'grid'=>$grid);

		//for task
		TaskNotify::operate(TaskOperateType::TRANSFER);		

		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IHero::masterLearnSkill()
	 */
	public function masterLearnSkill ($skillId)
	{
		if (!EnSwitch::isOpen(SwitchDef::TRANSFER))
		{
			Logger::warning('fail to learn skill, treasfer switch return false');
			throw new Exception('fake');
		}
		
		$userObj = EnUser::getUserObj();
		$hero = $userObj->getMasterHeroObj();
		$hero->learnSkill($skillId);
		$userObj->update();

		//for task
		TaskNotify::operate(TaskOperateType::LEARN_SKILL);
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IHero::getMasterHeroProperty()
	 */
	public function getMasterHeroProperty ($uid=0)
	{
		$userObj = EnUser::getUserObj($uid);
		$hero = $userObj->getMasterHeroObj();
		$arrRet = $hero->getMasterHeroProperty();
		$arrRet['htid'] = $hero->getHtid();
		$arrRet['hid'] = $hero->getHid();
		// unset($arrRet['learned_normal_skills']);
		unset($arrRet['using_skill_time']);
		unset($arrRet['using_skill_num']);
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IHero::masterUsingSkill()
	 */
	public function masterUsingSkill ($skillId, $type)
	{
		$userObj = EnUser::getUserObj();
		$hero = $userObj->getMasterHeroObj();
		$hero->usingSkill($skillId, $type);
		$userObj->update();
		return 'ok';
	}
	
	/* (non-PHPdoc)
	 * @see IHero::getHeroByHid()
	 */
	public function getHeroByHid ($uid, $hid)
	{
		$userObj = EnUser::getUserObj($uid);
		$heroObj = $userObj->getHeroObj($hid);
		$hero = $heroObj->getAllAttr();
		$armingItem = $heroObj->getArmingItem();
		$daItem = $heroObj->getDaItem();
		$hero['armingInfo'] = $heroObj->arrItemInfo($armingItem);
		// $hero['daimonApple'] = $heroObj->arrItemInfo($daItem);
		// foreach ($hero['daimonApple'] as $daimonPos => $daimonInfo) {
			// $hero['daimonApple'][$daimonPos]['va_item_text'] = array('exp' => 0, 'evo_level' => 0);
		// }
		/*$sanWei = $heroObj->getSanWei();
		$hero['all_talnet_arrt']['evo_level'] = 0;
		list($sanWei['stg'], $sanWei['agile'], $sanWei['itg']) = array($sanWei['stg']/100, $sanWei['agile']/100, $sanWei['itg']/100);
		$hero['all_talnet_arrt'] = array_merge($hero['all_talnet_arrt'], $sanWei);
		$hero['all_talnet_arrt'] = array_merge($hero['all_talnet_arrt'], array('phyFDmgRatio' => 0, 'phyFEptRatio' => 0, 'killFDmgRatio' => 0, 'killFEptRatio' => 0, 'mgcFDmgRatio' => 0, 'mgcFEptRatio' => 0));
		$hero['all_talnet_arrt']['maxHp'] = $heroObj->getMaxHp();*/
		$hero['all_talnet_arrt'] = array();
		$hero['cur_formation'] = 10003;
		$hero['cur_formation_lv'] = 0;
		if ($heroObj->isMasterHero())
		{
			$hero['name'] = $userObj->getUname();
			$hero['using_skill'] = $hero['va_hero']['master']['using_skill'];
			$hero['transfer_num'] = $hero['va_hero']['master']['transfer_num'];
			$hero['talent_ast_id'] = Astrolabe::getCurTalentAstId ($uid);
			//$hero['learned_rage_skills'] = $hero['va_hero']['master']['learned_rage_skills'];
		}
		unset($hero['uid']);
		unset($hero['upgrade_time']);
		// unset($hero['va_hero']);
		// $va_hero = array('goodwill'=>$hero['va_hero']['goodwill']);
		// $hero['va_hero'] = $va_hero;
		// logger::warning($hero);	
		return $hero;
	}

	public function getMaxHp($hid)
	{
		$heroObj = EnUser::getUserObj()->getHeroObj($hid);
		return $heroObj->getMaxHp();
	}

	/* (non-PHPdoc)
	 * @see IHero::addGoodWillByItem()
	 */
	public function addGoodwillByItem ($hid, $gid, $itemId, $itemNum, $goldNum = 0)
	{
		//格式化输入
		$gid = intval($gid);
		$itemId = intval($itemId);
		$itemNum = intval($itemNum);
		
		$userObj = EnUser::getUserObj();
		$heroObj = $userObj->getHeroObj($hid);
		$heroObj->addGoodwillByItem($gid, $itemId, $itemNum, $goldNum);
		$userObj->update();

		$grid = BagManager::getInstance()->getBag()->update();		
		
		if ($goldNum !=0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_GOODWILL_GIFT, $goldNum, Util::getTime());
		}
		
		return array('ret'=>'ok', 'grid'=>$grid);
	}

	/* (non-PHPdoc)
	 * @see IHero::addGoodWillByGold()
	 */
	public function addGoodwillByGold ($hid)
	{
		$userObj = EnUser::getUserObj();
		$heroObj = $userObj->getHeroObj($hid);
		$heroObj->addGoodwillByGold();
		$userObj->update();				
		
		EnActive::addGoodwillGiftTimes();
		
		EnFestival::addGoldWillPoint();
		
		return array('ret'=>'ok');
	}

	/* (non-PHPdoc)
	 * @see IHero::masterLearnSkillFromOther()
	 */
	public function masterLearnSkillFromOther ($hid, $skillId)
	{
		if (!EnSwitch::isOpen(SwitchDef::TRANSFER))
		{
			Logger::warning('fail to learn skill, treasfer switch return false');
			throw new Exception('fake');
		}
		
		$userObj = EnUser::getUserObj();
		$heroObj = $userObj->getMasterHeroObj();
		$heroObj->learnSkillFromOther($hid, $skillId);
		$userObj->update();
				
		EnAchievements::notify($userObj->getUid(), AchievementsDef::LEARN_GOOD_WILL_SKILL_NUM, 1);		
		return array('ret'=>'ok');		
	}
	
	/* (non-PHPdoc)
	 * @see IHero::convert()
	 */
	public function getConvertHeroes()
	{
		$userObj = EnUser::getUserObj();
		return $userObj->getConvertHeroes();
	}
	
	/* (non-PHPdoc)
	 * @see IHero::convert()
	 */
	public function convert ($hid)
	{
		$userObj = EnUser::getUserObj();
		$heroObj = $userObj->getHeroObj($hid);
		$heroObj->convert();
		
		ItemManager::getInstance()->update();
		$userObj->update();
		SoulObj::getInstance()->save();		
		return 'ok';
	}
	
	/* (non-PHPdoc)
	 * @see IHero::convert()
	 */
	public function convertByHtid($htid)
	{
		$userObj = EnUser::getUserObj();
		$heroObj = $userObj->getHeroObjByHtid($htid);		
		$heroObj->convert();
		
		ItemManager::getInstance()->update();
		$userObj->update();
		SoulObj::getInstance()->save();		
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IHero::heritageGoodwill()
	 */
	public function heritageGoodwill ($srcHid, $desHid, $type)
	{
		if ($srcHid == $desHid)
		{
			Logger::warning('hero %d heritage goodwill to himself ', $srcHid);
			throw new Exception('fake');
		}
		
		$userObj = EnUser::getUserObj();
		if ($userObj->getTodayHeritageGoodwillNum()>=UserConf::NUM_HERIAGE_GOODWILL)
		{
			Logger::warning('fail to heritage goodwill, num over max');
			throw new Exception('fake');
		}
		
		$arrRet = array('ret'=>'ok', 'res'=>array('grid'=>array()));
		
		$srcHero = $userObj->getHeroObj($srcHid);
		$srcHero->heritageGoodwill($desHid, $type);			
						
		//消耗物品
		if ($type!=0)
		{
			$arrRet['res']['grid'] = BagManager::getInstance()->getBag()->update();
		}
		
		$userObj->addHeritageGoodwillNum(1);
		$userObj->update();
		
		return $arrRet;		
	}
	
	/* (non-PHPdoc)
	 * @see IHero::moveAllArmingAndJewelry()
	 */
	public function moveAllArmingAndJewelry ($srcHid, $desHid)
	{
		
		$moveEquip = new HeroMoveEquip($srcHid, $desHid);
		if (!$moveEquip->moveAllEquip('arming'))
		{
			Logger::warning('fail to move all arming');
			return 'fail';
		}
		
		if (!$moveEquip->moveAllEquip('jewelry'))
		{
			Logger::warning('fail to move all jewelry');
			return 'fail';
		}
		
		$moveEquip->update();
		return 'ok';

	}

	public function addElementItem($hid, $position, $itemId)
	{
		if (!EnSwitch::isOpen(SwitchDef::ELEMENT))
		{
			Logger::warning('fail to addElementItem, switch is no open');
			throw new Exception('fake');
		}
		
		$itemId = intval($itemId);		
	
		$item_manager = ItemManager::getInstance();
		$item = $item_manager->getItem($itemId);
		$return = array ('add_success' => FALSE, 'gid' => 0 );
	
		if ($item === NULL || $item->getItemType() != ItemDef::ITEM_ELEMENT ||
				!isset(ElementDef::$ELEMENT_POSITIONS[$position])
				|| !in_array($item->getElementType(), ElementDef::$ELEMENT_POSITIONS[$position]))
		{
			Logger::warning('item not exist!');
			return $return;
		}
	
		return $this->addEquip('element', $hid, $position, $itemId);
	}

	public function removeElementItem($hid, $position)
	{
		return $this->removeEquip('element', $hid, $position);
	}
	
	public function moveElementItem($srcHid, $desHid, $position)
	{
		$moveEquip = new HeroMoveEquip($srcHid, $desHid);		
		if (!$moveEquip->moveEquip('element', $position))
		{
			return false;
		}		
		
		$moveEquip->update();
		return true;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */