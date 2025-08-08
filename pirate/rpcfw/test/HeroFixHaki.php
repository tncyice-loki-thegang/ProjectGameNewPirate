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

class HeroFixHaki extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{			
		$arrField = array('hid', 'htid', 'va_hero');
		$data = new CData();
		// var_dump($arrField);
		$arrRet = $data->select($arrField)->from('t_hero')->where('hid', '>',10000000)->query();
		// var_dump($arrRet);
		foreach ($arrRet as $data)
		{
			// var_dump($data);
			$htid = $data['htid'];
			$va = $data['va_hero'];
			//
			// $DefaultNormalSkill = btstore_get()->CREATURES[$htid][CreatureInfoKey::normalAtk];
			// $normalSkill = $DefaultNormalSkill[0];
			// $va['master']['learned_normal_skills']=array($normalSkill);
			// $va['master']['using_normal_skill']=$normalSkill;
			// $va['element']=array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0);
			// $va['haki'] = array(
				// 'hp' => array('level'=>0, 'expe'=>0, 'property'=>array()),
				// 'master' => array('level'=>0, 'expe'=>0, 'property'=>array()),
				// 'xiuluo' => array('level'=>0, 'expe'=>0, 'property'=>array()),
				// 'defense' => array('level'=>0, 'expe'=>0, 'property'=>array()),
				// 'attack' => array('level'=>0, 'expe'=>0, 'property'=>array()),
			// );
			$va['master_haki_id'] = 0;
			// var_dump($va);
			HeroDao::update($data['hid'], array("va_hero" => $va));
		}
			// $hero = HeroDao::getByHid(10011900, array('va_hero'));
			// $va = $hero['va_hero'];
			// $va['daimonApple'] = array(0,0,0);
			
			// HeroDao::update(10012400, array("va_hero" => $va));
			// Logger::info('modify hero hid:%d, va_hero:%s', 10011900, $va);
		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */