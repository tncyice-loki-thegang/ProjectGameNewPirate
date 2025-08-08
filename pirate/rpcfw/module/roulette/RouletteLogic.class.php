<?php

class RouletteLogic
{
	private static $arrField = array('freecount', 'count', 'areaid');
	
	private static function insertDefault($uid)
	{
		RouletteDao::insert($uid, array(
			'freecount' => 10,
			'count' => 100,
			'areaid' => 0,
			));
	}		
	
	public static function getInitInfo($uid)
	{
		$ret = RouletteDao::get($uid, self::$arrField);
		if (empty($ret))
		{			
			self::insertDefault($uid);
			$ret = RouletteDao::get($uid, self::$arrField);
		}
		return $ret;
	}
	
	public static function start($uid)
	{		
		$info = RouletteDao::get($uid, self::$arrField);		
		if ($info['freecount']==0)
		{
			$costgold = 500;
			$user = EnUser::getInstance();
			$user->subGold($costgold);
			$user->update();
			$info['count']--;
			$ret['costgold'] = $costgold;
		} else
		{
			$info['freecount']--;
			$ret['costgold'] = 0;
		}
		$info['areaid'] = rand(1,8);
		RouletteDao::update($uid, $info);
		$ret = array_merge($info, $ret);
		return $ret;
	}
	
	public static function batch($uid, $type, $num)
	{
		$info = RouletteDao::get($uid, self::$arrField);
		
		if ($info['freecount']==0 && $info['count']==0)
		{
			$ret['prestige'] = 0;
			$ret['costgold'] = 0;
			$ret = array_merge($info, $ret);
			return $ret;
		}
		
		if ($info['freecount']==0)
		{
			$costgold = 500 * $num;
			$info['count'] -= $num;
		} else
		{
			$costgold = 500 * ($num - $info['freecount']);
			$info['count'] -= $num - $info['freecount'];
			$info['freecount'] = 0;
		}
		
		$expAdd = 130;
		$expBase = 2;
		$addTime = array(50,60,70,80,90,100,110,120);
		$timesGold = array(10000=>0,20000=>900,30000=>1800,40000=>2400);
		
		$user = EnUser::getInstance($uid);
		$sumExp = 0;
		$tmpExp = ($user->getMasterHeroLevel() + $expAdd) * $expBase;
		$multiples = floor($type/10000);
		for ($i=1; $i<=$num; $i++)
		{
			$areaid = rand(0,7);
			$sumExp += $tmpExp * $addTime[$areaid];			
		}
		
		$retExp = $sumExp * $multiples;
		$retCostGold = $timesGold[$type] * $num + $costgold;
		
		$user->addExp($retExp);
		$user->subGold($retCostGold);
		$user->update();
		
		$ret['exp'] = $retExp;
		$ret['costgold'] = $retCostGold;
		$ret['cur_exp'] = $user->getMasterHeroObj()->getExp();
		$ret['curuserlevel'] = $user->getMasterHeroLevel();
		
		RouletteDao::update($uid, $info);
		
		$ret = array_merge($info, $ret);
		return $ret;
	}
	
	public static function recieveExp($uid, $type)
	{
		$info = RouletteDao::get($uid, self::$arrField);
		$expAdd = 130;
		$expBase = 2;
		$addTime = array(50,60,70,80,90,100,110,120);
		$timesGold = array(10000=>0,20000=>900,30000=>1800,40000=>2400);

		$user = EnUser::getInstance($uid);		
		$retExp = ($user->getMasterHeroLevel() + $expAdd) * $expBase *
					$addTime[$info['areaid']-1] * $type / 10000;
		
		$user->addExp($retExp);		
		$user->subGold($timesGold[$type]);
		$user->update();
		
		$ret['exp'] = $retExp;
		$ret['costgold'] = $timesGold[$type];
		$ret['cur_exp'] = $user->getMasterHeroObj()->getExp();
		$ret['curuserlevel'] = $user->getMasterHeroLevel();
		
		$info['areaid'] = 0;
		RouletteDao::update($uid, $info);
		
		$ret = array_merge($info, $ret);
		return $ret;
	}
}
