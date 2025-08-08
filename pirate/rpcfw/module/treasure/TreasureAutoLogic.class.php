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

class TreasureAutoLogic
{

	/**
	 * 自动寻宝
	 * @param $line 寻宝类型
	 * @return unknown_type
	 */
	static public function autoHunt($uid,$line)
	{
		$ret = 'err';
		if(!EnElves::hasTreasureElf($uid))
		{
			Logger::warning("services in Elves not open");
			return $ret;
		}
		
		$info = TreasureLogic::getInfo($uid);
		
		$ret = self::checkAutoHunt($uid,$info,$line);
		if($ret != 'ok')
		{
			// 更新配置
			if($ret == 'up')
			{
				$arrField = array(
					'treasure_auto_type' => $line,
					'treasure_auto_begin_time' => Util::getTime()
				);
			
				TreasureDao::update($uid,$arrField);
			}
			
			// 返航中
			if(!TreasureLogic::isReturn($info) || $ret == 'err')
				return $ret;
		}
		
		// 开始自动寻宝了
		$ret = self::doAutoHunt($uid,$info,$line);
		return $ret;
	}
	
	
	
	/**
	 * 自动寻宝
	 * @param $uid 用户ID
	 * @param $info	寻宝信息
	 * @param $line 寻宝物件类型
	 * @return 'ok'/'err'
	 */
	static public function doAutoHunt($uid,$info,$line)
	{
		// 得到当前寻宝图位置
		$auto_map_info = self::doAutoRollTreasureMap($info,$line);
		
		if(!isset($auto_map_info['va_treasure']['line'][$line]) || $auto_map_info['va_treasure']['line'][$line]['cur_pos'] == -1)
		{
			Logger::warning('cur map pos invalid');
			return 'err';
		}

		$trea = new Treasure;
		$ret = $trea->hunt($line,$auto_map_info['va_treasure']['line'][$line]['cur_pos']);
		return $ret;	
	}
	


	/**
	 * 检测自动寻宝情况
	 * @param $uid 用户ID
	 * @param $info 寻宝信息
	 * @return 'ok'、'err'、
	 * 			'up' //刷新配置
	 */
	static protected function checkAutoHunt($uid,$info,$line)
	{
		$ret = 'err';
		
		// 返航中
		if (!TreasureLogic::isReturn($info) || $info['treasure_auto_begin_time'] == 0)
		{
			if($info['treasure_auto_type'] != $line || $info['treasure_auto_begin_time'] != Util::getTime())
			{
				$ret = 'up';
			}
			
			return $ret;
		}
		
		
		if($info['hunt_aviable_num'] <= 0)
		{
			return $ret;
		}

		$ret = 'ok';
		return $ret;
	}
	
	
	/**
	 * 自动选择寻宝图
	 * @param $info  寻宝信息	
	 * @param $line  那个寻宝类型，帽子or披风
	 * @return 
	 * 		array va_treasure
	 */
	static protected function doAutoRollTreasureMap($info,$line)
	{
		if ($line!=1 && $line!=2)
		{
			Logger::warning('fail to refresh, the argv line %d error ', $line);
			throw new Exception('fake');			
		}
		
		$needGold = -1;
		$openNext = 0;
		
		$uid = RPCContext::getInstance()->getUid();
		$user = EnUser::getUserObj($uid);
		$level = $user->getLevel();

		//返航的时候不能刷新
		if (!TreasureLogic::isReturn($info))
		{
			Logger::warning('fail to refresh, return is not end');
			throw new Exception('fake');
		}
		
		$curPos = $info['line'][$line]['cur_pos'];
		
		// 如果refresh有数据
		foreach($info['line'] as $ln => $pos)
		{
			if($pos['cur_pos'] > 0)
			{
				if($line != $ln)
				{
					Logger::fatal("client $line not match refresh line");
					throw new Exception("fake");
				}
				$curPos = $pos['cur_pos'];
				Logger::debug("refresh has data:line:%d,pos:%d",$ln,$pos);
				break;
			}
		}
		
		$tsuLevel = btstore_get()->TREASURE_LEVEL[$info['cur_treasure_level']];	
		$posMax = count($tsuLevel['line'][$line]);
		
		// 更新的数据
		$arrUpdateField = array();
		$arrUpdateField['va_treasure'] = $info['va_treasure'];
		$arrUpdateField['experience_refresh_num'] = $info['experience_refresh_num'];
		
		// 消耗的阅历
		$costExp = 0;
		
		// 开始自动roll寻宝图
		do
		{
			
			// 下一张图不存在
			if (!isset($tsuLevel['line'][$line][$curPos + 1]))
			{
				Logger::warning('fail to refresh, next map is not exist');
				break;
			}
			$nextMapId = $tsuLevel['line'][$line][$curPos + 1];
			
			// 开下一张图等级不够
			if (btstore_get()->TREASURE[$nextMapId]['need_level'] > $level)
			{
				Logger::warning('fail to refresh, level is not enough');
				break;
			}
			
			$arrUpdateField['refresh_time'] = Util::getTime();
			$experieceRefreshNum = TreasureLogic::getExperienceRefresh($level);
			//阅历刷新
			if ($arrUpdateField['experience_refresh_num'] < $experieceRefreshNum)
			{
				if (!$user->subExperience(btstore_get()->TREASURE[$nextMapId]['refreshCostExperience']))
				{
					Logger::warning('fail to refresh, fail to sub experience');
					break;
				}
				$costExp += btstore_get()->TREASURE[$nextMapId]['refreshCostExperience'];
				$arrUpdateField['experience_refresh_num'] = $arrUpdateField['experience_refresh_num'] + 1;
				
			}else
			{
				break;
			}
			

			$rand = rand(1,10000);
			//刷出新图
			if ($rand <= btstore_get()->TREASURE[$nextMapId]['rate'])
			{
				$curPos += 1;			
				$openNext = 1;			
			}	
			
		}while($curPos + 1 < $posMax);
			
		$user->update();
		if($user->isOnline())
		{
			RPCContext::getInstance()->sendMsg(array($uid),'updateTreasureAutoCost',array('expCost' => $costExp));
		}
		
		// 保存数据库
		$newInfoLine = array(
				'cur_pos' => $curPos,
		);
		
		foreach ($arrUpdateField['va_treasure']['line'] as &$tmpLine)
		{
			$tmpLine['cur_pos'] = -1;
		}
		unset($tmpLine);
		$arrUpdateField['va_treasure']['line'][$line] = $newInfoLine;
		var_dump($arrUpdateField['va_treasure']['line'][$line]);
		
		// hunt有拉数据。
		TreasureDao::update($uid, $arrUpdateField);
		
		
		//成就
		if ($openNext == 1)
		{
			EnAchievements::notify($uid, 
				AchievementsDef::TREASURE_QUALITY, 
				btstore_get()->TREASURE[$nextMapId]['quality']);
		}
		
		if ($needGold!=-1)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_TREASURE_REFRESH, $needGold, Util::getTime());
		}

		return $arrUpdateField;
	}
	
	/**
	 * 是否可以继续自动寻宝
	 * @param $uid		用户ID
	 * @param $info  	寻宝信息
	 * @return unknown_type
	 */
	static function isAutoContious($uid,$info)
	{
		$ret = false;
		
		// 跨天了
		if(!Util::isSameDay($info['treasure_auto_begin_time']))
		{
			$maxCnt = TreasureLogic::getTreasureCntByLvl(EnUser::getUserObj($uid)->getLevel());
			
			if($info['hunt_aviable_num'] > $maxCnt)
			{
				$ret = true;
			}
			
		}else
		{
			// 今天还有次数
			if($info['hunt_aviable_num'] > 0)
			{
				$ret = true;
			}
		}

		return $ret;
	}
	

	/**
	 * 停止自动寻宝
	 * @param $uid
	 * @return unknown_type
	 */
	static function stopAutoHunt($uid)
	{
		$arrFields = array('treasure_auto_begin_time' => 0);
		
		TreasureDao::update($uid,$arrFields);
	}
	
	
	/**
	 * 获取自动寻宝配置
	 * @return unknown_type
	 */
	static function getTreasureAutoConf($uid)
	{
		$ret = array();
		
		$arrFields = array('treasure_auto_type','treasure_auto_begin_time','va_treasure');
		$conf = TreasureDao::getByUid($uid,$arrFields);
		
		if(!empty($conf))
		{
			// 自动寻宝信息转化
			$ret['trea_type'] = $conf['treasure_auto_type'];
			if($conf['treasure_auto_begin_time'] != 0)
			{
				$ret['status'] = TreasureConf::TREASURE_STATUS_DOING;
			}else
			{
				$ret['status'] = TreasureConf::TREASURE_STATUS_IDLE;
			}
			
			$ret['canChange'] = 1;
			foreach($conf['va_treasure']['line'] as $line => $pos)
			{
				if($pos['cur_pos'] > 0)
				{
					$ret['canChange'] = 0;
					$ret['trea_type'] = $line;
				}
			}
		}
		
		return $ret;
	}
	
}


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
