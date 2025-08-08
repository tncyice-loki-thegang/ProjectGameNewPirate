<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: CountAllUserTrainMode.php 31672 2012-11-23 03:16:41Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/CountAllUserTrainMode.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-11-23 11:16:41 +0800 (五, 2012-11-23) $
 * @version $Revision: 31672 $
 * @brief 
 *  
 **/
class CountAllUserTrainMode extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		// 总执行次数，防止死循环
		$num = 20000;
		// 每次拉取一百个
		$offset = 0;
		$limit = 100;
		$arr = array();

		Logger::fatal('Attention. count all user train mode start.');
		while (--$num > 0)
		{
			// 从user表拉取一百个人出来
			$arrUserInfo = UserDao::getArrUser($offset, $limit, array('uid'));
			// 全表扫描完毕，退出
			if (empty($arrUserInfo))
			{
				Logger::fatal('Attention. count all user train mode end.');
				break;
			}
			// 对"拉出"的用户进行数据恢复
			foreach ($arrUserInfo as $u)
			{
				$trainInfo = TrainDao::getTrainInfo($u['uid']);
				if ($trainInfo !== false)
				{
					foreach ($trainInfo['va_train_info'] as $hero)
					{
						if (isset($arr[$hero['train_last_time']]))
						{
							++$arr[$hero['train_last_time']];
						}
						else 
						{
							$arr[$hero['train_last_time']] = 1;
						}
					} 
				} 
			}

			$offset += $limit;

			sleep(1);
		}
		Logger::fatal('Attention. count all user train mode ret is %s.', $arr);
		var_dump($arr);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */