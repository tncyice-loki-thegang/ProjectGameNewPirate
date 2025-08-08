<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: subUserBelly.class.php 39171 2013-02-23 09:55:47Z HaidongJia $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/subUserBelly.class.php $
 * @author $Author: HaidongJia $(hoping@babeltime.com)
 * @date $Date: 2013-02-23 17:55:47 +0800 (六, 2013-02-23) $
 * @version $Revision: 39171 $
 * @brief 
 *  
 **/

class subUserBelly extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 3)
		{
			echo "args err! need args:uid uname subbellynum!\n";
			return;
		}	
		
		$uid = intval($arrOption[0]);
		if ( $uid <= 0 )
		{
			echo "invalid uid!\n";
			return;
		}
		
		$uname = strval($arrOption[1]);
		
		$belly = intval($arrOption[2]);
		
		if ( $belly <= 0 )
		{
			echo "invalid belly number! must belly number > 0!\n";
			return;
		}
		
		$user = EnUser::getUserObj($uid);
		
		if ( $uname != $user->getUname() )
		{
			echo "user name is invalid!\n";
			return;
		}
		
		if ( $user->subBelly($belly) == FALSE )
		{
			echo "belly number is bigger than now!\n";
			return;
		}
		
		$user->update();

		echo "subUserBelly uid:$uid uname:$uname belly:$belly done!\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */