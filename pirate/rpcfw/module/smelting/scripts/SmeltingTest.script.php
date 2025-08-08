<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SmeltingTest.script.php 17794 2012-03-30 12:47:33Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/scripts/SmeltingTest.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-03-30 20:47:33 +0800 (五, 2012-03-30) $
 * @version $Revision: 17794 $
 * @brief 
 *  
 **/
class SmeltingTest extends BaseScript
{
	
	private function startTest($lv, $type, $itemType, $artLv, $times)
	{
		$ret = array();
		// 做很多次
		for ($i = 0; $i < $times; ++$i)
		{
			$dropID = self::getItem($lv, $type, $itemType, $artLv);
			if (isset($ret[$dropID]))
				++$ret[$dropID];
			else
				$ret[$dropID] = 1;
		}
		echo json_encode($ret);
	}

	private function getItem($lv, $type, $itemType, $artLv)
	{
		// 召集所有工匠
		$artificers = array();
		foreach (btstore_get()->ARTIFICER as $art)
		{
			// 将符合等级的工匠纳入
			if ($art['lv'] == $artLv)
			{
				$artificers[]['id'] = $art['id'];
			}
		}
		// 质量初始化
		$quality = 0;
		// 熔炼十次
		for ($i = 0; $i < SmeltingConf::MAX_SMELTING_TIMES; ++$i)
		{
			$quality += self::smeltingOnce($lv, $type, $artificers);
		}
		// 获取到总质量，去查询结果
		$dropID = self::dropItem($itemType, $quality);

//		echo 'All quality is '.$quality.' drop id is '.$dropID.chr(13).chr(10);
//		echo '================================================'.chr(13).chr(10).chr(13).chr(10);
		return $dropID;
	}
	
	private function smeltingOnce($lv, $type, $artificers)
	{
		// 质量初始化
		$quality = 0;
		/**************************************************************************************************************
		 * 获取当前等级所需的参数
 		 **************************************************************************************************************/
		$tmp = array();
		// 循环查看素有的熔炼参数
		foreach (btstore_get()->SMELTING['belly_smelt_bases'] as $base)
		{
			// 如果等级小于当前等级，那么记录下
			if ($base['lv'] > $lv)
			{
				$tmp = $base;
				break;
			}
		}
		// 如果策划配错了，哇哈哈，小俞，这就是证据啊……
		if (empty($tmp))
		{
			echo 'Can not smelt, user level is '.$lv.', go to find xiaoYu!'.chr(13).chr(10);
			return;
		}
		// 保存获取到的基础值
		$quality = $tmp['base'];
		// 如果不是游戏币制作
		if ($type != SmeltingConf::TYPE_BELLY)
		{
			// 查表获取金币制作增加的熔炼值
			$tmp = btstore_get()->VIP[10]['gold_smelt_open'][$type];
			// 这个值需要进行加算
			$quality += $tmp['base'];
		}

		/**************************************************************************************************************
		 * 进行装备制作
 		 **************************************************************************************************************/
		// 初始化暴击值
		$critical = btstore_get()->SMELTING['critical_base'];
		// 初始化暴击倍率
		$criticalRio = btstore_get()->SMELTING['critical_ratio_base'];
		// 计算各种权重
		foreach ($artificers as $artificer)
		{
			// 计算所有增加的概率
			// 计算品质增加值
			$quality += btstore_get()->ARTIFICER[$artificer['id']]['quality_low'];
			$quality += btstore_get()->ARTIFICER[$artificer['id']]['quality_high'];
			// 计算暴击
			$critical += btstore_get()->ARTIFICER[$artificer['id']]['critical_low'];
			$critical += btstore_get()->ARTIFICER[$artificer['id']]['critical_high'];
			// 计算暴击倍率
			$criticalRio += btstore_get()->ARTIFICER[$artificer['id']]['critical_ratio'];
		}
		// 随机出结果 , 判断是否暴击
		$randRet = rand(0, SmeltingConf::LITTLE_WHITE_PERCENT);
		// 查看是否暴击，暴击了以后，乘以暴击倍率神马的
		if ($randRet <= $critical)
		{
			$quality *= (1 + $criticalRio / SmeltingConf::LITTLE_WHITE_PERCENT);
//			echo 'Critical!'.chr(13).chr(10);
		}
//		echo 'Quality is '.$quality.chr(13);
		// 返回上层计算
		return $quality;
	}
	
	// 获取掉落表ID
	private function dropItem($type, $qualityValue)
	{
		// 获取掉落表
		$dropArr = array();
		// 根据参数判断哪个掉落表
		if ($type == SmeltingConf::TYPE_RING)
		{
			// 把戒指的掉落表拿出来
			$dropArr = btstore_get()->RING->toArray();
		}
		else if ($type == SmeltingConf::TYPE_CLOAK)
		{
			// 把披风的掉落表拿出来
			$dropArr = btstore_get()->CLOAK->toArray();
		}
		// 掉落表ID
		$dropID = 0;
		// 根据品质，获取掉落表ID
		foreach ($dropArr as $dropper)
		{
			// 以防万一，先赋值后推出，这样保证能有一个值不会为0 
			$dropID = $dropper['drop_id'];
			// 如果恰巧在区间内
			if ($qualityValue <= $dropper['quality_max'] && $qualityValue >= $dropper['quality_min'])
			{
				// 得到掉落表ID，退出
				break;
			}
		}
		return $dropID;
	}
	
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		return self::startTest($arrOption[0], $arrOption[1], $arrOption[2], $arrOption[3], $arrOption[4]);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */