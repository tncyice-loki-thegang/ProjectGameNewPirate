<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: PrestigeModify---del.php 20251 2012-05-11 12:28:33Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/PrestigeModify---del.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-11 20:28:33 +0800 (五, 2012-05-11) $
 * @version $Revision: 20251 $
 * @brief 
 *  
 **/

/**
 * 
 * Enter description here ...
 * @author idyll
 *
 */

class PrestigeModifyTest extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$name = '';
		$prestige = 0;
		$experience = 0;
		
		foreach ($arrOption as $option)
		{
			$tmp = explode('=', $option);
			$tmp = array_map('trim', $tmp);
			if (count($tmp)>1)
			{
				if ($tmp[0]=='name')
				{
					$name = trim($tmp[1]);
				}
				else if ($tmp[0]=='prestige')
				{
					$prestige = trim($tmp[1]);					
				}
				else if ($tmp[0]=='experience')
				{
					$experience = trim($tmp[1]);					
				}
			}
		}
		
		$uid = UserDao::unameToUid($name);
		if ($uid==0)
		{
			exit('err. fail to get uid by uname:' .  $name);
		}	
		$server = new ServerProxy();
		$server->closeUser($uid);
		$arrUpdate = array();
		if ($prestige!=0)
		{
			$arrUpdate['prestige_num'] = $prestige;			
		}
		if ($experience!=0)
		{
			$arrUpdate['experience_num'] = $experience;	
		}
		UserDao::updateUser($uid, $arrUpdate);		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */