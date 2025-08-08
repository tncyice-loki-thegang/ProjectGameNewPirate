<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Seller.class.php 27035 2012-09-12 07:20:30Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/trade/seller/Seller.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-09-12 15:20:30 +0800 (三, 2012-09-12) $
 * @version $Revision: 27035 $
 * @brief
 *
 **/



class Seller
{

	/**
	 *
	 * 商店信息
	 * @var array
	 */
	private $m_seller;

	/**
	 *
	 * 商品出售这ID
	 * @var int
	 */
	private $m_seller_id;

	/**
	 *
	 * 限制数量出售的物品列表
	 * @var array
	 */
	private $m_limit;

	public function Seller($seller_id)
	{
		$this->m_seller_id = $seller_id;
		//得到出售的配置信息
		if ( !isset(btstore_get()->SHOP[$this->m_seller_id]) )
		{
			Logger::FATAL('not exist seller id:%d', $seller_id);
			throw new Exception('fake');
		}
		$this->m_seller = btstore_get()->SHOP[$this->m_seller_id];
		$this->m_limit = SellerDAO::getSeller($seller_id);
		//刷新物品购买数量
		foreach ( $this->m_limit as $key => $limit )
		{
			$this->m_limit[$key] = $this->refresh($limit);
		}
	}

	/**
	 *
	 * 得到出售信息(仅包括需要刷新的物品)
	 *
	 * @return array
	 */
	public function getInfo()
	{
		$limit = array();
		foreach ( $this->m_limit as $key => $value )
		{
			unset($value[SellerDef::SELLER_SQL_SHOP_PLACE_ID]);
			$limit[$key] = $value;
		}
		return $limit;
	}

	/**
	 *
	 * 刷新数量
	 *
	 * @param array $limit
	 */
	private function refresh($limit)
	{
		$time = Util::getTime();
		$refresh_time = $limit[SellerDef::SELLER_SQL_REFRESH_TIME];
		if ( $refresh_time > $time )
		{
			return $limit;
		}
		for ( ; ; )
		{
			$refresh_time += $this->m_seller[SellerDef::SELLER_SHOP_REFRESH_TIME_STEP];
			if ( $refresh_time > $time )
			{
				break;
			}
		}
		return SellerDAO::refreshSeller($this->m_seller_id, $limit, $refresh_time);
	}

	/* (non-PHPdoc)
	 * @see ISeller::buy()
	 */
	public function buy($shop_place_id, $item_num)
	{
		$shop_place_id = intval($shop_place_id);
		$item_num = intval($item_num);
		$return = array();

		//如果输入的数量小于等于0
		if ( $item_num <= 0 )
		{
			Logger::debug('invaild item_num!');
			return $return;
		}

		$shop_item_info = $this->getSellItemInfo($shop_place_id);
		//如果该物品的购买上限已到,则返回错误
		if ( $shop_item_info[SellerDef::SELLER_SHOP_ITEM_NUM_LIMIT] != 0 )
		{
			if ( $this->m_limit[$shop_place_id][SellerDef::SELLER_SQL_ITEM_BOUGHT_NUM]
				 >= $shop_item_info[SellerDef::SELLER_SHOP_ITEM_NUM_LIMIT] )
			{
				Logger::debug('shop item limited!shop_place_id:%d', $shop_place_id);
				return $return;
			}
		}
		//得到物品购买需求
		$shop_req_type = $shop_item_info[SellerDef::SELLER_SHOP_ITEM_REQ][SellerDef::SELLER_SHOP_ITEM_REQ_TYPE];
		$shop_req_value = $shop_item_info[SellerDef::SELLER_SHOP_ITEM_REQ][SellerDef::SELLER_SHOP_ITEM_REQ_VALUE] * $item_num;
		$bag = BagManager::getInstance()->getBag();

		$user = EnUser::getUserObj();

		//是否满足购买需求
		switch ($shop_req_type)
		{
			case SellerDef::SHOP_TYPE_BELLY:
				if ( $user->subBelly($shop_req_value) == FALSE )
				{
					Logger::DEBUG("no enough money!");
					return $return;
				}
				break;
			case SellerDef::SHOP_TYPE_EXPRIENCE:
				break;
			case SellerDef::SHOP_TYPE_FOOD:
				break;
			case SellerDef::SHOP_TYPE_GIFT_CASH:
				break;
			case SellerDef::SHOP_TYPE_GOLD:
				if ( $user->subGold($shop_req_value) == FALSE )
				{
					Logger::DEBUG("no enough gold!");
					return $return;
				}
				break;
			case SellerDef::SHOP_TYPE_REWARD_POINT:
				break;
			case SellerDef::SHOP_TYPE_ITEM:
				$item_template_id = $shop_item_info[SellerDef::SELLER_SHOP_ITEM_REQ][SellerDef::SELLER_SHOP_ITEM_REQ_ITEM_TEMPLALTE_ID];
				if ( $bag->deleteItemByTemplateID($item_template_id, $item_num) == FALSE )
				{
					Logger::DEBUG("no enough items!");
					return $return;
				}
				break;
			default:
				Logger::FATAL('shop_req_value:%d is invalid!', $shop_req_type);
				return $return;
				break;
		}

		//将产生的物品放入到背包里去
		$item_ids = ItemManager::getInstance()->addItem($shop_item_info[SellerDef::SELLER_SHOP_ITEM_TEMPLATE_ID], $item_num);
		$chat_items = ChatTemplate::prepareItem($item_ids);
		if ( $bag->addItems($item_ids) == FALSE )
		{
			//如果失败，则说明背包已满
			Logger::DEBUG('full bag!');
			return $return;
		}

		//如果该物品有购买上限,则更新上限,如果更新失败,则直接返回错误
		if ( $shop_item_info[SellerDef::SELLER_SHOP_ITEM_NUM_LIMIT] != 0 )
		{
			if ( SellerDAO::updateSellerLimit($this->m_seller_id, $this->m_limit[$shop_place_id]) == FALSE )
			{
				Logger::DEBUG('shop item limited!shop_place_id:%d', $shop_place_id);
				return $return;
			}
		}

		//发送系统消息
		ChatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $chat_items);

		//更新用户数据
		$user->update();

		//Statistics
		if ( $shop_req_type == SellerDef::SHOP_TYPE_GOLD )
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_TRADE_BUY, $shop_req_value, Util::getTime());
		}

		//更新背包数据
		return $bag->update();
	}

	private function getSellItemInfo($shop_place_id)
	{
		if ( isset($this->m_seller[SellerDef::SELLER_SHOP_ITEMS][$shop_place_id]) )
		{
			return $this->m_seller[SellerDef::SELLER_SHOP_ITEMS][$shop_place_id];
		}
		else
		{
			Logger::FATAL('shop_place_id is NULL, shop_id:%s, shop_place_id:%s', $this->m_seller_id, $shop_place_id);
			throw new Exception('fake');
		}
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */