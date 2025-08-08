<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroModifyGW.php 27354 2012-09-19 06:36:57Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/HeroModifyGW.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-19 14:36:57 +0800 (三, 2012-09-19) $
 * @version $Revision: 27354 $
 * @brief 
 *  
 **/

/**
 * hid exp level upgrade_time
 * Enter description here ...
 * @author idyll
 *
 */

class CheckCopetVa extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{			
		$arrField = array('uid', 'va_pet_info');
		$data = new CData();
		// var_dump($arrField);
		$arrRet = $data->select($arrField)->from('t_pet')->where('uid', '=',20000)->query();
		// var_dump($arrRet);
		foreach ($arrRet as $data)
		{
			// var_dump($data);
			// $item_id = $data['item_id'];
			$va = $data['va_pet_info'];
			// unset($va['reinforce_level']);
			// unset($va['is_show_copet']);
			// unset($va['isfollow']);
			// unset($va['cur_show_copet']);
			$va[2]['lv'] = 100;
			$va[2]['exp'] = 30;
			var_dump($va);
			PetDao::updPetInfo(20000, array("va_pet_info" => $va));
		}
		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */