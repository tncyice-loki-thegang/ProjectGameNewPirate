<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Add4User.php 26431 2012-08-31 02:49:43Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/trunk/pirate/rpcfw/test/Add4User.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-31 10:49:43 +0800 (星期五, 31 八月 2012) $
 * @version $Revision: 26431 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */
class AstSum extends BaseScript
{
	
	protected function getStoneNum()
	{
		$data = new CData();
		//$data->useDb('pirate_stat');		
		$ret = $data->select(array('stone_num', 'uid'))->from('t_astrolabe_stone')->where('uid', '>', '0')->query();
		$ret = Util::arrayIndex($ret, 'uid');
		return $ret;		
	}
	
	protected function getConAst()
	{
		$data = new CData();
		//( select uid,  sum(all_levlup_exp) as exp from t_constellation_info group by uid ) t1,
		//( select uid,  sum(all_levlup_exp) as exp from t_astrolabe_info group by uid ) t2,
		$data->noCache();
		$ret = $data->select(array('uid', 'all_levlup_exp'))->from('t_constellation_info')->where('uid', '>', '0')->query();
		$ret = Util::arrayIndex($ret, 'uid');
		return $ret;
	}
	
	protected function getAstAst()
	{
		$data = new CData();
		$data->noCache();
		$ret = $data->select(array('uid', 'all_levlup_exp'))->from('t_astrolabe_info')->where('uid', '>', '0')->query();
		return $ret;
	}
	
	protected function getUser($arrUid)
	{
		$data = new CData();
		$ret = $data->select(array('uid', 'vip', 'pid'))->from('t_user')->where('uid', 'in', $arrUid)->query();
		$ret = Util::arrayIndex($ret, 'uid');
		return $ret;	
	}
	
	protected function getSpend($pid)
	{
		$data = new CData();
		
		//select sum(num) from pirate_stat.pirate_gold_log_201211 where pid=63649  
		//and direction=1 and function_key in (3301,3302) and server_key='2';
		$data->useDb('pirate_stat');
		$data->select("sum(num)")->from('pirate_gold_log_201210')->where('pid', '=', $pid);
		$data->where('direction', '=', '1')->where('function_key', 'in', array(3301, 3302));
		$ret10 = $data->where('server_key', '=', 2)->query();
		
		
		$data->useDb('pirate_stat');
		$data->select("sum(num)")->from('pirate_gold_log_201211')->where('pid', '=', $pid);
		$data->where('direction', '=', '1')->where('function_key', 'in', array(3301, 3302));
		$ret11 = $data->where('server_key', '=', 2)->query();
		
		$ret = $ret10['sum(num)'];
		$ret += $ret11['sum(num)'];		
		
	}
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$ret = $this->getStoneNum();
		if (empty($ret))
		{
			return;
		}
		
		$con = $this->getConAst();
		$ast = $this->getAstAst();
		
		foreach ($con as $tmp)
		{
			$ret[$tmp['uid']]['stone_num'] += $tmp['all_levlup_exp'];	
		}
		
		
		foreach ($ast as $tmp)
		{
			$ret[$tmp['uid']]['stone_num'] += $tmp['all_levlup_exp'];
		}				
		
		
		foreach ($ret as $uid=>$t)
		{
			if ($t['stone_num'] < 24000)
			{
				unset($ret[$uid]);
			}
		}
		
		$arrUid = array_keys($ret);
		$arrVip = $this->getUser($arrUid);
		
		foreach ($ret as $uid=>&$t)
		{
			$t['vip'] = $arrVip[$uid]['vip'];
			$t['pid'] = $arrVip[$uid]['pid'];
			$t['vip_num'] = (max(array($t['vip']-10, 0)) * 3500);
			//$spend = $this->getSpend($t['pid']);
			//$t['spend'] = $spend;
			
			//if ($t['stone_num'] < (24000 + $t['vip_num'] + $spend*2.5))
			{
				//unset($ret[$uid]);	
			}
			
			if ($t['stone_num'] < (2400 + $t['vip_num']))
			{
				unset($ret[$uid]);
			}
		}
		unset($t);
		
		$handle = fopen("/tmp/ast_info", 'a+');
		$group = RPCContext::getInstance()->getFramework()->getGroup();
		fwrite($handle, $group . "\n");
		fwrite($handle, serialize($ret) . "\n");		
		fclose($handle);

	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */