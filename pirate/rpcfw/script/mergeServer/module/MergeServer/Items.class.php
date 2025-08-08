<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Items.class.php 38797 2013-02-20 08:52:51Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/mergeServer/module/MergeServer/Items.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2013-02-20 16:52:51 +0800 (三, 2013-02-20) $
 * @version $Revision: 38797 $
 * @brief
 *
 **/

class Items
{
	/**
	 *
	 * 得到某个用户所有探索里的宝石ID
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function explore2item($data)
	{
		return $data['va_explore']['items'];
	}

	/**
	 *
	 * 得到某个英雄相关的物品
	 * 有恶魔果实、装备
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function hero2item($data)
	{
		$return = array();
		if ( isset($data['va_hero']['daimonApple']) && is_array($data['va_hero']['daimonApple']) )
		{
			foreach ($data['va_hero']['daimonApple'] as $key => $value)
			{
				if ( $value!='0' )
				{
					$return[] = $value;
				}
			}
		}
		if ( isset($data['va_hero']['arming']) && is_array($data['va_hero']['arming']) )
		{
			foreach ($data['va_hero']['arming'] as $key => $value)
			{
				if ( $value!='0' )
				{
					$return[] = $value;
				}
			}
		}
		if ( isset($data['va_hero']['dress']) && is_array($data['va_hero']['dress']) )
		{
			foreach ($data['va_hero']['dress'] as $key => $value)
			{
				if ( $value!='0' )
				{
					$return[] = $value;
				}
			}
		}
		if ( isset($data['va_hero']['jewelry']) && is_array($data['va_hero']['jewelry']) )
		{
			foreach ($data['va_hero']['jewelry'] as $key => $value)
			{
				if ( $value!='0' )
				{
					$return[] = $value;
				}
			}
		}
		return $return;
	}

	/**
	 *
	 * 得到某个背包格子里所相关的物品id
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function bag2item($data)
	{
		return array($data['item_id']);
	}

	/**
	 *
	 * 得到某个物品所关联的其他物品
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function item2item($data)
	{
		if ( isset($data['va_item_text']['arm_enchanse']) && is_array($data['va_item_text']['arm_enchanse']) )
		{
			$return = array();
			foreach ( $data['va_item_text']['arm_enchanse'] as $hole_id => $item_id )
			{
				if ( !empty($item_id) )
				{
					$return[] = $item_id;
				}
			}
			return $return;
		}
		else
		{
			return array();
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */