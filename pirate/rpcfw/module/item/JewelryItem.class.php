<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: JewelryItem.class.php 40386 2013-03-09 09:06:47Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/JewelryItem.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-03-09 17:06:47 +0800 (六, 2013-03-09) $
 * @version $Revision: 40386 $
 * @brief 
 *  
 **/

//宝物
class JewelryItem extends Item
{
	public function reBrith()
	{
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL]=0;
		$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL]=array();
		$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_FRESH]=array();
	}
	
	public function getJewelryType()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_JEWELRY_EQUIP_TYPE);
	}
	public function equipReq()
	{
		return ItemAttr::getItemAttrs($this->m_item_template_id,
				array(ItemDef::ITEM_ATTR_NAME_HERO_LEVEL, ItemDef::ITEM_ATTR_NAME_REBIRTH_NUM));
	}
	public function info()
	{
		$return=array();
		
		//宝物本体总生命=（宝物生命+宝物生命成长*宝物强化等级）*（1+宝物本体属性总加成/10000）
		//其他属性的计算与该公式类似
		 $attrs= ItemAttr::getItemAttrs($this->m_item_template_id,array(
				ItemDef::ITEM_ATTR_JEWELRY_BASELIFE,
				ItemDef::ITEM_ATTR_JEWELRY_BASEPHYATT,
				ItemDef::ITEM_ATTR_JEWELRY_BASEKILLATT,
				ItemDef::ITEM_ATTR_JEWELRY_BASEMAGATT,
				ItemDef::ITEM_ATTR_JEWELRY_BASEPHYDEF,
				ItemDef::ITEM_ATTR_JEWELRY_BASEKILLDEF,
				ItemDef::ITEM_ATTR_JEWELRY_BASEMAGDEF,
				ItemDef::ITEM_ATTR_JEWELRY_LIFEPL,
				ItemDef::ITEM_ATTR_JEWELRY_PHYATTPL,
				ItemDef::ITEM_ATTR_JEWELRY_KILLATTPL,
				ItemDef::ITEM_ATTR_JEWELRY_MAGATTPL,
				ItemDef::ITEM_ATTR_JEWELRY_PHYDEFPL,
				ItemDef::ITEM_ATTR_JEWELRY_KILLDEFPL,
				ItemDef::ITEM_ATTR_JEWELRY_MAGDEFPL));
		
		 //宝物本体属性总价成
		 $text=$this->m_item_text;$allrate=0;
		 if (isset($text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL]))
		 {
		 	foreach ($text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL] as $layer => $id)
		 	{
		 		$allrate+=btstore_get()->JEWELRY_SEAL[$id][ItemDef::ITEM_ATTR_JEWELRYSEAL_NOUN_ADDRATE];
		 	}
		 }
		 //强化等级
		 $reinforcelevel=$this->getReinforceLevel();
		 
		 //觉醒的属性也要加上
		 $ids=$this->getWakeProperties();$wakeids=array();
		 $needlevels=$this->getWakeUpReinforceLevels();
		 foreach ($ids as $index=>$id)
		 {
		 	//对应的层需要是开启状态
		 	if ($this->getLayerSatatus($needlevels[$index])==JewelryDef::JEWELRY_STATUS_OPEN &&
		 		$id > 0 && isset(btstore_get()->JEWELRY_SEAL[$id]))
		 	{
		 		$wakeids[]=$id;
		 		$allrate+=btstore_get()->JEWELRY_SEAL[$id][ItemDef::ITEM_ATTR_JEWELRYSEAL_NOUN_ADDRATE];
		 	}
		 }
		
		 //计算宝物本体生命
		 $return[ItemDef::ITEM_ATTR_NAME_HP]=
		 		($attrs[ItemDef::ITEM_ATTR_JEWELRY_BASELIFE]+$attrs[ItemDef::ITEM_ATTR_JEWELRY_LIFEPL]*$reinforcelevel)*(1+$allrate/10000);
		 //计算宝物本体物理攻击
		 $return[ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK]=
		 ($attrs[ItemDef::ITEM_ATTR_JEWELRY_BASEPHYATT]+$attrs[ItemDef::ITEM_ATTR_JEWELRY_PHYATTPL]*$reinforcelevel)*(1+$allrate/10000);
		 //计算宝物本体必杀攻击
		 $return[ItemDef::ITEM_ATTR_NAME_KILL_ATTACK]=
		 ($attrs[ItemDef::ITEM_ATTR_JEWELRY_BASEKILLATT]+$attrs[ItemDef::ITEM_ATTR_JEWELRY_KILLATTPL]*$reinforcelevel)*(1+$allrate/10000);
		 //计算宝物本体魔法攻击
		 $return[ItemDef::ITEM_ATTR_NAME_MAGIC_ATTACK]=
		 ($attrs[ItemDef::ITEM_ATTR_JEWELRY_BASEMAGATT]+$attrs[ItemDef::ITEM_ATTR_JEWELRY_MAGATTPL]*$reinforcelevel)*(1+$allrate/10000);
		 //计算宝物本体物理防御
		 $return[ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE]=
		 ($attrs[ItemDef::ITEM_ATTR_JEWELRY_BASEPHYDEF]+$attrs[ItemDef::ITEM_ATTR_JEWELRY_PHYDEFPL]*$reinforcelevel)*(1+$allrate/10000);
		 //计算宝物本体必杀防御
		 $return[ItemDef::ITEM_ATTR_NAME_KILL_DEFENCE]=
		 ($attrs[ItemDef::ITEM_ATTR_JEWELRY_BASEKILLDEF]+$attrs[ItemDef::ITEM_ATTR_JEWELRY_KILLDEFPL]*$reinforcelevel)*(1+$allrate/10000);
		 //计算宝物本体魔法防御
		 $return[ItemDef::ITEM_ATTR_NAME_MAGIC_DEFENCE]=
		 ($attrs[ItemDef::ITEM_ATTR_JEWELRY_BASEMAGDEF]+$attrs[ItemDef::ITEM_ATTR_JEWELRY_MAGDEFPL]*$reinforcelevel)*(1+$allrate/10000);
		 
		 //计算开启了封印的属性
		 $sealinfo=array();
		 if (isset($this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL]) )
		 {
		 	$sealinfo=$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL];
		 }
		 foreach ($sealinfo as $layer =>$sealid)
		 {
		 	$attr_id=btstore_get()->JEWELRY_SEAL[$sealid][ItemDef::ITEM_ATTR_JEWELRYSEAL_AFFIXID];
		 	$attr_value=btstore_get()->JEWELRY_SEAL[$sealid][ItemDef::ITEM_ATTR_JEWELRYSEAL_AFFIXVALUE];
		 	if (!isset(ItemDef::$ITEM_ATTR_IDS[$attr_id]))
		 	{
		 		continue;
		 	}
		 	$attr_name = ItemDef::$ITEM_ATTR_IDS[$attr_id];
		 	if ( isset($return[$attr_name]) )
		 	{
		 		$return[$attr_name] += $attr_value;
		 	}
		 	else
		 	{
		 		$return[$attr_name] = $attr_value;
		 	}
		 }
		 
		 //计算觉醒属性
		 foreach ($wakeids as $sealid)
		 {
		 	$attr_id=btstore_get()->JEWELRY_SEAL[$sealid][ItemDef::ITEM_ATTR_JEWELRYSEAL_AFFIXID];
		 	$attr_value=btstore_get()->JEWELRY_SEAL[$sealid][ItemDef::ITEM_ATTR_JEWELRYSEAL_AFFIXVALUE];
		 	if (!isset(ItemDef::$ITEM_ATTR_IDS[$attr_id]))
		 	{
		 		continue;
		 	}
		 	$attr_name = ItemDef::$ITEM_ATTR_IDS[$attr_id];
		 	if ( isset($return[$attr_name]) )
		 	{
		 		$return[$attr_name] += $attr_value;
		 	}
		 	else
		 	{
		 		$return[$attr_name] = $attr_value;
		 	}
		 }
		 
		 return $return;
	}
	/**
	 * 产生物品
	 * @param int $item_template_id		物品模板ID
	 *
	 * @return attrs 物品模板所指定的随机属性
	 */
	public static function createItem($item_template_id)
	{
		$item_text = array(ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL=>0,
						   ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL=>array(),
						   ItemDef::ITEM_ATTR_JEWELRY_TEXT_FRESH=>array());
		//随机初始属性
		$infos=ItemAttr::getItemAttrs($item_template_id,array(
				ItemDef::ITEM_ATTR_JEWELRY_INITMAXRANDSEALATTRNUM,
				ItemDef::ITEM_ATTR_JEWELRY_INITATTRRATES));
		$maxlayer=$infos[ItemDef::ITEM_ATTR_JEWELRY_INITMAXRANDSEALATTRNUM];
		$ratenums=$infos[ItemDef::ITEM_ATTR_JEWELRY_INITATTRRATES];
		
		//随机一下看能开启几层
		$randlayer=array();
		foreach ($ratenums as $index=>$val)
		{
			if ($val['rate'] > 0)
			{
				$randlayer[$index]=array('weight'=>$val['rate']);
			}
		}
		//如果为空，说明该物品没有指定初始随机属性，直接返回
		if (empty($randlayer))
		{
			return $item_text;
		}
		$randval=Util::noBackSample($randlayer,1);
		$randindex=$randval[0];
		$layer=$ratenums[$randindex]['num'];
		$layer= $layer>$maxlayer?$maxlayer:$layer;
		$initlayers=array();
		for ($i=1;$i<=$layer;$i++)
		{
			$initlayers[]=intval($i);
		}
		
		//随机属性
		if (!empty($initlayers))
		{
			$ret=JewelryItem::doLayersRefresh($item_template_id,$item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_FRESH],$initlayers);
			$item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL]=$ret;
		}
		return $item_text;
	}
	
	/**
	 * 获得装备洗练时对应类型的消耗信息
	 * @param array $layers
	 */
	public function getRefreshReq()
	{
		return ItemAttr::getItemAttrs($this->m_item_template_id,
				array(ItemDef::ITEM_ATTR_JEWELRY_GOLDSMITHCOST, 
					  ItemDef::ITEM_ATTR_JEWELRY_ENERGYSMITHCOSET,
					  ItemDef::ITEM_ATTR_JEWELRY_ITEMSMITHCOSET));
	}
	
	/**
	 * 底层方法，提供给本类使用
	 */
	public static function doLayersRefresh($item_template_id,$oldfresh,$layers)
	{
		$item_text=$oldfresh;
		foreach ($layers as $layer)
		{
			$fieldrate=ItemDef::ITEM_ATTR_JEWELRY_XILIAN_RATES_ . $layer;//获得具体哪一层的数据
			$aryrate=ItemAttr::getItemAttrs($item_template_id,array($fieldrate ));
			if (empty($aryrate))
			{
				continue;
			}
			$rates=array();$index=0;
			foreach ($aryrate[$fieldrate] as $rate)
			{
				$rates[$index++]=array('weight'=>$rate);
			}
			//先对这一层随机出一个大的组
			$randindex=Util::noBackSample($rates,1);
			$groupindex=$randindex[0];
			$fieldid=ItemDef::ITEM_ATTR_JEWELRY_XILIAN_IDS_ . $layer;//获得具体哪一层的数据
			$retval=ItemAttr::getItemAttrs($item_template_id,array($fieldid));
			$aryids=$retval[$fieldid];
			if (!isset($aryids[$groupindex]))
			{
				Logger::FATAL("JewelryItem.doLayersRefresh!item_tempalte_id:%d, index:%d",$item_template_id, $groupindex);
				throw new Exception('fake');
			}
			$ids=$aryids[$groupindex];
				
			//再从这一组里按照权重随机出一个id
			$newids=array();
			foreach ($ids as $id)
			{
				$weight=btstore_get()->JEWELRY_SEAL[$id][ItemDef::ITEM_ATTR_JEWELRYSEAL_PROPERTY_RATE];
				$newids[$id]=array('weight'=>$weight);
			}
			$randindex=Util::noBackSample($newids,1);
			$newid=$randindex[0];
			$item_text[$layer]=$newid;
		}
		return $item_text;
	}
	
	/**
	 * 对各个层，执行洗练操作,提供给上层使用
	 */
	public function doRefresh($layers)
	{
		$oldfresh=$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_FRESH];
		$info=JewelryItem::doLayersRefresh($this->m_item_template_id,$oldfresh,$layers);
		$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_FRESH]=$info;
		return $this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_FRESH];
	}
	
	/**
	 * 获取强化需求信息
	 * @return array
	 */
	public function getReinforceReq()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_JEWELRY_STRENTHPROPERTY);
	}
	
	
	/**
	 * 获取强化等级间隔
	 * @return int
	 */
	public function getReinforceLvlIntval()
	{
		return ItemAttr::getItemAttr($this->m_item_template_id, ItemDef::ITEM_ATTR_JEWELRY_STRENTHSPACE);
	}
	
	
	/**
	 * 替换洗练的属性
	 * @param int $layer
	 */
	public function doReplace($layer)
	{
		$return=array('success'=>false,'sealinfo'=>array(),'freshinfo'=>array());
		$info=$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_FRESH];
		if ($layer > 0 && !isset($info[$layer]))
		{
			Logger::FATAL("JewelryItem.doReplace!item_tempalte_id:%d, layer:%d info:%s",$this->m_item_template_id, $layer,$info);
			throw new Exception('fake');
			return  $return;
		}
		if (empty($this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL]))
		{
			$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL]=array();
		}
		if ($layer > 0 )
		{
			$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL][$layer]=$info[$layer];
			unset($info[$layer]);
		}
		elseif ($layer == 0)
		{
			foreach ($info as $layer => $id)
			{
				$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL][$layer]=$info[$layer];
			}
			$info=array();
		}
		$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_FRESH]=$info;
		$return['success']=true;
		$return['freshinfo']=$info;
		$return['sealinfo']=$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL];
	
		return $return;
	}
	
	/**
	 * 封印属性转移
	 */
	public function doSealTransfer($oldSealInfo)
	{
		//新宝物对应封印层处于解封状态或者开启状态
		foreach ($oldSealInfo as $layer => $id)
		{
			//if ($this->getLayerSatatus($layer)>=JewelryDef::JEWELRY_STATUS_UNSEAL )
			//{
				$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL][$layer]=$oldSealInfo[$layer];
			//}
		}
		return $this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL];
	}
	
	
	/**
	 * 设置该宝物当前的强化等级
	 */
	public function setReinforceLevel($level)
	{
		$this->m_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL]=$level;
	}
	/**
	 * 获得该宝物当前的强化等级
	 */
	public function getReinforceLevel()
	{
		$info=$this->m_item_text;
		if (empty($info))
		{
			return 0;
		}
		if (!isset($info[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL]))
		{
			return 0;
		}
		return $info[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL];
	}
	
	/**
	 * 得到该宝物的最高封印层数
	 */
	public function getMaxSealLayer()
	{
		$ret= ItemAttr::getItemAttrs($this->m_item_template_id,array(ItemDef::ITEM_ATTR_JEWELRY_MAX_SEALLAYER));
		return $ret[ItemDef::ITEM_ATTR_JEWELRY_MAX_SEALLAYER];
	}
	
	/**
	 * 觉醒属性开启需要封印开启层数数组
	 */
	public function getWakeUpReinforceLevels()
	{
		$ret= ItemAttr::getItemAttrs($this->m_item_template_id,array(ItemDef::ITEM_ATTR_JEWELRY_WAKENEEDSEALOPENNUM));
		if (!isset($ret[ItemDef::ITEM_ATTR_JEWELRY_WAKENEEDSEALOPENNUM]))
		{
			Logger::FATAL("JewelryItem getWakeUpReinforceLevel ret err !item_tempalte_id=%d, ret=%s",$this->m_item_template_id, $ret);
			throw new Exception('fake');
		}
		return $ret[ItemDef::ITEM_ATTR_JEWELRY_WAKENEEDSEALOPENNUM];
	}
	/**
	 * 获得该宝物的觉醒属性ID组
	 */
	public function getWakeProperties()
	{
		$ret= ItemAttr::getItemAttrs($this->m_item_template_id,array(ItemDef::ITEM_ATTR_JEWELRY_WAKEPROPERTIES));
		if (!isset($ret[ItemDef::ITEM_ATTR_JEWELRY_WAKEPROPERTIES]))
		{
			Logger::FATAL("JewelryItem getWakeUpIds ret err !item_tempalte_id=%d, ret=%s",$this->m_item_template_id, $ret);
			throw new Exception('fake');
		}
		return $ret[ItemDef::ITEM_ATTR_JEWELRY_WAKEPROPERTIES];
	}
	
	/**
	 * 当洗练某一层时，需要宝物强化到多少级
	 */
	public function getNeedReinforceLevel($layer)
	{
		$ret= ItemAttr::getItemAttrs($this->m_item_template_id,array(ItemDef::ITEM_ATTR_JEWELRY_OPENSEALNEEDREINFORCE_LV));
		if (!isset($ret[ItemDef::ITEM_ATTR_JEWELRY_OPENSEALNEEDREINFORCE_LV]))
		{
			Logger::FATAL("JewelryItem getNeedReinforceLevel ret err !item_tempalte_id=%d, ret=%s",$this->m_item_template_id, $ret);
			throw new Exception('fake');
		}
		$info=$ret[ItemDef::ITEM_ATTR_JEWELRY_OPENSEALNEEDREINFORCE_LV];
		return  $info[$layer-1];
	}
	
	/**
	 *获得某一层的状态（ 当洗练某一层时，需要前一层为开启状态）
	 */
	public function getLayerSatatus($layer)
	{
	  $reinforcelv=$this->getReinforceLevel();
	  $needlevel= $this->getNeedReinforceLevel($layer);
	  $info=$this->m_item_text;
	  
	  //如果该层已经洗出了属性（创建物品时会随机几个属性），则为开启状态
	  $tmpinfo= $info[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL];
	  if (isset($tmpinfo[$layer]) && $tmpinfo[$layer] > 0)
	  {
	  			return JewelryDef::JEWELRY_STATUS_OPEN;
	  } 
	  //如果没有洗出属性，且强化等级不够，则为封印状态
	  if ($reinforcelv < $needlevel)
	  {
	  	return JewelryDef::JEWELRY_STATUS_SEAL;
	  }
	  //如果强化等级够了，且该层没有属性，则为可解封状态
	  elseif ($reinforcelv >= $needlevel )
	  {
	  	return JewelryDef::JEWELRY_STATUS_UNSEAL;
	  }
	 
	  return JewelryDef::JEWELRY_STATUS_SEAL;
	}
	
	/**
	 * 获得当前已经开启了的所有层所对应的封印属性id
	 */
	public function getCurOpenLayerIds()
	{
		$info=$this->m_item_text;
		if (empty($info))
		{
			return array();
		}
		if (!isset($info[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL]))
		{
			return array();
		}
		$return=array();
		foreach ($info[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL] as $layer=>$attrid)
		{
			$return[]=intval($attrid);
		}
		return $return;
	}
	/**
	 * 获得封印属性信息
	 */
	public function getSealInfo()
	{
		return  $this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL];
	}
	
	/**
	 * 设置封印属性信息
	 */
	public function setSealInfo($val)
	{
		$this->m_item_text[ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL]=$val;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
