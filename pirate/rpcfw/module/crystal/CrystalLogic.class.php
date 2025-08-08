<?php

class CrystalLogic
{
	private static $arrField = array('uid', 'crystal_id', 'crystal_lv', 'crystal_satus', 'last_crystal_times', 'summon_time');
	
	public static function getInfo($uid)
	{
		$ret = CrystalDao::get($uid, self::$arrField);		
		if (empty($ret))
		{			
			self::insertDefault($uid);
			$ret = CrystalDao::get($uid, self::$arrField);
		}
		$days = Util::getDaysBetween($ret['summon_time']);
		for ($i=0; $i<$days; $i++)
		{
			if ($ret['last_crystal_times']<15)
			{
				$ret['last_crystal_times']+=5;
				if ($ret['last_crystal_times']>=15)
				{
					$ret['last_crystal_times']=15;
					break;
				}
			}			
		}
		CrystalDao::update($uid, $ret);
		return $ret;
	}
	
	private static function insertDefault($uid)
	{
		CrystalDao::insert($uid, array('last_crystal_times' => 5, 'summon_time' => Util::getTime()));
	}
	
	public static function summon($uid)
	{
		$info = CrystalDao::get($uid, self::$arrField);
		$crystal_id	= Util::noBackSample(array(101=>array('weight'=>5000),102=>array('weight'=>2500),103=>array('weight'=>1250),104=>array('weight'=>3000),105=>array('weight'=>1500),106=>array('weight'=>750),107=>array('weight'=>2000),108=>array('weight'=>1000),109=>array('weight'=>500)), 1);
		$info['crystal_id'] = $crystal_id[0];
		$info['crystal_lv'] = 1;
		$info['summon_time'] = Util::getTime();
		$info['last_crystal_times']--;
		CrystalDao::update($uid, $info);
		TaskNotify::operate(TaskOperateType::CRYSTAL_SUMMON);
		return $crystal_id[0];
	}
	
	public static function lvUp($uid, $type)
	{
		$ret = array('result' => 'err');
		$info = CrystalDao::get($uid, self::$arrField);		
		$crystal = btstore_get()->CRYSTAL;
		$user = EnUser::getInstance();
		switch ($type)
		{
			case 0:
				$user->subExperience($crystal['experienceRank'][$info['crystal_lv']]['experience']);
				$user->update();
				$rand= Util::noBackSample(array(2=>array('weight'=>9000),3=>array('weight'=>7000),4=>array('weight'=>4500),5=>array('weight'=>2500)), 1);
				if ($info['crystal_lv']>$rand[0])
				{
					$info['crystal_satus'] = 3;					
					CrystalDao::update($uid, $info);
					return $ret;
				}
				
				break;
			case 1:
				$user->subGold($crystal['goldRank'][$info['crystal_lv']]['gold']);
				$user->update();
				$rand= Util::noBackSample(array(2=>array('weight'=>10000),3=>array('weight'=>9000),4=>array('weight'=>6000),5=>array('weight'=>4000)), 1);
				if ($info['crystal_lv']>$rand[0])
				{
					$info['crystal_satus'] = 2;
					CrystalDao::update($uid, $info);
					return $ret;
				}
				
				break;
		}
		$ret = array('result' => 'ok');
		$info['crystal_lv']++;
		if ($info['crystal_lv'] == 5)
		{
			$info['crystal_satus'] = 1;
		}
		$user->update();
		CrystalDao::update($uid, $info);
		return $ret;
	}

	public static function lvUpByGold($uid)
	{
		$info = CrystalDao::get($uid, self::$arrField);
		$crystal = btstore_get()->CRYSTAL;
		$user = EnUser::getInstance();		
		$info['crystal_lv']=5;
		$info['crystal_satus'] = 1;
		$user->subGold(300);
		$user->update();
		CrystalDao::update($uid, $info);
	}

	public static function getResource($uid)
	{
		//$ret = 'err';
		$info = CrystalDao::get($uid, self::$arrField);
		$base = pow(4,$info['crystal_lv']-1);		
		if ($info['crystal_satus'] == 3)
		{
			$base = $base;
		}
		else if($info['crystal_satus'] == 2)
		{
			$base = $base * 2;
		}
		$rewardInfo = btstore_get()->CRYSTAL_REWARD[$info['crystal_id']];
		$newReward = array();
		$user = EnUser::getInstance();
		$bag = BagManager::getInstance()->getBag();
		foreach ($rewardInfo as $key => $val)
		{
			switch ($key)
			{
				case "belly":
					$user->addBelly($val*$base);
					break;
				case "experience":
					$user->addExperience($val*$base);
					break;
				case "item_num":
					$bag->addItemByTemplateID(120015, $val*$base);
					break;
				// case "exp":
				case "prestige":
					$user->addPrestige($val);
					break;
				case "jewelryElement":
					Jewelry::addEnergyElement($uid, 0, $val*$base);
					break;
				case "jewelryEnery":
					Jewelry::addEnergyElement($uid, $val*$base, 0);
					break;
				case "starstone":
					Astrolabe::addStone($uid, $val*$base);
					break;
				case "blueSoul":
					SoulObj::getInstance()->addBlue($val*$base);
					SoulObj::getInstance()->save();
					break;
			}
		}
		$user->update();
		$info['crystal_id'] = 0;
		$info['crystal_lv']=0;
		$info['crystal_satus'] = 0;
		CrystalDao::update($uid, $info);
		$ret = array('hero' => array(), 'bag'=>$bag->update());		
		return $ret;

	}

}