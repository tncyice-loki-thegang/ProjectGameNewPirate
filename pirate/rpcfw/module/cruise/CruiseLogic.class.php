<?php

class CruiseLogic
{
	private static $arrField = array('carved_stone', 'free_dice_times', 'gold_dice_times', 'dice_time', 'va_node_info');
	
	private static function insertDefault($uid)
	{
		$mVip = btstore_get()->VIP;
		$userObj = EnUser::getUserObj();
		CruiseDao::insert($uid, array(
			'free_dice_times' => 5,
			'gold_dice_times' => $mVip[$userObj->getVip()]['cruise_num'],
			'dice_time' => Util::getTime(),
			'va_node_info' => array('node'=>1, 'num'=>0, 'left_cruise'=>0)
			));
	}	
	
	public static function cruiseInfo($uid)
	{
		$ret = CruiseDao::get($uid, self::$arrField);		
		if (empty($ret))
		{			
			self::insertDefault($uid);
			$ret = CruiseDao::get($uid, self::$arrField);
		}
		if (!Util::isSameDay($ret['dice_time'], 14400))
		{			
			$ret['free_dice_times'] = 5;
			$mVip = btstore_get()->VIP;
			$userObj = EnUser::getUserObj();
			$ret['gold_dice_times'] = $mVip[$userObj->getVip()]['cruise_num'];
			$ret['dice_time'] = Util::getTime();
			CruiseDao::update($uid, $ret);
		}
		// logger::warning($ret);
		return $ret;
	}
	
	public static function throwDice($uid)
	{	
		$info = CruiseDao::get($uid, self::$arrField);
		unset($info['carved_stone']);
		$gold = array(40,40,40,40,40,40,40,40,40,40,45,50,55,60,65,70,75,80,85,90,95,100,100,100,100,100,100,100,100,100,100,100,100,100,100);
		
		if ($info['free_dice_times'] <= 0)
		{
			$needGold = $gold[35 - $info['gold_dice_times']];
			$user = EnUser::getInstance();
			$user->subGold($needGold);
			$user->update();
			$info['gold_dice_times']--;
		} else $info['free_dice_times']--;
		
		$info['dice_time'] = Util::getTime();
		$rand = rand(1,6);		
		$left_cruise = 0;		
		if ( $info['va_node_info']['num'] !=0 )
		{
			$num = $info['va_node_info']['num'];
			$info['va_node_info']['num'] = 0;
		} else $num = $rand;

		$node_move = $info['va_node_info']['node'];
		
		$node_info = array();
		$map = btstore_get()->CRUISE_MAP->toArray();
		for ($i=0; $i<$num; $i++)
		{
			if (is_numeric($map[$node_move]['nextId']))
			{
				$node_info[$i]['node'] = $map[$node_move]['nextId'];
				$node_move = $map[$node_move]['nextId'];
				if ($i+1 == $num)
				{
					$rewardArr = $map[$node_move]['reward'];
					$node_info[$i]['reward'] = $rewardArr;
					RewardUtil::reward($uid, $rewardArr);					
					$array[0] = array('question' => rand(1,69), 'weight' => 1000);
					$array[1] = array('question' => 0, 'weight' => 9000);
					$randKey = Util::noBackSample($array, 1);
					if ($array[$randKey[0]]['question'] != 0)
					{
						$node_info[$i]['task'] = array('fortuitousType' => 1, 'fortuitousId' => $array[$randKey[0]]['question']);
						$info['va_node_info']['fortuitousId'] = $node_info[$i]['task']['fortuitousId'];
					}				
				}
			} else
			{
				$info['va_node_info']['num'] = $num;
				$left_cruise = $num-$i;				
				break;
			}
		}
		
		$info['va_node_info']['node'] = $node_move;		
		$info['va_node_info']['left_cruise'] = $left_cruise;		
		CruiseDao::update($uid, $info);
		$ret['num'] = $num;
		$ret['left_cruise'] = $left_cruise;
		$ret['node_info'] = $node_info;
		$ret['node'] = $node_move;
		// Logger::warning($ret);
		return $ret;
	}
	
	public static function chooseNode($uid, $mapId)
	{
		$info = CruiseDao::get($uid, self::$arrField);
		unset($info['carved_stone']);
		if ($mapId == 0)
		{
			$needGold = 0;
		} else $needGold = 100;
		$user = EnUser::getInstance();
		$user->subGold($needGold);
		$user->update();

		if ($mapId == 0)
		{
			$array[0] = array('mapId' => 7, 'weight' => 2000);
			$array[1] = array('mapId' => 10, 'weight' => 8000);
			$randKey = Util::noBackSample($array, 1);
			$node_move = $array[$randKey[0]]['mapId'];
		} else $node_move = $mapId;
		
		if ($info['va_node_info']['left_cruise']>0)
		{			
			$num = $info['va_node_info']['left_cruise'];
			$info['va_node_info']['left_cruise'] = 0;
			$map = btstore_get()->CRUISE_MAP->toArray();
			$node_info[0]['node'] = $node_move;
			for ($i=1; $i<$num; $i++)
			{
				$node_info[$i]['node'] = $map[$node_move]['nextId'];
				$node_move = $map[$node_move]['nextId'];
				if ($i+1 == $num)
				{
					$rewardArr = $map[$node_move]['reward'];
					$node_info[$i]['reward'] = $rewardArr;
					RewardUtil::reward($uid, $rewardArr);				
					$array[0] = array('question' => rand(1,69), 'weight' => 1000);
					$array[1] = array('question' => 0, 'weight' => 9000);
					$randKey = Util::noBackSample($array, 1);
					if ($array[$randKey[0]]['question'] != 0)
					{
						$node_info[$i]['task'] = array('fortuitousType' => 1, 'fortuitousId' => $array[$randKey[0]]['question']);
						$info['va_node_info']['fortuitousId'] = $node_info[$i]['task']['fortuitousId'];
					}					
				}
			}
		}
		$info['va_node_info']['node'] = $node_move;		
		CruiseDao::update($uid, $info);
		$ret['num'] =  $num;
		$ret['node_info'] = $node_info;
		$ret['left_cruise'] = 0;
		$ret['node'] = $node_move;
		// Logger::warning($ret);
		return $ret;
	}
	
	public static function reCruise($uid, $num)
	{
		$info = CruiseDao::get($uid, array('va_node_info'));
		$user = EnUser::getInstance();
		$user->subGold(100);
		$user->update();
		$info['va_node_info']['num'] = $num;
		CruiseDao::update($uid, $info);
		return self::throwDice($uid);
	}
	
	public static function answer($uid, $node, $answer)
	{
		$info = CruiseDao::get($uid, self::$arrField);		
		$rewardId = btstore_get()->CRUISE_ANSWER[$info['va_node_info']['fortuitousId']][$answer];
		$reward = RewardUtil::reward($uid, array($rewardId));
		if (isset($reward['grid']))
		{
			$ret['bagInfo'] = $reward['grid'];
		}
		$ret['ret'] = 'ok';
		return $ret;
	}
	
	public static function addCarvedStone($uid, $num)
	{
		$info = CruiseDao::get($uid, self::$arrField);
		$info['carved_stone'] += $num;
		CruiseDao::update($uid, $info);
	}
	
	public static function subCarvedStone($uid, $num)
	{
		self::addCarvedStone($uid, -$num);
	}
}
