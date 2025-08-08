<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lijinfeng@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */


class SpringFestivalWelfareLogic
{
	// 如下两变量，和配置密切相关，
	private static $REWARD_MASK = 0xfe;
	private static $REWARD_DAY_MAX = 7;

	static function getInfo($uid = 0)
	{
		$ret = array('ret' => 'err');
		if(empty($uid))
		{
			Logger::fatal("uid invalid");
			throw new Exception("fake");
		}
		
		if(!self::checkActivityTime())
		{
			Logger::fatal("invalid activity time");
			throw new Exception("fake");	
		}
		
		$ret = SpringFestivalWelfareDao::getInfo($uid);
		if(!empty($ret))
		{
			if(self::validateSprFestWelInfo($ret))
			{
				// 刷新下
				SpringFestivalWelfareDao::updateWelfare($uid,$ret);
			}
		}else
		{
			$ret = self::initWelfareInfo($uid);
		}
		
		$ret['ret'] = 'ok';
		return $ret;
	}
	
	
	/**
	 * 检查活动时间
	 * @return unknown_type
	 */
	static protected function checkActivityTime()
	{
		$cur_time = Util::getTime();	
		if($cur_time < btstore_get()->REWARD_SPRFEST_WELFARE['begin_time'] || 
			$cur_time > btstore_get()->REWARD_SPRFEST_WELFARE['end_time'] )
		{
			return false;	
		}
		
		return true;
	}
	
	
	/**
	 * 检查新年福利数据，是不是过时的数据
	 * @param $info
	 * @return unknown_type
	 */
	static protected function validateSprFestWelInfo(&$info)
	{
		$isChange = false;
	
		// 老数据了
		if($info['day_time'] < btstore_get()->REWARD_SPRFEST_WELFARE['begin_time'])
		{
			$info['day'] = 0;
			$info['recieve'] = 0;
			$isChange = true;
		}
		
		if (!Util::isSameDay($info['day_time']))
		{
			$info['day'] += 1;
			$info['day_time'] = Util::getTime();
			$isChange = true;
		}
			
		return $isChange;
	}
	
	
	
	/**
	 * 领取福利索引
	 * @param $uid 			用户id
	 * @param $gift_index	福利数据索引
	 * @return unknown_type
	 */
	static function recieveSprFestWelfare($uid,$gift_index)
	{
		// 是不是在活动区间
		if(!self::checkActivityTime())
		{
			Logger::fatal("invalid activity time");
			throw new Exception("fake");	
		}
	
		$ret = array('ret' => 'err');
		if($gift_index < 1 || $gift_index > btstore_get()->REWARD_SPRFEST_WELFARE['prize_size_max'])
		{
			Logger::fatal("invalid welfare index");
			throw new Exception("fake");
		}
		
		$res = self::getInfo($uid);
		if($res['ret'] != 'ok')
		{
			Logger::warning("getInfo failed");
			return $ret;
		}
		
		// 修正下天数
		if(!Util::isSameDay($res['day_time']))
		{
			$res['day']++;
		}
		
		// 检查
		$isok = self::checkRecieveCondition($res,$gift_index);
		if($isok != 'ok')
		{
			Logger::warning("checkRecieveCondition failed");
			return $ret;
		}
		
		$reward_id = btstore_get()->REWARD_SPRFEST_WELFARE['prize_data'][$gift_index]['item'];
		if(!isset(btstore_get()->REWARD_ONLINE_LIB[$reward_id]))
		{
			Logger::fatal("invalid reward id %d",$reward_id);
			throw new Exception('fake');
		}
		
		
		// 这里很重要哦，
		$reward_mask = $res['recieve'] | (1 << $gift_index);
		Logger::warning("reward_mask %d",$reward_mask);
		if( ($reward_mask & self::$REWARD_MASK) == self::$REWARD_MASK )
		{
			if($res['day'] < self::$REWARD_DAY_MAX)
			{
				Logger::fatal("invalid day %d,logic fatal",$res['day']);
				throw new Exception("fake");
			}
			
			$res['day'] -= self::$REWARD_DAY_MAX;
			$reward_mask = 0;
		}
		
		// 先消费，后增加
		$arrRet = array(
			'day' => $res['day'],
			'day_time' => Util::getTime(),
			'recieve' => $reward_mask
		);
		SpringFestivalWelfareDao::updateWelfare($uid,$arrRet);
		
		// 发福利了
		$ret = RewardUtil::rewardById($uid,$reward_id,true);
		if($reward_mask == 0) // 重置了，通知客户端
		{
			$ret['day'] = $res['day'];
			$ret['recieve'] = $reward_mask;
		}
		$ret['ret'] = 'ok';
		return $ret;
	}
	
	
	/**
	 * 检查当前领取条件
	 * @param $info		福利数据
	 * @param $gift_idx	领取索引
	 * @return 
	 * 		'err'/'ok'/'redo'
	 */
	static function checkRecieveCondition($info,$gift_idx)
	{
		if(empty($info))
		{
			return 'err';
		}
		
		if(btstore_get()->REWARD_SPRFEST_WELFARE['prize_data'][$gift_idx]['day'] > $info['day'])
		{
			Logger::warning("day not enough");
			return 'err';
		}
		
		if($info['recieve'] & (1 << $gift_idx))
		{
			Logger::warning("has recieved");
			return 'redo';
		}
		
		return 'ok';
	}
	
	
	
	/**
	 * 初始化福利数据
	 * @param $uid
	 * @return unknown_type
	 */
	static protected function initWelfareInfo($uid)
	{
		$arrField = array( 
			'uid' => $uid,
			'day' => 1,
			'day_time' => Util::getTime(),
			'recieve' => 0		
		);
		
		SpringFestivalWelfareDao::insertWelfare($arrField);
		
		return $arrField;
	} 
	
	
	/**
	 * 新年福利登陆事件
	 * @return unknown_type
	 */
	static function login($uid)
	{
		if(!self::checkActivityTime())
		{
			return;
		}
		
		$ret = SpringFestivalWelfareDao::getInfo($uid);
		if(!empty($ret))
		{
			if(self::validateSprFestWelInfo($ret))
			{
				// 刷新下
				SpringFestivalWelfareDao::updateWelfare($uid,$ret);
			}
		}else
		{
			self::initWelfareInfo($uid);
		}
	}
	
}
