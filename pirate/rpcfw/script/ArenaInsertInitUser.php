<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArenaInsertInitUser.php 20408 2012-05-15 10:57:09Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/ArenaInsertInitUser.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-15 18:57:09 +0800 (二, 2012-05-15) $
 * @version $Revision: 20408 $
 * @brief 
 * 
 **/


require_once MOD_ROOT . '/arena/ArenaDao.class.php';
require_once MOD_ROOT . '/user/index.php';

/**
 * 这个脚本在开服前运行。
 * 使用uid 10001到10005 pid 1到5 调用UserLogic::createUser创建5个用户
 * 然后调用ArenaDao::insert插入竞技场
 * @author idyll
 *
 */
class ArenaInsertInitUser extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		//主角英雄的初始等级
		$INIT_LEVEL = 20;
		
		//check 
		$data = new CData();
		$count = $data->selectCount()->from("t_arena")->where(array('uid', '!=', 0))->query();
		
		$count = $count[0]['count'];
		if ($count!=0)
		{
			throw new Exception('fail to insert npc for arena, the t_arena is not empty');
		}		
		
		$uid_init = 10001;
		$pid_init = 1;
		
		$arrUname = array(
			'竞技场守卫1',
			'竞技场守卫2',
			'竞技场守卫3',
			'竞技场守卫4',
			'竞技场守卫5',
		);		
		
		for($i = 0; $i < 5; $i++)
		{
			$utid = mt_rand(1, 6);					 
			$uid = $uid_init + $i;
			$pid = $pid_init + $i;
			try
			{
				UserLogic::getUser($uid);
				Logger::fatal('get user by uid %d suc, the data isnot clear', $uid);
			}
			catch (Exception $e)
			{
				UserLogic::createUser($pid, $utid, $arrUname[$i], $uid);
				$arrHero = HeroDao::getHeroesByUid($uid, array('hid', 'htid'));
				$hero = current($arrHero);
				$hid = $hero['hid'];
				Logger::debug('hero :%s', $hero);
				HeroDao::update($hid, array('level'=>$INIT_LEVEL));
			}			
			$arrField = array('position'=>$i+1,
				'history_min_position' => $i+1,
				'va_reward' => array(), 
				'va_opponents'=>array());
			ArenaDao::insert($uid, $arrField);		
		}
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */