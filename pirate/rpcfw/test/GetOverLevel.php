<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GetOverLevel.php 20068 2012-05-09 09:27:25Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/GetOverLevel.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-09 17:27:25 +0800 (三, 2012-05-09) $
 * @version $Revision: 20068 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript VipMOdifyTest name=游戏名 vip=10
 * Enter description here ...
 * @author idyll
 *
 */

class GetOverLevel extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$num = 5000;
		$level = 50;
		if (isset($arrOption[0]))
		{
			$num = intval($arrOption[0]);
		}
		
		if (isset($arrOption[1]))
		{
			$level = 50;
		}
		
		$allUser = array();
		$allUidPid = array();
		
		$limit = CData::MAX_FETCH_SIZE;
		$offset = 0;		
		while ($offset < $num)
		{
			if (($offset + $limit) > $num)
			{
				$limit = $num - $offset;				
			}	
			
			$arrUser = UserLogic::getTopLevel($offset, $limit);
			//等级已经超了
			if ($arrUser[0]['level'] < $level)
			{
				break;
			}
			
			$arrUid = Util::arrayExtract($arrUser, 'uid');
			$arrUidPid = Util::getArrUser($arrUid, array('uid', 'pid'));
			$allUidPid += $arrUidPid;	
								
			$allUser = array_merge($allUser, $arrUser);

			$offset += $limit;
		}
		
		$handle = fopen('/tmp/top_level_' . $level, 'w');
		$i = 1;
		foreach ($allUser as $user)
		{
			if ($user['level'] < $level)
			{
				continue;
			}
			$str = $allUidPid[$user['uid']]['pid'] . "\t" . $user['level'] . "\t" . $user['uname'] . "\n"; 
			fwrite($handle, $str);
			$i++;
		}
		fclose($handle);
		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */