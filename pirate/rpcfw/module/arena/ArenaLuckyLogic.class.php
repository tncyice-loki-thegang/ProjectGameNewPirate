<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArenaLuckyLogic.class.php 26661 2012-09-05 03:00:13Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/arena/ArenaLuckyLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-05 11:00:13 +0800 (三, 2012-09-05) $
 * @version $Revision: 26661 $
 * @brief 
 *  
 **/







class ArenaLuckyLogic
{		
	/**
	 * 这个给脚本执行的，
	 * 产生幸运奖的排名
	 */
	public static function generatePosition()
	{
		if (!ArenaRound::isLock())
		{
			Logger::trace('today is not the day of generatePosition');
			return;
		}
		
		Logger::info('start to generatePosition for lucky postion');

		$curTime = Util::getTime();
		
		//数据查询到的发奖日期为空，说明是开服前运行脚本
		$curDateFromDb = ArenaRound::getCurRoundDate();
		if (empty($curDateFromDb))
		{
			//开服为第三天（包括当天）发奖
			$beginTime = strtotime(GameConf::SERVER_OPEN_YMD);
			$beginTime += (ArenaDateConf::LAST_DAYS - 1) * 86400; 
			$beginDate = strftime("%Y%m%d", $beginTime);
//			echo '竞技场发奖日期为:' . $beginDate . "\n";
//			echo "别提前开服，否则竞技场发奖时间可能会错误\n";
		}
		else
		{
			//三天后发奖
			$beginDateStr =  "+ " . ArenaDateConf::LAST_DAYS . " day";
			$beginDate = intval(strftime("%Y%m%d", strtotime($beginDateStr, $curTime)));
		}
		
		$arrPos = array();
		$va_lucky = array();
		foreach (ArenaConf::$LUCKY_POSITION_CONFIG as $cfg)
		{
			//循环多少次， 循环这么多次还没出结果可能是策划配置错误
			$circleNum = 1000;
			while($circleNum-- > 0)
			{
				$pos = rand($cfg[0], $cfg[1]);
				if (!in_array($pos, $arrPos))
				{
					$arrPos[] = $pos;
					$va_lucky[] = array('position'=>$pos, 'item'=>$cfg[2]);
					break;
				}
			}
		}
		
		$arrField = array('begin_date'=>$beginDate, 'va_lucky'=>$va_lucky);
		$arrRet = ArenaLuckyDao::insert($arrField);
		Logger::debug('insert return:%s', $arrRet);		
		
		
		ArenaRound::setCurRound();
		ArenaRound::setCurRoundDate();
		
	}
	
	public static function diffDate($date1, $date2)
	{
		$datetime1 = date_create($date1);
		$datetime2 = date_create($date2);
		$interval = date_diff($datetime2, $datetime1);
		return $interval->format('%R%a');		
	}
	
	/**
	 * 给脚本执行
	 * 给幸运排名发奖
	 * 每一步都update db， 出错后可以重做。
	 * 现在量很小，只有10个。如果量比较大，可以对position uid 使用select in
	 */
	public static function rewardLuckyPosition()
	{
		if (!ArenaRound::isLock())
		{
			Logger::trace('today is not the day of rewardLuckyPosition');
			return;
		}
		
		Logger::info('start to rewardLuckyPosition for lucky postion');
		
		//系统消息
		$message = ChatTemplate::makeMessage(ChatTemplateID::MSG_ARENA_LUCKY_AWARD, array());
		ArenaLogic::arenaBroadcast($message);
		
		$curTime = Util::getTime();
		//$begin_date = strftime("%Y%m%d", $curTime);
		$begin_date = ArenaRound::getCurRoundDate();		
		$arrLucky = ArenaLuckyDao::get($begin_date, array('va_lucky'));
		if (empty($arrLucky))
		{
			Logger::fatal('fail to reward lucky position, cannot get lucky position.');
			throw new Exception('sys');
		}
		
		$curRound = ArenaRound::getCurRound();
		$arrField = array('uid');
		$index = -1;
		$arrUpdateLucky = $arrLucky['va_lucky'];
		foreach ($arrLucky['va_lucky'] as $key => $luckyPos)
		{
			++$index;
			//已有uid，说明已经发过奖了
			if (isset($luckyPos['uid']))
			{
				continue;
			}
			
			$pos = $luckyPos['position'];
			$arrInfo = ArenaDao::getByPos($pos, $arrField);
			Logger::debug('get info:%s', $arrInfo);
			if (empty($arrInfo))
			{
				Logger::info('fail to reward lucky position %d, fail to get info by position', $pos);
				continue;
			}
			$arrUpdateLucky[$key]['uid'] = $arrInfo['uid'];
			$user = EnUser::getUserObj($arrInfo['uid']);
			$arrUpdateLucky[$key]['uname'] = $user->getUname();
			$arrUpdateLucky[$key]['utid'] = $user->getUtid();	

			MailTemplate::sendArenaLuckyAward($arrInfo['uid'], $curRound, $pos, 
				array(ArenaConf::$LUCKY_POSITION_CONFIG[$index][2] => 1, ArenaConf::LUCKY_ITEM_ID=>1), true);
			
			ArenaLuckyDao::update($begin_date, array('va_lucky' => $arrUpdateLucky));				
		}
		//unset($luckyPos);
		Logger::debug('finish rewarding lucky position:%s', $arrUpdateLucky);
	
	} 
	
	public static function getRewardLuckyList()
	{
		$arrReward = ArenaLuckyDao::getRewardLuckyList(array('va_lucky'));		
		$arrRet = array('last'=>array(), 'current'=>array());
		if (isset($arrReward[0]['va_lucky']))
		{
			$arrRet['last'] = $arrReward[0]['va_lucky'];
			foreach($arrRet['last'] as &$ret)
			{
				if (isset($ret['utid']))
				{
					$ret['master_htid'] = UserConf::$USER_INFO[$ret['utid']][1];
				}
			}
		}
		if (isset($arrReward[1]['va_lucky']))
		{
			$arrRet['current'] = $arrReward[1]['va_lucky'];
		}
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */