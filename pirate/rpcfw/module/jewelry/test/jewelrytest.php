<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: jewelrytest.php 40034 2013-03-06 06:03:34Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/jewelry/test/jewelrytest.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-03-06 14:03:34 +0800 (三, 2013-03-06) $
 * @version $Revision: 40034 $
 * @brief 
 *  
 **/
class JewelryTest extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	*/
	public function executeScript($arrOption)
	{
		$ary=array(ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL=>100,
				ItemDef::ITEM_ATTR_JEWELRY_TEXT_SEAL=>array(1=>10),
				ItemDef::ITEM_ATTR_JEWELRY_TEXT_FRESH=>array(1=>1,2=>2,3=>3,4=>4));

		RPCContext::getInstance ()->setSession ( 'global.uid', 20169 );
		
		$item = ItemManager::getInstance()->getItem(28805303);
		$item->setItemText($ary);
		ItemManager::getInstance()->update();
		
		//$ret=$item->getNeedReinforceLevel(1);
		//var_dump($ret);
		
		//$ret=$item->doRefresh(array(1));
		//var_dump($ret);
		
		$obj= new Jewelry();
        $ret=$obj->refresh(28805303, 2, array(1,2));
        var_dump($ret);
       
		//$ret=$obj->replace(28805303, 0);
		//var_dump($ret);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */