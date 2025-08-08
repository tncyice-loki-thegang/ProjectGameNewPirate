<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UserSub.class.php 36404 2013-01-18 10:08:51Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/UserSub.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2013-01-18 18:08:51 +0800 (五, 2013-01-18) $
 * @version $Revision: 36404 $
 * @brief
 *
 **/

class UserSub extends BaseScript
{
	
	public function get($uid)
	{
		$data = new CData();
		$ret = $data->select(array('uid', 'belly_num', 'experience_num', 'gold_num'))->from('t_user')->where('uid', '=', $uid)->query();
		if (!empty($ret))
		{
			return $ret[0];
		}
		return $ret;
	}
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		
		if (count($arrOption)!=4)
		{
			exit("argv err\n");
		}
		
		$uid = intval($arrOption[0]);
		$belly = $arrOption[1];
		$experience = $arrOption[2];
		$gold = $arrOption[3];
		
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);		
		sleep(1);
		
		$ret = $this->get($uid);
		if (empty($ret))
		{
			Logger::fatal('fail to get user by uid %d', $ret);
			exit("$uid err");
		}
		
		$arrField = array();
		if ($belly!=0)
		{
			$arrField['belly_num'] = $ret['belly_num'] - $belly;
			if ($arrField['belly_num'] < 0 )
			{
				$arrField['belly_num'] = 0;
			} 			
		}
		
		if ($experience!=0)
		{
			$arrField['experience_num'] = $ret['experience_num'] - $experience;
			if ($arrField['experience_num'] < 0 )
			{
				$arrField['experience_num'] = 0;
			}
		}
		
		if ($gold!=0)
		{
			$arrField['gold_num'] = $ret['gold_num'] - $gold;
			if ($arrField['gold_num'] < 0 )
			{
				$arrField['gold_num'] = 0;
			}
		}
		
		Logger::warning('bug_fix update uid %d from %s to %s', $uid, $ret, $arrField);
		UserDao::updateUser($uid, $arrField);
		
		$ret = $this->get($uid);
		foreach ($arrField as $k=>$v)
		{
			if ($ret[$k]!=$v)
			{
				Logger::fatal('check err');
				exit("check error\n");
			}
		}
		
		echo "ok\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */