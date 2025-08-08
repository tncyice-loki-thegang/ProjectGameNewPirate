<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: CheckDB.php 24321 2012-07-20 06:38:28Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/CheckDB.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-20 14:38:28 +0800 (五, 2012-07-20) $
 * @version $Revision: 24321 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class CheckDB extends BaseScript
{
	
	protected static function insertRandomNameTest($name, $gender)
	{
		$data = new CData();
		$ret = $data->insertInto("t_random_name")->values(array('name'=>$name, 'gender'=>$gender))->query();
		if ($ret['affected_rows']==1)
		{
			return 'ok';
		}
		return 'fail';		
	}
	
	protected static function updateRandomNameTest($name, $status)
	{
		$ret = UserDao::setRandomNameStatus($name, $status);
		if ($ret['affected_rows']==1)
		{
			return 'ok';
		}
		return 'fail';	
	}
	
	
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		echo "check random name\n";
		$ret = UserLogic::getRandomName(10, 0);
		if (empty($ret))
		{
			exit("error. 随机名字表是空的\n");
		}
		echo "ok\n";
		
		echo "check idgen. 如果这里没成功，检查一下dataproxy下的data目录是否有问题\n";
		$hid = IdGenerator::nextId("hid");
		if ($hid==0)
		{
			exit("error\n");
		}
		echo "ok\n";
		
		echo "check insert sql\n";
		$name = "我是东哥" . rand(0, 99);
		if ('ok'!= self::insertRandomNameTest($name, 1))
		{
			exit("error.\n");
		}		
		echo "ok\n";
		
		echo "check update sql\n";
		if ('ok'!= self::updateRandomNameTest($name, 1))
		{
			exit ("error.\n");
		}
		echo "ok\n";
		
		echo "check user table\n";
		$arrRet = UserDao::getArrUser(0, 10, array('uid'));
		if (!empty($arrRet))
		{
			exit ("error. user表不是空的\n");
		}
		
		echo "--------------------------------\n初始化之前的检查结束, 可以接着初始化\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */