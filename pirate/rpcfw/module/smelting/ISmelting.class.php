<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ISmelting.class.php 40138 2013-03-06 10:47:41Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/ISmelting.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-03-06 18:47:41 +0800 (三, 2013-03-06) $
 * @version $Revision: 40138 $
 * @brief 
 *  
 **/

interface ISmelting
{
	/**
	 * 获取人物的制作信息
	 * 
	 * @return array <code> : {
	 * uid:用户ID,
	 * artificer_leave_time:下次工匠刷新时刻
	 * last_smelt_times:剩余的熔炼次数,
	 * last_smelt_time：上次熔炼时刻
	 * cd_time:熔炼CD,
	 * gold_artificer_times:金币邀请工匠次数,
	 * gold_artificer_time:上次金币邀请工匠时刻,
	 * artificer_time:最近一次来工匠的时刻,
	 * smelt_times_1:戒指的熔炼次数,
	 * quality_1:戒指的熔炼品质,
	 * smelt_times_2:披风的熔炼次数,
	 * quality_2:披风的熔炼品质,
	 * va_smelt_info:
	 * artificers : 当前拥有的工匠 [
	 * lv:工匠等级
	 * type:工匠类型
	 * id:工匠ID
     * ]
     * integral : 现在拥有的积分
	 * }
	 * </code>
	 */
	function getSmeltingInfo();

	/**
	 * 熔炼所有次数
	 * 
	 * @param int $type							准备多大开销
	 * @param int $itemType						哪种装备
	 * 
	 * @return array <code> : {
	 * itemInfo:掉落的物品和背包信息,
	 * integral:积分信息
	 * artIDs：掉落工匠ID
	 * luckyTimes:幸运熔炼次数
	 * quality:这次熔炼的品质
	 * }
	 * </code>
	 */
	function smeltingAll($type, $itemType);

	/**
	 * 熔炼
	 * 
	 * @param int $type							准备多大开销
	 * @param int $itemType						哪种装备
	 * 
	 * @return array <code> : {
	 * quality:这次熔炼的品质,
	 * isLucky:是否幸运熔炼了,
	 * $isCritical:是否暴击
	 * artID：掉落工匠ID，0为没掉落
	 * }
	 * </code>
	 */
	function smeltingOnce($type, $itemType);

	/**
	 * 金币邀请工匠
	 * 
	 * @return err:string						金币不够，没掉落
	 * @return 工匠ID:int							掉落工匠ID，0为没掉落(满了)
	 */
	function inviteArtificer();

	/**
	 * 获取好东西
	 * 
	 * @param int $itemType						哪种装备
	 * 
	 * @return array <code> : {
	 * itemInfo:掉落的物品和背包信息 (背包满了的时候返回 err)
	 * cd_time:CD时刻 (背包满了的时候返回 0)
	 * integral:积分信息
	 * }
	 * </code>								
	 */
	function getSmeltingItem($itemType);

	/**
	 * 使用人民币清空CD时间
	 * 
	 * @return int								返回实际使用的金币数  清空CD时刻成功
	 *         string 'err'						清空失败 (应该是没钱吧)
	 */
	function clearCDByGold();

	/**
	 * 刷新所有工匠离开时刻
	 */
	function refreshArtificer();

	/**
	 * 积分换好礼
	 * 
	 * @param int $itemTID						物品模板ID
	 * 
	 * @return string 'err'						兑换失败
	 * @return array <code> : {
	 * bag:背包信息
	 * type:兑换类型
	 * }
	 * </code>	
	 */
	function integralExchange($itemTID);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */