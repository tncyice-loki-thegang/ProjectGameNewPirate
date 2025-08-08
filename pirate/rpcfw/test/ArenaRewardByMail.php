<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArenaRewardByMail.php 24618 2012-07-24 03:40:03Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/ArenaRewardByMail.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-24 11:40:03 +0800 (二, 2012-07-24) $
 * @version $Revision: 24618 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */
class ArenaRewardByMail extends BaseScript
{
	//date -d"20120723 22:00:00" +%s
	const MIN_TIME = 1343052000;
	
	//date -d"20120723 22:30:00" +%s
	const MAX_TIME = 1343053800	;

	protected function getMailExtra($uid)
	{
		
		$data = new CData();
		$ret = $data->select(array('template_id', 'mid', 'va_extra'))
			->from('t_mail')
			->where('reciever_uid', '=', $uid)
			->where ('template_id', '=', 502)
			->where ('recv_time', 'between', array(self::MIN_TIME, self::MAX_TIME))
			->query();
		if (!empty($ret))
		{
			if (count($ret)!=1)
			{
				Logger::fatal('more than 1 mail, %s', $ret);
				return array();
			}
			return $ret[0]['va_extra'];
		}
		return $ret;
	}
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$num=10000;
		$offset = 0;
		$limit = 100;
		
		$exe = 10;
		
		Logger::fatal('attention. reward arena again by mail');
		while ( $num-- > 0 )
		{
			$arrUserInfo = UserDao::getArrUser($offset, $limit, array('uid'));
			//Logger::debug('vip auto ics, arr user:%s', $arrUserInfo);
			if (empty($arrUserInfo))
			{
				Logger::fatal('attention. exit reward arena');
				break;
			}
			
			foreach ($arrUserInfo as $userInfo)
			{
				Logger::info('idyll userInfo:%s', $userInfo);
				$uid = $userInfo['uid'];
				
				$ret = $this->getMailExtra($uid);
				if (empty($ret))
				{
					continue;
				}
				
				$round = $ret['data'][0]['arena_turn_num'];
				$position = $ret['data'][1]['arena_position'];
				$belly = $ret['data'][2];
				$experience = $ret['data'][3];
				$prestige = $ret['data'][4];
				//$gold = $ret['data'][5];				
				
				/*
				 * 竞技场奖励补发

由于服务器数据错误，7.23的竞技场奖励只发了一半。为您带来的不便我们深表歉意，特此为您补发剩余奖励，请查收。
恭喜您在竞技场第25轮中取得了第5名的成绩，补发奖励440000贝里，121800阅历，16000声望。
				 */
				$subject = '竞技场奖励补发';
				$content = "由于服务器数据错误，7.23的竞技场奖励只发了一半。为您带来的不便我们深表歉意，特此为您补发剩余奖励，请查收。恭喜您在竞技场第 $round 轮中取得了第 $position 名的成绩，补发奖励 $belly 贝里，$experience 阅历，$prestige 声望。";
				
				$mailType = MailType::SYSTEM_MAIL;
				$senderUid = MailConf::SYSTEM_UID;
				$templateId = MailConf::DEFAULT_TEMPLATE_ID;
				$recieverUid = $uid;
				$vaExtra = array(array($round, $position, $belly, $experience, $prestige));
				MailDao::saveMail ( $mailType, $senderUid, $recieverUid, $templateId, $subject,
				$content, $vaExtra );				
				RPCContext::getInstance ()->sendMsg ( array (intval($recieverUid) ), 're.mail.newMail', array () );
				
				$user = EnUser::getUserObj($userInfo['uid']);
				
				$user->addBelly($belly);
				$user->addExperience($experience);
				$user->addPrestige($prestige);
				//$user->addGold($gold);
				$user->update();
				
				Logger::warning('uid:%d, belly:%d, experience:%d, prestige:%d', 
					$uid, $belly, $experience, $prestige);				
			
			}
			
			$offset += $limit;
			
			sleep(1);
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */