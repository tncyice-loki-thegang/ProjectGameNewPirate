<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: UpdateTempleInfo.php 38255 2013-02-06 15:16:33Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/UpdateTempleInfo.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-06 23:16:33 +0800 (三, 2013-02-06) $
 * @version $Revision: 38255 $
 * @brief 
 *  
 **/


class UpdateTempleInfo extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 6)
		{
			exit('argv err.');
		}
		
		$uid = $arrOption[0];
		$uname = $arrOption[1];
		$rank = $arrOption[2];
		$serverID = $arrOption[3];
		$htid = $arrOption[4];
		$serverName = $arrOption[5];


		// 设置更新参数
		$set = array('session' => 2,
					 'rank' => $rank, 
					 'uid' => $uid, 
					 'uname' => $uname, 
					 'server_id' => $serverID, 
					 'server_name' => $serverName, 
					 'htid' => $htid, 
					 'msg' => '');
		// 更新到数据库
		WorldwarDao::updTempleInfo($set);
		
		echo "ok\n";
	}
}

// game004
// 20420 "西门吹雪" 1 004 11002 "最游戏.s3"

// game70042
// 21894 "国士无双。" 1 70042 11005 "51wan.s42"
// 20153 "钢铁加鲁鲁" 2 161 11003 "最游戏.s160"

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */