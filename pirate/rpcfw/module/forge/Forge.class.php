<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Forge.class.php 40241 2013-03-07 08:22:28Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/forge/Forge.class.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-03-07 16:22:28 +0800 (四, 2013-03-07) $
 * @version $Revision: 40241 $
 * @brief
 *
 **/



class Forge implements IForge
{
	/**
	 *
	 * 物品管理器
	 * @var ItemManager
	 */
	private $m_manager;

	/**
	 *
	 * 用户UID
	 * @var int
	 */
	private $m_uid;

	/**
	 *
	 * 铁匠铺相关信息
	 * @var array
	 */
	private $m_info;

	public function Forge(){}

	/**
	 *
	 * 数据初始化,由于有static函数需要被timer调用,因此无法把init方法放置到构造函数中
	 *
	 * @return NULL
	 */
	private function forgeInfo()
	{
		$this->m_uid = RPCContext::getInstance()->getUid();
		$this->m_manager = ItemManager::getInstance();
		$this->m_info = $this->getForgeInfo();
		$this->resetTime();
	}

	/**
	 *
	 * 刷新铁匠铺相关的按时间刷新的信息(强化时间)
	 *
	 * @return NULL
	 */
	private function resetTime()
	{
		$values = array();
		if ( $this->m_info[ForgeDef::FORGE_TRANSFER_RESET_TIME] < Util::getTime() )
		{
			$this->m_info[ForgeDef::FORGE_TRANSFER_RESET_TIME] =
				$this->getRefreshResetTime($this->m_info[ForgeDef::FORGE_TRANSFER_RESET_TIME]);
			$values += ForgeDef::$FORGE_TRANSFER_VALUES;
			$values[ForgeDef::FORGE_TRANSFER_RESET_TIME] =
				$this->m_info[ForgeDef::FORGE_TRANSFER_RESET_TIME];
		}
		if ( isset($this->m_info[ForgeDef::FORGE_POTENTIALITY_TRANSFER_RESET_TIME]) )
		{
			if ( $this->m_info[ForgeDef::FORGE_POTENTIALITY_TRANSFER_RESET_TIME] < Util::getTime() )
			{
				$this->m_info[ForgeDef::FORGE_POTENTIALITY_TRANSFER_RESET_TIME] =
					$this->getPotentialityTransferResetTime($this->m_info[ForgeDef::FORGE_POTENTIALITY_TRANSFER_RESET_TIME]);
				$values += ForgeDef::$FORGE_POTENTIALITY_TRANSFER_VALUES;
				$values[ForgeDef::FORGE_SQL_POTENTIALITY_TRANSFER_RESET_TIME] =
					$this->m_info[ForgeDef::FORGE_POTENTIALITY_TRANSFER_RESET_TIME];
			}
		}
		// 刷新强化时间
		if ( $this->m_info[ForgeDef::FORGE_REINFORCE_TIME] < Util::getTime() &&
			$this->m_info[ForgeDef::FORGE_REINFORCE_TIME] != 0 )
		{
			$values += ForgeDef::$FORGE_REINFORCE_VALUES;
		}
		if ( !empty($values) )
		{
			$this->setForgeInfo($values);
		}
	}

	/* (non-PHPdoc)
	 * @see IForge::openMaxProbability()
	 */
	public function openMaxProbability()
	{
		//初始化数据
		$this->forgeInfo();
		$uid = RPCContext::getInstance()->getUid();

		if ( $this->m_info[ForgeDef::FORGE_IS_MAX_PROBABILITY] == 1 )
		{
			Logger::FATAL('already max probability!');
			throw new Exception('fake');
		}

		$user = EnUser::getUserObj();
		$vip_level = $user->getVip();

		if ( !isset(btstore_get()->VIP[$vip_level]) ||
			!isset(btstore_get()->VIP[$vip_level]['reinforce_100_open']) )
		{
			Logger::FATAL('vip reinforce max probability is not exists!vip_level:%d', $vip_level);
			throw new Exception('fake');
		}

		$need_gold = intval(btstore_get()->VIP[$vip_level]['reinforce_100_open']);

		if ( empty($need_gold) )
		{
			Logger::DEBUG('vip level:%d is not allowed open max probability!', $vip_level);
			return FALSE;
		}

		if ( $user->subGold($need_gold) == FALSE )
		{
			Logger::DEBUG('no gold!');
			return FALSE;
		}

		$this->setForgeInfo(array(ForgeDef::FORGE_IS_MAX_PROBABILITY => 1));

		//更新用户
		$user->update();

		//Statistics
		Statistics::gold(StatisticsDef::ST_FUNCKEY_FORGE_OPENMAX,
						$need_gold,
						Util::getTime());

		return TRUE;
	}

	public function fuse($item_id, $fuse_item_id)
	{
		$item_id = intval($item_id);
		$fuse_item_id = intval($fuse_item_id);

		$item = ItemManager::getInstance()->getItem($item_id);
		$fuse_item = ItemManager::getInstance()->getItem($fuse_item_id);

		//检查功能是否开启
		if ( EnSwitch::isOpen(SwitchDef::GEM_FUSE) == FALSE )
		{
			Logger::DEBUG('gem fuse is not open!');
			return FALSE;
		}

		//检测物品是否存在
		if ( $item == NULL || $item->getItemType() != ItemDef::ITEM_GEM )
		{
			Logger::DEBUG('item_id:%d not exist or not a gem!', $item_id);
			return FALSE;
		}

		if ( $fuse_item == NULL || $fuse_item->getItemType() != ItemDef::ITEM_GEM )
		{
			Logger::DEBUG('item_id:%d not exist or not a gem!', $fuse_item_id);
			return FALSE;
		}

		if ( $item_id == $fuse_item_id )
		{
			Logger::DEBUG('target item is source item!');
			return FALSE;
		}

		//检测物品是否属于当前用户
		$bag = BagManager::getInstance()->getBag();
		//不检测目标宝石是否属于当前用户,检查过于复杂,并且如果客户端造假,则对自己不利

		if ( $bag->getGridID($fuse_item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			Logger::DEBUG('item_id:%d not belong to you!', $fuse_item_id);
			return FALSE;
		}

		//检测目标宝石是否已经达到最高等级
		if ( $item->getMaxLevel() <= $item->getLevel() )
		{
			Logger::DEBUG('item_id:%d is max level!', $item_id);
			return FALSE;
		}

		$oldlevel=$item->getLevel();
		
		//检测目标宝石是否品质小于被吃掉的宝石
		if ( $item->getItemQuality() < $fuse_item->getItemQuality() )
		{
			Logger::WARNING('item_id:%d, quality:%d < fuse item_id:%d, quality:%d',
				$item_id, $item->getItemQuality(), $fuse_item_id, $fuse_item->getItemQuality());
			return FALSE;
		}

		$item->addExp($fuse_item->getFuseExp());
		$bag->deleteItem($fuse_item_id);

		$bag->update();

		//调用任务系统
		TaskNotify::operate(TaskOperateType::GEM_FUSE);
		
		//物品如果不在背包里则战斗优化
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID && $oldlevel < $item->getLevel())
		{
			EnUser::modifyBattleInfo();
		}

		return TRUE;
	}

	/* (non-PHPdoc)
	 * @see IForge::fuseAll()
	 */
	public function fuseAll($item_id)
	{
		$return = array('fuse_success' => FALSE);
		$item_id = intval($item_id);
		$item = ItemManager::getInstance()->getItem($item_id);

		//检查功能是否开启
		if ( EnSwitch::isOpen(SwitchDef::GEM_FUSE) == FALSE )
		{
			Logger::DEBUG('gem fuse is not open!');
			return FALSE;
		}

		//检测物品是否存在
		if ( $item == NULL || $item->getItemType() != ItemDef::ITEM_GEM )
		{
			Logger::DEBUG('item_id:%d not exist or not a gem!', $item_id);
			return $return;
		}

		$oldlevel= $item->getLevel();
		$bag = BagManager::getInstance()->getBag();
		$gem_item_ids = $bag->getItemIdsByItemType(ItemDef::ITEM_GEM);
		foreach ( $gem_item_ids as $fuse_item_id )
		{
			//如果被融合的宝石和目标一样,则跳过
			if ( $fuse_item_id == $item_id )
			{
				continue;
			}
			//如果目标表示已经达到最大等级
			if ( $item->getLevel() >= $item->getMaxLevel() )
			{
				Logger::DEBUG('item_id:%d is max level', $item_id);
				break;
			}
			$fuse_item = ItemManager::getInstance()->getItem($fuse_item_id);

			if ( $fuse_item === NULL )
			{
				continue;
			}

			//如果被融合的宝石不是最低级的,则跳过
			if ( $fuse_item->getLevel() > ItemDef::ITEM_GEM_MIN_LEVEL )
			{
				continue;
			}

			//如果目标宝石的品质小于被吃掉的宝石,则跳过
			if ( $item->getItemQuality() < $fuse_item->getItemQuality() )
			{
				continue;
			}

			//如果被融合的宝石品质大于ItemDef::ITEM_QUALITY_PURPLE
			if ( $fuse_item->getItemQuality() >= ItemDef::ITEM_QUALITY_PURPLE )
			{
				continue;
			}

			$item->addExp($fuse_item->getFuseExp());
			$bag->deleteItem($fuse_item_id);
		}

		$bag_modify = $bag->update();
		$fuse_item_text = $item->getItemText();
		$fuse_item_exp = $fuse_item_text[ItemDef::ITEM_ATTR_NAME_EXP];
		$return = array (
			'fuse_success' => TRUE,
			'fuse_item' => $fuse_item_exp,
			'bag_modify' => $bag_modify,
		);
		
		//物品如果不在背包里则战斗优化
		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID  && $oldlevel < $item->getLevel() )
		{
			EnUser::modifyBattleInfo();
		}

		//调用任务系统
		TaskNotify::operate(TaskOperateType::GEM_FUSE);

		return $return;
	}

	/* (non-PHPdoc)
	 * @see IForge::transfer()
	 */
	public function transfer($item_id, $target_item_id, $transfer_gem = FALSE)
	{

		$item_id = intval($item_id);
		$target_item_id = intval($target_item_id);
		$transfer_gem = intval($transfer_gem);

		//检查功能是否开启
		if ( EnSwitch::isOpen(SwitchDef::REINFORCE) == FALSE )
		{
			Logger::DEBUG('reinforce is not open!');
			return array();
		}

		$return = array();

		$item = ItemManager::getInstance()->getItem($item_id);
		$target_item = ItemManager::getInstance()->getItem($target_item_id);

		//检测物品是否存在
		if ( $item === NULL || $target_item === NULL ||
			$item->getItemType() != ItemDef::ITEM_ARM ||
			$target_item->getItemType() != ItemDef::ITEM_ARM )
		{
			Logger::DEBUG('item_id:%d or target item_id:%ditem not exist!',
				$item_id, $target_item_id);
			return $return;
		}

		//检查源物品id是否和目标物品id相同
		if ( $item_id == $target_item_id )
		{
			Logger::DEBUG('item_id:%d = target item_id:%d!',
				$item_id, $target_item_id);
			return $return;
		}

		//检查装备是否属于该用户
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return $return;
		}

		//检查装备是否属于该用户
		if ( EnUser::itemBelongTo($target_item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $target_item_id);
			return $return;
		}

		$item_quality = $item->getItemQuality();
		$target_item_quality = $target_item->getItemQuality();

		//装备转移只能发生在同品质或者目标物品比源物品品质大1的情况下
		if ( $item_quality != $target_item_quality &&
			$target_item_quality - $item_quality != 1 )
		{
			Logger::WARNING('item_quality:%d, target item quality:%d is invalid!',
					$item_quality, $target_item_quality);
			return $return;
		}

		//两个装备必须是同一类型的装备
		if ( $item->getItemType() != $target_item->getItemType() )
		{
			Logger::WARNING('src item:%d type is %d != target item:%d type is :%d!',
				$item_id, $item->getItemType(), $target_item_id, $target_item->getItemType());
			return $return;
		}

		//源物品的强化等级必须大于等于目的物品
		if ( $item->getReinforceLevel() < $target_item->getReinforceLevel() )
		{
			Logger::WARNING('src item:%d reinforce level is %d < target item:%d reinforce level is :%d!',
				$item_id, $item->getReinforceLevel(), $target_item_id, $target_item->getReinforceLevel());
			return $return;
		}

		$user = EnUser::getUserObj();
		$vip_level = $user->getVip();
		//检测是否vip达到
		if ( empty(btstore_get()->VIP[$vip_level]['arm_transfer_open']) )
		{
			Logger::WARNING('vip:%d do not use arm transfer!', $vip_level);
			return $return;
		}

		//数据初始化
		$this->forgeInfo();

		//扣除金币
		//金币=当前次数*每次所需金币
		$gold = $this->m_info[ForgeDef::FORGE_TRANSFER_TIME] *
			ForgeConfig::ARM_TRANSFER_GOLD_PRETIME;
		//如果大于金币所需上限
		if ( $gold > ForgeConfig::ARM_TRANSFER_MAX_GOLD )
		{
			$gold = ForgeConfig::ARM_TRANSFER_MAX_GOLD;
		}
		if ( $user->subGold($gold) == FALSE )
		{
			Logger::WARNING('no enough gold:%d, now gold:%d', $gold, $user->getGold());
			return $return;
		}

		//得到物品强化信息
		$reinforceInfo = $item->reinforceReq();
		$reinforceLevel = $item->getReinforceLevel();
		//计算所应该加的Belly数量
		$belly = 0;
		for ( $i = $reinforceLevel; $i > ItemDef::ITEM_REINFORCE_LEVEL_DEFAULT; $i-- )
		{
			$belly += $reinforceInfo[$i][ItemDef::REINFORCE_FEE_BELLY];
		}
		//src物品强化等级降级到默认强化等级
		$reinforceLevel = ItemDef::ITEM_REINFORCE_LEVEL_DEFAULT;

		//得到强化需求
		$target_reinforceInfo = $target_item->reinforceReq();
		//target物品的强化等级从默认强化等级开始计算
		$target_reinforceLevel = ItemDef::ITEM_REINFORCE_LEVEL_DEFAULT;

		//计算target物品的强化等级
		for ( $i = 0; $i < ForgeConfig::ARM_MAX_REINFORCE_LEVEL; $i++ )
		{
			$belly -= $target_reinforceInfo[$target_reinforceLevel+1][ItemDef::REINFORCE_FEE_BELLY];
			//到达最大等级/超过用户等级/belly不足
			if ( $target_reinforceLevel >= ForgeConfig::ARM_MAX_REINFORCE_LEVEL
				|| $target_reinforceLevel >= $user->getLevel() || $belly < 0 )
			{
				break;
			}
			$target_reinforceLevel++;
		}

		//设置target物品的强化等级
		$target_item->setReinforceLevel($target_reinforceLevel);

		//设置src物品的强化等级
		$item->setReinforceLevel($reinforceLevel);

		//宝石传承
		if ( $transfer_gem == TRUE )
		{
			$gem_item_ids = $item->getGemItems();
			if ( $target_item->enchaseGems($gem_item_ids) == FALSE )
			{
				Logger::WARNING('invalid transfer, gem can not transfer!item_id:%d, target_item_id:%d!',
								$item_id, $target_item_id);
				return $return;
			}
			$item->clearAllGem();
		}

		//更新物品信息
		ItemManager::getInstance()->update();

		//用户更新
		$user->update();

		//更新forge信息
		$values = array(
			ForgeDef::FORGE_TRANSFER_TIME => $this->m_info[ForgeDef::FORGE_TRANSFER_TIME] + 1,
		);
		$this->setForgeInfo($values);

		//Statistics
		if ( !empty($gold) )
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_FORGE_REINFORCE_TRANSFER,
							$gold,
							Util::getTime());
		}

		// 通知成就系统
		if ( $item->getArmType() == ItemDef::ITEM_ARM_ARM )
		{
			EnAchievements::notify(RPCContext::getInstance()->getUid(),
				AchievementsDef::REFORCE_ARM_COLOR_TIMES, $target_item->getItemQuality(),
				$target_item->getReinforceLevel());
		}

		//物品如果不在背包里则战斗优化
		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID||
			 $bag->getGridID($target_item_id) == BagDef::BAG_INVALID_BAG_ID	 )
		{
			EnUser::modifyBattleInfo();
		}
		
		return array (
			$item_id => ItemManager::getInstance()->itemInfo($item_id),
			$target_item_id => ItemManager::getInstance()->itemInfo($target_item_id),
		);
	}

	/* (non-PHPdoc)
	 * @see IForge::enchase()
	 */
	public function enchase($item_id, $gem_item_id, $hole_id) {

		//格式化输入
		$item_id = intval($item_id);
		$gem_item_id = intval($gem_item_id);
		$hole_id = intval($hole_id);

		//检查装备是否属于该用户
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return FALSE;
		}
		$item = ItemManager::getInstance()->getItem($item_id);
		$gem_item = ItemManager::getInstance()->getItem($gem_item_id);
		//如果物品不存在,或者类型不符合,则返回FALSE
		if ( $gem_item === NULL || $item === NULL
			|| $gem_item->getItemType() != ItemDef::ITEM_GEM
			|| $item->getItemType() != ItemDef::ITEM_ARM )
		{
			Logger::debug('enchase invalid item!');
			return FALSE;
		}

		$bag = BagManager::getInstance()->getBag();
		$user = EnUser::getInstance();

		//扣除镶嵌费用
		$enchaseReq = $gem_item->getEnchaseReq();
		if ( !empty($enchaseReq[ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_BELLY]) &&
			$user->subBelly($enchaseReq[ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_BELLY]) == FALSE )
		{
			Logger::DEBUG('no enough belly!');
			return FALSE;
		}
		if ( !empty($enchaseReq[ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_GOLD]) &&
			$user->subGold($enchaseReq[ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_GOLD]) == FALSE )
		{
			Logger::DEBUG('no enough gold!');
			return FALSE;
		}

		//镶嵌宝石
		if ( $item->enchase($gem_item_id, $hole_id) == TRUE )
		{
			//删除宝石,必须在enchase后调用,否则item已经被删除
			if ( $bag->removeItem($gem_item_id) == FALSE )
			{
				Logger::DEBUG('no enough gem item!');
				return FALSE;
			}
			//更新背包数据,数据变化不需要通知给前端
			$bag->update();
			//更新用户数据
			$user->update();

			//调用任务系统
			TaskNotify::operate(TaskOperateType::GEM_ENCHASE);

			//物品如果不在背包里则战斗优化
			$bag = BagManager::getInstance()->getBag();
			if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
			{
				EnUser::modifyBattleInfo();
			}
			
			//Statistics
			if (!empty($enchaseReq[ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_GOLD]) )
			{
				Statistics::gold(StatisticsDef::ST_FUNCKEY_FORGE_ENCHASE,
							$enchaseReq[ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_GOLD],
							Util::getTime());
			}

			return TRUE;
		}
		else
		{
			return FALSE;
		}
		return FALSE;

	}

	/* (non-PHPdoc)
	 * @see IForge::split()
	 */
	public function split($item_id, $hole_id) {

		//格式化输入
		$item_id = intval($item_id);
		$hole_id = intval($hole_id);

		//检查装备是否属于该用户
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d is not belong to me!', $item_id);
			return array();
		}

		$item = ItemManager::getInstance()->getItem($item_id);
		//如果物品不存在,或者类型不符合,则返回FALSE
		if ( $item === NULL	|| $item->getItemType() != ItemDef::ITEM_ARM )
		{
			Logger::debug('enchase invalid item!');
			return array();
		}

		$gem_item_id = $item->split($hole_id);
		if ( $gem_item_id == ItemDef::ITEM_ID_NO_ITEM )
		{
			Logger::DEBUG('split failed!');
			return array();
		}

		$bag = BagManager::getInstance()->getBag();
		$user = EnUser::getInstance();

		$gem_item = ItemManager::getInstance()->getItem($gem_item_id);

		if ( $gem_item->getItemType() != ItemDef::ITEM_GEM )
		{
			Logger::FATAL('invalid gem in hole!arm_item_id:%d', $item_id);
			return array();
		}

		//扣除摘除费用
		$splitReq = $gem_item->getSplitReq();
		if ( !empty($splitReq[ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_BELLY]) &&
			$user->subBelly($splitReq[ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_BELLY]) == FALSE )
		{
			Logger::DEBUG('no enough belly!');
			return array();
		}
		if ( !empty($splitReq[ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_GOLD]) &&
			$user->subGold($splitReq[ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_GOLD]) == FALSE )
		{
			Logger::DEBUG('no enough gold!');
			return array();
		}

		//放入背包
		if ( $bag->addItem($gem_item_id) == FALSE )
		{
			Logger::DEBUG('bag full!');
			return array();
		}

		//更新用户数据
		$user->update();

		//物品如果不在背包里则战斗优化
		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			EnUser::modifyBattleInfo();
		}
		
		//Statistics
		if ( !empty($splitReq[ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_GOLD]) )
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_FORGE_SPLIT,
						$splitReq[ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_GOLD],
						Util::getTime());
		}

		//更新背包装备
		return $bag->update();
	}

	/* (non-PHPdoc)
	 * @see IForge::reinforce()
	 */
	public function reinforce($item_id, $special) {

		$reinforce_success = TRUE;

		//初始化数据
		$this->forgeInfo();

		$item_id = intval($item_id);

		//检查功能是否开启
		if ( EnSwitch::isOpen(SwitchDef::REINFORCE) == FALSE )
		{
			Logger::DEBUG('reinforce is not open!');
			return array();
		}

		//检查装备是否属于该用户
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return array();
		}

		$item = $this->m_manager->getItem($item_id);
		//检查强化的物品是否为装备
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_ARM )
		{
			Logger::DEBUG('item_id:%d, not a arm!', $item_id);
			return array();
		}

		//检查强化时间是否在冷却
		if ( $this->m_info[ForgeDef::FORGE_REINFORCE_FREEZE] == 1 )
		{
			Logger::DEBUG('in freeze!');
			return array();
		}

		//得到用户对象
		$user = EnUser::getUserObj();

		//得到装备强化等级
		
		$reinforceLevel = $item->getReinforceLevel();		
		if ( $reinforceLevel >= $user->getLevel() )
		{
			Logger::DEBUG('reinforce level >= user level!');
			return array();
		}
		if ( $reinforceLevel >= ForgeConfig::ARM_MAX_REINFORCE_LEVEL )
		{
			Logger::DEBUG('max reinforce level!');
			return array();
		}

		$reinforceInfo = $item->reinforceReq();
		$reinforceInfo = $reinforceInfo[$reinforceLevel+1];

		//检查belly是否足够
		if ( $user->subBelly($reinforceInfo[ItemDef::REINFORCE_FEE_BELLY]) == FALSE )
		{
			Logger::DEBUG('no enough money!');
			return array();
		}

		$probabilityInfo = self::getReinforceProbability();
		//如果拉取不到数据,则把callback置为dummy
		if ( empty($probabilityInfo[ForgeDef::REINFORCE_PROBABILITY_NAME]) )
		{
			Logger::WARNING('reinforce probablity is null');
			RPCContext::getInstance()->getFramework()->resetCallback();
			return;
		}
		$probability = $probabilityInfo[ForgeDef::REINFORCE_PROBABILITY_NAME];

		//如果在新手阶段,则强化概率为100
		if ( EnSwitch::isOpen(SwitchDef::FUNCTION_CHANGE) == FALSE )
		{
			$probability = ForgeDef::MAX_REINFORCE_PROBABILITY;
		}

		//如果用户开启100强化
		if ( $this->m_info[ForgeDef::FORGE_IS_MAX_PROBABILITY] )
		{
			$probability = ForgeDef::MAX_REINFORCE_PROBABILITY;
		}

		//检查gold是否足够
		$needGold = 0;
		if ( $special == TRUE )
		{
			$needGold = ForgeDef::MAX_REINFORCE_PROBABILITY - $probability;
			$probability = ForgeDef::MAX_REINFORCE_PROBABILITY;
			//如果所需的金币数量大于配置的最大值,则为最大值
			if ( $needGold > ForgeConfig::ARM_REINFORCE_GOLD_MAX )
			{
				$needGold = ForgeConfig::ARM_REINFORCE_GOLD_MAX;
			}
			//检查当前的金币是否足够使用
			if ( $needGold > 0 )
			{
				if ( $user->subGold($needGold) == FALSE )
				{
					Logger::DEBUG('no enough gold!');
					return array();
				}
			}
		}

		//扣除物品
		$bag = BagManager::getInstance()->getBag();		
		if ( !isset($reinforceInfo[ItemDef::REINFORCE_FEE_ITEMS]) && !empty($reinforceInfo[ItemDef::REINFORCE_FEE_ITEMS]) )
		{
			if ( $bag->deleteItemsbyTemplateID($reinforceInfo[ItemDef::REINFORCE_FEE_ITEMS]) == FALSE )
			{
				Logger::FATAL('deleteItemsByTemplateID failed, items:%s', $reinforceInfo[ItemDef::REINFORCE_FEE_ITEMS]);
				return array();
			}
		}

		//强化
		$rand = rand(0, ForgeDef::MAX_REINFORCE_PROBABILITY);
		//强化成功
		if ( $rand <= $probability )
		{
			if ( $item->reinforce(ForgeConfig::ARM_MAX_REINFORCE_LEVEL) == FALSE )
			{
				return array();
			}
		}
		else//强化失败
		{
			Logger::INFO('reinforce rand failed!rand:%d, max:%d', $rand, $probability);
			$reinforce_success = FALSE;
		}

		//扣除强化所需
		$bag_modify = $bag->update();

		//扣除belly扣除金币
		$user->update();

		//增加冷却时间
		//如果物品上配置了强化冷却时间,则使用配置的时间,否则使用默认配置ForgeConfig::ARM_REINFORCE_INC_TIME
		$inc_time = $item->getReinforceIncTime();
		if ( empty($inc_time) )
		{
			$inc_time = ForgeConfig::ARM_REINFORCE_INC_TIME;
		}

		$values = array();
		//如果当前冷却时间为0,则设置冷却时间为当前系统时间
		if ( $this->m_info[ForgeDef::FORGE_REINFORCE_TIME] == 0 )
		{
			$values[ForgeDef::FORGE_REINFORCE_TIME] = Util::getTime();
		}
		else
		{
			$values[ForgeDef::FORGE_REINFORCE_TIME] = $this->m_info[ForgeDef::FORGE_REINFORCE_TIME];
		}
		$values[ForgeDef::FORGE_REINFORCE_TIME] = $values[ForgeDef::FORGE_REINFORCE_TIME] + $inc_time;

		//如果冷却时间超过冻结,则试着冻结状态
		if ( $values[ForgeDef::FORGE_REINFORCE_TIME] - Util::getTime() >=
			ForgeConfig::ARM_REINFORCE_FREEZE_TIME )
		{
			$values[ForgeDef::FORGE_REINFORCE_FREEZE] = 1;
		}
		$this->setForgeInfo($values);

		//调用每日任务
		EnDaytask::reinforce();
		//调用活跃度系统
		EnActive::addReinforceTimes();
		//调用节日活动系统
		EnFestival::addPeinforcePoint();
		//调用任务系统
		TaskNotify::operate(TaskOperateType::REINFORCE);

		//物品如果不在背包里则战斗优化
		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			EnUser::modifyBattleInfo();
		}
		
		//Statistics
		if ( $special == TRUE && !empty($needGold) )
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_FORGE_REINFORCE,
						$needGold,
						Util::getTime());
		}

		// 通知成就系统
		if ( $reinforce_success == TRUE )
		{
			EnAchievements::notify(RPCContext::getInstance()->getUid(),
		    	AchievementsDef::ITEM_REFORCE_LEVEL, $item->getReinforceLevel());
			EnAchievements::notify(RPCContext::getInstance()->getUid(),
				AchievementsDef::REFORCE_OK_TIMES, 1);
			if ( $item->getArmType() == ItemDef::ITEM_ARM_ARM )
			{
				EnAchievements::notify(RPCContext::getInstance()->getUid(),
					AchievementsDef::REFORCE_ARM_COLOR_TIMES, $item->getItemQuality(),
					$item->getReinforceLevel());
			}
		}

		return array(
			'reinforce_success' => $reinforce_success,
			'reinforce_time'	=> $this->m_info[ForgeDef::FORGE_REINFORCE_TIME],
			'reinforce_freeze'	=> $this->m_info[ForgeDef::FORGE_REINFORCE_FREEZE],
			'reinforce_items'	=> $bag_modify
		);
	}

	/* (non-PHPdoc)
	 * @see IForge::weakening()
	 */
	public function weakening($item_id, $level) {

		$item_id = intval($item_id);
		$level = intval($level);

		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return array();
		}

		$item = ItemManager::getInstance()->getItem($item_id);
		//检测降级的物品是否为装备
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_ARM )
		{
			Logger::DEBUG("item:%d(item_tempalte_id:%d) is not a arm!", $item_id, $item->getItemTemplateID() );
			return FALSE;
		}

		//检测降级的级数是否合法
		if ( !in_array($level, ForgeConfig::$ARM_WEAKENING_LEVEL_ALLOWED) )
		{
			Logger::DEBUG("level:%d is invalid!", $level);
			return FALSE;
		}

		//得到装备的当前强化等级
		$reinforceLevel = $item->getReinforceLevel();
		//弱化,此函数会检查$level的输入是否合法
		if ( $item->weakening($level) == FALSE )
		{
			return FALSE;
		}

		//得到物品强化信息
		$reinforceInfo = $item->reinforceReq();
		//计算所应该加的Belly数量
		$belly = 0;
		for ( $i = $level-1; $i >= 0; $i-- )
		{
			$belly += $reinforceInfo[$reinforceLevel - $i][ItemDef::REINFORCE_FEE_BELLY]
					* ForgeConfig::ARM_WEAKING_RECOVERY_PERCENT;
		}

		//增加贝里
		$user = EnUser::getInstance();
		if ( $user->addBelly($belly) == FALSE )
		{
			return FALSE;
		}

		//@see 强化时需要的物品不需归还给用户
		//更新数据
		$user->update();
		ItemManager::getInstance()->update();

		return TRUE;
	}

	/* (non-PHPdoc)
	 * @see IForge::reinforceBoatArm()
	 * @delete!
	 */
	public function reinforceBoatArm($item_id, $items)
	{
		//格式化输入
		$item_id = intval($item_id);

		$reinforce_success = TRUE;

		//检查装备是否属于该用户
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return array();
		}

		$item = $this->m_manager->getItem($item_id);
		//检查强化的物品是否为主船装备
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_BOATARM )
		{
			Logger::DEBUG('item_id:%d, not a boat arm!', $item_id);
			return array();
		}

		//检查物品是否可以强化
		if ( $item->canReinforce() == FALSE )
		{
			Logger::DEBUG('item_id:%d can not reinforce!', $item_id);
			return array();
		}

		//得到装备强化等级
		$reinforceLevel = $item->getReinforceLevel();
		if ( $reinforceLevel >= ForgeConfig::BOAT_ARM_MAX_REINFORCE_LEVEL )
		{
			Logger::DEBUG('max reinforce level!');
			return array();
		}

		$reinforceInfo = $item->reinforceReq();

		//检查belly是否足够
		$user = EnUser::getInstance();
		if ( !empty($reinforceInfo[ItemDef::REINFORCE_FEE_BELLY][$reinforceLevel+1]) &&
			$user->subBelly($reinforceInfo[ItemDef::REINFORCE_FEE_BELLY][$reinforceLevel+1]) == FALSE )
		{
			Logger::DEBUG('no enough money!');
			return array();
		}

		$probability = $reinforceInfo[ItemDef::REINFORCE_FEE_PROBABILITY][$reinforceLevel+1];
		//检查gold是否足够
		if ( !empty($reinforceInfo[ItemDef::REINFORCE_FEE_GOLD][$reinforceLevel+1]) &&
			$user->subBelly($reinforceInfo[ItemDef::REINFORCE_FEE_GOLD][$reinforceLevel+1]) == FALSE )
		{
			Logger::DEBUG('no enough gold!');
			return array();
		}

		//扣除物品,由于物品本身的问题,所以不能随便扣除物品
		$bag = BagManager::getInstance()->getBag();
		if ( !isset($reinforceInfo[ItemDef::REINFORCE_FEE_ITEMS])
		 && !empty($reinforceInfo[ItemDef::REINFORCE_FEE_ITEMS]) )
		{
			$tmp_items = array();
			foreach ( $items as $item_id => $item_num )
			{
				$item = ItemManager::getInstance()->getItem($item_id);
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
			foreach ( $reinforceInfo[ItemDef::REINFORCE_FEE_ITEMS] as $item_template_id => $item_num )
			{
				if ( $tmp_items[$item_template_id] != $item_num )
				{
					Logger::DEBUG('invalid input items');
					return array();
				}
			}
			foreach ( $items as $item_id => $item_num )
			{
				if ( $bag->decreaseItem($item_id, $item_num) == FALSE )
				{
					Logger::DEBUG("no enough item!item_id:%d, item_num:%d", $item_id, $item_num);
					return array();
				}
			}
		}

		//强化
		$rand = rand(0, ForgeDef::MAX_BOATARM_REINFORCE_PROBABILITY);
		//强化成功
		if ( $rand <= $probability )
		{
			if ( $item->reinforce(ForgeConfig::ARM_MAX_REINFORCE_LEVEL) == FALSE )
			{
				return array();
			}
		}
		else//强化失败
		{
			Logger::DEBUG('reinforce rand failed!rand:%d, max:%d', $rand, $probability);
			$reinforce_success = FALSE;
		}

		//扣除强化所需
		$bag_modify = $bag->update();

		//扣除belly扣除金币
		$user->update();

		return array(
			'reinforce_success' => $reinforce_success,
			'reinforce_items' => $bag_modify
		);
	}

	/* (non-PHPdoc)
	 * @see IForge::compose()
	 */
	public function compose($compose_id, $compose_number = 1, $items = array()) {

		//格式化输入
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
	}

	/* (non-PHPdoc)
	 * @see IForge::decompose()
	 */
	public function decompose($item_id) {

		//TODO
	}

	/* (non-PHPdoc)
	 * @see IForge::randRefresh()
	 */
	public function randRefresh($item_id, $special) {

		//格式化输入
		$item_id = intval($item_id);
		$special = intval($special);

		$return = array ('refresh_success' => FALSE);

		//检查功能是否开启
		if ( EnSwitch::isOpen(SwitchDef::ARM_REFRESH) == FALSE )
		{
			Logger::DEBUG('refresh is not open!');
			return $return;
		}

		//检查物品是否存在及是否是装备
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return $return;
		}
		$item = ItemManager::getInstance()->getItem($item_id);
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_ARM )
		{
			Logger::DEBUG('invalid item_id:%d', $item_id);
			return $return;
		}

		//减少金币和belly
		$user = EnUser::getInstance();
		if ( $special == FALSE )
		{
			//得到物品洗练需求
			$refresh_req_belly = $item->getRandRefreshReqBelly();
			if ( $user->subBelly($refresh_req_belly) == FALSE )
			{
				Logger::DEBUG('no enough belly!');
				return $return;
			}
		}
		else
		{
			if ( $user->subGold(ForgeConfig::ARM_RAND_REFRESH_POTENTIALITY) == FALSE )
			{
				Logger::DEBUG('no enough gold!');
				return $return;
			}
		}

		//随机洗练物品
		$refresh_info = $item->randRefresh();

		//更新金币和belly信息
		$user->update();

		//更新物品信息
		ItemManager::getInstance()->update();

		//调用每日任务
		EnDaytask::refreshEquip();
		//调用任务系统
		TaskNotify::operate(TaskOperateType::ARM_REFRESH);

		//Statistics
		if ( $special == TRUE )
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_FORGE_RAND_REFRESH,
							ForgeConfig::ARM_RAND_REFRESH_POTENTIALITY,
							Util::getTime());
		}

		$return = array (
			'refresh_success' => TRUE,
			'potentiality' => $refresh_info,
		);

		return $return;
	}

	/* (non-PHPdoc)
	 * @see IForge::fixedRefresh()
	 */
	public function fixedRefresh($item_id, $type) {

		//格式化输入
		$item_id = intval($item_id);
		$type = intval($type);

		$return = array ('refresh_success' => FALSE);

		//检查功能是否开启
		if ( EnSwitch::isOpen(SwitchDef::ARM_REFRESH) == FALSE )
		{
			Logger::DEBUG('refresh is not open!');
			return $return;
		}

		//检查物品是否存在及是否是装备
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return $return;
		}
		$item = ItemManager::getInstance()->getItem($item_id);
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_ARM )
		{
			Logger::DEBUG('invalid item_id:%d', $item_id);
			return $return;
		}

		//检查type是否合法
		if ( in_array($type, ForgeConfig::$VALID_FIXED_REFRESH_TYPES) == FALSE )
		{
			Logger::DEBUG('invalid type:%d', $type);
			return $return;
		}


		//减少金币和belly
		$user = EnUser::getInstance();
		if ( $type == ForgeDef::FIXED_REFRESH_TYPE_NORMAIL )
		{
			//得到物品洗练需求
			$refresh_req_belly = $item->getFixedRefreshReqBelly();
			if ( $user->subBelly($refresh_req_belly) == FALSE )
			{
				Logger::DEBUG('no enough belly!');
				return $return;
			}
		}
		else
		{
			$vip_level = $user->getVip();
			$gold = self::getFixedRefreshReqGold($vip_level, $type);
			if ( empty($gold) )
			{
				Logger::DEBUG('vip:%d can no use fixed refresh type:%d', $vip_level, $type);
				return $return;
			}
			if ( $user->subGold($gold) == FALSE )
			{
				Logger::DEBUG('no enough gold');
				return $return;
			}
		}

		//固定洗练物品
		$refresh_info = $item->fixedRefresh($type);

		//更新金币和belly信息
		$user->update();

		//更新物品信息
		ItemManager::getInstance()->update();

		//调用每日任务
		EnDaytask::refreshEquip();
		//调用任务系统
		TaskNotify::operate(TaskOperateType::ARM_REFRESH);

		//Statistics
		if ( $type != ForgeDef::FIXED_REFRESH_TYPE_NORMAIL )
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_FORGE_FIXED_REFRESH,
						$gold, Util::getTime());
		}

		$return = array (
			'refresh_success' => TRUE,
			'potentiality' => $refresh_info,
		);

		return $return;
	}

	/* (non-PHPdoc)
	 * @see IForge::randRefreshAffirm()
	 */
	public function randRefreshAffirm($item_id) {

		//格式化输入
		$item_id = intval($item_id);

		//检查功能是否开启
		if ( EnSwitch::isOpen(SwitchDef::ARM_REFRESH) == FALSE )
		{
			Logger::DEBUG('refresh is not open!');
			return FALSE;
		}

		//检查物品是否存在及是否是装备
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return FALSE;
		}
		$item = ItemManager::getInstance()->getItem($item_id);
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_ARM )
		{
			Logger::DEBUG('invalid item_id:%d', $item_id);
			return FALSE;
		}

		if ( $item->randRefreshAffirm() == TRUE )
		{
			ItemManager::getInstance()->update();
			
			//物品如果不在背包里则战斗优化
			$bag = BagManager::getInstance()->getBag();
			if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
			{
				EnUser::modifyBattleInfo();
			}
			
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/* (non-PHPdoc)
	 * @see IForge::fixedRefreshAffirm()
	 */
	public function fixedRefreshAffirm($item_id) {

		//格式化输入
		$item_id = intval($item_id);

		//检查功能是否开启
		if ( EnSwitch::isOpen(SwitchDef::ARM_REFRESH) == FALSE )
		{
			Logger::DEBUG('refresh is not open!');
			return FALSE;
		}

		//检查物品是否存在及是否是装备
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return FALSE;
		}
		$item = ItemManager::getInstance()->getItem($item_id);
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_ARM )
		{
			Logger::DEBUG('invalid item_id:%d', $item_id);
			return FALSE;
		}

		if ( $item->fixedRefreshAffirm() == TRUE )
		{
			ItemManager::getInstance()->update();
			
			//物品如果不在背包里则战斗优化
			$bag = BagManager::getInstance()->getBag();
			if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
			{
				EnUser::modifyBattleInfo();
			}
			
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/* (non-PHPdoc)
	 * @see IForge::potentialityTransfer()
	 */
	public function potentialityTransfer($src_item_id, $target_item_id, $transfer_type)
	{
		//格式化输入
		$src_item_id = intval($src_item_id);
		$target_item_id = intval($target_item_id);
		$transfer_type = intval($transfer_type);

		$return = array('transfer_success' => FALSE);

		//转换类型是否合法
		if ( !in_array($transfer_type, ForgeConfig::$VALID_POTENTIALITY_TRANSFER_TYPES) )
		{
			Logger::DEBUG('invalid potentiality transfer type:%d!', $transfer_type);
			return $return;
		}

		//检查源物品id是否和目标物品id相同
		if ( $src_item_id == $target_item_id )
		{
			Logger::DEBUG('item_id:%d = target item_id:%d!',
				$src_item_id, $target_item_id);
			return $return;
		}

		//检查物品是否存在
		$src_item = ItemManager::getInstance()->getItem($src_item_id);
		$target_item = ItemManager::getInstance()->getItem($target_item_id);
		if ( $src_item === NULL )
		{
			Logger::DEBUG('item_id:%s not exist!', $src_item_id);
			return $return;
		}
		if ( $target_item === NULL )
		{
			Logger::DEBUG('item_id:%s not exist!', $target_item_id);
			return $return;
		}
		if ( EnUser::itemBelongTo($src_item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $src_item_id);
			return $return;
		}
		if ( EnUser::itemBelongTo($target_item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $target_item_id);
			return $return;
		}

		//检查物品是否是ARM
		$src_item_type = $src_item->getItemType();
		$target_item_type = $target_item->getItemType();
		if ( $src_item_type != ItemDef::ITEM_ARM ||
			$target_item_type != ItemDef::ITEM_ARM )
		{
			Logger::DEBUG('source item:%d is not arm, item type:%d or target item:%d is not arm, arm type;%d!',
				$src_item_id, $src_item_type, $target_item_id, $target_item_type);
			return $return;
		}

		//检查物品品质是否合法,源物品品质<=目标物品品质
		$src_item_quality = $src_item->getItemQuality();
		$target_item_quality = $target_item->getItemQuality();
		if ( $src_item_quality > $target_item_quality )
		{
			Logger::DEBUG('source item quality:%d must <= target item quality:%d!',
				$src_item_quality, $target_item_quality);
			return $return;
		}

		//源物品品质必须>=红色
		if ( $src_item_quality < ItemDef::ITEM_QUALITY_RED )
		{
			Logger::DEBUG('source item quality:%d must >= %d!',
				$src_item_quality, ItemDef::ITEM_QUALITY_RED);
			return $return;
		}

		//目标物品品质必须>=红色
		if ( $target_item_quality < ItemDef::ITEM_QUALITY_RED )
		{
			Logger::DEBUG('target item quality:%d must >= %d!',
				$target_item_quality, ItemDef::ITEM_QUALITY_RED);
			return $return;
		}

		//检查源物品和目标物品是否可以随机洗练
		if ( $src_item->canRandomRefresh() == FALSE ||
			$target_item->canRandomRefresh() == FALSE )
		{
			Logger::DEBUG('source item:%d or target item:%d can not random refresh!',
				$src_item, $target_item);
			return $return;
		}

		$src_item_text = $src_item->getItemText();
		$src_potentiality = $src_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY];

		if ( empty($src_potentiality) )
		{
			Logger::DEBUG('source item:%d does not have potentiality!', $src_item_id);
			return $return;
		}

		$user = EnUser::getUserObj();
		$vip_level = $user->getVip();

		$potentiality_transfer_req = $this->getVipPTransferInfo($vip_level);

		$values = array();
		switch ( $transfer_type )
		{
			case ForgeDef::POTENTIALITY_TRANSFER_TYPE_GOLD:
				if ( $user->subGold($potentiality_transfer_req[ForgeDef::POTENTIALITY_TRANSFER_REQ_GOLD])
					 == FALSE )
				{
					Logger::DEBUG('no enough gold!');
					return $return;
				}
				break;
			case ForgeDef::POTENTIALITY_TRANSFER_TYPE_ITEM:
				$bag = BagManager::getInstance()->getBag();
				if ( $bag->deleteItembyTemplateID($potentiality_transfer_req[ForgeDef::POTENTIALITY_TRANSFER_REQ_ITEM],
					1) == FALSE )
				{
					Logger::DEBUG('no enough item!');
					return $return;
				}
				break;
			case ForgeDef::POTENTIALITY_TRANSFER_TYPE_FREE:
				//数据初始化
				$this->forgeInfo();
				if ( $this->m_info[ForgeDef::FORGE_POTENTIALITY_TRANSFER_TIME] >=
					$potentiality_transfer_req[ForgeDef::MAX_POTENTIALITY_TRANSFER_TIME] )
				{
					Logger::DEBUG('max free potentiality transfer time!');
					return $return;
				}
				$values = array(
					ForgeDef::FORGE_POTENTIALITY_TRANSFER_TIME =>
						 $this->m_info[ForgeDef::FORGE_POTENTIALITY_TRANSFER_TIME] + 1,
				);
				break;
			default:
				return $return;
				break;
		}

		$src_potentiality_id = $src_item->getRandPotentialityId();
		$target_potentiality_id = $target_item->getRandPotentialityId();

		$target_potentiality = Potentiality::transferPotentiality($src_potentiality_id, $target_potentiality_id,
			 $src_potentiality, ForgeConfig::$VALID_FIXED_REFRESH_TYPES);

		//更新源物品和目标物品
		$src_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY] = array();
		unset($src_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_RAND_REFRESH]);
		unset($src_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH]);
		$src_item->setItemText($src_item_text);

		$target_item_text = $target_item->getItemText();
		$target_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY] = $target_potentiality;
		unset($target_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_RAND_REFRESH]);
		unset($target_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH]);
		$target_item->setItemText($target_item_text);

		ItemManager::getInstance()->update();
		$user->update();

		$bag_modify = array();
		if ( $transfer_type == ForgeDef::POTENTIALITY_TRANSFER_TYPE_ITEM )
		{
			$bag = BagManager::getInstance()->getBag();
			$bag_modify = $bag->update();
		}
		else if ( $transfer_type == ForgeDef::POTENTIALITY_TRANSFER_TYPE_FREE )
		{
			$this->setForgeInfo($values);
		}

		//物品如果不在背包里则战斗优化
		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($src_item_id) == BagDef::BAG_INVALID_BAG_ID||
			 $bag->getGridID($target_item_id) == BagDef::BAG_INVALID_BAG_ID	 )
		{
			EnUser::modifyBattleInfo();
		}
		
		return array(
			'transfer_success' => TRUE,
			'items' => array (
				$src_item_id => $src_item->itemInfo(),
				$target_item_id => $target_item->itemInfo(),
			),
			'bag_info' => $bag_modify,
		);
	}

	/* (non-PHPdoc)
	 * @see IForge::getPotentialityTransfer()
	 */
	public function getPotentialityTransfer()
	{
		//初始化数据
		$this->forgeInfo();
		return array (
			'potentiality_free_time'	=>	$this->m_info[ForgeDef::FORGE_POTENTIALITY_TRANSFER_TIME],
			'potentiality_refresh_time'	=>	$this->m_info[ForgeDef::FORGE_POTENTIALITY_TRANSFER_RESET_TIME],
		);
	}

	/* (non-PHPdoc)
	 * @see IForge::getTransferInfo()
	 */
	public function getTransferInfo()
	{
		//初始化数据
		$this->forgeInfo();

		return array(
			ForgeDef::FORGE_TRANSFER_TIME => $this->m_info[ForgeDef::FORGE_TRANSFER_TIME],
			'transfer_max_gold' => ForgeConfig::ARM_TRANSFER_MAX_GOLD,
			'transfer_inc_gold' => ForgeConfig::ARM_TRANSFER_GOLD_PRETIME,
		);
	}

	/* (non-PHPdoc)
	 * @see IForge::getReinforceCD()
	 */
	public function getReinforceCD() {
		//初始化数据
		$this->forgeInfo();

		$return = array();
		$return[ForgeDef::FORGE_REINFORCE_TIME] = $this->m_info[ForgeDef::FORGE_REINFORCE_TIME];
		$return[ForgeDef::FORGE_REINFORCE_FREEZE] = $this->m_info[ForgeDef::FORGE_REINFORCE_FREEZE];
		return $return;
	}

	/* (non-PHPdoc)
	 * @see IForge::resetReinforceTime()
	 */
	public function resetReinforceTime() {
		//初始化数据
		$this->forgeInfo();

		//如果强化冷却时间小于当前时间,则不需要刷新
		if ( $this->m_info[ForgeDef::FORGE_REINFORCE_TIME] < Util::getTime() )
		{
			Logger::DEBUG('no need reset reinforce time!');
			return FALSE;
		}
		else
		{
			$time = $this->m_info[ForgeDef::FORGE_REINFORCE_TIME] - Util::getTime();
			//所需金币向上取整
			$gold = ceil(floatval($time) / ForgeConfig::ARM_REINFORCE_RESET_SECOND);
			$user = EnUser::getInstance();
			if ( $user->subGold($gold) == TRUE )
			{
				$this->setForgeInfo(ForgeDef::$FORGE_REINFORCE_VALUES);
				$user->update();

				//Statistics
				Statistics::gold(StatisticsDef::ST_FUNCKEY_FORGE_RESET_REINFORCE_TIME,
								$gold, Util::getTime());

				return $gold;
			}
			else
			{
				Logger::DEBUG('no enough gold!');
				return FALSE;
			}
		}
	}

	/* (non-PHPdoc)
	 * @see IForge::getReinforceProbability()
	 */
	public function getReinforceProbability()
	{

		//初始化数据
		$this->forgeInfo();

		$reinfroce_probability = ForgeDAO::getReinforceProbability();
		$probability = $reinfroce_probability[ForgeDef::REINFORCE_PROBABILITY_NAME];
		$direction = $reinfroce_probability[ForgeDef::REINFORCE_DIRECTION_NAME];
		$refresh_time = $reinfroce_probability[ForgeDef::REINFORCE_REFRESH_TIME_NAME];

		//如果没有得到数据,则出现致命错误,记录日志,返回无效数值,并且由lcserver调用产生新的值
		if ( $probability == NULL )
		{
			RPCContext::getInstance()->executeTask(ForgeConfig::FORGE_TIMER_UID, 'forge.refreshReinforceProbability', array());
			Logger::WARNING('reinforce probability is not init!');
			return array(ForgeDef::FORGE_IS_MAX_PROBABILITY => $this->m_info[ForgeDef::FORGE_IS_MAX_PROBABILITY]);
		}
		else if ( $refresh_time < Util::getTime() )
		{
			RPCContext::getInstance()->executeTask(ForgeConfig::FORGE_TIMER_UID, 'forge.refreshReinforceProbability', array());
			Logger::WARNING('reinforce probability timer failed!');
			return array(ForgeDef::FORGE_IS_MAX_PROBABILITY => $this->m_info[ForgeDef::FORGE_IS_MAX_PROBABILITY]);
		}
		else
		{
			return array (
				ForgeDef::REINFORCE_PROBABILITY_NAME => $probability,
				ForgeDef::REINFORCE_DIRECTION_NAME => $direction,
				ForgeDef::FORGE_IS_MAX_PROBABILITY => $this->m_info[ForgeDef::FORGE_IS_MAX_PROBABILITY],
			);
		}
	}

	/**
	 *
	 * 刷新强化概率,每隔固定时间刷新,并向前端广播
	 *
	 * @return NULL
	 *
	 */
	public static function refreshReinforceProbability()
	{
		$reinfroce_probability = ForgeDAO::getReinforceProbability();
		$probability = $reinfroce_probability[ForgeDef::REINFORCE_PROBABILITY_NAME];
		$direction = $reinfroce_probability[ForgeDef::REINFORCE_DIRECTION_NAME];
		$refresh_time = $reinfroce_probability[ForgeDef::REINFORCE_REFRESH_TIME_NAME];

		//如果重复请求,则忽略
		if ( $refresh_time > Util::getTime() )
		{
			return;
		}

		//如果数据尚未初始话,则初始化数据
		if ( $probability == NULL )
		{
			$probability = rand(ForgeConfig::INIT_REINFORCE_PROBABILITY_LOWER, ForgeConfig::INIT_REINFORCE_PROBABILITY_UPPER);
			$direction = rand(ForgeDef::REINFORCE_DIRECTION_MINIUS, ForgeDef::REINFORCE_DIRECTION_PLUS);
		}
		else
		{
			$rand = rand(ForgeConfig::RAND_REINFORCE_PROBABILITY_LOWER, ForgeConfig::RAND_REINFORCE_PROBABILITY_UPPER);
			if ( $direction )
			{
				$probability += $rand;
				if ( $probability > ForgeDef::MAX_REINFORCE_PROBABILITY )
				{
					$probability = ForgeDef::MAX_REINFORCE_PROBABILITY;
				}
			}
			else
			{
				$probability -= $rand;
				if ( $probability < ForgeDef::MIN_REINFORCE_PROBABILITY )
				{
					$probability = ForgeDef::MIN_REINFORCE_PROBABILITY;
				}
			}
		}
		//如果强化概率小于ForgeConfig::MINIUS_REINFORCE_PROBABILITY,则将强化概率变化方向置为下降
		if ( $probability >= ForgeConfig::MINIUS_REINFORCE_PROBABILITY )
		{
			$direction = ForgeDef::REINFORCE_DIRECTION_MINIUS;
		}
		//如果强化概率大于ForgeConfig::PLUS_REINFORCE_PROBABILITY,则将强化概率变化方向置为上升
		if ( $probability <= ForgeConfig::PLUS_REINFORCE_PROBABILITY )
		{
			$direction = ForgeDef::REINFORCE_DIRECTION_PLUS;
		}

		//计算下次的刷新时间
		$time = Util::getTime() + ForgeConfig::REFRESH_TIME_REINFORCE_PROBABILITY;
		$time -= $time % ForgeConfig::REFRESH_TIME_REINFORCE_PROBABILITY;

		ForgeDAO::setReinforceProbability($probability, $direction, $time);
		//增加Timer
		Logger::debug('refreshReinforceProbabilitytime:%d', $time);
		TimerTask::addTask(ForgeConfig::FORGE_TIMER_UID, $time, 'forge.refreshReinforceProbability', array());

		//向前端广播
		RPCContext::getInstance()->sendMsg(array(0), 're.forge.getReinforceProbability',
			array (
				ForgeDef::REINFORCE_PROBABILITY_NAME => $probability,
				ForgeDef::REINFORCE_DIRECTION_NAME => $direction,
			));

	}

	/**
	 *
	 * 得到合成条件
	 *
	 * @param int $compose_id						合成条件ID
	 *
	 * @throws Exception							如果相应的合成条件不存在,则抛出异常
	 *
	 * @return array
	 */
	private static function getComposeCondition($compose_id)
	{
		if ( !isset(btstore_get()->COMPOSECONDITION[$compose_id]) )
		{
			Logger::FATAL('invalid compose_id:%d', $compose_id);
			throw new Exception('fake');
		}
		return btstore_get()->COMPOSECONDITION[$compose_id];
	}

	/**
	 *
	 * 得到铁匠铺数据, 如果数据不存在，则初始化
	 *
	 */
	private function getForgeInfo()
	{
		$info = RPCContext::getInstance()->getSession(ForgeDef::SESSION_FORGE_INFO);
		if ( !isset($info) )
		{
			$info = ForgeDAO::getForge($this->m_uid);
			if ( empty($info) )
			{
				$info = ForgeDAO::initForge($this->m_uid);
			}
			RPCContext::getInstance()->setSession(ForgeDef::SESSION_FORGE_INFO, $info);
		}
		return $info;
	}

	/**
	 *
	 * 得到固定洗练所需要的gold
	 *
	 * @param int $vip_level
	 * @param int $type
	 * @throws Exception
	 *
	 * @return int
	 */
	private function getFixedRefreshReqGold($vip_level, $type)
	{
		if ( !isset(btstore_get()->VIP[$vip_level]) || !isset(btstore_get()->VIP[$vip_level]['refresh_potentiality'])
			|| !isset(btstore_get()->VIP[$vip_level]['refresh_potentiality'][$type]) )
		{
			Logger::FATAL('invalid vip_level:%d, type:%d!', $vip_level, $type);
			throw new Exception('fake');
		}
		return btstore_get()->VIP[$vip_level]['refresh_potentiality'][$type];
	}

	/**
	 *
	 * 得到装备转移刷新重置时间
	 *
	 * @param int $refresh_time						//当前重置时间
	 */
	private function getRefreshResetTime($refresh_time = 0)
	{
		if ( $refresh_time == 0 )
		{
			$date = date("Y-m-d ", Util::getTime());
			$date .= ForgeConfig::FORGE_RESET_DATE;
			$refresh_time = strtotime($date);
		}
		for ($i = 0; $i < ForgeDef::MAX_LOOP_TIME; $i++)
		{
			if ( $refresh_time > Util::getTime() )
			{
				return $refresh_time;
			}
			else
			{
				$refresh_time += ForgeConfig::FORGE_RESET_INTERVAL;
			}
		}
		Logger::FATAL('get refresh reset time failed!extend max execute time!');
	}

	/**
	 *
	 * 得到潜能转移刷新重置时间
	 *
	 * @param int $refresh_time						//当前重置时间
	 */
	private function getPotentialityTransferResetTime($refresh_time = 0)
	{
		if ( $refresh_time == 0 )
		{
			$date = date("Y-m-d ", Util::getTime());
			$weekday = date("N", Util::getTime());
			$date .= ForgeConfig::FORGE_RESET_DATE;
			$refresh_time = strtotime($date);
			$refresh_time = $refresh_time +
				( ForgeDef::WEEKEND - $weekday + 1) * ForgeDef::DAY_TIME;
		}
		for ($i = 0; $i < ForgeDef::MAX_LOOP_TIME; $i++)
		{
			if ( $refresh_time > Util::getTime() )
			{
				return $refresh_time;
			}
			else
			{
				$refresh_time += ForgeConfig::FORGE_POTENTIALITY_TRANSFER_RESET_INTERVAL;
			}
		}
		Logger::FATAL('get potentiality Transfer reset time failed!extend max execute time!');
	}

	public function getVipPTransferInfo($vip)
	{
		if ( !isset(btstore_get()->VIP[$vip]) ||
			!isset(btstore_get()->VIP[$vip][ForgeDef::POTENTIALITY_TRANSFER]) )
		{
			Logger::FATAL('invalid vip:%d potentiality transfer info!', $vip);
			throw new Exception('config!');
		}
		else
		{
			return btstore_get()->VIP[$vip][ForgeDef::POTENTIALITY_TRANSFER]->toArray();
		}
	}

	/**
	 *
	 * 更新数据
	 *
	 * @param array $values
	 *
	 */
	private function setForgeInfo($values)
	{
		ForgeDAO::setForge($this->m_uid, $values);
		foreach ($values as $key => $value)
		{
			$this->m_info[$key] = $value;
		}
		RPCContext::getInstance()->setSession(ForgeDef::SESSION_FORGE_INFO, $this->m_info);
	}
	
	/**
	 * 通过增加宝石经验，升级宝石等级，每次只升一级
	 */
	public function gemLevelUpByExp($item_id)
	{
		$return = array('levelup_success' => FALSE,'cost_exp'=>0);
		$item_id = intval($item_id);
		$item = ItemManager::getInstance()->getItem($item_id);
		
		//检测物品是否存在
		if ( $item == NULL || $item->getItemType() != ItemDef::ITEM_GEM )
		{
			Logger::warning('gemLevelUpByExp: item_id:%d not exist or not a gem!', $item_id);
			return $return;
		}
		
		//如果已经达到最大等级
		if ( $item->getLevel() >= $item->getMaxLevel() )
		{
			Logger::warning('gemLevelUpByExp:item_id:%d is max level', $item_id);
			return $return;
		}
		
		//获得升到下一级需要多少经验
		$costexp=$item->getNextLevelExp();
		if ($costexp < 0 )
		{
			return $return;
		}
		
		$user = EnUser::getUserObj();
		//如果当前经验不够，则直接灌到宝石里
		$curexp =$user->getGemExp();
		if ($curexp < $costexp )
		{
			$costexp=$curexp;
		}
		
		//扣除经验
		if(!$user->subGemExp($costexp))
		{
			return $return;
		}
		$user->update();
		
		//给宝石增加经验
		$item->addExp($costexp);
		ItemManager::getInstance()->update();
		
		//物品如果不在背包里则战斗优化
		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			EnUser::modifyBattleInfo();
		}
		
		//返回给前段
		$return['levelup_success']=TRUE;
		$return['cost_exp']=$costexp;
		return $return;
	}
		
	public function gild($item_id)
	{
		$ret = array('gild_success'=>false);

		//初始化数据
		// $this->forgeInfo();

		$item_id = intval($item_id);

		//检查功能是否开启
		if ( EnSwitch::isOpen(SwitchDef::CRYSTAL) == FALSE )
		{
			Logger::DEBUG('crystal is not open!');
			return array();
		}

		//检查装备是否属于该用户
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return array();
		}

		$item = ItemManager::getInstance()->getItem($item_id);
		//检查强化的物品是否为装备
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_ARM )
		{
			Logger::DEBUG('item_id:%d, not a arm!', $item_id);
			return array();
		}
		
		//得到用户对象
		$user = EnUser::getUserObj();
		
		$gildLevel = $item->getGildLevel();
		
		if ( $gildLevel >= 20 )
		{
			Logger::DEBUG('max gild level!');
			return $ret;
		}
		
		$bag = BagManager::getInstance()->getBag();
		$info = $item->gildReq();
		
		if ($bag->deleteItembyTemplateID(120015, $info[$gildLevel+1][0]) == FALSE || $user->subBelly($info[$gildLevel+1][1]) == FALSE)
		{
			return $ret;
		}
		$item->setGildLevel($gildLevel+1);
		$user->update();
		$ret = array('gild_success'=>true, 'baginfo'=>$bag->update());
		return $ret;
	}
	
	public function ungild($item_id)
	{
		$ret = array('ungild_success'=>false);
		// $this->forgeInfo();	
		$bag = BagManager::getInstance()->getBag();
		$item = ItemManager::getInstance()->getItem($item_id);
		$gildLevel = $item->getGildLevel();
		$info = $item->gildReq();
		$retNum = 0;
		for ($i=1; $i<=$gildLevel; $i++)
		{
			$retNum+=$info[$i][0];
		}
		$user = EnUser::getUserObj();
		if ($bag->addItembyTemplateID(120015, $retNum) == FALSE || $user->subGold($info[0]) == FALSE)
		{
			return $ret;
		}
		$user->update();
		$item->setGildLevel(0);
		$ret = array('ungild_success'=>true, 'baginfo'=>$bag->update());
		return $ret;
	}
	
	public function daimonAppleFuse($item_id, $fuse_item_id)
	{		
		$item_id = intval($item_id);
		$fuse_item_id = intval($fuse_item_id);

		$item = ItemManager::getInstance()->getItem($item_id);
		$fuse_item = ItemManager::getInstance()->getItem($fuse_item_id);

		//检测物品是否存在
		if ( $item == NULL || $item->getItemType() != ItemDef::ITEM_DAIMONAPPLE )
		{
			Logger::DEBUG('item_id:%d not exist or not a dmapple!', $item_id);
			return FALSE;
		}

		if ( $fuse_item == NULL || $fuse_item->getItemType() != ItemDef::ITEM_DAIMONAPPLE )
		{
			Logger::DEBUG('item_id:%d not exist or not a dmapple!', $fuse_item_id);
			return FALSE;
		}

		if ( $item_id == $fuse_item_id )
		{
			Logger::DEBUG('target item is source item!');
			return FALSE;
		}

		//检测物品是否属于当前用户
		$bag = BagManager::getInstance()->getBag();
		//不检测目标是否属于当前用户,检查过于复杂,并且如果客户端造假,则对自己不利

		if ( $bag->getGridID($fuse_item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			Logger::DEBUG('item_id:%d not belong to you!', $fuse_item_id);
			return FALSE;
		}

		//检测目标是否已经达到最高等级
		if ( $item->getMaxLevel() <= $item->getLevel() )
		{
			Logger::DEBUG('item_id:%d is max level!', $item_id);
			return FALSE;
		}

		$oldlevel=$item->getLevel();
		
		//检测目标是否品质小于被吃掉
		if ( $item->getItemQuality() < $fuse_item->getItemQuality() )
		{
			Logger::WARNING('item_id:%d, quality:%d < fuse item_id:%d, quality:%d',
				$item_id, $item->getItemQuality(), $fuse_item_id, $fuse_item->getItemQuality());
			return FALSE;
		}
		
		$exp = $fuse_item->getFuseExp();
		$bag->deleteItem($fuse_item_id);
		
		$uid = RPCContext::getInstance()->getUid();		
		$maxExp = $item->getMaxLevelExp();
		$allExp = $item->getExp() + $exp;
		$overExp = $allExp - $maxExp;
		if($overExp > 0)
		{
			$item->setExp($maxExp);
			$cur_exp = AppleFactoryDao::get($uid, array('apple_experience'));
			AppleFactoryDao::update($uid, array('apple_experience'=>$cur_exp+$overExp));
		} else $item->setExp($allExp);
				
		//物品如果不在背包里则战斗优化
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID && $oldlevel < $item->getLevel())
		{
			EnUser::modifyBattleInfo();
		}

		return array('fuse_success'=>'ok', 'baginfo'=>$bag->update());
	}	

	public function daimonAppleFuseAll($item_id)
	{		
		$item_id = intval($item_id);
		$item = ItemManager::getInstance()->getItem($item_id);
		
		// 检测物品是否存在
		if ( $item == NULL || $item->getItemType() != ItemDef::ITEM_DAIMONAPPLE )
		{
			Logger::DEBUG('item_id:%d not exist or not a daimonapple!', $item_id);
			return FALSE;
		}
		// 如果目标表示已经达到最大等级
		if ( $item->getLevel() >= $item->getMaxLevel() )
		{
			Logger::DEBUG('item_id:%d is max level', $item_id);
			return FALSE;
		}

		$oldlevel= $item->getLevel();
		$bag = BagManager::getInstance()->getBag();
		$dmapple_item_ids = $bag->getItemIdsByItemType(ItemDef::ITEM_DAIMONAPPLE);
		$exp = 0;
		foreach ( $dmapple_item_ids as $fuse_item_id )
		{

			// 如果被融合和目标一样,则跳过
			if ( $fuse_item_id == $item_id )
			{
				continue;
			}
			
			$fuse_item = ItemManager::getInstance()->getItem($fuse_item_id);

			if ( $fuse_item === NULL )
			{
				continue;
			}

			// 如果被融合不是最低级的,则跳过
			if ( $fuse_item->getLevel() > ItemDef::ITEM_DAIMONAPPLE_MIN_LEVEL )
			{
				continue;
			}

			// 如果目标的品质小于被吃掉,则跳过
			if ( $item->getItemQuality() <= $fuse_item->getItemQuality() )
			{
				continue;
			}

			// 如果被融合品质大于ItemDef::ITEM_QUALITY_PURPLE
			if ( $fuse_item->getItemQuality() >= ItemDef::ITEM_QUALITY_PURPLE )
			{
				continue;
			}

			$exp += $fuse_item->getFuseExp();
			$bag->deleteItem($fuse_item_id);
		}
		
		$uid = RPCContext::getInstance()->getUid();
		$maxExp = $item->getMaxLevelExp();
		$allExp = $item->getExp() + $exp;
		$overExp = $allExp - $maxExp;
		if($overExp > 0)
		{
			$item->setExp($maxExp);
			$cur_exp = AppleFactoryDao::get($uid, array('apple_experience'));
			AppleFactoryDao::update($uid, array('apple_experience'=>$cur_exp+$overExp));
		} else $item->setExp($allExp);
				
		// 物品如果不在背包里则战斗优化
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID  && $oldlevel < $item->getLevel() )
		{
			EnUser::modifyBattleInfo();
		}

		return array ('fuse_success' => TRUE, 'fuse_item' => $item->getExp(), 'bag_modify' => $bag->update());
	}
	
	public function daimonAppleLevelUpByExp($item_id)
	{		
		$item_id = intval($item_id);
		$item = ItemManager::getInstance()->getItem($item_id);
		
		//检测物品是否存在
		if ( $item == NULL || $item->getItemType() != ItemDef::ITEM_DAIMONAPPLE )
		{
			Logger::warning('dmAppleLevelUpByExp: item_id:%d not exist or not a dmApple!', $item_id);
			return FALSE;
		}
		
		//如果已经达到最大等级
		if ( $item->getLevel() >= $item->getMaxLevel() )
		{
			Logger::warning('dmAppleLevelUpByExp:item_id:%d is max level', $item_id);
			return FALSE;
		}
		
		$uid = RPCContext::getInstance()->getUid();
		$exp = AppleFactoryDao::get($uid, array('apple_experience'));
		$exp = $exp['apple_experience'];
		
		$maxExp = $item->getMaxLevelExp();
		$oldExp = $item->getExp();
		$allExp = $oldExp + $exp;
		$overExp = $allExp - $maxExp;
		if($overExp > 0)
		{
			$item->setExp($maxExp);
			AppleFactoryDao::update($uid, array('apple_experience'=>$overExp));
			$ret['cost_exp'] = $maxExp - $oldExp;
		} else 
		{
			$item->setExp($allExp);
			AppleFactoryDao::update($uid, array('apple_experience'=>0));
			$ret['cost_exp'] = $exp;
		}
		
		ItemManager::getInstance()->update();
		
		// 物品如果不在背包里则战斗优化
		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			EnUser::modifyBattleInfo();
		}
		$ret['levelup_success'] = 'ok';
		$ret['baginfo'] = $bag->update();
		
		return $ret;
	}
	
	public function demonEvoPanel()
	{
		$uid = RPCContext::getInstance()->getUid();
		$demonKernel = AppleFactoryDao::get($uid, array('demon_kernel'));
		return $demonKernel['demon_kernel'];		
	}
	
	public function demonEvoUp($item_id)
	{
		$ret = array('gild_success'=>false);

		// 检查装备是否属于该用户
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return $ret;
		}

		$item = ItemManager::getInstance()->getItem($item_id);
		// 检查强化的物品是否为装备
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_DAIMONAPPLE )
		{
			Logger::DEBUG('item_id:%d, not a arm!', $item_id);
			return $ret;
		}
		
		$gildLevel = $item->getGildLevel();
		
		if ( $gildLevel >= 20 )
		{
			Logger::DEBUG('max gild level!');
			return $ret;
		}
		
		$bag = BagManager::getInstance()->getBag();
		$itemTempLateId = $item->getItemTemplateID();
		$fineCost = btstore_get()->ITEMS[$itemTempLateId][ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_FINE_COST];
		$uid = RPCContext::getInstance()->getUid();
		$info = AppleFactoryDao::get($uid, array('demon_kernel'));
		$curKernel = $info['demon_kernel'];		
		AppleFactoryLogic::updateExpKernel($uid, NULL, $curKernel-$fineCost[$gildLevel]);
		$item->setGildLevel($gildLevel+1);		
		ItemManager::getInstance()->update();
		$ret = array('gild_success'=>true, 'demon_kernel'=>$curKernel-$fineCost[$gildLevel]);
		return $ret;
	}	
	
	public function elementFuseExp($item_id)
	{
		$return = array('levelup_success' => FALSE,'cost_exp'=>0);
		$item_id = intval($item_id);
		$item = ItemManager::getInstance()->getItem($item_id);
		
		//检测物品是否存在
		if ( $item == NULL || $item->getItemType() != ItemDef::ITEM_ELEMENT )
		{
			Logger::warning('elementFuseExp: item_id:%d not exist or not a element!', $item_id);
			return $return;
		}
		
		//如果已经达到最大等级
		if ( $item->getLevel() >= $item->getMaxLevel() )
		{
			Logger::warning('elementFuseExp:item_id:%d is max level', $item_id);
			return $return;
		}
		
		//获得升到下一级需要多少经验
		$costexp = $item->getNextLevelExp();
		if ($costexp < 0 )
		{
			return $return;
		}
		
		$uid = RPCContext::getInstance()->getUid();
		$info = ElementSysDao::get($uid, array('element_exp'));
		//如果当前经验不够，则直接灌
		$curexp =$info['element_exp'];
		if ($info['element_exp'] < $costexp )
		{
			$costexp = $info['element_exp'];
		}
		
		//扣除经验
		$info['element_exp'] -= $costexp;
		ElementSysDao::update($uid, $info);
		
		//给增加经验
		$item->addExp($costexp);
		ItemManager::getInstance()->update();
		
		//物品如果不在背包里则战斗优化
		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			EnUser::modifyBattleInfo();
		}
		
		//返回给前段
		$return['levelup_success']=TRUE;
		$return['cost_exp']=$costexp;
		return $return;
	}
	
	public function elementFuse($item_id, $fuse_item_id)
	{
		$item_id = intval($item_id);
		$fuse_item_id = intval($fuse_item_id);

		$item = ItemManager::getInstance()->getItem($item_id);
		$fuse_item = ItemManager::getInstance()->getItem($fuse_item_id);

		//检测物品是否存在
		if ( $item == NULL || $item->getItemType() != ItemDef::ITEM_ELEMENT )
		{
			Logger::DEBUG('item_id:%d not exist or not a element!', $item_id);
			return FALSE;
		}

		if ( $fuse_item == NULL || $fuse_item->getItemType() != ItemDef::ITEM_ELEMENT )
		{
			Logger::DEBUG('item_id:%d not exist or not a element!', $fuse_item_id);
			return FALSE;
		}

		if ( $item_id == $fuse_item_id )
		{
			Logger::DEBUG('target item is source item!');
			return FALSE;
		}

		//检测物品是否属于当前用户
		$bag = BagManager::getInstance()->getBag();
		//不检测目标宝石是否属于当前用户,检查过于复杂,并且如果客户端造假,则对自己不利

		if ( $bag->getGridID($fuse_item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			Logger::DEBUG('item_id:%d not belong to you!', $fuse_item_id);
			return FALSE;
		}

		//检测目标是否已经达到最高等级
		if ( $item->getMaxLevel() <= $item->getLevel() )
		{
			Logger::DEBUG('item_id:%d is max level!', $item_id);
			return FALSE;
		}

		$oldlevel=$item->getLevel();
		
		$item->addExp($fuse_item->getFuseExp());
		$bag->deleteItem($fuse_item_id);

		$bag->update();
		
		//物品如果不在背包里则战斗优化
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID && $oldlevel < $item->getLevel())
		{
			EnUser::modifyBattleInfo();
		}

		return TRUE;
	}
	
	public function elementFuseAll($item_id)
	{
		$return = array('fuse_success' => FALSE);
		$item_id = intval($item_id);
		$item = ItemManager::getInstance()->getItem($item_id);

		//检测物品是否存在
		if ( $item == NULL || $item->getItemType() != ItemDef::ITEM_ELEMENT )
		{
			Logger::DEBUG('item_id:%d not exist or not a element!', $item_id);
			return $return;
		}

		$oldlevel= $item->getLevel();
		$bag = BagManager::getInstance()->getBag();
		$element_item_ids = $bag->getItemIdsByItemType(ItemDef::ITEM_ELEMENT);
		foreach ( $element_item_ids as $fuse_item_id )
		{
			//如果被融合和目标一样,则跳过
			if ( $fuse_item_id == $item_id )
			{
				continue;
			}
			//如果目标表示已经达到最大等级
			if ( $item->getLevel() >= $item->getMaxLevel() )
			{
				Logger::DEBUG('item_id:%d is max level', $item_id);
				break;
			}
			$fuse_item = ItemManager::getInstance()->getItem($fuse_item_id);

			if ( $fuse_item === NULL )
			{
				continue;
			}

			//如果被融合不是最低级的,则跳过
			if ( $fuse_item->getLevel() > ItemDef::ITEM_ELEMENT_MIN_LEVEL )
			{
				continue;
			}

			$fuse_item_template_id = $fuse_item -> getItemTemplateID();
			if ( $fuse_item_template_id != 91011 )
			{
				continue;
			}			
			
			$item->addExp($fuse_item->getFuseExp());
			$bag->deleteItem($fuse_item_id);
		}

		$bag_modify = $bag->update();
		$fuse_item_text = $item->getItemText();
		$fuse_item_exp = $fuse_item_text[ItemDef::ITEM_ATTR_NAME_EXP];
		$return = array (
			'fuse_success' => TRUE,
			'fuse_item' => $fuse_item_exp,
			'bag_modify' => $bag_modify,
		);
		
		//物品如果不在背包里则战斗优化
		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID  && $oldlevel < $item->getLevel() )
		{
			EnUser::modifyBattleInfo();
		}

		return $return;
	}
		
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */