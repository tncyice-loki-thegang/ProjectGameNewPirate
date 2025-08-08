<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SetUserTalksWindow.php 22412 2012-06-15 06:59:52Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/SetUserTalksWindow.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-06-15 14:59:52 +0800 (五, 2012-06-15) $
 * @version $Revision: 22412 $
 * @brief 
 *  
 **/
class SetUserTalksWindow extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		// uid uname event_id_1 event_id_2 event_id_3
		if (count($arrOption) != 5)
		{
			exit('args err');
		}	

		$uid = $arrOption[0];
		$name = $arrOption[1];
		$event_1 = $arrOption[2];
		$event_2 = $arrOption[3];
		$event_3 = $arrOption[4];

		$user = EnUser::getUserObj($uid);
		if ($name !== $user->getUname())
		{
			exit('uname err, please confirm server ip.');
		}

		// 获取用户的会谈信息
		$talksInfo = TalksDao::getTalksInfo($uid);
		// 如果没获取到，因为是支线任务，那么就需要初始化数据
		if ($talksInfo === false)
		{
			exit('can not get talks info.');
		}
		$talksInfo['va_talks_info']['talk_win'][1] = $event_1;

		if (isset($talksInfo['va_talks_info']['talk_win'][2]))
			$talksInfo['va_talks_info']['talk_win'][2] = $event_2;

		if (isset($talksInfo['va_talks_info']['talk_win'][3]))
			$talksInfo['va_talks_info']['talk_win'][3] = $event_3;

		// 更新数据库
		TalksDao::updTalksInfo($uid, $talksInfo);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */