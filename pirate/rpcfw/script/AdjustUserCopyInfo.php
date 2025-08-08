<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AdjustUserCopyInfo.php 18092 2012-04-06 08:55:11Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/AdjustUserCopyInfo.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-04-06 16:55:11 +0800 (五, 2012-04-06) $
 * @version $Revision: 18092 $
 * @brief 
 *  
 **/

/**
 * 这个脚本在更新服务器时候运行，用于修复用户的副本数据：如果策划新配置了隐藏副本，需要通过这个脚本给那些曾经达到开启条件的人开启新的副本
 * 仅于策划配置了新的副本时刻或者修数据的时候使用
 *
 * 执行如下操作：
 *     扫描所有用户的所有副本，如果    1. 击败过了策划们配置的副本开启所需的部队。 
 *                         2. 并未开启这个副本。
 *     那么则给这个用户增加这个副本。
 */
class AdjustUserCopyInfo extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		/**************************************************************************************************************
 		 * 对策划们配置的副本信息进行过滤
 		 **************************************************************************************************************/
		// 先记录下需要进行同步的所有副本 —— 通过任务开启的副本不需要进行同步
		$copies = array();
		// 循环查看副本表
		foreach (btstore_get()->COPY as $copy)
		{
			// 仅仅需要查看那些不通过任务开启的副本信息
			if (empty($copy['task_open']) && !empty($copy['enemy_open']))
			{
				// 如果这个副本不需要通过任务开启的，记录这个副本ID
				$copies[] = $copy['id'];
			}
		}

		/**************************************************************************************************************
 		 * 获取所有用户信息
 		 **************************************************************************************************************/
		// 定义偏移量
		$offset = 0;
		// 循环查询， 以获取所有用户ID， 一次只能拉取100个
		do
		{
			// 获取一百个uid数据
			$uidList = UserDao::getArrUser($offset, DataDef::MAX_FETCH, array('uid'));
			// 查看实际获取的个数
			$length = count($uidList);
			// 重新计算偏移量
			$offset += $length;

			/**********************************************************************************************************
 		 	 * 循环查看所有用户的副本数据
 		 	 **********************************************************************************************************/
			foreach ($uidList as $v)
			{
				// 获取此人的所有副本信息
				$copiesInfo = EnCopy::getUserCopiesByUid($v['uid']);
				// 循环查看策划们配置的所有副本信息
				foreach ($copies as $copyID)
				{
					// 没有开启这个副本，那么需要进行检查
					if (!isset($copiesInfo[$copyID]) &&
					    EnCopy::isSomeOneEnemyDefeat($copiesInfo, btstore_get()->COPY[$copyID]['enemy_open']))
					{
						// 如果已经击败过需要击败的部队，那么需要给人家增加一条副本信息了
//						EnCopy::openNewCopyForSomeOne($v['uid'], $copyID);
						echo chr(13).chr(10).'Add a new copy for '.$v['uid'].', copy id is '.$copyID.'.'.chr(13).chr(10);
					}
				}
			}
		}
		// 如果能拉取满一百个，说明还没有结束，那么继续进行循环
		while ($length == DataDef::MAX_FETCH);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */