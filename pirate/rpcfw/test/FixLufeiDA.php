<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FixLufeiDA.php 32199 2012-12-03 06:43:36Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixLufeiDA.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-12-03 14:43:36 +0800 (一, 2012-12-03) $
 * @version $Revision: 32199 $
 * @brief 
 *  
 **/
class FixLufeiDA extends BaseScript
{

	
	protected function getConverted($htid)
	{
		$data = new CData();
		$ret = $data->select(array('htid', 'hid', 'va_hero'))->from('t_hero')->where('htid', '=', $htid)->query();
		return $ret;			
	}

	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$htid = 30037;
		$newItemTpl = 81031;
		
		$arrHero = $this->getConverted($htid);
		if (empty($arrHero))
		{
			$str = 'empty, exit';
			Logger::info('%s', $str);
			echo $str . "\n";
			return;
		}
		
		foreach ($arrHero as $hid=>$hero)
		{
			$hid = $hero['hid'];
			$va_hero = $hero['va_hero'];
			$itemId = $va_hero['daimonApple'][0];
			
			$itemMgr = ItemManager::getInstance()->getInstance();
			$itemObj = $itemMgr->getItem($itemId);
			$tplId = $itemObj->getItemTemplateID();
			if ($tplId == $newItemTpl)
			{
				continue;
			}

			$arrTmp = $itemMgr->addItem($newItemTpl);
			$newItemId = $arrTmp[0];
			$va_hero['daimonApple'][0] = $newItemId;
			$itemMgr->update();
			
			HeroDao::update($hid, array('va_hero'=>$va_hero));

			$str = "modify hero " . $hid;			
			Logger::info('%s', $str);
			
			echo $str . "\n";
		}
		
		echo "end, exit\n";
		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */