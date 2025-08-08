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

class CheckItemVa extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{			
		$arrField = array('item_id', 'item_template_id', 'item_time', 'item_deleted', 'va_item_text');

		$data = new CData();
		// var_dump($arrField);
		$arrRet = $data->select($arrField)->from('t_item_0')->where('item_template_id', '=',16601)->query();
		// var_dump($arrRet);
		foreach ($arrRet as $data)
		{
			// var_dump($data);
			$item_id = $data['item_id'];
			$va = $data['va_item_text'];
			// unset($va['reinforce_level']);
			// unset($va['level']);
			// $va['exp'] = 0;
			if ($va['gildlevel'] > 0)
			{	
				var_dump($data['item_id']);
				var_dump($va['gildlevel']);
			}
			// ItemStore::updateItem($item_id, array("va_item_text" => $va));
		}
		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */