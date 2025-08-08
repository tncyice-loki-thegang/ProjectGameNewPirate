<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IForge.class.php 33560 2012-12-21 07:04:55Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/forge/IForge.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2012-12-21 15:04:55 +0800 (五, 2012-12-21) $
 * @version $Revision: 33560 $
 * @brief
 *
 **/

interface IForge
{
	/**
	 *
	 * 开启最大强化概率
	 *
	 * @return boolean						TRUE表示成功,FALSE表示失败
	 */
	public function openMaxProbability();

	/**
	 *
	 * 宝石融合
	 *
	 * @param int $item_id					目标物品id
	 * @param int $fuse_item_id				被融合的物品id
	 *
	 * @return boolean						TRUE表示成功, FALSE表示失败
	 */
	public function fuse($item_id, $fuse_item_id);

	/**
	 *
	 * 宝石融合
	 *
	 * @param int $item_id					目标物品id
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'fuse_success':boolean
	 * 		'fuse_item_exp':int
	 * 		'bag_modify':array
	 * 		[
	 * 			gid:itemInfo
	 * 		]
	 * }
	 * </code>
	 */
	public function fuseAll($item_id);

	/**
	 *
	 * 镶嵌物品
	 *
	 * @param int $item_id			物品ID
	 * @param int $gem_item_id		宝石ID
	 * @param int $hole_id			孔洞ID
	 *
	 * @return boolean
	 */
	public function enchase($item_id, $gem_item_id, $hole_id);

	/**
	 *
	 * 摘除宝石
	 *
	 * @param int $item_id			物品ID
	 * @param int $hole_id			孔洞ID
	 *
	 * @return array
	 * <code>
	 * [
	 * 		gid:array
	 * 		[
	 * 			item_id:int,
	 * 			item_template_id:int,
	 * 			item_num:int,
	 * 			item_time:int,
	 * 			va_item_text:array,
	 * 		]
	 * ]
	 * </code>
	 */
	public function split($item_id, $hole_id);

	/**
	 *
	 * 强化物品
	 *
	 * @param int $item_id			物品ID
	 * @param int $special			是否使用金币
	 *
	 * @return array				array()表示条件不满足
	 * <code>
	 * [
	 * ]
	 * or
	 * [
	 * 		reinforce_success:boolean,		强化是否成功
	 * 		reinforce_time:timestamp,		冷却到期时间
	 * 		reinforce_freeze:boolean,		是否冻结
	 * 		reinforce_items:array			array(itemInfo)
	 * ]
	 * </code>
	 */
	public function reinforce($item_id, $special);

	/**
	 *
	 * 降级物品
	 *
	 * @param int $item_id			物品ID
	 * @param int $level			降级等级
	 *
	 * @return boolean
	 */
	public function weakening($item_id, $level);

	/**
	 *
	 * 合成物品
	 *
	 * @param int $compose_id				合成ID
	 * @param int $compose_number			合成数量
	 * @param array $items					合成使用的物品,如果不需要明确指定物品,则可以不传，
	 * 										系统将按照从先到后的顺序删除所需物品
	 * <code>
	 * [
	 * 		item_id => item_num
	 * ]
	 * </code>
	 *
	 * @return array						改变的物品
	 * <code>
	 * {
	 * 		compose_success:boolean
	 * 		compose_items:
	 * 			[
	 * 				gid:array
	 * 				[
	 * 					item_id:int,
	 * 					item_template_id:int,
	 * 					item_num:int,
	 * 					item_time:int,
	 * 					va_item_text:array,
	 * 				]
	 * 			]
	 * }
	 * </code>
	 */
	public function compose($compose_id, $compose_number = 1, $items=array());

	public function decompose($item_id);

	/**
	 *
	 * 装备强化转移
	 *
	 * @param int $item_id					源物品ID
	 * @param int $target_item_id			目标物品ID
	 * @param boolean $transfer_gem			是否宝石转移,默认为FALSE
	 *
	 * @return array						array()表示失败
	 * <code>
	 * [
	 * 		item_id:array
	 * 		[
	 * 			item_id:int,
	 * 			item_template_id:int,
	 * 			item_num:int,
	 * 			item_time:int,
	 * 			va_item_text:array,
	 * 		]
	 * ]
	 * </code>
	 */
	public function transfer($item_id, $target_item_id, $transfer_gem = FALSE);

	/**
	 *
	 * 装备潜能转移
	 *
	 * @param int $src_item_id				源物品ID
	 * @param int $target_item_id			目标物品ID
	 * @param int $transfer_type			转移类型, 1金币, 2物品, 3免费
	 *
	 * @return array						array表示失败
	 * <code>
	 * {
	 * 		'transfer_success':boolean			是否转移成功,如果为false,则后续节点不存在
	 * 		'items':array						源物品和目标物品
	 * 		[
	 * 			item_id:array
	 * 			[
	 * 				item_id:int,
	 * 				item_template_id:int,
	 * 				item_num:int,
	 * 				item_time:int,
	 * 				va_item_text:array,
	 * 			]
	 * 		]
	 * 		'bag_info':array					背包改变量
	 * 		[
	 * 			gid:array
	 * 			[
	 * 				item_id:int,
	 * 				item_template_id:int,
	 * 				item_num:int,
	 * 				item_time:int,
	 * 				va_item_text:array,
	 * 			]
	 * 		]
	 * }
	 * </code>
	 */
	public function potentialityTransfer($src_item_id, $target_item_id, $transfer_type);

	/**
	 *
	 * 得到潜能转移信息
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'potentiality_free_time':int				当前已使用的潜能转移免费次数
	 * 		'potentiality_refresh_time':int				潜能转移免费次数刷新时间
	 * }
	 * </code>
	 */
	public function getPotentialityTransfer();

	/**
	 *
	 * 装备转移信息
	 *
	 * @param NULL
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'transfer_time':int				当前的装备转移次数
	 * 		'transfer_max_gold':int			装备转移最大所需gold
	 * 		'transfer_inc_gold':int			装备转移每次递增的金币数量
	 * }
	 * </code>
	 */
	public function getTransferInfo();

	/**
	 *
	 * 随机洗练物品
	 *
	 * @param int $item_id			物品ID
	 * @param boolean $special		是否使用金币
	 *
	 * @return array
	 * <code>
	 * 		refresh_success:boolean			是否成功
	 *		potentiality:array				刷新出的数值
	 * </code>
	 */
	public function randRefresh($item_id, $special);

	/**
	 *
	 * 固定洗练物品
	 *
	 * @param int $item_id					物品ID
	 * @param int $type						洗练方式
	 *
	 * @return array
	 * <code>
	 * 		refresh_success:boolean			是否成功
	 *		potentiality:array				刷新出的数值
	 * </code>
	 */
	public function fixedRefresh($item_id, $type);

	/**
	 *
	 * 物品固定洗练确认
	 *
	 * @param int $item_id					物品ID
	 *
	 * @return boolean
	 */
	public function fixedRefreshAffirm($item_id);

	/**
	 *
	 * 物品随机洗练确认
	 *
	 * @param int $item_id					物品ID
	 *
	 * @return boolean
	 */
	public function randRefreshAffirm($item_id);

	/**
	 *
	 * 得到强化的冷却信息
	 *
	 * @return array
	 * <code>
	 * [
	 * 		reinforce_time:timestamp		冷却到期时间
	 * 		reinforce_freeze:boolean		是否冻结
	 * ]
	 * </code>
	 */
	public function getReinforceCD();

	/**
	 *
	 * 重置强化时间
	 *
	 * @return int							重置时间消耗掉的金币数量
	 */
	public function resetReinforceTime();

	/**
	 *
	 * 得到强化概率
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'reinforce_probability':int			当前强化概率
	 * 		'reinforce_direction':boolean		上升过程中为TRUE,下降过程中为FALSE
	 * 		'is_max_probability':boolean		是否开通了最大强化概率
	 * }
	 * </code>
	 */
	public function getReinforceProbability();
	
	/**
	 *
	 * 通过增加宝石经验，升级宝石等级，每次只升一级
	 *
	 * @param int $item_id					物品ID
	  * @return array
	 * <code>
	 * {
	 * 		'levelup_success':boolean	升级操作是否成功
	 * 		'cost_exp':int				该升级操作花费了多少经验
	 * }
	 * </code>
	 */
	public function gemLevelUpByExp($item_id);

	
	public function gild($item_id);
	
	public function ungild($item_id);
	
	public function daimonAppleFuse($item_id, $fuse_item_id);
	
	public function daimonAppleFuseAll($item_id);
	
	public function daimonAppleLevelUpByExp($item_id);
	
	public function demonEvoPanel();
	
	public function demonEvoUp($item_id);
	
	public function elementFuseExp($item_id);

	public function elementFuse($item_id, $fuse_item_id);
	
	public function elementFuseAll($item_id);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */