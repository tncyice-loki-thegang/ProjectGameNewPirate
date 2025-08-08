<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SendOlympicAward.php 31607 2012-11-22 06:32:58Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/SendOlympicAward.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-11-22 14:32:58 +0800 (四, 2012-11-22) $
 * @version $Revision: 31607 $
 * @brief 
 *  
 **/
class SendOlympicAward extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		// uid uname event_id_1 event_id_2 event_id_3
		if (count($arrOption) != 3)
		{
			exit('args err');
		}	

		$uid = intval($arrOption[0]);
		$name = $arrOption[1];
		$rank = $arrOption[2];

		$user = EnUser::getUserObj($uid);
		if ($name !== $user->getUname())
		{
			exit('uname err, please confirm server ip.');
		}

		Olympic::__executeAward($rank, $uid, OlympicDef::TYPE_WIN);
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */