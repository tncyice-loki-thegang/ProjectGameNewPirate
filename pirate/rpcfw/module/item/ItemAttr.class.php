<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ItemAttr.class.php 18734 2012-04-16 12:04:24Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/ItemAttr.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-04-16 20:04:24 +0800 (一, 2012-04-16) $
 * @version $Revision: 18734 $
 * @brief
 *
 **/

class ItemAttr
{
	/**
	 *
	 * 得到物品的属性
	 * @param int $item_template_id
	 * @param string $attr
	 *
	 * @throws Exception 			如果物品不存在这个属性，则会抛出异常
	 *
	 * @return mixed				物品的属性
	 */
	static public function getItemAttr($item_template_id, $attr)
	{
		//得到item的数据
		if ( !isset(btstore_get()->ITEMS[$item_template_id]) )
		{
			Logger::FATAL("invalid item_tempalte_id=%d", $item_template_id);
			throw new Exception('config');
		}

		$item = btstore_get()->ITEMS[$item_template_id];
		if ( isset($item[$attr]) )
		{
			return $item[$attr];
		}
		else
		{
			Logger::FATAL("Access invalid attibute!item_tempalte_id=%d, attribute=%s",
							$item_template_id, $attr);
			throw new Exception('fake');
		}
	}

	/**
	 *
	 * 得到物品的属性
	 * @param int $item_template_id
	 * @param array(string) $attr
	 *
	 * @throws Exception 			如果物品不存在这个属性，则会抛出异常
	 *
	 * @return mixed				物品的属性
	 */
	static public function getItemAttrs($item_template_id, $attrs)
	{
		if ( !isset(btstore_get()->ITEMS[$item_template_id]) )
		{
			Logger::FATAL("invalid item_tempalte_id=%d", $item_template_id);
			throw new Exception('config');
		}

		$item = btstore_get()->ITEMS[$item_template_id];
		$attrInfos = array();
		foreach ( $attrs as $attr )
		{
			if ( isset($item[$attr]) )
			{
				$attrInfos[$attr] = $item[$attr];
			}
			else
			{
				Logger::FATAL("Access invalid attibute!item_tempalte_id=%d, attribute=%s",
							 $item_template_id, $attr);
				throw new Exception('fake');
			}
		}
		return $attrInfos;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */