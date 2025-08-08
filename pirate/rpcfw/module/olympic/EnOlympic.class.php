<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnOlympic.class.php 34472 2013-01-07 03:52:49Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/olympic/EnOlympic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-07 11:52:49 +0800 (一, 2013-01-07) $
 * @version $Revision: 34472 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnOlympic
 * Description : 对内接口实现类
 * Inherit     :
 **********************************************************************************************************************/
class EnOlympic
{

	/**
	 * 判断是否是擂台赛时间
	 */
	static public function isOlympicTime($time)
	{
		// 每天擂台赛开始时间
		$startTime = strtotime(OlympicConf::START_TIME) + GameConf::BOSS_OFFSET;
		// 结束时间
		$endTime = OlympicUtil::getEndTime($startTime, 6, true);
		// 判断并返回
		return ($time >= $startTime && $time<= $endTime);
	}

	/**
	 * 返回用户积分排名
	 */
	static public function getUserIntegralRank()
	{
		// 获取本人积分名次
		return OlympicDao::getUserIntegralRank(RPCContext::getInstance()->getUid());
	}

	/**
	 * 获取积分列表 
	 */
	static public function getIntegralList($min, $max)
	{
		// 获取服务器积分排行
		$list = OlympicDao::getServerIntegralList($min, $max);
		// 对空加判断
		if (!empty($list))
		{
			// 获取uid列表
	    	$arrUids = Util::arrayExtract($list, 'uid');
	    	// 使用uid列表, 获取用户信息
    		$arrUser = Util::getArrUser($arrUids, array('uname', 'utid', 'guild_id', 'group_id', 'level'));
    		// 从用户信息中获取公会ID
    		$arrGuildIds = Util::arrayExtract($arrUser, 'guild_id');
    		// 使用公会ID获取公会名
    		$arrGuildName = GuildLogic::getMultiGuild($arrGuildIds, array('name'));
	    	// 将公会名称和等级插入数组
	    	foreach ($list as $key => $user)
	    	{
	    		// 合并公会名称
	    		$list[$key]['guild_name'] = isset($arrGuildName[$arrUser[$user['uid']]['guild_id']]['name']) ? 
	    		                                  $arrGuildName[$arrUser[$user['uid']]['guild_id']]['name'] : '';
	    		// 合并用户等级
	    		$list[$key]['level'] = $arrUser[$user['uid']]['level'];
	    		// 合并用户名
	    		$list[$key]['uname'] = $arrUser[$user['uid']]['uname'];
	    		// 合并用户模板ID
	    		$list[$key]['utid'] = $arrUser[$user['uid']]['utid'];
	    		// 合并用户公会ID
	    		$list[$key]['guild_id'] = $arrUser[$user['uid']]['guild_id'];
	    		// 合并用户阵营ID
	    		$list[$key]['group_id'] = $arrUser[$user['uid']]['group_id'];
	    	}
		}
    	// 返回给前端
    	return $list;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */