<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IBag.class.php 31685 2012-11-23 06:18:26Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/bag/IBag.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-11-23 14:18:26 +0800 (五, 2012-11-23) $
 * @version $Revision: 31685 $
 * @brief
 *
 **/

interface IBag
{
	/**
	 *
	 * 背包数据
	 *
	 * @return	mixed			用户UID的背包的数据
	 *
	 * <code>
	 * 	[
	 * 		user_bag:
	 * 		[
	 * 			gid:
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
	 * 		]
	 *		tmp_bag:user_bag							同user_bag
	 * 		mission_bag:user_bag						同user_bag
	 * 		depot_bag:user_bag							同user_bag
	 *		user_bag_grid_start:int						用户背包开始的gid
	 *		tmp_bag_grid_start:int						临时背包开始的gid
	 *		mission_bag_grid_start:int					任务背包开始的gid
	 *		depot_bag_grid_start:int					仓库背包开始的gid
	 *		user_bag_max_grid:int						用户背包的当前最大值
	 *		depot_bag_max_grid:int						仓库背包的当前最大值
	 *		user_bag_grid_num:int						用户背包系统最大值
	 *		tmp_bag_grid_num:int						临时背包系统最大值
	 *		mission_bag_grid_num:int					任务背包系统最大值
	 *		depot_bag_grid_num:int						仓库背包系统最大值
	 *		tmp_bag_expire_time:int						临时背包物品过期时间
	 *  ]
	 * </code>
	 *
	 */
	public function bagInfo();

	/**
	 *
	 * 格子数据
	 *
	 * @param int $gid
	 *
	 * @return array									物品信息
	 * <code>
	 * 	[
	 * 		item_id:int									物品ID
	 * 		item_template_id:int						物品模板ID
	 * 		item_num:int								物品数量
	 * 		item_time:int								物品产生时间
	 * 		va_item_text:								物品扩展信息
	 * 		[
	 * 			mixed
	 * 		]
	 * 	]
	 * </code>
	 */
	public function gridInfo($gid);

	/**
	 *
	 * 格子数据
	 *
	 * @param array(int) $gid
	 *
	 * @return array									物品信息
	 * <code>
	 * 	[
	 * 		gid:[
	 * 			item_id:int									物品ID
	 * 			item_template_id:int						物品模板ID
	 * 			item_num:int								物品数量
	 * 			item_time:int								物品产生时间
	 * 			va_item_text:								物品扩展信息
	 * 			[
	 * 				mixed
	 * 			]
	 * 		]
	 *  ]
	 * </code>
	 */
	public function gridInfos($gid);

	/**
	 *
	 * 移动物品,包括可叠加物品的 合并操作
	 *
	 * @param int $src_gid					源格子ID
	 * @param int $des_gid					目标格子ID
	 *
	 * @return boolean
	 */
	public function moveItem($src_gid, $des_gid);

	/**
	 *
	 * 交换用户背包和仓库
	 *
	 * @param int $gid						源格子ID
	 *
	 * @return array
	 * <code>
	 *	[
	 * 		gid:[
	 * 			item_id:int					物品ID
	 * 			item_template_id:int		物品模板ID
	 * 			item_num:int				物品数量
	 * 			item_time:int				物品产生时间
	 * 			va_item_text:				物品扩展信息
	 * 			[
	 * 				mixed
	 * 			]
	 * 		]
	 * 	]
	 * </code>
	 */
	public function swapBagDepot($gid);

	/**
	 *
	 * 使用物品
	 *
	 * @param int $gid						格子ID
	 * @param int $item_id					物品ID
	 * @param int $item_num					使用物品数量
	 *
	 * @return array						物品信息
	 * <code>
	 * 	{
	 * 		use_success:boolean
	 * 		bag_modify:array
	 * 		[
	 * 			gid:itemInfo
	 *		]
	 *		pet_modify:array
	 *		[
	 *			petInfo
	 *		]
	 * 	}
	 * </code>
	 */
	public function useItem($gid, $item_id, $item_num, $item_choose);

	/**
	 *
	 * 摧毁物品
	 *
	 * @param int $gid						格子ID
	 * @param int $item_id					物品ID
	 *
	 * @return boolean
	 */
	public function destoryItem($gid, $item_id);

	/**
	 *
	 * 领取物品
	 *
	 * @param int $gid						格子ID
	 * @param int $item_id					物品ID
	 *
	 * @return array
	 * <code>
	 * 	{
	 * 		'receive_success':boolean		是否领取成功,TRUE表示成功,FALSE表示背包已满
	 * 		'bag_modify':[
	 * 			gid:[
	 * 				item_id:int				物品ID
	 * 				item_template_id:int	物品模板ID
	 * 				item_num:int			物品数量
	 * 				item_time:int			物品产生时间
	 * 				va_item_text:			物品扩展信息
	 * 				[
	 * 					mixed
	 * 				]
	 * 			]
	 * 		]
	 *  ]
	 *  }
	 * </code>
	 */
	public function receiveItem($gid, $item_id);

	/**
	 *
	 * 开启格子
	 *
	 * @param int $grid_num				格子数目
	 *
	 * @return boolean
	 */
	public function openGrid($grid_num);

	/**
	 * 开启格子
	 *
	 * @param int $grid_num				格子数目
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'open_success':boolean
	 * 		'bag_modify':
	 *		[
	 * 			gid:[
	 * 				item_id:int				物品ID
	 * 				item_template_id:int	物品模板ID
	 * 				item_num:int			物品数量
	 * 				item_time:int			物品产生时间
	 * 				va_item_text:			物品扩展信息
	 * 				[
	 * 					mixed
	 * 				]
	 * 			]
	 * 		]
	 * }
	 * </code>
	 */
	public function openGridByItem($grid_num);

	/**
	 *
	 * 开启仓库格子
	 *
	 * @param int $grid_num				格子数目
	 *
	 * @return boolean
	 */
	public function openDepotGrid($grid_num);
	
	public function openDepotGridByItem($grid_num);

	/**
	 *
	 * 整理用户背包物品
	 *
	 * @return array
	 * <code>
	 * [
	 * 		gid:item_id					格子id:物品id
	 * ]
	 * </code>
	 */
	public function arrange();

	/**
	 *
	 * 整理仓库背包物品
	 *
	 * @return array
	 * <code>
	 * [
	 * 		gid:item_id					格子id:物品id
	 * ]
	 * </code>
	 */
	public function arrangeDepot();
	
	public function compositeItem($gid, $item_id);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */