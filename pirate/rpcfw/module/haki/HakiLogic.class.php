<?php

class HakiLogic
{
	private static $arrField = array('scene_level', 'bellyTimes', 'goldTimes', 'time', 'va_hakiInfo');
	
	private static function insertDefault($uid)
	{
		$mVip = btstore_get()->VIP;
		$userObj = EnUser::getUserObj();
		HakiDao::insert($uid, array(
			'bellyTimes' => 20,
			'goldTimes' => 0,
			'time' => Util::getTime(),
			'va_hakiInfo' => array(
				'arenaId' => 1, 
				'attack' => 0, 
				'defense' => 0, 
				'hp' => 0, 
				'master' => 0, 
				'xiuluo' => 0)
			));
	}

	public static function hakiInfo($uid)
	{
		$ret = HakiDao::get($uid, self::$arrField);
		if (empty($ret))
		{			
			self::insertDefault($uid);
			$ret = HakiDao::get($uid, self::$arrField);
		}
		if (!Util::isSameDay($ret['time'], 14400))
		{
			$ret['bellyTimes'] += 20;
			if ($ret['bellyTimes']>120)
			{
				$ret['bellyTimes'] = 120;
			}
			$ret['goldTimes'] = 0;
			$ret['time'] = Util::getTime();
			HakiDao::update($uid, $ret);
		}
		// logger::warning($ret);
		return $ret;
	}
		
	public static function trial($uid, $type)
	{
		$info = HakiDao::get($uid, self::$arrField);
		$cfg = btstore_get()->DOMINEER_SCENE[$info['va_hakiInfo']['arenaId']];	
		$user = EnUser::getInstance();
		$randWin = rand(1,10000);
		$xiuluo = 0;
		switch ($type)
		{
			case 1:
				$user->subBelly(100000);
				$info['bellyTimes']--;
				break;
			case 2:
				switch ($info['goldTimes'])
				{
					case 0:
						$gold = 200;
						break;
					case 1:
						$gold = 400;
						break;
					default:
						$gold = 1000;
						break;					
				}
				$user->subGold($gold);
				switch ($info['scene_level'])
				{
					case 0:
						if ($info['va_hakiInfo']['arenaId']<6)
						{
							$randWin = $cfg['victoryProbability'];
						}
						break;
					case 1:
						if ($info['va_hakiInfo']['arenaId']<14)
						{
							$randWin = $cfg['victoryProbability'];
						}
						break;
				}
				$info['goldTimes']++;
				break;
		}
		$user->update();		
		$key = array(0,'attack', 'defense', 'hp', 'xiuluo');
		switch ($info['scene_level'])
		{
			case 0:
				if ($info['va_hakiInfo']['arenaId']<5)
				{
					$randGet = rand(1,2);
				} else $randGet = rand(1,3);
				break;
			case 1:
				if ($info['va_hakiInfo']['arenaId']<13)
				{
					$randGet = rand(1,2);
				} else
				{
					$randGet = rand(1,3);
					$randXiuluo = rand(1,10000);
					if ($randXiuluo <= $cfg['AshuraDropRate'])
					{
						$xiuluo = rand($cfg['winAshuraDrop'][0],$cfg['winAshuraDrop'][1]);
					}
				}
				break;
		}
		if ($randWin <= $cfg['victoryProbability'])
		{
			$drop = rand($cfg['winDrop'][$randGet][0],$cfg['winDrop'][$randGet][1]);
			$info['va_hakiInfo']['arenaId']++;			
			switch ($info['scene_level'])
			{
				case 0:
					if ($info['va_hakiInfo']['arenaId']>8)
					{
						$info['va_hakiInfo']['arenaId']=8;
					}
					break;
				case 1:
					if ($info['va_hakiInfo']['arenaId']>16)
					{
						$info['va_hakiInfo']['arenaId']=16;
					}
					$info['va_hakiInfo']['xiuluo'] += $xiuluo;
					$ret['xiuluo'] = $xiuluo;
					break;
			}			
			$ret['isWin'] = 'win';
			
		} else
		{
			$drop = rand($cfg['lossDrop'][$randGet][0],$cfg['lossDrop'][$randGet][1]);
			$info['va_hakiInfo']['arenaId']--;
			switch ($info['scene_level'])
			{
				case 0:
					if ($info['va_hakiInfo']['arenaId']<1)
					{
						$info['va_hakiInfo']['arenaId']=1;
					}
					break;
				case 1:
					if ($info['va_hakiInfo']['arenaId']<9)
					{
						$info['va_hakiInfo']['arenaId']=9;
					}
					break;
			}
			$ret['isWin'] = 'lose';
		}
		
		$info['va_hakiInfo'][$key[$randGet]] += $drop;		
		$info['time'] = Util::getTime();
		HakiDao::update($uid, $info);
		$ret['arenaId'] = $info['va_hakiInfo']['arenaId'];
		$ret[$key[$randGet]] = $drop;
		// logger::warning($ret);
		TaskNotify::operate(TaskOperateType::HAKI_TRIAL);
		return $ret;
	}	

	public static function allTrial($uid, $type)
	{
		$info = HakiDao::get($uid, self::$arrField);
		$user = EnUser::getInstance();
		$user->subBelly(2000000);
		$user->update();
		$key = array(0,'attack', 'defense', 'hp', 'xiuluo');
		$ret = array('attack' => 0, 'defense' => 0, 'hp' => 0, 'master' => 0, 'xiuluo' => 0, 'winCount' => 0 , 'loseCount' => 0);
		$xiuluo = 0;
		for ($i=1; $i<=20; $i++)
		{
			$cfg = btstore_get()->DOMINEER_SCENE[$info['va_hakiInfo']['arenaId']];
			$randWin = rand(1,10000);
			switch ($info['scene_level'])
			{
				case 0:
					if ($info['va_hakiInfo']['arenaId']<5)
					{
						$randGet = rand(1,2);
					} else $randGet = rand(1,3);
					break;
				case 1:
					if ($info['va_hakiInfo']['arenaId']<13)
					{
						$randGet = rand(1,2);
					} else
					{
						$randGet = rand(1,3);
						$randXiuluo = rand(1,10000);
						if ($randXiuluo <= $cfg['AshuraDropRate'])
						{
							$xiuluo = rand($cfg['winAshuraDrop'][0],$cfg['winAshuraDrop'][1]);
						}
					}
					break;
			}
			if ($randWin <= $cfg['victoryProbability'])
			{
				$drop = rand($cfg['winDrop'][$randGet][0],$cfg['winDrop'][$randGet][1]);
				$info['va_hakiInfo']['arenaId']++;
				switch ($info['scene_level'])
				{
					case 0:
						if ($info['va_hakiInfo']['arenaId']>8)
						{
							$info['va_hakiInfo']['arenaId']=8;
						}
						break;
					case 1:
						if ($info['va_hakiInfo']['arenaId']>16)
						{
							$info['va_hakiInfo']['arenaId']=16;
						}
						$info['va_hakiInfo']['xiuluo'] += $xiuluo;
						$ret['xiuluo'] = $xiuluo;
						break;
				}				
				$ret['winCount']++;
				
			} else
			{
				$drop = rand($cfg['lossDrop'][$randGet][0],$cfg['lossDrop'][$randGet][1]);
				$info['va_hakiInfo']['arenaId']--;
				switch ($info['scene_level'])
				{
					case 0:
						if ($info['va_hakiInfo']['arenaId']<1)
						{
							$info['va_hakiInfo']['arenaId']=1;
						}
						break;
					case 1:
						if ($info['va_hakiInfo']['arenaId']<9)
						{
							$info['va_hakiInfo']['arenaId']=9;
						}
						break;
				}
				$ret['loseCount']++;
			}
			$ret[$key[$randGet]] += $drop;
			$info['va_hakiInfo'][$key[$randGet]] += $drop;
		}		
		$info['bellyTimes'] -= 20;
		$info['time'] = Util::getTime();
		HakiDao::update($uid, $info);
		$ret['arenaId'] = $info['va_hakiInfo']['arenaId'];
		// logger::warning($ret);
		TaskNotify::operate(TaskOperateType::HAKI_TRIAL);
		return $ret;
	}

	public static function allGoldTrial($uid, $type)
	{
		$info = HakiDao::get($uid, self::$arrField);
		$cfg = btstore_get()->DOMINEER_SCENE[$info['va_hakiInfo']['arenaId']];	
		$user = EnUser::getInstance();
		$ret = array('attack' => 0, 'defense' => 0, 'hp' => 0, 'master' => 0, 'xiuluo' => 0, 'winCount' => 0 , 'loseCount' => 0);
		switch ($type)
		{
			case 10:
				switch ($info['goldTimes'])
				{
					case 0:
						$gold = 8600;
						break;
					case 1:
						$gold = 9400;
						break;
					default:
						$gold = 10000;
						break;					
				}
				$info['va_hakiInfo']['hp'] += 35;
				$info['va_hakiInfo']['xiuluo'] += 35;
				$ret['hp'] = 35;
				$ret['xiuluo'] = 35;
				break;
			case 100:
				switch ($info['goldTimes'])
				{
					case 0:
						$gold = 98600;
						break;
					case 1:
						$gold = 99400;
						break;
					default:
						$gold = 100000;
						break;					
				}
				$info['va_hakiInfo']['hp'] += 350;
				$info['va_hakiInfo']['xiuluo'] += 350;
				$ret['hp'] = 350;
				$ret['xiuluo'] = 350;
				break;
		}
		$info['goldTimes']+=$type;
		$info['time'] = Util::getTime();
		$user->subGold($gold);
		$user->update();
		$key = array(0,'attack', 'defense', 'hp', 'xiuluo');
		$xiuluo = 0;
		for ($i=1; $i<=$type; $i++)
		{
			$cfg = btstore_get()->DOMINEER_SCENE[$info['va_hakiInfo']['arenaId']];
			$randWin = rand(1,10000);
			switch ($info['scene_level'])
			{
				case 0:
					if ($info['va_hakiInfo']['arenaId']<6)
					{
						$randWin = $cfg['victoryProbability'];
					}
					break;
				case 1:
					if ($info['va_hakiInfo']['arenaId']<14)
					{
						$randWin = $cfg['victoryProbability'];
					}
					break;
			}
			switch ($info['scene_level'])
			{
				case 0:
					if ($info['va_hakiInfo']['arenaId']<5)
					{
						$randGet = rand(1,2);
					} else $randGet = rand(1,3);
					break;
				case 1:
					if ($info['va_hakiInfo']['arenaId']<13)
					{
						$randGet = rand(1,2);
					} else
					{
						$randGet = rand(1,3);
						$randXiuluo = rand(1,10000);
						if ($randXiuluo <= $cfg['AshuraDropRate'])
						{
							$xiuluo = rand($cfg['winAshuraDrop'][0],$cfg['winAshuraDrop'][1]);
						}
					}
					break;
			}
			if ($randWin <= $cfg['victoryProbability'])
			{
				$drop = rand($cfg['winDrop'][$randGet][0],$cfg['winDrop'][$randGet][1]);
				$info['va_hakiInfo']['arenaId']++;
				switch ($info['scene_level'])
				{
					case 0:
						if ($info['va_hakiInfo']['arenaId']>8)
						{
							$info['va_hakiInfo']['arenaId']=8;
						}
						break;
					case 1:
						if ($info['va_hakiInfo']['arenaId']>16)
						{
							$info['va_hakiInfo']['arenaId']=16;
						}
						$info['va_hakiInfo']['xiuluo'] += $xiuluo;
						$ret['xiuluo'] += $xiuluo;
						break;
				}				
				$ret['winCount']++;				
			} else
			{
				$drop = rand($cfg['lossDrop'][$randGet][0],$cfg['lossDrop'][$randGet][1]);
				$info['va_hakiInfo']['arenaId']--;
				switch ($info['scene_level'])
				{
					case 0:
						if ($info['va_hakiInfo']['arenaId']<1)
						{
							$info['va_hakiInfo']['arenaId']=1;
						}
						break;
					case 1:
						if ($info['va_hakiInfo']['arenaId']<9)
						{
							$info['va_hakiInfo']['arenaId']=9;
						}
						break;
				}
				$ret['loseCount']++;
			}
			$ret[$key[$randGet]] += $drop;
			$info['va_hakiInfo'][$key[$randGet]] += $drop;
		}		
		HakiDao::update($uid, $info);
		$ret['arenaId'] = $info['va_hakiInfo']['arenaId'];
		// logger::warning($ret);
		TaskNotify::operate(TaskOperateType::HAKI_TRIAL);
		return $ret;
	}
	
	private static function trialResult($hakiInfo, $type)
	{
		
	}
	
	public static function addProperty($uid, $htid, $propertys)
	{
		$info = HakiDao::get($uid, self::$arrField);
		foreach ($propertys as $key => $value)
		{
			$info['va_hakiInfo'][$key] -= $value;
		}
		HakiDao::update($uid, $info);
		$userObj = EnUser::getUserObj();
		$heroObj = $userObj->getHeroObjByHtid($htid);
		$heroObj->addProperty($propertys);
		$userObj->update();
		// logger::warning($heroObj->getHakiInfo());
		return $heroObj->getHakiInfo();
	}

	public static function convert($uid, $htid)
	{
		$userObj = EnUser::getUserObj();
		$heroObj = $userObj->getHeroObjByHtid($htid);
		if ($heroObj->isMasterHero())
		{
			$heroObj->convertMaster();
			$ret['master_haki_id'] = $heroObj->getMasterHakiId();
			// $bag = BagManager::getInstance()->getBag();
			// $ret['baginfo'] = $bag->update();
			$userObj->update();			
			return $ret;
		} else
		{
			$heroObj->convertHaki();
			$userObj->update();
		}				
	}	
	
	public static function levelupHakiScene($uid)
	{		
		$info = HakiDao::get($uid, self::$arrField);
		$info['scene_level'] = 1;
		$info['va_hakiInfo']['arenaId'] = 9;
		HakiDao::update($uid, $info);
		TaskNotify::operate(TaskOperateType::HAKI_MASTER);
		return 'ok';
	}
	
	public static function updateHakiInfo($uid, $value)
	{
		$key = array(0,'attack', 'defense', 'hp', 'master', 'xiuluo');
		$info = HakiDao::get($uid, self::$arrField);
		foreach ($value as $k => $v)
		{
			$info['va_hakiInfo'][$key[$k]] += $v;
		}
		HakiDao::update($uid, $info);
	}
}
