<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: npcresourcetest.php 35944 2013-01-15 07:50:34Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/branches/pirate/rpcfw/newworldres/module/npcresource/test/npcresourcetest.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-15 15:50:34 +0800 (星期二, 15 一月 2013) $
 * @version $Revision: 35944 $
 * @brief 
 *  
 **/
class NpcResourceTest extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	*/
	public function executeScript($arrOption)
	{
		//RPCContext::getInstance ()->setSession ( 'global.uid', 74322 );
		RPCContext::getInstance ()->setSession ( 'global.uid', 74800 );
		
		//$ret=NpcReourceLogic::checkIsNeedInitResource(1);
		//var_dump($ret);
		$obj=new NpcResource();
		//$ret=$obj->resourceInfo(1);
		//var_dump($ret);
		//$ret=NpcReourceLogic::checkIsNeedUpdatePirateId(1);
		//var_dump($ret);
		
		//$ret=$obj->attackResourceByUser(1, 1);
		//var_dump($ret);
		
		//$obj->attackResourceByNpc(74322,1,1);
		
		
		//$ret=NpcReourceLogic::getServerLevel();
		//var_dump($ret);
		//$rate=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_RES_ID_ATTR_ARY][1][15];
		//echo $rate;
		
		$ret=$obj->givenupResource(1, 1);
		var_dump($ret);
		
		//NpcReourceLogic::resetServerLevelEveryDay();
		
		//NpcReourceLogic::resetResourceEveryDay();
		
		//NpcReourceLogic::checkIsNeedInitUserInfo(74800);
		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */