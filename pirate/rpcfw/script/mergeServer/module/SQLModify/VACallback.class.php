<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: VACallback.class.php 38797 2013-02-20 08:52:51Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/mergeServer/module/SQLModify/VACallback.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2013-02-20 16:52:51 +0800 (三, 2013-02-20) $
 * @version $Revision: 38797 $
 * @brief
 *
 **/

class VACallback
{
	public static function explore($data, $va_relative_id_offset)
	{
		$offset = $va_relative_id_offset['item_id'];

		$arrItems = $data['items'];
		foreach ($arrItems as &$item)
		{
			$item += $offset;
		}
		unset($item);
		$data['items'] = $arrItems;
		return $data;
	}

	/**
	 *
	 * 处理Hero的VA字段
	 * 恶魔果实 物品需要修改
	 *
	 * @param array $data
	 * @param array $va_relative_id_offset
	 *
	 * @return array
	 */
	public static function hero($data, $va_relative_id_offset)
	{
		$arrRet = $data;

		$offset = $va_relative_id_offset['item_id'];
		if (isset($arrRet['daimonApple']))
		{
			$dm = $arrRet['daimonApple'];
			foreach ($dm as $k=>&$v)
			{
				if ($v != 0)
				{
					$v += $offset;
				}
			}
			unset($v);
			$arrRet['daimonApple'] = $dm;
		}
		
		if (isset($arrRet['arming']))
		{
			$arming = $arrRet['arming'];
			foreach ($arming as $k=>&$v)
			{
				if ($v != 0)
				{
					$v += $offset;
				}
			}
			unset($v);
			$arrRet['arming'] = $arming;
		}


		if ( isset($arrRet['dress']) )
		{
			$dress = $arrRet['dress'];
			foreach ($dress as $k=>&$v)
			{
				if ($v!=0)
				{
					$v += $offset;
				}
			}
			unset($v);
			$arrRet['dress'] = $dress;
		}
		
		if ( isset($arrRet['jewelry']) )
		{
			$dress = $arrRet['jewelry'];
			foreach ($dress as $k=>&$v)
			{
				if ($v!=0)
				{
					$v += $offset;
				}
			}
			unset($v);
			$arrRet['jewelry'] = $dress;
		}

		return $arrRet;
	}

	/**
	 *
	 * 处理物品的VA字段
	 *
	 * @param array $data
	 * @param array $va_relative_id_offset
	 */
	public static function item($data, $va_relative_id_offset)
	{
		if ( isset($data['arm_enchanse']) && is_array($data['arm_enchanse']) )
		{
			foreach ( $data['arm_enchanse'] as $hole_id => $item_id )
			{
				if ( !empty($item_id) )
				{
					$data['arm_enchanse'][$hole_id] = $item_id + $va_relative_id_offset['item_id'];
				}
			}
		}
		return $data;
	}

	/**
	 *
	 * 处理user的VA字段
	 * 招募英雄顺序表
	 *
	 * @param array $data
	 * @param array $va_relative_id_offset
	 *
	 * @return array
	 */
	public static function user($data, $va_relative_id_offset)
	{
		$arrRet = $data;
		$rctHero = $arrRet['recruit_hero_order'];
		$hidOffset = $va_relative_id_offset['hid'];

		foreach ($rctHero as &$hid)
		{
			$hid += $hidOffset;
		}
		unset($hid);
		$arrRet['recruit_hero_order'] = $rctHero;

		return $arrRet;
	}

	/**
	 *
	 * 处理train的VA字段
	 *
	 * @param array $data
	 * @param array $va_relative_id_offset
	 */
	public static function train($data, $va_relative_id_offset)
	{
		return array();
	}

	/**
	 *
	 * 处理group_battle的VA字段
	 *
	 * @param array $data
	 * @param array $va_relative_id_offset
	 */
	public static function group_battle($data, $va_relative_id_offset)
	{
		unset($data['invite_set']);
		return $data;
	}

	/**
	 *
	 * 处理allblue的VA字段
	 *
	 * @param array $data
	 * @param array $va_relative_id_offset
	 */
	public static function allblue($data, $va_relative_id_offset)
	{
		$allblueVa = $data;
		if(EMPTY($allblueVa))
		{
			return $allblueVa;
		}
		$queueAry = array();
		foreach ($allblueVa as $qId => $qValue)
		{
			// 小偷信息uid
			if(!EMPTY($qValue['thief']))
			{
				$qValue['thief'] = array();
			}
			// 祝福信息uid
			if(!EMPTY($qValue['wisher']))
			{
				$qValue['wisher'] = array();
			}
			$queueAry[$qId] = $qValue;
		}
		return $queueAry;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */