<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Trade.class.php 31804 2012-11-24 10:46:29Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/trade/Trade.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-11-24 18:46:29 +0800 (六, 2012-11-24) $
 * @version $Revision: 31804 $
 * @brief
 *
 **/

class Trade implements ITrade
{
	/**
	 *
	 * 已经卖出的物品
	 * @var array
	 * <code>
	 * [
	 * 		item_id:int					物品ID
	 * 		sell_time:int				售出时间
	 * ]
	 * </code>
	 */
	private $m_repurchase;

	/**
	 *
	 * 用户uid
	 * @var int
	 */
	private $m_uid;

	public function Trade()	{
		$this->m_uid = RPCContext::getInstance ()->getSession("global.uid");
		if ( $this->m_uid == 0 )
		{
			if ( FrameworkConfig::DEBUG == TRUE )
			{
				$this->m_uid = 1;
			}
			else
			{
				Logger::FATAL('invalid user!uid=0');
				throw new Exception('fake');
			}
		}
		$this->getRepurchase();
	}

	/* (non-PHPdoc)
	 * @see ITrade::repurchaseInfo()
	 */
	public function repurchaseInfo() {
		$return = array();
		$item_manager = ItemManager::getInstance();
		foreach ( $this->m_repurchase as $key => $value )
		{
			$item_id = $value[TradeDef::REPURCHASE_ITEM_ID];
			$item = $item_manager->getItem($value[TradeDef::REPURCHASE_ITEM_ID]);
			$return[$item_id] = array (
				TradeDef::REPURCHASE_SELL_TIME => $value[TradeDef::REPURCHASE_SELL_TIME],
				TradeDef::REPURCHASE_ITEM_INFO => $item->itemInfo(),
			);
		}
		return $return;
	}

	/* (non-PHPdoc)
	 * @see ITrade::sellerInfo()
	 */
	public function sellerInfo($seller_id) {
		$seller = new Seller($seller_id);
		return $seller->getInfo();
	}

	/* (non-PHPdoc)
	 * @see ITrade::buy()
	 */
	public function buy($seller_id, $shop_place_id, $item_num) {
		$seller_id = intval($seller_id);
		$shop_place_id = intval($shop_place_id);
		$item_num = intval($item_num);
		$seller = new Seller($seller_id);
		if ( $seller === NULL )
		{
			return array();
		}
		else
		{
			return $seller->buy($shop_place_id, $item_num);
		}
	}

	/* (non-PHPdoc)
	 * @see ITrade::sell()
	 */
	public function sell($gid, $item_id, $item_num)	{

		$gid = intval($gid);
		$item_id = intval($item_id);
		$item_num = intval($item_num);

		$item_manager = ItemManager::getInstance();
		$bag = BagManager::getInstance()->getBag();
		//如果物品不属于该玩家(即不存在于玩家的背包里)
		if ( $bag->getGridID($item_id, TRUE) != $gid || $gid == BagDef::BAG_INVALID_BAG_ID )
		{
			Logger::debug('invalid item_id');
			return array();
		}

		$item = $item_manager->getItem($item_id);

		if ( $this->sellNoUpdate($item_id, $item_num) == FALSE )
		{
			return array();
		}

		//将物品拆分成两个物品
		$new_item_id = $item_manager->splitItem($item_id, $item_num);

		//如果拆分失败,则直接返回错误
		if ( $new_item_id == TradeDef::ITEM_ID_NO_ITEM )
		{
			Logger::FATAL('split error! item data change! check code avoid concurrency');
			return array();
		}

		//从背包里移除,!import由于回购的存在,故不能直接从系统中删除,并且可以卖出临时背包的物品
		if ( $new_item_id == $item_id )
		{
			$bag->removeItem($item_id, TRUE);
		}

		//更新用户数据
		$user = EnUser::getUserObj();
		$user->update();
		//更新背包数据
		$bag_modify = $bag->update();

		//将售出的物品添加到已出售列表
		$sell_time = Util::getTime();
		$this->addRepurchase($new_item_id, Util::getTime());
		$new_item = $item_manager->getItem($new_item_id);
		$return = array (
			'sell_time' => $sell_time,
			'item_info' => $new_item->itemInfo(),
		);
		return $return;
	}

	/**
	 *
	 * 出售物品
	 *
	 * @param int $item_id					物品id
	 * @param int $item_num					物品数量
	 * @param int $uid=0					用户uid
	 *
	 * @throws Exception
	 *
	 * @return boolean
	 */
	public function sellNoUpdate($item_id, $item_num, $uid=0)
	{
		$item_manager = ItemManager::getInstance();
		$item = $item_manager->getItem($item_id);
		//如果物品在系统中不存在,或者该物品类型不可出售,或者该物品的数量小于输入数量, 或者输入数量<=0
		if ( $item === NULL )
		{
			Logger::debug('item is not exist!');
			return FALSE;
		}
		if ( $item->canSell() == FALSE )
		{
			Logger::debug('item can not sell!');
			return FALSE;
		}
		if ( $item->getItemNum() < $item_num || $item_num <= 0)
		{
			Logger::debug('invalid item_num!');
			return FALSE;
		}

		$sell_info = $item->sellInfo();
		$sell_type = $sell_info['sell_type'];
		$sell_price = $sell_info['sell_price'] * $item_num;
		//调用User模块,增加金钱相关
		$user = EnUser::getUserObj($uid);
		switch ( $sell_type )
		{
			case TradeDef::TRADE_SELL_TYPE_BELLY:
				if ( $user->addBelly($sell_price) == FALSE )
				{
					Logger::FATAL('add belly failed!');
					return FALSE;
				}
				break;
			//TODO 可能有其他的出售所得类型
			default:
				Logger::FATAL('invalid sell type:%d', $sell_type);
				throw new Exception('config');
				break;
		}

		Logger::DEBUG('sellItem item_id=%d, item_template_id=%d, item_num=%d, sell_type=%d, sell_price=%d',
					$item->getItemID(), $item->getItemTemplateID(), $item->getItemNum(), $sell_info['sell_type'],
					 $sell_price);
		return TRUE;
	}

	/**
	 *
	 * 出售物品(不可回购)
	 * @deprecated
	 *
	 * @param array(int) $item_ids
	 *
	 * @return boolean
	 */
	public function sellNoRepurchases($item_ids)
	{
		$item_manager = ItemManager::getInstance();
		$bag = BagManager::getInstance()->getBag();
		foreach ( $item_ids as $item_id )
		{
			//如果物品不属于该玩家(即不存在于玩家的背包里)
			if ( $bag->getGridID($item_id, TRUE) == BagDef::BAG_INVALID_BAG_ID )
			{
				Logger::debug('invalid item_id:%d', $item_id);
				return FALSE;
			}

			$item = $item_manager->getItem($item_id);

			if ( $this->sellNoUpdate($item_id, $item->getItemNum()) == FALSE )
			{
				return FALSE;
			}

			//从背包里移除，并且从系统中移除
			$bag->deleteItem($item_id, TRUE);
		}

		//更新用户数据
		$user = EnUser::getUserObj();
		$user->update();
		//更新背包数据
		$bag->update();

		return TRUE;
	}

	/* (non-PHPdoc)
	 * @see ITrade::repurchase()
	 */
	public function repurchase($item_id)
	{
		$return = array();

		//如果该物品不在回购列表中
		$item_in_repurchase = FALSE;
		foreach ( $this->m_repurchase as $key => $value )
		{
			if ( $value[TradeDef::REPURCHASE_ITEM_ID] == $item_id )
			{
				$item_in_repurchase = TRUE;
				break;
			}
		}
		if ( $item_in_repurchase == FALSE )
		{
			Logger::DEBUG('item:%d not in repurchase list!', $item_id);
			return $return;
		}

		$item_manager = ItemManager::getInstance();
		$item = $item_manager->getItem($item_id);
		//如果物品在系统中不存在,或者该物品类型不可出售
		if ( $item === NULL || $item->canSell() == FALSE )
		{
			Logger::DEBUG('item can not sell or invalid item_id!');
			return $return;
		}

		$sell_info = $item->sellInfo();
		$sell_type = $sell_info['sell_type'];
		$sell_price = $sell_info['sell_price'] * $item->getItemNum();
		//调用User模块,增加金钱相关
		$user = EnUser::getUserObj();
		switch ( $sell_type )
		{
			case TradeDef::TRADE_SELL_TYPE_BELLY:
				if ( $user->subBelly($sell_price) == FALSE )
				{
					return $return;
				}
				break;
			//TODO 可能有其他的出售所得类型
			default:
				throw new Exception('invalid sell type:%d!', $sell_type);
				break;
		}

		//将物品添加到背包中
		$bag = BagManager::getInstance()->getBag();
		//如果添加成功，则将该物品移出回购列表
		if ( $bag->addItem($item_id) == TRUE )
		{
			$this->removeRepurchase($item_id);
		}
		else
		{
			return $return;
		}

		//更新用户数据
		$user->update();

		//更新背包数据
		return $bag->update();
	}

	private function getRepurchase()
	{
		$repurchases = RPCContext::getInstance()->getSession("trade.repurchase");
		if ( empty($repurchases) )
		{
			$repurchases = TradeDAO::getRepurchase($this->m_uid);
		}

		$item_manager = ItemManager::getInstance();
		$expire = FALSE;

		$item_ids = Util::arrayExtract($repurchases, TradeDef::REPURCHASE_ITEM_ID);
		ItemManager::getInstance()->getItems($item_ids);

		foreach ( $repurchases as $key => $repurchase )
		{
			if ( $repurchase[TradeDef::REPURCHASE_SQL_SELL_TIME] <
				 Util::getTime() - TradeConfig::REPURCHASE_EXPIRE_TIME )
			{
				$expire = TRUE;
				$item_manager->deleteItem($repurchase[TradeDef::REPURCHASE_ITEM_ID]);
				unset($repurchases[$key]);
			}
		}
		if ( $expire == TRUE )
		{
			TradeDAO::expireRepurchase($this->m_uid, Util::getTime() - TradeConfig::REPURCHASE_EXPIRE_TIME);
			$item_manager->update();
		}
		$this->m_repurchase = $repurchases;
		$this->setRepurchase();
	}

	private function addRepurchase($item_id, $sell_time)
	{
		TradeDAO::addRepurchase($this->m_uid, $item_id, $sell_time);
		$this->m_repurchase[] = array(
			TradeDef::REPURCHASE_ITEM_ID => $item_id,
			TradeDef::REPURCHASE_SELL_TIME => $sell_time
		);
		$this->setRepurchase();
	}

	private function removeRepurchase($item_id)
	{
		foreach ( $this->m_repurchase as $key => $value )
		{
			if ( $value[TradeDef::REPURCHASE_ITEM_ID] == $item_id )
			{
				TradeDAO::removeRepurchase($this->m_uid, $item_id);
				unset($this->m_repurchase[$key]);
			}
		}
		$this->setRepurchase();
	}

	private function setRepurchase()
	{
		RPCContext::getInstance()->setSession("trade.repurchase", $this->m_repurchase);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */