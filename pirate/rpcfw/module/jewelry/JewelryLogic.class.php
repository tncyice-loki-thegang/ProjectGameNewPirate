<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: JewelryLogic.class.php 40808 2013-03-15 06:35:18Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/jewelry/JewelryLogic.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-03-15 14:35:18 +0800 (五, 2013-03-15) $
 * @version $Revision: 40808 $
 * @brief 
 *  
 **/

class JewelryLogic
{
	/**
	 * 获得玩家的信息
	 * @param unknown_type $uid
	 */
	public static function getJewelryInfo($uid)
	{
		$ret=array();
		$wheres  =array(array (JewelryDef::JEWELRY_SQL_UID, '=', $uid));
		$selectfield = array(  JewelryDef::JEWELRY_SQL_ELEMENT,
							   JewelryDef::JEWELRY_SQL_ENERGY,
							   JewelryDef::JEWELRY_SQL_VIP_FREE_NUM,
							   JewelryDef::JEWELRY_SQL_VIP_TIME);
		$ret= JewelryDao::getInfo($selectfield, $wheres);
		
		//若为空则初始化
		if (empty($ret)) 
		{
			$arryfiled = array(
				JewelryDef::JEWELRY_SQL_UID =>$uid,
				JewelryDef::JEWELRY_SQL_ENERGY => 0,
				JewelryDef::JEWELRY_SQL_ELEMENT => 0,
				JewelryDef::JEWELRY_SQL_VIP_FREE_NUM=>0,
				JewelryDef::JEWELRY_SQL_VIP_TIME=>0);
			JewelryDao::insertInfo($arryfiled);
			unset($arryfiled[JewelryDef::JEWELRY_SQL_UID]);
			$ret=$arryfiled;
		}
		else
		{
			$ret=$ret[0];
			
			//vip免费次数更新
			$lasttime=$ret[JewelryDef::JEWELRY_SQL_VIP_TIME];
			if ($lasttime > 0 && !Util::isSameWeek($lasttime))
			{
				JewelryLogic::updateVipFreeNum($uid, 0,0);
				$ret[JewelryDef::JEWELRY_SQL_VIP_FREE_NUM]=0;
				$ret[JewelryDef::JEWELRY_SQL_VIP_TIME]=0;
			}
		}
		$info = AppleFactoryLogic::getInfo($uid);
		$ret['emoyinzi'] = $info['demon_kernel'];
		return $ret;
	}
	
	/**
	 * 检查洗练类型对不对
	 * @param unknown_type $type
	 * @return boolean
	 */
	public static function IsFreshTypeOk($type)
	{
		$ret=false;
		switch ($type) {
			case JewelryDef::JEWELRY_REFRESH_TYPE_GOLD:
			case JewelryDef::JEWELRY_REFRESH_TYPE_ENERGY:
			case JewelryDef::JEWELRY_REFRESH_TYPE_ITEM:
		    	$ret=true;
				break;
			default:
				$ret=false;
			break;
		}
		return $ret;
	}
	
	/**
	 * 整理洗练时需要消耗的信息
	 */
	public static function arrangeFreshCostinfo($type,$reqinfo,$layers)
	{
		$return=array('gold'=>0,'belly'=>0,'energy'=>0,'items'=>array());
		$gold=$reqinfo[ItemDef::ITEM_ATTR_JEWELRY_GOLDSMITHCOST];
		$energy=$reqinfo[ItemDef::ITEM_ATTR_JEWELRY_ENERGYSMITHCOSET];
		$item=$reqinfo[ItemDef::ITEM_ATTR_JEWELRY_ITEMSMITHCOSET];
		
		foreach ($layers as $layer)
		{
			if (!isset($gold[$layer]))
			{
				Logger::FATAL('JewelryLogic.arrangeFreshCostinfo invalid gold type:%d layer:%d ',$type,$layer);
				throw new Exception('fake');
			}
			if (!isset($energy[$layer]))
			{
				Logger::FATAL('JewelryLogic.arrangeFreshCostinfo invalid energy type:%d layer:%d ',$type,$layer);
				throw new Exception('fake');
			}
			if (!isset($item[$layer]))
			{
				Logger::FATAL('JewelryLogic.arrangeFreshCostinfo invalid item type:%d layer:%d ',$type,$layer);
				throw new Exception('fake');
			}
			
			if ($type==JewelryDef::JEWELRY_REFRESH_TYPE_GOLD)
			{
				$info=$gold[$layer];
				$goldcost=$info['gold'];$bellycost=$info['belly'];
				$return['gold']+=$goldcost;$return['belly']+=$bellycost;
			}
			elseif($type==JewelryDef::JEWELRY_REFRESH_TYPE_ENERGY)
			{
				$info=$energy[$layer];
				$energycost=$info['energy'];$bellycost=$info['belly'];
				$return['energy']+=$energycost;$return['belly']+=$bellycost;
			}
			elseif($type==JewelryDef::JEWELRY_REFRESH_TYPE_ITEM)
			{
				$info=$item[$layer];
				$itemid=$info['itemid'];$itemnum=$info['itemnum'];
				if (!isset($return['items'][$itemid]))
				{
					$return['items'][$itemid]=$itemnum;
				}
				 else 
				 {
				 	$return['items'][$itemid]+=$itemnum;
				 }
			}
		}
		
		return $return;
	}
	
	/**
	 * 增加能量石或元属石
	 * @param int $uid
	 * @param int $energy
	 * @param int $element
	 */
	public static function updateEnergyElement($uid,$energy,$element)
	{
		$set=array();
		if (!($energy===NULL))
		{
			$energy= ($energy< 0)?0:$energy;
			$set[JewelryDef::JEWELRY_SQL_ENERGY]=$energy;
		}
		if (!($element===NULL))
		{
			$element= ($element< 0)?0:$element;
			$set[JewelryDef::JEWELRY_SQL_ELEMENT]=$element;
		}
		if (empty($set))
		{
			Logger::warning('JewelryLogic.updateEnergyElement empty set');
			return false;
			throw new Exception('fake');
		}
		
		$wheres  =array(array (JewelryDef::JEWELRY_SQL_UID, '=',$uid));
		return JewelryDao::updateInfo($set, $wheres);
	}
	
	/**
	 * 更新vip次数
	 */
	public static  function updateVipFreeNum($uid,$num,$time)
	{
		$set=array(JewelryDef::JEWELRY_SQL_VIP_FREE_NUM =>$num,
				   JewelryDef::JEWELRY_SQL_VIP_TIME =>$time);
		$wheres  =array(array (JewelryDef::JEWELRY_SQL_UID, '=',$uid));
		return JewelryDao::updateInfo($set, $wheres);
	}
	
	/**
	 * 获得玩家当前可用的vip免费次数
	 */
	public static  function getUserVipFreeNum($uid,$curnum,$lasttime)
	{
		$user = EnUser::getInstance($uid);
		$viplevel=$user->getVip();
		
		$vipConf = btstore_get()->VIP;
		$weekmaxnum = $vipConf[$viplevel]['Seal_freeFreshNum']['free_num'];
		if (($weekmaxnum > 0 && $curnum >= $weekmaxnum) ||$weekmaxnum==0 )
		{
			return 0;
		}
		return $weekmaxnum- $curnum;
	}
	
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */