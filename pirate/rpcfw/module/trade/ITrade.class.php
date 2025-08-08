<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ITrade.class.php 15302 2012-02-29 14:31:17Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/trade/ITrade.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-02-29 22:31:17 +0800 (三, 2012-02-29) $
 * @version $Revision: 15302 $
 * @brief
 *
 **/

interface ITrade
{
	/**
	 *
	 * 得到购回信息
	 *
	 *
	 * @return array
	 * <code>
	 * [
	 * 		sell_time:int									出售时间
	 * 		item_info:array
	 * 		[
	 * 			item_id:int									物品ID
	 * 			item_template_id:int						物品模板ID
	 * 			item_num:int								物品数量
	 * 			item_time:int								物品产生时间
	 * 			va_item_text:								物品扩展信息
	 * 			[
	 * 				mixed
	 * 			]
	 *		]
	 * ]
	 * </code>
	 */
	public function repurchaseInfo();

	/**
	 *
	 * 得到出售者的信息
	 *
	 * @param int $seller_id			出售者ID(NPCID)
	 *
	 * @return array
	 * <code>
	 *	[
	 *		shop_place_id				位置ID
	 *		bought_num					已经售出几个
	 *		refresh_time				下次刷新时间
	 *	]
	 * </code>
	 */
	public function sellerInfo($seller_id);

	/**
	 *
	 * 购买
	 *
	 * @param int $seller_id			出售者ID
	 * @param int $shop_place_id		位置ID
	 * @param int $item_num				购买数量
	 *
	 * @return array					空数组表示购买失败
	 * <code>
	 * [
	 * ]
	 * or
	 * [
	 * 		gid:
	 * 				[
	 * 					item_id:int						物品ID
	 * 					item_template_id:int			物品模板ID
	 * 					item_num:int					物品数量
	 * 					item_time:int					物品产生时间
	 * 					va_item_text:					物品扩展信息
	 * 					[
	 * 						mixed
	 * 					]
	 * 				]
	 * ]
	 * </code>
	 */
	public function buy($seller_id, $shop_place_id, $item_num);

	/**
	 *
	 *  出售
	 *
	 * @param int $gid						背包格子id
	 * @param int $item_id					物品id
	 * @param int $item_num					物品数量
	 *
	 * @return array						如果是array()表示失败
	 * <code>
	 * {
	 * 		sell_time:int									出售时间
	 * 		item_info:array
	 * 		[
	 * 			item_id:int									物品ID
	 * 			item_template_id:int						物品模板ID
	 * 			item_num:int								物品数量
	 * 			item_time:int								物品产生时间
	 * 			va_item_text:								物品扩展信息
	 * 			[
	 * 				mixed
	 * 			]
	 *		]
	 * }
	 * </code>
	 *
	 */
	public function sell($gid, $item_id, $item_num);

	/**
	 *
	 * 购回
	 *
	 * @param int $item_id
	 *
	 * @return array
	 * <code>
	 * [
	 * 		gid:
	 * 				[
	 * 					item_id:int						物品ID
	 * 					item_template_id:int			物品模板ID
	 * 					item_num:int					物品数量
	 * 					item_time:int					物品产生时间
	 * 					va_item_text:					物品扩展信息
	 * 					[
	 * 						mixed
	 * 					]
	 * 				]
	 * ]
	 * </code>
	 */
	public function repurchase($item_id);
}
/* vim: set  ts=4 sw=4 sts=4 tw=100 noet: */