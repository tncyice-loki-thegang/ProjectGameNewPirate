<?php

class HorseDecorationLogic
{
	private static $arrField = array('resource', 'va_info');

	private static function insertDefault($uid)
	{
		$arrField['uid'] = $uid;
		$arrField['resource'] = 0;
		$arrField['va_info']['collection'] = array();
		$arrField['va_info']['refreshinfo'] = array('old_attr'=>array(), 'new_attr'=>array());
		//array('id'=>int, 'star'=>int)
		$arrField['va_info']['suitposition'] = array(
			1 => array('itemid' => 0, 'level' => 0), 
			2 => array('itemid' => 0, 'level' => 0), 
			3 => array('itemid' => 0, 'level' => 0), 
			4 => array('itemid' => 0, 'level' => 0), 
			5 => array('itemid' => 0, 'level' => 0), 
			6 => array('itemid' => 0, 'level' => 0));			
		HorseDecorationDao::insert($uid, $arrField);
	}

	
	public static function getInfo($uid)
	{
		$ret = HorseDecorationDao::get($uid, self::$arrField);
		if (empty($ret))
		{
			self::insertDefault($uid);
			$ret = HorseDecorationDao::get($uid, self::$arrField);
		}
		// logger::warning($ret);
		return $ret;
	}
	
	public static function setSuit($uid, $decoration_id)
	{		
		$info = self::getInfo($uid);
		$data = btstore_get()->HORSE_DECORATION_ITEM[$decoration_id];
		$info['va_info']['suitposition'][$data['position']]['itemid'] = $decoration_id;
		HorseDecorationDao::update($uid, $info);
		return 'ok';
	}
	
	public static function reinforce($uid, $pos)
	{
		$strCostResources = array(151,252,353,454,555,656,757,858,959,1060,1161,1262,1363,1464,1565,1666,1767,1915,2288,2695,2826,2964,3116,3282,3462,3657,3868,4095,4338,4600,4879,5176,5493,5830,6187,6565,6965,7387,7831,8300,8794,9314,9860,10433,11033,11661,12317,13002,13716,14460,15234,16042,16884,17764,18682,19642,20644,21692,22786,23930);
		$info = self::getInfo($uid);
		$info['resource'] -= $strCostResources[$info['va_info']['suitposition'][$pos]['level']];
		$info['va_info']['suitposition'][$pos]['level']++;
		HorseDecorationDao::update($uid, $info);
		return 'ok';
	}
	
	public static function refresh($uid, $decoration_id, $lock_ids=FALSE)
	{
		$userObj = EnUser::getUserObj($uid);
		$bag = BagManager::getInstance()->getBag();
		$data_item = btstore_get()->HORSE_DECORATION_ITEM[$decoration_id];
		$weightArr = btstore_get()->HORSE_DECORATION_STAR['weight']->toArray();
		$arrayStar = array_slice($weightArr,0,$data_item['numberlimit']);
		$arrayId = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
		if (isset($lock_ids))
		{
			foreach ($arrayId as $key => $id)
			{
				if (in_array($id, $lock_ids))
				{
					unset($arrayId[$key]);
				}
			}
		}

		for ($i=0; $i<$data_item['number']; $i++)
		{
			$randKey = array_rand($arrayId);
			$refressInfo[$i]['id'] = $arrayId[$randKey];
			unset($arrayId[$randKey]);
			$randStar = Util::noBackSample($arrayStar, 1);			
			$refressInfo[$i]['star'] = $arrayStar[$randStar[0]]['key'];			
		}
		
		$info = self::getInfo($uid);
		if (isset($lock_ids))
		{
			$num = count($lock_ids);
			$curStone = $bag->getItemNumByTemplateID(120039);
			if ($curStone < $num)
			{
				$user = EnUser::getInstance();
				$num -= $curStone;
				$bag->deleteItembyTemplateID(120039,$curStone);
				$needGold = 20*$num;
				$user->subGold($needGold);
				$user->update();
			}else $bag->deleteItembyTemplateID(120039,$num);
			
			foreach ($info['va_info']['refreshinfo']['old_attr'][$data_item['suitid']][$decoration_id] as $key => $val)
			{
				if (in_array($val['id'], $lock_ids))
				{
					$refressInfo[$key] = $val;
				}
			}
		}
		
		$info['va_info']['refreshinfo']['new_attr'][$data_item['suitid']][$decoration_id] = $refressInfo;
		$info['resource'] -= $data_item['expend'];
		HorseDecorationDao::update($uid, $info);
		$ret['success'] = 'ok';
		$ret['curgold'] = $userObj->getGold();
		$ret['baginfo'] = $bag->update();
		$ret['refreshinfo'] = $refressInfo;
		// logger::warning($ret);
		return $ret;
	}
	
	public static function replace($uid, $decoration_id)
	{
		$info = self::getInfo($uid);
		$data_item = btstore_get()->HORSE_DECORATION_ITEM[$decoration_id];
		$info['va_info']['refreshinfo']['old_attr'][$data_item['suitid']][$decoration_id] = $info['va_info']['refreshinfo']['new_attr'][$data_item['suitid']][$decoration_id];
		$info['va_info']['refreshinfo']['new_attr'][$data_item['suitid']][$decoration_id] = array();
		HorseDecorationDao::update($uid, $info);
		$ret['success'] = 'ok';
		return $ret;
	}

	public static function transfer($uid, $type, $old_id, $new_id)
	{
		switch ($type)
		{
			case 1:
				$bag = BagManager::getInstance()->getBag();
				$bag->deleteItembyTemplateID(120042,1);			
				$ret['baginfo'] = $bag->update();
				break;
			case 2:
				$user = EnUser::getInstance();
				$user->subGold(150);
				$user->update();
				$userObj = EnUser::getUserObj($uid);
				$ret['curgold'] = $userObj->getGold();
				break;	
		}
		$info = self::getInfo($uid);
		$data_old_item = btstore_get()->HORSE_DECORATION_ITEM[$old_id];
		$data_new_item = btstore_get()->HORSE_DECORATION_ITEM[$new_id];
		$info['va_info']['refreshinfo']['old_attr'][$data_new_item['suitid']][$new_id] = $info['va_info']['refreshinfo']['old_attr'][$data_old_item['suitid']][$old_id];
		$info['va_info']['refreshinfo']['old_attr'][$data_old_item['suitid']][$old_id] = array();
		HorseDecorationDao::update($uid, $info);
		return $ret;
	}
	
	public static function addResource($uid, $num)
	{
		$info = self::getInfo($uid);
		$info['resource'] += $num;
		HorseDecorationDao::update($uid, $info);
	}
	
	public static function subResource($uid, $num)
	{
		self::addResource($uid, -$num);
	}
	
	public static function addDecorationId($uid, $decoration_id)
	{
		$info = self::getInfo($uid);
		$data = btstore_get()->HORSE_DECORATION_ITEM[$decoration_id];
		if (!isset($info['va_info']['collection'][$data['suitid']]))
		{
			$info['va_info']['collection'][$data['suitid']] = array();
			$info['va_info']['refreshinfo']['old_attr'][$data['suitid']] = array();
			$info['va_info']['refreshinfo']['new_attr'][$data['suitid']] = array();
		}		
		array_push($info['va_info']['collection'][$data['suitid']],$decoration_id);
		$info['va_info']['refreshinfo']['old_attr'][$data['suitid']][$decoration_id] = array();
		$info['va_info']['refreshinfo']['new_attr'][$data['suitid']][$decoration_id] = array();
		HorseDecorationDao::update($uid, $info);
	}
}
