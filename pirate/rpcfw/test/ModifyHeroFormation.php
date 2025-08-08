<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ModifyHeroFormation.php 22307 2012-06-13 03:46:24Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/ModifyHeroFormation.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-06-13 11:46:24 +0800 (三, 2012-06-13) $
 * @version $Revision: 22307 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript GoldModifyTest.php uid gold
 * gold 表示设置为多少金币
 * Enter description here ...
 * @author idyll
 *
 */

class ModifyHeroFormation extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 1)
		{
			exit('argv err.');
		}
		
		$uid = $arrOption[0];
		//$hid = $arrOption[1];
		
		$data = new CData();
		$ret = $data->select(array('hid'))->from('t_hero')->where('uid', '=', $uid)->query();
		if (count($ret)!=1)
		{
			exit('sbl iao');
		}
		$hid = $ret[0]['hid'];
		echo "modify: $uid, $hid\n";
		
		
		$data = new CData();
		$ret = $data->update('t_hero_formation')->set(array('hid5'=>$hid))->where('uid', '=', $uid)->query();				
		var_dump($ret);				
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */