<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Gm.class.php 24325 2012-07-20 07:27:26Z HaidongJia $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/gm/Gm.class.php $
 * @author $Author: HaidongJia $(hoping@babeltime.com)
 * @date $Date: 2012-07-20 15:27:26 +0800 (五, 2012-07-20) $
 * @version $Revision: 24325 $
 * @brief
 *
 **/
class Gm implements IGm
{

	/* (non-PHPdoc)
	 * @see IGm::reportClientError()
	 */
	public function reportClientError($message)
	{

		$filename = LOG_ROOT . '/' . GmConf::AS_LOG_FILE;
		$file = fopen ( $filename, 'a+' );
		if (empty ( $file ))
		{
			Logger::fatal ( "report client error failed, can't open file:%s", $filename );
			return;
		}

		$context = RPCContext::getInstance ();
		$framework = $context->getFramework ();
		$arrMicro = explode ( " ", microtime () );
		$time = date ( 'Ymd H:i:s' );
		$microtime = sprintf ( "%06d", intval ( 1000000 * $arrMicro [0] ) );
		$log = sprintf ( "[%s %s][logid:%s][client:%s][server:%s][uid:%s]%s\n", $time, $microtime,
				$framework->getLogid (), $framework->getClientIp (), $framework->getServerIp (),
				$context->getUid (), $message );
		fputs ( $file, $log );
		fclose ( $file );
	}

	public function silentUser($uid, $time)
	{

		$user = EnUser::getUserObj ( $uid );
		$user->banChat ( $time );
		$user->update ();
	}

	public function getTime()
	{

		return RPCContext::getInstance ()->getFramework ()->getRequestTime ();
	}

	public function newResponse($uid)
	{

		return RPCContext::getInstance ()->sendMsg ( array ($uid ), 're.gm.newMsg', 0 );
	}

	/* (non-PHPdoc)
	 * @see IGm::newBroadCast()
	 */
	public function newBroadCast()
	{
		return RPCContext::getInstance ()->sendMsg ( array (0), 're.chat.getAnnounce', array() );
	}

	/* (non-PHPdoc)
	 * @see IGm::newBroadCastTest()
	 */
	public function newBroadCastTest($uid, $bid)
	{
		$uid = intval($uid);
		return RPCContext::getInstance ()->sendMsg ( array ($uid), 're.chat.getAnnounce', array($bid) );
	}

	/* (non-PHPdoc)
	 * @see IGm::sendRankingActivityLevelReward()
	 */
	public function sendRankingActivityLevelReward($list)
	{
		return rankingReward::sendRankingActivityReward('level', $list);
	}

	/* (non-PHPdoc)
	 * @see IGm::sendRankingActivityArenaReward()
	 */
	public function sendRankingActivityArenaReward($list)
	{
		return rankingReward::sendRankingActivityReward('arena', $list);
	}

	/* (non-PHPdoc)
	 * @see IGm::sendRankingActivityPrestigeReward()
	 */
	public function sendRankingActivityPrestigeReward($list)
	{
		return rankingReward::sendRankingActivityReward('prestige', $list);
	}

	/* (non-PHPdoc)
	 * @see IGm::sendRankingActivityOfferReward()
	 */
	public function sendRankingActivityOfferReward($list)
	{
		return rankingReward::sendRankingActivityReward('offer', $list);
	}

	/* (non-PHPdoc)
	 * @see IGm::sendRankingActivityCopyReward()
	 */
	public function sendRankingActivityCopyReward($list)
	{
		return rankingReward::sendRankingActivityReward('copy', $list);
	}

	/* (non-PHPdoc)
	 * @see IGm::sendRankingActivityGuildReward()
	 */
	public function sendRankingActivityGuildReward($list)
	{
		return rankingReward::sendRankingActivityGuildReward($list);
	}

	public function sendSysMail($recieverUid, $subject, $content, $items)
	{
		//如果标题或者内容为空,则返回
		if ( empty($subject) || empty($content) )
		{
			return FALSE;
		}

		//如果物品数组为空
		if ( empty($items) )
		{
			$return = MailLogic::sendSysMail($recieverUid, $subject, MailConf::DEFAULT_TEMPLATE_ID, array());
		}
		else
		{
			$return = MailLogic::sendSysItemMailByTemplate($recieverUid,
				 MailConf::DEFAULT_TEMPLATE_ID, $subject, $content, $items);
		}

		if ( $return )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function getInfoBeforeExit() 
	{
		return array();
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */