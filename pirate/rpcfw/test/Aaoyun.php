<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Aaoyun.php 25182 2012-08-03 08:03:49Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/Aaoyun.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-03 16:03:49 +0800 (五, 2012-08-03) $
 * @version $Revision: 25182 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class Aaoyun extends BaseScript
{
	protected function get($pid)
	{
		$data = new CData();
		$ret = $data->select(array('uid', 'belly_num', 'experience_num'))->from('t_user')->where('uid', '>', 0)
			->where('pid', '=', $pid)->query();
		
		if (empty($ret))
		{
			echo "fail to get by pid:$pid\n";	
		}
		return $ret[0];
	}
	
	protected function update($uid, $arrField)
	{
		$data = new CData();
		$data->update('t_user')->set($arrField)->where('uid', '=', $uid)->query();
	}
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$curGroup  = RPCContext::getInstance()->getFramework()->getGroup();
		$handle = fopen("/home/pirate/rpcfw/test/aoyun.txt", 'r') or exit("fail to open file");
		while (($line=fgets($handle))!=null)
		{
			$arr = explode("\t", $line);
			$group = $arr[2];
			if ($group!=$curGroup)
			{
				continue;
			}
			
			$pid = $arr[3];
			$belly = $arr[6];
			$experience = $arr[7];
			
			$ret = $this->get($pid);
			$uid = $ret['uid'];
			$belly = $ret['belly_num'] - $belly;
			if ($belly<0)
			{
				$belly = 0;
			}
			
			$experience = $ret['experience_num'] - $experience;
			if ($experience<0)
			{
				$experience=0;
			}			
			
			$arrField = array('belly_num'=>$belly, 'experience_num'=>$experience);
			$this->update($uid, $arrField);	
			Logger::info('aoyun, game:%s, pid:%d, new:%d, %d; old:%d, %d ',
				$group, $pid, $belly, $experience, $ret['belly_num'], $ret['experience_num']);
			echo "game:$group, pid:$pid\n";		
		}
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */