<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IMergeServer.class.php 30737 2012-10-31 13:13:25Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mergeserver/IMergeServer.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-10-31 21:13:25 +0800 (三, 2012-10-31) $
 * @version $Revision: 30737 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : IMergeServer
 * Description : 合服活动对外接口实现类
 * Inherit     : IMergeServer
 **********************************************************************************************************************/
interface IMergeServer
{
	/**
	 * 合服活动-新的征程
	 * 
	 * @return array
	 * <code>
	 * [
	 *				'ret' => 'ok'				领取OK
	 *						 'over'				 活动结束
	 *				'res' => array
	 * 						<code>
	 * 						[	 
	 * 							'reward' => num	已经领取奖励数，没有返回0
	 * 							'can' => true or false 当天是否能领取奖励(未领取就能，已领取过就不能)
	 * 						]
	 *						</code>
	 * ]
	 * </code>
	 * 
	 */
	function getRewardLast();

	/**
	 * 合服活动-新的征程
	 * 
	 * @return array
	 * <code>
	 * [
	 *				'ret' => 'ok'				领取OK
	 *						 'over'				 活动结束
	 *				'res' => array
	 * 						<code>
	 * 						[	 
	 * 							'belly' => belly 贝利
	 * 							'execution' => execution 行动力
	 * 						]
	 *						</code>
	 * ]
	 * </code>
	 * 
	 */
	function Reward();

	/**
	 * 是否有补偿
	 * 
	 * @return int								0:可以领取, 1:不可领取;
	 * 
	 */
	function getIsCompensation();

	/**
	 * 领取合服补偿
	 * 
	 * @return 'res' => array					非空:领取成功;
	 * 					<code>
	 * 					[	 
	 * 						'belly' => int  	贝利
	 * 						'gold' => int		金币
	 * 						'prestige' => int	声望
	 * 						'execution' => int	行动力
	 * 						'experience' => int	阅历
	 * 					]
	 *					</code>
	 * 
	 */
	function getCompensation();
	
	function getMergerServerTimes();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */