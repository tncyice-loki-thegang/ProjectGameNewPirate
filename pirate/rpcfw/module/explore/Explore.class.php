<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Explore.class.php 38130 2013-02-05 05:39:18Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/explore/Explore.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-05 13:39:18 +0800 (二, 2013-02-05) $
 * @version $Revision: 38130 $
 * @brief 
 *  
 **/








class Explore implements IExplore
{
	private $uid = null;
	
	public function __construct()
	{
		if (!EnSwitch::isOpen(SwitchDef::EXPLORE))
		{
			Logger::warning('fail to explore, switch return false');
			throw new Exception('fake');
		}
		$this->uid = RPCContext::getInstance()->getSession('global.uid');		
	}
	
	private function checkTownExploreService($exploreId)
	{
		$townId = RPCContext::getInstance()->getTownId();
		if (!City::isTownService($townId, TownDef::TOWN_SERVICE_EXPLORE, $exploreId))
		{
			Logger::warning('fail, the town %d has no the service %d', $townId, $exploreId);
			throw new Exception('fake');
		}
	}
	
	/* (non-PHPdoc)
	 * @see IExplore::getExplore()
	 */
	public function getExplore ($exploreId, $confKey=0)
	{
		$this->checkTownExploreService($exploreId);
		
		$exploreObj = new ExploreObj($this->uid, $exploreId);
		$arrInfo = $exploreObj->getExplore();
		
		$arrRet = array();		
		$arrRet['explore_time'] = $arrInfo['explore_time'];
		$arrRet['pos'] = $arrInfo['va_explore']['pos'];		
		
		
		$arrItemInfo = array();		
		$fix = false;
		foreach ($arrInfo['va_explore']['items'] as $key=>$itemId)
		{
			if ($itemId != 0)
			{
				$item = ItemManager::getInstance()->getItem($itemId);
				if ($item == null)
				{
					unset($arrInfo['va_explore']['items'][$key]);
					$fix = true;
					Logger::fatal('fixing. unset item %d in explore %d', $itemId, $exploreId);
				}
				else
				{
					$arrItemInfo[] = $item->itemInfo();
				}
			}
		}
		
		if ($fix)
		{
			$va_explore = $arrInfo['va_explore'];
			$va_explore['items'] = array_merge($arrInfo['va_explore']['items']);
			ExploreDao::update($this->uid, $exploreId, array('va_explore'=>$va_explore));
			$exploreObj->resetSession();
		}
		
		$arrRet['config'] = array(
				'quick_explore_arr' => ExploreConf::$QUICK_EXPLORE_ARR,
				'default_quick_explore' => ExploreConf::DEFAULT_QUICK_EXPLORE,
				);
		
		
		$arrRet['items'] = $arrItemInfo;
		
		
		if ($confKey!==0)
		{
			$user = EnUser::getUserObj();
			$arrConfig = $user->getArrConfig();
			if (isset($arrConfig[$confKey]))
			{
				$arrRet[$confKey] = $arrConfig[$confKey];
			}
		}
		$info = GemMatrixLogic::getInfo($this->uid);
		$arrRet['matrix_score'] = $info['score'];
		$arrRet['matrix_elite'] = $info['elite'];
		return $arrRet;
	}
	
	/* (non-PHPdoc)
	 * @see IExplore::exploreArm()
	 */
	public function explorePos ($exploreId, $snPos, $retainQuality)
	{
		$this->checkTownExploreService($exploreId);
		
		$exploreObj = new ExploreObj($this->uid, $exploreId);
		$arrRet = $exploreObj->explorePos($snPos, $retainQuality);
		$exploreObj->update();
		
		EnDaytask::treasure();
		TaskNotify::operate(TaskOperateType::EXPLORE);
		EnActive::addExploreTimes();
	
		
		if ($arrRet['item']!=0)
		{
			$arrRet['item'] = ItemManager::getInstance()->itemInfo($arrRet['item']);
		}
		
		$arrRet['ret'] = 'ok';
		$arrRet['pos'] = $exploreObj->getExplorePos();
		$arrRet['newPos'] = -1;
		if (isset($arrRet['pos'][$snPos+1]) && $arrRet['pos'][$snPos+1]==1)
		{
			$arrRet['newPos'] = $snPos + 1;
		}
		return $arrRet;
	}
	
	/* (non-PHPdoc)
	 * @see IExplore::quickExplore()
	*/
	public function quickExplore ($exploreId, $totalBelly, $retainQuality)
	{		
		$this->checkTownExploreService($exploreId);
		
		if (!in_array($totalBelly, ExploreConf::$QUICK_EXPLORE_ARR))
		{
			Logger::warning('belly %d error', $totalBelly);
			throw new Exception('fake');
		}
		
		$exploreObj = new ExploreObj($this->uid, $exploreId);
		$arrRet = $exploreObj->quickExplore($totalBelly, $retainQuality);
		$exploreObj->update();
		
		Logger::info('cost belly: %d , trash belly: %d , gem_exp: %d , items: %s', 
			$arrRet['cost_belly'], $arrRet['trash_belly'], $arrRet['gem_exp'], $arrRet['items']);
		
		EnDaytask::treasure(10);
		TaskNotify::operate(TaskOperateType::EXPLORE);
		EnActive::addExploreTimes();
		
		$arrItemInfo = array();
		if (!empty($arrRet['items']))
		{
			$arrRet['items'] = ItemManager::getInstance()->itemInfos($arrRet['items']);
			$arrRet['items'] = array_values($arrRet['items']);
		}
		
		$arrRet['pos'] = $exploreObj->getExplorePos(); 				
		$arrRet['ret'] = 'ok';
		return $arrRet;	
	}
	
	
	
	/* (non-PHPdoc)
	 * @see IExplore::sell()
	 */
	public function sell ($exploreId, $arrItemId)
	{
		if (empty($arrItemId))
		{
			Logger::warning('fail to sell explore item, item id is empty');
			throw new Exception('fake');
		}
		$this->checkTownExploreService($exploreId);
		
		$exploreObj = new ExploreObj($this->uid, $exploreId);
		$exploreObj->sell($arrItemId);
		$exploreObj->update();
		
		return 'ok';
	}
	
	/* (non-PHPdoc)
	 * @see IExplore::moveToBag()
	 */
	public function moveToBag ($exploreId, $arrItem=null)
	{
		$this->checkTownExploreService($exploreId);
		
		$exploreObj = new ExploreObj($this->uid, $exploreId);
		$arrRet = $exploreObj->move2bag($arrItem);
		if ($arrRet['ret']!='ok' && $arrRet['ret']!='bag')
		{
			Logger::warning('explore move2bag err:%s', $arrRet['ret']);
			throw new Exception('err');
		}
		
		$arrRet['grid'] = BagManager::getInstance()->getBag()->update();
		$exploreObj->update();
		
		if (!empty($arrRet['items']))
		{
			$arrRet['items'] = ItemManager::getInstance()->itemInfos($arrRet['items']);
			$arrRet['items'] = array_values($arrRet['items']);
		}
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IExplore::getBoxByGold()
	 */
	public function getBoxByGold ($exploreId, $snPos, $retainQuality)
	{
		$this->checkTownExploreService($exploreId);
		
		$exploreObj = new ExploreObj($this->uid, $exploreId);
		$arrRet = $exploreObj->getBoxByGold($snPos, $retainQuality);
		$exploreObj->update();
		
		if ($arrRet['item']!=0)
		{
			$arrRet['item'] = ItemManager::getInstance()->getItem($arrRet['item'])->itemInfo();
		}
		
		return $arrRet;
	}
	


}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */