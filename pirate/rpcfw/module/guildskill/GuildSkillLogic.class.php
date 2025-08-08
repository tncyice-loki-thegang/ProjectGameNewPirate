<?php

class GuildSkillLogic
{
	private static $arrField = array('TechPoint', 'times', 'time', 'va_level');
	
	private static function insertDefault($uid)
	{
		GuildSkillDao::insert($uid, array(
			'TechPoint' => 0,
			'times' => 5,
			'time' => Util::getTime(),
			'va_level' => array()
			));
	}	
	
	public static function getAllGuildTechLv($uid)
	{
		$ret = GuildSkillDao::get($uid, self::$arrField);
		if (empty($ret))
		{			
			self::insertDefault($uid);
			$ret = GuildSkillDao::get($uid, self::$arrField);
		}
		$userObj = EnUser::getUserObj();
		$level = $userObj->getMasterHeroObj()->getLevel();
		$data = btstore_get()->GUILDBOSS_SKILL->toArray();
		foreach ($data as $id => $val)
		{
			if (!array_key_exists($id, $ret['va_level']))
			{				
				if ($level >= $val['openlevel'])
				{
					$ret['va_level'][$id] = 0;
				}
			}			
		}
		if ($ret['times']<=5)
		{
			if (!Util::isSameDay($ret['time'], 14400))
			{
				$day_num = Util::getDaysBetween($ret['time']);
				if ($ret['times']+$day_num >=5)
				{
					$ret['times'] = 5;
				} else $ret['times'] += $day_num;
			}
		}
		GuildSkillDao::update($uid, $ret);
		// logger::warning($ret);
		return $ret;
	}
	
	public static function plusGuildTechLv($uid, $id)
	{
		$ret = GuildSkillDao::get($uid, self::$arrField);
		$data = btstore_get()->GUILDBOSS_SKILL[$id]->toArray();		
		$lvUpCost = btstore_get()->ST_LV[$data['costid']][$ret['va_level'][$id] + 1]->toArray();
		$ret['TechPoint'] -= $lvUpCost['experience'];
		$ret['va_level'][$id]++;
		GuildSkillDao::update($uid, $ret);
		return $ret;
	}
	
	public static function addTechPoint($uid, $num)
	{
		$ret = GuildSkillDao::get($uid, array('TechPoint'));
		$ret['TechPoint'] += $num;
		GuildSkillDao::update($uid, $ret);
	}
	public static function subTechPoint($uid, $num)
	{
		self::addTechPoint($uid, -$num);
	}
	
	public static function getBellyPurchaseTimes($uid)
	{
		$ret = GuildSkillDao::get($uid, array('times'));
		return $ret['times'];
	}
	
	public static function PurchaseTechPoint($uid, $type)
	{
		$ret = GuildSkillDao::get($uid, self::$arrField);
		$belly = $gold = 0;
		switch ($type)
		{
			case 0:
				$point = 400;
				$belly = 500000;
				$ret['times']--;
				$ret['time'] = Util::getTime();
				break;
			case 1:
				$point = 200;
				$gold = 500;
				break;
			case 2:
				$point = 1200;
				$gold = 2000;
				break;
			case 3:
				$point = 12000;
				$gold = 20000;
				break;
		}
		$user = EnUser::getInstance();
		$user->subBelly($belly);
		$user->subGold($gold);
		$user->update();
		$ret['TechPoint'] += $point;
		GuildSkillDao::update($uid, $ret);
		return $ret;
	}
	
}
