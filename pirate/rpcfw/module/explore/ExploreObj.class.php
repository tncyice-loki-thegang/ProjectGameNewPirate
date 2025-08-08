<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

class ExploreObj
{
	private $uid = 0;
	private $exploreId = 0;
	private $attr = array();
	private $attrModify = array();
	private $bag = null;
	
	public function __construct($uid, $exploreId)
	{
		$this->uid = $uid;
		$this->exploreId = $exploreId;		
		$this->loadInfo();
		
		if (empty($this->attr))
		{
			$this->insertDefault();
			$this->loadInfo();			
		}
		$this->attrModify = $this->attr;
	}
	
	public function getExplore()
	{
		return $this->attrModify;
	}
	
	private function getSessionKey()
	{
		return 	'explore' . $this->uid . $this->exploreId;
	}
	
	public function resetSession()
	{
		RPCContext::getInstance()->setSession($this->getSessionKey(), 0);	
	}
	
	private function loadInfo()
	{
		//from session
		$this->attr = RPCContext::getInstance()->getSession($this->getSessionKey());
		if (!empty($this->attr))
		{
			return $this->attr;
		}
	
		//from db
		$this->attr = ExploreDao::getInfo($this->uid, $this->exploreId, array('explore_time', 'integral', 'va_explore'));
		if (empty($this->attr))
		{
			return array();
		}
	
		//set session
		RPCContext::getInstance()->setSession(self::getSessionKey(), $this->attr);
		return $this->attr;
	}	
	
	private function insertDefault()
	{
		$EXP_CFG = btstore_get()->EXPLORE;
		if (!isset($EXP_CFG[$this->exploreId]))
		{
			Logger::warning('exploreId %d is not found', $this->exploreId);
			throw new Exception('fake');
		}
	
		$posNum = count($EXP_CFG[$this->exploreId]['pos']);
		$va_explore['pos'] = array_fill(0, $posNum, 0);
		$va_explore['pos'][0] = 1;
		$va_explore['items'] = array();
	
		ExploreDao::insert($this->uid, $this->exploreId,
			array('va_explore'=>$va_explore,
				'integral' => 0,
				'explore_time'=>0));
	}
	
	protected function explore($snPos, $retainQuality)
	{				
		Logger::debug('explore pos :%d', $snPos);
		$arrRet = array(
				0, //trush_belly
				0, //exp
				0, //item_tpl_id
				);
		
		$EXP_CFG = btstore_get()->EXPLORE[$this->exploreId]['pos'];	
		//掉装备
		//得到掉落表
		if ($snPos==ExploreConf::POS_CHANGE_DROPID
				&& $this->attrModify['integral'] >= ExploreConf::INTEGRAL_CHANGE_DROPID)
		{
			$this->attrModify['integral'] -= ExploreConf::INTEGRAL_CHANGE_DROPID;
			$droptableId = ExploreConf::CHANGED_DROPID;
		}
		else
		{
			$droptableId = $EXP_CFG[$snPos]['droptableId'];
		}
	
		//掉落物品模板
		$arrItemTpl = Drop::dropItem($droptableId);
		//有且只有一个物品
		$itemTpl = $arrItemTpl[0]['item_template_id'];		
	
		if (isset($EXP_CFG[$snPos+1]))
		{
			$nextRate = $EXP_CFG[$snPos+1]['rate'];
			$rand = rand(1, ExploreConf::NEXT_PROB_BASE);
			if ($rand <= $nextRate)
			{
				//开启新位置
				$this->attrModify['va_explore']['pos'][$snPos+1] = 1;
				//点亮加积分
				$this->attrModify['integral'] += ExploreConf::$POS_INTEGRAL[$snPos+1];
			}
			
		}
		
		//当前探索设置为0
		if ($snPos!=0)
		{
			$this->attrModify['va_explore']['pos'][$snPos] = 0;
		}
	
		//修改探索时间
		$this->attrModify['explore_time'] = Util::getTime();
				
		$this->exploreTime = 1;
		
		$itemMgr = ItemManager::getInstance();
		$itemTplInfo = $itemMgr->getExploreInfo($itemTpl);
		
		if ($retainQuality==0) 
		{
			//不转化， 掉物品
			$arrItemId = $itemMgr->addItem($itemTpl);
			$this->attrModify['va_explore']['items'][] = $arrItemId[0];
			return $arrRet;			
		}
				
		//垃圾卖掉
		if ($itemTplInfo['isgem']==0)
		{
			$arrRet[0] = $itemTplInfo['sell_price']; 
		}
		//不保留的变成经验
		else if ($itemTplInfo['quality'] < $retainQuality)
		{
			$arrRet[1] = $itemTplInfo['exp'];
			$arrRet[2] = $itemTpl;
		}
		else //掉物品
		{
			$arrItemId = $itemMgr->addItem($itemTpl);
			$this->attrModify['va_explore']['items'][] = $arrItemId[0];
		}
		
		return $arrRet;
	}
	
	private function getFitExplorePos()
	{
		$snPos = 0;
		foreach ($this->attrModify['va_explore']['pos'] as $k => $v)
		{
			if ($v!=0)
			{
				$snPos = $k;
			}
		}
		return $snPos;
	}
	
	public function quickExplore($totalBelly, $retainQuality)
	{		
		$user = EnUser::getUserObj($this->uid);
		
		//check belly
		if ($user->getBelly() < $totalBelly)
		{
			Logger::warning('belly is not enough for quick explore');
			throw new Exception('fake');
		}	
		
		//check vip
		$vip = $user->getVip();
		if (btstore_get()->VIP[$vip]['quick_explore']==0)
		{
			Logger::warning('the user vip level cannot quick explore');
			throw new Exception('fake');	
		}		

		$itemMgr = ItemManager::getInstance();
		
		$arrRet = array('cost_belly'=>0, 'explore_num'=>0, 'trash_num'=>0, 'trash_belly'=>0, 'gem_exp'=>0, 'items'=>array());	
		$totalMaxNum = 10000;
		while (--$totalMaxNum > 0) 
		{			
			//检查放装备的位置
			if (count($this->attrModify['va_explore']['items']) >=ExploreConf::ITEM_NUM)
			{
				Logger::debug('pos is full');
				break;
			}		

			$snPos = $this->getFitExplorePos();
			
			//减belly， 每天免费一次
			$EXP_CFG = btstore_get()->EXPLORE[$this->exploreId]['pos'];
			$needBelly = 0;
			if (Util::isSameDay(($this->attrModify['explore_time'])))
			{
				$needBelly = $EXP_CFG[$snPos]['spend'];
			}
			
			if (($arrRet['cost_belly']+$needBelly) > $totalBelly)
			{
				break;
			}			
			$arrRet['cost_belly'] += $needBelly;
			
			list($trashBelly, $gemExp, $dumy) 
					= $this->explore($snPos, $retainQuality);
			if ($trashBelly!=0)
			{
				$arrRet['trash_num'] += 1;
				$arrRet['trash_belly'] += $trashBelly; 
			}
			
			$arrRet['gem_exp'] += $gemExp;
			
			$arrRet['explore_num'] += 1;		
		}
		
		$arrRet['items'] = array_slice($this->attrModify['va_explore']['items'], count($this->attr['va_explore']['items']));
		
		$user->subBelly($arrRet['cost_belly']);
		$user->addGemExp($arrRet['gem_exp']);
		$user->addBelly($arrRet['trash_belly']);

		
		return $arrRet;
	}
	
	public function update()
	{
		$arrField = array();		
		foreach ($this->attrModify as $k=>$v)
		{
			if ($this->attr[$k]!=$v)
			{
				$arrField[$k] = $v;
			}
		}
		
		if (!empty($arrField))
		{
			ItemManager::getInstance()->update();
		}
		
		$user = EnUser::getUserObj();
		$user->update();
		
		if (!empty($arrField))
		{
			ExploreDao::update($this->uid, $this->exploreId, $arrField);
				
			// 广播
			if (count($this->attrModify['va_explore']['items']) > count($this->attr['va_explore']['items']))
			{
				
				$arrNewItem = array_slice($this->attrModify['va_explore']['items'], count($this->attr['va_explore']['items']));
				foreach ($arrNewItem as $itemId)
				{
					$tmpItem = ChatTemplate::prepareItem(array($itemId));
					ChatTemplate::sendExploreItem($user->getTemplateUserInfo(), $user->getGroupId(), $tmpItem);
				}
			}

			$this->attr = $this->attrModify;
			
			RPCContext::getInstance()->setSession(self::getSessionKey(), $this->attr);
		}
	}
	
	public function explorePos($snPos, $retainQuality)
	{		
		$arrRet = array('trash_belly'=>0, 'item'=>array(), 'gem_exp'=>0, 'item_tpl_id'=>0);
		
		if ($this->attrModify['va_explore']['pos'][$snPos]==0)
		{
			Logger::warning('the pos %d is not open', $snPos);
			throw new Exception('fake');
		}
		
		$EXP_CFG = btstore_get()->EXPLORE[$this->exploreId]['pos'];
		
		$user = EnUser::getUserObj($this->uid);
		//今天已经探索过，减belly
		if (Util::isSameDay(($this->attrModify['explore_time'])))
		{
			$needBelly = $EXP_CFG[$snPos]['spend'];
			if (!$user->subBelly($needBelly))
			{
				Logger::warning('belly is not enough for explore');
				throw new Exception('fake');				
			}
		}
	
		//检查放装备的位置
		if (count($this->attrModify['va_explore']['items'])>=ExploreConf::ITEM_NUM)
		{
			Logger::warning('fail to explore, the item is full');
			throw new Exception('fake');
		}
		
		list($arrRet['trash_belly'], $arrRet['gem_exp'], $arrRet['item_tpl_id']) 
			= $this->explore($snPos, $retainQuality);
		
		$user->addBelly($arrRet['trash_belly']);
		$user->addGemExp($arrRet['gem_exp']);
		$arrRet['item'] = array_slice($this->attrModify['va_explore']['items'], count($this->attr['va_explore']['items']));
		if (empty($arrRet['item']))
		{
			$arrRet['item'] = 0;
		}
		else 
		{
			$arrRet['item'] = $arrRet['item'][0];
		}
		return $arrRet;
	}
	
	public function getExplorePos()
	{
		return $this->attrModify['va_explore']['pos'];
	}
	
	public function sell($arrItemId)
	{
		$trade = new Trade();
		$itemMgr = ItemManager::getInstance();
		
		foreach ($arrItemId as $itemId)
		{
			$pos = array_search($itemId, $this->attrModify['va_explore']['items']);
			if ($pos===false)
			{
				Logger::warning('fail to sell, the item id %d is not exist', $itemId);
				throw new Exception('fake');
			}
				
			if (!$trade->sellNoUpdate($itemId, 1, $this->uid))
			{
				Logger::warning('fail to sell item %d, trade return false', $itemId);
				throw new Exception('sys');
			}				
			$itemMgr->deleteItem($itemId);
			unset($this->attrModify['va_explore']['items'][$pos]);
		}
				
		$this->attrModify['va_explore']['items'] = array_merge($this->attrModify['va_explore']['items']);
	}
	
	public function move2bag($arrMoveItemId)
	{
		$arrRet = array('ret'=>'ok', 'grid'=>array(), 'items'=>array());		
		$bag = BagManager::getInstance()->getBag($this->uid);

		$allItemId = $this->attrModify['va_explore']['items'];			
		$allItem = ItemManager::getInstance()->getItems($allItemId);
		
		foreach ($allItemId as $pos => $itemId)
		{
			if ($arrMoveItemId!=null)
			{
				if (!in_array($itemId, $arrMoveItemId))
				{
					$arrRet['items'][] = $itemId;
					continue;
				}
			}
				
			if ($allItem[$itemId]===null)
			{
				continue;
			}
				
			$itemType = $allItem[$itemId]->getItemType();
			if ($itemType == ItemDef::ITEM_GEM)
			{
				if (!$bag->addItem($itemId))
				{
					//放不下了
					$arrRet['items'][] = $itemId;
					$arrRet['ret'] = 'bag';
				}
			}
			else //不是宝石
			{
				$arrRet['items'][] = $itemId;
			}
		}		
		$this->attrModify['va_explore']['items'] = $arrRet['items'];
		return $arrRet;
	}
	
	public function getBoxByGold($snPos, $retainQuality)
	{
		$arrRet = array('ret'=>'ok', 'item'=>0, 'trash_belly'=>0, 'gem_exp'=>0);
		
		$user = EnUser::getUserObj($this->uid);
		$vip = $user->getVip();
		$EXP_VIP = btstore_get()->VIP[$vip]['explore_vip'];
		if (!isset($EXP_VIP[$this->exploreId]) || $EXP_VIP[$this->exploreId]['pos']!=$snPos)
		{
			Logger::warning('fail to getBoxByGold, the exploreId %d pos %d is not in config',
			$this->exploreId, $snPos);
			throw new Exception('fake');
		}
		
		$needGold = $EXP_VIP[$this->exploreId]['gold'];
		if (!$user->subGold($needGold))
		{
			Logger::warning('gold is not enough');
			throw new Exception('fake');
		}
				
		if ($this->attrModify['va_explore']['pos'][$snPos] == 1)
		{
			Logger::warning('fail to getBoxByGold, box is exist');
			throw new Exception('fake');
		}
		
		$this->attrModify['va_explore']['pos'][$snPos] = 1;
		//点亮加积分
		$this->attrModify['integral'] += ExploreConf::$POS_INTEGRAL[$snPos];
		
		//检查放装备的位置
		if (count($this->attrModify['va_explore']['items'])>=ExploreConf::ITEM_NUM)
		{
			Logger::warning('fail to getBoxByGold, the item is full');
			throw new Exception('fake');
		}
		
		//掉物品模板
		$arrItemTpl = Drop::dropItem(ExploreConf::DROP_ID_OPEN_BOX);
		//有且只有一个物品
		$itemTpl = $arrItemTpl[0]['item_template_id'];
		$itemMgr = ItemManager::getInstance();
		$itemTplInfo = $itemMgr->getExploreInfo($itemTpl);
		
		if ($retainQuality==0)
		{
			//不转化， 掉物品
			$arrItemId = $itemMgr->addItem($itemTpl);
			$this->attrModify['va_explore']['items'][] = $arrItemId[0];
			//return $arrRet;
		}		
		//垃圾卖掉
		else if ($itemTplInfo['isgem']==0)
		{
			$arrRet['trash_belly'] = $itemTplInfo['sell_price'];
		}
		//不保留的变成经验
		else if ($itemTplInfo['quality'] < $retainQuality)
		{
			$arrRet['gem_exp'] = $itemTplInfo['exp'];
			$arrRet['item_tpl_id'] = $itemTpl;
		}
		else //掉物品
		{
			$arrItemId = $itemMgr->addItem($itemTpl);
			$this->attrModify['va_explore']['items'][] = $arrItemId[0];
		}
		
		$arrRet['item'] = array_slice($this->attrModify['va_explore']['items'], count($this->attr['va_explore']['items']));
		if (empty($arrRet['item']))
		{
			$arrRet['item'] = 0;
		}
		else
		{
			$arrRet['item'] = $arrRet['item'][0];
		}
		
		$user->addGemExp($arrRet['gem_exp']);
		
		Statistics::gold(StatisticsDef::ST_FUNCKEY_EXPLORE_BOX, $needGold, Util::getTime());		
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */