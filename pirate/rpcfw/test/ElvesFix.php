<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ElvesFix.php 37817 2013-02-01 03:21:37Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/ElvesFix.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-01 11:21:37 +0800 (五, 2013-02-01) $
 * @version $Revision: 37817 $
 * @brief 
 *  
 **/

class ElvesFix extends BaseScript
{
	
	public function get()
	{
		$data = new CData();
		$ret = $data->select(array('uid', 'exp'))->from('t_elves')->where('exp', '>', '50' )->query();
		return $ret;
	}
	
	public function update($uid, $exp)
	{
		$data = new CData();
		$ret = $data->update('t_elves')->set(array('exp'=>$exp))->where('uid', '=', $uid)->query();
	}
	
	
	public $EXP_CON  = array(
			3500=>50,
			700=>10,
			2800=>40,
			); 

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$arrRet = $this->get();
		foreach ($arrRet as $ret)
		{
			$uid  = $ret['uid'];
			$proxy = new ServerProxy();
			$proxy->closeUser($uid);
			sleep(1);
			
			if (!isset($this->EXP_CON[$ret['exp']]))
			{
				Logger::fatal('unkown exp:%d  %s', $ret['exp'], $ret);
				exit("unkown exp " . $ret['exp']);
			}
			
			$exp = $this->EXP_CON[$ret['exp']];
			Logger::info('fix elves: %s to exp %d ', $ret, $exp);
			$this->update($uid, $exp);			
		}

	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */