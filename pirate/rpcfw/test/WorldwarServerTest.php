<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldwarServerTest.php 37929 2013-02-02 14:35:16Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/WorldwarServerTest.php $
 * @author $Author: YangLiu $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-02 22:35:16 +0800 (六, 2013-02-02) $
 * @version $Revision: 37929 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript VipMOdifyTest uname vip
 * Enter description here ...
 * @author idyll
 *
 */

class WorldwarServerTest extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		
		$data = new CData();
		$data->useDb(WorldwarConfig::KFZ_DB_NAME);
		// 设置查询语句
		$arrRet = $data->select(array('server_id'))
             		   ->from('t_server_info')
			 		   ->where(array('server_id', '!=', 0))
		               ->query();
		
		foreach ($arrRet as $v)
		{
	    	// 声明平台接口
			$platform = ApiManager::getApi(true);
			// 获取所有服的名字
			$ret = $platform->users('getServerGroup', array('pid' => 1, 
															'servid' => $v["server_id"], 
															'spanid' => 2,
															'action' => 'getServerGroup'));
			// 抽出key，返回
			$key = array_keys($ret);
			
			if (empty($key[0]))
			{
				echo("ServerInfo error, server id is " . $v["server_id"] . "\n");
			}
			else 
			{
//				echo "ok, team is " . $key[0] . "\n";
			}
		}
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */