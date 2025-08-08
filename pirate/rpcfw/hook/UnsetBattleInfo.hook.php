<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UnsetBattleInfo.hook.php 39944 2013-03-05 08:02:10Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/hook/UnsetBattleInfo.hook.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-05 16:02:10 +0800 (二, 2013-03-05) $
 * @version $Revision: 39944 $
 * @brief 
 * 
 **/



class UnsetBattleInfo
{
	function execute ($arrResponse)
	{
		$uid = RPCContext::getInstance()->getUid();
		if ($uid < FrameworkConfig::MIN_UID)
		{
			return $arrResponse;
		}
		
		if (RPCContext::getInstance()->getSession(EnUser::MODIFY_BATTLE_INFO_KEY)===1)
		{
			Logger::debug('unset battle info');
			RPCContext::getInstance()->unsetSession(EnUser::MODIFY_BATTLE_INFO_KEY);
			McClient::del(EnUser::getBattleInfoKey($uid));
		}
				
		return $arrResponse;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */