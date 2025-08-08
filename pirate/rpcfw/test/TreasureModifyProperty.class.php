<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UserModifyProperty.class.php 27689 2012-09-21 10:09:56Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/trunk/pirate/rpcfw/test/UserModifyProperty.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2012-09-21 18:09:56 +0800 (星期五, 21 九月 2012) $
 * @version $Revision: 27689 $
 * @brief
 *
 **/

class TreasureModifyProperty extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		
		if (count($arrOption)!=3)
		{
			exit("argv err\n");
		}
		
		$uid = intval($arrOption[0]);
		$propName = $arrOption[1];
		$propValue = $arrOption[2];
		
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);		
		sleep(1);
		
		Logger::warning('modify treasure uid %d, %s to %s', $uid, $propName, $propValue);
		TreasureDao::update($uid, array($propName=>$propValue));		
		
		echo "ok\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */