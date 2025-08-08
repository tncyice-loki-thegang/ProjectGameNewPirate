<?php

class SeaSoulLogic
{
	private static $arrField = array('seasoul_num', 'va_seasoul');
	
	public static function getInfo($uid)
	{
		$info = SeaSoulDao::get($uid, self::$arrField);		
		if (empty($info))
		{			
			self::insertDefault($uid);
			$info = SeaSoulDao::get($uid, self::$arrField);
		}

		return $info;
	}
	
	private static function insertDefault($uid)
	{
		$arrField = array('uid' => $uid, 'seasoul_num' => 10000, 'va_seasoul' => array());
		$arrField['va_seasoul']['curPalaceBigs'] = array(1);
		$arrField['va_seasoul']['finishedPalaces'] = array();
		$arrField['va_seasoul']['finishedStarfishes'] = array();		
		SeaSoulDao::insert($uid, $arrField);
	}
	
	public static function composeSeasoul($uid, $times) 
	{
		$ret = array('ret' => 0);

		$user = EnUser::getInstance();
		$bag = BagManager::getInstance()->getBag();
		
		$info = SeaSoulDao::get($uid, self::$arrField);
		$need_stone = SeaSoulDef::NEED_STONE * $times;
		$need_gold = 0;
		$array = array( SeaSoulDef::SILVER_STONE,
						SeaSoulDef::GREEN_STONE,
						SeaSoulDef::PURPLE_STONE );
		foreach ($array as $val)
		{
			$stone = $bag->getItemNumByTemplateID($val);
			if ($stone < $need_stone)
			{
				$shortage_stone = $need_stone - $stone;
				$need_gold = $need_gold + $shortage_stone * SeaSoulDef::NEED_GOLD;
				$bag->deleteItembyTemplateID($val, $stone);
			}
			$bag->deleteItembyTemplateID($val, $need_stone);
		}	
		if ($user->subgold($need_gold) == FALSE)
		{
			return $ret;
		}
		
		$info['seasoul_num'] += SeaSoulDef::TOP*$times;
		SeaSoulDao::update($uid, $info);
		
		$user->update();
		
		$ret = array('ret' => 1);
		$ret['addnum'] = SeaSoulDef::TOP*$times;
		$ret['item'] = $bag->update();
		TaskNotify::operate(TaskOperateType::SEASOUL_COMPOSE);
		return $ret;
	}
		
	public static function openMultiStarfish($uid, $sid)
	{
		$info = SeaSoulDao::get($uid, self::$arrField);
		$needNum = 0;
		$cfgBig = self::getBigPalaceInfoById(end($info['va_seasoul']['curPalaceBigs']));
		foreach ($cfgBig['seapalaceId'] as $palaceId)
		{
			if (in_array($palaceId, $info['va_seasoul']['finishedPalaces']))
			{
				continue;
			} else{
				$cfgPlace = self::getPalaceInfoById($palaceId);
				foreach ($cfgPlace['starfishId'] as $starFishId)
				{
					if (in_array($starFishId, $info['va_seasoul']['finishedStarfishes']))
					{
						continue;
					} else{
						$cfgStar = self::getStarfishInfoById($starFishId);
						$needNum += $cfgStar[5];
						array_push($info['va_seasoul']['finishedStarfishes'], $starFishId);
						if (in_array($sid, $info['va_seasoul']['finishedStarfishes']))
						{
							if (count($info['va_seasoul']['finishedStarfishes'])==10)
							{								
								array_push($info['va_seasoul']['finishedPalaces'], $palaceId);
								$info['va_seasoul']['finishedStarfishes'] = array();								
							}
							if (count($info['va_seasoul']['finishedPalaces'])==10)
							{
								$userObj = EnUser::getUserObj();
								$heroObj = $userObj->getMasterHeroObj();
								$htid = $heroObj->getHtid();
								if (count($cfgPlace['normalSkill'])>0)
								{
									$heroObj->learnNormalSkill($cfgPlace['normalSkill'][$htid]);
								}
								if (count($cfgPlace['angerSkill'])>0)
								{
									$heroObj->learnRageSkill($cfgPlace['angerSkill'][$htid]);
								}
								$userObj->update();
							}
							$info['seasoul_num'] -= $needNum;
							SeaSoulDao::update($uid, $info);							
							$ret = array('ret' => 1);
							$ret = array_merge($ret, $info['va_seasoul']);
							// logger::warning($ret);
							return $ret;
						}
					}
				}
			}
		}
	}
	
	public static function openMultiPalace($uid, $pid)
	{
		$info = SeaSoulDao::get($uid, self::$arrField);
		$needNum = 0;
		$cfgBig = self::getBigPalaceInfoById(end($info['va_seasoul']['curPalaceBigs']));
		foreach ($cfgBig['seapalaceId'] as $palaceId)
		{
			if (in_array($palaceId, $info['va_seasoul']['finishedPalaces']))
			{
				continue;
			} else{
				$cfgPlace = self::getPalaceInfoById($palaceId);
				foreach ($cfgPlace['starfishId'] as $starFishId)
				{
					if (in_array($starFishId, $info['va_seasoul']['finishedStarfishes']))
					{
						continue;
					} else{
						$cfgStar = self::getStarfishInfoById($starFishId);
						$needNum += $cfgStar[5];
						array_push($info['va_seasoul']['finishedStarfishes'], $starFishId);
						if (count($info['va_seasoul']['finishedStarfishes'])==10)
						{
							array_push($info['va_seasoul']['finishedPalaces'], $palaceId);
							$info['va_seasoul']['finishedStarfishes'] = array();
						}
						if (in_array($pid, $info['va_seasoul']['finishedPalaces']))
						{
							if (count($info['va_seasoul']['finishedPalaces'])==10)
							{
								$userObj = EnUser::getUserObj();
								$heroObj = $userObj->getMasterHeroObj();
								$htid = $heroObj->getHtid();
								if (count($cfgPlace['normalSkill'])>0)
								{
									$heroObj->learnNormalSkill($cfgPlace['normalSkill'][$htid]);
								}
								if (count($cfgPlace['angerSkill'])>0)
								{
									$heroObj->learnRageSkill($cfgPlace['angerSkill'][$htid]);
								}
								$userObj->update();
							}
							$info['seasoul_num'] -= $needNum;
							SeaSoulDao::update($uid, $info);	
							$ret = array('ret' => 1);
							$ret = array_merge($ret, $info['va_seasoul']);
							// logger::warning($ret);
							return $ret;
						}
					}
				}
			}
		}
	}
	
	public static function openPalaceBig($uid)
	{
		$info = SeaSoulDao::get($uid, self::$arrField);
		$cfgBig = self::getBigPalaceInfoById(end($info['va_seasoul']['curPalaceBigs'])+1);
		$userObj = EnUser::getUserObj();
		if ($userObj->getLevel()<$cfgBig['level'])
		{
			$bag = BagManager::getInstance()->getBag();
			if ($bag->deleteItembyTemplateID(120034, 1)==FALSE)
			{
				return array('ret'=>0);
			}
			$ret['baginfo'] = $bag->update();
		}
		$info['va_seasoul']['finishedPalaces'] = array();
		array_push($info['va_seasoul']['curPalaceBigs'], end($info['va_seasoul']['curPalaceBigs'])+1);
		SeaSoulDao::update($uid, $info);
		$ret['ret'] = 1;
		$ret = array_merge($ret, $info['va_seasoul']);		
		return $ret;
	}
	
	public static function addStone($num)
	{
		$uid = RPCContext::getInstance()->getUid();
		$info=self::getInfo($uid);
		$info['seasoul_num'] += $num;
		SeaSoulDao::update($uid, array('seasoul_num' => $info['seasoul_num']));
	}
	
	public static function subStone($num)
	{
		return $this->addStone(-$num);
	}

		public static function getBigPalaceInfoById($placeBigId)
	{
		return btstore_get()->PALACE_BIG[$placeBigId];
	}
	
	public static function getPalaceInfoById($placeId)
	{
		return btstore_get()->PALACE[$placeId];
	}
	
	public static function getStarfishInfoById($starFishId)
	{
		return btstore_get()->STAR_FISH[$starFishId];
	}
}