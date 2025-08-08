<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: CheckInitDB.php 24805 2012-07-26 07:05:40Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/CheckInitDB.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-26 15:05:40 +0800 (四, 2012-07-26) $
 * @version $Revision: 24805 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class CheckInitDB extends BaseScript
{
	
	protected static function select($tbl, $where)
	{
		$data = new CData();
		$ret = $data->selectCount()->from($tbl)->where($where)->query();
		
		$ret =  $ret[0]['count'];
		return $ret;		
	}
	
	
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{		
		echo "check user, 应该是5行\n";
		$ret = self::select("t_user", array('uid', '>', '0'));
		echo "$ret row\n";
		if ($ret==0)
		{
			echo "error.-------------------------------\n";
		}
		
		echo "check timer, 不是0行即可\n";
		$ret = self::select("t_timer", array('tid', '>', '0'));
		echo "$ret row\n";		
		if ($ret==0)
		{
			echo "error.-------------------------------\n";
		}
		
		echo "check arena, 应该是5行\n";
		$ret = self::select("t_arena", array('position', '>', '0'));
		echo "$ret row\n";		
		if ($ret==0)
		{
			echo "error.-------------------------------\n";
		}
		
		echo "check arena_lucky, 应该是1行\n";
		$ret = self::select("t_arena_lucky", array('begin_date', '>', '0'));
		echo "$ret row\n";
		if ($ret==0)
		{
			echo "error.-------------------------------\n";
		}
		
		
		echo "检查结束\n";
		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */