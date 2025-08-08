<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Send360.class.php 21041 2012-05-22 08:39:31Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-81/script/Send360.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-22 16:39:31 +0800 (二, 2012-05-22) $
 * @version $Revision: 21041 $
 * @brief
 *
 **/

class Send360 extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$MAX_EXEC_TIME = 65536;
		$USER_PRE_QUERY = 64;

		$subject = '360热血海贼王1区开服公告';
		$content = '360《热血海贼王》公测开启，探索神秘海域，获取海贼伙伴，做最伟大的海贼王！新区7大活动火爆来袭，充值返还，线上冲级，公会冲级等等精彩活动好礼等你拿，详情活动请点击：http://bbs.360.cn/5500259/253995030.html';

		$lastUid = 0;
		$uidCount = 0;
		$uidStart = 0;
		$send_time = Util::getTime() - 86400 * 2;

		for ( $i = 0; $i < $MAX_EXEC_TIME; $i++ )
		{
			$uids = UserDao::getArrUser($uidStart, $USER_PRE_QUERY, array('uid', 'last_login_time'));
			if ( empty($uids) )
			{
				Logger::INFO('EXEC END!LAST UID:%d, USER COUNT:%d', $lastUid, $uidCount);
				return;
			}
			else
			{
				foreach ( $uids as $info )
				{
					$uid = $info['uid'];
					$last_login_time = $info['last_login_time'];

					$lastUid = $uid;
					$uidCount++;
					Logger::INFO('send mail to uid:%d', $uid);
					Logger::INFO('send mail user count:%d', $uidCount);
					MailDao::saveMail ( MailType::SYSTEM_MAIL, 0, $uid, 0, $subject,
						$content, array() );
				}
				$uidStart += count($uids);
			}
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */