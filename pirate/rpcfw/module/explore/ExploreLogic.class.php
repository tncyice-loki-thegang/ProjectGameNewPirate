<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ExploreLogic.class.php 27645 2012-09-21 06:08:09Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-13-28/module/explore/ExploreLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-21 14:08:09 +0800 (Fri, 21 Sep 2012) $
 * @version $Revision: 27645 $
 * @brief 
 * 
 **/





class ExploreLogic
{
	public static function getExplore($uid, $exploreId)
	{
		$info = self::getInfo($uid, $exploreId);
		if (empty($info))
		{
			self::insertDefault($uid, $exploreId);
			return self::getInfo($uid, $exploreId);
		}
		return $info;
	}
	
	private static function getSessionKey($exploreId)
	{
		return 	'explore' . $exploreId;
	}
	
	private static function getInfo($uid, $exploreId)
	{
		//from session
		$info = RPCContext::getInstance()->getSession(self::getSessionKey($exploreId));
		if (!empty($info))
		{
			return $info;
		}
		
		//from db
		$info = ExploreDao::getInfo($uid, $exploreId, array('explore_time', 'integral', 'va_explore'));
		if (empty($info))
		{
			return array();
		}

		
		$arrRet = array();
		$arrRet['pos'] = $info['va_explore']['pos'];
		$arrRet['items'] = $info['va_explore']['items'];
		$arrRet['integral'] = $info['integral'];
		$arrRet['explore_time'] = $info['explore_time'];
		
		//set session
		$info = RPCContext::getInstance()->setSession(self::getSessionKey($exploreId), $arrRet);
		return $arrRet;
	}
	
	private static function insertDefault($uid, $exploreId)
	{
		$EXP_CFG = btstore_get()->EXPLORE;
		if (!isset($EXP_CFG[$exploreId]))
		{
			Logger::warning('exploreId %d is not found', $exploreId);
			throw new Exception('fake');
		}
		
		$posNum = count($EXP_CFG[$exploreId]['pos']);
		$va_explore['pos'] = array_fill(0, $posNum, 0);
		$va_explore['pos'][0] = 1;
		$va_explore['items'] = array();
		
		ExploreDao::insert($uid, $exploreId, 
			array('va_explore'=>$va_explore,
				'integral' => 0, 
				'explore_time'=>0));
	}
	
	public static function explorePos($uid, $exploreId, $snPos)
	{
		$arrRet = array('ret'=>'ok', 'item'=>0, 'newPos'=>-1);
		
		$info = self::getInfo($uid, $exploreId);
		if ($info['pos'][$snPos]==0)
		{
			Logger::warning('the pos %d is not open', $snPos);
			throw new Exception('fake');
		}		
		
		$EXP_CFG = btstore_get()->EXPLORE[$exploreId]['pos'];

		$user = EnUser::getUserObj($uid);
		//今天已经探索过，减belly
		if (Util::isSameDay(($info['explore_time'])))
		{
			$needBelly = $EXP_CFG[$snPos]['spend'];			
			if (!$user->subBelly($needBelly))
			{
				$arrRet['ret'] = 'belly';
				return $arrRet;
			}
		}	

		//检查放装备的位置
		if (count($info['items'])>=ExploreConf::ITEM_NUM)
		{
			Logger::warning('fail to explore, the item is full');
			throw new Exception('fake');
		}

		//掉装备
		//得到掉落表
		if ($snPos==ExploreConf::POS_CHANGE_DROPID
			&& $info['integral'] >= ExploreConf::INTEGRAL_CHANGE_DROPID)
		{
			$info['integral'] -= ExploreConf::INTEGRAL_CHANGE_DROPID;
			$droptableId = ExploreConf::CHANGED_DROPID;		
		}
		else
		{		
			$droptableId = $EXP_CFG[$snPos]['droptableId'];
		}
		$arrItem = ItemManager::getInstance()->dropItem($droptableId);				
		
		if (!empty($arrItem))
		{
			$arrRet['item'] = $arrItem[0];
			$info['items'][] = $arrRet['item'];
			ItemManager::getInstance()->update();

			$tmpItem = ChatTemplate::prepareItem(array($arrItem[0]));
			ChatTemplate::sendExploreItem($user->getTemplateUserInfo(), $user->getGroupId(), $tmpItem);
		}					
				
		if (isset($EXP_CFG[$snPos+1]))
		{
			$nextRate = $EXP_CFG[$snPos+1]['rate'];
			$rand = rand(1, ExploreConf::NEXT_PROB_BASE);
			if ($rand <= $nextRate)
			{
				$arrRet['newPos'] = $snPos + 1;
				$info['pos'][$snPos+1] = 1;
				//点亮加积分
				$info['integral'] += ExploreConf::$POS_INTEGRAL[$snPos+1];
			}
		}
		//当前探索设置为0
		if ($snPos!=0)
		{
			$info['pos'][$snPos] = 0;
		}
		
		//修改探索时间
		$info['explore_time'] = Util::getTime();
		
		
		$user->update();
		self::update($uid, $exploreId, $info);						
		return $arrRet;	
	}
	
	public static function sell($uid, $exploreId, $arrItemId)
	{
		$info = self::getInfo($uid, $exploreId);
		$trade = new Trade();
		$itemMgr = ItemManager::getInstance();
		
		foreach ($arrItemId as $itemId)
		{
			$pos = array_search($itemId, $info['items']);
			if ($pos===false)
			{
				Logger::warning('fail to sell, the item id %d is not exist', $itemId);
				throw new Exception('fake');
			}
			
			if (!$trade->sellNoUpdate($itemId, 1, $uid))
			{
				Logger::warning('fail to sell item %d, trade return false', $itemId);
				throw new Exception('sys');				
			}
			
			$itemMgr->deleteItem($itemId);
			unset($info['items'][$pos]);
		}
		$info['items'] = array_merge($info['items']);
		self::update($uid, $exploreId, $info);
		$itemMgr->update();
		EnUser::getUserObj($uid)->update();
	}
	
	public static function getBoxByGold($uid, $exploreId, $snPos)
	{
		$arrRet = array('ret'=>'ok', 'item'=>0);
		
		$user = EnUser::getUserObj($uid);
		$vip = $user->getVip();
		$EXP_VIP = btstore_get()->VIP[$vip]['explore_vip'];
		if (!isset($EXP_VIP[$exploreId]) || $EXP_VIP[$exploreId]['pos']!=$snPos)
		{
			Logger::warning('fail to getBoxByGold, the exploreId %d pos %d is not in config', 
				$exploreId, $snPos);
			throw new Exception('fake');
		}
		
		$needGold = $EXP_VIP[$exploreId]['gold'];
		if (!$user->subGold($needGold))
		{
			Logger::warning('gold is not enough');
			throw new Exception('fake');			
		}
		
		$info = self::getInfo($uid, $exploreId);
		if ($info['pos'][$snPos] == 1)
		{
			Logger::warning('fail to getBoxByGold, box is exist');
			throw new Exception('fake');
		}
		
		$info['pos'][$snPos] = 1;		
		//点亮加积分
		$info['integral'] += ExploreConf::$POS_INTEGRAL[$snPos];
		
		//检查放装备的位置
		if (count($info['items'])>=ExploreConf::ITEM_NUM)
		{
			Logger::warning('fail to getBoxByGold, the item is full');
			throw new Exception('fake');
		}

		//掉装备
		$arrItem = ItemManager::getInstance()->dropItem(ExploreConf::DROP_ID_OPEN_BOX);						
		
		if (!empty($arrItem))
		{
			$arrRet['item'] = $arrItem[0];
			$info['items'][] = $arrRet['item'];
			ItemManager::getInstance()->update();

			$tmpItem = ChatTemplate::prepareItem(array($arrItem[0]));
			ChatTemplate::sendExploreItem($user->getTemplateUserInfo(), $user->getGroupId(), $tmpItem);
		}
		
		
		self::update($uid, $exploreId, $info);
		Statistics::gold(StatisticsDef::ST_FUNCKEY_EXPLORE_BOX, $needGold, Util::getTime());
		
		$user->update();
		
		return $arrRet;
	}
	
	public static function moveToBag($uid, $exploreId, $arrItem=null)
	{
		$arrRet = array('ret'=>'ok', 'grid'=>array(), 'items'=>array());
		$info = self::getInfo($uid, $exploreId);
		$bag = BagManager::getInstance()->getBag($uid);
		
		$allItem = ItemManager::getInstance()->getItems($info['items']);
		foreach ($info['items'] as $pos => $item)
		{
			if ($arrItem!=null)
			{
				if (!in_array($item, $arrItem))
				{
					$arrRet['items'][] = $item;
					continue;
				}
			}
			$itemType = $allItem[$item]->getItemType();
			if ($itemType == ItemDef::ITEM_GEM)
			{
				if (!$bag->addItem($item))
				{
					//放不下了
					$arrRet['items'][] = $item;
					$arrRet['ret'] = 'bag';
				}
			}
			else //不是宝石
			{
				$arrRet['items'][] = $item;
			}
		}
		
		$arrRet['grid'] = $bag->update();
		$info['items'] = $arrRet['items'];
		
		//更新数据
		self::update($uid, $exploreId, $info);			
		return $arrRet;
	}
	
	private static function update($uid, $exploreId, $info)
	{
		RPCContext::getInstance()->setSession(self::getSessionKey($exploreId), $info);
		ExploreDao::update($uid, $exploreId, 
							array('explore_time'=>$info['explore_time'],
							'integral' => $info['integral'],
							'va_explore'=>array(
									'pos'=>$info['pos'],									
									'items'=>$info['items'])));	
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */