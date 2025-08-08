<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GetSpendReward.php 39156 2013-02-23 06:45:59Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/GetSpendReward.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-23 14:45:59 +0800 (六, 2013-02-23) $
 * @version $Revision: 39156 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class GetSpendReward extends BaseScript
{
	protected function get($offset, $limit)
	{
		$data = new CData();
		$ret = $data->select(array('uid', 'uname', 'va_user'))->from('t_user')->where('uid', '>', 0)
			->orderBy('uid', true)->limit($offset, $limit)->query();
		
		return $ret;
	}
	
	protected function filter($arrUser, $beginRewardId)
	{
		foreach ($arrUser as $key=>$user)
		{
			if (!isset($user['va_user']['spend_reward'][$beginRewardId]))
			{
				unset($arrUser[$key]);
			}
		}
		return $arrUser;
	}
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$beginRewardId = $arrOption[0];
		$offset = 0;
		$limit = 100;
		$group = RPCContext::getInstance()->getFramework()->getGroup();
		
		$total = 3000;
		while($total-->0)
		{
			$arrUser = $this->get($offset, $limit);
			if (empty($arrUser))
			{
				break;
			}
			
			$arrUser = $this->filter($arrUser, $beginRewardId);
			
			foreach ($arrUser as $user)
			{
				echo $group . " " . $user['uid'] ." " . $user['uname']  . " ";
				
				for($i=0; $i<strlen($user['va_user']['spend_reward']); $i++)
				{
					echo $i . " ";
				}
				echo "\n";
			}	

			$offset += $limit;
		}
		

	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */