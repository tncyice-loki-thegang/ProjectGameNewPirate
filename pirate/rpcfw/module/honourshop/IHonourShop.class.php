<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IHonourShop.class.php 32854 2012-12-11 08:41:11Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/honourshop/IHonourShop.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-11 16:41:11 +0800 (二, 2012-12-11) $
 * @version $Revision: 32854 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : IHonourShop
 * Description : 荣誉商店
 * Inherit     : IHonourShop
 **********************************************************************************************************************/
interface IHonourShop
{
	/**
	 * 获取该用户的荣誉信息
	 * 
	 * @return array <code> : {
	 * 		honourpoint:当前积分
	 * 		exiteminfo:兑换过的装备信息
	 * }
	 * </code>
	 */
	function honourInfo();

	/**
	 * 获取该用户的荣誉信息
	 * 
	 * @param int $exItemId								兑换的装备ID
	 * 
	 * @return array <code> : {
	 * ret:
	 *     ok											兑换成功
	 *     err											兑换失败
	 * 	   noPoint,										积分不足
	 *     noPrestige,									声望不足
	 *     noLevel,										等级不足
	 *     noExTimes,									兑换次数已满
	 * 
	 * items
	 * {
	 * 		bag：@see IBag::receiveItem
	 * }
	 */
	function exItemByHonour($exItemId, $num);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */