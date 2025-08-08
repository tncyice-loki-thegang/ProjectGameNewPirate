<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TestReward.class.php 20202 2012-05-11 03:51:20Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestReward.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-11 11:51:20 +0800 (五, 2012-05-11) $
 * @version $Revision: 20202 $
 * @brief
 *
 **/

class TestReward extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$MAX_EXEC_TIME		= 65536;

		$subject_level50 = '封测等级50奖励';
		$content_level50 = '恭喜您在封测期间成功达到等级50级，以下是活动奖励200金币，请领取附件并在背包中使用物品，感谢您对游戏的支持，祝您游戏愉快。';
		$itemTemplates_level50 = array ( 70015 => 1 );

		$level_file_name = $arrOption[0];

		if ( !file_exists($level_file_name) )
		{
			Logger::FATAL('invalid level file name:%s!', $level_file_name);
			return;
		}

		$level_file = fopen($level_file_name, 'r');
		if ( !$level_file )
		{
			Logger::FATAL('open file:%s failed!', $level_file_name);
			return;
		}

		$level_list = array();
		for ( $i = 0; $i < $MAX_EXEC_TIME; $i++ )
		{
			$line = fgets($level_file);
			if ( empty($line) )
			{
				Logger::INFO('LEVEL LIST END!');
				break;
			}
			$data = explode("\t", $line);
			$pid = intval($data[0]);

			if ( in_array($pid, $level_list) )
			{
				Logger::FATAL('pid:%d already in level list!', $pid);
				return;
			}

			$level_list[] = $pid;
		}

		fclose($level_file);

		foreach ( $level_list as $pid )
		{
			$users = UserDao::getUsers($pid, array('uid'));
			if ( empty($users) )
			{
				Logger::INFO('INFO_LEVEL_LIST::pid:%d not exist user!', $pid);
				continue;
			}

			$uid = $users[0]['uid'];
			Logger::INFO('send level list mail to pid:%d uid:%d', $pid, $uid);
			MailLogic::sendSysItemMailByTemplate($uid,
					MailConf::DEFAULT_TEMPLATE_ID, $subject_level50,
					 $content_level50, $itemTemplates_level50);
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */