<?php

class RollPrestigeLogic
{
	private static $arrField = array('freecount', 'count', 'areaid');
	
	private static function insertDefault($uid)
	{
		RollPrestigeDao::insert($uid, array(
			'freecount' => 10,
			'count' => 100,
			'areaid' => 0,
			));
	}		
	
	public static function getInitInfo($uid)
	{
		$ret = RollPrestigeDao::get($uid, self::$arrField);
		if (empty($ret))
		{			
			self::insertDefault($uid);
			$ret = RollPrestigeDao::get($uid, self::$arrField);
		}
		return $ret;
	}
	
	public static function start($uid)
	{		
		$info = RollPrestigeDao::get($uid, self::$arrField);		
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
		RollPrestigeDao::update($uid, $info);
		$ret = array_merge($info, $ret);
		return $ret;
	}
	
	public static function batch($uid, $type, $num)
	{
		$info = RollPrestigeDao::get($uid, self::$arrField);
		
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
		
		$prestige = array(150,160,180,200,230,260,300,400);
		$timesGold = array(10000=>0,20000=>900,30000=>1800,40000=>2500);
		
		$sumPrestige = $sumGold = 0;
		$multiples = floor($type/10000);
		for ($i=1; $i<=$num; $i++)
		{
			$areaid = rand(0,7);
			$sumPrestige += $prestige[$areaid];
			$sumGold += $timesGold[$type];
		}
		
		$retPrestige = $sumPrestige * $multiples;
		$retCostGold = $timesGold[$type] * $num + $costgold;
		
		$user = EnUser::getInstance();
		$user->addPrestige($retPrestige);
		$user->subGold($retCostGold);
		$user->update();
		
		$ret['prestige'] = $retPrestige;
		$ret['costgold'] = $retCostGold;
				
		RollPrestigeDao::update($uid, $info);
		
		$ret = array_merge($info, $ret);
		return $ret;
	}
	
	public static function recievePrestige($uid, $type)
	{
		$info = RollPrestigeDao::get($uid, self::$arrField);
		
		$prestige = array(150,160,180,200,230,260,300,400);
		$timesGold = array(10000=>0,20000=>900,30000=>1800,40000=>2500);
		
		$user = EnUser::getInstance();
		$user->addPrestige($prestige[$info['areaid']-1] * $type / 10000);
		$user->subGold($timesGold[$type]);
		$user->update();
		
		$ret['prestige'] = $prestige[$info['areaid']-1] * $type / 10000;
		$ret['costgold'] = $timesGold[$type];
		
		$info['areaid'] = 0;
		RollPrestigeDao::update($uid, $info);
		
		$ret = array_merge($info, $ret);
		return $ret;
	}
}
