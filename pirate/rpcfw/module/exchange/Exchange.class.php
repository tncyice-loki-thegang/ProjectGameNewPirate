<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Exchange.class.php 40302 2013-03-08 05:16:54Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/exchange/Exchange.class.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-03-08 13:16:54 +0800 (五, 2013-03-08) $
 * @version $Revision: 40302 $
 * @brief
 *
 **/

class Exchange implements IExchange
{
	public function Exchange()
	{
		$uid = RPCContext::getInstance()->getUid();
		if ( empty($uid) )
		{
			Logger::FATAL('invalid uid:%d', $uid);
			throw new Exception('fake');
		}
	}

	/* (non-PHPdoc)
	 * @see IExchange::getExchangeInfo()
	 */
	public function getExchangeInfo() {

		$uid = RPCContext::getInstance()->getUid();

		$exchange_info = $this->exchangeInfo();

		$return = array();
		$exchange_item = ItemManager::getInstance()->getItem($exchange_info[ExchangeDef::ITEM_ID]);
		if ( $exchange_item === NULL )
		{
			$return[ExchangeDef::EXCHANGE_ITEM] = array();
		}
		else
		{
			$return[ExchangeDef::EXCHANGE_ITEM] = $exchange_item->itemInfo();
		}
		$return[ExchangeDef::ITEMS] = $exchange_info[ExchangeDef::ITEMS];
		
		return $return;
	}

	/* (non-PHPdoc)
	 * @see IExchange::exchangeItem()
	 */
	public function exchangeItem($item_id) {
		$item_id = intval($item_id);

		$return = array(ExchangeDef::EXCHANGE_SUCCESS => FALSE);

		//检测参数是否合法
		if ( $item_id <= ItemDef::ITEM_ID_NO_ITEM )
		{
			Logger::WARNING('invalid item_id:%d', $item_id);
			return $return;
		}

		$item = ItemManager::getInstance()->getItem($item_id);

		//检测物品类型
		if ( $item === NULL )
		{
			Logger::WARNING('invalid item_id:%d', $item_id);
			return $return;
		}

		$item_type = $item->getItemType();
		if ( ($item_type != ItemDef::ITEM_ARM && $item_type != ItemDef::ITEM_FRAGMENT)
			|| $item->getArmExchangeId() == 0 )
		{
            Logger::WARNING('invalid item_id:%d, item_type:%d', $item_id, $item_type);
            return $return;
		}

		$exchange_info = $this->exchangeInfo();
		if ( !empty($exchange_info[ExchangeDef::ITEMS]) )
		{
			Logger::WARNING('has items not receieve!');
			return $return;
		}

		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			Logger::WARNING('item_id:%d not in bag!', $item_id);
			return $return;
		}

		if ( $item_type == ItemDef::ITEM_ARM && $item->noEnchase() == FALSE )
		{
			Logger::WARNING('item_id:%d should not be enchanse item!', $item_id);
			return $return;
		}

		if ( $bag->removeItem($item_id) == FALSE )
		{
			Logger::FATAL('remove item failed!');
			return $return;
		}

		$exchange_id = $item->getArmExchangeId();

		$user = EnUser::getUserObj();

		$experience = ExchangeUtil::getExchangeReqExperience($exchange_id) * $item->getItemNum();

		if ( $user->subExperience($experience) == FALSE )
		{
			Logger::WARNING('no enough experience!');
			return $return;
		}

		$sell_info = $item->sellInfo();
		$sell_type = $sell_info['sell_type'];
		$sell_price = $sell_info['sell_price'] * $item->getItemNum();

		switch ( $sell_type )
		{
			case TradeDef::TRADE_SELL_TYPE_BELLY:
				if ( $user->addBelly($sell_price) == FALSE )
				{
					Logger::FATAL('add belly failed!');
					return $return;
				}
				break;
			//TODO 可能有其他的出售所得类型
			default:
				Logger::FATAL('invalid sell type:%d', $sell_type);
				throw new Exception('invalid sell type:%d!', $sell_type);
				break;
		}

		$exchange_drop_list = ExchangeUtil::getExchangeDropList($exchange_id);

		shuffle($exchange_drop_list);

		$exchange_args = ExchangeUtil::getExchangeArgs($exchange_id);

		$values = ExchangeUtil::getExchangeValueList($exchange_args);

		$exchange_value = ExchangeUtil::getExchangeValue($exchange_id) * $item->getItemNum();

		$items = array();
		foreach ( $exchange_drop_list as $key => $drop_template_id )
		{
			$drop = Drop::dropItem($drop_template_id);
			if ( count($drop) != 1 )
			{
				Logger::FATAL('invalid good will drop id:%d', $drop_template_id);
				throw new Exception('config');
			}
			if ( $drop[0][DropDef::DROP_ITEM_NUM] != 1 )
			{
				Logger::FATAL('invalid good will drop id:%d, must drop num = 1', $drop_template_id);
				throw new Exception('config');
			}
			$item_template_id = $drop[0][DropDef::DROP_ITEM_TEMPLATE_ID];
			$number = intval($exchange_value * $values[$key] / ExchangeDef::EXCHANGE_MODULUS /
				 ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_GOOD_WILL));
			//如果产生的数量是0,则不添加到列表
			if ( $number != 0 )
			{
				$items[$item_template_id] = $number;
			}
		}

		if ( empty($items) )
		{
			Logger::FATAL('invalid config! check exchange drop list, exchange id:%d', $exchange_id);
			throw new Exception('config');
		}

		$this->setExchangeInfo($item_id, $items);

		$bag->update();

		$user->update();

		TaskNotify::operate(TaskOperateType::GIFT_EXCHANGE);

		$return = array (
			ExchangeDef::EXCHANGE_SUCCESS => true,
			ExchangeDef::ITEMS => $items,
		);
		
		return $return;

	}

	/* (non-PHPdoc)
	 * @see IExchange::recieveItem()
	 */
	public function recieveItem() {

		$exchange_info = $this->exchangeInfo();

		if ( empty($exchange_info[ExchangeDef::ITEMS]) )
		{
			Logger::WARNING('no items receieve!');
			return array();
		}

		$bag = BagManager::getInstance()->getBag();

		if ( $bag->addItemsByTemplateID($exchange_info[ExchangeDef::ITEMS]) == FALSE )
		{
			Logger::WARNING('bag full!');
			return array();
		}

		$bag_modify = $bag->update();

		$this->setExchangeInfo(ItemDef::ITEM_ID_NO_ITEM, array());

		return $bag_modify;
	}

	/* (non-PHPdoc)
	 * @see IExchange::exchangeGemItem()
	 */
	public function exchangeGemItem($src_item_ids, $target_item_template_id)
	{
		//格式化输入
		$target_item_template_id = intval($target_item_template_id);
		if ( !is_array( $src_item_ids ) )
		{
			Logger::WARNING('invalid args! src item id is not array!');
			throw new Exception('fake');
		}

		$return = array ( ExchangeDef::EXCHANGE_SUCCESS => FALSE );

		$bag = BagManager::getInstance()->getBag();
		$exchange_info = ExchangeUtil::getGemExchange($target_item_template_id);
		$fuse_exp = 0;
		$item_num = 0;
		foreach ( $src_item_ids as $src_item_id )
		{
			if ( $bag->getGridID($src_item_id) == BagDef::BAG_INVALID_BAG_ID )
			{
				Logger::DEBUG('item:%d not in bag!', $src_item_id);
				return $return;
			}

			$src_item = ItemManager::getInstance()->getItem($src_item_id);
			if ( $src_item->getItemType() != ItemDef::ITEM_GEM )
			{
				Logger::DEBUG('item:%d is not a gem!', $src_item_id);
				return $return;
			}

			// if ( $src_item->getItemQuality() != $exchange_info[ExchangeDef::EXCHANGE_REQ_GEM_ITEM_QUALITY] )
			// {
				// Logger::DEBUG('item:%d is not a required gem! required item quality:%d',
					 // $src_item_id, $exchange_info[ExchangeDef::EXCHANGE_REQ_GEM_ITEM_QUALITY]);
				// return $return;
			// }

			$item_num++;
			$src_item_text = $src_item->getItemText();
			$fuse_exp += $src_item_text[ItemDef::ITEM_ATTR_NAME_EXP];

			$bag->deleteItem($src_item_id);
		}

		if ( $item_num != $exchange_info[ExchangeDef::EXCHANGE_REQ_GEM_ITEM_NUM] )
		{
			Logger::DEBUG('not enough gem item!');
			return $return;
		}
		
		$uid = RPCContext::getInstance()->getUid();
		$info = GemMatrixLogic::getInfo($uid);		
		if ( $info['score'] < $exchange_info[ExchangeDef::EXCHANGE_REQ_GEM_ITEM_NEED_POINT] )
		{
			Logger::DEBUG('not enough gem score!');
			return $return;
		} else $info['score'] -= $exchange_info[ExchangeDef::EXCHANGE_REQ_GEM_ITEM_NEED_POINT];
		
		if ( $info['elite'] < $exchange_info[ExchangeDef::EXCHANGE_REQ_GEM_ITEM_NEED_ESSENCE] )
		{
			Logger::DEBUG('not enough gem elite!');
			return $return;
		} else $info['elite'] -= $exchange_info[ExchangeDef::EXCHANGE_REQ_GEM_ITEM_NEED_ESSENCE];
		
		GemMatrixDao::update($uid, $info);
		
		$target_item_ids = ItemManager::getInstance()->addItem($target_item_template_id);
		if ( count($target_item_ids) != 1 )
		{
			Logger::FATAL('exchange gem item insert item failed!');
			return $return;
		}
		$target_item_id = $target_item_ids[0];

		$target_item = ItemManager::getInstance()->getItem($target_item_id);

		$target_item->addExp($fuse_exp);

		if ( $bag->addItem($target_item_id) == FALSE )
		{
			Logger::FATAL('exchange gem item add item into bag failed!');
			return $return;
		}

		$bag_modify = $bag->update();

		return array(
			ExchangeDef::EXCHANGE_SUCCESS => TRUE,
			'bag_modify' => $bag_modify,
		);
	}

	/* (non-PHPdoc)
	 * @see IExchange::exchangeArmItem()
	 */
	public function exchangeArmItem($exchange_id, $src_item_id)
	{
		//格式化数据
		$exchange_id = intval($exchange_id);
		$src_item_id = intval($src_item_id);

		$return = array(ExchangeDef::EXCHANGE_SUCCESS => FALSE);

		//检查物品是否存在
		$src_item = ItemManager::getInstance()->getItem($src_item_id);
		if ( $src_item === NULL )
		{
			Logger::DEBUG("item_id:%d not exist!", $src_item_id);
			throw new Exception('fake');
		}

		$bag = BagManager::getInstance()->getBag();
		$exchange_info = ExchangeUtil::getArmExchange($exchange_id);

		if ( $bag->getGridID($src_item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			Logger::DEBUG("item_id:%d not in bag!", $src_item_id);
			throw new Exception('fake');
		}

		if ( $exchange_info[ExchangeDef::EXCHANGE_ITEM_ID] != $src_item->getItemTemplateID() )
		{
			Logger::DEBUG("invalid item id:%d!", $src_item_id);
			throw new Exception('fake');
		}

		$user = EnUser::getUserObj();

		//扣除belly
		if ( !empty($exchange_info[ExchangeDef::EXCHANGE_REQ_BELLY]) )
		{
			if ( $user->subBelly($exchange_info[ExchangeDef::EXCHANGE_REQ_BELLY]) == FALSE )
			{
				Logger::DEBUG("no enough belly!");
				return $return;
			}
		}

		//扣除阅历
		if ( !empty($exchange_info[ExchangeDef::EXCHANGE_REQ_EXPERIENCE]) )
		{
			if ( $user->subExperience($exchange_info[ExchangeDef::EXCHANGE_REQ_EXPERIENCE]) == FALSE )
			{
				Logger::DEBUG("no enough experience!");
				return $return;
			}
		}

		//扣除紫魂
		$soul = SoulObj::getInstance();
		if ( !empty($exchange_info[ExchangeDef::EXCHANGE_REQ_PURPLE_SOUL]) )
		{
			$soul->subPurple($exchange_info[ExchangeDef::EXCHANGE_REQ_PURPLE_SOUL]);
		}

		//扣除物品
		if ( !empty($exchange_info[ExchangeDef::EXCHANGE_REQ_ITEMS]) )
		{
			if ( $bag->deleteItemsByTemplateID($exchange_info[ExchangeDef::EXCHANGE_REQ_ITEMS]) == FALSE )
			{
				Logger::DEBUG("no enough items!");
				return $return;
			}
		}

		//新增加物品
		$target_item_ids = ItemManager::getInstance()->addItem($exchange_info[ExchangeDef::EXCHANGE_TARGET_ITEM_ID]);
		$target_item_id = $target_item_ids[0];
		$target_item = ItemManager::getInstance()->getItem($target_item_id);
		if ( $target_item->getItemType() !== ItemDef::ITEM_ARM )
		{
			Logger::DEBUG("invalid exchange target item template id:%d",
				$exchange_info[ExchangeDef::EXCHANGE_TARGET_ITEM_ID] );
			throw new Exception('config');
		}

		//得到物品强化信息
		$reinforceInfo = $src_item->reinforceReq();
		$reinforceLevel = $src_item->getReinforceLevel();
		//计算所应该加的Belly数量
		$belly = 0;
		for ( $i = $reinforceLevel; $i > ItemDef::ITEM_REINFORCE_LEVEL_DEFAULT; $i-- )
		{
			$belly += $reinforceInfo[$i][ItemDef::REINFORCE_FEE_BELLY];
		}

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
		
		$gildLevel = $src_item->getGildLevel();
		if ($gildLevel>0)
		{
			$gildInfo =  $src_item->gildReq();		
			
			$itemNum = $belly = 0;
			for ( $i = $gildLevel; $i > ItemDef::ITEM_GILD_LEVEL_DEFAULT; $i-- )
			{
				$itemNum += $gildInfo[$i][0];
				$belly += $gildInfo[$i][1];			
			}
			
			$target_gildInfo = $target_item->gildReq();
			$target_gildLevel = ItemDef::ITEM_GILD_LEVEL_DEFAULT;

			for ( $i = 0; $i < ItemAttr::getItemAttr($src_item->getItemTemplateID(), ItemDef::ITEM_ATTR_NAME_MAX_GILDING_LV); $i++ )
			{
				$itemNum -= $target_gildInfo[$target_gildLevel+1][0];
				$belly -= $target_gildInfo[$target_gildLevel+1][1];
				//到达最大等级/超过用户等级/belly不足
				if ( $target_gildLevel >= ItemAttr::getItemAttr($src_item->getItemTemplateID(), ItemDef::ITEM_ATTR_NAME_MAX_GILDING_LV) || $belly < 0 )
				{
					break;
				}
				$target_gildLevel++;
			}
			$target_item->setGildLevel($target_gildLevel);
		}

		//宝石传承
		$gem_item_ids = $src_item->getGemItems();
		if ( $target_item->enchaseGems($gem_item_ids) == FALSE )
		{
			Logger::FATAL("can not enchase gems! invalid exchange config!id:%d", $exchange_id);
			throw new Exception('fake');
		}

		//处理潜能
		$src_potentiality_id = $src_item->getRandPotentialityId();
		$target_potentiality_id = $target_item->getRandPotentialityId();
		$src_item_text = $src_item->getItemText();
		$src_potentiality = $src_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY];

		$target_potentiality = Potentiality::transferPotentiality($src_potentiality_id, $target_potentiality_id,
			 $src_potentiality, ForgeConfig::$VALID_FIXED_REFRESH_TYPES);

		//更新目标物品的潜能
		$target_item_text = $target_item->getItemText();
		$target_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY] = $target_potentiality;
		unset($target_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_RAND_REFRESH]);
		unset($target_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH]);
		$target_item->setItemText($target_item_text);

		//删除源物品(由于需要发送消息，所以并不真正删除物品)
		$bag->removeItem($src_item_id);

		//添加新的物品
		if ( $bag->addItem($target_item_id) == FALSE )
		{
			Logger::DEBUG("bag full!");
			return $return;
		}

		//保存数据
		$soul->save();
		$user->update();
		$bag_modify = $bag->update();

		//发送系统公告
		ChatTemplate::sendUpgradeItem ($user->getTemplateUserInfo(), $src_item_id, $target_item_id);

		return array(
			ExchangeDef::EXCHANGE_SUCCESS => TRUE,
			'target_item_id' => $target_item_id,
			'bag_modify' => $bag_modify,
		);

	}

	/* (non-PHPdoc)
	 * @see IExchange::exchangeDirectItem()
	 */
	public function exchangeDirectItem($exchange_id, $number)
	{
		//格式化数据
		$exchange_id = intval($exchange_id);
		$number = intval($number);

		if ( $number <= 0 )
		{
			Logger::WARNING("invalid number!:%d", $number);
			throw new Exception('fake');
		}

		$return = array(ExchangeDef::EXCHANGE_SUCCESS => FALSE);

		$bag = BagManager::getInstance()->getBag();
		$exchange_info = ExchangeUtil::getDirectExchange($exchange_id);

		if ( $bag->deleteItembyTemplateID($exchange_info[ExchangeDef::EXCHANGE_ITEM_ID]
			, $exchange_info[ExchangeDef::EXCHANGE_ITEM_NUM] * $number ) == FALSE )
		{
			Logger::DEBUG("no enough item!");
			return $return;
		}

		if ( $bag->addItemByTemplateID($exchange_info[ExchangeDef::EXCHANGE_TARGET_ITEM_ID],
			 $exchange_info[ExchangeDef::EXCHANGE_TARGET_ITEM_NUM] * $number ) == FALSE )
		{
			Logger::DEBUG("bag full!");
			return $return;
		}

		$bag_modify = $bag->update();

		return array(
			ExchangeDef::EXCHANGE_SUCCESS => TRUE,
			'bag_modify' => $bag_modify,
		);
	}

	public static function exchangeInfo()
	{
		$uid = RPCContext::getInstance()->getUid();
		$exchange_info = RPCContext::getInstance()->getSession(ExchangeDef::EXCHANGE_SESSION);
		if ( empty($exchange_info) )
		{
			$exchange_info = ExchangeDAO::getExchange($uid);
			if ( empty($exchange_info) )
			{
				$exchange_info = array (
					ExchangeDef::ITEM_ID => 0,
					ExchangeDef::ITEMS => array(),
				);
				ExchangeDAO::initExchange($uid);
			}
			else
			{
				$exchange_info = $exchange_info[0];
			}
			RPCContext::getInstance()->setSession(ExchangeDef::EXCHANGE_SESSION, $exchange_info);
		}
		return $exchange_info;
	}

	public static function setExchangeInfo($item_id, $items)
	{
		$uid = RPCContext::getInstance()->getUid();
		$exchange_info = array (
			ExchangeDef::ITEM_ID => $item_id,
			ExchangeDef::ITEMS => $items,
		);
		ExchangeDAO::setExchange($uid, $item_id, $items);
		RPCContext::getInstance()->setSession(ExchangeDef::EXCHANGE_SESSION, $exchange_info);
	}
	
	/* (non-PHPdoc)
	 * @see IExchange::exchangeJewelryItem()
	 */
	public function exchangeJewelryItem($item_id)
	{
		$item_id = intval($item_id);

		$return = array(ExchangeDef::EXCHANGE_SUCCESS => FALSE);

		//检测参数是否合法
		if ( $item_id <= ItemDef::ITEM_ID_NO_ITEM )
		{
			Logger::WARNING('invalid item_id:%d', $item_id);
			return $return;
		}

		$item = ItemManager::getInstance()->getItem($item_id);

		//检测物品类型
		if ( $item === NULL )
		{
			Logger::WARNING('invalid item_id:%d', $item_id);
			return $return;
		}

		$item_type = $item->getItemType();
		if ($item_type != ItemDef::ITEM_JEWELRY)
		{
            Logger::WARNING('invalid item_id:%d, item_type:%d', $item_id, $item_type);
            return $return;
		}

		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			Logger::WARNING('item_id:%d not in bag!', $item_id);
			return $return;
		}

		if ( $item_type == ItemDef::ITEM_ARM && $item->noEnchase() == FALSE )
		{
			Logger::WARNING('item_id:%d should not be enchanse item!', $item_id);
			return $return;
		}

		if ( $bag->removeItem($item_id) == FALSE )
		{
			Logger::FATAL('remove item failed!');
			return $return;
		}
		// 物品模板id
		$itemTempLateId = $item->getItemTemplateID();
		$itemInfo = btstore_get()->ITEMS[$itemTempLateId];
		// 该宝物可不可以分解
		if(empty(btstore_get()->ITEMS[$itemTempLateId]['canSplit']))
		{
			return $return;
		}
		// 宝物等级
		$level = $item->getReinforceLevel();
		
		// 分解强化价值
		$exValue = 0;
		$beforeValue = 0;
		// 宝物强化信息
		$strengthProperty = $itemInfo['strengthProperty'];
		if(empty($strengthProperty))
		{
			return $return;
		}
		
		for ($i = 0; $i < count($strengthProperty); $i++)
		{
			if($level >= $strengthProperty[$i]['limit'])
			{
				$strengthInfo = $strengthProperty[$i];
				// 强化等级上限 * 消耗物品个数 * 10000 / 强化概率
				$exValue += ($strengthProperty[$i]['limit'] - $beforeValue) * 
							$strengthProperty[$i]['costnum'] * 10000 / 
							$strengthProperty[$i]['rate'];
				$beforeValue = $strengthProperty[$i]['limit'];
				Logger::DEBUG('strength value1 is %s.', $exValue);
			}
			else if($level < $strengthProperty[$i]['limit'])
			{
				$strengthInfo = $strengthProperty[$i];
				// 强化等级上限 * 消耗物品个数 * 10000 / 强化概率
				$exValue += ($level - $beforeValue) * 
							$strengthProperty[$i]['costnum'] * 10000 / 
							$strengthProperty[$i]['rate'];
				Logger::DEBUG('strength value2 is %s.', $exValue);
				break;
			}
		}
		Logger::DEBUG('strength value is %s.', $exValue);
		
		// 宝物分解出元素石数量=int(宝物分解强化价值*宝物强化价值比率/10000)向下取整
		$elementsStone = intval(($exValue + $itemInfo['base_decom_val']) * $itemInfo['reinforce_val_rate'] / 10000);
		Logger::DEBUG('elementsStone is %s.', $elementsStone);
		
		// 当前装备的开启的层ID
		$openLayersIds = $item->getCurOpenLayerIds();
		Logger::DEBUG('openLayersIds is %s.', $openLayersIds);
		// 分解封印价值
		$sealValue = 0;
		foreach ($openLayersIds as $openLayersId)
		{
			if(!empty(btstore_get()->JEWELRY_SEAL[$openLayersId]))
			{
				$sealValue += btstore_get()->JEWELRY_SEAL[$openLayersId]['decom_val'];
			}
		}
		// 宝物分解出能量石数量=int(宝物分解封印价值*宝物封印价值比/10000)向下取整
		$energyStone = intval(($sealValue + $itemInfo['base_seal_val']) * $itemInfo['seal_val_rate'] / 10000);
		Logger::DEBUG('energyStone is %s.', $elementsStone);

		// 扣除阅历
		$user = EnUser::getUserObj();
		if ( !empty($itemInfo['yueli']) )
		{
			if ( $user->subExperience($itemInfo['yueli']) == FALSE )
			{
				Logger::DEBUG("no enough experience!");
				return $return;
			}
		}
		// 增加元素石
		if(!empty($elementsStone))
		{
			Jewelry::addEnergyElement($user->getUid(),0,$elementsStone);
		}
		// 增加能量石
		if(!empty($energyStone))
		{
			Jewelry::addEnergyElement($user->getUid(),$energyStone,0);
		}
		$user->update();
		$bag->update();

		$return = array (
			ExchangeDef::EXCHANGE_SUCCESS => true,
			'items' => array('elementsStone' => $elementsStone,
							 'energyStone' => $energyStone)
		);
		return $return;
	}
	
	public function exchangeHorseDecorationItem($item_id)
	{
		$return = array(ExchangeDef::EXCHANGE_SUCCESS => FALSE);
		
		//检测参数是否合法
		if ( $item_id <= ItemDef::ITEM_ID_NO_ITEM )
		{
			Logger::WARNING('invalid item_id:%d', $item_id);
			return $return;
		}

		$item = ItemManager::getInstance()->getItem($item_id);

		//检测物品类型
		if ( $item === NULL )
		{
			Logger::WARNING('invalid item_id:%d', $item_id);
			return $return;
		}

		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			Logger::WARNING('item_id:%d not in bag!', $item_id);
			return $return;
		}

		if ( $bag->removeItem($item_id) == FALSE )
		{
			Logger::FATAL('remove item failed!');
			return $return;
		}
		// 物品模板id
		$itemTempLateId = $item->getItemTemplateID();
		$itemInfo = btstore_get()->ITEMS[$itemTempLateId];
		
		$user = EnUser::getUserObj();
		$uid = RPCContext::getInstance()->getUid();
		
		$item_type = $item->getItemType();
		$number = $item->getItemNum();
		if ($item_type == ItemDef::ITEM_MOUNT)
		{
			// 扣除阅历		
			if ( $user->subExperience($itemInfo[ItemDef::ITEM_ATTR_NAME_MOUNT_SPLIT_COST][0]*$number) == FALSE )
				{
					Logger::DEBUG("no enough experience!");
					return $return;
				}
			
			//增加belly
			$user->addBelly($itemInfo[ItemDef::ITEM_ATTR_NAME_MOUNT_SPLIT_COST][1]*$number);
			
			//增加装饰水晶
			
			$curCrystal = HorseDecorationDao::get($uid, array('resource'));
			$retCrystal = $itemInfo[ItemDef::ITEM_ATTR_NAME_MOUNT_SPLIT_COST][2]*$number;
		} else
		{
			// 扣除阅历		
			if ( $user->subExperience($itemInfo[ItemDef::ITEM_ATTR_NAME_DECORATION_SPLIT_COST][0]*$number) == FALSE )
				{
					Logger::DEBUG("no enough experience!");
					return $return;
				}
			
			//增加belly
			$user->addBelly($itemInfo[ItemDef::ITEM_ATTR_NAME_DECORATION_SPLIT_COST][1]*$number);
			
			//增加装饰水晶
			
			$curCrystal = HorseDecorationDao::get($uid, array('resource'));
			$retCrystal = $itemInfo[ItemDef::ITEM_ATTR_NAME_DECORATION_SPLIT_COST][2]*$number;
		}

		HorseDecorationDao::update($uid, array('resource'=>$curCrystal['resource']+$retCrystal));
		
		$user->update();
		$bag->update();

		$return = array(ExchangeDef::EXCHANGE_SUCCESS => true);
		
		return $return;
	}
	
	public function exchangeDaimonAppleItem($item_id)
	{
		$return = array(ExchangeDef::EXCHANGE_SUCCESS => FALSE);
		
		//检测参数是否合法
		if ( $item_id <= ItemDef::ITEM_ID_NO_ITEM )
		{
			Logger::WARNING('invalid item_id:%d', $item_id);
			return $return;
		}

		$item = ItemManager::getInstance()->getItem($item_id);

		//检测物品类型
		if ( $item === NULL )
		{
			Logger::WARNING('invalid item_id:%d', $item_id);
			return $return;
		}

		$item_type = $item->getItemType();
		if ($item_type != ItemDef::ITEM_DAIMONAPPLE)
		{
            Logger::WARNING('invalid item_id:%d, item_type:%d', $item_id, $item_type);
            return $return;
		}

		$bag = BagManager::getInstance()->getBag();
		if ( $bag->getGridID($item_id) == BagDef::BAG_INVALID_BAG_ID )
		{
			Logger::WARNING('item_id:%d not in bag!', $item_id);
			return $return;
		}

		if ( $item_type == ItemDef::ITEM_ARM && $item->noEnchase() == FALSE )
		{
			Logger::WARNING('item_id:%d should not be enchanse item!', $item_id);
			return $return;
		}

		if ( $bag->removeItem($item_id) == FALSE )
		{
			Logger::FATAL('remove item failed!');
			return $return;
		}
		// 物品模板id
		$itemTempLateId = $item->getItemTemplateID();
		$itemInfo = btstore_get()->ITEMS[$itemTempLateId];
		
		// 扣除阅历
		$user = EnUser::getUserObj();
		if ( $user->subExperience($itemInfo[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_DECOMPOSE_NEED_PRESTIGE]) == FALSE )
			{
				Logger::DEBUG("no enough experience!");
				return $return;
			}		
		
		$item->returnExpKernel();
		$this->setExchangeInfo($item_id, $itemInfo[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_DECOMPOSE_ITEM]->toArray());
		
		$bag->update();
		$user->update();

		$return = array (
			ExchangeDef::EXCHANGE_SUCCESS => true,
			ExchangeDef::ITEMS => $itemInfo[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_DECOMPOSE_ITEM],
		);
		
		return $return;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */