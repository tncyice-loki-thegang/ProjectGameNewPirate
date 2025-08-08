<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: mergeServer.script.php 30520 2012-10-30 02:55:28Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mergeserver/scripts/mergeServer.script.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-10-30 10:55:28 +0800 (二, 2012-10-30) $
 * @version $Revision: 30520 $
 * @brief 
 *  
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/MergeServer.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!hefu_activity.csv output\n";
	exit;
}

// 数据对应表
$name = array (
	'mergeServer_type'					=>		2,					// 活动类型
	// 新的王者
	MergeServerDef::MSERVER_TYPE_NEWKING => array(
		'mergeServer_activitydays'		=>		3,					// 活动持续时间
		'mergeServer_reward'			=>		6					// 活动奖励
	),
	// 新的征程
	MergeServerDef::MSERVER_TYPE_NEWJOURNEY => array(
		'mergeServer_activitydays'		=>		3,					// 活动持续时间
		'mergeServer_reward'			=>		6					// 活动奖励
	),
	// 开心厨房麻辣出航
	MergeServerDef::MSERVER_TYPE_KITCHENSAIL => array(
		'mergeServer_activitydays'		=>		3,					// 活动持续时间
		'mergeServer_reward'			=>		6					// 活动奖励
	),
	// 充值返还
	MergeServerDef::MSERVER_TYPE_RECHARGE => array(
		'mergeServer_activitydays'		=>		3,					// 活动持续时间
		'mergeServer_reward'			=>		6					// 活动奖励
	),
	// 合服补偿
	MergeServerDef::MSERVER_TYPE_COMPENSATION => array(
		'mergeServer_activitydays'		=>		3,					// 活动持续时间
		'mergeServer_reward'			=>		6					// 活动奖励
	)
);

$file = fopen($argv[1].'/hefu_activity.csv', 'r');

// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);
$mergeServer = array();
while (TRUE)
{
	$data = fgetcsv($file);	
	if (empty($data))
		break;
	$array = array();

	// type取得
	$type = $data[$name['mergeServer_type']];
	$tempName = array();
	$tempName = $name[$type];
	if ($type == MergeServerDef::MSERVER_TYPE_NEWJOURNEY)
	{
		// 贝利 & 行动力
		$tempNewJourneyArray = array();
		$tempRewardArray = explode(",", $data[$tempName['mergeServer_reward']]);
		for ($i = 0; $i < count($tempRewardArray); $i++) {
			$ary = explode("|", $tempRewardArray[$i]);
			$tempNewJourneyArray[] = array($ary[0], $ary[1]);
		}
//		print_r($tempNewJourneyArray);
	}
	if ($type == MergeServerDef::MSERVER_TYPE_RECHARGE)
	{
//		1000|50,10000,itmeId*count#
//		3000|50,10000,itmeId*count

		// 金币 & 贝利 & 物品
		$tempRechargeArray = array();
		$tempRewardArray = explode("@", $data[$tempName['mergeServer_reward']]);
		for ($i = 0; $i < count($tempRewardArray); $i++) {
			$ary = explode("|", $tempRewardArray[$i]);
			// 奖励信息分隔
			$rewardAry = explode(",", $ary[1]);
			// 复数个物品
			$itemReward = array();
			foreach ($rewardAry as $reward) {
				$itemAry = explode("*", $reward);
				$itemReward[$itemAry[0]] = $itemAry[1];
			}
			$tempRechargeArray[] = array('rewardKey' => $ary[0],
									   'rewardValue' => $itemReward);
//			print_r($tempRechargeArray);
		}
			
	}
	
	if ($type == MergeServerDef::MSERVER_TYPE_COMPENSATION)
	{
		$tempCompensationArray = array();
		$tempRewardArray = explode(",", $data[$tempName['mergeServer_reward']]);
		for ($i = 0; $i < count($tempRewardArray); $i++) {
			$ary = explode("|", $tempRewardArray[$i]);
			$red = explode("*", $ary[1]);
			$tempCompensationArray[$ary[0]] = $red;
		}
	}
	// 数据做成
	foreach ($tempName as $key => $v)
	{
		if ($type == MergeServerDef::MSERVER_TYPE_NEWJOURNEY)
		{
			if ($key == 'mergeServer_reward')
			{
				$array[$key] = $tempNewJourneyArray;
			}
			else
			{
				$array[$key] = $data[$v];
			}
		}
		else if ($type == MergeServerDef::MSERVER_TYPE_RECHARGE)
		{
			if ($key == 'mergeServer_reward')
			{
				$array[$key] = $tempRechargeArray;
			}
			else
			{
				$array[$key] = $data[$v];
			}
		}
		else if ($type == MergeServerDef::MSERVER_TYPE_COMPENSATION)
		{
			if ($key == 'mergeServer_reward')
			{
				$array[$key] = $tempCompensationArray;
			}
			else
			{
				$array[$key] = $data[$v];
			}
		}
		else
		{
			$array[$key] = $data[$v];
		}
	}
	$mergeServer[$type] = $array;
}
fclose($file);

print_r($mergeServer);
$file = fopen($argv[2].'/MERGESERVER', 'w');
fwrite($file, serialize($mergeServer));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
