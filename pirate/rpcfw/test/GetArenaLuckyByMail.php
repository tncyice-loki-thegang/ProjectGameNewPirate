<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GetArenaLuckyByMail.php 26992 2012-09-11 07:56:37Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/GetArenaLuckyByMail.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-11 15:56:37 +0800 (二, 2012-09-11) $
 * @version $Revision: 26992 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */
class GetArenaLuckyByMail extends BaseScript
{
	//date date -d"20120904 22:00:00" +%s
	const MIN_TIME = 1346767200;
	
	//date -d"20120905 22:00:00" +%s
	const MAX_TIME = 1346853600	;

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
	
	protected function getArenaLucky($date)
	{
		$ret = ArenaLuckyDao::get($date, array('va_lucky'));
		if (empty($ret))
		{
			exit("fail to get by $date \n");
		}
		
		return $ret['va_lucky'];
	}
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$num=10000;
		$offset = 0;
		$limit = 100;		

		$begin_date = '20120904';
		$va_lucky = $this->getArenaLucky($begin_date); 
		
		
		$arrPositon = array();
		
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
				//Logger::info('idyll userInfo:%s', $userInfo);
				$uid = $userInfo['uid'];
				
				$ret = $this->getMailExtra($uid);
				if (empty($ret))
				{
					continue;
				}
				
				$round = $ret['data'][0]['arena_turn_num'];
				$position = $ret['data'][1]['arena_position'];
				
				foreach ($va_lucky as $lucky)
				{
					if ($lucky['position'] == $position)
					{ 
						$arrPositon [$position] = $uid;
					}
				}		
			
			}
			
			$offset += $limit;
			
			sleep(1);
		}
		
		
		var_dump($arrPositon);
		echo "pres any key continue\n";
		fgetc(STDIN);
		
		$index = -1;
		$curRound = 16;
		$arrUpdateLucky = $va_lucky;
		Logger::info('arena lucky, reward:%s', $va_lucky);
		foreach ($va_lucky as $key => $luckyPos)
		{
			++$index;
			//已有uid，说明已经发过奖了
			if (isset($luckyPos['uid']))
			{
				continue;
			}
			
			$pos = $luckyPos['position'];
			$uid = $arrPositon[$pos];
			
			$arrUpdateLucky[$key]['uid'] = $uid;
			$user = EnUser::getUserObj($uid);
			$arrUpdateLucky[$key]['uname'] = $user->getUname();
			$arrUpdateLucky[$key]['utid'] = $user->getUtid();	

			Logger::info('arena lucky, uid:%d, pos:%d', $uid, $pos);
			MailTemplate::sendArenaLuckyAward($uid, $curRound, $pos, 
				array(ArenaConf::$LUCKY_POSITION_CONFIG[$index][2] => 1, ArenaConf::LUCKY_ITEM_ID=>1), true);
			
			ArenaLuckyDao::update($begin_date, array('va_lucky' => $arrUpdateLucky));				
		}
		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */