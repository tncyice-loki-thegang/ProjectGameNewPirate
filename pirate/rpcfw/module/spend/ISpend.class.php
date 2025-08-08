<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ISpend.class.php 40313 2013-03-08 06:25:13Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/spend/ISpend.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-08 14:25:13 +0800 (五, 2013-03-08) $
 * @version $Revision: 40313 $
 * @brief 
 *  
 **/

interface ISpend
{
	/**
	 * 得到消费累计信息
	 * @return array
	 * <code>
	 * ret: ok, over:过期
	 * res: object(
	 * gold_accum: num 累计消费的金币
	 * reward: array(id1, id2) 已经领取奖励的id
	 * )
	 * </code>
	 */
	public function getInfo();

	/**
	 * 得到奖励
	 * @param unknown_type $id
	 * <code>
	 * ret:ok ， over 过期， full 背包满了
	 * res:object(
	 * belly => belly,
	 * expience => 阅历,
	 * execution => 行动力,
	 * energy => 能量石
	 * element => 元素石
	 * grid => 背包信息
	 * treasure => object 寻宝积分
	 * (
	 * 'red_score' => 红色积分
	 * 'purple_score' => 紫色积分
	 * )
	 * arming_produce => object 装备制作积分
	 * (
	 * 'red_score' => 红色积分
	 * 'purple_score' => 紫色积分
	 * )
	 * )
	 * </code>
	 */
	public function getReward($id);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */