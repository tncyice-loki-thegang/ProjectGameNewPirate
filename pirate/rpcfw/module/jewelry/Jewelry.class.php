<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Jewelry.class.php 40808 2013-03-15 06:35:18Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/jewelry/Jewelry.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-03-15 14:35:18 +0800 (五, 2013-03-15) $
 * @version $Revision: 40808 $
 * @brief 
 *  
 **/

//宝物
class Jewelry implements IJewelry
{
	 // 物品管理器
	private $m_manager;
	
	 // 用户UID
	private $m_uid;
	
	 //相关信息
	private $m_info;
	
	/**
	 * 初始化
	 */
	private function initJewelryInfo()
	{
		$this->m_uid 	 = RPCContext::getInstance()->getUid();
		$this->m_manager = ItemManager::getInstance();
		$this->m_info 	 = JewelryLogic::getJewelryInfo($this->m_uid);
	}
	
	/**
	 * 强化
	 */
	public function reinforce($item_id, $cnt)
	{
		//功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::JEWELRY_REINFORCE))
		{
			Logger::warning('Jewelry.reinforce err Jewelry is not open');
			throw new Exception('fake');
		}
		
		$ret = array('ret' => 'err', 'elecost' => 0, 'reinforce_success' => 0);
		if($cnt <= 0 || $cnt > 10)
		{
			Logger::warning("invalid reinforce cnt");
			return $ret;
		}
		
		$this->initJewelryInfo();
		
		$item_id = intval($item_id);
		$cost_ele = 0;
		$is_rein_success = false;
		
		//检查装备是否属于该用户
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::DEBUG('item_id:%d not belong to me!', $item_id);
			return $ret;
		}

		$item = $this->m_manager->getItem($item_id);
		//检查强化的物品是否为宝物
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_JEWELRY )
		{
			Logger::DEBUG('item_id:%d, not a arm!', $item_id);
			return $ret;
		}
		

		//得到用户对象
		$user = EnUser::getUserObj();
		$reinforceReq = $item->getReinforceReq();
		$bag = BagManager::getInstance()->getBag();
		

		//得到宝物强化等级
		$reinforceLevel = $item->getReinforceLevel();
		//当前玩家可以强化的最大等级
		$reinforceLevel_max = self::getMaxReinforceLvl($user,$item);

		// 支持多次强化
		$op_cnt = $cnt;
		
		do
		{
			// 宝物强化等级限制
			if ( $reinforceLevel >= $reinforceLevel_max )
			{
				Logger::warning("reinforce lvl exceeded,cur:%d/max:%d",$reinforceLevel,$reinforceLevel_max);
				break;
			}
			
			// 当前强化等级的消耗需求
			$curReq = self::getCurReinforceReq($item);
			if(empty($curReq))
			{
				Logger::fatal("can get current reinforce req");
				throw new Exception("fake");
			}
			
			// 消耗
			if(!self::checkReinforceCost($curReq))
			{
				Logger::debug("check jewelry reinforce cost fail");
				break;
			}
			
			$cost_ele += $curReq['costnum'];
			
			// roll
			$rnd = rand(1,JewelryDef::JEWELRY_REINFORCE_ROLL_MAX);
			if($rnd <= $curReq['rate'])
			{
				$is_rein_success = true;
				$reinforceLevel++;
				$item->setReinforceLevel($reinforceLevel);
			}
			
			// 不管成功与否减次数
			$op_cnt--;
			
		}while($op_cnt > 0);
		
		
		if($cost_ele > 0)
		{
			if(Jewelry::addEnergyElement($this->m_uid,0,-$cost_ele))
			{
				//$this->m_manager->update();
				ItemManager::getInstance()->update();
				
				$ret['ret'] = 'ok';
				$ret['elecost'] = $cost_ele;
				$ret['rein_lvl'] = $reinforceLevel;
				$ret['reinforce_success'] = $is_rein_success?1:0;
				
				//通知任务系统
				TaskNotify::operate(TaskOperateType::JEWELRY_REINFORCE);
				
				//战斗优化
				EnUser::modifyBattleInfo();
			}	
		}
		
		return $ret;
	}
	
	
	/**
	 * 检测强化消耗
	 * @param $req 强化需求
	 */
	protected function checkReinforceCost($req)
	{
		$ele_cnt = $this->m_info[JewelryDef::JEWELRY_SQL_ELEMENT];
		if($req['costnum'] > $ele_cnt)
		{
			return false;
		}
		
		return true;
	}

	
	/**
	 * 获取当前强化等级的需求
	 * @param $item 宝物
	 * @return array
	 */
	protected function getCurReinforceReq($item)
	{
		$ret = array();
		
		$rein_lvl = $item->getReinforceLevel();
		$req = $item->getReinforceReq();
		foreach($req as $req_item)
		{
			$ret = $req_item;
			if($req_item['limit'] > $rein_lvl)
			{
				break;
			}
		}
		
		return $ret;
	}
	
	/**
	 * 返回最大可强化等级
	 * @return int
	 */
	protected function getMaxReinforceLvl($user,$item)
	{
		$intval = $item->getReinforceLvlIntval();
		if($intval <= 0)
		{
			Logger::fatal("invalid reinforce lvl");
			throw new Exception("fake");
		}
		
		$lvlMax = intval(floor($user->getLevel()/$intval)*JewelryDef::JEWELRY_REINFORCE_FACTOR);
		if($lvlMax <= 0)
		{
			$lvlMax = 1;
		}
		
		return $lvlMax;
	}
	
	
	
	public function getStrengthInfo()
	{
		$this->initJewelryInfo();
		return $this->m_info[JewelryDef::JEWELRY_SQL_ELEMENT];
	}
	
	
	/**
	 * 洗练
	 */
	public function refresh($item_id, $type,$layers)
	{
		
		//初始化所需信息
		$this->initJewelryInfo();

		//格式化输入
		$item_id = intval($item_id);
		
		//这个物品是不是我的
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::warning('item_id:%d not belong to me!', $item_id);
			return FALSE;
		}
		
		//该物品是不是宝物
		$item = ItemManager::getInstance()->getItem($item_id);
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_JEWELRY )
		{
			Logger::warning('invalid item_id:%d', $item_id);
			return FALSE;
		}
		
		//请求类型对不对
		if (!JewelryLogic::IsFreshTypeOk($type))
		{
			Logger::warning('invalid type:%d', $type);
			return FALSE;
		}
		
		//层数对不对
		if (empty($layers))
		{
			Logger::DEBUG('empty layer');
			return FALSE;
		}
		foreach ($layers as  $layer)
		{
			if ($layer <=0 || $layer> $item->getMaxSealLayer())
			{
				Logger::warning('invalid layer:%d', $layer);
				return FALSE;
			}
		}
		
		//每层所要求的强化等级和开启状态是否满足
		$curreinforcelevel= $item->getReinforceLevel();
		foreach ($layers as $layer)
		{
			$needlevel=$item->getNeedReinforceLevel($layer);
			if ($curreinforcelevel < $needlevel &&
				$item->getLayerSatatus($layer)==JewelryDef::JEWELRY_STATUS_SEAL)
			{
				Logger::warning('reinforcelevel err! layer:%d forcelv:%d curlv:%d', $layer,$curreinforcelevel,$needlevel);
				return FALSE;
			}
			//前一层需要是开启状态
			if ($layer > 1 && $item->getLayerSatatus($layer-1)!=JewelryDef::JEWELRY_STATUS_OPEN)
			{
				Logger::warning('pre level is not open layer:%d', $layer);
				return FALSE;
			}
		}
		
		//得到物品洗练需求
		$reqinfo = $item->getRefreshReq();
		
		//整理这些需求
		$costinfo=JewelryLogic::arrangeFreshCostinfo($type,$reqinfo,$layers);
		
		//检查消耗够不够
		$user = EnUser::getInstance($this->m_uid);
		$bag = BagManager::getInstance()->getBag($this->m_uid);
		$goldcost=$costinfo['gold'];
		if ( $goldcost > 0 && $user->subGold($goldcost) == FALSE )
		{
				Logger::warning('no enough gold!');
				return false;
		}
		$bellycost=$costinfo['belly'];
		if ( $bellycost > 0 && $user->subBelly($bellycost) == FALSE )
		{
				Logger::warning('no enough belly!');
				return false;
		}
		$energycost=$costinfo['energy'];
		$curenergy=$this->m_info[JewelryDef::JEWELRY_SQL_ENERGY];
		if ($curenergy < $energycost)
		{
			Logger::warning('no enough energy!');
			return false;
		}
		if ($energycost > 0 && !JewelryLogic::updateEnergyElement($this->m_uid, $curenergy-$energycost,NULL))
		{
			Logger::warning('updateEnergyElement fail!');
			return false;
		}
		$items=$costinfo['items'];
		foreach ($items as $itemid=>$itemnum)
		{
			if ($itemid > 0 && $itemnum > 0 && $bag->deleteItembyTemplateID($itemid, $itemnum)==FALSE )
			{
				Logger::warning('no enough item!');
				return false;
			}
		}
		
		//洗练
		$ret=$item->doRefresh($layers);
		
		//更新金币和belly信息
		$user->update();
		
		//金币统计
		if ($goldcost)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_JEWELRY_FRESH, $goldcost,  Util::getTime());
		}
		
		//更新物品信息
		ItemManager::getInstance()->update();
		
		//更新背包信息
		$baginfo=$bag->update();
		
		//调用每日任务
		EnDaytask::refreshEquip();
		
		//返回给前端
		$return=array('fresh'=>$ret,'baginfo'=>$baginfo,
				'costgold'=>$goldcost,'costbelly'=>$bellycost,'costenergy'=>$energycost);
		return $return;
	}
	
	/**
	 * 确认洗练
	 */
	public function replace($item_id,$layer)
	{
		$return=array('success'=>false,'sealinfo'=>array(),'freshinfo'=>array());
		
		//格式化输入
		$item_id = intval($item_id);
		
		//这个物品是不是我的
		if ( EnUser::itemBelongTo($item_id) == FALSE )
		{
			Logger::warning('item_id:%d not belong to me!', $item_id);
			return $return;
		}
		
		//该物品是不是宝物
		$item = ItemManager::getInstance()->getItem($item_id);
		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_JEWELRY )
		{
			Logger::warning('invalid item_id:%d', $item_id);
			return $return;
		}
		
		//层数对不对,如果层数为0，则全部替换
		if ($layer <0 || $layer> $item->getMaxSealLayer())
		{
			Logger::warning('invalid layer:%d', $layer);
			return $return;
		}
		
		//确认洗练
		$return= $item->doReplace($layer);
		
		//更新物品信息
		if ($return['success']==true)
		{
			ItemManager::getInstance()->update();
			
			//战斗优化
			EnUser::modifyBattleInfo();
		}
		
		return $return;
	}
	
	public function refreshInfo()
	{
		$this->initJewelryInfo();
		$return =$this->m_info;
		
		//获得当前剩余的vip免费次数
		$curvipnum=$this->m_info[JewelryDef::JEWELRY_SQL_VIP_FREE_NUM];
		$lastviptime=$this->m_info[JewelryDef::JEWELRY_SQL_VIP_TIME];
		$curcount=JewelryLogic::getUserVipFreeNum($this->m_uid,$curvipnum,$lastviptime);
		$return[JewelryDef::JEWELRY_SQL_VIP_FREE_NUM]=$curcount;
		unset($return[JewelryDef::JEWELRY_SQL_VIP_TIME]);
		return $return;
	}
	/**
	 * 把$itemid_a 的封印属性转移到$itemid_b
	 */
	public function sealTransfer($itemid_a,$itemid_b,$type)
	{
		$return=array('success'=>false);
		
		//初始化所需信息
		$this->initJewelryInfo();
		
		//两个物品是不是宝物
		$itema = ItemManager::getInstance()->getItem($itemid_a);
		if ( $itema === NULL || $itema->getItemType() != ItemDef::ITEM_JEWELRY )
		{
			Logger::warning('invalid a item_id:%d', $itemid_a);
			return $return;
		}
		$itemb = ItemManager::getInstance()->getItem($itemid_b);
		if ( $itemb === NULL || $itemb->getItemType() != ItemDef::ITEM_JEWELRY )
		{
			Logger::warning('invalid b item_id:%d', $itemid_b);
			return $return;
		}
		//两个物品的类型一不一样
		if ($itema->getJewelryType() !=$itemb->getJewelryType())
		{
			Logger::warning('invalid type typea:%d typeb:%d' ,
			$itema->getJewelryType(),$itemb->getJewelryType());
			return $return;
		}
		//$itemid_b物品的品质是不是比$itemid_a高
		if ($itemb->getItemQuality() < $itema->getItemQuality())
		{
			Logger::warning('invalid quality qualitya:%d qualityb:%d' ,
			$itema->getItemQuality(),$itemb->getItemQuality());
			return $return;
		}
		//$itemid_a上有没有封印属性
		$sealinfo_a=$itema->getSealInfo();
		if (empty($sealinfo_a))
		{
			Logger::warning('item_a seal info empty');
			return $return;
		}
		
		//检查消耗，vip次数够不够
		$goldcost=0;
		$user = EnUser::getInstance($this->m_uid);
		$bag = BagManager::getInstance()->getBag($this->m_uid);
		if ($type==JewelryDef::JEWELRY_SEALTRANSFER_TYPE_FREE)
		{
			$curvipnum=$this->m_info[JewelryDef::JEWELRY_SQL_VIP_FREE_NUM];
			$lastviptime=$this->m_info[JewelryDef::JEWELRY_SQL_VIP_TIME];
			$curcount=JewelryLogic::getUserVipFreeNum($this->m_uid,$curvipnum,$lastviptime);
			if ($curcount <=0)
			{
				Logger::warning('not enough vip free num:%d',$curcount);
				return $return;
			}
			//扣一次vip次数
			if (!JewelryLogic::updateVipFreeNum($this->m_uid, $curvipnum+1,Util::getTime()))
			{
				Logger::warning('updateVipFreeNum err');
				return $return;
			}
		}
		elseif($type==JewelryDef::JEWELRY_SEALTRANSFER_TYPE_GOLD)
		{
			$curgold=$user->getGold();
			$viplevel=$user->getVip();
			$goldcost=btstore_get()->VIP[$viplevel]['Seal_freeFreshNum']['gold'];
			if ( $goldcost > 0 && $user->subGold($goldcost) == FALSE )
			{
				Logger::warning('not enough gold cur:%d cost:%d',$curgold,$goldcost);
				return $return;
			}
		}
		elseif($type==JewelryDef::JEWELRY_SEALTRANSFER_TYPE_ITEM)
		{
			$viplevel=$user->getVip();
			$itemid=btstore_get()->VIP[$viplevel]['Seal_freeFreshNum']['itemid'];
			if ($itemid > 0 && $bag->deleteItembyTemplateID($itemid, 1)==FALSE )
			{
				Logger::warning('not enough item ');
				return $return;
			}
		}
		else
		{
			Logger::warning('invalid type:%d',$type);
			return $return;
		}
		
		//不管哪种转移，都要消耗贝里和能量石
		//需要的能量石和贝里数量为宝物A每层封印属性用能量石洗练方式各洗练1次的总和
		$layers=array();
		foreach ($sealinfo_a as $layer=>$id)
		{
			$layers[]=$layer;
		}
		$reqinfo = $itema->getRefreshReq();
		$costinfo=JewelryLogic::arrangeFreshCostinfo(JewelryDef::JEWELRY_REFRESH_TYPE_ENERGY,$reqinfo,$layers);
		$bellycost=$costinfo['belly'];
		if ( $bellycost > 0 && $user->subBelly($bellycost) == FALSE )
		{
			Logger::warning('no enough belly!');
			return false;
		}
		$energycost=$costinfo['energy'];
		$curenergy=$this->m_info[JewelryDef::JEWELRY_SQL_ENERGY];
		if ($curenergy < $energycost)
		{
			Logger::warning('no enough energy!');
			return false;
		}
		if ($energycost > 0)
		{
			JewelryLogic::updateEnergyElement($this->m_uid, $curenergy-$energycost,NULL);
		}
		
		//执行洗练操作
		$ret=$itemb->doSealTransfer($sealinfo_a);
		
		//洗完后，原来宝物的封印属性要置空
		$itema->setSealInfo(array());
		
		//更新
		$user->update();
		ItemManager::getInstance()->update();
		$baginfo=$bag->update();
		
		//金币统计
		if ($goldcost)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_JEWELRY_SEAL_TRANSFER, $goldcost,  Util::getTime());
		}
		
		//返回给前端
		$reutrn['success']=true;
		$reutrn['seal_info_a']=array();
		$reutrn['seal_info_b']=$ret;
		$reutrn['baginfo']=$baginfo;
		$reutrn['gold']=$goldcost;
		$reutrn['belly']=$bellycost;
		$reutrn['energy']=$energycost;
		return $reutrn;
	}
	
	/**
	 * 增加元素石能量石
	 * @param int $uid
	 * @param int $val
	 */
	public static function  addEnergyElement($uid,$energy,$element)
	{
		$curuid	 = RPCContext::getInstance()->getUid();
		if ($curuid == $uid)
		{
			$obj=new Jewelry();
			return $obj->updateEnergyElement($uid,$energy,$element);
		}
		else
		{
			//扔到用户进程执行
			RPCContext::getInstance()->executeTask($uid,'jewelry.updateEnergyElement',array($uid,$energy,$element));
		}
		return true;
	}
	public  function updateEnergyElement($uid,$energy,$element)
	{
		$this->m_uid  = $uid;
		$this->m_info = JewelryLogic::getJewelryInfo($this->m_uid );
		
		$oldenergy=$this->m_info[JewelryDef::JEWELRY_SQL_ENERGY];
		$newengergy=$oldenergy+$energy;
		$newengergy= ($newengergy==$oldenergy)?NULL:$newengergy;
		
		$oldelement=$this->m_info[JewelryDef::JEWELRY_SQL_ELEMENT];
		$newelement=$oldelement+$element;
		$newelement= ($newelement==$oldelement)?NULL:$newelement;
		
		return JewelryLogic::updateEnergyElement($this->m_uid, $newengergy,$newelement);
	}
	
	private static function getRebornNum($item)
	{
		$num = 0;
		
		$rein_lvl = $item->getReinforceLevel();
		$req = $item->getReinforceReq();
		foreach($req as $req_item)
		{
			if($req_item['limit'] > $rein_lvl)
			{
				break;
			}			
			$num += $req_item['costnum'];
		}
		
		return $num;
	}
	
	public function reBrith($item_id)
	{
		$return = array('success' => false, 'elementsStone' => 0);

		$this->initJewelryInfo();

		$item_id = intval($item_id);
		if (EnUser::itemBelongTo($item_id) == false) {
			Logger::debug('item_id: %d not belong to me!', $item_id);
			return $return;
		}
		
		$item = $this->m_manager->getItem($item_id);
		$num = self::getRebornNum($item);
		$item->reBrith();

		Jewelry::addEnergyElement($this->m_uid, 0, $num);
			$this->m_manager->update();

				$return['success'] = true;
				$return['elementsStone'] = $num;
				$return['item_text'] = $item->itemInfo();
		return $return;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */