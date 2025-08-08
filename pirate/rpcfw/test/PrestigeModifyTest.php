<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: PrestigeModifyTest.php 20254 2012-05-11 12:30:56Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/PrestigeModifyTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-11 20:30:56 +0800 (五, 2012-05-11) $
 * @version $Revision: 20254 $
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
		if (count($arrOption) != 4)
		{
			exit('args err');
		}	
		
		$uid = $arrOption[0];
		$belly = $arrOption[1];
		$prestige = $arrOption[2];		
		$experience = $arrOption[3];
		
		$user = EnUser::getUserObj($uid);
		$user->addBelly($belly);
		$user->addPrestige($prestige);
		$user->addExperience($experience);
		$user->update();		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */