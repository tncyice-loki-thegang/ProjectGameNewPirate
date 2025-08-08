<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SendAchievementToUser.php 29662 2012-10-16 09:14:28Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/SendAchievementToUser.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-16 17:14:28 +0800 (二, 2012-10-16) $
 * @version $Revision: 29662 $
 * @brief 
 *  
 **/
class SendAchievementToUser extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 4)
		{
			exit('args err');
		}	
		
		$uid = $arrOption[0];
		$name = $arrOption[1];
		$achieveID = $arrOption[2];
		$value = $arrOption[3];

		$user = EnUser::getUserObj($uid);
		if ($name !== $user->getUname())
		{
			exit('uname err, please confirm server ip.');
		}

		EnAchievements::notify($uid, $achieveID, $value);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */