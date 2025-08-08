<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IExchange.class.php 38722 2013-02-20 05:52:17Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/exchange/IExchange.class.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-02-20 13:52:17 +0800 (三, 2013-02-20) $
 * @version $Revision: 38722 $
 * @brief
 *
 **/

interface IExchange
{
	/**
	 *
	 * 得到兑换信息
	 *
	 * @return array
	 * <code>
	 * [
	 * 		'exchange_item':array
	 * 		{
	 * 			item_id:int						物品ID
	 * 			item_template_id:int			物品模板ID
	 * 			item_num:int					物品数量
	 * 			item_time:int					物品产生时间
	 * 			va_item_text:array				物品扩展信息
	 * 		}
	 * 		'va_items':array
	 * 		[
	 * 			item_template_id:item_num
	 * 		]
	 * ]
	 * </code>
	 */
	public function getExchangeInfo();

	/**
	 *
	 * 兑换
	 *
	 * @param int $item_id
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'exchange_success':boolean
	 * 		'va_items':array
	 * 		[
	 * 			item_template_id:item_num
	 * 		]
	 * }
	 * </code>
	 */
	public function exchangeItem($item_id);

	/**
	 *
	 * 收取
	 *
	 * @return array 失败返回array()
	 * <code>
	 * [
	 * 		gid:array
	 * 		{
	 * 			item_id:int						物品ID
	 * 			item_template_id:int			物品模板ID
	 * 			item_num:int					物品数量
	 * 			item_time:int					物品产生时间
	 * 			va_item_text:array				物品扩展信息
	 * 		}
	 * ]
	 * </code>
	 */
	public function recieveItem();


	/**
	 *
	 * 兑换宝石
	 *
	 * @param array(int) $src_item_ids			源物品数组(物品ID)
	 * @param int $target_item_template_id		目标物品模板ID
	 *
	 * @return array
	 * {
	 * 		'exchange_success':boolean			是否兑换成功
	 * 		'bag_modify':array
	 * 		{
	 * 			gid:array
	 * 			{
	 * 				item_id:int					物品ID
	 * 				item_template_id:int		物品模板ID
	 * 				item_num:int				物品数量
	 * 				item_time:int				物品产生时间
	 * 				va_item_text:array			物品扩展信息
	 * 			}
	 * 		}
	 * }
	 */
	public function exchangeGemItem($src_item_ids, $target_item_template_id);

	/**
	 *
	 * 兑换装备
	 *
	 * @param int $exchange_id					兑换ID
	 * @param int $src_item_id					源物品ID
	 *
	 * @return array
	 * {
	 * 		'exchange_success':boolean			是否兑换成功
	 * 		'target_item_id':int				目标物品ID
	 * 		'bag_modify':array
	 * 		{
	 * 			gid:array
	 * 			{
	 * 				item_id:int					物品ID
	 * 				item_template_id:int		物品模板ID
	 * 				item_num:int				物品数量
	 * 				item_time:int				物品产生时间
	 * 				va_item_text:array			物品扩展信息
	 * 			}
	 * 		}
	 * }
	 */
	public function exchangeArmItem($exchange_id, $src_item_id);

	/**
	 *
	 * 直接兑换物品
	 *
	 * @param int $exchange_id					兑换ID
	 * @param int $number						数量
	 *
	 * @return array
	 * {
	 * 		'exchange_success':boolean			是否兑换成功
	 * 		'bag_modify':array
	 * 		{
	 * 			gid:array
	 * 			{
	 * 				item_id:int					物品ID
	 * 				item_template_id:int		物品模板ID
	 * 				item_num:int				物品数量
	 * 				item_time:int				物品产生时间
	 * 				va_item_text:array			物品扩展信息
	 * 			}
	 * 		}
	 * }
	 */
	public function exchangeDirectItem($exchange_id, $number);
	
	/**
	 *
	 * 分解宝物
	 *
	 * @param int $item_id
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'exchange_success':boolean          true:分解成功 false:分解失败
	 * 		'items':array
	 * 		[
	 * 			'elementsStone':int				元素石数量
	 * 			'energyStone':int				能量石数量
	 * 		]
	 * }
	 * </code>
	 */
	public function exchangeJewelryItem($item_id);
	
	public function exchangeHorseDecorationItem($item_id);
	
	public function exchangeDaimonAppleItem($item_id);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */