<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: astrolabetest.php 30076 2012-10-19 12:12:05Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/astrolabe/test/astrolabetest.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-10-19 20:12:05 +0800 (五, 2012-10-19) $
 * @version $Revision: 30076 $
 * @brief 
 *  
 **/

class AstrolabeTest extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	public function executeScript($arrOption)
	{
		
		RPCContext::getInstance ()->setSession ( 'global.uid', 51739 );
		
		//var_dump($ret);
		$arryfiled1= array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_UID => 51409,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID => 2,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_TYPE =>1,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL =>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS =>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS =>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_AST_EXP_TIME=>0);
		//AstrolabeDAO::insertIntoAstTable($arryfiled1);
		
	
		
		$arryfiled2= array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_UID => 51409,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_ID => 14,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_LEV =>5,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_STAT =>0,
				AstrolabeDef::ASTROLABE_SQL_FIELD_CONS_TYPE =>0,);
		//AstrolabeDAO::insertIntoConsTable($arryfiled2);
		
		
		$arryfiled3= array(
				AstrolabeDef::ASTROLABE_SQL_FIELD_UID => 21713,
				AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM => 12,
				AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT =>1,
				AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_TIME => Util::getTime(),);
		
		//AstrolabeDAO::insertIntoStoneTable($arryfiled3);
		
		$uid=51409;
		//$val=AstrolabeLogic::getBasicAstInfo($uid);
		//$val=AstrolabeLogic::getTalentAstInfo($uid);
		//$consIds=array(12);
		//$val=AstrolabeLogic::getConsStatusFromDb($uid,$consIds);
		//var_dump($val);
		
		//$ret=AstrolabeLogic::getAstConsStatusByLevulUp(51310,12);
		
		//$status=AstrolabeLogic::checkConsStatus(50709,2002);
		//var_dump($status);
		
		//$val=AstrolabeLogic::getStoneInfo($uid);
		
		//var_dump($val);
		
		//$ret=AstrolabeLogic::checkSkillStatusByLevulUp($uid,14,5);
		//var_dump($ret);
		
		
		//AstrolabeLogic::initAstInfo(21713);
		
		
		//$ret=AstrolabeLogic::getCurTalentAstAttr(20038);
		//var_dump($ret);
		
		//$ret=AstrolabeLogic::getCurMainAstAttr(20038);
		//var_dump($ret);
		
		
		$astobj=new Astrolabe();
		
		//$aryexp=btstore_get()->ASTROLABE_EXP[intval(10)];
		//$ary=btstore_get()->ASTROLABE_DEPEND_MAIN;
		//var_dump($ary);
		
		//$ret=$astobj->askInitInfo();
		//var_dump($ret);
		//$ret=$astobj->askLevelUpMain();
		//var_dump($ret);
		//$astobj->askLevelUpCons(14);
		$ret=$astobj->askSwitchTalentAst(2);
		var_dump($ret);
		
		//$ret=$astobj->askEquipTalentAst(2);
		//$astobj->askUnEquipTalentAst(2);
		 
		//$ret=$astobj->askBuyStone(0);
		//var_dump($ret);
		//$ret=$astobj->getCurCommonSkillId();
		//var_dump($ret);
		
		//$ret=$astobj->getCurMainAstAttr();
		///$ret=$astobj->getCurTalentAstAttr();
		
		//$retStat=AstrolabeLogic::getConsStatus(50709, array(3001));
		//var_dump($retStat);
		//$ret=$astobj->askObtainTodayExp();
		//var_dump($ret);
		//$ret1=Astrolabe::getCurStone(50709);
		//var_dump($ret1);
		//$ret2=Astrolabe::addStone(50709,10);
		//var_dump($ret2);
		
	   //	return $ret;
		
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */