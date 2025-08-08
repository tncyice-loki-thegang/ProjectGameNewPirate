<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IItemInfo.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/itemInfo/IItemInfo.class.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

interface IItemInfo
{
	/**
	 *
	 * 得到物品信息
	 *
	 * @param array(int) $item_ids
	 *
	 * @return array							如果物品不存在，则[$item_id] = array();
	 * <code>
	 * [
	 * 		item_id:array
	 * 		{
	 * 			item_id:int						物品ID
	 * 			item_template_id:int			物品模板ID
	 * 			item_num:int					物品数量
	 * 			item_time:int					物品产生时间
	 * 			va_item_text:array				物品扩展信息
	 * 			{
	 * 				'reinforce_level':int		强化等级
	 * 				'exp':int					物品经验
	 * 				'potentiality':array		潜能属性
	 * 				[
	 *					attr_id:attr_value
	 * 				]
	 * 				'potentiality_fixed_refresh': 固定洗练属性
	 * 				[
	 *					attr_id:attr_value
	 * 				]
	 * 				'potentiality_rand_refresh': 随机洗练属性
	 * 				[
	 *					attr_id:attr_value
	 * 				]
	 * 				'arm_enchanse':array		镶嵌信息
	 * 				[
	 * 					hole_id:itemInfo
	 * 				]
	 * 				to be continue...
	 * 			}
	 * 		}
	 * ]
	 * </code>
	 */
	public function getItemInfos($item_ids);

	/**
	 *
	 * 得到物品信息
	 *
	 * @param int $item_id
	 *
	 * @return array						如果物品不存在,则返回array()
	 * <code>
	 * 	{
	 * 		item_id:int						物品ID
	 * 		item_template_id:int			物品模板ID
	 * 		item_num:int					物品数量
	 * 		item_time:int					物品产生时间
	 * 		va_item_text:array				物品扩展信息
	 * 		{
	 * 			'reinforce_level':int		强化等级
	 * 			'exp':int					物品经验
	 * 			'potentiality':array		潜能属性
	 * 			[
	 *				attr_id:attr_value
	 * 			]
	 * 			'arm_enchanse':array		镶嵌信息
	 * 			[
	 * 				hole_id:itemInfo
	 * 			]
	 * 			to be continue...
	 * 		}
	 * 	}
	 * </code>
	 */
	public function getItemInfo($item_id);

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */