<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Bag.class.php 39705 2013-03-01 03:13:25Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/bag/Bag.class.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-03-01 11:13:25 +0800 (五, 2013-03-01) $
 * @version $Revision: 39705 $
 * @brief
 *
 **/




class Bag implements IBag
{
	/**
	 *
	 * 用户UID
	 * @var int
	 */
	private $m_uid;

	/**
	 *
	 * 用户背包数据
	 * @var array
	 */
	private $m_user_bag;

	/**
	 *
	 * 用户背包的格子数
	 * @var int
	 */
	private $m_user_bag_max_grid;

	/**
	 *
	 * 临时背包数据
	 * @var array
	 */
	private $m_tmp_bag;

	/**
	 *
	 * 任务背包数据
	 * @var array
	 */
	private $m_mission_bag;

	/**
	 *
	 * 仓库背包数据
	 * @var array
	 */
	private $m_depot_bag;

	/**
	 *
	 * 仓库背包的格子数
	 * @var int
	 */
	private $m_depot_bag_max_grid;

	/**
	 *
	 * 被修改的数据
	 * @var array(int)
	 */
	private $m_modify = array();

	/**
	 * 是否执行出错
	 * @var boolean
	 */
	private $error = FALSE;

	/**
	 *
	 * 物品管理器对象
	 * @var ItemManager
	 */
	private $m_manager;

	private $m_original_user_bag;

	private $m_original_tmp_bag;

	private $m_original_mission_bag;

	private $m_original_depot_bag;

	public function Bag()
	{
		$this->m_uid = RPCContext::getInstance()->getSession(BagDef::SESSION_USER_ID);
		if ( $this->m_uid == 0 )
		{
			Logger::FATAL('invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
		$this->m_manager = ItemManager::getInstance();
		$bagData = $this->getBagData();
		$this->m_user_bag = $bagData[BagDef::USER_BAG];
		$this->m_tmp_bag = $bagData[BagDef::TMP_BAG];
		$this->m_mission_bag = $bagData[BagDef::MISSION_BAG];
		$this->m_depot_bag = $bagData[BagDef::DEPOT_BAG];
		$this->m_user_bag_max_grid = count($this->m_user_bag);
		$this->m_depot_bag_max_grid = count($this->m_depot_bag);
		//保存处理前的数据
		$this->m_original_user_bag = $this->m_user_bag;
		$this->m_original_tmp_bag = $this->m_tmp_bag;
		$this->m_original_mission_bag = $this->m_mission_bag;
		$this->m_original_depot_bag = $this->m_depot_bag;
		$this->initBag();
		//处理临时背包过期数据
		$this->expire();
	}

	/* (non-PHPdoc)
	 * @see IBag::bagInfo()
	 */
	public function bagInfo() {
		//一次性拉取所有物品数据(临时背包已经拉取完毕)
		$item_ids = array_merge($this->m_user_bag, $this->m_mission_bag);
		$this->m_manager->getItems($item_ids);
		$return = array();
		//用户背包
		$return[BagDef::USER_BAG] = $this->__info($this->m_user_bag);
		//临时背包
		$return[BagDef::TMP_BAG] = $this->__info($this->m_tmp_bag);
		//任务背包
		$return[BagDef::MISSION_BAG] = $this->__info($this->m_mission_bag);
		//仓库背包
		$return[BagDef::DEPOT_BAG] = $this->__info($this->m_depot_bag);
		//背包分段信息
		$return[BagDef::USER_BAG_GRID_START_NAME] = BagDef::USER_BAG_GRID_START;
		$return[BagDef::TMP_BAG_GRID_START_NAME] = BagDef::TMP_BAG_GRID_START;
		$return[BagDef::MISSION_BAG_GRID_START_NAME] = BagDef::MISSION_BAG_GRID_START;
		$return[BagDef::DEPOT_BAG_GRID_START_NAME] = BagDef::DEPOT_BAG_GRID_START;
		//用户背包当前开启的最大格子数
		$return[BagDef::USER_BAG_MAX_GRID_NAME]	= $this->m_user_bag_max_grid;
		//仓库当前开启的最大格子数
		$return[BagDef::DEPOT_BAG_MAX_GRID_NAME] = $this->m_depot_bag_max_grid;
		//背包的最大格子数
		$return[BagDef::USER_BAG_GRID_NUM_NAME] = BagConfig::USER_BAG_GRID_NUM;
		$return[BagDef::TMP_BAG_GRID_NUM_NAME] = BagConfig::TMP_BAG_GRID_NUM;
		$return[BagDef::MISSION_BAG_GRID_NUM_NAME] = BagConfig::MISSION_BAG_GRID_NUM;
		$return[BagDef::DEPOT_BAG_GRID_NUM_NAME] = BagConfig::DEPOT_BAG_GRID_NUM;
		//临时背包过期时间
		$return[BagDef::TMP_BAG_EXPIRE_TIME_NAME] = BagConfig::TMP_BAG_EXPIRE_TIME;
		$this->update();
		return $return;
	}

	private function __info($bag)
	{
		$return = array();
		foreach ($bag as $gid => $item_id)
		{
			if ( $item_id != BagDef::ITEM_ID_NO_ITEM )
			{
				$return[$gid] = $this->gridInfo($gid);
			}
		}
		return $return;
	}

	/* (non-PHPdoc)
	 * @see IBag::gridInfo()
	 */
	public function gridInfo($gid)
	{
		//格式化输入
		$gid = intval($gid);

		$bag = $this->gid2Bag($gid);
		if ( !isset($bag[$gid]) || $bag[$gid] == BagDef::ITEM_ID_NO_ITEM )
			return array();
		else
		{
			$item = $this->m_manager->getItem($bag[$gid]);

			if ( $item === NULL )
			{
				$this->setBagData($gid, BagDef::ITEM_ID_NO_ITEM);
				Logger::FATAL('fixed invalid item!user:%d has invalid item:%d in gid:%d!',
					 RPCContext::getInstance()->getUid(), $bag[$gid], $gid);
				return array();
			}
			return $item->itemInfo();
		}
	}

	/* (non-PHPdoc)
	 * @see IBag::gridInfos()
	 */
	public function gridInfos($gids)
	{
		$return = array();
		foreach ( $gids as $gid )
		{
			$return[$gid] = $this->gridInfo($gid);
		}
		return $return;
	}

	private function expire()
	{
		//拉取临时背包的数据
		$item_ids = array_merge($this->m_tmp_bag);
		$this->m_manager->getItems($item_ids);
		$return = array();
		foreach ($this->m_tmp_bag as $gid => $item_id)
		{
			if ( $item_id != BagDef::ITEM_ID_NO_ITEM )
			{
				$item = $this->m_manager->getItem($item_id);
				if ( $item === NULL || Util::getTime() > $item->getItemTime() + BagConfig::TMP_BAG_EXPIRE_TIME )
				{
					$this->setBagData($gid, BagDef::ITEM_ID_NO_ITEM);
					$this->m_manager->deleteItem($item_id);
				}
			}
		}
		$this->update();
		return $return;
	}

	/* (non-PHPdoc)
	 * @see IBag::moveItem()
	 */
	public function moveItem($src_gid, $des_gid) {

		//格式化输入
		$src_gid = intval($src_gid);
		$des_gid = intval($des_gid);

		if ( $src_gid == $des_gid )
		{
			Logger::WARNING('src_gid == des_gid');
			return FALSE;
		}

		if ( ( !isset($this->m_user_bag[$src_gid]) && !isset($this->m_depot_bag[$src_gid]) )
		 || ( !isset($this->m_user_bag[$des_gid]) && !isset($this->m_depot_bag[$des_gid]) ) )
		{
			Logger::WARNING('src_gid or des_gid is not exist!');
			return FALSE;
		}

		if ( $src_gid >= BagDef::DEPOT_BAG_GRID_START )
		{
			$src_item_id = $this->m_depot_bag[$src_gid];
		}
		else
		{
			$src_item_id = $this->m_user_bag[$src_gid];
		}

		if ( $des_gid >= BagDef::DEPOT_BAG_GRID_START )
		{
			$des_item_id = $this->m_depot_bag[$des_gid];
		}
		else
		{
			$des_item_id = $this->m_user_bag[$des_gid];
		}

		if ( $src_item_id == 0 )
		{
			Logger::WARNING('src item is null');
			return FALSE;
		}
		$src_item = $this->m_manager->getItem($src_item_id);
		$des_item = $this->m_manager->getItem($des_item_id);

		//目的目标无物品;或源目标物品和目的目标物品不是同一种;或者是同一种,但是不可以叠加
		//则进行互换操作
		if ( $des_item_id == 0 ||
				$src_item->getItemTemplateID() != $des_item->getItemTemplateID() ||
				( $src_item->getItemTemplateID() == $des_item->getItemTemplateID() &&
				!$src_item->canStackable() ) )
		{
			$this->setBagData($src_gid, $des_item_id);
			$this->setBagData($des_gid, $src_item_id);
		}
		//否则源目标向目的目标合并
		else
		{
			$this->m_manager->unionItem($src_item_id, $des_item_id);
			//如果源目标物品被完全合并
			if ( $this->m_manager->getItem($src_item_id) === NULL )
			{
				$this->setBagData($src_gid, BagDef::ITEM_ID_NO_ITEM);
			}
		}
		$this->update();
		return TRUE;
	}

	/* (non-PHPdoc)
	 * @see IBag::swapBagDepot()
	 */
	public function swapBagDepot($gid)
	{
		if ( $gid >= BagDef::USER_BAG_GRID_START
			 && $gid < BagDef::TMP_BAG_GRID_START )
		{
			if ( !isset($this->m_user_bag[$gid]) )
			{
				Logger::WARNING("the user grid:%d is not open!", $gid);
				throw new Exception('fake');
			}
			$item_id = $this->m_user_bag[$gid];
			if ( $this->addDepotItem($item_id) == FALSE )
			{
				return array();
			}
			$this->setBagData($gid, ItemDef::ITEM_ID_NO_ITEM);
		}
		else if ( $gid >= BagDef::DEPOT_BAG_GRID_START )
		{
			if ( !isset($this->m_depot_bag[$gid]) )
			{
				Logger::WARNING("the depot grid:%d is not open!", $gid);
				throw new Exception('fake');
			}
			$item_id = $this->m_depot_bag[$gid];
			if ( $this->addItem($item_id) == FALSE )
			{
				return array();
			}
			$this->setBagData($gid, ItemDef::ITEM_ID_NO_ITEM);
		}
		else
		{
			Logger::WARNING("invalid grid:%d!", $gid);
			throw new Exception('fake');
		}

		return $this->update();
	}

	/* (non-PHPdoc)
	 * @see IBag::useItem()
	 */
	public function useItem($gid, $item_id, $item_num, $item_choose = FALSE)
	{
		//格式化输入
		$gid = intval($gid);
		$item_id = intval($item_id);
		$item_num = intval($item_num);
		$item_num_bak = $item_num;

		$return = array('use_success' => FALSE);
		$petInfo = array();

		//物品是否存在
		if ( !isset($this->m_user_bag[$gid]) || $this->m_user_bag[$gid] != $item_id )
		{
			Logger::WARNING('invalid item_id:%d not in gid:%d', $item_id, $gid);
			return $return;
		}

		//物品数量是否合法
		if ( $item_num <= 0 )
		{
			Logger::WARNING('invalid item_num:%d', $item_num);
			return $return;
		}

		$item = $this->m_manager->getItem($item_id);
		$item_template_id = $item->getItemTemplateID();
		$item_type = $item->getItemType();

		//物品是否存在于系统中,如果不存在于系统中，则是一个致命bug!
		if ( $item === NULL )
		{
			Logger::FATAL('fixed me!invalid item_id:%d in bag!', $item_id);
			return $return;
		}

		//物品是否足够
		if ( $this->decreaseItem($item_id, $item_num) == FALSE )
		{
			Logger::WARNING('use item invalid!item_id:%d, item_num:%d', $item_id, $item_num);
			return $return;
		}

		//如果是碎片物品,则需要当前的数量等于堆叠数量才能使用
		if ( $item_type == ItemDef::ITEM_FRAGMENT )
		{
			if ( $item_num % $item->getStackable() != 0 )
			{
				Logger::WARNING('use fragment item invalid!item_id:%d, item_num:%d', $item_id, $item_num);
				return $return;
			}
			else
			{
				$item_num /= $item->getStackable();
			}
		}

		//得到使用需求
		$useReqInfo = $item->useReqInfo();
		//得到试用获得信息
		$useInfo = $item->useInfo();

		//检测是否不可使用
		if ( empty($useReqInfo) && empty($useInfo) )
		{
			Logger::FATAL("item_id:%d item_template_id:%d not exist!", $item_id, $item_template_id);
			throw new Exception('fake');
		}

		$user = EnUser::getUserObj();

		//物品使用需求
		if ( !empty($useReqInfo) )
		{
			//使用需要消耗belly
			if ( isset($useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_BELLY]) )
			{
				if ( $user->subBelly($useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_BELLY] * $item_num) == FALSE )
				{
					Logger::WARNING('no enough belly!');
					return $return;
				}
			}

			//使用需要消耗gold
			if ( isset($useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_GOLD]) )
			{
				if ( $user->subGold($useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_GOLD] * $item_num) == FALSE )
				{
					Logger::WARNING('no enough gold!');
					return $return;
				}
			}

			//检测延迟使用时间
			if ( isset($useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_DELAYTIME]) )
			{
				$time = Util::getTime();
				if ( $time - $item->getItemTime() < $useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_DELAYTIME] )
				{
					Logger::WARNING('the item need wait time:%d!\n', $useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_DELAYTIME]);
					return $return;
				}
			}

			//使用消耗物品
			if ( isset($useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_ITEMS]) )
			{
				$items = $useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_ITEMS]->toArray();
				foreach ( $items as $key => $value )
				{
					$items[$key] = $value * $item_num;
				}
				if ( $this->deleteItemsByTemplateID($items) == FALSE )
				{
					Logger::WARNING('no item!%s', $useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_ITEMS]);
					return $return;
				}
			}

			//检测用户等级
			if ( isset( $useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_USER_LEVEL] ) )
			{
				$user_level = $user->getLevel();
				if ( $user_level < $useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_USER_LEVEL][0] ||
					$user_level >= $useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_USER_LEVEL][1] )
				{
					Logger::WARNING('invalid user level:%d not in [%d, %d)!', $user_level,
						$useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_USER_LEVEL][0],
						$useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_USER_LEVEL][1]);
					return $return;
				}
			}
		}

		//物品使用得到
		$use_items4chat = array();
		if ( !empty($useInfo) )
		{
			//使用得到物品
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_ITEMS]) )
			{
				$use_items = $useInfo[ItemDef::ITEM_ATTR_NAME_USE_ITEMS]->toArray();
				foreach ( $use_items as $template_id => $value )
				{
					$use_items[$template_id] = $value * $item_num;
				}
				$use_item_ids = $this->m_manager->addItems($use_items);
				$use_items4chat = ChatTemplate::prepareItem($use_item_ids);
				if ( $this->addItems($use_item_ids)== FALSE )
				{
					Logger::DEBUG('full bag!items:%s', $use_items);
					return $return;
				}
			}

			//使用得到belly
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_BELLY]) )
			{
				if ( $user->addBelly($useInfo[ItemDef::ITEM_ATTR_NAME_USE_BELLY] * $item_num) == FALSE )
				{
					Logger::FATAL('add belly failed!');
					return $return;
				}
			}

			//使用得到gold
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_GOLD]) )
			{
				if ( $user->addGold($useInfo[ItemDef::ITEM_ATTR_NAME_USE_GOLD] * $item_num) == FALSE )
				{
					Logger::FATAL('add gold failed!');
					return $return;
				}
			}

			//使用得到血库
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_BLOOD_PACKAGE]) )
			{
				if ( $user->addBloodPackage($useInfo[ItemDef::ITEM_ATTR_NAME_USE_BLOOD_PACKAGE] * $item_num ) == FALSE )
				{
					Logger::FATAL('add blood package failed!');
					return $return;
				}
			}

			//使用得到食物
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_FOOD]) )
			{
				if ( $user->addFood($useInfo[ItemDef::ITEM_ATTR_NAME_USE_FOOD] * $item_num) == FALSE )
				{
					Logger::DEBUG('add food failed!');
					return $return;
				}
			}

			//使用得到行动力
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_EXECUTION]) )
			{
				if ( $user->addExecution($useInfo[ItemDef::ITEM_ATTR_NAME_USE_EXECUTION] * $item_num) == FALSE )
				{
					Logger::FATAL('add execution failed!');
					return $return;
				}
			}

			//使用得到阅历
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_EXPRIENCE]) )
			{
				if ( $user->addExperience($useInfo[ItemDef::ITEM_ATTR_NAME_USE_EXPRIENCE] * $item_num) == FALSE )
				{
					Logger::DEBUG('add exprience failed!');
					return $return;
				}
			}

			//使用增加称号
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_TITLE]) )
			{
				EnAchievements::addNewTitle($useInfo[ItemDef::ITEM_ATTR_NAME_USE_TITLE]);
			}

			//使用得到星灵石
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_STAR_STONE]) )
			{
				Astrolabe::addStone($this->m_uid, $useInfo[ItemDef::ITEM_ATTR_NAME_USE_STAR_STONE]* $item_num);
			}

			//使用得到hero
			if (isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_HERO]))
			{
				$htid=$useInfo[ItemDef::ITEM_ATTR_NAME_USE_HERO];
				$user = EnUser::getUserObj();
				if (!HeroUtil::isHeroByHtid($htid))
				{
					Logger::FATAL('add herr failed! htid:%d',$htid);
					return $return;
				}
				if ($user->hasHero($htid))
				{
					Logger::FATAL('add herr failed! already has hero htid:%d',$htid);
					return $return;
				}
				$user->addNewHeroToPub($htid);
				Logger::info('Bag.useItem add new hero to pub by item! htid:%d item_id:%d gid:%d',$htid,$item_id,$gid);
			}

			//使用后增加寻宝的紫星
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_TREASURE_PURPLE]) )
			{
				EnTreasure::addScore(0, $useInfo[ItemDef::ITEM_ATTR_NAME_USE_TREASURE_PURPLE]* $item_num);
			}
			
			//使用后增加寻宝的红星
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_TREASURE_RED]) )
			{
				EnTreasure::addScore($useInfo[ItemDef::ITEM_ATTR_NAME_USE_TREASURE_RED]* $item_num, 0);
			}

			//使用后增加装备制作的紫星
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_EQUIP_PURPLE]) )
			{
				EnSmelting::addIntegralWithoutFestival(0, $useInfo[ItemDef::ITEM_ATTR_NAME_USE_EQUIP_PURPLE]* $item_num);
			}
			
			//使用后增加装备制作的红星
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_EQUIP_RED]) )
			{
				EnSmelting::addIntegralWithoutFestival($useInfo[ItemDef::ITEM_ATTR_NAME_USE_EQUIP_RED]* $item_num, 0);
			}
			
			//使用后增加海魂石
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_SEA_SOUL]) )
			{
				SeaSoulLogic::addStone($useInfo[ItemDef::ITEM_ATTR_NAME_USE_SEA_SOUL]);
			}


			//使用掉落物品
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_DROP_TEMPLATE_ID]) )
			{
				for ( $i = 0; $i < $item_num; $i++ )
				{
					$items = $this->m_manager->dropItem($useInfo[ItemDef::ITEM_ATTR_NAME_USE_DROP_TEMPLATE_ID]);
					$use_items4chat = ChatTemplate::prepareItem($items);
					if ( $this->addItems($items) == FALSE )
					{
						Logger::DEBUG('full bag!');
						return $return;
					}
				}
			}

			//使用得到宠物
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_PET_TEMPLATE_ID]) )
			{
				for ( $i = 0; $i < $item_num; $i++ )
				{
					$petEggType = ItemAttr::getIteMAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_PET_EGG_TYPE);					
					switch ($petEggType)
					{
						case 0:
							$petInfo = EnCoPet::hatch($useInfo[ItemDef::ITEM_ATTR_NAME_USE_PET_TEMPLATE_ID]);
							if ( $petInfo === FALSE )
							{
								Logger::FATAL('hatch copet failed!');
								return $return;
							}
							break;
						case 1:
							$petInfo = EnPet::hatch($useInfo[ItemDef::ITEM_ATTR_NAME_USE_PET_TEMPLATE_ID]);
							if ( $petInfo === FALSE )
							{
								Logger::FATAL('hatch pet failed!');
								return $return;
							}
							break;
					}
				}
			}
			
			//使用得到坐骑
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_MOUNT_TEMPLATE_ID]) )
			{
				for ( $i = 0; $i < $item_num; $i++ )
				{
					if ( RideLogic::addRide($this->m_uid, $useInfo[ItemDef::ITEM_ATTR_NAME_USE_MOUNT_TEMPLATE_ID]) == FALSE )
					{
						Logger::FATAL('hatch ride failed!');
						return $return;
					}
				}
			}
			
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_ITEM_CHOOSE]) )
			{				
				$item_choose_num = $useInfo[ItemDef::ITEM_ATTR_NAME_USE_ITEM_CHOOSE][$item_choose];
				if ( $this->addItemByTemplateID($item_choose, $item_choose_num)== FALSE )
				{
					Logger::DEBUG('full bag!items:%d', $item_choose);
					return $return;
				}
			}
			
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_HTID_ITEMS]) )
			{							
				$item_htid = $useInfo[ItemDef::ITEM_ATTR_NAME_USE_HTID_ITEMS]->toArray();
				$htid = UserConf::$USER_INFO[$user->getUtid()][1];
				$itemis = $item_htid[$htid];
				$itemssss = array();
				foreach ($itemis as $id => $num) {
					$itemssss = array('itemid' => $id, 'itemnum' => $num);
				}
				if ( $this->addItemByTemplateID($itemssss['itemid'], $itemssss['itemnum'])== FALSE )
				{
					Logger::DEBUG('full bag!items:%d', $itemssss['itemid']);
					return $return;
				}
			}
			
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_DAIMONAPPLE_EXP]) )
			{				
				$retExp = $useInfo[ItemDef::ITEM_ATTR_NAME_USE_DAIMONAPPLE_EXP];
				AppleFactoryLogic::addExp($retExp);
			}
			
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_GEM_ESSENCE]) )
			{				
				$info = GemMatrixDao::get($this->m_uid, array('elite'));
				$retEssence = $useInfo[ItemDef::ITEM_ATTR_NAME_USE_GEM_ESSENCE];
				$curEssence = $info['elite'];
				GemMatrixLogic::updateScoreElite($this->m_uid, NULL,$curEssence+$retEssence);				
			}
			
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_GEM_SCORE]) )
			{				
				$info = GemMatrixDao::get($this->m_uid, array('score'));
				$retScore = $useInfo[ItemDef::ITEM_ATTR_NAME_USE_GEM_SCORE];
				$curScore = $info['score'];
				GemMatrixLogic::updateScoreElite($this->m_uid, $curScore+$retScore, NULL);
			}

			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_DECORATION]) )
			{
				HorseDecorationLogic::addDecorationId($this->m_uid, $useInfo[ItemDef::ITEM_ATTR_NAME_USE_DECORATION], $item_id); 
			}
			
			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_DECORATION_CRYSTAL]) )
			{				
				HorseDecorationLogic::addResource($this->m_uid, $useInfo[ItemDef::ITEM_ATTR_NAME_USE_DECORATION_CRYSTAL]); 
			}

			if ( isset($useInfo[ItemDef::ITEM_ATTR_NAME_USE_DOMINEER]) )
			{
				return $return;
			}

		}

		//更新用户数据
		$user->update();

		//更新物品数据
		$bag_change_info = $this->update();

		//chat
		if ( !empty($use_items4chat) )
		{
			if ( $item_type == ItemDef::ITEM_FRAGMENT )
			{
				ChatTemplate::sendFragmentItem($user->getTemplateUserInfo(), $user->getGroupId(),
					array(BagDef::BAG_ITEM_ID=>0, ItemDef::ITEM_ATTR_NAME_ITEM_NUM=>$item_num_bak,
					ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID=>$item_template_id), $use_items4chat );
			}
			else
			{
				ChatTemplate::sendCommonItem($user->getTemplateUserInfo(),
					 $user->getGroupId(), $use_items4chat);
			}
		}

		//Statistics
		if ( !empty($useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_GOLD]) )
		{
			Statistics::gold4Item(StatisticsDef::ST_FUNCKEY_BAG_USEITEM,
					 $useReqInfo[ItemDef::ITEM_ATTR_NAME_USE_REQ_GOLD] * $item_num,
					 $item_template_id, $item_num_bak, Util::getTime());
		}
		if ( !empty($useInfo[ItemDef::ITEM_ATTR_NAME_USE_GOLD]) )
		{
			Statistics::gold4Item(StatisticsDef::ST_FUNCKEY_BAG_USEITEM,
					 $useInfo[ItemDef::ITEM_ATTR_NAME_USE_GOLD] * $item_num,
					 $item_template_id, $item_num_bak, Util::getTime(), FALSE);
		}

		$return['use_success'] = TRUE;
		$return['bag_modify'] = $bag_change_info;
		$return['pet_modify'] = $petInfo;
		return $return;
	}

	/* (non-PHPdoc)
	 * @see IBag::destoryItem()
	 */
	public function destoryItem($gid, $item_id) {
		//格式化输入
		$gid = intval($gid);
		$item_id = intval($item_id);

		//检测是否存在这个物品
		$bag = $this->gid2Bag($gid);
		if ( !isset($bag[$gid]) || $item_id != $bag[$gid] )
		{
			Logger::DEBUG('invalid gid:%d or item_id:%d', $gid, $item_id);
			return FALSE;
		}
		else
		{
			if ( $this->m_manager->destoryItem($item_id) === TRUE )
			{
				Logger::DEBUG('destory item:%d', $item_id);
				$this->setBagData($gid, BagDef::ITEM_ID_NO_ITEM);
				$this->update();
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		return FALSE;
	}

	/* (non-PHPdoc)
	 * @see IBag::receiveItem()
	 */
	public function receiveItem($gid, $item_id)
	{
		if ( !isset($this->m_tmp_bag[$gid]) || $this->m_tmp_bag[$gid] != $item_id )
		{
			return array();
		}
		else
		{
			$return = $this->__addItem($item_id, $this->m_user_bag, BagDef::USER_BAG_GRID_START,
						 BagDef::USER_BAG_GRID_START + BagConfig::USER_BAG_GRID_NUM, FALSE );
			if ( $return == FALSE )
			{
				return array('receive_success' => FALSE);
			}
			else
			{
				Logger::DEBUG('receive item:%d', $item_id);
				$this->setBagData($gid, BagDef::ITEM_ID_NO_ITEM);
				$return = array (
					'receive_success' => TRUE,
					'bag_modify' => $this->update(),
				);
				return $return;
			}
		}
	}

	/* (non-PHPdoc)
	 * @see IBag::openGrid()
	 */
	public function openGrid($grid_num)
	{
		$grid_num = intval($grid_num);

		//输入参数是否合法
		if ( $grid_num <= 0 )
		{
			Logger::WARNING('grid_num:%d <= 0', $grid_num);
			return FALSE;
		}

		//是否存在数据错误
		if ( $this->m_user_bag_max_grid > BagConfig::USER_BAG_GRID_NUM )
		{
			Logger::FATAL('fixed me!user:%d, user grid count:%d is invalid! bigger than %d',
				$this->m_uid, $this->m_user_bag_max_grid, BagConfig::USER_BAG_GRID_NUM);
			return FALSE;
		}
		//当前是否已经格子开放到最大
		else if ( $this->m_user_bag_max_grid + $grid_num > BagConfig::USER_BAG_GRID_NUM )
		{
			Logger::WARNING('invalid grid_num:%d!', $grid_num);
			return FALSE;
		}
		else
		{
			$all_gold = 0;
			for ( $i = 0; $i < $grid_num; $i++ )
			{
				$this->setBagData($this->m_user_bag_max_grid+1, BagDef::ITEM_ID_NO_ITEM);
				$gold = BagConfig::BAG_UNLOCK_GOLD +
						($this->m_user_bag_max_grid - BagConfig::BAG_UNLOCK_GID_START) * BagConfig::BAG_UNLOCK_GOLD_STEP;
				$all_gold += $gold;
				$this->m_user_bag_max_grid++;
				$user = EnUser::getInstance();
				if ( $user->subGold($gold) == FALSE )
				{
					Logger::WARNING('no enough gold!');
					return FALSE;
				}
			}

			//更新用户数据
			$user->update();

			//更新背包数据
			$this->update();

			//Statistics
			Statistics::gold(StatisticsDef::ST_FUNCKEY_BAG_OPENGRID, $all_gold, Util::getTime());

			// 通知成就系统
			EnAchievements::notify($user->getUid(), AchievementsDef::BAG_GRID_NUM, $this->m_user_bag_max_grid);
			return TRUE;
		}
		return FALSE;
	}

	/* (non-PHPdoc)
	 * @see IBag::openGrid()
	 */
	public function openDepotGrid($grid_num)
	{
		$grid_num = intval($grid_num);

		//输入参数是否合法
		if ( $grid_num <= 0 )
		{
			Logger::WARNING('grid_num:%d <= 0', $grid_num);
			return FALSE;
		}

		//是否存在数据错误
		if ( $this->m_depot_bag_max_grid > BagConfig::DEPOT_BAG_GRID_NUM )
		{
			Logger::FATAL('fixed me!user:%d, user grid count:%d is invalid! bigger than %d',
				$this->m_uid, $this->m_depot_bag_max_grid, BagConfig::DEPOT_BAG_GRID_NUM);
			return FALSE;
		}
		//当前是否已经格子开放到最大
		else if ( $this->m_depot_bag_max_grid + $grid_num > BagConfig::DEPOT_BAG_GRID_NUM )
		{
			Logger::WARNING('invalid grid_num:%d!', $grid_num);
			return FALSE;
		}
		else
		{
			$all_gold = 0;
			for ( $i = 0; $i < $grid_num; $i++ )
			{
				$this->setBagData($this->m_depot_bag_max_grid+BagDef::DEPOT_BAG_GRID_START,
					 BagDef::ITEM_ID_NO_ITEM);
				$gold = BagConfig::DEPOT_BAG_UNLOCK_GOLD +
						($this->m_depot_bag_max_grid - BagConfig::DEPOT_BAG_UNLOCK_GID_START) * BagConfig::DEPOT_BAG_UNLOCK_GOLD_STEP;
				$all_gold += $gold;
				$this->m_depot_bag_max_grid++;
				$user = EnUser::getInstance();
				if ( $user->subGold($gold) == FALSE )
				{
					Logger::WARNING('no enough gold!');
					return FALSE;
				}
			}

			//更新用户数据
			$user->update();

			//更新背包数据
			$this->update();

			//Statistics
			Statistics::gold(StatisticsDef::ST_FUNCKEY_BAG_OPENGRID, $all_gold, Util::getTime());

			return TRUE;
		}
		return FALSE;
	}

	/* (non-PHPdoc)
	 * @see IBag::openGridByItem()
	 */
	public function openGridByItem($grid_num)
	{
		$grid_num = intval($grid_num);

		$return = array ( 'open_success' => FALSE );
		//输入参数是否合法
		if ( $grid_num <= 0 )
		{
			Logger::WARNING('grid_num:%d <= 0', $grid_num);
			return $return;
		}

		//是否存在数据错误
		if ( $this->m_user_bag_max_grid > BagConfig::USER_BAG_GRID_NUM )
		{
			Logger::FATAL('fixed me!user:%d, user grid count:%d is invalid! bigger than %d',
				$this->m_uid, $this->m_user_bag_max_grid, BagConfig::USER_BAG_GRID_NUM);
			return $return;
		}
		//当前是否已经格子开放到最大
		else if ( $this->m_user_bag_max_grid + $grid_num > BagConfig::USER_BAG_GRID_NUM )
		{
			Logger::WARNING('invalid grid_num:%d!', $grid_num);
			return $return;
		}
		else
		{
			$all_gold = 0;

			if ( $this->deleteItembyTemplateID(BagConfig::BAG_UNLOCK_ITEM_ID, $grid_num) == FALSE )
			{
				return $return;
			}

			for ( $i = 0; $i < $grid_num; $i++ )
			{
				$this->setBagData($this->m_user_bag_max_grid+1, BagDef::ITEM_ID_NO_ITEM);
				$this->m_user_bag_max_grid++;
			}

			//更新背包数据
			$bag_modify = $this->update();

			// 通知成就系统
			EnAchievements::notify(RPCContext::getInstance()->getUid(), AchievementsDef::BAG_GRID_NUM, $this->m_user_bag_max_grid);
			return array (
				'open_success' => TRUE,
				'bag_modify' => $bag_modify
			);
		}
		return $return;
	}

	public function openDepotGridByItem($grid_num)
	{
		$grid_num = intval($grid_num);

		$return = array ( 'open_success' => FALSE );
		//输入参数是否合法
		if ( $grid_num <= 0 )
		{
			Logger::WARNING('grid_num:%d <= 0', $grid_num);
			return $return;
		}

		//是否存在数据错误
		if ( $this->m_depot_bag_max_grid > BagConfig::DEPOT_BAG_GRID_NUM )
		{
			Logger::FATAL('fixed me!user:%d, user grid count:%d is invalid! bigger than %d',
				$this->m_uid, $this->m_depot_bag_max_grid, BagConfig::DEPOT_BAG_GRID_NUM);
			return $return;
		}
		//当前是否已经格子开放到最大
		else if ( $this->m_depot_bag_max_grid + $grid_num > BagConfig::DEPOT_BAG_GRID_NUM )
		{
			Logger::WARNING('invalid grid_num:%d!', $grid_num);
			return $return;
		}
		else
		{		
			if ( $this->deleteItembyTemplateID(BagConfig::DEPOT_BAG_UNLOCK_ITEM_ID, $grid_num) == FALSE )
			{
				return $return;
			}
			for ( $i = 0; $i < $grid_num; $i++ )
			{
				$this->setBagData($this->m_depot_bag_max_grid+BagDef::DEPOT_BAG_GRID_START, BagDef::ITEM_ID_NO_ITEM);
				$this->m_depot_bag_max_grid++;
			}

			//更新背包数据			
			$bag_modify = $this->update();

			return array (
				'open_success' => TRUE,
				'bag_modify' => $bag_modify);
		}
		return $return;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IBag::arrange()
	 */
	public function arrange()
	{
		return self::__arrange(BagDef::USER_BAG);
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function arrangeDepot()
	{
		return self::__arrange(BagDef::DEPOT_BAG);
	}

	private function __arrange($type)
	{
		$array = array();

		//拉取背包的数据
		$bag = array();
		switch ( $type )
		{
			case BagDef::USER_BAG:
				$bag = &$this->m_user_bag;
				break;
			case BagDef::DEPOT_BAG:
				$bag = &$this->m_depot_bag;
				break;
			default:
				throw new Exception('invalid type!');
				break;
		}

		$item_ids = array_merge($bag);

		foreach ( $item_ids as $key => $item_id )
		{
			if ( $item_id == ItemDef::ITEM_ID_NO_ITEM )
			{
				unset($item_ids[$key]);
			}
		}

		$item_ids = array_merge($item_ids);
		ItemManager::getInstance()->getItems($item_ids);


		Logger::DEBUG('item_ids:%s', $item_ids);
		usort($item_ids, 'Bag::compare');

		$i = 0;
		$values = array();

		foreach ( $bag as $key => $value )
		{
			if ( isset($item_ids[$i]) )
			{
				$bag[$key] = $item_ids[$i];
				$i++;
			}
			else
			{
				$bag[$key] = ItemDef::ITEM_ID_NO_ITEM;
			}
			if ( $bag[$key] != $value )
			{
				$values[] = array (
						BagDef::BAG_ITEM_ID => $bag[$key],
	                    BagDef::BAG_UID => $this->m_uid,
	                	BagDef::BAG_GID => $key,
				);
			}
		}

		//为了防止数据出错，此处采用bactchUpdate
		if ( !empty($values) )
		{
			BagDao::batchUpdateBag($values);
			switch ( $type )
			{
				case BagDef::USER_BAG:
					$this->m_original_user_bag = $bag;
					RPCContext::getInstance()->setSession(BagDef::SESSION_USER_BAG, $this->m_user_bag);
					break;
				case BagDef::DEPOT_BAG:
					$this->m_original_depot_bag = $bag;
					RPCContext::getInstance()->setSession(BagDef::SESSION_DEPOT_BAG, $this->m_depot_bag);
					break;
				default:
					throw new Exception('invalid type!');
					break;
			}
		}

		return $bag;
	}

	public static function compare($a, $b)
	{
		$item_a = ItemManager::getInstance()->getItem($a);
		$item_b = ItemManager::getInstance()->getItem($b);

		if ( $item_a->getItemType() < $item_b->getItemType() )
		{
			return -1;
		}
		else if ( $item_a->getItemType() > $item_b->getItemType() )
		{
			return 1;
		}
		else
		{
			if ( $item_a->getItemQuality() > $item_b->getItemQuality() )
			{
				return -1;
			}
			else if ( $item_a->getItemQuality() < $item_b->getItemQuality() )
			{
				return 1;
			}
			else
			{
				if ( $item_a->getItemTemplateID() > $item_b->getItemTemplateID() )
				{
					return -1;
				}
				else if ( $item_a->getItemTemplateID() < $item_b->getItemTemplateID() )
				{
					return 1;
				}
				else
				{
					if ( $item_a->getItemType() == ItemDef::ITEM_ARM &&
						$item_b->getItemType() == ItemDef::ITEM_ARM )
					{
						if ( $item_a->getReinforceLevel() > $item_a->getReinforceLevel() )
						{
							return -1;
						}
						else if ( $item_a->getReinforceLevel() < $item_a->getReinforceLevel() )
						{
							return 1;
						}
					}

					if ( $item_a->getItemType() == ItemDef::ITEM_GEM &&
						$item_b->getItemType() == ItemDef::ITEM_GEM )
					{
						if ( $item_a->getLevel() > $item_a->getLevel() )
						{
							return -1;
						}
						else if ( $item_a->getLevel() < $item_a->getLevel() )
						{
							return 1;
						}
					}

					if ( $a < $b )
					{
						return -1;
					}
					else
					{
						return 1;
					}
				}
			}
		}
	}

	/**
	 *
	 * 根据物品ID,得到物品的位置
	 *
	 * @param int $item_id
	 * @param boolean $tmp_bag				是否检查临时背包,默认值为TRUE
	 *
	 * @return int $gid
	 */
	public function getGridID($item_id, $in_tmp_bag = TRUE )
	{
		if ( $item_id == BagDef::ITEM_ID_NO_ITEM )
		{
			return BagDef::BAG_INVALID_BAG_ID;
		}

		$gid = $this->__getGridID($this->m_user_bag, $item_id);
		if ( $gid == BagDef::BAG_INVALID_BAG_ID )
		{
			$gid = $this->__getGridID($this->m_mission_bag, $item_id);
			if ( $gid == BagDef::BAG_INVALID_BAG_ID )
			{
				$gid = $this->__getGridID($this->m_depot_bag, $item_id);
				if ( $gid == BagDef::BAG_INVALID_BAG_ID && $in_tmp_bag == TRUE)
				{
					$gid = $this->__getGridID($this->m_tmp_bag, $item_id);
				}
			}
		}
		return $gid;
	}

	private function __getGridID($bag, $item_id)
	{
		foreach ( $bag as $gid => $value )
		{
			if ( $item_id == $value )
			{
				return $gid;
			}
		}
		return BagDef::BAG_INVALID_BAG_ID;
	}

	/**
	 *
	 * 初始化背包数据
	 *
	 * @see
	 */
	public function initBag()
	{
		$start = BagDef::USER_BAG_GRID_START;
		//如果用户背包数不为0
		if ( $this->m_user_bag_max_grid !== 0 )
		{
			//如果当前背包格子总数大于初始化数量,则表示已经初始化完成,直接返回
			if ( $this->m_user_bag_max_grid < BagConfig::BAG_UNLOCK_GID_START )
			{
				Logger::FATAL('invalid bag info! need continue init!');
			}
			$start = $this->m_user_bag_max_grid + BagDef::USER_BAG_GRID_START;
		}

		for ( $i = $start; $i < BagDef::USER_BAG_GRID_START + BagConfig::BAG_UNLOCK_GID_START; $i++ )
		{
			$this->setBagData($i, BagDef::ITEM_ID_NO_ITEM);
		}
		$this->m_user_bag_max_grid = count($this->m_user_bag);

		$start = BagDef::DEPOT_BAG_GRID_START;
		//如果用户背包数不为0
		if ( $this->m_depot_bag_max_grid !== 0 )
		{
			//如果当前背包格子总数大于初始化数量,则表示已经初始化完成,直接返回
			if ( $this->m_depot_bag_max_grid < BagConfig::DEPOT_BAG_UNLOCK_GID_START )
			{
				Logger::FATAL('invalid bag info! need continue init!');
			}
			$start = $this->m_depot_bag_max_grid + BagDef::DEPOT_BAG_GRID_START;
		}

		for ( $i = $start; $i < BagConfig::DEPOT_BAG_UNLOCK_GID_START + BagDef::DEPOT_BAG_GRID_START; $i++ )
		{
			$this->setBagData($i, BagDef::ITEM_ID_NO_ITEM);
		}
		$this->m_depot_bag_max_grid = count($this->m_depot_bag);

		return TRUE;
	}

	/**
	 *
	 * 增加物品(批量)
	 *
	 * @param array(int) $item_ids		物品IDs
	 * @param boolean $in_tmp_bag		是否添加到临时背包中,DEFAULT=FALSE
	 *
	 * @return boolean
	 */
	public function addItems($item_ids, $in_tmp_bag = FALSE)
	{
		foreach ($item_ids as $item_id)
		{
			if ( $this->addItem($item_id, $in_tmp_bag) == FALSE )
				return FALSE;
		}
		return TRUE;
	}

	/**
	 *
	 * 增加物品
	 *
	 * @param int $item_id				物品ID
	 * @param boolean $in_tmp_bag		是否添加到临时背包中,DEFAULT=FALSE
	 *
	 * @return boolean
	 */
	public function addItem($item_id, $in_tmp_bag = FALSE)
	{
		$item = $this->m_manager->getItem($item_id);
		//如果物品不存在，直接返回FALSE
		if ( $item === NULL )
		{
			return FALSE;
		}
		//如果是任务物品,则放入到任务背包中
		if ( $item->getITemType() == ItemDef::ITEM_MISSION )
		{
			return $this->__addItem($item_id, $this->m_mission_bag, BagDef::MISSION_BAG_GRID_START,
						 BagDef::MISSION_BAG_GRID_START + BagConfig::MISSION_BAG_GRID_NUM);
		}
		else
		{
			//先尝试添加到用户背包中
			$return = $this->__addItem($item_id, $this->m_user_bag, BagDef::USER_BAG_GRID_START,
						 BagDef::USER_BAG_GRID_START + BagConfig::USER_BAG_GRID_NUM, FALSE );
			if ( empty($return) && $in_tmp_bag == TRUE )
			{
				//失败后添加到临时背包中
				return $this->__addItemInTmpBag($item_id);
			}
			else
			{
				return $return;
			}
		}
		return FALSE;
	}

	private function addDepotItem($item_id)
	{
		$item = $this->m_manager->getItem($item_id);
		//如果物品不存在，直接返回FALSE
		if ( $item === NULL )
		{
			return FALSE;
		}
		//如果是任务物品,则失败
		if ( $item->getITemType() == ItemDef::ITEM_MISSION )
		{
			return FALSE;
		}
		else
		{
			return $this->__addItem($item_id, $this->m_depot_bag, BagDef::DEPOT_BAG_GRID_START,
						 BagDef::DEPOT_BAG_GRID_START + BagConfig::DEPOT_BAG_GRID_NUM, FALSE );
		}
		return FALSE;
	}

	private function __addItemInTmpBag($item_id)
	{
		//临时背包不合并物品
		for ( $i = BagDef::TMP_BAG_GRID_START; $i < BagDef::TMP_BAG_GRID_START + BagConfig::TMP_BAG_GRID_NUM; $i++ )
		{
			if ( !isset($this->m_tmp_bag[$i]) ||
				( isset($this->m_tmp_bag[$i]) && $this->m_tmp_bag[$i] == BagDef::ITEM_ID_NO_ITEM ) )
			{
				$this->setBagData($i, $item_id);
				return TRUE;
			}
		}
		return FALSE;
	}

	private function __addItem($item_id, $bag, $start, $end, $insert = TRUE)
	{
		$item = $this->m_manager->getItem($item_id);

		//如果物品不存在,则返回失败
		if ( $item === NULL )
		{
			return FALSE;
		}

		//如果物品可叠加
		if ( $item->getStackable() != ItemDef::ITEM_CAN_NOT_STACKABLE )
		{
			$stack = array();
			$number = $item->getItemNum();
			//在当前的背包中寻找相同模板id的物品,用于叠加
			for( $i = $start; $i < $end; $i++ )
			{
				if ( isset($bag[$i]) && $bag[$i] != BagDef::ITEM_ID_NO_ITEM )
				{
					$o_item = $this->m_manager->getItem($bag[$i]);
					if ( $o_item != NULL && $o_item->getItemTemplateID() == $item->getItemTemplateID()
						&& $o_item->getItemNum() < $o_item->getStackable() )
					{
						$stack[] = $i;
						$number -= $item->getStackable() - $o_item->getItemNum();
					}
				}
				if ( $number <= 0 )
				{
					break;
				}
			}
			//如果没有完全叠加
			if ( $number > 0 )
			{
				//查找一个可以用于存放的位置
				for ( $i = $start; $i < $end; $i++ )
				{
					if ( ( $insert == TRUE && !isset($bag[$i]) ) ||
						( isset($bag[$i]) && $bag[$i] == BagDef::ITEM_ID_NO_ITEM ) )
					{
						//如果查找到,则先将所有的应该合并的物品，先合并
						foreach ( $stack as $k )
						{
							$this->m_manager->unionItem($item_id, $bag[$k]);
							$this->m_modify[] = $k;
							Logger::DEBUG('modify bag:%d', $k);
						}
						//将物品放置到空的格子上去
						$item->setItemNum($number);
						$this->setBagData($i, $item_id);
						return TRUE;
					}
				}
			}
			else//如果可以完全合并
			{
				foreach ( $stack as $k )
				{
					$this->m_manager->unionItem($item_id, $bag[$k]);
					$this->m_modify[] = $k;
					Logger::DEBUG('modify bag:%d', $k);
				}
				return TRUE;
			}
		}
		//如果物品不可叠加,则查找一个可用的位置放置
		else
		{
			for ( $i = $start; $i < $end; $i++ )
			{
				if ( ( $insert == TRUE && !isset($bag[$i]) ) ||
					( isset($bag[$i]) && $bag[$i] == BagDef::ITEM_ID_NO_ITEM ) )
				{
					$this->setBagData($i, $item_id);
					return TRUE;
				}
			}
		}
		$this->error = TRUE;
		return FALSE;
	}

	/**
	 *
	 * 移除物品(从背包里移除)
	 *
	 * @param int $item_id
	 * @param int $in_tmp_bag
	 *
	 * @return boolean
	 */
	public function removeItem($item_id, $in_tmp_bag = FALSE)
	{
		$gid = $this->getGridID($item_id, $in_tmp_bag);
		if ( $gid == BagDef::BAG_INVALID_BAG_ID )
		{
			Logger::WARNING('item_id:%d not in Bag!', $item_id);
			return FALSE;
		}

		Logger::DEBUG('remove item:%d', $item_id);
		$this->setBagData($gid, BagDef::ITEM_ID_NO_ITEM);
		return TRUE;
	}

	/**
	 *
	 * 删除物品(直接从系统中删除)
	 *
	 * @param int $item_id
	 * @param int $in_tmp_bag
	 *
	 * @return boolean
	 */
	public function deleteItem($item_id, $in_tmp_bag = FALSE)
	{
		if ( $this->removeItem($item_id, $in_tmp_bag) == TRUE )
		{
			if ( $this->m_manager->deleteItem($item_id) == TRUE )
			{
				return TRUE;
			}
			else
			{
				Logger::FATAL('fixed me!delete item_id:%d failed!', $item_id);
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 *
	 * 减少物品
	 *
	 * @param int $item_id					物品id
	 * @param int $item_number				物品数量
	 *
	 * @return boolean
	 *
	 */
	public function decreaseItem($item_id, $item_number)
	{
		$gid = $this->getGridID($item_id);
		if ( $gid == BagDef::BAG_INVALID_BAG_ID )
		{
			return FALSE;
		}
		else
		{
			if ( $this->m_manager->decreaseItem($item_id, $item_number) == FALSE )
			{
				return FALSE;
			}
			else
			{
				$item = $this->m_manager->getItem($item_id);
				//如果物品被删除
				if ( $item === NULL )
				{
					$this->setBagData($gid, BagDef::ITEM_ID_NO_ITEM);
				}
				Logger::DEBUG('modify bag, gid:%d', $gid);
				$this->m_modify[] = $gid;
				return TRUE;
			}
		}
		return TRUE;
	}

	/**
	 *
	 * 增加物品
	 * @param int $item_template_id					物品模板ID
	 * @param int $item_num							物品数量
	 * @param boolean $in_tmp_bag					是否添加到临时背包中
	 *
	 * @see 如果需要发送公告,则不该使用该函数
	 *
	 * @return boolean
	 */
	public function addItemByTemplateID($item_template_id, $item_num, $in_tmp_bag = FALSE)
	{
		$array = $this->m_manager->addItem($item_template_id, $item_num);
		return $this->addItems($array, $in_tmp_bag);
	}

	/**
	 *
	 * 增加物品(批量)
	 *
	 * @param array $items
	 * <code>
	 * [
	 * 		item_template_id:int => item_num:int
	 * ]
	 * <code>
	 * @param boolean $in_tmp_bag					是否添加到临时背包中
	 *
	 * @see 如果需要发送公告,则不该使用该函数
	 *
	 * @return boolean
	 */
	public function addItemsByTemplateID($items, $in_tmp_bag = FALSE)
	{
		if ( is_array($items) || get_class($items) == 'BtstoreElement' )
		{
			foreach ( $items as $item_template_id => $item_num )
			{
				if ( $this->addItemByTemplateID($item_template_id, $item_num, $in_tmp_bag) == FALSE )
				{
					return FALSE;
				}
			}
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 *
	 * 删除物品
	 *
	 * @param int $item_template_id
	 * @param int $item_num
	 *
	 * @return boolean
	 */
	public function deleteItembyTemplateID($item_template_id, $item_num)
	{
		if ( $item_num <= 0 )
		{
			return FALSE;
		}
		if ( $this->m_manager->isMissionItem($item_template_id) == TRUE )
		{
			//一次性拉取所有任务物品数据
			$item_ids = array_merge($this->m_mission_bag);
			$this->m_manager->getItems($item_ids);
			return $this->__deleteItemByTemplateID($this->m_mission_bag, $item_template_id, $item_num);
		}
		else
		{
			//一次性拉取所有用户物品数据
			$item_ids = array_merge($this->m_user_bag);
			$this->m_manager->getItems($item_ids);
			return $this->__deleteItemByTemplateID($this->m_user_bag, $item_template_id, $item_num);
		}
	}

	/**
	 *
	 * 删除物品(批量)
	 *
	 * @param array $items
	 * <code>
	 * [
	 * 		item_template_id:int => item_num:int
	 * ]
	 * <code>
	 *
	 * @return boolean
	 */
	public function deleteItemsByTemplateID($items)
	{
		if ( is_array($items) || get_class($items) == 'BtstoreElement' )
		{
			foreach ( $items as $item_template_id => $item_num )
			{
				if ( $this->deleteItembyTemplateID($item_template_id, $item_num) == FALSE )
				{
					return FALSE;
				}
			}
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	private function __deleteItemByTemplateID($bag, $item_template_id, $item_num)
	{
		$number = 0;
		if ( $this->__getItemNumByTemplateID($bag, $item_template_id) < $item_num )
		{
			$this->error = TRUE;
			return FALSE;
		}
		foreach ( $bag as $gid => $item_id )
		{
			if ( $item_num <= 0 )
				break;
			if ( $item_id !== BagDef::ITEM_ID_NO_ITEM )
			{
				$item = $this->m_manager->getItem($item_id);
				if ( $item !== NULL && $item->getItemTemplateID() == $item_template_id )
				{
					$number = $item->getItemNum();
					if ( $number > $item_num )
					{
						$this->decreaseItem($item_id, $item_num);
						$item_num = 0;
					}
					else
					{
						$this->deleteItem($item_id);
						$item_num -= $number;
					}
				}
			}
		}
		if ( $item_num != 0 )
		{
			$this->error = TRUE;
			return FALSE;
		}
		return TRUE;
	}

	/**
	 *
	 * 得到某类物品的数量
	 *
	 * @param int $item_template_id
	 *
	 * @return int
	 */
	public function getItemNumByTemplateID($item_template_id)
	{
		if ( $this->m_manager->getItemType($item_template_id) == ItemDef::ITEM_MISSION   )
		{
			//一次性拉取所有任务物品数据
			$item_ids = array_merge($this->m_mission_bag);
			$this->m_manager->getItems($item_ids);
			return $this->__getItemNumByTemplateID($this->m_mission_bag, $item_template_id);
		}
		else
		{
			//一次性拉取所有用户物品数据
			$item_ids = array_merge($this->m_user_bag);
			$this->m_manager->getItems($item_ids);
			return $this->__getItemNumByTemplateID($this->m_user_bag, $item_template_id);
		}
	}

	private function __getItemNumByTemplateID($bag, $item_template_id)
	{
		$number = 0;
		foreach  ( $bag as $gid => $item_id )
		{
			if ( $item_id !== BagDef::ITEM_ID_NO_ITEM )
			{
				$item = $this->m_manager->getItem($item_id);
				if ( $item !== NULL && $item->getItemTemplateID() == $item_template_id )
				{
					$number += $item->getItemNum();
				}
			}
		}
		return $number;
	}

	/**
	 *
	 * 得到某类物品的ids
	 *
	 * @param int $item_type				物品类型
	 *
	 * @return array(int)					物品id数组
	 */
	public function getItemIdsByItemType($item_type)
	{
		if ( $item_type == ItemDef::ITEM_MISSION   )
		{
			//一次性拉取所有任务物品数据
			$item_ids = array_merge($this->m_mission_bag);
			$this->m_manager->getItems($item_ids);
			return $this->__getItemIdsByItemType($this->m_mission_bag, $item_type);
		}
		else
		{
			//一次性拉取所有用户物品数据
			$item_ids = array_merge($this->m_user_bag);
			$this->m_manager->getItems($item_ids);
			return $this->__getItemIdsByItemType($this->m_user_bag, $item_type);
		}
	}

	private function __getItemIdsByItemType($bag, $item_type)
	{
		$item_ids = array();
		foreach  ( $bag as $gid => $item_id )
		{
			if ( $item_id !== BagDef::ITEM_ID_NO_ITEM )
			{
				$item = $this->m_manager->getItem($item_id);
				if ( $item !== NULL && $item->getItemType() == $item_type )
				{
					$item_ids[] = $item_id;
				}
			}
		}
		return $item_ids;
	}

	/**
	 *
	 * 掉落物品(批量)
	 *
	 * @param array(int) $drop_template_ids				掉落表模板IDs
	 * @param boolean $in_tmp_bag						是否放入临时背包
	 *
	 * @see 如果需要发送公告,则不该使用该函数
	 *
	 * @return boolean
	 */
	public function dropItems($drop_template_ids, $in_tmp_bag = FALSE )
	{
		foreach ( $drop_template_ids as $drop_template_id )
		{
			if ( $this->dropItem($drop_template_id, $in_tmp_bag) == FALSE )
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 *
	 * 掉落物品
	 *
	 * @param int $drop_template_id				掉落表ID
	 * @param boolean $in_tmp_bag				是否添加到临时背包里
	 *
	 * @see 如果需要发送公告,则不该使用该函数
	 *
	 * @return boolean
	 */
	public function dropItem($drop_template_id, $in_tmp_bag = FALSE )
	{
		$item_ids = $this->m_manager->dropItem($drop_template_id);
		$deleted = FALSE;
		foreach ($item_ids as $item_id)
		{
			if ( $deleted == FALSE )
			{
				if ( $this->addItem($item_id, $in_tmp_bag) == FALSE )
				{
					$this->m_manager->deleteItem($item_id);
					$deleted = TRUE;
				}
			}
			else
			{
				$this->m_manager->deleteItem($item_id);
			}
		}
		return !$deleted;
	}

	/**
	 *
	 * 清空背包
	 *
	 */
	public function clearBag()
	{
		foreach ( $this->m_user_bag as $gid => $item_id )
		{
			if ( $item_id != BagDef::ITEM_ID_NO_ITEM )
			{
				if ( $this->deleteItem($item_id, TRUE) == FALSE )
					return FALSE;
			}
		}

		foreach ( $this->m_tmp_bag as $gid => $item_id )
		{
			if ( $item_id != BagDef::ITEM_ID_NO_ITEM )
			{
				if ( $this->deleteItem($item_id, TRUE) == FALSE )
					return FALSE;
			}
		}

		foreach ( $this->m_mission_bag as $gid => $item_id )
		{
			if ( $item_id != BagDef::ITEM_ID_NO_ITEM )
			{
				if ( $this->deleteItem($item_id, TRUE) == FALSE )
					return FALSE;
			}
		}

		foreach ( $this->m_depot_bag as $gid => $item_id )
		{
			if ( $item_id != BagDef::ITEM_ID_NO_ITEM )
			{
				if ( $this->deleteItem($item_id, TRUE) == FALSE )
					return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 *
	 * 将脏数据回滚
	 */
	public function rollback()
	{
		if ( $this->error == TRUE )
		{
			$this->m_manager->rollback();
			$this->m_modify = array();
			$this->m_user_bag = $this->m_original_user_bag;
			$this->m_tmp_bag = $this->m_original_tmp_bag;
			$this->m_mission_bag = $this->m_original_mission_bag;
			$this->m_depot_bag = $this->m_original_depot_bag;
			$this->error = FALSE;
		}
	}

	private function &gid2Bag($gid)
	{
		if ( $gid < BagDef::TMP_BAG_GRID_START )
		{
			return $this->m_user_bag;
		}
		else if (  $gid >= BagDef::TMP_BAG_GRID_START &&
			$gid < BagDef::MISSION_BAG_GRID_START )
		{
			return $this->m_tmp_bag;
		}
		else if ( $gid >= BagDef::MISSION_BAG_GRID_START &&
			$gid < BagDef::DEPOT_BAG_GRID_START )
		{
			return $this->m_mission_bag;
		}
		else
		{
			return $this->m_depot_bag;
		}
	}

	/**
	 *
	 * 得到背包数据
	 *
	 * @return array
	 */
	private function getBagData()
	{
		$array = array();
		$array[BagDef::USER_BAG] = RPCContext::getInstance()->getSession(BagDef::SESSION_USER_BAG);
		$array[BagDef::TMP_BAG] = RPCContext::getInstance()->getSession(BagDef::SESSION_TMP_BAG);
		$array[BagDef::MISSION_BAG] = RPCContext::getInstance()->getSession(BagDef::SESSION_MISSION_BAG);
		$array[BagDef::DEPOT_BAG] = RPCContext::getInstance()->getSession(BagDef::SESSION_DEPOT_BAG);
		//如果session中存在,则使用session中的数据
		if ( isset($array[BagDef::USER_BAG]) && isset($array[BagDef::TMP_BAG])
			 && isset($array[BagDef::MISSION_BAG]) && isset($array[BagDef::SESSION_DEPOT_BAG]) )
		{
			return $array;
		}

		$select = array(BagDef::BAG_ITEM_ID, BagDef::BAG_GID);
		$where = array(BagDef::BAG_UID, '=', $this->m_uid);
		$return = BagDAO::selectBag($select, $where);
		$array[BagDef::USER_BAG] = array();
		$array[BagDef::TMP_BAG] = array();
		$array[BagDef::MISSION_BAG] = array();
		$array[BagDef::DEPOT_BAG] = array();
		foreach ($return as $value)
		{
			$gid = intval($value[BagDef::BAG_GID]);
			$item_id = intval($value[BagDef::BAG_ITEM_ID]);
			if ( $value[BagDef::BAG_GID] >= BagDef::USER_BAG_GRID_START &&
				$value[BagDef::BAG_GID] < BagDef::TMP_BAG_GRID_START )
			{
				$array[BagDef::USER_BAG][$gid] = $item_id;
			}
			else if (  $value[BagDef::BAG_GID] >= BagDef::TMP_BAG_GRID_START &&
				$value[BagDef::BAG_GID] < BagDef::MISSION_BAG_GRID_START )
			{
				$array[BagDef::TMP_BAG][$gid] = $item_id;
			}
			else if ( $value[BagDef::BAG_GID] >= BagDef::MISSION_BAG_GRID_START &&
				$value[BagDef::BAG_GID] < BagDef::DEPOT_BAG_GRID_START )
			{
				$array[BagDef::MISSION_BAG][$gid] = $item_id;
			}
			else
			{
				$array[BagDef::DEPOT_BAG][$gid] = $item_id;
			}
		}
		RPCContext::getInstance()->setSession(BagDef::SESSION_USER_BAG, $array[BagDef::USER_BAG]);
		RPCContext::getInstance()->setSession(BagDef::SESSION_TMP_BAG, $array[BagDef::TMP_BAG]);
		RPCContext::getInstance()->setSession(BagDef::SESSION_MISSION_BAG, $array[BagDef::MISSION_BAG]);
		RPCContext::getInstance()->setSession(BagDef::SESSION_DEPOT_BAG, $array[BagDef::DEPOT_BAG]);
		return $array;
	}

	/**
	 *
	 * 更新背包数据
	 */
	private function setBagData($gid, $item_id)
	{
		$bag = &$this->gid2Bag($gid);
		$bag[$gid] = $item_id;
		$this->m_modify[] = $gid;
		Logger::DEBUG('modify bag:%d', $gid);
	}

	/**
	 *
	 * 更新背包数据
	 *
	 * @return @grid
	 */
	public function update()
	{
		$this->__update($this->m_user_bag,		$this->m_original_user_bag);
		$this->__update($this->m_tmp_bag,		$this->m_original_tmp_bag);
		$this->__update($this->m_mission_bag,	$this->m_original_mission_bag);
		$this->__update($this->m_depot_bag,		$this->m_original_depot_bag);
		$this->m_manager->update();
		$this->m_original_user_bag = $this->m_user_bag;
		$this->m_original_tmp_bag = $this->m_tmp_bag;
		$this->m_original_mission_bag = $this->m_mission_bag;
		$this->m_original_depot_bag = $this->m_depot_bag;
		RPCContext::getInstance()->setSession(BagDef::SESSION_USER_BAG, $this->m_user_bag);
		RPCContext::getInstance()->setSession(BagDef::SESSION_TMP_BAG, $this->m_tmp_bag);
		RPCContext::getInstance()->setSession(BagDef::SESSION_MISSION_BAG, $this->m_mission_bag);
		RPCContext::getInstance()->setSession(BagDef::SESSION_DEPOT_BAG, $this->m_depot_bag);
		Logger::DEBUG('bag modify:%s!', $this->m_modify);
		$modifyInfo = $this->gridInfos(array_unique($this->m_modify));
		//如果背包内数据改变,则通知任务系统
		if ( !empty($this->m_modify) )
		{
			$this->m_modify = array();
			TaskNotify::itemChange();

			//发送成就消息
			//TODO:MAYBE MODIFY
			foreach ( $modifyInfo as $gid => $itemInfo )
			{
				if ( !empty($itemInfo) && ItemAttr::getItemAttr($itemInfo[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID],
					ItemDef::ITEM_ATTR_NAME_TYPE) == ItemDef::ITEM_ARM )
				{
					EnAchievements::notify($this->m_uid, AchievementsDef::ITEM_COLOR,
						ItemAttr::getItemAttr($itemInfo[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID],
						ItemDef::ITEM_ATTR_NAME_QUALITY));
				}
			}
		}
		return $modifyInfo;
	}

	private function __update($bag, $original_bag)
	{
		foreach ( $bag as $gid => $item_id )
		{
			if ( !isset($original_bag[$gid]) || $original_bag[$gid] != $item_id )
			{
       			$values = array(BagDef::BAG_ITEM_ID => $item_id,
                    BagDef::BAG_UID => $this->m_uid,
                	BagDef::BAG_GID => $gid
                );
                try
                {
                	BagDAO::insertOrupdateBag($values);
                }
                catch ( Exception $e)
                {
					Logger::FATAL('FIXED_BAG_DATA:uid:%d, gid:%d, item_id:%d',
						 $values[BagDef::BAG_UID], $values[BagDef::BAG_GID],
						 $values[BagDef::BAG_ITEM_ID]);
					throw $e;
                }
			}
		}
	}
	
	public function compositeItem($gid, $item_id)
	{
		
		$compose_id = intval($compose_id);

		$compose_condition = self::getComposeCondition($compose_id);

		$compose_success = TRUE;
		$return = array();

		$user = EnUser::getInstance();
		//检查是否有足够的belly
		if ( $compose_condition[ComposeConditionDef::COMPOSE_REQ_BELLY] != 0 &&
			$user->subBelly($compose_condition[ComposeConditionDef::COMPOSE_REQ_BELLY]) == FALSE )
		{
			Logger::DEBUG("no enough belly!");
			return $return;
		}
		//检查是否有足够的金币
		if ( $compose_condition[ComposeConditionDef::COMPOSE_REQ_GOLD] != 0 &&
			$user->subGold($compose_condition[ComposeConditionDef::COMPOSE_REQ_GOLD]) == FALSE )
		{
			Logger::DEBUG("no enough gold!");
			return $return;
		}

		$bag = BagManager::getInstance()->getBag();
		//检查是否有足够的合成所需物品
		if ( empty($items) )
		{
			foreach ( $compose_condition[ComposeConditionDef::COMPOSE_REQ_ITEMS] as $item_template_id => $item_num )
			{
				if ( $bag->deleteItembyTemplateID($item_template_id, $item_num * $compose_number) == FALSE )
				{
					Logger::DEBUG("no enough item!item_template_id:%d, item_num:%d",
						$item_template_id, $item_num * $compose_number);
					return $return;
				}
			}
		}
		else//如果明确指出了使用那些物品
		{
			$tmp_items = array();
			foreach ( $items as $item_id => $item_num )
			{
				$item = ItemManager::getInstance()->getItem($item_id);
				if ( $item === NULL )
				{
					Logger::DEBUG('invalid item_id:%d', $item_id);
					throw new Exception('fake');
				}
				$item_template_id = $item->getItemTemplateID();
				if ( isset($tmp_items[$item_template_id]) )
				{
					$tmp_items[$item_template_id] += $item_num;
				}
				else
				{
					$tmp_items[$item_template_id] = $item_num;
				}
			}
			foreach ( $compose_condition[ComposeConditionDef::COMPOSE_REQ_ITEMS] as $item_template_id => $item_num )
			{
				if ( $tmp_items[$item_template_id] != $item_num * $compose_number )
				{
					Logger::DEBUG('invalid input items');
					return $return;
				}
			}
			foreach ( $items as $item_id => $item_num )
			{
				if ( $bag->decreaseItem($item_id, $item_num * $compose_number) == FALSE )
				{
					Logger::DEBUG("no enough item!item_id:%d, item_num:%d", $item_id, $item_num * $compose_number);
					return $return;
				}
			}
		}

		$rand = mt_rand(0, ComposeConditionDef::MAX_PROBABILITY);
		if ( $rand <= $compose_condition[ComposeConditionDef::COMPOSE_PROBABILITY] )
		{
			//增加新的物品
			foreach ( $compose_condition[ComposeConditionDef::COMPOSE_GEN_ITEMS] as $item_template_id => $item_num )
			{
				if ( $bag->addItemByTemplateID($item_template_id, $item_num * $compose_number ) == FALSE )
				{
					Logger::DEBUG("bag full!");
					return $return;
				}
			}
		}
		else
		{
			Logger::INFO("compose rand failed!rand:%d > MAX:%d", $rand, $compose_condition[ComposeConditionDef::COMPOSE_PROBABILITY]);
			$compose_success = FALSE;
		}

		//扣除belly和金币
		$user->update();
		//扣除物品
		$bag_modify = $bag->update();

		//Statistics
		if ( !empty($compose_condition[ComposeConditionDef::COMPOSE_REQ_GOLD]) )
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_FORGE_COMPOSE,
						$compose_condition[ComposeConditionDef::COMPOSE_REQ_GOLD],
						Util::getTime());
		}


		$return['compose_items'] = $bag_modify;
		$return['compose_success'] = $compose_success;
		return $return;

		//return array('composite_success'=>'ok', 'bag_modify' => $this->update(), 'item_get'=>array(70623=>1));
	}
	
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */