<?php

class DressLogic
{
	private static $arrField = array('cur_dress', 'va_collect_ids', 'va_info');

	private static function insertDefault($uid)
	{
		$arrField = array(
			'uid'=>$uid, 
			'cur_dress' => 0, 
			'va_collect_ids' => array(), 
			'va_info' => array(),
			);		
		DressDao::insert($uid, $arrField);
	}

	public static function getDressRommInfo($uid)
	{
		$ret = DressDao::get($uid, self::$arrField);
		if (empty($ret))
		{
			self::insertDefault($uid);
			$ret = DressDao::get($uid, self::$arrField);
		}
		return $ret;
	}	
	
	public static function reinforce($uid, $item_id)
	{
		$ret = array('success' => FALSE);
		
		//检查装备是否属于该用户
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return $ret;
		}
		$itemMgr = ItemManager::getInstance();
		$item = $itemMgr->getItem($item_id);
		//检查强化的物品是否为时装
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_FASHION_DRESS )
		{
			Logger::DEBUG('item_id:%d, not a dress!', $item_id);
			return $ret;
		}		

		//得到时装强化等级
		$reinforceLevel = $item->getReinforceLevel();
		
		//得到用户对象
		$user = EnUser::getInstance();
		$bag = BagManager::getInstance()->getBag();
		
		//当前玩家可以强化的最大等级
		$item_template_id = $item->getItemTemplateID();
		$strengthInfo = btstore_get()->DRESS_STRENGTH[$item->getStrengthId($item_template_id)];
		$reinforceLevel_max = 0;
		$userlv=$user->getLevel();
		foreach ($strengthInfo['levelLimit'] as $key => $value)
		{			
			if ($userlv < $value)
			{
				$reinforceLevel_max = $key--;
				break;
			}
			$reinforceLevel_max = $key;
		}
		
		// 时装强化等级限制
		if ( $reinforceLevel >= $reinforceLevel_max )
		{
			Logger::warning("reinforce lvl exceeded,cur:%d/max:%d",$reinforceLevel,$reinforceLevel_max);
			break;
		}
		
		// 当前强化等级的消耗需求
		$itemreq = $strengthInfo['itemID'];
		$itemNumReq = $strengthInfo['itemNum'][$reinforceLevel+1];
		$strengthBellyReq = $strengthInfo['strengthBelly'][$reinforceLevel+1];
	
		if($bag->deleteItembyTemplateID($itemreq, $itemNumReq)==FALSE || $user->subBelly($strengthBellyReq)==FALSE)
		{
			Logger::warning('not enough req');
			return $ret;
		}
		$ret['success'] = TRUE;
		$ret['level'] = $reinforceLevel+1;
		$ret['curbelly'] = $user->getbelly();
		$ret['baginfo'] = $bag->update();
		$item->setReinforceLevel($reinforceLevel+1);
		$user->update();
		$itemMgr->update();
		return $ret;
	}
	
	public static function compose($uid, $id)
	{
		
	}
	
	public static function split($uid, $item_id)
	{
		$ret = array('success' => FALSE);
		
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
		$item_template_id = $item->getItemTemplateID();
		$strengthId = $item->getStrengthId($item_template_id);
		if ( $item_type != ItemDef::ITEM_FASHION_DRESS || $strengthId == 0 )
		{
            Logger::WARNING('invalid item_id:%d, item_type:%d', $item_id, $item_type);
            return $return;
		}

		$exchange_info = Exchange::exchangeInfo();
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

		if ( $bag->removeItem($item_id) == FALSE )
		{
			Logger::FATAL('remove item failed!');
			return $return;
		}
				
		$user = EnUser::getUserObj();
		
		$strengthInfo = btstore_get()->DRESS_STRENGTH[$strengthId];
		$reinforceLevel = $item->getReinforceLevel();
		$experiencereq = $strengthInfo['experience'][$reinforceLevel];
		
		$sell_info = $item->sellInfo();
		$retNum = $strengthInfo['returnNum'][$reinforceLevel];
		$retBelly = $strengthInfo['returnBelly'][$reinforceLevel] + $sell_info['sell_price'];
		
		if ( $user->subExperience($experiencereq) == FALSE )
		{
			Logger::WARNING('no enough experience!');
			return $return;
		}
		
		switch ( $sell_info['sell_type'] )
		{
			case TradeDef::TRADE_SELL_TYPE_BELLY:
				if ( $user->addBelly($retBelly) == FALSE )
				{
					Logger::FATAL('add belly failed!');
					return $return;
				}
				break;
			//TODO 可能有其他的出售所得类型
			default:
				Logger::FATAL('invalid sell type:%d', $sell_info['sell_type']);
				throw new Exception('invalid sell type:%d!', $sell_info['sell_type']);
				break;
		}

		$items[120013]=$retNum;
		Exchange::setExchangeInfo($item_id, $items);
		
		$bag->update();
		$user->update();
		
		TaskNotify::operate(TaskOperateType::GIFT_EXCHANGE);
		
		$ret['success'] = TRUE;
		$ret['curbelly'] = $user->getbelly();
		$ret['curexperience'] = $user->getExperience();
		$ret['va_items'] = $items;
		
		return $ret;
	}
	
	public static function gethtid($htid)
	{
		switch ($htid)
		{
			case 11001:
				$ret = array(2100001,2100021,2100031,2100041,2100051,2100061,2100071,2100081,2100091,2100101,2100111,2100121,2100131,2100141,2100151,2100161,2100171,2100181);
				break;

			case 11002:
				$ret = array(2100002,2100022,2100032,2100042,2100052,2100062,2100072,2100082,2100092,2100102,2100112,2100122,2100132,2100142,2100152,2100162,2100172,2100182);
				break;

			case 11003:
				$ret = array(2100003,2100023,2100033,2100043,2100053,2100063,2100073,2100083,2100093,2100103,2100113,2100123,2100133,2100143,2100153,2100163,2100173,2100183);
				break;

			case 11004:
				$ret = array(2100004,2100024,2100034,2100044,2100054,2100064,2100074,2100084,2100094,2100104,2100114,2100124,2100134,2100144,2100154,2100164,2100174,2100184);
				break;

			case 11005:
				$ret = array(2100005,2100025,2100035,2100045,2100055,2100065,2100075,2100085,2100095,2100105,2100115,2100125,2100135,2100145,2100155,2100165,2100175,2100185);
				break;

			case 11006:
				$ret = array(2100006,2100026,2100036,2100046,2100056,2100066,2100076,2100086,2100096,2100106,2100116,2100126,2100136,2100146,2100156,2100166,2100176,2100186);
				break;
		}
		return $ret;
	}
	
	public static function addDressRoom($item_template_id)
	{
		$uid = RPCContext::getInstance()->getUid();
		$user = EnUser::getUserObj($uid);
		$htid = UserConf::$USER_INFO[$user->getUtid()][1];
		if (!in_array($item_template_id, self::gethtid($htid))) {
			return $item_template_id;
		}
		
		$info = self::getDressRommInfo($uid);
		if (!empty($info['va_info'])) {
			$max_keys = max(array_keys($info['va_info'])) + 1;
		} else {
			$max_keys = 1;
		}
		if (!in_array($item_template_id, $info['va_collect_ids'])) {
			// up info
			$info['va_info'] = array_merge($info['va_info'], array($max_keys => array('itemid' => $item_template_id)));
			// up collect_ids
			array_push($info['va_collect_ids'], $item_template_id);
			DressDao::update($uid, $info);
		}
	}

}
